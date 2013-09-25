<?php
/**
 * News submit controller
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Copyright (c) Pi Engine http://www.xoopsengine.org
 * @license         http://www.xoopsengine.org/license New BSD License
 * @author          Hossein Azizabadi <azizabadi@faragostaresh.com>
 * @since           3.0
 * @package         Module\News
 * @version         $Id$
 */

namespace Module\News\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Pi\File\Transfer\Upload;
use Module\News\Form\StoryUserForm;
use Module\News\Form\StoryUserFilter;
use Zend\Json\Json;

class ManagementController extends ActionController
{
    protected $ImagePrefix = 'image_';
    protected $storyColumns = array(
        'id', 'title', 'subtitle', 'slug', 'topic', 'short', 'body', 'keywords', 'description', 'important', 'status', 'create', 
        'update', 'publish', 'author', 'hits', 'image', 'path', 'comments', 'point', 'count', 'favorite', 'attach', 'extra'
    );

    /**
     * This function add just for test and is not quite safe
     * After finish ACL manager improv it
     * return allowaed topics for edit
     */
    public function AllowedAction($resource)
    {
        $checkAccess = $this->acl()->checkAccess($resource);
        $topic = Pi::service('api')->news(array('Topic', 'EditAccess'));
        if (!$checkAccess) {
            return $this->redirect()->toRoute('', array('controller' => 'index', 'action' => 'index'));
        }
        if (empty($topic)) {
            return $this->redirect()->toRoute('', array('controller' => 'index', 'action' => 'index'));
        }
        return $topic;
    }

    public function indexAction()
    {
        // Get page
        $page = $this->params('page', 1);
        $module = $this->params('module');
        $status = $this->params('status');
        $topic = $this->params('topic');
        $author = $this->params('author');
        // check Allowed
        $topicId = $this->AllowedAction($this->params()->fromRoute());
        // Topic list
        $columns = array('id', 'title', 'slug');
        $where = array('status' => 1, 'id' => $topicId);
        $select = $this->getModel('topic')->select()->columns($columns)->where($where);
        $rowset = $this->getModel('topic')->selectWith($select);
        foreach ($rowset as $row) {
            $topicList[$row->id] = $row->toArray();
        }
        // Set info
        $offset = (int)($page - 1) * $this->config('show_perpage');
        $order = array('publish DESC', 'id DESC');
        $limit = intval($this->config('show_perpage'));
        // Set where
        $whereLink = array();
        // Set where status
        if (!empty($status) && in_array($status, array(1, 2))) {
            $whereLink['status'] = $status;
        } else {
            $whereLink['status != ?'] = 5;
        }
        // Set where topic
        if (!empty($topic) && in_array($topic, $topicId)) {
            $whereLink['topic'] = $topic;
        } else {
            $whereLink['topic'] = $topicId;
        }
        // Set limit by day
        if ($this->config('daylimit')) {
            $whereLink['publish > ?'] = time() - (86400 * $this->config('daylimit'));
        }
        // Get info from link table
        $select = $this->getModel('link')->select()->where($whereLink)->columns(array('story' => new \Zend\Db\Sql\Predicate\Expression('DISTINCT story')))->order($order)->offset($offset)->limit($limit);
        $rowset = $this->getModel('link')->selectWith($select)->toArray();
        // Make list
        foreach ($rowset as $id) {
            $storyId[] = $id['story'];
        }
        // Set info
        $columnStory = array('id', 'title', 'slug', 'topic', 'author', 'hits', 'status', 'publish');
        $whereStory = array('id' => $storyId);
        // Get list of story
        $select = $this->getModel('story')->select()->columns($columnStory)->where($whereStory)->order($order);
        $rowset = $this->getModel('story')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $story[$row->id] = $row->toArray();
            $story[$row->id]['publish'] = _date($story[$row->id]['publish']);
        }
        // Set message
        $message = __('Welcome to module managment, You have access for add ,edit or delete some of stroes. ');
        // Set paginator
        $select = $this->getModel('link')->select()->where($whereLink)->columns(array('count' => new \Zend\Db\Sql\Predicate\Expression('count(DISTINCT `story`)')));
        $count = $this->getModel('link')->selectWith($select)->current()->count;
        $paginator = \Pi\Paginator\Paginator::factory(intval($count));
        $paginator->setItemCountPerPage($this->config('show_perpage'));
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(array(
            'template' => $this->url('.news', array('module' => $module, 'controller' => 'management', 'page' => '%page%', 'topic' => $topic, 'status' => $status)),
        ));
        // Set view
        $this->view()->setTemplate('management_index');
        $this->view()->assign('stores', $story);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('topics', $topicList);
        $this->view()->assign('message', $message);
        $this->view()->assign('title', __('News managment'));
    }

    public function removeAction()
    {
        // check Allowed
        $this->AllowedAction($this->params()->fromRoute());
        // Get id and status
        $slug = $this->params('slug');
        // set story
        $story = $this->getModel('story')->find($slug, 'slug');
        // Check
        if ($story && !empty($slug)) {
            // remove file
            $files = array(
                Pi::path('upload/' . $this->config('image_path') . '/original/' . $story->path . '/' . $story->image),
                Pi::path('upload/' . $this->config('image_path') . '/large/' . $story->path . '/' . $story->image),
                Pi::path('upload/' . $this->config('image_path') . '/medium/' . $story->path . '/' . $story->image),
                Pi::path('upload/' . $this->config('image_path') . '/thumb/' . $story->path . '/' . $story->image),
            );
            Pi::service('file')->remove($files);
            // clear DB
            $story->image = '';
            $story->path = '';
            // Save
            if ($story->save()) {
                $message = sprintf(__('Image of %s removed'), $story->title);
                $status = 1;
            } else {
                $message = __('Image not remove');
                $status = 0;
            }
        } else {
            $message = __('Please select story');
            $status = 0;
        }
        return array(
            'status' => $status,
            'message' => $message,
        );
    }

    public function submitAction()
    {
        // get info
        $module = $this->params('module');
        $slug = $this->params('slug');
        // check Allowed
        $this->AllowedAction($this->params()->fromRoute());
        // Set story image url
        $options['imageurl'] = null;
        // Get story
        if ($slug) {
            $values = $this->getModel('story')->find($slug, 'slug')->toArray();
            // Set story image url
            if ($values['image']) {
                $options['imageurl'] = Pi::url('upload/' . $this->config('image_path') . '/thumb/' . $values['path'] . '/' . $values['image']);
                $options['removeurl'] = $this->url('', array('action' => 'remove', 'id' => $values['id']));
            }
        }
        // Get extra field
        $fields = Pi::service('api')->news(array('Extra', 'Get'));
        $options['field'] = $fields['extra'];
        // Set form
        $form = new StoryUserForm('story', $module, $options);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $file = $this->request->getFiles();
            $form->setInputFilter(new StoryUserFilter($options['field']));
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                // Set extra data array
                if (!empty($fields['field'])) {
                    foreach ($fields['field'] as $field) {
                        $extra[$field]['field'] = $field;
                        $extra[$field]['data'] = $values[$field];
                    }
                }
                // Tag
                if (!empty($values['tag'])) {
                    $tag = explode(' ', $values['tag']);
                }
                // upload image
                if (!empty($file['image']['name'])) {
                    // Set path
                    $values['path'] = date('Y') . '/' . date('m');
                    $original_path = $this->config('image_path') . '/original/' . $values['path'];
                    $large_path = $this->config('image_path') . '/large/' . $values['path'];
                    $medium_path = $this->config('image_path') . '/medium/' . $values['path'];
                    $thumb_path = $this->config('image_path') . '/thumb/' . $values['path'];
                    // Do upload
                    $uploader = new Upload(array('destination' => $original_path, 'rename' => $this->ImagePrefix . '%random%'));
                    $uploader->setExtension($this->config('image_extension'));
                    $uploader->setSize($this->config('image_size'));
                    if ($uploader->isValid()) {
                        $uploader->receive();
                        // Get image name
                        $values['image'] = $uploader->getUploaded('image');
                        // Resize
                        Pi::service('api')->news(array('Resize', 'start'), $values['image'], $original_path, $large_path, $this->config('image_largew'), $this->config('image_largeh'));
                        Pi::service('api')->news(array('Resize', 'start'), $values['image'], $original_path, $medium_path, $this->config('image_mediumw'), $this->config('image_mediumh'));
                        Pi::service('api')->news(array('Resize', 'start'), $values['image'], $original_path, $thumb_path, $this->config('image_thumbw'), $this->config('image_thumbh'));
                    } else {
                        $this->jump(array('action' => 'update'), __('Problem in upload image. please try again'));
                    }
                } else {
                    $values['image'] = '';
                }
                /* End upload image */
                foreach (array_keys($values) as $key) {
                    if (!in_array($key, $this->storyColumns)) {
                        unset($values[$key]);
                    }
                }
                // Topics
                $values['topic'] = Json::encode(array_unique($values['topic']));
                // Set time
                if (empty($values['id'])) {
                    $values['create'] = $values['update'] = $values['publish'] = time();
                } else {
                    $values['update'] = time();
                }
                // Set user
                if (empty($values['id'])) {
                    $values['author'] = Pi::registry('user')->id;
                }
                // Set status
                $values['status'] = 1;
                // Set keywords
                $values['keywords'] = Pi::service('api')->news(array('Text', 'keywords'), $values['title']);
                // Set description
                $values['description'] = Pi::service('api')->news(array('Text', 'description'), $values['title']);
                // Set slug
                $values['slug'] = Pi::service('api')->news(array('Text', 'slug'), $values['title'], $values['id'], $this->getModel('story'));
                // Save values
                if (!empty($values['id'])) {
                    $row = $this->getModel('story')->find($values['id']);
                } else {
                    $row = $this->getModel('story')->createRow();
                }
                $row->assign($values);
                $row->save();
                // Topic
                Pi::service('api')->news(array('Topic', 'Set'), $row->id, $row->topic, $row->publish, $row->status, $row->author);
                // Tag
                if (isset($tag) && is_array($tag) && Pi::service('module')->isActive('tag')) {
                    if (empty($values['id'])) {
                        Pi::service('tag')->add($module, $row->id, '', $tag);
                    } else {
                        Pi::service('tag')->update($module, $row->id, '', $tag);
                    }
                }
                // Writer
                if (empty($values['id'])) {
                    Pi::service('api')->news(array('writer', 'add'), $values['author']);
                }
                // Extra
                if (!empty($extra)) {
                    Pi::service('api')->news(array('Extra', 'Set'), $extra, $row->id);
                }
                // Check it save or not
                if ($row->id) {
                    $message = __('Your selected stroy delete successfully.');
                    $this->jump(array('route' => '.news', 'module' => $module, 'controller' => 'management'), $message);
                } else {
                    $message = __('Story data not saved.');
                }
            } else {
                $message = __('Invalid data, please check and re-submit.');
            }
        } else {
            if (!empty($slug)) {
                if ($values['status'] == 5) {
                    $message = __('Error in Edit story');
                    $this->jump(array('route' => '.news', 'module' => $module, 'controller' => 'management'), $message);
                }
                // Get Extra
                $values = Pi::service('api')->news(array('Extra', 'Form'), $values);
                // Get tag list
                if (Pi::service('module')->isActive('tag')) {
                    $tag = Pi::service('tag')->get($module, $values['id'], '');
                    if (is_array($tag)) {
                        $values['tag'] = implode(' ', $tag);
                    }
                }
                $values['topic'] = Json::decode($values['topic']);
                $form->setData($values);
                $message = 'You can edit this Story';
            } else {
                $message = 'You can add new Story';
            }
        }
        $this->view()->setTemplate('management_submit');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add a Story'));
        $this->view()->assign('message', $message);
    }

    public function deleteAction()
    {
        // Get page ID or slug from url
        $module = $this->params('module');
        $slug = $this->params('slug');
        // check Allowed
        $topic = $this->AllowedAction($this->params()->fromRoute());
        // Get row
        $row = $this->getModel('story')->find($slug, 'slug');
        if (!isset($row) || $row->status == 5 || !in_array($row->topic, $topic)) {
            $this->jump(array('route' => '.news', 'module' => $module, 'controller' => 'management'), __('Error Delete story'));
        }
        // Update Status in story table
        $this->getModel('story')->update(array('status' => 5), array('id' => $row->id));
        // Update Status in link table
        $this->getModel('link')->update(array('status' => 5), array('story' => $row->id));
        // back
        $this->jump(array('route' => '.news', 'module' => $module, 'controller' => 'management'), __('Your selected stroy delete successfully.'));
        //view
        $this->view()->setTemplate(false);
    }
}
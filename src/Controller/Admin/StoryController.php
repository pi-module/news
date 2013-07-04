<?php
/**
 * News admin story controller
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

namespace Module\News\Controller\Admin;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Pi\File\Transfer\Upload;
use Module\News\Form\StoryForm;
use Module\News\Form\StoryFilter;
use Zend\Json\Json;

class StoryController extends ActionController
{
    protected $ImagePrefix = 'image_';
    protected $storyColumns = array(
        'id', 'title', 'subtitle', 'alias', 'topic', 'short', 'body', 'keywords', 'description', 'important', 'status', 'create', 
        'update', 'publish', 'author', 'hits', 'image', 'path', 'comments', 'point', 'count', 'favorite', 'attach', 'extra'
    );

    public function indexAction()
    {
        // Get page
        $page = $this->params('p', 1);
        $module = $this->params('module');
        $status = $this->params('status');
        $topic = $this->params('topic');
        $author = $this->params('author');
        // Set info
        $offset = (int)($page - 1) * $this->config('admin_perpage');
        $order = array('publish DESC', 'id DESC');
        $limit = intval($this->config('admin_perpage'));
        // Set where
        $whereLink = array();
        if (!empty($status)) {
            $whereLink['status'] = $status;
        }
        if (!empty($topic)) {
            $whereLink['topic'] = $topic;
        }
        if (!empty($author)) {
            $whereLink['author'] = $author;
        }
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
        $columnStory = array('id', 'title', 'alias', 'status', 'publish', 'author');
        $whereStory = array('id' => $storyId);
        // Get list of story
        $select = $this->getModel('story')->select()->columns($columnStory)->where($whereStory)->order($order);
        $rowset = $this->getModel('story')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $story[$row->id] = $row->toArray();
            $story[$row->id]['publish'] = _date($story[$row->id]['publish']);
        }
        // Go to update page if empty
        if (empty($story) && empty($status)) {
            return $this->redirect()->toRoute('', array('action' => 'update'));
        }
        // Set paginator
        $select = $this->getModel('link')->select()->where($whereLink)->columns(array('count' => new \Zend\Db\Sql\Predicate\Expression('count(DISTINCT `story`)')));
        $count = $this->getModel('link')->selectWith($select)->current()->count;
        $paginator = \Pi\Paginator\Paginator::factory(intval($count));
        $paginator->setItemCountPerPage($this->config('admin_perpage'));
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(array(
            // Use router to build URL for each page
            'pageParam' => 'p',
            'totalParam' => 't',
            'router' => $this->getEvent()->getRouter(),
            'route' => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
            'params' => array(
                'module' => $this->getModule(),
                'controller' => 'story',
                'action' => 'index',
            ),
            // Or use a URL template to create URLs
            //'template'      => '/url/p/%page%/t/%total%',

        ));
        // Set view
        $this->view()->setTemplate('story_index');
        $this->view()->assign('stores', $story);
        $this->view()->assign('paginator', $paginator);

    }

    public function viewAction()
    {
        // Get info
        $module = $this->params('module');
        // Set template
        $this->view()->setTemplate('story_view');
        // Get story
        $id = $this->params('id');
        $story = $this->getModel('story')->find($id)->toArray();
        $story['publish'] = _date($story['publish']);
        // Check message
        if (!$story['id']) {
            $this->jump(array('action' => 'index'), __('Please select story'));
        }
        // Get topic
        $topics = Pi::service('api')->news(array('Story', 'Topic'), $story['topic']);
        // Set view
        $this->view()->assign('topics', $topics);
        $this->view()->assign('story', $story);
    }

    public function acceptAction()
    {
        // Get id and status
        $id = $this->params('id');
        $status = $this->params('status');
        $return = array();
        // set story
        $story = $this->getModel('story')->find($id);
        // Check
        if ($story && in_array($status, array(1, 2, 3, 4, 5))) {
            // Accept
            $story->status = $status;
            // Save
            if ($story->save()) {
                $this->getModel('link')->update(array('status' => $story->status), array('story' => $story->id));
                $return['message'] = sprintf(__('%s story accept successfully'), $story->title);
                $return['ajaxstatus'] = 1;
                $return['id'] = $story->id;
                $return['storystatus'] = $story->status;
            } else {
                $return['message'] = sprintf(__('Error in accept %s story'), $story->title);
                $return['ajaxstatus'] = 0;
                $return['id'] = 0;
                $return['storystatus'] = $story->status;
            }
        } else {
            $return['message'] = __('Please select story');
            $return['ajaxstatus'] = 0;
            $return['id'] = 0;
            $return['storystatus'] = 0;
        }

        return $return;
    }


    public function removeAction()
    {
        // Get id and status
        $id = $this->params('id');
        // set story
        $story = $this->getModel('story')->find($id);
        // Check
        if ($story && !empty($id)) {
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

    public function updateAction()
    {
        // Get id
        $id = $this->params('id');
        $module = $this->params('module');
        // Set story image url
        $options['imageurl'] = null;
        // Get story
        if ($id) {
            $values = $this->getModel('story')->find($id)->toArray();
            // Set story image url
            if ($values['image']) {
                $options['imageurl'] = Pi::url('upload/' . $this->config('image_path') . '/thumb/' . $values['path'] . '/' . $values['image']);
                $options['removeurl'] = $this->url('', array('action' => 'remove', 'id' => $values['id']));
            }
        }
        // Get extra field
        $fields = Pi::service('api')->news(array('Extra', 'Get'));
        $options['field'] = $fields['extra'];
        // Set link array
        $link['attach'] = '#';
        // Set form
        $form = new StoryForm('story', $module, $options);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $file = $this->request->getFiles();
            $form->setInputFilter(new StoryFilter($options['field']));
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
                    $addSlide = $values['slide'];
                } elseif (!isset($values['image'])) {
	                $values['image'] = '';	
                }
                // Set just story fields
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
                // Set keywords
                $keywords = ($values['keywords']) ? $values['keywords'] : $values['title'];
                $values['keywords'] = Pi::service('api')->news(array('Text', 'keywords'), $keywords);
                // Set description
                $description = ($values['description']) ? $values['description'] : $values['title'];
                $values['description'] = Pi::service('api')->news(array('Text', 'description'), $description);
                // Set alias
                $alias = ($values['alias']) ? $values['alias'] : $values['title'];
                $values['alias'] = Pi::service('api')->news(array('Text', 'alias'), $alias, $values['id'], $this->getModel('story'));
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
                    Pi::service('api')->news(array('Writer', 'Add'), $values['author']);
                }
                // Extra
                if (!empty($extra)) {
                    Pi::service('api')->news(array('Extra', 'Set'), $extra, $row->id);
                }
                // Add as slide
                if ($addSlide && Pi::service('module')->isActive('slide')) {
                    $slide['title'] = $values['title'];
                    $slide['description'] = $values['short'];
                    $slide['url'] = $this->url('.news', array('module' => $module, 'controller' => 'story', 'alias' => $values['alias']));
                    $slide['image'] = $medium_path;
                    Pi::service('api')->slide(array('add', 'slide'), $slide);
                }
                // Check it save or not
                if ($row->id) {
                    $message = __('Story data saved successfully.');
                    $url = array('controller' => 'story', 'action' => 'index');
                    $this->jump($url, $message);
                } else {
                    $message = __('Story data not saved.');
                }
            } else {
                $message = __('Invalid data, please check and re-submit.');
            }
        } else {
            if ($id) {
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
                $link['attach'] = $this->url('', array('controller' => 'attach', 'action' => 'add', 'id' => $values['id']));
            } else {
                $message = 'You can add new Story';
            }
        }
        $this->view()->setTemplate('story_update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add a Story'));
        $this->view()->assign('message', $message);
        $this->view()->assign('link', $link);
        $this->view()->assign('slide', Pi::service('module')->isActive('slide'));
    }

    public function deleteAction()
    {
        /*
           * not completed and need confirm option
           */
        // Get information
        $this->view()->setTemplate(false);
        $id = $this->params('id');
        $row = $this->getModel('story')->find($id);
        if ($row) {
            // Writer
            Pi::service('api')->news(array('Writer', 'Delete'), $row->author);
            // Topic
            $this->getModel('link')->delete(array('story' => $row->id));
            // Attach
            $this->getModel('attach')->delete(array('story' => $row->id));
            // Extra
            $this->getModel('data')->delete(array(story => $row->id));
            // Spotlight
            $this->getModel('spotlight')->delete(array('story' => $row->id));
            // Remove page
            $row->delete();
            $this->jump(array('action' => 'index'), __('This story deleted'));
        }
        $this->jump(array('action' => 'index'), __('Please select story'));
    }
}
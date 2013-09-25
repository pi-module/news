<?php
/**
 * News admin topic controller
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
use Module\News\Form\TopicForm;
use Module\News\Form\TopicFilter;
use Zend\Json\Json;

class TopicController extends ActionController
{
    protected $ImagePrefix = 'topic_';
    protected $topicColumns = array(
        'id', 'pid', 'title', 'slug', 'body', 'image', 'path', 'keywords', 'description', 'topic_type', 'topic_style', 'perpage', 'columns', 'author', 'create', 'status',
        'showtopic', 'showtopicinfo', 'showauthor', 'showdate', 'showpdf', 'showprint', 'showmail', 'shownav', 'showhits', 'showcoms', 'topic_homepage', 'inlist'
    );

    public function indexAction()
    {
        // Get page
        $page = $this->params('p', 1);
        $module = $this->params('module');
        // Get info
        $columns = array('id', 'title', 'slug', 'topic_type', 'status');
        $order = array('id DESC', 'create DESC');
        $select = $this->getModel('topic')->select()->columns($columns)->order($order);
        $rowset = $this->getModel('topic')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $list[$row->id] = $row->toArray();
        }
        // Go to update page if empty
        if (empty($list)) {
            return $this->redirect()->toRoute('', array('action' => 'update'));
        }
        // Set paginator
        $paginator = \Pi\Paginator\Paginator::factory($list);
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
                'controller' => 'topic',
                'action' => 'index',
            ),
            // Or use a URL template to create URLs
            //'template'      => '/url/p/%page%/t/%total%',

        ));
        // Set view
        $this->view()->setTemplate('topic_index');
        $this->view()->assign('topics', $paginator);
    }

    public function removeAction()
    {
        // Get id and status
        $id = $this->params('id');
        // set story
        $topic = $this->getModel('topic')->find($id);
        // Check
        if ($topic && !empty($id)) {
            // remove file
            $files = array(
                Pi::path('upload/' . $this->config('image_path') . '/original/' . $topic->path . '/' . $topic->image),
                Pi::path('upload/' . $this->config('image_path') . '/large/' . $topic->path . '/' . $topic->image),
                Pi::path('upload/' . $this->config('image_path') . '/medium/' . $topic->path . '/' . $topic->image),
                Pi::path('upload/' . $this->config('image_path') . '/thumb/' . $topic->path . '/' . $topic->image),
            );
            Pi::service('file')->remove($files);
            // clear DB
            $topic->image = '';
            $topic->path = '';
            // Save
            if ($topic->save()) {
                $message = sprintf(__('Image of %s removed'), $topic->title);
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
            $topic = $this->getModel('topic')->find($id)->toArray();
            // Set story image url
            if ($row['image']) {
                $options['imageurl'] = Pi::url('upload/' . $this->config('image_path') . '/thumb/' . $topic['path'] . '/' . $topic['image']);
                $options['removeurl'] = $this->url('', array('action' => 'remove', 'id' => $topic['id']));
            }
        }
        // Set form
        $form = new TopicForm('topic', $module, $options);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $file = $this->request->getFiles();
            $form->setInputFilter(new TopicFilter);
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
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
                        Pi::service('api')->news(array('Resize', 'start'), $values['image'], $original_path, $large_path, '8000', '8000');
                        Pi::service('api')->news(array('Resize', 'start'), $values['image'], $original_path, $medium_path, '8000', '8000');
                        Pi::service('api')->news(array('Resize', 'start'), $values['image'], $original_path, $thumb_path, '8000', '8000');
                    } else {
                        $this->jump(array('action' => 'update'), __('Problem in upload image. please try again'));
                    }
                } elseif (!isset($values['image'])) {
	                $values['image'] = '';	
                }	
                /* End upload image */
                foreach (array_keys($values) as $key) {
                    if (!in_array($key, $this->topicColumns)) {
                        unset($values[$key]);
                    }
                }
                // Set topic_homepage
                $values['topic_homepage'] = ($values['topic_homepage']) ? $values['topic_homepage'] : $this->config('show_homepage');
                // Set topic_style
                $values['topic_style'] = ($values['topic_style']) ? $values['topic_style'] : $this->config('show_shwotype');
                // Set perpage
                $values['perpage'] = ($values['perpage']) ? $values['perpage'] : $this->config('show_perpage');
                // Set columns
                $values['columns'] = ($values['columns']) ? $values['columns'] : $this->config('show_columns');
                // Set keywords
                $keywords = ($values['keywords']) ? $values['keywords'] : $values['title'];
                $values['keywords'] = Pi::service('api')->news(array('Text', 'keywords'), $keywords);
                // Set description
                $description = ($values['description']) ? $values['description'] : $values['title'];
                $values['description'] = Pi::service('api')->news(array('Text', 'description'), $description);
                // Set slug
                $slug = ($values['slug']) ? $values['slug'] : $values['title'];
                $values['slug'] = Pi::service('api')->news(array('Text', 'slug'), $slug, $values['id'], $this->getModel('topic'));
                // Set if new
                if (empty($values['id'])) {
                    // Set time
                    $values['create'] = time();
                    // Set user
                    $values['author'] = Pi::registry('user')->id;
                }
                // Save values
                if (!empty($values['id'])) {
                    $row = $this->getModel('topic')->find($values['id']);
                } else {
                    $row = $this->getModel('topic')->createRow();
                }
                $row->assign($values);
                $row->save();
                // Check it save or not
                if ($row->id) {
                	  // Set topic as page for dress up block 
                	  if(empty($values['id'])) {
	                	  $this->setPage($row->slug, $row->title);
                	  } else {	
                	  	  $this->updatePage($topic['slug'], $row->slug, $row->title);
                    }
                    Pi::service('registry')->page->clear($this->getModule());
                    $message = __('Topic data saved successfully.');
                    $this->jump(array('action' => 'index'), $message);
                } else {
                    $message = __('Topic data not saved.');
                }
            } else {
                $message = __('Invalid data, please check and re-submit.');
            }
        } else {
            if ($id) {
                $form->setData($topic);
                $message = 'You can edit this Topic';
            } else {
                $message = 'You can add new Topic';
            }
        }
        $this->view()->setTemplate('topic_update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add a Topic'));
        $this->view()->assign('message', $message);
    }

    public function deleteAction()
    {
        /*
           * not completed and need confirm option
           */
        // Get information
        $this->view()->setTemplate(false);
        $id = $this->params('id');
        $row = $this->getModel('topic')->find($id);
        if ($row) {
            // Delete writers
            Pi::service('api')->news(array('Writer', 'DeleteTopic'), $row->id);
            // Update sub topics
            $this->getModel('topic')->update(array('pid' => $row->pid), array('pid' => $row->id));
            // remove topic links
            $this->getModel('link')->delete(array('topic' => $row->id));
            // remove moderator
            $this->getModel('moderator')->delete(array('topic' => $row->id));
            // Get this topic stores and remove
            $select = $this->getModel('story')->select()->columns(array('id', 'path', 'image'))->where(array('topic' => Json::encode(array($row->id))));
            $rowset = $this->getModel('story')->selectWith($select)->toArray();
            foreach ($rowset as $story) {
                // Attach
                $this->getModel('attach')->delete(array('story' => $story['id']));
                // Extra
                $this->getModel('data')->delete(array(story => $story['id']));
                // Spotlight
                $this->getModel('spotlight')->delete(array('story' => $story['id']));
                // Remove story images
                $files = array(
                    Pi::path('upload/' . $this->config('image_path') . '/original/' . $story['path'] . '/' . $story['image']),
                    Pi::path('upload/' . $this->config('image_path') . '/large/' . $story['path'] . '/' . $story['image']),
                    Pi::path('upload/' . $this->config('image_path') . '/medium/' . $story['path'] . '/' . $story['image']),
                    Pi::path('upload/' . $this->config('image_path') . '/thumb/' . $story['path'] . '/' . $story['image']),
                );
                Pi::service('file')->remove($files);
                // Story
                $this->getModel('story')->delete(array('id' => $story['id']));
            }
            // Remove topic images
            $files = array(
                Pi::path('upload/' . $this->config('image_path') . '/original/' . $topic->path . '/' . $topic->image),
                Pi::path('upload/' . $this->config('image_path') . '/large/' . $topic->path . '/' . $topic->image),
                Pi::path('upload/' . $this->config('image_path') . '/medium/' . $topic->path . '/' . $topic->image),
                Pi::path('upload/' . $this->config('image_path') . '/thumb/' . $topic->path . '/' . $topic->image),
            );
            Pi::service('file')->remove($files);
            // Remove page
            $this->removePage($row->slug);
            Pi::service('registry')->page->clear($this->getModule());
            // Remove topic
            $row->delete();
            $this->jump(array('action' => 'index'), __('This topic and all of stores deleted'));
        }
        $this->jump(array('action' => 'index'), __('Please select topic'));
    }
    
    /**
     * Add page settings to system
     *
     * @author Taiwen Jiang <taiwenjiang@tsinghua.org.cn>
     * @param string $name
     * @param string $title
     * @return int
     */
    protected function setPage($name, $title)
    {
        $page = array(
            'section'       => 'front',
            'module'        => $this->getModule(),
            'controller'    => 'topic',
            'action'        => $name,
            'title'         => $title,
            'block'         => 1,
            'custom'        => 0,
        );
        $row = Pi::model('page')->createRow($page);
        $row->save();
        return $row->id;
    }

    /**
     * Remove from system page settings
     *
     * @author Taiwen Jiang <taiwenjiang@tsinghua.org.cn>
     * @param stinr $name
     * @return int
     */
    protected function removePage($name)
    {
        $where = array(
            'section'       => 'front',
            'module'        => $this->getModule(),
            'controller'    => 'topic',
            'action'        => $name,
        );
        $count = Pi::model('page')->delete($where);
        return $count;
    }
    
    /**
     * Update from system page settings
     *
     * @param stinr $name
     * @return int
     */
    protected function updatePage($old_action, $new_action, $new_title)
    {
        $where = array(
            'section'       => 'front',
            'module'        => $this->getModule(),
            'controller'    => 'topic',
            'action'        => $old_action,
        );
        $count = Pi::model('page')->update(array('action' => $new_action, 'title' => $new_title), $where);
        return $count;
    }
}
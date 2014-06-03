<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
namespace Module\News\Controller\Admin;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Pi\Paginator\Paginator;
use Pi\File\Transfer\Upload;
use Module\News\Form\StoryForm;
use Module\News\Form\StoryFilter;
use Module\News\Form\StorySearchForm;
use Module\News\Form\StorySearchFilter;
use Zend\Json\Json;

class StoryController extends ActionController
{
    protected $ImageStoryPrefix = 'image_';

    protected $storyColumns = array(
        'id', 'title', 'subtitle', 'slug', 'topic', 'author', 'short', 'body', 'seo_title', 'seo_keywords', 
        'seo_description', 'important', 'status', 'time_create', 'time_update', 'time_publish', 
        'uid', 'hits', 'image', 'path', 'point', 'count', 'favorite', 'attach', 'extra', 'type'
    );

    public function indexAction()
    {
        // Get page
        $page = $this->params('page', 1);
        $module = $this->params('module');
        $status = $this->params('status');
        $topic = $this->params('topic');
        $uid = $this->params('uid');
        $title = $this->params('title');
        // Set info
        $offset = (int)($page - 1) * $this->config('admin_perpage');
        $order = array('time_publish DESC', 'id DESC');
        $limit = intval($this->config('admin_perpage'));
        // Get
        if (empty($title)) {
            // Set where
            $whereLink = array();
            if (!empty($status)) {
                $whereLink['status'] = $status;
            }
            if (!empty($topic)) {
                $whereLink['topic'] = $topic;
            }
            if (!empty($uid)) {
                $whereLink['uid'] = $uid;
            }
            // Set columns
            $columnsLink = array('story' => new \Zend\Db\Sql\Predicate\Expression('DISTINCT story'));
            // Get info from link table
            $select = $this->getModel('link')->select()->where($whereLink)->columns($columnsLink)->order($order)->offset($offset)->limit($limit);
            $rowset = $this->getModel('link')->selectWith($select)->toArray();
            // Make list
            foreach ($rowset as $id) {
                $storyId[] = $id['story'];
            }
            // Set info
            $whereStory = array('id' => $storyId);
        } else {
            $whereStory = array();
            $whereStory['title LIKE ?'] = '%' . $title . '%';
        }
        // Set info
        $columnStory = array('id', 'title', 'slug', 'status', 'time_publish', 'uid', 'type');
        // Get list of story
        $select = $this->getModel('story')->select()->columns($columnStory)->where($whereStory)->order($order);
        $rowset = $this->getModel('story')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $story[$row->id] = $row->toArray();
            $story[$row->id]['time_publish'] = _date($story[$row->id]['time_publish']);
            // Set story type view
            switch ($row->type) {
                case 'download':
                    $story[$row->id]['type_view'] = __('Download');
                    break;
                    
                case 'media':
                    $story[$row->id]['type_view'] = __('Media');
                    break;
                    
                case 'gallery':
                    $story[$row->id]['type_view'] = __('Gallery');
                    break;           

                case 'text':
                default:
                    $story[$row->id]['type_view'] = __('Text');
                    break;
            }
        }
        // Go to time_update page if empty
        if (empty($story) && empty($status)) {
            return $this->redirect()->toRoute('', array('action' => 'update'));
        }
        // Set paginator
        $count = array('count' => new \Zend\Db\Sql\Predicate\Expression('count(DISTINCT `story`)'));
        $select = $this->getModel('link')->select()->where($whereLink)->columns($count);
        $count = $this->getModel('link')->selectWith($select)->current()->count;
        $paginator = Paginator::factory(intval($count));
        $paginator->setItemCountPerPage($this->config('admin_perpage'));
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(array(
            'router'    => $this->getEvent()->getRouter(),
            'route'     => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
            'params'    => array_filter(array(
                'module'        => $this->getModule(),
                'controller'    => 'story',
                'action'        => 'index',
                'status'        => $status,
                'topic'         => $topic,
                'uid'           => $uid,
                'title'         => $title,
            )),
        ));
        // Set form
        $values = array(
            'title' => $title,
        );
        $form = new StorySearchForm('search');
        $form->setAttribute('action', $this->url('', array('action' => 'process')));
        $form->setData($values);
        // Set view
        $this->view()->setTemplate('story_index');
        $this->view()->assign('stores', $story);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('form', $form);
    }

    public function processAction()
    {
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form = new StorySearchForm('search');
            $form->setInputFilter(new StorySearchFilter());
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                $message = __('View filtered stores');
                $url = array(
                    'action' => 'index',
                    'title' => $values['title'],
                );
            } else {
                $message = __('Not valid');
                $url = array(
                    'action' => 'index',
                );
            }
        } else {
            $message = __('Not set');
            $url = array(
                'action' => 'index',
            );
        } 
        return $this->jump($url, $message);  
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
        $story['time_publish'] = _date($story['time_publish']);
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
            /* $files = array(
                Pi::path('upload/' . $this->config('image_path') . '/original/' . $story->path . '/' . $story->image),
                Pi::path('upload/' . $this->config('image_path') . '/large/' . $story->path . '/' . $story->image),
                Pi::path('upload/' . $this->config('image_path') . '/medium/' . $story->path . '/' . $story->image),
                Pi::path('upload/' . $this->config('image_path') . '/thumb/' . $story->path . '/' . $story->image),
            );
            Pi::service('file')->remove($files); */
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
        $type = $this->params('type');
        $option = array();
        // Find Product
        if ($id) {
            $story = $this->getModel('story')->find($id)->toArray();
            $story['topic'] = Json::decode($story['topic']);
            if ($story['image']) {
                $thumbUrl = sprintf('upload/%s/thumb/%s/%s', $this->config('image_path'), $story['path'], $story['image']);
                $option['thumbUrl'] = Pi::url($thumbUrl);
                $option['removeUrl'] = $this->url('', array('action' => 'remove', 'id' => $story['id']));
            }
        }
        // Get extra field
        $fields = Pi::api('extra', 'news')->Get();
        $option['field'] = $fields['extra'];
        // Set author
        if ($this->config('admin_setauthor')) {
            $option['author'] = Pi::api('author', 'news')->getFormAuthor();
            $option['role'] = Pi::api('author', 'news')->getFormRole();
        } else {
            $option['author'] = '';
            $option['role'] = '';
        }
        // Set form
        $form = new StoryForm('story', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $file = $this->request->getFiles();
            // Set slug
            $slug = ($data['slug']) ? $data['slug'] : $data['title'];
            $data['slug'] = Pi::api('text', 'news')->slug($slug);
            // Form filter
            $form->setInputFilter(new StoryFilter($option));
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                // Set author
                $author =  array();
                if (!empty($option['role'])) {
                    foreach ($option['role'] as $role) {
                        if ($values[$role['name']] > 0) {
                            $author[$role['id']]['role'] = $role['id'];
                            $author[$role['id']]['author'] = $values[$role['name']];
                        }
                    }
                }
                // Set extra data array
                if (!empty($fields['field'])) {
                    foreach ($fields['field'] as $field) {
                        $extra[$field]['field'] = $field;
                        $extra[$field]['data'] = $values[$field];
                    }
                }
                // Tag
                if (!empty($values['tag'])) {
                    $tag = explode('|', $values['tag']);
                }
                // upload image
                if (!empty($file['image']['name'])) {
                    // Set upload path
                    $values['path'] = sprintf('%s/%s', date('Y'), date('m'));
                    $originalPath = Pi::path(sprintf('upload/%s/original/%s', $this->config('image_path'), $values['path']));
                    // Upload
                    $uploader = new Upload;
                    $uploader->setDestination($originalPath);
                    $uploader->setRename($this->ImageStoryPrefix . '%random%');
                    $uploader->setExtension($this->config('image_extension'));
                    $uploader->setSize($this->config('image_size'));
                    if ($uploader->isValid()) {
                        $uploader->receive();
                        // Get image name
                        $values['image'] = $uploader->getUploaded('image');
                        // process image
                        Pi::api('image', 'news')->process($values['image'], $values['path']);
                    } else {
                        $this->jump(array('action' => 'update'), __('Problem in upload image. please try again'));
                    }
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
                // Author
                $values['author'] = (empty($author)) ? '' : Json::encode($author);
                // Set seo_title
                $title = ($values['seo_title']) ? $values['seo_title'] : $values['title'];
                $values['seo_title'] = Pi::api('text', 'news')->title($title);
                // Set seo_keywords
                $keywords = ($values['seo_keywords']) ? $values['seo_keywords'] : $values['title'];
                $values['seo_keywords'] = Pi::api('text', 'news')->keywords($keywords);
                // Set seo_description
                $description = ($values['seo_description']) ? $values['seo_description'] : $values['title'];
                $values['seo_description'] = Pi::api('text', 'news')->description($description);
                // Set time
                if (empty($values['id'])) {
                    $values['time_create'] = time();
                    $values['time_publish'] = time();
                    $values['uid'] = Pi::user()->getId();
                }
                $values['time_update'] = time();
                // Save values
                if (!empty($values['id'])) {
                    $row = $this->getModel('story')->find($values['id']);
                } else {
                    $row = $this->getModel('story')->createRow();
                }
                $row->assign($values);
                $row->save();
                // Topic
                Pi::api('topic', 'news')->setLink($row->id, $row->topic, $row->time_publish, $row->status, $row->uid);
                // Author
                Pi::api('author', 'news')->setAuthorStory($row->id, $row->time_publish, $row->status, $author);
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
                    Pi::api('writer', 'news')->Add($values['uid']);
                }
                // Extra
                if (!empty($extra)) {
                    Pi::api('extra', 'news')->Set($extra, $row->id);
                }
                // Add / Edit sitemap
                if (Pi::service('module')->isActive('sitemap')) {
                    $loc = Pi::url($this->url('news', array(
                        'module' => $module, 
                        'controller' => 'story', 
                        'slug' => $values['slug']
                    )));
                    if (empty($values['id'])) {
                        Pi::api('sitemap', 'sitemap')->add('news', 'story', $row->id, $loc);
                    } else {
                        Pi::api('sitemap', 'sitemap')->update('news', 'story', $row->id, $loc);
                    }              
                }
                // Make jump information
                switch ($row->type) {
                    case 'download':
                        $message = __('Download data saved successfully. Please attach your files');
                        $url = array('controller' => 'attach', 'action' => 'add', 'id' => $row->id);
                        break;
                    
                    case 'media':
                        $message = __('Media data saved successfully. Please attach your medias');
                        $url = array('controller' => 'attach', 'action' => 'add', 'id' => $row->id);
                        break;
                    
                    case 'gallery':
                        $message = __('Gallery data saved successfully. Please attach your images');
                        $url = array('controller' => 'attach', 'action' => 'add', 'id' => $row->id);
                        break;           

                    case 'text':
                    default:
                        $message = __('Text data saved successfully.');
                        $url = array('controller' => 'story', 'action' => 'index');
                        break;
                }
                // Do jump
                $this->jump($url, $message);
            } else {
                $message = __('Invalid data, please check and re-submit.');
            }
        } else {
            if ($id) {
                // Get Extra
                $story = Pi::api('extra', 'news')->setFormValues($story);
                // Get author
                $story = Pi::api('author', 'news')->setFormValues($story);
                // Get tag list
                if (Pi::service('module')->isActive('tag')) {
                    $tag = Pi::service('tag')->get($module, $story['id'], '');
                    if (is_array($tag)) {
                        $story['tag'] = implode('|', $tag);
                    }
                }
                $form->setData($story);
                $type = $story['type'];
            } else {
                $story = array('type' => $type);
                $form->setData($story);
            }
        }
        // Set type message
        switch ($type) {
            case 'download':
                $message = __('Your story type is <strong>Download</strong> , first please completion this form , after that if you see extra file fields, you can add your file urls , and after click on submit button , you can upload your download files on next page');
                break;
                   
            case 'media':
                $message = __('Your story type is <strong>Media</strong> , first please completion this form , after that if you see extra video or audio fields, you can add your video or audio urls for play on website media player, and after click on submit button , you can upload your media on next page');
                break;
                    
            case 'gallery':
                $message = __('Your story type is <strong>Gallery</strong> , first please completion this form , after click on submit button , you can upload your images on next page');
                break;           

            case 'text':
            default:
                $message = __('Your story type is <strong>Text</strong> and you shuold add text information on form fields');
                break;
        }

        $this->view()->setTemplate('story_update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add a Story'));
        $this->view()->assign('message', $message);
    }

    public function deleteAction()
    {
        // Get information
        $this->view()->setTemplate(false);
        $id = $this->params('id');
        $row = $this->getModel('story')->find($id);
        if ($row) {
            // Writer
            Pi::service('api')->news(array('Writer', 'Delete'), $row->uid);
            // Topic
            $this->getModel('link')->delete(array('story' => $row->id));
            // Attach
            $this->getModel('attach')->delete(array('story' => $row->id));
            // Extra
            $this->getModel('field_data')->delete(array('story' => $row->id));
            // Spotlight
            $this->getModel('spotlight')->delete(array('story' => $row->id));
            // Remove sitemap
            if (Pi::service('module')->isActive('sitemap')) {
                $loc = Pi::url($this->url('news', array(
                        'module' => $module, 
                        'controller' => 'story', 
                        'slug' => $row->slug
                    )));
                Pi::api('sitemap', 'sitemap')->remove($loc);
            } 
            // Remove page
            $row->delete();
            $this->jump(array('action' => 'index'), __('This story deleted'));
        }
        $this->jump(array('action' => 'index'), __('Please select story'));
    }
}
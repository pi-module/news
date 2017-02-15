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
use Pi\Filter;
use Pi\Mvc\Controller\ActionController;
use Pi\Paginator\Paginator;
use Pi\File\Transfer\Upload;
use Module\News\Form\StoryForm;
use Module\News\Form\StoryFilter;
use Module\News\Form\StoryAdditionalForm;
use Module\News\Form\StoryAdditionalFilter;
use Module\News\Form\StorySearchForm;
use Module\News\Form\StorySearchFilter;
use Zend\Db\Sql\Predicate\Expression;

class StoryController extends ActionController
{
    protected $ImageStoryPrefix = 'image-';

    public function indexAction()
    {
        // Get page
        $page = $this->params('page', 1);
        $module = $this->params('module');
        $status = $this->params('status');
        $topic = $this->params('topic');
        $uid = $this->params('uid');
        $title = $this->params('title');
        // Get Module Config
        $config = Pi::service('registry')->config->read($module);
        // Set info
        $offset = (int)($page - 1) * $this->config('admin_perpage');
        $order = array('id DESC');
        $limit = intval($this->config('admin_perpage'));
        $columnStory = array('id', 'title', 'slug', 'status', 'time_publish', 'uid', 'type');
        $users = array();
        // Set where
        $whereStory = array();
        if (empty($title)) {
            $whereStory['type'] = array(
                'text', 'post', 'article', 'magazine', 'image', 'gallery', 'media', 'download'
            );
            if (!empty($status) && in_array($status, array(1, 2, 3, 4, 5))) {
                $whereStory['status'] = $status;
            } elseif (!empty($status) && $status == 6) {
                $whereStory['status'] = 6;
            } else {
                $whereStory['status'] = array(1, 2, 3, 4);
            }
            if (!empty($uid)) {
                $whereStory['uid'] = $uid;
            }
        } else {
            $whereStory['title LIKE ?'] = '%' . $title . '%';
            $whereStory['status'] = array(1, 2, 3, 4, 5);
            $whereStory['type'] = array(
                'text', 'post', 'article', 'magazine', 'image', 'gallery', 'media', 'download'
            );
        }
        // Get list of story
        $select = $this->getModel('story')->select()->columns($columnStory)->where($whereStory)->order($order)->offset($offset)->limit($limit);
        $rowset = $this->getModel('story')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $story[$row->id] = $row->toArray();
            $story[$row->id]['time_publish'] = _date($story[$row->id]['time_publish']);
            // Get use
            if (isset($users[$row->uid]) && !empty($users[$row->uid])) {
                $story[$row->id]['user'] = $users[$row->uid];
            } else {
                $user = Pi::user()->get($row->uid, array(
                    'id', 'identity', 'name', 'email'
                ));
                $story[$row->id]['user'] = $user;
                $users[$row->uid] = $user;
            }
            // Set url
            if ($row->status == 1) {
                $story[$row->id]['storyUrl'] = $this->url('news', array(
                    'module' => $module,
                    'controller' => 'story',
                    'slug' => $row->slug
                ));
            } else {
                $story[$row->id]['storyUrl'] = '';
            }
            // Set story type view
            switch ($row->type) {
                case 'article':
                    $story[$row->id]['type_view'] = __('Article');
                    break;

                case 'magazine':
                    $story[$row->id]['type_view'] = __('Magazine');
                    break;

                case 'download':
                    $story[$row->id]['type_view'] = __('Download');
                    break;

                case 'media':
                    $story[$row->id]['type_view'] = __('Media');
                    break;

                case 'gallery':
                    $story[$row->id]['type_view'] = __('Gallery album');
                    break;

                case 'image':
                    $story[$row->id]['type_view'] = __('Single image');
                    break;

                case 'post':
                    // Set type
                    $story[$row->id]['type_view'] = __('Blog post');
                    // Set url
                    if (Pi::service('module')->isActive('blog')) {
                        if ($row->status == 1) {
                            $story[$row->id]['storyUrl'] = $this->url('blog', array(
                                'module' => 'blog',
                                'controller' => 'post',
                                'slug' => $row->slug
                            ));
                        }
                    } else {
                        $story[$row->id]['storyUrl'] = '';
                    }
                    break;

                case 'text':
                default:
                    $story[$row->id]['type_view'] = __('Text');
                    break;
            }
        }
        // Set count
        $columnsLink = array('count' => new Expression('count(*)'));
        $select = $this->getModel('story')->select()->where($whereStory)->columns($columnsLink);
        $count = $this->getModel('story')->selectWith($select)->current()->count;
        // Set paginator
        $paginator = Paginator::factory(intval($count));
        $paginator->setItemCountPerPage($this->config('admin_perpage'));
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(array(
            'router' => $this->getEvent()->getRouter(),
            'route' => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
            'params' => array_filter(array(
                'module' => $this->getModule(),
                'controller' => 'story',
                'action' => 'index',
                'status' => $status,
                'topic' => $topic,
                'uid' => $uid,
                'title' => $title,
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
        $this->view()->setTemplate('story-index');
        $this->view()->assign('stores', $story);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('form', $form);
        $this->view()->assign('config', $config);
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
        $id = $this->params('id');
        // Get topic list
        $topicList = Pi::registry('topicList', 'news')->read();
        // Get author list
        $authorList = Pi::registry('authorList', 'news')->read();
        // Find story
        $story = $this->getModel('story')->find($id);
        $story = Pi::api('story', 'news')->canonizeStory($story, $topicList, $authorList);
        // Check message
        if (!$story) {
            $this->jump(array('action' => 'index'), __('Please select story'));
        }
        // Set view
        $this->view()->setTemplate('story-view');
        $this->view()->assign('story', $story);
    }

    public function acceptAction()
    {
        // Get id and status
        $module = $this->params('module');
        $id = $this->params('id');
        $status = $this->params('status');
        $return = array();
        // set story
        $story = $this->getModel('story')->find($id);
        // Check
        if ($story && in_array($status, array(1, 2, 3, 4, 5, 6))) {
            // Accept
            $story->status = $status;
            // Save
            if ($story->save()) {
                // Update link table
                $this->getModel('link')->update(array('status' => $story->status), array('story' => $story->id));
                // Clear registry
                Pi::registry('spotlightStoryId', 'news')->clear();
                // Add / Edit sitemap
                if (Pi::service('module')->isActive('sitemap')) {
                    // Set loc
                    $loc = Pi::url($this->url('news', array(
                        'module' => $module,
                        'controller' => 'story',
                        'slug' => $story->slug
                    )));
                    // Update sitemap
                    Pi::api('sitemap', 'sitemap')->singleLink($loc, $story->status, $module, 'story', $story->id);
                }
                // Set return
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

    public function addAction() {
        $id = uniqid('story-');
        $row = $this->getModel('story')->createRow();
        $row->title = $id;
        $row->slug = $id;
        $row->status = 6;
        $row->time_create = time();
        $row->type = $this->params('type');
        $row->uid = Pi::user()->getId();
        $row->save();
        // Make jump information
        $message = '';
        $url = array('controller' => 'story', 'action' => 'update', 'id' => $row->id);
        $this->jump($url, $message);
    }

    public function updateAction()
    {
        // Get id
        $id = $this->params('id');
        $module = $this->params('module');
        // Get Module Config
        $config = Pi::service('registry')->config->read($module);
        // Set option
        $option = array();
        $option['admin_time_publish'] = $config['admin_time_publish'];
        $option['admin_text_extra'] = $config['admin_text_extra'];
        // Find story
        if ($id) {
            $story = $this->getModel('story')->find($id)->toArray();
        } else {
            $this->jump(array('action' => 'index'), __('Please select story'));
        }
        // Set topic
        $story['topic'] = json_decode($story['topic'], true);
        // Set image
        if ($story['image']) {
            $thumbUrl = sprintf('upload/%s/thumb/%s/%s', $this->config('image_path'), $story['path'], $story['image']);
            $option['thumbUrl'] = Pi::url($thumbUrl);
            $option['removeUrl'] = $this->url('', array('action' => 'remove', 'id' => $story['id']));
        }
        // Get attribute field
        $fields = Pi::api('attribute', 'news')->Get($story['topic_main']);
        $option['field'] = $fields['attribute'];
        // Set author
        if ($this->config('admin_setauthor')) {
            $option['author'] = Pi::api('author', 'news')->getFormAuthor();
            $option['role'] = Pi::api('author', 'news')->getFormRole();
        } else {
            $option['author'] = '';
            $option['role'] = '';
        }
        // Set type
        $option['type'] = $story['type'];
        $option['admin_deactivate_view'] = $config['admin_deactivate_view'];
        // Set form
        $form = new StoryForm('story', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $file = $this->request->getFiles();
            // Set slug
            $slug = ($data['slug']) ? $data['slug'] : $data['title'];
            $filter = new Filter\Slug;
            $data['slug'] = $filter($slug);
            // Form filter
            $form->setInputFilter(new StoryFilter($option));
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                // Set author
                $author = array();
                if (!empty($option['role'])) {
                    foreach ($option['role'] as $role) {
                        if ($values[$role['name']] > 0) {
                            $author[$role['id']]['role'] = $role['id'];
                            $author[$role['id']]['author'] = $values[$role['name']];
                        }
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
                    // Image name
                    $imageName = Pi::api('image', 'news')->rename($file['image']['name'], $this->ImageStoryPrefix, $values['path']);
                    // Upload
                    $uploader = new Upload;
                    $uploader->setDestination($originalPath);
                    $uploader->setRename($imageName);
                    $uploader->setExtension($this->config('image_extension'));
                    $uploader->setSize($this->config('image_size'));
                    if ($uploader->isValid()) {
                        $uploader->receive();
                        // Get image name
                        $values['image'] = $uploader->getUploaded('image');
                        // process image
                        Pi::api('image', 'news')->process($values['image'], $values['path']);
                    } else {
                        $messages = $uploader->getMessages();
                        $this->jump(array('action' => 'update', 'id' => $id), $messages ? implode('; ', $messages) : __('Problem in upload image. please try again'));
                    }
                } elseif (!isset($values['image'])) {
                    $values['image'] = '';
                }

                if (isset($values['image']) && $values['image'] == '') {
                    unset($values['image']);
                }

                // Topics
                $values['topic'] = json_encode(array_unique($values['topic']));
                // Author
                $values['author'] = (empty($author)) ? '' : json_encode($author);
                // Set seo_title
                $title = ($values['seo_title']) ? $values['seo_title'] : $values['title'];
                $filter = new Filter\HeadTitle;
                $values['seo_title'] = $filter($title);
                // Set seo_keywords
                $keywords = ($values['seo_keywords']) ? $values['seo_keywords'] : $values['title'];
                $filter = new Filter\HeadKeywords;
                $filter->setOptions(array(
                    'force_replace_space' => (bool)$this->config('force_replace_space'),
                ));
                $values['seo_keywords'] = $filter($keywords);
                // Set seo_description
                $description = ($values['seo_description']) ? $values['seo_description'] : $values['title'];
                $filter = new Filter\HeadDescription;
                $values['seo_description'] = $filter($description);
                // Set time
                if ($story['status'] == 6) {
                    $values['uid'] = Pi::user()->getId();
                    $values['time_create'] = time();
                    if ($values['time_publish'] && $config['admin_time_publish']) {
                        $values['time_publish'] = strtotime($values['time_publish']);
                    } else {
                        $values['time_publish'] = time();
                    }
                } else {
                    $values['time_update'] = time();
                    if ($values['time_publish'] && $config['admin_time_publish']) {
                        $values['time_publish'] = strtotime($values['time_publish']);
                    }
                }
                // Save values
                $row = $this->getModel('story')->find($values['id']);
                $row->assign($values);
                $row->save();
                // Topic
                Pi::api('topic', 'news')->setLink($row->id, $row->topic, $row->time_publish, $row->time_update, $row->status, $row->uid, $row->type);
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
                // Add / Edit sitemap
                if (Pi::service('module')->isActive('sitemap')) {
                    // Set loc
                    $loc = Pi::url($this->url('news', array(
                        'module' => $module,
                        'controller' => 'story',
                        'slug' => $values['slug']
                    )));
                    // Update sitemap
                    Pi::api('sitemap', 'sitemap')->singleLink($loc, $row->status, $module, 'story', $row->id);
                }
                // Add as spotlight
                if ($values['spotlight']
                    && !Pi::api('spotlight', 'news')->isSpotlight($row->id)
                    && $row->status == 1
                ) {
                    // Set values
                    $spotlightValues = array();
                    $spotlightValues['time_publish'] = time();
                    $spotlightValues['time_expire'] = time() + (60 * 60 * 24 * 14);
                    $spotlightValues['uid'] = Pi::user()->getId();
                    $spotlightValues['story'] = $row->id;
                    $spotlightValues['topic'] = 0;
                    $spotlightValues['status'] = $row->status;
                    // Save values
                    $spotlight = $this->getModel('spotlight')->createRow();
                    $spotlight->assign($spotlightValues);
                    $spotlight->save();
                }
                // Clear registry
                Pi::registry('spotlightStoryId', 'news')->clear();
                // Make jump information
                $message = __('Story data saved successfully.');
                $url = array('controller' => 'story', 'action' => 'additional', 'id' => $row->id);
                $this->jump($url, $message);
            }
        } else {
            // Get author
            $story = Pi::api('author', 'news')->setFormValues($story);
            // Get tag list
            if (Pi::service('module')->isActive('tag')) {
                $tag = Pi::service('tag')->get($module, $story['id'], '');
                if (is_array($tag)) {
                    $story['tag'] = implode('|', $tag);
                }
            }
            // Check is draft
            if ($story['status'] == 6) {
                unset($story['title']);
                unset($story['slug']);
                unset($story['status']);
                $story['time_publish'] = date("Y-m-d H:i:s", time());
            } else {
                $story['time_publish'] = date("Y-m-d H:i:s", $story['time_publish']);
            }
            // Set from data
            $form->setData($story);
            $type = $story['type'];
        }
        // Set type message
        switch ($type) {
            case 'download':
                $message = __('Your story type is <strong>Download</strong> , first please completion this form , after that if you see extra file fields, you can add your file urls , and after click on submit button , you can upload your download files on next page');
                break;

            case 'media':
                $message = __('Your story type is <strong>Media</strong> , first please completion this form , after that if you see extra video or audio fields, you can add your video or audio urls for play on website media player, and after click on submit button , you can upload your media on next page');
                break;

            case 'image':
                $message = __('Your story type is <strong>Image</strong> , please completion this form and attach your image');
                break;

            case 'gallery':
                $message = __('Your story type is <strong>Gallery</strong> , first please completion this form , after click on submit button , you can upload your images on next page');
                break;

            case 'post':
                $message = __('Your story type is <strong>Blog post</strong> , write your post here and it will show on blog module');
                break;

            case 'article':
                $message = __('Your story type is <strong>Article</strong> and you should add text information on form fields, image set on side bar');
                break;

            case 'magazine':
                $message = __('Your story type is <strong>Magazine</strong> and you should add text information on form fields, image set on side bar');
                break;

            case 'text':
            default:
                $message = __('Your story type is <strong>Text</strong> and you should add text information on form fields, image set on top and center');
                break;
        }
        // Get all attach files
        $select = $this->getModel('attach')->select()->where(array('item_id' => $story['id'], 'item_table' => 'story'));
        $attachs = $this->getModel('attach')->selectWith($select);
        // Make list
        $contents = array();
        foreach ($attachs as $attach) {
            $content[$attach->id] = $attach->toArray();
            $content[$attach->id]['time_create'] = _date($content[$attach->id]['time_create']);
            $content[$attach->id]['downloadUrl'] = Pi::url($this->url('news', array(
                'module' => $this->getModule(),
                'controller' => 'media',
                'action' => 'download',
                'id' => $attach->id,
            )));
            $content[$attach->id]['editUrl'] = $this->url('', array(
                'controller' => 'attach',
                'action' => 'edit',
                'id' => $attach->id,
            ));
            $content[$attach->id]['preview'] = Pi::api('attach', 'news')->filePreview(
                $content[$attach->id]['type'],
                $content[$attach->id]['path'],
                $content[$attach->id]['file']
            );
            $contents[] = $content[$attach->id];
        }
        // Set view
        $this->view()->setTemplate('story-update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add a Story'));
        $this->view()->assign('message', $message);
        $this->view()->assign('story', $story);
        $this->view()->assign('content', json_encode($contents));
    }

    public function additionalAction()
    {
        // Get id
        $id = $this->params('id');
        $option = array();
        // Find story
        if ($id) {
            $story = $this->getModel('story')->find($id)->toArray();
        } else {
            $this->jump(array('action' => 'index'), __('Please select story'));
        }
        // Get attribute field
        $fields = Pi::api('attribute', 'news')->Get($story['topic_main']);
        $option['field'] = $fields['attribute'];
        // Check attribute is empty
        if (empty($fields['attribute'])) {
            // Make jump information
            /* switch ($story['type']) {
                case 'download':
                    $message = __('Download data saved successfully. Please attach your files');
                    $url = array('controller' => 'attach', 'action' => 'add', 'id' => $story['id']);
                    break;

                case 'media':
                    $message = __('Media data saved successfully. Please attach your medias');
                    $url = array('controller' => 'attach', 'action' => 'add', 'id' => $story['id']);
                    break;

                case 'gallery':
                    $message = __('Gallery data saved successfully. Please attach your images');
                    $url = array('controller' => 'attach', 'action' => 'add', 'id' => $story['id']);
                    break;

                case 'text':
                default:
                    $message = __('Text data saved successfully.');
                    $url = array('controller' => 'story', 'action' => 'index');
                    break;
            } */
            // Do jump
            $message = __('Text data saved successfully.');
            $url = array('controller' => 'story', 'action' => 'index');
            $this->jump($url, $message);
        }
        // Check post
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            // Set form
            $form = new StoryAdditionalForm('story', $option);
            $form->setAttribute('enctype', 'multipart/form-data');
            $form->setInputFilter(new StoryAdditionalFilter($option));
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                // Set attribute data array
                if (!empty($fields['field'])) {
                    foreach ($fields['field'] as $field) {
                        $attribute[$field]['field'] = $field;
                        $attribute[$field]['data'] = $values[$field];
                    }
                }
                // Set time
                $values['time_update'] = time();
                // Save
                $row = $this->getModel('story')->find($values['id']);
                $row->assign($values);
                $row->save();
                // Set attribute
                if (isset($attribute) && !empty($attribute)) {
                    Pi::api('attribute', 'news')->Set($attribute, $row->id);
                }
                // Make jump information
                /* switch ($row->type) {
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
                } */
                // Do jump
                $message = __('Text data saved successfully.');
                $url = array('controller' => 'story', 'action' => 'index');
                $this->jump($url, $message);
            }
        } else {
            // Get attribute
            $story = Pi::api('attribute', 'news')->Form($story);
            // Set form
            $form = new StoryAdditionalForm('story', $option);
            $form->setAttribute('enctype', 'multipart/form-data');
            $form->setData($story);
        }
        // Set type message
        switch ($story['type']) {
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
        // Set view
        $this->view()->setTemplate('story-update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Manage additional information'));
        $this->view()->assign('message', $message);
        $this->view()->assign('story', $story);
        $this->view()->assign('content', '');
    }

    public function deleteAction()
    {
        // Get information
        $this->view()->setTemplate(false);
        $module = $this->params('module');
        $id = $this->params('id');
        $row = $this->getModel('story')->find($id);
        if ($row) {
            $row->status = 5;
            $row->save();
            // update links
            $this->getModel('link')->update(array('status' => $row->status), array('story' => $row->id));
            // Spotlight
            $this->getModel('spotlight')->delete(array('story' => $row->id));
            // Remove sitemap
            if (Pi::service('module')->isActive('sitemap')) {
                $loc = Pi::url($this->url('news', array(
                        'module'      => $module,
                        'controller'  => 'story',
                        'slug'        => $row->slug
                    )));
                Pi::api('sitemap', 'sitemap')->remove($loc);
            }
            // Remove page
            $this->jump(array('action' => 'index'), __('This story deleted'));
        }
        $this->jump(array('action' => 'index'), __('Please select story'));
    }
}
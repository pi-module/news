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
use Module\News\Form\AuthorForm;
use Module\News\Form\AuthorFilter;
use Module\News\Form\AuthorRoleForm;
use Module\News\Form\AuthorRoleFilter;
use Module\News\Form\StorySearchForm;
use Module\News\Form\StorySearchFilter;
use Zend\Json\Json;

class AuthorController extends ActionController
{
    protected $ImageAuthorPrefix = 'author_';

    protected $authorColumns = array(
    	'id', 'title', 'slug', 'description', 'seo_title', 'seo_keywords', 'seo_description', 
    	'time_create', 'time_update', 'uid', 'hits', 'image', 'path', 'status'
    );

    protected $authorRoleColumns = array(
    	'id', 'title', 'status'
    );

    public function indexAction()
    {
        // Get page
        $page = $this->params('page', 1);
        $module = $this->params('module');
        $title = $this->params('title');
        // Set info
        $offset = (int)($page - 1) * $this->config('admin_perpage');
        $order = array('title DESC', 'id DESC');
        $limit = intval($this->config('admin_perpage'));
        // Get
        $where = array();
        if (!empty($title)) {
            $where['title LIKE ?'] = '%' . $title . '%';
        }
        // Get list of author
        $select = $this->getModel('author')->select()->where($where)->order($order)->offset($offset)->limit($limit);
        $rowset = $this->getModel('author')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $author[$row->id] = $row->toArray();
            $author[$row->id]['time_create'] = _date($author[$row->id]['time_create']);
        }
        // Set paginator
        $columns = array('count' => new \Zend\Db\Sql\Predicate\Expression('count(*)'));
        $select = $this->getModel('author')->select()->where($where)->columns($columns);
        $count = $this->getModel('author')->selectWith($select)->current()->count;
        $paginator = Paginator::factory(intval($count));
        $paginator->setItemCountPerPage($this->config('admin_perpage'));
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(array(
            'router'    => $this->getEvent()->getRouter(),
            'route'     => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
            'params'    => array_filter(array(
                'module'        => $this->getModule(),
                'controller'    => 'author',
                'action'        => 'index',
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
        $this->view()->setTemplate('author_index');
        $this->view()->assign('authors', $author);
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

    public function updateAction()
    {
        // Get id
        $id = $this->params('id');
        $module = $this->params('module');
        $option = array();
        // Find author
        if ($id) {
            $author = $this->getModel('author')->find($id)->toArray();
            if ($author['image']) {
                $author['thumbUrl'] = sprintf('upload/%s/thumb/%s/%s', $this->config('image_path'), $author['path'], $author['image']);
                $option['thumbUrl'] = Pi::url($author['thumbUrl']);
                $option['removeUrl'] = $this->url('', array('action' => 'remove', 'id' => $author['id']));
            }
        }
        // Set form
        $form = new AuthorForm('author', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $file = $this->request->getFiles();
            // Set slug
            $slug = ($data['slug']) ? $data['slug'] : $data['title'];
            $data['slug'] = Pi::api('text', 'news')->slug($slug);
            // Form filter
            $form->setInputFilter(new AuthorFilter);
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                // upload image
                if (!empty($file['image']['name'])) {
                    // Set upload path
                    $values['path'] = sprintf('%s/%s', date('Y'), date('m'));
                    $originalPath = Pi::path(sprintf('upload/%s/original/%s', $this->config('image_path'), $values['path']));
                    // Upload
                    $uploader = new Upload;
                    $uploader->setDestination($originalPath);
                    $uploader->setRename($this->ImageAuthorPrefix . '%random%');
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
                // Set just category fields
                foreach (array_keys($values) as $key) {
                    if (!in_array($key, $this->authorColumns)) {
                        unset($values[$key]);
                    }
                }
                // Set seo_title
                $title = ($values['seo_title']) ? $values['seo_title'] : $values['title'];
                $values['seo_title'] = Pi::api('text', 'news')->title($title);
                // Set seo_keywords
                $keywords = ($values['seo_keywords']) ? $values['seo_keywords'] : $values['title'];
                $values['seo_keywords'] = Pi::api('text', 'news')->keywords($keywords);
                // Set seo_description
                $description = ($values['seo_description']) ? $values['seo_description'] : $values['title'];
                $values['seo_description'] = Pi::api('text', 'news')->description($description);
                // Set if new
                if (empty($values['id'])) {
                    // Set time
                    $values['time_create'] = time();
                    // Set user
                    $values['uid'] = Pi::user()->getId();
                }
                // Set time_update
                $values['time_update'] = time();
                // Save values
                if (!empty($values['id'])) {
                    $row = $this->getModel('author')->find($values['id']);
                } else {
                    $row = $this->getModel('author')->createRow();
                }
                $row->assign($values);
                $row->save();
                // Add / Edit sitemap
                if (Pi::service('module')->isActive('sitemap')) {
                    $loc = Pi::url($this->url('news', array(
                        'module'      => $module, 
                        'controller'  => 'author', 
                        'slug'        => $values['slug']
                    )));
                    if (empty($values['id'])) {
                        Pi::api('sitemap', 'sitemap')->add('news', 'author', $row->id, $loc);
                    } else {
                        Pi::api('sitemap', 'sitemap')->update('news', 'author', $row->id, $loc);
                    }              
                }
                $message = __('Author data saved successfully.');
                $this->jump(array('action' => 'index'), $message);
            } else {
                $message = __('Invalid data, please check and re-submit.');
            }
        } else {
            if ($id) {
                $form->setData($author);
            }
        }
        // Set view
        $this->view()->setTemplate('author_update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add Author')); 
    }

    public function roleAction()
    {
        // Get page
        $module = $this->params('module');
        // Set info
        $order = array('title DESC', 'id DESC');
        // Get list of author
        $select = $this->getModel('author_role')->select()->order($order);
        $rowset = $this->getModel('author_role')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $role[$row->id] = $row->toArray();
        }
        // Set form
        $form = new StorySearchForm('search');
        $form->setAttribute('action', $this->url('', array('action' => 'process')));
        // Set view
        $this->view()->setTemplate('author_role');
        $this->view()->assign('roles', $role);
        $this->view()->assign('form', $form);
    }

    public function roleUpdateAction()
    {
        // Get id
        $id = $this->params('id');
        $module = $this->params('module');
        // Set form
        $form = new AuthorRoleForm('role');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setInputFilter(new AuthorRoleFilter);
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                // Set just category fields
                foreach (array_keys($values) as $key) {
                    if (!in_array($key, $this->authorRoleColumns)) {
                        unset($values[$key]);
                    }
                }
                // Save values
                if (!empty($values['id'])) {
                    $row = $this->getModel('author_role')->find($values['id']);
                } else {
                    $row = $this->getModel('author_role')->createRow();
                }
                $row->assign($values);
                $row->save();
                $message = __('Role data saved successfully.');
                $this->jump(array('action' => 'role'), $message);
            } else {
                $message = __('Invalid data, please check and re-submit.');
            }
        } else {
            if ($id) {
            	$role = $this->getModel('author_role')->find($id)->toArray();
                $form->setData($role);
            }
        }
        // Set view
        $this->view()->setTemplate('author_role_update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add Role')); 
    }

    public function removeAction()
    {
        // Get id and status
        $id = $this->params('id');
        // set author
        $author = $this->getModel('author')->find($id);
        // Check
        if ($author && !empty($id)) {
            // remove file
            /* $files = array(
                Pi::path('upload/' . $this->config('image_path') . '/original/' . $author->path . '/' . $author->image),
                Pi::path('upload/' . $this->config('image_path') . '/large/' . $author->path . '/' . $author->image),
                Pi::path('upload/' . $this->config('image_path') . '/medium/' . $author->path . '/' . $author->image),
                Pi::path('upload/' . $this->config('image_path') . '/thumb/' . $author->path . '/' . $author->image),
            );
            Pi::service('file')->remove($files); */
            // clear DB
            $author->image = '';
            $author->path = '';
            // Save
            if ($author->save()) {
                $message = sprintf(__('Image of %s removed'), $author->title);
                $status = 1;
            } else {
                $message = __('Image not remove');
                $status = 0;
            }
        } else {
            $message = __('Please select author');
            $status = 0;
        }
        return array(
            'status' => $status,
            'message' => $message,
        );
    }
}    
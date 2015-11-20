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
use Module\News\Form\AuthorForm;
use Module\News\Form\AuthorFilter;
use Module\News\Form\StorySearchForm;
use Module\News\Form\StorySearchFilter;
use Zend\Json\Json;

class AuthorController extends ActionController
{
    protected $ImageAuthorPrefix = 'author_';

    protected $authorColumns = array(
        'id', 'title', 'slug', 'text_description', 'seo_title', 'seo_keywords', 'seo_description',
        'time_create', 'time_update', 'uid', 'hits', 'image', 'path', 'status'
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
            'router' => $this->getEvent()->getRouter(),
            'route' => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
            'params' => array_filter(array(
                'module' => $this->getModule(),
                'controller' => 'author',
                'action' => 'index',
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
        $this->view()->setTemplate('author-index');
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
            $filter = new Filter\Slug;
            $data['slug'] = $filter($slug);
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
                    // Image name
                    $imageName = Pi::api('image', 'news')->rename($file['image']['name'], $this->ImageAuthorPrefix, $values['path']);
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
                    // Set loc
                    $loc = Pi::url($this->url('news', array(
                        'module' => $module,
                        'controller' => 'author',
                        'slug' => $values['slug']
                    )));
                    // Update sitemap
                    Pi::api('sitemap', 'sitemap')->singleLink($loc, $row->status, $module, 'author', $row->id);
                }
                // Clear registry
                Pi::registry('authorList', 'news')->clear();
                // jump
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
        $this->view()->setTemplate('author-update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add Author'));
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
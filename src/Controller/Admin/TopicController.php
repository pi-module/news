<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
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
use Module\News\Form\TopicForm;
use Module\News\Form\TopicFilter;
use Zend\Db\Sql\Predicate\Expression;

class TopicController extends ActionController
{
    protected $ImageTopicPrefix = 'topic-';

    public function indexAction()
    {
        // Get page
        $page   = $this->params('page', 1);
        $module = $this->params('module');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Get info
        $where   = ['status' => [1,2,3,4]];
        $columns = ['id', 'title', 'slug', 'style', 'status', 'type'];
        $order   = ['id DESC', 'time_create DESC'];
        $limit   = intval($this->config('admin_perpage'));
        $offset  = (int)($page - 1) * $this->config('admin_perpage');

        // Select
        $select  = $this->getModel('topic')->select()->columns($columns)->where($where)->order($order)->offset($offset)->limit($limit);
        $rowset  = $this->getModel('topic')->selectWith($select);

        // Make list
        foreach ($rowset as $row) {
            $list[$row->id] = $row->toArray();

            // Set topic style view
            switch ($row->style) {
                case 'list':
                    $list[$row->id]['style_view'] = __('List');
                    break;

                case 'table':
                    $list[$row->id]['style_view'] = __('Table');
                    break;

                case 'media':
                    $list[$row->id]['style_view'] = __('Media');
                    break;

                case 'spotlight':
                    $list[$row->id]['style_view'] = __('Spotlight');
                    break;

                case 'topic':
                    $list[$row->id]['style_view'] = __('Topic');
                    break;

                case 'news':
                default:
                    $list[$row->id]['style_view'] = __('News');
                    break;
            }

            // Set topic style view
            switch ($row->type) {
                case 'blog':
                    $list[$row->id]['type_view'] = __('Blog module');
                    break;

                case 'event':
                    $list[$row->id]['type_view'] = __('Event module');
                    break;

                case 'general':
                default:
                    $list[$row->id]['type_view'] = __('General');
                    break;
            }
        }

        // Go to time_update page if empty
        if (empty($list)) {
            return $this->redirect()->toRoute('', ['action' => 'update']);
        }

        // Set count
        $count     = ['count' => new Expression('count(*)')];
        $select    = $this->getModel('topic')->select()->columns($count)->where($where);
        $count     = $this->getModel('topic')->selectWith($select)->current()->count;

        // Set paginator
        $paginator = Paginator::factory(intval($count));
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(
            [
                'router' => $this->getEvent()->getRouter(),
                'route'  => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
                'params' => array_filter(
                    [
                        'module'     => $this->getModule(),
                        'controller' => 'topic',
                        'action'     => 'index',
                    ]
                ),
            ]
        );

        // Set view
        $this->view()->setTemplate('topic-index');
        $this->view()->assign('topics', $list);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('config', $config);
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
            /* $files = array(
                Pi::path('upload/' . $this->config('image_path') . '/original/' . $topic->path . '/' . $topic->image),
                Pi::path('upload/' . $this->config('image_path') . '/large/' . $topic->path . '/' . $topic->image),
                Pi::path('upload/' . $this->config('image_path') . '/medium/' . $topic->path . '/' . $topic->image),
                Pi::path('upload/' . $this->config('image_path') . '/thumb/' . $topic->path . '/' . $topic->image),
            );
            Pi::service('file')->remove($files); */
            // clear DB
            $topic->image = '';
            $topic->path  = '';
            // Save
            if ($topic->save()) {
                $message = sprintf(__('Image of %s removed'), $topic->title);
                $status  = 1;
            } else {
                $message = __('Image not remove');
                $status  = 0;
            }
        } else {
            $message = __('Please select story');
            $status  = 0;
        }
        return [
            'status'  => $status,
            'message' => $message,
        ];
    }

    public function updateAction()
    {
        // Get id
        $id     = $this->params('id');
        $module = $this->params('module');
        $option = [];
        // Find topic
        if ($id) {
            $topic = $this->getModel('topic')->find($id)->toArray();
            if ($topic['image']) {
                $topic['thumbUrl']   = sprintf('upload/%s/thumb/%s/%s', $this->config('image_path'), $topic['path'], $topic['image']);
                $option['thumbUrl']  = Pi::url($topic['thumbUrl']);
                $option['removeUrl'] = $this->url('', ['action' => 'remove', 'id' => $topic['id']]);
            }
        }
        // Set form
        $form = new TopicForm('topic', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $file = $this->request->getFiles();
            // Set slug
            $slug         = ($data['slug']) ? $data['slug'] : $data['title'];
            $filter       = new Filter\Slug;
            $data['slug'] = $filter($slug);
            // Form filter
            $form->setInputFilter(new TopicFilter);
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                // upload image
                if (!empty($file['image']['name'])) {
                    // Set upload path
                    $values['path'] = sprintf('%s/%s', date('Y'), date('m'));
                    $originalPath   = Pi::path(sprintf('upload/%s/original/%s', $this->config('image_path'), $values['path']));
                    // Image name
                    $imageName = Pi::api('image', 'news')->rename($file['image']['name'], $this->ImageTopicPrefix, $values['path']);
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
                        $this->jump(['action' => 'update'], __('Problem in upload image. please try again'));
                    }
                } elseif (!isset($values['image'])) {
                    $values['image'] = '';
                }
                // Set setting
                $setting                    = [];
                $setting['show_config']     = $values['show_config'];
                $setting['show_perpage']    = ($values['show_perpage']) ? $values['show_perpage'] : $this->config('show_perpage');
                $setting['show_columns']    = $values['show_columns'];
                $setting['show_order_link'] = $values['show_order_link'];
                $setting['show_topic']      = $values['show_topic'];
                $setting['show_topicinfo']  = $values['show_topicinfo'];
                $setting['show_date']       = $values['show_date'];
                $setting['show_pdf']        = $values['show_pdf'];
                $setting['show_print']      = $values['show_print'];
                $setting['show_mail']       = $values['show_mail'];
                $setting['show_hits']       = $values['show_hits'];
                $setting['show_tag']        = $values['show_tag'];
                $setting['show_subid']      = $values['show_subid'];
                $setting['view_position']   = $values['view_position'];
                $setting['set_page']        = $values['set_page'];
                $setting['attach']          = $values['attach'];
                $values['setting']          = json_encode($setting);
                // Set seo_title
                $title               = ($values['seo_title']) ? $values['seo_title'] : $values['title'];
                $filter              = new Filter\HeadTitle;
                $values['seo_title'] = $filter($title);
                // Set seo_keywords
                $keywords = ($values['seo_keywords']) ? $values['seo_keywords'] : '';
                $filter   = new Filter\HeadKeywords;
                $filter->setOptions(
                    [
                        'force_replace_space' => (bool)$this->config('force_replace_space'),
                    ]
                );
                $values['seo_keywords'] = $filter($keywords);
                // Set seo_description
                $description               = ($values['seo_description']) ? $values['seo_description'] : $values['title'];
                $filter                    = new Filter\HeadDescription;
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
                    $row = $this->getModel('topic')->find($values['id']);
                } else {
                    $row = $this->getModel('topic')->createRow();
                }
                $row->assign($values);
                $row->save();
                // Add attach
                if (!empty($values['attach_link'])) {
                    $attach['url']         = $values['attach_link'];
                    $attach['title']       = empty($values['attach_title']) ? __('Download') : $values['attach_title'];
                    $attach['item_id']     = $row->id;
                    $attach['item_table']  = 'topic';
                    $attach['time_create'] = time();
                    $attach['type']        = 'link';
                    $attach['status']      = 1;
                    // save in DB
                    $rowAttach = $this->getModel('attach')->createRow();
                    $rowAttach->assign($attach);
                    $rowAttach->save();
                }
                // Set topic as page for dress up block
                $pageName = sprintf('topic-%s', $row->id);
                if ($this->config('admin_setpage') && $setting['set_page']) {
                    if (empty($values['id'])) {
                        $this->setPage($pageName, sprintf(__('Page : %s'), $row->title));
                    } else {
                        $this->updatePage($pageName, sprintf(__('Page : %s'), $row->title));
                    }
                } else {
                    $this->removePage($pageName);
                }
                Pi::service('registry')->page->clear($this->getModule());
                // Add / Edit sitemap
                if (Pi::service('module')->isActive('sitemap')) {
                    // Set loc
                    $loc = Pi::url(
                        $this->url(
                            'news', [
                                'module'     => $module,
                                'controller' => 'topic',
                                'slug'       => $values['slug'],
                            ]
                        )
                    );
                    // Update sitemap
                    Pi::api('sitemap', 'sitemap')->singleLink($loc, $row->status, $module, 'topic', $row->id);
                }
                // Clear registry
                Pi::registry('topicList', 'news')->clear();
                Pi::registry('topicRoute', 'news')->clear();
                // jump
                $message = __('Topic data saved successfully.');
                $this->jump(['action' => 'index'], $message);
            } else {
                $message = __('Invalid data, please check and re-submit.');
            }
        } else {
            if ($id) {
                $setting = json_decode($topic['setting'], true);
                $topic   = array_merge($topic, $setting);
                $form->setData($topic);
            }
        }
        $this->view()->setTemplate('topic-update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add a Topic'));
    }

    public function deleteAction()
    {
        // Set view
        $this->view()->setTemplate(false);

        // Get information
        $module = $this->params('module');
        $id     = $this->params('id');

        // Find row
        $row    = $this->getModel('topic')->find($id);

        // Check row
        if ($row) {
            // Delete row
            $row->status = 5;
            $row->save();

            // Remove sitemap
            if (Pi::service('module')->isActive('sitemap')) {
                $loc = Pi::url(
                    $this->url(
                        'news', [
                            'module'     => $module,
                            'controller' => 'topic',
                            'slug'       => $row->slug,
                        ]
                    )
                );
                Pi::api('sitemap', 'sitemap')->remove($loc);
            }

            // Set topic as page for dress up block
            $pageName = sprintf('topic-%s', $row->id);
            $this->removePage($pageName);

            // Clear registry
            Pi::service('registry')->page->clear($this->getModule());
            Pi::registry('topicList', 'news')->clear();
            Pi::registry('topicRoute', 'news')->clear();

            // Remove page
            $this->jump(['action' => 'index'], __('This topic deleted'));
        }

        $this->jump(['action' => 'index'], __('Please select story'));
    }

    /* public function deleteAction()
    {
        // Get information
        $this->view()->setTemplate(false);
        $id = $this->params('id');
        $module = $this->params('module');
        $row = $this->getModel('topic')->find($id);
        if ($row) {
            // update sub topics
            $this->getModel('topic')->update(array('pid' => $row->pid), array('pid' => $row->id));
            // remove topic links
            $this->getModel('link')->delete(array('topic' => $row->id));
            // Get this topic stores and remove
            $select = $this->getModel('story')->select()->columns(array('id', 'path', 'image'))->where(array('topic' => json_encode(array($row->id))));
            $rowset = $this->getModel('story')->selectWith($select)->toArray();
            foreach ($rowset as $story) {
                // Attach
                $this->getModel('attach')->delete(array('story' => $story['id']));
                // Attribute
                $this->getModel('field_data')->delete(array('story' => $story['id']));
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
            $pageName = sprintf('topic-%s', $row->id);
            $this->removePage($pageName);
            Pi::service('registry')->page->clear($this->getModule());
            // Remove sitemap
            if (Pi::service('module')->isActive('sitemap')) {
                $loc = Pi::url($this->url('news', array(
                        'module' => $module,
                        'controller' => 'topic',
                        'slug' => $row->slug
                    )));
                Pi::api('sitemap', 'sitemap')->remove($loc);
            }
            // Remove topic
            $row->delete();
            $this->jump(array('action' => 'index'), __('This topic and all of stores deleted'));
        }
        $this->jump(array('action' => 'index'), __('Please select topic'));
    } */

    /**
     * Add page settings to system
     *
     * @uid Taiwen Jiang <taiwenjiang@tsinghua.org.cn>
     *
     * @param string $name
     * @param string $title
     *
     * @return int
     */
    protected function setPage($name, $title)
    {
        $page = [
            'section'    => 'front',
            'module'     => $this->getModule(),
            'controller' => 'topic',
            'action'     => $name,
            'title'      => $title,
            'block'      => 1,
            'custom'     => 0,
        ];
        $row  = Pi::model('page')->createRow($page);
        $row->save();
        return $row->id;
    }

    /**
     * Remove from system page settings
     *
     * @uid Taiwen Jiang <taiwenjiang@tsinghua.org.cn>
     *
     * @param stinr $name
     *
     * @return int
     */
    protected function removePage($name)
    {
        $where = [
            'section'    => 'front',
            'module'     => $this->getModule(),
            'controller' => 'topic',
            'action'     => $name,
        ];
        $count = Pi::model('page')->delete($where);
        return $count;
    }

    /**
     * time_update from system page settings
     *
     * @param stinr $name
     *
     * @return int
     */
    protected function updatePage($name, $title)
    {
        // Set where
        $where = [
            'section'    => 'front',
            'module'     => $this->getModule(),
            'controller' => 'topic',
            'action'     => $name,
        ];
        // Get count
        $columns = ['count' => new Expression('count(*)')];
        $select  = Pi::model('page')->select()->where($where)->columns($columns);
        $count   = Pi::model('page')->selectWith($select)->current()->count;
        // Set page
        if ($count == 0) {
            $this->setPage($name, $title);
        } else {
            Pi::model('page')->update(['action' => $name, 'title' => $title], $where);
        }
    }
}

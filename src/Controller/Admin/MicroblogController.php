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
use Pi\Mvc\Controller\ActionController;
use Pi\Paginator\Paginator;
use Module\News\Form\MicroblogForm;
use Module\News\Form\MicroblogFilter;
use Laminas\Db\Sql\Predicate\Expression;

class MicroblogController extends ActionController
{
    public function indexAction()
    {
        // Get page
        $page   = $this->params('page', 1);
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get info
        $list   = [];
        $order  = ['id DESC'];
        $limit  = intval($this->config('admin_perpage'));
        $offset = (int)($page - 1) * $this->config('admin_perpage');
        $where  = ['status' => [1, 2, 3, 4]];
        $select = $this->getModel('microblog')->select()->where($where)->order($order)->offset($offset)->limit($limit);
        $rowset = $this->getModel('microblog')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $list[$row->id]                 = $row->toArray();
            $list[$row->id]['time_publish'] = _date($list[$row->id]['time_publish']);
            $list[$row->id]['user']         = Pi::user()->get(
                $row->uid, [
                    'id', 'identity', 'name', 'email',
                ]
            );
            // Set url
            if ($row->status == 1) {
                $list[$row->id]['microblogUrl'] = $this->url(
                    'news', [
                        'module'     => $module,
                        'controller' => 'microblog',
                        'id'         => $row->id,
                    ]
                );
            } else {
                $list[$row->id]['microblogUrl'] = '';
            }
        }
        // Set paginator
        $count     = ['count' => new Expression('count(*)')];
        $select    = $this->getModel('microblog')->select()->columns($count);
        $count     = $this->getModel('microblog')->selectWith($select)->current()->count;
        $paginator = Paginator::factory(intval($count));
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(
            [
                'router' => $this->getEvent()->getRouter(),
                'route'  => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
                'params' => array_filter(
                    [
                        'module'     => 'news',
                        'controller' => 'microblog',
                        'action'     => 'index',
                    ]
                ),
            ]
        );
        // Set view
        $this->view()->setTemplate('microblog-index');
        $this->view()->assign('list', $list);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('config', $config);
    }

    public function updateAction()
    {
        // Get id
        $id     = $this->params('id');
        $type   = $this->params('type');
        $module = $this->params('module');
        // Get Module Config
        $config = Pi::service('registry')->config->read($module);
        // Check status
        if (!$config['microblog_active']) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('Microblog system not active'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }
        //
        $option = [
            'type' => $type,
        ];
        // Set form
        $form = new MicroblogForm('microblog', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setInputFilter(new MicroblogFilter);
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                if (empty($values['id'])) {
                    $values['time_create'] = time();
                    $values['uid']         = Pi::user()->getId();
                }
                // Save values
                if (!empty($values['id'])) {
                    $row = $this->getModel('microblog')->find($values['id']);
                } else {
                    $row = $this->getModel('microblog')->createRow();
                }
                $row->assign($values);
                $row->save();
                // Add / Edit sitemap
                if (Pi::service('module')->isActive('sitemap')) {
                    // Set loc
                    $loc = Pi::url(
                        $this->url(
                            'news', [
                                'module'     => $module,
                                'controller' => 'microblog',
                                'id'         => $row->id,
                            ]
                        )
                    );
                    // Update sitemap
                    Pi::api('sitemap', 'sitemap')->singleLink($loc, $row->status, $module, 'microblog', $row->id);
                }
                // Jump
                $message = __('Post saved successfully.');
                $this->jump(['action' => 'index'], $message);
            }
        } else {
            if ($id) {
                $microblog = $this->getModel('microblog')->find($id)->toArray();
                $form->setData($microblog);
            }
        }
        //
        if ($type == 'post') {
            $title = __('New post');
        } elseif ($type == 'news') {
            $title = __('New news');
        } else {
            $title = __('Update');
        }
        // Set view
        $this->view()->setTemplate('microblog-update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', $title);
        $this->view()->assign('config', $config);
    }

    public function deleteAction()
    {
        // Get information
        $this->view()->setTemplate(false);
        // Get id
        $id     = $this->params('id');
        $module = $this->params('module');
        // Get Module Config
        $config = Pi::service('registry')->config->read($module);
        // Check status
        if (!$config['microblog_active']) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('Microblog system not active'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Get row
        $row = $this->getModel('microblog')->find($id);
        if ($row) {
            $row->status = 5;
            $row->save();
            // Remove sitemap
            if (Pi::service('module')->isActive('sitemap')) {
                $loc = Pi::url(
                    $this->url(
                        'news', [
                            'module'     => $module,
                            'controller' => 'microblog',
                            'id'         => $row->id,
                        ]
                    )
                );
                Pi::api('sitemap', 'sitemap')->remove($loc);
            }
            // Remove page
            $this->jump(['action' => 'index'], __('This post deleted'));
        }
        $this->jump(['action' => 'index'], __('Please select post'));
    }
}

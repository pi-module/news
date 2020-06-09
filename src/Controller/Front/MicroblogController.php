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

namespace Module\News\Controller\Front;

use Pi;
use Pi\Filter;
use Pi\Mvc\Controller\ActionController;
use Pi\Paginator\Paginator;
use Laminas\Db\Sql\Predicate\Expression;

class MicroblogController extends ActionController
{
    public function indexAction()
    {
        // Get info from url
        $id     = $this->params('id');
        $module = $this->params('module');

        // Get Module Config
        $config = Pi::service('registry')->config->read($module);

        // Check deactivate view
        if ($config['admin_deactivate_view']) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('Page not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Check status
        if (!$config['microblog_active']) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('Microblog system not active'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Find microblog
        $microblog = $this->getModel('microblog')->find($id);
        $microblog = Pi::api('microblog', 'news')->canonizeMicroblog($microblog);
        // Check status
        if (!$microblog || $microblog['status'] != 1) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('The post not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Update Hits
        $this->getModel('microblog')->increment('hits', ['id' => $microblog['id']]);

        // Set view
        $this->view()->headTitle($microblog['seo_title']);
        $this->view()->headdescription($microblog['seo_description'], 'set');
        $this->view()->headkeywords($microblog['seo_keywords'], 'set');
        $this->view()->setTemplate('microblog-single');
        $this->view()->assign('microblog', $microblog);
        $this->view()->assign('config', $config);
    }

    public function listAction()
    {
        // Get info from url
        $module = $this->params('module');
        $uid    = $this->params('uid');
        $topic  = $this->params('topic');
        $page   = $this->params('page', 1);

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Check deactivate view
        if ($config['admin_deactivate_view']) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('Page not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Check status
        if (!$config['microblog_active']) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('Microblog system not active'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Set info
        $list  = [];
        $where = ['status' => 1];

        // Check uid and topic
        if (isset($uid) && intval($uid) > 0) {
            $where['uid']   = intval($uid);
            $where['topic'] = 0;
        } elseif (isset($topic) && intval($topic) > 0) {
            $where['topic'] = 1;
        }

        // Set info
        $order  = ['time_create DESC', 'id DESC'];
        $offset = (int)($page - 1) * $this->config('microblog_perpage');
        $limit  = intval($this->config('microblog_perpage'));

        // Get info
        $select = $this->getModel('microblog')->select()->where($where)->order($order)->offset($offset)->limit($limit);
        $rowset = $this->getModel('microblog')->selectWith($select);

        // Make list
        foreach ($rowset as $row) {
            $list[$row->id] = Pi::api('microblog', 'news')->canonizeMicroblog($row);
        }

        // get count
        $columns = ['count' => new Expression('count(*)')];
        $select  = $this->getModel('microblog')->select()->where($where)->columns($columns);
        $count   = $this->getModel('microblog')->selectWith($select)->current()->count;

        // paginator
        $paginator = Paginator::factory(intval($count));
        $paginator->setItemCountPerPage(intval($limit));
        $paginator->setCurrentPageNumber(intval($page));
        $paginator->setUrlOptions(
            [
                'router' => $this->getEvent()->getRouter(),
                'route'  => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
                'params' => array_filter(
                    [
                        'module'     => $this->getModule(),
                        'controller' => 'microblog',
                        'action'     => 'list',
                        'uid'        => $uid,
                        'topic'      => $topic,
                    ]
                ),
            ]
        );

        // Set header and title
        $title = __('All posts on microblog system');

        // Set seo_keywords
        $filter = new Filter\HeadKeywords;
        $filter->setOptions(
            [
                'force_replace_space' => true,
            ]
        );
        $seoKeywords = $filter($title);

        // Set view
        $this->view()->headTitle($title);
        $this->view()->headdescription($title, 'set');
        $this->view()->headkeywords($seoKeywords, 'set');
        $this->view()->setTemplate('microblog-list');
        $this->view()->assign('list', $list);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('config', $config);
    }
}
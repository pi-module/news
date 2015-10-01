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
namespace Module\News\Controller\Front;

use Pi;
use Pi\Filter;
use Pi\Mvc\Controller\ActionController;
use Pi\Paginator\Paginator;
use Zend\Db\Sql\Predicate\Expression;

class MicroblogController extends ActionController
{
    public function indexAction()
    {
        // Get info from url
        $id = $this->params('id');
        $module = $this->params('module');
        // Get Module Config
        $config = Pi::service('registry')->config->read($module);
        // Find microblog
        $microblog = $this->getModel('microblog')->find($id);
        $microblog = Pi::api('microblog', 'news')->canonizeMicroblog($microblog);
        // Check status
        if (!$microblog || $microblog['status'] != 1) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('The story not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Update Hits
        $this->getModel('microblog')->increment('hits', array('id' => $microblog['id']));
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
        $page = $this->params('page', 1);
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Set info
        $list = array();
        $where = array('status' => 1);
        $order = array('time_create DESC', 'id DESC');
        $offset = (int)($page - 1) * $this->config('microblog_perpage');
        $limit = intval($this->config('microblog_perpage'));
        // Get info
        $select = $this->getModel('microblog')->select()->where($where)->order($order)->offset($offset)->limit($limit);
        $rowset = $this->getModel('microblog')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $list[$row->id] = Pi::api('microblog', 'news')->canonizeMicroblog($row);
        }
        // get count
        $columns = array('count' => new Expression('count(*)'));
        $select = $this->getModel('microblog')->select()->where($where)->columns($columns);
        $count = $this->getModel('microblog')->selectWith($select)->current()->count;
        // paginator
        $paginator = Paginator::factory(intval($count));
        $paginator->setItemCountPerPage(intval($limit));
        $paginator->setCurrentPageNumber(intval($page));
        $paginator->setUrlOptions(array(
            'router'    => $this->getEvent()->getRouter(),
            'route'     => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
            'params'    => array_filter(array(
                'module'        => $this->getModule(),
                'controller'    => 'microblog',
                'action'        => 'list',
            )),
        ));
        // Set view
        $this->view()->setTemplate('microblog-list');
        $this->view()->assign('list', $list);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('config', $config);
    }
}
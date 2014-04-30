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
use Pi\Mvc\Controller\ActionController;
use Zend\Json\Json;

class TopicController extends IndexController
{
    public function indexAction()
    {
        // Get info from url
        $module = $this->params('module');
        $slug = $this->params('action');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get topic information from model
        $topic = $this->getModel('topic')->find($slug, 'slug');
        // Check slug set
        if(empty($topic)) {
            $this->jump(array('', 'module' => $module, 'controller' => 'topic', 'action' => 'list'), __('Go to topic list.'));
        }
        // Get topic or homepage setting
        $topic = Pi::api('topic', 'news')->canonizeTopic($topic);
        // Set story info
        $where = array('status' => 1, 'topic' => $topic['ids'], 'time_publish <= ?' => time());
        // Get story List
        $storyList = $this->storyList($where, $topic['show_perpage']);
        // Set paginator info
        $template = array(
            'controller' => 'topic',
            'action' => 'index',
            'slug' => $topic['slug'],
            );
        // Get paginator
        $paginator = $this->storyPaginator($template, $where, $topic['show_perpage']);
        // Spotlight
        $spotlight = Pi::api('spotlight', 'news')->getSpotlight();
        // Set view
        $this->view()->headTitle($topic['seo_title']);
        $this->view()->headdescription($topic['seo_description'], 'set');
        $this->view()->headkeywords($topic['seo_keywords'], 'set');
        $this->view()->setTemplate($topic['template']);
        $this->view()->assign('stores', $storyList);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('topic', $topic);
        $this->view()->assign('config', $config);
        $this->view()->assign('spotlight', $spotlight);
    }

    public function listAction()
    {
        // Get page ID or slug from url
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get topic list
        $where = array('status' => 1);
        $order = array('time_create DESC', 'id DESC');
        $select = $this->getModel('topic')->select()->where($where)->order($order);
        $rowset = $this->getModel('topic')->selectWith($select);
        foreach ($rowset as $row) {
            $topics[$row->id] = Pi::api('topic', 'news')->canonizeTopic($row);
        }
        // Set view
        $this->view()->headTitle(__('List of all topics'));
        $this->view()->headkeywords(__('List of all topics'), 'set');
        $this->view()->headkeywords(__('List,topics'), 'set');
        $this->view()->setTemplate('topic_list');
        $this->view()->assign('topics', $topics);
        $this->view()->assign('config', $config);
    }
    
    public static function getMethodFromAction($action)
    {
        if($action == 'list') {
            return 'listAction';
        }
        return 'indexAction';
    }
}
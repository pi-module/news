<?php
/**
 * News topic controller
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

namespace Module\News\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Zend\Json\Json;

class TopicController extends IndexController
{
    public function indexAction()
    {
        // Get info from url
        $page = $this->params('page', 1);
        $alias = $this->params('action');
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get topic information from model
        $topic = $this->getModel('topic')->find($alias, 'alias');
        // Check alias set
        if(empty($topic)) {
            $this->jump(array('route' => '.news', 'module' => $module, 'controller' => 'topic', 'action' => 'list'), __('Got to topic list.'));
        }	
        // topic to array
        $topic = $topic->toArray();
        // Check page
        if (!$topic || $topic['status'] != 1) {
            $this->jump(array('route' => '.news', 'module' => $module, 'controller' => 'index'), __('The Topic not found.'));
        }
        // Get topic or homepage setting
        $topic = Pi::service('api')->news(array('Topic', 'Setting'), $config, $topic);
        // Get list of topic ids
        $topicId = Pi::service('api')->news(array('Topic', 'Id'), $topic['topic_homepage'], $topic['id']);
        // Set topic image url
        $topic['mediumurl'] = Pi::url('upload/' . $this->config('image_path') . '/medium/' . $topic['path'] . '/' . $topic['image']);
        $topic['thumburl'] = Pi::url('upload/' . $this->config('image_path') . '/thumb/' . $topic['path'] . '/' . $topic['image']);
        // Set info
        $offset = (int)($page - 1) * $topic['perpage'];
        $limit = intval($topic['perpage']);
        $where = array('status' => 1, 'topic' => $topicId, 'publish <= ?' => time());
        // Story
        $story = $this->StoryList($where, $offset, $limit);
        // Set paginator
        $template = array('module' => $module, 'controller' => 'topic', 'alias' => $alias, 'page' => '%page%');
        $paginator = $this->StoryPaginator($template, $where, $page, $limit);
        // Spotlight
        $spotlight = Pi::service('api')->news(array('Spotlight', 'load'), $config, $topic['id']);
        // Set view
        $this->view()->headTitle($topic['title']);
        $this->view()->headDescription($topic['description'], 'set');
        $this->view()->headKeywords($topic['keywords'], 'set');
        $this->view()->setTemplate($topic['template']);
        $this->view()->assign('stores', $story);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('topic', $topic);
        $this->view()->assign('config', $config);
        $this->view()->assign('spotlight', $spotlight);
    }

    public function listAction()
    {
        // Get page ID or alias from url
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module, 'show');
        // Get topic list
        $columns = array('id', 'title', 'alias', 'pid', 'body', 'path', 'image');
        $where = array('status' => 1, 'inlist' => 1);
        $order = array('create DESC', 'id DESC');
        $select = $this->getModel('topic')->select()->columns($columns)->where($where)->order($order);
        $rowset = $this->getModel('topic')->selectWith($select)->toArray();
        // Set view
        $this->view()->headTitle(__('List of all topics'));
        $this->view()->headKeywords(__('List of all topics'), 'set');
        $this->view()->headKeywords(__('List,topics'), 'set');
        $this->view()->setTemplate('topic_list');
        $this->view()->assign('topics', $rowset);
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
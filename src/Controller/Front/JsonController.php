<?php
/**
 * News json controller
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

class JsonController extends IndexController
{
    public function indexAction()
    {
        // Set view
        $this->view()->setTemplate(false)->setLayout('layout-content');
        // Get info from url
        $story = array();
        $page = $this->params('page', 1);
        $alias = $this->params('topic');
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get topic information from model
        $topic = $this->getModel('topic')->find($alias, 'alias');
        // Check alias set
        if(empty($topic)) {
            echo Json::encode($story);
            exit;
        }
        // topic to array
        $topic = $topic->toArray();
        // Check page
        if (!$topic || $topic['status'] != 1) {
            echo Json::encode($story);
            exit;
        }
        // Get topic or homepage setting
        $topic = Pi::service('api')->news(array('Topic', 'Setting'), $config, $topic);
        // Get list of topic ids
        $topicId = Pi::service('api')->news(array('Topic', 'Id'), $topic['topic_homepage'], $topic['id']);
        // Set info
        $offset = (int)($page - 1) * $topic['perpage'];
        $limit = intval($topic['perpage']);
        $where = array('status' => 1, 'topic' => $topicId, 'publish <= ?' => time());
        // Story
        $story = $this->StoryList($where, $offset, $limit);
        // echo json
        echo Json::encode($story);
        exit;
    }
}
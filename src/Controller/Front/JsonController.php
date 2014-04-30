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

class JsonController extends IndexController
{
    public function indexAction()
    {
        // Set view
        $this->view()->setTemplate(false)->setLayout('layout-content');
        // Get info from url
        $story = array();
        $page = $this->params('page', 1);
        $slug = $this->params('topic');
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get topic information from model
        $topic = $this->getModel('topic')->find($slug, 'slug');
        // Check slug set
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
        $where = array('status' => 1, 'topic' => $topicId, /*'time_publish <= ?' => time(),*/);
        // Story
        $story = $this->StoryList($where, $offset, $limit);
        // echo json
        echo Json::encode($story);
        exit;
    }
}
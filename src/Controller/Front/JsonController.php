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
        $module = $this->params('module');
        $topic = $this->params('topic');
        $start = $this->params('start');
        $limit = $this->params('limit');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Check json
        if (!$config['admin_json']) {
            echo Json::encode($story);
            exit;
        }
        // Get topic information from model
        $topic = $this->getModel('topic')->find($topic);
        // Check slug set
        if(empty($topic)) {
            echo Json::encode($story);
            exit;
        }
        // Get topic or homepage setting
        $topic = Pi::api('topic', 'news')->canonizeTopic($topic);
        // Check topic
        if ($topic['status'] != 1) {
            echo Json::encode($story);
            exit;
        }
        // Check topic style
        if ($topic['style'] == 'topic') {
            // Get topic list
            $where = array('status' => 1, 'pid' => $topic['id']);
            $order = array('id DESC');
            $select = $this->getModel('topic')->select()->where($where)->order($order);
            $rowset = $this->getModel('topic')->selectWith($select);
            foreach ($rowset as $row) {
                $topics[$row->id] = Pi::api('topic', 'news')->canonizeTopic($row);
            }
            echo Json::encode($topics);
            exit;
        } else {
            // Get story List
            $storyList = $this->jsonList($topic, $start, $limit);
            echo Json::encode($storyList);
            exit;
        }
    }
}
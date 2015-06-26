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
    /* public function indexAction()
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
    } */

    public function indexAction()
    {
        // Set return
        $return = array(
            'website' => Pi::url(),
            'module' => $this->params('module'),
        );
        // Set view
        $this->view()->setTemplate(false)->setLayout('layout-content');
        return Json::encode($return);
    }

    public function storyAllAction()
    {
        // Get info from url
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get topic or homepage setting
        $topic = Pi::api('topic', 'news')->canonizeTopic();
        // Set story info
        $where = array('status' => 1);
        // Get story List
        $storyList = $this->storyJsonList($where);
        // Set view
        $this->view()->setTemplate(false)->setLayout('layout-content');
        return Json::encode($storyList);
    }

    public function storyTopicAction()
    {
        // Get info from url
        $id = $this->params('id');
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get topic information from model
        $topic = $this->getModel('topic')->find($id);
        $topic = Pi::api('topic', 'news')->canonizeTopic($topic);
        // Check category
        if (!$topic || $topic['status'] != 1) {
            $storyList = array();
        } else {
            // Set story info
            $where = array('status' => 1, 'topic' => $topic['ids']);
            // Get story List
            $storyList = $this->storyJsonList($where);
        }
        // Set view
        $this->view()->setTemplate(false)->setLayout('layout-content');
        return Json::encode($storyList);
    }

    public function storySingleAction()
    {
        // Get info from url
        $id = $this->params('id');
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get topic list
        $topicList = Pi::registry('topicList', 'news')->read();
        // Get author list
        $authorList = Pi::registry('authorList', 'news')->read();
        // Find story
        $story = $this->getModel('story')->find($id);
        $story = Pi::api('story', 'news')->canonizeStory($story, $topicList, $authorList);
        // Check item
        if (!$story || $story['status'] != 1) {
            $storySingle = array();
        } else {
            $storySingle = array(
                'id' => $story['id'],
                'title' => $story['title'],
                'subtitle' => $story['subtitle'],
                'topic' => $story['topic'][0],
                'summary' => $story['text_summary'],
                'description' => $story['text_description'],
                'body' => $story['text_summary'] . $story['text_description'],
                'time_publish' => $story['time_publish'],
                'time_publish_view' => $story['time_publish_view'],
                'storyUrl' => $story['storyUrl'],
                'largeUrl' => $story['largeUrl'],
                'mediumUrl' => $story['mediumUrl'],
                'thumbUrl' => $story['thumbUrl'],
            );
        }
        $storySingle = array($storySingle);
        // Set view
        $this->view()->setTemplate(false)->setLayout('layout-content');
        return Json::encode($storySingle);
    }
}
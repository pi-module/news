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
use Zend\Json\Json;

class TopicController extends IndexController
{
    public function indexAction()
    {
        // Get info from url
        $module = $this->params('module');
        $slug = $this->params('slug');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get topic information from model
        $topic = $this->getModel('topic')->find($slug, 'slug');
        // Check slug set
        if(empty($topic)) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('Topic not set.'), '', 'error-404');
            return;
        }
        // Get topic or homepage setting
        $topic = Pi::api('topic', 'news')->canonizeTopic($topic);
        // Check topic
        if ($topic['status'] != 1) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('Topic not active.'), '', 'error-404');
            return;
        }
        // Check topic style
        if ($topic['style'] == 'topic') {
            // Get topic list
            $where = array('status' => 1, 'pid' => $topic['id']);
            $order = array('id DESC');
            $select = $this->getModel('topic')->select()->where($where)->order($order);
            $rowset = $this->getModel('topic')->selectWith($select);
            foreach ($rowset as $row) {
                // Reset topic setting
                if (!empty($row) && is_object($row)) {
                    $setting = Json::decode($row->setting, true);
                    $setting['show_subid'] = 0;
                    $row->setting = Json::encode($setting);
                }
                // Canonize topic
                $topics[$row->id] = Pi::api('topic', 'news')->canonizeTopic($row);
            }
            // Set view
            $this->view()->assign('topics', $topics);
        } else {
            // Set story info
            $where = array('status' => 1, 'topic' => $topic['ids'], /*'time_publish <= ?' => time(),*/);
            // Get story List
            $storyList = $this->storyList($where, $topic['show_perpage'], $topic['show_order_link']);
            // Set paginator info
            $template = array(
                'controller'  => 'topic',
                'action'      => 'index',
                'slug'        => $topic['slug'],
            );
            // Get paginator
            $paginator = $this->storyPaginator($template, $where, $topic['show_perpage']);
            // Spotlight
            $spotlight = Pi::api('spotlight', 'news')->getSpotlight();
            // Set view
            $this->view()->assign('stores', $storyList);
            $this->view()->assign('paginator', $paginator);
            $this->view()->assign('spotlight', $spotlight);
        }
        // Set view
        $this->view()->headTitle($topic['seo_title']);
        $this->view()->headdescription($topic['seo_description'], 'set');
        $this->view()->headkeywords($topic['seo_keywords'], 'set');
        $this->view()->setTemplate($topic['template']);
        $this->view()->assign('topic', $topic);
        $this->view()->assign('config', $config);
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
        // Set header and title
        $title = __('List of all topics');
        // Set seo_keywords
        $filter = new Filter\HeadKeywords;
        $filter->setOptions(array(
            'force_replace_space' => true
        ));
        $seoKeywords = $filter($title);
        // Set view
        $this->view()->headTitle($title);
        $this->view()->headdescription($title, 'set');
        $this->view()->headkeywords($seoKeywords, 'set');
        $this->view()->setTemplate('topic-list');
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
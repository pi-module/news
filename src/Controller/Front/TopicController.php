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

class TopicController extends IndexController
{
    public function indexAction()
    {
        // Get info from url
        $module = $this->params('module');
        $slug   = $this->params('slug');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Check deactivate view
        if ($config['admin_deactivate_view']) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('Page not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Get topic information from model
        $topic = $this->getModel('topic')->find($slug, 'slug');

        // Check slug set
        if (empty($topic)) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('Topic not set.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Get topic or homepage setting
        $topic = Pi::api('topic', 'news')->canonizeTopic($topic);

        // Check topic
        if ($topic['status'] != 1 || $topic['type'] != 'general') {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('Topic not active.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Check topic style
        if ($topic['style'] == 'topic') {
            // Get topic list
            $where  = ['status' => 1, 'pid' => $topic['id']];
            $order  = ['id DESC'];
            $select = $this->getModel('topic')->select()->where($where)->order($order);
            $rowset = $this->getModel('topic')->selectWith($select);
            foreach ($rowset as $row) {
                // Reset topic setting
                if (!empty($row) && is_object($row)) {
                    $setting               = json_decode($row->setting, true);
                    $setting['show_subid'] = 0;
                    $row->setting          = json_encode($setting);
                }

                // Canonize topic
                $topics[$row->id] = Pi::api('topic', 'news')->canonizeTopic($row);
            }

            // Set view
            $this->view()->assign('topics', $topics);
        } else {
            // Set story info
            $where = [
                'status' => 1,
                'topic'  => $topic['ids'],
                'type'   => [
                    'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download',
                ],
            ];

            // Set paginator info
            $template = [
                'controller' => 'topic',
                'action'     => 'index',
                'slug'       => $topic['slug'],
            ];

            // Get paginator
            $paginator = $this->storyPaginator($template, $where, $topic['show_perpage'], $topic['show_order_link']);

            // Get story List
            $storyList = $this->storyList($paginator, $topic['show_order_link']);

            // Spotlight
            $spotlight = Pi::api('spotlight', 'news')->getSpotlight();

            // Set view
            $this->view()->assign('stores', $storyList);
            $this->view()->assign('paginator', $paginator);
            $this->view()->assign('spotlight', $spotlight);
        }

        // Save statistics
        if (Pi::service('module')->isActive('statistics')) {
            Pi::api('log', 'statistics')->save('news', 'topic', $topic['id']);
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

        // Check deactivate view
        if ($config['admin_deactivate_view']) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('Page not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Get topic list
        $where  = ['status' => 1, 'type' => 'general'];
        $order  = ['time_create DESC', 'id DESC'];
        $select = $this->getModel('topic')->select()->where($where)->order($order);
        $rowset = $this->getModel('topic')->selectWith($select);
        foreach ($rowset as $row) {
            $topics[$row->id] = Pi::api('topic', 'news')->canonizeTopic($row);
        }

        // Set header and title
        $title = __('List of all topics');

        // Set seo_keywords
        $filter = new Filter\HeadKeywords;
        $filter->setOptions(
            [
                'force_replace_space' => true,
            ]
        );
        $seoKeywords = $filter($title);

        // Save statistics
        if (Pi::service('module')->isActive('statistics')) {
            Pi::api('log', 'statistics')->save('news', 'topicList');
        }

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
        if ($action == 'list') {
            return 'listAction';
        }
        return 'indexAction';
    }
}

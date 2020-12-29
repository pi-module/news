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
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Paginator\Adapter\DbSelect;

class IndexController extends ActionController
{
    public function indexAction()
    {
        // Get info from url
        $module = $this->params('module');
        $page   = $this->params('page', 1);
        //$search = $this->params('q');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Check deactivate view
        if ($config['admin_deactivate_view']) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('Page not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Save statistics
        if (Pi::service('module')->isActive('statistics')) {
            Pi::api('log', 'statistics')->save('news', 'index');
        }

        // Check index
        if ($config['style'] == 'topic') {
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

            // Set view
            $this->view()->headTitle($title);
            $this->view()->headdescription($title, 'set');
            $this->view()->headkeywords($seoKeywords, 'set');
            $this->view()->setTemplate('topic-list');
            $this->view()->assign('topics', $topics);
            $this->view()->assign('config', $config);
        } else {
            // Get topic or homepage setting
            $topic = Pi::api('topic', 'news')->canonizeTopic();

            // Set story info
            $where = [
                'status' => 1,
                'type'   => [
                    'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download',
                ],
            ];

            // Set paginator info
            $template = [
                'controller' => 'index',
                'action'     => 'index',
            ];

            // Get paginator
            $paginator = $this->storyPaginator($template, $where, $topic['show_perpage'], $topic['show_order_link']);

            // Get story List
            $storyList = $this->storyList($paginator, $topic['show_order_link']);

            // Spotlight
            $spotlight = Pi::api('spotlight', 'news')->getSpotlight();

            // Set header and title
            $itemTitleH1 = __('List of Latest stories');

            // Set view
            $this->view()->setTemplate($topic['template']);
            $this->view()->assign('stores', $storyList);
            $this->view()->assign('paginator', $paginator);
            $this->view()->assign('topic', $topic);
            $this->view()->assign('config', $config);
            $this->view()->assign('spotlight', $spotlight);
            $this->view()->assign('showIndexDesc', 1);
            $this->view()->assign('page', $page);
            $this->view()->assign('newsTitleH1', $itemTitleH1);
        }
    }

    public function storyList($paginator, $orderLink = 'publishDESC')
    {
        $story = [];
        $order = $this->setLinkOrder($orderLink);

        // Make list
        foreach ($paginator as $id) {
            $storyId[] = $id['story'];
        }
        if (empty($storyId)) {
            return $story;
        }

        // Set info
        $where = ['status' => 1, 'id' => $storyId];

        // Get topic list
        $topicList = Pi::registry('topicList', 'news')->read();

        // Get author list
        $authorList = Pi::registry('authorList', 'news')->read();

        // Get list of story
        $select = $this->getModel('story')->select()->where($where)->order($order);
        $rowset = $this->getModel('story')->selectWith($select);
        foreach ($rowset as $row) {
            $story[$row->id] = Pi::api('story', 'news')->canonizeStory($row, $topicList, $authorList);
        }

        // return story
        return $story;
    }

    public function storyJsonList($where)
    {
        // Set info
        $story  = [];
        $page   = $this->params('page', 1);
        $module = $this->params('module');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Set info
        $limit  = $config['json_perpage'];
        $offset = (int)($page - 1) * $limit;
        $order  = ['time_publish DESC', 'id DESC'];

        // Set info
        $columns = ['story' => new Expression('DISTINCT story')];

        // Get info from link table
        $select = $this->getModel('link')->select()->where($where)->columns($columns)->order($order)->offset($offset)->limit($limit);
        $rowset = $this->getModel('link')->selectWith($select)->toArray();

        // Make list
        foreach ($rowset as $id) {
            $storyId[] = $id['story'];
        }
        if (empty($storyId)) {
            return $story;
        }

        // Set info
        $where = ['status' => 1, 'id' => $storyId];

        // Get list of story
        $select = $this->getModel('story')->select()->where($where)->order($order);
        $rowset = $this->getModel('story')->selectWith($select);
        foreach ($rowset as $row) {
            $story[] = Pi::api('story', 'news')->canonizeStoryJson($row);
        }

        // return story
        return $story;
    }

    public function storyPaginator($template, $where, $itemPerPage, $order)
    {
        // Set info
        $page   = $this->params('page', 1);
        $order  = $this->setLinkOrder($order);
        $module = $this->params('module');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Set day limit
        if ($this->config('day_limit')) {
            $where['time_publish > ?'] = time() - (86400 * $config['day_limit']);
        }

        // Set info
        $columns = ['story_id' => new Expression('DISTINCT story'), '*'];
        // Get info from link table
        $select = $this->getModel('link')->select()->where($where)->columns($columns)->order($order);

        $resultSetPrototype = new  ResultSet();
        $paginatorAdapter   = new DbSelect(
            $select,
            $this->getModel('link')->getAdapter(),
            $resultSetPrototype
        );

        $template['slug']   = (isset($template['slug'])) ? $template['slug'] : '';
        $template['action'] = (isset($template['action'])) ? $template['action'] : 'index';
        $template['module'] = $this->params('module');
        $options            = [];
        if (isset($template['q']) && !empty($template['q'])) {
            foreach ($template['q'] as $key => $value) {
                $options['query'][$key] = $value;
            }
        }

        // paginator
        $paginator = new Paginator($paginatorAdapter);
        $paginator->setItemCountPerPage(intval($itemPerPage));
        $paginator->setCurrentPageNumber(intval($page));
        $paginator->setUrlOptions(
            [
                'router'  => $this->getEvent()->getRouter(),
                'route'   => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
                'params'  => array_filter(
                    [
                        'module'     => $template['module'],
                        'controller' => $template['controller'],
                        'action'     => $template['action'],
                        'slug'       => $template['slug'],
                    ]
                ),
                'options' => $options,
            ]
        );
        return $paginator;
    }

    public function setLinkOrder($sort = 'publishDESC')
    {
        // Set order
        switch ($sort) {
            case 'random':
                $order = [new Expression('RAND()')];
                break;

            case 'publishASC':
                $order = ['time_publish ASC', 'id ASC'];
                break;

            case 'publishDESC':
            default:
                $order = ['time_publish DESC', 'id DESC'];
                break;
        }
        return $order;
    }
}

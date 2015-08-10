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

class IndexController extends ActionController
{
    public function indexAction()
    {
        // Get info from url
        $module = $this->params('module');
        $page = $this->params('page', 1);
        $search = $this->params('q');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get topic or homepage setting
        $topic = Pi::api('topic', 'news')->canonizeTopic();
        // Check set search
        if (!empty($search)) {
            // Unset page
            if (isset($search['page'])) {
                unset($search['page']);
            }
            // Set item info from search
            $where = array();
            // Set title
            if (isset($search['title'])
                && !empty($search['title'])
            ) {
                // check type
                if (!isset($search['type']) || empty($search['type'])) {
                    $search['type'] = 1;
                }
                // switch
                switch ($search['type']) {
                    case 1:
                        $where['title LIKE ?'] = '%' . $search['title'] . '%';
                        break;

                    case 2:
                        $where['title LIKE ?'] = $search['title'] . '%';
                        break;

                    case 3:
                        $where['title LIKE ?'] = '%' . $search['title'];
                        break;

                    case 4:
                        $where['title LIKE ?'] = $search['title'];
                        break;
                }
            }
            // Check where
            if (!empty($where)) {
                // Set general where
                $where['status'] = 1;
                // Get item List
                $storyList = $this->searchList($where, $topic['show_perpage'], $topic['show_order_link']);
                // Set paginator info
                $template = array(
                    'controller' => 'index',
                    'action' => 'index',
                    'q' => $search,
                );
                // Get paginator
                $paginator = $this->searchPaginator($template, $where, $topic['show_perpage']);
                // Set view
                $this->view()->assign('searchResult', 1);
            } else {
                $this->getResponse()->setStatusCode(404);
                $this->terminate(__('The search query is wrong.'), '', 'error-404');
                $this->view()->setLayout('layout-simple');
                return;
            }
        } else {
            // Set story info
            $where = array('status' => 1);
            // Get story List
            $storyList = $this->storyList($where, $topic['show_perpage'], $topic['show_order_link']);
            // Set paginator info
            $template = array(
                'controller' => 'index',
                'action' => 'index',
            );
            // Get paginator
            $paginator = $this->storyPaginator($template, $where, $topic['show_perpage']);
        }
        // Spotlight
        $spotlight = Pi::api('spotlight', 'news')->getSpotlight();
        // Set header and title
        if (isset($search['title'])
            && !empty($search['title'])
        ) {
            // Set title
            $title = sprintf(__('Search result of %s'), $search['title']);
            $itemTitleH1 = sprintf(__('Search result of <strong>%s</strong>'), $search['title']);
            // Set seo_keywords
            $filter = new Filter\HeadKeywords;
            $filter->setOptions(array(
                'force_replace_space' => true
            ));
            $seoKeywords = $filter($title);
            // Set view
            $this->view()->headTitle($title);
            $this->view()->headDescription($title, 'set');
            $this->view()->headKeywords($seoKeywords, 'set');
        } else {
            $itemTitleH1 = __('List of Latest stories');
        }
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

    public function filterAction()
    {
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $data = $data->toArray();
            // Set search session
            $search = array();
            // Set title
            if (isset($data['title']) && !empty($data['title'])) {
                $search['title'] = $data['title'];
            }
            // Make url
            $url = $this->url('news', array(
                'controller' => 'index',
                'action' => 'index',
                'q' => '?' . http_build_query($search),
            ));
            // jump
            return $this->jump($url);
        } else {
            $message = __('Search again');
            $url = array('action' => 'index');
            $this->jump($url, $message, 'error');
        }
    }

    public function storyList($where, $limit, $orderLink = 'publishDESC')
    {
        // Set info
        $story = array();
        $page = $this->params('page', 1);
        $module = $this->params('module');
        $offset = (int)($page - 1) * $limit;
        $order = $this->setLinkOrder($orderLink);
        $limit = intval($limit);
        // Set day limit
        if ($this->config('daylimit')) {
            $where['time_publish > ?'] = time() - (86400 * $config['daylimit']);
        }
        // Set info
        $columns = array('story' => new Expression('DISTINCT story'));
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
        $where = array('status' => 1, 'id' => $storyId);
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

    public function searchList($where, $limit = 0, $orderLink = 'publishDESC')
    {
        // Set info
        $story = array();
        $page = $this->params('page', 1);
        $offset = (int)($page - 1) * $this->config('view_perpage');
        // Set limit
        if ($limit == 0) {
            $limit = intval($this->config('view_perpage'));
        }
        // Set order
        $order = $this->setLinkOrder($orderLink);
        // Get topic list
        $topicList = Pi::registry('topicList', 'news')->read();
        // Get author list
        $authorList = Pi::registry('authorList', 'news')->read();
        // Get list of item
        $select = $this->getModel('story')->select()->where($where)->order($order)->offset($offset)->limit($limit);
        $rowset = $this->getModel('story')->selectWith($select);
        foreach ($rowset as $row) {
            $story[$row->id] = Pi::api('story', 'news')->canonizeStory($row, $topicList, $authorList);
        }
        // return item
        return $story;
    }

    public function storyJsonList($where)
    {
        // Set info
        $story = array();
        $limit = 150;
        $page = $this->params('page', 1);
        $module = $this->params('module');
        $offset = (int)($page - 1) * $limit;
        $order = array('time_publish DESC', 'id DESC');
        // Set info
        $columns = array('story' => new Expression('DISTINCT story'));
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
        $where = array('status' => 1, 'id' => $storyId);
        // Get list of story
        $select = $this->getModel('story')->select()->where($where)->order($order);
        $rowset = $this->getModel('story')->selectWith($select);
        foreach ($rowset as $row) {
            $story[] = Pi::api('story', 'news')->canonizeStoryJson($row);
        }
        // return story
        return $story;
    }

    /* public function storyPaginator($template, $where, $limit)
    {
        $page = $this->params('page', 1);
        //
        $template['slug'] = (isset($template['slug'])) ? $template['slug'] : '';
        $template['year'] = (isset($template['year'])) ? $template['year'] : '';
        $template['month'] = (isset($template['month'])) ? $template['month'] : '';
        // get count
        $columns = array('count' => new Expression('count(DISTINCT `story`)'));
        $select = $this->getModel('link')->select()->where($where)->columns($columns);
        $count = $this->getModel('link')->selectWith($select)->current()->count;
        // paginator
        $paginator = Paginator::factory(intval($count));
        $paginator->setItemCountPerPage(intval($limit));
        $paginator->setCurrentPageNumber(intval($page));
        $paginator->setUrlOptions(array(
            'router' => $this->getEvent()->getRouter(),
            'route' => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
            'params' => array_filter(array(
                'module' => $this->getModule(),
                'controller' => $template['controller'],
                'action' => $template['action'],
                'slug' => $template['slug'],
                'year' => $template['year'],
                'month' => $template['month'],
            )),
        ));
        return $paginator;
    } */

    public function storyPaginator($template, $where, $limit)
    {
        $template['module'] = $this->params('module');
        $template['page'] = $this->params('page', 1);
        $template['limit'] = $limit;
        // get count
        $columns = array('count' => new Expression('count(DISTINCT `story`)'));
        $select = $this->getModel('link')->select()->where($where)->columns($columns);
        $template['count'] = $this->getModel('link')->selectWith($select)->current()->count;
        // paginator
        $paginator = $this->canonizePaginator($template);
        return $paginator;
    }

    public function searchPaginator($template, $where, $limit)
    {
        $template['module'] = $this->params('module');
        $template['page'] = $this->params('page', 1);
        $template['limit'] = $limit;
        // get count
        $columns = array('count' => new Expression('count(*)'));
        $select = $this->getModel('story')->select()->where($where)->columns($columns);
        $template['count'] = $this->getModel('story')->selectWith($select)->current()->count;
        // paginator
        $paginator = $this->canonizePaginator($template);
        return $paginator;
    }

    public function canonizePaginator($template)
    {
        $template['slug'] = (isset($template['slug'])) ? $template['slug'] : '';
        $template['action'] = (isset($template['action'])) ? $template['action'] : 'index';

        $options = array();
        if (isset($template['q']) && !empty($template['q'])) {
            foreach ($template['q'] as $key => $value) {
                $options['query'][$key] = $value;
            }
        }

        // paginator
        $paginator = Paginator::factory(intval($template['count']));
        $paginator->setItemCountPerPage(intval($template['limit']));
        $paginator->setCurrentPageNumber(intval($template['page']));
        $paginator->setUrlOptions(array(
            'router' => $this->getEvent()->getRouter(),
            'route' => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
            'params' => array_filter(array(
                'module' => $this->getModule(),
                'controller' => $template['controller'],
                'action' => $template['action'],
                'slug' => $template['slug'],
            )),
            'options' => $options,
        ));
        return $paginator;
    }

    /* public function jsonList($topic, $start, $limit)
    {
        // Set story info
        if ($start) {
            $where = array('status' => 1, 'topic' => $topic['ids'], 'story < ?' => $start);
        } else {
            $where = array('status' => 1, 'topic' => $topic['ids']);
        }
        // Set info
        $story = array();
        $module = $this->params('module');
        $order = $this->setLinkOrder($topic['show_order_link']);
        $limit = ($limit) ? $limit : $topic['show_perpage'];
        $limit = intval($limit);
        // Set day limit
        if ($this->config('daylimit')) {
            $where['time_publish > ?'] = time() - (86400 * $config['daylimit']);
        }
        // Set info
        $columns = array('story' => new Expression('DISTINCT story'));
        // Get info from link table
        $select = $this->getModel('link')->select()->where($where)->columns($columns)->order($order)->limit($limit);
        $rowset = $this->getModel('link')->selectWith($select)->toArray();
        // Make list
        foreach ($rowset as $id) {
            $storyId[] = $id['story'];
        }
        if (empty($storyId)) {
            return $story;
        }
        // Set info
        $where = array('status' => 1, 'id' => $storyId);
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
    } */

    public function setLinkOrder($sort = 'publishDESC')
    {
        // Set order
        switch ($sort) {
            case 'random':
                $order = array(new Expression('RAND()'));
                break;

            case 'publishASC':
                $order = array('time_publish ASC', 'id ASC');
                break;

            case 'publishDESC':
            default:
                $order = array('time_publish DESC', 'id DESC');
                break;
        }
        return $order;
    }
}
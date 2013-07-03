<?php
/**
 * News index controller
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

class IndexController extends ActionController
{
    public function indexAction()
    {
        // Get info from url
        $page = $this->params('page', 1);
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get topic or homepage setting
        $topic = Pi::service('api')->news(array('Topic', 'Setting'), $config);
        // Get list of topic ids
        $topicId = Pi::service('api')->news(array('Topic', 'Id'));
        // Set info
        $offset = (int)($page - 1) * $topic['perpage'];
        $limit = intval($topic['perpage']);
        $where = array('status' => 1, 'topic' => $topicId, 'publish <= ?' => time());
        // Story
        $story = $this->StoryList($where, $offset, $limit);
        // Set paginator
        $template = array('module' => $module, 'controller' => 'index', 'page' => '%page%');
        $paginator = $this->StoryPaginator($template, $where, $page, $limit);
        // Spotlight
        $spotlight = Pi::service('api')->news(array('Spotlight', 'load'), $config);
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
    
    public function StoryList($where, $offset, $limit)
    {
        $id = array();
        $story = array();
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get topic list
        $topicList = Pi::service('api')->news(array('Topic', 'Info'));
        // Set info
        $columns = array('story' => new \Zend\Db\Sql\Expression('DISTINCT story'));
        $order = array('publish DESC', 'id DESC');
        if ($config['daylimit']) {
            $where['publish > ?'] = time() - (86400 * $config['daylimit']);
        }
        // Get info from link table
        $select = $this->getModel('link')->select()->where($where)->columns($columns)->order($order)->offset($offset)->limit($limit);
        $rowset = $this->getModel('link')->selectWith($select)->toArray();
        // Make list
        foreach ($rowset as $id) {
            $stpryId[] = $id['story'];
        }
        // Set info
        $column = array('id', 'title', 'alias', 'topic', 'short', 'important', 'publish', 'hits', 'image', 'path', 'comments');
        $where = array('status' => 1, 'id' => $stpryId);
        // Get list of story
        $select = $this->getModel('story')->select()->columns($column)->where($where)->order($order);
        $rowset = $this->getModel('story')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $story[$row->id] = $row->toArray();
            $storytopics = Json::decode($story[$row->id]['topic']);
            foreach ($storytopics as $storytopic) {
                $story[$row->id]['topics'][$storytopic]['title'] = $topicList[$storytopic]['title'];
                $story[$row->id]['topics'][$storytopic]['alias'] = $topicList[$storytopic]['alias'];
            }
            $story[$row->id]['short'] = Pi::service('markup')->render($story[$row->id]['short'], 'text', 'html');
            $story[$row->id]['publish'] = _date($story[$row->id]['publish']);
            if ($story[$row->id]['image']) {
                $story[$row->id]['originalurl'] = Pi::url(sprintf('upload/%s/original/%s/%s', $this->config('image_path'), $story[$row->id]['path'], $story[$row->id]['image']));
                $story[$row->id]['largeurl'] = Pi::url(sprintf('upload/%s/large/%s/%s', $this->config('image_path'), $story[$row->id]['path'], $story[$row->id]['image']));
                $story[$row->id]['mediumurl'] = Pi::url(sprintf('upload/%s/medium/%s/%s', $this->config('image_path'), $story[$row->id]['path'], $story[$row->id]['image']));
                $story[$row->id]['thumburl'] = Pi::url(sprintf('upload/%s/thumb/%s/%s', $this->config('image_path'), $story[$row->id]['path'], $story[$row->id]['image']));
            }
        }
        // return story
        return $story;
    }
    
    public function StoryPaginator($template, $where, $page, $perpage)
    {
        $columns = array('count' => new \Zend\Db\Sql\Expression('count(DISTINCT `story`)'));
        $select = $this->getModel('link')->select()->where($where)->columns($columns);
        $count = $this->getModel('link')->selectWith($select)->current()->count;
        $paginator = \Pi\Paginator\Paginator::factory(intval($count));
        $paginator->setItemCountPerPage($perpage);
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(array(
            'template' => $this->url('.news', $template),
        ));
        // return paginator
        return $paginator;
    }
}
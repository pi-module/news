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

class TagController extends IndexController
{
    public function termAction()
    {
        // Get info from url
        $module = $this->params('module');
        $slug = $this->params('slug');
        $action = $this->params('action');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get topic or homepage setting
        $topic = Pi::api('news', 'topic')->canonizeTopic();
        // Check slug
        if (!isset($slug) || empty($slug)) {
            $url = array('', 'module' => $module, 'controller' => 'index', 'action' => 'index');
            $this->jump($url, __('The tag not found.'));
        }
        // Get id from tag module
        $tags = Pi::service('tag')->getList($module, $slug);
        // Check slug
        if (empty($tags)) {
            $url = array('', 'module' => $module, 'controller' => 'index', 'action' => 'index');
            $this->jump($url, __('The tag not found.'));
        }
        // Set story info
        $where = array('status' => 1, 'story' => $tags, 'time_publish <= ?' => time());
        // Get story List
        $storyList = $this->storyList($where, $topic['show_perpage']);
        // Set paginator info
        $template = array(
            'controller' => 'tag',
            'action' => 'term',
            'slug' => $topic['slug'],
            );
        // Get paginator
        $paginator = $this->storyPaginator($template, $where, $topic['show_perpage']);
        // Spotlight
        $spotlight = Pi::api('news', 'spotlight')->getSpotlight();
        // Set view
        $this->view()->headTitle(sprintf(__('All stores from %s'), $slug));
        $this->view()->headdescription(sprintf(__('All stores from %s'), $slug), 'set');
        $this->view()->headkeywords($slug, 'set');
        $this->view()->setTemplate($topic['template']);
        $this->view()->assign('stores', $storyList);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('topic', $topic);
        $this->view()->assign('config', $config);
        $this->view()->assign('spotlight', $spotlight);
    }

    public function listAction()
    {
        // Get info from url
        $module = $this->params('module');
        $where = array('module' => $module);
        $order = array('count DESC', 'id DESC');
        $select = Pi::model('stats', 'tag')->select()->where($where)->order($order);
        $rowset = Pi::model('stats', 'tag')->selectWith($select);
        foreach ($rowset as $row) {
            $tag = Pi::model('tag', 'tag')->find($row->tag);
            $tagList[$row->id] = $row->toArray();
            $tagList[$row->id]['term'] = $tag['term'];
            $tagList[$row->id]['url'] = $this->url('', array(
                'controller' => 'tag', 
                'action' => 'term', 
                'slug' => urldecode($tag['term'])
                ));
        }
        // Set view
        $this->view()->setTemplate('tag_list');
        $this->view()->assign('tagList', $tagList);
    }
}	
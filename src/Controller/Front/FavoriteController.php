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

class FavoriteController extends IndexController
{
    public function indexAction()
    {
        /* // Get info from url
        $page = $this->params('page', 1);
        $module = $this->params('module');
        $uid = Pi::user()->getId();
        // Check user
        if (!$uid) {
            $this->jump(array('route' => '.news', 'module' => $module, 'controller' => 'index'), __('The user not select'));
        }
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get topic or homepage setting
        $topic = Pi::service('api')->news(array('Topic', 'Setting'), $config);
        // Get list of topic ids
        $topicId = Pi::service('api')->news(array('Topic', 'Id'));
        // Set offset
        $offset = (int)($page - 1) * $topic['perpage'];
        // Get favorite ides
        $favoriteId = Pi::service('api')->favorite(array('Favorite', 'userFavorite'), $uid, $module);
        // Check favorite
        if (empty($favoriteId)) {
            $this->jump(array('route' => '.news', 'module' => $module, 'controller' => 'index'), __('You have not any favorite story'));
        }
        // Set info
        $limit = intval($topic['perpage']);
        $where = array('status' => 1, 'topic' => $topicId, 'time_publish <= ?' => time(), 'story' => $favoriteId);
        // Story
        $story = $this->StoryList($where, $offset, $limit);
        // Set paginator
        $template = array('module' => $module, 'controller' => 'favorite', 'page' => '%page%');
        $paginator = $this->StoryPaginator($template, $where, $page, $limit);
        // Spotlight
        $spotlight = Pi::service('api')->news(array('Spotlight', 'load'), $config);
        // Set view
        $this->view()->headTitle(__('All your favorite stores'));
        $this->view()->headdescription(__('All your favorite stores'), 'set');
        $this->view()->headkeywords(__('Favorite,Story'), 'set');
        $this->view()->setTemplate($topic['template']);
        $this->view()->assign('stores', $story);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('topic', $topic);
        $this->view()->assign('config', $config);
        $this->view()->assign('spotlight', $spotlight); */
        $this->view()->setTemplate('empty');
    }
}
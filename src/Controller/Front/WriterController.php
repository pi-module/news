<?php
/**
 * News reporter controller
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

class WriterController extends IndexController
{
    public function indexAction()
    {
        // Get info from url
        $alias = $this->params('alias');
        $module = $this->params('module');
        $page = $this->params('page', 1);
        // Check alias
        if (empty($alias)) {
            $this->jump(array('route' => '.news', 'module' => $module, 'controller' => 'writer', 'action' => 'list'), __('Got to writer list.'));
        }
        // Find user
        $user = Pi::model('user_account')->find($alias, 'identity');
        // Check alias
        if (empty($user)) {
            $this->jump(array('route' => '.news', 'module' => $module, 'controller' => 'writer', 'action' => 'list'), __('Got to writer list.'));
        }
        // Set user array
        $user = $user->toArray();
        // Get info from writer table
        $writer = $this->getModel('writer')->find($user['id'], 'author')->toArray();
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get topic or homepage setting
        $topic = Pi::service('api')->news(array('Topic', 'Setting'), $config);
        // Get list of topic ids
        $topicId = Pi::service('api')->news(array('Topic', 'Id'));
        // Set info
        $offset = (int)($page - 1) * $topic['perpage'];
        $limit = intval($topic['perpage']);
        $where = array('status' => 1, 'topic' => $topicId, 'publish <= ?' => time(), 'author' => $user['id']);
        // Story
        $story = $this->StoryList($where, $offset, $limit);
        // Set paginator
        $template = array('module' => $module, 'controller' => 'writer', 'alias' => urlencode($alias), 'page' => '%page%');
        $paginator = $this->StoryPaginator($template, $where, $page, $limit);
        // Spotlight
        $spotlight = Pi::service('api')->news(array('Spotlight', 'load'), $config);
        // Set view
        $this->view()->headTitle(sprintf(__('All stores from %s'), $alias));
        $this->view()->headDescription($alias, 'set');
        $this->view()->headKeywords($alias, 'set');
        $this->view()->setTemplate($topic['template']);
        $this->view()->assign('stores', $story);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('topic', $topic);
        $this->view()->assign('config', $config);
        $this->view()->assign('spotlight', $spotlight);
        $this->view()->assign('user', $user);
    }

    public function listAction()
    {
        // Get params
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get info
        $select = $this->getModel('writer')->select()->order(array('count DESC'));
        $rowset = $this->getModel('writer')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $list[$row->id] = $row->toArray();
            $user = Pi::model('user_account')->find($row->author);
            $list[$row->id]['identity'] = $user->identity;
            $list[$row->id]['email'] = $user->email;
        }
        // Set view
        $this->view()->headTitle(__('List of all writers'));
        $this->view()->headDescription(__('List of all writers'), 'set');
        $this->view()->headKeywords(__('List,writers'), 'set');
        $this->view()->setTemplate('writer_list');
        $this->view()->assign('writers', $list);
        $this->view()->assign('config', $config);
    }
}
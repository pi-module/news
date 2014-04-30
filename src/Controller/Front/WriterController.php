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

class WriterController extends IndexController
{
    public function indexAction()
    {
        // Get info from url
        $module = $this->params('module');
        $slug = $this->params('slug');
        $action = $this->params('action');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get topic or homepage setting
        $topic = Pi::api('topic', 'news')->canonizeTopic();
        // Check slug
        if (!isset($slug) || empty($slug)) {
            $url = array('', 'module' => $module, 'controller' => 'index', 'action' => 'index');
            $this->jump($url, __('The tag not found.'));
        }
        // Get user
        $user = Pi::user()->bind($slug, 'identity');
        $writer = $this->getModel('writer')->find($user->id, 'uid')->toArray();
        $writer['identity'] = $user->identity;
        $writer['name'] = $user->name;
        $writer['email'] = $user->email;
        $writer['avatar'] = '';
        // Set story info
        $where = array('status' => 1, /*'time_publish <= ?' => time(),*/ 'uid' => $writer['uid']);
        // Get story List
        $storyList = $this->storyList($where, $topic['show_perpage']);
        // Set paginator info
        $template = array(
            'controller' => 'tag',
            'action' => 'term',
            'slug' => $writer['user']['identity'],
            );
        // Get paginator
        $paginator = $this->storyPaginator($template, $where, $topic['show_perpage']);
        // Spotlight
        $spotlight = Pi::api('spotlight', 'news')->getSpotlight();
        // Set header and title
        $title = sprintf(__('All stores from %s'), $writer['user']['identity']);
        $seoTitle = Pi::api('text', 'news')->title($title);
        $seoDescription = Pi::api('text', 'news')->description($title);
        $seoKeywords = Pi::api('text', 'news')->keywords($title);
        // Set view
        $this->view()->headTitle($seoTitle);
        $this->view()->headDescription($seoDescription, 'set');
        $this->view()->headKeywords($seoKeywords, 'set');
        $this->view()->setTemplate($topic['template']);
        $this->view()->assign('stores', $storyList);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('topic', $topic);
        $this->view()->assign('config', $config);
        $this->view()->assign('spotlight', $spotlight);
        $this->view()->assign('writer', $writer);
        $this->view()->assign('title', $title);
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
            $user = Pi::user()->get($row->uid, array('id', 'identity', 'name', 'email'));
            $list[$row->id]['identity'] = $user['identity'];
            $list[$row->id]['email'] = $user['email'];
            $list[$row->id]['avatar'] = '';
            $list[$row->id]['url'] = $this->url('', array(
                'module' => $module,
                'controller' => 'writer',
                'slug' => $list[$row->id]['identity'],
            ));
        }
        // Set header and title
        $title = __('List of all writers');
        $seoTitle = Pi::api('text', 'news')->title($title);
        $seoDescription = Pi::api('text', 'news')->description($title);
        $seoKeywords = Pi::api('text', 'news')->keywords($title);
        // Set view
        $this->view()->headTitle($seoTitle);
        $this->view()->headDescription($seoDescription, 'set');
        $this->view()->headKeywords($seoKeywords, 'set');
        $this->view()->setTemplate('writer_list');
        $this->view()->assign('writers', $list);
        $this->view()->assign('config', $config);
        $this->view()->assign('title', $title);
    }
}
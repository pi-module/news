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

class AuthorController extends ActionController
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
        // Get topic or homepage setting
        $topic = Pi::api('topic', 'news')->canonizeTopic();
        // Get topic information from model
        $author = $this->getModel('author')->find($slug, 'slug');
        $author = Pi::api('author', 'news')->canonizeAuthor($author);
        // Check status
        if (!$author || $author['status'] != 1) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('The author not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Get role
        $roles = Pi::api('author', 'news')->getFormRole();
        // Get story
        $storyList = Pi::api('author', 'news')->getStoryList($author['id'], $roles);

        // Save statistics
        if (Pi::service('module')->isActive('statistics')) {
            Pi::api('log', 'statistics')->save('news', 'author', $author['id']);
        }

        // Set view
        $this->view()->headTitle($author['seo_title']);
        $this->view()->headdescription($author['seo_description'], 'set');
        $this->view()->headkeywords($author['seo_keywords'], 'set');
        $this->view()->setTemplate('author-index');
        $this->view()->assign('stores', $storyList);
        $this->view()->assign('author', $author);
        $this->view()->assign('roles', $roles);
        $this->view()->assign('topic', $topic);
        $this->view()->assign('config', $config);
    }

    public function listAction()
    {
        // Get page
        $page   = $this->params('page', 1);
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
        // Set info
        $order  = ['title ASC', 'id ASC'];
        $where  = ['status' => 1];
        $author = [];
        // Get list of author
        $select = $this->getModel('author')->select()->where($where)->order($order);
        $rowset = $this->getModel('author')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $author[$row->id] = Pi::api('author', 'news')->canonizeAuthor($row);
        }
        // Set header and title
        $title = __('List of all authors');
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
            Pi::api('log', 'statistics')->save('news', 'authorList');
        }
        // Set view
        $this->view()->headTitle($title);
        $this->view()->headDescription($title, 'set');
        $this->view()->headKeywords($seoKeywords, 'set');
        $this->view()->setTemplate('author-list');
        $this->view()->assign('authors', $author);
        $this->view()->assign('config', $config);
    }
}    
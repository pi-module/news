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

class FavouriteController extends IndexController
{
    public function indexAction()
    {
        // Check user is login or not
        Pi::service('authentication')->requireLogin();
        // Get info from url
        $module = $this->params('module');
        $userId = Pi::user()->getId();
        // Check user
        if (!$userId) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('The user not select'), '', 'error-404');
            return;
        }
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get topic or homepage setting
        $topic = Pi::api('topic', 'news')->canonizeTopic();
        // Get story id
        $storyId = Pi::api('favourite', 'favourite')->userFavourite($userId, $module);
        // Check id
        if (empty($storyId)) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('No favourite find by you'), '', 'error-404');
            return;
        }
        // Set story info
        $where = array('status' => 1, 'story' => $storyId);
        // Get story List
        $storyList = $this->storyList($where, $topic['show_perpage'], $topic['show_order_link']);
        // Set paginator info
        $template = array(
            'controller' => 'favourite',
            'action' => 'index',
        );
        // Get paginator
        $paginator = $this->storyPaginator($template, $where, $topic['show_perpage']);
        // Spotlight
        $spotlight = Pi::api('spotlight', 'news')->getSpotlight();

        // Set header and title
        $title = __('All favourite stories by you');
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
        $this->view()->setTemplate($topic['template']);
        $this->view()->assign('stores', $storyList);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('spotlight', $spotlight);
        $this->view()->assign('topic', $topic);
        $this->view()->assign('config', $config);
    }
}
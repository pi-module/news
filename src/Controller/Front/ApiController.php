<?php

namespace Module\News\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;

class ApiController extends ActionController
{
    public function favouriteAction()
    {
        /* @var Pi\Mvc\Controller\Plugin\View $view */
        $view = $this->view();

        Pi::service('log')->mute();

        // Get info from url
        $slug   = $this->params('slug');
        $module = $this->params('module');

        // Get Module Config
        $config = Pi::service('registry')->config->read($module);

        // Find story
        $story = $this->getModel('story')->find($slug, 'slug');

        // favourite
        if ($config['favourite_bar'] && Pi::service('module')->isActive('favourite')) {
            $favourite['is']     = Pi::api('favourite', 'favourite')->loadFavourite($module, 'story', $story['id']);
            $favourite['item']   = $story['id'];
            $favourite['table']  = 'story';
            $favourite['module'] = $module;
            $view->assign('favourite', $favourite);

            $configFavourite = Pi::service('registry')->config->read('favourite');
            if ($configFavourite['favourite_list']) {
                $favouriteList = Pi::api('favourite', 'favourite')->listItemFavourite('news', 'story', $story['id']);
                $view->assign('favouriteList', $favouriteList);
            }
        }

        $view->setLayout('layout-content');
        $view->setTemplate('partial/favourite');

        header("X-Robots-Tag: noindex, nofollow", true);

        echo Pi::service('view')->render($view->getViewModel());
        die();
    }

    public function hitAction()
    {
        // Get info from url
        $module = $this->params('module');
        $slug   = $this->params('slug');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Find story
        $story = Pi::model('story', 'news')->find($slug, 'slug');

        // Update Hits
        if ($config['story_all_hits']) {
            $this->getModel('story')->increment('hits', ['id' => $story['id']]);
            $this->getModel('link')->increment('hits', ['story' => $story['id']]);
        } else {
            if (!isset($_SESSION['hits_news'][$story['id']])) {
                if (!isset($_SESSION['hits_news'])) {
                    $_SESSION['hits_news'] = [];
                }

                $_SESSION['hits_news'][$story['id']] = false;
            }

            if (!$_SESSION['hits_news'][$story['id']]) {
                $this->getModel('story')->increment('hits', ['id' => $story['id']]);
                $this->getModel('link')->increment('hits', ['story' => $story['id']]);
                $_SESSION['hits_news'][$story['id']] = true;
            }
        }

        /**
         * Get new hit count
         */
        $story = Pi::model('story', 'news')->find($slug, 'slug');

        return [
            'status' => 1,
            'hits'   => (int)$story->hits,
        ];
    }
}

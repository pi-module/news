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
}

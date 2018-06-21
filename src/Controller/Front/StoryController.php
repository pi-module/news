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

class StoryController extends ActionController
{
    public function indexAction()
    {
        // Get info from url
        $slug = $this->params('slug');
        $module = $this->params('module');
        // Get Module Config
        $config = Pi::service('registry')->config->read($module);
        // Check deactivate view
        if ($config['admin_deactivate_view']) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('Page not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Get topic list
        $topicList = Pi::registry('topicList', 'news')->read();
        // Get author list
        $authorList = Pi::registry('authorList', 'news')->read();
        // Find story
        $story = $this->getModel('story')->find($slug, 'slug');

        if($slug != $story['slug']){
            return $this->redirect()->toRoute('', array('slug' => $story['slug']))->setStatusCode(301);
        }

        $story = Pi::api('story', 'news')->canonizeStory($story, $topicList, $authorList);
        // Check status
        if (!$story || $story['status'] != 1 || !in_array($story['type'] , array(
                'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download'
            ))) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('The story not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Check time_publish
        if ($story['time_publish'] > time()) {
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('The Story not publish.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Update Hits
        if(!isset($_SESSION['hits_news'][$story['id']])){
            if(!isset($_SESSION['hits_news'])){
                $_SESSION['hits_news'] = array();
            }

            $_SESSION['hits_news'][$story['id']] = false;
        }

        if(!$_SESSION['hits_news'][$story['id']]){
            $this->getModel('story')->increment('hits', array('id' => $story['id']));
            $_SESSION['hits_news'][$story['id']] = true;
        }

        // Links
        $link = Pi::api('story', 'news')->Link($story['id'], array($story['topic_main']));
        $this->view()->assign('link', $link);
        // Related
        if ($config['show_related']) {
            $related = Pi::api('story', 'news')->Related($story['id'], $story['topic_main']);
            $this->view()->assign('relateds', $related);
        }
        // Attached
        if ($config['show_attach'] && $story['attach']) {
            $attach = Pi::api('story', 'news')->AttachList($story['id']);
            $this->view()->assign('attach', $attach);
        }
        // Attribute
        if ($config['show_attribute'] && $story['attribute']) {
            $attribute = Pi::api('attribute', 'news')->Story($story['id'], $story['topic_main']);
            $this->view()->assign('attribute', $attribute);
        }
        // Tag
        if ($config['show_tag'] && Pi::service('module')->isActive('tag')) {
            $tag = Pi::service('tag')->get($module, $story['id'], '');
            $this->view()->assign('tag', $tag);
        }
        // Author
        /* if ($config['show_author']) {
            $author = Pi::api('author', 'news')->getStorySingle($story['id']);
            $this->view()->assign('authors', $author);
        } */
        // Set vote
        if ($config['vote_bar'] && Pi::service('module')->isActive('vote')) {
            $vote['point'] = $story['point'];
            $vote['count'] = $story['count'];
            $vote['item'] = $story['id'];
            $vote['table'] = 'story';
            $vote['module'] = $module;
            $vote['type'] = 'star';
            $this->view()->assign('vote', $vote);
        }
        // favourite
        if ($config['favourite_bar'] && Pi::service('module')->isActive('favourite')) {
            $favourite['is'] = Pi::api('favourite', 'favourite')->loadFavourite($module, 'story', $story['id']);
            $favourite['item'] = $story['id'];
            $favourite['table'] = 'story';
            $favourite['module'] = $module;
            $this->view()->assign('favourite', $favourite);
            
            $configFavourite = Pi::service('registry')->config->read('favourite');
            if ($configFavourite['favourite_list']) {
                $favouriteList = Pi::api('favourite', 'favourite')->listItemFavourite('news', 'story', $story['id']);
                $this->view()->assign('favouriteList', $favouriteList);
            }
            
        }
        // Set template
        switch ($story['type']) {
            case 'download':
                $template = 'story-download';
                break;

            case 'media':
                $template = 'story-media';
                break;

            case 'gallery':
                $template = 'story-gallery';
                break;

            case 'image':
                $template = 'story-image';
                break;

            case 'magazine':
                $template = 'story-magazine';
                break;

            case 'article':
                $template = 'story-article';
                break;

            case 'text':
            default:
                $template = 'story-text';
                break;
        }

        // Save statistics
        if (Pi::service('module')->isActive('statistics')) {
            Pi::api('log', 'statistics')->save('news', 'story', $story['id']);
        }

        // Set view
        $this->view()->headTitle($story['seo_title']);
        $this->view()->headdescription($story['seo_description'], 'set');
        $this->view()->headkeywords($story['seo_keywords'], 'set');
        $this->view()->setTemplate($template);
        $this->view()->assign('story', $story);
        $this->view()->assign('config', $config);
    }
}
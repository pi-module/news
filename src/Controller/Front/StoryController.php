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
        // Get topic list
        $topicList = Pi::registry('topicList', 'news')->read();
        // Get author list
        $authorList = Pi::registry('authorList', 'news')->read();
        // Find story
        $story = $this->getModel('story')->find($slug, 'slug');
        $story = Pi::api('story', 'news')->canonizeStory($story, $topicList, $authorList);
        // Check status
        if (!$story || $story['status'] != 1) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('The story not found.'), '', 'error-404');
            return;
        }
        // Check time_publish
        if ($story['time_publish'] > time()) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('The Story not publish.'), '', 'error-404');
            return;
        }
        // Update Hits
        $this->getModel('story')->increment('hits', array('id' => $story['id']));
        // Links
        if ($config['show_nav']) {
            $link = Pi::api('story', 'news')->Link($story['id'], $story['topic']);
            $this->view()->assign('link', $link);
        }
        // Related
        if ($config['show_related']) {
            $related = Pi::api('story', 'news')->Related($story['id'], $story['topic']);
            $this->view()->assign('relateds', $related);
        }
        // Attached
        if ($config['show_attach'] && $story['attach']) {
            $attach = Pi::api('story', 'news')->AttachList($story['id']);
            $this->view()->assign('attach', $attach);
        } 
        // attribute
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
            $vote['type'] = 'plus';
            $this->view()->assign('vote', $vote);
        }
        // favourite
        if ($config['favourite_bar'] && Pi::service('module')->isActive('favourite')) {
            $favourite['is'] = Pi::api('favourite', 'favourite')->loadFavourite($module, 'story', $story['id']);
            $favourite['item'] = $story['id'];
            $favourite['table'] = 'story';
            $favourite['module'] = $module;
            $this->view()->assign('favourite', $favourite);
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

            case 'text':
            default:
                $template = 'story-text';
                break;
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
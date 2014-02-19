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
        // Find story
        $story = $this->getModel('story')->find($slug, 'slug');
        $story = Pi::api('story', 'news')->canonizeStory($story);
        // Check status
        if (!$story || $story['status'] != 1) {
            $this->jump(array('', 'module' => $module, 'controller' => 'index'), __('The story not found.'));
        }
        // Check time_publish
        if ($story['time_publish'] > time()) {
            $this->jump(array('', 'module' => $module, 'controller' => 'index'), __('The Story not publish.'));
        }
        // Update Hits
        $this->getModel('story')->update(array('hits' => $story['hits'] + 1), array('id' => $story['id']));
        // Writer
        if ($config['show_writer']) {
            $story['user'] = Pi::user()->get($story['uid'], array('id', 'identity', 'name', 'email'));
            $story['user']['url'] = $this->url('', array(
                'module' => $module,
                'controller' => 'writer',
                'slug' => $story['user']['identity'],
            ));
        }
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
        // Tag
        if ($config['show_tag']) {
            $tag = Pi::service('tag')->get($module, $story['id'], '');
            $this->view()->assign('tag', $tag);  
        }
        // Attached
        if ($config['show_attach'] && $story['attach']) {
            $attach = Pi::api('story', 'news')->AttachList($story['id']);
            $this->view()->assign('attach', $attach);
        } 
        // Extra
        if ($config['show_extra'] && $story['extra']) {
            $extra = Pi::api('extra', 'news')->Story($story['id']);
            $this->view()->assign('extra', $extra);
        }
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
        // favorite
        /* if ($config['favorite_bar'] && Pi::service('module')->isActive('favorite')) {
            $favorite['is'] = Pi::service('api')->favorite(array('Favorite', 'loadFavorite'), $module, $story['id']);
            $favorite['item'] = $story['id'];
            $favorite['module'] = $module;
            $favorite['table'] = 'story';
            $this->view()->assign('favorite', $favorite);
        }  */
        // Set view
        $this->view()->headTitle($story['seo_title']);
        $this->view()->headdescription($story['seo_description'], 'set');
        $this->view()->headkeywords($story['seo_keywords'], 'set');
        $this->view()->setTemplate('story_index');
        $this->view()->assign('story', $story);
        $this->view()->assign('config', $config);
    }
}
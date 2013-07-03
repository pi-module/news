<?php
/**
 * News story controller
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

class StoryController extends ActionController
{
    public function indexAction()
    {
        // Get info from url
        $alias = $this->params('alias');
        $module = $this->params('module');
        // Find story
        $story = $this->getModel('story')->find($alias, 'alias')->toArray();
        // Get Module Config
        $config = Pi::service('registry')->config->read($module);
        // Check page
        if (!$story || $story['status'] != 1) {
            $this->jump(array('route' => '.news', 'module' => $module, 'controller' => 'index'), __('The story not found.'));
        }
        // Check publish
        if ($story['publish'] > time()) {
            $this->jump(array('route' => '.news', 'module' => $module, 'controller' => 'index'), __('The Story not publish.'));
        }
        // Get topic or homepage setting
        $topic = Pi::service('api')->news(array('Topic', 'Setting'), $config);
        // Check  topic is active
        if (empty($topic)) {
            $this->jump(array('route' => '.news', 'module' => $module, 'controller' => 'index'), __('The topic not found.'));
        }
        // Update Hits
        $this->getModel('story')->update(array('hits' => $story['hits'] + 1), array('id' => $story['id']));
        // Set image
        if ($story['image']) {
            $story['originalurl'] = Pi::url(sprintf('upload/%s/original/%s/%s', $config['image_path'], $story['path'], $story['image']));
            $story['largeurl'] = Pi::url(sprintf('upload/%s/large/%s/%s', $config['image_path'], $story['path'], $story['image']));
            $story['mediumurl'] = Pi::url(sprintf('upload/%s/medium/%s/%s', $config['image_path'], $story['path'], $story['image']));
            $story['thumburl'] = Pi::url(sprintf('upload/%s/thumb/%s/%s', $config['image_path'], $story['path'], $story['image']));
        }
        // Set date
        $story['publish'] = _date($story['publish']);
        // Get writer identity
        $writer = Pi::model('user_account')->find($story['author'])->toArray();
        $story['identity'] = $writer['identity'];
        unset($writer);
        // Links
        if ($topic['shownav']) {
            $link = Pi::service('api')->news(array('Story', 'Link'), $story['id'], $story['topic']);
            $this->view()->assign('link', $link);
        }
        // Related
        if ($config['show_related']) {
            $related = Pi::service('api')->news(array('Story', 'Related'), $story['id'], $story['topic']);
            $this->view()->assign('relateds', $related);
        }
        // Set vote
        if ($config['vote_bar'] && Pi::service('module')->isActive('vote')) {
            $vote['point'] = $story['point'];
            $vote['count'] = $story['count'];
            $vote['item'] = $story['id'];
            $vote['module'] = $module;
            $vote['type'] = $config['vote_type'];
            $vote['table'] = 'story';
            $this->view()->assign('vote', $vote);
        }
        // favorite
        if ($config['favorite_bar'] && Pi::service('module')->isActive('favorite')) {
            $favorite['is'] = Pi::service('api')->favorite(array('Favorite', 'loadFavorite'), $module, $story['id']);
            $favorite['item'] = $story['id'];
            $favorite['module'] = $module;
            $favorite['table'] = 'story';
            $this->view()->assign('favorite', $favorite);
        }
        // Get list of attached files
        if ($story['attach']) {
            $attach = Pi::service('api')->news(array('Story', 'AttachList'), $config, $story['id']);
            $this->view()->assign('attach', $attach);
        }
        // Get list of extra fields
        if ($story['extra']) {
            $extra = Pi::service('api')->news(array('Extra', 'Story'), $story['id']);
            $this->view()->assign('extra', $extra);
        }
        // Find topic
        $story['topic'] = Pi::service('api')->news(array('Story', 'Topic'), $story['topic']);
        // Set view
        $this->view()->headTitle($story['title']);
        $this->view()->headDescription($story['description'], 'set');
        $this->view()->headKeywords($story['keywords'], 'set');
        $this->view()->setTemplate('story_index');
        $this->view()->assign('story', $story);
        $this->view()->assign('topic', $topic);
        $this->view()->assign('config', $config);
        $this->view()->assign('tags', Pi::service('tag')->get($module, $story['id'], ''));
        // Support Comment system for test
        if (Pi::service('module')->isActive('comment')) {
        	   // Set story url
            $url['route'] = $module . '-news';
            $url['parameter']['module'] = $module;
            $url['parameter']['controller'] = 'story';
            $url['parameter']['alias'] = $story['alias'];
            // Get comment
            $comment = Pi::service('api')->comment(array('Comment', 'Render'), $module, $story['id'], $url);
            $this->view()->assign('usecomment', $comment['config']['comment_active']);
            $this->view()->assign('commentForm', $comment['form']);
            $this->view()->assign('commentList', $comment['list']);
            $this->view()->assign('commentConfig', $comment['config']);
        }
    }
}
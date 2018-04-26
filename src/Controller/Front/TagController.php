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

class TagController extends IndexController
{
    public function termAction()
    {
        // Get info from url
        $module = $this->params('module');
        $slug = $this->params('slug');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Check deactivate view
        if ($config['admin_deactivate_view']) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('Page not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Check tag
        if (!Pi::service('module')->isActive('tag')) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('Tag module not installed.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Check slug
        if (!isset($slug) || empty($slug)) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('The tag not set.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Get id from tag module
        $tagId = array();
        $tags = Pi::service('tag')->getList($slug, $module);
        foreach ($tags as $tag) {
            $tagId[] = $tag['item'];
        }
        // Check slug
        if (empty($tagId)) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('The tag not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
        }
        // Get topic or homepage setting
        $topic = Pi::api('topic', 'news')->canonizeTopic();
        if ($topic['style'] == 'topic') {
            $topic['style'] = 'news';
            $topic['template'] = 'index-news';
            $topic['show_columns'] = '1';
            $topic['column_class'] = '';
        }

        // Set story info
        $where = array(
            'status' => 1,
            'story' => $tagId,
            'type' => array(
                'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download'
            )
        );
        // Set paginator info
        $template = array(
            'controller' => 'tag',
            'action' => 'term',
            'slug' => $slug,
        );
        // Get paginator
        $paginator = $this->storyPaginator($template, $where, $topic['show_perpage'], $topic['show_order_link']);
        // Get story List
        $storyList = $this->storyList($paginator, $topic['show_order_link']);
  
        // Spotlight
        $spotlight = Pi::api('spotlight', 'news')->getSpotlight();
        // Set header and title
        $title = sprintf(__('All stores from %s'), $slug);
        // Set seo_keywords
        $filter = new Filter\HeadKeywords;
        $filter->setOptions(array(
            'force_replace_space' => true
        ));
        $seoKeywords = $filter($title);

        // Save statistics
        if (Pi::service('module')->isActive('statistics')) {
            Pi::api('log', 'statistics')->save('news', 'tag');
        }

        // Set view
        $this->view()->headTitle($title);
        $this->view()->headDescription($title, 'set');
        $this->view()->headKeywords($seoKeywords, 'set');
        $this->view()->setTemplate($topic['template']);
        $this->view()->assign('stores', $storyList);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('topic', $topic);
        $this->view()->assign('config', $config);
        $this->view()->assign('spotlight', $spotlight);
        $this->view()->assign('title', $title);
    }

    public function listAction()
    {
        // Get info from url
        $module = $this->params('module');
        $tagList = array();
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Check deactivate view
        if ($config['admin_deactivate_view']) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('Page not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Check tag module install or not
        if (Pi::service('module')->isActive('tag')) {
            $where = array('module' => $module);
            $order = array('count DESC', 'id DESC');
            $select = Pi::model('stats', 'tag')->select()->where($where)->order($order);
            $rowset = Pi::model('stats', 'tag')->selectWith($select);
            foreach ($rowset as $row) {
                $tag = Pi::model('tag', 'tag')->find($row->term, 'term');
                $tagList[$row->id] = $row->toArray();
                $tagList[$row->id]['term'] = $tag['term'];
                $tagList[$row->id]['url'] = Pi::url($this->url('', array(
                    'controller' => 'tag',
                    'action' => 'term',
                    'slug' => urldecode($tag['term'])
                )));
            }
        }
        // Set header and title
        $title = __('List of all used tags');
        // Set seo_keywords
        $filter = new Filter\HeadKeywords;
        $filter->setOptions(array(
            'force_replace_space' => true
        ));
        $seoKeywords = $filter($title);

        // Save statistics
        if (Pi::service('module')->isActive('statistics')) {
            Pi::api('log', 'statistics')->save('news', 'tagList');
        }

        // Set view
        $this->view()->headTitle($title);
        $this->view()->headDescription($title, 'set');
        $this->view()->headKeywords($seoKeywords, 'set');
        $this->view()->setTemplate('tag-list');
        $this->view()->assign('title', $title);
        $this->view()->assign('tagList', $tagList);
    }
}
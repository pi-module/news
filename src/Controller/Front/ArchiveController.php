<?php
/**
 * News index controller
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
use Zend\Json\Json;

class ArchiveController extends IndexController
{
    public function indexAction()
    {
        // Get info from url
        $page = $this->params('page', 1);
        $year = $this->params('year', date('Y'));
        $month = $this->params('month', date('m'));
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get list of topic ids
        $topicId = Pi::service('api')->news(array('Topic', 'Id'));
        // Set year and month
        $start = mktime(0, 0, 0, $month, 1, $year);
        $end = mktime(0, 0, 0, $month + 1, 1, $year);
        // Set date name
        $date['next-month'] = strftime('%m', mktime(0, 0, 0, $month + 1, 1, $year));
        $date['prev-month'] = strftime('%m', mktime(0, 0, 0, $month - 1, 1, $year));
        $date['next-year'] = strftime('%Y', mktime(0, 0, 0, 1, 1, $year + 1));
        $date['prev-year'] = strftime('%Y', mktime(0, 0, 0, 1, 1, $year - 1));
        // Set date url
        $url['next-month'] = $this->url('.news', array('module' => $module, 'controller' => 'archive', 'year' => strftime('%Y', mktime(0, 0, 0, $month + 1, 1, $year)), 'month' => strftime('%m', mktime(0, 0, 0, $month + 1, 1, $year))));
        $url['prev-month'] = $this->url('.news', array('module' => $module, 'controller' => 'archive', 'year' => strftime('%Y', mktime(0, 0, 0, $month - 1, 1, $year)), 'month' => strftime('%m', mktime(0, 0, 0, $month - 1, 1, $year))));
        $url['next-year'] = $this->url('.news', array('module' => $module, 'controller' => 'archive', 'year' => strftime('%Y', mktime(0, 0, 0, 1, 1, $year + 1))));
        $url['prev-year'] = $this->url('.news', array('module' => $module, 'controller' => 'archive', 'year' => strftime('%Y', mktime(0, 0, 0, 1, 1, $year - 1))));
        // Set info
        $offset = (int)($page - 1) * $this->config('show_perpage');
        $limit = intval($this->config('show_perpage'));
        $where = array('status' => 1, 'topic' => $topicId, 'publish > ?' => $start, 'publish <= ?' => $end);
        // Story
        $story = $this->StoryList($where, $offset, $limit);
        // Set paginator
        $template = array('module' => $module, 'controller' => 'archive', 'year' => $year, 'month' => $month, 'page' => '%page%');
        $paginator = $this->StoryPaginator($template, $where, $page, $limit);
        // Set title
        $pageTitle = sprintf(__('Archive - %s - %s'), $month, $year);
        $pageDescription = sprintf(__('Archive of all stores in - %s - %s'), $month, $year);
        $pageKeywords = sprintf(__('Archive,stores,topic,%s,%s'), $month, $year);
        $message = sprintf(__('List if all stroes in <strong>%s / %s </strong>, You can see all stores in next or prev month and year'), $month, $year);
        // Set view
        $this->view()->headTitle($pageTitle);
        $this->view()->headDescription($pageDescription, 'set');
        $this->view()->headKeywords($pageKeywords, 'set');
        $this->view()->setTemplate('archive_index');
        $this->view()->assign('stores', $story);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('url', $url);
        $this->view()->assign('date', $date);
        $this->view()->assign('month', $month);
        $this->view()->assign('year', $year);
        $this->view()->assign('message', $message);
        $this->view()->assign('config', $config);
    }
}
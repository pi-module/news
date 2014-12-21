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

class ArchiveController extends IndexController
{
    public function indexAction()
    {
        // Get info from url
        $year = $this->params('year', date('Y'));
        $month = $this->params('month', date('m'));
        $module = $this->params('module');
        $archive = array();
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get topic or homepage setting
        $topic = Pi::api('topic', 'news')->canonizeTopic();
        // Set year and month
        $start = mktime(0, 0, 0, $month, 1, $year);
        $end = mktime(0, 0, 0, $month + 1, 1, $year);
        // Set date name
        $archive['date-next-month'] = strftime('%m', mktime(0, 0, 0, $month + 1, 1, $year));
        $archive['date-prev-month'] = strftime('%m', mktime(0, 0, 0, $month - 1, 1, $year));
        $archive['date-this-month'] = $month;
        $archive['date-next-year'] = strftime('%Y', mktime(0, 0, 0, 1, 1, $year + 1));
        $archive['date-prev-year'] = strftime('%Y', mktime(0, 0, 0, 1, 1, $year - 1));
        $archive['date-this-year'] = $year;
        // Set date url
        $archive['url-next-month'] = Pi::url($this->url('', array('module' => $module, 'controller' => 'archive', 'year' => strftime('%Y', mktime(0, 0, 0, $month + 1, 1, $year)), 'month' => strftime('%m', mktime(0, 0, 0, $month + 1, 1, $year)))));
        $archive['url-prev-month'] = Pi::url($this->url('', array('module' => $module, 'controller' => 'archive', 'year' => strftime('%Y', mktime(0, 0, 0, $month - 1, 1, $year)), 'month' => strftime('%m', mktime(0, 0, 0, $month - 1, 1, $year))));
        $archive['url-next-year'] = Pi::url($this->url('', array('module' => $module, 'controller' => 'archive', 'year' => strftime('%Y', mktime(0, 0, 0, 1, 1, $year + 1)))));
        $archive['url-prev-year'] = Pi::url($this->url('', array('module' => $module, 'controller' => 'archive', 'year' => strftime('%Y', mktime(0, 0, 0, 1, 1, $year - 1)))));
        // Set text
        $archive['title'] = sprintf(__('Archive - %s - %s'), $month, $year);
        $archive['description'] = sprintf(__('Archive of all stores in - %s - %s'), $month, $year);
        $archive['keywords'] = sprintf(__('Archive,stores,topic,%s,%s'), $month, $year);
        $archive['message'] = sprintf(__('List if all stroes in <strong>%s / %s </strong>, You can see all stores in next or prev month and year'), $month, $year);
        // Set story info
        $where = array('status' => 1, 'time_publish > ?' => $start, 'time_publish <= ?' => $end);
        // Get story List
        $storyList = $this->storyList($where, $topic['show_perpage'], $topic['show_order_link']);
        // Set paginator info
        $template = array(
            'controller'  => 'archive',
            'action'      => 'index',
            'year'        => $year, 
            'month'       => $month,
        );
        // Get paginator
        $paginator = $this->storyPaginator($template, $where, $topic['show_perpage']);
        // Spotlight
        $spotlight = Pi::api('spotlight', 'news')->getSpotlight();
        // Set view
        $this->view()->headTitle($archive['title']);
        $this->view()->headdescription($archive['description'], 'set');
        $this->view()->headkeywords($archive['keywords'], 'set');
        $this->view()->setTemplate($topic['template']);
        $this->view()->assign('stores', $storyList);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('topic', $topic);
        $this->view()->assign('config', $config);
        $this->view()->assign('spotlight', $spotlight);
        $this->view()->assign('archive', $archive);
    }
}
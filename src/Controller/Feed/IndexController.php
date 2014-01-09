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
namespace Module\News\Controller\Feed;

use Pi;
use Pi\Mvc\Controller\FeedController;
use Pi\Feed\Model as DataModel;

class IndexController extends FeedController
{
    public function indexAction()
    {
        $feed = $this->getDataModel(array(
            'title'         => __('News feed'),
            'description'   => __('Recent News.'),
            'date_created'  => time(),
        ));
        $columns = array('id', 'title', 'slug', 'short', 'time_publish');
        $order = array('time_publish DESC', 'id DESC');
        $where = array('status' => 1);
        $limit = intval($this->config('feed_num'));
        $select = $this->getModel('story')->select()->columns($columns)->where($where)->order($order)->limit($limit);
        $rowset = $this->getModel('story')->selectWith($select);
        foreach ($rowset as $row) {
            $entry = array();
            $entry['title'] = $row->title;
            $entry['description'] = $row->short;
            $entry['date_modified'] = (int)$row->time_publish;
            //$entry['link'] = '';
            $feed->entry = $entry;
        }
        return $feed;
    }
}
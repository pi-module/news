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
namespace Module\News\Api;

use Pi;
use Pi\Application\AbstractApi;

/*
 * Pi::api('spotlight', 'news')->getSpotlight($topic);
 */

class Spotlight extends AbstractApi
{
    public function getSpotlight($topic = -1)
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());

        $where1 = array('status' => 1, 'time_publish < ?' => time(), 'time_expire > ?' => time()); 
        $where2 = array('topic' => 0);
        $where3 = array('topic' => $topic);
        $order = array('id DESC', 'time_publish DESC');
        $limit = intval($config['spotlight_number']);
        $columns = array('story');
        
        $select = Pi::model('spotlight', $this->getModule())->select()
        ->columns($columns)->where($where1)->where($where2)->where($where3, 'OR')->order($order)->limit($limit);
        $rowset = Pi::model('spotlight', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $row = $row->toArray();
            $spotlightId[] = $row['story'];
        }
        if (!empty($spotlightId)) {
            $columns = array('id', 'title', 'slug', 'short', 'image', 'path');
            $order = array('time_publish DESC', 'id DESC');
            $where = array('status' => 1, 'id' => $spotlightId);
            $select = Pi::model('story', $this->getModule())->select()->columns($columns)->where($where)->order($order);
            $rowset = Pi::model('story', $this->getModule())->selectWith($select);
            foreach ($rowset as $row) {
                $story[$row->id] = $row->toArray();
                $story[$row->id]['short'] = mb_strlen($story[$row->id]['short'], 'utf-8') > 140 ? mb_substr($story[$row->id]['short'], 0, 140, 'utf-8') . "..." : $story[$row->id]['short'];
                if ($story[$row->id]['image']) {
                    $story[$row->id]['originalurl'] = Pi::url('upload/' . $config['image_path'] . '/original/' . $story[$row->id]['path'] . '/' . $story[$row->id]['image']);
                    $story[$row->id]['largeurl'] = Pi::url('upload/' . $config['image_path'] . '/large/' . $story[$row->id]['path'] . '/' . $story[$row->id]['image']);
                    $story[$row->id]['mediumurl'] = Pi::url('upload/' . $config['image_path'] . '/medium/' . $story[$row->id]['path'] . '/' . $story[$row->id]['image']);
                    $story[$row->id]['thumburl'] = Pi::url('upload/' . $config['image_path'] . '/thumb/' . $story[$row->id]['path'] . '/' . $story[$row->id]['image']);
                }
            }
            $spotlight['top'] = array_shift($story);
            $spotlight['list'] = $story;
            return $spotlight;
        }
        return false;
    }
}	
<?php
/**
 * News module Topic class
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

namespace Module\News\Api;

use Pi;
use Pi\Application\AbstractApi;

/*
 * Pi::service('api')->news(array('Spotlight', 'load'), $config, $topic);
 */

class Spotlight extends AbstractApi
{
    public function load($config, $topic = -1)
    {
        
        $where1 = array('status' => 1, 'publish < ?' => time(), 'expire > ?' => time()); 
        $where2 = array('topic' => 0);
        $where3 = array('topic' => $topic);
        $order = array('id DESC', 'publish DESC');
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
            $order = array('publish DESC', 'id DESC');
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
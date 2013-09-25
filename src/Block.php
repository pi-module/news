<?php
/**
 * News block class
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

namespace Module\News;

use Pi;

class Block
{
    public static function item($options = array(), $module = null)
    {
        // Set options
        $block = array();
        $block = array_merge($block, $options);
        // Check topic permission
        if (!in_array(0, $block['topicid'])) {
            $whereLink['topic'] = $block['topicid'];
        }
        // Set model and get information
        $whereLink['status'] = 1;
        //$order = array($block['order'],'id DESC');
        $order = array('publish DESC', 'id DESC');
        $limit = intval($block['number']);
        // Get info from link table
        $select = Pi::model('link', $module)->select()->where($whereLink)->columns(array('story' => new \Zend\Db\Sql\Predicate\Expression('DISTINCT story')))->order($order)->offset($offset)->limit($limit);
        $rowset = Pi::model('link', $module)->selectWith($select)->toArray();
        // Make list
        foreach ($rowset as $id) {
            $storyId[] = $id['story'];
        }
        // Set info
        $columnStory = array('id', 'title', 'slug', 'topic', 'short', 'important', 'publish', 'image', 'path', 'hits');
        $whereStory = array('id' => $storyId);
        // Get list of story
        $select = Pi::model('story', $module)->select()->columns($columnStory)->where($whereStory)->order($order);
        $rowset = Pi::model('story', $module)->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $story[$row->id] = $row->toArray();
            $story[$row->id]['publish'] = date('Y/m/d H:i:s', $story[$row->id]['publish']);
            if ($story[$row->id]['image']) {
                $story[$row->id]['originalurl'] = Pi::url('upload/' . $module . '/original/' . $story[$row->id]['path'] . '/' . $story[$row->id]['image']);
                $story[$row->id]['largeurl'] = Pi::url('upload/' . $module . '/large/' . $story[$row->id]['path'] . '/' . $story[$row->id]['image']);
                $story[$row->id]['mediumurl'] = Pi::url('upload/' . $module . '/medium/' . $story[$row->id]['path'] . '/' . $story[$row->id]['image']);
                $story[$row->id]['thumburl'] = Pi::url('upload/' . $module . '/thumb/' . $story[$row->id]['path'] . '/' . $story[$row->id]['image']);
            }
        }
        // Set block array
        $block['resources'] = $story;
        return $block;
    }

    public static function spotlight($options = array(), $module = null)
    {
        /*
           // Set options
           $block = array();
           $block = array_merge($block, $options);

           // Set model and get information
            $story_model = \Pi::service('module')->model('story', 'news');
            $story_list = $story_model->Story_GetBlock($block['topicid'], $block['order'] ,$block['number']);

          // Process information
          $spotlight = \App\News\Module::Spotlight($story_list , $block['subspotlight']);
           foreach ($story_list as $row)
           {
               $story[$row->id] = $row->toArray();
              $story[$row->id]['mediumurl'] = \Pi::url('upload/news/medium/') . $row->image;
              $story[$row->id]['thumburl'] = \Pi::url('upload/news/thumb/') . $row->image;
              $story[$row->id]['url'] = \App\News\Module::StoryUrl($row->slug);
             $story[$row->id]['spotlight'] = $spotlight[$row->id];
           }

          // Make Spotlight
          $story = \App\News\Module::Spotlight($story);

          // Set block array
          $block['resources'] = $story;
           return $block;
           */
    }

    public static function topic($options = array(), $module = null)
    {
        // Set options
        $block = array();
        $block = array_merge($block, $options);
        // Set model and get information
        $columns = array('id', 'title', 'slug');
        $where = array('status' => 1);
        if (!in_array(0, $block['topicid'])) {
            $where['id'] = $block['topicid'];
        }
        $order = array('create DESC', 'id DESC');
        $select = Pi::model('topic', $module)->select()->columns($columns)->where($where)->order($order);
        $rowset = Pi::model('topic', $module)->selectWith($select);
        // Process information
        foreach ($rowset as $row) {
            $topic[$row->id] = $row->toArray();
        }
        // Set block array
        $block['resources'] = $topic;
        return $block;
    }
}
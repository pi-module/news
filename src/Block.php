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
        $columns = array('story' => new \Zend\Db\Sql\Predicate\Expression('DISTINCT story'));
        $order = array('time_publish DESC', 'id DESC');
        $limit = intval($block['number']);
        // Get info from link table
        $select = Pi::model('link', $module)->select()->where($whereLink)->columns($columns)->order($order)->limit($limit);
        $rowset = Pi::model('link', $module)->selectWith($select)->toArray();
        // Make list
        foreach ($rowset as $id) {
            $storyId[] = $id['story'];
        }
        // Set info
        $columnStory = array('id', 'title', 'slug', 'topic', 'short', 'important', 'time_publish', 'image', 'path', 'hits');
        $whereStory = array('id' => $storyId);
        // Get list of story
        $select = Pi::model('story', $module)->select()->columns($columnStory)->where($whereStory)->order($order);
        $rowset = Pi::model('story', $module)->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $story[$row->id] = $row->toArray();
            $story[$row->id]['time_publish'] = date('Y/m/d H:i:s', $story[$row->id]['time_publish']);
            if ($story[$row->id]['image']) {
                $story[$row->id]['originalurl'] = Pi::url(sprintf('upload/%s/image/original/%s/%s', $module, $story[$row->id]['path'], $story[$row->id]['image']));
                $story[$row->id]['largeurl'] = Pi::url(sprintf('upload/%s/image/large/%s/%s', $module, $story[$row->id]['path'], $story[$row->id]['image']));
                $story[$row->id]['mediumurl'] = Pi::url(sprintf('upload/%s/image/medium/%s/%s', $module, $story[$row->id]['path'], $story[$row->id]['image']));
                $story[$row->id]['thumburl'] = Pi::url(sprintf('upload/%s/image/thumb/%s/%s', $module, $story[$row->id]['path'], $story[$row->id]['image']));
            }
        }
        // Set block array
        $block['resources'] = $story;
        return $block;
    }

    public static function spotlight($options = array(), $module = null)
    {
      // Set options
      $block = array();
      $block = array_merge($block, $options);
      return $block;
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
        $order = array('time_create DESC', 'id DESC');
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
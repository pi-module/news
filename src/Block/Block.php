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
namespace Module\News\Block;

use Pi;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Json\Json;

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
        $columns = array('story' => new Expression('DISTINCT story'));
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
        $whereStory = array('status' => 1, 'id' => $storyId);
        // Get topic list
        $topicList = Pi::api('news', 'topic')->topicList();
        // Get list of story
        $select = Pi::model('story', $module)->select()->where($whereStory)->order($order);
        $rowset = Pi::model('story', $module)->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $story[$row->id] = Pi::api('news', 'story')->canonizeStory($row, $topicList);
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
            $topic[$row->id]['topicUrl'] = Pi::service('url')->assemble('news', array(
                'module'        => $module,
                'controller'    => 'topic',
                'slug'          => $topic[$row->id]['slug'],
            ));
        }
        // Set block array
        $block['resources'] = $topic;
        return $block;
    }
}
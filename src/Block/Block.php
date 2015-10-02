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
        $whereLink = array();
        // Check topic permission
        if (isset($block['topicid']) && !empty($block['topicid']) && !in_array(0, $block['topicid'])) {
            $whereLink['topic'] = $block['topicid'];
        }
        // Set model and get information
        $whereLink['status'] = 1;
        $columns = array('story' => new Expression('DISTINCT story'));
        $limit = intval($block['number']);
        // Set order
        switch ($block['order']) {
            case 'random':
                $order = array(new Expression('RAND()'));
                break;

            case 'publishASC':
                $order = array('time_publish ASC', 'id ASC');;
                break;

            case 'publishDESC':
            default:
                $order = array('time_publish DESC', 'id DESC');;
                break;
        }
        //
        $select = Pi::model('link', $module)->select()->where($whereLink);
        // skip show last X story
        if (!$block['notShowSpotlight']) {
            $ids = Pi::registry('spotlightStoryId', 'news')->read();
            foreach ($ids as $id) {
                $select->where(array('story != ?' => $id));
            }
        }
        // Get info from link table
        $select->columns($columns)->order($order)->limit($limit);
        $rowset = Pi::model('link', $module)->selectWith($select)->toArray();
        // Make list
        foreach ($rowset as $id) {
            $storyId[] = $id['story'];
        }
        // Set info
        $whereStory = array('status' => 1, 'id' => $storyId);
        // Get topic list
        $topicList = Pi::registry('topicList', 'news')->read();
        // Get author list
        $authorList = Pi::registry('authorList', 'news')->read();
        // Get list of story
        $select = Pi::model('story', $module)->select()->where($whereStory)->order($order);
        $rowset = Pi::model('story', $module)->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $story[$row->id] = Pi::api('story', 'news')->canonizeStory($row, $topicList, $authorList);
            if (!empty($block['textlimit']) && $block['textlimit'] > 0) {
                $story[$row->id]['text_summary'] = mb_substr(strip_tags($story[$row->id]['text_summary']), 0, $block['textlimit'], 'utf-8' ) . "...";
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
        // Set model and get information
        $whereSpotlight['status'] = array('status' => 1, 'time_publish < ?' => time(), 'time_expire > ?' => time());
        $columns = array('story' => new Expression('DISTINCT story'));
        $limit = intval($block['number']);
        // Set order
        switch ($block['order']) {
            case 'random':
                $order = array(new Expression('RAND()'));
                break;

            case 'publishASC':
                $order = array('time_publish ASC', 'id ASC');;
                break;

            case 'publishDESC':
            default:
                $order = array('time_publish DESC', 'id DESC');;
                break;
        }
        // Get info from link table
        $select = Pi::model('spotlight', $module)->select()->where($whereSpotlight)->columns($columns)->order($order)->limit($limit);
        $rowset = Pi::model('spotlight', $module)->selectWith($select)->toArray();
        // Make list
        foreach ($rowset as $id) {
            $storyId[] = $id['story'];
        }
        // Set info
        $whereStory = array('status' => 1, 'id' => $storyId);
        // Get topic list
        $topicList = Pi::registry('topicList', 'news')->read();
        // Get author list
        $authorList = Pi::registry('authorList', 'news')->read();
        // Get list of story
        $select = Pi::model('story', $module)->select()->where($whereStory)->order($order);
        $rowset = Pi::model('story', $module)->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $story[$row->id] = Pi::api('story', 'news')->canonizeStory($row, $topicList, $authorList);
            if (!empty($block['textlimit']) && $block['textlimit'] > 0) {
                $story[$row->id]['text_summary'] = mb_substr(strip_tags($story[$row->id]['text_summary']), 0, $block['textlimit'], 'utf-8' ) . "...";
            }
        }
        // Set block array
        $block['resources'] = $story;
        return $block;
    }

    public static function topic($options = array(), $module = null)
    {
        // Set options
        $block = array();
        $block = array_merge($block, $options);
        // Set model and get information
        $where = array('status' => 1);
        if (isset($block['topicid']) && !empty($block['topicid']) && !in_array(0, $block['topicid'])) {
            $where['id'] = $block['topicid'];
        }
        // Set order
        switch ($block['order']) {
            case 'titleASC':
                $order = array('title ASC', 'id ASC');
                break;

            case 'titleDESC':
                $order = array('title DESC', 'id DESC');
                break;

            case 'createASC':
                $order = array('time_create ASC', 'id ASC');
                break;

            case 'createDESC':
            default:
                $order = array('time_create DESC', 'id DESC');
                break;
        }
        // Get info from topic table
        $select = Pi::model('topic', $module)->select()->where($where)->order($order);
        $rowset = Pi::model('topic', $module)->selectWith($select);
        // Process information
        foreach ($rowset as $row) {
            $topic[$row->id] = Pi::api('topic', 'news')->canonizeTopic($row);
        }
        // Set block array
        $block['resources'] = $topic;
        return $block;
    }

    public static function gallery($options = array(), $module = null)
    {
        // Set options
        $block = array();
        $block = array_merge($block, $options);
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Set info
        $where = array('type' => 'image');
        $order = array('time_create DESC', 'id DESC');
        $limit = intval($block['number']);
        // Select
        $select = Pi::model('attach', $module)->select()->where($where)->order($order)->limit($limit);
        $rowset = Pi::model('attach', $module)->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $list[$row->id] = $row->toArray();
            // Set image links
            $list[$row->id]['largeUrl'] = Pi::url(
                sprintf('upload/%s/large/%s/%s',
                    $config['image_path'],
                    $list[$row->id]['path'],
                    $list[$row->id]['file']
                ));
            $list[$row->id]['mediumUrl'] = Pi::url(
                sprintf('upload/%s/medium/%s/%s',
                    $config['image_path'],
                    $list[$row->id]['path'],
                    $list[$row->id]['file']
                ));
            $list[$row->id]['thumbUrl'] = Pi::url(
                sprintf('upload/%s/thumb/%s/%s',
                    $config['image_path'],
                    $list[$row->id]['path'],
                    $list[$row->id]['file']
                ));
            // Set mediaUrl
            $list[$row->id]['mediaUrl'] = Pi::service('url')->assemble('news', array(
                'module' => $module,
                'controller' => 'media',
                'action' => 'explorer',
                'id' => $list[$row->id]['id'],
            ));
        }
        // Set block array
        $block['resources'] = $list;
        return $block;
    }

    public static function microblog($options = array(), $module = null)
    {
        // Set options
        $block = array();
        $block = array_merge($block, $options);
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Set info
        $microblog = array();
        $where = array('status' => 1);
        $order = array('time_create DESC', 'id DESC');
        $limit = intval($block['number']);
        // Check uid
        if (intval($block['uid']) > 0) {
            $where['uid'] = intval($block['uid']);
        }
        // Select
        $select = Pi::model('microblog', $module)->select()->where($where)->order($order)->limit($limit);
        $rowset = Pi::model('microblog', $module)->selectWith($select);
        // Process information
        foreach ($rowset as $row) {
            $microblog[$row->id] = Pi::api('microblog', 'news')->canonizeMicroblog($row);
            $microblog[$row->id]['user']['avatar'] = Pi::service('user')->avatar($microblog[$row->id]['uid'], 'medume', array(
                'alt' => $microblog[$row->id]['user']['name'],
                'class' => 'img-circle',
            ));
        }
        // Set block array
        $block['resources'] = $microblog;
        return $block;
    }

    public static function media($options = array(), $module = null)
    {
        // Set options
        $block = array();
        $block = array_merge($block, $options);
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Set info
        $story = array();
        $whereLink = array();
        // Check topic permission
        if (isset($block['topicid']) && !empty($block['topicid']) && !in_array(0, $block['topicid'])) {
            $whereLink['topic'] = $block['topicid'];
        }
        // Set model and get information
        $whereLink['status'] = 1;
        $columns = array('story' => new Expression('DISTINCT story'));
        $order = array('time_publish DESC', 'id DESC');
        // select
        $select = Pi::model('link', $module)->select()->where($whereLink)->columns($columns)->order($order)->limit(1);
        $rowset = Pi::model('link', $module)->selectWith($select)->toArray();
        // Make list
        foreach ($rowset as $id) {
            $storyId[] = $id['story'];
        }
        // Set info
        $whereStory = array('status' => 1, 'id' => $storyId);
        // Get topic list
        $topicList = Pi::registry('topicList', 'news')->read();
        // Get author list
        $authorList = Pi::registry('authorList', 'news')->read();
        // Get list of story
        $select = Pi::model('story', $module)->select()->where($whereStory)->order($order);
        $rowset = Pi::model('story', $module)->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $story[$row->id] = Pi::api('story', 'news')->canonizeStory($row, $topicList, $authorList);
            $story[$row->id]['media_attach'] = Pi::api('story', 'news')->AttachList($row->id);
            $story[$row->id]['media_attribute'] = Pi::api('attribute', 'news')->Story($row->id, $row->topic_main);
            if (!empty($block['textlimit']) && $block['textlimit'] > 0) {
                $story[$row->id]['text_summary'] = mb_substr(strip_tags($story[$row->id]['text_summary']), 0, $block['textlimit'], 'utf-8' ) . "...";
            }
        }
        // Set block array
        $block['resources'] = $story;
        return $block;
    }
}
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
        $whereLink['type'] = array(
            'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download'
        );
        $columns = array('story' => new Expression('DISTINCT story'));
        $limit = intval($block['number']);
        // Set order
        switch ($block['order']) {
            case 'random':
                $order = array(new Expression('RAND()'));
                break;

            case 'updateASC':
                $order = array('time_update ASC', 'id ASC');;
                break;

            case 'updateDESC':
                $order = array('time_update DESC', 'id DESC');;
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

            case 'updateASC':
                $order = array('time_update ASC', 'id ASC');;
                break;

            case 'updateDESC':
                $order = array('time_update DESC', 'id DESC');;
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
        //
        if ($block['tree']) {
            $topic = Pi::api('topic', 'news')->getTreeFull($topic);
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

        $order = array('time_create DESC', 'id DESC');
        $limit = intval($block['number']);
        // Select
        $select = Pi::model('story', $module)->select()->order($order)->limit($limit);
        $rowset = Pi::model('story', $module)->selectWith($select);
        // Make list
        foreach ($rowset as $row) {

            $galleryImagesLarge = Pi::api('doc','media')->getGalleryLinkData($row['additional_images'], 1024, 768);
            $galleryImagesMedium = Pi::api('doc','media')->getGalleryLinkData($row['additional_images'], 800, 600);
            $galleryImagesThumb = Pi::api('doc','media')->getGalleryLinkData($row['additional_images'], 320, 240);

            foreach($galleryImagesLarge as $key => $galleryImageLarge){
                $list[$row->id . '-' . $key] = $galleryImageLarge;
                // Set image links
                $list[$row->id . '-' . $key]['largeUrl'] = $galleryImageLarge['resized_url'];
                $list[$row->id . '-' . $key]['mediumUrl'] = $galleryImagesMedium[$key]['resized_url'];
                $list[$row->id . '-' . $key]['thumbUrl'] = $galleryImagesThumb[$key]['resized_url'];
            }

            // Set mediaUrl
//            $list[$row->id]['mediaUrl'] = Pi::service('url')->assemble('news', array(
//                'module' => $module,
//                'controller' => 'media',
//                'action' => 'explorer',
//                'id' => $list[$row->id]['id'],
//            ));

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
        $block['microblog_active'] = $config['microblog_active'];
        // Set info
        $microblog = array();
        $where = array('status' => 1);
        $order = array('time_create DESC', 'id DESC');
        $limit = intval($block['number']);
        // Check uid and topic
        if (intval($block['uid']) > 0) {
            $where['uid'] = intval($block['uid']);
            $where['topic'] = 0;
        } elseif (isset($block['topicid']) && !empty($block['topicid'])) {
            $where['topic'] = 1;
        }
        // Select
        $select = Pi::model('microblog', $module)->select()->where($where)->order($order)->limit($limit);
        $rowset = Pi::model('microblog', $module)->selectWith($select);
        // Process information
        foreach ($rowset as $row) {
            $microblog[$row->id] = Pi::api('microblog', 'news')->canonizeMicroblog($row);
            $microblog[$row->id]['user']['avatar'] = Pi::service('user')->avatar($microblog[$row->id]['uid'], 'medium', array(
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
        // Set info
        $story = array();
        $whereLink = array();
        // Check topic permission
        if (isset($block['topicid']) && !empty($block['topicid']) && !in_array(0, $block['topicid'])) {
            $whereLink['topic'] = $block['topicid'];
        }
        // Set model and get information
        $whereLink['status'] = 1;
        $whereLink['type'] = array(
            'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download'
        );
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
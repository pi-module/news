<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\News\Block;

use Pi;
use Laminas\Db\Sql\Predicate\Expression;

class Block
{
    public static function item($options = [], $module = null)
    {
        // Set options
        $block = [];
        $block = array_merge($block, $options);

        // Set model and get information
        $whereLink = [
            'status' => 1,
            'type'   => ['text', 'article', 'magazine', 'image', 'gallery', 'media', 'download'],
        ];
        $columns   = ['story' => new Expression('DISTINCT story')];
        $limit     = intval($block['number']);
        $storyId   = [];
        $storyList = [];

        // Check topic permission
        if (isset($block['topicid']) && !empty($block['topicid']) && !in_array(0, $block['topicid'])) {
            $whereLink['topic'] = $block['topicid'];
        }

        // Set day limit
        if (intval($block['day_limit']) > 0) {
            $whereLink['time_publish > ?'] = time() - (86400 * $block['day_limit']);
        }

        // Set order
        switch ($block['order']) {
            case 'random':
                $order = [new Expression('RAND()')];
                break;

            case 'updateASC':
                $order = ['time_update ASC', 'id ASC'];;
                break;

            case 'updateDESC':
                $order = ['time_update DESC', 'id DESC'];;
                break;

            case 'hitsDESC':
                $order = ['hits DESC', 'id DESC'];;
                break;

            case 'publishASC':
                $order = ['time_publish ASC', 'id ASC'];;
                break;

            case 'publishDESC':
            default:
                $order = ['time_publish DESC', 'id DESC'];;
                break;
        }

        // Select
        $select = Pi::model('link', $module)->select()->where($whereLink);

        // Select : skip show last X story
        if (!$block['notShowSpotlight']) {
            $ids = Pi::registry('spotlightStoryId', 'news')->read();
            foreach ($ids as $id) {
                $select->where(['story != ?' => $id]);
            }
        }

        // Select : Get info from link table
        $select->columns($columns)->order($order)->limit($limit);
        $rowset = Pi::model('link', $module)->selectWith($select);

        // Make list
        if (!empty($rowset)) {
            foreach ($rowset as $row) {
                $storyId[] = $row->story;
            }
        }

        // Check story not empty
        if (!empty($storyId)) {

            // Set info
            $whereStory = [
                'status' => 1,
                'id'     => $storyId,
            ];

            // Get topic list
            $topicList = Pi::registry('topicList', 'news')->read();

            // Get author list
            $authorList = Pi::registry('authorList', 'news')->read();

            // Get list of story
            $select = Pi::model('story', $module)->select()->where($whereStory)->order($order);
            $rowset = Pi::model('story', $module)->selectWith($select);

            // Make list
            foreach ($rowset as $row) {
                $storyList[$row->id] = Pi::api('story', 'news')->canonizeStory($row, $topicList, $authorList);
                if (!empty($block['textlimit']) && $block['textlimit'] > 0) {
                    $storyList[$row->id]['text_summary'] = mb_substr(strip_tags($storyList[$row->id]['text_summary']), 0, $block['textlimit'], 'utf-8') . "...";
                }
            }
        }

        // Set block array
        $block['resources'] = $storyList;
        return $block;
    }

    public static function spotlight($options = [], $module = null)
    {
        // Set options
        $block = [];
        $block = array_merge($block, $options);

        // Set model and get information
        $whereSpotlight = [
            'status' => [
                'status'           => 1,
                'time_publish < ?' => time(),
                'time_expire > ?'  => time(),
            ],
        ];
        $columns        = ['story' => new Expression('DISTINCT story')];
        $limit          = intval($block['number']);
        $storyId        = [];
        $storyList      = [];

        // Set order
        switch ($block['order']) {
            case 'random':
                $order = [new Expression('RAND()')];
                break;

            case 'updateASC':
                $order = ['time_update ASC', 'id ASC'];;
                break;

            case 'updateDESC':
                $order = ['time_update DESC', 'id DESC'];;
                break;

            case 'publishASC':
                $order = ['time_publish ASC', 'id ASC'];;
                break;

            case 'publishDESC':
            default:
                $order = ['time_publish DESC', 'id DESC'];;
                break;
        }

        // Get info from link table
        $select = Pi::model('spotlight', $module)->select()->where($whereSpotlight)->columns($columns)->order($order)->limit($limit);
        $rowset = Pi::model('spotlight', $module)->selectWith($select);

        // Make list
        if (!empty($rowset)) {
            foreach ($rowset as $row) {
                $storyId[] = $row->story;
            }
        }

        // Check story not empty
        if (!empty($storyId)) {

            // Set info
            $whereStory = [
                'status' => 1,
                'id'     => $storyId,
            ];

            // Get topic list
            $topicList = Pi::registry('topicList', 'news')->read();

            // Get author list
            $authorList = Pi::registry('authorList', 'news')->read();

            // Get list of story
            $select = Pi::model('story', $module)->select()->where($whereStory)->order($order);
            $rowset = Pi::model('story', $module)->selectWith($select);

            // Make list
            foreach ($rowset as $row) {
                $storyList[$row->id] = Pi::api('story', 'news')->canonizeStory($row, $topicList, $authorList);
                if (!empty($block['textlimit']) && $block['textlimit'] > 0) {
                    $storyList[$row->id]['text_summary'] = mb_substr(strip_tags($storyList[$row->id]['text_summary']), 0, $block['textlimit'], 'utf-8') . "...";
                }
            }
        }

        // Set block array
        $block['resources'] = $storyList;
        return $block;
    }

    public static function topic($options = [], $module = null)
    {
        // Set options
        $block     = [];
        $block     = array_merge($block, $options);
        $topicList = [];

        // Set model and get information
        $where = [
            'status' => 1,
        ];
        if (isset($block['topicid']) && !empty($block['topicid']) && !in_array(0, $block['topicid'])) {
            $where['id'] = $block['topicid'];
        }

        // Set order
        switch ($block['order']) {
            case 'titleASC':
                $order = ['title ASC', 'id ASC'];
                break;

            case 'titleDESC':
                $order = ['title DESC', 'id DESC'];
                break;

            case 'createASC':
                $order = ['time_create ASC', 'id ASC'];
                break;

            case 'createDESC':
            default:
                $order = ['time_create DESC', 'id DESC'];
                break;
        }

        // Get info from topic table
        $select = Pi::model('topic', $module)->select()->where($where)->order($order);
        $rowset = Pi::model('topic', $module)->selectWith($select);

        // Process information
        foreach ($rowset as $row) {
            $topicList[$row->id] = Pi::api('topic', 'news')->canonizeTopic($row);
        }

        // Make tree
        if ($block['tree'] && !empty($topicList)) {
            $topicList = Pi::api('topic', 'news')->getTreeFull($topicList);
        }

        // Set block array
        $block['resources'] = $topicList;
        return $block;
    }

    public static function gallery($options = [], $module = null)
    {
        // Set options
        $block = [];
        $block = array_merge($block, $options);

        $where = ['module' => 'news', 'object_name' => 'story'];
        $limit = intval($block['number']);

        // Select
        $select = Pi::model('link', 'media')->select()->where($where)->limit($limit);
        $rowset = Pi::model('link', 'media')->selectWith($select);

        $list = [];
        // Make list
        foreach ($rowset as $row) {
            $media = Pi::model('doc', 'media')->find($row->id);

            $imageLarge  = (string)Pi::api('doc', 'media')->getSingleLinkUrl($row->id)->setConfigModule('news')->thumb('large');
            $imageMedium = (string)Pi::api('doc', 'media')->getSingleLinkUrl($row->id)->setConfigModule('news')->thumb('medium');
            $imageThumb  = (string)Pi::api('doc', 'media')->getSingleLinkUrl($row->id)->setConfigModule('news')->thumb('thumbnail');

            $data = [
                'title'     => $media->title,
                'largeUrl'  => $imageLarge,
                'mediumUrl' => $imageMedium,
                'thumbUrl'  => $imageThumb,
            ];

            $list[] = $data;
        }

        // Set block array
        $block['resources'] = $list;
        return $block;
    }

    public static function microblog($options = [], $module = null)
    {
        // Set options
        $block = [];
        $block = array_merge($block, $options);

        // Get config
        $config                    = Pi::service('registry')->config->read($module);
        $block['microblog_active'] = $config['microblog_active'];

        // Set info
        $microblog = [];
        $where     = ['status' => 1];
        $order     = ['time_create DESC', 'id DESC'];
        $limit     = intval($block['number']);

        // Check uid and topic
        if (intval($block['uid']) > 0) {
            $where['uid']   = intval($block['uid']);
            $where['topic'] = 0;
        } elseif (isset($block['topicid']) && !empty($block['topicid'])) {
            $where['topic'] = 1;
        }

        // Select
        $select = Pi::model('microblog', $module)->select()->where($where)->order($order)->limit($limit);
        $rowset = Pi::model('microblog', $module)->selectWith($select);

        // Process information
        foreach ($rowset as $row) {
            $microblog[$row->id]                   = Pi::api('microblog', 'news')->canonizeMicroblog($row);
            $microblog[$row->id]['user']['avatar'] = Pi::service('user')->avatar(
                $microblog[$row->id]['uid'],
                'medium',
                [
                    'alt'   => $microblog[$row->id]['user']['name'],
                    'class' => 'rounded-circle',
                ]
            );
        }

        // Set block array
        $block['resources'] = $microblog;
        return $block;
    }

    public static function media($options = [], $module = null)
    {
        // Set options
        $block = [];
        $block = array_merge($block, $options);

        // Set info
        $story     = [];
        $whereLink = [];

        // Check topic permission
        if (isset($block['topicid']) && !empty($block['topicid']) && !in_array(0, $block['topicid'])) {
            $whereLink['topic'] = $block['topicid'];
        }

        // Set model and get information
        $whereLink['status'] = 1;
        $whereLink['type']   = [
            'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download',
        ];
        $columns             = ['story' => new Expression('DISTINCT story')];
        $order               = ['time_publish DESC', 'id DESC'];

        // select
        $select = Pi::model('link', $module)->select()->where($whereLink)->columns($columns)->order($order)->limit(1);
        $rowset = Pi::model('link', $module)->selectWith($select);

        // Make list
        if (!empty($rowset)) {
            foreach ($rowset as $row) {
                $storyId[] = $row->story;
            }
        }

        // Set info
        $whereStory = [
            'status' => 1,
            'id'     => $storyId,
        ];

        // Check story not empty
        if (!empty($storyId)) {
            // Get topic list
            $topicList = Pi::registry('topicList', 'news')->read();

            // Get author list
            $authorList = Pi::registry('authorList', 'news')->read();
            // Get list of story
            $select = Pi::model('story', $module)->select()->where($whereStory)->order($order);
            $rowset = Pi::model('story', $module)->selectWith($select);
            // Make list
            foreach ($rowset as $row) {
                $story[$row->id]                    = Pi::api('story', 'news')->canonizeStory($row, $topicList, $authorList);
                $story[$row->id]['media_attach']    = Pi::api('story', 'news')->AttachList($row->id);
                $story[$row->id]['media_attribute'] = Pi::api('attribute', 'news')->Story($row->id, $row->topic_main);
                if (!empty($block['textlimit']) && $block['textlimit'] > 0) {
                    $story[$row->id]['text_summary'] = mb_substr(strip_tags($story[$row->id]['text_summary']), 0, $block['textlimit'], 'utf-8') . "...";
                }
            }
        }

        // Set block array
        $block['resources'] = $story;
        return $block;
    }
}

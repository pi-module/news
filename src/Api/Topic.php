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

namespace Module\News\Api;

use Pi;
use Pi\Application\Api\AbstractApi;
use Laminas\Db\Sql\Predicate\Expression;

/*
 * Pi::api('topic', 'news')->getTopic($parameter, $field;
 * Pi::api('topic', 'news')->getTopicFull($parameter, $field);
 * Pi::api('topic', 'news')->getTopicList();
 * Pi::api('topic', 'news')->getTopicFullList($options);
 * Pi::api('topic', 'news')->canonizeTopic($topic);
 * Pi::api('topic', 'news')->setLink($story, $topics, $publish $update, $status, $uid, $type, $module, $controller);
 * Pi::api('topic', 'news')->topicCount();
 * Pi::api('topic', 'news')->getTreeFull($list);
 * Pi::api('topic', 'news')->sitemap();
 * Pi::api('topic', 'news')->regenerateImage();
 */

class Topic extends AbstractApi
{
    public function getTopic($parameter, $field = 'id')
    {
        // Get topic
        $topic             = Pi::model('topic', 'news')->find($parameter, $field);
        $topic             = $topic->toArray();
        $topic['topicUrl'] = Pi::url(
            Pi::service('url')->assemble(
                'news',
                [
                    'module'     => 'news',
                    'controller' => 'topic',
                    'slug'       => $topic['slug'],
                ]
            )
        );
        return $topic;
    }

    public function getTopicFull($parameter, $field = 'id')
    {
        // Get topic
        $topic = Pi::model('topic', 'news')->find($parameter, $field);
        $topic = $this->canonizeTopic($topic);
        return $topic;
    }

    public function getTopicList($type = 'general')
    {
        $topicList = [];
        $columns   = ['id', 'title'];
        $where     = ['status' => 1, 'type' => $type];
        $order     = ['title ASC', 'id ASC'];
        $select    = Pi::model('topic', 'news')->select()->columns($columns)->where($where)->order($order);
        $rowset    = Pi::model('topic', 'news')->selectWith($select);
        foreach ($rowset as $row) {
            $topicList[$row->id]['id']    = $row->id;
            $topicList[$row->id]['title'] = $row->title;
        }
        return $topicList;
    }

    public function getTopicFullList($options)
    {
        $topicList = [];
        $where     = ['status' => 1, 'type' => $options['type']];
        $order     = ['display_order ASC', 'title ASC', 'id ASC'];
        $select    = Pi::model('topic', 'news')->select()->where($where)->order($order);
        $rowset    = Pi::model('topic', 'news')->selectWith($select);
        foreach ($rowset as $row) {
            $topicList[$row->id] = $this->canonizeTopic($row);
        }
        return $topicList;
    }

    /**
     * Set page setting from topic or module config
     */
    public function canonizeTopic($topic = null)
    {
        // Get config
        $config = Pi::service('registry')->config->read('news');
        // Set topic information
        if (isset($topic) && !empty($topic) && is_object($topic)) {
            $topic = $topic->toArray();
            // Get setting
            $setting = json_decode($topic['setting'], true);
            $topic   = array_merge($topic, $setting);
            // Set topic url
            $topic['topicUrl'] = Pi::url(
                Pi::service('url')->assemble(
                    'news',
                    [
                        'module'     => 'news',
                        'controller' => 'topic',
                        'slug'       => $topic['slug'],
                    ]
                )
            );
            // Set image url
            if ($topic['image']) {
                // Set image original url
                $topic['originalUrl'] = Pi::url(
                    sprintf(
                        'upload/%s/original/%s/%s',
                        $config['image_path'],
                        $topic['path'],
                        $topic['image']
                    )
                );
                // Set image large url
                $topic['largeUrl'] = Pi::url(
                    sprintf(
                        'upload/%s/large/%s/%s',
                        $config['image_path'],
                        $topic['path'],
                        $topic['image']
                    )
                );
                // Set image medium url
                $topic['mediumUrl'] = Pi::url(
                    sprintf(
                        'upload/%s/medium/%s/%s',
                        $config['image_path'],
                        $topic['path'],
                        $topic['image']
                    )
                );
                // Set image thumb url
                $topic['thumbUrl'] = Pi::url(
                    sprintf(
                        'upload/%s/thumb/%s/%s',
                        $config['image_path'],
                        $topic['path'],
                        $topic['image']
                    )
                );
            }
            // Show sub id
            if ($topic['show_subid']) {
                $topic['ids'] = $this->topicSubId($topic['id']);
            } else {
                $topic['ids'] = $topic['id'];
            }
            // Check attach
            if (isset($topic['attach']) && $topic['attach']) {
                // Set info
                $file  = [];
                $where = ['item_id' => $topic['id'], 'item_table' => 'topic', 'status' => 1];
                $order = ['time_create DESC', 'id DESC'];
                // Get all attach files
                $select = Pi::model('attach', 'news')->select()->where($where)->order($order);
                $rowset = Pi::model('attach', 'news')->selectWith($select);
                // Make list
                foreach ($rowset as $row) {
                    $file[$row->type][$row->id]                = $row->toArray();
                    $file[$row->type][$row->id]['time_create'] = _date($file[$row->type][$row->id]['time_create']);
                    // Set download url
                    $file[$row->type][$row->id]['downloadUrl'] = Pi::url(
                        Pi::service('url')->assemble(
                            'news',
                            [
                                'module'     => 'news',
                                'controller' => 'media',
                                'action'     => 'download',
                                'id'         => $row->id,
                            ]
                        )
                    );
                }
                $topic['attachList'] = $file;
            }
        }
        // Set topic config
        if (!isset($topic) || $topic['show_config'] == 'module') {
            $topic['style']           = $config['style'];
            $topic['show_perpage']    = $config['show_perpage'];
            $topic['show_columns']    = $config['show_columns'];
            $topic['show_order_link'] = $config['show_order_link'];
            $topic['show_topic']      = $config['show_topic'];
            $topic['show_topicinfo']  = $config['show_topicinfo'];
            $topic['show_date']       = $config['show_date'];
            $topic['show_hits']       = $config['show_hits'];
            $topic['show_tag']        = $config['show_tag'];
        } else {
            if (!isset($topic['show_order_link'])) {
                $topic['show_order_link'] = $config['show_order_link'];
            }
            if (!isset($topic['show_topic'])) {
                $topic['show_topic'] = $config['show_topic'];
            }
            if (!isset($topic['show_topicinfo'])) {
                $topic['show_topicinfo'] = $config['show_topicinfo'];
            }
            if (!isset($topic['show_date'])) {
                $topic['show_date'] = $config['show_date'];
            }
            if (!isset($topic['show_hits'])) {
                $topic['show_hits'] = $config['show_hits'];
            }
            if (!isset($topic['show_tag'])) {
                $topic['show_tag'] = $config['show_tag'];
            }
        }
        // Set perpage
        if (empty($topic['show_perpage'])) {
            $topic['show_perpage'] = $config['show_perpage'];
        }
        // Set columns
        if (empty($topic['show_columns'])) {
            $topic['show_columns'] = $config['show_columns'];
        }
        /* // title
        if (empty($topic['seo_title'])) {
            $topic['seo_title'] = $config['text_title'];
        }
        // seo_description
        if (empty($topic['seo_description'])) {
            $topic['seo_description'] = $config['text_description'];
        }
        // seo_keywords
        if (empty($topic['seo_keywords'])) {
            $topic['seo_keywords'] = $config['text_keywords'];
        } */
        // Template
        $topic['template'] = $this->Template($topic['style']);
        // column class
        $topic['column_class'] = $this->Column($topic['show_columns']);
        // Return topic setting
        return $topic;
    }

    /**
     * Set page template
     * By shwotype option
     */
    public function Column($columns)
    {
        switch ($columns) {
            default:
            case 1:
                $class = '';
                break;

            case 2:
                $class = 'col-md-6';
                break;

            case 3:
                $class = 'col-md-4';
                break;

            case 4:
                $class = 'col-md-3';
                break;
        }
        return $class;
    }

    /**
     * Set page template
     * By shwotype option
     */
    public function Template($style)
    {
        switch ($style) {
            default:
            case 'news':
                $template = 'index-news';
                break;

            case 'list':
                $template = 'index-list';
                break;

            case 'table':
                $template = 'index-table';
                break;

            case 'media':
                $template = 'index-media';
                break;

            case 'spotlight':
                $template = 'index-spotlight';
                break;

            case 'topic':
                $template = 'index-topic';
                break;

        }
        return $template;
    }

    public function setLink(
        $story,
        $topics,
        $publish,
        $update,
        $status,
        $uid,
        $type = 'text',
        $module = 'news',
        $controller = 'topic'
    ) {
        //Remove
        Pi::model('link', 'news')->delete(['story' => $story]);
        // Add
        $newTopics = json_decode($topics, true);
        foreach ($newTopics as $topic) {
            // Set array
            $values['topic']        = $topic;
            $values['story']        = $story;
            $values['time_publish'] = $publish;
            $values['time_update']  = $update;
            $values['status']       = $status;
            $values['uid']          = $uid;
            $values['type']         = $type;
            $values['module']       = $module;
            $values['controller']   = $controller;
            // Save
            $row = Pi::model('link', 'news')->createRow();
            $row->assign($values);
            $row->save();
        }
    }

    public function topicCount()
    {
        $columns = ['count' => new Expression('count(*)')];
        $select  = Pi::model('topic', 'news')->select()->columns($columns);
        $count   = Pi::model('topic', 'news')->selectWith($select)->current()->count;
        return $count;
    }

    public function topicSubId($id)
    {
        $list    = [];
        $where   = ['status' => 1];
        $columns = ['id', 'pid'];
        $select  = Pi::model('topic', 'news')->select()->columns($columns)->where($where);
        $rowset  = Pi::model('topic', 'news')->selectWith($select);
        foreach ($rowset as $row) {
            $list[$row->id] = $row->toArray();
        }
        $ids = $this->getTreeId($list, $id);
        $ids = array_unique($ids);
        return $ids;
    }

    public function getTreeId($topics, $id, $ids = [])
    {
        $ids[$id] = $id;
        foreach ($topics as $topic) {
            if ($topic['pid'] == $id) {
                $ids[$topic['id']] = $topic['id'];
                $ids               = $this->getTreeId($topics, $topic['id'], $ids);
                unset($topics[$topic['id']]);
            }
        }
        return $ids;
    }

    public function getTreeFull($elements, $parentId = 0)
    {
        $branch = [];
        // Set category list as tree
        foreach ($elements as $element) {
            if ($element['pid'] == $parentId) {
                $depth                           = 0;
                $branch[$element['id']]          = $element;
                $branch[$element['id']]['depth'] = $depth;
                $children                        = $this->getTreeFull($elements, $element['id']);
                if ($children) {
                    $depth++;
                    foreach ($children as $key => $value) {
                        $branch[$key]          = $value;
                        $branch[$key]['depth'] = $depth;
                    }
                }
                unset($elements[$element['id']]);
                unset($depth);
            }
        }
        return $branch;
    }

    public function sitemap()
    {
        if (Pi::service('module')->isActive('sitemap')) {
            // Remove old links
            Pi::api('sitemap', 'sitemap')->removeAll('news', 'topic');
            // find and import
            $columns = ['id', 'slug', 'status'];
            $select  = Pi::model('topic', 'news')->select()->columns($columns);
            $rowset  = Pi::model('topic', 'news')->selectWith($select);
            foreach ($rowset as $row) {
                // Make url
                $loc = Pi::url(
                    Pi::service('url')->assemble(
                        'news',
                        [
                            'module'     => 'news',
                            'controller' => 'topic',
                            'slug'       => $row->slug,
                        ]
                    )
                );
                // Add to sitemap
                Pi::api('sitemap', 'sitemap')->groupLink($loc, $row->status, 'news', 'topic', $row->id);
            }
        }
    }

    public function regenerateImage()
    {
        // Get config
        $config = Pi::service('registry')->config->read('news');
        // Set info
        $columns = ['id', 'image', 'path'];
        $order   = ['id ASC'];
        $select  = Pi::model('topic', 'news')->select()->columns($columns)->order($order);
        $rowset  = Pi::model('topic', 'news')->selectWith($select);
        foreach ($rowset as $row) {
            if (!empty($row->image) && !empty($row->path)) {
                // Set image original path
                $original = Pi::path(
                    sprintf(
                        'upload/%s/original/%s/%s',
                        $config['image_path'],
                        $row->path,
                        $row->image
                    )
                );
                // Set image large path
                $images['large'] = Pi::path(
                    sprintf(
                        'upload/%s/large/%s/%s',
                        $config['image_path'],
                        $row->path,
                        $row->image
                    )
                );
                // Set image medium path
                $images['medium'] = Pi::path(
                    sprintf(
                        'upload/%s/medium/%s/%s',
                        $config['image_path'],
                        $row->path,
                        $row->image
                    )
                );
                // Set image thumb path
                $images['thumb'] = Pi::path(
                    sprintf(
                        'upload/%s/thumb/%s/%s',
                        $config['image_path'],
                        $row->path,
                        $row->image
                    )
                );
                // Check original exist of not
                if (file_exists($original)) {
                    // Remove old images
                    foreach ($images as $image) {
                        if (file_exists($image)) {
                            Pi::service('file')->remove($image);
                        }
                    }
                    // regenerate
                    Pi::api('image', 'news')->process($row->image, $row->path);
                }
            }
        }
    }
}

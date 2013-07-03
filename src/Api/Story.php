<?php
/**
 * News module Story class
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
use Zend\Json\Json;

/*
 * Pi::service('api')->news(array('Story', 'AttachCount'), $id);
 * Pi::service('api')->news(array('Story', 'AttachList'), $config, $id);
 * Pi::service('api')->news(array('Story', 'Topic'), $topic);
 * Pi::service('api')->news(array('Story', 'ExtraCount'), $id);
 * Pi::service('api')->news(array('Story', 'Related'), $id, $topic, $limit);
 * Pi::service('api')->news(array('Story', 'Link'), $id, $topic);
 */

class Story extends AbstractApi
{
    /**
     * Set number of attach files for selected story
     */
    public function AttachCount($id)
    {
        // Get attach count
        $where = array('story' => $id);
        $columns = array('count' => new \Zend\Db\Sql\Predicate\Expression('count(*)'));
        $select = Pi::model('attach', $this->getModule())->select()->columns($columns)->where($where);
        $count = Pi::model('attach', $this->getModule())->selectWith($select)->current()->count;
        // Set attach count
        Pi::model('story', $this->getModule())->update(array('attach' => $count), array('id' => $id));
    }

    /**
     * Get list of attach files
     */
    public function AttachList($config, $id)
    {
        // Set info
        $where = array('story' => $id, 'status' => 1);
        $order = array('create DESC', 'id DESC');
        // Get all attach files
        $select = Pi::model('attach', $this->getModule())->select()->where($where)->order($order);
        $rowset = Pi::model('attach', $this->getModule())->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $file[$row->type][$row->id] = $row->toArray();
            $file[$row->type][$row->id]['create'] = date('Y/m/d', $file[$row->type][$row->id]['create']);
            if ($file[$row->type][$row->id]['type'] == 'image') {
                $file[$row->type][$row->id]['mediumurl'] = Pi::url('upload/' . $config['image_path'] . '/medium/' . $file[$row->type][$row->id]['path'] . '/' . $file[$row->type][$row->id]['file']);
                $file[$row->type][$row->id]['thumburl'] = Pi::url('upload/' . $config['image_path'] . '/thumb/' . $file[$row->type][$row->id]['path'] . '/' . $file[$row->type][$row->id]['file']);
            } else {
                $file[$row->type][$row->id]['link'] = Pi::url('upload/' . $config['file_path'] . '/' . $file[$row->type][$row->id]['type'] . '/' . $file[$row->type][$row->id]['path'] . '/' . $file[$row->type][$row->id]['file']);
            }
        }
        // return
        return $file;
    }

    /**
     * Get list of topics for selected story
     */
    public function Topic($topic)
    {
        $where = array('id' => Json::decode($topic), 'status' => 1);
        $columns = array('id', 'title', 'alias');
        $select = Pi::model('topic', $this->getModule())->select()->where($where)->columns($columns);
        $topic = Pi::model('topic', $this->getModule())->selectWith($select)->toArray();
        return $topic;
    }

    /**
     * Set number of used extra fields for selected story
     */
    public function ExtraCount($id)
    {
        // Get attach count
        $columns = array('count' => new \Zend\Db\Sql\Predicate\Expression('count(*)'));
        $select = Pi::model('data', $this->getModule())->select()->columns($columns);
        $count = Pi::model('data', $this->getModule())->selectWith($select)->current()->count;
        // Set attach count
        Pi::model('story', $this->getModule())->update(array('extra' => $count), array('id' => $id));
    }

    /**
     * Get related stores
     */
    public function Related($id, $topic, $limit = 10)
    {
        // Set info
        $related = array();
        $order = array('publish DESC', 'id DESC');
        $whereLink = array('status' => 1, 'story != ?' => $id, 'publish <= ?' => time(), 'topic' => Json::decode($topic));
        $columnsLink = array('story' => new \Zend\Db\Sql\Predicate\Expression('DISTINCT story'));
        // Get info from link table
        $select = Pi::model('link', $this->getModule())->select()->where($whereLink)->columns($columnsLink)->order($order)->limit($limit);
        $rowset = Pi::model('link', $this->getModule())->selectWith($select)->toArray();
        // Make list
        foreach ($rowset as $id) {
            $storyId[] = $id['story'];
        }
        // Get story
        if (!empty($storyId)) {
            $whereStory = array('status' => 1, 'id' => $storyId);
            $columnStory = array('id', 'title', 'alias');
            $select = Pi::model('story', $this->getModule())->select()->columns($columnStory)->where($whereStory)->order($order);
            $related = Pi::model('story', $this->getModule())->selectWith($select)->toArray();
        }
        return $related;
    }

    public function Link($id, $topic)
    {
        // Set info
        $link = array();
        $columnsLink = array('story');
        // Select next
        $whereNextLink = array('status' => 1, 'story > ?' => $id, 'publish <= ?' => time(), 'topic' => Json::decode($topic));
        $selectNextLink = Pi::model('link', $this->getModule())->select()->columns($columnsLink)->where($whereNextLink)->order(array('id ASC'))->limit(1);
        $storyNextLink = Pi::model('link', $this->getModule())->selectWith($selectNextLink)->toArray();
        if (!empty($storyNextLink[0]['story'])) {
            $story = Pi::model('story', $this->getModule())->find($storyNextLink[0]['story'])->toArray();
            $link['next'] = $story['alias'];
        }
        // Select Prev
        $wherePrevLink = array('status' => 1, 'story <  ?' => $id, 'publish <= ?' => time(), 'topic' => Json::decode($topic));
        $selectPrevLink = Pi::model('link', $this->getModule())->select()->columns($columnsLink)->where($wherePrevLink)->order(array('id DESC'))->limit(1);
        $storyPrevLink = Pi::model('link', $this->getModule())->selectWith($selectPrevLink)->toArray();
        if (!empty($storyPrevLink[0]['story'])) {
            $story = Pi::model('story', $this->getModule())->find($storyPrevLink[0]['story'])->toArray();
            $link['previous'] = $story['alias'];
        }
        return $link;
    }
}
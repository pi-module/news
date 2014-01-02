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
use Zend\Json\Json;

/*
 * Pi::api('news', 'story')->AttachCount($id);
 * Pi::api('news', 'story')->AttachList($id);
 * Pi::api('news', 'story')->ExtraCount($id);
 * Pi::api('news', 'story')->Related($id, $topic;
 * Pi::api('news', 'story')->Link($id, $topic);
 * Pi::api('news', 'story')->getListFromId($id);
 * Pi::api('news', 'story')->getListFromIdLight($id);
 * Pi::api('news', 'story')->canonizeStory($story, $ctopicList);
 * Pi::api('news', 'story')->canonizeStoryLight($story);
 */

class Story extends AbstractApi
{
    /**
     * Set number of attach files for selected story
     */
    public function AttachCount($id)
    {
        // set info
        $where = array('story' => $id);
        $columns = array('count' => new \Zend\Db\Sql\Predicate\Expression('count(*)'));
        // Get attach count
        $select = Pi::model('attach', $this->getModule())->select()->columns($columns)->where($where);
        $count = Pi::model('attach', $this->getModule())->selectWith($select)->current()->count;
        // Set attach count
        Pi::model('story', $this->getModule())->update(array('attach' => $count), array('id' => $id));
    }

    /**
     * Get list of attach files
     */
    public function AttachList($id)
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Set info
        $where = array('story' => $id, 'status' => 1);
        $order = array('time_create DESC', 'id DESC');
        // Get all attach files
        $select = Pi::model('attach', $this->getModule())->select()->where($where)->order($order);
        $rowset = Pi::model('attach', $this->getModule())->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $file[$row->type][$row->id] = $row->toArray();
            $file[$row->type][$row->id]['time_create'] = _date($file[$row->type][$row->id]['time_create']);
            if ($file[$row->type][$row->id]['type'] == 'image') {
                $file[$row->type][$row->id]['mediumUrl'] = Pi::url(
                    sprintf('upload/%s/medium/%s/%s', 
                        $config['image_path'], 
                        $file[$row->type][$row->id]['path'], 
                        $file[$row->type][$row->id]['file']
                    )); 
                $file[$row->type][$row->id]['thumbUrl'] = Pi::url(
                    sprintf('upload/%s/thumb/%s/%s', 
                        $config['image_path'], 
                        $file[$row->type][$row->id]['path'], 
                        $file[$row->type][$row->id]['file']
                    ));
            } else {
                $file[$row->type][$row->id]['link'] = Pi::url(
                    sprintf('upload/%s/thumb/%s/%s/%s', 
                        $config['file_path'], 
                        $file[$row->type][$row->id]['type'], 
                        $file[$row->type][$row->id]['path'], 
                        $file[$row->type][$row->id]['file']
                    ));
            }
        }
        // return
        return $file;
    }

    /**
     * Set number of used extra fields for selected story
     */
    public function ExtraCount($id)
    {
        // set info
        $where = array('story' => $id);
        $columns = array('count' => new \Zend\Db\Sql\Predicate\Expression('count(*)'));
        // Get extra count
        $select = Pi::model('field_data', $this->getModule())->select()->columns($columns)->where($where);
        $count = Pi::model('field_data', $this->getModule())->selectWith($select)->current()->count;
        // Set extra count
        Pi::model('story', $this->getModule())->update(array('extra' => $count), array('id' => $id));
    }

    /**
     * Get related stores
     */
    public function Related($id, $topic)
    {
        // Set info
        $config = Pi::service('registry')->config->read($this->getModule());
        $related = array();
        $order = array('time_publish DESC', 'id DESC');
        $where = array('status' => 1, 'story != ?' => $id, 'time_publish <= ?' => time(), 'topic' => $topic);
        $columns = array('story' => new \Zend\Db\Sql\Predicate\Expression('DISTINCT story'));
        $limit = intval($config['related_num']);
        // Get info from link table
        $select = Pi::model('link', $this->getModule())->select()->where($where)->columns($columns)->order($order)->limit($limit);
        $rowset = Pi::model('link', $this->getModule())->selectWith($select)->toArray();
        // Make list
        foreach ($rowset as $id) {
            $storyId[] = $id['story'];
        }
        // Get story
        if (!empty($storyId)) {
            $related = $this->getListFromIdLight($storyId);
        }
        return $related;
    }

    public function Link($id, $topic)
    {
        // Set info
        $link = array();
        $columns = array('story');
        // Select next
        $where = array('status' => 1, 'story > ?' => $id, 'time_publish <= ?' => time(), 'topic' => $topic);
        $select = Pi::model('link', $this->getModule())->select()->columns($columns)->where($where)->order(array('id ASC'))->limit(1);
        $row = Pi::model('link', $this->getModule())->selectWith($select)->current();
        if (!empty($row)) {
            $row = $row->toArray();
            $story = Pi::model('story', $this->getModule())->find($row['story'])->toArray();
            $link['next']['title'] = $story['title'];
            $link['next']['url'] = Pi::service('url')->assemble('news', array(
                'module'        => $this->getModule(),
                'controller'    => 'story',
                'slug'          => $story['slug'],
            ));
        }
        // Select Prev
        $where = array('status' => 1, 'story <  ?' => $id, 'time_publish <= ?' => time(), 'topic' => $topic);
        $select = Pi::model('link', $this->getModule())->select()->columns($columns)->where($where)->order(array('id ASC'))->limit(1);
        $row = Pi::model('link', $this->getModule())->selectWith($select)->current();
        if (!empty($row)) {
            $row = $row->toArray();
            $story = Pi::model('story', $this->getModule())->find($row['story'])->toArray();
            $link['previous']['title'] = $story['title'];
            $link['previous']['url'] = Pi::service('url')->assemble('news', array(
                'module'        => $this->getModule(),
                'controller'    => 'story',
                'slug'          => $story['slug'],
            ));
        }
        return $link;
    }

    public function getListFromId($id)
    {
        $list = array();
        $where = array('id' => $id, 'status' => 1);
        $select = Pi::model('story', $this->getModule())->select()->where($where);
        $rowset = Pi::model('story', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $list[$row->id] = $this->canonizeStory($row);
        }
        return $list;
    }

    public function getListFromIdLight($id)
    {
        $list = array();
        $where = array('id' => $id, 'status' => 1);
        $select = Pi::model('story', $this->getModule())->select()->where($where);
        $rowset = Pi::model('story', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $list[$row->id] = $this->canonizeStoryLight($row);
        }
        return $list;
    }

    public function canonizeStory($story, $topicList = array())
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // boject to array
        $story = $story->toArray();
        // Set short text
        $story['short'] = Pi::service('markup')->render($story['short'], 'text', 'html');
        // Set body text
        $story['body'] = Pi::service('markup')->render($story['body'], 'text', 'html');
        // Set times
        $story['time_create_view'] = _date($story['time_create']);
        $story['time_publish_view'] = _date($story['time_publish']);
        $story['time_update_view'] = _date($story['time_update']);
        // Set story url
        $story['storyUrl'] = Pi::service('url')->assemble('news', array(
            'module'        => $this->getModule(),
            'controller'    => 'story',
            'slug'          => $story['slug'],
        ));
        // Set topic information
        $story['topic'] = Json::decode($story['topic']);
        // Get topic list
        $topicList = (empty($topicList)) ? 
                      Pi::api('news', 'topic')->topicList($story['topic']) : $topicList;
        foreach ($story['topic'] as $topic) {
            $story['topics'][$topic]['title'] = $topicList[$topic]['title'];
            $story['topics'][$topic]['url'] = Pi::service('url')->assemble('news', array(
                'module'        => $this->getModule(),
                'controller'    => 'topic',
                'slug'          => $topicList[$topic]['slug'],
            ));
        }
        // Set image url
        if ($story['image']) {
            // Set image original url
            $story['originalUrl'] = Pi::url(
                sprintf('upload/%s/original/%s/%s', 
                    $config['image_path'], 
                    $story['path'], 
                    $story['image']
                ));
            // Set image large url
            $story['largeUrl'] = Pi::url(
                sprintf('upload/%s/large/%s/%s', 
                    $config['image_path'], 
                    $story['path'], 
                    $story['image']
                ));
            // Set image medium url
            $story['mediumUrl'] = Pi::url(
                sprintf('upload/%s/medium/%s/%s', 
                    $config['image_path'], 
                    $story['path'], 
                    $story['image']
                ));
            // Set image thumb url
            $story['thumbUrl'] = Pi::url(
                sprintf('upload/%s/thumb/%s/%s', 
                    $config['image_path'], 
                    $story['path'], 
                    $story['image']
                ));
        }
        // return story
        return $story; 
    }

    public function canonizeStoryLight($story)
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // boject to array
        $story = $story->toArray();
        // Set times
        $story['time_publish_view'] = _date($story['time_publish']);
        // Set story url
        $story['storyUrl'] = Pi::service('url')->assemble('news', array(
            'module'        => $this->getModule(),
            'controller'    => 'story',
            'slug'          => $story['slug'],
        ));
        // Set image url
        if ($story['image']) {
            // Set image thumb url
            $story['thumbUrl'] = Pi::url(
                sprintf('upload/%s/thumb/%s/%s', 
                    $config['image_path'], 
                    $story['path'], 
                    $story['image']
                ));
        }
        // unset
        unset($story['short']);
        unset($story['body']);
        unset($story['time_create']);
        unset($story['time_update']);
        unset($story['topic']);
        // return story
        return $story; 
    }
}
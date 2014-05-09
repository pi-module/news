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
use Pi\Application\Api\AbstractApi;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Json\Json;

/*
 * Pi::api('topic', 'news')->canonizeTopic($topic);
 * Pi::api('topic', 'news')->setLink($story, $topics, $time_publish, $status, $uid);
 * Pi::api('topic', 'news')->topicList($id);
 * Pi::api('topic', 'news')->topicCount();
 */

class Topic extends AbstractApi
{
    /**
     * Set page setting from topic or module config
     */
    public function canonizeTopic($topic = null)
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Set topic information
        if (isset($topic) && !empty($topic) && is_object($topic)) {
            $topic = $topic->toArray();
            // Get setting
            $setting = Json::decode($topic['setting']);
            $topic = array_merge($topic, (array) $setting);
            // Set topic url
            $topic['topicUrl'] = Pi::service('url')->assemble('news', array(
                'module'        => $this->getModule(),
                'controller'    => 'topic',
                'slug'          => $topic['slug'],
            ));
            // Set image url
            if ($topic['image']) {
                // Set image original url
                $topic['originalUrl'] = Pi::url(
                    sprintf('upload/%s/original/%s/%s', 
                        $config['image_path'], 
                        $topic['path'], 
                        $topic['image']
                    ));
                // Set image large url
                $topic['largeUrl'] = Pi::url(
                    sprintf('upload/%s/large/%s/%s', 
                        $config['image_path'], 
                        $topic['path'], 
                        $topic['image']
                    ));
                // Set image medium url
                $topic['mediumUrl'] = Pi::url(
                    sprintf('upload/%s/medium/%s/%s', 
                        $config['image_path'], 
                        $topic['path'], 
                        $topic['image']
                    ));
                // Set image thumb url
                $topic['thumbUrl'] = Pi::url(
                    sprintf('upload/%s/thumb/%s/%s', 
                        $config['image_path'], 
                        $topic['path'], 
                        $topic['image']
                    ));
            }
            // Show sub id
            if ($topic['show_subid']) {
                $topic['ids'] = $this->topicSubId($topic['id']);
            } else {
                $topic['ids'] = $topic['id'];
            }
            // Show attach
            $topic['attach'] = array();
            if ($topic['show_attach'] && $topic['show_topicinfo']) {
                $topic['attach'] = $this->topicAttach($topic['id']);
            }
        }  
        // Set topic config
        if (!isset($topic) || $topic['show_config'] == 'module') {
            $topic['style'] = $config['style'];
            $topic['show_perpage'] = $config['show_perpage'];
            $topic['show_columns'] = $config['show_columns'];
            $topic['show_topic'] = $config['show_topic'];
            $topic['show_topicinfo'] = $config['show_topicinfo'];
            $topic['show_writer'] = $config['show_writer'];
            $topic['show_date'] = $config['show_date'];
            $topic['show_print'] = $config['show_print'];
            $topic['show_pdf'] = $config['show_pdf'];
            $topic['show_mail'] = $config['show_mail'];
            $topic['show_hits'] = $config['show_hits'];
            $topic['show_tag'] = $config['show_tag'];
        }
        // Set perpage
        if (empty($topic['show_perpage'])) {
            $topic['show_perpage'] = $config['show_perpage'];
        }
        // Set columns
        if (empty($topic['show_columns'])) {
            $topic['show_columns'] = $config['show_columns'];
        }
        // title
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
        }
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
    public function Template($type)
    {
        switch ($type) {
            default:
            case 'news':
                $template = 'index_news';
                break;

            case 'list':
                $template = 'index_list';
                break;

            case 'table':
                $template = 'index_table';
                break;

            case 'media':
                $template = 'index_media';
                break;

            case 'spotlight':
                $template = 'index_spotlight';
                break;
        }
        return $template;
    }

    public function setLink($story, $topics, $time_publish, $status, $uid)
    {
        //Remove
        Pi::model('link', $this->getModule())->delete(array('story' => $story));
        // Add
        $newTopics = Json::decode($topics);
        foreach ($newTopics as $topic) {
            // Set array
            $values['topic'] = $topic;
            $values['story'] = $story;
            $values['time_publish'] = $time_publish;
            $values['status'] = $status;
            $values['uid'] = $uid;
            // Save
            $row = Pi::model('link', $this->getModule())->createRow();
            $row->assign($values);
            $row->save();
        }
    }

    public function topicList($id = null)
    {
        $return = array();
        if (is_null($id)) {
            $where = array('status' => 1);
        } else {
            $where = array('status' => 1, 'id' => $id);
        }
        $order = array('time_create DESC', 'id DESC');
        $select = Pi::model('topic', $this->getModule())->select()->where($where)->order($order);
        $rowset = Pi::model('topic', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $return[$row->id] = $row->toArray();
            $return[$row->id]['url'] = Pi::service('url')->assemble('news', array(
                'module'        => $this->getModule(),
                'controller'    => 'topic',
                'slug'          => $return[$row->id]['slug'],
            ));
        }
        return $return;
    }  

    public function topicCount()
    {
        $columns = array('count' => new \Zend\Db\Sql\Predicate\Expression('count(*)'));
        $select = Pi::model('topic', $this->getModule())->select()->columns($columns);
        $count = Pi::model('topic', $this->getModule())->selectWith($select)->current()->count;
        return $count;
    }

    public function topicSubId($id)
    {
        $list = array();
        $where = array('status' => 1);
        $columns = array('id', 'pid');
        $select = Pi::model('topic', $this->getModule())->select()->columns($columns)->where($where);
        $rowset = Pi::model('topic', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $list[$row->id] = $row->toArray();
        }
        $ids = $this->getTree($list, $id);
        $ids = array_unique($ids);
        return $ids;
    }

    public function getTree($elements, $id, $ids = array())
    {
        $ids[$id] = $id;
        foreach ($elements as $element) {
            if ($element['pid'] == $id) {
                $ids[$element['id']] = $element['id'];
                $ids = $this->getTree($elements, $element['id'], $ids);
                unset($elements[$element['id']]);
            }
        }
        return $ids;
    }

    public function topicAttach($id)
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Set info
        $where = array('status' => 1, 'topic' => $id);
        $columns = array('story' => new Expression('DISTINCT story'));
        $order = array('time_publish DESC', 'id DESC');
        $limit = 50;
        // Get info from link table
        $select = Pi::model('link', $this->getModule())->select()->where($where)->columns($columns)->order($order)->limit($limit);
        $rowset = Pi::model('link', $this->getModule())->selectWith($select)->toArray();
        // Make list
        foreach ($rowset as $id) {
            $storyId[] = $id['story'];
        }
        if (empty($storyId)) {
            return '';
        }
        // Set info
        $file = array();
        $where = array('story' => $storyId, 'status' => 1, 'type' => array('archive','pdf','doc','other'));
        $order = array('time_create DESC', 'id DESC');
        // Get all attach files
        $select = Pi::model('attach', $this->getModule())->select()->where($where)->order($order);
        $rowset = Pi::model('attach', $this->getModule())->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $file[$row->id] = $row->toArray();
            $file[$row->id]['time_create'] = _date($file[$row->id]['time_create']);
            // Set file link
            if ($file[$row->id]['type'] == 'image') {
                $file[$row->id]['largeUrl'] = Pi::url(
                    sprintf('upload/%s/large/%s/%s', 
                        $config['image_path'], 
                        $file[$row->id]['path'], 
                        $file[$row->id]['file']
                    )); 
                $file[$row->id]['mediumUrl'] = Pi::url(
                    sprintf('upload/%s/medium/%s/%s', 
                        $config['image_path'], 
                        $file[$row->id]['path'], 
                        $file[$row->id]['file']
                    )); 
                $file[$row->id]['thumbUrl'] = Pi::url(
                    sprintf('upload/%s/thumb/%s/%s', 
                        $config['image_path'], 
                        $file[$row->id]['path'], 
                        $file[$row->id]['file']
                    ));
            } elseif($file[$row->id]['type'] == 'other') {
                $file[$row->id]['link'] = Pi::url(
                    sprintf('upload/%s/%s/%s/%s', 
                        $config['file_path'], 
                        'file', 
                        $file[$row->id]['path'], 
                        $file[$row->id]['file']
                    ));
            } else {
                $file[$row->id]['link'] = Pi::url(
                    sprintf('upload/%s/%s/%s/%s', 
                        $config['file_path'], 
                        $file[$row->id]['type'], 
                        $file[$row->id]['path'], 
                        $file[$row->id]['file']
                    ));
            }
            // Set download url
            $file[$row->id]['downloadUrl'] = Pi::service('url')->assemble('news', array(
                'module'        => $this->getModule(),
                'controller'    => 'media',
                'action'        => 'download',
                'id'            => $row->id,
            ));
        }
        // return
        return $file;
    }
}
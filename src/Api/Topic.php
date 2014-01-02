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
 * Pi::api('news', 'topic')->canonizeTopic($topic);
 * Pi::api('news', 'topic')->setLink($story, $topics, $time_publish, $status, $uid);
 * Pi::api('news', 'topic')->topicList($id);
 * Pi::api('news', 'topic')->topicCount();
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
            case 1:
                $class = 'col-md-12';
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
}
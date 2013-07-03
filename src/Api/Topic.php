<?php
/**
 * News module Topic class
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
 * Pi::service('api')->news(array('Topic', 'Setting'), $config, $topic = null);
 * Pi::service('api')->news(array('Topic', 'Template'), $type);
 * Pi::service('api')->news(array('Topic', 'Info'), $id = null);
 * Pi::service('api')->news(array('Topic', 'Id'), $homepage = null, $id = null);
 * Pi::service('api')->news(array('Topic', 'EditAccess'));
 * Pi::service('api')->news(array('Topic', 'Set'), $story, $topics);
 */

class Topic extends AbstractApi
{
    /**
     * Set page setting from topic or module config
     */
    public function Setting($config, $topic = null)
    {
        // Set topic topic_style
        if (!isset($topic) || $topic['topic_type'] == 'module') {
            $topic['topic_style'] = $config['show_shwotype'];
            $topic['perpage'] = $config['show_perpage'];
            $topic['columns'] = $config['show_columns'];
            $topic['showtopic'] = $config['show_topic'];
            $topic['showtopicinfo'] = $config['show_topicinfo'];
            $topic['showauthor'] = $config['show_author'];
            $topic['showdate'] = $config['show_date'];
            $topic['showprint'] = $config['show_print'];
            $topic['showpdf'] = $config['show_pdf'];
            $topic['showmail'] = $config['show_mail'];
            $topic['shownav'] = $config['show_nav'];
            $topic['showhits'] = $config['show_hits'];
            $topic['showcoms'] = $config['show_coms'];
            $topic['topic_homepage'] = $config['show_homepage'];
        }
        // Set perpage
        if (empty($topic['perpage'])) {
            $topic['perpage'] = $config['show_perpage'];
        }
        // Set columns
        if (empty($topic['columns'])) {
            $topic['columns'] = $config['show_columns'];
        }
        // title
        if (empty($topic['title'])) {
            $topic['title'] = $config['text_title'];
        }
        // description
        if (empty($topic['description'])) {
            $topic['description'] = $config['text_description'];
        }
        // keywords
        if (empty($topic['keywords'])) {
            $topic['keywords'] = $config['text_keywords'];
        }
        // Template
        $topic['template'] = $this->Template($topic['topic_style']);
        // column class
        $topic['column_class'] = $this->Column($topic['columns']);
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
                $class = 'span12';
                break;

            case 2:
                $class = 'span6';
                break;

            case 3:
                $class = 'span4';
                break;

            case 4:
                $class = 'span3';
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

    /**
     * Get list of topics than use in page
     * in topic pages just select main and sub topics
     * in main page select all topics
     */

    public function Info()
    {
        $columns = array('id', 'title', 'alias');
        $where = array('status' => 1);
        $select = Pi::model('topic', $this->getModule())->select()->columns($columns)->where($where);
        $rowset = Pi::model('topic', $this->getModule())->selectWith($select);
        // Make topic list
        foreach ($rowset as $row) {
            $topic[$row->id] = $row->toArray();
        }
        return $topic;
    }

    /**
     * Get topic ids for select story
     * in homepage all topic selected
     * in topic page use homepage setting for select
     */
    public function Id($homepage = null, $id = null)
    {
        if (isset($homepage)) {
            switch ($homepage) {
                case 'type1':
                    $select = Pi::model('topic', $this->getModule())->select()->columns(array('id'))->where(array('status' => 1))->where(array('id ' => $id))->where(array('pid' => $id), 'OR');
                    $list = Pi::model('topic', $this->getModule())->selectWith($select);
                    foreach ($list as $item) {
                        $topicId[] = $item->id;
                    }
                    break;

                case 'type2':
                    $topicId[] = $id;
                    break;

                case 'type3':
                    $select = Pi::model('topic', $this->getModule())->select()->columns(array('id'))->where(array('status' => 1, 'pid' => $id));
                    $list = Pi::model('topic', $this->getModule())->selectWith($select);
                    foreach ($list as $item) {
                        $topicId[] = $item->id;
                    }
                    break;
            }
        } else {
            $select = Pi::model('topic', $this->getModule())->select()->columns(array('id'))->where(array('status' => 1, 'inlist' => 1));
            $list = Pi::model('topic', $this->getModule())->selectWith($select);
            foreach ($list as $item) {
                $topicId[] = $item->id;
            }
        }

        if (empty($topicId)) {
            throw new \Exception(__('No Topic selected.'));
        }

        return $topicId;
    }

    /**
     * Get topic ids for select story
     */
    public function EditAccess()
    {
        $select = Pi::model('moderator', $this->getModule())->select()->columns(array('topic'))->where(array('status' => 1, 'manager' => Pi::registry('user')->id));
        $rowset = Pi::model('moderator', $this->getModule())->selectWith($select)->toArray();
        foreach ($rowset as $row) {
            $topic[] = $row['topic'];
        }
        return $topic;
    }

    /**
     * Set story topics to link table
     */
    public function Set($story, $topics, $publish, $status, $author)
    {
        //Remove
        Pi::model('link', $this->getModule())->delete(array('story' => $story));
        // Add
        $newTopics = Json::decode($topics);
        foreach ($newTopics as $topic) {
            // Set array
            $values['topic'] = $topic;
            $values['story'] = $story;
            $values['publish'] = $publish;
            $values['status'] = $status;
            $values['author'] = $author;
            // Save
            $row = Pi::model('link', $this->getModule())->createRow();
            $row->assign($values);
            $row->save();
        }
    }
}
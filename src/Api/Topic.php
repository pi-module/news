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
 * Pi::api('topic', 'news')->getTopic($parameter, $type = 'id');
 * Pi::api('topic', 'news')->canonizeTopic($topic);
 * Pi::api('topic', 'news')->setLink($story, $topics, $time_publish, $status, $uid);
 * Pi::api('topic', 'news')->topicCount();
 * Pi::api('topic', 'news')->sitemap();
 * Pi::api('topic', 'news')->regenerateImage();
 */

class Topic extends AbstractApi
{
    public function getTopic($parameter, $type = 'id')
    {
        // Get topic
        $topic = Pi::model('topic', $this->getModule())->find($parameter, $type);
        $topic = $topic->toArray();
        $topic['topicUrl'] = Pi::url(Pi::service('url')->assemble('news', array(
            'module'        => $this->getModule(),
            'controller'    => 'topic',
            'slug'          => $topic['slug'],
        )));
        return $topic;
    }

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
            $topic['topicUrl'] = Pi::url(Pi::service('url')->assemble('news', array(
                'module'        => $this->getModule(),
                'controller'    => 'topic',
                'slug'          => $topic['slug'],
            )));
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
            // Check attach
            if (!empty($topic['attach_link'])) {
                $topic['attach_download_link'] = Pi::url(Pi::service('url')->assemble('news', array(
                    'module'        => $this->getModule(),
                    'controller'    => 'media',
                    'action'        => 'topic',
                    'id'            => $topic['id'],
                )));
                // Check attach title
                if (empty($topic['attach_title'])) {
                    $topic['attach_title'] = __('Download');
                }
            }
        }  
        // Set topic config
        if (!isset($topic) || $topic['show_config'] == 'module') {
            $topic['style'] = $config['style'];
            $topic['show_perpage'] = $config['show_perpage'];
            $topic['show_columns'] = $config['show_columns'];
            $topic['show_order_link'] = $config['show_order_link'];
            $topic['show_topic'] = $config['show_topic'];
            $topic['show_topicinfo'] = $config['show_topicinfo'];
            $topic['show_date'] = $config['show_date'];
            $topic['show_print'] = $config['show_print'];
            $topic['show_pdf'] = $config['show_pdf'];
            $topic['show_mail'] = $config['show_mail'];
            $topic['show_hits'] = $config['show_hits'];
            $topic['show_tag'] = $config['show_tag'];
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

    public function sitemap()
    {
        if (Pi::service('module')->isActive('sitemap')) {
            // Remove old links
            Pi::api('sitemap', 'sitemap')->removeAll($this->getModule(), 'topic');
            // find and import
            $columns = array('id', 'slug', 'status');
            $select = Pi::model('topic', $this->getModule())->select()->columns($columns);
            $rowset = Pi::model('topic', $this->getModule())->selectWith($select);
            foreach ($rowset as $row) {
                // Make url
                $loc = Pi::url(Pi::service('url')->assemble('news', array(
                    'module'        => $this->getModule(),
                    'controller'    => 'topic',
                    'slug'          => $row->slug,
                )));
                // Add to sitemap
                Pi::api('sitemap', 'sitemap')->groupLink($loc, $row->status, $this->getModule(), 'topic', $row->id);
            }
        }
    }

    public function regenerateImage()
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Set info
        $columns = array('id', 'image', 'path');
        $order = array('id ASC');
        $select = Pi::model('topic', $this->getModule())->select()->columns($columns)->order($order);
        $rowset = Pi::model('topic', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            if (!empty($row->image) && !empty($row->path)) {
                // Set image original path
                $original = Pi::path(
                    sprintf('upload/%s/large/%s/%s', 
                        $config['image_path'], 
                        $row->path,
                        $row->image
                    ));
                // Set image large path
                $images['large'] = Pi::path(
                    sprintf('upload/%s/large/%s/%s', 
                        $config['image_path'], 
                        $row->path,
                        $row->image
                    ));
                // Set image medium path
                $images['medium'] = Pi::path(
                    sprintf('upload/%s/medium/%s/%s', 
                        $config['image_path'], 
                        $row->path, 
                        $row->image
                    ));
                // Set image thumb path
                $images['thumb'] = Pi::path(
                    sprintf('upload/%s/thumb/%s/%s', 
                        $config['image_path'], 
                        $row->path, 
                        $row->image
                    ));
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
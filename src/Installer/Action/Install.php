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

namespace Module\News\Installer\Action;

use Pi;
use Pi\Application\Installer\Action\Install as BasicInstall;
use Zend\EventManager\Event;

class Install extends BasicInstall
{
    protected function attachDefaultListeners()
    {
        $events = $this->events;
        $events->attach('install.pre', [$this, 'preInstall'], 1000);
        $events->attach('install.post', [$this, 'postInstall'], 1);
        parent::attachDefaultListeners();
        return $this;
    }

    public function preInstall(Event $e)
    {
        $result = [
            'status'  => true,
            'message' => sprintf('Called from %s', __METHOD__),
        ];
        $e->setParam('result', $result);
    }

    public function postInstall(Event $e)
    {
        $module = $e->getParam('module');

        // Set model
        $storyModel         = Pi::model('story', $module);
        $topicModel         = Pi::model('topic', $module);
        $linkModel          = Pi::model('link', $module);
        $fieldModel         = Pi::model('field', $module);
        $fieldPositionModel = Pi::model('field_position', $module);
        $fieldTopicModel    = Pi::model('field_topic', $module);
        $authorModel        = Pi::model('author', $module);
        $authorRoleModel    = Pi::model('author_role', $module);
        $authorStoryModel   = Pi::model('author_story', $module);

        // Set topic setting
        $setting                          = [];
        $setting['show_config']           = 'topic';
        $setting['show_order_link']       = 'publishDESC';
        $setting['show_perpage']          = 5;
        $setting['show_columns']          = 1;
        $setting['show_topic']            = 1;
        $setting['show_topicinfo']        = 1;
        $setting['show_date']             = 1;
        $setting['show_pdf']              = 1;
        $setting['show_print']            = 1;
        $setting['show_mail']             = 1;
        $setting['show_hits']             = 1;
        $setting['show_tag']              = 1;
        $setting['show_subid']            = 1;
        $setting['attach_link']           = '';
        $setting['attach_title']          = '';
        $setting['attach_download_count'] = '';

        // Add topic
        $topicData = [
            'title'            => __('Default'),
            'slug'             => __('default'),
            'text_description' => __('This is a default topic for news module'),
            'seo_title'        => __('default topic'),
            'seo_keywords'     => __('default,topic'),
            'seo_description'  => __('default topic'),
            'time_create'      => time(),
            'time_update'      => time(),
            'status'           => '1',
            'style'            => 'news',
            'uid'              => Pi::user()->getId(),
            'setting'          => json_encode($setting),
        ];
        $topicModel->insert($topicData);

        // Add topic as page
        /* $pageData = array(
            'section'          => 'front',
            'module'           => $module,
            'controller'       => 'topic',
            'action'           => 'default',
            'title'            => __('Default'),
            'block'            => 1,
            'custom'           => 0,
        );
        $pageRow = Pi::model('page')->createRow($pageData);
        $pageRow->save(); */

        // Add story
        $storyData = [
            'title'            => __('Hello World'),
            'subtitle'         => __('This is subtitle for this story'),
            'slug'             => __('hello-world'),
            'topic'            => json_encode(['1']),
            'topic_main'       => 1,
            'text_summary'     => __('This is a short text and you can edit this part easy. for read more infor please click on title or more link'),
            'text_description' => __('This is more text. you can edit this part easy too and if you want you can add new topics and new storys'),
            'seo_title'        => __('hello world'),
            'seo_keywords'     => __('hello,world'),
            'seo_description'  => __('hello world'),
            'status'           => '1',
            'time_create'      => time(),
            'time_update'      => time(),
            'time_publish'     => time(),
            'uid'              => Pi::user()->getId(),
        ];
        $storyModel->insert($storyData);

        // Add link
        $linkData = [
            'story'        => '1',
            'topic'        => '1',
            'time_publish' => time(),
            'time_update'  => time(),
            'status'       => '1',
            'uid'          => Pi::user()->getId(),
        ];
        $linkModel->insert($linkData);

        // Add field position
        $fieldPositionData = [
            'title'  => __('Extra information'),
            'order'  => '1',
            'status' => '1',
        ];
        $fieldPositionModel->insert($fieldPositionData);

        // Add field topic
        $fieldTopicData = [
            'field' => '1',
            'topic' => '0',
        ];
        $fieldTopicModel->insert($fieldTopicData);

        // Add field
        $fieldData = [
            'title'    => __('Source'),
            'name'     => 'source',
            'type'     => 'link',
            'order'    => '1',
            'status'   => '1',
            'search'   => '1',
            'position' => '1',
        ];
        $fieldModel->insert($fieldData);

        // Add author
        $authorData = [
            'title'           => __('The admin'),
            'slug'            => 'admin',
            'seo_title'       => __('Admin'),
            'seo_keywords'    => __('author,admin'),
            'seo_description' => __('author is admin'),
            'time_create'     => time(),
            'time_update'     => time(),
            'status'          => '1',
            'uid'             => Pi::user()->getId(),
        ];
        $authorModel->insert($authorData);

        // Add author role
        $authorRoleData = [
            'title'  => __('Writer'),
            'status' => '1',
        ];
        $authorRoleModel->insert($authorRoleData);

        // Add author story
        $authorStoryData = [
            'story'        => '1',
            'author'       => '1',
            'role'         => '1',
            'time_publish' => time(),
            'status'       => '1',
        ];
        $authorStoryModel->insert($authorStoryData);

        // Result
        $result = [
            'status'  => true,
            'message' => __('Default information added.'),
        ];
        $this->setResult('post-install', $result);
    }
}
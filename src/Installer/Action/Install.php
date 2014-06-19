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
use Zend\Json\Json;

class Install extends BasicInstall
{
    protected function attachDefaultListeners()
    {
        $events = $this->events;
        $events->attach('install.pre', array($this, 'preInstall'), 1000);
        $events->attach('install.post', array($this, 'postInstall'), 1);
        parent::attachDefaultListeners();
        return $this;
    }

    public function preInstall(Event $e)
    {
        $result = array(
            'status' => true,
            'message' => sprintf('Called from %s', __METHOD__),
        );
        $e->setParam('result', $result);
    }

    public function postInstall(Event $e)
    {
        // Make path
        /* $dir = Pi::path('upload/' . $e->getParam('module'));
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
            chmod($dir, 0777);
        } */
        
        $module = $e->getParam('module');
        
        // Set model
        $storyModel = Pi::model($module . '/story');
        $topicModel = Pi::model($module . '/topic');
        $linkModel = Pi::model($module . '/link');

        // Set topic setting
        $setting = array();
        $setting['show_config'] = 'topic';
        $setting['show_order_link'] = 'publishDESC';
        $setting['show_perpage'] = 5;
        $setting['show_columns'] = 1;
        $setting['show_topic'] = 1;
        $setting['show_topicinfo'] = 1;
        $setting['show_date'] = 1;
        $setting['show_pdf'] = 1;
        $setting['show_print'] = 1;
        $setting['show_mail'] = 1;
        $setting['show_hits'] = 1;
        $setting['show_tag'] = 1;
        $setting['show_subid'] = 1;
        $setting['show_attach'] = 0;

        // Add topic
        $topicData = array(
            'title'            => __('Default'),
            'slug'             => __('default'),
            'description'      => __('This is a default topic for news module'),
            'seo_title'        => __('default topic'),
            'seo_keywords'     => __('default,topic'),
            'seo_description'  => __('default topic'),
            'time_create'      => time(),
            'time_update'      => time(),
            'status'           => '1',
            'style'            => 'news',
            'uid'              => Pi::user()->getId(),
            'setting'          => Json::encode($setting),
        );
        $topicModel->insert($topicData);
        
        // Add topic as page
        $pageData = array(
            'section'          => 'front',
            'module'           => $module,
            'controller'       => 'topic',
            'action'           => 'default',
            'title'            => __('Default'),
            'block'            => 1,
            'custom'           => 0,
        );
        $pageRow = Pi::model('page')->createRow($pageData);
        $pageRow->save();
        
        // Add story
        $storyData = array(
            'title'            => __('Hello World'),
            'subtitle'         => __('This is subtitle for this story'),
            'slug'             => __('hello-world'),
            'topic'            => Json::encode(array('1')),
            'short'            => __('This is a short text and you can edit this part easy. for read more infor please click on title or more link'),
            'body'             => __('This is more text. you can edit this part easy too and if you want you can add new topics and new storys'),
            'seo_title'        => __('hello world'),
            'seo_keywords'     => __('hello,world'),
            'seo_description'  => __('hello world'),
            'status'           => '1',
            'time_create'      => time(),
            'time_update'      => time(),
            'time_publish'     => time(),
            'uid'              => Pi::user()->getId(),
        );
        $storyModel->insert($storyData);

        // Add link
        $linkData = array(
            'story'            => '1',
            'topic'            => '1',
            'time_publish'     => time(),
            'status'           => '1',
            'uid'              => Pi::user()->getId(),
        );
        $linkModel->insert($linkData);

        // Result
        $result = array(
            'status'           => true,
            'message'          => __('Default information added.'),
        );
        $this->setResult('post-install', $result);
    }
}
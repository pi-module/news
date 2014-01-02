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
        $dir = Pi::path('upload/' . $e->getParam('module'));
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
            chmod($dir, 0777);
        }
        
        $module = $e->getParam('module');
        
        // Set model
        $storyModel = Pi::model($module . '/story');
        $topicModel = Pi::model($module . '/topic');
        $writerModel = Pi::model($module . '/writer');
        $linkModel = Pi::model($module . '/link');

        // Add topic
        $topicData = array(
            'title' => __('Default'),
            'slug' => 'default',
            'description' => __('This is a default topic for news module'),
            'seo_title' => 'default topic',
            'seo_keywords' => 'default,topic',
            'seo_description' => 'default topic',
            'time_create' => time(),
            'time_update' => time(),
            'status' => '1',
            'style' => 'news',
            'uid' => Pi::user()->getId(),
        );
        $topicModel->insert($topicData);
        
        // Add topic as page
        $pageData = array(
            'section'       => 'front',
            'module'        => $module,
            'controller'    => 'topic',
            'action'        => 'default',
            'title'         => __('Default'),
            'block'         => 1,
            'custom'        => 0,
        );
        $pageRow = Pi::model('page')->createRow($pageData);
        $pageRow->save();
        
        // Add story
        $storyData = array(
            'title' => __('Hello world !'),
            'subtitle' => __('Hello World'),
            'slug' => 'hello-world',
            'topic' => Json::encode(array('1')),
            'short' => __('This is a short text and you can edit this part easy. for read more infor please click on title or more link'),
            'body' => __('This is more text. you can edit this part easy too and if you want you can add new topics and new storys'),
            'seo_title' => 'hello world',
            'seo_keywords' => 'hello,world',
            'seo_description' => 'hello world',
            'status' => '1',
            'time_create' => time(),
            'time_update' => time(),
            'time_publish' => time(),
            'uid' => Pi::user()->getId(),
        );
        $storyModel->insert($storyData);

        // Add writer
        $writerData = array(
            'uid' => Pi::user()->getId(),
            'count' => '1',
        );
        $writerModel->insert($writerData);

        // Add link
        $linkData = array(
            'story' => '1',
            'topic' => '1',
            'time_publish' => time(),
            'status' => '1',
            'uid' => Pi::user()->getId(),
        );
        $linkModel->insert($linkData);

        // Result
        $result = array(
            'status'    => true,
            'message'   => __('Default information added.'),
        );
        $this->setResult('post-install', $result);
    }
}
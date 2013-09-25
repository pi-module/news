<?php
/**
 * Pi module installer action
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
 * @subpackage      Installer
 * @version         $Id$
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
        $moderatorModel = Pi::model($module . '/moderator');
        $linkModel = Pi::model($module . '/link');

        // Add topic
        $topicData = array(
            'title' => __('Default'),
            'slug' => 'default',
            'body' => __('This is a default topic for news module'),
            'keywords' => 'default,topic',
            'description' => 'default topic',
            'topic_style' => 'news',
            'perpage' => '5',
            'columns' => '1',
            'create' => time(),
            'status' => '1',
            'topic_type' => 'module',
            'topic_homepage' => 'type1',
            'author' => Pi::registry('user')->id,
        );
        $topicModel->insert($topicData);
        
        // Add topic as page
        /* Temporary solution */
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
            'keywords' => 'hello,world',
            'description' => 'hello world',
            'status' => '1',
            'create' => time(),
            'update' => time(),
            'publish' => time(),
            'author' => Pi::registry('user')->id,
        );
        $storyModel->insert($storyData);

        // Add writer
        $writerData = array(
            'author' => Pi::registry('user')->id,
            'count' => '1',
        );
        $writerModel->insert($writerData);

        // Add moderator
        $moderatorData = array(
            'manager' => Pi::registry('user')->id,
            'topic' => '1',
            'status' => '1',
        );
        $moderatorModel->insert($moderatorData);

        // Add link
        $linkData = array(
            'story' => '1',
            'topic' => '1',
            'publish' => time(),
            'status' => '1',
            'author' => Pi::registry('user')->id,
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
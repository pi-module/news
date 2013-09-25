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
use Pi\Application\Installer\Action\Update as BasicUpdate;
use Zend\EventManager\Event;
use Pi\Application\Installer\SqlSchema;

class Update extends BasicUpdate
{
    protected function attachDefaultListeners()
    {
        $events = $this->events;
        $events->attach('update.pre', array($this, 'updateSchema'));
        parent::attachDefaultListeners();
        return $this;
    }

    public function updateSchema(Event $e)
    {
        $version = $e->getParam('version');
        $module = $e->getParam('module');

        if (version_compare($version, '1.0.3', '<')) {

        	$sqlHandler = new SqlSchema;
        	$adapter = Pi::db()->getAdapter();

        	// CHANGE `alias` to `slug` on story table
        	$storyModel = Pi::model('story', $module);
        	$storyTable = $storyModel->getTable();
        	$sql = sprintf('ALTER TABLE %s CHANGE `alias` `slug` VARCHAR( 255 ) NOT NULL', $storyTable);
        	try {
        		$adapter->query($sql, 'execute');
        	} catch (\Exception $exception) {
        		$this->setResult('db', array(
        			'status'    => false,
        			'message'   => 'Story table alter change alias to slug query failed: ' . $exception->getMessage(),
        		));
        		return false;	
        	}

            // CHANGE `alias` to `slug` on topic table
        	$topicModel = Pi::model('topic', $module);
        	$topicTable = $topicModel->getTable();
        	$sql = sprintf('ALTER TABLE %s CHANGE `alias` `slug` VARCHAR( 255 ) NOT NULL', $topicTable);
        	try {
        		$adapter->query($sql, 'execute');
        	} catch (\Exception $exception) {
        		$this->setResult('db', array(
        			'status'    => false,
        			'message'   => 'Topic table alter change alias to slug query failed: ' . $exception->getMessage(),
        		));
        		return false;	
        	}

        }
        return true;
    }    
}	
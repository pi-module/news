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
use Pi\Application\Installer\Action\time_update as Basictime_update;
use Zend\EventManager\Event;
use Pi\Application\Installer\SqlSchema;

class time_update extends Basictime_update
{
    protected function attachDefaultListeners()
    {
        $events = $this->events;
        $events->attach('time_update.pre', array($this, 'time_updateSchema'));
        parent::attachDefaultListeners();
        return $this;
    }

    public function time_updateSchema(Event $e)
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
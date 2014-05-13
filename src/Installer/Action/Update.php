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
use Pi\Application\Installer\Action\Update as BasicUpdate;
use Pi\Application\Installer\SqlSchema;
use Zend\EventManager\Event;

class Update extends BasicUpdate
{
    /**
     * {@inheritDoc}
     */
    protected function attachDefaultListeners()
    {
        $events = $this->events;
        $events->attach('update.pre', array($this, 'updateSchema'));
        parent::attachDefaultListeners();
        
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function updateSchema(Event $e)
    {
        $moduleVersion  = $e->getParam('version');
        
        // Set story model
        $storyModel    = Pi::model('story', $this->module);
        $storyTable    = $storyModel->getTable();
        $storyAdapter  = $storyModel->getAdapter();

        // Update to version 1.2.0
        if (version_compare($moduleVersion, '1.2.0', '<')) {
            // Alter table field `type`
        	$sql = sprintf("ALTER TABLE %s ADD `type` ENUM( 'text', 'gallery', 'media', 'download' ) 
        		NOT NULL DEFAULT 'text'", $storyTable);
            try {
                $storyAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status'    => false,
                    'message'   => 'Table alter query failed: '
                                   . $exception->getMessage(),
                ));
                return false;
            }
        }

        // Update to version 1.2.3
        if (version_compare($moduleVersion, '1.2.3', '<')) {
            // Alter table field `short`
            $sql = sprintf("ALTER TABLE %s CHANGE `short` `short` mediumtext", $storyTable);
            try {
                $storyAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status'    => false,
                    'message'   => 'Table alter query failed: '
                                   . $exception->getMessage(),
                ));
                return false;
            }

            // Alter table field `body`
            $sql = sprintf("ALTER TABLE %s CHANGE `body` `body` mediumtext", $storyTable);
            try {
                $storyAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status'    => false,
                    'message'   => 'Table alter query failed: '
                                   . $exception->getMessage(),
                ));
                return false;
            }
        }
        
        return true;
    }    
}
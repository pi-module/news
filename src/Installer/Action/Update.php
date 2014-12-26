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

        // Set topic model
        $topicModel    = Pi::model('topic', $this->module);
        $topicTable    = $topicModel->getTable();
        $topicAdapter  = $topicModel->getAdapter();

        // Set author model
        $authorModel    = Pi::model('author', $this->module);
        $authorTable    = $authorModel->getTable();
        $authorAdapter  = $authorModel->getAdapter();

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

        // Update to version 1.2.5
        if (version_compare($moduleVersion, '1.2.5', '<')) {

            // Add table of author
            $sql =<<<'EOD'
CREATE TABLE `{author}` (
    `id` int (10) unsigned NOT NULL auto_increment,
    `title` varchar (255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `description` text,
    `seo_title` varchar(255) NOT NULL,
    `seo_keywords` varchar(255) NOT NULL,
    `seo_description` varchar(255) NOT NULL,
    `time_create` int(10) unsigned NOT NULL,
    `time_update` int(10) unsigned NOT NULL,
    `uid` int(10) unsigned NOT NULL,
    `hits` int(10) unsigned NOT NULL,
    `image` varchar(255) NOT NULL,
    `path` varchar(16) NOT NULL,
    `status` tinyint(1) unsigned NOT NULL default '1',
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `status` (`status`),
    KEY `title` (`title`),
    KEY `time_create` (`time_create`),
    KEY `order` (`title`, `id`)
);
EOD;
            SqlSchema::setType($this->module);
            $sqlHandler = new SqlSchema;
            try {
                $sqlHandler->queryContent($sql);
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status'    => false,
                    'message'   => 'SQL schema query for author table failed: '
                                   . $exception->getMessage(),
                ));

                return false;
            }

            // Add table of author
            $sql =<<<'EOD'
CREATE TABLE `{author_role}` (
    `id` int (10) unsigned NOT NULL auto_increment,
    `title` varchar (255) NOT NULL,
    `status` tinyint(1) unsigned NOT NULL default '1',
    PRIMARY KEY (`id`),
    KEY `status` (`status`),
    KEY `title` (`title`)
);
EOD;
            SqlSchema::setType($this->module);
            $sqlHandler = new SqlSchema;
            try {
                $sqlHandler->queryContent($sql);
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status'    => false,
                    'message'   => 'SQL schema query for author table failed: '
                                   . $exception->getMessage(),
                ));

                return false;
            }

            // Add table of author
            $sql =<<<'EOD'
CREATE TABLE `{author_story}` (
    `id` int (10) unsigned NOT NULL auto_increment,
    `story` int(10) unsigned NOT NULL,
    `author` int(10) unsigned NOT NULL,
    `role` int(10) unsigned NOT NULL,
    `time_publish` int(10) unsigned NOT NULL,
    `status` tinyint(1) unsigned NOT NULL default '1',
    PRIMARY KEY (`id`),
    KEY `story` (`story`),
    KEY `author` (`author`),
    KEY `role` (`role`),
    KEY `status` (`status`),
    KEY `time_publish` (`time_publish`),
    KEY `select` (`author`, `role`, `story`)
);
EOD;
            SqlSchema::setType($this->module);
            $sqlHandler = new SqlSchema;
            try {
                $sqlHandler->queryContent($sql);
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status'    => false,
                    'message'   => 'SQL schema query for author table failed: '
                                   . $exception->getMessage(),
                ));

                return false;
            }
        }

        // Update to version 1.2.7
        if (version_compare($moduleVersion, '1.2.7', '<')) {
            // Alter table field `type`
            $sql = sprintf("ALTER TABLE %s CHANGE `style` `style` 
                ENUM( 'news', 'list', 'table', 'media', 'spotlight', 'topic' ) NOT NULL", $topicTable);

            try {
                $topicAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status'    => false,
                    'message'   => 'Table alter query failed: '
                                   . $exception->getMessage(),
                ));
                return false;
            }
        }

        // Update to version 1.2.8
        if (version_compare($moduleVersion, '1.2.8', '<')) {
            // Alter table field `type`
            $sql = sprintf("ALTER TABLE %s ADD `author` 
                VARCHAR( 255 ) NOT NULL AFTER `topic` ", $storyTable);

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

        // Update to version 1.4.4
        if (version_compare($moduleVersion, '1.4.4', '<')) {

            // Alter table field `text_summary`
            $sql = sprintf("ALTER TABLE %s CHANGE `short` `text_summary` text", $storyTable);
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

            // Alter table field `text_description`
            $sql = sprintf("ALTER TABLE %s CHANGE `body` `text_description` text", $storyTable);
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

            // Alter table field `text_description`
            $sql = sprintf("ALTER TABLE %s CHANGE `description` `text_description` text", $topicTable);
            try {
                $topicAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status'    => false,
                    'message'   => 'Table alter query failed: '
                                   . $exception->getMessage(),
                ));
                return false;
            }

            // Alter table field `text_description`
            $sql = sprintf("ALTER TABLE %s CHANGE `description` `text_description` text", $authorTable);
            try {
                $authorAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status'    => false,
                    'message'   => 'Table alter query failed: '
                                   . $exception->getMessage(),
                ));
                return false;
            }
        }

        // Update to version 1.4.6
        if (version_compare($moduleVersion, '1.4.6', '<')) {

            // Alter table field add `topic_main`
            $sql = sprintf("ALTER TABLE %s ADD `topic_main` int(10) unsigned NOT NULL default '0' AFTER `topic`", $storyTable);
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
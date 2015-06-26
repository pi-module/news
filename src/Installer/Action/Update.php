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
use Zend\Json\Json;

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
        $moduleVersion = $e->getParam('version');

        // Set story model
        $storyModel = Pi::model('story', $this->module);
        $storyTable = $storyModel->getTable();
        $storyAdapter = $storyModel->getAdapter();

        // Set topic model
        $topicModel = Pi::model('topic', $this->module);
        $topicTable = $topicModel->getTable();
        $topicAdapter = $topicModel->getAdapter();

        // Set author model
        $authorModel = Pi::model('author', $this->module);
        $authorTable = $authorModel->getTable();
        $authorAdapter = $authorModel->getAdapter();

        // Set field model
        $fieldModel = Pi::model('field', $this->module);
        $fieldTable = $fieldModel->getTable();
        $fieldAdapter = $fieldModel->getAdapter();

        // Set attach model
        $attachModel = Pi::model('attach', $this->module);
        $attachTable = $attachModel->getTable();
        $attachAdapter = $attachModel->getAdapter();

        // Update to version 1.2.0
        if (version_compare($moduleVersion, '1.2.0', '<')) {
            // Alter table field `type`
            $sql = sprintf("ALTER TABLE %s ADD `type` ENUM( 'text', 'gallery', 'media', 'download' )
        		NOT NULL DEFAULT 'text'", $storyTable);
            try {
                $storyAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'Table alter query failed: '
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
                    'status' => false,
                    'message' => 'Table alter query failed: '
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
                    'status' => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ));
                return false;
            }
        }

        // Update to version 1.2.5
        if (version_compare($moduleVersion, '1.2.5', '<')) {
            // Add table of author
            $sql = <<<'EOD'
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
                    'status' => false,
                    'message' => 'SQL schema query for author table failed: '
                        . $exception->getMessage(),
                ));

                return false;
            }

            // Add table of author
            $sql = <<<'EOD'
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
                    'status' => false,
                    'message' => 'SQL schema query for author table failed: '
                        . $exception->getMessage(),
                ));

                return false;
            }

            // Add table of author
            $sql = <<<'EOD'
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
                    'status' => false,
                    'message' => 'SQL schema query for author table failed: '
                        . $exception->getMessage(),
                ));

                return false;
            }
        }

        // Update to version 1.2.7
        if (version_compare($moduleVersion, '1.2.7', '<')) {
            // Alter table field `type`
            $sql = sprintf("ALTER TABLE %s CHANGE `style` `style` ENUM( 'news', 'list', 'table', 'media', 'spotlight', 'topic' ) NOT NULL", $topicTable);
            try {
                $topicAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ));
                return false;
            }
        }

        // Update to version 1.2.8
        if (version_compare($moduleVersion, '1.2.8', '<')) {
            // Alter table field `type`
            $sql = sprintf("ALTER TABLE %s ADD `author` VARCHAR( 255 ) NOT NULL AFTER `topic` ", $storyTable);

            try {
                $storyAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'Table alter query failed: '
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
                    'status' => false,
                    'message' => 'Table alter query failed: '
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
                    'status' => false,
                    'message' => 'Table alter query failed: '
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
                    'status' => false,
                    'message' => 'Table alter query failed: '
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
                    'status' => false,
                    'message' => 'Table alter query failed: '
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
                    'status' => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ));
                return false;
            }
        }

        // Update to version 1.5.0
        if (version_compare($moduleVersion, '1.5.0', '<')) {
            // Alter table : DROP `image`
            $sql = sprintf("ALTER TABLE %s DROP `image`;", $fieldTable);
            try {
                $fieldAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ));
                return false;
            }
            // Alter table : CHANGE `type`
            $sql = sprintf("ALTER TABLE %s CHANGE `type` `type` ENUM('text', 'link', 'currency', 'date', 'number', 'select', 'video', 'audio', 'file', 'checkbox') NOT NULL DEFAULT 'text'", $fieldTable);
            try {
                $fieldAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ));
                return false;
            }
            // Alter table : ADD `icon`
            $sql = sprintf("ALTER TABLE %s ADD `icon` VARCHAR(32) NOT NULL DEFAULT ''", $fieldTable);
            try {
                $fieldAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ));
                return false;
            }
            // Alter table : ADD `name`
            $sql = sprintf("ALTER TABLE %s ADD `name` varchar(64) default NULL , ADD UNIQUE `name` (`name`)", $fieldTable);
            try {
                $fieldAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ));
                return false;
            }
            // Alter table : ADD `position`
            $sql = sprintf("ALTER TABLE %s ADD `position` INT(10) UNSIGNED NOT NULL DEFAULT '0'", $fieldTable);
            try {
                $fieldAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ));
                return false;
            }
            // Alter table : CHANGE `extra`
            $sql = sprintf("ALTER TABLE %s CHANGE `extra` `attribute` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0'", $storyTable);
            try {
                $storyAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ));
                return false;
            }

            // Add table of field_topic
            $sql = <<<'EOD'
CREATE TABLE `{field_topic}` (
  `id`    INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `field` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `topic` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `field` (`field`),
  KEY `topic` (`topic`),
  KEY `field_category` (`field`, `topic`)
);
EOD;
            SqlSchema::setType($this->module);
            $sqlHandler = new SqlSchema;
            try {
                $sqlHandler->queryContent($sql);
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'SQL schema query for author table failed: '
                        . $exception->getMessage(),
                ));
                return false;
            }

            // Add table of field_position
            $sql = <<<'EOD'
CREATE TABLE `{field_position}` (
  `id`     INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `title`  VARCHAR(255)        NOT NULL DEFAULT '',
  `order`  INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `order` (`order`),
  KEY `status` (`status`),
  KEY `order_status` (`order`, `status`)
);
EOD;
            SqlSchema::setType($this->module);
            $sqlHandler = new SqlSchema;
            try {
                $sqlHandler->queryContent($sql);
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'SQL schema query for author table failed: '
                        . $exception->getMessage(),
                ));
                return false;
            }
        }

        // Update to version 1.5.2
        if (version_compare($moduleVersion, '1.5.2', '<')) {
            // Alter table : CHANGE `extra`
            $sql = sprintf("ALTER TABLE %s CHANGE `story` `item_id` INT(10) UNSIGNED NOT NULL DEFAULT '0'", $attachTable);
            try {
                $attachAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ));
                return false;
            }
            // Alter table : CHANGE `extra`
            $sql = sprintf("ALTER TABLE %s CHANGE `type` `type` ENUM('archive', 'image', 'video', 'audio', 'pdf', 'doc', 'link', 'other') NOT NULL DEFAULT 'image'", $attachTable);
            try {
                $attachAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ));
                return false;
            }
            // Alter table : ADD item_table
            $sql = sprintf("ALTER TABLE %s ADD `item_table` ENUM('story', 'topic', 'author') NOT NULL DEFAULT 'story', ADD INDEX (`item_table`)", $attachTable);
            try {
                $attachAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ));
                return false;
            }
            // Alter table : ADD url
            $sql = sprintf("ALTER TABLE %s ADD `url` VARCHAR(255) NOT NULL DEFAULT ''", $attachTable);
            try {
                $attachAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ));
                return false;
            }
        }

        // Update to version 1.5.3
        if (version_compare($moduleVersion, '1.5.3', '<')) {
            // Update value
            $select = $topicModel->select();
            $rowset = $topicModel->selectWith($select);
            foreach ($rowset as $row) {
                $setting = Json::decode($row->setting, true);
                // Check attach
                if (!empty($setting['attach_title']) && !empty($setting['attach_link'])) {
                    // Set save array
                    $values['title'] = $setting['attach_title'];
                    $values['url'] = $setting['attach_link'];
                    $values['hits'] = $setting['attach_download_count'];
                    $values['item_id'] = $row->id;
                    $values['item_table'] = 'topic';
                    $values['time_create'] = time();
                    $values['type'] = 'link';
                    $values['status'] = 1;
                    // save in DB
                    $attach = $attachModel->createRow();
                    $attach->assign($values);
                    $attach->save();
                    // Set attach
                    $setting['attach'] = 1;
                } else {
                    $setting['attach'] = 0;
                }
                // Unset
                unset($setting['attach_title']);
                unset($setting['attach_link']);
                unset($setting['attach_download_count']);
                $setting = Json::encode($setting);
                // Save value
                $row->setting = $setting;
                $row->save();
            }
        }

        return true;
    }
}
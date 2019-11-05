<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
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
        $events->attach('update.pre', [$this, 'updateSchema']);
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
        $storyModel   = Pi::model('story', $this->module);
        $storyTable   = $storyModel->getTable();
        $storyAdapter = $storyModel->getAdapter();

        // Set topic model
        $topicModel   = Pi::model('topic', $this->module);
        $topicTable   = $topicModel->getTable();
        $topicAdapter = $topicModel->getAdapter();

        // Set author model
        $authorModel   = Pi::model('author', $this->module);
        $authorTable   = $authorModel->getTable();
        $authorAdapter = $authorModel->getAdapter();

        // Set field model
        $fieldModel   = Pi::model('field', $this->module);
        $fieldTable   = $fieldModel->getTable();
        $fieldAdapter = $fieldModel->getAdapter();

        // Set attach model
        $attachModel   = Pi::model('attach', $this->module);
        $attachTable   = $attachModel->getTable();
        $attachAdapter = $attachModel->getAdapter();

        // Set microblog model
        $microblogModel   = Pi::model('microblog', $this->module);
        $microblogTable   = $microblogModel->getTable();
        $microblogAdapter = $microblogModel->getAdapter();

        // Set link model
        $linkModel   = Pi::model('link', $this->module);
        $linkTable   = $linkModel->getTable();
        $linkAdapter = $linkModel->getAdapter();

        // Update to version 1.2.0
        if (version_compare($moduleVersion, '1.2.0', '<')) {
            // Alter table field `type`
            $sql = sprintf(
                "ALTER TABLE %s ADD `type` ENUM( 'text', 'gallery', 'media', 'download' )
        		NOT NULL DEFAULT 'text'", $storyTable
            );
            try {
                $storyAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
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
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }

            // Alter table field `body`
            $sql = sprintf("ALTER TABLE %s CHANGE `body` `body` mediumtext", $storyTable);
            try {
                $storyAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
        }

        // Update to version 1.2.5
        if (version_compare($moduleVersion, '1.2.5', '<')) {
            // Add table of author
            $sql
                = <<<'EOD'
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
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'SQL schema query for author table failed: '
                        . $exception->getMessage(),
                ]
                );

                return false;
            }

            // Add table of author
            $sql
                = <<<'EOD'
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
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'SQL schema query for author table failed: '
                        . $exception->getMessage(),
                ]
                );

                return false;
            }

            // Add table of author
            $sql
                = <<<'EOD'
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
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'SQL schema query for author table failed: '
                        . $exception->getMessage(),
                ]
                );

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
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
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
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
        }

        // Update to version 1.4.4
        if (version_compare($moduleVersion, '1.4.4', '<')) {
            // Alter table field `text_summary`
            $sql = sprintf("ALTER TABLE %s CHANGE `short` `text_summary` MEDIUMTEXT", $storyTable);
            try {
                $storyAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }

            // Alter table field `text_description`
            $sql = sprintf("ALTER TABLE %s CHANGE `body` `text_description` MEDIUMTEXT", $storyTable);
            try {
                $storyAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }

            // Alter table field `text_description`
            $sql = sprintf("ALTER TABLE %s CHANGE `description` `text_description` text", $topicTable);
            try {
                $topicAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }

            // Alter table field `text_description`
            $sql = sprintf("ALTER TABLE %s CHANGE `description` `text_description` text", $authorTable);
            try {
                $authorAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
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
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
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
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
            // Alter table : CHANGE `type`
            $sql = sprintf(
                "ALTER TABLE %s CHANGE `type` `type` ENUM('text', 'link', 'currency', 'date', 'number', 'select', 'video', 'audio', 'file', 'checkbox') NOT NULL DEFAULT 'text'",
                $fieldTable
            );
            try {
                $fieldAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
            // Alter table : ADD `icon`
            $sql = sprintf("ALTER TABLE %s ADD `icon` VARCHAR(32) NOT NULL DEFAULT ''", $fieldTable);
            try {
                $fieldAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
            // Alter table : ADD `name`
            $sql = sprintf("ALTER TABLE %s ADD `name` varchar(64) default NULL , ADD UNIQUE `name` (`name`)", $fieldTable);
            try {
                $fieldAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
            // Alter table : ADD `position`
            $sql = sprintf("ALTER TABLE %s ADD `position` INT(10) UNSIGNED NOT NULL DEFAULT '0'", $fieldTable);
            try {
                $fieldAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
            // Alter table : CHANGE `extra`
            $sql = sprintf("ALTER TABLE %s CHANGE `extra` `attribute` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0'", $storyTable);
            try {
                $storyAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }

            // Add table of field_topic
            $sql
                = <<<'EOD'
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
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'SQL schema query for author table failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }

            // Add table of field_position
            $sql
                = <<<'EOD'
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
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'SQL schema query for author table failed: '
                        . $exception->getMessage(),
                ]
                );
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
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
            // Alter table : CHANGE `extra`
            $sql = sprintf(
                "ALTER TABLE %s CHANGE `type` `type` ENUM('archive', 'image', 'video', 'audio', 'pdf', 'doc', 'link', 'other') NOT NULL DEFAULT 'image'",
                $attachTable
            );
            try {
                $attachAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
            // Alter table : ADD item_table
            $sql = sprintf("ALTER TABLE %s ADD `item_table` ENUM('story', 'topic', 'author') NOT NULL DEFAULT 'story', ADD INDEX (`item_table`)", $attachTable);
            try {
                $attachAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
            // Alter table : ADD url
            $sql = sprintf("ALTER TABLE %s ADD `url` VARCHAR(255) NOT NULL DEFAULT ''", $attachTable);
            try {
                $attachAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
        }

        // Update to version 1.5.3
        if (version_compare($moduleVersion, '1.5.3', '<')) {
            // Update value
            $select = $topicModel->select();
            $rowset = $topicModel->selectWith($select);
            foreach ($rowset as $row) {
                $setting = json_decode($row->setting, true);
                // Check attach
                if (!empty($setting['attach_title']) && !empty($setting['attach_link'])) {
                    // Set save array
                    $values['title']       = $setting['attach_title'];
                    $values['url']         = $setting['attach_link'];
                    $values['hits']        = $setting['attach_download_count'];
                    $values['item_id']     = $row->id;
                    $values['item_table']  = 'topic';
                    $values['time_create'] = time();
                    $values['type']        = 'link';
                    $values['status']      = 1;
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
                $setting = json_encode($setting);
                // Save value
                $row->setting = $setting;
                $row->save();
            }
        }

        // Update to version 1.6.4
        if (version_compare($moduleVersion, '1.6.4', '<')) {
            // Add table of microblog
            $sql
                = <<<'EOD'
CREATE TABLE `{microblog}` (
  `id`          INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `post`        TEXT,
  `status`      TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `uid`         INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `hits`        INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `time_create` INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `uid` (`uid`),
  KEY `time_create` (`time_create`),
  KEY `select` (`status`, `uid`)
);
EOD;
            SqlSchema::setType($this->module);
            $sqlHandler = new SqlSchema;
            try {
                $sqlHandler->queryContent($sql);
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'SQL schema query for author table failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }

            // Alter table : CHANGE `type`
            $sql = sprintf("ALTER TABLE %s CHANGE `type` `type` ENUM('text', 'gallery', 'media', 'download', 'image') NOT NULL DEFAULT 'text'", $storyTable);
            try {
                $storyAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
        }

        // Update to version 1.6.8
        if (version_compare($moduleVersion, '1.6.8', '<')) {
            // Alter table : ADD item_table
            $sql = sprintf("ALTER TABLE %s ADD `topic` INT(10) UNSIGNED NOT NULL DEFAULT '0', ADD INDEX (`topic`)", $microblogTable);
            try {
                $microblogAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
        }

        // Update to version 1.7.2
        if (version_compare($moduleVersion, '1.7.2', '<')) {
            // Alter table : ADD time_update
            $sql = sprintf("ALTER TABLE %s ADD `time_update` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `time_publish`", $linkTable);
            try {
                $linkAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
            // Update time_update
            $sql = sprintf("UPDATE %s SET `time_update` = `time_publish`", $linkTable);
            try {
                $linkAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
        }

        // Update to version 1.7.5
        if (version_compare($moduleVersion, '1.7.5', '<')) {
            // Alter table : ADD type
            $sql = sprintf(
                "ALTER TABLE %s ADD `type` ENUM('text', 'post', 'article', 'magazine', 'event', 'image', 'gallery', 'media', 'download', 'feed') NOT NULL DEFAULT 'text', ADD INDEX (`type`)",
                $linkTable
            );
            try {
                $linkAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
            // Alter table : ADD index
            $sql = sprintf("ALTER TABLE %s ADD INDEX `topic_list_type` (`status`, `topic`, `time_publish`, `type`)", $linkTable);
            try {
                $linkAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
            // Alter table : ADD type
            $sql = sprintf(
                "ALTER TABLE %s CHANGE `type` `type` ENUM('text', 'post', 'article', 'magazine', 'event', 'image', 'gallery', 'media', 'download', 'feed') NOT NULL DEFAULT 'text', ADD INDEX (`type`)",
                $storyTable
            );
            try {
                $storyAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
            // Alter table : ADD index
            $sql = sprintf("ALTER TABLE %s ADD INDEX `story_list_type` (`status`, `id`, `type`)", $storyTable);
            try {
                $storyAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
            // Update value
            $select = $linkModel->select();
            $rowset = $linkModel->selectWith($select);
            foreach ($rowset as $row) {
                $story     = Pi::api('story', 'news')->getStoryLight($row->story);
                $row->type = $story['type'];
                $row->save();
            }
        }

        // Update to version 1.7.6
        if (version_compare($moduleVersion, '1.7.6', '<')) {
            // Alter table : ADD
            $sql = sprintf("ALTER TABLE %s ADD `module` VARCHAR(16) NOT NULL DEFAULT 'news'", $linkTable);
            try {
                $linkAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
            // Alter table : ADD
            $sql = sprintf("ALTER TABLE %s ADD `controller` VARCHAR(16) NOT NULL DEFAULT 'topic'", $linkTable);
            try {
                $linkAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
            // Alter table : ADD index
            $sql = sprintf("ALTER TABLE %s ADD INDEX `topic_list_type_module` (`status`, `topic`, `time_publish`, `type`, `module`, `controller`)", $linkTable);
            try {
                $linkAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
        }

        // Update to version 1.7.9
        if (version_compare($moduleVersion, '1.7.9', '<')) {
            // Alter table : ADD text_summary
            $sql = sprintf("ALTER TABLE %s ADD `text_summary` TEXT", $topicTable);
            try {
                $topicAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
            // Alter table : ADD type
            $sql = sprintf("ALTER TABLE %s ADD `type` ENUM('general', 'event', 'blog') NOT NULL DEFAULT 'general', ADD INDEX `type` (`type`)", $topicTable);
            try {
                $topicAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
            // Alter table : ADD index
            $sql = sprintf("ALTER TABLE %s ADD INDEX `topic_list_type` (`status`, `pid`, `id`, `type`)", $topicTable);
            try {
                $topicAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
        }

        // Update to version 1.8.3
        if (version_compare($moduleVersion, '1.8.3', '<')) {
            // Alter table : ADD text_html
            $sql = sprintf("ALTER TABLE %s ADD `text_html` TEXT", $storyTable);
            try {
                $storyAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
        }

        if (version_compare($moduleVersion, '1.8.5', '<')) {
            $sql = sprintf("ALTER TABLE %s ADD `cropping` TEXT NULL DEFAULT NULL AFTER `path`", $storyTable);
            try {
                $storyAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
        }

        if (version_compare($moduleVersion, '1.8.6', '<')) {
            /* $sql = sprintf("ALTER TABLE %s ADD FULLTEXT `search_idx` (`title`, `text_description`);", $storyTable);
            try {
                $storyAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ));
                return false;
            } */
        }


        if (version_compare($moduleVersion, '2.0.0', '<')) {
            $sql = sprintf(
                "ALTER TABLE %s ADD `main_image` INT NULL AFTER `cropping`, ADD `additional_images` VARCHAR(255) NULL AFTER `main_image`;", $storyTable
            );
            try {
                $storyAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
        }

        if (version_compare($moduleVersion, '2.0.2', '<')) {
            /* $sql = sprintf("ALTER TABLE %s ADD FULLTEXT `search_title_idx` (`title`);", $storyTable);

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

            $sql = sprintf("ALTER TABLE %s ADD FULLTEXT `search_description_idx` (`text_description`);", $storyTable);

            try {
                $storyAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ));
                return false;
            } */
        }

        if (version_compare($moduleVersion, '2.0.4', '<')) {
            $query = "ALTER TABLE %s CHANGE `additional_images` `additional_images` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;";
            $sql   = sprintf($query, $storyTable);
            try {
                $storyAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
        }

        if (version_compare($moduleVersion, '2.0.5', '<')) {

            $sql
                = <<<SQL
ALTER TABLE `{story}` ADD FULLTEXT `search_seo_title_idx` (`seo_title`);
ALTER TABLE `{story}` ADD FULLTEXT `search_seo_keywords_idx` (`seo_keywords`);
ALTER TABLE `{story}` ADD FULLTEXT `search_seo_description_idx` (`seo_description`);
ALTER TABLE `{story}` ADD FULLTEXT `search_idx_2` (`title`, `text_description`, `seo_title`, `seo_keywords`, `seo_description`);
SQL;

            $sql = str_replace('{story}', $storyTable, $sql);

            try {
                $storyAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
        }

        if (version_compare($moduleVersion, '2.1.2', '<')) {
            $sql = sprintf("ALTER TABLE %s ADD `display_order` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `time_update`", $topicTable);
            try {
                $topicAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                        'status'  => false,
                        'message' => 'Table alter query failed: '
                            . $exception->getMessage(),
                    ]
                );
                return false;
            }
        }

        return true;
    }
}
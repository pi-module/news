CREATE TABLE `{story}` (
  `id`               INT(10) UNSIGNED                                                                                       NOT NULL AUTO_INCREMENT,
  `title`            VARCHAR(255)                                                                                           NOT NULL DEFAULT '',
  `subtitle`         VARCHAR(255)                                                                                           NOT NULL DEFAULT '',
  `slug`             VARCHAR(255)                                                                                           NOT NULL DEFAULT '',
  `topic`            VARCHAR(255)                                                                                           NOT NULL DEFAULT '',
  `topic_main`       INT(10) UNSIGNED                                                                                       NOT NULL DEFAULT '0',
  `author`           VARCHAR(255)                                                                                           NOT NULL DEFAULT '',
  `text_summary`     MEDIUMTEXT,
  `text_description` MEDIUMTEXT,
  `text_html`        TEXT,
  `seo_title`        VARCHAR(255)                                                                                           NOT NULL DEFAULT '',
  `seo_keywords`     VARCHAR(255)                                                                                           NOT NULL DEFAULT '',
  `seo_description`  VARCHAR(255)                                                                                           NOT NULL DEFAULT '',
  `important`        TINYINT(1) UNSIGNED                                                                                    NOT NULL DEFAULT '0',
  `status`           TINYINT(1) UNSIGNED                                                                                    NOT NULL DEFAULT '0',
  `time_create`      INT(10) UNSIGNED                                                                                       NOT NULL DEFAULT '0',
  `time_update`      INT(10) UNSIGNED                                                                                       NOT NULL DEFAULT '0',
  `time_publish`     INT(10) UNSIGNED                                                                                       NOT NULL DEFAULT '0',
  `uid`              INT(10) UNSIGNED                                                                                       NOT NULL DEFAULT '0',
  `hits`             INT(10) UNSIGNED                                                                                       NOT NULL DEFAULT '0',
  `image`            VARCHAR(255)                                                                                           NOT NULL DEFAULT '',
  `path`             VARCHAR(16)                                                                                            NOT NULL DEFAULT '',
  `cropping`         TEXT,
  `point`            INT(10)                                                                                                NOT NULL DEFAULT '0',
  `count`            INT(10) UNSIGNED                                                                                       NOT NULL DEFAULT '0',
  `favourite`        INT(10) UNSIGNED                                                                                       NOT NULL DEFAULT '0',
  `attach`           TINYINT(3) UNSIGNED                                                                                    NOT NULL DEFAULT '0',
  `attribute`        TINYINT(3) UNSIGNED                                                                                    NOT NULL DEFAULT '0',
  `type`             ENUM ('text', 'post', 'article', 'magazine', 'event', 'image', 'gallery', 'media', 'download', 'feed') NOT NULL DEFAULT 'text',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `title` (`title`),
  KEY `time_publish` (`time_publish`),
  KEY `status` (`status`),
  KEY `uid` (`uid`),
  KEY `type` (`type`),
  KEY `story_list` (`status`, `id`),
  KEY `story_list_type` (`status`, `id`, `type`),
  KEY `story_order` (`time_publish`, `id`)
);

ALTER TABLE `{story}` ADD FULLTEXT `search_idx` (`title`, `text_description`);
ALTER TABLE `{story}` ADD `main_image` INT NULL AFTER `cropping`, ADD `additional_images` TEXT NULL AFTER `main_image`;
ALTER TABLE `{story}` ADD FULLTEXT `search_title_idx` (`title`);
ALTER TABLE `{story}` ADD FULLTEXT `search_description_idx` (`text_description`);

CREATE TABLE `{topic}` (
  `id`               INT(10) UNSIGNED                                              NOT NULL AUTO_INCREMENT,
  `pid`              INT(5) UNSIGNED                                               NOT NULL DEFAULT '0',
  `title`            VARCHAR(255)                                                  NOT NULL DEFAULT '',
  `slug`             VARCHAR(255)                                                  NOT NULL DEFAULT '',
  `text_summary`     TEXT,
  `text_description` TEXT,
  `image`            VARCHAR(255)                                                  NOT NULL DEFAULT '',
  `path`             VARCHAR(16)                                                   NOT NULL DEFAULT '',
  `seo_title`        VARCHAR(255)                                                  NOT NULL DEFAULT '',
  `seo_keywords`     VARCHAR(255)                                                  NOT NULL DEFAULT '',
  `seo_description`  VARCHAR(255)                                                  NOT NULL DEFAULT '',
  `uid`              INT(10) UNSIGNED                                              NOT NULL DEFAULT '0',
  `time_create`      INT(10) UNSIGNED                                              NOT NULL DEFAULT '0',
  `time_update`      INT(10) UNSIGNED                                              NOT NULL DEFAULT '0',
  `setting`          TEXT,
  `status`           TINYINT(1) UNSIGNED                                           NOT NULL DEFAULT '0',
  `style`            ENUM ('news', 'list', 'table', 'media', 'spotlight', 'topic') NOT NULL DEFAULT 'news',
  `type`             ENUM ('general', 'event', 'blog')                             NOT NULL DEFAULT 'general',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `pid` (`pid`),
  KEY `title` (`title`),
  KEY `type` (`type`),
  KEY `time_create` (`time_create`),
  KEY `status` (`status`),
  KEY `topic_list` (`status`, `pid`, `id`),
  KEY `topic_list_type` (`status`, `pid`, `id`, `type`)
);

CREATE TABLE `{link}` (
  `id`           INT(10) UNSIGNED                                                                                       NOT NULL AUTO_INCREMENT,
  `story`        INT(10) UNSIGNED                                                                                       NOT NULL DEFAULT '0',
  `topic`        INT(10) UNSIGNED                                                                                       NOT NULL DEFAULT '0',
  `time_publish` INT(10) UNSIGNED                                                                                       NOT NULL DEFAULT '0',
  `time_update`  INT(10) UNSIGNED                                                                                       NOT NULL DEFAULT '0',
  `status`       TINYINT(1) UNSIGNED                                                                                    NOT NULL DEFAULT '0',
  `uid`          INT(10) UNSIGNED                                                                                       NOT NULL DEFAULT '0',
  `type`         ENUM ('text', 'post', 'article', 'magazine', 'event', 'image', 'gallery', 'media', 'download', 'feed') NOT NULL DEFAULT 'text',
  `module`       VARCHAR(16)                                                                                            NOT NULL DEFAULT 'news',
  `controller`   VARCHAR(16)                                                                                            NOT NULL DEFAULT 'topic',
  PRIMARY KEY (`id`),
  KEY `story` (`story`),
  KEY `topic` (`topic`),
  KEY `time_publish` (`time_publish`),
  KEY `status` (`status`),
  KEY `uid` (`uid`),
  KEY `type` (`type`),
  KEY `topic_list` (`status`, `topic`, `time_publish`),
  KEY `topic_list_type` (`status`, `topic`, `time_publish`, `type`),
  KEY `topic_list_type_module` (`status`, `topic`, `time_publish`, `type`, `module`, `controller`),
  KEY `uid_list` (`status`, `topic`, `time_publish`, `uid`),
  KEY `story_list` (`status`, `story`, `time_publish`, `topic`),
  KEY `link_order` (`time_publish`, `id`)
);

CREATE TABLE `{spotlight}` (
  `id`           INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `story`        INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `topic`        INT(10)             NOT NULL DEFAULT '0',
  `uid`          INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `time_publish` INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `time_expire`  INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `status`       TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `time_publish` (`time_publish`),
  KEY `time_expire` (`time_expire`),
  KEY `status` (`status`),
  KEY `topic` (`topic`),
  KEY `spotlight` (`status`, `time_publish`, `time_expire`, `topic`),
  KEY `link_order` (`id`, `time_publish`)
);

CREATE TABLE `{attach}` (
  `id`          INT(10) UNSIGNED                                                           NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(255)                                                               NOT NULL DEFAULT '',
  `file`        VARCHAR(255)                                                               NOT NULL DEFAULT '',
  `path`        VARCHAR(16)                                                                NOT NULL DEFAULT '',
  `url`         VARCHAR(255)                                                               NOT NULL DEFAULT '',
  `item_table`  ENUM ('story', 'topic', 'author')                                          NOT NULL DEFAULT 'story',
  `item_id`     INT(10) UNSIGNED                                                           NOT NULL DEFAULT '0',
  `time_create` INT(10) UNSIGNED                                                           NOT NULL DEFAULT '0',
  `size`        INT(10) UNSIGNED                                                           NOT NULL DEFAULT '0',
  `type`        ENUM ('archive', 'image', 'video', 'audio', 'pdf', 'doc', 'link', 'other') NOT NULL DEFAULT 'image',
  `status`      TINYINT(1) UNSIGNED                                                        NOT NULL DEFAULT '0',
  `hits`        INT(10) UNSIGNED                                                           NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `item_table` (`item_table`),
  KEY `item_id` (`item_id`),
  KEY `time_create` (`time_create`),
  KEY `type` (`type`),
  KEY `item_status` (`item_id`, `status`)
);

CREATE TABLE `{field}` (
  `id`       INT(10) UNSIGNED                                                                                    NOT NULL AUTO_INCREMENT,
  `title`    VARCHAR(255)                                                                                        NOT NULL DEFAULT '',
  `icon`     VARCHAR(32)                                                                                         NOT NULL DEFAULT '',
  `type`     ENUM ('text', 'link', 'currency', 'date', 'number', 'select', 'video', 'audio', 'file', 'checkbox') NOT NULL DEFAULT 'text',
  `order`    INT(10) UNSIGNED                                                                                    NOT NULL DEFAULT '0',
  `status`   TINYINT(1) UNSIGNED                                                                                 NOT NULL DEFAULT '0',
  `search`   TINYINT(1) UNSIGNED                                                                                 NOT NULL DEFAULT '0',
  `position` INT(10) UNSIGNED                                                                                    NOT NULL DEFAULT '0',
  `value`    TEXT,
  `name`     VARCHAR(64)                                                                                                  DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `title` (`title`),
  KEY `order` (`order`),
  KEY `status` (`status`),
  KEY `search` (`search`),
  KEY `order_status` (`order`, `status`)
);

CREATE TABLE `{field_topic}` (
  `id`    INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `field` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `topic` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `field` (`field`),
  KEY `topic` (`topic`),
  KEY `field_category` (`field`, `topic`)
);

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

CREATE TABLE `{field_data}` (
  `id`    INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `field` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `story` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `data`  VARCHAR(255)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `field` (`field`),
  KEY `story` (`story`),
  KEY `data` (`data`),
  KEY `field_story` (`field`, `story`)
);

CREATE TABLE `{author}` (
  `id`               INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `title`            VARCHAR(255)        NOT NULL DEFAULT '',
  `slug`             VARCHAR(255)        NOT NULL DEFAULT '',
  `text_description` TEXT,
  `seo_title`        VARCHAR(255)        NOT NULL DEFAULT '',
  `seo_keywords`     VARCHAR(255)        NOT NULL DEFAULT '',
  `seo_description`  VARCHAR(255)        NOT NULL DEFAULT '',
  `time_create`      INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `time_update`      INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `uid`              INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `hits`             INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `image`            VARCHAR(255)        NOT NULL DEFAULT '',
  `path`             VARCHAR(16)         NOT NULL DEFAULT '',
  `status`           TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `status` (`status`),
  KEY `title` (`title`),
  KEY `time_create` (`time_create`),
  KEY `order` (`title`, `id`)
);

CREATE TABLE `{author_role}` (
  `id`     INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `title`  VARCHAR(255)        NOT NULL DEFAULT '',
  `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `title` (`title`)
);

CREATE TABLE `{author_story}` (
  `id`           INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `story`        INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `author`       INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `role`         INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `time_publish` INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `status`       TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `story` (`story`),
  KEY `author` (`author`),
  KEY `role` (`role`),
  KEY `status` (`status`),
  KEY `time_publish` (`time_publish`),
  KEY `select` (`author`, `role`, `story`)
);

CREATE TABLE `{microblog}` (
  `id`          INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `post`        TEXT,
  `status`      TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `uid`         INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `hits`        INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `time_create` INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `topic`       INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `uid` (`uid`),
  KEY `topic` (`topic`),
  KEY `time_create` (`time_create`),
  KEY `select` (`status`, `uid`)
);
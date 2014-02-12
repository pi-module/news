CREATE TABLE `{story}` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `title` varchar(255) NOT NULL,
    `subtitle` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `topic` varchar(255) NOT NULL,
    `short` text,
    `body` text,
    `seo_title` varchar(255) NOT NULL,
    `seo_keywords` varchar(255) NOT NULL,
    `seo_description` varchar(255) NOT NULL,
    `important` tinyint(1) unsigned NOT NULL,
    `status` tinyint(1) unsigned NOT NULL,
    `time_create` int(10) unsigned NOT NULL,
    `time_update` int(10) unsigned NOT NULL,
    `time_publish` int(10) unsigned NOT NULL,
    `uid` int(10) unsigned NOT NULL,
    `hits` int(10) unsigned NOT NULL,
    `image` varchar(255) NOT NULL,
    `path` varchar(16) NOT NULL,
    `point` int(10) NOT NULL,
    `count` int(10) unsigned NOT NULL,
    `favorite` int(10) unsigned NOT NULL,
    `attach` tinyint(3) unsigned NOT NULL,
    `extra` tinyint(3) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `title` (`title`),
    KEY `time_publish` (`time_publish`),
    KEY `status` (`status`),
    KEY `uid` (`uid`),
    KEY `story_list` (`status`, `id`),
    KEY `story_order` (`time_publish`, `id`)
);

CREATE TABLE `{topic}` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `pid` int(5) unsigned NOT NULL,
    `title` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `description` text,
    `image` varchar(255) NOT NULL,
    `path` varchar(16) NOT NULL,
    `seo_title` varchar(255) NOT NULL,
    `seo_keywords` varchar(255) NOT NULL,
    `seo_description` varchar(255) NOT NULL,
    `uid` int(10) unsigned NOT NULL,
    `time_create` int(10) unsigned NOT NULL,
    `time_update` int(10) unsigned NOT NULL,
    `setting` text,
    `status` tinyint(1) unsigned NOT NULL,
    `style` enum('news','list','table','media','spotlight') NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `pid` (`pid`),
    KEY `title` (`title`),
    KEY `status` (`status`),
    KEY `topic_list` (`status`, `pid`, `id`)
);

CREATE TABLE `{link}` (
    `id` int (10) unsigned NOT NULL    auto_increment,
    `story` int(10) unsigned NOT NULL,
    `topic` int(10) unsigned NOT NULL,
    `time_publish` int(10) unsigned NOT NULL,
    `status` tinyint(1) unsigned NOT NULL,
    `uid` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `story` (`story`),
    KEY `topic` (`topic`),
    KEY `time_publish` (`time_publish`),
    KEY `status` (`status`),
    KEY `uid` (`uid`),
    KEY `topic_list` (`status`, `topic`, `time_publish`),
    KEY `uid_list` (`status`, `topic`, `time_publish`, `uid`),
    KEY `story_list` (`status`, `story`, `time_publish`, `topic`),
    KEY `link_order` (`time_publish`, `id`)
);

CREATE TABLE `{writer}` (
    `id` int(10) unsigned NOT NULL    auto_increment,
    `uid` int(10) unsigned NOT NULL,
    `count` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uid` (`uid`),
    KEY `count` (`count`)
);

CREATE TABLE `{spotlight}` (
    `id` int(10) unsigned NOT NULL    auto_increment,
    `story` int(10) unsigned NOT NULL,
    `topic` int(10) NOT NULL,
    `uid` int(10) unsigned NOT NULL,
    `time_publish` int(10) unsigned NOT NULL,
    `time_expire` int(10) unsigned NOT NULL,
    `status` tinyint(1) unsigned NOT NULL default '1',
    PRIMARY KEY (`id`),
    KEY `time_publish` (`time_publish`),
    KEY `time_expire` (`time_expire`),
    KEY `status` (`status`),
    KEY `topic` (`topic`),
    KEY `spotlight` (`status`, `time_publish`, `time_expire`, `topic`),
    KEY `link_order` (`id`, `time_publish`)
);

CREATE TABLE `{attach}` (
    `id` int (10) unsigned NOT NULL    auto_increment,
    `title` varchar (255) NOT NULL,
    `file` varchar (255) NOT NULL,
    `path` varchar(16) NOT NULL,
    `story` int(10) unsigned NOT NULL,
    `time_create` int(10) unsigned NOT NULL,
    `size` int(10) unsigned NOT NULL,
    `type` enum('archive','image','video','audio','pdf','doc','other') NOT NULL,
    `status` tinyint(1) unsigned NOT NULL,
    `hits` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `title` (`title`),
    KEY `story` (`story`),
    KEY `time_create` (`time_create`),
    KEY `type` (`type`),
    KEY `story_status` (`story`, `status`)
);

CREATE TABLE `{field}` (
    `id` int (10) unsigned NOT NULL auto_increment,
    `title` varchar (255) NOT NULL,
    `image` varchar (255) NOT NULL,
    `type` enum('text','link','currency','date','number','select','video','audio','file') NOT NULL,
    `order` int(10) unsigned NOT NULL,
    `status` tinyint(1) unsigned NOT NULL default '1',
    `search` tinyint(1) unsigned NOT NULL default '1',
    `value` text,
    PRIMARY KEY (`id`),
    KEY `title` (`title`),
    KEY `order` (`order`),
    KEY `status` (`status`),
    KEY `search` (`search`),
    KEY `order_status` (`order`, `status`)
);

CREATE TABLE `{field_data}` (
    `id` int (10) unsigned NOT NULL auto_increment,
    `field` int(10) unsigned NOT NULL,
    `story` int(10) unsigned NOT NULL,
    `data` varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `field` (`field`),
    KEY `story` (`story`),
    KEY `data` (`data`),
    KEY `field_story` (`field`, `story`)
);
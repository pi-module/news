CREATE TABLE `{story}` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `title` varchar(255) NOT NULL default '',
    `subtitle` varchar(255) NOT NULL default '',
    `slug` varchar(255) NOT NULL default '',
    `topic` varchar(255) NOT NULL default '',
    `topic_main` int(10) unsigned NOT NULL default '0',
    `author` varchar(255) NOT NULL default '',
    `text_summary` mediumtext,
    `text_description` mediumtext,
    `seo_title` varchar(255) NOT NULL default '',
    `seo_keywords` varchar(255) NOT NULL default '',
    `seo_description` varchar(255) NOT NULL default '',
    `important` tinyint(1) unsigned NOT NULL default '0',
    `status` tinyint(1) unsigned NOT NULL default '0',
    `time_create` int(10) unsigned NOT NULL default '0',
    `time_update` int(10) unsigned NOT NULL default '0',
    `time_publish` int(10) unsigned NOT NULL default '0',
    `uid` int(10) unsigned NOT NULL default '0',
    `hits` int(10) unsigned NOT NULL default '0',
    `image` varchar(255) NOT NULL default '',
    `path` varchar(16) NOT NULL default '',
    `point` int(10) NOT NULL default '0',
    `count` int(10) unsigned NOT NULL default '0',
    `favourite` int(10) unsigned NOT NULL default '0',
    `attach` tinyint(3) unsigned NOT NULL default '0',
    `extra` tinyint(3) unsigned NOT NULL default '0',
    `type` enum('text','gallery','media','download') NOT NULL default 'text',
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
    `pid` int(5) unsigned NOT NULL default '0',
    `title` varchar(255) NOT NULL default '',
    `slug` varchar(255) NOT NULL default '',
    `text_description` text,
    `image` varchar(255) NOT NULL default '',
    `path` varchar(16) NOT NULL default '',
    `seo_title` varchar(255) NOT NULL default '',
    `seo_keywords` varchar(255) NOT NULL default '',
    `seo_description` varchar(255) NOT NULL default '',
    `uid` int(10) unsigned NOT NULL default '0',
    `time_create` int(10) unsigned NOT NULL default '0',
    `time_update` int(10) unsigned NOT NULL default '0',
    `setting` text,
    `status` tinyint(1) unsigned NOT NULL default '0',
    `style` enum('news','list','table','media','spotlight','topic') NOT NULL default 'news',
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `pid` (`pid`),
    KEY `title` (`title`),
    KEY `time_create` (`time_create`),
    KEY `status` (`status`),
    KEY `topic_list` (`status`, `pid`, `id`)
);

CREATE TABLE `{link}` (
    `id` int (10) unsigned NOT NULL auto_increment,
    `story` int(10) unsigned NOT NULL default '0',
    `topic` int(10) unsigned NOT NULL default '0',
    `time_publish` int(10) unsigned NOT NULL default '0',
    `status` tinyint(1) unsigned NOT NULL default '0',
    `uid` int(10) unsigned NOT NULL default '0',
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

CREATE TABLE `{spotlight}` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `story` int(10) unsigned NOT NULL default '0',
    `topic` int(10) NOT NULL default '0',
    `uid` int(10) unsigned NOT NULL default '0',
    `time_publish` int(10) unsigned NOT NULL default '0',
    `time_expire` int(10) unsigned NOT NULL default '0',
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
    `id` int (10) unsigned NOT NULL auto_increment,
    `title` varchar(255) NOT NULL default '',
    `file` varchar(255) NOT NULL default '',
    `path` varchar(16) NOT NULL default '',
    `story` int(10) unsigned NOT NULL default '0',
    `time_create` int(10) unsigned NOT NULL default '0',
    `size` int(10) unsigned NOT NULL default '0',
    `type` enum('archive','image','video','audio','pdf','doc','other') NOT NULL default 'image',
    `status` tinyint(1) unsigned NOT NULL default '0',
    `hits` int(10) unsigned NOT NULL default '0',
    PRIMARY KEY (`id`),
    KEY `title` (`title`),
    KEY `story` (`story`),
    KEY `time_create` (`time_create`),
    KEY `type` (`type`),
    KEY `story_status` (`story`, `status`)
);

CREATE TABLE `{field}` (
    `id` int (10) unsigned NOT NULL auto_increment,
    `title` varchar(255) NOT NULL default '',
    `image` varchar(255) NOT NULL default '',
    `type` enum('text','link','currency','date','number','select','video','audio','file') NOT NULL default 'text',
    `order` int(10) unsigned NOT NULL default '0',
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
    `field` int(10) unsigned NOT NULL default '0',
    `story` int(10) unsigned NOT NULL default '0',
    `data` varchar(255) NOT NULL default '',
    PRIMARY KEY (`id`),
    KEY `field` (`field`),
    KEY `story` (`story`),
    KEY `data` (`data`),
    KEY `field_story` (`field`, `story`)
);

CREATE TABLE `{author}` (
    `id` int (10) unsigned NOT NULL auto_increment,
    `title` varchar(255) NOT NULL default '',
    `slug` varchar(255) NOT NULL default '',
    `text_description` text,
    `seo_title` varchar(255) NOT NULL default '',
    `seo_keywords` varchar(255) NOT NULL default '',
    `seo_description` varchar(255) NOT NULL default '',
    `time_create` int(10) unsigned NOT NULL default '0',
    `time_update` int(10) unsigned NOT NULL default '0',
    `uid` int(10) unsigned NOT NULL default '0',
    `hits` int(10) unsigned NOT NULL default '0',
    `image` varchar(255) NOT NULL default '',
    `path` varchar(16) NOT NULL default '',
    `status` tinyint(1) unsigned NOT NULL default '1',
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `status` (`status`),
    KEY `title` (`title`),
    KEY `time_create` (`time_create`),
    KEY `order` (`title`, `id`)
);

CREATE TABLE `{author_role}` (
    `id` int (10) unsigned NOT NULL auto_increment,
    `title` varchar(255) NOT NULL default '',
    `status` tinyint(1) unsigned NOT NULL default '1',
    PRIMARY KEY (`id`),
    KEY `status` (`status`),
    KEY `title` (`title`)
);

CREATE TABLE `{author_story}` (
    `id` int (10) unsigned NOT NULL auto_increment,
    `story` int(10) unsigned NOT NULL default '0',
    `author` int(10) unsigned NOT NULL default '0',
    `role` int(10) unsigned NOT NULL default '0',
    `time_publish` int(10) unsigned NOT NULL default '0',
    `status` tinyint(1) unsigned NOT NULL default '1',
    PRIMARY KEY (`id`),
    KEY `story` (`story`),
    KEY `author` (`author`),
    KEY `role` (`role`),
    KEY `status` (`status`),
    KEY `time_publish` (`time_publish`),
    KEY `select` (`author`, `role`, `story`)
);
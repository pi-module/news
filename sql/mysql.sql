CREATE TABLE `{story}` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `topic` varchar(255) NOT NULL,
  `short` text,
  `body` text,
  `keywords` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `important` tinyint(1) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `create` int(10) unsigned NOT NULL,
  `update` int(10) unsigned NOT NULL,
  `publish` int(10) unsigned NOT NULL,
  `author` int(10) unsigned NOT NULL,
  `hits` int(10) unsigned NOT NULL,
  `image` varchar(255) NOT NULL,
  `path` varchar(16) NOT NULL,
  `comments` int(10) unsigned NOT NULL,
  `point` int(10) NOT NULL,
  `count` int(10) unsigned NOT NULL,
  `favorite` int(10) unsigned NOT NULL,
  `attach` tinyint(3) unsigned NOT NULL,
  `extra` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`),
  KEY `title` (`title`),
  KEY `publish` (`publish`),
  KEY `status` (`status`),
  KEY `author` (`author`),
  KEY `story_list` (`status`, `id`),
  KEY `story_order` (`publish`, `id`)
);

CREATE TABLE `{topic}` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(5) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `body` text,
  `image` varchar(255) NOT NULL,
  `path` varchar(16) NOT NULL,
  `keywords` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `author` int(10) unsigned NOT NULL,
  `create` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `inlist` tinyint(1) unsigned NOT NULL default '1',
  `topic_type` enum('module','topic') NOT NULL,
  `topic_homepage` enum('type1','type2','type3') NOT NULL,
  `topic_style` varchar(64) NOT NULL,
  `perpage` tinyint(3) unsigned NOT NULL,
  `columns` tinyint(3) unsigned NOT NULL,
  `showtopic` tinyint(1) unsigned NOT NULL default '1',
  `showtopicinfo` tinyint(1) unsigned NOT NULL default '1',
  `showauthor` tinyint(1) unsigned NOT NULL default '1',
  `showdate` tinyint(1) unsigned NOT NULL default '1',
  `showpdf` tinyint(1) unsigned NOT NULL default '1',
  `showprint` tinyint(1) unsigned NOT NULL default '1',
  `showmail` tinyint(1) unsigned NOT NULL default '1',
  `shownav` tinyint(1) unsigned NOT NULL default '1',
  `showhits` tinyint(1) unsigned NOT NULL default '1',
  `showcoms` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`),
  KEY `pid` (`pid`),
  KEY `title` (`title`),
  KEY `status` (`status`),
  KEY `inlist` (`inlist`),
  KEY `status_inlist` (`status`, `inlist`),
  KEY `topic_list` (`status`, `inlist`, `pid`, `id`)
);

CREATE TABLE `{writer}` (
  `id` int(10) unsigned NOT NULL  auto_increment,
  `author` int(10) unsigned NOT NULL,
  `count` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `author` (`author`),
  KEY `count` (`count`)
);

CREATE TABLE `{moderator}` (
  `id` int(10) unsigned NOT NULL  auto_increment,
  `manager` int(10) unsigned NOT NULL,
  `topic` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `manager` (`manager`),
  KEY `topic` (`topic`)
);

CREATE TABLE `{spotlight}` (
  `id` int(10) unsigned NOT NULL  auto_increment,
  `story` int(10) unsigned NOT NULL,
  `topic` int(10) NOT NULL,
  `author` int(10) unsigned NOT NULL,
  `publish` int(10) unsigned NOT NULL,
  `expire` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY (`id`),
  KEY `publish` (`publish`),
  KEY `expire` (`expire`),
  KEY `status` (`status`),
  KEY `topic` (`topic`),
  KEY `spotlight` (`status`, `publish`, `expire`, `topic`),
  KEY `link_order` (`id`, `publish`)
);

CREATE TABLE `{attach}` (
  `id` int (10) unsigned NOT NULL  auto_increment,
  `title` varchar (255) NOT NULL,
  `file` varchar (255) NOT NULL,
  `path` varchar(16) NOT NULL,
  `story` int(10) unsigned NOT NULL,
  `create` int(10) unsigned NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `type` enum('archive','image','video','audio','pdf','doc','other') NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `hits` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `story` (`story`),
  KEY `create` (`create`),
  KEY `type` (`type`),
  KEY `story_status` (`story`, `status`)
);

CREATE TABLE `{field}` (
  `id` int (10) unsigned NOT NULL  auto_increment,
  `title` varchar (255) NOT NULL,
  `image` varchar (255) NOT NULL,
  `type` enum('text','link','currency','date','number') NOT NULL,
  `order` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL default '1',
  `search` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `order` (`order`),
  KEY `status` (`status`),
  KEY `search` (`search`),
  KEY `order_status` (`order`, `status`)
);

CREATE TABLE `{data}` (
  `id` int (10) unsigned NOT NULL  auto_increment,
  `field` int(10) unsigned NOT NULL,
  `story` int(10) unsigned NOT NULL,
  `data` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `field` (`field`),
  KEY `story` (`story`),
  KEY `data` (`data`),
  KEY `field_story` (`field`, `story`)
);

CREATE TABLE `{link}` (
  `id` int (10) unsigned NOT NULL  auto_increment,
  `story` int(10) unsigned NOT NULL,
  `topic` int(10) unsigned NOT NULL,
  `publish` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `author` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `story` (`story`),
  KEY `topic` (`topic`),
  KEY `publish` (`publish`),
  KEY `status` (`status`),
  KEY `author` (`author`),
  KEY `topic_list` (`status`, `topic`, `publish`),
  KEY `author_list` (`status`, `topic`, `publish`, `author`),
  KEY `story_list` (`status`, `story`, `publish`, `topic`),
  KEY `link_order` (`publish`, `id`)
);
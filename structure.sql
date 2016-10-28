DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `website` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL DEFAULT 'World',
  `avatar` int(255) unsigned NOT NULL,
  `gravatar` tinyint(1) NOT NULL DEFAULT '1',
  `timezone` varchar(100) NOT NULL DEFAULT 'GMT',
  `public_email` enum('0','1') NOT NULL DEFAULT '0',
  `group` smallint(127) unsigned NOT NULL DEFAULT '2',
  `last_ip` varchar(15) NOT NULL,
  `last_time` datetime NOT NULL,
  `regdate` date NOT NULL,
  `hash` varchar(255) NOT NULL,
  `permissions` varchar(255) NOT NULL,
  `recover_hash` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=InnoDB AUTO_INCREMENT=2104 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attempts` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(255) unsigned NOT NULL,
  `challenge_id` int(255) unsigned NOT NULL,
  `bestattempt` tinyint(1) DEFAULT NULL,
  `time` datetime NOT NULL COMMENT 'Uploaded',
  `code` mediumblob NOT NULL,
  `input` longtext,
  `valid` longtext,
  `result` longtext,
  `errors` longtext,
  `size` smallint(127) unsigned NOT NULL,
  `executed` tinyint(1) NOT NULL DEFAULT '0',
  `passed` tinyint(1) NOT NULL DEFAULT '0',
  `locked` tinyint(1) NOT NULL COMMENT 'If true, this attempt will not revalidate',
  `version` smallint(127) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `challenge_id` (`challenge_id`),
  KEY `user_id` (`user_id`),
  KEY `time` (`time`),
  KEY `passed` (`passed`),
  KEY `executed` (`executed`),
  KEY `size` (`size`),
  CONSTRAINT `attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20100 DEFAULT CHARSET=utf8 COMMENT='Table for challenge attempts';
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `challenges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `challenges` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `instructions` text NOT NULL,
  `active` tinyint(4) NOT NULL,
  `open` tinyint(1) NOT NULL,
  `enddate` date DEFAULT NULL,
  `type` enum('public','protected','private') NOT NULL,
  `output_type` enum('static','variable') NOT NULL COMMENT 'What kind of output is showed',
  `trim_type` tinyint(3) NOT NULL COMMENT ' 0:no-trim,1:rtrim,2:ltrim,3:trim',
  `disabled_func` varchar(255) NOT NULL COMMENT 'Functions that is disabled',
  `constant` varchar(255) NOT NULL,
  `input` longtext NOT NULL COMMENT 'example input',
  `output` longtext NOT NULL COMMENT 'example output',
  `engine` longtext NOT NULL COMMENT 'Create solution and input',
  `topic_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `challenges_rating`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `challenges_rating` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `challenge_id` int(255) unsigned NOT NULL,
  `user_id` int(255) unsigned DEFAULT NULL,
  `direction` tinyint(1) NOT NULL COMMENT 'true = up, false = down',
  PRIMARY KEY (`id`),
  KEY `challenge_id` (`challenge_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `challenges_rating_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1295 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `forum_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_categories` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `category` varchar(255) NOT NULL,
  `discription` varchar(255) NOT NULL,
  `locked` tinyint(4) NOT NULL COMMENT 'set to 1 if the cat is not open for new topics',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `forum_replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_replies` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `category_id` int(10) NOT NULL,
  `topic_id` int(10) NOT NULL,
  `text` text NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `forum_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_topics` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `category_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `topic` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `permissions` varchar(255) NOT NULL COMMENT 'Permission id seperated by ;',
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `md5` varchar(128) NOT NULL,
  `name` varchar(255) NOT NULL,
  `format` varchar(10) NOT NULL,
  `uploaded` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logins` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(255) unsigned NOT NULL,
  `logintime` datetime NOT NULL,
  `ipa` int(4) unsigned NOT NULL COMMENT 'INET_ATON() to set, NET_NTOA() to get',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16945 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `author` int(255) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `post` longtext NOT NULL,
  `challenge` int(255) unsigned NOT NULL COMMENT 'Challenge ID to be related with',
  `twitter` varchar(255) NOT NULL COMMENT 'Twitter id',
  `bitly` varchar(50) NOT NULL COMMENT 'bit.ly url',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `online_users_peak`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `online_users_peak` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `users_online` int(10) NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` smallint(127) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `key` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


DROP TABLE IF EXISTS `xforum_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xforum_categories` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `dom_id` int(255) unsigned DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `user_level` int(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dom_id` (`dom_id`),
  CONSTRAINT `xforum_categories_ibfk_1` FOREIGN KEY (`dom_id`) REFERENCES `xforum_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `xforum_replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xforum_replies` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `topic_id` int(255) unsigned DEFAULT NULL,
  `cat_id` int(255) unsigned NOT NULL,
  `user_id` int(255) unsigned DEFAULT NULL,
  `editor_id` int(255) unsigned DEFAULT NULL,
  `posted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `edited` timestamp NULL DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `message` mediumblob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cat_id` (`cat_id`),
  KEY `topic_id` (`topic_id`),
  KEY `user_id` (`user_id`),
  KEY `editor_id` (`editor_id`),
  CONSTRAINT `xforum_replies_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `xforum_replies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `xforum_replies_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `xforum_replies_ibfk_4` FOREIGN KEY (`editor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `xforum_replies_ibfk_5` FOREIGN KEY (`cat_id`) REFERENCES `xforum_categories` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
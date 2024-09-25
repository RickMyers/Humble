DROP TABLE IF EXISTS `blog_whatsnew`;

CREATE TABLE `blog_whatsnew` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` char(10) DEFAULT NULL,
  `title` char(128) DEFAULT NULL,
  `article` text,
  `author` int(11) DEFAULT NULL,
  `active` char(1) DEFAULT 'N',
  `published` date DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `blog_whatsnew_code` */

DROP TABLE IF EXISTS `blog_whatsnew_code`;

CREATE TABLE `blog_whatsnew_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) DEFAULT NULL,
  `language_id` int(11) DEFAULT NULL,
  `lexicon_id` int(11) DEFAULT NULL,
  `sample` char(255) DEFAULT NULL,
  `scroll` char(16) DEFAULT '100%',
  `height` char(16) DEFAULT '200px',
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `blog_whatsnew_languages` */

DROP TABLE IF EXISTS `blog_whatsnew_languages`;

CREATE TABLE `blog_whatsnew_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` char(32) DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `blog_whatsnew_lexicons` */

DROP TABLE IF EXISTS `blog_whatsnew_lexicons`;

CREATE TABLE `blog_whatsnew_lexicons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lexicon` char(255) DEFAULT NULL,
  `description` char(255) DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `consultation_forms` */
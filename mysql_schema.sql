CREATE TABLE `articles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `brief` text,
  `last_retrieved` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`),
  KEY `last_retrieved` (`last_retrieved`)
) ENGINE=InnoDB AUTO_INCREMENT=148035 DEFAULT CHARSET=latin1;

CREATE TABLE `books` (
  `isbn` bigint(13) unsigned zerofill NOT NULL,
  `asin` int(10) unsigned zerofill DEFAULT NULL,
  `last_retrieved` datetime DEFAULT NULL,
  `url` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`isbn`),
  UNIQUE KEY `asin` (`asin`),
  KEY `last_retrieved` (`last_retrieved`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `book_references` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `context` text NOT NULL,
  `books_isbn` int(11) NOT NULL,
  `articles_id` int(11) NOT NULL,
  `last_retrieved` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `books_isbn` (`books_isbn`),
  KEY `articles_id` (`articles_id`),
  KEY `last_retrieved` (`last_retrieved`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `date_references` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `sentence` text NOT NULL,
  `paragraph` text NOT NULL,
  `last_retrieved` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `last_retrieved` (`last_retrieved`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
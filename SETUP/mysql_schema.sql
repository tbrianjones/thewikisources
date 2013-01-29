# ************************************************************
# Sequel Pro SQL dump
# Version 3408
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: wikipedia.cw0tm7tgwtd4.us-east-1.rds.amazonaws.com (MySQL 5.5.27)
# Database: wikipedia
# Generation Time: 2013-01-29 03:27:45 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table articles
# ------------------------------------------------------------

CREATE TABLE `articles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `brief` text,
  `image_page_url` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `love` tinyint(1) unsigned DEFAULT NULL,
  `hate` tinyint(1) unsigned DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `love` (`love`),
  KEY `hate` (`hate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table books
# ------------------------------------------------------------

CREATE TABLE `books` (
  `isbn_13` bigint(13) unsigned zerofill NOT NULL,
  `google_book_id` varchar(25) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`isbn_13`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table books_to_categories
# ------------------------------------------------------------

CREATE TABLE `books_to_categories` (
  `book_isbn_13` bigint(13) unsigned zerofill NOT NULL,
  `category` varchar(255) NOT NULL DEFAULT '',
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `book_isbn_to_category` (`book_isbn_13`,`category`),
  KEY `book_isbn` (`book_isbn_13`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table books_to_references
# ------------------------------------------------------------

CREATE TABLE `books_to_references` (
  `book_isbn_13` bigint(13) unsigned zerofill NOT NULL DEFAULT '0000000000000',
  `reference_id` int(11) NOT NULL DEFAULT '0',
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `book_isbn_and_reference_id` (`book_isbn_13`,`reference_id`),
  KEY `book_isbn` (`book_isbn_13`),
  KEY `reference_id` (`reference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table events
# ------------------------------------------------------------

CREATE TABLE `events` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `year` smallint(5) unsigned DEFAULT NULL,
  `context` text,
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table references
# ------------------------------------------------------------

CREATE TABLE `references` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(11) DEFAULT NULL,
  `reference_html` text,
  `context_html` text,
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `articles_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

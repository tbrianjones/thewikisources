SETUP WikiSources.com
=====================


linux setup
------------
-install
	- httpd
	- php
	- php-mysql



mysql setup
-----------

### from scratch
- create database with the `mysql_schema.sql` file ( not a functional file yet. just contains the schema )
	- everything should be utf-8 encoded ( default encoding and all fields )
- run `import_wiki_titles.php` ( see instructions at top of this file )

### from database backup
- launch an rds instance using the `only-contains-all-article-titles` db snapshot in tbj's aws account
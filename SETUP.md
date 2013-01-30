SETUP WikiSources.com
=====================


linux setup
------------
-install
	- httpd
		- update httpd.conf to allow htaccess file to override apache conf
		- `sudo vim /etc/httpd/conf/httpd.conf`
		- make `AllowOveride = All` inside the `<Directory "/var/www/html"` conf section
	- php
	- php-mysql
	- php-pecl-xdebug
		- update /etc/php.ini to `html_error = On`
		- start ( or restart ) httpd
	- php-xml
	- git
	

mysql setup
-----------

### from scratch
- create database with the `mysql_schema.sql` file ( not a functional file yet. just contains the schema )
	- everything should be utf-8 encoded ( default encoding and all fields )
- run `import_wiki_titles.php` ( see instructions at top of this file )

### from database backup
- launch an rds instance using the `only-contains-all-article-titles` db snapshot in tbj's aws account


elasticsearch setup
-------------------
- boot up an instance running the 'wikicortex elasticsearch node'
- create indexes
	- run `sh SETUP/create_elasticsearch_events_mapping.sh` to configure our events index
	- run `sh SETUP/create_elasticsearch_books_mapping.sh` to configure our books index
- events data is automatically pushed to these indexes when we run wiki_article_model.php
	- relevant data is also deleted every time we rerun an article
- books data must be pushed all at once by running `application/data_processors/push_books_to_elasticsearch.php`
	- shouldn't have to create a fresh index to do this, but may want to.  books will simply be versioned if they exist already
	- this needs to be run whenever we want to update the books in the system
	- we can kind of keep this efficient by only pushing books that have been modified since the last update
		- this will miss references to old books that have been added though ... and some other stuff
		- creating a new books index is the best option

	
setting up app after checking it out
-------------

### data processors
- copy `application/data_processors/config.php.example` to `application/data_processors/config.php` and update your info

### web app / codeigniter
- check stuff in configs ... need to add more notes here!!!

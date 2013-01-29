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
- run `sh SETUP/create_elasticsearch_mapping.sh` to configure our index
- add data ... not working yet
<?php

	// imports all wiki titles from a wiki dump file located at $file_path
	//
	//	1) download the latest wiki dump of titles: http://download.wikimedia.org/enwiki/latest/enwiki-latest-all-titles-in-ns0.gz
	//	2) extract it
	//	3) set $file_path ( below ) to the location of the extracted dump
	//	4) check database settings below
	//	5) execute this file
	//

	// applciation settings
	$file_path = '/home/ec2-user/enwiki-latest-all-titles-in-ns0';

	// database settings
	$db_host = 'wikipedia.cw0tm7tgwtd4.us-east-1.rds.amazonaws.com';
	$db_user = 'jones';
	$db_pass = 'zMfZdhce';
	$db_name = 'wikipedia';
	
	// connect to mysql
	$Mysqli = new mysqli( $db_host, $db_user, $db_pass, $db_name );
	if( $Mysqli->connect_errno ) {
	    printf("Connect failed: %s\n", $mysqli->connect_error);
    	exit();
    }
    
	// process file with article names
	$i = 0;
	$file_handle = fopen( $file_path, "r" );
	while( ! feof( $file_handle ) )
	{
		
		// get next line
		$title = trim( fgets( $file_handle ) );
		
		// process title into db
		if( $title != 'page_title' ) {
		
			$i++;
			echo "\n  - $i) Adding '$title' to '$db_name' database";
		
			$sql = "INSERT INTO articles( title )
					VALUES( '$title' )";
			$Query = $Mysqli->query( $sql );
			
		}
			
	}
	fclose( $file_handle );
	
?>
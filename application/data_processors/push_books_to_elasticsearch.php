<?php


	// push all books to elasticsearch
	//
	//	- do not need to create a fresh index
	//	- this will simple version each book and add new data to them
	//
	//	- if you want to create a fresh index, run SETUP/create_elasticsearch_books_mapping.sh
	//
	
	echo "\n\n -- adding books to elasticsearch\n";
	ini_set('memory_limit','64M');
	
	// include configuration ( you must update config.example with passwords when checking this repo out the first time )
	require( 'config.php');
	
	// connect to mysql
	$Mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );
	$Mysqli->set_charset( 'utf8' );
	
	// get all books
	$books = array();
	$sql = "SELECT isbn_13, title, subtitle
			FROM books";
	$Response = $Mysqli->query( $sql );
	if( ! $Response )
		die( $Mysqli->error );
	if( $Response->num_rows > 0 ) {
		while( $Row = $Response->fetch_object() ) {
			$book['isbn_13']	= $Row->isbn_13;
			$book['title']		= $Row->title;
			$book['subtitle']	= $Row->subtitle;
			$books[] = $book;
		}
	} else {
		die( 'no books found' );
	}
	
	// get related book data from other tables
	$i = 0;
	$count = count( $books );
	foreach( $books as $book )
	{
		
		$i++;
		echo "\n  - $i of $count) processing book isbn_13: " . $book['isbn_13'];
		
		// get book categories
		$categories = array();
		$sql = "SELECT category
				FROM books_to_categories
				WHERE book_isbn_13 = " . $book['isbn_13'];
		$Response = $Mysqli->query( $sql );
		if( ! $Response )
			die( $Mysqli->error );
		if( $Response->num_rows > 0 ) {
			while( $Row = $Response->fetch_object() )
				$categories[] = $Row->category;
		}
		
		// get book references
		$reference_ids = array();
		$sql = "SELECT reference_id
				FROM books_to_references
				WHERE book_isbn_13 = " . $book['isbn_13'];
		$Response = $Mysqli->query( $sql );
		if( ! $Response )
			die( $Mysqli->error );
		if( $Response->num_rows > 0 ) {
			while( $Row = $Response->fetch_object() )
				$reference_ids[] = $Row->reference_id;
		}
		
		// get reference data
		$references = array();
		foreach( $reference_ids as $reference_id ) {
			$sql = "SELECT id, context_html
					FROM `references`
					WHERE id = $reference_id";
			$Response = $Mysqli->query( $sql );
			if( ! $Response )
				die( $Mysqli->error );
			if( $Response->num_rows > 0 ) {
				while( $Row = $Response->fetch_object() ) {
					$reference['id'] = $Row->id;
					$reference['context'] = strip_tags( $Row->context_html );
				}
			}
			$references[] = $reference;
		}
		
		// push book to elasticsearch
		$book['categories'] = $categories;
		$book['references'] = $references;
		$book_json = json_encode( $book, JSON_HEX_APOS );
		$cmd = "curl -s -S -XPUT '" . ES_HOST . "books/book/" . $book['isbn_13'] . "' -d '" . $book_json . "'";
		$response = shell_exec( $cmd );
		$Response = json_decode( $response );
		if( ! isset( $Response ) )
			throw new Exception( "Elasticsearch Error: request to Elasticsearch was malformed.\nresponse: " . $response );			
		if( isset( $Response->error ) )
			throw new Exception( "Elasticsearch Error: failed to push event to Elasticsearch index.\nresponse: " . $Response->status . "\nerror message: " . $Response->error );
			
	}
	
	echo "\n\n";

?>
<?php
	
		
	class Wiki_article_model
	{
		
		
		// settings
		private $sleep_seconds = 1;		// time to sleep between http requests so we don't crush wikipedia
		private $brief_length = 1;		// number of paragraphs to grab from the begining of the article to use as brief
		
	// --- DO NOT EDIT SETTINGS BELOW THIS LINE -----------------------------------
		
		
		// class attributes
		public $title;							// article title ( unique identifier on wikipedia )
		public $url;							// article's url on wikipedia
		public $html;							// raw html of the article
		
		// extracted article data
		public $image_page_url;					// wiki url for the page of the main image ( not the direct image url )
		public $image_url;						// wiki url for the main image
		public $brief;							// first section of a wikipedia article
		public $references = array();			// all references from the article
		
		// for fun extracted article data
		public $love = NULL;					// is love discussed in the article
		public $hate = NULL;					// is hate discussed in the article
		
		// mysql connection
		private $mysqli;
		
		// scraping stuff
		private $Dom;
		private $Dom_body;
		
		
		// construct
		//
		public function __construct(
			$title	// the article title to fetch
		) {
			
			// set default timezone
			date_default_timezone_set('America/Los_Angeles');
			
			// connect to mysql
			$this->Mysqli = new mysqli( 'wikipedia.cw0tm7tgwtd4.us-east-1.rds.amazonaws.com', 'jones', 'zMfZdhce', 'wikipedia' );
			
			// set mysql connection to use utf-8
			$this->Mysqli->set_charset( 'utf8' );
			
			// create data with passed values
			$this->title = $title;
			$this->url = 'http://en.wikipedia.org/wiki/' . $title;
			$this->get_id();
			
			try {

				// extract data from html
				$this->get_html();
				$this->get_brief();
				$this->get_image();
				$this->get_references();
				
				// write data to database
				//$this->update_article();
				//$this->update_references();
				//$this->insert_referenced_books();
			
			} catch( Exception $e ) {
				
				echo $e->getMessage();
				die;
				
			}
			
		}
	
	
	// --- GETTERS ----------------------------------------------------------------
		
		
		privagte function get_id() {
			$sql = "SELECT id
					FROM articles
					WHERE title = '" . $this->title . "'";
			$Response = $this->Mysqli->query( $sql );
			if( $Response )
				$this->id = $Response->fetch_object()->id;
			else
				throw new Exception( "Mysql Query Error: failed to retrieve article id.\n$this->Mysqli->error" );
		}

		// retrieve the webpage from wikipedia.org
		//
		private function get_html() {
			
			// sleep so we don't crush wikipedia
			//sleep( $this->sleep_seconds );
			
			// download page
			$response = file_get_contents( $this->url );
			if( ! $response )
				thrown new Exception( 'file_get_contents() error: failed to retrieve article from wikipedia.org.' );
			else
				$this->html = $response;
				
			// load html into dom document parser
			$this->Dom = new DOMDocument();
            @$this->Dom->loadHTML( '<?xml encoding="UTF-8">' . $this->html );
			$this->Dom_body = $this->Dom->getElementById( 'bodyContent' );
						
		}

		
		// populates $this->brief with the first paragraph of the article
		//
		private function get_brief()
		{
		
			$i = 0;
			$this->brief = '';
			$ps = $this->Dom_body->getElementsByTagName( 'p' );
			foreach( $ps as $p ) {
				if( $i < $this->brief_length )
					$this->brief .= "\n" . $p->nodeValue;
				else
					break;
				$i++;
			}
			
			$this->brief = trim( $this->brief );
			
		}
		
		
		// gets the html and context for all references in an article
		//
		//	- populates $this->references array[0]['html'], array[0]['context']
		//
		private function get_references()
		{
			
			$s = strpos( $this->html, '<ol class="references">' . "\n" . '<li id="cite_note-1">' ) + 23;
			$f = strpos( $this->html, '</ol>', $s );
			$references = trim( substr( $this->html, $s, $f - $s ) );
			$references = explode( "\n", $references );
			
			// extract context
			$i = 0;
			foreach( $references as $reference )
			{	
				
				// get reference id
				$s = strpos( $reference, '<a href="#cite_ref-' );
				if( $s !== FALSE )
					$s += 19;
				else
					continue;
				$f = strpos( $reference, '">', $s );
				if( $f === FALSE )
					continue;
				$ref_id = substr( $reference, $s, $f - $s );
				
				// get reference context using ref id
				$pos = strpos( $this->html, '<sup id="cite_ref-' . $ref_id . '"' );
				if( $pos === FALSE )
					continue;
				$s = $this->rstrpos( $this->html, '<p>', $pos );
				if( $s !== FALSE )
					$s -= 3;
				else
					continue;
				$f = strpos( $this->html, '</p>', $pos );
				if( $f !== FALSE )
					$f += 4;
				else
					continue;
				$context = trim( substr( $this->html, $s, $f - $s ) );
				
				// store extracted data
				$this->references[$i]['html'] = $reference;
				$this->references[$i]['context'] = $context;
				
				// increment counter
				$i++;
				
			}
						
		}
		
		
		// pass a reference in here to check if it's a book and retrieve data
		//
		private function get_book_from_reference( $reference )
		{
			
				if( strpos( $reference['html'], 'ISBN' ) ) {
					
					// get isbn_13
					$s = strpos( $reference['html'], 'href="/wiki/Special:BookSources/' );
					if( $s !== FALSE )
						$s += 32;
					else
						continue;
					$f = strpos( $reference['html'], '"', $s );
					if( $f === FALSE )
						continue;
					$isbn_13 = substr( $reference['html'], $s, $f - $s );
					$isbn_13 = str_replace( '-', '', $isbn_13 );
					
					// request book data from google books api
					$Curl = curl_init();
					$url = 'https://www.googleapis.com/books/v1/volumes?q=isbn:' . $isbn_13 . '&fields=items(id,volumeInfo(title,subtitle,categories))';
					curl_setopt( $Curl, CURLOPT_URL, $url );
					curl_setopt( $Curl, CURLOPT_RETURNTRANSFER, 1 );
					curl_setopt( $Curl, CURLOPT_CONNECTTIMEOUT, 5 );
					$data = curl_exec( $Curl );
					curl_close( $Curl );
					
					// extract data we want from the google books api reponse
					$Data = json_decode( $data );
					$Book = $Data->items[0];
					
					// store data
					$book['isbn_13']			= $isbn_13;
					$book['google_book_id']	= $Book->id;
					$book['title']			= $Book->volumeInfo->title;
					$book['subtitle']			= $Book->volumeInfo->subtitle;
					foreach( $Book->volumeInfo->categories as $category )
						$book['categories'][] = $category;
						
					// return book data
					return $book;

				} else {
					return FALSE;
				}
	
			}
			
		}
		
		
		private function get_image() {

			// get image page url
			$s = strpos( $this->html, 'a href="/wiki/File' );
			if( $s !== FALSE )
				$s += 8;
			else
				return FALSE;
			$f = strpos( $this->html, '" ', $s );
			if( $f === FALSE )
				return FALSE;
			$this->image_page_url = substr( $this->html, $s, $f - $s );
			
			// get image url			
			$s = strpos( $this->html, 'src="', $f );
			if( $s !== FALSE )
				$s += 5;
			else
				return FALSE;
			$f = strpos( $this->html, '" ', $s );
			if( $f === FALSE )
				return FALSE;
			$this->image_url = substr( $this->html, $s, $f - $s );
			
		}
		
		
		private function get_love() {
			if(
				stripos( $this->html, 'love' )
				or stripos( $this->html, 'loves' )
				or stripos( $this->html, 'loved' )
				or stripos( $this->html, 'loving' )
			)
				$this->love = TRUE;
			else
				$this->love = FALSE;
		}
		
		
		private function get_hate() {
			if(
				stripos( $this->html, 'hate' )
				or stripos( $this->html, 'hates' )
				or stripos( $this->html, 'hated' )
				or stripos( $this->html, 'hating' )
			)
				$this->hate = TRUE;
			else
				$this->hate = FALSE;
		}
		
		
	// --- DATABASE STUFF -----------------------------------------------------
	
	
		// removes all info related to this article in every table
		//
		//	- this should be run before processing an article so that when we reprocess articles, we get fresh data
		//
		private function reset_article()
		{
			
			// empty this article's info
			$sql = "UPDATE articles
					SET
						brief			= NULL,
						imafge_page_url	= NULL,
						image_url		= NULL,
						love			= NULL,
						hate			= NULL
					WHERE title = $this->title";
			$Response = $this->Mysqli->query( $sql );
			if( ! $Response )
				throw new Exception( "Mysql Query Error: failed to reset article in mysql database.\n$this->Mysqli->error" );
			
			// delete all book to references for references for this article ( from the books_to_refernces table )
			//
			//	*** this has to be run before we delete the references, or we won't be able to find these
			//	*** we never delete books ( they should never change )
			//
			$sql = "DELETE FROM books_to_references
					WHERE reference_id IN (
						SELECT id
						FROM references
						WHERE article_id = " . $this->id ."
					)";
			$Response = $this->Mysqli->query( $sql );
			if( ! $Response )
				throw new Exception( "Mysql Query Error: failed to delete book to references data.\n$this->Mysqli->error" );	
			
			// delete all references for this article
			$sql = "DELETE FROM references
					WHERE article_id = " . $this->id;
			$Response = $this->Mysqli->query( $sql );
			if( ! $Response )
				throw new Exception( "Mysql Query Error: failed to delete references.\n$this->Mysqli->error" );
			
			return TRUE;
			
		}
	
		private function update_articles()
		{
			
			// prep data for insertion
			$brief			= $this->prep_string( $this->brief );
			$image_page_url	= $this->prep_string( $this-image_page_url );
			$image_url		= $this->prep_string( $this-image_url );
			
			// update data
			$sql = "UPDATE articles
					SET
						brief				= '$brief',
						image_page_url		= '$image_page_url',
						image_url			= '$image_url',
						brian_jones			= $this->brian_jones,
						love				= $this->love,
						hate				= $this->hate
					WHERE title	= '" . $this->prep_string( $this->title ) . "'";
			$Response = $this->Mysqli->query( $sql );
			if( ! $Response )
				throw new Exception( "Mysql Query Error: failed to save artcile to 'article' table.\n$this->Mysqli->error" );
				
			return TRUE;

		}
		
		
		private function update_references()
		{
		
			foreach( $this->references as $reference )
			{
			
				// prep data for insertion
				$html		= $this->prep_string( $reference['html'] );
				$context	= $this->prep_string( $reference['context'] );
				
				// insert data
				$sql = "INSERT INTO references( article_id, html, context )
						VALUES( $this->id, '$html', '$context' )";
				$Response = $this->Mysqli->query( $sql );
				if( ! $Response )
					throw new Exception( "Mysql Query Error: failed to save references to 'references' table.\n$this->Mysqli->error" );
				
				// check the reference for a book by isbn and save book if it is one
				$book = $this->get_book_from_reference( $reference );
				if( $book !== FALSE ) {
					$reference_id = $this->Mysqli->insert_id;
					$this->insert_referenced_book( $reference_id, $book );
				}
				
			}
			
			return TRUE;
		
		}
		
		
		// insert a referenced book and it's data into multiple tables
		//
		private function insert_referenced_book(
			$reference_id,		// reference id from references table
			$book				// array with book data
		) {
		
			// prep data for insertion
			$google_book_id	= $this->prep_string( $book['google_book_id'] );
			$title			= $this->prep_string( $book['title'] );
			$subtitle		= $this->prep_string( $book['subtitle'] );
			
			// insert data
			$sql = "INSERT INTO books( isbn_13, google_book_id, title, subtitle )
					VALUES( " . $book['isbn_13'] . ", '$google_book_id', '$title', '$subtitle' )
					ON DUPLICATE KEY UPDATE
						google_book_id	= '$google_book_id',
						title			= '$title',
						subtitle		= '$subtitle'";
			$Response = $this->Mysqli->query( $sql );
			if( ! $Response )
				throw new Exception( "Mysql Query Error: failed to save book to 'books' table.\n$this->Mysqli->error" );
						
			// insert book categories
			foreach( $book['categories'] as $category ) {
				$category = $this->prep_string( $category );
				$sql = "INSERT IGNORE INTO books_to_categories( book_isbn, category )
						VALUES( " . $book['isbn_13'] . ", '$category' )";
				$Response = $this->Mysqli->query( $sql );
				if( ! $Response )
					throw new Exception( "Mysql Query Error: failed to save book category to 'books_to_categories' table.\n$this->Mysqli->error" );

			}
			
			// insert book and reference data into references_to_books table
			$sql = "INSERT IGNORE INTO books_to_references( book_isbn, reference_id )
					VALUES( " . $book['isbn_13'] . ", $reference_id";
			$Response = $this->Mysqli->query( $sql );
			if( ! $Response )
				throw new Exception( "Mysql Query Error: failed to save book to 'books_to_references' table.\n$this->Mysqli->error" );
			
			// success
			return TRUE;
			
		}
		
		
	// --- UTILITIES --------------------------------------------------------------
	
		
		// return next previous occurace of a string
		//
		private function rstrpos( $haystack, $needle, $offset ) {
			$size = strlen( $haystack );
			$pos = strpos( strrev( $haystack ), strrev( $needle ), $size - $offset );
			if( $pos === FALSE )
				return FALSE;
			return $size - $pos;
		}
		
		// clean a string and prep it for database insertion
		private function prep_string( $string ){
			$string = trim( $string );
			$string = $this->Mysqli->real_escape_string( $string );
			return $string;
		}
				
		
	} // end class

	$Model = new Wiki_article_model( 'Kapton' );
	
?>
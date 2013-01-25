<?php
	
		
	class Wiki_article_model
	{
		
		
		// settings
		private $brief_length = 1; // number of paragraphs to grab from the begining of the article to use as brief
		
		
	// --- DO NOT EDIT SETTINGS BELOW THIS LINE -----------------------------------
		
		
		// mysql connection
		private $mysqli;
		
		// class attributes
		public $title;							// article title ( unique identifier on wikipedia )
		public $url;							// article's url on wikipedia
		public $html;							// raw html of the article
		
		// extracted article data
		public $image_page_url;					// wiki url for the page of the main image ( not the direct image url )
		public $image_url;						// wiki url for the main image
		public $brief;							// first section of a wikipedia article
		public $references = array();			// all references from the article
		public $referenced_books = array();
		public $mentioned_dates = array();
		
		// for fun extracted article data
		public $love = NULL;					// is love discussed in the article
		public $hate = NULL;					// is hate discussed in the article
		public $brian_jones = NULL;				// is 'brian jones' mentioned in the article
		
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
			
			// extract data from html
			$this->get_html();
			$this->get_brief();
			$this->get_image();
			$this->get_references();
			
			
			
			// write data to database
			//$this->update_article();
			//this->update_references();
			
		}
	
	
	// --- GETTERS ----------------------------------------------------------------
	

		// retrieve the webpage from wikipedia.org
		//
		private function get_html() {
			
			// sleep so we don't crush wikipedia
			//sleep( 1 );
			
			// download page
			$this->html = file_get_contents( $this->url );
			
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
						
			/**********************************
			
			$Nodes = $this->Xpath->query( "//*[contains(@class, 'reflist')]" );
			foreach( $Nodes->item(0)->childNodes->item(1)->childNodes as $li ) {
				if( $li->nodeName == 'li' ) {
					foreach( $li->childNodes as $stuff )
						$this->references[] = trim( $stuff->nodeValue );
				}
			}
			
			**********************************/
						
		}
		
		private function get_referenced_books()
		{
			
			// loop through $this->references and extract books
			
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

			echo ": " . $this->image_page_url;
			echo "\n: " . $this->image_url;
			die;
			
		}
		
		private function get_brian_jones() {
			if( stripos( $this->html, 'brian jones' ) )
				$this->brian_jones = TRUE;
			else
				$this->brian_jones = FALSE;
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
		
		
	// --- WRITE TO DATABASE ------------------------------------------------------
	//
	//	NOTES:
	//		- we prob have to remove all data about an article every time we process it
	//			- select if of all references for this article
	//				- delete those references by id
	//				- delete all references_to_books where the reference_id matches these reference_ids
	//				- repeat with other reference types
	//			- delete mentioned dates where they match this article title
	//
	
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
			$response = $this->Mysqli->query( $sql );
						
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
				$response = $this->Mysqli->query( $sql );
				
			}
		
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

	$Model = new Wiki_article_model( 'San_diego' );

?>
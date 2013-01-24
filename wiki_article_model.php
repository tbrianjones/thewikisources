<?php

	class Wiki_article_model
	{
		
		
		// settings
		private $brief_length = 1; // number of paragraphs to grab from the begining of the article to use as brief
		
		
	// --- DO NOT EDIT SETTINGS BELOW THIS LINE -----------------------------------
		
		
		// mysql connection
		private $mysqli;
		
		// class attributes
		public $title;		// article title ( unique identifier on wikipedia )
		public $url;		// article's url on wikipedia
		public $html;		// raw html of the article
		
		// extracted article data
		public $brief;		// first section of a wikipedia article
		public $books = array();
		public $book_references = array();
		public $date_references = array();
		
		// dom stuff
		private $Dom;
		private $Body;
		
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
			
			
			
			// write data to database
			$this->update_article();
			
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
            $this->Dom->loadHTML( '<?xml encoding="UTF-8">' . $this->html );
			$this->Body = $this->Dom->getElementById( 'bodyContent' );
			
		}


		private function get_brief()
		{
		
			$i = 0;
			$this->brief = '';
			$ps = $this->Body->getElementsByTagName( 'p' );
			foreach( $ps as $p ) {
				if( $i < $this->brief_length )
					$this->brief .= "\n" . $p->nodeValue;
				else
					break;
				$i++;
			}
			
			$this->brief = trim( $this->brief );
			
		}
		
		
	// --- WRITE TO DATABASE ------------------------------------------------------
	
	
		private function update_article()
		{
			
			$sql = "UPDATE articles
					SET
						brief = '" . $this->Mysqli->real_escape_string( $this->brief ) . "',
						last_retrieved = '" . date( 'c' ) . "'
						
					WHERE title = '" . $this->Mysqli->real_escape_string( $this->title ) . "'";
			$response = $this->Mysqli->query( $sql );
			
		}
		
		
	} // end class

	$Model = new Wiki_article_model( 'Kapton' );

?>
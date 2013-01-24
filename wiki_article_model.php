<?php

	class Wiki_article_model
	{
		
		// mysql connection
		private $mysqli;
		
		// class attributes
		private $title;		// article title ( unique identifier on wikipedia )
		private $url;		// article's url on wikipedia
		private $html;		// raw html of the article
		
		// extracted article data
		private $brief;		// first section of a wikipedia article
		private $books = array();
		private $book_references = array();
		private $date_references = array();
		
		// dom stuff
		private $Dom;
		private $Body;
		
		// construct
		//
		public function __construct(
			$title	// the article title to fetch
		) {
			
			// connect to mysql
			$this->Mysqli = new mysqli( 'wikipedia.cw0tm7tgwtd4.us-east-1.rds.amazonaws.com', 'jones', 'zMfZdhce', 'wikipedia' );
			
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
				if( $i < 3 )
					$this->brief .= "\n\n" . $p->nodeValue;
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
					SET brief = '" . $this->Mysqli->real_escape_string( $this->brief ) . "'
					WHERE title = '" . $this->Mysqli->real_escape_string( $this->title ) . "'";
			$response = $this->Mysqli->query( $sql );
			
		}
		
		
	} // end class

	$Model = new Wiki_article_model( 'Kapton' );

?>
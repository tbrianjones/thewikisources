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
		private $Xpath;
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
			$this->get_references();
			
			
			
			// write data to database
			//$this->update_article();
			
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
			
			// load xpath finder
			$this->Xpath = new DomXPath( $this->Dom );
			
		}

		
		// populates $this->brief with the first paragraph of the article
		//
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
		
		
		// gets the html and context for all references in an article
		//
		//	- populates $this->references array[0]['html'], array[0]['context']
		//
		private function get_references()
		{
			
			$i = strpos( $this->html, '<ol class="references">' ) + 23;
			$f = strpos( $this->html, '</ol>', $i );
			$references = trim( substr( $this->html, $i, $f - $i ) );
			$this->references = explode( "\n", $references );
			var_dump( $references );
			
			/* extract the context from the article for each reference
			
			HTML:
			<p><b>Kapton</b> is a <a href="/wiki/Polyimide" title="Polyimide">polyimide</a> film developed by <a href="/wiki/DuPont" title="DuPont">DuPont</a> which can remain stable in a wide range of temperatures, from ?273 to +400 °C (?459 ? 752 °F / 0 ? 673 K).<sup id="cite_ref-1" class="reference"><a href="#cite_note-1"><span>[</span>1<span>]</span></a></sup> Kapton is used in, among other things, flexible printed circuits (<a href="/wiki/Flexible_electronics" title="Flexible electronics">flexible electronics</a>) and <a href="/wiki/Thermal_micrometeoroid_garment" title="Thermal micrometeoroid garment" class="mw-redirect">thermal micrometeoroid garments</a>, the outside layer of <a href="/wiki/Space_suit" title="Space suit">space suits</a>.</p>
			
			- see the <sup id="cite_ref-1 code in the html above to spot references
			
			*/
			
			
			/*
			$Nodes = $this->Xpath->query( "//*[contains(@class, 'reflist')]" );
			foreach( $Nodes->item(0)->childNodes->item(1)->childNodes as $li ) {
				if( $li->nodeName == 'li' ) {
					foreach( $li->childNodes as $stuff )
						$this->references[] = trim( $stuff->nodeValue );
				}
			}
			*/
						
		}
		
		private function get_referenced_books()
		{
			
			// loop through $this->references and extract books
			
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
			
			$sql = "UPDATE articles
					SET
						brief = '" . $this->Mysqli->real_escape_string( $this->brief ) . "',
						last_retrieved = '" . date( 'c' ) . "',
						brian_jones = $this->brian_jones,
						love = $this->love,
						hate = $this->hate
					WHERE title = '" . $this->Mysqli->real_escape_string( $this->title ) . "'";
			$response = $this->Mysqli->query( $sql );
						
		}
		
		
		private function update_references()
		{
		
			foreach( $this->references as $reference ) {
				$html = $this->Mysqli->real_escape_string( $reference['html'];
				$context = $this->Mysqli->real_escape_string( $reference['context'];
				$sql = "INSERT INTO references( article_id, html, context )
						VALUES( $this->id, '$html', '$context' )";
				$response = $this->Mysqli->query( $sql );
			}
		
		}
		
		
	} // end class

	$Model = new Wiki_article_model( 'Kapton' );

?>
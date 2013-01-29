<?php

	// files table access
	//
	class Book_model extends CI_Model
	{

		
		// attributes
		public $isbn_13;
		public $isbn_10;
		public $title;
		public $subtitle;
		public $last_modified;
		
		// derived attributes
		public $profile_url;
		
		// google resources
		public $google_book_id;
		public $google_book_url;
		public $cover_image_urls = array();
		
		// amazon resources
		public $amazon_affiliate_url;
		
		
		// load data for this reference
		//
		public function load( $isbn_13 )
		{
						
			$this->db->select( '
				isbn_13,
				google_book_id,
				title,
				subtitle,
				last_modified
			' );
			$this->db->where( 'isbn_13', $isbn_13 );
			$this->db->from( 'books' );
			$query = $this->db->get();
			
			if( $query->num_rows > 0 )
			{
	
				$row = $query->row();
				
				$this->isbn_13			= $row->isbn_13;
				$this->isbn_10			= substr( $this->isbn_13, 3 );
				$this->google_book_id	= $row->google_book_id;
				$this->title			= $row->title;
				$this->subtitle			= $row->subtitle;
				$this->last_modified	= $row->last_modified;
				
				// set derived data
				$this->get_profile_url();
				$this->get_cover_image_urls();
				$this->get_google_book_url();
				$this->get_amazon_affiliate_url();
				
			} else {
			
				return FALSE;
			
			}
			
		}
	
	
		private function get_profile_url() {
			$this->profile_url = '/reference/book/' . $this->isbn_13;
			return $this->profile_url;
		}
		
		private function get_cover_image_urls() {
			$this->cover_image_urls['small'] = 'http://bks6.books.google.com/books?id=' . $this->google_book_id . '&printsec=frontcover&img=1&zoom=5';
			$this->cover_image_urls['medium'] = 'http://bks6.books.google.com/books?id=' . $this->google_book_id . '&printsec=frontcover&img=1&zoom=1';
			$this->cover_image_urls['large'] = 'http://bks6.books.google.com/books?id=' . $this->google_book_id . '&printsec=frontcover&img=1&zoom=2';
		}
		
		private function get_google_book_url() {
			$this->google_book_url = 'http://books.google.com/books?id=' . $this->google_book_id;
		}
		
		private function get_amazon_affiliate_url() {
			$this->amazon_affiliate_url = 'http://www.amazon.com/gp/product/' . $this->isbn_10 . '/&tag=induinteinc-20';
		}

	} // end class
	
?>

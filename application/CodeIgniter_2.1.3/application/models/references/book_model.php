<?php

	// files table access
	//
	class Book_model extends CI_Model
	{

		
		// attributes
		public $isbn_13;
		public $isbn_10;
		public $google_book_id;
		public $title;
		public $subtitle;
		public $last_modified;
		public $profile_url;
		
		
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
				
			} else {
			
				return FALSE;
			
			}
			
		}
	
	
		private function get_profile_url() {
			$this->profile_url = '/reference/book/' . $this->isbn_13;
			return $this->profile_url;
		}

	} // end class
	
?>

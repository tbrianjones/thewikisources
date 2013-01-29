<?php


	class Article_model extends CI_Model
	{

		
		// attributes
		public $id;
		public $title;
		public $brief;
		public $image_page_url;
		public $image_urls = array();
		public $love;
		public $hate;
		public $last_modified;
		
		// derived attributes
		public $profile_url;
		
		
		// load data for this reference
		//
		public function load( $id )
		{
						
			$this->db->select( '
				id,
				title,
				brief,
				image_page_url,
				image_url,
				love,
				hate,
				last_modified
			' );
			$this->db->where( 'id', $id );
			$this->db->from( 'articles' );
			$query = $this->db->get();
			
			if( $query->num_rows > 0 )
			{
	
				$row = $query->row();
				
				$this->id				= $row->id;
				$this->title			= $row->title;
				$this->brief			= $row->brief;
				$this->love				= $row->love;
				$this->hate				= $row->hate;
				$this->last_modified	= $row->last_modified;

				// image urls
				$this->image_urls['cached'] = 'http:' . $row->image_url; // this image should be cached by wikipedia
				$this->image_urls['image_page_url'] = 'http://en.wikipedia.org' . $row->image_page_url;
				
				// set derived data
				$this->get_profile_url();
				
			} else {
			
				return FALSE;
			
			}
			
		}
	
		private function get_profile_url() {
			$this->profile_url = '/article/profile' . $this->id;
			return $this->profile_url;
		}

		
	} // end class
	
?>

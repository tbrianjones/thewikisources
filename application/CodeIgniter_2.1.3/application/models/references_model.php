<?php

	// files table access
	//
	class References_model extends CI_Model
	{
		
		
		public $references = array();
		
		
		// load data for this reference
		//
		public function get_references_by_book_isbn_13( $isbn_13 )
		{
						
			$this->db->select( 'reference_id' );
			$this->db->where( 'book_isbn_13', $isbn_13 );
			$this->db->from( 'books_to_references' );
			$query = $this->db->get();
			if( $query->num_rows > 0 ) {
				
				foreach( $query->result() as $row ) {
					
					// get reference
					$this->load->model( 'Reference_model', 'Reference' );
					$this->Reference->load( $row->reference_id );
					$reference = get_object_vars( $this->Reference );
					
					// get article for this reference
					$this->load->model( 'Article_model', 'Article' );
					$this->Article->load( $this->Reference->article_id );
					$reference['article'] = get_object_vars( $this->Article );
					unset( $reference['article_id'] );
					
					// store reference			
					$this->references[] = $reference;
					
				}
				
			} else {
				return FALSE;
			}
			
		}
	

	} // end class
	
?>

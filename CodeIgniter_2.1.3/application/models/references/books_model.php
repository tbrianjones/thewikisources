<?php


	// deals with multiple books at once
	//
	class Books_model extends CI_Model
	{


		// attributes
		public $books = array();
		
		
		// load data for this reference
		//
		public function get_popular_books_by_number_of_references(
			$limit = 10 // number of books to return
		) {
						
			$this->db->select( 'book_isbn_13, COUNT( book_isbn_13 ) AS count' );
			$this->db->from( 'books_to_references' );
			$this->db->group_by( 'book_isbn_13' );
			$this->db->order_by( 'count' );
			$this->db->limit( $limit );
			$query = $this->db->get();
			if( $query->num_rows > 0 ) {
				foreach( $query->result() as $row ) {
					$this->load->model( 'references/Book_model', 'Book' );
					$this->Book->load( $row->book_isbn_13 );
					$this->books[] = $this->Book;
				}
			} else {
				return FALSE;
			}
			
		}
	

	} // end class
	
?>

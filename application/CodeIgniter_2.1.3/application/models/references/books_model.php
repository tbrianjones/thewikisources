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
			
			$sql = "select book_isbn_13, count( book_isbn_13 ) as count
					from books_to_references
					group by book_isbn_13
					order by count desc;";
			$this->db->select( 'book_isbn_13, COUNT( book_isbn_13 ) AS count', FALSE );
			$this->db->from( 'books_to_references' );
			$this->db->group_by( 'book_isbn_13' );
			$this->db->order_by( 'count', 'DESC' );
			$this->db->limit( $limit );
			$Query = $this->db->get();
			if( $Query->num_rows > 0 ) {
				$i = 0;
				foreach( $Query->result() as $Row ) {
					$this->load->model( 'references/Book_model', 'Book' );
					$this->Book->load( $Row->book_isbn_13 );
					$this->books[$i] = get_object_vars( $this->Book );
					$this->books[$i]['count'] = $Row->count;
					$i++;
				}
			} else {
				return FALSE;
			}
			
		}
	

	} // end class
	
?>

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


	class Reference extends MY_Controller {	
		
		
		function __construct() {
			parent::__construct();
		}
			
		
		public function book( $isbn_13 )
		{
				
			// load book
			$this->load->model( 'references/Book_model', 'Book' );
			$this->Book->load( $isbn_13 );
			$data['book'] = $this->Book;
			$this->load->view( 'books/profile.php', $data );
			
			// load references
			$this->load->model( 'references/References_model', 'References' );
			$this->References->get_references_by_book_isbn_13( $this->Book->isbn_13 );
			$data['references'] = $this->References;
			$this->load->view( 'books/references.php', $data );
			
			
		}
	
	
	}
	
?>
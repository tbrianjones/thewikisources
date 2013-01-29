<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


	class Reference extends MY_Controller {	
		
		
		public function book( $isbn_13 )
		{
						
			// load book
			$this->load->model( 'Book_model', 'Book' );
			$this->Book->load( $isbn_13 );
			$data['book'] = get_object_vars( $this->Book );
			
			// load references
			$this->load->model( 'References_model', 'References' );
			$this->References->get_references_by_book_isbn_13( $this->Book->isbn_13 );
			$data['references'] = get_object_vars( $this->References );
			
			// render page
			$page['content'] = $this->load->view( 'books/profile.php', $data, TRUE );
			$page['content'] .= $this->load->view( 'books/references.php', $data, TRUE );
			$this->load->view( 'core/page.php', $page );
			
		}
	
	
	}
	
?>
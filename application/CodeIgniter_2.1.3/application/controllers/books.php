<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Books extends MY_Controller {	
		
		
		public function index()
		{
			
			$this->load->helper( array( 'form', 'url' ) );
						
			// load book
			$this->load->model( 'Books_model', 'Books' );
			$this->Books->get_popular_books_by_number_of_references();
			$data = get_object_vars( $this->Books );
			$page['content'] = $this->load->view( 'pages/books_index.php', $data, TRUE );

			// render page
			$this->load->view( 'core/page.php', $page );
			
		}
		
		
		
		public function profile( $isbn_13 )
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
			$page['content'] = $this->load->view( 'book/profile.php', $data, TRUE );
			$page['content'] .= $this->load->view( 'book/profile_references.php', $data, TRUE );
			$this->load->view( 'core/page.php', $page );
			
		}
		
		
		public function search()
		{
			
			// --- PROCESS FORM --- //

			// load required files
			$this->load->helper( array( 'form', 'url' ) );
			$this->load->library( 'form_validation' );
			// form field cannot be empty
			
			// form failed
			if( $this->form_validation->run() === FALSE ) {
				
				// just load the search form
				$page['content'] = $this->load->view( 'search/search_books_form.php', NULL, TRUE );
				
			// form was successful
			} else {
			
				// search es for books
				// display results
			
			}
			
			$this->load->view( 'core/page.php', $page );
			
		}
	
	
	}
	
?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Welcome extends MY_Controller {
	
	
		public function index()
		{
			
			$this->load->helper( array( 'form', 'url' ) );
						
			// load book
			$this->load->model( 'Books_model', 'Books' );
			$this->Books->get_popular_books_by_number_of_references();
			$data = get_object_vars( $this->Books );
			$page['content'] = $this->load->view( 'pages/welcome_index.php', $data, TRUE );

			// render page			
			$this->load->view( 'core/page.php', $page );
			
		}
	
	
	} // end class

?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Welcome extends MY_Controller {
	
	
		public function index()
		{
						
			// load book
			$this->load->model( 'Books_model', 'Books' );
			$this->Books->get_popular_books_by_number_of_references();
			$data['books'] = $this->Books;
			$page['content'] = $this->load->view( 'home.php', $data, TRUE );
			
			// render page
			$this->load->view( 'core/page.php', $page );
			
		}
	
	
	} // end class

?>
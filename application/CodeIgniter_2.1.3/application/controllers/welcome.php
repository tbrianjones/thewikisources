<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Welcome extends MY_Controller {
	
	
		public function index()
		{
		
			// load book
			$this->load->model( 'references/Books_model', 'Books' );
			$this->Books->get_popular_books_by_number_of_references();
			$data['books'] = $this->Books;
			$this->load->view( 'home.php', $data );
					
		}
	
	
	} // end class

?>
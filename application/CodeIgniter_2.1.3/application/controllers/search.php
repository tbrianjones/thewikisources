<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Search extends MY_Controller {
	
		public function books()
		{
			
			// --- PROCESS FORM --- //

			// load required files
			$this->load->helper( array( 'form', 'url' ) );
			$this->load->library( 'form_validation' );
			// form field cannot be empty
			
			// form failed
			if( $this->form_validation->run() === FALSE ) {
				
				// just load the search form
				
			// form was successful
			} else {
			
			// search es for books
			// display results
			
			}
			
			$this->load->view( 'core/page.php', $page );
			
		}
	
	
	} // end class

?>
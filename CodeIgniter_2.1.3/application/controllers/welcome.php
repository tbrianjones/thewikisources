<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Welcome extends MY_Controller {
	
	
		public function index()
		{
		
			$this->load->view( 'home' );
		
		}
	
	
	} // end class

?>
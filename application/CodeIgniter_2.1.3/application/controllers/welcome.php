<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Welcome extends MY_Controller {
	
		
		public function index()
		{
			
			$page['content'] = $this->load->view( 'pages/welcome_index.php', NULL, TRUE );

			// render page
			$this->load->view( 'core/page.php', $page );
			
		}

	
	} // end class

?>
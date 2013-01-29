<?php


	// other controllers extend MY_Controller so we can do common stuff here
	//
	class MY_Controller extends CI_Controller
	{			
		
		
		function __construct() {

			parent::__construct();
			
			// do stuff in development mode
			if( ENVIRONMENT == 'development' ) 
			{

				$this->output->enable_profiler( TRUE );

			}

			
		}
		
	
	} // end class
	

?>
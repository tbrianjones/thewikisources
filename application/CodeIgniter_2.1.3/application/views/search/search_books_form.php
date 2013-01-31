<?php

	// setup form
	echo form_open( '/search/books/', array( 'class' => 'form', 'id' => 'search_form', 'style' => '' ) );

		// adv max
		$data = array(
			'name'			=> 'query',
			'id'			=> 'query',
			'class'			=> 'text',
			'maxlength'		=> '255',
			'size'			=> '50'
		);
		echo form_input( $data );
		
		$data = array(
			'name'		=> 'submit',
			'value'		=> 'Search Books',
			'class'		=> 'button'
		);
		echo form_submit( $data );
	
	echo form_close();
	
?>
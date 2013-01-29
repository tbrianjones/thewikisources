<?php

	// files table access
	//
	class Reference_model extends CI_Model
	{

		
		// attributes
		public $id;
		public $article_id;
		public $reference_html;
		public $context_html;
		public $last_modified;

		
		// load data for this reference
		//
		public function load( $id )
		{
						
			$this->db->select( '
				id,
				article_id,
				reference_html,
				context_html,
				last_modified
			' );
			$this->db->where( 'id', $id );
			$this->db->from( 'references' );
			$query = $this->db->get();
			
			if( $query->num_rows > 0 )
			{
	
				$row = $query->row();
				
				$this->id				= $row->id;
				$this->article_id		= $row->article_id;
				$this->reference_html	= $row->reference_html;
				$this->context_html		= str_replace( '<a href="/wiki', '<a href="http://en.wikipedia.org/wiki', $row->context_html );
				$this->last_modified	= $row->last_modified;
				
			} else {
			
				return FALSE;
			
			}
			
		}
	

	} // end class
	
?>

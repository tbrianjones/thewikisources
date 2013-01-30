<h1>The Most Frequency Cited Books on Wikipedia</h1>
<p>Find the most frequently cited book on any topic by searching our site.</p>
<p>We aggregate every book cited on Wikipedia and the number of times it's cited throughout the site.</p>

<?php

	foreach( $books as $book ) {
		$this->load->view( 'book/result_profile', $book );
	}
	
?>
<h1>The Most Frequently Cited Books on Wikipedia</h1>
<p>Find the most frequently cited book on any topic by searching our site.</p>
<p>We aggregate every book cited on Wikipedia and the number of times it's cited throughout the site.</p>

<!-- search books form -->
<?php $this->load->view( 'search/search_books_form.php' ); ?>

<!-- book results -->
<?php

	foreach( $books as $book ) {
		$this->load->view( 'book/result_profile', $book );
	}
	
?>
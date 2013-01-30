<img src="<?php echo $book['cover_image_urls']['medium']; ?>" />

<h1><?php echo $book['title']; ?>: <?php echo $book['subtitle']; ?></h1>

<ul>
	<li><a href="<?php echo $book['amazon_affiliate_url']; ?>">Buy from amazon</a></li>
	<li><a href="<?php echo $book['google_book_url']; ?>">Read on Google.com</a></li>
</ul>

<?php // var_dump( $book ); ?>
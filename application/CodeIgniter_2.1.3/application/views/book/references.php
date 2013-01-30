<h2>Wikipedia References for this book:</h2>
<?php foreach( $references['references'] as $reference ) { ?>
	
	<?php // var_dump( $reference ); ?>
	
	<h3>From <a href=""><?php echo $reference['article']['title']; ?></a></h3>
	<?php echo $reference['context_html']; ?>
	<p>Retrieved: <?php echo $reference['last_modified']; ?></p>

<?php } ?>
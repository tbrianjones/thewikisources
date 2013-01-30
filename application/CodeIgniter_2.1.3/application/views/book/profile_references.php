<h2>Wikipedia References for this book:</h2>
<?php foreach( $references['references'] as $reference ) { ?>
	
	<?php // var_dump( $reference ); ?>
	
	<div id="reference_result_profile">
		<h2>From Wikipedia Article: <a href="<?php echo $reference['article']['wikipedia_url']; ?>"><?php echo $reference['article']['title']; ?></a></h2>
		<?php echo $reference['context_html']; ?>
		<p>Retrieved: <?php echo $reference['last_modified']; ?></p>
	</div>

<?php } ?>
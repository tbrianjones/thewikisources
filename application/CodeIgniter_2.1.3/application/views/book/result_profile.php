<div id="book_result_profile">
	<h2>
		<a href="<?php echo $profile_url; ?>">
			<?php echo $title; ?>
			<?php if ( isset( $subtitle ) AND $subtitle != '' ) echo ": $subtitle"; ?>
		</a>
	</h2>
	<p>Number of Citations: <?php echo $count; ?></p>
</div>
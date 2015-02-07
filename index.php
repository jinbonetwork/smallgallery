<?php
require_once TEMPLATEPATH.'/query.php';
if(have_posts()):
	while(have_posts()):
		the_post();
		header('location:'.get_permalink(get_the_ID()));
	endwhile;
endif;
?>

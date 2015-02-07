<?php
get_header();
if(have_posts()):
	while(have_posts()):
		the_post();
		$post = rebuild_post($post);
		echo <<<EOT
<section id="entry-{$post->ID}" class="current {$post->class}">
	<div class="wrap">
		{$post->div_feature}
		{$post->div_title}
		{$post->div_content}
		{$post->div_author}
		{$post->div_date}
	</div>
</section><!--/#entry-{$post->ID}-->
EOT;
	endwhile;
endif;
get_footer();
?>

<?php
define('CONTROL',false);
get_header();
if(have_posts()):
	while(have_posts()):
		the_post();
		$post = rebuild_post($post);
		$edit_link = current_user_can('edit_post',$post->ID)?"<div class='edit_link'><a href='{$post->edit_link}'><span>".__('Edit this post',TEXTDOMAIN)."</span></a></div>":'';
		echo <<<EOT
<section id="entry-{$post->ID}" class="current format-standard {$post->class}">
	<div class="wrap">
		{$post->div_feature}
		<div class="caption">
			{$edit_link}
			{$post->div_title}
			{$post->div_content}
			{$post->div_author}
			{$post->div_date}
		</div>
	</div>
</section><!--/#entry-{$post->ID}-->
EOT;
	endwhile;
endif;
get_footer();
?>

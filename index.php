<?php
get_header();

if(have_posts()):
	while(have_posts()):
		the_post();
		$post = rebuild_post($post);
		$title = $post->slide_title?"<{$post->heading} class='title'>{$post->filtered_title}</{$post->heading}><!--/.title-->":'';
		$content = $post->slide_content?"<div class='content'>{$post->filtered_content}</div><!--/.content-->":'';
		$author = "<div class='author'>{$post->filtered_author}</div><!--/.author-->";

		echo <<<EOT
	<article id="entry-{$post->ID}" class="{$post->class}">
		<div class="wrap">
			{$post->filtered_feature_medium}
			{$title}
			{$content}
			{$author}
		</div>
	</article><!--/#entry-{$post->ID}-->

EOT;
	endwhile;
endif;

get_footer();
?>

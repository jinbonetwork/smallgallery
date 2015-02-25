<?php
	global $post;
	setup_postdata($post);
	$post = rebuild_post($post);
	the_post();
?><!DOCTYPE html>
<html>
<head>
<meta charset="<?php echo get_option('blog_charset'); ?>">
<base target="_top">
<?php
	smallgallery_head();
	wp_head();
?>
<script>
jQuery(document).ready(function(e){
	jQuery('a.comment-date').on('click',function(e){
		e.preventDefault();
	});
});
</script>
</head>
<body id="comments-body">
<div id="entry-<?php echo $post->ID; ?>-comments" class="comments-container">
	<h1 class="site-title"><?php echo get_option('blogname'); ?></h1>
	<h1 class="entry-title"><?php printf(__('Reply on <strong>%s</strong>',TEXTDOMAIN),$post->filtered_title); ?></h1>
<?php
	define('CONTROL',false);
	//echo build_post($post);
	comments_template();
?>
</div><!--.comments-->
<?php
	wp_footer();
?>
</body>
</html>

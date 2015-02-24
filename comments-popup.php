<?php
if(post_password_required()){
	return;
}
function smallgallery_comment_nav() {
	if(get_comment_pages_count()>1&&get_option('page_comments')):
	?>
	<nav class="navigation comment-navigation" role="navigation">
		<h2 class="screen-reader-text"><?php _e( 'Comment navigation', 'smallgallery' ); ?></h2>
		<div class="nav-links">
			<?php
				if ( $prev_link = get_previous_comments_link( __( 'Older Comments', 'smallgallery' ) ) ) :
					printf( '<div class="nav-previous">%s</div>', $prev_link );
				endif;

				if ( $next_link = get_next_comments_link( __( 'Newer Comments', 'smallgallery' ) ) ) :
					printf( '<div class="nav-next">%s</div>', $next_link );
				endif;
			?>
		</div><!-- .nav-links -->
	</nav><!-- .comment-navigation -->
	<?php
	endif;
}

global $post;
$post = rebuild_post($post);
the_post();

?><!DOCTYPE html>
<html>
<head>
	<meta charset="<?php echo get_option('blog_charset'); ?>">
	<base target="_blank">
	<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri().'/style.php'; ?>">
	<?php
		wp_head();
	?>
</head>
<body>
<div id="entry-<?php echo $post->ID; ?>-comments" class="comments">
	<h1 class="site-title a11y"><?php echo get_option('blogname'); ?></h1>
	<h1 class="entry-title"><?php printf(__('Reply on <strong>%s</strong>',TEXTDOMAIN),$post->filtered_title); ?></h1>

<?php
	if(get_comments_number()):
		smallgallery_comment_nav();
		echo "<ol class='comment-list'>".PHP_EOL;
		wp_list_comments(array(
			'style'	=> 'ol',
			'short_ping' => true,
			'avatar_size' => 56,
		),get_comments(array('post_id'=>$post->ID)));
		echo "</ol><!--/.comment-list-->".PHP_EOL;
		smallgallery_comment_nav();
	else:
		echo "<p class='there-are-no-comments'>".__('There are no comments.',TEXTDOMAIN)."</p>".PHP_EOL;
	endif;

	if(comments_open()&&post_type_supports(get_post_type(),'comments')):
		comment_form(array('comment_notes_after'=>"<input type='hidden' name='redirect_to' value='{$post->comment_link}'>"));
	else:
		echo "<p class='comments-are-closed'>".__('Comments are closed.',TEXTDOMAIN)."</p>".PHP_EOL;
	endif;
?>

</div><!--.comments-->
<?php
	wp_footer();
?>
</body>
</html>

<?php
if(have_posts()):
	while(have_posts()):
		the_post();
		$post = rebuild_post($post);
		switch($_GET['format']):
			case 'json':
				$post->filtered_post = build_post($post);
				header('Content-type:application/json;charset='.get_option('blog_charset'));
				echo json_encode($post);
			break;
			default:
				get_header();
				echo build_post($post);
				build_control($post);
				get_footer();
			break;
		endswitch;
	endwhile;
endif;
?>

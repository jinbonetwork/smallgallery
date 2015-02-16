<?php
if(is_home()):
	require_once TEMPLATEPATH.'/query.php';
	if(have_posts()):
		the_post();
		header('location:'.get_permalink(get_the_ID()));
	else:
		$message = (object) array(
			'context' => 'error',
			'type' => '',
			'title' => __('Gallery is empty.',TEXTDOMAIN),
			'description' => '',
			'guide' => '',
		);
		print_r($message);
		get_header();
		get_feedback($message);
		get_footer();
	endif;
else:
	if(have_posts()):
		the_post();
		$message = (object) array(
			'context' => 'archive ',
			'type' => '',
			'title' => '',
			'description' => '',
			'links' => '',
		);
		global $wp_query;
		$object = $wp_query->get_queried_object();

		if(is_author()):
			$object = rebuild_user($object);
			$message->type = 'author';
			$message->title = $object->display_name;
			$message->description = "<ul class='profile'>".PHP_EOL
				. ($object->user_email?"<li class='email'><dl><dt>".__('Email',TEXTDOMAIN)."</dt><dd>{$object->div_email}</dd></dl></li>".PHP_EOL:'')
				. ($object->user_url?"<li class='url'><dl><dt>".__('Homepage',TEXTDOMAIN)."</dt><dd>{$object->div_url}</dd></dl></li>".PHP_EOL:'')
				. ($object->description?"<li class='bio'><dl><dt>".__('About',TEXTDOMAIN)."</dt><dd>{$object->div_description}</dd></dl></li>".PHP_EOL:'')
				. "</ul>";
		elseif(is_date()):
			$message->type = 'date';
		elseif(is_category()):
			$message->type = 'category';
			$message->title = $object->name;
			$message->description = $object->description;
		elseif(is_tag()):
			$message->type = 'tag';
			$message->title = $object->name;
			$message->description = $object->description; 
		endif;
		
		get_header();
		rewind_posts();

		$entries = array();
		while(have_posts()):
			the_post();
			$entry = get_post();
			$entries[] = $entry;
		endwhile;
		$message->links = build_archives($entries);

		get_feedback($message);
		get_footer();
	else:
		$message = (object) array(
			'context' => 'error',
			'type' => '',
			'title' => __('Archive is empty.',TEXTDOMAIN),
			'description' => '',
			'guide' => '',
		);
		print_r($message);
		get_header();
		get_feedback($message);
		get_footer();
	endif;
endif;
?>

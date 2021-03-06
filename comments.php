<?php
if(post_password_required()){
	return;
}

global $post;

if(have_comments()):
	global $comments;

	require_once TEMPLATEPATH.'/class.walker.comment.php';
	$comments_walk_arguments = array(
		'walker' => new Smallgallery_Walker_Comment,
		'style' => 'ol',
		'short_ping' => true,
		'avatar_size' => UI_PORTRAIT_SIZE*2, // doubling for retina display
	);

	if(get_option('page_comments')){
		$pagination = (object) array();
		$pagination->comments_per_page = get_option('comments_per_page');
		$pagination->total_comments = $post->comments_number;
		$pagination->total_pages = ceil($pagination->total_comments/$pagination->comments_per_page);
		$pagination->page = max(1,get_query_var('page'));
		$pagination->offset = $pagination->comments_per_page*($pagination->page-1);

		$pagination_arguments = array(
			'base' => $post->comment_link.'%_%',
			'format' => '&page=%#%',
			'total' => $pagination->total_pages,
			'current' => $pagination->page,
			'type' => 'list',
			'echo' => false,
		);
		$pagination->markup = str_replace('<a ','<a target="_self" ',paginate_links($pagination_arguments));

		$default_comments_page = get_option('default_comments_page');
		$comment_order = get_option('comment_order');
		$comments_arguments = array(
			'status' => 'approve',
			'orderby' => 'comment_date_gmt',
			'order' => ($default_comments_page=='newest'?'DESC':'ASC'),
			'number' => $pagination->comments_per_page,
			//'count' => $pagination->comments_per_page,
			'offset' => $pagination->comments_per_page*($pagination->page-1),
			'post_id' => $post->ID,
		);
		$comments = get_comments($comments_arguments);

		$comments_walk_arguments['reverse_top_level'] = ($default_comments_page=='newest'&&$comment_order=='asc'||$default_comments_page=='oldest'&&$comment_order=='desc')?1:0;
	}else{
		$pagination->markup = '';
	}

	$pagination->markup = $pagination->markup?"<div class='pagination'>{$pagination->markup}</div><!--/.pagination-->":'';
	echo $pagination->markup;
	wp_list_comments($comments_walk_arguments,$comments);
	echo $pagination->markup;
else:
	echo "<p class='there-are-no-comments'>".__('There are no comments.',TEXTDOMAIN)."</p>".PHP_EOL;
endif;

if(comments_open()&&post_type_supports(get_post_type(),'comments')):
	ob_start();
	comment_form(array(
		'fields' => array(
			'author' => '<p class="comment-form-author"><label for="author">'.__('Name',TEXTDOMAIN).($req?'<span class="required">*</span>':'').'</label><input id="author" name="author" type="text" value="'.esc_attr($commenter['comment_author']).'" size="30"'.$aria_req.' placeholder="'.__('Your name here',TEXTDOMAIN).'"></p>',
			'email' => '<p class="comment-form-email"><label for="email">'.__('Email',TEXTDOMAIN ).($req?'<span class="required">*</span>':'').'</label><input id="email" name="email" type="text" value="'.esc_attr($commenter['comment_author_email']).'" size="30"'.$aria_req.' placeholder="'.__('email@domain.com',TEXTDOMAIN ).'"></p>',
			'url' => '<p class="comment-form-url"><label for="url">'.__('Homepage',TEXTDOMAIN).'</label><input id="url" name="url" type="text" value="'.esc_attr($commenter['comment_author_url']).'" size="30" placeholder="'.__('http://homepage.domain.com',TEXTDOMAIN).'"></p>',
		),
		'comment_field' => '<p class="comment-form-comment"><label for="comment">'.__('Comment',TEXTDOMAIN).'</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" placeholder="'.__('Your comment here',TEXTDOMAIN).'"></textarea><input type="hidden" name="redirect_to" value="'.$post->comment_link.'">',
		'must_log_in' => '<p class="must-log-in">'.sprintf(__('You must be <a href=\'%s\' target=\'_self\'>logged in</a> to post a comment.',TEXTDOMAIN),wp_login_url($post->comment_link)).'</p>',
		'logged_in_as' => '<p class="logged-in-as">'.sprintf(__('Logged in as <a href=\'%1$s\'>%2$s</a>. <a href=\'%3$s\' title=\'Log out of this account\' target=\'_self\'>Log out?</a>',TEXTDOMAIN),admin_url('profile.php'),$user_identity,wp_logout_url($post->comment_link)).'</p>',
		'comment_notes_before' => '<p class="comment-notes">'.__('Your email address will not be published.',TEXTDOMAIN).($req?$required_text:'').'</p>',
		'comment_notes_after' => '<p class="form-allowed-tags">'.sprintf(__('You may use these <abbr title=\'HyperText Markup Language\'>HTML</abbr> tags and attributes: %s',TEXTDOMAIN),' <code>'.allowed_tags().'</code>').'</p>',
	));
	$comment_form = ob_get_contents();
	ob_end_clean();
	echo str_replace('<form ','<form target="_self" ',$comment_form);
else:
	echo "<p class='comments-are-closed'>".__('Comments are closed.',TEXTDOMAIN)."</p>".PHP_EOL;
endif;
?>

<?php

function make_label($string=''){
	$result = '';
	$pattern = array(
		'-' => ' ',
		'_' => ' ',
	);
	$string = str_replace(array_keys($pattern),array_values($pattern),$string);
	$string = ucfirst($string);
	$string = __($string,TEXTDOMAIN);
	$result = $string;
	return $result;
}

function get_properties($post){
	$post = (object) $post;
	$menu_order = $post->menu_order;
	if($menu_order<100000000){
		$menu_order = '1' // dummy
			.DEFAULT_SLIDE_WEIGHT
			.DEFAULT_SLIDE_ANIMATION
			.DEFAULT_SLIDE_TITLE
			.DEFAULT_SLIDE_CONTENT
			.DEFAULT_SLIDE_AUTHOR
			.DEFAULT_SLIDE_DATE
			.DEFAULT_SLIDE_CATEGORY
			.DEFAULT_SLIDE_TAG;
		global $wpdb;
		$wpdb->update($wpdb->posts,array('menu_order'=>$menu_order),array('ID'=>$post->ID),array('%d'),array('%d'));
	}
	$properties = str_split($menu_order);
	$filtered = array(
		'slide_weight' => $properties[1],
		'slide_animation' => $properties[2],
		'slide_title' => $properties[3],
		'slide_content' => $properties[4],
		'slide_author' => $properties[5],
		'slide_date' => $properties[6],
		'slide_category' => $properties[7],
		'slide_tag' => $properties[8],
	);
	return $filtered;
}

function rebuild_post($post){
	$properties = get_properties($post);
	$post->slide_weight = $properties['slide_weight'];
	$post->slide_animation = $properties['slide_animation'];
	$post->slide_title = $properties['slide_title'];
	$post->slide_content = $properties['slide_content'];
	$post->slide_author = $properties['slide_author'];
	$post->slide_date = $properties['slide_date'];
	$post->slide_category = $properties['slide_category'];
	$post->slide_tag = $properties['slide_tag'];

	$post->heading = __heading(get_post_format($post->ID));
	$post->class = implode(' ',get_post_class('',$post->ID));
	$post->filtered_title = apply_filters('the_title',$post->post_title);
	$post->filtered_content = apply_filters('the_content',$post->post_content);

	ob_start();
		the_post_thumbnail('medium');
		$post->filtered_feature_medium = ob_get_contents();
		ob_clean();

		the_author_posts_link();
		$post->filtered_author = ob_get_contents();
		ob_clean();

	ob_end_clean();

	return $post;
}

?>

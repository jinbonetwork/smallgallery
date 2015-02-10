<?php

add_filter('show_admin_bar', '__return_false');

function on_init(){
	add_theme_support('post-thumbnails');
	add_theme_support('post-formats',array('standard','image'));

	if(DEFAULT_POST_FORMAT){
		update_option('default_post_format',DEFAULT_POST_FORMAT);
	}

	register_nav_menu('navigation',__('Navigation',TEXTDOMAIN));
}
add_action('init','on_init');

function on_add_meta_boxes($post){
	$boxes = array(
		'slide_properties' => array(
			'label' => make_label('slide_properties'),
			'callback' => '_metabox',
			'post_type' => 'post',
			'context' => 'side',
			'priority' => 'core',
			'fields' => array(
				'slide_weight' => array(
					'name' => 'slide_weight',
					'type' => 'radios',
					'children' => array(
						array(
							'name' => 'slide_weight',
							'type' => 'radio',
							'label' => 'default',
							'value' => 0,
						),
						array(
							'name' => 'slide_weight',
							'type' => 'radio',
							'label' => 'cover',
							'value' => 1,
						),
					),
				),
				'slide_animation' => array(
					'name' => 'slide_animation',
					'type' => 'radios',
					'children' => array(
					),
				),
				'slide_title' => array(
					'name' => 'slide_title',
					'type' => 'radios',
					'children' => array(
						array(
							'name' => 'slide_title',
							'type' => 'radio',
							'label' => 'hide_title',
							'value' => 0,
						),
						array(
							'name' => 'slide_title',
							'type' => 'radio',
							'label' => 'show_title',
							'value' => 1,
						),
					),
				),
				'slide_content' => array(
					'name' => 'slide_content',
					'type' => 'radios',
					'children' => array(
						array(
							'name' => 'slide_content',
							'type' => 'radio',
							'label' => 'hide_content',
							'value' => 0,
						),
						array(
							'name' => 'slide_content',
							'type' => 'radio',
							'label' => 'show_content',
							'value' => 1,
						),
					),
				),
				'slide_author' => array(
					'name' => 'slide_author',
					'type' => 'radios',
					'children' => array(
						array(
							'name' => 'slide_author',
							'type' => 'radio',
							'label' => 'hide_author',
							'value' => 0,
						),
						array(
							'name' => 'slide_author',
							'type' => 'radio',
							'label' => 'show_author',
							'value' => 1,
						),
					),
				),
				'slide_date' => array(
					'name' => 'slide_date',
					'type' => 'radios',
					'children' => array(
						array(
							'name' => 'slide_date',
							'type' => 'radio',
							'label' => 'hide_date',
							'value' => 0,
						),
						array(
							'name' => 'slide_date',
							'type' => 'radio',
							'label' => 'show_date',
							'value' => 1,
						),
					),
				),
				'slide_category' => array(
					'name' => 'slide_category',
					'type' => 'radios',
					'children' => array(
						array(
							'name' => 'slide_category',
							'type' => 'radio',
							'label' => 'hide_category',
							'value' => 0,
						),
						array(
							'name' => 'slide_category',
							'type' => 'radio',
							'label' => 'show_category',
							'value' => 1,
						),
					),
				),
				'slide_tag' => array(
					'name' => 'slide_tag',
					'type' => 'radios',
					'children' => array(
						array(
							'name' => 'slide_tag',
							'type' => 'radio',
							'label' => 'hide_tag',
							'value' => 0,
						),
						array(
							'name' => 'slide_tag',
							'type' => 'radio',
							'label' => 'show_tag',
							'value' => 1,
						),
					),
				),
			),
		),
	);

	$slide_animation_names = explode(':',SLIDE_ANIMATION_NAMES);
	foreach($slide_animation_names as $index => $name){
		$boxes['slide_properties']['fields']['slide_animation']['children'][] = array(
			'name' => 'slide_animation',
			'type' => 'radio',
			'label' => $name,
			'value' => $index,
		);
	}

	foreach($boxes as $id => $options){
		add_meta_box($id,$options['label'],$options['callback'],$options['post_type'],$options['context'],$options['priority'],$options); // $id, $title, $callback, $post_type, $context, $priority, $callback_args
	}
}
add_action('add_meta_boxes','on_add_meta_boxes');

function on_save_post($post_id){
	if(defined('SMALLGALLERY_ON_SAVE_POST')){
		return;
	}else{
		define('SMALLGALLERY_ON_SAVE_POST',true);
	}

	if($_POST['post_type']!='post'){
		return;
	}

	if(defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE){
		return;
	}

	if(!isset($_POST['slide_properties_nonce'])||!wp_verify_nonce($_POST['slide_properties_nonce'],'slide_properties')){
		return;
	}

	$menu_order = intval('1'.$_POST['slide_weight'].$_POST['slide_animation'].$_POST['slide_title'].$_POST['slide_content'].$_POST['slide_author'].$_POST['slide_date'].$_POST['slide_category'].$_POST['slide_tag']);

	global $wpdb;
	$wpdb->update($wpdb->posts,array('menu_order'=>$menu_order),array('ID'=>$post_id),array('%d'),array('%d'));
}
add_action('save_post','on_save_post');

function on_wp_enqueue_scripts(){
	wp_enqueue_style('font-awesome',get_template_directory_uri().'/contrib/font-awesome/css/font-awesome.min.css');

	wp_enqueue_style('fancybox',get_template_directory_uri().'/contrib/fancybox/source/jquery.fancybox.css');
	wp_enqueue_script('fancybox',get_template_directory_uri().'/contrib/fancybox/source/jquery.fancybox.pack.js',array('jquery'));

	wp_enqueue_style('smallgallery',get_template_directory_uri().'/less.php',array('font-awesome'));
	wp_enqueue_script('smallgallery',get_template_directory_uri().'/script.js',array('jquery','fancybox'));
}
add_action('wp_enqueue_scripts','on_wp_enqueue_scripts');

?>

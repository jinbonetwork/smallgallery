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

function on_after_theme_setup(){
	load_theme_textdomain(TEXTDOMAIN,get_template_directory().'/languages');
}
add_action('after_setup_theme', 'on_after_theme_setup');

function on_wp_enqueue_scripts(){
	global $withcomments;
	$withcomments = 1;

	wp_enqueue_script('comment-reply');

	wp_enqueue_script('jquery-cookie',get_template_directory_uri().'/contrib/jquery-cookie/src/jquery.cookie.js',array('jquery'));

	wp_enqueue_script('imagesloaded',get_template_directory_uri().'/contrib/imagesloaded/imagesloaded.pkgd.min.js',array('jquery'));

	wp_enqueue_script('touchswipe',get_template_directory_uri().'/contrib/touchswipe/jquery.touchSwipe.min.js',array('jquery'));

	wp_enqueue_style('font-awesome',get_template_directory_uri().'/contrib/font-awesome/css/font-awesome.min.css');

	//wp_enqueue_style('perfect-scrollbar',get_template_directory_uri().'/contrib/perfect-scrollbar/min/perfect-scrollbar.min.css');
	//wp_enqueue_script('perfect-scrollbar',get_template_directory_uri().'/contrib/perfect-scrollbar/min/perfect-scrollbar.min.js',array('jquery'));

	wp_enqueue_style('fancybox',get_template_directory_uri().'/contrib/fancybox/source/jquery.fancybox.css');
	wp_enqueue_script('fancybox',get_template_directory_uri().'/contrib/fancybox/source/jquery.fancybox.pack.js',array('jquery'));

	wp_enqueue_style('smallgallery',get_template_directory_uri().'/style.php',array('font-awesome'));
	//wp_enqueue_script('smallgallery',get_template_directory_uri().'/script.js',array('jquery','jquery-cookie','imagesloaded','perfect-scrollbar','fancybox','touchswipe'));
	wp_enqueue_script('smallgallery',get_template_directory_uri().'/script.js',array('jquery','jquery-cookie','imagesloaded','fancybox','touchswipe'));
}
add_action('wp_enqueue_scripts','on_wp_enqueue_scripts');
add_action('wp_head','comments_popup_script');

function smallgallery_head(){
	do_action('smallgallery_head');
}
function on_smallgallery_head(){
	$purge_console = !DEBUG_SCRIPT?'console.log = function(){};'.PHP_EOL:'';
	$title_text = TITLE_TEXT;
	$title_separator = TITLE_SEPARATOR;
	$slide_padding = SLIDE_PADDING;
	$slide_animation_names = "'".implode("','",explode(':',SLIDE_ANIMATION_NAMES))."'";
	$slide_animation_duration = SLIDE_ANIMATION_DURATION;
	echo <<<HEAD
<script>
{$purge_console}
var \$smallgallery = {
	title: {
		text: '{$title_text}',
		separator: '{$title_separator}'
	},
	padding: '{$slide_padding}',
	animation: {
		name: [{$slide_animation_names}],
		duration: '{$slide_animation_duration}'
	}
};
</script>
HEAD;
}
add_action('smallgallery_head','on_smallgallery_head');


function shortcode_thumbnail_archives($options=array()){
	$markup = '';

	$defaults = array(
		'posts_per_page' => -1,
		'size' => 'thumbnail',
		'category' => false,
		'tag' => false,
		'author' => false,
		'ids' => false,
	);
	$options = shortcode_atts($defaults,$options);

	$entries = get_posts($options);
	$markup = !empty($entries)?build_archives($entries):'';

	return $markup;
}
add_shortcode('thumbnail_archives','shortcode_thumbnail_archives');

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
							'label' => 'default slide',
							'value' => 0,
						),
						array(
							'name' => 'slide_weight',
							'type' => 'radio',
							'label' => 'cover slide',
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
							'label' => 'hidden',
							'value' => 0,
						),
						array(
							'name' => 'slide_title',
							'type' => 'radio',
							'label' => 'caption',
							'value' => 1,
						),
						array(
							'name' => 'slide_title',
							'type' => 'radio',
							'label' => 'popup',
							'value' => 2,
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
							'label' => 'hidden',
							'value' => 0,
						),
						array(
							'name' => 'slide_content',
							'type' => 'radio',
							'label' => 'caption',
							'value' => 1,
						),
						array(
							'name' => 'slide_content',
							'type' => 'radio',
							'label' => 'popup',
							'value' => 2,
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
							'label' => 'hidden',
							'value' => 0,
						),
						array(
							'name' => 'slide_author',
							'type' => 'radio',
							'label' => 'caption',
							'value' => 1,
						),
						array(
							'name' => 'slide_author',
							'type' => 'radio',
							'label' => 'popup',
							'value' => 2,
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
							'label' => 'hidden',
							'value' => 0,
						),
						array(
							'name' => 'slide_date',
							'type' => 'radio',
							'label' => 'caption',
							'value' => 1,
						),
						array(
							'name' => 'slide_date',
							'type' => 'radio',
							'label' => 'popup',
							'value' => 2,
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
							'label' => 'hidden',
							'value' => 0,
						),
						array(
							'name' => 'slide_category',
							'type' => 'radio',
							'label' => 'caption',
							'value' => 1,
						),
						array(
							'name' => 'slide_category',
							'type' => 'radio',
							'label' => 'popup',
							'value' => 2,
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
							'label' => 'hidden',
							'value' => 0,
						),
						array(
							'name' => 'slide_tag',
							'type' => 'radio',
							'label' => 'caption',
							'value' => 1,
						),
						array(
							'name' => 'slide_tag',
							'type' => 'radio',
							'label' => 'popup',
							'value' => 2,
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

	$slide_format = $_POST['post_format'];
	$slide_format = $slide_format?$slide_format:'standard';
	$slide_property_names = explode(':',SLIDE_PROPERTY_NAMES);
	foreach($slide_property_names as $slide_property_name){
		$slide_property_name = "slide_{$slide_property_name}";
		$slide_constant_name = strtoupper("default_{$post->post_format}_{$slide_property_name}");
		$slide_property_value = 0;
		if(isset($_POST[$slide_property_name])){
			$slide_property_value = $_POST[$slide_property_name];
		}else if(defined($slide_constant_name)){
			$slide_property_value = constant($slide_constant_name);
		}
		update_post_meta($post_id,$slide_property_name,$slide_property_value);
	}

	return;
}
add_action('save_post','on_save_post');

?>

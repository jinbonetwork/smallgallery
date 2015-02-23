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
	$slide_format = get_post_format($post->ID);
	$slide_format = $slide_format?$slide_format:'standard';
	$slide_properties = array();
	$slide_property_names = explode(':',SLIDE_PROPERTY_NAMES);
	foreach($slide_property_names as $slide_property_name){
		$slide_property_name = "slide_{$slide_property_name}";
		$slide_property_value = get_post_meta($post->ID,$slide_property_name);
		if(empty($slide_property_value)){
			$slide_constant_name = strtoupper("default_{$slide_format}_{$slide_property_name}");
			$slide_property_value = array(constant($slide_constant_name));
			update_post_meta($post->ID,$slide_property_name,$slide_property_value[0]);
		}
		$slide_properties[$slide_property_name] = $slide_property_value[0];
	}
	return $slide_properties;
}

function rebuild_post($post){
	global $wpdb;
	$post->permalink = get_permalink($post->ID);
	$post->author_url = get_author_posts_url($post->post_author);
	$post->comments_number = get_comments_number($post->ID);
	$post->has_comment = $post->comments_number>0||$post->comment_status=='open'||$post->ping_status=='open'?true:false;

	if($post->post_type=='post'){
		$post->pprev_ID = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish' AND post_date < '{$post->post_date}' ORDER BY post_date DESC LIMIT 1,1");
		$post->prev_ID = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish' AND post_date < '{$post->post_date}' ORDER BY post_date DESC LIMIT 1");
		if(!$post->pprev_ID&&$post->prev_ID){
			$post->pprev_ID = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 1");
		}else if(!$post->pprev_ID&&!$post->prev_ID){
			$post->pprev_ID = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 1,1");
			$post->prev_ID = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 1");
		}
		$post->next_ID = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish' AND post_date > '{$post->post_date}' ORDER BY post_date ASC LIMIT 1");
		$post->nnext_ID = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish' AND post_date > '{$post->post_date}' ORDER BY post_date ASC LIMIT 1,1");
		if($post->next_ID&&!$post->nnext_ID){
			$post->nnext_ID = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date ASC LIMIT 1");
		}else if(!$post->next_ID&&!$post->nnext_ID){
			$post->next_ID = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date ASC LIMIT 1");
			$post->nnext_ID = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date ASC LIMIT 1,1");
		}
	}

	if($post->pprev_ID){
		$post->pprev_permalink = get_permalink($post->pprev_ID);
	}

	if($post->prev_ID){
		$post->prev_permalink = get_permalink($post->prev_ID);
	}

	if($post->next_ID){
		$post->next_permalink = get_permalink($post->next_ID);
	}

	if($post->nnext_ID){
		$post->nnext_permalink = get_permalink($post->nnext_ID);
	}

	$post->categories = get_the_category($post->ID);
	$post->tags = get_the_tags($post->ID);
	$post->format = get_post_format($post->ID);
	if(!$post->format){
		$post->format = 'standard';
	}

	$post->post_type_label = $post->post_type=='post'?'slide':$post->post_type;
	$post->edit_link = current_user_can('edit_'.$post->post_type,$post->ID)?get_edit_post_link($post->ID):'';

	$properties = get_properties($post);
	$post->slide_weight = $properties['slide_weight'];
	$post->slide_animation = $properties['slide_animation'];
	$post->slide_feature = has_post_thumbnail($post->ID);
	$post->slide_title = $properties['slide_title'];
	$post->slide_content = $properties['slide_content'];
	$post->slide_author = $properties['slide_author'];
	$post->slide_date = $properties['slide_date'];
	$post->slide_category = $properties['slide_category'];
	$post->slide_tag = $properties['slide_tag'];

	$post->heading_label = __heading_label($post);
	$post->heading = __heading($post);
	$post->classes = get_post_class('',$post->ID);

	$post->classes[] = 'entry';
	$post->classes[] = 'slide-heading-label-'.strtolower($post->heading_label);
	$post->classes[] = 'slide-heading-'.$post->heading;
	$post->classes[] = 'slide-weight-'.$post->slide_weight;
	$post->classes[] = 'slide-animation-'.$post->slide_animation;

	$post->classes[] = 'slide-title-'.$post->slide_title;
	$post->classes[] = 'slide-content-'.$post->slide_content;
	$post->classes[] = 'slide-author-'.$post->slide_author;
	$post->classes[] = 'slide-date-'.$post->slide_date;
	$post->classes[] = 'slide-category-'.$post->slide_category;
	$post->classes[] = 'slide-tag-'.$post->slide_tag;
	if($post->categories){
		foreach($post->categories as $category){
			$post->classes[] = 'category-id-'.$category->term_id;
		}
	}
	if($post->tags){
		foreach($post->tags as $tag){
			$post->classes[] = 'tag-id-'.$tag->term_id;
		}
	}
	$post->class = implode(' ',$post->classes);

	$post->filtered_feature = get_the_post_thumbnail($post->ID,'medium');
	$post->filtered_title = apply_filters('the_title',$post->post_title);
	$post->filtered_content = apply_filters('the_content',$post->post_content);
	$post->filtered_excerpt = apply_filters('get_the_excerpt',$post->post_excerpt);
	ob_start();
		the_post_thumbnail('medium');
		$post->filtered_feature_medium = ob_get_contents();
		ob_clean();

		the_author_posts_link();
		$post->filtered_author = ob_get_contents();
		ob_clean();
	ob_end_clean();

	$post->alt_title = esc_attr(strip_tags($post->filtered_title));
	$post->alt_content = esc_attr(strip_tags($post->filtered_content));
	$post->alt_excerpt = esc_attr(strip_tags($post->filtered_excerpt));

	$post->div_feature = ($post->slide_feature?"<div class='feature'>{$post->filtered_feature}</div>":'').'<!--/.feature-->';
	$post->div_title = ($post->slide_title?"<{$post->heading} class='title'>{$post->filtered_title}</{$post->heading}>":'').'<!--/.title-->';
	$post->div_content = ($post->slide_content?"<div class='content'>{$post->filtered_content}</div>":'').'<!--/.content-->';
	$post->div_author = ($post->slide_author?"<div class='author'>{$post->filtered_author}</div>":'').'<!--/.author-->';
	$post->div_date = ($post->slide_date?"<div class='date'>{$post->filtered_date}</div>":'').'<!--/.date-->';
	$post->div_category = ($post->slide_category?"<div class='category'>{$post->filtered_category}</div>":'').'<!--/.category-->';
	$post->div_tag = ($post->slide_tag?"<div class='tag'>{$post->filtered_tag}</div>":'').'<!--/.tag-->';

	$post->caption_title = $post->slide_title==1?$post->div_title:'';
	$post->caption_content = $post->slide_content==1?$post->div_content:'';
	$post->caption_author = $post->slide_author==1?$post->div_author:'';
	$post->caption_date = $post->slide_date==1?$post->div_date:'';
	$post->caption_category = $post->slide_category==1?$post->div_category:'';
	$post->caption_tag = $post->slide_tag==1?$post->div_tag:'';

	$post->popup_title = $post->slide_title==2?$post->div_title:'';
	$post->popup_content = $post->slide_content==2?$post->div_content:'';
	$post->popup_author = $post->slide_author==2?$post->div_author:'';
	$post->popup_date = $post->slide_date==2?$post->div_date:'';
	$post->popup_category = $post->slide_category==2?$post->div_category:'';
	$post->popup_tag = $post->slide_tag==2?$post->div_tag:'';

	$post->div_caption = build_caption($post);
	$post->div_popup = build_popup($post);

	$post->div_edit_link = ($post->edit_link?"<div class='edit_link'><a href='{$post->edit_link}'><span>".__("Edit this {$post->post_type_label}",TEXTDOMAIN)."</span></a></div><!--/.edit_link-->":'');
	$post->div_popup_link = $post->div_popup?"<div class='popup_link'><a href='#entry-{$post->ID}-popup'><span>".__("Open popup",TEXTDOMAIN)."</span></a></div>":'';
	$post->div_comment_link = $post->has_comment?"<div class='comment_link'><a href='{$post->comment_link}'><span>{$post->comments_number}</span></a></div>":'';
	$post->div_social_links = build_social($post);

	return $post;
}

function build_post($post){
	$markup = '';

	$post->div_comment_link = '';
	switch($post->format){
		case 'standard':
			$post->div_popup_link = '';
		break;
	}

	ob_start();
	echo <<<EOT
<section id="entry-{$post->ID}" class="current {$post->class}"
	data-ID="{$post->ID}"
	data-format="{$post->format}"
	data-animation="{$post->slide_animation}"
	data-pprev_permalink="{$post->pprev_permalink}"
	data-prev_permalink="{$post->prev_permalink}"
	data-next_permalink="{$post->next_permalink}"
	data-nnext_permalink="{$post->nnext_permalink}"
	data-title="{$post->alt_title}"
	data-permalink="{$post->permalink}"
	data-description="{$post->alt_excerpt}"
	data-time-published=""
	data-time-modified=""
	data-author-url="{$post->author_url}"
>
	<div class="wrap">
		{$post->div_edit_link}
		{$post->div_social_links}
		{$post->div_feature}

		<div class="wrap-inner">
			{$post->div_popup_link}
			{$post->div_comment_link}
			{$post->div_caption}
			{$post->div_popup}
		</div>
	</div>
	<!--/#entry-{$post->ID}-->
</section>

EOT;
	$markup = ob_get_contents();
	ob_end_clean();
	return $markup;
}

function build_popup($post){
	$markup;

	if($post->popup_title||$post->popup_content||$post->popup_author||$post->popup_date||$post->popup_category||$post->popup_tag){
		ob_start();
		echo <<<EOT
			<div id="entry-{$post->ID}-popup" class="popup">
				{$post->popup_title}
				{$post->popup_author}
				{$post->popup_date}
				{$post->popup_content}
				{$post->popup_category}
				{$post->popup_tag}
			</div><!--/#entry-{$post->ID}-popup-->
EOT;
		$markup = ob_get_contents();
		ob_end_clean();
	}

	return $markup;
}

function get_comments_popup_link($post){
	$link = '';
	global $wpcommentspopupfile, $wpcommentsjavascript;

	$link = (empty($wpcommentspopupfile)?home_url():get_option('siteurl')).'/'.$wpcommentspopupfile.'?comments_popup='.$post->ID;
	$link = apply_filters('comments_popup_link_attributes',$link);
	return $link;
}

function build_caption($post){
	$markup;

	if($post->caption_title||$post->caption_content||$post->caption_author||$post->caption_date||$post->caption_category||$post->caption_tag){
		ob_start();
		echo <<<EOT
			<div id="entry-{$post->ID}-caption" class="caption">
				{$post->caption_title}
				{$post->caption_author}
				{$post->caption_date}
				{$post->caption_content}
				{$post->caption_category}
				{$post->caption_tag}
			</div><!--/#entry-{$post->ID}-caption-->
EOT;
		$markup = ob_get_contents();
		ob_end_clean();
	}

	return $markup;
}

function build_social($post){
	$markup;

	if(USE_SOCIAL_SHARING){
		$twitter = __('Share with Twitter',TEXTDOMAIN);
		$facebook = __('Share with Facebook',TEXTDOMAIN);
		$googleplus = __('Share with Google+',TEXTDOMAIN);
		$kakaotalk = __('Share with Kakaotalk',TEXTDOMAIN);

		ob_start();
		echo <<<EOT
<div class="social">
	<ul class="items">
		<li class="item twitter"><a href="https://twitter.com/share?u={$post->permalink}"><span>{$twitter}</span></a></li>
		<li class="item facebook"><a href="https://facebook.com/sharer.php?u={$post->permalink}"><span>{$facebook}</span></a></li>
		<li class="item googleplus"><a href="https://plus.google.com/share?url={$post->permalink}"><span>{$googleplus}</span></a></li>
		<li class="item kakaotalk"><a href="{$post->permalink}"><span>{$kakaotalk}</span></a></li>
	</ul>
</div>
EOT;
		$markup = ob_get_contents();
		ob_end_clean();
	}

	return $markup;
}

function build_control($post){
	$control = '';
	if(defined('CONTROL')){
		return $control;
	}

	$prev_label = __('Previous slide',TEXTDOMAIN);
	$next_label = __('Next slide',TEXTDOMAIN);

	ob_start();
	echo <<<EOT
		<nav id="control" class="autofade" data-autofade-default-opacity="0">
			<ul class="items">
				<li class="item prev disabled" data-position="prev"><a href="{$post->prev_permalink}"><span>{$prev_label}</span></a></li>
				<li class="item next disabled" data-position="next"><a href="{$post->next_permalink}"><span>{$next_label}</span></a></li>
			</ul>
		</nav><!--/#control-->
EOT;
	$control = ob_get_contents();
	ob_end_clean();

	define('CONTROL',$control);
	return $control;
}

function build_archives($entries){
	$markup = '';

	$items = array();
	foreach($entries as $entry):
		$entry = rebuild_post($entry);
		$entry->feature = has_post_thumbnail($entry->ID)?get_the_post_thumbnail($entry->ID,'thumbnail',array('alt'=>$entry->alt_title)):"<img src='".DEFAULT_POST_THUMBNAIL."' alt='{$entry->alt_title}'>";
		$items[] = "<li id='thumbnail-{$entry->ID}' class='item'><a href='{$entry->permalink}'>{$entry->feature}</a></li>".PHP_EOL;
	endforeach;

	$markup = implode(PHP_EOL,$items);
	$markup = "<ul class='thumbnail-archives items'>{$markup}</ul>".PHP_EOL;

	return $markup;
}

function rebuild_user($user){
	$user->filtered_email = $user->user_email?str_replace('@',' at ',$user->user_email):'';
	$user->filtered_url = strpos($user->user_url,'http')==0?$user->user_url:'http://'.$user->user_url;
	$user->filtered_description = $user->description?wptexturize($user->description):'';

	$user->div_email = $user->filtered_email?"<div class='email'>{$user->filtered_email}</div>":'';
	$user->div_url = $user->filtered_url?"<div class='url'>{$user->filtered_url}</div>":'';
	$user->div_description = $user->filtered_description?"<div class='description'>{$user->filtered_description}</div>":'';

	return $user;
}

function build_feedback($message){
	$markup = '';

	define('CONTROL',false);
	ob_start();
	echo <<<EOT
<section class="{$message->context} {$message->type} entry format-standard current">
	<div class="wrap">
		<h1 class="title">{$message->title}</h1>
		<div class="description">{$message->description}</div>
		{$message->links}
	</div>
</section>

EOT;
	$markup = ob_get_contents();
	ob_end_clean();

	return $markup;
}

function get_feedback($message){
	echo build_feedback($message);
}

?>

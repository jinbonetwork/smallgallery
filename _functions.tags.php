<?php

function __field($options=array()){
	extract($options);

	$heading = $heading?$heading:'h4';
	$type = esc_attr($type);
	$name = esc_attr($name);
	$label = make_label(($label?$label:$name));
	$value = $type=='textarea'?htmlspecialchars($value):esc_attr($value);
	$disabled = $disabled?" disabled='{$disabled}'":'';
	$checked = $checked?" checked='{$checked}'":'';
	$selected = $selected?" selected='{$selected}'":'';
	if(!empty($children)){
		$children_markup = '';
		foreach($children as $child){
			$children_markup .= __field($child);
		}
	}

	switch($type){
	case 'text':
	case 'password':
	case 'hidden':
	default:
		$markup = <<<EOT
<p>
	<{$heading}><label for="{$name}">{$label}</label></{$heading}>
	<input type="{$type}" name="{$name}" value="{$value}"{$disabled}{$checked}{$selected}/>
</p>
EOT;
		break;
	case 'checkbox':
		$markup = <<<EOT
<div>
	<label>{$label}
		<input type="{$type}" name="{$name}[]" value="{$value}"{$disabled}{$checked}{$selected}/>
	</label>
</div>
EOT;
		break;
	case 'radio':
		$markup = <<<EOT
<div>
	<label>{$label}
		<input type="{$type}" name="{$name}" value="{$value}"{$disabled}{$checked}{$selected}/>
	</label>
</div>
EOT;
		break;
	case 'select':
		$markup = <<<EOT
<p>
	<{$heading}><label for="{$name}">{$label}</label></{$heading}>
	<select name="{$name}">
{$children_markup}
	</select>
</p>
EOT;
		break;
	case 'checkboxes':
	case 'radios':
		$markup = <<<EOT
<p>
	<{$heading}><label>{$label}</label></{$heading}>
{$children_markup}
</p>
EOT;
		break;
	}
	return $markup;
}

function _field($options=array()){
	echo __field($options);
}

function __metabox($post,$options=array()){
	$properties = get_properties($post);
	$options['args']['fields']['slide_weight']['children'][$properties['slide_weight']]['checked']='checked';
	$options['args']['fields']['slide_animation']['children'][$properties['slide_animation']]['checked']='checked';
	$options['args']['fields']['slide_title']['children'][$properties['slide_title']]['checked']='checked';
	$options['args']['fields']['slide_content']['children'][$properties['slide_content']]['checked']='checked';
	$options['args']['fields']['slide_author']['children'][$properties['slide_author']]['checked']='checked';
	$options['args']['fields']['slide_date']['children'][$properties['slide_date']]['checked']='checked';
	$options['args']['fields']['slide_category']['children'][$properties['slide_category']]['checked']='checked';
	$options['args']['fields']['slide_tag']['children'][$properties['slide_tag']]['checked']='checked';

	wp_nonce_field($options['id'],$options['id'].'_nonce');
    foreach($options['args']['fields'] as $field){
		_field($field);
	}
}

function _metabox($post=array(),$options=array()){
	echo __metabox($post,$options);
}

function __heading_label($post){
	$default = strtoupper('heading');
	$in_format = strtoupper($default.'_'.$post->format);
	$in_weight = strtoupper($in_format.'_'.$post->slide_weight);

	if(defined($in_weight)){
		$label = $in_weight;
	}else if(defined($in_format)){
		$label = $in_format;
	}else if(defined($default)){
		$label = $default;
	}

	return $label;
}

function _heading_label($post){
	echo __heading_label($post);
}

function __heading($post){
	$heading = isset($post->heading_label)?constant($post->heading_label):constant(__heading_label($post));
	return $heading;
}

function _heading($post){
	echo __heading($post);
}

function __photoswipe($options=array()){
	$markup = '';
	return $markup;
}

function _photoswipe($options=array()){
	echo __photoswipe($options);
}

?>

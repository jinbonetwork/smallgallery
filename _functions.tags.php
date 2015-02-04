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
	$options['args']['fields'][0]['children'][$properties['slide_weight']]['checked']='checked';
	$options['args']['fields'][1]['children'][$properties['slide_animation']]['checked']='checked';
	$options['args']['fields'][2]['children'][$properties['slide_title']]['checked']='checked';
	$options['args']['fields'][3]['children'][$properties['slide_content']]['checked']='checked';
	$options['args']['fields'][4]['children'][$properties['slide_author']]['checked']='checked';
	$options['args']['fields'][5]['children'][$properties['slide_date']]['checked']='checked';
	$options['args']['fields'][6]['children'][$properties['slide_category']]['checked']='checked';
	$options['args']['fields'][7]['children'][$properties['slide_tag']]['checked']='checked';

	wp_nonce_field($options['id'],$options['id'].'_nonce');
    foreach($options['args']['fields'] as $field){
		_field($field);
	}
}

function _metabox($post=array(),$options=array()){
	echo __metabox($post,$options);
}

function __heading($type=''){
	$heading = '';
	$default = strtoupper('heading');
	$index = strtoupper($default.'_'.$type);
	$heading = defined($index)?constant($index):constant($default);
	return $heading;
}

function _heading($type=''){
	echo __heading($type);
}

function __photoswipe($options=array()){
	$markup = '';
	return $markup;
}

function _photoswipe($options=array()){
	echo __photoswipe($options);
}

?>

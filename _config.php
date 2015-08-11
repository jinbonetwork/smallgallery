<?php
// Development
define('TEXTDOMAIN','smallgallery');
define('DEBUG_CSS',false);
define('DEBUG_SCRIPT',false);
define('CSS_SOURCE',TEMPLATEPATH.'/style.less');
define('CSS_OUTPUT',TEMPLATEPATH.'/style.css');

// Sitewide
define('TITLE_TEXT',esc_attr(get_option('blogname')));
define('TITLE_SEPARATOR',' | ');
define('USE_SOCIAL_SHARING',true);

// LESS variables
define('SLIDE_PADDING','10%');
define('SLIDE_ANIMATION_DURATION','600');
define('SLIDE_ANIMATION_TIMING','linear');
define('UI_ANIMATION_DURATION','300');
define('UI_ANIMATION_TIMING','linear');
define('UI_PORTRAIT_SIZE',64);
define('TEXT_PADDING','20px');

// Editing options
define('SLIDE_ANIMATION_NAMES','horizontal:corner_in:corner_out');

define('HEADING','h1');
define('HEADING_STANDARD','h2');
define('HEADING_STANDARD_0','h2');
define('HEADING_STANDARD_1','h1');
define('HEADING_IMAGE','h3');
define('HEADING_IMAGE_0','h3');
define('HEADING_IMAGE_1','h1');

define('DEFAULT_POST_FORMAT','image'); // set false to make configurable
define('DEFAULT_POST_THUMBNAIL',get_stylesheet_directory_uri().'/images/default-post-thumbnail.png');

define('SLIDE_PROPERTY_NAMES','weight:title:content:author:date:category:tag:animation');
define('DEFAULT_IMAGE_SLIDE_WEIGHT',0);
define('DEFAULT_IMAGE_SLIDE_TITLE',0);
define('DEFAULT_IMAGE_SLIDE_CONTENT',0);
define('DEFAULT_IMAGE_SLIDE_AUTHOR',1);
define('DEFAULT_IMAGE_SLIDE_DATE',0);
define('DEFAULT_IMAGE_SLIDE_CATEGORY',0);
define('DEFAULT_IMAGE_SLIDE_TAG',0);
define('DEFAULT_IMAGE_SLIDE_ANIMATION',0); // array index from SLIDE_ANIMATION_NAMES
define('DEFAULT_STANDARD_SLIDE_WEIGHT',0);
define('DEFAULT_STANDARD_SLIDE_TITLE',1);
define('DEFAULT_STANDARD_SLIDE_CONTENT',1);
define('DEFAULT_STANDARD_SLIDE_AUTHOR',0);
define('DEFAULT_STANDARD_SLIDE_DATE',0);
define('DEFAULT_STANDARD_SLIDE_CATEGORY',0);
define('DEFAULT_STANDARD_SLIDE_TAG',0);
define('DEFAULT_STANDARD_SLIDE_ANIMATION',0); // array index from SLIDE_ANIMATION_NAMES

// Other settings
define('DEFAULT_MENU_FLAG',false);
define('DEFAULT_MENU_FLAG_STRING',DEFAULT_MENU_FLAG?'true':'false');
define('DEFAULT_HELP_FLAG',true);
define('DEFAULT_HELP_FLAG_STRING',DEFAULT_HELP_FLAG?'true':'false');
define('DEFAULT_FULLSCREEN_FLAG',false);
define('DEFAULT_FULLSCREEN_FLAG_STRING',DEFAULT_FULLSCREEN_FLAG?'true':'false');
?>

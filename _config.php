<?php
// Developmental
define('DEBUG_CSS',true);
define('DEBUG_SCRIPT',true);
define('TEXTDOMAIN','smallgallery');

// Sitewide
define('TITLE_TEXT',esc_attr(get_option('blogname')));
define('TITLE_SEPARATOR',' | ');

// LESS variables
define('SLIDE_PADDING','10%');
define('SLIDE_ANIMATION_DURATION','600');
define('SLIDE_ANIMATION_TIMING','linear');
define('UI_ANIMATION_DURATION','300');
define('UI_ANIMATION_TIMING','linear');
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

define('DEFAULT_SLIDE_WEIGHT',0);
define('DEFAULT_SLIDE_TITLE',0);
define('DEFAULT_SLIDE_CONTENT',0);
define('DEFAULT_SLIDE_AUTHOR',1);
define('DEFAULT_SLIDE_DATE',0);
define('DEFAULT_SLIDE_CATEGORY',0);
define('DEFAULT_SLIDE_TAG',0);
define('DEFAULT_SLIDE_ANIMATION',0); // array index from SLIDE_ANIMATION_NAMES

// Other settings
define('DEFAULT_MENU_FLAG',false);
define('DEFAULT_MENU_FLAG_STRING',DEFAULT_MENU_FLAG?'true':'false');
define('DEFAULT_FULLSCREEN_FLAG',false);
define('DEFAULT_FULLSCREEN_FLAG_STRING',DEFAULT_FULLSCREEN_FLAG?'true':'false');
?>

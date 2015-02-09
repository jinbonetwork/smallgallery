<?php

define('TEXTDOMAIN','smallgallery');

define('TITLE_TEXT',esc_attr(get_option('blogname')));
define('TITLE_SEPARATOR',' | ');

define('SLIDE_ANIMATION_DURATION',1000); // number of milliseconds
define('SLIDE_ANIMATION_HORIZONTAL',0);
define('SLIDE_ANIMATION_CORNER_IN',1);
define('SLIDE_ANIMATION_CORNER_OUT',2);

define('HEADING','h1');
define('HEADING_STANDARD','h2');
define('HEADING_STANDARD_0','h2');
define('HEADING_STANDARD_1','h1');
define('HEADING_IMAGE','h3');
define('HEADING_IMAGE_0','h3');
define('HEADING_IMAGE_1','h1');

define('DEFAULT_POST_FORMAT','image'); // set false to make configurable

define('DEFAULT_SLIDE_WEIGHT',0);
define('DEFAULT_SLIDE_TITLE',0);
define('DEFAULT_SLIDE_CONTENT',0);
define('DEFAULT_SLIDE_AUTHOR',1);
define('DEFAULT_SLIDE_DATE',0);
define('DEFAULT_SLIDE_CATEGORY',0);
define('DEFAULT_SLIDE_TAG',0);
define('DEFAULT_SLIDE_ANIMATION',SLIDE_ANIMATION_HORIZONTAL);

define('DEFAULT_MENU_FLAG',false);
define('DEFAULT_MENU_FLAG_STRING',DEFAULT_MENU_FLAG?'true':'false');
define('DEFAULT_CAPTION_FLAG',false);
define('DEFAULT_CAPTION_FLAG_STRING',DEFAULT_CAPTION_FLAG?'true':'false');


?>

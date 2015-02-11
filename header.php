<?php
	define('DEFAULT_MENU_FLAG_STRING',DEFAULT_MENU_FLAG?'true':'false');
	define('DEFAULT_CAPTION_FLAG_STRING',DEFAULT_CAPTION_FLAG?'true':'false');
?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
	<title><?php wp_title(''); ?></title>
	<script>
		var $smallgallery = {
			title: {
				text: '<?php echo TITLE_TEXT; ?>',
				separator: '<?php echo TITLE_SEPARATOR; ?>'
			},
			padding: '<?php echo SLIDE_PADDING; ?>',
			animation: {
				name: [<?php echo "'".implode("','",explode(':',SLIDE_ANIMATION_NAMES))."'".PHP_EOL; ?>],
				duration: '<?php echo SLIDE_ANIMATION_DURATION; ?>'
			}
		};
	</script>
<?php
	ob_start();
	wp_head();
	$head = ob_get_contents();
	ob_end_clean();
	echo "\t".str_replace("\n","\n\t",$head);

	if(has_nav_menu('navigation')){
		$navigation_options = array(
			'theme_location'  => 'navigation',
			'container'       => false,
			'echo'            => false,
			'fallback_cb'     => 'wp_page_menu',
			'link_before'     => '<span>',
			'link_after'      => '</span>',
			'items_wrap'      => '<ul id="menu" class="menu items disabled">%3$s</ul><!--/#menu-->',
			'depth'           => 0,
		);
		$navigation = '<nav id="navigation">'.PHP_EOL
			. '<h2 class="title a11y">'.__('Navigation',TEXTDOMAIN).'</h2>'.PHP_EOL
			. '<ul class="console items">'.PHP_EOL
			. '<li class="toggler-container item"><a id="toggle-navigation" class="toggler" href="#menu" data-flag="'.DEFAULT_MENU_FLAG_STRING.'" data-flag-true-class="enabled" data-flag-false-class="disabled"><span>'.__('Toggle navigation',TEXTDOMAIN).'</span></a></li>'.PHP_EOL
			. '<li class="toggler-container item"><a id="toggle-fullscreen" class="toggler" href="#body" data-flag="'.DEFAULT_FULLSCREEN_FLAG_STRING.'" data-flag-true-class="fullscreen-enabled" data-flag-false-class="fullscreen-disabled"><span>'.__('Fullscreen',TEXTDOMAIN).'</span></a></li>'.PHP_EOL
			. '</ul><!--/.console-->'.PHP_EOL
			. wp_nav_menu($navigation_options).PHP_EOL
			. '</nav><!--/#navigation-->'.PHP_EOL;
	}else{
		$navigation = '';
	}
?>
</head>
<body id="body" <?php body_class(); ?>>
<div id="smallgallery">
	<div id="header-and-navigation" class="autofade" data-autofade-default-opacity="0">
		<header id="header">
			<h1 class="site-title"><a href="<?php bloginfo('siteurl'); ?>"><span><?php bloginfo('title'); ?></span></a></h1>
			<div class="site-description"><?php bloginfo('description'); ?></div><!--/.site-description-->
		</header><!--/#header-->
		<?php echo $navigation; ?>
	</div><!--/#header-and-navigation-->
	<article id="article">
		<div id="container" class="entries wrap">


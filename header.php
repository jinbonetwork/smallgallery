<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php wp_title(''); ?></title>
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
			'menu_class'      => 'menu items',
			'echo'            => false,
			'fallback_cb'     => 'wp_page_menu',
			'link_before'     => '<span>',
			'link_after'      => '</span>',
			'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
			'depth'           => 0,
		);
		$navigation = '<nav id="navigation">'.PHP_EOL
			. '<h2 class="toggler a11y"><a href="#navigation"><span>'.__('Navigation',TEXTDOMAIN).'</span></a></h2>'.PHP_EOL
			. wp_nav_menu($navigation_options).PHP_EOL
			. '</nav><!--/#navigation-->'.PHP_EOL;
	}
?>
	<script>
		var $smallgallery = {
			title: {
				text: '<?php echo TITLE_TEXT; ?>',
				separator: '<?php echo TITLE_SEPARATOR; ?>'
			},
			animation: {
				duration: <?php echo SLIDE_ANIMATION_DURATION; ?>
			}
		};
	</script>
</head>
<body>
<div id="smallgallery">
	<header id="header">
		<h1 class="site-title"><a href="<?php bloginfo('siteurl'); ?>"><span><?php bloginfo('title'); ?></span></a></h1>
		<div class="site-description"><?php bloginfo('description'); ?></div><!--/.site-description-->
	</header><!--/#header-->
	<?php echo $navigation; ?>
	<article id="article">
		<div id="container" class="entries wrap">


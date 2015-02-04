<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php echo get_option('sitename'); ?></title>
<?php
	ob_start();
	wp_head();
	$head = ob_get_contents();
	ob_end_clean();
	echo "\t".str_replace("\n","\n\t",$head);
?>
</head>
<body>
<div id="smallgallery">



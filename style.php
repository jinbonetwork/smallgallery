<?php
define('DIR',dirname(__FILE__));
define('LESS',DIR.'/contrib/lessphp/lessc.inc.php');
define('SOURCE',DIR.'/style.less');
define('OUTPUT',DIR.'/style.css');

if(DEBUG||!file_exists(OUTPUT)||filemtime(SOURCE)>filemtime(OUTPUT)){
	define('WP_USE_THEMES', false);

	//require_once dirname(__FILE__).'/../../../wp-blog-header.php'; // 404 header failure
	require_once dirname(__FILE__).'/../../../wp-config.php';
	$wp->register_globals();
	$wp->send_headers();

	require_once dirname(__FILE__).'/contrib/lessphp/lessc.inc.php';
	$less = new lessc;
	try{
		$less->setVariables(array(
			'slide-padding' => SLIDE_PADDING,
			'slide-animation-duration' => SLIDE_ANIMATION_DURATION.'ms',
			'slide-animation-timing' => SLIDE_ANIMATION_TIMING,
			'ui-animation-duration' => UI_ANIMATION_DURATION.'ms',
			'ui-animation-timing' => UI_ANIMATION_TIMING,
			'text-padding' => TEXT_PADDING,
		));
		$less->setPreserveComments(true);
		if(DEBUG){
			$less->compileFile(SOURCE,OUTPUT);
		}else{
			$less->checkedCompile(SOURCE,OUTPUT);
		}
		$content = file_get_contents(OUTPUT);
	}catch(exception $e){
		$content = $e->getMessage();
	}
}else{
	$content = file_get_contents(OUTPUT);
}

header('Content-type:text/css;charset=UTF-8');
echo $content;
?>

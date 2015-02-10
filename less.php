<?php
define('DIR',dirname(__FILE__));
define('LESS',DIR.'/contrib/lessphp/lessc.inc.php');
define('SOURCE',DIR.'/style.less');
define('OUTPUT',DIR.'/style.css');

require_once dirname(__FILE__).'/contrib/lessphp/lessc.inc.php';
$less = new lessc;
try{
	$less->checkedCompile(SOURCE,OUTPUT);
	$content = file_get_contents(OUTPUT);
}catch(exception $e){
	$content = $e->getMessage();
}

header('Content-type:text/css;charset=UTF-8');
echo $content;
?>

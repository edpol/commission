<?php
// (\ for Windows, / for Unix)
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

if (!defined('SITE_ROOT')) {
	$br = explode(DS,__FILE__); // __FILE__ = D:\wamp\www\fitnesstabs\include\initialize.php
	array_pop($br);
	array_pop($br);
	$ans = implode(DS,$br);	// $ans = D:\wamp\www\fitnesstabs
	define('SITE_ROOT', $ans);
}

defined('LIB_PATH')  ? null : define('LIB_PATH', SITE_ROOT . DS . 'includes');
defined('smtp_user') ? null : define('smtp_user', "fm@mdr.com");
defined('smtp_pass') ? null : define('smtp_pass', "grove33325");
defined('magic_quotes_active') ? null : define('magic_quotes_active', get_magic_quotes_gpc());
defined('real_escape_string_exists') ? null : define('real_escape_string_exists', function_exists( "mysqli_real_escape_string" ));

//echo LIB_PATH.DS."functions.php<br />";
require_once(LIB_PATH.DS.'functions.php');
//echo LIB_PATH.DS."sqlsrv.php<br />";
require_once(LIB_PATH.DS.'sqlsrv.php');
//echo LIB_PATH.DS."mysqli.php<br />";
require_once(LIB_PATH.DS.'mysqli.php');
//echo LIB_PATH.DS."session.php<br />";
require_once(LIB_PATH.DS.'session.php');
//echo "Exit Initialize.. <br />";

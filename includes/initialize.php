<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// (\ for Windows, / for Unix)
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

if (!defined('SITE_ROOT')) {
	$br = explode(DS,__FILE__); // __FILE__ = D:\wamp\www\fitnesstabs\include\initialize.php
	array_pop($br);
	array_pop($br);
	$ans = implode(DS,$br);	// $ans = D:\wamp\www\fitnesstabs
	define('SITE_ROOT', $ans);
}

if(!defined('LIB_PATH'))                  define('LIB_PATH', SITE_ROOT . DS . 'includes');
if(!defined('real_escape_string_exists')) define('real_escape_string_exists', function_exists( "mysqli_real_escape_string" ));

//echo LIB_PATH.DS."functions.php<br />";
require_once(LIB_PATH.DS.'functions.php');
//echo LIB_PATH.DS."sqlsrv.php<br />";
require_once(LIB_PATH.DS.'sqlsrv.php');
//echo LIB_PATH.DS."mysqli.php<br />";
require_once(LIB_PATH.DS.'mysqli.php');
//echo LIB_PATH.DS."session.php<br />";
require_once(LIB_PATH.DS.'session.php');
//echo "Exit Initialize.. <br />";

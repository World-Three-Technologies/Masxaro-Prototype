<?php
/**
 * Configuration file
 * 
 * @author Yaxing Chen
 * @team SET
 * @date 03/25/2011
 */

define('ROOT_PATH', dirname(__FILE__));

ini_set('default_charset', 'utf-8');

ini_set('include_path', 
			ROOT_PATH.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'common'.DIRECTORY_SEPARATOR
			
			//ROOT_PATH.'/php/common/'
        );
	
define ( "IS_DEBUG", true );
//define ( "IS_DEBUG", FALSE );

if (IS_DEBUG) {
	ini_set ( 'display_errors', 'on' );
	//error_reporting ( E_ALL );
	error_reporting((E_ALL ^ E_NOTICE)|E_USER_ERROR|E_USER_WARNING);
} else {
	ini_set ( 'display_errors', 'off' );
	error_reporting ( 0 );
}

ini_set ( 'display_startup_errors', IS_DEBUG );
	
///configuration file
function __autoload($className) {
	include_once($className.'.class.php');
}

//session manage
session_start();

define("PAGE_SIZE", "20");

?>
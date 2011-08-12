<?php
/*
 * config.php -- application configuration 
 *
 *  Copyright 2011 World Three Technologies, Inc. 
 *  All Rights Reserved.
 * 
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 *  Written by Yaxing Chen <Yaxing@masxaro.com>
 * 
 *  Configuration file
 */

define('ROOT_PATH', dirname(__FILE__));
ini_set('default_charset', 'utf-8');

ini_set('include_path', 
			ROOT_PATH.'/php/:'
			.ROOT_PATH.'/php/class/:'
			.ROOT_PATH.'/php/class/common/:'
			.ROOT_PATH.'/php/class/builder/:'
			.ROOT_PATH.'/php/class/control/:'
			.ROOT_PATH.'/php/class/entity/:'
			.ROOT_PATH.'/php/class/unitTest/:'
			.ROOT_PATH.'/php/header.php:'
			.ROOT_PATH.'/php/class/library:'
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
	
function __autoload($className) {
	if(preg_match('(^Zend)', $className)){
		include_once ROOT_PATH.'/php/class/library/Zend/Loader.php';
	}
	else{
		include_once($className.'.class.php');
	}
}

//session manage
session_start();

define("PAGE_SIZE", "20");

date_default_timezone_set("UTC");

/*
 * general config
 */
define('MIN_ACC_LEN', 6);//minimal user/store account length, used for verify code generation
define('CON_DELIMITER', ':');//delimiter to for operator and operator tag in query conditions

/*
 * db config
 */
//AWS db
//define('DB_HOST', '46.51.255.119');
//define('DB_USER', 'w3t');
//define('DB_PWD', 'w3t');
//define('DB_DBNAME', 'w3tdb');

//AWS db - free
define('DB_HOST', '50.19.213.157');
define('DB_USER', 'w3t');
define('DB_PWD', 'w3t');
define('DB_DBNAME', 'w3tdb');

//Godaddy db
//define('DB_HOST', 'w3tdb.db.7762973.hostedresource.com');
//define('DB_USER', 'w3tdb');
//define('DB_PWD', 'W3TAdmin');
//define('DB_DBNAME', 'w3tdb');

/*
 * email config
 */
define('DOMADMIN_EMAIL', 'bws@masxaro.net');
define('DOMADMIN_PWD', 'Masxaro2011!');
define('DOMAIN', 'masxaro.net');
define('IMAP_HOST', '{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX');
define('EMAIL_DIR', ROOT_PATH.'/masxaro_email_tmp');

if(!is_dir(EMAIL_DIR)){
	mkdir(EMAIL_DIR);
}

/*
 * register config
 */
$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$reg_v_url = substr($url, 0, strripos($url, "/") + 1).'verifyRegister.php';
$receipt_url = substr($url, 0, strripos($url, "/") + 1).'receiptOperation.php';

define('REGISTER_V_URL', $reg_v_url);
define('RECEIPT_URL', $receipt_url);

?>

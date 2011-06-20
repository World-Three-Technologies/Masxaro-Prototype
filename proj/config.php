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
			.ROOT_PATH.'/php/class/control/:'
			.ROOT_PATH.'/php/class/entity/:'
			.ROOT_PATH.'/php/class/unitTest/:'
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

date_default_timezone_set("UTC");

?>

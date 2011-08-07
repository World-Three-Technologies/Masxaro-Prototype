<?php
/*
 *  header.php -- backend header file 
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
 *  
 */
include_once '../config.php';

	$test = "[{\"json\":{\"receipt_id\":\"105\",\"user_account\":\"new\",\"items\":[{\"receipt_id\":\"105\",\"item_price\":\"5\",\"item_name\":\"coke\",\"item_id\":\"12\",\"item_qty\":\"1\"},{\"receipt_id\":\"105\",\"item_price\":\"2\",\"item_name\":\"fries-mid\",\"item_id\":\"10\",\"item_qty\":\"1\"}],\"acc\":\"new\",\"receipt\":{\"receipt_id\":\"105\",\"store_name\":\"McD\", \"user_account\":\"new\"}}]";
	$post = str_replace("\\", "", $test);
	$post = json_decode($post, true);
	$post = $post['json'];
	echo $test;
	die();
$post = null;

if(isset($_POST['json'])){
	/**
	 * @desc 
	 * this is used to accept JSON data from special senario,
	 * mobile end for example
	 */
	$post = str_replace("\\", "", $_POST['json']);
	$post = json_decode($post, true);
	$post = $post['json'];
}
else{
	$post = $_POST;
}

?>
u<?php

/*
 *  login.php -- user/store login 
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
 *  user/store login
 */
include_once '../config.php';
include_once 'header.php';

$acc = $post['acc'];
$pwd = $post['pwd'];
$type = $post['type']; // string, 'user' or 'store'

ob_start();

$ctrl = null;
switch($type){
	case 'user':
		$ctrl = new UserCtrl();
    	break;
		
	case 'store':
		$ctrl = new StoreCtrl();
		break;
		
	default:
		die('wrong parameters');
}

$result = $ctrl->find($acc, $pwd);

if($result < 0){
	die('not verified or illegal login information');
}

if($result == 0){
	die('wrong account or password');
}

echo Tool::login($acc, $type);

updateEmailReceipts($acc);

ob_end_flush();

function updateEmailReceipts($acc){
	$path = "50.19.213.157/masxaro_backend/proj/php/receiptOperation.php";
	$param = "acc=$acc&opcode=grab_email_receipts";
	
	$ch = curl_init($path);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
	
	$result = curl_exec($ch);
}
?>

<?php
/*
 *  verifyRegister.php -- verify registration based on code 
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

$code = $_GET['code'];

// registration code verification
if(!isset($code)){
	die('code is necessary');
}

$ctrl = null;

$info = Tool::decodeVerifyCode($code);

$acc = $info[1];
$pwd = $info[2];

switch($info[0]){
	case 'user':
		$ctrl = new UserCtrl();
		break;
		
	case 'user':
		$ctrl = new StoreCtrl();
		break;
		
	default:
		die('wrong code');
}

$tmp = $ctrl->find($acc, $pwd);

if($tmp < 0){
//	$emailCtrl = new EmailCtrl();
//	echo $emailCtrl->createUserAcc($acc, Tool::getPassword($acc)) 
//		? $ctrl->update($acc, array('verified'=>true)) : 'verification failed.';
//	die();
	die('verification success');
}

if($tmp == 0){
	die('wrong verification');
}

else{
	die('already verified');
}

?>
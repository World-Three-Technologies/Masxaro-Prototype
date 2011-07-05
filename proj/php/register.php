<?php
/*
 *  register.php -- user/store register 
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
 *  post type indicating whether user or store register
 *  
 *  code is from verification email. if code is set, then verify the registration.
 * 
 */

include_once '../config.php';
include_once 'header.php';

$personEmail = "";
$ctrl = null;
$accType = "";
$verifyPage = "verifyRegister.php";

$registerType = $post['type']; //user / store

//normal register
switch($registerType){
	case 'user':
		$param = array(
					'user_account'=>$post['userAccount'], 
					'first_name'=>$post['firstName'],
					'age_range_id'=>$post['ageRangeId'],
					'ethnicity'=>$post['ethnicity'],
					'pwd'=>$post['pwd'],
					'opt_in'=>$post['optIn']
		);
		
		$accType = 'user_account';
		$ctrl = new UserCtrl();
		break;
		
	case 'store':
		$param = array( 
					'store_account'=>$post['storeAccount'],
					'store_name'=>$post['storeName'],
					'parent_store_account'=>$post['parentStoreAcc'],
					'store_type'=>$post['storeType'],
					'pwd'=>$post['pwd']
		);
		
		$accType = 'store_account';
		$ctrl = new StoreCtrl();
		break;
		
	default:
		die("incorrect register information");
}


$personEmail = $post['email'];

if($ctrl->insert($param)){

	$contacts = array();
	
	//masxaro email
	$email = "$param[$accType]@".DOMAIN;
	array_push($contacts, array(
							$accType=>$param[$accType],
							'contact_type'=>'email',
							'value'=>$email
						)
	);
				
	//personal email
	$email = $personEmail;
	array_push($contacts, array(
							$accType=>$param[$accType],
							'contact_type'=>'email',
							'value'=>$email
						)
	);
	
	$ctrlCon = new ContactCtrl();
	
	if(!$ctrlCon->insertContact($contacts)){
		//rollback
		$ctrlCon->deleteAccContact($param[$accType]);
		switch($registerType){
			case 'user':
				$ctrl->realDeleteUser($param[$accType]);
				break;
			case 'store':
				$ctrl->delete($param[$accType]);
		}
		die("insert contacts information failed");
	}
	
	$codeParam = array(
		$registerType,
		$param[$accType],
		$param['pwd'],
		$personEmail
	); 
	$code = Tool::verifyCodeGen($codeParam);
	
	$mailSub = "Please verify your registration on Masxaro.com";
	$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	$url = substr($url, 0, strripos($url, "/") + 1).'verifyRegister.php';
	$code = $url."?code=$code";
	
	$email = new EmailCtrl();
	$mailContent = "
				<html>
				<head>
				  <title>Masxaro registration verification</title>\n
				</head>
				<body>
				  <p>Please click following link to verify your registration!</p>
				  $code
				</body>
				</html>
	";
	
	if($email->mail($personEmail, $mailSub, $mailContent)){
		echo "Register Success, please check your mailbox for authenticate\n";
	}
}

else{
	die("Register failed, please check your register information");
}
?>

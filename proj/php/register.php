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

$registerType = $_REQUEST['type']; //user / store

$code = $_REQUEST['code'];

$personEmail = "";

$ctrl = null;

$accType = "";

if(isset($code)){
	$info = Tool::decodeVerifyCode($code);
	$ctrl = new UserCtrl();
	echo $ctrl->updateUserInfo($info[0], array('verified'=>true));
	die();
}

switch($registerType){
	
	case 'user':
		$param = array(
					'user_account'=>$_REQUEST['userAccount'], 
					'first_name'=>$_REQUEST['firstName'],
					'age_range_id'=>$_REQUEST['ageRangeId'],
					'ethnicity'=>$_REQUEST['ethnicity'],
					'pwd'=>$_REQUEST['pwd'],
					'opt_in'=>$_REQUEST['optIn']
		);
		
		$accType = 'user_account';
		$ctrl = new UserCtrl();
		break;
		
		
	case 'store':
		$param = array( 
					'store_account'=>$_REQUEST['storeAccount'],
					'store_name'=>$_REQUEST['storeName'],
					'parent_store_account'=>$_REQUEST['parentStoreAcc'],
					'store_type'=>$_REQUEST['storeType'],
					'pwd'=>$_REQUEST['pwd']
		);
		
		$acc = 'store_account';
		$ctrl = new StoreCtrl();
		break;
		
	default:
		echo false;
		die();
		
}

$personEmail = $_REQUEST['email'];

if($ctrl->insert($param)){

	$contacts = array();
	
	//masxaro email
	$email = $param[$accType].'@masxaro.com';
	array_push($contacts, array(
						$accType=>$param[$accType],
						'contact_type'=>'email',
						'value'=>$email)
	);
				
	//personal email
	$email = $personEmail;
	array_push($contacts, array(
						$accType=>$param[$accType],
			'contact_type'=>'email',
			'value'=>$email)
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
		echo false;
		die();
	}
	else{
		echo true;
	}
	
	$codeParam = array(
		$param[$accType],
		$personEmail
	); 
	
	$mailSub = "Please verify your registration on Masxaro.com";

	$url = "http://".$_SERVER ['HTTP_HOST'].$_SERVER['PHP_SELF'];
	
	$code = Tool::verifyCodeGen($codeParam);
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
	
	$email->mail($personEmail, $mailSub, $mailContent);
}

else{
	echo false;
}
?>
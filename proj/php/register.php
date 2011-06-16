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
 */

include_once '../config.php';

$registerType = $_POST['type']; //user / store

//$registerType = 'user';

switch($registerType){
	case 'user':
		
		$param = array(
					'user_account'=>$_POST['userAccount'], 
					'first_name'=>$_POST['firstName'],
					'age_range_id'=>$_POST['ageRangeId'],
					'ethnicity'=>$_POST['ethnicity'],
					'pwd'=>$_POST['pwd'],
					'opt_in'=>$_POST['optIn']
		);

//		$param = array(
//					'user_account'=>'newww', 
//					'first_name'=>'W3tTest',
//					'age_range_id'=>'null',
//					'ethnicity'=>null,
//					'pwd'=>'123',
//					'opt_in'=>'null'
//		);
		
		$ctrl = new UserCtrl();
		
		if(!$ctrl->chkAccount($param['user_account'])){
			echo false;
			die();
		}
		
		if($ctrl->insertUser($param)){
			$email = $param['user_account'].'@masxaro.com';
			
			$info = array();
			
			array_push($info, array(
								'user_account'=>$param['user_account'],
								'contact_type'=>'email',
								'value'=>$email)
			);
			
			$ctrlCon = new ContactCtrl();
			
			if(!$ctrlCon->insertContact($info)){
				//rollback
				$ctrl->realDeleteUser($param['user_account']);
				echo false;
			}
			else{
				echo true;
			}
		}
		
		else{
			echo false;
		}
		
		break;
	
	case 'store':
		
		$param = array( 
					'store_name'=>$_POST['storeName'],
					'parent_store_account'=>$_POST['parentStoreAcc'],
					'store_type'=>$_POST['storeType'],
					'pwd'=>$_POST['pwd']
		);
		
//		$param = array( 
//					'store_account'=>'Mc_NYU',
//					'store_name'=>'McDonalds(NYU)',
//					'parent_store_account'=>null,
//					'store_type'=>'normal',
//					'pwd'=>'123'
//		);
		
		$ctrl = new StoreCtrl();
		
		
		
		if($ctrl->insertStore($param)){
			$email = $param['store_account'].'@masxaro.com';
			
			$info = array();
			
			array_push($info, array(
								'store_account'=>$param['store_account'],
								'contact_type'=>'email',
								'value'=>$email)
			);
			
			$ctrlCon = new ContactCtrl();
			
			if(!$ctrlCon->insertContact($info)){
				//rollback
				$ctrl->deleteStore($param['store_account']);
				echo false;
			}
			else{
				echo true;
			}
		}
		
		break;
}

?>
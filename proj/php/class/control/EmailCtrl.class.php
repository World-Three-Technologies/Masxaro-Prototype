<?php

/*
 *  EmailCtrl.class.php -- email control, email receive & send 
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
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Gapps');

class EmailCtrl extends Ctrl{
	private $client;
	private $service;
	
	function __construct(){
		parent::__construct();
		//$this->initGdata();
	}
	
	private function initGdata(){
		try{
			$this->client = Zend_Gdata_ClientLogin::getHttpClient(
																DOMADMIN_EMAIL, 
																DOMADMIN_PWD, 
																Zend_Gdata_Gapps::AUTH_SERVICE_NAME
															);
			$this->service = new Zend_Gdata_Gapps($this->client, DOMAIN);
		}catch(Exception $e){
			die('Zend error');
		}
	}
	
	/**
	 * 
	 * 
	 * @param string $to
	 * 
	 * @param string $subject
	 * 
	 * @param string $message
	 * 
	 * @param string $addHeaders
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * send email
	 */
	public function mail($to, $subject, $message, $addHeaders = ""){
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		// Additional headers
		$headers .= 'From: Masxaro <masxaro-notice@masxaro.com>' . "\r\n";
		$headers .= $addHeaders;
		
		return mail($to, $subject, $message, $headers);
	}
	
	/**
	 * 
	 * 
	 * @param string $acc
	 * 
	 * @return 
	 */
	public function grabEmail($acc){
		
	}
	
	/**
	 * 
	 * 
	 * @param strint $username
	 * 
	 * @param string $password
	 * 
	 * @param string $givenName
	 * 
	 * @param string $familyName
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * create a masxaro email account for a new user.
	 */
	public function createUserAcc($username, $password, $givenName = '', $familyName = ''){
		try{
			$this->service->createUser(
									$username, 
									$givenName, 
									$familyName, 
									$password, 
									$passwordHashFunction=null, 
									$quota=null
								);
		}catch(Exception $e){
			return false;
		}
		return true;
	}
	
	/**
	 * 
	 * 
	 * @param string $username
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * suspend an account
	 */
	public function suspendAcc($username){
		try{
			$this->service->suspendUser($username);
		}catch(Exception $e){
			return false;
		}
		return true;
	}
	
/**
	 * 
	 * 
	 * @param string $username
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * delete an account
	 */
	public function deleteAcc($username){
		try{
			$this->service->deleteUser($username);
		}catch(Exception $e){
			return false;
		}
		return true;
	}
	
	/**
	 * 
	 * 
	 * @param string $username
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * restore a suspended account
	 */
	public function restoreAcc($username){
		try{
			$this->service->restoreUser($username);
		}catch(Exception $e){
			return false;
		}
		return true;
	}
	
}

?>
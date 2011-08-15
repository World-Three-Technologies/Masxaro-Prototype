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

	function __construct() {
		parent::__construct();
		
		/*
		 * Zend Gmail API init
		 */
		//$this->initGdata();
	}
	
	protected function initGdata() {
		try{
			$this->client = Zend_Gdata_ClientLogin::getHttpClient(
																DOMADMIN_EMAIL, 
																DOMADMIN_PWD, 
																Zend_Gdata_Gapps::AUTH_SERVICE_NAME
															);
			$this->service = new Zend_Gdata_Gapps($this->client, DOMAIN);
		}catch(Exception $e) {
			die('Zend error');
		}
	}
	
	/**
	 * 
	 * @desc
	 * generate alias email account for new user
	 * 
	 * @param string $userAcc
	 * @return string $emailAcc user email address of masxaro.net
	 */
	public function aliasMailAccGen($userAcc) {
		return BASE_ACC."+$userAcc@".DOMAIN;
	}
	
	/**
	 * 
	 * 
	 * @param string $url verificaton url with register code
	 * 
	 * @desc
	 * generate registration confirmation email content
	 */
	public function registerEmailGen($url) {
		$email = "
				<html>
					<head>
					  <title>Masxaro registration verification</title>\n
					</head>
					<body>
					  <p>Please click <a href='$url'><strong>here</strong></a> to verify your registration!</p>
					</body>
				</html>
		";
		return $email;
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
	public function mail($to, $subject, $message, $addHeaders = "") {
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
	 * @param string $personalEmail client email address
	 * 
	 * @param array(string) $codeParam parameters for code generation
	 * include:
	 * 	{
	 * 		registerType,
	 *		account,
	 *		password,
	 *		personEmail
	 *	}
	 * 
	 * @desc
	 * send register confirmation email
	 * 
	 * @return
	 * boolean
	 * 
	 * @example
	 * $codeParam = array(
	 *		registerType=>'user',
	 *		account=>'newAccount',
	 *		pwd=>'123456',
	 *		personalEmail=>'user@eg.com'
	 *	); 
	 */
	public function sendRegisterEmail($personalEmail, $codeParam) {
		$code = Tool::verifyCodeGen($codeParam);
		
		$subject = "Please verify your registration on Masxaro.com";
		return $this->mail(
						$personalEmail, 
						$subject, 
						$this->registerEmailGen(REGISTER_V_URL."?code=$code")
				);
	}
	
	/**
	 * 
	 * 
	 * @param string $acc
	 * 
	 * @return 
	 */
	public function grabEmails($acc) {
		$username = BASE_ACC.'@'.DOMAIN;
		//$password = Tool::getEmailPwd($acc);
		$password = BASE_ACC_PWD;
		
		$inbox = imap_open(IMAP_HOST, $username, $password) 
				or die('Cannot connect to mailbox'.imap_last_error());
				
		$emails = imap_search($inbox, 'UNSEEN');
		
		$emails_file = array();
		
		if($emails) {
			$output = '';
			rsort($emails);
			foreach($emails as $email_number) {
				$overview = imap_fetch_overview($inbox,$email_number);
				
				if($overview[0]->to != $this->aliasMailAccGen($acc)) {
					continue;
				}
				
				$subject = $overview[0]->subject;

				/**
				 * @todo check subject
				 **/
//				if(!preg_match("/.*receipt.*/i", $subject) && !preg_match("/.*order.*/i", $subject)) {
//					continue;
//				}
				
				$message = html_entity_decode($subject . imap_fetchbody($inbox,$email_number,2));
				$header = imap_headerinfo($inbox, $email_number);
				$from = "{$header->from[0]->mailbox}@{$header->from[0]->host}";
				$contactCtrl = new ContactCtrl();
				$storeAcc = null;
				if(($storeAcc = $contactCtrl->getContactAccount($from, 'store')) == null) {
					continue;
				}
				
				array_push($emails_file, array('from'=>$storeAcc, 'message'=>$message));
				
				//$overview = imap_fetch_overview($inbox,$email_number);
//				$output.= '<div class="toggler '.($overview[0]->seen ? 'read' : 'unread').'">';
//				$output.= '<span class="subject">'.$overview[0]->subject.'</span> ';
//				$output.= '<span class="from">'.$overview[0]->from.'</span>';
//				$output.= '<span class="date">on '.$overview[0]->date.'</span>';
//				$output.= '</div>';
			}
			
			return $this->saveEmails($emails_file, $acc);
		}
		else{
			return 'no email';
		}
		
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
	public function createUserAcc($username, $givenName = 'N/A', $familyName = 'N/A') {
		try{
			$this->service->createUser(
									$username, 
									$givenName, 
									$familyName, 
									Tool::getEmailPwd($username), 
									$passwordHashFunction=null, 
									$quota=null
								);
		}catch(Exception $e) {
			echo $e;
			die();
			//return false;
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
	public function suspendAcc($username) {
		try{
			$this->service->suspendUser($username);
		}catch(Exception $e) {
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
	public function deleteAcc($username) {
		try{
			$this->service->deleteUser($username);
		}catch(Exception $e) {
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
	public function restoreAcc($username) {
		try{
			$this->service->restoreUser($username);
		}catch(Exception $e) {
			return false;
		}
		return true;
	}
	
	
	public function retrieveAllUser() {
		return $this->service->retrieveAllUsers();
	}
	
	public function retrieveUser($acc) {
		return $this->service->retrieveUser($acc);
	}
	
	
	/**
	 * 
	 * 
	 * @param array emails array of emails, each line is an email, containing 'from' & 'message'
	 * @example array(
	 * 				array('from'=>'gmail team', 'message'=>'test'), 
	 * 				array()...
	 * 			);
	 * 
	 * @param string $userAcc user account
	 * 
	 * @desc
	 * save grabbed email receipts to files
	 */
	public function saveEmails($emails, $userAcc) {
		try{
			$curDir = EMAIL_DIR."/$userAcc";
			if(!is_dir($curDir)) {
				mkdir($curDir);
			}
			
			$dir = opendir($curDir);
			
			foreach($emails as $email) {
				$curFile = null;
				for($i = 0; $i < 1000 ; $i ++) {
					$tmpName = $curDir."/{$email['from']}:::$i";
					if(is_file($tmpName)) {
						continue;
					}
					else{
						$curFile = fopen($tmpName, 'w');
						break;
					}
				}
				fputs($curFile, "{$email['message']}");
				fclose($curFile);
			}
			closedir($dir);
			return true;
		}catch(Exception $e) {
			return $e->getMessage();
		}
	}
}

?>
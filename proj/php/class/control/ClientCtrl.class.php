<?php
/*
 *  UserCtrl.class.php -- user controller 
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

abstract class ClientCtrl extends Ctrl{
	
	protected $clientType = null;//store or user
	
	function __construct($clientType){
		$this->clientType = $clientType;
		parent::__construct();
	}
	
	/**
	 * 
	 * @param string $acc
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * check whether a certain account is available
	 */
	public function chkAccount($acc){
		
		if(!Tool::securityChk($acc)){
			return false;
		}
		
		$sql = "
			SELECT 
				count(*) as count
			FROM 
				`user`, `store`
			WHERE
				`user_account`='$acc'
			OR
				`store_account`='$acc'
		";
		
		if($this->db->select($sql) < 0){
			return false;
		}
		
		$result = $this->db->fetchObject();
		
		if($result[0]->count == 0){
			return true;
		}
		
		return false;
	}
	
	/**
	 * 
	 * 
	 * @param array() $info
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * insert a new client(store/user), depending on instances of ClientCtrl (UserCtrl or StoreCtrl)
	 */
	public function insert($info){
		
		$info['pwd'] = md5($info['pwd']);
		
		$info = Tool::infoArray2SQL($info);
		
		if(!Tool::securityChk($info)){
			return false;
		}
		
		$sql = "
			INSERT
			INTO 
				`$this->clientType`
			SET
				$info
		";
			
		if($this->db->insert($sql) < 0){
			return false;
		}
		
		return true;
	}
	
	
	/**
	 * 
	 * 
	 * @param string $acc
	 * 
	 * @param string $pwd
	 * 
	 * @return int 1: found, verified, 0: not found, -1: found, unverified
	 * 
	 * @desc
	 * 
	 * find client(store/user), depending on instances of ClientCtrl (UserCtrl or StoreCtrl)
	 * 
	 */
	public function find($acc, $pwd){
		if(!Tool::securityChk($acc)){
			return false;
		}
		
		$pwd = md5($pwd);
		
		$sql = "
			SELECT 
				*
			FROM 
				`$this->clientType`
			WHERE
				`{$this->clientType}_account`='$acc'
			AND
				`pwd`='$pwd'
		";
		
		$this->db->select($sql);
		$result = $this->db->fetchAssoc();
		if($this->db->numRows() == 1){
			if($result[0]['verified'] == true){
				return 1;
			}
			else{
				return -1;
			}
		}
		
		return 0;
	}
	
	/**
	 * @param string $acc
	 * 
	 * @param array() $info
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * 
	 * update client(store/user), depending on instances of ClientCtrl (UserCtrl or StoreCtrl)
	 * 
	 */
	public function update($acc, $info){
		$info = Tool::infoArray2SQL($info);
		
		if(!Tool::securityChk($info)){
			return false;
		}
		
		$sql = "
			UPDATE 
				`$this->clientType`
			SET
				$info
			WHERE
				`{$this->clientType}_account`='$acc'
		";
			
		if($this->db->update($sql) <= 0){
			return false;
		}
		
		return true;
	}
	
	/**
	 * 
	 * 
	 * @param string $acc
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * 
	 * delete a client(store/user), depending on instances of ClientCtrl (UserCtrl or StoreCtrl)
	 */
	public function delete($acc){
		
		$sql = "
			DELETE
			FROM 
				`$this->clientType`
			WHERE
				`{$this->clientType}_account`='$acc'
		";
		
		if($this->db->delete($sql) <= 0){
			return false;
		}
		
		return true;
	}
	
	/**
	 * 
	 * 
	 * @param string $acc
	 * 
	 * @return object
	 * 
	 * @desc
	 * 
	 * according to client account, return client profile object (store/user)
	 * depending on instances of ClientCtrl (UserCtrl or StoreCtrl)
	 */
	public function getProfile($acc){
		$sql = "
			SELECT 
				*
			FROM 
				`$this->clientType`
			WHERE 
				`{$this->clientType}_account`='$acc'
			AND 
				`verified`=1
		";

		
		$this->db->select($sql);
		$result = $this->db->fetchAssoc();

    $contactCtrl = new ContactCtrl();
    foreach($contactCtrl->getContacts($acc,"user") as $contact){
      $email = explode("@",$contact["value"],2);
      if($email[1] == "masxaro.com"){
        $result[0]["masxaro"] = $contact["value"];
      }else{
        $result[0]["personal"] = $contact["value"];
      }
    };
		
		return $result[0];
	}
}

?>

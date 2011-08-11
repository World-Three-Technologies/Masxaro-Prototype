<?php
/*
 *  contactCtrl.class.php -- contact control class 
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
 *  contact control APIs
 */
class ContactCtrl extends Ctrl{
	
	public $regex = "(^.*@masxaro)";
	
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * 
	 * 
	 * @param string $value
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * 
	 * check whether the certain contact is available, true: available, false: not available
	 */
	public function chkContact($value){
		
		$sql = "
			SELECT 
				`value`
			FROM 
				`contact`
			WHERE
				`value` = '$value'
		";
		
		$this->db->select($sql);
		
		if($this->db->numRows() > 0){
			return false;
		}
		
		return true;
	}
	
	/**
	 * 
	 * @param string $type
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * 
	 * insert a new contact type
	 */
	public function insertContactType($type){
		if(!Tool::securityChk($type)){
			return false;
		}
		
		$sql = "
			INSERT 
			INTO 
				`contact_type`
			SET
				`contact_type`='$type'
		";
		
		if($this->db->insert($sql) < 0){
			return false;
		}
		
		return true;
	}
	
	
	/**
	 * 
	 * 
	 * @param string $old
	 * 
	 * @param string $new
	 */
	public function modifyContactType($old, $new){
		$sql = "
			UPDATE
				`contact_type`
			SET
				`contact_type`='$new'
			WHERE
				`contact_type`='$old'
		";
		
		if($this->db->update($sql) <= 0){
			return false;
		}
		return true;
	}
	
	
	/**
	 * 
	 * 
	 * @param string $type
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * delete contact type
	 */
	public function deleteContactType($type){
		$sql = "
			DELETE
			FROM 
				`contact_type`
			WHERE
				`contact_type`='$type'
		";
		
		if($this->db->delete($sql) <= 0){
			return false;
		}
		
		return true;
	}
	
	/**
	 *
	 * @param array(array(), ...) $info one line as one contact, in order for batch processing
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * 
	 * insert a new contact for an user or store
	 * 
	 * distinguish an user or store by set certain Account/ID in $info
	 */
	public function insertContact($info){
		
		$n = count($info);
		
		$inserted = array(); //record inserted contact value within current process
		
		for($i = 0; $i < $n; $i ++){
			
			$acc = isset($info[$i]['user_account']) ? $info[$i]['user_account'] : $info[$i]['store_account'];
			
			$masxaroMailChkPreg = "(^$acc)";
			
			//only one masxaro mail box is allowed, $acc@masxaro.com
			if(preg_match($this->regex, $info[$i]['value']) 
			   && !preg_match($masxaroMailChkPreg, $info[$i]['value'])){
				return false;
			}
			
			$cur = Tool::infoArray2SQL($info[$i]);
			
			if(!Tool::securityChk($cur)){
				
				for($i = 0; $i < count($inserted); $i ++){
					//rollback
					$this->deleteContact($inserted[$i]);
				}
				return false;
			}
			
			$sql = "
				INSERT 
				INTO 
					`contact`
				SET
					$cur
			";
			
			if($this->db->insert($sql) < 0){
				for($i = 0; $i < count($inserted); $i ++){
					//rollback
					$this->deleteContact($inserted[$i]);
				}
				
				return false;
			}
			else{
				array_push($inserted, $info[$i]['value']);
			}
		}
		
		return true;
	}
	
	
	/**
	 * 
	 * @param string $value the value of the contact that needs to be deleted
	 * 
	 * @param boolean $admin
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * 
	 * delete a certain contact, if $admin = true, then @masxaro mail box can be deleted
	 * 
	 *  if $admin = false, then @masxaro mail box cannot be deleted
	 * 
	 */
	public function deleteContact($value, $admin = false){
		
		if(!$admin && preg_match($this->regex, $value)){
			return false;
		}
		
		$sql = "DELETE
				FROM 
					`contact`
				WHERE
					`value`='$value'		
		";
		
		if($this->db->delete($sql) <= 0){
			return false;
		}
		else{
			return true;
		}
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
	 * delete contacts of a certain account(user/store)
	 */
	public function deleteAccContact($acc){
		$sql = "
			DELETE
			FROM
				`contact`
			WHERE
				`user_account`='$acc'
			OR
				`store_account`='$acc'
		";
		
		if($this->db->delete($sql) <= 0){
			return false;
		}
		return true;
	}
	
	/**
	 * 
	 * 
	 * @param string $curValue
	 * 
	 * @param array() $newInfo
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * update contact
	 */
	public function updateContact($curValue, $newInfo){
		
		if(preg_match($this->regex, $newInfo['value'])){
			return false;
		}
		
		$info = Tool::infoArray2SQL($newInfo);
		
		if(!Tool::securityChk($info)){
			return false;
		}
		
		$sql = "
			UPDATE 
				`contact`
			SET
				$newInfo
			WHERE
				`value`='$curValue'
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
	 * @param string $who store or user
	 * 
	 * @return object
	 * 
	 * @desc
	 * 
	 * get all contacts of a certain account
	 */
	public function getContacts($acc, $who){
		
		$who .= '_account';
		
		$sql = "
			SELECT 
				*
			FROM 
				`contact`
			WHERE
				`$who`='$acc'
		";
		
		$this->db->select($sql);
		
		return $this->db->fetchAssoc();
	}
	
	/**
	 * 
	 * based on contact value get account
	 * @param string $contact
	 * @param string $who user/store
	 */
	public function getContactAccount($contact, $who){
		$who .= '_account';
		
		$sql = "
			SELECT 
				`$who`
			FROM 
				`contact`
			WHERE
				`value`='$contact'
		";
		
		$this->db->select($sql);
		
		if($this->db->numRows() == 0){
			return null;
		}
		
		$result = $this->db->fetchAssoc();
		return $result[0]["$who"];
	}
	
}
?>

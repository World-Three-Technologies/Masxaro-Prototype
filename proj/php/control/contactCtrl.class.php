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
	
	public $regex = "(^.*@masxaro.com)";
	
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * 
	 * 
	 * @param string $acc store or user account
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * 
	 * check whether the certain account is available, true: available, false: not available
	 */
	public function chkAccMail($acc, $value){
		
		if(!preg_match($this->regex, $value)){
			return true;
		}
		
		$sql = "
			SELECT `value`
			FROM `contact`, `user`, `store`
			WHERE
			`value` regexp '$this->regex'
			AND
			`user_account`='$acc'
			OR
			`store_account`='$acc'
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
			INSERT INTO `contact_type`
			SET
			`contact_type`='$type'
		";
		
		if($this->db->insert($sql) < 0){
			return false;
		}
		
		return true;
	}
	
	public function modifyContactType($old, $new){
		
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
			FROM `contact_type`
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
		
		//$regex = "(.*@masxaro.com)";
		
		for($i = 0; $i < $n; $i ++){
			
			if(!$this->chkAccMail($info[$i]['user_account'], $info[$i]['value'])){
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
				INSERT INTO `contact`
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
	
	public function getContact(){
		
	}
	
	
	/**
	 * 
	 * @param string $value the value of the contact that needs to be deleted
	 * 
	 * @return boolean
	 * 
	 */
	public function deleteContact($value){
		
		if(preg_match($this->regex, $value)){
			return false;
		}
		
		$sql = "DELETE
				FROM `contact`
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
		$regex = "(.*@masxaro.com)";
		
		if(preg_match($regex, $newInfo['value'])){
			return false;
		}
		
		$info = Tool::infoArray2SQL($newInfo);
		
		if(!Tool::securityChk($info)){
			return false;
		}
		
		$sql = "
			UPDATE `contact`
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
}
?>

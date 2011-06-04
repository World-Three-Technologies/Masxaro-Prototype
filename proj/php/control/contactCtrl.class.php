<?php
/*
 * ContactCtrl.php -- contact control class 
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
	
	function __construct(){
		parent::__construct();
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
		$sql = "
			INSERT INTO `contact_type`
			SET
			`contact_type`='$type'
		";
		
		if($this->db->insert($sql) <= 0){
			return false;
		}
		
		return true;
	}
	
	public function modifyContactType($old, $new){
		
	}
	
	public function deleteContactType($old, $new){
		
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
			$cur = Tool::infoArray2SQL($info[$i]);
			
			$sql = "
				INSERT INTO `contact`
				SET
				$cur
			";
			
			if($this->db->insert($sql) < 0){
				
				for($i = 0; $i < count($inserted); $i ++){
					
					//rollback
					$sql = "
						DELETE FROM `contact`
						WHERE
						`value`=$inserted[$i]
					";
					
					$this->db->delete($sql);
				}
				//echo $insertedId;
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
		$regex = "^(.*)[@]masxaro[.]com%";
		
		if(preg_match($regex, $value)){
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
		$info = Tool::infoArray2SQL($newInfo);
		
		$sql = "
			UPDATE `contact`
			SET
			$newInfo
			WHERE
			`value`='$curValue'
		";
			
		if($this->db->update($sql) < 0){
			return false;
		}

		return true;
	}
}
?>
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
class ContactCtrl{
	private static $db;
	
	function __construct(){
		$this->db = new Database();
	}
	
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
	 * insert a new contact for an user or store
	 * 
	 * distinguish an user or store by set certain IDs in $info
	 *
	 * @param Array(Array(), ...) $info one line as one contact, in order for group handling
	 * 
	 * @return boolean
	 */
	public function insertContact($info){
		$n = count($info);
		
		$inserted = Array(); //record inserted contact id within current process
		
		for($i = 0; $i < $n; $i ++){
			$cur = Ctrl::infoArray2SQL($info[$i]);
			
			$sql = "
				INSERT INTO `contact`
				SET
				$cur
			";
		
			$insertedId = $this->db->insert($sql);
			
			if($insertedId < 0){
				
				for($i = 0; $i < count($inserted); $i ++){
					
					//rollback
					$sql = "
						DELETE FROM `contact`
						WHERE
						`contact_id`=$inserted[$i]
					";
					
					$this->db->delete($sql);
				}
				echo $insertedId;
				return false;
			}
			else{
				array_push($inserted, $info[$i]['value']);
			}
		}
		
		return true;
	}
	
	public function getContact($con){
		
	}
	
	public function deleteContact($con){
		
	}
}
?>
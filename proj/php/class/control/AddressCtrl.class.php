<?php
/*
 *  addressCtrl.class.php -- address control class 
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

class AddressCtrl extends Ctrl{
	
	/**
	 * 
	 * @param array() $info
	 * 
	 * @return false / inserted id
	 */
	public function insertAddress($info){
		$info = Tool::infoArray2SQL($info);
		
		if(!Tool::securityChk($info)){
			return false;
		}
		
		$sql = "
			INSERT
			INTO
				`address`
			SET
				$info	
		";
		
		$inserted = $this->db->insert($sql);
		
		if($inserted < 0){
			return false;
		}
		return $inserted;
	}
	
	/**
	 * 
	 * 
	 * @param array() $info condition array
	 * 
	 * @return boolean
	 */
	public function deleteAddress($info){
		$info = Tool::infoArray2SQL($info);
		
		$sql = "
			DELETE
			FROM
				`address`
			WHERE
				$info
		";
				
		if($this->db->delete($sql)){
			return true;
		}
		return false;
	}
	
	/**
	 * 
	 * 
	 * @param array() $info
	 */
	public function updateAddress($info){
		
		$acc = "";
		$accType = "_account";
		if(isset($info['user_account'])){
			$acc = $info['user_account'];
			$accType = 'user'.$accType;
		}
		else{
			$acc = $info['store_account'];
			$accType = 'store'.$accType;
		}
		
		$info = Tool::infoArray2SQL($info);
		
		$sql = "
			UPDATE
				`address`
			SET
				$info
			WHERE
				`$accType`='$acc'
		";
		
		if($this->db->update($sql) <= 0){
			return false;
		}
		return true;
	}
	
}
?>
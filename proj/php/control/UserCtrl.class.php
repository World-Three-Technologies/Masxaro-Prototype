<?php
/*
 * UserCtrl.class.php -- user control class 
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
 *  user control class, including all control functions for user
 */

class UserCtrl{
	private static $db;
	
	function __construct(){
		$this->db = new Database();
	}
	
	/**
	 * 
	 * insert a new user
	 * 
	 * @param Array() $info
	 * 
	 * @return boolean
	 */
	public function insert($info){
		$con = Ctrl::infoArray2SQL($info);
		
		$sql = "
			INSERT INTO `user`
			SET
			$con
		";
		
		if($this->db->insert($sql) < 0){
			return false;
		}
			
		return true;
	} 
	
	/**
	 * 
	 * fake delete account
	 * @param string $acc
	 * @return boolean
	 */
	public function fakeDelete($acc){
		$sql = "
			UPDATE `user`
			SET
			`deleted`=true
			WHERE
			`user_account`='$acc';
		";
		
		if($this->db->update($sql) <= 0){
			return false;
		}
		
		return true;
	}
	
	/**
	 * 
	 * real delete account
	 * @param string $acc
	 * @return boolean
	 */
	public function realDelete($acc){
		$sql = "
			DELETE
			FROM `user`
			WHERE
			`user_account`='$acc';
		";
		
		if($this->db->delete($sql) <= 0){
			return false;
		}
		
		return true;
	}
	
	public function getUser($con){
		
	}
	
	public function update($info){
		
	}
}
?>
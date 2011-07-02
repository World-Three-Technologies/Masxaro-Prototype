<?php
/*
 *  userCtrl.class.php -- user control class 
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

class UserCtrl extends Ctrl{
	
	function __construct(){
		parent::__construct();
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
		
		$info['pwd'] = md5($info['pwd']);
		
		$con = Tool::infoArray2SQL($info);
		
		if(!Tool::securityChk($con)){
			return false;
		}
		
		$sql = "
			INSERT INTO 
				`user`
			SET
				$con
		";
			
		$result = $this->db->insert($sql);
		
		if($result < 0){
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
	public function fakeDeleteUser($acc){
		
		if(!Tool::securityChk($acc)){
			return false;
		}
		
		$sql = "
			UPDATE 
				`user`
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
	 * recover a fake deleted user
	 * @param string $acc
	 * @return boolean
	 */
	public function recoverDeletedUser($acc){
		
		if(!Tool::securityChk($acc)){
			return false;
		}
		
		$sql = "
			UPDATE 
				`user`
			SET
				`deleted`=false
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
	public function realDeleteUser($acc){
		
		if(!Tool::securityChk($acc)){
			return false;
		}
		
		$sql = "
			DELETE
			FROM 
				`user`
			WHERE
				`user_account`='$acc';
		";
		
		if($this->db->delete($sql) <= 0){
			return false;
		}
		
		return true;
	}
	
	
	/**
	 * 
	 * @param string $curAcc current account name
	 * @param array() $info updated info, array('first_name'=>'John', 'age_range_id'=>2, ...)
	 */
	public function updateUserInfo($curAcc, $info){
		
		$info = Tool::infoArray2SQL($info);
		
		if(!Tool::securityChk($info)){
			return false;
		}
		
		$sql = "
			UPDATE 
				`user`
			SET
				$info
			WHERE
				`user_account` = '$curAcc'
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
	 * @param string $pwd
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * 
	 * find user 
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
				`user`
			WHERE
				`user_account`='$acc'
			AND
				`pwd`='$pwd'
			AND 
				`deleted`=false
		";
		
		$this->db->select($sql);
		if($this->db->numRows() == 1){
			return true;
		}
		
		return false;
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
	 * according to user account, return user profile object
	 */
	public function getUserProfile($acc){
		$sql = "
			SELECT 
				*
			FROM 
				`user`
			WHERE 
				`user_account`='$acc'
		";
		
		$this->db->select($sql);
		$result = $this->db->fetchAssoc();
		
		return $result[0];
	}
}
?>
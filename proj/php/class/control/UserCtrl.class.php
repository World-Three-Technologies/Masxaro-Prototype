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

class UserCtrl extends ClientCtrl{
	
	function __construct(){
		parent::__construct('user');
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

}
?>

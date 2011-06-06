<?php
/*
 *  storeCtrl.class.php -- store control class 
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
 */
class StoreCtrl extends Ctrl{
	
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * 
	 * 
	 * @param array() $info
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * insert a new store
	 */
	public function insertStore($info){
		
		$info['pwd'] = md5($info['pwd']);
		
		$info = Tool::infoArray2SQL($info);
		
		if(!Tool::securityChk($info)){
			return false;
		}
		
		$sql = "
			INSERT
			INTO `store`
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
	 * @return boolean
	 * 
	 * @desc
	 * 
	 * store login
	 * 
	 */
	public function storeLogin($acc, $pwd){
		if(!Tool::securityChk($acc)){
			return false;
		}
		
		$pwd = md5($pwd);
		
		$sql = "
			SELECT *
			FROM `store`
			WHERE
			`store_name`='$acc'
			AND
			`pwd`='$pwd'
		";
		
		$this->db->select($sql);
		if($this->db->numRows() == 1){
			return true;
		}
		
		return false;
	}
	
	/**
	 * @param int $storeId
	 * 
	 * @param array() $info
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * 
	 * update store info
	 * 
	 */
	public function updateStore($storeId, $info){
		$info = Tool::infoArray2SQL($info);
		
		if(!Tool::securityChk($info)){
			return false;
		}
		
		$sql = "
			UPDATE `store`
			SET
			$info
			WHERE
			`store_id`=$storeId
		";
			
		if($this->db->update($sql) <= 0){
			return false;
		}
	}
	
	/**
	 * 
	 * 
	 * @param string $storeId
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * 
	 * delete a store
	 */
	public function deleteStore($storeId){
		$info = Tool::infoArray2SQL($info);
		
		if(!Tool::securityChk($info)){
			return false;
		}
		
		$sql = "
			DELETE
			FROM `store`
			WHERE
			`store_id`=$storeId
		";
		
		if($this->db->delete($sql) <= 0){
			return false;
		}
		
		return true;
	}
	
}

?>
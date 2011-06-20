<?php

/*
 *  ctrl.class.php -- super class of all control classes 
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

abstract class Ctrl {
	protected $db;
	
	function __construct(){
		$this->db = new Database();
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
}

?>
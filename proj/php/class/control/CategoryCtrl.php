<?php
/*
 *  CategoryCtrl.class.php -- category control class 
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
 */

class CategoryCtrl extends Ctrl{
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 
	 * 
	 * @param string $cat
	 * 
	 * @return boolean
	 */
	public function insertCategory($info){
		
		$info = Tool::infoArray2SQL($info);
		
		$sql = "
			INSERT
			INTO
				`category`
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
	 * @param string $cat
	 * 
	 * @return boolean
	 */
	public function deleteCategory($cat){
		$sql = "
			DELETE
			FROM
				`category`
			WHERE
				`category` = '$cat'
		";
		
		if($this->db->delete($sql) <= 0){
			return false;
		}
		return true;
	}
	
	/**
	 * 
	 * @param array $con
	 * 
	 * @return assoc-array result set
	 * 
	 * @desc
	 * get category of certain target: receipt, all, item
	 */
	public function getTargetCategory($con){
		$con = Tool::condArray2SQL($con);
		$sql = "
			SELECT
				*
			FROM
				`category`
			WHERE
				$con
		";
		$this->db->select($sql);
		return $this->db->fetchAssoc();
	}
}

?>
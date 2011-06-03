<?php
/*
 * ReceiptCtrl.class.php -- receipt control class 
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
 *  receipt control class, including all control functions for receipt
 */

class ReceiptCtrl{
	private static $db;
	
	function __construct(){
		$this->db = new Database();
	}
	
	/**
	 * 
	 * generate an unique receipt code based on its basic information
	 * 
	 * @param array() $basicInfo
	 */
	private function codeGen($basicInfo){
		$code = "983094867189238-0929347";
		return $code;
	}
	
	/**
	 * @example
	 * insert($basicInfo, $items);
	 * 
	 * insert($basicInfo, null);
	 * 
	 * insert(null, $items);
	 * 
	 * @param array() $basicInfo
	 * 
	 * @param array(array(), array()...) $items
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * 
	 * 1. insert a new receipt with multiple items
	 * 
	 * (Unnecessary to set up items' receipt id).
	 * 
	 * 2. insert new items for an existed receipt
	 * 
	 * (Need to set up the receipt_id for each item, $basicInfo is null).
	 * 
	 * 3. insert a new receipt without any items
	 * 
	 * ($items is null)
	 */
	public function insert($basicInfo = "", $items = ""){
		
		$basicInfoNull = is_null($basicInfo) || strlen($basicInfo) == 0;
		$itemsNull = is_null($items) || strlen($items) == 0;
		
		$totalCost = 0;
		
		if($basicInfoNull && $itemsNull){
			return false;
		}
		
		if(!$basicInfoNull){
			
			$code = $basicInfo['receipt_id'];
			
			if($basicInfo['receipt_time'] == null || strlen($basicInfo['receipt_time']) == 0){
				$basicInfo['receipt_time'] = date("Y-m-d H:i:s");
			}
			
			if($code == null || strlen($code) == 0){
				$code = $this->codeGen($basicInfo);
				$basicInfo['receipt_id'] = $code;
			}
			
			$info = "";
			
			$info = Ctrl::infoArray2SQL($basicInfo);
			
			$sql = "
				INSERT INTO `receipt`
				SET
				$info
			";
			
			if($this->db->insert($sql) < 0){
				//rollback
				$this->realDelete($code);
				return false;
			}
			
		}
				
		if(!$itemsNull){
			
			if($code == null || strlen($code) == 0){
				$code = $items[0]['receipt_id'];
			}
		
			$sql = "
				SELECT `total_cost`
				FROM `receipt`
				WHERE
				`receipt_id`='$code'
			";
			
			$this->db->select($sql);
			
			if($this->db->numRows() > 0){
				$result = $this->db->fetchObject();
				$totalCost = $result[0]->total_cost;
			}
			else{
				$this->realDelete($code);
				return false;
			}
			
			$n = count($items);
			
			for($i = 0; $i < $n; $i ++){
				$curCost =  $items[$i]['item_price'] * $items[$i]['item_qty'] * $items[$i]['item_discount'];
				$totalCost += $curCost;
				
				if($code != null && strlen($code) == 0){
					$items[$i]['receipt_id'] = $code;	
				}
				
				$info = "";
				$info = Ctrl::infoArray2SQL($items[$i]);
				$sql = "
					INSERT INTO `receipt_item`
					SET
					$info	
				";
				if($this->db->insert($sql) < 0){
					$this->realDelete($code);
					return false;
				}
			}
			
			$totalCost += $totalCost * $basicInfo['tax'];
			
			$sql = "
				UPDATE `receipt`
				SET
				`total_cost`=$totalCost
				WHERE
				`receipt_id`='$code'
			";
			
			if($this->db->update($sql) <= 0){
				$this->realDelete($code);
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * 
	 * delete completely a receipt and its items
	 * 
	 * @param string $id
	 * 
	 * @return boolean
	 */
	public function realDelete($id){
		$sql = "
			DELETE from `receipt_item`
			WHERE
			`receipt_id` = '$id'
		";
		$delItem = $this->db->delete($sql);
		
		$sql = "
			DELETE from `receipt`
			WHERE
			`receipt_id` = '$id'
		";
		if($this->db->delete($sql) <= 0 && $delItem){
			return false;
		}
		
		return true;
	}
	
	/**
	 * 
	 * fake delete a receipt
	 * 
	 * @param string $id
	 * 
	 * @return boolean
	 */
	public function fakeDelete($id){
		$sql = "
			UPDATE `receipt_item`
			SET
			`deleted` = true
			WHERE
			`receipt_id` = '$id'
		";
		
		if($this->db->update($sql) <= 0){
			return false;
		}
		
		$sql = "
			UPDATE `receipt`
			SET
			`deleted` = true
			WHERE
			`receipt_id` = '$id'
		";
		
		if($this->db->update($sql) <= 0){
			return false;
		}
		
		return true;
	}
	
	/**
	 * 
	 * recover a fake-deleted receipt
	 * 
	 * @param string $id
	 * 
	 * @return boolean
	 */
	public function recoverDeleted($id){
		$sql = "
			UPDATE `receipt_item`
			SET
			`deleted` = false
			WHERE
			`receipt_id` = '$id'
		";
		if($this->db->update($sql) <= 0){
			return false;
		}
		
		$sql = "
			UPDATE `receipt`
			SET
			`deleted` = false
			WHERE
			`receipt_id` = '$id'
		";
		if($this->db->update($sql) <= 0){
			return false;
		}
		
		return true;
	}
	
}
?>
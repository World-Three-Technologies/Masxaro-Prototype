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

class ReceiptCtrl extends Ctrl{
	
	function __construct(){
		parent::__construct();
	}
	
	
	/**
	 * 
	 * @param array() $receipts 
	 * 	associate array, 
	 *  the result set selected by joining `receipt` & `receipt_item` with certain conditions
	 * 
	 * @return array() $result
	 *  object array, each receipt is an object including basic info and items.
	 *  
	 * @desc
	 *  accept the result set (assoc array) from joining `receipt`&`receipt_item`,
	 *  encapsulate basic info & items of a certain receipt into an object,
	 *  organize all receipt objects into an indexed array, return.
	 */
	private function buildReceiptObj($receipts){
		$result = array();
		
		if(count($receipts) > 0){
			$curRecId = 0;
			$curRec = null;
			$curItems = null;
			$itemRegex = "(^item)";
			
			foreach($receipts as $cur){
				
				$curItems = array();
				$newRecFlag = false;
				
				if($cur['id'] != $curRecId){
					$curRec = new ReceiptEntity();
					$newRecFlag = true;
				} 
				
				foreach($cur as $key=>$value){
					if(!preg_match($itemRegex, $key)){
						$curRec->$key = $value;
					}
					else if(!empty($value)){
						$curItems["$key"] = $value;
					}
				}
				
				if(!empty($curItems) && isset($curRec->items)){
					array_push($curRec->items, $curItems);
				}
				
				if($newRecFlag){
					if(!empty($curRec)){
						array_push($result, $curRec);
					}
					$curRecId = $cur['id'];
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * 
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
	 * (Need to set up the id for each item, $basicInfo is null).
	 * 
	 * 3. insert a new receipt without any items
	 * 
	 * ($items is null)
	 */
	public function insertReceipt($basicInfo, $items){
		
		$receiptId = 0;
		
		$basicInfoNull = is_null($basicInfo);
		
		$itemsNull = is_null($items);
		
		$totalCost = 0;
		
		if($basicInfoNull && $itemsNull){
			return false;
		}
		
		if(!$basicInfoNull){
			
			$info = "";
			
			$info = Tool::infoArray2SQL($basicInfo);
			
			if(!Tool::securityChk($info)){
				return false;
			}
			
			$sql = "
				INSERT INTO 
					`receipt` 
				SET 
					$info
			";

			$receiptId = $this->db->insert($sql);
				
			if($receiptId < 0){
				return false;
			}
			
		}
				
		if(!$itemsNull){
			
			if($receiptId == null || strlen($receiptId) == 0 || $receiptId == 0){
				$receiptId = $items['id']; // if current receipt id is not set, items[0] should be the receipt id.
			}
		
			$sql = "
				SELECT 
					`total_cost`
				FROM 	
					`receipt`
				WHERE
					`id`=$receiptId
				AND 
					`deleted`=false
			";
			$this->db->select($sql);
			
			if($this->db->numRows() > 0){
				$result = $this->db->fetchObject();
				$totalCost = $result[0]->total_cost;
			}
			else{
				//$this->realDelete($receiptId);
				return false;
			}
			
			$n = count($items);
			
			for($i = 0; $i < $n; $i ++){
				
				if(empty($items[$i])){
					continue;
				}
				
				if(empty($items[$i]['item_discount'])){
					$items[$i]['item_discount'] = 0;
				}
				
				$curCost =  
					$items[$i]['item_price'] * 
					$items[$i]['item_qty'] * 
					((100 - $items[$i]['item_discount']) / 100);
				
				$totalCost += $curCost;
				
				$items[$i]['receipt_id'] = $receiptId;	
				
				$info = Tool::infoArray2SQL($items[$i]);
				
				if(!Tool::securityChk($info)){
					return false;
				}
				
				$sql = "
					INSERT 
					INTO 
						`receipt_item`
					SET
						$info	
				";
					
				if($this->db->insert($sql) < 0){
					//$this->realDelete($receiptId);
					return false;
				}
			}
			$totalCost += $totalCost * $basicInfo['tax'] / 100;

			$sql = "
				UPDATE 
					`receipt`
				SET
					`total_cost`=$totalCost
				WHERE
					`id`='$receiptId'
				AND 
					`deleted`=false
			";
			
			if($this->db->update($sql) <= 0){
				//$this->realDelete($receiptId);
				return false;
			}
		}
		
		return $receiptId;
	}
	
	
	/**
	 * @param string $receiptId
	 * 
	 * @param array() $param
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * update basic receipt info
	 * 
	 */
	public function updateReceiptBasic($receiptId, $param){
		
		$info = Tool::infoArray2SQL($param);
		
		if(!Tool::securityChk($info)){
			return false;
		}
		
		$sql = "
			UPDATE `
				receipt`
			SET
				$info
			WHERE
				`id`=$receiptId
			AND 
				`deleted`=false
		";
		
		if($this->db->update($sql) <= 0){
			return false;
		}
			
		return true;
	}
	
	
	/**
	 * 
	 * @param string $receiptId receipt id
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * 
	 * delete completely a receipt and its items
	 */
	public function realDelete($receiptId){
		$sql = "
			DELETE 
			FROM 
				`receipt_item`
			WHERE
				`receipt_id` = $receiptId
		";
		$delItem = $this->db->delete($sql);
		
		$sql = "
			DELETE 
			FROM 
				`receipt`
			WHERE
				`id` = $receiptId
		";
		
		if($this->db->delete($sql) <= 0 || $delItem <= 0){
			return false;
		}
		
		return true;
	}
	
	/**
	 * 
	 * 
	 * @param string $receiptId receipt receiptId
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * fake delete a receipt
	 * 
	 */
	public function fakeDelete($receiptId){
		$sql = "
			UPDATE 
				`receipt_item`
			SET
				`deleted` = true
			WHERE
				`receipt_id` = $receiptId
		";
		
		if($this->db->update($sql) <= 0){
			return false;
		}
		
		$sql = "
			UPDATE 
				`receipt`
			SET
				`deleted` = true
			WHERE
				`id` = $receiptId
			AND 
				`deleted`=false
		";
		
		if($this->db->update($sql) <= 0){
			return false;
		}
		
		return true;
	}
	
	/**
	 * 
	 * 
	 * @param string $receiptId
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * recover a fake-deleted receipt
	 */
	public function recoverDeleted($receiptId){
		$sql = "
			UPDATE 
				`receipt_item`
			SET
				`deleted` = false
			WHERE
				`receipt_id` = '$receiptId'
		";
		if($this->db->update($sql) <= 0){
			return false;
		}
		
		$sql = "
			UPDATE 
				`receipt`
			SET
				`deleted` = false
			WHERE
				`id` = '$receiptId'
			AND 
				`deleted`=true
		";
		if($this->db->update($sql) <= 0){
			return false;
		}
		
		return true;
	}
	
	/**
	 * 
	 * 
	 * @param string $userAcc
	 * 
	 * @return array(object);
	 * 
	 * @desc
	 * get all receipt based on a certain user account
	 */
	public function userGetAllReceiptBasic($userAcc){

		$sql = "
			SELECT 
				r.`id`,r.`user_account`,r.`receipt_time`, r.`tax`, r.`total_cost`, s.`store_name`
			FROM 
				`receipt` as r
			JOIN 
				`store` as s
			ON
				r.`store_account`=s.`store_account`
			AND
				r.`user_account`='$userAcc'
			AND 
				`deleted`=false
			ORDER BY
				r.`receipt_time`
			DESC;
		";
		
		$this->db->select($sql);
		
		if($this->db->numRows() == 0){
			return "";
		} else{
			return $this->db->fetchObject();
		}
	}
	
	/**
	 * 
	 * 
	 * @param string $receiptId
	 * 
	 * @return array(obj, ...) items
	 * 
	 * return all items of a receipt
	 */
	public function userGetReceiptItems($receiptId){
		
		$sql = "
			SELECT
				*
			FROM 
				`receipt_item`
			WHERE
				`id`=$receiptId
			AND
				`deleted`=false
		";
		
		$this->db->select($sql);
		
		if($this->db->numRows() == 0){
			return "";
		}
		
		else{
			$result = $this->db->fetchObject();
			return $result;
		}
		
	}
	
	
	/**
	 * 
	 * @param string $userAcc
	 * 
	 * @return array(obj,...) each ReceiptEntity obj conclude 2 arrays, basicInfo & items (array(array(),..))
	 * 
	 * @desc
	 * 
	 * return detail information of a certain receipt
	 */
	public function userGetAllReceipt($userAcc){
		$sql = "
			SELECT 
				r.`id`,r.`receipt_time`, r.`tax`, r.`total_cost`, s.`store_name`,ri.`item_id`,
        		ri.`item_name`, ri.`item_qty`, ri.`item_discount`, ri.`item_price`
			FROM 
				`receipt` as r 
			LEFT JOIN
				`receipt_item` as ri
			ON
				r.`id`=ri.`receipt_id`
			LEFT JOIN
				`store` as s
			ON
				r.`store_account`=s.`store_account`
			WHERE
				r.`user_account`='$userAcc'
			AND 
				r.`deleted`=false
			AND
				ri.`deleted`=false
			ORDER BY
				r.`receipt_time`
			DESC
		";
		
		$this->db->select($sql);
		$receipts = $this->db->fetchAssoc();
		return $this->buildReceiptObj($receipts);
	}
	
	/**
	 * @todo
	 * consider about the sql here, it's very inefficient and slow since lots of functions
	 * are used in the sql, maybe change the precision of database instead of handle it while selecting.
	 * 
	 * @param array() $con multi-dimension array of searching conditions
	 * 
	 * @return array(obj,...) each ReceiptEntity obj conclude 2 arrays, basicInfo & items (array(array(),..))
	 * 
	 * @desc
	 * search for receipts of a certain account(option) based on certain conditions
	 */
	public function searchReceipt($con, $acc = null){
		$con = Tool::condArray2SQL($con);
		
		$acc = isset($acc) ? "(^$acc$)" : "(.*)";
		
		if(!Tool::securityChk($con)){
			return false;
		}
		
		$sql = "
			SELECT 
				r.`id`,
				DATE_FORMAT(r.`receipt_time`, '%m-%d-%Y %h:%i %p') as receipt_time, 
				r.`tax`,
				r.`total_cost`,
				r.`receipt_category`, 
				s.`store_name`,
				ri.`item_id`,
        		ri.`item_name`, 
        		ri.`item_qty`, 
        		ri.`item_discount`,
				ri.`item_price`,
        		ri.`item_category`
			FROM 
				`receipt` 
			AS 
				r 
			LEFT JOIN
				`receipt_item` 
			AS 
				ri
			ON
				r.`id`=ri.`receipt_id`
			LEFT JOIN
				`store` as s
			ON
				r.`store_account`=s.`store_account`
			WHERE
				$con
			AND
				r.`user_account` regexp '$acc'
			AND 
				r.`deleted`=false
			AND
				ri.`deleted`=false
			ORDER BY
				r.`receipt_time`
			DESC
		";
				
		$this->db->select($sql);
		$receipts = $this->db->fetchAssoc();
		return $this->buildReceiptObj($receipts);
	}
	
	/**
	 * 
	 * @param string receiptId
	 * 
	 * @return object receipt or img blob
	 * 
	 * @desc
	 * 
	 * return detail information of a certain receipt
	 */
	public function getReceiptDetail($receiptId){
		$sql = "
			SELECT 
				r.`id`,r.`user_account`,r.`receipt_time`, r.`tax`, r.`total_cost`, s.`store_name`
			FROM 
				`receipt` as r 
			JOIN
				`store` as s
			ON
				r.`store_account`=s.`store_account`
			AND
				`id`=$receiptId
			AND 
				`deleted`=false
		";
		
		$this->db->select($sql);
		$result = $this->db->fetchObject();
		
		return $result;
	}
}
?>

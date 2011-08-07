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
		$this->builder = new ReceiptBuilder();
	}
	
	/**
	 * @param array() $receiptIds result set of fetchAssoc()
	 * 
	 * @return array(int) $receiptIds 1-d array of receipt ids
	 * 
	 * @desc
	 * convert fetchAssoc result set of receipt ids into 1-d int array
	 * 
	 */
	protected function nomarlizeReceiptIdArray($receiptIds) {
		$ids = array();
		foreach($receiptIds as $id) {
			array_push($ids, $id['id']);
		}
		return $ids;
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
	 * @param array() $basicInfo[optional]
	 * 
	 * @param array(array(), array()...) $items[optional]
	 * 
	 * @return boolean
	 * 
	 * @example
	 * $items = array(
	 * 		array(
	 * 			item_id=>10,
	 * 			item_name=>'fries-mid',
	 * 			item_qty=>2,
	 * 			item_price=>1.99
	 * 		),
	 * 		array(
	 * 			item_id=>2,
	 * 			item_name=>'Salad',
	 * 			item_qty=>1,
	 * 			item_price=>1
	 * 			)
	 * );
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
			
			$sqlColumns = "";
			$sqlValues = "";
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
					((100 - $items[$i]['item_discount']) * 0.01);
				
				$totalCost += $curCost;
				$items[$i]['receipt_id'] = $receiptId;	
				
				//$info = Tool::infoArray2SQL($items[$i]);
				$info = Tool::infoArray2ValueSQL($items[$i]);
				
				if(!Tool::securityChk($info['values'])){
					return false;
				}
				
				$sqlColumns = "({$info['columns']})";

				$sqlValues .= "({$info['values']}),";
			}
			
			$sqlValues = substr($sqlValues, 0, -1);
			$sql = "
				INSERT INTO
					`receipt_item`
					$sqlColumns
				VALUES
					$sqlValues
			";
					
			if($this->db->insert($sql) < 0){
				return false;
			}
			
			$totalCost += $totalCost * $basicInfo['tax'] * 0.01;

			$sql = "
				UPDATE 
					`receipt`
				SET
					`total_cost`=$totalCost
				WHERE
					`id`=$receiptId
				AND 
					`deleted`=false
			";
			
			if($this->db->update($sql) <= 0){
				$this->realDelete($receiptId);
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
			UPDATE 
				`receipt`
			SET
				$info
			WHERE
				`id`=$receiptId
			AND 
				`deleted`=false;
		";

		if($this->db->update($sql) <= 0){
      echo $sql;
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
	 * @param array(int) receiptIds
	 * 
	 * @return object receipt 
	 * 
	 * @desc
	 * 
	 * return detail information of certain receipts, without items
	 */
	public function getReceiptDetail($receiptIds){
		return $this->builder->build_modile($receiptIds);
	}
	
	/**
	 * @see sample/conArraySample.class.php
	 * 
	 * @param condition-array $con multi-dimension array of searching conditions
	 * 
	 * @param string $acc user account
	 * 
	 * @param int $limitStart limit start offset(optional)
	 * 
	 * @param int $limitOffset limit end offset(optional)
	 * 
	 * @param string $groupBy (optional) set group by field
	 * 
	 * @param string $orderBy (optional) set order by field, default as 'receipt_time'
	 * 
	 * @param boolean $orderDesc (optional)
	 * 
	 * @param boolean $mobile (optional) whether request is coming from mobile end, default as false
	 * 
	 * @return array(obj,...) each ReceiptEntity obj conclude 2 arrays, basicInfo & items (array(array(),..))
	 * 
	 * @desc
	 * search for receipts of a certain account(option) based on certain conditions
	 */
	public function searchReceipt($con, $acc, $limitStart = 0, $limitOffset = 999999,
									$groupBy=null, $orderBy='receipt_time', $orderDesc=true, $mobile=false){

		$limitStart = isset($limitStart) ? $limitStart : 0;
		$limitOffset = isset($limitOffset) ? $limitOffset : 999999;
		$orderBy = isset($orderBy) ? $orderBy : 'receipt_time';
		$orderDesc = isset($orderDesc) ? $orderDesc : true;
		$mobile = isset($mobile) ? $mobile : false;
		$groupBy = isset($groupBy) ? $groupBy : '`receipt`.`id`';
		
		$con = Tool::condArray2SQL($con);
		
		if(!Tool::securityChk($con)){
			return false;
		}
		
		$sql = <<<SEL
					SELECT
	      				`receipt`.`id`
	      			FROM
	      				`receipt`
	      			LEFT OUTER JOIN
	      				`receipt_item`
	      			ON
	      				`receipt_item`.`receipt_id`=`id`,
	      				`store`,
	      				`receipt_tag`
	      			WHERE
	      				$con
	      			AND
	      				`receipt`.`store_account`=`store`.`store_account`
	      			AND
	      				`receipt`.`deleted` = false
	      			AND
	      				(
		                    `receipt_item`.`deleted` = false
		                  OR
		                    `receipt_item`.`deleted` IS NULL
                		)
                	ORDER BY
                		$orderBy
                	DESC
	      			GROUP BY
	      				$groupBy
	      			LIMIT
	      				$limitStart, $limitOffset
SEL;
		$this->db->select($sql);
		$results = $this->db->fetchAssoc();
		
		if(!$mobile) {
			return $this->builder->build($this->nomarlizeReceiptIdArray($results));
		}
		else{
			return $this->builder->build_modile($this->nomarlizeReceiptIdArray($results));
		}
	}
	

  /**
   * @param array(int) receipt ids
   *
   * @return array $tags
   *
   * @desc return tags array for each receipt, indexed with receipt id
   * 
   * @author Jimmy Chao
   */
	public function getReceiptsTags($ids){
	    $this->builder->getReceiptsTags($ids);
	}	
	
	/**
	 * 
	 * 
	 * @param array(int) $receiptIds
	 * 
	 * @return 2-d-array() items
	 * 
	 * return all items of a list of receipts
	 */
	public function userGetReceiptItems($receiptIds){
		return $this->builder->getReceiptsItems($receiptIds);
	}
}
?>

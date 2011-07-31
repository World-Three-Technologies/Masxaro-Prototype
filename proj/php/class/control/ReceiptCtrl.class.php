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
	 * 
	 * 
	 * @param string $acc user/store account
	 * 
	 * @param string $subquery subquery statement, should return a set of receipt id
	 * 
	 * @param string $groupBy (optional) set group by field
	 * 
	 * @param string $orderBy (optional) set order by field, default as 'receipt_time'
	 * 
	 * @param boolean $orderDesc
	 * 
	 * @param int $limitStart (optional) limit start offset(optional)
	 * 
	 * @param int $limitOffset (optional) limit offset(optional)
	 * 
	 * @desc
	 * build the basic sql statement for receipt searching, return receipt full entities with all information
	 */
	protected function buildSearchSql($acc, $subquery, $limitStart=0, $limitOffset=999999, 
										   $groupBy=null, $orderBy='receipt_time', $orderDesc=true){
										   	
		$limitStart = isset($limitStart) ? $limitStart : 0;
		$limitOffset = isset($limitOffset) ? $limitOffset : 999999;
		$orderBy = isset($orderBy) ? $orderBy : 'receipt_time';
		$orderDesc = isset($orderDesc) ? $orderDesc : true;
										   	
		$sql = "
			SELECT 
				r.`id`,
				DATE_FORMAT(r.`receipt_time`, '%m-%d-%Y %h:%i %p') as receipt_time, 
				r.`tax`,
				r.`total_cost`,
				r.`source`,
				s.`store_name`,
				ri.`item_id`,
		        ri.`item_name`, 
		        ri.`item_qty`, 
		        ri.`item_discount`,
		        ri.`item_price`
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
				r.`user_account`='$acc'
			AND 
				r.`deleted`=false
      		AND
				ri.`deleted`=false
			AND
				r.`id`
      		IN
      			(
	      			$subquery
      			)";
	      			
	      if(isset($groupBy)){
	      	$sql .= "
	      		GROUP BY
	      			$groupBy
	      	";
	      }
	      
	      $sql .= "
	      		ORDER BY
	      			$orderBy
	      ";
	      
	      if($orderDesc){
	      	$sql .= "DESC";
	      }
	      			
	      $sql .= "
	      		LIMIT
	      			$limitStart, $limitOffset
	      ";
	      			
	      return $sql;
	}
	
	/**
	 * 
	 * 
	 * 
	 * @param string $acc user/store account
	 * 
	 * @param string $subquery subquery statement, should return a set of receipt id
	 * 
	 * @param string $groupBy (optional) set group by field
	 * 
	 * @param string $orderBy (optional) set order by field, default as 'receipt_time'
	 * 
	 * @param boolean $orderDesc
	 * 
	 * @param int $limitStart (optional) limit start offset(optional)
	 * 
	 * @param int $limitOffset (optional) limit offset(optional)
	 * 
	 * @desc
	 * build the basic sql statement for receipt searching, return receipt full entities with all information
	 */
	protected function buildSearchSql_mobile($acc, $subquery, $limitStart=0, $limitOffset=999999, 
										   $groupBy=null, $orderBy='receipt_time', $orderDesc=true){
		$limitStart = isset($limitStart) ? $limitStart : 0;
		$limitOffset = isset($limitOffset) ? $limitOffset : 999999;
		$orderBy = isset($orderBy) ? $orderBy : 'receipt_time';
		$orderDesc = isset($orderDesc) ? $orderDesc : true;
										   	
		$sql = "
			SELECT 
				r.`id`,
				DATE_FORMAT(r.`receipt_time`, '%m-%d-%Y %h:%i %p') as receipt_time, 
				r.`tax`,
				r.`total_cost`,
				r.`source`,
				s.`store_name`
			FROM 
				`receipt` 
			AS 
				r
			LEFT JOIN
				`store` as s
			ON
				r.`store_account`=s.`store_account`
			WHERE
				r.`user_account`='$acc'
			AND 
				r.`deleted`=false
			AND
				r.`id`
      		IN
      			(
	      			$subquery
      			)";
	      			
	      if(isset($groupBy)){
	      	$sql .= "
	      		GROUP BY
	      			$groupBy
	      	";
	      }
	      
	      $sql .= "
	      		ORDER BY
	      			$orderBy
	      ";
	      
	      if($orderDesc){
	      	$sql .= "DESC";
	      }
	      			
	      $sql .= "
	      		LIMIT
	      			$limitStart, $limitOffset
	      ";
	      			
	      return $sql;
	}
	
	/**
	 * @see ReceiptEntity
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
	protected function buildReceiptObj($receipts){
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
	 * @see ReceiptCtrl::buildReceiptObj
	 * @see ReceiptEntity
	 * 
	 * @param array $receiptObjs
	 * receipt object array, the result of ReceiptCtrl::buildReceiptObj($receipts)
	 * 
	 * @return array $result
	 * receipt object array with tags fetched
	 * 
	 */
	protected function fetchReceiptTags($receipts){
	    foreach($receipts as $receipt){
	      $ids[] = $receipt->id;
	    }
	    $tags = $this->getReceiptsTags($ids);
	    foreach($receipts as $receipt){
	      $receipt->tags = $tags[$receipt->id];
	    }
	    return $receipts;
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
	 * 
	 * @param string $userAcc
	 * 
	 * @param int $limitStart limit start offset(optional)
	 * 
	 * @param int $limitOffset limit offset(optional)
	 * 
	 * @param string $groupBy (optional) set group by field
	 * 
	 * @param string $orderBy (optional) set order by field, default as 'receipt_time'
	 * 
	 * @return array(object);
	 * 
	 * @desc
	 * get all receipt based on a certain user account
	 */
	public function userGetAllReceiptBasic($userAcc, $limitStart, $limitOffset = 999999,
											$groupBy=null, $orderBy='receipt_time', $orderDesc=true){
												
		$limitStart = isset($limitStart) ? $limitStart : 0;
		$limitOffset = isset($limitOffset) ? $limitOffset : 999999;
		$orderBy = isset($orderBy) ? $orderBy : 'receipt_time';
		$orderDesc = isset($orderDesc) ? $orderDesc : true;

		$sql = "
			SELECT 
				r.`id`,
				DATE_FORMAT(r.`receipt_time`, '%m-%d-%Y %h:%i %p') as receipt_time, 
				r.`tax`,
				r.`total_cost`,
				r.`source`,
				s.`store_name`
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
			";
		
		if(isset($groupBy)){
			$sql .= "
				ORDER BY
					$groupBy
			";
		}
		
		$sql .= "
				ORDER BY
					r.`receipt_time` DESC
				LIMIT
				    $limitStart, $limitOffset
		";
		
		$this->db->select($sql);
		
		if($this->db->numRows() == 0){
			return "";
		} else{
			return $this->db->fetchAssoc();
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
				`receipt_id`=$receiptId
			AND
				`deleted`=false
		";
		
		$this->db->select($sql);
		
		if($this->db->numRows() == 0){
			return "";
		}
		
		else{
			$result = $this->db->fetchAssoc();
			return $result;
		}
		
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
				r.`id`,
				r.`user_account`,
				DATE_FORMAT(r.`receipt_time`, '%m-%d-%Y %h:%i %p') as receipt_time,
				r.`tax`, 
				r.`total_cost`,
				r.`source`, 
				s.`store_name`
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
		$result = $this->db->fetchAssoc();
		
		return $result;
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

		if(!isset($mobile)){
			$mobile = false;
		}
		$con = Tool::condArray2SQL($con);
		
		if(!Tool::securityChk($con)){
			return false;
		}
		
		$subquery = "
					SELECT DISTINCT
	      				`receipt`.`id`
	      			FROM
	      				`receipt`,
	      				`receipt_item`,
	      				`store`,
	      				`receipt_tag`
	      			WHERE
	      				$con
	      			AND
	      				`receipt_item`.`receipt_id`=`id`
	      			AND
	      				`receipt`.`store_account`=`store`.`store_account`
		";

	    $sql = $mobile ? 
	    			$this->buildSearchSql_mobile($acc, $subquery, $limitStart, $limitOffset, 
	    							$groupBy, $orderBy, $orderDesc)
	    			:
	    			$this->buildSearchSql($acc, $subquery, $limitStart, $limitOffset, 
	    							$groupBy, $orderBy, $orderDesc);
	    						
	    							
	    							
		$this->db->select($sql);
		$receipts = $this->db->fetchAssoc();
		return $this->fetchReceiptTags($this->buildReceiptObj($receipts));
	}
	
	/**
	 * 
	 * @see sample/conArraySample.class.php
	 * @see php/receiptOperation.php: tag_search
	 * 
	 * @param condition-array $con 
	 * here the $con array should be an 'OR' statement only includes tags
	 * 
	 * @param string $acc
	 * 
	 * @param int $limitStart limit start offset(optional)
	 * 
	 * @param int $limitOffset limit end offset(optional)
	 * 
	 * @param string $groupBy (optional) set group by field
	 * 
	 * @param string $orderBy (optional) set order by field, default as 'receipt_time'
	 * 
	 * @param boolean $orderDesc
	 * 
	 * @desc
	 * search receipts with certain tags
	 */
	public function searchTagReceipt($con, $acc, $limitStart = 0, $limitOffset = 999999,
									$groupBy=null, $orderBy='receipt_time', $orderDesc=true){
		$con = Tool::condArray2SQL($con);
		
		$subquery = "
			SELECT
				`receipt_id`
			FROM
				`receipt_tag`
			WHERE
				$con
			AND
				`user_account`='$acc'
			ORDER BY
				`receipt_id`
		";
		
		$sql = $this->buildSearchSql($acc, $subquery, $limitStart, $limitOffset, 
	    							$groupBy, $orderBy, $orderDesc);
				
		$this->db->select($sql);
		$receipts = $this->db->fetchAssoc();
		return $this->fetchReceiptTags($this->buildReceiptObj($receipts));
	}
	
	/**
	 * 
	 * 
	 * @param int $id receipt id
	 * 
	 * @return array $result
	 * 
	 * @desc
	 * return an array of tags of a certain receipt
	 */
  public function getReceiptTags($id){
		$sql = "
			SELECT
				`tag`
			FROM
				`receipt_tag`
			WHERE
				`receipt_id` = ($id)
		";

		$this->db->select($sql);
		$results = $this->db->fetchAssoc();
  		return $results;
  }

  /**
   * @param array(int) receipt ids
   *
   * @return array $tags
   *
   * @desc return tags array for each receipt, indexed with receipt id
   */
	public function getReceiptsTags($ids){
	    if(!isset($ids) || !is_array($ids)) return false;
	    $idList = implode(',',$ids);
			$sql = "
				SELECT
					`receipt_id`, `tag`
				FROM
					`receipt_tag`
				WHERE
					`receipt_id` IN ($idList)
			";
			
			$this->db->select($sql);
			$results = $this->db->fetchAssoc();
	
	    foreach($results as $result){
	      $tags[$result['receipt_id']][] = $result['tag'];
	    }
    	return $tags;
	}	
}
?>

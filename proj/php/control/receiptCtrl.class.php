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
	
	public $user = 0;
	
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * 
	 * generate an unique receipt id based on its basic information
   * with format : "000000000000000-0000000"
	 * 
	 * @param array() $basicInfo
	 */
	private function idGen($basicInfo){
    return vsprintf("%015d-%07d",array(rand(1,999999999999999),rand(1,9999999)));
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
	 * (Need to set up the receipt_id for each item, $basicInfo is null).
	 * 
	 * 3. insert a new receipt without any items
	 * 
	 * ($items is null)
	 */
	public function insertReceipt($basicInfo, $items){
		
		$basicInfoNull = is_null($basicInfo);
		
		$itemsNull = is_null($items);
		
		$totalCost = 0;
		
		if($basicInfoNull && $itemsNull){
			return false;
		}
		
		if(!$basicInfoNull){
			
			$receiptId = $basicInfo['receipt_id'];
			
			if(empty($basicInfo['receipt_time']) || strlen($basicInfo['receipt_time']) == 0){
				$basicInfo['receipt_time'] = date("Y-m-d H:i:s");
			}
			
			if(empty($receiptId) || strlen($receiptId) == 0){
				$receiptId = $this->idGen($basicInfo);
				$basicInfo['receipt_id'] = $receiptId;
			}
			
			if(empty($basicInfo['total_cost']) || strlen($basicInfo['total_cost']) == 0){
				$basicInfo['total_cost'] = $totalCost;
			}
			
			$info = "";
			
			$info = Tool::infoArray2SQL($basicInfo);
			
			if(!Tool::securityChk($info)){
				return false;
			}
			
			$sql = "
				INSERT INTO `receipt`
				SET
				$info
			";
      echo $sql;
				
			if($this->db->insert($sql) < 0){
				//rollback
				$this->realDelete($receiptId);
				return false;
			}
			
		}
				
		if(!$itemsNull){
			
			if($receiptId == null || strlen($receiptId) == 0){
				$receiptId = $items[0]['receipt_id'];
			}
		
			$sql = "
				SELECT `total_cost`
				FROM `receipt`
				WHERE
				`receipt_id`='$receiptId'
			";
			echo $sql;
			$this->db->select($sql);
			
			if($this->db->numRows() > 0){
				$result = $this->db->fetchObject();
				$totalCost = $result[0]->total_cost;
			}
			else{
				$this->realDelete($receiptId);
				return false;
			}
			
			$n = count($items);
			
			for($i = 0; $i < $n; $i ++){
				$curCost =  $items[$i]['item_price'] * $items[$i]['item_qty'] * $items[$i]['item_discount'];
				$totalCost += $curCost;
				
				if($receiptId != null && strlen($receiptId) != 0){
					$items[$i]['receipt_id'] = $receiptId;	
				}
				
				$info = Tool::infoArray2SQL($items[$i]);
				
				if(!Tool::securityChk($info)){
					return false;
				}
				
				$sql = "
					INSERT INTO `receipt_item`
					SET
					$info	
				";
        echo $sql;
					
				if($this->db->insert($sql) < 0){
					$this->realDelete($receiptId);
					return false;
				}
			}
			
			$totalCost += $totalCost * $basicInfo['tax'];
			
			$sql = "
				UPDATE `receipt`
				SET
				`total_cost`=$totalCost
				WHERE
				`receipt_id`='$receiptId'
			";
      echo $sql;
			
			if($this->db->update($sql) <= 0){
				$this->realDelete($receiptId);
				return false;
			}
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
			DELETE from `receipt_item`
			WHERE
			`receipt_id` = '$receiptId'
		";
		$delItem = $this->db->delete($sql);
		
		$sql = "
			DELETE from `receipt`
			WHERE
			`receipt_id` = '$receiptId'
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
			UPDATE `receipt_item`
			SET
			`deleted` = true
			WHERE
			`receipt_id` = '$receiptId'
		";
		
		if($this->db->update($sql) <= 0){
			return false;
		}
		
		$sql = "
			UPDATE `receipt`
			SET
			`deleted` = true
			WHERE
			`receipt_id` = '$receiptId'
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
			UPDATE `receipt_item`
			SET
			`deleted` = false
			WHERE
			`receipt_id` = '$receiptId'
		";
		if($this->db->update($sql) <= 0){
			return false;
		}
		
		$sql = "
			UPDATE `receipt`
			SET
			`deleted` = false
			WHERE
			`receipt_id` = '$receiptId'
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
	 * @return array(object, object...);
	 * 
	 * @desc
	 * get all receipt based on a certain user account
	 */
	public function userGetAllReceipt($userAcc){
		
		$sql = "
      SELECT r.receipt_id,r.user_account,s.store_name,r.receipt_time,
      i.item_name,i.item_price,i.item_qty,r.total_cost 
			FROM `receipt` as r 
      LEFT JOIN receipt_item as i 
      ON r.receipt_id = i.receipt_id 
      LEFT JOIN store as s
      ON r.store_account = s.store_account  
			WHERE
			r.user_account='$userAcc'
      AND 
			r.`deleted`=0
      AND 
			i.`deleted`=0 
      ORDER BY r.receipt_time DESC 
		";
		
		$this->db->select($sql);
		
		if($this->db->numRows() == 0){
			return "";
		}
		else{
			$result = $this->db->fetchObject();
      $first = $result[0];
			return $this->reduceReceipt($result);
		}
	}

  public function reduceReceipt($result){
    $reduced = array();
    $last_id = 0;
    foreach($result as $item){
      if($last_id == $item->receipt_id){
        $reduced[count($reduced)-1]["items"][] = $this->buildReceiptItem($item); 
      }else{
        $reduced[] = $this->buildReceipt($item);
      }
      $last_id = $item->receipt_id;
    }
    return $reduced;
  }

  private function buildReceiptItem($item){
    return array(
      "item_name"=>$item->item_name,
      "item_price"=>$item->item_price,
      "item_qty"=>$item->item_qty,
    );
  }

  private function buildReceipt($item){
    return array(
      "receipt_id"=>$item->receipt_id,
      "store_name"=>$item->store_name,
      "receipt_time"=>$item->receipt_time,
      "total_cost"=>$item->total_cost,
      "items" => array($this->buildReceiptItem($item))
    );
  }
	
	public function userGetAllReceiptItems($receiptId){
		
		$sql = "
			SELECT *
			FROM `receipt_item`
			WHERE
			`receipt_id`='$receiptId'
			AND
			`deleted`=0
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
}
?>

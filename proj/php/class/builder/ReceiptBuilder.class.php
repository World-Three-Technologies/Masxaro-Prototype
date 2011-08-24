<?php
/*
 * ReceiptBuilder.class.php -- receipt builder class 
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
 *  build receipt objects from a list of receipt ids
 */

class ReceiptBuilder {
	protected $db;

	public function __construct(){
		$this->db = new Database();
	}
	
	/**
	 * 
	 * 
	 * @param array(int) $receiptIds
	 * 
	 * @return array(ReceiptEntity) receipt entity array, with only ids set
	 */
	public function initObjs($receiptIds) {
		$objs = array();
		foreach($receiptIds as $id) {
			$tmp = new ReceiptEntity();
			$tmp->id = $id;
			array_push($objs, $tmp);
		}
		
		return $objs;
	}
	
	/**
	 * 
	 * 
	 * @param array(int) $receiptIds
	 * 
	 * @return array(ReceiptEntity) $receipts
	 * 
	 * @desc
	 * 
	 * fetch full receipts, including tags, items
	 */
	public function build($receiptIds) {
		$receipts = $this->initObjs($receiptIds);
		$receipts = $this->fetchReceiptBasic($receipts);
		$receipts = $this->fetchReceiptTags($receipts);
		$receipts = $this->fetchReceiptItems($receipts);
		return $receipts;
	}
	
	/**
	 * 
	 * 
	 * @param array(int) $receiptIds
	 * 
	 * @return array(ReceiptEntity) $receipts
	 * 
	 * @desc
	 * 
	 * fetch mobile receipts, no items
	 */
	public function build_mobile($receiptIds) {
		$receipts = $this->initObjs($receiptIds);
		$receipts = $this->fetchReceiptBasic($receipts);
		$receipts = $this->fetchReceiptTags($receipts);
		return $receipts;
	}
	
	/**
	 * 
	 * 
	 * @param array() $receipts receipt entity array
	 * 
	 * @return array() $ids id array
	 * 
	 * @desc
	 * explode receipt ids in each entity into an array
	 */
	protected function explodeIds($receipts){
		$ids = array();
	    foreach($receipts as $receipt){
	      array_push($ids, $receipt->id);
	    }
	    return $ids;
	}
	
	/**
	 * 
	 * 
	 * @param array(ReceiptEntity) $receipts
	 * @return array(ReceiptEntity) $receipts
	 * 
	 * @desc fetch basic info for entities in the array
	 */
	protected function fetchReceiptBasic($receipts) {
		$ids = $this->explodeIds($receipts);
		$basics = $this->getReceiptsBasic($ids);
		
		$i = 0;
		foreach($receipts as $receipt) {
			$basic = $basics[$receipt->id];
			if(isset($basic)) {
				$ref = new ReflectionClass('ReceiptEntity');
				$memObjs = $ref->getProperties();
				foreach($memObjs as $memObj){
					$mem = $memObj->name;
					if(isset($basic[$mem])){
						$receipt->$mem = $basic[$mem];
					}
				}
				$i ++;
			}
		}
		return $receipts;
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
		$ids = $this->explodeIds($receipts);
	    $tags = $this->getReceiptsTags($ids);
	    foreach($receipts as $receipt){
	    	if(isset($tags[$receipt->id])){
	    		$receipt->tags = $tags[$receipt->id];
	    	}
	    }
	    return $receipts;
	}
	
	/**
	 * 
	 * 
	 * @param array() $receipts array of receipt entities
	 * 
	 * @return $receipts
	 * 
	 * @desc
	 * fetch items for each receipt entitiy in $receipts
	 */
	protected function fetchReceiptItems($receipts) {
		$ids = $this->explodeIds($receipts);
	    $items = $this->getReceiptsItems($ids);
	    foreach($receipts as $receipt) {
	    	if(isset($items[$receipt->id])) {
	    		$receipt->items = $items[$receipt->id];
	    	}
	    }
	    return $receipts;
	}
	
	/**
  	 * @param array(int) receipt ids
  	 *
  	 * @return array $receipts
  	 *
   	 * @desc return receipts basic info array, indexed by receipt ID
     */
	public function getReceiptsBasic($receiptIds) {
		$ids = implode(',', $receiptIds);
		$sql = <<<SEL
			SELECT 
				r.`id`,
				r.`store_define_id`,
				DATE_FORMAT(r.`receipt_time`, '%Y-%m-%d %h:%i %p') as receipt_time,
				r.`extra_cost`,
				r.`sub_total_cost`,
				r.`cut_down_cost`, 
				r.`tax`,
				r.`total_cost`,
				r.`source`,
				r.`currency_mark`,
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
				r.`id`
			IN
				($ids)
SEL;
		$this->db->select($sql);
		$result = $this->db->fetchAssoc();
		
		$receipts = array();
		foreach($result as $receipt) {
			$receipts[$receipt['id']] = $receipt;
		}
		
		return $receipts;
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
	
	/**
	 * 
	 * 
	 * @param array() $receiptIds
	 * 
	 * @return 2-d-array() items
	 * 
	 * return all items of a list of receipts
	 * 
	 * @example
	 * Array(
	 * [105] => Array
     *   (
     *       [0] => Array
     *          (
     *               [receipt_id] => 105
     *               [item_id] => 23
     *               [item_name] => Coffee
     *               [item_qty] => 1
     *               [item_discount] => 0.00
     *               [item_price] => 1.00
     *               [deleted] => 0
     *           )
     *
     *       [1] => Array
     *           (
     *               [receipt_id] => 105
     *               [item_id] => 29
     *               [item_name] => Salad
     *               [item_qty] => 1
     *               [item_discount] => 30.00
     *               [item_price] => 3.00
     *               [deleted] => 0
     *           )
     *	)
     *)
	 */
	public function getReceiptsItems($receiptIds){
		
		$ids = implode(",", $receiptIds);
		
		$sql = "
			SELECT
				*
			FROM 
				`receipt_item`
			WHERE
				`receipt_id`
			IN
				($ids)
			AND
				`deleted`=false
		";
		
		$this->db->select($sql);
		
		if($this->db->numRows() == 0){
			return "";
		}
		
		else {
			$result = $this->db->fetchAssoc();
			$items = array();
			foreach($result as $item) {
				if(!isset($items[$item['receipt_id']])) {
					$new = array();
					array_push($new, $item);
					$items[$item['receipt_id']] = $new;
				}
				else {
					array_push($items[$item['receipt_id']], $item);
				}
			}
			
			return $items;
		}
		
	}
}
?>

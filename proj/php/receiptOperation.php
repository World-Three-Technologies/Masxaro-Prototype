<?php
/*
 *  receiptOperation.php -- receipt logic 
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

include_once '../config.php';
include_once 'header.php';

$opcode = $post['opcode'];
$userAcc = $post['acc'];

//following parameters are optional

/**
 * 
 * @desc
 * for mobile end
 * @var boolean $mobile
 */
$mobile = $post['mobile'];

/**
 * @desc
 * result set offset control, optional
 * 
 * @var int $limitStart
 * @var int $limitOffset
 **/
$limitStart = $post['limitStart'];
$limitOffset = $post['limitOffset'];

/**
 * @desc
 * sql options, all are optional
 * @var string $groupBy
 * @var string $orderBy
 * @var boolean $orderDesc
 */
$groupBy=$post['groupBy']; 
$orderBy=$post['orderBy'];   
$orderDesc=$post['orderDesc']; 

$ctrl = new ReceiptCtrl();

Tool::setJSON();

switch($opcode){
	case 'new_receipt':
		//1-d array
		$basicInfo = $post['receipt'];
		echo $ctrl->insertReceipt($basicInfo, null);
		break;
	
	case 'new_item':
		/*
		 * items: array(array(), ...), 2-d array of items
		 * each sub array is an item
		 */
		$items = $post['items'];
		echo $ctrl->insertReceipt(null, $items);
		break;
		
	case 'f_delete_receipt':
		echo $ctrl->fakeDelete($post['id']);
		break;
		
	case 'delete_receipt':
		//delete one receipt
		echo $ctrl->realDelete($post['id']);
		break;
		
	case 'recover':
		//recover fake deleted receipt
		echo $ctrl->recoverDeleted($post['id']);
		break;
		
	case 'user_get_all_receipt':
		//user get all receipt, with basic info and all items info
		$con = array(
						'='=>array(
									'field'=>'user_account',
									'value'=>$userAcc
								  )
					);
		echo json_encode(
							$ctrl->searchReceipt($con,$userAcc, $limitStart, $limitOffset, 
											  	$groupBy, $orderBy, $orderDesc)
						);
		break;
		
	case 'user_get_receipt_items':
		echo json_encode($ctrl->userGetReceiptItems($post['receiptId']));
		break;
	
	case 'user_get_receipt_detail':
		echo json_encode($ctrl->getReceiptDetail($post['receiptId']));
	
	case 'search':
		/**
		 * @see searchingConHandler()
		 */
		$con = searchingConHandler();
		echo json_encode(
							$ctrl->searchReceipt($con,$userAcc, $limitStart, $limitOffset, 
											  	$groupBy, $orderBy, $orderDesc, $mobile)
						);
		break;
	
	case 'get_store_receipts':
		$store = $post['store'];
		$con = array(
				'='=>array(
						'field'=>'store_name',
						'value'=>$store
					)
		);
		echo json_encode(
							$ctrl->searchReceipt($con,$userAcc, $limitStart, $limitOffset, 
											  	$groupBy, $orderBy, $orderDesc)
						);
		break;
		
	case 'get_source_receipts':
		//get receipts from certain sources
		
		$sources = $post['sources'];
		
		$orConds = array();
		$i = 0;
		foreach($sources as $source){
			$orConds['=:'.$i++] = array(
													'field'=>'source',
													'value'=>$source
												);
		}
		
		$con = array(
					'OR'=>$orConds,
		);
		
		echo json_encode(
							$ctrl->searchReceipt($con,$userAcc, $limitStart, $limitOffset, 
											  	$groupBy, $orderBy, $orderDesc)
						);
		break;
		
	default:
		die('wrong parameter');
}

/**
 * @desc
 * search receipts based on keywords in item_name and store_name,
 * and time range, tags
 * 
 * multiple keywords should be organized as an 1-d array
 * keys=>array(key1, key2, key3...), eg. keys=>array('Coffee', 'coke')
 * 
 * multiple tags should be organized as an 1-d array
 * tags=>array(tag1, tag2, tag3...), eg. tags=>array('food', 'restaurant')
 * 
 * time range should be organized as an 1-d array (at least one of start, end should be set)
 * array('timeRange'=>array('start'=>'', 'end'=>''))
 * 
 * POST (optional):
 * @param array keys
 * 
 * @param array $tags
 * 
 * @param array $timeRange 'timeRange'=>array('start'=>'', 'end'=>''), 
 *                          date format: YY-MM-DD HH:MM:SS or YY-MM-DD
 * 
 * @return
 * encoded JSON of receipt object array
 **/
function searchingConHandler(){
	
	//array('timeRange'=>array('start'=>'', 'end'=>''))
	$timeStart = $post['timeRange']['start'];
	$timeEnd = $post['timeRange']['end'];
	
	$keys = isset($post['keys']) ? $post['keys'] : '';
	$tags = $post['tags'];
	
	$con = array();
	
		//keys
	if(!is_array($keys)){
		$keys = "%$keys%";
		
		// 'item_name LIKE %keys% OR store_name LIKE %$keys%'
		$con['OR'] = array(
				'LIKE:0'=>array(
						'field'=>'item_name',
						'value'=>$keys
					),
				'LIKE:1'=>array(
					'field'=>'store_name',
					'value'=>$keys
				)
			);
	}
	else{
		$tmp = array();
		$i = 0;
		foreach($keys as $key){
			$key = "%$key%";
			$tmp['like:'.$i ++] = array(
								'field'=>'item_name',
								'value'=>$key
				);
				$tmp['like:'.$i ++] = array(
									'field'=>'store_name',
									'value'=>$key
				);
			}
		$con['OR'] = $tmp;
	}
	
	//tags
	if(isset($tags) && is_array($tags)){
		$tmp = array();
		$i = 0;
		foreach($tags as $tag){
			$tag = "%$tag%";
			$tmp['like:'.$i ++] = array(
								'field'=>'tag',
								'value'=>$tag
			);
	}
	$con['OR:'."1"] = $tmp;
	$con['='] = array(
			'field'=>'`receipt_tag`.`receipt_id`',
			'field:1'=>'`receipt`.`id`'
		);
	}
	
	//time range
	if(isset($timeStart)){
		$con['>='] = array(
			'field'=>'receipt_time',
			'value'=>$timeStart
		);
	}
	if(isset($timeEnd)){
		$con['<='] = array(
			'field'=>'receipt_time',
			'value'=>$timeEnd
		);
	}
	
	$con['value'] = 1;
	
	//organize into AND condition
	$tmp = array();
	$tmp['AND'] = $con;
	$con = $tmp;
	
	return $con;
}

?>

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

/**
 * @desc
 * result set offset control, optional
 * 
 * @param int $limitStart
 * @param int $limitEnd
 **/
$limitStart = $post['limitStart'];
$limitEnd = $post['limitEnd'];

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
		echo json_encode($ctrl->searchReceipt($con,$userAcc));
		break;
	
	case 'search':
		echo json_encode($ctrl->searchReceipt($post['con']));
		break;
	
	case 'key_search':
		/**
		 * @desc
		 * search receipts based on keywords in item_name and store_name
		 * 
		 * multiple keywords should be organized as an 1-d array
		 *
		 * array(key1, key2, key3...), eg. array('Coffee', 'coke')
		 * 
		 * POST:
		 * @param array keys
		 **/
		
		$keys = isset($post['keys']) ? $post['keys'] : '';
		
		$con = array();
		
		if(!is_array($keys)){
			$keys = "%$keys%";
			
			// 'item_name LIKE %keys% OR store_name LIKE %$keys%'
			$con['OR'] = array(
							'LIKE'.CON_DELIMITER.'0'=>array(
								'field'=>'item_name',
								'value'=>$keys
							),
							'LIKE'.CON_DELIMITER.'1'=>array(
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
				$tmp['like'.CON_DELIMITER.$i ++] = array(
														'field'=>'item_name',
														'value'=>$key
													);
				$tmp['like'.CON_DELIMITER.$i ++] = array(
														'field'=>'store_name',
														'value'=>$key
													);
			}
			$con['OR'] = $tmp;
		}
		
		if(isset($limitStart) && isset($limitEnd)){
			echo json_encode($ctrl->searchReceipt($con, $userAcc, $limitStart, $limitEnd));
		}
		else{
			echo json_encode($ctrl->searchReceipt($con, $userAcc));
		}
		break;
		
	case 'tag_search':
		/**
		 * @see tagOperation.php $tags
		 * 
		 * @desc
		 * search receipts based on tags
		 * 
		 * multiple tags should be organized as an 1-d array
		 *
		 * array(tag1, tag2, tag3...), eg. array('gym', 'museum')
		 * 
		 * POST:
		 * @param array tags
		 **/
		
		$tags = $post['tags'];

		if(!is_array($tags)){
			die('wrong parameters');
		}
		$orConds = array();
		$i = 0;
		foreach($tags as $tag){
			$orConds['='.CON_DELIMITER.$i++] = array(
													'field'=>'tag',
													'value'=>$tag
												);
		}
		
		$con = array(
					'OR'=>$orConds,
		);
		
		if(isset($limitStart) && isset($limitEnd)){
			echo json_encode($ctrl->searchTagReceipt($con, $userAcc, $limitStart, $limitEnd));
		}
		else{
			echo json_encode($ctrl->searchTagReceipt($con, $userAcc));
		}
		break;
		
	case 'time_search':
		/**
		 * @desc
		 * search receipts based on time range
		 * 
		 * date format: YY-MM-DD HH:MM:SS or YY-MM-DD
		 * 
		 * POST:
		 * start or end cannot be null at the same time,
		 * at least one of them should be set
		 * @param string start start date
		 *      
		 * @param string end end date
		 * 
		 * @param int limitStart (optional)
		 *      
		 * @param int limitEnd   (optional)
		 **/
		$start = $post['start'];
		$end = $post['end'];
		
		$searchOption = -1;
		
		if(isset($start)){
			$searchOption = 0;
			if(isset($end)){
				$searchOption = 2;
			}
		}
		else if(isset($end)){
			$searchOption = 1;
		}
		else{
			die('wrong parameter');
		}
		
		$con = array();
		
		switch($searchOption){
			case 0:
				$con = array(
						'>='=>array(
								'field'=>'receipt_time',
								'value'=>$start
							)
				);
				break;
			case 1:
				$con = array(
						'<='=>array(
								'field'=>'receipt_time',
								'value'=>$end
							)
				);
				break;
			case 2:
				$con = array(
						'AND'=>array(
							'>='=>array(
								'field'=>'receipt_time',
								'value'=>$start
							),
							'<='=>array(
								'field'=>'receipt_time',
								'value'=>$end
							),
						)
						
				);
				break;
		}

		if(isset($limitStart) && isset($limitEnd)){
			echo json_encode($ctrl->searchReceipt($con, $userAcc, $limitStart, $limitEnd));
		}
		else{
			echo json_encode($ctrl->searchReceipt($con, $userAcc));
		}
		break;
	
	case 'get_store_receipt':
		$store = $post['store'];
		$con = array(
				'='=>array(
						'field'=>'store_name',
						'value'=>$store
					)
		);
		echo json_encode($ctrl->searchReceipt($con, $userAcc));
		break;
}
?>

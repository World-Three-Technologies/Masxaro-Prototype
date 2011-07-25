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
		
	case 'user_get_all_receipt_basic':
		//user get  all receipts' basic info
		echo json_encode($ctrl->userGetAllReceiptBasic($userAcc));
		break;
		
	case 'user_get_receipt_item':
		//user get items info of one certain receipt
		echo json_encode($ctrl->userGetReceiptItems($post['id']));
		break;
		
	case 'user_get_all_receipt':
		//user get all receipt, with basic info and all items info
		$con = array(
						'='=>array(
									'field'=>'user_account',
									'value'=>$userAcc
								  )
					);
		echo json_encode($ctrl->searchReceipt($con));
		break;
		
	case 'user_get_receipt_detail':
		//user get basic info of one certain receipt
		echo json_encode($ctrl->getReceiptDetail($post['id']));
		break;
	
	case 'search':
		echo json_encode($ctrl->searchReceipt($post['con']));
		break;
	
	case 'key_search':
		/*
		 * search receipts based on keywords in item_name and store_name
		 * 
		 * multiple keywords should be organized as an 1-d array
		 *
		 * array(key1, key2, key3...), eg. array('Coffee', 'coke')
		 */
		
		$keys = isset($post['keys']) ? $post['keys'] : '';
		
		$keys = array('Coffee', 'coke');
		
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
		
		echo json_encode($ctrl->searchReceipt($con, $userAcc));
		break;
		
	case 'tag_search':
		/**
		 * @see tagOperation.php $tags
		 */
		$tags = $post['tags'];
//		$tags = array(
//				array('tag'=>'restaurant'),
//				array('tag'=>'movie')
//		);
		if(!is_array($tags)){
			die('wrong parameters');
		}
		$orConds = array();
		$i = 0;
		foreach($tags as $tag){
			$orConds['='.CON_DELIMITER.$i++] = array(
													'field'=>'tag',
													'value'=>$tag['tag']
												);
		}
		
		$con = array(
					'OR'=>$orConds,
		);
		echo json_encode($ctrl->searchTagReceipt($con, $userAcc));
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
		
	default:
		echo 'wrong parameters';
		break;
}

?>

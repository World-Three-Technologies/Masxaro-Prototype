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

$ctrl = new ReceiptCtrl();

Tool::setJSON();

switch($opcode){
	case 'new_receipt':
		//1-d array
		$basicInfo = $post['receipt'];
		echo $ctrl->insertReceipt($basicInfo, null);
		break;
	
	case 'new_item':
		//2-d array
		$items = $post['items'];
		echo $ctrl->insertReceipt(null, $items);
		break;
		
	case 'f_delete_receipt':
		//fake delete one receipt
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
		echo json_encode($ctrl->userGetAllReceiptBasic($post['acc']));
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
									'value'=>$post['acc']
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
		$key = isset($post['key']) ? $post['key'] : '';
		$con = array(
				'OR'=>array(
					'like:0'=>array(
						'field'=>'item_name',
						'value'=>"%$key%"
					),
					'like:1'=>array(
						'field'=>'store_name',
						'value'=>"%$key%"
					),
				)
		);
		echo json_encode($ctrl->searchReceipt($con, $post['acc']));
		break;
		
	default:
		echo 'wrong parameters';
		break;
}

?>

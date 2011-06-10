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

$opcode = $_POST['opcode'];

//$opcode = 'user_get_all_receipt_item';

$ctrl = new ReceiptCtrl();

switch($opcode){
	case 'new_receipt':
		
		//$code = "983094867189238-0929347";
		
		//1-d array
		$basicInfo = $_POST['receipt'];
					
		echo $ctrl->insertReceipt($basicInfo, null);
		
		break;
		
	
	case 'new_item':
		
		//2-d array
		$items = $_POST['items'];
		
		echo $ctrl->insertReceipt(null, $items);
		break;
		
		
	case 'delete_receipt':
		echo $ctrl->fakeDelete($_POST['receiptId']);
		break;
		
		
	case 'recover':
		echo $ctrl->fakeDelete($_POST['receiptId']);
		break;
		
		
	case 'user_get_all_receipt':
		echo json_encode($ctrl->userGetAllReceipt($_POST['acc']));
		break;
		
		
	case 'user_get_all_receipt_item':
		echo json_encode($ctrl->userGetAllReceiptItems($_POST['receiptId']));
		break;
}





?>
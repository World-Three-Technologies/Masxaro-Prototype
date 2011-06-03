<?php
/*
 * receipt.php -- receipt logic testing 
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
 *  test receipt control API and simple logic
 */

include_once '../config.php';

$code  ="983094867189238-0929347";

//basic info
$basicInfo = array('receipt_id'=>$code, 
				   'store_id'=>1, 
				   'user_account'=>'test', 
				   'tax'=>0.1, 
				   'total_cost'=>0);

//item list
$items = array();

//item 1
$i = array('receipt_id'=>$code, 
		   'item_id'=>1, 
		   'item_name'=>'test', 
		   'item_qty'=>3, 
		   'item_price'=>10, 
		   'item_discount'=>1);

array_push($items, $i);

//item 2
$i = array('receipt_id'=>$code, 
		   'item_id'=>2, 
		   'item_name'=>'test2', 
		   'item_qty'=>1, 
		   'item_price'=>20, 
		   'item_discount'=>1);

array_push($items, $i);



$ctrl = new ReceiptCtrl();

echo "delete".$result = $ctrl->realDelete($code);

echo "</br>receipt insert".$result1 = $ctrl->insert($basicInfo, $items);

$result &= $result1;

//echo "</br>receipt insert".$result1 = $ctrl->insert($basicInfo, null);
//
//$result &= $result1;
//
//echo "</br>item insert".$result1 = $ctrl->insert(null, $items);
//
//$result &= $result1;

echo "</br>fake delete".$result1 = $ctrl->fakeDelete($code);

$result &= $result1;

echo "</br>recover".$result1 = $ctrl->recoverDeleted($code);

$result &= $result1;

echo "</br>";

if($result){
	echo "success";
}
else{
	echo "fail";
}
?>
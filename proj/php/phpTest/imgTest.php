<?php
/*
 * imgTest.php -- test image functions 
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
 */

include_once '../../config.php';

$path = ROOT_PATH."/buffer/test.jpg";

//header ("Content-type: image/jpeg");

$img = Tool::imgToBlob($path);

$ctrl = new ReceiptCtrl();

$receiptId = 1;

$param = array('img'=>$img);

print_r($ctrl->updateReceiptBasic($receiptId, $param));

$result = $ctrl->getReceiptDetail(1);

if(!empty($result[0]->img)){
	header ("Content-type: image/jpeg");
	echo $result[0]->img;
}

else{
	print_r($result[0]);
}

?>
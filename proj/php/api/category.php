<?php
/*
 *  categoryOperation.php -- category operations 
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
include_once 'header.php';

$opcode = $post['opcode'];
$cat = $post['category'];
$ctrl = new CategoryCtrl();

switch($opcode){
	case 'add_receipt_category':
		$info = array(
				'category'=>$cat,
				'target'=>'receipt'
		);
		echo json_encode($ctrl->insertCategory($info));
		break;
		
	case 'add_item_category':
		$info = array(
				'category'=>$cat,
				'target'=>'item'
		);
		echo json_encode($ctrl->insertCategory($info));
		break;
	
	case 'add_common_category':
		$info = array(
				'category'=>$cat,
				'target'=>'all'
		);
		echo json_encode($ctrl->insertCategory($info));
		break;
		
	case 'delete':
		echo $ctrl->deleteCategory($cat);
		break;
	
	case 'get_target_category':
		$con = array(
				'='=>array(
						'field'=>'target',
						'value'=>$post['target']
					)
		);
		echo json_encode($ctrl->getTargetCategory($con));
		break;
}

?>

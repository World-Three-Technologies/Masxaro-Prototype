<?php
/*
 *  tagOperation.php -- tag operations 
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

include_once '../config.php';
include_once 'header.php';

$opcode = $post['opcode'];
$opcode = 'get_user_tags';
/*
 * array, 
 * eg: array(
 * 			array('tag'=>'tag'),
 * 			array('tag'=>'tag1')
 * 		)
 */
$tags = $post['tags']; 
//$tags = array(
//  			array('tag'=>'food'),
//  			array('tag'=>'play')
// 		);
$user = $post['user_account'];
$receipt = $post['receipt_id'];
if(isset($tags) && !is_array($tags)){
	die('wrong parameters');
}

$ctrl = new TagCtrl();
$fails = array();//failed records

switch($opcode){
	case 'new_tags':
		foreach($tags as $tag){
			$info = array(
				'tag'=>$tag['tag'],
				'user_account'=>$user
			);
			if(!$ctrl->insert($info)){
				array_push($fails, $tag['tag']);
			}
		}
		echo errorHandler($fails);
		break;
		
	case 'delete_tags':
		foreach($tags as $tag){
			$con = array(
					'AND'=>array(
							'='=>array(
									'field'=>'tag',
									'value'=>$tag['tag']
							),
							'='.CON_DELIMITER.'1'=>array(
									'field'=>'user_account',
									'value'=>$user
							)
					)
			);
			
			if($ctrl->delete($con) <= 0){
				array_push($fails, $tag['tag']);
			}
		}
		echo errorHandler($fails);
		break;
	
	case 'add_receipt_tags':
		//add multi tags to a certain receipt
		foreach($tags as $tag){
			$info = array(
				'tag'=>$tag['tag'],
				'user_account'=>$user,
				'receipt_id'=>$receipt
			);
			if(!$ctrl->tagReceipt($info)){
				array_push($fails, $tag['tag']);
			}
		}
		echo errorHandler($fails);
		break;
		
	case 'delete_receipt_tags':
		foreach($tags as $tag){
			$con = array(
				'AND'=>array(
							'=' => array(
										'field'=>'tag',
										'value'=>$tag['tag']	
									),
							'='.CON_DELIMITER.'1' => array(
										'field'=>'user_account',
										'value'=>$user	
									),
							'='.CON_DELIMITER.'2' => array(
										'field'=>'receipt_id',
										'value'=>$receipt	
									)
						)
			);
			
			if($ctrl->deleteReceiptTag($con) <= 0){
				array_push($fails, $tag['tag']);
			}
		}
		echo errorHandler($fails);
		die();
	
	case 'get_user_tags':
		$con = array(
				'='=>array(
					'field'=>'user_account',
					'value'=>$user
				)
		);
		
		echo json_encode($ctrl->select($con));
}

function errorHandler($fails){
	if(count($fails) == 0){
		return true;
	}
	$failedReturn = "";
	foreach($fails as $failedTag){
				$failedReturn .= "\"$failedTag\", ";
			}
	$failedReturn = substr($failedReturn, 0, -2);
	return "Errors occured on tags: $failedReturn.";
}

?>

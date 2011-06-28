<?php
/*
 *  contactOperation.php -- contact operation class 
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

$acc = isset($jsonPost) ? $jsonPost['acc'] : $_POST['acc'];

if(!Tool::authenticate($acc)){
	echo "error: need login";
	die();
}

$opcode = isset($jsonPost) ? $jsonPost['opcode'] : $_POST['opcode'];

$ctrl = new ContactCtrl();

switch($opcode){
	case 'new_contacts':
		$contacts = isset($jsonPost) ? $jsonPost['contacts'] : $_POST['contacts'];
		echo $ctrl->insertContact($contacts);
		break;
		
	case 'delete_contacts':
		$values = isset($jsonPost) ? $jsonPost['values'] : $_POST['values'];
		$n = count($values);
		$result = true;
		for($i = 0; $i < $n; $i ++){
			$result &= $ctrl->deleteContact($values[$i]['value']);
		}
		echo $result;
		break;
		 
	case 'get_contacts':
		$acc = isset($jsonPost) ? $jsonPost['acc'] : $_POST['acc'];
		$who = isset($jsonPost) ? $jsonPost['who'] : $_POST['who'];
		echo json_encode($ctrl->getContacts($acc, $who));
		break;
		
	default:
		break;
}

?>

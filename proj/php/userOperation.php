<?php
/*
 *  userOperation.php -- user operations 
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

$ctrl = new UserCtrl();

switch($opcode){
	case 'get_profile':
		echo json_encode($ctrl->getProfile($post['acc']));
		break;
	
	case 'update_profile':
		echo $ctrl->update($post['acc'], $post['info']);
		break;
	
	case 'verify':
		echo $ctrl->find($post['acc'], $post['pwd']);
		break;

	case 'fake_delete':
		$acc = $post['acc'];
		$emailCtrl = new EmailCtrl();
		echo $emailCtrl->suspendAcc($acc) ? $ctrl->fakeDeleteUser($acc) : false;
		break;
		
	case 'recover':
		$acc = $post['acc'];
		$emailCtrl = new EmailCtrl();
		echo $emailCtrl->restoreAcc($acc) ? $ctrl->recoverDeletedUser($acc) : false;
		$ctrl->recoverDeletedUser($acc);
		break;
	
	case 'delete':
		$acc = $post['acc'];
		$emailCtrl = new EmailCtrl();
		echo $emailCtrl->deleteAcc($acc) ? $ctrl->delete($acc) : false;
		$ctrl->realDeleteUser($acc);
		break;
	
	default:
		die('wrong parameters');
		break;
}


?>

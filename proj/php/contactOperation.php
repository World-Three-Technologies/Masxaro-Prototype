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

$opcode = $_POST['op'];

//var_dump($opcode);
//die();

switch($opcode){
	case 'new_contacts':
		
		$contacts = $_POST['contacts'];
		//$contacts = json_decode($contacts);
		
//		var_dump($contacts);
//		die();
		
		$ctrl = new ContactCtrl();
		//$ctrl->insertContact($contacts);
		var_dump($ctrl->insertContact($contacts));
		break;
		
	default:
		break;
}

?>

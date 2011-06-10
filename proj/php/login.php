<?php

/*
 *  login.php -- user/store login 
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
 *  user/store login
 */
include_once '../config.php';

$acc = $_POST['acc'];
$pwd = $_POST['pwd'];
$type = $_POST['type']; // string, 'user' or 'store'


//for test
//$acc = 'new';
//$pwd = '123';
//$type = 'user';

//$acc = 'Mc_NYU';
//$pwd = '123';
//$type = 'store';

switch($type){
	case 'user':
		$ctrl = new UserCtrl();
		if(!$ctrl->findUser($acc, $pwd)){
			echo false;
		}
		else{
			Tool::login($acc, $pwd, $type);
			echo true;
      Tool::redirect("/php/index.html");
    }
		break;
		
	case 'store':
		$ctrl = new StoreCtrl();
		if(!$ctrl->findStore($acc, $pwd)){
			echo false;
		}
		else{
			Tool::login($acc, $pwd, $type);
			echo true;
		}
		break;
		
	default:
		echo 'wrong login';
		break;
}

?>

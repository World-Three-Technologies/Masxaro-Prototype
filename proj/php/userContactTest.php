<?php
/*
 * user_contactTest.php -- user API testing 
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
 *  user, contact API test
 */
include_once '../config.php';


//contact test
//$ctrl = new ContactCtrl();
//
//print_r($ctrl->deleteContact('test@masxaro.com'));
//print_r($ctrl->deleteContact('personal@gmail.com'));
//
//die();

//user ctrl test

$ctrl = new UserCtrl();

//var_dump($ctrl->userLogin('test', '123'));

//die();

$param = Array('user_account'=>'test', 
			   'first_name'=>'yaxing', 
			   'age_range_id'=>1, 
			   'ethnicity'=>'Asia', 
			   'pwd'=>'123');

var_dump($ctrl->realDeleteUser('test')." delete user </br>");

var_dump($ctrl->insertUser($param)." insert user </br>");


//contact test
$ctrl = new ContactCtrl();

var_dump($ctrl->insertContactType("email")." insert contact type </br>");
var_dump($ctrl->insertContactType("phone")." insert contact type </br>");

$param = Array();

$cont = Array('user_account'=>'test', 
			  'contact_type'=>'email', 
			  'value'=>'test@masxaro.com');

array_push($param, $cont);

$cont = Array('user_account'=>'test', 
			  'contact_type'=>'email', 
			  'value'=>'personal@gmail.com');

array_push($param, $cont);

var_dump($ctrl->insertContact($param)." insert contacts </br>");

?>
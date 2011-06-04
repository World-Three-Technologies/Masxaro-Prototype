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
$ctrl = new ContactCtrl();

echo $ctrl->deleteContact('test@masxaro.com');
echo $ctrl->deleteContact('personal@gmail.com');

die();

//user ctrl test

$ctrl = new UserCtrl();

$param = Array('user_account'=>'test', 
			   'first_name'=>'yaxing', 
			   'age_range'=>1, 
			   'ethnicity'=>'Asia', 
			   'pwd'=>'123');

$result = $ctrl->realDelete('test');

$result = $ctrl->insert($param);


//contact test
$ctrl = new ContactCtrl();

$ctrl->insertContactType("email");
$ctrl->insertContactType("phone");

$param = Array();

$cont = Array('user_account'=>'test', 
			  'contact_type'=>'email', 
			  'value'=>'test@masxaro.com');

array_push($param, $cont);

$cont = Array('user_account'=>'test', 
			  'contact_type'=>'email', 
			  'value'=>'personal@gmail.com');

array_push($param, $cont);

$result = $ctrl->insertContact($param);

if($result){
	echo "success";
}
else{
	echo "fail";
}

?>
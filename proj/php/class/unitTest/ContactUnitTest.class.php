<?php
/*
 *  ContactUnitTest.class.php -- unit test control for contact control class 
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

class ContactUnitTest extends UnitTest{
	function __construct(){
		parent::__construct();
		$this->ctrl = new ContactCtrl();
	}
	
	public function insertContact_Test($acc){
		$param = array(
						array(
							'user_account'=>$acc, 
							'value'=>'contact_test@masxaro.com', 
							'contact_type'=>'email'
						),
						
						array(
							'user_account'=>$acc,
							'value'=>'2025112231',
							'contact_type'=>'phone'
						)
				);
				
		$this->assertTrue($this->ctrl->insertContact($param));
	}
	
	public function deleteContact_User_Test(){
		$this->assertTrue(!$this->ctrl->deleteContact('contact_test@masxaro.com'));
		$this->assertTrue($this->ctrl->deleteContact('2025112231'));
	}
	
	public function deleteContact_Admin_Test(){
		$this->assertTrue($this->ctrl->deleteContact('contact_test@masxaro.com', true));
	}
	
	public function chkContact_Test($value, $result){
		
		$this->assertEquals($this->ctrl->chkContact($value), $result);
	}
}

?>
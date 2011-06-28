<?php
/*
 *  StoreUnitTest.class.php -- unit test class for store control class  
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

class StoreUnitTest extends UnitTest{
	
	public function __construct(){
		parent::__construct();
		$this->ctrl = new StoreCtrl();
	}
	

	public function chkAccount_Test($acc){
		$this->assertTrue($this->ctrl->chkAccount($acc));
	}
	
	public function insertStore_Test(){
		$info = array(
						'store_account'=>'stest',
						'pwd'=>'123',
						'store_name'=>'stest_store'
					);
		$this->assertTrue($this->ctrl->insert($info));
	}
	
	public function deleteStore_Test($acc){
		$this->assertTrue($this->ctrl->delete($acc));
	}
	
	public function updateStoreInfo_Test($acc){
		$info = array(
						'store_account'=>'stest',
						'pwd'=>'123'
					);
		
		$this->assertTrue($this->ctrl->update($acc, $info));
	}
	
	public function findStore_Test($acc, $pwd){
		$this->assertTrue($this->ctrl->find($acc, $pwd));
	}
}
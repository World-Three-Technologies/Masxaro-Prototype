<?php
/*
 *  ReceiptUnitTest.class.php -- unit test class for receipt control class  
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

class ReceiptUnitTest extends UnitTest{
	
	public $testId = 0;
	
	function __construct(){
		parent::__construct();
		$this->ctrl = new ReceiptCtrl();
	}
	
	/**
	 * 
	 * @desc
	 * test insertReceipt funciton, insert a full receipt with items
	 */
	function insertReceipt_Full_Test(){
		$basicInfo = array(
							"store_account"=>"Mc_NYU",
							"user_account"=>"new",
							"tax"=>15
						);
						
		$items = array();
		
		$item = array(
						"item_id"=>23,
						"item_name"=>"Coffee",
						"item_qty"=>1,
						"item_price"=>1,
					);
					
		array_push($items, $item);
		
		$item = array(
						"item_id"=>29,
						"item_name"=>"Salad",
						"item_discount"=>"30",
						"item_qty"=>1,
						"item_price"=>3,
					);
		
		array_push($items, $item);
						
		
						
		$this->assertTrue(($this->testId = $this->ctrl->insertReceipt($basicInfo, $items)) > 0);
	}
	
	
	/**
	 * 
	 * @desc
	 * test insertReceipt funciton, insert a receipt with no items
	 */
	function insertReceipt_Empty_Test(){
		$basicInfo = array(
							"store_account"=>"Mc_NYU",
							"user_account"=>"new",
							"tax"=>10
						);
						
		$this->assertTrue(($this->testId = $this->ctrl->insertReceipt($basicInfo, null)) > 0);
	}
	
	/**
	 * 
	 * @desc
	 * test insertReceipt funciton, insert a items for an existed receipt,
	 * 
	 * modify receipt id before perform test
	 */
	function insertReceipt_NewItem_Test($testId){
						
		$items = array("receipt_id"=>$testId);
		
		$item = array(
						"item_id"=>23,
						"item_name"=>"Coffee",
						"item_qty"=>1,
						"item_price"=>1.00,
					);
					
		array_push($items, $item);
		
		$item = array(
						"item_id"=>29,
						"item_name"=>"Salad",
						"item_qty"=>1,
						"item_price"=>3.00,
					);
		
		array_push($items, $item);
		
		$items['id'] = $this->testId;
						
		
						
		$this->assertTrue($this->ctrl->insertReceipt(null, $items));
	}
	
	/**
	 * @desc
	 * fake delete test
	 */
	function fakeDelete_Test($id){
		
		$this->assertTrue($this->ctrl->fakeDelete($id));
	}
	
	/**
	 * @desc
	 * recover test
	 */
	function recoverDeleted_Test($id){
		
		$this->assertTrue($this->ctrl->recoverDeleted($id));
	}
	
	/**
	 * @desc
	 * real delete test
	 */
	function realDelete_Test($id){
		
		$this->assertTrue($this->ctrl->realDelete($id));
	}
}
?>

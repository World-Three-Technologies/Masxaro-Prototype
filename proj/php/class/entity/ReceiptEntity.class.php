<?php
/*
 * ReceiptEntity.class.php -- Receipt entity 
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

class ReceiptEntity{
	//basic info
	public $id = NULL;
	public $store_name = NULL;
	public $user_account = NULL;
	public $receipt_time = NULL;
	public $store_define_id = NULL;
	public $sub_total_cost = 0;
	public $cut_down_cost = 0;
	public $extra_cost = 0;
	public $tax = 1;
	public $total_cost = 0;
	public $currency_mark = '$';
	public $source = 'default';
	public $img = NULL;
	public $deleted = 0;
	
	//additional info
	public $items = array();
	public $tags = array();
}
?>

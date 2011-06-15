<?php
/*
 *  unitTest.class.php -- temprary unit test class  
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

class UnitTest{
	
	public function assertTrue($value){
		$back = debug_backtrace();
		$curCall = $back[1]['function']."(".get_called_class().")";
		
		$result = "";
		
		if(!$value){
			$result = "FAILED: ";
		}
		else{
			$result = "PASSED: ";
		}
		
		echo $result.$curCall."</br>";
	}
	
	public function assertEquals($value, $target){
		$back = debug_backtrace();
		$curCall = $back[1]['function']."(".get_called_class().")";
		
		$result = "";
		
		if($value != $target){
			$result = "FAILED: ";
		}
		else{
			$result = "PASSED: ";
		}
		
		echo $result.$curCall."</br>";	
	}
}

?>
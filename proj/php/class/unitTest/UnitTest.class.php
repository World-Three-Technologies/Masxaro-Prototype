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
	
	protected $ctrl = null;
	private $fail = "FAILED: ";
	private $pass = "PASSED: ";
	private $curCall = null;
	private $result = null;
	
	public function __construct(){
		
	}
	
	/**
	 * 
	 * 
	 * @param array $var
	 * 
	 * turn an 1-d array into string
	 */
	private function oneDArrayToString($var){
		if(!is_array($var)){
			return $var;
		}
		return implode(",", $var);
	}
	
	
	private function printResult(){
		echo $this->result.$this->curCall."</br>";
	}
	
	
	public function assertTrue($value){
		$back = debug_backtrace();
		$this->curCall = $back[1]['function']."(".get_called_class().")";
		
		$this->result = "";
		
		if(!$value){
			$this->result = $this->fail;
		}
		else{
			$this->result = $this->pass;
		}
		
		$this->printResult();
		return;
	}
	
	/**
	 * 
	 * 
	 * @param array or string $value
	 * @param array or string $target
	 */
	public function assertEquals($value, $target){
		
		$back = debug_backtrace();
		$this->curCall = $back[1]['function']."(".get_called_class().")";
		
		$this->result = "";
		
		if(is_array($value)^is_array($target)){
			$this->result = $this->fail;
			$this->printResult();
			return;
		}
		
		if(is_array($value)){
			if(!is_array($target)){
				$this->result = $this->fail;
				$this->printResult();
				return;
			}
			else if(count($value, 1) != count($target, 1)){
				$this->result = $this->fail;
				$this->printResult();
				return;
			}
			else{
				$value = array_map($this->oneDArrayToString, $value);
				$target = array_map($this->oneDArrayToString, $target);
				
				$value = $this->oneDArrayToString($value);
				$target = $this->oneDArrayToString($target);
			}
		}
		
		if($value != $target){
			$this->result = $this->fail;
		}
		else{
			$this->result = $this->pass;
		}
		
		$this->printResult();
		return;	
	}
}

?>
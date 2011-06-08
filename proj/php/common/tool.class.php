<?php
/*
 *  tool.class.php -- public common tool functions 
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
 *  tool global functions for all
 */

class Tool{
	/**
	 * infoArray to SQL query
	 *
	 * @param array() contains item info ([0] key1 => value1, [1] key2 => value2, [2] key3 => value3...)
	 * 
	 * @return string SQL query ("key1=v1, key2=v2...")
	 */
	public static function infoArray2SQL($info)
	{
		$sql = '';
		foreach ($info as $key => $value)
			$sql = $sql."`{$key}` = '$value',";
		$sql = substr($sql, 0, strlen($sql)-1);		
		return $sql;
	}
	
	/**
	 * 
	 * @param string $str
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * make sure a string doesn't contain any malisious scripts,
	 * if clear, return true
	 * else return false
	 */
	public static function securityChk($str){
		return true;
	}
	
	/**
	 * 
	 * 
	 * @param string $acc (user account) OR int $acc (store id)
	 * 
	 * @return boolean
	 * 
	 * @desc
	 * 
	 * authenticate user/store log in status
	 */
	public static function authenticate($acc){
		if(isset($_COOKIE['user_acc']) && $_COOKIE['user_acc'] == $acc){
			return true;
		}
		
		else if(isset($_COOKIE['store_id']) && $_COOKIE['store_id'] == $acc){
			return true;
		}
		
		return false;
	}
	
	
	/**
	 * 
	 * 
	 * @param string $acc (user account or store id)
	 * 
	 * @param string $pwd
	 * 
	 * @param string $type (user or store)
	 * 
	 * @desc
	 * 
	 * user / store login cookie set
	 */
	public static function login($acc, $pwd, $type){
		switch($type){
			case 'user':
				setcookie('user_acc', $acc, time() + 20 * 60 * 60); //1 day
				break;
			case 'store':
				setcookie('store_id', $acc, time() + 20 * 60 * 60); //1 day
				break;
		}
	}
	
	/**
	 * 
	 * 
	 * @param string $acc (user account or store id)
	 * 
	 * @desc
	 * 
	 * user / store log off
	 */
	public static function logoff($acc){
		setcookie('user_acc', '');
		setcookie('store_id', '');
	}
}
?>
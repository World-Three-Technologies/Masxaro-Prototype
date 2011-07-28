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
		
		$regxFunc = '(^.*\(\))';
		//$regxNumber = '(^[0-9]+$)';
		
		foreach ($info as $key => $value){
			
			//if(preg_match($regxFunc, $value) || preg_match($regxNumber, $value)){
			if(preg_match($regxFunc, $value)){
				$sql = $sql."`{$key}` = {$value},";
			}
			
			else if(!isset($value) || strcasecmp($value, 'null') == 0){
				$sql = $sql."`{$key}` = NULL,";
				continue;
			}
			else{
				$sql = $sql."`{$key}` = '$value',";
			}
		}
		$sql = substr($sql, 0, -1);	
		return $sql;
	}
	
	/**
	 * 
	 * 
	 * @param array() $con multi-dimension array of SQL condition
	 * 
	 * @param string $curOperator for the use of recursion algorithm, don't need to set at the first call
	 * 
	 * @return string $condition
	 * 
	 * @desc
	 * transfer sql condition array into string sql statement  
	 */
	public static function condArray2SQL($con, $curOperator = ''){
		
		if(!is_array($con)){
			if(preg_match("(^field)", $curOperator) || preg_match("(^formula)", $curOperator)){
				return $con;
			}
			else if(preg_match("(^value)", $curOperator)){
				return is_bool($con) ? $con : "'".$con."'";
			}
			else{
				die('wrong parameter');
			}
		}
		
		$buffer = array();
		foreach($con as $operator=>$operand){
			$tmp = explode(CON_DELIMITER, $operator);
			$operator = $tmp[0];
			array_push($buffer, Tool::condArray2SQL($operand, $operator));
		}
		return '('.implode(" $curOperator ", $buffer).')';
	}

	
	/**
	 * @todo
	 * complete securityChk regular expression
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
		$regex = "(<script>)";
		//$regex = "(/[^a-z0-9\\/\\\\_.:-]/i)";
		return !preg_match($regex, $str); 
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
	public static function authenticate($acc = ''){
		if(isset($_COOKIE['user_acc']) && ($acc == '' ? true : $_COOKIE['user_acc'] == $acc)){
				return true;
		}
		else if(isset($_COOKIE['store_acc']) && ($acc == '' ? true :  $_COOKIE['store_acc'] == $acc)){
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
	public static function login($acc, $type){
		switch($type){
			case 'user':
				return setcookie('user_acc', $acc, time() + 24 * 60 * 60, "/"); //1 day
				break;
			case 'store':
				return setcookie('store_acc', $acc, time() + 24 * 60 * 60, "/"); //1 day
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
		setcookie('user_acc', '', time() - 1, "/");
		setcookie('store_acc', '', time() - 1, "/");
	}
	
	/**
	 * 
	 * @param string $path image path
	 * 
	 * @return string blob string
	 */
	public static function imgToBlob($path){
		return addslashes(fread(fopen($path,"r"),filesize($path)));
	}
	
	/**
	 * 
	 * error handler
	 */
	public static function errorHandle(){
		
	}

  /**
   * @param string $path redirect path
   * 
   * redirect user to path
   */
  public static function redirect($path){
    Header("HTTP/1.1 301 Redirect");
    Header("Location: $path");
  }

  public static function redirectToPortal(){
    Tool::redirect("/php/index.php");
  }

  public static function redirectToProduct(){
    Tool::redirect("/php/product.php");
  }

  /**
   * set mime header to application/json 
   */
  public static function setJSON(){
    header("Content-Type: application/json");
  }
  
	/**
	 * 
	 * 
	 * @param array() $info
	 * 
	 * @return  generated code
	 * 
	 * @desc
	 * 
	 * registration verification code generation
	 */
	public static function verifyCodeGen($info){
		return base64_encode(base64_encode(implode("&&", $info)));
	}
	
	/**
	 *
	 * @param string $code
	 * 
	 * @desc decode verification code
	 * 
	 * @return array()
	 */
	public static function decodeVerifyCode($code){
		$code = base64_decode(base64_decode($code));
		
		return explode("&&", $code);
	}
	
	/**
	 * 
	 * @param string $acc user account
	 * 
	 * @return string pwd user password for masxaro mailbox
	 * 
	 * @desc
	 * retrieve the password of masxaro mailbox according to user account
	 */
	public static function getEmailPwd($acc){
		$buf = md5(md5($acc));
		return substr($buf, 0, MIN_ACC_LEN * 2);
	}
}
?>

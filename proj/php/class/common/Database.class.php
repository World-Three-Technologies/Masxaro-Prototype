<?php  
/*
 * Database.class.php -- DB access class
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
 */     

class Database  
{  
  private $pConnect=FALSE;//permernent connect permission  
  
  private $mHost;//host name
  
  private $db; //database name
  
  private $mUser;//user name
  
  private $mPwd;//pass word
  
  private $mConn;//connection handle
  
  private $result;//results set
    
  private $num_rows;// number of result rows
    
  private $insert_id;// the id of the last insert instruction
    
  private $affected_rows;// affected rows number by query instructions  
                             // INSERT UPDATE or DELETE             
  
  
  /*
   * Construction function
   * 
   * @param: if no arguments - connect database as configured user
   * 		 or connect database as defined user
   */
  public function Database($user='', $pwd=''){
  	$this->db = DB_DBNAME;
  	$this->mHost = DB_HOST;
  	$this->mUser = DB_USER;
  	$this->mPwd = DB_PWD;
  	if(strlen($user) > 0 && strlen($pwd) > 0){
	  	$this->mUser = $user;
	  	$this->mPwd = $pwd;
  	}
  }
  
  /*
   * connect mysql
   */               
  public function connect(){  
  	
    if($this->pConnect){  
    	$this->mConn=mysql_pconnect($this->mHost,$this->mUser,$this->mPwd) or die(mysql_error());
    }
    else{  
    	$this->mConn=mysql_connect($this->mHost,$this->mUser,$this->mPwd) or die(mysql_error());//short connect
    }
  
    if(!$this->mConn){
    	$this->dbhalt();
    }
    
    if($this->db==""){
    	$this->dbhalt("No database is selected!");
    }
    
    if(!mysql_select_db($this->db,$this->mConn)){
    	//echo mysql_error();
    	$this->dbhalt("Wrong database!");
    }
    $this->execute("set names UTF8");
  } 
  
  /*
   * close database connection
   */
  public function dbclose(){  
    mysql_close($this->mConn);  
  }  
  
  /*
   * change database
   */ 
  public function dbChange($newDb){  
    $this->db = $newDb;  
    $this->connect();  
  }  
  
  /*
   * execute sql and return source id
   */  
  public function execute($sql){ 
  	
  	try{ 
    	$this->result = mysql_query($sql);  
    	
    	if(!is_null(mysql_error()) && strlen(mysql_error()) > 0){
    		return false;
    	}
    	
  	}catch(Exception $e){
  		$this->dbhalt();
  	}
    return true;  
  }  
  
  /*
   * check sql format
   */
  private function chkSql($sql){
  	if(strrpos($sql, ";", 0) == strlen($sql) - 1){
  		return true;
  	}
  	else{
  		return false;
  	}
  }
  
  /*
   * define database error message
   */
  private function dbhalt($errmsg = ''){  
    //echo mysql_error();
  	$msg = mysql_error();
    if($errmsg != null){  
    	if($errmsg != ''){
    		$msg=$errmsg;  
    	}
    }
    
    $this->dbclose();
    //echo $msg;  
    die();  
  }  
  
  /*
   * get result as an indexed and associated array
   */  
  public function fetchArray($resultType=MYSQL_BOTH){  
  	$all = array();
	while (($tmp[] = mysql_fetch_array($this->result)) == true) {$all = $tmp;}
	return $all;
  }  
      
  /*
   * get result as an associate array
   */
  public function fetchAssoc(){  
    $all = array();
	while (($tmp[] = mysql_fetch_assoc($this->result)) == true) {$all = $tmp;}
	return $all;  
  }      
      
  /*
   * get result as an index array
   */  
  public function fetchIndexArray(){  
    $all = array();
	while (($tmp[] = mysql_fetch_row($this->result)) == true) {$all = $tmp;}
	return $all; 
  }  
      
  /*
   * get result as an object array
   */  
  public function fetchObject(){  
    $all = array();
	while (($tmp[] = mysql_fetch_object($this->result)) == true) {$all = $tmp;}
	return $all; 
  }          
      
  /*
   * return number of result rows
   */  
  public function numRows(){  
    if($this->result == null){
    	return 0;
    }
  	return mysql_num_rows($this->result);  
  }  
  
  /*
   * return all database name in local host
   */  
  public function dbNames(){  
    $rsPtr=mysql_list_dbs($this->mConn);  
    $i=0;  
    $cnt=mysql_num_rows($rsPtr);  
    while($i<$cnt)  
    {  
      $rs[]=mysql_db_name($rsPtr,$i);  
      $i++;  
    }  
    return $rs;  
  }  
  
  /*
   * select
   */
  public function select($sql){
  	if(!$this->chkSql($sql)){
  		$sql = $sql.";";
  	}
  	$this->connect();
  	$execConfirm = $this->execute($sql);
  	$this->dbclose();
  	
  	if(!$execConfirm){
  		return -1;
  	}
  	
  	return $this->result;
  }
  
  /*
   * delete
   */
  public function delete($sql){  
  	$execConfirm = 0;
  	
    if(!$this->chkSql($sql)){
  		$sql = $sql.";";
  	}
	try{    
		$this->connect();
  		$execConfirm = $this->execute($sql);  
	    $this->affected_rows=mysql_affected_rows($this->mConn);  
	    //$this->free_result($result);
	    $this->dbclose();  
	    
  	}catch(Exception $e){
  		$this->dbhalt();
  	}  
  	
  	if(!$execConfirm){
 		return -1;
  	}
    
    return $this->affected_rows;
  }  
  
  /*
   * insert
   */  
  public function insert($sql){
  	$execConfirm = 0;
  	
    if(!$this->chkSql($sql)){
  		$sql = $sql.";";
  	}  
  	try{
  		$this->connect();
	    $execConfirm = $this->execute($sql);  
	    $this->insert_id=mysql_insert_id($this->mConn);  
	    $this->dbclose();
	      
  	}catch(Exception $e){
  		$this->dbhalt();
  	}
  	
  	if(!$execConfirm){
  		return -1;
  	}
  	
  	return $this->insert_id;
  }  
  
  /*
   * update
   */ 
  public function update($sql){
  	$execConfirm = 0;
  	
  	if(!$this->chkSql($sql)){
  		$sql = $sql.";";
  	}  
	try{    
		$this->connect();
	    $execConfirm = $this->execute($sql);  
	    $this->affected_rows=mysql_affected_rows($this->mConn);  
	    //$this->free_result($result);
	    $this->dbclose();
		  
  	}catch(Exception $e){
  		$this->dbhalt();
  	}
  	
  	if(!$execConfirm){
  		return -1;
  	}
  	return $this->affected_rows;
  	
  }  
}// end class  
?>
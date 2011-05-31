<?php  
/** 
*Database module
*@author Yaxing Chen
*@date 05/31/2011
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
  	$database = require('Dbconfig.php');
  	$this->db = $database['dbName'];
  	$this->mHost = $database['host'];
  	$this->mUser = $database['user'];
  	$this->mPwd = $database['pwd'];
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
    	$this->mConn=mysql_pconnect($this->mHost,$this->mUser,$this->mPwd);
    }
    else{  
    	$this->mConn=mysql_connect($this->mHost,$this->mUser,$this->mPwd);//short connect
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
    $this->db=$newDb;  
    $this->connect();  
  }  
  
  /*
   * execute sql and return source id
   */  
  public function execute($sql){ 
  	try{ 
    	$this->result=mysql_query($sql);  
  	}catch(Exception $e){
  		$this->dbhalt();
  	}
    return;  
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
    $msg=mysql_error();
    if($errmsg != null){  
    	if($errmsg != ''){
    		$msg=$errmsg;  
    	}
    }
    echo $msg;  
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
  	$this->execute($sql);
  	$this->dbclose();
  	return $this->result;
  }
  
  /*
   * delete
   */
  public function delete($sql){  
    if(!$this->chkSql($sql)){
  		$sql = $sql.";";
  	}
	try{    
		$this->connect();
  		$this->result=$this->execute($sql);  
	    $this->affected_rows=mysql_affected_rows($this->mConn);  
	    //$this->free_result($result);
	    $this->dbclose();  
	    return $this->affected_rows;
  	}catch(Exception $e){
  		$this->dbhalt();
  	}  
  }  
  
  /*
   * insert
   */  
  public function insert($sql){
    if(!$this->chkSql($sql)){
  		$sql = $sql.";";
  	}  
  	try{
  		$this->connect();
	    $this->result=$this->execute($sql);  
	    $this->insert_id=mysql_insert_id($this->mConn);  
	    $this->dbclose();
	    return $this->insert_id;  
  	}catch(Exception $e){
  		$this->dbhalt();
  	}
  }  
  
  /*
   * update
   */ 
  public function update($sql){
  	if(!$this->chkSql($sql)){
  		$sql = $sql.";";
  	}  
	try{    
		$this->connect();
	    $this->result=$this->execute($sql);  
	    $this->affected_rows=mysql_affected_rows($this->mConn);  
	    //$this->free_result($result);
	    $this->dbclose();
		return $this->affected_rows;  
  	}catch(Exception $e){
  		$this->dbhalt();
  	}
  }  
}// end class  
?>
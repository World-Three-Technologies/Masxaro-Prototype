<?php
require_once dirname(__FILE__)."/../../config.php";
require_once 'database.class.php';
  
class DatabaseTest extends PHPUnit_Framework_TestCase{

  public function test_database_can_connect(){
    $db = new Database();
    $db->connect();
    $this->assertTrue($db->execute("set names UTF8"));
    $db->dbclose();
  }
}
?>

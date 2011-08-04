<?php

require_once dirname(__FILE__).'/../../config.php';
require_once "ctrl.class.php";
require_once "receiptCtrl.class.php";
  
class ReceiptCtrlTest extends PHPUnit_Framework_TestCase{

  public function test_it_can_return_users_receipt(){

    $control = new ReceiptCtrl();

    $receipts = $control->userGetAllReceipt("w3t");   

    $this->assertEquals("Arraystore_name",$receipts[0].store_name);

  }
}

?>

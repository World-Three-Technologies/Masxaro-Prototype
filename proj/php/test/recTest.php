<?php

class RecTest extends TestStory{

  public function beforeRun(){
    $this->code  ="983094867189238-0929347";

    $this->basicInfo = array('receipt_id'=>$code, 
               'store_id'=>1, 
               'user_account'=>'test', 
               'tax'=>0.1, 
               'total_cost'=>0);

    //item list
    $this->items = array();

    //item 1
    array_push($items, array('receipt_id'=>$code, 
           'item_id'=>1, 
           'item_name'=>'test', 
           'item_qty'=>3, 
           'item_price'=>10, 
           'item_discount'=>1));

    //item 2
    array_push($items, array('receipt_id'=>$code, 
           'item_id'=>2, 
           'item_name'=>'test2', 
           'item_qty'=>1, 
           'item_price'=>20, 
           'item_discount'=>1));
  }

  public function integrate_db_can_access(){
    $ctrl = new ReceiptCtrl();
    should_be_true(false);
  }

  public function integrate_can_get_receipt_data_and_items_by_id(){
    should_be_true(true);
  }

  public function integrate_can_get_receipt_data_and_items_by_users(){
    should_be_equal(1,1);
  }
}

?>

<?php

class RecTest extends TestStory{

  public function beforeRun(){
    $this->code  ="983094867189238-0929347";
  }

  public function integrate_can_get_all_receipt_by_account(){
    $control = new ReceiptCtrl();
    $receipts = $control->userGetAllReceipt("w3t");
    echo json_encode($receipts);
    should_be_equal($receipts[0]->user_account,"w3t");
  }

  public function integrate_can_get_receipt_data_and_items_by_users(){
    $control = new ReceiptCtrl();
    $items = $control->userGetAllReceiptItems($this->code);
    should_be_equal(count($items),2);
  }

  public function integrate_can_connect_to_database(){
    $db = new Database();
    $sql = "insert into `age_range` values(null, '99-100')";
    should_be_true($db->insert($sql));
  }
}

?>

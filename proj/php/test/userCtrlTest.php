<?php

class userCtrlTest extends TestStory{

  public function integrate_can_find_username(){
    $userControl = new UserCtrl();
    should_be_true($userControl->findUser("w3t","w3t"));
  }

}

?>

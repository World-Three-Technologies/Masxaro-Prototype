<?php
include '../../config.php';

//echo phpinfo();
//die();

$ctrl = new EmailCtrl();
//$tmp = $ctrl->retrieveAllUser();
//$tmp = $ctrl->retrieveUser('bws');
$tmp = $ctrl->grabEmails('w3tAcc');
print_r($tmp);
?>
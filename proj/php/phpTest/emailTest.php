<?php
include '../../config.php';

$ctrl = new EmailCtrl();
//$tmp = $ctrl->grabEmails('w3tAcc');
$tmp = $ctrl->retrieveAllUser();
//$tmp = $ctrl->retrieveUser('w3tAcc');
print_r($tmp);
?>
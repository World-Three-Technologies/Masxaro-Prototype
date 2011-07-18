<?php
include '../../config.php';
echo 1;
die();
$ctrl = new EmailCtrl();
//$tmp = $ctrl->grabEmails('w3tAcc');
$tmp = $ctrl->retrieveUser('w3tAcc');
print_r($tmp);
?>
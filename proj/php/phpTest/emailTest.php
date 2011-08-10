<?php
include '../../config.php';
echo Tool::getEmailPwd('Jimmy');
die();

$ctrl = new EmailCtrl();
$tmp = $ctrl->grabEmails('w3tAcc');
//$tmp = $ctrl->retrieveAllUser();
//$tmp = $ctrl->retrieveUser('w3tAcc');
print_r($tmp);
?>
<?php
include '../../config.php';
//echo Tool::getEmailPwd('Jimmy');
//die();

$ctrl = new EmailCtrl();
$tmp = $ctrl->grabEmails('yaxingc');
//$tmp = $ctrl->retrieveAllUser();
//$tmp = $ctrl->retrieveUser('w3tAcc');
print_r($tmp);
?>
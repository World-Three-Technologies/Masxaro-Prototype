<?php
include_once '../../config.php';
$dbCtrl = new Database();
$dbCtrl->select('select * from user');
print_r($dbCtrl->fetchAssoc());
?>
<?php
include_once '../../config.php';
$test = new ReceiptBuilder();
print_r($test->build(array(1, 2, 3, 11)));
?>
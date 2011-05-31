<?php
/**
 * database test file
 * @author Yaxing Chen
 * @date 05/31/2011
 */

include_once '../config.php';
$db = new Database();
$sql = "insert into `age_range` values(null, '25-30')";
echo $db->insert($sql);
?>
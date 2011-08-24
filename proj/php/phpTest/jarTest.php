<?php
include_once '../../config.php';
var_dump(exec('java -jar '.ROOT_PATH.'/parser.jar yaxingc '.ROOT_PATH.'/masxaro_email_tmp '.ROOT_PATH.'/masxaro_email_tmp'));
?>
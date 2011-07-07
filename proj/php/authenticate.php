<?php

include_once '../config.php';
include_once 'header.php';

$acc = $post['acc'];//can be null
//$acc = 'new';

echo Tool::authenticate($acc);

?>


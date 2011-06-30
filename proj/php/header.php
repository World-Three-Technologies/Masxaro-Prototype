<?php
include_once '../config.php';

$post = null;

if(isset($_POST['json'])){
	$post = str_replace("\\", "", $_POST['json']);
	$post = json_decode($post, true);
	$post = $post['json'];
}
else{
	$post = $_REQUEST;
}

?>
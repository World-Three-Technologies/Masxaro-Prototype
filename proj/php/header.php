<?php
include_once '../config.php';

$jsonPost = null;

if(isset($_POST['json'])){
	$jsonPost = str_replace("\\", "", $_POST['json']);
	$jsonPost = json_decode($jsonPost, true);
	$jsonPost = $jsonPost['json'];
}

?>
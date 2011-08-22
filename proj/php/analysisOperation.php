<?php 

include_once '../config.php';
include_once 'header.php';

$acc = $_COOKIE['user_acc'];
$method = $_SERVER['REQUEST_METHOD'];

if(!Tool::authenticate($acc)){
	die("error: need login!");
}

$control = new AnalysisCtrl();

Tool::setJSON();

switch($method){
  //return analysis data with specific month
  case "GET":
    $type = $_REQUEST["opcode"];
    if($type=="tag"){
      $date = $_REQUEST["date"]; 
      echo json_encode($control->getTagData($acc,$date));
    }else if($type == "store"){
      $date = $_REQUEST["date"]; 
      echo json_encode($control->getStoreData($acc,$date));
    }
    break;
}
?>

<?php

include_once '../../config.php';
//ob_start();
//setcookie('user_acc', 'new', time() + 24 * 60 * 60, '/');
//var_dump($_COOKIE['user_acc']);
//ob_end_flush();
//die();

$path = "http://50.19.213.157/masxaro/proj/php/login.php";
$param = "acc=new&pwd=123&type=user";

$ch = curl_init($path);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $param);

$result = curl_exec($ch);

var_dump($result);

function authenticate($acc){
	    if(empty($acc) || strlen($acc) == 0){
	      return false;
	    }

		if(isset($_COOKIE['user_acc']) && $_COOKIE['user_acc'] == $acc){
				return true;
		}
		else if(isset($_COOKIE['store_acc']) && $_COOKIE['store_acc'] == $acc){
				return true;
		}

		return false;
	}

	function login($acc, $pwd, $type){
		switch($type){
			case 'user':
				return setcookie('user_acc', $acc, time() + 24 * 60 * 60, '/'); //1 day
				break;
			case 'store':
				return setcookie('store_acc', $acc, time() + 24 * 60 * 60, '/'); //1 day
				break;
		}
	}

?>
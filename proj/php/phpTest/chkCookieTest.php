<?php
$acc = 'new';
if(empty($acc) || strlen($acc) == 0){
	      echo false;
	    }

		if(isset($_COOKIE['user_acc']) && $_COOKIE['user_acc'] == $acc){
				echo true;
		}
		else if(isset($_COOKIE['store_acc']) && $_COOKIE['store_acc'] == $acc){
				echo true;
		}

		echo false;
?>
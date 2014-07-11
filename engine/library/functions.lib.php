<?php

	function tax($price, $tax){
		return (($tax / 100 * $price) + $price);
	}
	
	function hash_pass($pass){
		return hash($GLOBALS['algorithm'], $GLOBALS['salt'] . $pass . $GLOBALS['salt']);
	}
	
	function nice_number($number = 0, $length = 7){
		$pn_lengt = $length;
		$pn_curl = strlen($number);
		$pn_add = $pn_lengt - $pn_curl;
		return str_repeat(0, $pn_add) . $number;
	}
	
	function convert_nice_number($number = 0){
		return (int)preg_replace('/[^0-9]/','',$number);
	}
	
	function money($money){
		if(strlen(substr(strrchr(floatval($money), "."), 1)) == 0){
			$money = number_format($money, 0, ',', '.') . ',-';
		}
		else{
			$money = number_format($money, 2, ',', '.');
		}
		return $money;
	}

?>
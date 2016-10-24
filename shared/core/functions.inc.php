<?php

/* Common functions */

function redirectRequest($path){
	global $_SERVER;
	header('Location:http://' . $_SERVER['HTTP_HOST'] . $path);
	exit;
}

function sqlDateTimeToDisplayDate($datetime){
	$datetime_arr = explode(' ' , $datetime);
	$date_arr = explode('-' , $datetime_arr[0]);
	return $date_arr[2] . '/' . $date_arr[1] . '/' . $date_arr[0];
}

function sqlDateTimeToDisplayDateTime($datetime){
	$datetime_arr = explode(' ' , $datetime);
	$date_arr = explode('-' , $datetime_arr[0]);
	return $date_arr[2] . '/' . $date_arr[1] . '/' . $date_arr[0] . ' ' . $datetime_arr[1];
}

function number_format_app($number, $decimals=-1){
	$num = str_replace(',','',$number);
	//$num = str_replace('.','.',$num);
	$num = doubleval($num);     
    if($decimals > -1){
		return number_format( $num, $decimals,  '.','');
	}else{
		return $num;
	}
}


?>
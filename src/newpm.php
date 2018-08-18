<?php

include('connect.php');
include('functions.php');

secureINI();
session_start();
secureSession();

date_default_timezone_set('America/New_York');
$time = date('Y-m-d H:i:s', time());

$message = filter($_POST['text']);
$user = $_SESSION['Name'];
$ID = fetch_id($user);
$reciever = $_SESSION['ToUser'];

if(!isset($reciever))
{
	die();
}

if(!isBanned($user))
{
	set_login_time($user);
}

if($message != '' && strlen($message) < 225 && isset($user) && !isBanned($user)) 
{
	mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', '$message', '0', '1', '$reciever');");
	log_aop();
}
		
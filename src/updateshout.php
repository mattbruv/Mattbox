<?php

include('connect.php');
include('functions.php');

secureINI();
session_start();
secureSession();

$rawname = $_SESSION['Name'];

if(isset($_POST['shoutid']) && isset($_POST['del']))
{
	$delete = $_POST['del'];
	$shoutID = (int)$_POST['shoutid'];
	
	if(isset($_POST['message']))
	{
		$newShout = filter($_POST['message']);
	}
	
	if ($delete)
	{
		mysql_query("DELETE FROM `shouts` WHERE `ShoutID` = '$shoutID'");
	}
	else
	{
		mysql_query("UPDATE `shouts` SET `Shout` = '$newShout' WHERE `ShoutID` = '$shoutID'");
	}
	
	log_aop();
}

?>

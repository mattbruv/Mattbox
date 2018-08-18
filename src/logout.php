<?php

include('connect.php');
include('functions.php');

secureINI();
session_start();
secureSession();

$user = $_SESSION['Name'];

if(isBanned($user) || isTempBanned($user))
{
	header('location: index.php');
	exit;
}

else 
{
	if($_COOKIE['Remember'])
	{
		setcookie("Remember", 1, time() - (20 * 365 * 24 * 60 * 60));
		setcookie("Name", $username, time() - (20 * 365 * 24 * 60 * 60));
	}
	else
	{
		setcookie("Remember", 0, time() - 3600);
		setcookie("Name", $username, time() - 3600);
	}
	session_unset();
	session_destroy();
	header('location: index.php');
	exit;
}

?>

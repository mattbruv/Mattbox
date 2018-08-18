<?php

include('../connect.php');
include('../functions.php');

secureINI();
session_start();
secureSession();

if (!isset($_SESSION['Name']))
{
	die('You are not logged in. Please click below<br /><a href="../login.php">Log In</a>');
}

if(!can_do_admin($_SESSION['Name']) || isBanned($_SESSION['Name']))
{
	die('Insufficient Priveledges');
}

?>
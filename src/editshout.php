<?php

include('connect.php');
include('functions.php');

secureINI();
session_start();
secureSession();

$rawname = $_SESSION['Name'];
$shoutid = (int)$_POST['shoutID'];

$query = mysql_query("SELECT * FROM shouts WHERE ShoutID = '$shoutid'");
$r = mysql_fetch_assoc($query);

$shout = stripslashes($r['Shout']);
$uid = $r['User'];

$query = mysql_query("SELECT Name FROM user WHERE UserID = '$uid'");
$result = mysql_result($query, 0);

if (can_do_admin($rawname) || can_do_mod($rawname))
{
	$array = array("shout" => $shout, "id" => $shoutid);
	echo json_encode($array);
}
else if ($rawname == $result)
{
	$array = array("shout" => $shout, "id" => $shoutid);
	echo json_encode($array);
}
?>
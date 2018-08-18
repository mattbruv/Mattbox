<?php

include('connect.php');
include('functions.php');

secureINI();
session_start();
secureSession();

$query = mysql_query("SELECT * FROM smilies");
$smilearray = array();

while($r = mysql_fetch_assoc($query))
{
	$smilearray[] = array('SmileyID' => $r['SmileyID'], 'Name' => $r['Name'], 'URL' => $r['URL'], 'Code' => $r['Code'], 'Uploaded' => $r['Uploaded']);
}

shuffle($smilearray);

$newArray = array();

foreach ($smilearray as $s)
{
	$URL = $s['URL'];
	$name = $s['Name'];
	$code = $s['Code'];
	$newArray[] = '<a href="#" onclick="appendSmiley(\'' . $code . '\')" ><img src="' . $URL . '" alt="' . $name . '" title="' . $name . '"/></a>';
}

echo implode(' ', $newArray);

?>

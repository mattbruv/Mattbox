<?php

include('connect.php');
include('functions.php');

secureINI();
session_start();
secureSession();

$user = $_SESSION['Name'];
$ID = fetch_id($user);

$bold = $_POST['bold'];
$italic = $_POST['italic'];
$underline = $_POST['underline'];
$color = lcfirst($_POST['color']);
$font = $_POST['font'];

$yesno = array('1', '0');

$dbcolors = array(
	'default',
	'red',
	'crimson',
	'blue',
	'green',
	'orange',
	'brown',
	'black',
	'purple'
);

$fontarray = array(
	'Default',
	'Arial',
	'Arial Narrow',
	'Book Antiqua',
	'Century Gothic',
	'Comic Sans MS',
	'Courier New',
	'Fixedsys',
	'Franklin Gothic Medium',
	'Garamond',
	'Georgia',
	'Lucida Console',
	'Microsoft Sans Serif',
	'Palatino Linotype',
	'System',
	'Tahoma',
	'Times New Roman',
	'Trebuchet',
	'Verdana'
);

if(isset($bold) && in_array($bold, $yesno)) {
	mysql_query("UPDATE `user` SET `Bold`='$bold' WHERE `Name`='$user'");
}

if(isset($underline) && in_array($underline, $yesno)) {
	mysql_query("UPDATE `user` SET `Underline`='$underline' WHERE `Name`='$user'");
}

if(isset($italic) && in_array($italic, $yesno)) {
	mysql_query("UPDATE `user` SET `Italic`='$italic' WHERE `Name`='$user'");
}

if(isset($color) && in_array($color, $dbcolors)) {
	mysql_query("UPDATE `user` SET `Color`='$color' WHERE `Name`='$user'");	
}

if(isset($font) && in_array($font, $fontarray)) {
	$font = ($font == "Default") ? "Tahoma" : $font;
	mysql_query("UPDATE `user` SET `Font`='$font' WHERE `Name`='$user'");
}

?>
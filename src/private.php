<?php

include('connect.php');
include('functions.php');

secureINI();
session_start();
secureSession();
$settings = get_mattbox_settings();
$notice = BBcode(smileMe(stripslashes($settings['notice'])));

if(isset($_POST['username']))
{
	$ar = $_POST['username'];
	$_SESSION['ToUser'] = $ar;
}

if(isset($_SESSION['ToUser']))
{
	$recipient = $_SESSION['ToUser'];
}
else
{
	die();
}

$username = $_SESSION['Name'];

$query = mysql_query("SELECT shouts.ShoutID, user.Name, user.Silenced, shouts.Time, shouts.Shout, shouts.To FROM `shouts` INNER JOIN `user` ON `shouts`.`User` = `user`.`UserID` WHERE `shouts`.`Private`=1 AND (`user`.`Name` = '$username' AND `To` = '$recipient') OR (`user`.`Name` = '$recipient' AND `To` = '$username') ORDER BY shouts.ShoutID DESC LIMIT 20");


$shouts = array();

if (!isBanned($username))
{
	if(strlen(trim($notice)) != 0)
	{
		echo('<div id="notice">Notice : ' . $notice . '</div>');
	}
	
	echo '<div id="shoutlist">';
	
	while ($r = mysql_fetch_assoc($query))
	{
		$shouts[] = array('ShoutID' => $r['ShoutID'], 'Name' => $r['Name'], 'Silenced' => $r['Silenced'], 'Time' => $r['Time'], 'Shout' => $r['Shout'], 'To' => $r['To']);
	}
	
	foreach ($shouts as $s)
	{
		$rawto = $s['To'];
		$ID = $s['ShoutID'];
		$rawname = ($s['Name']);
		$to = style_user($rawto);
		$name = make_clickable(style_user($rawname));
		$shout = stripslashes(markup(smileMe(URLMe($s['Shout'])), $rawname));
		$time = convertTime($s['Time'], $username);
		$silenced = $s['Silenced'];
		$span1 = '<span id="' . $ID . '" ondblclick="editShout(this)">';
		$span2 = '</span>';
		
		if(!$silenced)
		{
			echo $span1 . '[' .  $time . '] <b>[PM]</b> ' . $name . ' : ' . $shout . $span2 . '<br />';
		}
		
	}
}
?>

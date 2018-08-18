<?php

include('connect.php');
include('functions.php');

secureINI();
session_start();
secureSession();
set_login_time(filter($_SESSION['Name']));
unban_temp_banned();

$cookieName = (isset($_COOKIE['Name'])) ? $_COOKIE['Name'] : '';
$rememberMe = (isset($_COOKIE['Remember'])) ? $_COOKIE['Remember'] : '';

$query = mysql_query("SELECT * FROM user WHERE Name = '$cookieName'");
$nameExists = mysql_num_rows($query);
$approved = mysql_fetch_assoc($query);
$check = $approved['Approved'];
$activeUsers = fetch_active_user_number();
$settings = get_mattbox_settings();
$notice = BBcode(smileMe(stripslashes($settings['notice'])));
$happyhour = $settings['happyhour'];
$mattbot = (int)$settings['mattbot'];

if ($mattbot) {	mattbot(); }

if($nameExists == 0)
{
	die('<span style="background-color: #fbef8d;"><b>Mattbox Notice:</b></span> Your name has changed, or you have been signed out. Please <a href="logout.php">Log Out</a>.');
}
if(!isset($_SESSION['Name'])) 
{
	die();
}
if(!$check)
{
	die('Your account has not been approved. An admin will look into it soon.');
}

$username = $_SESSION['Name'];
$rawid = fetch_id($username);
$usernameStyled = make_clickable(style_user($username));

$query = mysql_query("SELECT shouts.ShoutID, shouts.User, user.Name, user.Silenced, shouts.Time, shouts.Shout, shouts.Me, shouts.Private, shouts.To FROM `shouts` INNER JOIN `user` ON `shouts`.`User` = `user`.`UserID` WHERE user.Silenced = 0  ORDER BY shouts.ShoutID DESC LIMIT 20");
$shouts = array();

if (!isBanned($username) && !isTempBanned($username))
{
	while ($r = mysql_fetch_assoc($query))
	{
		$shouts[] = array('ShoutID' => $r['ShoutID'], 'User' => $r['User'], 'Name' => $r['Name'], 'Silenced' => $r['Silenced'], 'Time' => $r['Time'], 'Shout' => $r['Shout'], 'Me' => $r['Me'], 'Private' => $r['Private'], 'To' => $r['To']);
	}
	
	if (empty($shouts))
	{
		die('<i>Nothing to display...</i>');
	}

	if(strlen(trim($notice)) != 0)
	{
		echo('<div id="notice">Notice : ' . $notice . '</div>');
	}
	
	echo '<div id="shoutlist">';
	
	foreach ($shouts as $s)
	{
		$time = (!$happyhour) ? '[' . convertTime($s['Time'], $username) . ']' : "<span style='color:white; font-weight:bold; background-image:url(\"images/rain1.gif\");'>HAPPY HOUR</span>";
		$name = make_clickable(style_user($s['Name']));
		$mename = style_user($s['Name']);
		$rawname = $s['Name'];
		$rawmsg = $s['Shout'];
		$ID = $s['ShoutID'];
		$message = markup(smileMe(URLMe(stripslashes($s['Shout']))), $s['Name']);
		if(can_do_admin($rawname))
		{
			$message = BBCode($message);
		}
		$span1 = '<span class="shoutHeight" id="' . $ID . '" ondblclick="editShout(this)">';
		$span2 = '</span>';
		$silenced = $s['Silenced'];
		$private = $s['Private'];
		$pm_reciever = $s['To'];
		$me = $s['Me'];
		
		if (!$private)
		{
			if (!$me)
			{
				echo $span1 . $time . ' ' . $name . ' : ' . $message . $span2;
				echo '<br />';
			}
			else
			{
				echo $span1 . '*' . $mename . ' ' .   $message . '*' . $span2;
				echo '<br />';
			}
		}
		else
		{
			if ($username == $rawname || $username == $pm_reciever)
			{
				echo $span1 . $time . ' <b>[PM]</b> ' . $name . ' : ' . $message . $span2;
				echo '<br />';
			}
			else if (can_do_admin($username))
			{
				$string = '[' . $rawname . ' -> ' . $pm_reciever . '] ';
				$string = $string . $rawmsg;
				echo $time . ' <b>[PM]</b> ' . $usernameStyled . ' : ' . markup(smileMe(URLMe($string)), $username);
				echo '<br />';
			}
		}
	}
	echo '</div>';
}

else if (isBanned($username))
{
	echo '<span style="background-color: #fbef8d;"><b>Mattbox Notice:</b></span> You are banned from the Mattbox.';
}

else
{
	$time = formatTempBannedTime(getTempBannedTime($username) - time());
	echo '<span style="background-color: #fbef8d;"><b>Mattbox Notice:</b></span> You have been temporarily banned. You will be auto-unbanned in ' . $time . '.';
}
?>
<script type="text/javascript">
updateActiveUsers(<?php echo $activeUsers; ?>);
</script>
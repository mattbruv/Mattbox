<?php

include('connect.php');
include('global.php');
define("TIMENOW", time());

// #######################################
// FETCH TOP SHOUTERS
// #######################################

function get_statistic_top_shouters()
{
	$array = array();
	$q = mysql_query("SELECT user.Name, Count(*) AS 'TotalShouts' FROM shouts INNER JOIN user ON user.UserID = shouts.User GROUP BY (shouts.User) ORDER BY TotalShouts DESC LIMIT 5");


	while($r = mysql_fetch_assoc($q))
	{
			$array[] = array(style_user($r['Name']), $r['TotalShouts']);
	}
	return $array;
}

// #######################################
// GET SHOUT NUMBER
// #######################################

function get_statistic_shouts()
{
	$query = mysql_query("SELECT count(*) AS 'Total' FROM shouts");
	$var = mysql_result($query, 0);
	return $var;
}

// #######################################
// GET USER NUMBER
// #######################################

function get_statistic_users()
{
	$query = mysql_query("SELECT count(*) AS 'Total' FROM `user`");
	$var = mysql_result($query, 0);
	return $var;
}

// #######################################
// GET ALL ADMIN NAMES AS ARRAY
// #######################################

function get_admins()
{
	$query = mysql_query("SELECT `UserID` FROM user WHERE `UsergroupID`=1");
	$array = array();
	while ($r = mysql_fetch_assoc($query))
	{
		$array[] = $r['UserID'];
	}
	return $array;
}

// #######################################
// GET ALL USERNAMES AS ARRAY
// #######################################

function get_users()
{
	$query = mysql_query("SELECT `UserID` FROM user");
	$array = array();
	while ($r = mysql_fetch_assoc($query))
	{
		$array[] = $r['UserID'];
	}
	return $array;
}

// #######################################
// GET SETTINGS
// #######################################

function get_mattbox_settings()
{
	$query = mysql_query("SELECT * FROM settings WHERE ID='1'");
	$array = mysql_fetch_assoc($query);
	return $array;
}

// #######################################
// LOAD STYLES
// #######################################

function import_styles($username)
{
	$query = mysql_query("SELECT Bold, Italic, Underline, Color, Font FROM user WHERE Name='$username'");
	$array = mysql_fetch_assoc($query);
	return $array;
}

// #######################################
// FETCH TEMP BANNED USERS
// #######################################

function fetch_temp_banned()
{
	$query = mysql_query("SELECT TempBanned, Name FROM user WHERE TempBanned != '0'");
	$array = array();
	while($r = mysql_fetch_assoc($query))
	{
		$array[] = array('Name' => $r['Name'], 'Time' => $r['TempBanned']);
	}
	return $array;
}

// #######################################
// IS TEMPORARILY BANNED?
// #######################################

function isTempBanned($user)
{
	$query = mysql_query("SELECT TempBanned FROM user WHERE Name='$user'");
	$result = mysql_result($query, 0);

	if($result != 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function formatTempBannedTime($seconds)
{
	// $seconds = ceil((getTempBannedTime($username) - time()) / 60);
	$days = round(((($seconds / 60) / 60) / 24), 2);
	if ($days < 1)
	{
		$hours = round((($seconds / 60) / 60), 2);
		if ($hours < 1)
		{
			$minutes = round(($seconds / 60), 0);
			if ($minutes < 1)
			{
				return $seconds . " seconds";
			}
			else {
				return $minutes . " minutes";
			}
		}
		else {
			return $hours . " hours";
		}
	} else {
		return $days . " days";
	}
}

// #######################################
// GET TEMP BAN SECONDS LEFT
// #######################################

function getTempBannedTime($user)
{
	$query = mysql_query("SELECT TempBanned FROM user WHERE Name='$user'");
	$result = mysql_result($query, 0);

	return $result;
}

// #######################################
// UNBAN TEMP BANNED USERS
// #######################################

function unban_temp_banned()
{
	date_default_timezone_set('America/New_York');
	$time = date('Y-m-d H:i:s', time());
	$query = mysql_query("SELECT TempBanned, Name FROM user WHERE TempBanned != '0'");
	$array = array();

	while($r = mysql_fetch_assoc($query))
	{
		$array[] = array('Name' => $r['Name'], 'Time' => $r['TempBanned']);
	}

	foreach ($array as $a)
	{
		if ($a['Time'] < time())
		{
			$user = $a['Name'];
			mysql_query("UPDATE `user` SET `TempBanned`='0' WHERE `Name`='$user'");
			mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '1', '$time', '[AUTO] $user unbanned', '1', '0', '')");
		}
	}
}

// #######################################
// MATTBOT BRAIN
// #######################################

function mattbot()
{
	date_default_timezone_set('America/New_York');
	$time = date('Y-m-d H:i:s', time());

	$rand = rand(1, 250);

	if($rand == 37)
	{
		$query = mysql_query("SELECT COUNT(ShoutID) AS NumShouts FROM shouts");
		$limit = mysql_result($query, 0);
		$randShout = rand(1, $limit);
		if($randShout >= 50)
		{
			$query = mysql_query("SELECT Shout FROM shouts WHERE ShoutID='$randShout'");
			$shout = mysql_result($query, 0);
			mysql_query("INSERT INTO `shouts`(`ShoutID`, `User`, `Time`, `Shout`, `Me`, `Private`, `To`) VALUES ('', '107', '$time', '$shout', '0', '0', '')");
		}
	}
}

// #######################################
// FETCH APPROVED OR NOT
// #######################################

function isApproved($string)
{
	$query = mysql_query("SELECT Approved FROM user WHERE Name='$string'");
	$count = mysql_fetch_assoc($query);
	$result = $count['Approved'];
	return $result;
}

// #######################################
// LOG ACTIONS
// #######################################

function log_aop()
{
	$time = time();
	$myFile = "aop.php";
	$fh = fopen($myFile, 'w');
	fwrite($fh, $time);
	fclose($fh);
}

// #######################################
// LOG ACTIONS
// #######################################

function log_event($string)
{
	$addr = '[' . $_SERVER['REMOTE_ADDR'] . ']';
	date_default_timezone_set('America/New_York');
	$time = date('[m-d-Y h:i A]', time());
	$myFile = "log.txt";
	$fh = fopen($myFile, 'a');
	$string = $addr . $time . " " . $string . PHP_EOL;
	fwrite($fh, $string);
	fclose($fh);
}

// #######################################
// CHECK IF SMILEY EXISTS
// #######################################

function group_id_exists($ID)
{
	$query = mysql_query("SELECT * FROM usergroup WHERE UsergroupID = '$ID'");
	$rows = mysql_num_rows($query);

	if($rows == 1)
	{
		return true;
	}
	else
	{
		return false;
	}
}

// #######################################
// CHECK IF SMILEY EXISTS
// #######################################

function smiley_id_exists($ID)
{
	$query = mysql_query("SELECT * FROM smilies WHERE SmileyID = '$ID'");
	$rows = mysql_num_rows($query);

	if($rows == 1)
	{
		return true;
	}
	else
	{
		return false;
	}
}

// #######################################
// CHECK IF ID EXISTS
// #######################################

function id_exists($ID)
{
	$query = mysql_query("SELECT * FROM user WHERE UserID = '$ID'");
	$rows = mysql_num_rows($query);

	if($rows == 1)
	{
		return true;
	}
	else
	{
		return false;
	}
}

// #######################################
// VALIDATE USERNAME
// #######################################

function check_username($username)
{
	$bool = false;
	if(strlen($username) > 3 && strlen($username) < 26)
	{
		$bool = true;
	}
	else
	{
		$bool = false;
	}
	return $bool;
}

// #######################################
// VALIDATE PASSWORD
// #######################################

function check_password($password)
{
	$bool = false;
	if(strlen($password) >= 4 && strlen($password) < 26)
	{
		$bool = true;
	}
	else
	{
		$bool = false;
	}
	return $bool;
}

// #######################################
// GET USERGROUP NAME
// #######################################

function get_usergroup_name($usergroup)
{
	$query = mysql_query("SELECT `Name` FROM `usergroup` WHERE `UsergroupID` = '$usergroup'");
	$answer = mysql_fetch_assoc($query);

	return $answer['Name'];
}

// #######################################
// GET USER'S TIME PREFERENCE
// #######################################

function fetch_user_time($username)
{
	$time_query = mysql_query("SELECT `TimeZone` FROM `user` WHERE `Name` = '$username'");
	$timezone = mysql_fetch_assoc($time_query);
	$userTime = $timezone['TimeZone'];

	return $userTime;
}

// #######################################
// SET LOGIN TIME
// #######################################

function set_login_time($username)
{
	mysql_query("UPDATE `user` SET `LastLogin`= NOW() WHERE `Name`='$username'");
}

// #######################################
// SET LOGOUT TIME
// #######################################

function set_logout_time($username)
{
	mysql_query("UPDATE `user` SET `LastLogin`= '' WHERE `Name`='$username'");
}

// #######################################
// FETCH ACTIVE USERS
// #######################################

function fetch_active_users($click)
{
	$array = array();
	$q = mysql_query("SELECT `Name`, `UserID` FROM `user` WHERE `LastLogin` > NOW() -30 * 60 ORDER BY `UserID` ASC");


	while($r = mysql_fetch_assoc($q))
	{
		if (!$click)
		{
			$array[] = style_user($r['Name']);
		}
		else
		{
			$array[] = make_clickable(style_user($r['Name']));
		}
	}

	$handle = count($array);

	if ($handle >= 1)
	{
		echo implode(' , ', $array);
	}
	else
	{
		echo 'No users are currently active';
	}
}

// #######################################
// FETCH ACTIVE USER NUMBER
// #######################################

function fetch_active_user_number()
{
	$array = array();
	$q = mysql_query("SELECT `Name`, `UserID` FROM `user` WHERE `LastLogin` > NOW() -30 * 60 ORDER BY `UserID` ASC");


	while($r = mysql_fetch_assoc($q))
	{
		$array[] = style_user($r['Name']);
	}

	$handle = count($array);

	return $handle;
}

// #######################################
// FETCH USER ID
// #######################################

function fetch_id($username)
{
	$query = mysql_query("SELECT `UserID` FROM `user` WHERE `Name`='$username'");
	$user = mysql_fetch_assoc($query);
	$ID = $user['UserID'];
	return $ID;
}

// #######################################
// FETCH USERNAME
// #######################################

function fetch_username($UID)
{
	$query = mysql_query("SELECT `Name` FROM `user` WHERE `UserID`='$UID'");
	$user = mysql_fetch_assoc($query);
	$ID = $user['Name'];
	return $ID;
}


// #######################################
// STYLE USER NAME
// #######################################

function style_user($username)
{
	$name = $username;
	$query = mysql_query("SELECT `UsergroupID` FROM `user` WHERE `Name`='$username'");
	$user = mysql_fetch_assoc($query);
	$ID = $user['UsergroupID'];

	$query = mysql_query("SELECT `Bold`,`Italic`,`Color`,`Background` FROM `usergroup` WHERE `UsergroupID`='$ID'");
	$group = mysql_fetch_assoc($query);

	$bold = $group['Bold'];
	$italic = $group['Italic'];
	$color = $group['Color'];
	$background = $group['Background'];

	if (!isBanned($username) && !isTempBanned($username))
	{
		if ($bold)
		{
			$name = '<b>' . $name . '</b>';
		}

		if ($italic)
		{
			$name = '<i>' . $name . '</i>';
		}

		if ($color != "")
		{
			if($background != "")
			{
				$name = "<span style=\"background-image:url('" . $background . "'); color: " . $color . "\">" . $name . "</span>";
			}
			else
			{
				$name = '<span style="color: ' . $color . '">' . $name . '</span>';
			}
		}
	}

	else
	{
		$name = '<s><span style="color: #FFFFFF">' . $name . '</span></s>';
	}

	return $name;
}

function style_group($id, $string)
{
	$name = $string;
	$query = mysql_query("SELECT `Bold`,`Italic`,`Color`,`Background` FROM `usergroup` WHERE `UsergroupID`='$id'");
	$group = mysql_fetch_assoc($query);

	$bold = $group['Bold'];
	$italic = $group['Italic'];
	$color = $group['Color'];
	$background = $group['Background'];

	if ($bold)
	{
		$name = '<b>' . $name . '</b>';
	}

	if ($italic)
	{
		$name = '<i>' . $name . '</i>';
	}

	if ($color != "")
	{
		if($background != "")
		{
			$name = "<span style=\"background-image:url('" . $background . "'); color: " . $color . "\">" . $name . "</span>";
		}
		else
		{
			$name = '<span style="color: ' . $color . '">' . $name . '</span>';
		}
	}

	else
	{
		$name = '<s><span style="color: #FFFFFF">' . $name . '</span></s>';
	}

	return $name;
}

// #######################################
// MAKE USERNAME CLICKABLE
// #######################################

function make_clickable($user)
{
	$var = strip_tags($user);
	$user = '<a href="#" onclick="loadPm(this); return false;" id="' . $var . '">' .$user . '</a>';
	return $user;
}

// #######################################
// CHECK IF ADMINISTRATOR
// #######################################

function can_do_admin($user)
{
	$query = mysql_query("SELECT `UsergroupID` FROM `user` WHERE `Name`='$user'");
	$user = mysql_fetch_assoc($query);
	$ID = $user['UsergroupID'];

	$query = mysql_query("SELECT `Administrator` FROM `usergroup` WHERE `UsergroupID`='$ID'");
	$group = mysql_fetch_assoc($query);

	return ($group['Administrator']) ? true : false;
}

// #######################################
// CHECK IF MODERATOR
// #######################################

function can_do_mod($user)
{
	$query = mysql_query("SELECT `UsergroupID` FROM `user` WHERE `Name`='$user'");
	$user = mysql_fetch_assoc($query);
	$ID = $user['UsergroupID'];

	$query = mysql_query("SELECT `Moderator` FROM `usergroup` WHERE `UsergroupID`='$ID'");
	$group = mysql_fetch_assoc($query);

	return ($group['Moderator']) ? true : false;
}

// #######################################
// CHECK IF OWNER
// #######################################

function can_do_owner($user) {

	$query = mysql_query("SELECT `UserID` FROM `user` WHERE `Name`='$user'");
	$user = mysql_fetch_assoc($query);

	return ($user['UserID'] == 1) ? true : false;
}

// #######################################
// FILTER OUT BAD MESSAGES
// #######################################

function filter($var)
{
	$var = mysql_real_escape_string($var);
	// $var = stripslashes($var);
	$var = htmlentities($var);
	$var = trim($var);
	return $var;
}

// #######################################
// CONVERT TIME
// #######################################

function convertTime($time, $username)
{
	date_default_timezone_set('America/New_York');

	$today = date('Ymd', time());
	$yesterday = date('Ymd', time()) - 1;
	$testTime = date('Ymd', strtotime($time));

	$boolToday = false;
	$boolYesterday = false;
	$boolOtherDay = false;

	if ($today == $testTime)
	{
		$boolToday = true;
	}
	else if ($yesterday == $testTime)
	{
		$boolYesterday = true;
	}
	else
	{
		$boolOtherDay = true;
	}

	$date = new DateTime($time, new DateTimeZone('America/New_York'));
	$date->setTimezone(new DateTimeZone(fetch_user_time($username)));

	if ($boolToday)
	{
		$output = 'Today ' . $date->format('h:i A');
	}
	else if ($boolYesterday)
	{
		$output = 'Yesterday ' . $date->format('h:i A');
	}
	else
	{
		$output = $date->format('m-d-y h:i A');
	}
	return $output;
}

// #######################################
// CHECK IF BANNED
// #######################################

function isBanned($user)
{
	$query = mysql_query("SELECT `Banned` FROM `user` WHERE `Name`='$user'");
	$status = mysql_fetch_assoc($query);
	return ($status['Banned']) ? true : false;
}

// #######################################
// COLOR SHOUT ACCORDING TO PREFERENCES
// #######################################

function markup($shout, $user)
{
	$markup = mysql_query("SELECT `Bold`, `Italic`,`Color`,`Font`,`Underline` FROM `user` WHERE `Name`='$user'");
	$marray = mysql_fetch_assoc($markup);

	if($marray['Bold'])
	{
		$shout = '<b>' . $shout . '</b>';
	}
	if($marray['Italic'])
	{
		$shout = '<i>' . $shout . '</i>';
	}

	if($marray['Underline'])
	{
		$shout = '<u>' . $shout . '</u>';
	}
	if($marray['Font'] != "Default" && $marray['Font'] != "default")
	{
		$shout = '<font face="' . $marray['Font'] . '">' . $shout . '</font>';
	}
	if($marray['Color'] != "Default" && $marray['Color'] != "default")
	{
		$shout = '<span style="color:'. $marray['Color'] .'">' . $shout . '</span>';
	}

	return $shout;
}

// #######################################
// CONVERT MESSAGE TO URL
// #######################################

function URLMe($msg)
{
	if (preg_match_all('#(\s+|^)(htt(ps|p)[^\s]+)#', $msg, $matches, PREG_PATTERN_ORDER))
	{
		$count = count($matches);
		for ($i = 0; $i <= $count; $i++) {
			if ($i != 0) { unset($matches[$i]); }
		}
		$count = count($matches[0]);
		for ($i = 0; $i < $count; $i++)
		{
			$value = $matches[0][$i];
			if (strlen($value) >= 42) {
				$start = substr($value, 0, 35);
				$end = substr($value, (strlen($value) - 6), strlen($value));
				$fixed = $start . '...' . $end;
			} else {
				$fixed = $value;
			}
			$msg = str_replace($value, '<a href="' . $value . '" target="_blank">' . $fixed . '</a>', $msg);
		}
	}
	// OLD METHOD: $msg = preg_replace('#(htt(ps|p)[^\s]+)#', '<a href="$1" target="_blank">$1</a>', $msg);
	return $msg;
}

// #######################################
// BBCODE
// #######################################

function BBcode($message, $bool = false)
{
	$search = array(
		'/\[IMG\](.*?)\[\/IMG\]/is',
		'/\[color="(.*?)"\](.*?)\[\/color\]/is',
		'/\[url="(.*?)"\](.*?)\[\/url\]/i',
		'/\[S\](.*?)\[\/S\]/is',
		'/\[I\](.*?)\[\/I\]/is',
		'/\[B\](.*?)\[\/B\]/is',
		'/\[size="(.*?)"\](.*?)\[\/size\]/is'
	);

	$replace = array(
		($bool) ? '<a href="$1" target="_blank">Posted Image</a>' : '<img src="$1"/>',
		'<span style="color:$1;">$2</span>',
		'<a href="$1" target="_blank">$2</a>',
		'<s>$1</s>',
		'<i>$1</i>',
		'<b>$1</b>',
		'<span style="font-size:$1;">$2</span>'
	);

	$message = preg_replace($search, $replace, $message);
	return $message;
}

// #######################################
// CONVERT MESSAGE TO SMILEYS
// #######################################

function smileMe($message)
{
	$query = mysql_query("SELECT * FROM smilies ORDER BY code DESC");
	$find = array();
	$replace = array();

	$i = 0;
	while($r = mysql_fetch_assoc($query))
	{
		$name = $r['Name'];
		$find[$i] = $r['Code'];
		$replace[$i] = '<img src="' . $r['URL'] . '" alt="' . $name . '" title="' . $name . '" />';
		$i++;
	}

	$message = str_replace($find, $replace, $message);

	return $message;
}

// #######################################
// SECURE SESSION
// #######################################

function secureSession()
{
	if(isset($_SESSION['last_ip']) === false)
	{
		$_SESSION['last_ip'] = $_SERVER['REMOTE_ADDR'];
	}
	if($_SESSION['last_ip'] !== $_SERVER['REMOTE_ADDR'])
	{
		session_unset();
		session_destroy();
	}
}

// #######################################
// SECURE SESSION
// #######################################

function secureINI()
{
	ini_set('session.cookie_httponly', true);
}

// #######################################
// DISPLAY SETTINGS
// #######################################

function get_display_settings($username)
{
	$query = mysql_query("SELECT `Name`, `Background`, `Opacity`, `Repeat` FROM user WHERE Name='$username'");
	$r = mysql_fetch_assoc($query);
	$array = array();
	$array['Name'] = $r['Name'];
	$array['Background'] = $r['Background'];
	$array['Opacity'] = $r['Opacity'];
	$array['Repeat'] = $r['Repeat'];

	return $array;
}

?>
<?php

include('connect.php');
include('functions.php');

secureINI();
session_start();
secureSession();

$adminmsg = $_POST['text'];
$message = filter($_POST['text']);
$user = $_SESSION['Name'];
$ID = fetch_id($user);

// access level variables
$can_do_admin = can_do_admin($user);
$can_do_mod = can_do_mod($user);

date_default_timezone_set('America/New_York');
$time = date('Y-m-d H:i:s', time());
// User Style Arrays

if(!isBanned($user))
{
	set_login_time($user);
}

if (isTempBanned($user))
{
	die();
}

if(($message != '' && strlen($message) < 1000 && isset($user) && !isBanned($user)) || $message != '' && $can_do_admin)
{
	if (trim($message) == "/admin" && $can_do_admin)
	{
		$array = array_reverse(get_admins());
		foreach ($array as $user)
		{
			mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$user', '$time', ':dumb:', '1', '0', '');");
		}
	}

	else if (trim($message) == "/yolo" && $can_do_admin)
	{
		$array = get_users();
		shuffle($array);
		foreach ($array as $user)
		{
			$string = "#Y0LO SwAQqG YuNg $ c4sh $";
			mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$user', '$time', '$string', '1', '0', '');");
		}
	}

	else if (trim($message) == "/debug" && $can_do_admin)
	{
		for ($i = 1; $i <= 2000; $i++)
		{
			$string = "Shout " . $i;
			mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', '$string', '1', '0', '');");
		}
	}


	else if (trim($message) == "/canada" && $can_do_admin)
	{
		$array = get_users();

		$anticanada = array(
			"America > Canada",
			"All canadians know is french",
			"the only thing canada is famous for is being north of the USA",
			"all those posers in canada have a leaf for a flag... fags",
			"canada ruined basketball with the toronto team",
			"canada ruined bacon and they harvest ice",
			"canadians hit baby seals",
			"the metric system isn't very cool",
			"Who even runs Canada?"
		);

		shuffle($array);

		foreach ($array as $user)
		{
			$rand = rand(0, 1);
			$me = 0;
			shuffle($anticanada);
			$string = $anticanada[0];
			mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$user', '$time', '$string', '$me', '0', '');");
		}
	}

	/*
	else if((trim($message) == "/prune" && $can_do_admin) || (trim($message) == "/prune" && $can_do_mod))
	{
		mysql_query("TRUNCATE `shouts`");
		mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', 'has pruned the Mattbox', '1', '0', '');");
	}

	else if((preg_match("#^(/prune\s+?)#i", $message, $matches) && $can_do_admin) || (preg_match("#^(/prune\s+?)#i", $message, $matches) && $can_do_mod))
	{

		$message = trim(str_replace($matches[0], '', $message));

		if($message != '') {

			$q = mysql_query("SELECT * FROM user WHERE `Name`='$message'");
			$r = mysql_num_rows($q);

			if($r != 0) {

				$a = mysql_query("SELECT `UserID`,`Name` FROM user WHERE `Name`='$message'");
				$v = mysql_fetch_assoc($a);
				$pruned = $v['Name'];
				$id = $v['UserID'];

				if($id != 1)
				{
					mysql_query("DELETE FROM `shouts` WHERE `User`='$id'");
					mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', 'has pruned all shouts by $pruned', '1', '0', '');");
				}
				else if(can_do_owner($user))
				{
					mysql_query("DELETE FROM `shouts` WHERE `User`='$id'");
					mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', 'has pruned all shouts by $pruned', '1', '0', '');");
				}
				else
				{
					mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', 'You can\'t prune $pruned', '0', '1', '$user');");
				}
			}
		}
	}
	*/
	else if ((preg_match("#^(/ban\s+?)#i", $message, $matches) && $can_do_admin) || (preg_match("#^(/ban\s+?)#i", $message, $matches) && $can_do_mod))
	{

		$message = trim(str_replace($matches[0], '', $message));
		if($message != '')
		{
			$q = mysql_query("SELECT * FROM user WHERE `Name`='$message'");
			$r = mysql_num_rows($q);

			if($r != 0)
			{
				$a = mysql_query("SELECT `UserID`,`Name` FROM user WHERE `Name`='$message'");
				$v = mysql_fetch_assoc($a);
				$banned = $v['Name'];

				if(($can_do_admin && $v['UserID'] != 1 && !can_do_admin($banned)) || ($can_do_mod && !can_do_admin($banned)))
				{
					mysql_query("UPDATE `user` SET `Banned` = '1' WHERE `Name`='$message'");
					mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', 'has Banned $banned from the Mattbox', '1', '0', '')");
				}
				else
				{
					mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', 'You can\'t ban $banned', '0', '1', '$user')");
				}
			}
		}
	}

	else if((preg_match("#^(/unban\s+?)#i", $message, $matches) && $can_do_admin) || (preg_match("#^(/unban\s+?)#i", $message, $matches) && $can_do_mod))
	{

		$message = trim(str_replace($matches[0], '', $message));

		if($message != '')
		{
			$q = mysql_query("SELECT * FROM user WHERE `Name`='$message'");
			$r = mysql_num_rows($q);
			$detail = mysql_fetch_assoc($q);
			$unbanned = $detail['Name'];

			if($r != 0)
			{
				mysql_query("UPDATE `user` SET `Banned` = '0' WHERE `Name`='$message'");
				mysql_query("UPDATE `user` SET `TempBanned` = '0' WHERE `Name`='$message'");
				mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', 'has unbanned $unbanned from the Mattbox', '1', '0', '')");
			}
		}
	}

	else if ((preg_match("#^(/silence\s+?)#i", $message, $matches) && $can_do_admin) || (preg_match("#^(/silence\s+?)#i", $message, $matches) && $can_do_mod))
	{

		$message = trim(str_replace($matches[0], '', $message));
		if($message != '')
		{
			$q = mysql_query("SELECT * FROM user WHERE `Name`='$message'");
			$r = mysql_num_rows($q);

			if($r != 0)
			{
				$a = mysql_query("SELECT `UserID`,`Name` FROM user WHERE `Name`='$message'");
				$v = mysql_fetch_assoc($a);
				$silenced = $v['Name'];

				if(($can_do_admin && $v['UserID'] != 1 && !can_do_admin($silenced)) || ($can_do_mod && !can_do_admin($silenced)))
				{
					mysql_query("UPDATE `user` SET `Silenced` = '1' WHERE `Name`='$message'");
					mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', 'has Silenced $silenced from the Mattbox', '1', '0', '')");
				}
				else
				{
					mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', 'You can\'t silence $silenced', '0', '1', '$user')");
				}
			}
		}
	}

	else if((preg_match("#^(/unsilence\s+?)#i", $message, $matches) && $can_do_admin) || (preg_match("#^(/unsilenced\s+?)#i", $message, $matches) && $can_do_mod))
	{

		$message = trim(str_replace($matches[0], '', $message));

		if($message != '') {

			$q = mysql_query("SELECT * FROM user WHERE `Name`='$message'");
			$r = mysql_num_rows($q);
			$detail = mysql_fetch_assoc($q);
			$unsilenced = $detail['Name'];

			if($r != 0)
			{
				mysql_query("UPDATE `user` SET `Silenced` = '0' WHERE `Name`='$message'");
				mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', 'has unSilenced $unsilenced from the Mattbox', '1', '0', '')");
			}
		}
	}

	else if (preg_match("#^(/tban\s+)(.+?[^;]);(.+?)$#i", $message, $matches) && $can_do_admin)
	{
		$tban_user = trim($matches[2]);
		$bantime = time() + ($matches[3] * 60 * 60);
		$arr = fetch_temp_banned();

		if ($matches[3] == 1) {
			$note = 'hour';
		} else {
			$note = 'hours';
		}

		if ((!in_array($tban_user, $arr) && !can_do_admin($tban_user)) || (!in_array($tban_user, $arr) && can_do_owner($user)))
		{
			if ($bantime > time())
			{
				$q = mysql_query("SELECT * FROM user WHERE `Name`='$tban_user'");
				$r = mysql_num_rows($q);
				$detail = mysql_fetch_assoc($q);
				$banned = $detail['Name'];

				if($r != 0)
				{
					$tban_user = $banned;
					$string = "has temporarily banned " . $tban_user . " from the Mattbox for " . $matches[3] . " " . $note;
					mysql_query("UPDATE `user` SET `TempBanned`='$bantime' WHERE `Name`='$tban_user'");
					mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', '$string', '1', '0', '')");
				}
			}
		}
	}

	else if(preg_match("#^(/me\s+?)#i", $message, $matches)) {

		$message = trim(str_replace($matches[0], '', $message));

		if($message != '')
		{
			mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', '$message', '1', '0', '');");
		}
	}

	else if(trim($message) == '/happyhour' && $can_do_admin)
	{
		$settings = get_mattbox_settings();

		if($settings['happyhour'])
		{
			mysql_query("UPDATE settings SET happyhour='0' WHERE ID='1'");
			mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', 'has disabled happy hour!', '1', '0', '');");
		}
		else {
			mysql_query("UPDATE settings SET happyhour='1' WHERE ID='1'");
			mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', 'has enabled happy hour!', '1', '0', '');");
		}
	}

	else if(trim($message) == "/mattbot" && $can_do_admin)
	{
		$settings = get_mattbox_settings();

		if($settings['mattbot'])
		{
			mysql_query("UPDATE settings SET mattbot='0' WHERE ID='1'");
			mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', 'has banished Mattbot!', '1', '0', '');");
		}
		else {
			mysql_query("UPDATE settings SET mattbot='1' WHERE ID='1'");
			mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', 'has summoned Mattbot!', '1', '0', '');");
		}
	}

	else if(preg_match("#^(/notice\s+?)#i", $message, $matches))
	{
		$notice = trim(str_replace($matches[0], '', $message));
		mysql_query("UPDATE settings SET notice='$notice' WHERE ID='1'");
	}

	else if(trim($message) == "/removenotice" && $can_do_admin)
	{
		mysql_query("UPDATE settings SET notice='' WHERE ID='1'");
	}

	else if(preg_match("#^(/say\s+)(.+?[^;]);(.+?)$#i", $message, $matches) && $can_do_admin) {

		$say = trim($matches[2]);
		$saymsg = trim($matches[3]);
		$q = mysql_query("SELECT * FROM user WHERE `Name`='$say'");
		$r = mysql_num_rows($q);

		if($r != 0)
		{
			$a = mysql_query("SELECT `UserID`,`Name` FROM user WHERE `Name`='$say'");
			$v = mysql_fetch_assoc($a);
			$said = $v['Name'];
			$saidID = $v['UserID'];

			if($v['UserID'] != 1)
			{
				set_login_time($said);
				mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$saidID', '$time', '$saymsg', '0', '0', '')");
			}
			else
			{
				mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', 'You don\'t have permission to make $said say $saymsg', '0', '1', '$user')");
			}
		}
	}

	else if(preg_match("#^(/pm\s+)(.+?[^;]);(.+?)$#i", $message, $matches))
	{
		$pmuser = trim($matches[2]);
		$pm = $matches[3];

		$q = mysql_query("SELECT * FROM user WHERE `Name`='$pmuser'");
		$r = mysql_num_rows($q);

		if($r != 0)
		{
			$v = mysql_fetch_assoc($q);
			$pmuser = $v['Name'];
			mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', '$pm', '0', '1', '$pmuser')");
		}
	}

	else {
		mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', '$message', '0', '0', '');");
	}
	log_aop();
}
?>

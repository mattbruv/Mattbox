<?php
include('connect.php');
include('functions.php');

secureINI();
session_start();
secureSession();
$version = 'v3';
if(isset($_COOKIE['Name']) && isset($_COOKIE['Remember']))
{
	$cookieName = $_COOKIE['Name'];
	$rememberMe = $_COOKIE['Remember'];
	$_SESSION['Name'] = $cookieName;
	$rawuserSession = $_SESSION['Name'];
}
else {
	header('location: login.php');
	exit;
}

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo SITENAME; ?></title>
<script type="text/javascript">
	function help()
	{
		alert('The Archive is all of the shouts in the database combined in one list.\r\nSmilies and images have been removed making it (somewhat) safe to read in public ;)');
	}
</script>
<style type="text/css">

td {
	overflow:hidden;
	whitespace:nowrap;
}

#table {
	border:solid;
	border-color:#DFDFDF;
	border-width:2px;
}

#pages a {
	color:#000000;
}

#selected a {
	font-size:16px;
	font-weight:bold;
}

#selected {
	font-size:16px;
	font-weight:bold;
}

#shout {
	text-shadow: 1px 1px #FFFFFF;
}

#even {
	background-color: #FFFFFF;
	border:solid;
	border-color:#DFDFDF;
	border-width:1px;
}


#odd {
	background-color: #DFDFDF;
	border:solid;
	border-color:#DFDFDF;
	border-width:1px;
}
	
</style>
<link rel="stylesheet" href="styles.css" type="text/css">
</head>
<body>
<center><p>
<?php

if(isset($_SESSION['Name']))
{
	$user = $_SESSION['Name'];
	set_login_time($user);
	if(!isBanned($user))
	{
		echo style_user($user) . ', Viewing Archive (<a href="#" onclick="help()">?</a>)<br />';
		echo '[<a href="index.php">Return</a>]&nbsp;';
		echo '[<a href="logout.php" title="Log Out">Log Out</a>]<br />';
	}
	else
	{
		echo 'You are no longer welcome to the Mattbox, ' . $user . '<br />';
	}
}

?></p>
<span id="pages">
<?php

$list = 40;
$pages_query = mysql_query("SELECT COUNT(`ShoutID`) FROM `shouts`");
$rows = mysql_result($pages_query, 0);
$pages = ceil($rows / $list);
$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $list;

function showPages()
{
	$list = 40;
	$pages_query = mysql_query("SELECT COUNT(`ShoutID`) FROM `shouts`");
	$rows = mysql_result($pages_query, 0);
	$pages = ceil($rows / $list);
	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
	$start = ($page - 1) * $list;

	if ($pages >= 1 && $page <= $pages)
	{
		$higher = $page + 2;
		echo '<a href="archive.php?page=1">First</a> | ';
		echo '<span id="links">';
		for ($x = ($page - 2); $x <= $higher; $x++)
		{
			if ($x >= 1 && $x <= $pages)
			{
				echo ($x == $page) ? '<span id="selected">' . $x . '</span>' : '<a href="archive.php?page='.$x.'">' . $x . '</a>';
				if(($x != $higher) && ($x != $pages))
				{
					echo '-';
				}
			}
		}
		echo '</span>';
		echo ' | <a href="archive.php?page=' . $pages . '">Last</a>';
	}
}

if ($rows == 0)
{
	die('<br />Nothing to display...');
}



showPages();

?></span>
<br />========
<table id="table">
	<tr id="odd" style="color: #404040;">
		<td>Shout:</td>
	</tr>

<?php

$query = mysql_query("SELECT shouts.ShoutID, shouts.User, user.Name, shouts.Time, shouts.Shout, shouts.Me FROM `shouts` INNER JOIN `user` ON `shouts`.`User` = `user`.`UserID` WHERE user.Silenced = 0 AND shouts.Private = 0 ORDER BY shouts.ShoutID DESC LIMIT $start, $list");

$i = 1;

while ($r = mysql_fetch_assoc($query))
{
	$rawuser = $r['Name'];
	$user = style_user($rawuser);
	$shout = markup(URLMe(stripslashes($r['Shout'])), $rawuser);
	$time = convertTime($r['Time'], $rawuserSession);
	$me = $r['Me'];
	if (can_do_admin($rawuser))
	{
		$shout = BBCode($shout, true);
	}
	$id = ($i & 1) ? 'even' : 'odd';

	echo '<tr id="' . $id . '">';
	
	if (!$me)
	{
		echo '<td>[' . $time . ']' . ' ' .  $user . ' : ' . $shout . '</td><tr />';
	}
	else
	{
		echo '<td>*' .  $user . ' ' . $shout . '*</td><tr />';
	}
	
	$i++;
}

?>

</table>
<span id="pages">
<?php

// list = total shouts per page
// pages = total page # of all shouts
// page = current page in URL, or 1

showPages();

?></span><br />
========
</center>
</body>
</html>
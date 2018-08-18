<?php

include('header.php');

if(isset($_GET['id']))
{
	$ID = (int)$_GET['id'];
}
else {
	die('Invalid ID');
}

if(!id_exists($ID))
{
	die('Invalid ID');
}

if($ID == 1 && !can_do_owner($_SESSION['Name']))
{
	die('You do not have permission to edit this user');
}

$username = style_user($_SESSION['Name']);
$rawname = $_SESSION['Name'];

$query = mysql_query("SELECT `Name`,`UsergroupID`, `Banned`, `Silenced` FROM user WHERE UserID = '$ID'");
$result = mysql_fetch_assoc($query);
$banned = $result['Banned'];
$silenced = $result['Silenced'];
$editUserRaw = $result['Name'];
$editUser = style_user($result['Name']);

$usergquery = mysql_query("SELECT `UsergroupID`, `Name` FROM `usergroup`");
$usergroups = array();

while($r = mysql_fetch_assoc($usergquery))
{
	$usergroups[] = array('UsergroupID' => $r['UsergroupID'], 'Name' => $r['Name']);
}

$editusergroupID = $result['UsergroupID'];

// POST DATA

if(isset($_POST['submit']))
{
	$newName = $_POST['name'];
	$newPassword = $_POST['password'];
	$newUsergroup = $_POST['usergroup'];
	$error = false;
	$login = false;
	
	if(check_username($newName) && $newName != $editUserRaw)
	{
		$newName = filter($newName);

		$query = mysql_query("SELECT * FROM user WHERE `Name`='$newName'");
		$rows = mysql_num_rows($query);

		if ($rows > 0)
		{
			$error = true;
		}
		else
		{
			$query = mysql_query("UPDATE `user` SET `Name`='$newName' WHERE `UserID`='$ID'");
			$query2 = mysql_query("UPDATE `shouts` SET `To`='$newName' WHERE `To`='$editUserRaw'");
			log_event($rawname . " changed " . $editUserRaw . " username to " . $newName);
			if($editUserRaw == $rawname)
			{
				$login = true;
			}
		}
	}
	else if (!check_username($newName))
	{
		$error = true;
	}
	
	if(check_password($newPassword))
	{
		$newPassword = md5($newPassword);
		$query = mysql_query("UPDATE `user` SET `Password`='$newPassword' WHERE `UserID`='$ID'");
		log_event($rawname . " changed " . $editUserRaw . " password");
	}
	else if ($newPassword != '' && !check_password($newPassword))
	{
		$error = true;
	}
	
	if($newUsergroup != '')
	{
		$newUsergroup = (int)$newUsergroup;
		mysql_query("UPDATE `user` SET `UsergroupID` = '$newUsergroup' WHERE `UserID` = '$ID'");
		log_event($rawname . " changed " . $editUserRaw . " group to " . get_usergroup_name($newUsergroup));
	}

	
	if($login)
	{
		header('location: ../logout.php');
		exit;
	}
	else if($error)
	{
		header('location: error.php');
		exit;
	}
	else {
		header('location: success.php');
		exit;
	}
	
}

if(isset($_POST['approve']))
{
	mysql_query("UPDATE `user` SET `Approved`='1' WHERE `UserID`='$ID'");
	$string = mysql_real_escape_string('has been approved by ' . $rawname . '.');
	mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', '$string', '1', '0', '');");
	header('location: success.php');
}

if(isset($_POST['ban']))
{
	mysql_query("UPDATE user SET Banned = '1' WHERE UserID = '$ID'");
	header('location: success.php');
}
if(isset($_POST['unban']))
{
	mysql_query("UPDATE user SET Banned = '0' WHERE UserID = '$ID'");
	header('location: success.php');
}
if(isset($_POST['silence']))
{
	mysql_query("UPDATE user SET Silenced = '1' WHERE UserID = '$ID'");
	header('location: success.php');
}
if(isset($_POST['unsilence']))
{
	mysql_query("UPDATE user SET Silenced = '0' WHERE UserID = '$ID'");
	header('location: success.php');
}

?>
<html>
<head>
	<title><?php echo $_SESSION['Name']; ?> - Admin Control Panel</title>
	<link rel="stylesheet" href="admincp.css" type="text/css">
</head>

<body>
<?php echo 'Welcome, ' . $username . '.<br /><i>(<a href="users.php">Users</a> - <a href="../logout.php">Log Out</a>)</i>'; ?>
<h3><u>Mattbox AdminCP</u></h3>
<p>
<script type="text/javascript">
	
	function alertPass()
	{
		alert('Leave field blank to keep password the same.\r\nRemember, Passwords have to be between 4 and 25 chars in length');
	}
	
	function alertUser()
	{
		alert('Don\'t modify the field if you want to keep username the same\r\nRemember, Usernames have to be between 3 and 16 chars in length');
	}
	
	function alertUG()
	{
		alert('Leave selection unmodified to keep user in the same usergroup.');
	}
	
</script>
Editing User: <?php echo $editUser; ?><br />
==============<br />

<ul>
	<li><a href="../usercp/settings.php?id=<?php echo $ID;?>">Edit <?php echo $editUserRaw;?>'s Styles</a></li>
</ul>

<form action="edituser.php?id=<?php echo $ID; ?>" method="POST"/>
<table>
<tr>
<td>Username: </td> <td><input type="text" name="name" value="<?php echo $editUserRaw; ?>"/> (<a href="#" onclick="alertUser()">?</a>)</td>
</tr>
<tr>
<td>Password: </td> <td><input type="text" name="password"/> (<a href="#" onclick="alertPass()">?</a>)</td>
</tr>
<tr>
<td>Usergroup: </td> <td>
	
	<select name = "usergroup">
		<?php
		
			foreach($usergroups as $u)
			{
				$usergroupID = $u['UsergroupID'];
				$usergroupName = $u['Name'];
				
				if ($editusergroupID == $usergroupID)
				{
					echo '<option value="" selected="selected">' . $usergroupName . '</option>';
				}
				else
				{
					echo '<option value="' . $usergroupID . '">' . $usergroupName . '</option>';
				}
			}
		?>
	</select>  (<a href="#" onclick="alertUG()">?</a>)
</td>
</tr><tr><td><input type="submit" name="submit" value="Update" /></td></tr>
</table>
</form>
==============<br /><br />
<form action="edituser.php?id=<?php echo $ID; ?>" method="post">
	<?php
	
	if(!isApproved($editUserRaw))
	{
		echo '<input type="submit" name="approve" value="Approve ' . $editUserRaw . '"/>';
	}
	
	else
	{
		if(!$banned)
		{
			echo '<input type="submit" name="ban" value="Ban ' . $editUserRaw . '"/>';
		}
		else
		{
			echo '<input type="submit" name="unban" value="Unban ' . $editUserRaw . '"/>';
		}
		
		echo ' | ';
		
		if(!$silenced)
		{
			echo '<input type="submit" name="silence" value="Silence ' . $editUserRaw . '"/>';
		}
		else
		{
			echo '<input type="submit" name="unsilence" value="Unsilence ' . $editUserRaw . '"/>';
		}
	}

	
	?>
</form>
==============<br />

</form>
</body>
</html>
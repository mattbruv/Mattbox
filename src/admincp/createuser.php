<?php

include('header.php');
$username = style_user($_SESSION['Name']);
$rawname = $_SESSION['Name'];

$usergquery = mysql_query("SELECT `UsergroupID`, `Name` FROM `usergroup`");
$usergroups = array();

while($r = mysql_fetch_assoc($usergquery))
{
	$usergroups[] = array('UsergroupID' => $r['UsergroupID'], 'Name' => $r['Name']);
}

if(isset($_POST['submit']))
{
	$name = filter($_POST['name']);
	$pass = filter($_POST['password']);
	$usergroup = (int)$_POST['usergroup'];
	
	if(isset($name) && check_username($name) && isset($pass) && check_password($pass) && isset($usergroup))
	{
		$pass = md5($pass);
		mysql_query("INSERT INTO `user` (`UserID` ,`Name` ,`Password` ,`UsergroupID`, `Approved` ,`Banned` ,`Silenced` ,`Bold` ,`Italic` ,`Underline` ,`Color` ,`Font` ,`Background` ,`IPAddress` ,`LastLogin` ,`TimeZone`)VALUES ('', '$name', '$pass', '$usergroup', '1', '0', '0', '0', '0', '0', 'Default', 'Default', '', '', '', 'America/New_York')");
		$id = fetch_id($name);
		mysql_query("INSERT INTO `shouts`(`ShoutID`, `User`, `Time`, `Shout`, `Me`, `Private`, `To`) VALUES ('', '$id', NOW(), 'has been concieved by $rawname', '1', '0', '')");
		mysql_query("INSERT INTO `shouts`(`ShoutID`, `User`, `Time`, `Shout`, `Me`, `Private`, `To`) VALUES ('', '$id', NOW(), '...the miracle of life :fa:', '0', '0', '')");
		log_event($rawname . ' created user ' . $name . ' as a ' . get_usergroup_name($usergroup));
		header('location: success.php');
		exit;
	}
	else {
		$error = 'Invalid Details. Please enter all categories and make information accurate.<br />';
	}
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
AdminCP - Create User<br />
==============<br /><br />
User Details
<form action="createuser.php" method="POST">
<table>
	<tr>
		<td>Username: </td><td><input type="text" name="name" /></td>
	</tr>
	<tr>
		<td>Password: </td><td><input type="password" name="password" /></td>
	</tr>
	<tr>
		<td>Usergroup: </td><td>
			<select name = "usergroup">
			<?php
			
				foreach($usergroups as $u)
				{
					$usergroupID = $u['UsergroupID'];
					$usergroupName = $u['Name'];
					
					if ($usergroupName == 'Member')
					{
						echo '<option value="' . $usergroupID . '" selected="selected">' . $usergroupName . '</option>';
					}
					else
					{
						echo '<option value="' . $usergroupID . '">' . $usergroupName . '</option>';
					}
				}
			?>
		</select>
		</td>
	</tr>
</table>
<input type="submit" name="submit" value="Create User"/>
</form>
<?php
if(isset($error))
{
	echo '<span id="list" style="color:red">' . $error . '</span>';
}
?>
==============
</body>
</html>
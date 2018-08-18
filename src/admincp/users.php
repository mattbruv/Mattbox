<?php

include('header.php');
$username = style_user($_SESSION['Name']);
$rawname = $_SESSION['Name'];

echo 'Welcome, ' . $username . '.<br /><i>(<a href="index.php">AdminCP</a> - <a href="../logout.php">Log Out</a>)</i>';

?>
<html>
<head>
	<title><?php echo $_SESSION['Name']; ?> - Admin Control Panel</title>
	<link rel="stylesheet" href="admincp.css" type="text/css">
<style type="text/css">
	#fakeuser {
		background-color:#FFFF99;
	}
	#notapproved {
		background-color:#FFCCCC;
	}
</style>
</head>

<body>
<h3><u>Mattbox AdminCP</u></h3>
AdminCP - Manage Users<br />
==============<br />
<ul>
	<li><a href="createuser.php">Create New User</a></li>
</ul>
<b>Key:</b> | <span id="notapproved">Not Approved</span> | <span id="fakeuser">Admin Created User</span> | Regular User |
<div id="list">
<h3>Users:</h3>
<table border="1">
<tr>
	<td><b>User ID</b></td>
	<td><b>Username</b></td>
	<td><b>Usergroup</b></td>
	<td><b>Banned</b></td>
	<td><b>Silenced</b></td>
	<td><b>Registration IP</b></td>
	<td><b>Timezone</b></td>
	<td><b>Last Login</b></td>
	<td><b>Edit</b></td>
	<td><b>Delete</b></td>
</tr>
<?php

$query = mysql_query("SELECT * FROM user");

$users = array();

while ($r = mysql_fetch_assoc($query))
{
	$users[] = array('UserID' => $r['UserID'], 'Name' => $r['Name'], 'Password' => $r['Password'], 'UsergroupID' => $r['UsergroupID'], 'Banned' => $r['Banned'], 'Silenced' => $r['Silenced'], 'IPAddress' => $r['IPAddress'], 'TimeZone' => $r['TimeZone'], 'LastLogin' => $r['LastLogin']);
}

foreach ($users as $u)
{
	$UserID = $u['UserID'];
	$Name = style_user($u['Name']);
	$Usergroup = get_usergroup_name($u['UsergroupID']);
	$banned = $u['Banned'];
	$silenced = $u['Silenced'];
	$IP = $u['IPAddress'];
	$timezone = $u['TimeZone'];
	$login = $u['LastLogin'];
	$approved = isApproved($u['Name']);
	
	if ($banned)
	{
		$banned = 'True';
	}
	else
	{
		$banned = '<i>False</i>';
	}
	
	if ($silenced)
	{
		$silenced = 'True';
	}
	else
	{
		$silenced = '<i>False</i>';
	}
	if($IP == '')
	{
		$IP = 'N/A';
	}
	if($UserID == 1)
	{
		$IP = 'Hidden';
	}
	
	if($IP == 'N/A')
	{
		echo '<tr id="fakeuser">';
	}
	if(!$approved)
	{
		echo '<tr id="notapproved">';
	}
	else
	{
		echo '<tr>';
	}
	echo '<td>' . $UserID . '</td>';
	echo '<td><span id="normal">' . $Name . '</span></td>';
	echo '<td>' . $Usergroup . '</td>';
	echo '<td>' . $banned . '</td>';
	echo '<td>' . $silenced . '</td>';
	echo '<td>' . $IP . '</td>';
	echo '<td>' . $timezone . '</td>';
	echo '<td>' . $login . '</td>';
	
	if($UserID != 1 || can_do_owner($rawname))
	{
		echo "<td>[<a href='edituser.php?id=" . $UserID . "'>EDIT</a>]</td>"; 
		echo "<td>[<a href='deleteuser.php?id=" . $UserID . "'>DELETE</a>]</td>";
	}
	else
	{
		echo '<td>N/A</td>';
		echo '<td>N/A</td>';
	}

	echo '</tr>';
}

?>
</table>
<br />
==============<br />
</div>
</body>
</html>
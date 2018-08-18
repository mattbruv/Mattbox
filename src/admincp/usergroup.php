<?php
include('header.php');
$username = style_user($_SESSION['Name']);

echo 'Welcome, ' . $username . '.<br /><i>(<a href="index.php">AdminCP</a> - <a href="../logout.php">Log Out</a>)</i>';

?>
<html>
<head>
	<title><?php echo $_SESSION['Name']; ?> - Admin Control Panel</title>
	<link rel="stylesheet" href="admincp.css" type="text/css">
</head>

<body>
	
<h3><u>Mattbox AdminCP</u></h3>
<style type="text/css">
#admin {
	background-color:#FFCCCC;
}
#mod {
	background-color:#CCCCFF;
}
td {
	text-align:center;
}
</style>
AdminCP - Manage Usergroups<br />
==============<br />
<ul>
	<li><a href="createusergroup.php">Create New Usergroup</a></li>
</ul>
<b>Key:</b> | <span id="admin">Admin Permissions</span> | <span id="mod">Moderator Permissions</span> | Regular Permissions |<br />
<div id="list">
<h3>Usergroups:</h3>
<table border="1">
<tr>
<td>ID</td><td>Users</td><td>Name</td><td>Color</td><td>B.G.</td><td>Bold</td><td>Italic</td><td>Edit</td><td>Delete</td>
</tr>
<?php

$query = mysql_query("SELECT * FROM usergroup");

$users = array();

while ($r = mysql_fetch_assoc($query))
{
	$users[] = array('UsergroupID' => $r['UsergroupID'], 'Name' => $r['Name'], 'Administrator' => $r['Administrator'], 'Moderator' => $r['Moderator'], 'Bold' => $r['Bold'], 'Italic' => $r['Italic'], 'Color' => $r['Color'], 'Background' => $r['Background']);
}

foreach ($users as $u)
{
	$ID = $u['UsergroupID'];
	$name = $u['Name'];
	$admin = $u['Administrator'];
	$mod = $u['Moderator'];
	$bold = $u['Bold'];
	$italic = $u['Italic'];
	$color = $u['Color'];
	$background = $u['Background'];
	
	$userquery = mysql_query("SELECT COUNT(`UserID`) FROM `user` WHERE `UsergroupID`='$ID'");
	$numusers = mysql_result($userquery, 0);
	
	if ($admin) {
		echo '<tr id="admin">';
	}
	else if ($mod) {
		echo '<tr id="mod">';
	}
	if($bold)
	{
		$bold = 'True';
	}
	else {
		$bold = 'False';
	}
	if($italic)
	{
		$italic = 'True';
	}
	else {
		$italic = 'False';
	}
	$background = ($background != '') ? 'True' : 'False';
		
	echo '<td align="center">' . $ID . '</td>';
	echo '<td align="center">' . $numusers . '</td>';
	echo '<td><span id="normal">' . style_group($ID, $name) . '</span></td>';
	echo '<td>' . $color . '</td>';
	echo '<td>' . $background . '</td>';
	echo '<td align="center">' . $bold . '</td>';
	echo '<td align="center">' . $italic . '</td>';
	echo '<td>[<a href="editgroup.php?id=' . $ID . '">EDIT</a>]</td>';

	if ($ID != 1 && $ID != 2) 
	{
		echo '<td>[<a href="deletegroup.php?id=' . $ID . '">DELETE</a>]</td>';
	}
	else
	{
		echo '<td align="center">N/A</td>';
	}

	echo '</tr>';
}

?>
</table><br />
==============<br />
</body>
</html>
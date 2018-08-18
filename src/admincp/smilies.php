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
</head>

<body>
	
<h3><u>Mattbox AdminCP</u></h3>
AdminCP - Manage Smilies<br />
==============<br />
<ul>
	<li><a href="addsmiley.php">Add New Smiley</a></li>
</ul>
<div id="list">
<h3>Smilies:</h3>

<table border="1">
	<tr>
		<td>ID</td>
		<td>Image</td>
		<td>Code</td>
		<td>Name</td>
		<td>Location</td>
		<td>Uploaded By</td>
		<td>Edit</td>
		<td>Delete</td>
	</tr>
<?php
$query = mysql_query("SELECT * FROM smilies");
$smilearray = array();

while($r = mysql_fetch_assoc($query))
{
	$smilearray[] = array('SmileyID' => $r['SmileyID'], 'Name' => $r['Name'], 'URL' => $r['URL'], 'Code' => $r['Code'], 'Uploaded' => $r['Uploaded']);
}

foreach ($smilearray as $s)
{
	$ID = $s['SmileyID'];
	$URL = '../' . $s['URL'];
	$name = $s['Name'];
	$code = $s['Code'];
	$userID = $s['Uploaded'];
	
	$query = mysql_query("SELECT Name FROM user WHERE UserID = '$userID'");
	$uploadeduser = mysql_result($query, 0);
	
	echo '<tr>';
	echo '<td>'. $ID . '</td>';
	echo '<td><center><img src="' . $URL . '" alt="' . $name . '" title="' . $name . '"/></center></td>';
	echo '<td>' . $code . '</td>';
	echo '<td>' . $name . '</td>';
	echo '<td>' . $URL . '</td>';
	echo '<td><center><span id="normal">' . style_user($uploadeduser) . '</span></center></td>';
	echo '<td>[<a href="editsmiley.php?id='.$ID.'" >EDIT</a>]</td>';
	echo '<td>[<a href="deletesmiley.php?id='.$ID.'" >DELETE</a>]</td>';
	echo '</tr>';
}

?>
</table>
<br />
==============<br />
</body>
</html>
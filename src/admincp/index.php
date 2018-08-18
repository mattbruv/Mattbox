<?php
include('header.php');
$username = style_user($_SESSION['Name']);

echo 'Welcome, ' . $username . '.<br /><i>(<a href="../">Return</a> - <a href="../logout.php">Log Out</a>)</i>';

?>
<html>
<head>
	<title><?php echo $_SESSION['Name']; ?> - Admin Control Panel</title>
	<link rel="stylesheet" href="admincp.css" type="text/css">
</head>

<body>
	
<h3><u>Mattbox AdminCP</u></h3>
Admin Control Panel<br />
==============<br />

<ul>
	<li><a href="tutorial.php">View Commands</a></li>
	<li><a href="users.php" >Manage Users</a></li>
	<li><a href="smilies.php" >Manage Smilies</a></li>
	<li><a href="usergroup.php" >Manage Usergroups</a></li>
</ul>
==============<br />
</body>
</html>
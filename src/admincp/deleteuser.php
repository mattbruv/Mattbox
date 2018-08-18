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

$query = mysql_query("SELECT * FROM user WHERE UserID ='$ID'");
$deleteUser = mysql_fetch_assoc($query);
$deleteName = $deleteUser['Name'];

if($ID == 1 || can_do_admin($deleteName))
{
	die('You do not have permission to delete administrators');
}

$username = style_user($_SESSION['Name']);
$rawname = $_SESSION['Name'];

if (isset($_POST['submit']))
{
	$query = mysql_query("DELETE FROM `user` WHERE `user`.`UserID` = '$ID'");
	$query2 = mysql_query("DELETE FROM `shouts` WHERE `User`='$ID'");
	log_event($rawname . ' has deleted ' . $deleteName . ' from the database');
	header('location: success.php');
	exit;
}

echo 'Welcome, ' . $username . '.<br /><i>(<a href="users.php">Users</a> - <a href="../logout.php">Log Out</a>)</i>';

?>
<html>
<head>
	<title><?php echo $_SESSION['Name']; ?> - Admin Control Panel</title>
	<link rel="stylesheet" href="admincp.css" type="text/css">
</head>

<body>
	
<h3><u>Mattbox AdminCP</u></h3>
Delete User: <?php echo style_user($deleteName); ?><br />
==============<br /><br />
<span id="list" style="color:red"><b>*** WARNING ***</b></span><br />
You are about to delete <?php echo $deleteName; ?> from the database.<br />
<?php echo $deleteName; ?> will be lost forever. Continue? <br />
<form method="POST" action="deleteuser.php?id=<?php echo $ID; ?>">
<input type="submit" name="submit" value="Continue"/></form>
</body>
</html>
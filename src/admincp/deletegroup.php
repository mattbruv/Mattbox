<?php

include('header.php');
$rawname = $_SESSION['Name'];

if(isset($_GET['id']))
{
	$ID = (int)$_GET['id'];
}
else {
	die('Invalid Usergroup ID');
}

if(!group_id_exists($ID))
{
	die('Invalid Usergroup ID');
}

if($ID == 1 || $ID == 2)
{
	die('Cannot delete this Usergroup. You can however change it');
}

$query = mysql_query("SELECT * FROM usergroup WHERE UsergroupID ='$ID'");
$group = mysql_fetch_assoc($query);
$group_name = style_group($ID, $group['Name']);

$query22 = mysql_query("SELECT COUNT(UserID) FROM user WHERE UsergroupID = '$ID'");
$count = mysql_result($query22, 0);

if ($count == 1)
{
	$note = '1 user';
	$grammar = 'is';
}
else
{
	$note = $count . ' users';
	$grammar = 'are';
}

if(isset($_POST['submit']))
{
	if($count > 0)
	{
		mysql_query("UPDATE `user` SET `UsergroupID`='2' WHERE `UsergroupID`='$ID'");
	}
	mysql_query("DELETE FROM `usergroup` WHERE `UsergroupID`='$ID'");
	header('location: success.php');
	exit;
}

?>
<html>
<head>
	<title><?php echo $_SESSION['Name']; ?> - Admin Control Panel</title>
	<link rel="stylesheet" href="admincp.css" type="text/css">
</head>

<body>
	
<h3><u>Mattbox AdminCP</u></h3>
Delete Usergroup: <?php echo $group_name; ?><br />
==============<br /><br />
<span id="list" style="color:red"><b>*** WARNING ***</b></span><br />
You are about to delete the usergroup: <?php echo $group_name; ?> from the database.<br />
<?php if($count != 0) { ?>
There <?php echo $grammar; ?> currently <?php echo $note; ?> in the group that will be affected by this.<br />
They will be moved to the default group <?php echo style_group(2, 'Member'); ?> upon deletion.<br />
<?php echo $group['Name']; ?> will be lost forever. <br />
<?php } ?>
Continue?
<form method="POST" action="deletegroup.php?id=<?php echo $ID; ?>">
<input type="submit" name="submit" value="Continue"/></form>
</body>
</html>
<?php

include('header.php');
$rawname = $_SESSION['Name'];

if(isset($_GET['id']))
{
	$ID = (int)$_GET['id'];
}
else {
	die('Invalid Smiley ID');
}

if(!smiley_id_exists($ID))
{
	die('Invalid Smiley ID');
}

if($ID == 1)
{
	die('Cannot delete this smiley. You can however change it');
}

log_event($rawname . ' deleted smiley id: ' . $ID);
$query = mysql_query("DELETE FROM `smilies` WHERE `SmileyID` = '$ID'");
header('location: success.php');
exit;

?>

<html>
<head>
	<title><?php echo $_SESSION['Name']; ?> - Admin Control Panel</title>
	<link rel="stylesheet" href="admincp.css" type="text/css">
</head>

<body>
	
<h3><u>Mattbox AdminCP</u></h3>
Delete Usergroup: <?php echo style_user($deleteName); ?><br />
==============<br /><br />
<span id="list" style="color:red"><b>*** WARNING ***</b></span><br />
You are about to delete <?php echo $deleteName; ?> from the database.<br />
<?php echo $deleteName; ?> will be lost forever. Continue? <br />
<form method="POST" action="deleteuser.php?id=<?php echo $ID; ?>">
<input type="submit" name="submit" value="Continue"/></form>
</body>
</html>
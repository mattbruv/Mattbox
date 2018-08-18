<?php

include('header.php');
$username = style_user($_SESSION['Name']);
$rawname = $_SESSION['Name'];
$id = fetch_id($rawname);

if(isset($_POST['submit']))
{
	$smileyName = $_POST['name'];
	$smileyCode = $_POST['code'];
	$smileyURL = $_POST['URL'];
	
	if($smileyCode != '' && $smileyName != '' && $smileyURL != '')
	{
		$query = mysql_query("SELECT * FROM smilies WHERE Code='$smileyCode'");
		$check = mysql_num_rows($query);
		
		if($check == 0)
		{
			$type_array = array('.gif', '.png', '.jpg', '.bmp');
			$type = substr($smileyURL, -4);
			$filename = strtolower(str_replace(' ', '', $smileyName));
			$input = $smileyURL;
			$output = 'images/smilies/' . $filename . $type;
			$path = '../' . $output;
			
			if(in_array($type, $type_array))
			{
				file_put_contents($path, file_get_contents($input));
				$errors = 'Something went horribly wrong';
				mysql_query("INSERT INTO `smilies`(`SmileyID`, `Name`, `URL`, `Code`, `Uploaded`) VALUES ('', '$smileyName', '$output', '$smileyCode', '$id')");
				log_event($rawname . ' uploaded a new smiley: ' . $smileyCode);
				header('location: success.php');
				exit;
			}
			else
			{
				$errors = 'Invalid File Type. Check end of the URL for either .gif, .jpg, .png or .bmp';
			}
		}
		else
		{
			$errors = 'Code already exists, pick a new one';
		}
	}
	else
	{
		$errors = 'Please enter all information.';
	}
}

?>
<html>
<head>
	<title><?php echo $_SESSION['Name']; ?> - Admin Control Panel</title>
	<link rel="stylesheet" href="admincp.css" type="text/css">
</head>

<body>
<?php echo 'Welcome, ' . $username . '.<br /><i>(<a href="smilies.php">Smilies</a> - <a href="../logout.php">Log Out</a>)</i>'; ?>
<h3><u>Mattbox AdminCP</u></h3>
<script type="text/javascript">
	function tellName()
	{
		alert('The name of the smiley.\r\nThis is what will appear when you hover over it');
	}
	function tellCode()
	{
		alert('The code for the smiley\r\ne.g Forever alone is :fa:');
	}
	function tellURL()
	{
		alert('The website URL to where the image is directly hosted\r\nUpon submission, the image will be locally saved\r\nDirect Uploader coming soon.');
	}
</script>
AdminCP - Add Smiley<br />
==============<br />

<form action="addsmiley.php" method="POST">
<table>
	<tr>
		<td>Smiley Name: </td><td><input type="text" name="name" /> (<a href="#" onclick="tellName()">?</a>)</td>
	</tr>
	<tr>
		<td>Smiley Code: </td><td><input type="text" name="code" /> (<a href="#" onclick="tellCode()">?</a>)</td>
	</tr>
	<tr>
		<td>Smiley URL: </td><td><input type="text" name="URL" /> (<a href="#" onclick="tellURL()">?</a>)</td>
	</tr>
</table>
<input type="submit" name="submit" value="Add Smiley" />
</form>
<?php if(isset($errors)) { echo $errors; } ?><br />
==============

</body>
</html>
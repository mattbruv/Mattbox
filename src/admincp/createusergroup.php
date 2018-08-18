<?php
include('header.php');
$username = style_user($_SESSION['Name']);
$rawname = $_SESSION['Name'];

if(isset($_POST['submit']))
{
	$new_name = (isset($_POST['name'])) ? filter($_POST['name']) : '';
	$color = (isset($_POST['color'])) ? $_POST['color'] : '';
	$perms = (isset($_POST['perms'])) ? filter($_POST['perms']) : 'Standard';
	$background = (isset($_POST['background'])) ? filter($_POST['background']) : '';
	$bold = (isset($_POST['bold']) && $_POST['bold'] == 'Yes') ? 1 : 0;
	$italic = (isset($_POST['italic']) && $_POST['italic'] == 'Yes') ? 1 : 0;
	$errors = array();
	if($perms == 'Standard')
	{
		$admin = 0;
		$mod = 0;
	}
	else if ($perms == 'Mod')
	{
		$admin = 0;
		$mod = 1;
	}
	else if ($perms == 'Admin')
	{
		$admin = 1;
		$mod = 0;
	}
	if($new_name == '')
	{
		$errors[] = 'Please Enter a Name';
	}
	if(!preg_match('/#?[0-9A-Fa-f]{6}/', $color))
	{
		$errors[] = 'Please Enter a valid 6 Digit Hex Color';
	}
	if($new_name != '')
	{
		$query = mysql_query("SELECT * FROM usergroup WHERE Name = '$new_name'");
		$result = mysql_num_rows($query);
		if($result > 0)
		{
			$errors[] = 'Name Taken. Choose a different usergroup name';
		}
	}
	
	// $type_array = array('.gif', '.png', '.jpg', '.bmp');
	// $type = substr($background, -4);
	// $filename = strtolower(str_replace(' ', '', $new_name));
	// $input = $background;
	// $output = 'images/backgrounds/' . $filename . $type;
	// $path = '../' . $output;
// 	
	// if(in_array($type, $type_array))
	// {
		// file_put_contents($path, file_get_contents($input));
		// log_event($rawname . ' uploaded a new background: ' . $new_name);
	// }
	// else
	// {
		// $errors[] = 'Invalid File Type. Check end of the URL for either .gif, .jpg, .png or .bmp';
	// }
	
	if (empty($errors))
	{
		mysql_query("INSERT INTO `usergroup`(`UsergroupID`, `Name`, `Administrator`, `Moderator`, `Bold`, `Italic`, `Color`, `Background`) VALUES ('', '$new_name', '$admin', '$mod', '$bold', '$italic', '$color', '$background')");
		log_event($rawname . ' created group ' . $new_name);
		header('location: success.php');
		exit;
	}
}

?>
<html>
<head>
<title><?php echo $_SESSION['Name']; ?> - Admin Control Panel</title>
<link rel="stylesheet" href="admincp.css" type="text/css">
<script type="text/javascript">

function colorMe(value)
{
	string = value.value;
	preview = document.getElementById('preview');
	input = document.getElementById('color');
	pat = /#?[0-9A-Fa-f]{6}/g;
	condition = pat.test(string);
	
	if(value.value.length > 7)
	{
		input.value = value.value.substr(0,7);
	}
	
	if(condition)
	{
		preview.style.color = string;
		input.style.background = '';
	}
	else
	{
		preview.style.color = '';
		input.style.background = '#FF9999';
	}
}

function setBold(value)
{
	preview = document.getElementById('preview');
	if(value.value == 'Yes')
	{
		preview.style.fontWeight = 'bold';
	}
	else {
		preview.style.fontWeight = '';
	}
}

function setItalic(value)
{
	preview = document.getElementById('preview');
	if(value.value == 'Yes')
	{
		preview.style.fontStyle = 'italic';
	}
	else {
		preview.style.fontStyle = '';
	}
}

function setBackground(value)
{
	preview = document.getElementById('preview');
	preview.style.backgroundImage = "url('" + value.value + "')";
}
	
</script>
</head>

<body>
<?php echo 'Welcome, ' . $username . '.<br /><i>(<a href="usergroup.php">Usergroups</a> - <a href="../logout.php">Log Out</a>)</i>'; ?>
<h3><u>Mattbox AdminCP</u></h3>

AdminCP - Create Usergroup<br />
==============<br /><br />
Preview: <span id="preview"><?php echo $_SESSION['Name']; ?></span><br /><br />
<form action="createusergroup.php" method="POST">
<table>
	<tr>
		<td>Group Name:</td><td><input type="text" name="name"/></td>
	</tr>
	<tr>
		<td>Color (Hex):</td><td><input id="color" type="text" onkeyup="colorMe(this)" name="color" value="#000000"/></td><td>(<a href="http://www.w3schools.com/html/html_colors.asp" target="_blank">Reference</a>)</td>
	</tr>
	<tr>
		<td>Permissions:</td>
		<td>
			<select name="perms">
				<option value="Standard">Standard (None)</option>
				<option value="Mod">Moderator (Few)</option>
				<option value="Admin">Administrator (All)</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Background:</td><td><input type="text" onkeyup="setBackground(this)" name="background" /></td><td>(Optional)</td>
	</tr>
	<tr>
		<td><b>Bold</b>:</td>
	</tr>
	<tr>
		<td>
			<input type="radio" onchange="setBold(this)" name="bold" value="Yes" /> Yes
		</td>
		<td>
			<input type="radio" onchange="setBold(this)" name="bold" value="No" /> No
		</td>
	</tr>
	<tr>
		<td><i>Italic</i>:</td>
	</tr>
	<tr>
		<td>
			<input type="radio" onchange="setItalic(this)" name="italic" value="Yes" /> Yes
		</td>
		<td>
			<input type="radio" onchange="setItalic(this)" name="italic" value="No" /> No
		</td>
	</tr>
</table>
<input type="submit" name="submit" value="Create Group" />
</form><span style="color:red; font-weight: bold;">
<?php

if(!empty($errors))
{
	foreach($errors as $s)
	{
		echo $s . '<br />';
	}
}
?></span>
==============
</body>
</html>

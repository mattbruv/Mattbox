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
Viewing Commands<br />
==============<br />
<table border="1">
	<tr>
		<td><b>Command</b></td><td><b>Example</b></td><td><b>Description</b></td>
	</tr>
	<tr>
		<td>/notice [text]</td><td>/notice Hello!</td><td>Changes the Mattbox Notice (effected by BBcode)</td>
	</tr>
	<tr>
		<td>/removenotice</td><td>/removenotice</td><td>Deletes the Mattbox Notice</td>
	</tr>
	<tr>
		<td>/mattbot</td><td>/mattbot</td><td>Toggles the infamous Mattbot</td>
	</tr>
	<tr>
		<td>/happyhour</td><td>/happyhour</td><td>Toggles Happy Hour</td>
	</tr>
	<tr>
		<td>[IMG]Image URL[/IMG]</td><td>[IMG]http://i.imgur.com/ngCBV.gif[/IMG]</td><td>Displays an image.</td>
	</tr>
	<tr>
		<td>/say [user]; [message]</td><td>/say cody; I am gay!</td><td>Posts a shout as the specified user</td>
	</tr>
	<tr>
		<td>/ban [user]</td><td>/ban cody</td><td>Bans a user from the Mattbox</td>
	</tr>
	<tr>
		<td>/unban [user]</td><td>/unban cody</td><td>unbans a user from the Mattbox</td>
	</tr>
	<tr>
		<td>/silence [user]</td><td>/silence cody</td><td>silences a user from the Mattbox</td>
	</tr>
	<tr>
		<td>/unsilence [user]</td><td>/unsilence cody</td><td>unsilences a user from the Mattbox</td>
	</tr>
		<tr>
		<td>/prune [user]</td><td>/prune cody</td><td>Erases all shouts by Cody</td>
	</tr>
	<tr>
		<td>/prune</td><td>/prune</td><td>Erases the entire Mattbox</td>
	</tr>
</table>
==============<br />
</body>
</html>
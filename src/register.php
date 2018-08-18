

<html>

<head>
<link rel="stylesheet" type="text/css" href="loginstyle.css" />
<script type="text/javascript">
	function redirect()
	{
		window.location = "login.php";
	}
</script>
</head>

<body>

<?php

include('functions.php');
include('connect.php');

date_default_timezone_set('America/New_York');
$time = date('Y-m-d H:i:s', time());

//form data
$ip = $_SERVER['REMOTE_ADDR'];
$multi = mysql_query("SELECT Name FROM user WHERE IPAddress='$ip'");
$multiaccount = mysql_fetch_assoc($multi);

if ($multiaccount != 0) 
	
	{
		die ("Our records indicate you're already registered by the name of: </br> <b>" . $multiaccount['Name'] . '</b> <br /> <br /><a href="login.php">Log In</a>');
	}

if (isset($_POST['submit']))
{
	$username = filter($_POST['username']);
	
	if(!preg_match('/^(?=.{1,16}$)[a-zA-Z][a-zA-Z0-9]*(?: [a-zA-Z0-9]+)*$/', $username))
	{
		die ("Your username must be 3-16 characters.<br />Username can only include letters A-Z, spaces and numbers.<br />No Special characters (such as \$,#,%) Allowed.");
	}
	
	$password = filter($_POST['password']);
	$rpassword = filter($_POST['rpassword']);
	$ip = $_SERVER['REMOTE_ADDR'];

	$namecheck = mysql_query("SELECT Name FROM user WHERE Name='$username'");
	$count = mysql_num_rows($namecheck);
	
	$multi = mysql_query("SELECT Name FROM user WHERE IPAddress='$ip'");
	$multiaccount = mysql_fetch_assoc($multi);
	
	
	if ($count!=0)
	{
	
	die ("Username already taken.<br />");
	
	}

if($username&&$password&&$rpassword)
{
	if ($password==$rpassword)
	{
		//check username length
	if (strlen($username)>25)
	{
		echo "Length of username is too long<br />";
	}

	elseif (strlen($username)<3)
	{
		echo "Your username is too short!<br />";
	}

	else 
	{
		if (strlen($password)<4||strlen($password)>25)
		{
		echo "Password must be between 4 and 25 characters<br />";
		}

		else
		{
			echo "Success! ";
			$password = md5($password);
			
			$query = mysql_query("INSERT INTO `user` (`UserID` ,`Name` ,`Password` ,`UsergroupID` ,`Banned` ,`Silenced` ,`Bold` ,`Italic` ,`Underline` ,`Color` ,`Font` ,`Background` ,`IPAddress`)VALUES ('' , '$username', '$password', '2', '0', '0', '0', '0', '0', 'Default', 'Default', '', '$ip');");
			$ID = fetch_id($username);
			$string = mysql_real_escape_string("just registered and needs to be <a target='_blank' href='admincp/edituser.php?id=" . $ID . "'>approved or denied</a>.");
			
			mysql_query("INSERT INTO `shouts` (`ShoutID` ,`User` ,`Time` ,`Shout` ,`Me` ,`Private` ,`To`)VALUES ('' , '$ID', '$time', '$string', '1', '0', '');");
			echo ("You have been registered, click <a href='login.php'>here</a> to login!<br />");
		}

	}

}
	
				else
					echo ("Your passwords do not match<br />");
		
		}

	


else
	echo "Please fill out <b>all</b> fields<br />";



}



?>
<div id="header">
	Register
</div>
<div id="container">
<form action='register.php' method="POST">
	<table>
        <tr>
            <td>
            Username:
            </td>
            <td>
            <input type='text' name='username' value='<?php echo @$username;?>'>
            
            </td>
        </tr>
        <tr>
            <td>
            Password:
            </td>
            <td>
            <input type='password' name='password'>
            
            </td>
        </tr>
        <tr>
            <td>
            Repeat Password:
            </td>
            <td><input type='password' name='rpassword'></td>
        </tr>
        <tr>
        	<td><input type='submit' id="button" name='submit' value='Register'>&nbsp;<input type='button' id="button" onclick="redirect();" value='Log In'></td>
        </tr>
    </table>
    
</div>

	</body>

</html>
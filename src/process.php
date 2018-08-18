<?php

include('connect.php');
include('functions.php');

secureINI();
session_start();
secureSession();

$username = filter($_POST['username']);
$checkname = strtolower($username);
$password = filter($_POST['password']);

$errors = array();

if ($username && $password)
{
	
	// Fetch username
	$query = mysql_query("SELECT * FROM user WHERE Name='$username'");
	
	$numrows = mysql_num_rows($query);
	
	if ($numrows!=0)
	{
	
	    while ($row = mysql_fetch_assoc($query))
	    {
	        $dbusername = $row['Name'];
			$dbnametest = strtolower($dbusername);
	        $dbpassword = $row['Password'];
	    }
	        //check to see if provided username and password match username and password in the database
	        
	        if ($checkname == $dbnametest && md5($password)==$dbpassword)
	        {
	        	$username = $dbusername;
	        	if(isset($_POST['remember']))
				{
					setcookie("Remember", 1, time() + (20 * 365 * 24 * 60 * 60));
					setcookie("Name", $username, time() + (20 * 365 * 24 * 60 * 60));
					$_SESSION['Name'] = $username;
					set_login_time($username);
					header('location: index.php');
					exit;
				}
				else
				{
					setcookie("Remember", 0, time() + 3600);
					setcookie("Name", $username, time() + 3600);
					$_SESSION['Name'] = $username;
					set_login_time($username);
					header('location: index.php');
					exit;
				}
	
	        }
			else if (md5($password) != $dbpassword)
			{
				$errors[] = "You have entered an incorrect password";
			}
	        else {
	            $errors[] = "Correct Password, however the username is case sensitive.";
			}
	}
	else
	    $errors[] = "That user doesn't exist!";
	
	
	
	}
else {
    $errors[] = "Please enter a username and password to continue.";
}

?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="loginstyle.css" />
</head>
<body>
<div id='main'>
<?php if(!empty($errors)) {
	foreach ($errors as $s)
	{
		echo $s . '<br />';
	}
}
?>
</div>
</body>
</html>
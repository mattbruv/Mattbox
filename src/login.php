<?php
	$bool = false;
	if (isset($_POST['username']))
	{
		$username = $_POST['username'];
		$bool = true;
	}
?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="loginstyle.css" />
<script type="text/javascript">
	function redirect()
	{
		window.location = "register.php";
	}
</script>
</head>
<body>
	<div id="header">
		Log In
	</div>
	<div id="container">
	    <form action="process.php" name='login' method='POST'>
	    	<table id="table">
	    		<tr>
	    			<td><label for="username" >Username: </label></td><td><input type="text" name="username" value="<? if($bool) { echo $username; } ?>"><br /></td>
	    		</tr>
	    		<tr>
	    			<td><label for="password" >Password: </label></td><td><input type="password" name="password"><br /></td>
	    		</tr>
	    		<tr>
	    			<td><input type="checkbox" value="remember" name="remember" /> Remember Me</td>
	    		</tr>
	    		<tr>
	    			<td><input type="submit" id="button" value="Log In" />&nbsp;<input type="button" onclick="redirect();" id="button" href='register.php' value="Register" /></td>
	    		</tr>
	    	</table>
	    </form> 
	</div>
</body>
</html>

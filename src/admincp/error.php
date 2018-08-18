<?php

include('header.php');

?>
<html>
<head>
	<title><?php echo $_SESSION['Name']; ?> - Admin Control Panel</title>
	<link rel="stylesheet" href="admincp.css" type="text/css">
</head>

<body>
	
<h3><u>Mattbox AdminCP</u></h3>
<script type="text/javascript">
function delayer()
{
    window.location = "index.php";
}
</script>
<body onLoad="setTimeout('delayer()', 6000)">
	<span id="list" style="color:red"><b>ERROR:</b></span><br />
		There was an error with one of the pieces of information provided.
		<br />That piece of information has not been changed.
		<br /><br />
		Redirecting...
	</body>
</html>
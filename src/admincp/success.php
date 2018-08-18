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
<body onLoad="setTimeout('delayer()', 2000)">
		Settings Changed Succesfully
		<br />
		Redirecting...
	</body>
</html>
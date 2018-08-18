<?php
	include('functions.php');
	include('connect.php');
	
	if (empty($_COOKIE['Name']))
	{
		header("location:login.php");
		exit;
	}
	
	$totalShouts = get_statistic_shouts();
	$totalUsers = get_statistic_users();
	$activeUsers = fetch_active_user_number();
	$topShouters = get_statistic_top_shouters();
?>

<html>
	<head>
		<title><? echo SITENAME; ?> - statistics</title>
		<link rel="stylesheet" type="text/css" href="loginstyle.css" />
		<style type="text/css">
			#small {
				padding-left:5px;
				font-style:italic;
				font-variant:small-caps;
				font-size:12px
			}
			#active {
				font-variant:small-caps;
				font-size:18px;
			}
		</style>
	</head>
	
	<body>
		<div id="header">
			<? echo SITENAME; ?> Statistics
		</div>
		<div id="container">
			<table id="table">
				<tr>
					<td>Total Shouts:</td><td><? echo $totalShouts; ?></td>
				</tr>
				<tr>
					<td>Total Users:</td><td><? echo $totalUsers; ?></td>
				</tr>
				<tr>
					<td>Active Users:</td><td><? echo $activeUsers; ?></td>
				</tr>
				</table>
				<hr />
				<center><b id="active">Most Active Users</b></center>
				<table cellspacing="10">
					<tr>
						<td>Rank:</td><td>Username:</td><td>Total Shouts:</td>
					</tr>
					<?php
						$i = 1;
						foreach ($topShouters as $key)
						{
							$percentage = round(($key[1] / $totalShouts) * 100, 2);
							echo '<tr>';
							echo '<td align="center">' . $i . '</td><td>' . $key[0] . '</td><td>' . $key[1] . '<span id="small">(' . $percentage . '%)</span></td>';
							echo '</tr>';
							$i++;
						}
					?>
						
				</table>
				<input type="button" id="button" onclick="window.location = 'index.php';" value="Return to Shoutbox" /><br /><br />
		</div>
	</body>
</html>
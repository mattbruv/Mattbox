<?php

// connection information: Keep your information between the two apostrophes.
// $db_server = 'localhost'; will work, while $db_server = localhost; will not.

// your database server
$db_server = 'localhost';

// your database username
$db_user = 'root';

// your database password
$db_pass = '';

// the name of your database, set in the installation script
$db_name = 'fixedbox';

mysql_connect($db_server, $db_user, $db_pass);

if(!empty($db_name)) {
	mysql_select_db($db_name);
}


?>
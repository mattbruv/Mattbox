<?php

include('connect.php');
include('functions.php');

$username = filter($_SESSION['Name']);
set_login_time($username);

echo 'Currently Active Shoutbox Users: ' . fetch_active_user_number() . '<br />';
echo fetch_active_users(true);

?>
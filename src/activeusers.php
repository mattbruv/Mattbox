<?php

include('connect.php');
include('functions.php');

echo json_encode(fetch_active_user_number(true));

?>

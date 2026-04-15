<?php

include "config/db.php";

$id = $_GET['id'];

$result = mysqli_query(
    $conn,
    "SELECT is_active FROM users WHERE user_id='$id'"
);

$row = mysqli_fetch_assoc($result);

$new_status = ($row['is_active'] == 1) ? 0 : 1;

mysqli_query(
    $conn,
    "UPDATE users SET is_active='$new_status' WHERE user_id='$id'"
);

header("Location: user_managment.php");

exit();

<?php

include "config/db.php";

$id = $_GET['id'];

mysqli_query(
    $conn,
    "DELETE FROM users WHERE user_id='$id'"
);

header("Location: user_managment.php");

exit();

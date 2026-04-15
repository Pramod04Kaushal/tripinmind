<?php

include "config/db.php";

$id = $_GET['id'];

mysqli_query(
    $conn,
    "DELETE FROM location WHERE location_id='$id'"
);

header("Location: manage_location.php");

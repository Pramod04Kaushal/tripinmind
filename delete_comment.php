<?php

session_start();
include "config/db.php";

$comment_id = intval($_POST['comment_id']);

/* Get location id before deleting */

$get = mysqli_query(
    $conn,
    "SELECT location_id FROM comment WHERE comment_id='$comment_id'"
);

$row = mysqli_fetch_assoc($get);
$location_id = $row['location_id'];

/* Delete images first */

mysqli_query(
    $conn,
    "DELETE FROM comment_images WHERE comment_id='$comment_id'"
);

/* Delete comment */

mysqli_query(
    $conn,
    "DELETE FROM comment WHERE comment_id='$comment_id'"
);

/* Redirect back to location */

header("Location: location_details.php?id=" . $location_id);
exit();

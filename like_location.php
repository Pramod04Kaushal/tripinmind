<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$location_id = intval($_POST['location_id']);

/* Check if already liked */

$check = mysqli_query(
    $conn,
    "SELECT * FROM likes 
WHERE user_id='$user_id' AND location_id='$location_id'"
);

if (mysqli_num_rows($check) > 0) {

    /* UNLIKE */
    mysqli_query(
        $conn,
        "DELETE FROM likes 
    WHERE user_id='$user_id' AND location_id='$location_id'"
    );
} else {

    /* LIKE */
    mysqli_query(
        $conn,
        "INSERT INTO likes (user_id, location_id)
    VALUES ('$user_id','$location_id')"
    );
}

header("Location: location_details.php?id=$location_id");
exit();

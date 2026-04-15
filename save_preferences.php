<?php

session_start();
include "config/db.php";

$user_id = $_SESSION['user_id'];

/* FIX undefined errors */
$receive_email = $_POST['receive_email'] ?? "";
$preferred_category = $_POST['preferred_category'] ?? "";
$preferred_trip_type = $_POST['preferred_trip_type'] ?? "";

/* check if already saved */

$check = mysqli_query(
    $conn,
    "SELECT * FROM user_preferences
     WHERE user_id='$user_id'"
);

if (mysqli_num_rows($check) > 0) {

    mysqli_query(
        $conn,
        "UPDATE user_preferences SET
        receive_email='$receive_email',
        preferred_category='$preferred_category',
        preferred_trip_type='$preferred_trip_type'
        WHERE user_id='$user_id'"
    );
} else {

    mysqli_query(
        $conn,
        "INSERT INTO user_preferences
        (user_id,receive_email,preferred_category,preferred_trip_type)
        VALUES
        ('$user_id','$receive_email','$preferred_category','$preferred_trip_type')"
    );
}

header("Location: profile.php");
exit();

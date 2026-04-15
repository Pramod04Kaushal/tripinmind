<?php

session_start();
include "config/db.php";

$user_id = $_SESSION['user_id'];
$location_id = $_POST['location_id'];

/* check already liked */

$check = mysqli_query(
    $conn,
    "SELECT * FROM favorites
WHERE user_id='$user_id'
AND location_id='$location_id'"
);

if (mysqli_num_rows($check) > 0) {

    /* remove like */

    mysqli_query(
        $conn,
        "DELETE FROM favorites
WHERE user_id='$user_id'
AND location_id='$location_id'"
    );
} else {

    /* add like */

    mysqli_query(
        $conn,
        "INSERT INTO favorites
(user_id,location_id)
VALUES
('$user_id','$location_id')"
    );
}

header("Location: " . $_SERVER['HTTP_REFERER']);

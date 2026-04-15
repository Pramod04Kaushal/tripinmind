<?php

session_start();
include "config/db.php";

$user_id = $_SESSION['user_id'];
$comment_id = intval($_POST['comment_id']);

$check = mysqli_query(
    $conn,
    "SELECT * FROM comment_likes
WHERE user_id='$user_id' AND comment_id='$comment_id'"
);

if (mysqli_num_rows($check) > 0) {

    mysqli_query(
        $conn,
        "DELETE FROM comment_likes
     WHERE user_id='$user_id' AND comment_id='$comment_id'"
    );
} else {

    mysqli_query(
        $conn,
        "INSERT INTO comment_likes (user_id,comment_id)
     VALUES ('$user_id','$comment_id')"
    );
}

$count = mysqli_query(
    $conn,
    "SELECT COUNT(*) as total FROM comment_likes
WHERE comment_id='$comment_id'"
);

$row = mysqli_fetch_assoc($count);

echo $row['total'];

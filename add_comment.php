<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$location_id = intval($_POST['location_id']);
$content = htmlspecialchars($_POST['content']);

/* Insert comment first */

mysqli_query(
    $conn,
    "INSERT INTO comment (user_id, location_id, content)
    VALUES ('$user_id','$location_id','$content')"
);

/* Get the comment ID */

$comment_id = mysqli_insert_id($conn);


/* IMAGE UPLOAD */

if (!empty($_FILES['images']['name'][0])) {

    if (!file_exists("uploads/comments")) {
        mkdir("uploads/comments", 0777, true);
    }

    foreach ($_FILES['images']['tmp_name'] as $key => $tmp) {

        $image_name = time() . "_" . $_FILES['images']['name'][$key];

        $target = "uploads/comments/" . $image_name;

        move_uploaded_file($tmp, $target);

        mysqli_query(
            $conn,
            "INSERT INTO comment_images (comment_id, image_url)
            VALUES ('$comment_id','$target')"
        );
    }
}

echo '
<div class="comment-box">

<div class="comment-avatar">
<i class="fa-solid fa-user"></i>
</div>

<div class="comment-content">

<div class="comment-bubble">
<strong>' . $_SESSION['username'] . '</strong>
<p>' . $content . '</p>';

if (!empty($_FILES['images']['name'][0])) {

    echo '<div class="comment-image-grid images-' . count($_FILES['images']['name']) . '">';

    foreach ($_FILES['images']['name'] as $key => $name) {

        $image_name = time() . '_' . $name;

        echo '<img src="uploads/comments/' . $image_name . '" class="comment-image">';
    }

    echo '</div>';
}

echo '

</div>

<div class="comment-actions">

<span class="comment-like" data-id="' . $comment_id . '">
❤️ <span class="like-count">0</span>
</span>

<span class="reply-btn" onclick="toggleReply(' . $comment_id . ')">
Reply
</span>

</div>

</div>

</div>';

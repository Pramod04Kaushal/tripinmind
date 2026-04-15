<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$location_id = intval($_POST['location_id']);
$parent_id = intval($_POST['parent_id']);
$content = $_POST['content'];

mysqli_query(
    $conn,
    "INSERT INTO comment (user_id, location_id, content, parent_id)
VALUES ('$user_id','$location_id','$content','$parent_id')"
);

echo '

<div class="reply-box">

<div class="reply-avatar">
<i class="fa-solid fa-user"></i>
</div>

<div class="reply-content">

<div class="reply-bubble">
<strong>' . $_SESSION['username'] . '</strong>
<p>' . $content . '</p>
</div>

</div>

</div>

';
exit();

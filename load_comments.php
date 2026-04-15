<?php

include "config/db.php";

$location_id = intval($_POST['location_id']);
$offset = intval($_POST['offset']);

$limit = 5;

$comments = mysqli_query(
    $conn,
    "SELECT comment.*, users.username
FROM comment
JOIN users ON comment.user_id = users.user_id
WHERE location_id='$location_id'
AND parent_id IS NULL
ORDER BY created_at DESC
LIMIT $limit OFFSET $offset"
);

$count = mysqli_num_rows($comments);

while ($row = mysqli_fetch_assoc($comments)) {

    echo '
<div class="comment-box">

<div class="comment-avatar">
<i class="fa-solid fa-user"></i>
</div>

<div class="comment-content">

<div class="comment-bubble">
<strong>' . $row['username'] . '</strong>
<p>' . $row['content'] . '</p>
</div>

</div>

</div>
';
}

/* send signal if no more comments */
if ($count < $limit) {
    echo '<span id="noMoreComments" style="display:none;"></span>';
}

<?php

include "config/db.php";

$id = $_GET['id'];

mysqli_query($conn, "
DELETE FROM location_media 
WHERE media_id='$id'
");

header("Location: " . $_SERVER['HTTP_REFERER']);

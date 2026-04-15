<?php

include "config/db.php";

$category_id = $_GET['category_id'];

$sub_filter = "";

if (isset($_GET['sub_id'])) {

    $sub_id = $_GET['sub_id'];

    $sub_filter = "AND location.location_id IN (
        SELECT location_id
        FROM location_subcategory
        WHERE sub_id='$sub_id'
    )";
}

$query = "

SELECT location.location_id,
location.location_name,
location.description,
province.province_name,
location_media.media_url

FROM location

LEFT JOIN province
ON location.province_id = province.province_id

LEFT JOIN location_media
ON location.location_id = location_media.location_id

WHERE location.category_id='$category_id'
AND location.is_active=1
$sub_filter

GROUP BY location.location_id

";

$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {

    echo '

<a href="location_details.php?id=' . $row['location_id'] . '" class="location-card">

<img src="' . $row['media_url'] . '">

<div class="location-content">

<h3>' . $row['location_name'] . '</h3>

<p class="province">' . $row['province_name'] . '</p>

<p class="description">' . substr($row['description'], 0, 100) . '...</p>

</div>

</a>

';
}

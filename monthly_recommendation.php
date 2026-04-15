<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "config/db.php";

if (!isset($_SESSION['user_id'])) {
    echo "Login required";
    exit();
}

$user_id = $_SESSION['user_id'];

/* get user preferences */

$pref = mysqli_query(
    $conn,
    "SELECT * FROM user_preferences
WHERE user_id='$user_id'"
);

$preferences = mysqli_fetch_assoc($pref);

/* current month number (1-12) */

$current_month = date("n");

/* build filters */

$category_filter = "";

if (!empty($preferences['preferred_category'])) {

    $category_filter =
        "AND location.category_id='" . $preferences['preferred_category'] . "'";
}


/* trip type filter */

$trip_filter = "";

if ($preferences['preferred_trip_type'] == "one_day") {

    $trip_filter = "AND location.is_one_day_trip = 1";
}


/* main recommendation query */

$query = "

SELECT

location.location_id,
location.location_name,
location.description,

(SELECT media_url
 FROM location_media
 WHERE location_media.location_id = location.location_id
 LIMIT 1) AS media_url

FROM location

JOIN location_season
ON location.location_id = location_season.location_id

WHERE location_season.season_id = '$current_month'

AND location.is_active = 1

$category_filter

$trip_filter

LIMIT 6

";

$result = mysqli_query($conn, $query);

?>

<h3>
    Recommended for <?php echo date("F"); ?>
</h3>

<div class="recommendations-container profile-recommend">

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>

        <a href="location_details.php?id=<?php echo $row['location_id']; ?>"

            class="recommend-card profile-card">

            <img src="<?php echo !empty($row['media_url']) ? $row['media_url'] : 'images/default.jpg'; ?>">

            <div class="recommend-overlay">

                <h4><?php echo $row['location_name']; ?></h4>

            </div>

        </a>

    <?php } ?>

</div>
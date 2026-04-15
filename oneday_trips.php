<?php
session_start();
include "config/db.php";

/* Get one day trips */

$trip_query = "

SELECT 
l.location_id,
l.location_name,
l.description,
p.province_name,
lm.media_url

FROM location l

LEFT JOIN province p
ON l.province_id = p.province_id

LEFT JOIN location_media lm
ON l.location_id = lm.location_id

WHERE l.is_one_day_trip = 1
AND l.is_active = 1

GROUP BY l.location_id

";

$trip_result = mysqli_query($conn, $trip_query);

?>

<!DOCTYPE html>
<html>

<head>

    <title>One Day Trips</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="navbar.css">

    <link rel="stylesheet" href="location_card.css">

</head>

<body class="category-locations-page">

    <?php include "navbar.php"; ?>


    <h2 class="section-title category-page-title">
        Explore One Day Trips
    </h2>


    <div class="cat-grid">

        <?php while ($row = mysqli_fetch_assoc($trip_result)) { ?>

            <a href="location_details.php?id=<?php echo $row['location_id']; ?>"
                class="cat-card">

                <img src="<?php echo $row['media_url']; ?>">

                <div class="cat-content">

                    <h3>
                        <?php echo $row['location_name']; ?>
                    </h3>

                    <p class="cat-province">
                        <?php echo $row['province_name']; ?>
                    </p>

                    <p class="cat-description">

                        <?php echo substr($row['description'], 0, 120) . '...'; ?>

                    </p>

                </div>

            </a>

        <?php } ?>

    </div>


    <?php include "auth_popup.php"; ?>
    <script src="auth.js"></script>

</body>

</html>
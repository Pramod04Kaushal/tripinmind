<?php
session_start();
include "config/db.php";

/* Get category id */

$category_id = $_GET['category_id'];


/* Sub category filter logic */

$sub_filter = "";

if (isset($_GET['sub_id'])) {

    $sub_id = $_GET['sub_id'];

    $sub_filter = "AND location.location_id IN (
        SELECT location_id
        FROM location_subcategory
        WHERE sub_id='$sub_id'
    )";
}


/* Get category name */

$category_query = "
SELECT category_name
FROM category
WHERE category_id='$category_id'
";

$category_result = mysqli_query($conn, $category_query);

$category = mysqli_fetch_assoc($category_result);

$category_name = $category['category_name'];


/* Get sub categories of this category */

$sub_query = "
SELECT *
FROM sub_category
WHERE category_id='$category_id'
";

$sub_result = mysqli_query($conn, $sub_query);


/* Get locations */

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


/* Active button highlight */

$active_sub = isset($_GET['sub_id']) ? $_GET['sub_id'] : "all";

?>

<!DOCTYPE html>
<html>

<head>
    <title>Locations</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="location_card.css">

</head>

<body class="category-locations-page">
    <?php include "navbar.php"; ?>



    <h2 class="section-title category-page-title">
        Explore <?php echo $category_name; ?> Destinations
    </h2>
    <div class="sub-category-wrapper">
        <div class="sub-category-filter">

            <a href="#"
                class="<?php echo ($active_sub == 'all') ? 'active' : ''; ?>"
                onclick="filterLocations('all')">
                ALL
            </a>

            <?php while ($sub = mysqli_fetch_assoc($sub_result)) { ?>

                <a href="#"
                    class="<?php echo ($active_sub == $sub['sub_id']) ? 'active' : ''; ?>"
                    onclick="filterLocations(<?php echo $sub['sub_id']; ?>)">
                    <?php echo $sub['sub_name']; ?>
                </a>

            <?php } ?>

        </div>
    </div>

    <div class="cat-grid" id="locationsContainer">

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

            <a href="location_details.php?id=<?php echo $row['location_id']; ?>" class="cat-card">

                <img src="<?php echo $row['media_url']; ?>">

                <div class="cat-content">

                    <h3><?php echo $row['location_name']; ?></h3>

                    <p class="cat-province">
                        <?php echo $row['province_name']; ?>
                    </p>

                    <p class="cat-description">
                        <?php echo substr($row['description'], 0, 100); ?>...
                    </p>

                </div>

            </a>

        <?php } ?>

    </div>

    <!-- Sub categegory filter without reloading page -->
    <script>
        function filterLocations(sub_id) {

            let category_id = <?php echo $category_id; ?>;

            let url = "fetch_locations.php?category_id=" + category_id;

            if (sub_id !== 'all') {

                url += "&sub_id=" + sub_id;

            }

            fetch(url)

                .then(response => response.text())

                .then(data => {

                    document.getElementById("locationsContainer").innerHTML = data;

                })

        }

        /* auto load ALL */
        window.onload = function() {

            filterLocations('all');

        }
    </script>

</body>

</html>
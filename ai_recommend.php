<?php
session_start();
include "config/db.php";
?>

<!DOCTYPE html>
<html>

<head>

    <title>AI Travel Recommendation</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="ai_recommend.css">

</head>

<body class="ai-page">

    <?php include "navbar.php"; ?>

    <!-- AUTH POPUP -->
    <?php include "auth_popup.php"; ?>

    <div class="section-content ai-page">

        <h1>AI Travel Recommendation</h1>

        <p>Describe your ideal place</p>

        <form method="POST">

            <input type="text"
                name="user_text"
                placeholder="Example: calm nature place for weekend"
                style="width:100%; padding:12px; margin-top:10px;">

            <br><br>

            <button class="ai-btn">Get Suggestion</button>

        </form>


        <?php

        if (isset($_POST['user_text'])) {

            $text = strtolower(trim($_POST['user_text']));

            /* convert plural words to singular */

            $text = str_replace("waterfalls", "waterfall", $text);
            $text = str_replace("temples", "temple", $text);
            $text = str_replace("beaches", "beach", $text);
            $text = str_replace("mountains", "mountain", $text);
            $text = str_replace("historical sites", "historical", $text);

            /* words that should be ignored */

            $stop_words = [

                "place",
                "places",
                "trip",
                "visit",
                "visiting",
                "location",
                "spot",
                "destination",
                "area",
                "best",
                "good",
                "nice"

            ];

            /* break sentence */

            $words = explode(" ", $text);

            /* remove weak words */

            $filtered_words = [];

            foreach ($words as $word) {

                if (!in_array($word, $stop_words)) {

                    $filtered_words[] = $word;
                }
            }

            /* build smart search */

            $search_query = "";

            foreach ($filtered_words as $word) {

                $search_query .= $word . "* ";
            }

            /* FILTERS */

            $filters = [];

            /* meaning keywords */

            if (strpos($text, "historical") !== false) {

                $filters[] = "l.category_id = 3";
            }

            if (strpos($text, "temple") !== false) {

                $filters[] = "l.category_id = 3";
            }

            if (strpos($text, "beach") !== false) {

                $filters[] = "l.location_id IN (

        SELECT location_id 
        FROM location_subcategory 
        WHERE sub_id IN (

            SELECT sub_id FROM sub_category 
            WHERE sub_name='Beach'

        )

    )";
            }

            /* ONE DAY TRIP */

            if (
                strpos($text, "weekend") !== false
                || strpos($text, "one day") !== false
                || strpos($text, "today") !== false
                || strpos($text, "tomorrow") !== false
            ) {

                $filters[] = "l.is_one_day_trip = 1";
            }

            /* PROVINCE DETECTION */

            $province_sql = "SELECT province_id,province_name FROM province";

            $province_result = mysqli_query($conn, $province_sql);

            while ($p = mysqli_fetch_assoc($province_result)) {

                $name = strtolower($p['province_name']);

                if (strpos($text, $name) !== false) {

                    $filters[] = "l.province_id = " . $p['province_id'];
                }
            }

            /* CATEGORY DETECTION */

            $category_sql = "SELECT category_id,category_name FROM category";

            $category_result = mysqli_query($conn, $category_sql);

            while ($c = mysqli_fetch_assoc($category_result)) {

                $name = strtolower($c['category_name']);

                if (strpos($text, $name) !== false) {

                    $filters[] = "l.category_id = " . $c['category_id'];
                }
            }

            /* SUB CATEGORY DETECTION */

            $sub_sql = "SELECT sub_id,sub_name FROM sub_category";

            $sub_result = mysqli_query($conn, $sub_sql);

            $sub_conditions = [];

            while ($s = mysqli_fetch_assoc($sub_result)) {

                $name = strtolower($s['sub_name']);

                /* singular match */

                if (strpos($text, $name) !== false) {

                    $sub_conditions[] = $s['sub_id'];
                }

                /* plural match */

                if (strpos($text, $name . "s") !== false) {

                    $sub_conditions[] = $s['sub_id'];
                }
            }

            /* MAIN QUERY */

            $query = "

SELECT 
l.location_id,
l.location_name,
l.description,

(SELECT media_url
FROM location_media
WHERE location_media.location_id = l.location_id
LIMIT 1) AS media_url,

MATCH(
l.location_name,
l.description,
l.things_to_do,
l.nearby_places,
l.travel_tips,
l.why_visit
)

AGAINST('$search_query' IN BOOLEAN MODE) AS relevance

FROM location l

";

            /* join subcategory */

            if (!empty($sub_conditions)) {

                $query .= "

JOIN location_subcategory ls
ON l.location_id = ls.location_id
AND ls.sub_id IN(" . implode(",", $sub_conditions) . ")

";
            }

            /* WHERE */

            $query .= " WHERE l.is_active = 1 ";

            /* apply filters */

            if (!empty($filters)) {

                $query .= " AND " . implode(" AND ", $filters);
            }

            /* text search */

            $query .= "

AND MATCH(
l.location_name,
l.description,
l.things_to_do,
l.nearby_places,
l.travel_tips,
l.why_visit
)

AGAINST('$search_query' IN BOOLEAN MODE)

ORDER BY relevance DESC

LIMIT 12

";

            $result = mysqli_query($conn, $query);


            /* SHOW RESULTS */

            echo "<h2>Recommended places</h2>";

            echo "<div class='locations-container'>";

            while ($row = mysqli_fetch_assoc($result)) {

                echo "

<a href='location_details.php?id=" . $row['location_id'] . "' class='location-card'>

<img src='" . $row['media_url'] . "'>

<div class='location-content'>

<h3>" . $row['location_name'] . "</h3>

<p>" . substr($row['description'], 0, 120) . "...</p>

</div>

</a>

";
            }

            echo "</div>";
        }
        ?>

    </div>
    <!-- HOW IT WORKS POPUP -->

    <div id="aiPopup" class="modal">

        <div class="modal-content">

            <span id="closeAiPopup" class="close-login">

                &times;

            </span>

            <h2>How TripInMind Works</h2>

            <div class="steps-container">

                <div class="step-card">
                    <h3>🧠 Share your trip idea</h3>
                    <p>Describe what kind of place you want</p>
                </div>

                <div class="step-card">
                    <h3>📍 Get smart suggestions</h3>
                    <p>
                        We recommend destinations that best match your idea.
                    </p>
                </div>

                <div class="step-card">
                    <h3>🗺️ Plan your journey</h3>
                    <p>
                        Compare places and finalize your perfect trip.
                    </p>
                </div>

            </div>

        </div>

    </div>

    <script>
        /* show popup only first time */

        if (!sessionStorage.getItem("aiPopupShown")) {

            window.onload = function() {

                document.getElementById("aiPopup").style.display = "flex";

                sessionStorage.setItem("aiPopupShown", "yes");

            }

        }


        /* close popup */

        document.getElementById("closeAiPopup").onclick = function() {

            document.getElementById("aiPopup").style.display = "none";

        }
    </script>
</body>

</html>
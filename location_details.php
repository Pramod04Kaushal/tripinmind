<?php
session_start();
include "config/db.php";

$location_id = intval($_GET['id']);

$query = "
SELECT 
location.location_name,
location.description,
location.things_to_do,
location.nearby_places,
location.travel_tips,
location.why_visit,
province.province_name,
location_media.media_url,
location.latitude,
location.longitude

FROM location

LEFT JOIN province
ON location.province_id = province.province_id

LEFT JOIN location_media
ON location.location_id = location_media.location_id

WHERE location.location_id = '$location_id'
";


$result = mysqli_query($conn, $query);

$images = [];
$location = null;


while ($row = mysqli_fetch_assoc($result)) {

    if (!$location) {
        $location = $row;
    }

    if ($row['media_url']) {
        $images[] = $row['media_url'];
    }
}



/* WEATHER API (WITH SESSION CACHE) */

$city = $location['location_name'];

if (!isset($_SESSION['weather'][$city])) {

    $apiKey = "f40166278dae46ce821193018261803";

    $url = "http://api.weatherapi.com/v1/forecast.json?key=$apiKey&q=$city&days=7";

    $response = @file_get_contents($url);
    $data = json_decode($response, true);

    //save in session
    $_SESSION['weather'][$city] = $data;
} else {

    //use saved data
    $data = $_SESSION['weather'][$city];
}


/* FIND BEST DAY */

$bestDay = null;
$bestTemp = -100;

if ($data && isset($data['forecast']['forecastday'])) {

    foreach ($data['forecast']['forecastday'] as $day) {

        $condition = strtolower($day['day']['condition']['text']);
        $temp = $day['day']['avgtemp_c'];

        // Prefer sunny / clear weather
        if (strpos($condition, 'rain') === false) {

            if ($temp > $bestTemp) {
                $bestTemp = $temp;
                $bestDay = $day;
            }
        }
    }
}

/* GET TOTAL LIKES */

$likes = mysqli_query(
    $conn,
    "SELECT COUNT(*) as total 
FROM likes 
WHERE location_id='$location_id'"
);

$row = mysqli_fetch_assoc($likes);

/* CHECK IF USER ALREADY LIKED */

$user_liked = false;

if (isset($_SESSION['user_id'])) {

    $user_id = $_SESSION['user_id'];

    $check = mysqli_query(
        $conn,
        "SELECT * FROM likes 
WHERE user_id='$user_id' AND location_id='$location_id'"
    );

    if (mysqli_num_rows($check) > 0) {
        $user_liked = true;
    }
}


/* GET LOCATIONS FROM SAME PROVINCE*/

$related_locations = mysqli_query(
    $conn,
    "SELECT location.location_id,
location.location_name,
location.description,
province.province_name,
location_media.media_url

FROM location

LEFT JOIN province
ON location.province_id = province.province_id

LEFT JOIN location_media
ON location.location_id = location_media.location_id

WHERE location.province_id =
(SELECT province_id FROM location WHERE location_id='$location_id')

AND location.location_id != '$location_id'

GROUP BY location.location_id
LIMIT 4"
);
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo $location['location_name']; ?></title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="location_details.css">
    <link rel="stylesheet" href="comments.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Leaflet CSS and JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- Leaflet Routing Machine -->
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />


</head>

<body>
    <!--NAV BAR-->
    <?php include "navbar.php"; ?>

    <!-- AUTH POPUP -->
    <?php include "auth_popup.php"; ?>

    <div class="location-details section-content">

        <h1 class="location-title">
            <?php echo $location['location_name']; ?>
        </h1>

        <p class="location-meta">
            Province: <?php echo $location['province_name']; ?>
        </p>

        <div class="location-main-grid">

            <!-- Location Images Gallery -->

            <?php $imageCount = count($images); ?>

            <div class="gallery-grid">

                <!-- BIG IMAGE -->
                <div class="gallery-main">
                    <img src="<?php echo $images[0]; ?>">

                    <form action="like_location.php" method="POST" class="image-like">
                        <input type="hidden" name="location_id" value="<?php echo $location_id; ?>">

                        <button type="submit" class="like-btn">
                            ❤️ <?php echo $row['total']; ?>
                        </button>
                    </form>
                </div>

                <!-- SECOND IMAGE -->
                <?php if (isset($images[1])) { ?>
                    <div class="gallery-small">
                        <img src="<?php echo $images[1]; ?>">
                    </div>
                <?php } ?>

                <!-- THIRD IMAGE + SEE MORE -->
                <?php if (isset($images[2])) { ?>
                    <div class="gallery-small see-more">

                        <img src="<?php echo $images[2]; ?>">

                        <?php if ($imageCount > 3) { ?>
                            <div class="see-overlay">
                                +<?php echo $imageCount - 2; ?> See More
                            </div>
                        <?php } ?>

                    </div>
                <?php } ?>

            </div>


        </div>

        <div class="location-description">

            <h3>Overview</h3>
            <p class="main-description">
                <?php echo $location['description']; ?>
            </p>


            <!-- 2 COLUMN SECTION -->
            <div class="desc-grid">

                <!-- LEFT -->
                <div>

                    <?php if (!empty($location['things_to_do'])) { ?>

                        <h3>Things to Do</h3>

                        <ul class="desc-list">
                            <?php
                            $things = explode("\n", $location['things_to_do']);

                            foreach ($things as $item) {
                                echo "<li>$item</li>";
                            }
                            ?>
                        </ul>

                    <?php } ?>



                    <?php if (!empty($location['nearby_places'])) { ?>

                        <h3>Nearby Places to Visit</h3>

                        <ul class="desc-list">
                            <?php
                            $nearby = explode("\n", $location['nearby_places']);

                            foreach ($nearby as $item) {
                                echo "<li>$item</li>";
                            }
                            ?>
                        </ul>

                    <?php } ?>

                </div>



                <!-- RIGHT -->
                <div>

                    <?php if (!empty($location['travel_tips'])) { ?>

                        <h3>Travel Tips</h3>

                        <ul class="desc-list tips">
                            <?php
                            $tips = explode("\n", $location['travel_tips']);

                            foreach ($tips as $item) {
                                echo "<li>$item</li>";
                            }
                            ?>
                        </ul>

                    <?php } ?>



                    <?php if (!empty($location['why_visit'])) { ?>

                        <h3>Why Visit</h3>

                        <ul class="desc-list">
                            <?php
                            $why = explode("\n", $location['why_visit']);

                            foreach ($why as $item) {
                                echo "<li>$item</li>";
                            }
                            ?>
                        </ul>

                    <?php } ?>

                </div>

            </div>

        </div>




        <h3 class="section-title">Location on Map</h3>
        <hr class="section-divider">

        <!--MAP + Info Section-->
        <div class="map-distance-layout">


            <div id="map"></div>

            <div class="rigth-pannel">
                <div class="distance-box" id="route-info">
                    Calculating distance...

                </div>
                <?php
                $lat = $location['latitude'];
                $lng = $location['longitude'];
                ?>

                <a
                    href="https://www.google.com/maps?q=<?php echo $lat; ?>,<?php echo $lng; ?>"
                    target="_blank"
                    class="google-map-btn">

                    <i class="fa-solid fa-location-arrow"></i>
                    Open in Google Maps

                </a>
            </div>

        </div>

        <div class="weather-section">

            <h3 class="section-title">Weather Information</h3>
            <hr class="section-divider">

            <div class="weather-top">
                <!-- Weather Box -->
                <div class="weather-box">

                    <h3>Weather Today</h3>

                    <?php if ($data && isset($data['current'])) { ?>

                        <p class="temp">
                            <?php echo $data['current']['temp_c']; ?>°C
                        </p>

                        <p><?php echo $data['current']['condition']['text']; ?></p>

                        <p>Humidity: <?php echo $data['current']['humidity']; ?>%</p>

                        <p>Wind: <?php echo $data['current']['wind_kph']; ?> km/h</p>

                    <?php } else { ?>

                        <p>Weather not available</p>

                    <?php } ?>

                </div>

                <!-- Best Day to Visit -->
                <?php if ($bestDay) { ?>

                    <div class="best-day-box">

                        <h3>Best Day to Visit</h3>

                        <p class="temp">
                            <?php echo date("l", strtotime($bestDay['date'])); ?>
                        </p>

                        <p>
                            <?php echo $bestDay['day']['condition']['text']; ?>
                        </p>

                        <p>
                            <?php echo $bestDay['day']['avgtemp_c']; ?>°C
                        </p>

                    </div>

            </div>


        <?php } ?>

        <!-- Weekly Weather Calendar -->
        <div class="weather-calendar">

            <h3>📅 This Week Weather</h3>

            <div class="calendar-grid">

                <?php if ($data && isset($data['forecast']['forecastday'])) { ?>

                    <?php
                    $today = date("Y-m-d");

                    foreach ($data['forecast']['forecastday'] as $day) {

                        $isToday = ($day['date'] == $today);
                    ?>

                        <div class="day-box <?php echo $isToday ? 'today' : ''; ?>">

                            <p class="date">
                                <?php echo date("D", strtotime($day['date'])); ?>
                            </p>

                            <small class="full-date">
                                <?php echo date("d M", strtotime($day['date'])); ?>
                            </small>

                            <img src="https:<?php echo $day['day']['condition']['icon']; ?>">

                            <p class="temp">
                                <?php echo $day['day']['avgtemp_c']; ?>°C
                            </p>

                            <small>
                                <?php echo $day['day']['condition']['text']; ?>
                            </small>

                        </div>

                    <?php } ?>

                <?php } else { ?>

                    <p>Weather data not available</p>


                <?php } ?>

            </div>

        </div>
        </div>

        <!--Nearby Locations-->

        <div class="related-section">

            <h3>More Places in <?php echo $location['province_name']; ?></h3>

            <div class="scroll-wrapper">

                <button class="scroll-btn left">&#10094;</button>

                <div class="trip-container">

                    <?php while ($place = mysqli_fetch_assoc($related_locations)) { ?>

                        <a href="location_details.php?id=<?php echo $place['location_id']; ?>" class="trip-card">

                            <img src="<?php echo $place['media_url']; ?>" alt="<?php echo $place['location_name']; ?>">
                            <div class="trip-content">

                                <h3>
                                    <?php echo $place['location_name']; ?>
                                </h3>

                                <span class="trip-province">
                                    <?php echo $place['province_name']; ?> Province
                                </span>

                                <p>
                                    <?php echo substr($place['description'], 0, 90); ?>...
                                </p>

                            </div>

                        </a>

                    <?php } ?>

                </div>

                <button class="scroll-btn right">&#10095;</button>

            </div>

        </div>
        <br><br>

        <h3>Share Your Experience</h3>

        <form id="commentForm"
            action="add_comment.php"
            method="POST"
            enctype="multipart/form-data"
            class="comment-form">

            <input type="hidden"
                name="location_id"
                value="<?php echo $location_id; ?>">

            <textarea
                name="content"
                placeholder="Share your experience..."
                required></textarea>

            <input type="file" name="images[]" multiple>

            <button type="submit">Post Comment</button>

        </form>



        <br><br>

        <h3>Visitor Experiences</h3>

        <div class="comment-section" id="comments">

            <?php
            $comments = mysqli_query(
                $conn,
                "SELECT comment.*, users.username
                    FROM comment
                    JOIN users ON comment.user_id = users.user_id
                    WHERE location_id='$location_id'
                    AND parent_id IS NULL
                    ORDER BY created_at DESC
                    LIMIT 5"
            );

            while ($row = mysqli_fetch_assoc($comments)) {
            ?>

                <div class="comment-box"
                    id="comment<?php echo $row['comment_id']; ?>">

                    <div class="comment-avatar">
                        <i class="fa-solid fa-user"></i>
                    </div>

                    <div class="comment-content">

                        <div class="comment-bubble">
                            <strong><?php echo $row['username']; ?></strong>
                            <p><?php echo $row['content']; ?></p>

                            <?php
                            $images = mysqli_query(
                                $conn,
                                "SELECT * FROM comment_images 
                                    WHERE comment_id='" . $row['comment_id'] . "'"
                            );

                            $image_array = [];
                            while ($img = mysqli_fetch_assoc($images)) {
                                $image_array[] = $img;
                            }

                            $image_count = count($image_array);
                            ?>

                            <?php if ($image_count > 0) { ?>
                                <div class="comment-image-grid images-<?php echo $image_count; ?>">

                                    <?php foreach ($image_array as $img) { ?>

                                        <img src="<?php echo $img['image_url']; ?>" class="comment-image">

                                    <?php } ?>

                                </div>
                            <?php } ?>

                        </div>

                        <div class="comment-actions">


                            <?php
                            $like_count = mysqli_query(
                                $conn,
                                "SELECT COUNT(*) as total FROM comment_likes
                                    WHERE comment_id='" . $row['comment_id'] . "'"
                            );

                            $likes = mysqli_fetch_assoc($like_count);
                            ?>

                            <span class="comment-like"
                                data-id="<?php echo $row['comment_id']; ?>">
                                ❤️ <span class="like-count"><?php echo $likes['total']; ?></span>
                            </span>

                            <span class="reply-btn"
                                data-id="<?php echo $row['comment_id']; ?>">
                                Reply
                            </span>

                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']) { ?>

                                <span class="delete-comment delete-btn"
                                    data-id="<?php echo $row['comment_id']; ?>">
                                    <i class="fa-solid fa-trash"></i>
                                </span>

                            <?php } ?>

                        </div>

                        <?php

                        $reply_count_query = mysqli_query(
                            $conn,
                            "SELECT COUNT(*) as total FROM comment
                                WHERE parent_id='" . $row['comment_id'] . "'"
                        );

                        $reply_count = mysqli_fetch_assoc($reply_count_query);

                        /* get replies */

                        $replies = mysqli_query(
                            $conn,
                            "SELECT comment.*, users.username
                                FROM comment
                                JOIN users ON comment.user_id = users.user_id
                                WHERE parent_id='" . $row['comment_id'] . "'
                                ORDER BY created_at ASC"
                        );

                        ?>

                        <?php if ($reply_count['total'] > 0) { ?>

                            <span class="view-replies"
                                onclick="toggleReplies(<?php echo $row['comment_id']; ?>)">
                                View replies (<?php echo $reply_count['total']; ?>)
                            </span>

                        <?php } ?>

                        <div class="replies-container"
                            id="replies-<?php echo $row['comment_id']; ?>"
                            style="display:none;">

                            <?php while ($reply = mysqli_fetch_assoc($replies)) { ?>

                                <div class="reply-box">

                                    <div class="reply-avatar">
                                        <i class="fa-solid fa-user"></i>
                                    </div>

                                    <div class="reply-content">

                                        <div class="reply-bubble">
                                            <strong><?php echo $reply['username']; ?></strong>
                                            <p><?php echo $reply['content']; ?></p>
                                        </div>

                                    </div>

                                </div>

                            <?php } ?>

                        </div>

                        <form action="add_reply.php"
                            method="POST"
                            class="reply-form"
                            id="reply-form-<?php echo $row['comment_id']; ?>"
                            style="display:none;">

                            <input type="hidden" name="location_id"
                                value="<?php echo $location_id; ?>">

                            <input type="hidden" name="parent_id"
                                value="<?php echo $row['comment_id']; ?>">

                            <textarea name="content"
                                placeholder="Reply to this comment..." required></textarea>

                            <button type="submit">➤</button>

                        </form>

                    </div>

                </div>

            <?php } ?>

            <!-- Counting Comments -->
            <?php
            $total_comments_query = mysqli_query(
                $conn,
                "SELECT COUNT(*) as total 
                    FROM comment 
                    WHERE location_id='$location_id' 
                    AND parent_id IS NULL"
            );

            $total_comments = mysqli_fetch_assoc($total_comments_query);
            ?>

            <!-- Load More Comments if more than 5 comments -->
            <?php if ($total_comments['total'] > 5) { ?>

                <button id="loadMoreComments"
                    data-location="<?php echo $location_id; ?>">
                    Load More Comments
                </button>

            <?php } ?>

        </div>


    </div>

    </div>

    </div>




    <script>
        const isLoggedIn =
            <?php echo isset($_SESSION['user_id']) ? "true" : "false"; ?>;


        /* COMMENT SUBMIT */
        document
            .getElementById("commentForm")
            .addEventListener("submit", function(e) {

                if (!isLoggedIn) {

                    e.preventDefault();

                    document
                        .getElementById("loginPopup")
                        .style.display = "flex";

                    return;

                }

                e.preventDefault();

                let formData = new FormData(this);

                fetch("add_comment.php", {

                        method: "POST",
                        body: formData

                    })

                    .then(res => res.text())

                    .then(data => {

                        document
                            .getElementById("comments")
                            .insertAdjacentHTML("afterbegin", data);

                        this.reset();

                    });

            });


        /* REPLY BUTTON */
        document.addEventListener("click", function(e) {

            if (e.target.classList.contains("reply-btn")) {

                if (!isLoggedIn) {

                    document
                        .getElementById("loginPopup")
                        .style.display = "flex";

                    return;

                }

                let id = e.target.dataset.id;

                let form =
                    document.getElementById("reply-form-" + id);

                form.style.display =
                    form.style.display === "none" ?
                    "block" :
                    "none";

            }

        });


        /* LIKE COMMENT */
        document.addEventListener("click", function(e) {

            if (e.target.closest(".comment-like")) {

                if (!isLoggedIn) {

                    document
                        .getElementById("loginPopup")
                        .style.display = "flex";

                    return;

                }

                let btn = e.target.closest(".comment-like");

                let commentId = btn.dataset.id;

                let formData = new FormData();

                formData.append("comment_id", commentId);

                fetch("like_comment.php", {

                        method: "POST",
                        body: formData

                    })

                    .then(res => res.text())

                    .then(count => {

                        btn.querySelector(".like-count").innerText = count;

                    });

            }

        });
    </script>

    <!-- Reply comment -->
    <script>
        document.addEventListener("submit", function(e) {

            if (e.target.classList.contains("reply-form")) {

                if (!isLoggedIn) {

                    e.preventDefault();

                    document
                        .getElementById("loginPopup")
                        .style.display = "flex";

                    return;

                }

                e.preventDefault();

                let formData = new FormData(e.target);

                fetch("add_reply.php", {
                        method: "POST",
                        body: formData
                    })

                    .then(res => res.text())

                    .then(data => {

                        e.target.insertAdjacentHTML("beforebegin", data);

                        e.target.reset();
                        e.target.style.display = "none";

                    });

            }

        });
    </script>



    <!-- Delete Comment without Reloading -->
    <script>
        document.addEventListener("click", function(e) {

            if (e.target.closest(".delete-comment")) {

                if (!confirm("Delete this comment?")) return;

                let btn = e.target.closest(".delete-comment");
                let commentId = btn.dataset.id;

                let formData = new FormData();
                formData.append("comment_id", commentId);

                fetch("delete_comment.php", {
                        method: "POST",
                        body: formData
                    })

                    .then(res => res.text())
                    .then(() => {

                        btn.closest(".comment-box").remove();

                    });

            }

        });
    </script>

    <!-- LOAD MORE COMMENTS-->
    <script>
        let offset = 5;

        document.getElementById("loadMoreComments").addEventListener("click", function() {

            let locationId = this.dataset.location;

            let formData = new FormData();
            formData.append("location_id", locationId);
            formData.append("offset", offset);

            fetch("load_comments.php", {
                    method: "POST",
                    body: formData
                })

                .then(res => res.text())
                .then(data => {


                    document.getElementById("loadMoreComments")
                        .insertAdjacentHTML("beforebegin", data);

                    offset += 5;

                    /* remove button if no more comments */
                    if (document.getElementById("noMoreComments")) {
                        document.getElementById("loadMoreComments").remove();
                    }

                });

        });
    </script>



    <!--Hide reply-->
    <script>
        function toggleReplies(id) {

            let box = document.getElementById("replies-" + id);

            if (box.style.display === "none") {
                box.style.display = "block";
            } else {
                box.style.display = "none";
            }

        }
    </script>


    <!-- Clickable Weather Calendar -->
    <script>
        document.querySelectorAll(".day-box").forEach(box => {
            box.addEventListener("click", () => {
                alert(box.innerText);
            });
        });
    </script>

    <!-- Leaflet Map -->
    <script>
        const destination = [
            <?php echo $location['latitude']; ?>,
            <?php echo $location['longitude']; ?>
        ];

        const map = L.map('map').setView(destination, 10);

        // Map tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Destination marker
        L.marker(destination).addTo(map)
            .bindPopup("Destination")
            .openPopup();

        // Get user location
        navigator.geolocation.getCurrentPosition(function(position) {

            const user = [
                position.coords.latitude,
                position.coords.longitude
            ];

            L.marker(user).addTo(map)
                .bindPopup("You are here");

            // 🔥 REAL ROUTE
            const control = L.Routing.control({
                waypoints: [
                    L.latLng(user[0], user[1]),
                    L.latLng(destination[0], destination[1])
                ],
                routeWhileDragging: false,
                show: false // hide default panel
            }).addTo(map);

            control.on('routesfound', function(e) {

                const route = e.routes[0];

                const distance = (route.summary.totalDistance / 1000).toFixed(2);

                const hours = Math.floor(route.summary.totalTime / 3600);
                const minutes = Math.floor((route.summary.totalTime % 3600) / 60);

                let timeText = "";

                if (hours > 0) {
                    timeText = hours + " hr " + minutes + " min";
                } else {
                    timeText = minutes + " min";
                }

                document.getElementById("route-info").innerHTML = `
<div class="distance-item">
<div class="distance-icon">🚗</div>
<div>
<small>Distance</small>
<h3>${distance} km</h3>
</div>
</div>

<div class="distance-item">
<div class="distance-icon">⏱</div>
<div>
<small>Travel time</small>
<h3>${timeText}</h3>
</div>
</div>
`;
            });

        });
    </script>


    <div id="galleryModal" class="gallery-modal">

        <span class="close-gallery">&times;</span>

        <button class="nav-btn prev">&#10094;</button>

        <img id="galleryImage" class="gallery-full">

        <button class="nav-btn next">&#10095;</button>

    </div>

    <script>
        const images = <?php echo json_encode($images); ?>;

        let currentIndex = 0;

        const modal = document.getElementById("galleryModal");
        const modalImg = document.getElementById("galleryImage");

        /* click on images */

        document.querySelectorAll(".gallery-grid img").forEach((img, index) => {

            img.addEventListener("click", function() {

                currentIndex = index;

                showImage();

                modal.style.display = "flex";

            });

        });

        /* click on SEE MORE overlay */

        const seeMore = document.querySelector(".see-more");

        if (seeMore) {

            seeMore.addEventListener("click", function() {

                currentIndex = 2; // start from 3rd image

                showImage();

                modal.style.display = "flex";

            });

        }

        /* show image */

        function showImage() {

            modalImg.src = images[currentIndex];

        }

        /* next */

        document.querySelector(".next").onclick = function() {

            currentIndex++;

            if (currentIndex >= images.length) {

                currentIndex = 0;

            }

            showImage();

        }

        /* previous */

        document.querySelector(".prev").onclick = function() {

            currentIndex--;

            if (currentIndex < 0) {

                currentIndex = images.length - 1;

            }

            showImage();

        }

        /* close */

        document.querySelector(".close-gallery").onclick = function() {

            modal.style.display = "none";

        }

        /* click outside */

        modal.onclick = function(e) {

            if (e.target === modal) {

                modal.style.display = "none";

            }

        }
    </script>

    <!-- Login Popup -->
    <div id="loginPopup" class="login-popup">

        <div class="login-box">

            <h3>Login Required</h3>

            <p>Please login to comment or reply.</p>

            <button onclick="openLoginPopup()" class="login-btn">
                Login
            </button>

            <button onclick="openRegisterPopup()" class="register-btn">
                Sign Up
            </button>

            <button onclick="
document.getElementById('loginPopup').style.display='none'
">

                Cancel

            </button>

        </div>

    </div>
    <!-- Register Modal -->
    <script>
        function openLoginPopup() {

            document.getElementById("loginPopup").style.display = "none";

            document.getElementById("loginModal").style.display = "flex";

        }

        function openRegisterPopup() {

            document.getElementById("loginPopup").style.display = "none";

            document.getElementById("registerModal").style.display = "flex";

        }
    </script>

    <script>
        const container = document.querySelector(".trip-container");

        document.querySelector(".scroll-btn.left")
            .addEventListener("click", () => {

                container.scrollBy({
                    left: -300,
                    behavior: "smooth"
                });

            });

        document.querySelector(".scroll-btn.right")
            .addEventListener("click", () => {

                container.scrollBy({
                    left: 300,
                    behavior: "smooth"
                });

            });
    </script>
</body>

</html>
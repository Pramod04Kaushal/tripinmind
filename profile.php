<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* UPDATE USER */

if (isset($_POST['username'])) {

    $new_username = $_POST['username'];

    if (!empty($_FILES['profile_image']['name'])) {

        if (!file_exists("uploads")) {
            mkdir("uploads");
        }

        $image_name = time() . "_" . $_FILES['profile_image']['name'];
        $image_path = "uploads/" . $image_name;

        move_uploaded_file(
            $_FILES['profile_image']['tmp_name'],
            $image_path
        );

        mysqli_query(
            $conn,
            "UPDATE users SET
username='$new_username',
profile_image='$image_name'
WHERE user_id='$user_id'"
        );
    } else {

        mysqli_query(
            $conn,
            "UPDATE users SET
username='$new_username'
WHERE user_id='$user_id'"
        );
    }

    header("Location: profile.php");
    exit();
}


/* GET USER DATA */

$result = mysqli_query(
    $conn,
    "SELECT username,email,profile_image
    FROM users
    WHERE user_id='$user_id'"
);

$user = mysqli_fetch_assoc($result);
?>


<!DOCTYPE html>
<html class="profile-page">

<head>

    <title>Profile | TripInMind</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="navbar.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


</head>

<body>
    <!-- NAV BAR -->
    <?php include "navbar.php"; ?>

    <div class="profile-wrapper">

        <!-- PROFILE HEADER -->

        <div class="profile-header">

            <div class="profile-avatar">

                <?php if (!empty($user['profile_image'])) { ?>

                    <img src="uploads/<?php echo $user['profile_image']; ?>">

                <?php } else { ?>

                    <div class="avatar">
                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                    </div>

                <?php } ?>

            </div>

            <h2 class="profile-name">

                <?php echo $user['username']; ?>

                <i class="fa-solid fa-pen edit-icon"
                    onclick="openPopup()"></i>

            </h2>

        </div>


        <!-- TABS -->

        <div class="profile-tabs">

            <button id="tabFav" onclick="showFavorites()">Favorites</button>

            <button id="tabRec" onclick="showRecommend()">Recommends</button>

            <button id="tabCom" onclick="showComments()">Comments</button>

            <button id="tabPref" onclick="showPreferences()">Settings</button>

        </div>



        <!-- FAVORITES -->

        <div id="favorites-section">

            <h3>Your Favorite Places</h3>

            <div class="card-grid">

                <?php

                $favorites = mysqli_query($conn, "

SELECT 
location.location_id,
location.location_name,
location.description,
province.province_name,
location_media.media_url

FROM likes

JOIN location
ON likes.location_id = location.location_id

LEFT JOIN province
ON location.province_id = province.province_id

LEFT JOIN location_media
ON location.location_id = location_media.location_id

WHERE likes.user_id='$user_id'

GROUP BY location.location_id

");

                if (mysqli_num_rows($favorites) == 0) {

                    echo "<p>No favorite places yet ❤️</p>";
                }

                while ($fav = mysqli_fetch_assoc($favorites)) {

                ?>

                    <a href="location_details.php?id=<?php echo $fav['location_id']; ?>" class="trip-card">

                        <img src="<?php echo $fav['media_url']; ?>">

                        <div class="trip-content">

                            <h3>

                                <?php echo $fav['location_name']; ?>

                            </h3>

                            <span class="trip-province">

                                <?php echo $fav['province_name']; ?> Province

                            </span>

                            <p>

                                <?php echo substr($fav['description'], 0, 90); ?>...

                            </p>

                        </div>

                    </a>

                <?php } ?>

            </div>

        </div>




        <!-- RECOMMENDATIONS -->

        <div id="recommend-section">

            <h3>Recommended for this month</h3>

            <div class="card-grid">

                <?php include "monthly_recommendation.php"; ?>

            </div>

        </div>




        <!-- COMMENTS -->
        <!-- COMMENTS -->

        <div id="comments-section">

            <h3 class="section-title">My Comments</h3>

            <div class="comment-grid">

                <?php

                $comments = mysqli_query($conn, "

SELECT 
comment.comment_id,
comment.content,
comment.created_at,

location.location_id,
location.location_name,

(SELECT media_url
 FROM location_media
 WHERE location_media.location_id = location.location_id
 LIMIT 1) AS image

FROM comment

JOIN location
ON comment.location_id = location.location_id

WHERE comment.user_id='$user_id'

ORDER BY comment.created_at DESC

");

                if (mysqli_num_rows($comments) == 0) {

                    echo "<p>No comments yet.</p>";
                }

                while ($row = mysqli_fetch_assoc($comments)) {

                ?>

                    <a
                        href="location_details.php?id=<?php echo $row['location_id']; ?>#comment<?php echo $row['comment_id']; ?>"
                        class="comment-card">

                        <img src="<?php echo $row['image']; ?>">

                        <div class="comment-content">

                            <h4>

                                <?php echo $row['location_name']; ?>

                            </h4>

                            <p>

                                <?php echo $row['content']; ?>

                            </p>

                            <span class="comment-date">

                                <?php echo date("d M Y", strtotime($row['created_at'])); ?>

                            </span>

                        </div>

                    </a>

                <?php } ?>

            </div>

        </div>





        <!-- PREFERENCES -->

        <div id="preferences-section">

            <form id="settingsForm" action="save_preferences.php" method="POST">

                <h3>Settings</h3>

                <label>

                    <input type="checkbox"

                        id="emailToggle"

                        name="receive_email"

                        value="1"

                        <?php if ($preferences['receive_email'] == 1) echo "checked"; ?>>

                    Enable Monthly Suggestions

                </label>

                <p>Get email about best places to visit this month</p>






            </form>

        </div>


    </div>




    <!-- EDIT PROFILE POPUP -->

    <div class="popup" id="popup">

        <div class="popup-content">

            <h3>Edit Profile</h3>

            <form method="POST" enctype="multipart/form-data">

                <input type="text" name="username"
                    value="<?php echo $user['username'] ?? ''; ?>" required>



                <label>Profile image</label>

                <input type="file" name="profile_image">

                <button class="save-btn" type="submit">

                    Save

                </button>

            </form>

            <button class="close-btn" onclick="closePopup()">

                Cancel

            </button>

        </div>

    </div>



    <script>
        function openPopup() {

            document.getElementById("popup").style.display = "flex";

        }


        function closePopup() {

            document.getElementById("popup").style.display = "none";

        }



        /* TAB FUNCTIONS */


        function activateTab(tab) {

            document
                .querySelectorAll(".profile-tabs button")
                .forEach(btn => btn.classList.remove("active"));

            tab.classList.add("active");

        }



        function showFavorites() {

            hideAll();

            document
                .getElementById("favorites-section")
                .style.display = "block";

            activateTab(document.getElementById("tabFav"));

        }



        function showRecommend() {

            hideAll();

            document
                .getElementById("recommend-section")
                .style.display = "block";

            activateTab(document.getElementById("tabRec"));

        }



        function showComments() {

            hideAll();

            document
                .getElementById("comments-section")
                .style.display = "block";

            activateTab(document.getElementById("tabCom"));

        }



        function showPreferences() {

            hideAll();

            document
                .getElementById("preferences-section")
                .style.display = "block";

            activateTab(document.getElementById("tabPref"));

        }



        function hideAll() {

            document.getElementById("favorites-section").style.display = "none";

            document.getElementById("recommend-section").style.display = "none";

            document.getElementById("comments-section").style.display = "none";

            document.getElementById("preferences-section").style.display = "none";

        }



        /* default tab */

        showFavorites();
    </script>


    <script>
        document.getElementById("emailToggle").addEventListener("change", function() {

            if (this.checked) {

                let confirmBox = confirm(
                    "Enable Monthly Suggestions?\n\nWe will send you travel ideas for this month."
                );

                if (confirmBox) {

                    document.getElementById("settingsForm").submit();

                } else {

                    this.checked = false;

                }

            }

        });
    </script>
</body>

</html>
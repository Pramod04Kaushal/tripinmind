<?php
include "config/db.php";

$id = $_GET['id'];

/* GET LOCATION DATA */
$query = "
SELECT l.*, ls.sub_id, sc.category_id
FROM location l
LEFT JOIN location_subcategory ls 
ON l.location_id = ls.location_id
LEFT JOIN sub_category sc 
ON ls.sub_id = sc.sub_id
WHERE l.location_id='$id'
";

$result = mysqli_query($conn, $query);
$location = mysqli_fetch_assoc($result);


if (isset($_POST['update_location'])) {

    $name = $_POST['location_name'];
    $province = $_POST['province_id'];
    $description = $_POST['description'];

    $sub_id = $_POST['sub_id'];

    $one_day = isset($_POST['is_one_day_trip']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    /* UPDATE LOCATION */

    $things_to_do = $_POST['things_to_do'];
    $nearby_places = $_POST['nearby_places'];
    $travel_tips = $_POST['travel_tips'];
    $why_visit = $_POST['why_visit'];

    $update = "UPDATE location SET
location_name='$name',
province_id='$province',
description='$description',
things_to_do='$things_to_do',
nearby_places='$nearby_places',
travel_tips='$travel_tips',
why_visit='$why_visit',
is_one_day_trip='$one_day',
is_active='$is_active'
WHERE location_id='$id'";

    mysqli_query($conn, $update);


    /* UPDATE SUBCATEGORY */

    mysqli_query($conn, "DELETE FROM location_subcategory WHERE location_id='$id'");

    mysqli_query($conn, "
INSERT INTO location_subcategory(location_id,sub_id)
VALUES('$id','$sub_id')
");


    /* UPDATE SEASONS */

    $seasons = $_POST['seasons'] ?? [];

    mysqli_query($conn, "DELETE FROM location_season WHERE location_id='$id'");

    foreach ($seasons as $season) {

        mysqli_query($conn, "
INSERT INTO location_season(location_id,season_id)
VALUES('$id','$season')
");
    }

    /* UPLOAD NEW IMAGES */

    if (!empty($_FILES['images']['name'][0])) {

        foreach ($_FILES['images']['tmp_name'] as $key => $tmp) {

            $filename = $_FILES['images']['name'][$key];
            $tempname = $_FILES['images']['tmp_name'][$key];

            $path = "uploads/" . $filename;

            move_uploaded_file($tempname, $path);

            mysqli_query($conn, "
        INSERT INTO location_media(location_id,media_type,media_url)
        VALUES('$id','image','$path')
        ");
        }
    }

    header("Location: manage_location.php");
    exit();
}
?>

<?php

$season_query = mysqli_query($conn, "
SELECT season_id 
FROM location_season 
WHERE location_id='$id'
");

$selected_seasons = [];

while ($row = mysqli_fetch_assoc($season_query)) {
    $selected_seasons[] = $row['season_id'];
}


?>

<!DOCTYPE html>
<html>

<head>

    <title>Edit Location | TripInMind</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin_style.css">
    <link rel="stylesheet" href="edit_location.css">

</head>

<body class="admin-body">

    <div class="admin-layout">

        <!-- SIDEBAR -->
        <aside class="admin-sidebar">
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="add_location.php">Add Location</a></li>
                <li class="active"><a href="manage_location.php">Manage Locations</a></li>
                <li><a href="user_managment.php">Users</a></li>
            </ul>
        </aside>


        <!-- MAIN CONTENT -->
        <main class="admin-content">

            <h2>Edit Location</h2>

            <form class="admin-form" method="POST" enctype="multipart/form-data">

                <div class="form-section">

                    <h3>Basic Information</h3>

                    <div class="form-grid">


                        <div class="admin-group">

                            <label>Location Name</label>
                            <input type="text"
                                name="location_name"
                                value="<?php echo $location['location_name']; ?>"
                                required>
                        </div>

                        <!-- CATEGORY -->
                        <div class="admin-group">
                            <label>Category</label>
                            <select name="category_id" id="categorySelect">

                                <?php

                                $categories = mysqli_query($conn, "SELECT * FROM category");

                                while ($row = mysqli_fetch_assoc($categories)) {

                                    $selected = "";

                                    if ($row['category_id'] == $location['category_id']) {
                                        $selected = "selected";
                                    }

                                    echo "<option value='" . $row['category_id'] . "' $selected>" . $row['category_name'] . "</option>";
                                }

                                ?>

                            </select>

                        </div>


                        <!-- SUB CATEGORY -->
                        <div class="admin-group">

                            <label>Sub Category</label>

                            <select name="sub_id">

                                <?php

                                $subs = mysqli_query($conn, "SELECT * FROM sub_category");

                                while ($row = mysqli_fetch_assoc($subs)) {

                                    $selected = "";

                                    if ($row['sub_id'] == $location['sub_id']) {
                                        $selected = "selected";
                                    }

                                    echo "<option value='" . $row['sub_id'] . "' $selected>" . $row['sub_name'] . "</option>";
                                }

                                ?>

                            </select>

                        </div>

                        <!-- PROVINCE -->
                        <div class="admin-group">

                            <label>Province</label>

                            <select name="province_id">

                                <?php

                                $province_query = mysqli_query($conn, "SELECT * FROM province");

                                while ($row = mysqli_fetch_assoc($province_query)) {

                                    $selected = "";

                                    if ($row['province_id'] == $location['province_id']) {
                                        $selected = "selected";
                                    }

                                    echo "<option value='" . $row['province_id'] . "' $selected>" . $row['province_name'] . "</option>";
                                }

                                ?>

                            </select>

                        </div>

                        <div class="form-section full">
                            <div class="admin-group">

                                <label>Description</label>

                                <textarea name="description" rows="5"><?php echo $location['description']; ?></textarea>


                                <label>Things to Do</label>

                                <textarea name="things_to_do"><?php echo $location['things_to_do']; ?></textarea>



                                <label>Nearby Places to Visit</label>

                                <textarea name="nearby_places"><?php echo $location['nearby_places']; ?></textarea>



                                <label>Travel Tips</label>

                                <textarea name="travel_tips"><?php echo $location['travel_tips']; ?></textarea>



                                <label>Why Visit</label>

                                <textarea name="why_visit"><?php echo $location['why_visit']; ?></textarea>

                            </div>

                        </div>
                    </div>
                </div>


                <!--Right Side-->
                <div class="form-section">

                    <h3>Travel Details</h3>

                    <div class="form-grid">
                        <div class="toggle-group">

                            <!-- ONE DAY TRIP -->
                            <label>One Day Trip</label>
                            <label class="switch">
                                <input type="checkbox" name="is_one_day_trip"
                                    <?php if ($location['is_one_day_trip'] == 1) {
                                        echo "checked";
                                    } ?>>
                                <span class="slider"></span>
                            </label>
                        </div>


                        <div class="toggle-group">

                            <label>Active</label>

                            <label class="switch">
                                <input type="checkbox" name="is_active"
                                    <?php if ($location['is_active'] == 1) {
                                        echo "checked";
                                    } ?>>
                                <span class="slider"></span>
                            </label>

                        </div>

                        <div class="admin-group">

                            <label>Seasons</label>

                            <div class="season-grid">

                                <?php

                                $seasons = mysqli_query($conn, "SELECT * FROM season");

                                while ($row = mysqli_fetch_assoc($seasons)) {

                                    $checked = "";

                                    if (in_array($row['season_id'], $selected_seasons)) {
                                        $checked = "checked";
                                    }

                                    echo "<label><span>" . $row['season_name'] . "</span><input type='checkbox'name='seasons[]'value='" . $row['season_id'] . "'$checked></label>";
                                }

                                ?>

                            </div>

                        </div>



                        <div class="form-section full">
                            <!-- Existing Images-->
                            <div class="admin-group">

                                <label>Existing Images</label>

                                <div class="image-preview">

                                    <?php

                                    $images = mysqli_query($conn, "SELECT * FROM location_media WHERE location_id='$id'");

                                    while ($img = mysqli_fetch_assoc($images)) {

                                        echo "<div class='img-box'>
                                            <img src='" . $img['media_url'] . "'><a href='delete_media.php?id=" . $img['media_id'] . "'class='delete-btn'onclick=\"return confirm('Delete image?')\">Delete</a></div>";
                                    }

                                    ?>

                                </div>

                            </div>
                        </div>


                        <div class="admin-group">

                            <label>Upload New Images</label>

                            <input type="file" name="images[]" multiple>

                        </div>




                    </div>

                    <!-- BUTTONS -->
                    <div class="form-buttons">
                        <button type="submit" name="update_location" class="btn-update">
                            Update Location
                        </button>

                        <button type="button" class="btn-cancel"
                            onclick="window.location.href='manage_location.php'">
                            Cancel
                        </button>

                    </div>


            </form>

        </main>

    </div>

</body>

</html>
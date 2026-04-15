<?php
include "config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $location_name = $_POST['location_name'];
    $description = $_POST['description'];
    $things_to_do = $_POST['things_to_do'];
    $nearby_places = $_POST['nearby_places'];
    $travel_tips = $_POST['travel_tips'];
    $why_visit = $_POST['why_visit'];
    $province_id = $_POST['province_id'];
    $category_id = $_POST['category_id'];

    $is_one_day_trip = isset($_POST['is_one_day_trip']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $created_by = 1; // Admin ID 

    /* GET LATITUDE & LONGITUDE */

    $name = $location_name . " Sri Lanka";

    $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($name) . "&format=json&limit=1";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "TripInMindApp/1.0");

    $response = curl_exec($ch);
    curl_close($ch);
    sleep(1);

    $data = json_decode($response, true);

    $lat = NULL;
    $lon = NULL;

    if (!empty($data)) {
        $lat = $data[0]['lat'];
        $lon = $data[0]['lon'];
    }

    /* Insert location */

    $sql = "INSERT INTO location
(
location_name,
description,
things_to_do,
nearby_places,
travel_tips,
why_visit,
category_id,
is_one_day_trip,
is_active,
province_id,
created_by,
latitude,
longitude
)
VALUES
(
'$location_name',
'$description',
'$things_to_do',
'$nearby_places',
'$travel_tips',
'$why_visit',
'$category_id',
'$is_one_day_trip',
'$is_active',
'$province_id',
'$created_by',
'$lat',
'$lon'
)";

    if (mysqli_query($conn, $sql)) {

        $success = "Location added successfully";

        $location_id = mysqli_insert_id($conn);

        /* Save seasons */

        if (!empty($_POST['seasons'])) {

            foreach ($_POST['seasons'] as $season) {

                mysqli_query(
                    $conn,
                    "INSERT INTO location_season (location_id, season_id)
                    VALUES ('$location_id','$season')"
                );
            }
        }

        /* Save sub categories */

        if (!empty($_POST['sub_categories'])) {

            foreach ($_POST['sub_categories'] as $sub) {

                mysqli_query(
                    $conn,
                    "INSERT INTO location_subcategory (location_id, sub_id)
                    VALUES ('$location_id','$sub')"
                );
            }
        }

        /* Create upload folder if not exists */

        if (!file_exists("uploads")) {
            mkdir("uploads");
        }

        /* Image Upload */

        if (!empty($_FILES['images']['name'][0])) {

            foreach ($_FILES['images']['tmp_name'] as $key => $tmp) {

                $image_name = $_FILES['images']['name'][$key];

                $target = "uploads/" . $image_name;

                move_uploaded_file($tmp, $target);

                mysqli_query(
                    $conn,
                    "INSERT INTO location_media (location_id, media_type, media_url)
                    VALUES ('$location_id','image','$target')"
                );
            }
        }
    } else {

        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>TripInMind | Add Locations</title>

    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet" href="admin_style.css">
    <link rel="stylesheet" href="admin_sidebar.css">

    <link rel="stylesheet" href="popup_message.css">

    <link rel="stylesheet" href="add_location.css">

    <link rel="icon" type="image" href="images/icon.png">

</head>

<body class="admin-body">
    <!-- Messages -->
    <?php include "message.php"; ?>

    <div class="admin-layout">
        <!-- Sidebar -->
        <?php include "admin_sidebar.php"; ?>


        <!-- Main Content -->
        <main class="admin-content">

            <h2>Add New Location</h2>

            <form class="admin-form" action="add_location.php" method="POST" enctype="multipart/form-data">

                <div class="form-section">

                    <h3>Basic Information</h3>

                    <div class="form-grid">

                        <!-- Location Name -->

                        <div class="admin-group">
                            <label>Location Name</label>
                            <input type="text" name="location_name" required>
                        </div>

                        <!-- Category -->

                        <div class="admin-group">
                            <label>Category</label>

                            <select name="category_id" id="categorySelect" required>

                                <option value="">Select Category</option>

                                <option value="1">Leisure & Relaxation</option>
                                <option value="2">Adventure & Exploration</option>
                                <option value="3">Cultural & Religious Purpose</option>
                                <option value="4">Wildlife & Nature Observation</option>
                                <option value="5">Educational Purpose</option>
                                <option value="6">Family and Friends</option>

                            </select>

                        </div>


                        <!-- Sub Categories -->

                        <div class="admin-group">
                            <label>Sub Categories</label>

                            <div id="subCategoryContainer"></div>

                        </div>


                        <!-- Province -->

                        <div class="admin-group">

                            <label>Province</label>

                            <select name="province_id" required>

                                <option value="">Select Province</option>

                                <option value="1">Western</option>
                                <option value="2">Central</option>
                                <option value="3">Southern</option>
                                <option value="4">North Western</option>
                                <option value="5">Sabaragamuwa</option>
                                <option value="6">Northern</option>
                                <option value="7">Eastern</option>
                                <option value="8">Uva</option>
                                <option value="9">North Central</option>

                            </select>

                        </div>

                    </div>


                </div>

                <div class="form-section">

                    <h3>Travel Details</h3>

                    <div class="form-grid">

                        <!-- One Day Trip -->

                        <div class="toggle-group">

                            <label>One Day Trip</label>

                            <label class="switch">
                                <input type="checkbox" name="is_one_day_trip" value="1">
                                <span class="slider"></span>
                            </label>

                        </div>


                        <div class="toggle-group">

                            <label>Active</label>

                            <label class="switch">
                                <input type="checkbox" name="is_active" value="1" checked>
                                <span class="slider"></span>
                            </label>

                        </div>

                        <!-- Seasons -->

                        <div class="admin-group">

                            <label>Seasons</label>

                            <div class="season-grid">

                                <label><span>January</span><input type="checkbox" name="seasons[]" value="1"></label>
                                <label><span>February</span><input type="checkbox" name="seasons[]" value="2"></label>
                                <label><span>March</span><input type="checkbox" name="seasons[]" value="3"></label>
                                <label><span>April</span><input type="checkbox" name="seasons[]" value="4"></label>
                                <label><span>May</span><input type="checkbox" name="seasons[]" value="5"></label>
                                <label><span>June</span><input type="checkbox" name="seasons[]" value="6"></label>
                                <label><span>July</span><input type="checkbox" name="seasons[]" value="7"></label>
                                <label><span>August</span><input type="checkbox" name="seasons[]" value="8"></label>
                                <label><span>September</span><input type="checkbox" name="seasons[]" value="9"></label>
                                <label><span>October</span><input type="checkbox" name="seasons[]" value="10"></label>
                                <label><span>November</span><input type="checkbox" name="seasons[]" value="11"></label>
                                <label><span>December</span><input type="checkbox" name="seasons[]" value="12"></label>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="form-section full">

                    <!-- Description -->

                    <div class="admin-group">

                        <label>Description</label>

                        <textarea name="description" rows="5"></textarea>

                        <!--SUB TOPICES-->

                        <label>Things to Do</label>
                        <textarea name="things_to_do"></textarea>

                        <label>Nearby Places to Visit</label>
                        <textarea name="nearby_places"></textarea>

                        <label>Travel Tips</label>
                        <textarea name="travel_tips"></textarea>

                        <label>Why Visit</label>
                        <textarea name="why_visit"></textarea>

                    </div>


                    <!-- Images -->

                    <div class="admin-group">

                        <label>Images</label>

                        <input type="file" name="images[]" multiple>

                    </div>
                </div>

                <!-- Submit -->

                <button type="submit" class="btn-primary">

                    Add Place

                </button>

            </form>
        </main>

    </div>

    <!-- Sub Categories-->
    <script>
        const subCategories = {

            1: [{
                    id: 26,
                    name: "Beach"
                },
                {
                    id: 27,
                    name: "Country side relaxation"
                },
                {
                    id: 28,
                    name: "Surfing or Diving"
                },
                {
                    id: 29,
                    name: "Spa and wellness retreats"
                },
                {
                    id: 30,
                    name: "Cultural & Historical"
                }
            ],

            2: [{
                    id: 31,
                    name: "Hiking"
                },
                {
                    id: 32,
                    name: "Cave exploration"
                },
                {
                    id: 33,
                    name: "Forest walks"
                },
                {
                    id: 34,
                    name: "Sightseeing"
                },
                {
                    id: 36,
                    name: "Rock climbing"
                },
                {
                    id: 35,
                    name: "Water sports"
                }
            ],

            3: [{
                    id: 37,
                    name: "Temple"
                },
                {
                    id: 38,
                    name: "Historical Site"
                },
                {
                    id: 39,
                    name: "Religious Landmark"
                },
                {
                    id: 40,
                    name: "Ancient City"
                },
                {
                    id: 41,
                    name: "Archaeological Site"
                }
            ],

            4: [{
                    id: 42,
                    name: "National Park"
                },
                {
                    id: 43,
                    name: "Safari"
                },
                {
                    id: 44,
                    name: "Botanical Garden"
                },
                {
                    id: 45,
                    name: "Bird Watching"
                },
                {
                    id: 46,
                    name: "Nature Reserve"
                }
            ],

            5: [{
                    id: 47,
                    name: "Museum"
                },
                {
                    id: 48,
                    name: "Research Center"
                },
                {
                    id: 49,
                    name: "Educational Park"
                },
                {
                    id: 50,
                    name: "Cultural Museum"
                }
            ]

        };

        document.getElementById("categorySelect").addEventListener("change", function() {

            const container = document.getElementById("subCategoryContainer");

            container.innerHTML = "";

            const selected = this.value;

            if (subCategories[selected]) {

                subCategories[selected].forEach(sub => {

                    const label = document.createElement("label");

                    label.innerHTML = `
<span>${sub.name}</span>
<input type="checkbox" name="sub_categories[]" value="${sub.id}">
`;

                    container.appendChild(label);

                });

            }

        });
    </script>

</body>

</html>
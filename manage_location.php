<?php
include "config/db.php";

$query = "SELECT location.location_id, location.location_name, location.description,
province.province_name
FROM location
JOIN province ON location.province_id = province.province_id
ORDER BY location.created_at DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>

<head>

    <title>Manage Locations | TripInMind</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin_style.css">
    <link rel="stylesheet" href="admin_sidebar.css">

    <link rel="icon" type="image" href="images/icon.png">

</head>

<body class="admin-body">

    <div class="admin-layout">

        <?php include "admin_sidebar.php"; ?>

        <!-- Main Content -->
        <main class="admin-content">


            <h2>Manage Locations</h2><br>

            <!-- Search -->
            <div class="table-controls">
                <input type="text" id="searchLocation" class="search-box" placeholder="Search Here...">
            </div>

            <table class="manage-table" id="locationTable">

                <tr>

                    <th>ID</th>
                    <th>Location</th>
                    <th>Province</th>
                    <th>Description</th>
                    <th>Actions</th>

                </tr>

                <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                    <tr>

                        <td><?php echo $row['location_id']; ?></td>

                        <td><?php echo $row['location_name']; ?></td>

                        <td><?php echo $row['province_name']; ?></td>

                        <td><?php echo substr($row['description'], 0, 80); ?>...</td>

                        <td class="action-buttons">

                            <a class="edit-btn"
                                href="edit_location.php?id=<?php echo $row['location_id']; ?>">
                                Edit
                            </a>

                            <a class="delete-btn"
                                href="delete_location.php?id=<?php echo $row['location_id']; ?>"
                                onclick="return confirm('Are you sure you want to delete this location? This action cannot be undone.')">
                                Delete
                            </a>

                        </td>

                    </tr>

                <?php } ?>

            </table>

        </main>



    </div>


    <!-- Live Search -->
    <script>
        document.getElementById("searchLocation").addEventListener("keyup", function() {

            let filter = this.value.toLowerCase();

            let table = document.getElementById("locationTable");

            let rows = table.getElementsByTagName("tr");

            for (let i = 1; i < rows.length; i++) {

                let location = rows[i].getElementsByTagName("td")[1];
                let province = rows[i].getElementsByTagName("td")[2];

                if (location || province) {

                    let locationText = location.textContent.toLowerCase();
                    let provinceText = province.textContent.toLowerCase();

                    if (locationText.includes(filter) || provinceText.includes(filter)) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }

                }

            }

        });
    </script>



</body>

</html>
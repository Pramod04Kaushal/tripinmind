<?php
session_start();

if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>

<?php

include "config/db.php";

$active = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE is_active=1"));
$blocked = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE is_active=0"));
$locationCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM location"));
?>

<!-- Get Total Locations -->
<?php
$total_locations_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM location");
$total_locations = mysqli_fetch_assoc($total_locations_query);
?>

<!-- Get One Day Trips -->
<?php
$one_day_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM location WHERE is_one_day_trip = 1");
$one_day = mysqli_fetch_assoc($one_day_query);
?>

<!-- Get Active Users -->
<?php
$active_users_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE is_active = 1");
$active_users = mysqli_fetch_assoc($active_users_query);
?>

<!DOCTYPE html>
<html>
<title>TripInMind | Admin Dashboard</title>

<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin_style.css">

    <link rel="stylesheet" href="admin_sidebar.css">

    <link rel="icon" type="image" href="images/icon.png">

    <!-- ADD CHart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>



    <div class="admin-layout">

        <!--Sidebar-->
        <?php include "admin_sidebar.php"; ?>

        <!--Main Content-->
        <main class="admin-content">



            <h2>Dashboard Overview</h2>

            <div class="dashboard-layout">

                <!-- LEFT SIDE (Cards) -->
                <div class="dashboard-cards">

                    <div class="dashboard-card">
                        <h4>Total Locations</h4>
                        <p><?php echo $total_locations['total']; ?></p>
                    </div>

                    <div class="dashboard-card">
                        <h4>One Day Trips</h4>
                        <p><?php echo $one_day['total']; ?></p>
                    </div>

                    <div class="dashboard-card">
                        <h4>Active Users</h4>
                        <p><?php echo $active_users['total']; ?></p>
                    </div>

                </div>

                <!-- RIGHT SIDE (Graphs) -->
                <div class="dashboard-graphs">

                    <div class="chart-box">
                        <h3>User Status</h3>
                        <canvas id="userChart"></canvas>
                    </div>

                    <div class="chart-box">
                        <h3>Locations Overview</h3>
                        <canvas id="locationChart"></canvas>
                    </div>

                </div>

            </div>


        </main>



    </div>

    <!-- User Chart -->
    <script>
        const ctx = document.getElementById('userChart');

        new Chart(ctx, {

            type: 'doughnut',

            data: {
                labels: ['Active Users', 'Blocked Users'],

                datasets: [{
                    data: [<?php echo $active['total']; ?>, <?php echo $blocked['total']; ?>],

                    backgroundColor: [
                        '#2ecc71',
                        '#e74c3c'
                    ]
                }]

            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }

        });
    </script>


    <!-- Location CHart -->
    <script>
        const locationCtx = document.getElementById('locationChart');

        new Chart(locationCtx, {

            type: 'bar',

            data: {
                labels: ['Locations'],

                datasets: [{
                    label: 'Total Locations',
                    data: [<?php echo $locationCount['total']; ?>],
                    backgroundColor: '#3498db'
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false
            }

        });
    </script>

</body>

</html>
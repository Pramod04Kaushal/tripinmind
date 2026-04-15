<?php
include "config/db.php";
?>

<!DOCTYPE html>
<html>

<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="style.css">

    <link rel="icon" type="image" href="images/icon.png">
</head>

<body>
    <header class="dashboard-header">
        <h2>Welcome, User</h2>
        <a href="index.html" class="login-btn">Logout</a>
    </header>

    <section class="dashboard-section">
        <div class="section-content">
            <h3>Your Features</h3>
            <ul class="dashboard-list">
                <li>✔ Personalized Recommendations</li>
                <li>✔ Save Favorite Places</li>
                <li>✔ View One Day Trips</li>
            </ul>
        </div>
    </section>
</body>

</html>
<aside class="admin-sidebar">

    <h2 class="logo">
        <a href="index.php">TripInMind</a>
    </h2>

    <ul>

        <li class="<?php if (basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php') echo 'active'; ?>">
            <a href="admin_dashboard.php">Dashboard</a>
        </li>

        <li class="<?php if (basename($_SERVER['PHP_SELF']) == 'add_location.php') echo 'active'; ?>">
            <a href="add_location.php">Add Location</a>
        </li>

        <li class="<?php if (basename($_SERVER['PHP_SELF']) == 'manage_location.php') echo 'active'; ?>">
            <a href="manage_location.php">Manage Locations</a>
        </li>

        <li class="<?php if (basename($_SERVER['PHP_SELF']) == 'user_managment.php') echo 'active'; ?>">
            <a href="user_managment.php">Users</a>
        </li>

        <li>
            <a href="logout.php">Logout</a>
        </li>

    </ul>

</aside>
<?php
include "config/db.php";

$query = "SELECT * FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!-- Get Total User Count -->
<?php

$total_users_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
$total_users = mysqli_fetch_assoc($total_users_query);

?>

<!-- Get Active User Count -->
<?php

$active_users_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE is_active = 1");
$active_users = mysqli_fetch_assoc($active_users_query);

?>

<!-- Get Blocked User Count -->
<?php

$blocked_users_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE is_active = 0");
$blocked_users = mysqli_fetch_assoc($blocked_users_query);

?>



<!DOCTYPE html>
<html>

<head>
    <title>User Management | TripInMind</title>
    <link rel="stylesheet" href="admin_style.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin_sidebar.css">
</head>

<body>


    <div class="admin-layout">

        <?php include "admin_sidebar.php"; ?>

        <main class="admin-content">

            <h2>Manage Users</h2>

            <div class="admin-cards">

                <div class="admin-card">
                    <h4>Total Users</h4>
                    <p><?php echo $total_users['total']; ?></p>
                </div>

                <div class="admin-card">
                    <h4>Active Users</h4>
                    <p><?php echo $active_users['total']; ?></p>
                </div>

                <div class="admin-card">
                    <h4>Blocked Users</h4>
                    <p><?php echo $blocked_users['total']; ?></p>
                </div>

            </div>

            <br>

            <div class="table-controls">

                <input type="text" id="searchUser" class="search-box" placeholder="Search users...">

            </div>

            <table class="manage-table" id="userTable">

                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>

                <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                    <tr>

                        <td><?php echo $row['user_id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['role']; ?></td>

                        <td>
                            <?php if ($row['is_active'] == 1) { ?>
                                <span class="status active">Active</span>
                            <?php } else { ?>
                                <span class="status inactive">Inactive</span>
                            <?php } ?>
                        </td>

                        <td class="actions">

                            <a class="toggle-btn"
                                href="toggle_user.php?id=<?php echo $row['user_id']; ?>">
                                Toggle
                            </a>

                            <a class="delete-btn"
                                href="delete_user.php?id=<?php echo $row['user_id']; ?>"
                                onclick="return confirm('Delete user?')">
                                🗑 Delete
                            </a>

                        </td>

                    </tr>

                <?php } ?>

            </table>

        </main>

    </div>

    </div>

    <!-- LIVE SEARCH -->
    <script>
        document.getElementById("searchUser").addEventListener("keyup", function() {

            let searchValue = this.value.toLowerCase();

            let table = document.getElementById("userTable");

            let rows = table.getElementsByTagName("tr");

            for (let i = 1; i < rows.length; i++) {

                let text = rows[i].textContent.toLowerCase();

                if (text.includes(searchValue)) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }

            }

        });
    </script>




</body>

</html>
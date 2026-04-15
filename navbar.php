<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include "config/db.php";

if (isset($_SESSION['user_id'])) {

    $user_id = $_SESSION['user_id'];

    $query = "
SELECT profile_image, username
FROM users
WHERE user_id = '$user_id'
";

    $result = mysqli_query($conn, $query);

    $user = mysqli_fetch_assoc($result);
}
?>




<header class="navbar">

    <nav class="section-content nav-container">

        <a href="index.php" class="nav-logo">
            <span class="logo-text">
                Trip<span class="logo-mind">InMind</span>
            </span>
        </a>

        <ul class="nav-menu">
            <li><a href="index.php#categories" class="nav-link">Categories</a></li>

            <li><a href="index.php#popular" class="nav-link">Popular</a></li>

            <li><a href="index.php#recommendations" class="nav-link">Recommendations</a></li>
            <li><a href="index.php#about" class="nav-link">About</a></li>

            <!-- <li><a href="ai_recommend.php" class="nav-link">Suggestions</a></li>    -->
        </ul>

        <!-- Auth buttons -->
        <div class="auth-buttons">

            <button id="themeToggle" class="theme-toggle">🌙</button>

            <?php if (isset($_SESSION['user_id'])) { ?>

                <div class="user-menu">

                    <!-- Profile Picture -->
                    <div class="user-avatar">

                        <?php if (!empty($user['profile_image'])) { ?>

                            <img src="uploads/<?php echo $user['profile_image']; ?>">

                        <?php } else { ?>

                            <div class="avatar-letter">

                                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>

                            </div>

                        <?php } ?>

                    </div>

                    <div class="dropdown-menu">

                        <!-- show only for admin -->
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>

                            <a href="admin_dashboard.php">
                                Dashboard
                            </a>

                        <?php } ?>

                        <!-- show for all logged users -->
                        <a href="profile.php">
                            User Profile
                        </a>

                        <a href="logout.php">
                            Logout
                        </a>

                    </div>

                </div>

            <?php } else { ?>

                <button onclick="openLoginPopup()" class="login-btn">
                    Login
                </button>

                <button onclick="openRegisterPopup()" class="register-btn">
                    Sign Up
                </button>

            <?php } ?>

        </div>
    </nav>
</header>

<script>
    document.addEventListener("click", function(e) {

        const avatar = e.target.closest(".user-avatar");
        const menu = document.querySelector(".dropdown-menu");

        if (!menu) return;

        /* click on avatar */
        if (avatar) {

            e.stopPropagation();

            menu.classList.toggle("show");

            return;

        }

        /* click anywhere else */
        menu.classList.remove("show");

    });
</script>
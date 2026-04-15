<!-- LOGIN POPUP -->
<div id="loginModal" class="auth-modal">

    <div class="auth-box">

        <span class="close-auth" onclick="closeAuth()">×</span>

        <h2>Login</h2>

        <form action="login.php" method="POST">

            <input type="hidden"
                name="redirect_url"
                value="<?php echo $_SERVER['REQUEST_URI']; ?>">

            <input type="email"
                name="email"
                placeholder="Email"
                required>

            <input type="password"
                name="password"
                placeholder="Password"
                required>

            <button type="submit">Login</button>

            <p style="font-size:13px;text-align:center;margin-top:10px;">
                Don't have account?
                <a href="#" onclick="switchToRegister()">Register</a>
            </p>

        </form>

    </div>

</div>


<!-- REGISTER POPUP -->
<div id="registerModal" class="auth-modal">

    <div class="auth-box modern-auth">

        <span class="close-auth" onclick="closeAuth()">×</span>

        <h2>Create Account</h2>

        <p class="auth-subtitle">
            Register to get personalized travel recommendations
        </p>

        <form action="register.php" method="POST">

            <input type="text"
                name="username"
                placeholder="Full Name"
                required>

            <input type="email"
                name="email"
                placeholder="Email"
                required>

            <input type="password"
                name="password"
                placeholder="Password"
                required>

            <input type="password"
                name="confirm_password"
                placeholder="Confirm Password"
                required>

            <button type="submit">
                Register
            </button>

        </form>

        <p class="auth-switch">
            Already have an account?
            <a onclick="switchToLogin()">Login</a>
        </p>

    </div>

</div>



<script>
    function openLoginPopup() {

        document.getElementById("loginModal").style.display = "flex";

    }

    function openRegisterPopup() {

        document.getElementById("registerModal").style.display = "flex";

    }

    function switchToRegister() {

        document.getElementById("loginModal").style.display = "none";

        document.getElementById("registerModal").style.display = "flex";

    }

    function switchToLogin() {

        document.getElementById("registerModal").style.display = "none";

        document.getElementById("loginModal").style.display = "flex";

    }

    function closeAuth() {

        document.getElementById("loginModal").style.display = "none";

        document.getElementById("registerModal").style.display = "none";

    }

    /* close outside click */

    window.onclick = function(event) {

        const loginModal = document.getElementById("loginModal");

        const registerModal = document.getElementById("registerModal");

        if (event.target == loginModal) {

            loginModal.style.display = "none";

        }

        if (event.target == registerModal) {

            registerModal.style.display = "none";

        }

    }

    /* reopen login if error */

    const params = new URLSearchParams(window.location.search);

    /* open login only if NOT logged in */
    <?php if (!isset($_SESSION['user_id'])) { ?>

        if (params.get("openLogin") == "1") {

            openLoginPopup();

            /* remove parameter after opening */
            window.history.replaceState({}, document.title, window.location.pathname);

        }

    <?php } ?>
</script>


<?php if (isset($_SESSION['user_id'])) { ?>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const loginModal = document.getElementById("loginModal");
            const registerModal = document.getElementById("registerModal");

            if (loginModal) loginModal.style.display = "none";
            if (registerModal) registerModal.style.display = "none";

        });
    </script>

<?php } ?>
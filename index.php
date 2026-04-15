<?php
include "config/db.php";

session_start();

/* ONE DAY TRIPS */

$one_day_query = "

SELECT 
location.location_id,
location.location_name,
location.description,
province.province_name,

(SELECT media_url 
 FROM location_media 
 WHERE location_media.location_id = location.location_id 
 LIMIT 1) AS media_url

FROM location

LEFT JOIN province
ON location.province_id = province.province_id

WHERE location.is_one_day_trip = 1
AND location.is_active = 1

";

$one_day_result = mysqli_query($conn, $one_day_query);

/* MOST POPULAR DESTINATIONS */

$popular_query = "

SELECT 
location.location_id,
location.location_name,
location_media.media_url,

COUNT(DISTINCT likes.like_id) AS total_likes,
COUNT(DISTINCT comment.comment_id) AS total_comments,

(COUNT(DISTINCT likes.like_id) + COUNT(DISTINCT comment.comment_id)) AS popularity_score

FROM location

LEFT JOIN likes 
ON location.location_id = likes.location_id

LEFT JOIN comment
ON location.location_id = comment.location_id

LEFT JOIN location_media
ON location.location_id = location_media.location_id

WHERE location.is_active = 1

GROUP BY location.location_id

ORDER BY popularity_score DESC

LIMIT 11

";

$popular_result = mysqli_query($conn, $popular_query);


/* GET CURRENT MONTH */
$current_month = date("F");

/* GET NEXT MONTH */
$next_month = date("F", strtotime("+1 month"));

$hurry_query = "

SELECT 
location.location_id,
location.location_name,
location_media.media_url

FROM location

JOIN location_season
ON location.location_id = location_season.location_id

JOIN season
ON location_season.season_id = season.season_id

LEFT JOIN location_media
ON location.location_id = location_media.location_id

WHERE season.season_name = '$current_month'
AND location.is_active = 1

GROUP BY location.location_id
LIMIT 8

";

$hurry_result = mysqli_query($conn, $hurry_query);

$next_query = "

SELECT 
location.location_id,
location.location_name,
location_media.media_url

FROM location

JOIN location_season
ON location.location_id = location_season.location_id

JOIN season
ON location_season.season_id = season.season_id

LEFT JOIN location_media
ON location.location_id = location_media.location_id

WHERE season.season_name = '$next_month'
AND location.is_active = 1

GROUP BY location.location_id
LIMIT 4

";

$next_result = mysqli_query($conn, $next_query);

?>



<!DOCTYPE html>
<html>

<head>
    <title>TripInMind</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="popup_message.css">
    <link rel="stylesheet" href="navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600&display=swap" rel="stylesheet">

    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="icon" type="image" href="images/icon.png">

</head>

<body>
    <!--NAV BAR-->
    <div class="home-navbar">
        <?php include "navbar.php"; ?>
    </div>

    <!-- AUTH POPUP -->


    <!--Header / Navbar-->
    <!--
    <header>
        <nav class="navbar section-content">

            <a href="#" class="nav-logo">
                <span class="logo-text">
                    Trip<span class="logo-mind">InMind</span>
                </span>
            </a>

            <ul class="nav-menu">
                <li><a href="#oneday" class="nav-link">One Day Trip</a></li>
                <li><a href="#popular" class="nav-link">Popular Places</a></li>
                <li><a href="#categories" class="nav-link">Trip Ideas</a></li>
                <li><a href="#recommendations" class="nav-link">Get Recommendations</a></li>
                <li><a href="#about" class="nav-link">About</a></li>
            </ul>


            <div class="auth-buttons">

                <button id="themeToggle" class="theme-toggle">🌙</button>

                <?php if (isset($_SESSION['username'])) { ?>

                    <div class="user-menu">

                        <span class="user-name">
                            👤 <?php echo $_SESSION['username']; ?> ▼
                        </span>

                        <div class="dropdown-menu">

                            <a href="profile.php">
                                👤 User Profile
                            </a>

                            <a href="logout.php">
                                🚪 Logout
                            </a>

                        </div>

                    </div>

                <?php } else { ?>

                    <button id="loginBtn" class="login-btn">Login</button>
                    <button id="registerBtn" class="register-btn">Sign Up</button>

                <?php } ?>

            </div>
        </nav>
    </header>
-->

    <main>
        <!--Hero Setion-->
        <section class="hero-section">

            <div class="hero-slideshow">

                <div class="slide active"
                    style="background-image:url('images/home1.png')"></div>

                <div class="slide"
                    style="background-image:url('images/home2.png')"></div>

                <div class="slide"
                    style="background-image:url('images/home3.png')"></div>

                <div class="slide"
                    style="background-image:url('images/home4.png')"></div>

                <div class="slide"
                    style="background-image:url('images/home5.png')"></div>

            </div>

            <div class="hero-overlay">

                <h1 class="hero-title">
                    Trip<span>InMind</span>
                </h1>

                <p class="hero-subtitle">
                    <!-- FIND THE PLACE IN YOUR MIND -->
                    FIND THE PLACE YOU HAVE IN MIND
                </p>

            </div>

            <a href="#categories" class="scroll-indicator">
                Explore
            </a>


        </section>
        <!--

        <section class="how-it-works-section">
            <div class="section-content">
                <div class="section-header">
                    <h2 class="section-title">How TripInMind Works</h2>
                    <p class="section-subtitle">
                        From an idea in your mind to a well-planned journey
                    </p>
                </div>

                <div class="steps-container">
                    <div class="step-card" data-aos="fade-up">
                        <h3>🧠 Share your trip idea</h3>
                        <p>Tell us your purpose, time and travel preferences.</p>
                    </div>

                    <div class="step-card" data-aos="zoom-in">
                        <h3>📍 Get smart suggestions</h3>
                        <p>We recommend destinations that best match your idea.</p>
                    </div>

                    <div class="step-card" data-aos="fade-up">
                        <h3>🗺️ Plan your journey</h3>
                        <p>Compare places and finalize your perfect trip.</p>
                    </div>
                </div>
            </div>
        </section>
        -->

        <!--Categories Section-->
        <section class="categories-section" id="categories">
            <div class="section-content">

                <div class="section-header">
                    <h2 class="section-title">Travel Categories</h2>
                    <p class="section-subtitle">
                        Choose the type of destination you want to explore in Sri Lanka.
                    </p>
                </div>

                <div class="categories-grid">

                    <a href="category_locations.php?category_id=1" class="category-card big" data-aos="zoom-in">
                        <img src="images/leisure.png">
                        <h3>Leisure & Relaxation</h3>
                    </a>

                    <a href="category_locations.php?category_id=2" class="category-card wide" data-aos="zoom-in">
                        <img src="images/adventure.png">
                        <h3>Adventure & Exploration</h3>
                    </a>

                    <a href="category_locations.php?category_id=3" class="category-card small" data-aos="zoom-in">
                        <img src="images/religious.png">
                        <h3>Cultural & Religious</h3>
                    </a>

                    <a href="category_locations.php?category_id=4" class="category-card tall" data-aos="zoom-in">
                        <img src="images/wild.png">
                        <h3>Wildlife & Nature</h3>
                    </a>

                    <a href="category_locations.php?category_id=5" class="category-card small" data-aos="zoom-in">
                        <img src="images/art.png">
                        <h3>Educational</h3>
                    </a>

                    <a href="category_locations.php?category_id=6" class="category-card medium" data-aos="zoom-in">
                        <img src="images/family1.png">
                        <h3>Family & Friends</h3>
                    </a>

                    <a href="oneday_trips.php" class="category-card medium" data-aos="zoom-in">
                        <img src="images/oneday.png">
                        <h3>One Day Trips</h3>
                    </a>

                    <a href="ai_recommend.php" class="category-card wide" data-aos="zoom-in">
                        <img src="images/ai.png">
                        <h3>AI Recommendations</h3>
                    </a>

                </div>



            </div>

        </section>

        <!--One Day Trips-->
        <!--
        <section class="oneday-section" id="oneday">
            <div class="section-content">

                <div class="section-header">
                    <h2 class="section-title">One Day Trips</h2>
                    <p class="section-subtitle">
                        Ideal destinations for short and refreshing trips
                    </p>
                </div>

                <div class="scroll-wrapper">

                    <button class="scroll-btn left" onclick="scrollTrips(-300)">❮</button>

                    <div class="trip-container" id="tripScroll">

                        <?php while ($row = mysqli_fetch_assoc($one_day_result)) { ?>

                            <a href="location_details.php?id=<?php echo $row['location_id']; ?>" class="trip-card">

                                <img src="<?php echo $row['media_url']; ?>">

                                <div class="trip-content">

                                    <h3><?php echo $row['location_name']; ?></h3>

                                    <p class="province">
                                        <?php echo $row['province_name']; ?> Province
                                    </p>

                                    <p>
                                        <?php echo substr($row['description'], 0, 80); ?>...
                                    </p>

                                </div>

                            </a>

                        <?php } ?>

                    </div>
                    <button class="scroll-btn right" onclick="scrollTrips(300)"></button>
                </div>
            </div>
        </section>
-->


        <!-- Most Popular Destinations Section -->
        <section class="popular-section" id="popular">

            <div class="section-content">

                <div class="section-header">

                    <h2 class="section-title">
                        Most Popular Destinations
                    </h2>

                    <p class="section-subtitle">
                        Trending places travelers love right now
                    </p>

                </div>



                <div class="popular-layout">

                    <?php
                    $count = 0;

                    while ($row = mysqli_fetch_assoc($popular_result)) {

                        if ($count == 0) {
                    ?>

                            <!-- BIG FEATURED CARD -->
                            <a href="location_details.php?id=<?php echo $row['location_id']; ?>"
                                class="popular-feature"
                                data-aos="fade-up">

                                <img src="<?php echo $row['media_url']; ?>">

                                <div class="like-badge">

                                    <i class="fa-solid fa-heart"></i>

                                    <?php echo $row['total_likes']; ?>

                                </div>

                                <div class="popular-overlay">

                                    <span class="popular-badge">
                                        Most Loved
                                    </span>

                                    <h2>

                                        <?php echo $row['location_name']; ?>

                                    </h2>

                                </div>

                            </a>

                        <?php } else { ?>

                            <!-- NORMAL CARDS -->
                            <a href="location_details.php?id=<?php echo $row['location_id']; ?>"
                                class="popular-card"
                                data-aos="fade-up"
                                data-aos-delay="<?php echo $count * 80; ?>">

                                <img src="<?php echo $row['media_url']; ?>">

                                <div class="like-badge">

                                    <i class="fa-solid fa-heart"></i>

                                    <?php echo $row['total_likes']; ?>

                                </div>

                                <div class="popular-overlay">

                                    <h3>

                                        <?php echo $row['location_name']; ?>

                                    </h3>

                                </div>

                            </a>

                    <?php }

                        $count++;
                    }
                    ?>

                </div>

            </div>

        </section>


        <!--Seasonal Recommendations Section-->
        <section class="recommendations-section" id="recommendations">
            <div class="section-content">
                <div class="section-header">
                    <h2 class="section-title">Seasonal Travel Recommendations</h2>
                    <p class="section-subtitle" id="season-text">
                        Places that are great to visit right now.
                    </p>
                </div>
                <!-- CURRENT MONTH -->
                <h3 class="season-title">
                    Best Spots for <?php echo $current_month; ?>
                </h3>

                <div class="season-layout">

                    <?php
                    $count = 0;

                    while ($row = mysqli_fetch_assoc($hurry_result)) {

                        if ($count == 0) {
                    ?>

                            <a href="location_details.php?id=<?php echo $row['location_id']; ?>"
                                class="season-feature">

                                <img src="<?php echo $row['media_url']; ?>">

                                <div class="season-overlay">

                                    <span class="season-badge">
                                        Best this month
                                    </span>

                                    <h2>

                                        <?php echo $row['location_name']; ?>

                                    </h2>

                                </div>

                            </a>

                        <?php } else { ?>

                            <a href="location_details.php?id=<?php echo $row['location_id']; ?>"
                                class="season-card">

                                <img src="<?php echo $row['media_url']; ?>">

                                <div class="season-overlay">

                                    <h3>

                                        <?php echo $row['location_name']; ?>

                                    </h3>

                                </div>

                            </a>

                    <?php }

                        $count++;
                    }
                    ?>

                </div>



                <!-- NEXT MONTH -->
                <h3 class="season-title">
                    Thinking About <?php echo $next_month; ?> ?

                </h3>

                <div class="season-grid">

                    <?php while ($row = mysqli_fetch_assoc($next_result)) { ?>

                        <a href="location_details.php?id=<?php echo $row['location_id']; ?>"
                            class="season-card">

                            <img src="<?php echo $row['media_url']; ?>">

                            <div class="season-overlay">

                                <h3>

                                    <?php echo $row['location_name']; ?>

                                </h3>

                            </div>

                        </a>

                    <?php } ?>

                </div>
            </div>

        </section>

        <!-- About Section -->
        <section class="about-section" id="about">

            <div class="section-content">

                <p class="about-label">ABOUT TRIPINMIND</p>

                <h2 class="about-title">
                    <!-- Smart travel planning made simple -->
                    Smart Travel Planning Made Simple
                </h2>

                <p class="about-description">

                    TripInMind is a personalized travel destination recommendation platform designed to help travelers discover the most suitable places in Sri Lanka based on their interests, available time, and travel preferences.

                    Our system combines travel categories, seasonal insights, and popular destinations to provide recommendations that make trip planning faster, easier, and more enjoyable.

                </p>


                <div class="about-grid">

                    <div class="about-card" data-aos="fade-up">

                        <div class="about-icon">🧠</div>

                        <h3>Personalized Suggestions</h3>

                        <p>
                            Get destination recommendations based on your travel preferences and interests.
                        </p>

                    </div>


                    <div class="about-card" data-aos="fade-up" data-aos-delay="100">

                        <div class="about-icon">⏳</div>

                        <h3>Save Planning Time</h3>

                        <p>
                            Quickly discover suitable destinations without spending hours searching.
                        </p>

                    </div>


                    <div class="about-card" data-aos="fade-up" data-aos-delay="200">

                        <div class="about-icon">📍</div>

                        <h3>Explore Top Places</h3>

                        <p>
                            Find popular, seasonal, and one-day trip destinations across Sri Lanka.
                        </p>

                    </div>


                    <div class="about-card" data-aos="fade-up" data-aos-delay="300">

                        <div class="about-icon">🌿</div>

                        <h3>Travel Categories</h3>

                        <p>
                            Browse destinations based on nature, adventure, culture, and relaxation.
                        </p>

                    </div>

                </div>

            </div>

        </section>

        <!-- Footer Section -->
        <footer class="footer-section">
            <div class="section-content">

                <div class="footer-container">

                    <!-- Footer About -->
                    <div class="footer-box">
                        <h3>TripInMind</h3>
                        <p>
                            TripInMind is a personalized travel destination recommendation system
                            designed to help users turn their trip ideas into perfect journeys across Sri Lanka.
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div class="footer-box">
                        <h3>Quick Links</h3>
                        <ul>
                            <li><a href="#">Home</a></li>
                            <li><a href="#">Categories</a></li>
                            <li><a href="#">One Day Trips</a></li>
                            <li><a href="#">Popular Places</a></li>
                        </ul>
                    </div>

                    <!-- Contact Info -->
                    <div class="footer-box">
                        <h3>Contact</h3>
                        <p>Email: tripinmind2026@gmail.com</p>
                        <p>Phone: +94 77 123 4567</p>
                        <p>Location: Sri Lanka</p>
                    </div>

                </div>

                <div class="footer-bottom">
                    <p>© 2026 TripInMind | Academic Project – Sri Lanka</p>
    </main>







    <!-- NAVBAR SCROLL EFFECT -->
    <script>
        window.addEventListener("scroll", function() {

            const navbar = document.querySelector(".navbar");
            const hero = document.querySelector(".hero-section");

            const heroBottom = hero.offsetHeight;

            if (window.scrollY >= heroBottom - 80) {

                navbar.classList.add("scrolled");

            } else {

                navbar.classList.remove("scrolled");

            }

        });
    </script>

    <script>
        function scrollPopular(value) {

            document.getElementById("popularScroll").scrollBy({

                left: value,
                behavior: "smooth"

            });

        }
    </script>





    <script>
        // Dark mode toggle
        const toggleBtn = document.getElementById("themeToggle");

        // Load saved theme
        if (localStorage.getItem("theme") === "dark") {
            document.body.classList.add("dark-mode");
            toggleBtn.textContent = "☀️";
        }

        toggleBtn.addEventListener("click", () => {
            document.body.classList.toggle("dark-mode");

            const isDark = document.body.classList.contains("dark-mode");
            toggleBtn.textContent = isDark ? "☀️" : "🌙";
            localStorage.setItem("theme", isDark ? "dark" : "light");
        });
    </script>

    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

    <script>
        AOS.init({
            duration: 900,
            easing: 'ease-in-out',
            once: true
        });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

    <script>
        const container = document.getElementById('hero-3d');

        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, 1, 0.1, 1000);

        const renderer = new THREE.WebGLRenderer({
            alpha: true,
            antialias: true
        });
        renderer.setSize(container.clientWidth, container.clientHeight);
        container.appendChild(renderer.domElement);

        // Create Geometry (You can replace with Yaka model later)
        const geometry = new THREE.TorusKnotGeometry(1, 0.3, 150, 20);
        const material = new THREE.MeshStandardMaterial({
            color: 0x1DB954,
            metalness: 0.6,
            roughness: 0.3
        });
        const mesh = new THREE.Mesh(geometry, material);
        scene.add(mesh);

        // Light
        const light = new THREE.PointLight(0xffffff, 1);
        light.position.set(5, 5, 5);
        scene.add(light);

        const ambient = new THREE.AmbientLight(0xffffff, 0.5);
        scene.add(ambient);

        camera.position.z = 4;

        // Animation
        function animate() {
            requestAnimationFrame(animate);
            mesh.rotation.x += 0.01;
            mesh.rotation.y += 0.01;
            renderer.render(scene, camera);
        }

        animate();

        // Mouse interaction
        document.addEventListener('mousemove', (event) => {
            const mouseX = (event.clientX / window.innerWidth) * 2 - 1;
            const mouseY = -(event.clientY / window.innerHeight) * 2 + 1;

            mesh.rotation.y = mouseX * 1.5;
            mesh.rotation.x = mouseY * 1.5;
        });
    </script>


    <script>
        function scrollTrips(value) {
            document.getElementById("tripScroll").scrollBy({
                left: value,
                behavior: "smooth"
            });
        }
    </script>

    <script>
        const slider = document.getElementById('tripScroll');

        let isDown = false;
        let startX;
        let scrollLeft;

        slider.addEventListener('mousedown', (e) => {

            isDown = true;
            slider.classList.add('active');

            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;

        });

        slider.addEventListener('mouseleave', () => {
            isDown = false;
        });

        slider.addEventListener('mouseup', () => {
            isDown = false;
        });

        slider.addEventListener('mousemove', (e) => {

            if (!isDown) return;

            e.preventDefault();

            const x = e.pageX - slider.offsetLeft;
            const walk = (x - startX) * 2;

            slider.scrollLeft = scrollLeft - walk;

        });
    </script>

    <!-- Hero Slideshow Script -->
    <script>
        let slides = document.querySelectorAll(".slide");
        let currentSlide = 0;

        function changeSlide() {

            slides[currentSlide].style.opacity = "0";

            currentSlide++;

            if (currentSlide >= slides.length) {
                currentSlide = 0;
            }

            slides[currentSlide].style.opacity = "1";

        }

        setInterval(changeSlide, 10000);
    </script>

    <script>
        setTimeout(function() {

            let msg = document.getElementById("successMessage");

            if (msg) {

                msg.style.opacity = "0";

                setTimeout(() => {
                    msg.style.display = "none";
                }, 500);

            }

        }, 3000);
    </script>


    <?php include "auth_popup.php"; ?>
</body>

</html>
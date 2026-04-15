<?php

session_start();
include "config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $redirect = $_POST['redirect_url']; // page user came from

    $sql = "SELECT * FROM users 
WHERE email='$email'
AND is_active = 1";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {

        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password_hash'])) {

            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            $_SESSION['success'] = "Login successful";

            /* admin redirect */
            if ($row['role'] == "admin") {

                header("Location: admin_dashboard.php");
            } else {

                header("Location: $redirect");
            }

            exit();
        } else {

            $_SESSION['error'] = "Wrong password";

            header("Location: $redirect?openLogin=1");

            exit();
        }
    } else {

        $_SESSION['error'] = "User not found";

        header("Location: $redirect?openLogin=1");

        exit();
    }
}

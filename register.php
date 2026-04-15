<?php
session_start();
include "config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password == $confirm_password) {

        // check email OR username already exists
        $check = "SELECT * FROM users 
        WHERE email='$email' 
        OR username='$fullname'";

        $result = mysqli_query($conn, $check);

        if (mysqli_num_rows($result) > 0) {

            $row = mysqli_fetch_assoc($result);

            if ($row['email'] == $email) {

                $_SESSION['error'] = "Email already registered!";
            } else {

                $_SESSION['error'] = "Username already taken!";
            }

            header("Location: index.php?openRegister=1");
            exit();
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users 
            (username, email, password_hash, role, is_active)
            VALUES 
            ('$fullname', '$email', '$hashed_password', 'user', 1)";

            if (mysqli_query($conn, $sql)) {

                $_SESSION['success'] = "Registration successful";

                header("Location: index.php?openLogin=1");
                exit();
            } else {

                $_SESSION['error'] = "Something went wrong!";
                header("Location: index.php?openRegister=1");
                exit();
            }
        }
    } else {

        $_SESSION['error'] = "Passwords do not match";
        header("Location: index.php?openRegister=1");
        exit();
    }
}

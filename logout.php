<?php

session_start();

/* remove popup messages */
unset($_SESSION['success']);
unset($_SESSION['error']);

/* destroy session */
session_unset();
session_destroy();

/* redirect to home page */
header("Location: index.php");
exit();

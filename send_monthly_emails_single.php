<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

include "config/db.php";

/* current user */

$user_id = $_SESSION['user_id'];

$current_month = date("n");

/* get user info */

$user_query = mysqli_query($conn, "

SELECT users.email,
users.username,

user_preferences.preferred_category,
user_preferences.preferred_trip_type

FROM users

JOIN user_preferences
ON users.user_id = user_preferences.user_id

WHERE users.user_id='$user_id'

");

$user = mysqli_fetch_assoc($user_query);

$email = $user['email'];
$name = $user['username'];

/* filters */

$category_filter = "";

if (!empty($user['preferred_category'])) {

    $category_filter = "AND location.category_id='" . $user['preferred_category'] . "'";
}

$trip_filter = "";

if ($user['preferred_trip_type'] == "one_day") {

    $trip_filter = "AND location.is_one_day_trip=1";
}

/* get locations */

$locations = mysqli_query($conn, "

SELECT location.location_name

FROM location

JOIN location_season
ON location.location_id = location_season.location_id

WHERE location_season.season_id='$current_month'

AND location.is_active=1

$category_filter

$trip_filter

LIMIT 5

");

/* email message (beautiful template) */

$message = "

<div style='font-family: Arial, sans-serif; background:#f4f6f9; padding:20px;'>

<div style='max-width:600px; margin:auto; background:white; border-radius:12px; overflow:hidden; box-shadow:0 4px 15px rgba(0,0,0,0.08);'>

<!-- HEADER -->
<div style='background:linear-gradient(135deg,#3498db,#2ecc71); color:white; padding:25px; text-align:center;'>

<h2 style='margin:0;'>TripInMind</h2>
<p style='margin:5px 0 0;'>Your Travel Companion</p>

</div>

<!-- BODY -->
<div style='padding:25px;'>

<h3>Hello $name</h3>

<p style='font-size:15px; line-height:1.6;'>
<strong>You have successfully subscribed to Monthly Recommendations!</strong>
</p>

<p style='font-size:14px; color:#555;'>
Every month, we will suggest the best places to visit based on your preferences and the best travel season.
</p>

<p style='margin-top:20px; font-size:15px;'><b>Top places to visit this month:</b></p>

<ul style='padding-left:20px; line-height:1.8; font-size:14px; color:#333;'>
";

while ($place = mysqli_fetch_assoc($locations)) {

    $message .= "<li>" . $place['location_name'] . "</li>";
}

$message .= "

</ul>

<div style='text-align:center; margin:25px 0;'>

<a href='http://localhost/tripinmind'
style='background:#3498db;
color:white;
padding:12px 25px;
text-decoration:none;
border-radius:6px;
font-weight:bold;
display:inline-block;'>

Explore More Destinations

</a>

</div>

<p style='font-size:14px; color:#555; text-align:center;'>
We hope these suggestions help you plan an amazing trip ✈️
</p>

</div>

<!-- FOOTER -->
<div style='background:#ecf0f1; padding:15px; text-align:center; font-size:12px; color:#777;'>

You are receiving this email because you enabled monthly travel suggestions.<br>
© 2026 TripInMind

</div>

</div>

</div>

";

/* send */

$mail = new PHPMailer(true);

$mail->isSMTP();

$mail->Host = 'smtp.gmail.com';

$mail->SMTPAuth = true;

$mail->Username = 'Tripinmind2026@gmail.com';

$mail->Password = 'tekcvyhppwmydoct';

$mail->SMTPSecure = 'tls';

$mail->Port = 587;

$mail->setFrom('Tripinmind2026@gmail.com', 'TripInMind');

$mail->addAddress($email, $name);

$mail->isHTML(true);

$mail->Subject = "🎉 Subscription Successful - TripInMind Suggestions";

$mail->Body = $message;

$mail->send();

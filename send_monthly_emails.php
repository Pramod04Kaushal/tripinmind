<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

include "config/db.php";

/* current month */
$current_month = date("n");

/* get users who enabled emails */

$users = mysqli_query(

    $conn,

    "SELECT users.user_id,
users.email,
users.username,

user_preferences.preferred_category,
user_preferences.preferred_trip_type

FROM users

JOIN user_preferences
ON users.user_id = user_preferences.user_id

WHERE user_preferences.receive_email = 1

AND users.is_active = 1"

);

while ($user = mysqli_fetch_assoc($users)) {

    $email = $user['email'];

    $name = $user['username'];

    $category_filter = "";

    if (!empty($user['preferred_category'])) {

        $category_filter =
            "AND location.category_id='" . $user['preferred_category'] . "'";
    }

    $trip_filter = "";

    if ($user['preferred_trip_type'] == "one_day") {

        $trip_filter = "AND location.is_one_day_trip=1";
    }


    /* get recommended locations */

    $locations = mysqli_query(

        $conn,

        "

SELECT location.location_name

FROM location

JOIN location_season
ON location.location_id = location_season.location_id

WHERE location_season.season_id='$current_month'

AND location.is_active=1

$category_filter

$trip_filter

LIMIT 5

"

    );


    /* email message design */

    $message = "

<div style='font-family: Arial, sans-serif; background:#f5f7fa; padding:20px;'>

<div style='max-width:600px; margin:auto; background:white; border-radius:10px; overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.05);'>

<div style='background:#2c3e50; color:white; padding:20px; text-align:center;'>

<h2 style='margin:0;'>TripInMind</h2>

<p style='margin:5px 0 0;'>Your Monthly Travel Inspiration</p>

</div>

<div style='padding:25px;'>

<h3>Hello $name,</h3>

<p>Here are the best places to visit this month based on your travel preferences.</p>

<ul style='line-height:1.8; font-size:16px;'>";

    if (mysqli_num_rows($locations) > 0) {

        while ($place = mysqli_fetch_assoc($locations)) {

            $message .= "<li>" . $place['location_name'] . "</li>";
        }
    } else {

        $message .= "<li>Explore popular destinations on TripInMind</li>";
    }

    $message .= "

</ul>

<p style='margin-top:20px;'>

Discover more destinations and plan your perfect trip with TripInMind.

</p>

<div style='text-align:center; margin-top:25px;'>

<a href='http://localhost/tripinmind'

style='background:#3498db;
color:white;
padding:12px 20px;
text-decoration:none;
border-radius:5px;
font-weight:bold;'>

Explore Destinations

</a>

</div>

</div>

<div style='background:#ecf0f1; padding:15px; text-align:center; font-size:12px; color:#7f8c8d;'>

You are receiving this email because you enabled monthly travel suggestions.

</div>

</div>

</div>

";


    /* send email */

    $mail = new PHPMailer(true);

    $mail->SMTPDebug = 0;

    try {

        $mail->isSMTP();

        $mail->Host = 'smtp.gmail.com';

        $mail->SMTPAuth = true;

        /* replace with your gmail */

        $mail->Username = 'Tripinmind2026@gmail.com';

        /* replace with app password */

        $mail->Password = 'tekcvyhppwmydoct';

        $mail->SMTPSecure = 'tls';

        $mail->Port = 587;

        $mail->setFrom('Tripinmind2026@gmail.com', 'TripInMind');

        $mail->addAddress($email, $name);

        $mail->isHTML(true);

        $mail->Subject = "TripInMind Monthly Travel Suggestions";

        $mail->Body = $message;

        $mail->send();

        echo "Email sent to " . $email . "<br>";
    } catch (Exception $e) {

        echo "Error sending to " . $email . "<br>";
    }
}

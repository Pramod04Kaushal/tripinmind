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


    /* email message */

    $message = "Hello " . $name . ",<br><br>";

    $message .= "Best places to visit this month:<br><br>";

    while ($place = mysqli_fetch_assoc($locations)) {

        $message .= "• " . $place['location_name'] . "<br>";
    }

    $message .= "<br>Visit TripInMind to explore more.";


    /* send email */

    $mail = new PHPMailer(true);

    $mail->SMTPDebug = 2;
    $mail->Debugoutput = 'html';

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

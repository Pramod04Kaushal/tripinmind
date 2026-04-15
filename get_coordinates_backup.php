<?php
include "config/db.php";

$result = mysqli_query($conn, "SELECT * FROM location");

while ($row = mysqli_fetch_assoc($result)) {

    $name = $row['location_name'] . " Sri Lanka";

    $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($name) . "&format=json&limit=1";

    // ✅ USE CURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "TripInMindApp/1.0");

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (!empty($data)) {

        $lat = $data[0]['lat'];
        $lon = $data[0]['lon'];

        mysqli_query($conn, "
            UPDATE location 
            SET latitude='$lat', longitude='$lon'
            WHERE location_id='" . $row['location_id'] . "'
        ");

        echo $row['location_name'] . " updated<br>";

        sleep(1); // prevent blocking
    } else {
        echo $row['location_name'] . " not found<br>";
    }
}

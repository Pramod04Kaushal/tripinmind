<?php

$apiKey = "f40166278dae46ce821193018261803"; // paste your key here
$city = $location['location_name'];

$url = "http://api.weatherapi.com/v1/forecast.json?key=$apiKey&q=$city&days=7";

$response = file_get_contents($url);
$data = json_decode($response, true);

?>

<h2>Weather in <?php echo $city; ?></h2>

<p>Temperature: <?php echo $data['current']['temp_c']; ?>°C</p>
<p>Condition: <?php echo $data['current']['condition']['text']; ?></p>
<p>Humidity: <?php echo $data['current']['humidity']; ?>%</p>
<p>Wind: <?php echo $data['current']['wind_kph']; ?> km/h</p>
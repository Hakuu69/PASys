<?php
// signal_detector.php

$currentTime = time();
$maxWindSpeed = 0;  // Maximum wind speed in km/h over the next 24 hours

// The forecast API returns data in 3-hour intervals
foreach ($data['list'] as $forecast) {
    $forecastTime = $forecast['dt'];
    $hoursAhead = ($forecastTime - $currentTime) / 3600;
    
    // Consider only forecasts for the next 24 hours
    if ($hoursAhead > 0 && $hoursAhead <= 24) {
        // Convert wind speed from m/s to km/h (1 m/s = 3.6 km/h)
        $windSpeedKmh = $forecast['wind']['speed'] * 3.6;
        if ($windSpeedKmh > $maxWindSpeed) {
            $maxWindSpeed = $windSpeedKmh;
        }
    }
}

// Determine the PAGASA wind signal level based on maximum wind speed
$signalLevel = '';
if ($maxWindSpeed >= 221) {
    $signalLevel = 'Tropical Cyclone Wind Signal No. 5';
} elseif ($maxWindSpeed >= 171) {
    $signalLevel = 'Tropical Cyclone Wind Signal No. 4';
} elseif ($maxWindSpeed >= 121) {
    $signalLevel = 'Tropical Cyclone Wind Signal No. 3';
} elseif ($maxWindSpeed >= 61) {
    $signalLevel = 'Tropical Cyclone Wind Signal No. 2';
} elseif ($maxWindSpeed >= 30) {
    $signalLevel = 'Tropical Cyclone Wind Signal No. 1';
} else {
    $signalLevel = 'No Tropical Cyclone Wind Signal';
}

// Prepare the output message
$output = "Forecasted maximum wind speed in the next 24 hours: " . round($maxWindSpeed, 1) . " km/h.<br>";
$output .= "Alert: " . $signalLevel;

echo $output;
?>

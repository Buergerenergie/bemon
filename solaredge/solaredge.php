<?php

require __DIR__ . '/vendor/autoload.php';
#use influxdb/influxdb-php;

$APIkey = "SolaredgeAPIKey";
$ID = "SolarEdgeSiteID";
$influxhost = "";
$influxport = "8086";
$influxdbname = "";
$influxuser = urlencode("");
$influxpw = urlencode("");
$dev_name = "PV Musteranlage";
$dev_id = "xxx-00001";
$cooperative = "Beispiel eG";
$dev_location = "Musterstadt";	
$latitude = "50.000000";
$longitude = "13.0000000";

$content = file_get_contents("https://monitoringapi.solaredge.com/site/".$ID."/overview?api_key=".$APIkey );
$json=json_decode($content);
//print_r($json);
$unixtimestamp = strtotime($json->overview->lastUpdateTime);

// directly get the database object
$database = InfluxDB\Client::fromDSN(sprintf('influxdb://'.$influxuser.':'.$influxpw.'@%s:%s/%s', $influxhost, $influxport, $influxdbname));

$points = [
	new InfluxDB\Point(
		'energy', // the name of the measurement
		null, // measurement value
		['dev_id' => $dev_id, 'cooperative' => $cooperative, 'dev_name' => $dev_name, 'dev_location' => $dev_location], // measurement tags
		['harvestday' => (int)$json->overview->lastDayData->energy, 'harvestmonth' => (int)$json->overview->lastMonthData->energy, 'harvestyear' => (int)$json->overview->lastYearData->energy, 'power' => (int)$json->overview->currentPower->power, 'longitude' => (float)$longtitude, 'latitude' => (float)$latitude], $unixtimestamp
	)
];
$newPoints = $database->writePoints($points, InfluxDB\Database::PRECISION_SECONDS);

?> 

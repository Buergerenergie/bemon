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

echo "Last Update Time: ".$json->overview->lastUpdateTime;
echo PHP_EOL;
echo "Last Year Data: ".$json->overview->lastYearData->energy;
echo PHP_EOL;
echo "Last Month Data: ".$json->overview->lastMonthData->energy;
echo PHP_EOL;
echo "Last Day Data: ".$json->overview->lastDayData->energy;
echo PHP_EOL;
echo "Current Power: ".$json->overview->currentPower->power;

$unixtimestamp = strtotime($json->overview->lastUpdateTime);

// directly get the database object
$database = InfluxDB\Client::fromDSN(sprintf('influxdb://'.$influxuser.':'.$influxpw.'.@%s:%s/%s', $influxhost, $influxport, $influxdbname));
// get the client to retrieve other databases
$client = $database->getClient();

$points = [
	new InfluxDB\Point(
		'energy', // the name of the measurement
		null, // measurement value
		['dev_id' => $dev_id, 'cooperative' => $cooperative, 'dev_name' => $dev_name, 'dev_location' => $dev_location], // measurement tags
		['harvestday' => 10, 'harvestweek' => 1, 'harvestmonth' => 10, 'harvestyear' => 10, 'longitude' => 10, 'latitude' => 10], // measurement fields
		exec($unixtimestamp) // timestamp in nanoseconds on Linux ONLY
	)
];
$newPoints = $database->writePoints($points, InfluxDB\Database::PRECISION_SECONDS);

?> 

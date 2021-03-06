<?php
require("json.php");
require("sql.php");
$mysqli = getSqlInterface();

$action = $_GET["action"];
switch ($action) {
	case 'get':
		getChipData();
		break;
	case 'log':
		logDeviceData();
		break;
}

function getChipData() {
	if ($chipId = $_GET['chipId']) $whereStatement = "WHERE chipId = '$chipId'";
	
	$sql = "SELECT diameter, faintInterval, pushInterval, hasOled  FROM GreenAsh_Device $whereStatement";
	$result = sqlCommand($sql);
	
	$output = array();
	$output["version"] = "1.0";
	$output["data"] = array();
	
	while ($devices = $result->fetch_assoc()) {
		$output["data"][] = $devices;
	}
	
	echoJson($output);
	$result->close();
	closeSqlInterface();
}

function logDeviceData() {
	$chipId				= toValidSqlValue($_GET['chipId']);
	$dateTime			= toValidSqlValue(time());
	$distance			= toValidSqlValue($_GET['distance']);
	$speed				= toValidSqlValue($_GET['speed']);
	$cumulatedDistance	= toValidSqlValue($_GET['cumulatedDistance']);
	
	$sqlDateTime = $dateTime - ($dateTime % 60);

	$sql = "SELECT * FROM GreenAsh_Log WHERE chipId = '$chipId' AND dateTime = '$sqlDateTime'";
	$result = sqlCommand($sql);

	if ($entry = $result->fetch_assoc()) {
		$distance += $entry['distance'];
		updateSqlCommand("UPDATE GreenAsh_Log SET distance = '$distance', speed = '$speed', cumulatedDistance = '$cumulatedDistance' WHERE chipId = '$chipId' AND dateTime = '$sqlDateTime'");
		echo "Entry updated";
	} else {
		updateSqlCommand("INSERT INTO GreenAsh_Log (chipId, dateTime, distance, speed, cumulatedDistance) VALUES ('$chipId', '$sqlDateTime', '$distance','$speed', '$cumulatedDistance')");
		echo "Entry created";
	}
}
?>
<?php
require("json.php");
require("sql.php");
$mysqli = getSqlInterface();

$action = $_GET["action"];
switch ($action) {
	case 'getTeams':
		getTeams();
		break;
	case 'getLog':
		getLog();
		break;
	case 'getSetup':
		getSetup();
		break;
	case 'randomData':
		createRandomData();
		break;
	case 'updateTeam':
		updateTeam();
		break;
	case 'updateSetup':
		updateSetup();
		break;
	case 'deleteLog':
		deleteLog();
		break;
}

function updateTeam() {
	$field = $_POST['field'];
	$value = toValidSqlValue($_POST['value']);
	$chipId = $_POST['chipId'];
	
	$sqlStatement = "UPDATE GreenAsh_Device SET `$field` = '$value' WHERE chipId = '$chipId'";
	updateSqlCommand($sqlStatement);

	echoJson($_POST);
}

function deleteLog() {
	$chipId = $_GET['chipId'];
	
	$sqlStatement = "DELETE FROM GreenAsh_Log";
	if ($chipId != '') $sqlStatement = "DELETE FROM GreenAsh_Log WHERE chipId = '$chipId'";
	updateSqlCommand($sqlStatement);

	echoJson($_GET);
}

function createRandomData() {
	$startTimestamp = 1558248506;
	
	for ($j = 0; $j < 11; $j++) {
		$sqlStatement = "Insert Into `GreenAsh_Log` (`entryNo`, `chipId`, `dateTime`, `distance`, `speed`, `cumulatedDistance`)  values ";
		for ($i = 0; $i < 60*60; $i++) {
			$sqlStatement .= "( NULL, '1', '" . ($startTimestamp + i + ($j * 60*60)) . "', '" . (rand(28,55 + j)/10) . "', '" . (rand(5,20)) . "', ''), ";
			$sqlStatement .= "( NULL, '2', '" . ($startTimestamp + i + ($j * 60*60)) . "', '" . (rand(32,51 + j)/10) . "', '" . (rand(5,20)) . "', ''), ";
			$sqlStatement .= "( NULL, '3', '" . ($startTimestamp + i + ($j * 60*60)) . "', '" . (rand(36,46 + j)/10) . "', '" . (rand(5,20)) . "', ''), ";
			$sqlStatement .= "( NULL, '4', '" . ($startTimestamp + i + ($j * 60*60)) . "', '" . (rand(38,45 + j)/10) . "', '" . (rand(5,20)) . "', ''), ";
			$sqlStatement .= "( NULL, '5', '" . ($startTimestamp + i + ($j * 60*60)) . "', '" . (rand(35,42 + j)/10) . "', '" . (rand(5,20)) . "', ''), ";
			$sqlStatement .= "( NULL, '6', '" . ($startTimestamp + i + ($j * 60*60)) . "', '" . (rand(33,42 + j)/10) . "', '" . (rand(5,20)) . "', ''), ";
			$sqlStatement .= "( NULL, '7', '" . ($startTimestamp + i + ($j * 60*60)) . "', '" . (rand(20,48 + j)/10) . "', '" . (rand(5,20)) . "', ''), ";
			$sqlStatement .= "( NULL, '8', '" . ($startTimestamp + i + ($j * 60*60)) . "', '" . (rand(35,53 + j)/10) . "', '" . (rand(5,20)) . "', ''), ";
		}
		$sqlStatement = trim($sqlStatement," ,.");
		$result = sqlCommand($sqlStatement);	
	}
}

function getTeams() {
	$output = array();
	$output["version"] = "1.0";
	$output["data"] = array();
	
	$sqlStatement = "SELECT *, (SELECT SUM(distance) FROM GreenAsh_Log, GreenAsh_Setup WHERE (GreenAsh_Log.chipId = GreenAsh_Device.chipId) AND (GreenAsh_Log.dateTime >= GreenAsh_Setup.timeOffset)) AS distance, (SELECT datetime FROM GreenAsh_Log WHERE GreenAsh_Log.chipId = GreenAsh_Device.chipId ORDER BY datetime DESC LIMIT 1) AS heartbeat, (SELECT speed FROM GreenAsh_Log WHERE GreenAsh_Log.chipId = GreenAsh_Device.chipId ORDER BY datetime DESC LIMIT 1) AS speed FROM GreenAsh_Device";
	$result = sqlCommand($sqlStatement);
	
	$output["query"] = $sqlStatement;
	while ($devices = $result->fetch_assoc()) {
		$output["data"][] = $devices;
	}
	$result->close();
	closeSqlInterface();

	// print_r($output);
	echoJson($output);
}

function getSetup() {
	$output = array();
	$output["version"] = "1.0";
	$output["data"] = array();
	
	$sqlStatement = "SELECT * FROM GreenAsh_Setup";
	$result = sqlCommand($sqlStatement);
	
	$setup = $result->fetch_assoc();
	$output["query"] = $sqlStatement;
	$output["data"] = $setup;
	
	$result->close();
	closeSqlInterface();

	echoJson($output);
}

function updateSetup() {
	$field = $_POST['field'];
	$value = toValidSqlValue($_POST['value']);
	
	$sqlStatement = "UPDATE GreenAsh_Setup SET `$field` = '$value'";
	updateSqlCommand($sqlStatement);

	echoJson($_POST);
}

function getLog() {
	$output = array();
	$output["version"] = "1.0";
	$output["data"] = array();
	
	$minTime = singleSqlCommand("SELECT timeOffset FROM GreenAsh_Setup");
	// $maxTime = singleSqlCommand("SELECT MAX(dateTime) FROM GreenAsh_Log");
	$maxTime = time();
	
	getTimeIntervals($minTime, $maxTime, $intervalMin, $intervalMax, $labels);
	
	$sqlStatement = "";
	$sqlStatement .= "SELECT *,";

	for ($i = 0; $i < 12; $i++) {
		$sqlStatement .= "(SELECT SUM(distance) FROM GreenAsh_Log WHERE GreenAsh_Log.chipId = GreenAsh_Device.chipId AND (GreenAsh_Log.dateTime BETWEEN $intervalMin[$i] AND $intervalMax[$i])) AS `distanceInterval$i`, ";
	}
	$sqlStatement .= "(SELECT speed FROM GreenAsh_Log WHERE GreenAsh_Log.chipId = GreenAsh_Device.chipId ORDER BY dateTime DESC LIMIT 1) AS `currentSpeed`, ";
	$sqlStatement .= "(SELECT SUM(distance) FROM GreenAsh_Log WHERE GreenAsh_Log.chipId = GreenAsh_Device.chipId AND GreenAsh_Log.dateTime >= $minTime) AS `totalDistance` ";
	// $sqlStatement = trim($sqlStatement," ,.");
	
	$sqlStatement .= "FROM `GreenAsh_Device`";
	$result = sqlCommand($sqlStatement);
	
	$output["query"] = $sqlStatement;
	$output["data"]["labels"] = $labels;
	while ($team = $result->fetch_assoc()) {
		$output["data"]["teams"][] = $team;
	}	
	$result->close();
	closeSqlInterface();

	echoJson($output);
}

function getTimeIntervals($minTime, $maxTime, & $intervalMin, & $intervalMax, & $labels) {
	$timeDifference = $maxTime - $minTime;
	
	if ($timeDifference <= 720) {
		for ($i = 0; $i < 12; $i++) { // 12min
			$intervalMin[] = $minTime + ($i * 60);
			$intervalMax[] = $minTime + (($i + 1) * 60) - 1;
			$labels[] = ($i + 1) . "min";
		}
		return;
	}
	if ($timeDifference <= 3600) {
		for ($i = 0; $i < 12; $i++) { // 1h
			$intervalMin[] = $minTime + ($i * 300);
			$intervalMax[] = $minTime + (($i + 1) * 300) - 1;
			$labels[] = ($i + 1) * 2 . "min";
		}
		return;
	}
	if ($timeDifference <= 7200) { // 2h
		for ($i = 0; $i < 12; $i++) {
			$intervalMin[] = $minTime + ($i * 600);
			$intervalMax[] = $minTime + (($i + 1) * 600) - 1;
			$labels[] = ($i + 1) * 4 . "min";
		}
		return;
	}
	if ($timeDifference <= 21600) { // 6h
		for ($i = 0; $i < 12; $i++) {
			$intervalMin[] = $minTime + ($i * 1800);
			$intervalMax[] = $minTime + (($i + 1) * 1800) - 1;
			$labels[] = ($i + 1) * 12 . "min";
		}
		return;
	}
	if ($timeDifference <= 43200) { // 12h
		for ($i = 0; $i < 12; $i++) {
			$intervalMin[] = $minTime + ($i * 3600);
			$intervalMax[] = $minTime + (($i + 1) * 3600) - 1;
			$labels[] = ($i + 1) . "h";
		}
		return;
	}
	if ($timeDifference <= 86400) { // 24h
		for ($i = 0; $i < 12; $i++) {
			$intervalMin[] = $minTime + ($i * 7200);
			$intervalMax[] = $minTime + (($i + 1) * 7200) - 1;
			$labels[] = ($i + 1) * 2 . "h";
		}
		return;
	}

	// split into 12 intervals
	for ($i = 0; $i < 12; $i++) {
		$intervalMin[] = $minTime + ($i * $timeDifference / 12);
		$intervalMax[] = $minTime + (($i + 1) * $timeDifference / 12) - 1;
		$labels[] = round(($i + 1) * ($timeDifference / 12 / 60 / 60), 2) . "h";
	}
}
?>
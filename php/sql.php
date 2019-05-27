<?php
require("sql.config");

function getSqlInterface() {
	global $mysqli, $sql_servername, $sql_username, $sql_password, $sql_dbname;
	
	$_mysqli = new mysqli($sql_servername, $sql_username, $sql_password, $sql_dbname);
	if ($_mysqli->connect_error) die("Connection failed: " . $_mysqli->connect_error);
	
	return $_mysqli;
}

function sqlCommand($cmd) {
	global $mysqli;
	
	// $mysqli->query("SET NAMES 'utf8'");
	// die($cmd);
	if (!$result = $mysqli->query($cmd)) {
		echo "Sorry, the website is experiencing problems.";
		echo "Error: Our query failed to execute and here is why: \n";
		echo "Query: " . $cmd . "\n";
		echo "Errno: " . $mysqli->errno . "\n";
		echo "Error: " . $mysqli->error . "\n";
		exit;
		
	} else return $result;
}

function singleSqlCommand($sqlStatement) {
	$result = sqlCommand($sqlStatement);
	if ($row = $result->fetch_array()) return $row[0];
}

function updateSqlCommand($sqlStatement) {
	$result = sqlCommand($sqlStatement);
	$result->close();
	closeSqlConnection();
}

function closeSqlInterface() {
	global $mysqli;
	return $mysqli->close();
}

function toValidSqlValue($value) {
	global $mysqli;
	
	return $mysqli->real_escape_string(checkSqlValue($value));
}
function checkSqlValue($value) {
	return $value;
}
?>
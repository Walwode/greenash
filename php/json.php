<?php
function echoJson($output) {
	// echo json_encode($output); // changed, due to UTF-8 problems
	
	header('Content-Type: application/json; charset=utf-8');
	echo toJson($output);
}
	
function toJson($output) {
	array_walk_recursive($output, 'encode_items');
	return json_encode($output);
}

function encode_items(&$item, $key) {
    $item = utf8_encode($item);
}

function utf8_urldecode($str) {
	$str = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($str));
	return html_entity_decode($str,null,'UTF-8');;
}
?>
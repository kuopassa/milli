<?php

declare(strict_types=1);

date_default_timezone_set('Europe/Helsinki');

const API_URL_TEMPLATE = 'https://www.veikkaus.fi/api/millilotto/v1/draws/results?startDate=%d&endDate=%d';

function prepareMillilottoTimestamp(string $date):int {

	$timestamp = strtotime($date);
	$timestamp = strval($timestamp);
	
	$timestamp = str_pad(
		$timestamp,
		13,
		'0'
	);
	
	return (int) $timestamp;
}

function getMillilottoResults(string $apiURL):array {
	
	$options = array(
		'http'=>array(
			'method'=>'GET',
			'header'=>'Accept: application/json',
		),
	);
	
	$context = stream_context_create($options);

	$results = file_get_contents(
		$apiURL,
		false,
		$context
	);
	
	if (json_last_error() === JSON_ERROR_NONE) {
		
		$results = json_decode(
			$results,
			true
		);
		
		if (isset($results['draws'])) {
			
			$results = $results['draws'];
		}
	}
	else {
		
		$results = array();
	}
	
	return $results;
}

if (isset($_GET['startDate'],$_GET['endDate'])) {

	$startDate = $_GET['startDate'];
	$endDate = $_GET['endDate'];
}
else {

	$startDate = date(
		'Y-m-d',
		strtotime('-1 day')
	);
	
	$endDate = date('Y-m-d');
}

$startDate = prepareMillilottoTimestamp($startDate);
$endDate = prepareMillilottoTimestamp($endDate);

$apiURL = sprintf(
	API_URL_TEMPLATE,
	$startDate,
	$endDate
);

$results = getMillilottoResults($apiURL);

var_dump($results);

unset(
	$startDate,
	$endDate,
	$results
);
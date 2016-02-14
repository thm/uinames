<?php
	
// get status of webpage (e.g. 200, 404 or 502)
function getStatus($url) {
    $headers = get_headers($url);
    return substr($headers[0], 9, 3);
}

// get github contributors
function github($format) {
	
	// location of cached file
	$file = 'assets/cache/contributors.json';
	
	// whatever this does, but it works
	stream_context_set_default(array("http" => array("header" => "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/601.3.9 (KHTML, like Gecko) Version/9.0.2 Safari/601.3.9")));
	
	// update cached file if it's older than half a day
	if ((time() - filemtime($file)) > 3600*12) { // || $_GET['forcerefresh']) {

		// get most recent json data
		$url = 'https://api.github.com/repos/thm/uinames/contributors?per_page=100';
		
		// check status
		if (getStatus($url) == 200) {
		
			// update cached file
			$data = file_get_contents($url);
			file_put_contents($file, $data, LOCK_EX);
			
		} else {
			$data = file_get_contents($file);
		}
		
		// prepare fresh data for implementation
		$data = json_decode($data, true);
		
	} else {
		
		// prepare cached data for implementation
		$data = json_decode(file_get_contents($file), true);
		
	}
	
	// shuffle people up so they're in a different spot every time
	shuffle($data);
	
	// loop: render faces
	for ($i = 0; $i < count($data); $i++) {
		$needles = ['$href','$img','$user','$contributions'];
		$details = [
			$data[$i]['html_url'],
			$data[$i]['avatar_url'],
			$data[$i]['login'],
			$data[$i]['contributions']
		];
		
		echo str_replace($needles, $details, $format);
	}
}

// update day and relative stats
function updateDay($stats) {

	// update saved day
	$stats->updated = (int)date('d');
	
	// trash values we don't need anymore
	array_splice($stats->web->daily, 30);
	array_splice($stats->web->daily_relative, 30);
	array_splice($stats->api->daily, 30);
	array_splice($stats->api->daily_relative, 30);
	
	// compare and adjust all values in api->daily_relative to highest value
	$highestVal = max($stats->api->daily);
	for ($i = 0; $i < count($stats->api->daily); $i++) {
		$stats->api->daily_relative[$i] = round($stats->api->daily[$i] / $highestVal * 100, 1);
	}
	
	// compare and adjust all values in web->daily_relative to total/api values
	$stats->web->daily_relative = [];
	for ($i = 0; $i < count($stats->web->daily); $i++) {
		$stats->web->daily_relative[$i] = round($stats->web->daily[$i] / $stats->api->daily[$i] * 100, 1);
	}
	
	// prepend new 0 values to start counting again
	array_unshift($stats->web->daily, 0);
	array_unshift($stats->web->daily_relative, 0);
	array_unshift($stats->api->daily, 0);
	array_unshift($stats->api->daily_relative, 0);
}
<?php

const STATS = 'stats.json';

// prevent easy access
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	// get old stats
	$stats = json_decode(file_get_contents(STATS));
	
	if (isset($stats->web->calculated)) {
		
		// update confirmed stats
		$stats->web->confirmed++;
		
		// a = days since names generated are confirmed
		// b = days since start of uinames.com
		$time = time();
		$a = ($time - strtotime('Jan 16, 2016')) / 86400;
		$b = ($time - strtotime('Dec 23, 2013')) / 86400;
		
		// determine confirmed generated names
		$confirmed = $stats->web->confirmed / $a;
		
		// calculate avg generated names
		$stats->web->calculated = floor($confirmed * $b);
		
		// push new stats
		file_put_contents(STATS, json_encode($stats));
		
		echo 'Counted.';
	
	} else {
		echo 'Conflicted.';
	}

} else {
	echo 'Unauthorized.';
}
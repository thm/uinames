<?php

// prevent easy access, hopefully
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$stats = json_decode(file_get_contents('stats.json'));
	if (isset($stats->web)) {
		$stats->web++;
		file_put_contents('stats.json', json_encode($stats));
	}
}
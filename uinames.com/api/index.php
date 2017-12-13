<?php

// define('DEBUGGING', ($_GET['debug'] == 1) ? true : false);

// error_reporting(DEBUGGING ? E_ALL : 0);
// ini_set('display_errors', DEBUGGING ? 'On' : 'Off');
// if (!DEBUGGING) ob_start();

const ANY = NULL;

function sanitize($database) {
	static $defaultValues = [
		'male' => [],
		'female'	 => [],
		'surname' => [],
		'exceptions' => [],
		'male_exceptions' => [],
		'female_exceptions' => []
	];

	foreach ($database as $pool) {
		foreach ($defaultValues as $key => $defaultValue) {
			if (!property_exists($pool, $key)) $pool->$key = $defaultValue;
		}
	}

	return $database;
}

function get_sizes($database) {
	return array_map(function ($pool) {
		return (count($pool->male) + count($pool->female)) * count($pool->surnames);
	}, $database);
}

function get_random_pool($database) {
	$index = mt_rand(0, count($database) - 1);
	$pool  = $database[$index];

	return $pool;
}

function get_random_pool_by_size($database) {
	$sizes   = get_sizes($database);
	$total   = array_sum($sizes);
	$random  = mt_rand(0, $total);
	$pointer = 0;

	foreach ($sizes as $index => $size) {
		$pointer += $size;

		if ($pointer >= $random) break;
	}

	return $database[$index];
}

function generate_ext_result($result) {
	// re-setting values
	$name = $result['name'];
	$surname = $result['surname'];
	$gender = $result['gender'];
	
	// set birthdate
	$randDate = mt_rand(strtotime('-36 years'), strtotime('-21 years'));
	$birthYear = substr(date('Y', $randDate), -2);
	
	// age
	$age = date('Y') - date('Y', $randDate);
	$result['age'] = $age;
	
	// title
	$femaleTitle = ($age > 26 && rand(0,100) > 65) ? 'mrs' : 'ms';
	$result['title'] = ($gender == 'male') ? 'mr' : $femaleTitle;
	
	// phone
	$result['phone'] = '(' . mt_rand(100,999) . ') ' . mt_rand(100,999) . ' ' . mt_rand(1000,9999);
	
	// birthday
	$result['birthday']['dmy'] = date('d/m/Y', $randDate);
	$result['birthday']['mdy'] = date('m/d/Y', $randDate);
	$result['birthday']['raw'] = $randDate;
	
	// email address
	$separation = ['.','_','-',''];
	if (strlen($name . $surname) >= 12) {
		$email = strtolower($name) . $separation[rand(1,3)] . $birthYear;
	} else {
		$email = strtolower($name . $separation[rand(0,3)] . $surname);
	}
	$result['email'] = "$email@example.com";
	
	// password
	$signs = ['!','@','#','$','%','^','&','*','(',')','{','}','~','+','=','_',''];
	$result['password'] = str_replace(' ', '', $surname) . $birthYear . $signs[rand(0,count($signs)-1)] . $signs[rand(0,count($signs)-1)];
	
	// credit card
	$result['credit_card']['expiration'] = rand(1,12) . '/' . substr(date('Y') + rand(1,8), -2);
	$result['credit_card']['number'] = mt_rand(1000,9999) . '-' . mt_rand(1000,9999) . '-' . mt_rand(1000,9999) . '-' . mt_rand(1000,9999);
	$result['credit_card']['pin'] = mt_rand(1000,9999);
	$result['credit_card']['security'] = mt_rand(100,999);
	
	// photo
	$photos = ($gender == 'male') ? glob('photos/male/*.jpg') : glob('photos/female/*.jpg');
	$result['photo'] = 'https://uinames.com/api/' . $photos[array_rand($photos)];
	
	return $result;
}

function generate_name($database, $region = ANY, $language = ANY, $gender = ANY) {
	if ($region === ANY || $region === 'random') {
		$pool = get_random_pool_by_size($database);
	} else {
		// find pool by region name and language
		$found   = false;
		$matches = [];

		foreach ($database as $pool) {
			if (strtolower($pool->region) === strtolower($region)) $matches[] = $pool;
		}

		if ($language === ANY && count($matches)) {
			$pool  = get_random_pool($matches);
			$found = true;
		} else if (isset($match->language)) {
			foreach ($matches as $match) {
				if (strtolower($match->language) !== strtolower($language)) continue;

				$found = true;
				$pool  = $match;
			}
		}
		
		if (!$found) throw new Exception('Region or language not found');
	}

	if ($gender === ANY || $gender === 'random') {
		// we're being sexist here to make the code more effective
		$male_size = count($pool->male);
		$total	 = $male_size + count($pool->female);
		$gender	= (mt_rand(0, $total) >= $male_size) ? 'female' : 'male';
	} else if ($gender !== 'male' && $gender !== 'female') {
		// transphobic now too
		throw new Exception('Invalid gender');
	}

	// find random name and apply exceptions
	$name_index	   = mt_rand(0, count($pool->$gender) - 1);
	$name			 = $pool->{$gender}[$name_index];
	$name_chunk_count = substr_count($name, ' ');

	// find random surname and apply exceptions
	$surname_index = mt_rand(0, count($pool->surnames) - 1);
	$surname	   = $pool->surnames[$surname_index];

	$subject = $name . ' ' . $surname;

	// do magic exception stuff, don't ever ask about this
	$gender_exceptions   = $pool->{$gender . '_exceptions'};
	$gender_patterns	 = array_map(function ($exception) { return $exception[0]; }, $gender_exceptions);
	$gender_replacements = array_map(function ($exception) { return $exception[1]; }, $gender_exceptions);
	$subject			 = preg_replace($gender_patterns, $gender_replacements, $subject);

	$general_patterns	  = array_map(function ($exception) { return $exception[0]; }, $pool->exceptions);
	$general_replacements = array_map(function ($exception) { return $exception[1]; }, $pool->exceptions);
	$subject			  = preg_replace($general_patterns, $general_replacements, $subject);

	// this works 99.7% of the time, maybe
	$chunks	     = explode(' ', $subject);
	$name_chunks = array_splice($chunks, 0, $name_chunk_count - 1);
	$name		 = implode(' ', $name_chunks);
	$surname	 = implode(' ', $chunks);

	$result = [
		'name' => $name,
		'surname' => $surname,
		'gender' => $gender,
		'region' => $pool->region
	];

	if (!empty($pool->language)) {
		$result->language = $pool->language;
	}
	
	// return data
	return isset($_GET['ext']) ? generate_ext_result($result) : $result;
}

$json	 = file_get_contents('names.json');
$database = sanitize(json_decode($json));

$amount   = isset($_GET['amount']) ? (int) $_GET['amount'] : 1;
$region   = isset($_GET['region']) ? $_GET['region'] : (isset($_GET['country']) ? $_GET['country'] : 'random');
$language = @$_GET['language'];
$gender   = @$_GET['gender'];
$minlen   = isset($_GET['minlen']) ? (int) $_GET['minlen'] : 1;
$maxlen   = isset($_GET['maxlen']) ? (int) $_GET['maxlen'] : 1000;
$results  = [];
$count	= 0;

function send($content, $code = 200) {
	$type = !empty($_GET['callback']) ? 'application/javascript' : 'application/json';

	header('Content-Type: ' . $type . '; charset=utf-8');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET');
	http_response_code($code); // use this for PHP <5.4 instead: header("HTTP/1.1 200 OK");
	
	$output = json_encode($content, JSON_UNESCAPED_UNICODE);

	// if (!DEBUGGING) ob_end_clean();
	// use JSONP if asked
	echo empty($_GET['callback']) ? $output : $_GET['callback'] . "($output);";
	
	// it only counts if names are actually served
	/*
	if ($code == 200) {
		$stats = json_decode(file_get_contents('stats.json'));
		if (isset($stats->api)) {
			$stats->api += isset($content['name']) ? 1 : count($content);
			file_put_contents('stats.json', json_encode($stats));
		}
	}
	*/
	exit;
}

try {

	if ($amount < 1 || $amount > 500) {
		throw new Exception('Amount of requested names exceeds maximum allowed');
	}
	
	while ($count < $amount) {
		$name = generate_name($database, $region, $language, $gender);
		$name_length = iconv_strlen($name['name'] . ' ' . $name['surname']);
		if ($name_length >= $minlen && $name_length <= $maxlen) {
			$results[] = $name;
			$count++;
		}
	}

	if ($amount == 1) {
		send($results[0]);
	} else {
		send($results);
	}
} catch (Exception $e) {
	send(['error' => $e->getMessage()], 400);
}

?>
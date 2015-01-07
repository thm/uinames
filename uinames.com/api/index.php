<?php

// define('DEBUGGING', ($_GET['debug'] == 1) ? true : false);

// error_reporting(DEBUGGING ? E_ALL : 0);
// ini_set('display_errors', DEBUGGING ? 'On' : 'Off');
// if (!DEBUGGING) ob_start();

const ANY = NULL;

function sanitize($database) {
    static $defaultValues = array(
        'male'              => array(),
        'female'            => array(),
        'surname'           => array(),
        'exceptions'        => array(),
        'male_exceptions'   => array(),
        'female_exceptions' => array()
    );

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

function get_random_pool ($database) {
    $index = mt_rand(0, count($database) - 1);
    $pool  = $database[$index];

    return $pool;
}

function get_random_pool_by_size ($database) {
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

function generate_name ($database, $country = ANY, $language = ANY, $gender = ANY) {
    if ($country === ANY) {
        $pool = get_random_pool_by_size($database);
    } else {
        // find pool by country name and language
        $found   = false;
        $matches = array();

        foreach ($database as $pool) {
            if (strtolower($pool->country) === strtolower($country)) $matches[] = $pool;
        }

        if ($language === ANY) {
            $pool  = get_random_pool($matches);
            $found = true;
        } else {
            foreach ($matches as $match) {
                if (strtolower($match->language) !== strtolower($language)) continue;

                $found = true;
                $pool  = $match;
            }
        }

        if (!$found) throw new Exception('No matching pool found');
    }

    if ($gender === ANY) {
        // we're being sexist here to make the code more effective
        $male_size = count($pool->male);
        $total     = $male_size + count($pool->female);
        $gender    = (mt_rand(0, $total) >= $male_size) ? 'female' : 'male';
    } elseif ($gender !== 'male' && $gender !== 'female') {
        // transphobic now too
        throw new Exception('Invalid gender');
    }

    // find random name and apply exceptions
    $name_index       = mt_rand(0, count($pool->$gender) - 1);
    $name             = $pool->{$gender}[$name_index];
    $name_chunk_count = substr_count($name, ' ');

    // find random surname and apply exceptions
    $surname_index = mt_rand(0, count($pool->surnames) - 1);
    $surname       = $pool->surnames[$surname_index];

    $subject = $name . ' ' . $surname;

    // do magic exception stuff, don't ever ask about this
    $gender_exceptions   = $pool->{$gender . '_exceptions'};
    $gender_patterns     = array_map(function ($exception) { return $exception[0]; }, $gender_exceptions);
    $gender_replacements = array_map(function ($exception) { return $exception[1]; }, $gender_exceptions);
    $subject             = preg_replace($gender_patterns, $gender_replacements, $subject);

    $general_patterns     = array_map(function ($exception) { return $exception[0]; }, $pool->exceptions);
    $general_replacements = array_map(function ($exception) { return $exception[1]; }, $pool->exceptions);
    $subject              = preg_replace($general_patterns, $general_replacements, $subject);

    // this works 99.7% of the time, maybe
    $chunks      = explode(' ', $subject);
    $name_chunks = array_splice($chunks, 0, $name_chunk_count - 1);
    $name        = implode(' ', $name_chunks);
    $surname     = implode(' ', $chunks);

    $result = array(
        'name'     => $name,
        'surname'  => $surname,
        'gender'   => $gender,
        'country'  => $pool->country
    );

    if (!empty($pool->language)) {
        $result->language = $pool->language;
    }

    return $result;
}

$json     = file_get_contents('names.json');
$database = sanitize(json_decode($json));

$amount   = isset($_GET['amount']) ? (int) $_GET['amount'] : 1;
$country  = @$_GET['country'];
$language = @$_GET['language'];
$gender   = @$_GET['gender'];
$results  = array();

function send ($content) { //, $code = 200) {
    $type = (!empty($_GET['callback'])) ? 'application/javascript' : 'application/json';

    header('Content-Type: ' . $type . '; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');
    header("HTTP/1.1 200 OK"); // http_response_code($code);

    $output = json_encode($content); // , JSON_UNESCAPED_UNICODE);

    // if (!DEBUGGING) ob_end_clean();
    if (!empty($_GET['callback'])) {
        // use JSONP
        echo $_GET['callback'] . '(' . $output . ');';
    } else {
        echo $output;
    }

    exit;
}

try {
    for ($i = 0; $i < $amount; $i++) {
        $results[] = generate_name($database, $country, $language, $gender);
    }

    if (!isset($_GET['amount'])) {
        send($results[0]);
    } else {
        send($results);
    }
} catch (Exception $e) {
    send(array('error' => $e->getMessage()), 400);
}

?>
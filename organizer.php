<?php
date_default_timezone_set('America/New_York');
$input = 'pics.txt';
$lines = trim(exec('wc -l ' . $input));
$lines = substr($lines, 0, strpos($lines, " "));
$base_path = '/Volumes/media_archive/';
$file_handle = fopen($input, "r");

$directories = array();
$limit = false; //Set to an integer to stop at that many

$i = 1;
while (!feof($file_handle) && (!$limit || (is_int($limit) && $i <= $limit))) {
	$dates = array();
	$file = $base_path . trim(fgets($file_handle));
	echo $i . ' of ' . $lines . " (" . floor(($i / $lines) * 100) . "%)\n";

	//Get date from exif
	$data = @exif_read_data($file, NULL, TRUE);
	if ($data && isset($data['EXIF']) && isset($data['EXIF']['DateTimeOriginal'])) {
		//We have exif data and a date
		$dates[] = date('U', strtotime($data['EXIF']['DateTimeOriginal']));
	}

	//Get date from file path
	$date_regex = "/(?'year'20[01][0123456789])(_?(?'month'01|02|03|04|05|06|07|08|09|10|11|12))?(_?(?'day'\d\d))?/";
	$matches = array();
	preg_match_all($date_regex, $file, $matches);
	if (count($matches[0])) {
		//We found a date in the file name
		$dates[] = convertDate($matches);
	}

	//Get created, last accessed, and last modified dates
	$stat = stat($file);
	if ($stat) {
		$dates[] = $stat['atime'];
		$dates[] = $stat['ctime'];
		$dates[] = $stat['mtime'];
	}

	//Figure out which date to use
	$date = getCorrectDate($dates);

	$human_dates = array();
	foreach ($dates as $d) {
		$human_dates[] = date('Y-m-d H:i:s', $d);
	}

	$directories[date('Ymd', $date)][] = array(
		'file' => $file,
		//'basename' => basename($file),
		//'dates' => $dates,
		'human_dates' => $human_dates,
		'matches' => $matches
	);
	$i++;
}
echo "finished.\n";
fclose($file_handle);
file_put_contents('pics_array.txt', serialize($directories));
echo count($directories);
exit;

//Figure out the oldest date that is not older than a specific date
function getCorrectDate ($dates) {
	rsort($dates);
	do {
		$date = array_pop($dates);
	} while ($date < 852094800 && count($dates)); //852094800 = 01/01/1997
	return $date;
}

//Convert preg_match_all result into a valid timestamp
function convertDate ($matches) {
	$year = array_pop($matches['year']); //Get last found year
	$year = ($year) ? $year : date('Y'); //If nothing found, use this year
	$month = array_pop($matches['month']); //Get last found month
	$month = ($month) ? $month : '01'; //If nothing found, use January
	$day = array_pop($matches['day']); //Get last found day
	$day = ($day) ? $day : '01'; //If nothing found, use 1st
	$time = date('U', strtotime($month . '/' . $day . '/' . $year)); //Convert to unix timestamp
	return $time;
}
?>

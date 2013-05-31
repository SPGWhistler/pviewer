<?php
//$copy_from = '/Users/tpetty/Sites/pviewer/images';
$copy_from = '/Users/tpetty/Sites/pviewer';
$copy_to = '/Users/tpetty/Sites/pviewer/ian3';
$file_list = '/Users/tpetty/Sites/pviewer/list3.txt';
date_default_timezone_set('America/New_York');

$files = @file($file_list, FILE_IGNORE_NEW_LINES);
echo "Starting " . count($files) . " files...\n";
$i = 1;
foreach ($files as $file) {
	$data = exif_read_data($copy_from . '/' . $file, NULL, TRUE);
	$filename = date('U', strtotime($data['EXIF']['DateTimeOriginal']));
	echo $i . "   " . $copy_from . '/' . $file . " -> " . $copy_to . '/' . $filename;
	for ($j = 1; $j <= 10; $j++) {
		if ($j < 10) {
			if (file_exists($copy_to . '/' . $filename . $j . ".JPG")) {
				continue;
			} else {
				$filename .= $j . ".JPG";
				echo $j . ".JPG ";
				break;
			}
		}
		echo "Too many duplicate files.\n";
		exit;
	}
	$res = copy($copy_from . '/' . $file, $copy_to . '/' . $filename);
	if (!$res) {
		echo "Error.\n";
		exit;
	}
	if (file_exists($copy_to . '/' . $filename)) {
		echo "Good.\n";
	} else {
		echo "Error File doesn't exist.\n";
		exit;
	}
	$i++;
}
?>

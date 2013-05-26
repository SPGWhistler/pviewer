<?php
$copy_from = '/Users/tpetty/Sites/pviewer/images';
$copy_to = '/Users/tpetty/Sites/pviewer/ian';
$file_list = '/Users/tpetty/Sites/pviewer/list.txt';
date_default_timezone_set('America/New_York');

$files = @file($file_list, FILE_IGNORE_NEW_LINES);
foreach ($files as $file) {
	$data = exif_read_data($copy_from . '/' . $file, NULL, TRUE);
	$filename = date('U', strtotime($data['EXIF']['DateTimeOriginal']));
	$filename .= (file_exists($copy_top . '/' . $filename . '1.JPG')) ? '2.JPG' : '1.JPG';
	echo $file . "\n";
	copy($copy_from . '/' . $file, $copy_to . '/' . $filename);
}
?>

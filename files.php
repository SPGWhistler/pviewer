<?php
$dir = '/Volumes/media_archive/new_media_archive_1';
//$dir = '/Users/tpetty/Pictures/Eye-Fi';
$pattern = '*.{jpg,JPG}';
$flags = GLOB_BRACE;
chdir($dir);

function glob_recursive($pattern, $flags = 0)
{
	$files = glob($pattern, $flags);
	foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
	{
		$files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
	}
	return $files;
}

$dirlist = glob('*', GLOB_ONLYDIR);

if (!isset($_GET['dir'])) {
	/*
	foreach ($dirlist as $dir) {
		echo "<a href='index.php?dir=" . urlencode($dir) . "'>" . $dir . "</a><br />";
	}
	*/
	echo json_encode(array(
		'dirs' => $dirlist
	));
	exit;
}
chdir($_GET['dir']);

$files = glob_recursive($pattern, $flags);
if (!isset($_GET['file'])) {
	$_GET['file'] = $files[0];
}
echo json_encode(array(
	'cur' => array_search($_GET['file'], $files),
	'files' => $files
));
exit;
/*
$cur_files = array();
$cur_files[2] = array_search($_GET['file'], $files);
$cur_files[0] = ($cur_files[2] > 1) ? $cur_files[2] - 2 : NULL;
$cur_files[1] = ($cur_files[2] > 0) ? $cur_files[2] - 1 : NULL;
$cur_files[3] = ($cur_files[2] < count($files) - 1) ? $cur_files[2] + 1: NULL;
$cur_files[4] = ($cur_files[2] < count($files) - 2) ? $cur_files[2] + 2: NULL;
*/
?>

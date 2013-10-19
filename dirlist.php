<html>
<head>
</head>
<body>
<?php
ini_set('memory_limit', '2G');
ini_set('max_execution_time', 300);
require('/Users/tpetty/kint/Kint.class.php');
date_default_timezone_set('America/New_York');
$input = 'pics_array.txt';
$string = file_get_contents($input);
$directories = unserialize($string);
ksort($directories);
$i = 0;
foreach ($directories as $dir=>$files) {
	d($dir, $files);
	/*
	echo $dir . " - " . count($files) . "\n";
	foreach ($files as $file) {
		echo count($file) . "\n";
	}
	*/
	if ($i > 30) break;
	$i++;
}
?>
</body>
</html>

<?php
$save_file = 'list2.txt';
$output = array(
	'success' => FALSE,
	'reason' => 'file_error'
);
if (isset($_GET['dir']) && isset($_GET['file'])) {
	$newline = $_GET['dir'] . "/" . $_GET['file'] . "\n";
	$lines = @file($save_file);
	if (is_array($lines) && $index = array_search($newline, $lines) !== FALSE) {
		if (isset($_GET['delete'])) {
			array_splice($lines, $index, 1);
			$result = @file_put_contents($save_file, $lines);
			if ($result !== FALSE) {
				$output = array(
					'success' => TRUE
				);
			}
		} else {
			$output['reason'] = 'duplicate';
		}
	} else {
		$result = @file_put_contents($save_file, $newline, FILE_APPEND);
		if ($result !== FALSE) {
			$output = array(
				'success' => TRUE
			);
		}
	}
}
echo json_encode($output);
?>

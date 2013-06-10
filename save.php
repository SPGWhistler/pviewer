<?php
$save_file = '2013.txt';
$output = array(
	'success' => FALSE,
	'reason' => 'file_error'
);
if (isset($_GET['dir']) && isset($_GET['file'])) {
	$newline = $_GET['dir'] . "/" . $_GET['file'] . "\n";
	$lines = @file($save_file);
	if (isset($_GET['checkonly'])) {
		if (is_array($lines) && in_array($newline, $lines)) {
			$output = array(
				'in_list' => TRUE
			);
		} else {
			$output = array(
				'in_list' => FALSE
			);
		}
	} else {
		if (is_array($lines) && in_array($newline, $lines)) {
			if (isset($_GET['delete'])) {
				$index = array_search($newline, $lines);
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
}
echo json_encode($output);
?>

<?php
$output = array(
	'success' => FALSE,
	'reason' => 'file_error'
);
if (isset($_GET['dir']) && isset($_GET['file'])) {
	$newline = $_GET['dir'] . "/" . $_GET['file'] . "\n";
	$lines = @file('list.txt');
	if (is_array($lines) && in_array($newline, $lines)) {
		$output['reason'] = 'duplicate';
	} else {
		$result = @file_put_contents("list.txt", $newline, FILE_APPEND);
		if ($result !== FALSE) {
			$output = array(
				'success' => TRUE
			);
		}
	}
}
echo json_encode($output);
?>

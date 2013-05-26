<?php
//$dir = '/Volumes/media_archive/new_media_archive_1';
//$dir = '/Users/tpetty/Sites/pviewer/images';
$dir = '/Users/tpetty/Sites/pviewer';
//$dir = '/Users/tpetty/Pictures/Eye-Fi';
chdir($dir);
chdir($_GET['dir']);
if (isset($_GET['details'])) {
	$data = exif_read_data($_GET['file'], NULL, TRUE);
	echo json_encode($data);
} else {
	$image = exif_thumbnail($_GET['file'], $width, $height, $type);
	if ($image!==false) {
		header('Content-type: ' .image_type_to_mime_type($type));
		echo $image;
	} else {
		$im = imagecreatetruecolor(120, 20);
		$text_color = imagecolorallocate($im, 233, 14, 91);
		imagestring($im, 1, 5, 5,  'No Preview Available', $text_color);
		header('Content-Type: image/jpeg');
		imagejpeg($im);
		imagedestroy($im);
	}
}
exit;
?>

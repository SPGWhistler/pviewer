<?php
$twidth = '160px';
$theight = '120px';
$fwidth = '500px';
$fheight = '500px';
?>
<html>
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
<style>
.links {
	position: fixed;
	left: 0;
	right: 0;
	bottom: 0;
	text-align: center;
}
.container {
	width: 100%;
}
.left {
	background-color: red;
	float: left;
	width: <?=$twidth;?>;
	height: <?=$theight;?>;
}
.center {
	background-color: yellow;
	margin: 0 auto;
	width: <?=$fwidth;?>;
	height: <?=$fheight;?>;
}
.right {
	background-color: blue;
	float: right;
	width: <?=$twidth;?>;
	height: <?=$theight;?>;
}
.behind {
	background-color: black;
}
.clear {
	clear: both;
}
</style>
</head>
<body>
<?php
$dir = '/Volumes/media_archive/new_media_archive_1';
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
	foreach ($dirlist as $dir) {
		echo "<a href='index.php?dir=" . urlencode($dir) . "'>" . $dir . "</a><br />";
	}
	exit;
}
chdir($_GET['dir']);

$files = glob_recursive($pattern, $flags);
if (!isset($_GET['file'])) {
	$_GET['file'] = $files[0];
}
$cur = array_search($_GET['file'], $files);
$prev = ($cur > 0) ? $cur - 1 : NULL;
$next = ($cur < count($files) - 1) ? $cur + 1: NULL;
?>
<script>
	var prev = '<?=urlencode($files[$prev]);?>';
	var cur = '<?=urlencode($files[$cur]);?>';
	var next = '<?=urlencode($files[$next]);?>';
	<?php
	//echo "<a href='image.php?dir=" . urlencode($_GET['dir']) . "&file=" . urlencode($files[0]) . "'>" . $files[0] . "</a><br />";
	?>

	var rotate = {
		locations: ['a', 'b', 'c', 'd'],

		init : function() {
			var self = this;
			this.left_offset = jQuery('#a').offset();
			this.center_offset = jQuery('#b').offset();
			this.right_offset = jQuery('#c').offset();
			jQuery('#a').css({position: 'absolute', top: this.left_offset.top, left: this.left_offset.left});
			jQuery('#b').css({position: 'absolute', top: this.center_offset.top, left: this.center_offset.left, margin: 'auto', 'z-index': '2'});
			jQuery('#c').css({position: 'absolute', top: this.right_offset.top, left: this.right_offset.left});
			jQuery('#d').css({position: 'absolute'});
			jQuery(document).bind('keydown', function(e){
				switch (e.which) {
					case 37:
						//37 - left
						self.moveLeft();
						break;
					case 39:
						//39 - right
						self.moveRight();
						break;
				}
			});
			jQuery('#left').click(function(e){
				e.preventDefault();
				self.moveLeft();
				return false;
			});
			jQuery('#right').click(function(e){
				e.preventDefault();
				self.moveRight();
				return false;
			});
		},

		moveRight : function() {
			//Move Right (pics rotate to left)
			jQuery('#' + this.locations[3]).css({top: this.right_offset.top, left: this.right_offset.left, width: '<?=$twidth;?>', height: '<?=$theight;?>', 'z-index': '-1'});
			jQuery('#' + this.locations[0]).css({'z-index': '-1'});
			jQuery('#' + this.locations[1]).css({'z-index': '1'});
			jQuery('#' + this.locations[1]).animate({top: this.left_offset.top, left: this.left_offset.left, width: '<?=$twidth;?>', height: '<?=$theight;?>'});
			jQuery('#' + this.locations[2]).css({'z-index': '2'});
			jQuery('#' + this.locations[2]).animate({top: this.center_offset.top, left: this.center_offset.left, width: '<?=$fwidth;?>', height: '<?=$fheight;?>'});
			this.locations = [this.locations[1], this.locations[2], this.locations[3], this.locations[0]];
		},

		moveLeft : function() {
			//Move Left (pics rotate to right)
			jQuery('#' + this.locations[3]).css({top: this.left_offset.top, left: this.left_offset.left, width: '<?=$twidth;?>', height: '<?=$theight;?>', 'z-index': '-1'});
			jQuery('#' + this.locations[2]).css({'z-index': '-1'});
			jQuery('#' + this.locations[1]).css({'z-index': '1'});
			jQuery('#' + this.locations[1]).animate({top: this.right_offset.top, left: this.right_offset.left, width: '<?=$twidth;?>', height: '<?=$theight;?>'});
			jQuery('#' + this.locations[0]).css({'z-index': '2'});
			jQuery('#' + this.locations[0]).animate({top: this.center_offset.top, left: this.center_offset.left, width: '<?=$fwidth;?>', height: '<?=$fheight;?>'});
			this.locations = [this.locations[3], this.locations[0], this.locations[1], this.locations[2]];
		}
	};
	$(document).ready(function(){
		rotate.init();
	});
</script>
<!--
<a href="index.php?dir=<?=urlencode($_GET['dir']);?>&file=<?=urlencode($files[$prev]);?>"><img src='image.php?dir=<?=urlencode($_GET['dir']);?>&file=<?=urlencode($files[$prev]);?>' /></a>
<a href="index.php?dir=<?=urlencode($_GET['dir']);?>&file=<?=urlencode($files[$cur]);?>"><img src='image.php?dir=<?=urlencode($_GET['dir']);?>&file=<?=urlencode($files[$cur]);?>' /></a>
<a href="index.php?dir=<?=urlencode($_GET['dir']);?>&file=<?=urlencode($files[$next]);?>"><img src='image.php?dir=<?=urlencode($_GET['dir']);?>&file=<?=urlencode($files[$next]);?>' /></a>
-->
<div class="container">
	<div id="a" class="left"><img src='image.php?dir=<?=urlencode($_GET['dir']);?>&file=<?=urlencode($files[$prev]);?>' width="100%" height="100%" /></div>
	<div id="c" class="right"><img src='image.php?dir=<?=urlencode($_GET['dir']);?>&file=<?=urlencode($files[$cur]);?>' width="100%" height="100%" /></div>
	<div id="b" class="center"><img src='image.php?dir=<?=urlencode($_GET['dir']);?>&file=<?=urlencode($files[$next]);?>' width="100%" height="100%" /></div>
	<div class="clear"></div>
</div>
<div id="d" class="behind"></div>
<div class="links">
	<a id="left" href="#">&lt;--</a> | <a id="right" href="#">--&gt;</a>
</div>
</body>
</html>

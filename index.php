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
	border: 1px solid gray;
	float: left;
	width: <?=$twidth;?>;
	height: <?=$theight;?>;
}
.center {
	border: 1px solid gray;
	margin: 0 auto;
	width: <?=$fwidth;?>;
	height: <?=$fheight;?>;
}
.right {
	border: 1px solid gray;
	float: right;
	width: <?=$twidth;?>;
	height: <?=$theight;?>;
}
.behind {
	border: 1px solid gray;
	width: <?=$twidth;?>;
	height: <?=$theight;?>;
	z-index: -1;
}
.clear {
	clear: both;
}
</style>
</head>
<body>
<script>
	var rotate = {
		locations: ['a', 'b', 'c', 'd'],

		init : function() {
			var self = this;
			var search = location.search.substring(1);
			var params = search?JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g,'":"') + '"}', function(key, value) { return key===""?value:decodeURIComponent(value) }):{}
			this.dir = params.dir;
			if (this.dir) {
				this.getList(params, function(data) {
					self.files = data.files;
					self.cur = data.cur;
					if (self.cur > 0) {
						jQuery('#a').html('<img src="image.php?dir=' + params.dir + '&file=' + self.files[self.cur - 1] + '" width="100%" height="100%" />');
					}
					jQuery('#b').html('<img src="image.php?dir=' + params.dir + '&file=' + self.files[self.cur] + '" width="100%" height="100%" />');
					if (self.cur + 1 < self.files.length - 1) {
						jQuery('#c').html('<img src="image.php?dir=' + params.dir + '&file=' + self.files[self.cur + 1] + '" width="100%" height="100%" />');
					}
					//jQuery('#d').html('<img src="image.php?dir=' + params.dir + '&file=' + self.files[self.cur + 2] + '" width="100%" height="100%" />');
				});
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
			} else {
				console.log('dirlist');
				jQuery('#pics').hide();
				jQuery('#dirlist').show();
				this.getList(params, function(data){
					var html = '';
					for (var i in data) {
						html += '<a href="index.php?dir=' + data[i] + '">' + data[i] + '</a><br />';
					}
					jQuery('#dirlist').html(html);
				});
			}
		},

		getList : function(params, callback) {
			var self = this;
			jQuery.getJSON('files.php', params, function(data){
				callback(data);
			});
		},

		moveRight : function() {
			//Move Right (pics rotate to left)
			if (this.files[this.cur + 1]) {
				jQuery('#' + this.locations[3]).css({top: this.right_offset.top, left: this.right_offset.left, width: '<?=$twidth;?>', height: '<?=$theight;?>', 'z-index': '-1'});
				jQuery('#' + this.locations[0]).css({'z-index': '-1'});
				jQuery('#' + this.locations[1]).css({'z-index': '1'});
				jQuery('#' + this.locations[1]).animate({top: this.left_offset.top, left: this.left_offset.left, width: '<?=$twidth;?>', height: '<?=$theight;?>'});
				jQuery('#' + this.locations[2]).css({'z-index': '2'});
				jQuery('#' + this.locations[2]).animate({top: this.center_offset.top, left: this.center_offset.left, width: '<?=$fwidth;?>', height: '<?=$fheight;?>'});
				this.locations = [this.locations[1], this.locations[2], this.locations[3], this.locations[0]];
				this.loadImage('next');
			}
		},

		moveLeft : function() {
			//Move Left (pics rotate to right)
			if (this.files[this.cur - 1]) {
				jQuery('#' + this.locations[3]).css({top: this.left_offset.top, left: this.left_offset.left, width: '<?=$twidth;?>', height: '<?=$theight;?>', 'z-index': '-1'});
				jQuery('#' + this.locations[2]).css({'z-index': '-1'});
				jQuery('#' + this.locations[1]).css({'z-index': '1'});
				jQuery('#' + this.locations[1]).animate({top: this.right_offset.top, left: this.right_offset.left, width: '<?=$twidth;?>', height: '<?=$theight;?>'});
				jQuery('#' + this.locations[0]).css({'z-index': '2'});
				jQuery('#' + this.locations[0]).animate({top: this.center_offset.top, left: this.center_offset.left, width: '<?=$fwidth;?>', height: '<?=$fheight;?>'});
				this.locations = [this.locations[3], this.locations[0], this.locations[1], this.locations[2]];
				this.loadImage('prev');
			}
		},

		loadImage : function(image) {
			switch (image) {
				case 'next':
					if (this.files[this.cur + 2]) {
						jQuery('#' + this.locations[3]).html('<img src="image.php?dir=' + this.dir + '&file=' + this.files[this.cur + 2] + '" width="100%" height="100%" />');
					} else {
						jQuery('#' + this.locations[3]).html('');
					}
					break;
				case 'prev':
					if (this.files[this.cur - 2]) {
						jQuery('#' + this.locations[3]).html('<img src="image.php?dir=' + this.dir + '&file=' + this.files[this.cur - 2] + '" width="100%" height="100%" />');
					} else {
						jQuery('#' + this.locations[3]).html('');
					}
					break;
			}
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
<div id="pics">
	<div class="container">
		<div id="a" class="left"></div>
		<div id="c" class="right"></div>
		<div id="b" class="center"></div>
		<div class="clear"></div>
	</div>
	<div id="d" class="behind"></div>
	<div class="links">
		<a id="left" href="#">&lt;--</a> | <a id="right" href="#">--&gt;</a>
	</div>
</div>
<div id="dirlist"></div>
</body>
</html>

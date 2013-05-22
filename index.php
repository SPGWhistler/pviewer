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
	background-color: #fff;
	float: left;
	width: <?=$twidth;?>;
	height: <?=$theight;?>;
}
.center {
	background-color: #fff;
	margin: 0 auto;
	width: <?=$fwidth;?>;
	height: <?=$fheight;?>;
}
.right {
	background-color: #fff;
	float: right;
	width: <?=$twidth;?>;
	height: <?=$theight;?>;
}
.behind {
	background-color: #fff;
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

		enabled: false,

		init : function() {
			var self = this;
			var search = location.search.substring(1);
			var params = search?JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g,'":"') + '"}', function(key, value) { return key===""?value:decodeURIComponent(value) }):{}
			this.dir = params.dir;
			if (this.dir) {
				this.getList(params, function(data) {
					self.files = data.files;
					self.cur = data.cur;
					console.debug('start cur', self.cur);
					console.debug('start file', self.files[self.cur]);
					console.debug('start locations', self.locations);
					if (self.cur > 0) {
						jQuery('#a').html('<img src="image.php?dir=' + params.dir + '&file=' + self.files[self.cur - 1] + '" width="100%" height="100%" />');
					}
					jQuery('#b').html('<img src="image.php?dir=' + params.dir + '&file=' + self.files[self.cur] + '" width="100%" height="100%" />');
					if (self.cur + 1 < self.files.length - 1) {
						jQuery('#c').html('<img src="image.php?dir=' + params.dir + '&file=' + self.files[self.cur + 1] + '" width="100%" height="100%" />');
					}
					if (self.cur + 2 < self.files.length - 1) {
						jQuery('#d').html('<img src="image.php?dir=' + params.dir + '&file=' + self.files[self.cur + 2] + '" width="100%" height="100%" />');
					}
					self.enabled = true;
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
							self.movePrev();
							break;
						case 39:
							//39 - right
							self.moveNext();
							break;
						case 32:
							//32 - space
							self.loadFullImage();
							break;
					}
				});
				jQuery('#left').click(function(e){
					e.preventDefault();
					self.movePrev();
					return false;
				});
				jQuery('#right').click(function(e){
					e.preventDefault();
					self.moveNext();
					return false;
				});
			} else {
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

		moveNext : function() {
			//Move Right (pics rotate to left)
			var self = this;
			if (this.enabled === true && this.files[this.cur + 1]) {
				this.enabled = false;
				jQuery('#' + this.locations[3]).css({top: this.right_offset.top, left: this.right_offset.left, width: '<?=$twidth;?>', height: '<?=$theight;?>', 'z-index': '-1'});
				jQuery('#' + this.locations[0]).css({'z-index': '-1'});
				jQuery('#' + this.locations[1]).css({'z-index': '1'});
				jQuery('#' + this.locations[1]).animate({top: this.left_offset.top, left: this.left_offset.left, width: '<?=$twidth;?>', height: '<?=$theight;?>'});
				jQuery('#' + this.locations[2]).css({'z-index': '2'});
				jQuery('#' + this.locations[2]).animate({top: this.center_offset.top, left: this.center_offset.left, width: '<?=$fwidth;?>', height: '<?=$fheight;?>'}, function(){
					self.enabled = true;
					self.locations = [self.locations[1], self.locations[2], self.locations[3], self.locations[0]];
					self.cur += 1;
					self.loadImage('next');
				});
			}
		},

		movePrev : function() {
			//Move Left (pics rotate to right)
			var self = this;
			if (this.enabled === true && this.files[this.cur - 1]) {
				this.enabled = false;
				jQuery('#' + this.locations[3]).css({top: this.left_offset.top, left: this.left_offset.left, width: '<?=$twidth;?>', height: '<?=$theight;?>', 'z-index': '-1'});
				jQuery('#' + this.locations[2]).css({'z-index': '-1'});
				jQuery('#' + this.locations[1]).css({'z-index': '1'});
				jQuery('#' + this.locations[1]).animate({top: this.right_offset.top, left: this.right_offset.left, width: '<?=$twidth;?>', height: '<?=$theight;?>'});
				jQuery('#' + this.locations[0]).css({'z-index': '2'});
				jQuery('#' + this.locations[0]).animate({top: this.center_offset.top, left: this.center_offset.left, width: '<?=$fwidth;?>', height: '<?=$fheight;?>'}, function() {
					self.enabled = true;
					self.locations = [self.locations[3], self.locations[0], self.locations[1], self.locations[2]];
					self.cur -= 1;
					self.loadImage('prev');
				});
			}
		},

		loadImage : function(direction) {
			switch (direction) {
				case 'next':
					console.debug('cur', this.cur);
					console.debug('file', this.files[this.cur]);
					console.debug('locations', this.locations);
					if (this.files[this.cur + 2]) {
						jQuery('#' + this.locations[3]).html('<img src="image.php?dir=' + this.dir + '&file=' + this.files[this.cur + 2] + '" width="100%" height="100%" />');
					} else {
						jQuery('#' + this.locations[3]).html('');
					}
					break;
				case 'prev':
					console.debug('cur', this.cur);
					console.debug('file', this.files[this.cur]);
					console.debug('locations', this.locations);
					if (this.files[this.cur - 2]) {
						jQuery('#' + this.locations[3]).html('<img src="image.php?dir=' + this.dir + '&file=' + this.files[this.cur - 2] + '" width="100%" height="100%" />');
					} else {
						jQuery('#' + this.locations[3]).html('');
					}
					break;
			}
		},

		loadFullImage : function() {
			console.debug('cur', this.cur);
			console.debug('file', this.files[this.cur]);
			console.debug('locations', this.locations);
			jQuery('#' + this.locations[1]).html('<img src="images/' + this.dir + '/' + this.files[this.cur] + '" width="100%" height="100%" />');
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

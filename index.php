<?php
$otwidth = 160;
$otheight = 120;
$twidth = $otwidth . 'px';
$theight = $otheight . 'px';
?>
<html>
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
<style>
body {
	margin: 0;
}
.green {
	background-color: green;
}
.links {
	position: fixed;
	left: 0;
	right: 0;
	bottom: 0;
	text-align: center;
	font-size: 30px;
}
#pics {
	overflow: hidden;
}
.container {
	width: 100%;
	height: 100%;
	display: table;
	overflow: hidden;
	position: static;
}
.left-col {
	display: table-cell;
	vertical-align: middle;
	position: static;
}
.left {
	/*background-color: blue;*/
	float: left;
	width: <?=$twidth;?>;
	height: <?=$theight;?>;
}
.center-col {
	display: table-cell;
	vertical-align: middle;
	position: static;
}
.center {
	/*background-color: green;*/
	margin: 0 auto;
}
.right-col {
	display: table-cell;
	vertical-align: middle;
	position: static;
}
.right {
	/*background-color: red;*/
	float: right;
	width: <?=$twidth;?>;
	height: <?=$theight;?>;
}
.behind {
	display: none;
	background-color: #fff;
	width: <?=$twidth;?>;
	height: <?=$theight;?>;
	z-index: -1;
}
.fullsize {
	position: absolute !important;
	left: 0 !important;
	top: 0 !important;
	right: -10000 !important;
	bottom: -10000 !important;
	margin: 0 !important;
	padding: 0 !important;
	overflow: scroll !important;
	width: auto !important;
	height: auto !important;
	z-index: 1000 !important;
}
.fullsize img {
	width: auto !important;
	height: auto !important;
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

		timer: null,

		cur_deg: 0,

		isFullImage: false,

		fheight: 0,

		fwidth: 0,

		init : function() {
			var ww = $(window).width();
			this.fwidth = (ww - (<?=$otwidth;?> * 2));
			this.fheight = Math.round(this.fwidth / 1.5);
			$('div.center').css({
				width : this.fwidth + 'px',
				height : this.fheight + 'px'
			});
			var self = this;
			var search = location.search.substring(1);
			var params = search?JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g,'":"') + '"}', function(key, value) { return key===""?value:decodeURIComponent(value) }):{}
			this.dir = params.dir;
			if (this.dir) {
				this.getList({}, function(data){
					self.dirs = data.dirs;
					self.curdir = data.dirs.indexOf(self.dir);
					self.getList(params, function(data) {
						self.files = data.files;
						self.cur = data.cur;
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
						self.startTimer();
					});
				});
				this.left_offset = jQuery('#a').offset();
				this.center_offset = jQuery('#b').offset();
				this.right_offset = jQuery('#c').offset();
				jQuery('#a').css({position: 'absolute', top: this.left_offset.top, left: this.left_offset.left});
				jQuery('#b').css({position: 'absolute', top: this.center_offset.top, left: this.center_offset.left, margin: 'auto', 'z-index': '2'});
				jQuery('#c').css({position: 'absolute', top: this.right_offset.top, left: this.right_offset.left});
				jQuery('#d').css({position: 'absolute', top: this.right_offset.top, left: this.right_offset.left, display: 'block'});
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
						case 70:
							//70 - f
							self.toggleFullImage();
							break;
						case 13:
							//13 - enter
							self.addFile();
							break;
						case 40:
							//40 - down
							self.moveNextDir();
							break;
						case 38:
							//38 - up
							self.movePrevDir();
							break;
						/*
						default:
							console.log(e.which);
							break;
						*/
					}
				});
				jQuery(document).bind('keypress', function(e) {
					switch (e.which) {
						case 82:
							//82 - R (rotate counter clockwise)
							self.rotateImage(false, self.cur_deg - 90);
							break;
						case 114:
							//114 - r (rotate clockwise)
							self.rotateImage();
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
					for (var i in data.dirs) {
						html += '<a href="index.php?dir=' + data.dirs[i] + '">' + data.dirs[i] + '</a><br />';
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
				this.rotateImage(true, 0);
				this.loadImage('next');
				jQuery('#links').html('');
				jQuery('#' + this.locations[3]).css({top: this.right_offset.top, left: this.right_offset.left, width: '<?=$twidth;?>', height: '<?=$theight;?>', 'z-index': '-1'});
				jQuery('#' + this.locations[0]).css({'z-index': '-1'});
				jQuery('#' + this.locations[1]).css({'z-index': '1'});
				jQuery('#' + this.locations[1]).animate({top: this.left_offset.top, left: this.left_offset.left, width: '<?=$twidth;?>', height: '<?=$theight;?>'}, 200);
				jQuery('#' + this.locations[2]).css({'z-index': '2'});
				jQuery('#' + this.locations[2]).animate({top: this.center_offset.top, left: this.center_offset.left, width: this.fwidth + 'px', height: this.fheight + 'px'}, 200, function(){
					self.enabled = true;
					self.locations = [self.locations[1], self.locations[2], self.locations[3], self.locations[0]];
					self.cur += 1;
					self.startTimer();
				});
			}
		},

		movePrev : function() {
			//Move Left (pics rotate to right)
			var self = this;
			if (this.enabled === true && this.files[this.cur - 1]) {
				this.enabled = false;
				this.rotateImage(true, 0);
				this.loadImage('prev');
				jQuery('#links').html('');
				jQuery('#' + this.locations[3]).css({top: this.left_offset.top, left: this.left_offset.left, width: '<?=$twidth;?>', height: '<?=$theight;?>', 'z-index': '-1'});
				jQuery('#' + this.locations[2]).css({'z-index': '-1'});
				jQuery('#' + this.locations[1]).css({'z-index': '1'});
				jQuery('#' + this.locations[1]).animate({top: this.right_offset.top, left: this.right_offset.left, width: '<?=$twidth;?>', height: '<?=$theight;?>'}, 200);
				jQuery('#' + this.locations[0]).css({'z-index': '2'});
				jQuery('#' + this.locations[0]).animate({top: this.center_offset.top, left: this.center_offset.left, width: this.fwidth + 'px', height: this.fheight + 'px'}, 200, function() {
					self.enabled = true;
					self.locations = [self.locations[3], self.locations[0], self.locations[1], self.locations[2]];
					self.cur -= 1;
					self.startTimer();
				});
			}
		},

		loadImage : function(direction) {
			switch (direction) {
				case 'next':
					if (this.files[this.cur + 2]) {
						jQuery('#' + this.locations[3]).html('<img src="image.php?dir=' + this.dir + '&file=' + this.files[this.cur + 2] + '" width="100%" height="100%" />');
					} else {
						jQuery('#' + this.locations[3]).empty();
					}
					break;
				case 'prev':
					if (this.files[this.cur - 2]) {
						jQuery('#' + this.locations[3]).html('<img src="image.php?dir=' + this.dir + '&file=' + this.files[this.cur - 2] + '" width="100%" height="100%" />');
					} else {
						jQuery('#' + this.locations[3]).empty();
					}
					break;
			}
		},

		loadFullImage : function() {
			var self = this;
			var cur_cur = this.cur;
			clearTimeout(this.timer);
			jQuery('<img width="100%" height="100%" />').load(function(){
				if (cur_cur === self.cur) {
					jQuery('#' + self.locations[1]).html(this);
				}
			}).attr('src', 'images/' + this.dir + '/' + this.files[this.cur]);
			//}).attr('src', this.dir + '/' + this.files[this.cur]);
			jQuery.getJSON('image.php', {dir: this.dir, file: this.files[this.cur], details: true}, function(data){
				var myyear = data.EXIF.DateTimeOriginal.substr(0, 4);
				var mymonthint = parseInt(data.EXIF.DateTimeOriginal.substr(5, 2), 10);
				var months = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
				var mymonth = months[mymonthint];
				var myday = data.EXIF.DateTimeOriginal.substr(8, 2);
				jQuery('#links').html(mymonth + " " + myday + " " + myyear);
				jQuery.getJSON('save.php', {checkonly: true, dir: self.dir, file: self.files[self.cur]}, function(data){
					//console.log(data);
					if (data.in_list === true) {
						jQuery('#links').addClass('green');
					} else {
						jQuery('#links').removeClass('green');
					}
				});
			});
		},

		startTimer : function() {
			var self = this;
			clearTimeout(this.timer);
			this.timer = setTimeout(function() {
				self.loadFullImage();
			}, 750);
		},

		rotateImage : function(noanimate, d) {
			var self = this;
			var dur = (noanimate) ? 0 : 400;
			d = (typeof d === 'number') ? d : this.cur_deg + 90;
			var elem = jQuery("#" + this.locations[1]);
			if (this.cur_deg != d) {
				jQuery({deg: this.cur_deg}).animate({deg: d}, {
					duration: dur,
					step: function(now){
						elem.css({
							transform: "rotate(" + now + "deg)"
						});
					},
					complete: function() {
						if (d >= 360) {
							d = 0;
						}
						self.cur_deg = d;
					}
				});
			}
		},

		toggleFullImage : function() {
			if (!this.isFullImage) {
				this.isFullImage = true;
				this.enabled = false;
				jQuery('#' + this.locations[1]).addClass('fullsize');
			} else {
				this.isFullImage = false;
				this.enabled = true;
				jQuery('#' + this.locations[1]).removeClass('fullsize');
			}
		},

		addFile : function() {
			var self = this;
			jQuery.getJSON('save.php', {dir: this.dir, file: this.files[this.cur]}, function(data){
				if (data.success === false) {
					if (data.reason === "duplicate"){
						if (confirm("File is already in the list. Click OK to REMOVE it. Or click CANCEL to KEEP it in the list.")) {
							jQuery.getJSON('save.php', {'delete': true, dir: self.dir, file: self.files[self.cur]}, function(newdata){
								if (newdata.success === false) {
									alert("File not removed from list: " + newdata.reason);
								} else {
									self.moveNext();
								}
							});
						}
					} else {
						alert("File not added to list: " + data.reason);
					}
				} else {
					self.moveNext();
				}
			});
		},

		moveNextDir : function() {
			if (this.dirs[this.curdir + 1]) {
				window.location = 'index.php?dir=' + this.dirs[this.curdir + 1];
			}
		},

		movePrevDir : function() {
			if (this.dirs[this.curdir - 1]) {
				window.location = 'index.php?dir=' + this.dirs[this.curdir - 1];
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
		<div class="left-col">
			<div id="a" class="left"></div>
		</div>
		<div class="center-col">
			<div id="b" class="center"></div>
		</div>
		<div class="right-col">
			<div id="c" class="right"></div>
		</div>
		<div class="clear"></div>
	</div>
	<div id="d" class="behind"></div>
	<div id="links" class="links">
		<?php
		//<a id="left" href="#">&lt;--</a> | <a id="right" href="#">--&gt;</a>
		?>
	</div>
</div>
<div id="dirlist"></div>
</body>
</html>

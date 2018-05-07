<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?= $this->lang['title']; ?> - <?= $this->data['name']; ?> - <?= $this->data['width']; ?>x<?= $this->data['height']; ?> - <?= $this->data['size']; ?></title>
<link href="themes/<?= $this->theme ?>/css/preview.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="themes/<?= $this->theme ?>/jscripts/general.js"></script>

<script language="javascript" type="text/javascript">
	var nexturl = "<?= $this->data['next']['path']; ?>";
	var nextwidth = <?= $this->data['next']['width']; ?>;
	var nextheight = <?= $this->data['next']['height']; ?>;

	var previousurl = "<?= $this->data['previous']['path']; ?>";
	var previouswidth = <?= $this->data['previous']['width']; ?>;
	var previousheight = <?= $this->data['previous']['height']; ?>;

	var heightmodifier = 75; // 50 + 25 for prev/next links
	var widthmodifier = 150;

	function nextimage() {
		if (nexturl != "")
			window.document.location.href = "preview.php?path=" + nexturl +"";
	}

	function previousimage() {
		if (previousurl != "")
			window.document.location.href = "preview.php?path=" + previousurl +"";
	}

	function setsize(width, height) {
		height += heightmodifier;
		width += widthmodifier;

		// need to have space for previous / next buttons
		if (width < 200)
			width = 200;

		if (height < 100)
			height = 100;

		var isMSIE = (navigator.appName == "Microsoft Internet Explorer");
		var x = parseInt(screen.width / 2.0) - (width / 2.0);
		var y = parseInt(screen.height / 2.0) - (height / 2.0);

		if (!isMSIE) {
			window.innerWidth = width;
			window.innerHeight = height;
		} else {
			width = width+7;
			height = height+35;
			//window.resizeTo(width, height);
			// ugly hack, msie is crappy
			setTimeout("window.resizeTo("+ width +", "+ height +")", 1);
		}
		window.moveTo(x,y);
	}

	function checkKey(event) {
		var key = getkey(event);
		switch (key) {
			case 32: // space
			case 110: // n
			case 34: // page down
			case 39: // right arrow
				nextimage();
			break;
			case 102: // p
			case 33: // page up
			case 37: // left arrow
				previousimage();
			break;
		}

		if (key == 27)
			window.close(); // escape
	}

	function getkey(e) {
		if (window.event)
			return window.event.keyCode;
		else if (e)
			return e.keyCode;
		else
			return null;
	}

	setsize(<?= $this->data['width']; ?>,<?= $this->data['height']; ?>);

</script>
</head>
<body onkeypress="checkKey(event);" onkeydown="checkKey(event);">
<div class="next_prev">
<? if ($this->data['previous']['path'] != "") { ?>
	<a href="javascript:previousimage();" alt="<?= $this->lang['keyboard_prev']; ?>" title="<?= $this->lang['keyboard_prev']; ?>">&lt;&lt;&nbsp;<?= $this->lang['prev']; ?></a>
<? } else { ?>
	&lt;&lt;&nbsp;<?= $this->lang['prev']; ?>
<? } ?>
&nbsp;|&nbsp;
<? if ($this->data['next']['path'] != "") { ?>
<a href="javascript:nextimage();" alt="<?= $this->lang['keyboard_next']; ?>" title="<?= $this->lang['keyboard_next']; ?>"><?= $this->lang['next']; ?>&nbsp;&gt;&gt;</a>
<? } else { ?>
	<?= $this->lang['next']; ?>&nbsp;&gt;&gt;
<? } ?>
</div>
<div class="preview_image">
	<img src="<?= $this->data['previewurl']; ?>?time=<?= time(); ?>" width="<?= $this->data['width']; ?>" height="<?= $this->data['height']; ?>" alt="<?= $this->data['name']; ?>" title="<?= $this->data['name']; ?>" />
</div>
</body>
</html>

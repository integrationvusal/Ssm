<?= '<?xml version="1.0" encoding="iso-8859-1"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="sv" xml:lang="sv">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?= $this->lang['title']; ?> (<?= ($this->data['gd_support'] ? "GD enabled" : "GD disabled"); ?> <?= ($this->data['exif_support'] ? "EXIF enabled" : "EXIF disabled"); ?>)</title>
<script language="javascript" type="text/javascript">
	var disabledTools = '<?= $this->data['disabled_tools'] ?>';
	var hasReadAccess = <?= $this->data['hasReadAccess'] ?>;
	var hasWriteAccess = <?= $this->data['hasWriteAccess'] ?>;
	var path = "<?= $this->data['path'] ?>";
	var full_path = "<?= $this->data['full_path'] ?>";
	var errorMsg = "<?= $this->data['errorMsg'] ?>";
	var formname = "<?= $this->data['formname'] ?>";
	var elementnames = "<?= $this->data['elementnames'] ?>";
	var js = "<?= $this->data['js'] ?>";
	var demo = "<?= $this->data['demo'] ?>";
	var demo_msg = "<?= $this->data['demo_msg'] ?>";
	var filemanger_urlprefix = "<?= $this->data['filemanager_urlprefix']; ?>";
</script>
<script language="javascript" type="text/javascript">
	var deleteConfirm = '<?= $this->lang["confirm_delete"] ?>';

	function insertURL(url) {
		// Crop away query
		if ((pos = url.indexOf('?')) != -1)
			url = url.substring(0, url.indexOf('?'));

		// Handle custom js call
		if (window.opener && js != "") {
			eval("window.opener." + js + "(url);");
			top.close();
			return;
		}

		// Handle form item call
		if (window.opener && formname != "") {
			var elements = elementnames.split(',');

			for (var i=0; i<elements.length; i++) {
				var elm = window.opener.document.forms[formname].elements[elements[i]];
				if (elm && typeof elm != "undefined")
					elm.value = url;
			}

			top.close();
		}
		top.close();
	} 

	function selectPath(select) {
		var value = select.options[select.selectedIndex].value;
		document.location.href = "images.php?path="+ escape(value) +"&formname="+ formname +"&elementnames="+ elementnames + "&js="+ js +"";
	}

	function imagePreview(width, height, path) {
		if (width < 200)
			width = 200;

		if (height < 200)
			height = 200;

		openPop("preview.php?path="+ escape(path), width, height);
	}

	function imageInfo(name, set_style) {
		var elm = document.getElementById("img_"+ name);
		if (elm != null)
			elm.style.display = set_style;
	}

	function imageDelete(del_path) {
		if (!demo) {
			if (confirm(deleteConfirm)) {
				document.location.href = "images.php?action=delete&path="+ full_path +"&file_01="+ del_path +"&formname="+ formname +"&elementnames="+ elementnames + "&js="+ js +"&time="+ new Date().getTime();
			}
			else
				return;
		} else
			alert(demo_msg);
	}

	function imageEdit(path) {
		if (path != "")
			openImageEditor(path);
	}

	function gotoAnchor(anchor) {
		if ((typeof(anchor) != "undefined") || (anchor != ""))
			document.location.href = "#f_"+ anchor +"";
	}

</script>
<script language="javascript" type="text/javascript" src="themes/<?= $this->theme ?>/jscripts/general.js"></script>
<script language="javascript" type="text/javascript" src="themes/<?= $this->theme ?>/jscripts/imagetools.js"></script>
<link href="themes/<?= $this->theme ?>/css/general.css" rel="stylesheet" media="screen" type="text/css" />
<style>
	.thumbnail { width: <?= $this->data['thumbnail_width']; ?>px; height: <?= ($this->data['thumbnail_height'] + 20); ?>px; margin: 0 <?= $this->data['thumbnail_margin_around']; ?>px <?= $this->data['thumbnail_margin_around']; ?>px <?= $this->data['thumbnail_margin_around']; ?>px; }
	.image { width: <?= $this->data['thumbnail_width']+2; ?>px;	height: <?= $this->data['thumbnail_height']+2; ?>px; border: <?= $this->data['thumbnail_border_style']; ?>; }
	.imagename { width: <?= $this->data['thumbnail_width']; ?>px; }
	.imageinfo { width: <?= $this->data['thumbnail_width']; ?>px; border: <?= $this->data['thumbnail_border_style']; ?>; }
	.infotable, .infotable td, .infotable .tr { background-color: #ffffff; font-size: 10px; }
</style>
</head>
<body onload="init(errorMsg);gotoAnchor('<?= $this->data['anchor']; ?>');">
	<div class="toolbar">
		<div class="toolbarwrap">
			<fieldset style="width: auto;">
			<legend align="left"><?= $this->lang['subtitle']; ?></legend>
				<div style="margin: 5px;vertical-align: middle; line-height: 25px;">
				<div style="float: left;"><?= $this->lang['select_directory']; ?>&nbsp;<select name="folder" onchange="selectPath(this);">
				<? foreach($this->data['dirlist'] as $dir) { ?>
					<option value="<?= $dir['abs_path']; ?>" <? if ($this->data['selectedPath'] == $dir['path']) { echo "selected"; } ?>><?= $dir['path']; ?></option>
				<? } ?>
				</select></div>
				<div style="float: right;">
					<nobr>
						<?
							foreach ($this->data['toolbar'] as $item) {
								if ($item['command'] == "separator")
									echo '<img src="themes/' . $this->theme . '/images/spacer.gif" width="1" height="15" class="mceSeparatorLine" />';
								else
									echo '<a href="javascript:execFileCommand(\'' . $item['command'] . '\');"><img id="' . $item['command'] . '" src="themes/' . $this->theme . '/images/' . $item['icon'] . '" alt="' . $this->lang[$item['command']] . '" title="' . $this->lang[$item['command']] . '" border="0" class="mceButtonDisabled" width="20" height="20" /></a>';
							}
						?>
					</nobr>
				</div>
				</div>
				<br style="clear:both;" />
			</fieldset>
		</div>
	</div>
	<div class="thwrapper">
		<div class="thwrapperwrapper">
			<? if (count($this->data['files']) == 0) { ?>
				<?= $this->lang['no_files']; ?>
			<? } ?>

			<? foreach($this->data['files'] as $file) { ?>
				<div class="thumbnail">
					<a name="f_<?= $file['name']; ?>"></a>
					<div class="image">
						<div class="imagewrapper">
							<div style="float: left;">
							<? if (in_array("preview", $this->data['image_tools'])) { ?>
								<a href="javascript:imagePreview(<?= $file['real_width']; ?>,<?= $file['real_height']; ?>,'<?= $file['path'] ?>');"><img src="themes/<?= $this->theme ?>/images/tool_preview.gif" width="16" height="16" border="0" /></a>
							<? } ?>
							<? if (in_array("info", $this->data['image_tools'])) { ?>
								<a href="javascript:imageInfo('<?= $file['name'] ?>', 'block');"><img src="themes/<?= $this->theme ?>/images/tool_info.gif" width="16" height="16" onmouseover="imageInfo('<?= $file['name'] ?>', 'block');" onmouseout="imageInfo('<?= $file['name'] ?>', 'none');" border="0" /></a>
							<? } ?>
							<? if (in_array("delete", $this->data['image_tools'])) { ?>
								<a href="javascript:imageDelete('<?= $file['path'] ?>');"><img src="themes/<?= $this->theme ?>/images/tool_del.gif" width="16" height="16" border="0" /></a>
							<? } ?>
							<? if (in_array("edit", $this->data['image_tools']) && $file['editable']) { ?>
								<a href="javascript:imageEdit('<?= $file['path'] ?>');"><img src="themes/<?= $this->theme ?>/images/tool_edit.gif" width="16" height="16" border="0" /></a>
							<? } ?>
							</div>
							<? if ($this->data['extension_image']) { ?>
							<div style="float: right;"><img src="themes/<?= $this->theme ?>/images/filetypes/<?= $file['icon']; ?>" width="16" height="16"></div>
							<? } ?>
							<br style="clear:both;" />
							<div class="imageinfo" id="img_<?= $file['name']; ?>">
								<div style="margin: 5px;">
									<? if (in_array("name", $this->data['information'])) { ?>
										<?= $file['name']; ?><br />
									<? } ?>
									<? if (count($this->data['information']) != 0) { ?>
										<table border="0" cellspacing="0" cellpadding="0" width="100%" class="infotable">
										<? if (in_array("width", $this->data['information'])) { ?>
										<tr>
											<td><?= $this->lang['width']; ?></td>
											<td><?= $file['real_width']; ?> px</td>
										</tr>
										<? } ?>
										<? if (in_array("height", $this->data['information'])) { ?>
										<tr>
											<td><?= $this->lang['height']; ?></td>
											<td><?= $file['real_height']; ?> px</td>
										</tr>
										<? } ?>
										<? if (in_array("type", $this->data['information'])) { ?>
										<tr>
											<td><?= $this->lang['type']; ?></td>
											<td><?= $this->lang[$file['type']] ?></td>
										</tr>
										<? } ?>
										<? if (in_array("size", $this->data['information'])) { ?>
										<tr>
											<td><?= $this->lang['size']; ?></td>
											<td><?= $file['size']; ?></td>
										</tr>
										<? } ?>
										<? if (in_array("scale", $this->data['information'])) { ?>
											<? if ($file['scale'] != 0) { ?>
											<tr>
												<td><?= $this->lang['scale']; ?></td>
												<td><?= $file['scale']; ?>%</td>
											</tr>
											<? } ?>
										<? } ?>
										</table>
									<? } ?>
								</div>
							</div>
						</div>
						<div style="text-align: center;">
							<? if ($this->data['insert']) { ?>
							<a href="javascript:insertURL('<?= $file['previewurl']; ?>');"><img src="<?= $file['url']; ?>" width="<?= $file['width']; ?>" height="<?= $file['height']; ?>" id="<?= $file['name']; ?>" style="margin-top: <?= $file['margin']; ?>px;" title="<?= $file['name']; ?>" alt="<?= $file['name']; ?>" border="0" /></a>
							<? } else { ?>
							<img src="<?= $file['url']; ?>" width="<?= $file['width']; ?>" height="<?= $file['height']; ?>" id="<?= $file['name']; ?>" style="margin-top: <?= $file['margin']; ?>px;" title="<?= $file['name']; ?>" alt="<?= $file['name']; ?>"  />
							<? } ?>
						</div>
					</div>
					<div class="imagename" style="position: absolute; top: <?= $this->data['thumbnail_height']+5; ?>px; width:<?= $this->data['thumbnail_width']+2; ?>px; text-align: center;"><?= $file['name']; ?></div>
				</div>
			<? } ?>
		</div>
	</div>
</body>
</html>

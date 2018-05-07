<? echo '<?xml version="1.0" encoding="iso-8859-1"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="sv" xml:lang="sv">
<head>
<title>Image editor</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="imagetoolbar" content="no" />
<script type="text/javascript" src="themes/<?= $this->theme ?>/jscripts/SelectionRect.js"></script>
<script type="text/javascript">
var selectionRect = new SelectionRect();
var action = "<?=$this->data['action']?>", orgWidth, orgHeight;
var imageStatus = "<?=$this->data['status']?>";
var orgPath = "<?=$this->data['orgpath']?>";
var demo = "<?=$this->data['demo']?>";
var demoMsg = "<?=$this->data['demo_msg']?>";

function init() {
	var elm = document.getElementById("imageWrapper");
	var image = document.getElementById("image");

	selectionRect.onSelection = onSelectionHandler;
	selectionRect.init(elm);
	selectionRect.setSelectionColor("black");
	selectionRect.setVisible(false);

	var formObj = document.forms[0];

	orgWidth = image.width;
	orgHeight = image.height;

	formObj.resize_width_field.value = orgWidth;
	formObj.resize_height_field.value = orgHeight;

	selectionRect.setBounderies(0, 0, orgWidth, orgHeight);

	setClassLock('switchSelectionTool', false);
	switchClass('switchSelectionTool', 'mceButtonDisabled');
	setClassLock('switchSelectionTool', true);

	if (imageStatus != "processed") {
		setClassLock('saveTool', false);
		switchClass('saveTool', 'mceButtonDisabled');
		setClassLock('saveTool', true);

		setClassLock('revertTool', false);
		switchClass('revertTool', 'mceButtonDisabled');
		setClassLock('revertTool', true);
	}

	if (imageStatus == "saved" && window.opener)
		window.opener.execFileCommand("refresh");

	if (action == "crop") {
		action = "";
		toggleCropTool();
	}

	if (action == "resize") {
		action = "";
		toggleResizeTool();
	}
}

function onSelectionHandler(selection, rect) {
	var formObj = document.forms[0];

	formObj.x_field.value = rect.x;
	formObj.y_field.value = rect.y;
	formObj.width_field.value = rect.width;
	formObj.height_field.value = rect.height;
}

function updateSelection(type) {
	var formObj = document.forms[0];

	if (action == "crop")
		selectionRect.setRect(parseInt(formObj.x_field.value), parseInt(formObj.y_field.value), parseInt(formObj.width_field.value), parseInt(formObj.height_field.value));
	else if (action == "resize") {
		var elm = document.getElementById("image");
		var newWidth = formObj.resize_width_field.value;
		var newHeight = formObj.resize_height_field.value;

		if (formObj.resize_proportions.checked) {
			if (type == "width") {
				newHeight = Math.round(orgHeight * (newWidth / orgWidth));
				formObj.resize_height_field.value = newHeight;
			} else {
				newWidth = Math.round(orgWidth * (newHeight / orgHeight));
				formObj.resize_width_field.value = newWidth;
			}
		}

		elm.width = newWidth;
		elm.height = newHeight;
	}

	return false;
}

function toggleSelectionColor() {
	if (getClassLock('switchSelectionTool'))
		return;

	selectionRect.setSelectionColor(selectionRect.getSelectionColor() == "white" ? "black" : "white");
}

function toggleCropTool() {
	action = action == "crop" ? "" : "crop";

	if (action == "crop") {
		selectionRect.setVisible(true);

		switchClass('cropTool', 'mceButtonSelected');
		setClassLock('cropTool', true);

		setClassLock('switchSelectionTool', false);
		switchClass('switchSelectionTool', 'mceButtonNormal');

		setClassLock('resizeTool', false);
		switchClass('resizeTool', 'mceButtonNormal');

		document.getElementById('cropPanel').style.display = "block";
		document.getElementById('resizePanel').style.display = "none";

		var elm = document.getElementById("image");
		elm.width = orgWidth;
		elm.height = orgHeight;

		var formObj = document.forms[0];
		formObj.resize_width_field.value = orgWidth;
		formObj.resize_height_field.value = orgHeight;
	} else {
		selectionRect.setVisible(false);

		setClassLock('cropTool', false);
		switchClass('cropTool', 'mceButtonNormal');

		setClassLock('resizeTool', false);
		switchClass('resizeTool', 'mceButtonNormal');

		setClassLock('switchSelectionTool', false);
		switchClass('switchSelectionTool', 'mceButtonDisabled');
		setClassLock('switchSelectionTool', true);

		document.getElementById('cropPanel').style.display = "none";
	}
}

function toggleResizeTool() {
	action = action == "resize" ? "" : "resize";

	if (action == "resize") {
		selectionRect.setVisible(false);

		setClassLock('cropTool', false);
		switchClass('cropTool', 'mceButtonNormal');

		setClassLock('switchSelectionTool', false);
		switchClass('switchSelectionTool', 'mceButtonDisabled');
		setClassLock('switchSelectionTool', true);

		switchClass('resizeTool', 'mceButtonSelected');
		setClassLock('resizeTool', true);

		document.getElementById('cropPanel').style.display = "none";
		document.getElementById('resizePanel').style.display = "block";
	} else {
		setClassLock('resizeTool', false);
		switchClass('resizeTool', 'mceButtonNormal');

		setClassLock('cropTool', false);
		switchClass('cropTool', 'mceButtonNormal');

		document.getElementById('resizePanel').style.display = "none";

		var elm = document.getElementById("image");
		elm.width = orgWidth;
		elm.height = orgHeight;

		var formObj = document.forms[0];
		formObj.resize_width_field.value = orgWidth;
		formObj.resize_height_field.value = orgHeight;
	}
}

function switchClass(element_id, class_name) {
	var element = document.getElementById(element_id);
	if (element != null && !element.classLock) {
		element.oldClassName = element.className;
		element.className = class_name;
	}
}

function restoreClass(element_id) {
	var element = document.getElementById(element_id);
	if (element != null && element.oldClassName && !element.classLock) {
		element.className = element.oldClassName;
		element.oldClassName = null;
	}
}

function setClassLock(element_id, lock_state) {
	var element = document.getElementById(element_id);
	if (element != null)
		element.classLock = lock_state;
}

function getClassLock(element_id) {
	var element = document.getElementById(element_id);
	if (element != null)
		return element.classLock;

	return false;
}

function cropImage() {
	var formObj1 = document.forms[0];
	var formObj2 = document.forms[1];

	formObj2.action.value = "crop";
	formObj2.orgwidth.value = orgWidth;
	formObj2.orgheight.value = orgHeight;
	formObj2.left.value = formObj1.x_field.value;
	formObj2.top.value = formObj1.y_field.value;
	formObj2.newwidth.value = formObj1.width_field.value;
	formObj2.newheight.value = formObj1.height_field.value;

	document.getElementById('processingDialog').style.display = 'block';

	formObj2.submit();
}

function resizeImage() {
	var formObj1 = document.forms[0];
	var formObj2 = document.forms[1];

	formObj2.action.value = "resize";
	formObj2.orgwidth.value = orgWidth;
	formObj2.orgheight.value = orgHeight;
	formObj2.left.value = "0";
	formObj2.top.value = "0";
	formObj2.newwidth.value = formObj1.resize_width_field.value;
	formObj2.newheight.value = formObj1.resize_height_field.value;

	document.getElementById('processingDialog').style.display = 'block';

	formObj2.submit();
}

function saveImage() {
	if (getClassLock('saveTool'))
		return;

	if (demo == "true") {
		alert(demoMsg);
		return;
	}

	var formObj = document.forms[1];

	formObj.action.value = "save";

	formObj.submit();
}

function revertImage() {
	document.location = "edit_image.php?path=" + escape(orgPath) + "&action=" + action;
}
</script>
<link href="themes/<?= $this->theme ?>/css/crop_tool.css" rel="stylesheet" type="text/css" />
</head>
<body onload="init();">

<form onsubmit="return updateSelection();";>
	<div class="toolbar">
		<div class="tools">
			<a href="javascript:saveImage();" onmouseover="switchClass('saveTool','mceButtonOver');" onmouseout="switchClass('saveTool','mceButtonNormal');" onmousedown="switchClass('saveTool','mceButtonNormal');"><img id="saveTool" src="themes/<?= $this->theme ?>/images/edit_image/save_tool.gif" border="0" class="mceButtonNormal" alt="<?= $this->lang['save_image'] ?>" title="<?= $this->lang['save_image'] ?>" /></a>
			<a href="javascript:revertImage();" onmouseover="switchClass('revertTool','mceButtonOver');" onmouseout="switchClass('revertTool','mceButtonNormal');" onmousedown="switchClass('revertTool','mceButtonNormal');"><img id="revertTool" src="themes/<?= $this->theme ?>/images/edit_image/revert_tool.gif" border="0" class="mceButtonNormal" alt="<?= $this->lang['revert_image'] ?>" title="<?= $this->lang['revert_image'] ?>" /></a>
			<a href="javascript:toggleSelectionColor();" onmouseover="switchClass('switchSelectionTool','mceButtonOver');" onmouseout="switchClass('switchSelectionTool','mceButtonNormal');" onmousedown="switchClass('switchSelectionTool','mceButtonNormal');"><img id="switchSelectionTool" src="themes/<?= $this->theme ?>/images/edit_image/toggle_selection_color.gif" border="0" class="mceButtonNormal" alt="<?= $this->lang['toggle_color'] ?>" title="<?= $this->lang['toggle_color'] ?>" /></a></span>
			<a href="javascript:toggleCropTool();" onmouseover="switchClass('cropTool','mceButtonOver');" onmouseout="switchClass('cropTool','mceButtonNormal');" onmousedown="switchClass('cropTool','mceButtonNormal');"><img id="cropTool" src="themes/<?= $this->theme ?>/images/edit_image/crop_tool.gif" border="0" class="mceButtonNormal" alt="<?= $this->lang['crop_image'] ?>" title="<?= $this->lang['crop_image'] ?>" /></a>
			<a href="javascript:toggleResizeTool();" onmouseover="switchClass('resizeTool','mceButtonOver');" onmouseout="switchClass('resizeTool','mceButtonNormal');" onmousedown="switchClass('resizeTool','mceButtonNormal');"><img id="resizeTool" src="themes/<?= $this->theme ?>/images/edit_image/resize_tool.gif" border="0" class="mceButtonNormal" alt="<?= $this->lang['resize_image'] ?>" title="<?= $this->lang['resize_image'] ?>" /></a>
		</div>

		<div id="cropPanel">
			<div class="cropDimentions">
				x: <input type="text" class="inputtext" id="x_field" name="x_field" value="" onkeyup="updateSelection();" onchange="updateSelection();" />
				y: <input type="text" class="inputtext" id="y_field" name="y_field" value="" onkeyup="updateSelection();" onchange="updateSelection();" />
				<?= $this->lang['width'] ?> <input type="text" class="inputtext" id="width_field" name="width_field" value="" onchange="updateSelection();" />
				<?= $this->lang['height'] ?> <input type="text" class="inputtext" id="height_field" name="height_field" value="" onchange="updateSelection();" />
			</div>
			<div class="confirmAction">
				<a href="javascript:toggleCropTool();" onmouseover="switchClass('cropCancel','mceButtonOver');" onmouseout="switchClass('cropCancel','mceButtonNormal');" onmousedown="switchClass('cropCancel','mceButtonNormal');"><img id="cropCancel" src="themes/<?= $this->theme ?>/images/edit_image/cancel.gif" border="0" class="mceButtonNormal" /></a>
				<a href="javascript:cropImage();" onmouseover="switchClass('cropApply','mceButtonOver');" onmouseout="switchClass('cropApply','mceButtonNormal');" onmousedown="switchClass('cropApply','mceButtonNormal');"><img id="cropApply" src="themes/<?= $this->theme ?>/images/edit_image/apply.gif" border="0" class="mceButtonNormal" /></a>
			</div>
		</div>

		<div id="resizePanel">
			<div class="cropDimentions">
				<?= $this->lang['width'] ?> <input type="text" class="inputtext" id="resize_width_field" name="resize_width_field" value="" onkeyup="updateSelection('width');" onchange="updateSelection('width');" />
				<?= $this->lang['height'] ?> <input type="text" class="inputtext" id="resize_height_field" name="resize_height_field" value="" onkeyup="updateSelection('height');" onchange="updateSelection('height');" />
				<input type="checkbox" class="inputcheckbox" id="resize_proportions" name="resize_proportions" checked="checked" /><label><?= $this->lang["constrain"] ?></label> 
			</div>
			<div class="confirmAction">
				<a href="javascript:toggleResizeTool();" onmouseover="switchClass('resizeCancel','mceButtonOver');" onmouseout="switchClass('resizeCancel','mceButtonNormal');" onmousedown="switchClass('resizeCancel','mceButtonNormal');"><img id="resizeCancel" src="themes/<?= $this->theme ?>/images/edit_image/cancel.gif" border="0" class="mceButtonNormal" /></a>
				<a href="javascript:resizeImage();" onmouseover="switchClass('resizeApply','mceButtonOver');" onmouseout="switchClass('resizeApply','mceButtonNormal');" onmousedown="switchClass('resizeApply','mceButtonNormal');"><img id="resizeApply" src="themes/<?= $this->theme ?>/images/edit_image/apply.gif" border="0" class="mceButtonNormal" /></a>
			</div>
		</div>
	</div>

	<br style="clear: both" />
</form>

<div id="imageWrapper"><img id="image" src="<?=$this->data['src']?>" style="cursor: crosshair;" /></div>

<div id="processingDialog"><div id="processingMsg"><?=$this->lang['please_wait']?></div></div>

<form action="process_image.php" method="post">
	<input type="hidden" name="action" value="" />
	<input type="hidden" name="orgpath" value="<?=$this->data['orgpath']?>" />
	<input type="hidden" name="path" value="<?=$this->data['path']?>" />
	<input type="hidden" name="orgwidth" value="" />
	<input type="hidden" name="orgheight" value="" />
	<input type="hidden" name="top" value="" />
	<input type="hidden" name="left" value="" />
	<input type="hidden" name="newwidth" value="" />
	<input type="hidden" name="newheight" value="" />
</form>

</body>
</html>
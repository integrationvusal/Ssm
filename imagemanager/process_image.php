<?php
	require_once("includes/general.php");
	require_once("classes/FileSystems/LocalFileImpl.php");

	@set_time_limit(20*60); // 20 minutes execution time

	$orgWidth = getRequestParam("orgwidth");
	$orgHeight = getRequestParam("orgheight");
	$newWidth = getRequestParam("newwidth");
	$newHeight = getRequestParam("newheight");
	$left = getRequestParam("left");
	$top = getRequestParam("top");
	$action = getRequestParam("action");
	$path = getRequestParam("path");
	$orgpath = getRequestParam("orgpath", "");

	if ($orgpath == "")
		$orgpath = $path;

	$temp_image = "mcic_". session_id() ."";

	verifyAccess($mcImageManagerConfig);

	$rootpath = removeTrailingSlash(getRequestParam("rootpath", toUnixPath(getRealPath($mcImageManagerConfig, 'filesystem.rootpath'))));
	$fileFactory =& new FileFactory($mcImageManagerConfig, $rootPath);

	$file =& $fileFactory->getFile($path);
	$config = $file->getConfig();
	$demo = checkBool($config['general.demo']) ? "true" : "false";

	$tools = explode(',', $config['thumbnail.image_tools']);
	if (!in_array("edit", $tools))
		die("The thumbnail.image_tools needs to include edit.");

	// File info
	$fileInfo = getFileType($file->getAbsolutePath());
	$file_icon = $fileInfo['icon'];
	$file_type = $fileInfo['type'];
	$file_ext = $fileInfo['ext'];

	$tempFile =& $fileFactory->getFile(dirname($file->getAbsolutePath()) . "/" . $temp_image .".". $file_ext);

	switch ($action) {
		case "resize":
			$status = resizeImage($file->getAbsolutePath(), $tempFile->getAbsolutePath(), $file_ext, $newWidth, $newHeight, $orgWidth, $orgHeight);
			if ($status)
				$tempFile->importFile();

			$outpath = $tempFile->getAbsolutePath();
			$outstatus = "processed";
		break;

		case "crop":
			$status = cropImage($file->getAbsolutePath(), $tempFile->getAbsolutePath(), $file_ext, $top, $left, $newWidth, $newHeight);
			if ($status)
				$tempFile->importFile();

			$outpath = $tempFile->getAbsolutePath();
			$outstatus = "processed";
		break;

		case "save":
			// Skip save on demo
			if ($demo == "true")
				break;

			$orgFile =& $fileFactory->getFile($orgpath);
			if ($orgFile->exists())
				$orgFile->delete();

			$file->renameTo($orgFile);
			$outpath = $orgFile->getAbsolutePath();
			$outstatus = "saved";
		break;

		default :
			die("No action");
	}

function cropImage($source, $target, $fileType, $top, $left, $newWidth, $newHeight) {
	if (!gdExists())
		return false;

	$functions = getFunctionsByType($fileType);
	$source = $functions['createfunction']($source);
	$image = ImageCreateTrueColor($newWidth, $newHeight);
	$status = ImageCopyResampled($image, $source, 0, 0, $left, $top, $newWidth, $newHeight, $newWidth, $newHeight);

	// this should set it to same file
	if ($status) {
		if ($functions['quality'])
			$functions['imagefunction']($image, $target, 100);
		else
			$functions['imagefunction']($image, $target);
	}

	return $status;
}

function resizeImage($source, $target, $fileType, $newWidth, $newHeight, $orgWidth, $orgHeight) {
	if (!gdExists())
		return false;

	$functions = getFunctionsByType($fileType);
	$source = $functions['createfunction']($source);
	$image = ImageCreateTrueColor($newWidth, $newHeight);
	$status = ImageCopyResampled($image, $source, 0, 0, 0, 0, $newWidth, $newHeight, $orgWidth, $orgHeight);

	// this should set it to same file
	if ($status) {
		if ($functions['quality'])
			$functions['imagefunction']($image, $target, 100);
		else
			$functions['imagefunction']($image, $target);
	}
	return $status;
}

header("Location: edit_image.php?path=". $outpath ."&orgpath=". $orgpath ."&action=". $action ."&status=". $outstatus);

?>
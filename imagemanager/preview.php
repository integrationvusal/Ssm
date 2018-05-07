<?php
	require_once("includes/general.php");
	require_once("classes/FileSystems/FileFactory.php");
	require_once("classes/FileSystems/LocalFileImpl.php");

	$data = array();
	verifyAccess($mcImageManagerConfig);
	$path = getRequestParam("path", toUnixPath(getRealPath($mcImageManagerConfig, 'filesystem.path')));
	$rootpath = getRequestParam("rootpath", toUnixPath(getRealPath($mcImageManagerConfig, 'filesystem.rootpath')));
	$fileFactory =& new FileFactory($mcImageManagerConfig, $rootpath);
	$targetFile =& $fileFactory->getFile($path);
	$targetFolder = $targetFile =& $targetFile->getParentFile();
	$config = $targetFile->getConfig();

	// Preview path
	$wwwroot = removeTrailingSlash(toUnixPath(getWWWRoot($config)));
	$urlprefix = removeTrailingSlash(toUnixPath($config['preview.urlprefix']));
	$urlsuffix = toUnixPath($config['preview.urlsuffix']);

	addFileEventListeners($fileFactory);

	// Get filtered files
	$fileFilter =& new BasicFileFilter();
	$fileFilter->setIncludeDirectoryPattern($config['filesystem.include_directory_pattern']);
	$fileFilter->setExcludeDirectoryPattern($config['filesystem.exclude_directory_pattern']);
	$fileFilter->setIncludeFilePattern($config['filesystem.include_file_pattern']);
	$fileFilter->setExcludeFilePattern($config['filesystem.exclude_file_pattern']);
	$fileFilter->setOnlyFiles(true);
	$files =& $targetFolder->listFilesFiltered($fileFilter);

	$fileList = array();
	$previous = array();
	$previous['width'] = 0;
	$previous['height'] = 0;
	$previous['path'] = "";

	$next = array();
	$next['width'] = 0;
	$next['height'] = 0;
	$next['path'] = "";
	$current = false;

	foreach ($files as $file) {
		if ($file->isDirectory())
			continue;

		$fileItem = array();
		$imageSize = array();
		$filepath = $file->getAbsolutePath();

		if ($filepath == $path) {
			$current = true;
			continue;
		}

		$fileItem['margin'] = 0;

		$imageSize = getimagesize($file->getAbsolutePath());
		$fileItem['width'] = $imageSize[0];
		$fileItem['height'] = $imageSize[1];

		$fileItem['name'] = basename($file->getAbsolutePath());
		$fileItem['path'] = $file->getAbsolutePath();

		if ($current) {
			$next = $fileItem;
			break;
		} else {
			$previous = $fileItem;
		}
	}

	$imagesize = getimagesize($path);

	$pos = strpos($path, $wwwroot);
	if ($pos !== false && $pos == 0)
		$data['previewurl'] = $urlprefix . substr($path, strlen($wwwroot)) . $urlsuffix;
	else
		$data['previewurl'] = "";

	$data['next'] = $next;
	$data['previous'] = $previous;
	$data['path'] = $path;
	$data['name'] = basename($path);
	$data['width'] = $imagesize[0];
	$data['height'] = $imagesize[1];
	$data['size'] = getSizeStr($targetFile->length());
	$data['errorMsg'] = "";

	// Render output
	renderPage("preview.tpl.php", $data);
?>

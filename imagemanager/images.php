<?php	error_reporting(0);
	require_once("includes/general.php");
	require_once("includes/toolbar.php");
	require_once("classes/FileSystems/LocalFileImpl.php");

	$errorMsg = getRequestParam("errorMsg", "");
	// Get path and config
	verifyAccess($mcImageManagerConfig);

	$path = removeTrailingSlash(getRequestParam("path", toUnixPath(getRealPath($mcImageManagerConfig, 'filesystem.path'))));
	$url = getRequestParam("url", "");
	$dropcache = getRequestParam("dropcache", false);
	$rootpath = removeTrailingSlash(getRequestParam("rootpath", toUnixPath(getRealPath($mcImageManagerConfig, 'filesystem.rootpath'))));
	$fileFactory =& new FileFactory($mcImageManagerConfig, $rootPath);

	$rootFile =& $fileFactory->getFile($rootpath);
	if (!$rootFile->exists())
		die("Critical error: Could not find defined root path.");

	// Handle URL
	if ($url != "") {
		// Is absolute URL
		if (strpos($url, $mcImageManagerConfig['preview.urlprefix']) === 0) {
			// Trim away prefix
			$path = substr($url, strlen($mcImageManagerConfig['preview.urlprefix'])-1);

			// Add rest to wwwroot
			$path = toUnixPath(getWWWRoot($mcImageManagerConfig)) . $path;
		}

		// Try step 2
		if ($path == "") {
			$tmppath = toUnixPath(getWWWRoot($mcImageManagerConfig)) . $url;
			if ($fileFactory->verifyPath($tmppath)) {
				$file = $fileFactory->getFile($tmppath);

				if ($file->exists())
					$path = $file->getAbsolutePath();
			}
		}

		// Try step 2
		if ($path == "") {
			$tmppath = toUnixPath(getRealPath($mcImageManagerConfig, 'filesystem.rootpath')) . $url;
			if ($fileFactory->verifyPath($tmppath)) {
				$file = $fileFactory->getFile($tmppath);

				if ($file->exists())
					$path = $file->getAbsolutePath();
			}
		}
	}

	// Remove session last path
	if (getRequestParam("remember", "true") != "true")
		unset($_SESSION['mc_imagebrowser_lastpath']);

	// Use last path
	if (isset($_SESSION['mc_imagebrowser_lastpath']) && !getRequestParam("path", ""))
		$path = $_SESSION['mc_imagebrowser_lastpath'];

	// Invalid path, use root path
	if (!$fileFactory->verifyPath($path))
		$path = $rootpath;

	$targetFile =& $fileFactory->getFile($path);

	// Check if it's exits use root instead
	if (!$targetFile->exists()) {
		$targetFile = $rootFile;
		$dropcache = true;
	}

	if ($targetFile->isFile()) {
		$anchor = basename($path);
		$path = $targetFile->getParent();
		$targetFile =& $targetFile->getParentFile();
	} else
		$anchor = "none";

	$config = $targetFile->getConfig();
	$rootconfig = $rootFile->getConfig();
	addFileEventListeners($fileFactory);

	// Save away path
	$_SESSION['mc_imagebrowser_lastpath'] = $path;
	$selectedPath = getUserFriendlyPath($path);

	// Get rest of input
	$action = getRequestParam("action");
	$value = getRequestParam("value", "");
	$formname = getRequestParam("formname", "");
	$elementnames = getRequestParam("elementnames", "");
	$isGD = gdExists();

	$selectedFiles = array();
	foreach ($_REQUEST as $name => $value) {
		if (strpos($name, "file_") !== false || strpos($name, "dir_") !== false)
			$selectedFiles[] =& $fileFactory->getFile($value);
	}

	// Do action
	switch ($action) {
		case "delete":
		// No access, tool disabled
		if (!in_array("delete", explode(',', $config['thumbnail.image_tools'])))
			die($mcLanguage['error_delete_failed']);

		if (checkBool($config['general.demo']))
			break;

			$canread = false;
			$canwrite = false;
			foreach ($selectedFiles as $file) {
				$canread = $file->canRead() && checkBool($config["filesystem.readable"]) ? true : false;
				$canwrite = $file->canWrite() && checkBool($config["filesystem.writable"]) ? true : false;

				if ($canwrite) {
					// Check for Thumbnail
					if ($config['thumbnail.gd.delete'] == true) {
						$th_folder = "/". $config['thumbnail.gd.folder'];
						$th_folder = dirname($file->getAbsolutePath()) . $th_folder;
						$thFolder = $fileFactory->getFile($th_folder);
						if ($thFolder->exists()) {
							$th_path = $thFolder->getAbsolutePath() . "/" . $config['thumbnail.gd.prefix'] . basename($file->getAbsolutePath());
							$th = $fileFactory->getFile($th_path);

							if ($th->exists())
								$th->delete();
						}
					}
					$file->delete();
				}
				else
					$errorMsg = "error_no_access";
			}
			header("Location: images.php?path=". $path ."&errorMsg=". $errorMsg);
			die();
			break;
	}

	$data = array();
	$data['rootpath'] = $rootpath;

	// Cache stuff, we need to have cache cause dirlist can be really big.
	if ($dropcache != false)
		unset($_SESSION['dropdown']);

	if (($config['dropdown.cache'] == true) && (isset($_SESSION['dropdown'])) && ($dropcache == false)) {
		$dirList = unserialize($_SESSION['dropdown']);
	} else {
		// Get filtered dirs, deep
		$fileFilter =& new BasicFileFilter();
		$fileFilter->setIncludeDirectoryPattern($rootconfig['filesystem.include_directory_pattern']);
		$fileFilter->setExcludeDirectoryPattern($rootconfig['filesystem.exclude_directory_pattern']);
		$fileFilter->setOnlyDirs(true);

		$treeHandler = new ConfigFilteredFileTreeHandler();
		$treeHandler->setOnlyDirs(true);

		$rootFile->listTree($treeHandler);
		$dirsFiltered =& $treeHandler->getFileArray();
		// end

		if (!is_array($dirsFiltered))
			$dirsFiltered = array();

		$dirs = array();
		// End filter run

		$dirconfig = "";
		$dirList = array();
		$evenDir = true;
		
		foreach($dirsFiltered as $dir) {
			$dirconfig = $dir->getConfig();

			if (($dirconfig['dropdown.exclude_path_pattern']) && (preg_match($dirconfig['dropdown.exclude_path_pattern'], getUserFriendlyPath($dir->getAbsolutePath())))) {
				continue;
			}

			if (($dirconfig['dropdown.include_path_pattern']) && (!preg_match($dirconfig['dropdown.include_path_pattern'], getUserFriendlyPath($dir->getAbsolutePath())))) {
				continue;
			}

			$dirItem = array();
			
			$dirPath = $dir->getAbsolutePath();
			$dirItem['even'] = $evenDir;
			$dirItem['abs_path'] = $dirPath;
			$dirItem['path'] = getUserFriendlyPath($dirPath);

			$evenDir = !$evenDir;
			$dirList[] = $dirItem;
		}

		if (($config['dropdown.cache'] == true) && (!isset($_SESSION['dropdown']))) {
			$_SESSION['dropdown'] = serialize($dirList);
		}
	}

	// Check so that we have the current folder in the list, or its screwed.
	$valid_folder = false;
	foreach($dirList as $dirListItem) {
		if ($targetFile->getAbsolutePath() == $dirListItem['abs_path']) {
			$valid_folder = true;
			break;
		}
	}

	// Dir is not in droplist, use first one or root if the first one is gone too
	if ($valid_folder == false) {
		if (count($dirList) > 0) {
			$targetFile =& $fileFactory->getFile($dirList[0]['abs_path']);
			if (!$targetFile->exists())
				$targetFile = $rootFile;

			// Grab new config
			$config = $targetFile->getConfig();
		} else {
			$dirItem = array();
			$dirItem['even'] = false;
			$dirItem['abs_path'] = $targetFile->getAbsolutePath();
			$dirItem['path'] = getUserFriendlyPath($targetFile->getAbsolutePath());

			$dirList[] = $dirItem;
		}
	}

	$dirsFiltered = array();

	// Get filtered files
	$fileFilter = new BasicFileFilter();
	$fileFilter->setIncludeDirectoryPattern($config['filesystem.include_directory_pattern']);
	$fileFilter->setExcludeDirectoryPattern($config['filesystem.exclude_directory_pattern']);
	$fileFilter->setIncludeFilePattern($config['filesystem.include_file_pattern']);
	$fileFilter->setExcludeFilePattern($config['filesystem.exclude_file_pattern']);
	$fileFilter->setIncludeExtensions($config['filesystem.extensions']);
	$fileFilter->setOnlyFiles(true);
	$files =& $targetFile->listFilesFiltered($fileFilter);

	$fileList = array();
	$even = true;

// List files
	foreach ($files as $file) {
		if ($file->isDirectory())
			continue;

		$fileItem = array();
		$imageSize = array();
		$filepath = $file->getAbsolutePath();
		$fileItem['margin'] = 0;

		$imageSize = getimagesize($file->getAbsolutePath());
		$fileItem['real_width'] = $imageSize[0];
		$fileItem['real_height'] = $imageSize[1];

		// calculate percentage width and height
		if ($config['thumbnail.scale_mode'] == "percentage") {
			if ($config['thumbnail.height'] > $config['thumbnail.width'])
				$target = $config['thumbnail.width'];
			else
				$target = $config['thumbnail.height'];

			$imageSize = imageResize($imageSize[0], $imageSize[1], $target);

			$fileItem['width'] = $imageSize['width'];
			$fileItem['height'] = $imageSize['height'];
			$fileItem['scale'] = $imageSize['scale'];

			// Calculate margin
			if (($config['thumbnail.height'] - $imageSize['height']) > 0)
				$fileItem['margin'] = (($config['thumbnail.height'] - $imageSize['height']) / 2);
	
		} else {
			$fileItem['width'] = $config['thumbnail.width'];
			$fileItem['height'] = $config['thumbnail.height'];
			$fileItem['scale'] = 0;
		}

		$fileItem['name'] = basename($file->getAbsolutePath());
		$fileItem['path'] = $file->getAbsolutePath();
		$fileItem['size'] = getSizeStr($file->length());
		$fileItem['modificationdate'] = date($config['filesystem.datefmt'], $file->lastModified());
		$fileItem['even'] = $even;
		$fileItem['hasReadAccess'] = $file->canRead() && checkBool($config["filesystem.readable"]) ? "true" : "false";
		$fileItem['hasWriteAccess'] = $file->canWrite() && checkBool($config["filesystem.writable"]) ? "true" : "false";

		// File info
		$fileType = getFileType($file->getAbsolutePath());
		$fileItem['icon'] = $fileType['icon'];
		$fileItem['type'] = $fileType['type'];
		$fileItem['ext'] = $fileType['ext'];
		$fileItem['editable'] = $isGD && ($fileType['ext'] == "gif" || $fileType['ext'] == "jpg" || $fileType['ext'] == "jpeg" || $fileType['ext'] == "png");

		// Preview path
		$wwwroot = removeTrailingSlash(toUnixPath(getWWWRoot($config)));
		$urlprefix = removeTrailingSlash(toUnixPath($config['preview.urlprefix']));
		$urlsuffix = toUnixPath($config['preview.urlsuffix']);

		$fileItem['previewurl'] = "";
		$pos = strpos($filepath, $wwwroot);
		if ($pos !== false && $pos == 0)
			$fileItem['previewurl'] = $urlprefix . substr($filepath, strlen($wwwroot)) . $urlsuffix;
		else
			$fileItem['previewurl'] = "ERROR IN PATH";

		if (($fileItem['editable'] == true) AND (checkBool($config['thumbnail.gd.enabled']) == true))
			$fileItem['url'] = "thumbnail.php?path=". $fileItem['path'] ."&width=". $fileItem['width'] ."&height=". $fileItem['height'] ."&ext=". $fileItem['ext'];
		else
			$fileItem['url'] = $fileItem['previewurl'];

		$even = !$even;
		$fileList[] = $fileItem;
	}

	$data['files'] = $fileList;
	$data['path'] = $path;
	$data['hasReadAccess'] = $targetFile->canRead() ? "true" : "false";
	$data['hasWriteAccess'] = $targetFile->canWrite() ? "true" : "false";

	$toolbarCommands = explode(',', $config['general.toolbar']);
	$tools = array();
	foreach ($toolbarCommands as $command) {
		foreach ($toolbar as $tool) {
			if ($tool['command'] == $command)
				$tools[] = $tool;
		}
	}

	$imageTools = array();
	$imageTools = explode(',', $config['thumbnail.image_tools']);

	$information = array();
	$information = explode(',', $config['thumbnail.information']);

	$data['js'] = getRequestParam("js", "");
	$data['formname'] = getRequestParam("formname", "");
	$data['elementnames'] = getRequestParam("elementnames", "");
	$data['disabled_tools'] = $config['general.disabled_tools'];
	$data['image_tools'] = $imageTools;
	$data['toolbar'] = $tools;
	$data['full_path'] = $path;
	$data['errorMsg'] = $errorMsg;
	$data['selectedPath'] = $selectedPath;
	$data['dirlist'] = $dirList;
	$data['anchor'] = $anchor;
	$data['exif_support'] = exifExists();
	$data['gd_support'] = $isGD;
	$data['edit_enabled'] = checkBool($config["thumbnail.gd.enabled"]);
	$data['demo'] = checkBool($config["general.demo"]);
	$data['demo_msg'] = $config["general.demo_msg"];
	$data['information'] = $information;
	$data['extension_image'] = checkBool($config["thumbnail.extension_image"]);
	$data['insert'] = checkBool($config["thumbnail.insert"]);
	$data['filemanager_urlprefix'] = removeTrailingSlash($config["filemanager.urlprefix"]);
	$data['thumbnail_width'] = $config['thumbnail.width'];
	$data['thumbnail_height'] = $config['thumbnail.height'];
	$data['thumbnail_border_style'] = $config['thumbnail.border_style'];
	$data['thumbnail_margin_around'] = $config['thumbnail.margin_around'];

	renderPage("images.tpl.php", $data);
?>
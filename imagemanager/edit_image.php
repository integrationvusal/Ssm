<?php
/**
 * upload.php
 *
 * @package MCImageManager.pages
 * @author Moxiecode
 * @copyright Copyright © 2005, Moxiecode Systems AB, All rights reserved.
 */

	require_once("includes/general.php");

	$data = array();

	verifyAccess($mcImageManagerConfig);

	$path = getRequestParam("path", "");
	$orgpath = getRequestParam("orgpath", "");
	$action = getRequestParam("action", "");
	$status = getRequestParam("status", "");

	$rootpath = toUnixPath(getRealPath($mcImageManagerConfig, 'filesystem.rootpath'));
	$fileFactory =& new FileFactory($mcImageManagerConfig, $rootpath);
	$targetFile =& $fileFactory->getFile($path);
	$config = $targetFile->getConfig();
	$wwwroot = removeTrailingSlash(toUnixPath(getWWWRoot($config)));

	$tools = explode(',', $config['thumbnail.image_tools']);
	if (!in_array("edit", $tools))
		die("The thumbnail.image_tools needs to include edit.");

	if ($targetFile->isDirectory())
		die("You may not edit directories.");

	$dir = $targetFile->getParentFile();
	if (!$dir->canWrite())
		die("You don't have write access to the directory.");

	// Delete old files
	$files = $dir->listFiles();
	foreach ($files as $file) {
		if (strpos($file->getName(), "mcic_") === 0 && time() - $file->lastModified() > 3600)
			$file->delete();
	}

	$urlprefix = removeTrailingSlash(toUnixPath($config['preview.urlprefix']));
	$urlsuffix = toUnixPath($config['preview.urlsuffix']);

	$pos = strpos($path, $wwwroot);
	if ($pos !== false && $pos == 0)
		$data['src'] = $urlprefix . substr($path, strlen($wwwroot)) . $urlsuffix;
	else
		$data['src'] = "";

	if (!$orgpath)
		$orgpath = $path;

	$data['src'] .= "?rnd=" . time();

	$data['orgpath'] = $orgpath;
	$data['action'] = $action;
	$data['status'] = $status;
	$data['path'] = $path;
	$data['demo'] = checkBool($config['general.demo']) ? "true" : "false";
	$data['demo_msg'] = $config['general.demo_msg'];

	// Render output
	renderPage("edit_image.tpl.php", $data);
?>

<?php
/**
 * general.php
 *
 * @package MCFileManager.includes
 * @author Moxiecode
 * @copyright Copyright © 2005, Moxiecode Systems AB, All rights reserved.
 */

// * * Get site root absolute path
global $rootPath, $mcLanguage;
$rootPath = dirname(dirname(__FILE__));
$mcLanguage = array();

session_start();
//error_reporting(E_ALL);
error_reporting(0);

require_once(getAbsPath("../classes/Savant2.php"));
require_once(getAbsPath("../classes/Utils/LanguageReader.php"));
require_once(getAbsPath("../file_types.php"));

require_once(getAbsPath("../classes/FileSystems/FileFactory.php"));
require_once(getAbsPath("../classes/Authenticators/BaseAuthenticator.php"));

require_once(getAbsPath("default_config.php"));
require_once(getAbsPath("../config.php"));

// Include LocalFileSystem
if (isset($mcImageManagerConfig['filesystem']) && $mcImageManagerConfig['filesystem'] == "LocalFileImpl")
	require_once(getAbsPath("../classes/FileSystems/LocalFileImpl.php"));

// Include authenticator
if (isset($mcImageManagerConfig['authenticator']) && $mcImageManagerConfig['authenticator'] == "SessionAuthenticatorImpl")
	require_once(getAbsPath("../classes/Authenticators/SessionAuthenticatorImpl.php"));

/**
 * Redirects to the login page if the user isn't logged in.
 *
 * @param Array $config Name/Value array of config items.
 */
function verifyAccess(&$config) {
	$path = resolvePath($config["filesystem.path"]);
	$rootpath = resolvePath($config["filesystem.rootpath"]);
	$auth = null;

	// Execute authenicator
	if (isset($config['authenticator'])) {
		$auth = new $config['authenticator']();
		$auth->init($config);
	}
	
	// Restore rootpath if authenticator tries to get out of it
	if (!isChildPath($rootpath, resolvePath($config["filesystem.rootpath"])))
		$config["filesystem.rootpath"] = $rootpath;

	// Restore path if authenticator tries to get out of it
	if (!isChildPath($rootpath, resolvePath($config["filesystem.path"])))
		$config["filesystem.path"] = $path;

	// Setup new paths to check agains or restore to
	$rootpath = resolvePath($config["filesystem.rootpath"]);
	$path = resolvePath($config["filesystem.path"]);

	// Override root path with JS, must be inside authenicator rootpath
	if (getRequestParam("initial_rootpath")) {
		$_SESSION["mc_javascript_rootpath"] = getRequestParam("initial_rootpath");
		unset($_SESSION['dropdown']);
	}

	// Use session rootpath, if it's inside authenticator rootpath
	if (isset($_SESSION["mc_javascript_rootpath"]) && $_SESSION["mc_javascript_rootpath"] != "mce_clear") {
		if (isChildPath($rootpath, resolvePath($_SESSION["mc_javascript_rootpath"]))) {
			$config["filesystem.rootpath"] = resolvePath($_SESSION["mc_javascript_rootpath"]);
			$rootpath = $config["filesystem.rootpath"];

			if (!file_exists($rootpath))
				die("The specified root path \"" . $_SESSION["mc_javascript_rootpath"] . "\" could not be found, the path was resolved to \"" . $rootpath . "\".");
		} else
			die("The specified root path \"" . $_SESSION["mc_javascript_rootpath"] . "\" is invalid, the path was resolved to \"" . resolvePath($_SESSION["mc_javascript_rootpath"]) . "\".");
	}

	// Override path with JS, must be inside authenicator rootpath
	if (getRequestParam("initial_path"))
		$_SESSION["mc_javascript_path"] = getRequestParam("initial_path");

	// Use session path, if it's inside authenticator rootpath
	if (isset($_SESSION["mc_javascript_path"]) && $_SESSION["mc_javascript_path"] != "mce_clear") {
		if (isChildPath($rootpath, resolvePath($_SESSION["mc_javascript_path"]))) {
			$config["filesystem.path"] = resolvePath($_SESSION["mc_javascript_path"]);
			$path = $config["filesystem.path"];
		} else
			$config["filesystem.path"] = $path;
	}

	// If path is outside, place it within rootpath
	if (!isChildPath($rootpath, $path))
		$config["filesystem.path"] = $rootpath;

	// Could not find path, use rootpath instead
	if (!file_exists($path))
		$config["filesystem.path"] = $rootpath;

	if ($auth != null && !$auth->isLoggedIn()) {
		header("Location: " . $config['general.login_page'] . "?" . $_SERVER['QUERY_STRING']);
		die;
	}

	loadLanguagePack($config);

	//echo $config["filesystem.path"];
}

/**
 * Returns a config parameter by name of the default value if it wasn't found.
 *
 * @param $name Name of param to retrive.
 * @return Config value.
 */
function getConfigParam(&$config, $name, $default_value = false) {
	if (!isset($name))
		return $default_value;

	return $config[$name];
}

/**
 * Redirects to the login page if the user isn't logged in.
 *
 * @param Array $config Name/Value array of config items.
 */
function addFileEventListeners(&$file_factory) {
	global $mcImageManagerConfig;

	// Include file listeners
	if (isset($mcImageManagerConfig['filesystem.file_event_listeners'])) {
		 $listenerNames = explode(",", $mcImageManagerConfig['filesystem.file_event_listeners']);

		 foreach ($listenerNames as $listenerName) {
			if ($listenerName != "") {
				$listener = new $listenerName();

				$listener->init($mcImageManagerConfig);

				$file_factory->addFileEventListener($listener);
			}
		 }
	}
}

/**
 * Adds no cache headers to HTTP response.
 */
function addNoCacheHeaders() {
	// Date in the past
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

	// always modified
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

	// HTTP/1.1
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);

	// HTTP/1.0
	header("Pragma: no-cache");
}

/**
 * Returns a absolute path from a virtual path.
 *
 * @param String $path Virtual path to map.
 * @return String Returns a absolute path from a virtual path.
 */
function getAbsPath($path) {
	global $rootPath;

	if (substr($path, 0, 1) == "/")
		return toOSPath($rootPath . $path);

	return toOSPath(dirname(__FILE__) . "/" . $path);
}

/**
 * Returns an request value by name without magic quoting.
 *
 * @param String $name Name of parameter to get.
 * @param String $default_value Default value to return if value not found.
 * @return String request value by name without magic quoting or default value.
 */
function getRequestParam($name, $default_value = false) {
	if (!isset($_REQUEST[$name]))
		return $default_value;

	if (!isset($_GLOBALS['magic_quotes_gpc']))
		$_GLOBALS['magic_quotes_gpc'] = ini_get("magic_quotes_gpc");

	if (isset($_GLOBALS['magic_quotes_gpc'])) {
		if (is_array($_REQUEST[$name])) {
			$newarray = array();

			foreach($_REQUEST[$name] as $name => $value)
				$newarray[stripslashes($name)] = stripslashes($value);

			return $newarray;
		}
		return stripslashes($_REQUEST[$name]);
	}

	return $_REQUEST[$name];
}

/**
 * Loads and initializes the mcLanguage array based on config.
 *
 * @param Array $config Name/Value config array.
 */
function loadLanguagePack($config) {
	global $mcImageManagerConfig, $mcLanguage;

	$language = $config['general.language'];
	$languageDefault = "en";

	$langReader = new LanguageReader();
	$langReader->loadXML(realpath("langs/". $language .".xml"));

	$foreignLanguage = $langReader->_items;

	// Load default language and merge arrays
	if ($language != $languageDefault) {
		$defaultLangReader = new LanguageReader();
		$defaultLangReader->loadXML(realpath("langs/". $languageDefault .".xml"));
		$defaultLanguage = $defaultLangReader->_items;

		// Merge arrays
		foreach ($foreignLanguage as $ftarget => $fvalue) {
			foreach($fvalue as $iname => $ivalue)
				$defaultLanguage[$ftarget][$iname] = $ivalue;
		}

		$lang = isset($defaultLanguage[getScriptName()]) ? $defaultLanguage[getScriptName()] : array();
		$commonLang = $defaultLanguage["common"];
	} else {
		$lang = $foreignLanguage[getScriptName()];
		$commonLang = $foreignLanguage["common"];
	}

	$mcLanguage = array_merge($commonLang, $lang);
}

/**
 * Renders a page to output stream using smarty template and data.
 *
 * @param String $template Template filename to be used for rendering.
 * @param Array $data Array of data items to be appended to smarty.
 * @param Array $config Optional config array.
 */
function renderPage($template, $data, $config = -1) {
	global $mcImageManagerConfig, $rootPath, $mcLanguage;

	// Savant Integration
	$savant = new Savant2();

	// Assign smarty items
	$savant->assign('theme', $mcImageManagerConfig['general.theme']);
	$savant->assign('data', $data);
	$savant->assign('lang', $mcLanguage);
	$savant->addPath("template", "themes/" . $mcImageManagerConfig['general.theme']);

	// Display Data through Smarty
	$err = $savant->display($template);
	if ($savant->isError($err))
		echo "There was an error displaying the template:<br />[" . $err->code . "] " . $err->text . ".<br />";

	die;
}

/**
 * Removes illegal characters from a filename and returns the cleaned one.
 *
 * @param String $filename Name of file to check
 * @return String a filename containing only allowed characters
 */
function cleanFilename($filename) {
	$charLookup = array(
		"å" => "a", 
		"ä" => "a", 
		"ö" => "o",
		"Å" => "a", 
		"Ä" => "a", 
		"Ö" => "o",
		" " => "_"
	);

	$filename = strtolower($filename);
	$filename = strtr($filename, $charLookup);
	$strlen = strlen($filename);

	for ($i=0;$i<=$strlen;$i++) {
		$chr = substr($filename, $i, 1);
		$ord = ord($chr);

		if ( ( ($ord >= ord('0')) AND ($ord <= ord('9')) ) OR ( ($ord >= ord('a')) AND ($ord <= ord('z')) ) OR (ord('_') == $ord ) )
			$outstr .= $chr;
	}

	return $outstr;
}

/**
 * Checks for an already existing file with the same name, and
 * renames the active file to a unique name if one is found.
 *
 * @param String $path Path of file
 * @param String $filename Name of file
 * @return String A unique filename.
 */
function getUniqueFilename($path, $filename) {
	if (file_exists($path . "/" . $filename)) {
		$ar = explode('.', $filename);
		$fileext  = array_pop($ar);
		$basename = basename($filename, '.'.$fileext);
		$instance = 2;

		while(file_exists($path . "/" . $basename . "_" . $instance . "." . $fileext))
			$instance++;

		return $basename . "_" . $instance . "." . $fileext;
	}

	return $filename;
}

/**
 * Returns a filesize as a nice truncated string like "10.3 MB".
 *
 * @param int $size File size to convert.
 * @return String Nice truncated string of the file size.
 */
function getSizeStr($size) {
	// MB
	if ($size > 1048576)
		return round($size / 1048576, 1) . " MB";

	// KB
	if ($size > 1024)
		return round($size / 1024, 1) . " KB";

	return $size . " bytes";
}

/**
 * Returns the file type of a file.
 *
 * @param String file name
 * @return Array Array with file type info.
 */
function getFileType($file_name) {
	global $mcImageManagerFileTypes;

	$ar = explode('.', $file_name);
	$ext = strtolower(array_pop($ar));

	// Search for extention
	foreach ($mcImageManagerFileTypes as $type) {
		foreach ($type[0] as $targetExt) {
			if ($ext == strtolower($targetExt))
				return array("icon" => $type[1], "type" => $type[2], "preview" => $type[3], "ext" => $ext);
		}
	}

	// Not in list
	return array("icon" => "unknown.gif", "type" => "Normal file", "preview" => false, "ext" => $ext);
}

/**
 * Removes the trailing slash from a path.
 *
 * @param String path Path to remove trailing slash from.
 * @return String New path without trailing slash.
 */
function removeTrailingSlash($path) {
	// Is root
	if ($path == "/")
		return $path;

	if ($path == "")
		return $path;

	if ($path[strlen($path)-1] == '/')
		$path = substr($path, 0, strlen($path)-1);

	return $path;
}

/**
 * Adds a trailing slash to a path.
 *
 * @param String path Path to add trailing slash on.
 * @return String New path with trailing slash.
 */
function addTrailingSlash($path) {
	if ($path[strlen($path)-1] != '/')
		$path .= '/';

	return $path;
}

/**
 * Returns the user path, the path that the users sees.
 *
 * @param String $path Absolute file path.
 * @return String Visual path, user friendly path.
 */
function getUserFriendlyPath($path, $max_len = -1) {
	global $mcImageManagerConfig;

	if (checkBool($mcImageManagerConfig['general.user_friendly_paths'])) {
		$path = substr($path, strlen(removeTrailingSlash(getRealPath($mcImageManagerConfig, 'filesystem.rootpath'))));

		if ($path == "")
			$path = "/";
	}

	if ($max_len != -1 && strlen($path) > $max_len)
		$path = "... " . substr($path, strlen($path)-$max_len);

	// Add slash in front
	if (strlen($path) > 0 && $path[0] != '/')
		$path = "/" . $path;

	return $path;
}

/**
 * Check if a value is true/false.
 *
 * @param string $str True/False value.
 * @return bool true/false
 */
function checkBool($str) {
	if ($str === true)
		return true;

	if ($str === false)
		return false;

	$str = strtolower($str);

	if ($str == "true")
		return true;

	return false;
}

/**
 * Converts a Unix path to OS specific path.
 *
 * @param String $path Unix path to convert.
 */
function toOSPath($path) {
	return str_replace("/", DIRECTORY_SEPARATOR, $path);
}

/**
 * Converts a OS specific path to Unix path.
 *
 * @param String $path OS path to convert to Unix style.
 */
function toUnixPath($path) {
	return str_replace(DIRECTORY_SEPARATOR, "/", $path);
}

/**
 * Returns the absolute path of a config key or die on failure.
 *
 * @param String $config Configuration name/value array.
 * @param String $key Path key to retrive.
 */
function getRealPath($config, $key) {
	$realPath = realpath($config[$key]);

	if ($realPath == "")
		die("The configurated path \"" . $config[$key] . "\" in key \"" . $key . "\" could not be found.");

	return $realPath;
}

/**
 * Converts a OS specific path to Unix path.
 *
 * @param Int $width Width of image.
 * @param Int $height Height of image.
 * @param Int $target Target to scale for.
 * @return Array $imageinfo Array with width, height and scale info.
 */
function imageResize($width, $height, $target) {
	$percentage = 0;
	$imageinfo = array();

	if ($width > $height)
		$percentage = ($target / $width);
	else
		$percentage = ($target / $height);

	if ($percentage <= 1) {
		$width = round($width * $percentage);
		$height = round($height * $percentage);
	} else {
		$percentage = 0;
	}
	//echo "P" . $percentage . " - W" . $width . " - H" . $height ." - T". $target ."\n";
	$imageinfo['width'] = $width;
	$imageinfo['height'] = $height;
	$imageinfo['scale'] = round($percentage, 2)*100;

	return $imageinfo;
} 

/**
 * Returns the wwwroot or null string if it was impossible to get.
 *
 * @return String wwwroot or null string if it was impossible to get.
 */
function getWWWRoot($config) {
	if (isset($config['preview.wwwroot']) && $config['preview.wwwroot'])
		return getRealPath($config, 'preview.wwwroot');
	
	// Check document root
	if (isset($_SERVER['DOCUMENT_ROOT'])) {
		if (ini_get("magic_quotes_gpc"))
			return realpath(stripslashes($_SERVER['DOCUMENT_ROOT']));
		else
			return realpath($_SERVER['DOCUMENT_ROOT']);
	}

	// Try script file
	if (isset($_SERVER["SCRIPT_NAME"]) && isset($_SERVER["SCRIPT_FILENAME"])) {
		$path = str_replace(toUnixPath($_SERVER["SCRIPT_NAME"]), "", toUnixPath($_SERVER["SCRIPT_FILENAME"]));
		if (is_dir($path))
			return toOSPath($path);
	}

	// If all else fails, try this.
	if (isset($_SERVER["SCRIPT_NAME"]) && isset($_SERVER["PATH_TRANSLATED"])) {
		$path = str_replace(toUnixPath($_SERVER["SCRIPT_NAME"]), "", str_replace("//", "/", toUnixPath($_SERVER["PATH_TRANSLATED"])));
		if (is_dir($path))
			return toOSPath($path);
	}

	die("Could not resolve WWWROOT path, please set an absolute path in preview.wwwroot config option.");
	return null;
}

/**
 * Check for the GD functions that are beeing used.
 * @return Bool true or false depending on success or not.
 */
function gdExists() {
	// just make a quick check, we dont need to loop if we can't find GD at all.
	if (!function_exists("gd_info"))
		return false;

	$gdUsedFunctions = array();
	$gdUsedFunctions[] = "ImagecreateFromJpeg";
	$gdUsedFunctions[] = "ImagecreateFromPng";
	$gdUsedFunctions[] = "ImagecreateFromGif";
	$gdUsedFunctions[] = "ImageJpeg";
	$gdUsedFunctions[] = "ImagePng";
	$gdUsedFunctions[] = "ImageGif";
	$gdUsedFunctions[] = "ImageCopyResized";
	$gdUsedFunctions[] = "ImageCreateTrueColor";
	$gdUsedFunctions[] = "ImageSX";
	$gdUsedFunctions[] = "ImageSY";

	foreach($gdUsedFunctions as $function) {
		if (!function_exists($function))
			return false;
	}

	return true;
}

/**
 * Check for the EXIF functions that are beeing used.
 * @return Bool true or false depending on success or not.
 */
function exifExists() {
	if (!function_exists("exif_thumbnail"))
		return false;

	return true;
}

/**
 * Returns what GD functions to use on on this type of file
 * @param String $type The extension/type of the file.
 * @return Array An array with "imagefunction", "createfunction" and "quality"
 */
function getFunctionsByType($type) {
	
	$functions = array();

	switch (strtolower($type)) {
		case "jpeg":
		case "jpg":
				$functions['createfunction'] = "ImagecreateFromJpeg";
				$functions['imagefunction'] = "ImageJpeg";
				$functions['quality'] = true;
			break;
		case "png":
				$functions['createfunction'] = "ImagecreateFromPng";
				$functions['imagefunction'] = "ImagePng";
				$functions['quality'] = false;
			break;
		case "gif":
				$functions['createfunction'] = "ImagecreateFromGif";
				$functions['imagefunction'] = "ImageGif";
				$functions['quality'] = false;
			break;
		default:
				return false;
			break;
	}
	return $functions;
}

/**
 * Generates a thumbnail as the target file, from the source file
 * @param String $source Absolute source path.
 * @param String $target Absolute source target.
 * @param Int $height Height of image.
 * @param Int $width Width of the image.
 * @param String $ext Extension of the image.
 * @return Bool true or false depending on success or not.
 */
function createThumbnail($source, $target, $width, $height, $ext, $quality) {
	if (!gdExists())
		return false;

	$functions = getFunctionsByType($ext);

	$image = $functions['createfunction']($source);
	$thumbnail = ImageCreateTrueColor($width, $height);
	ImageCopyResized($thumbnail, $image, 0, 0, 0, 0, $width, $height, ImageSX($image), ImageSY($image));

	if ($functions['quality'])
		return $functions['imagefunction']($thumbnail, $target, $quality);
	else
		return $functions['imagefunction']($thumbnail, $target);
}

/**
 * Resolves relative path to absolute path. THe output path is in unix format.
 */
function resolvePath($path) {
	return realpath($path);
}

/**
 * Verifies that a path is within the parent path.
 */
function isChildPath($parent_path, $path) {
	//echo $parent_path ." - " . $path . (strpos(strtolower($path), strtolower($parent_path)) === 0 ? "TRUE" : "") . "<br />";
	return strpos(strtolower($path), strtolower($parent_path)) === 0;
}

/**
 * Returns the script name.
 *
 * @return String script name.
 */
function getScriptName() {

	$arrayShifter = "";

	if (isset($_SERVER["SCRIPT_NAME"])) {
		$arrayShifter = explode(".", basename($_SERVER["SCRIPT_NAME"]));
		return array_shift($arrayShifter);
	}

	if (isset($_SERVER["PHP_SELF"]))
		$arrayShifter = explode(".", basename($_SERVER["PHP_SELF"]));
		return array_shift($arrayShifter);
}

?>
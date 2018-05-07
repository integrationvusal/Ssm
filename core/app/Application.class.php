<?php

	abstract class Application {
		
		public static $request;
		public static $fileRoot;
		public static $settings;
		public static $dbSettings;
		public static $urlPatterns;
		public static $modules;
		public static $adminName;
		public static $storage;
		
		public static $messages;
		
		private static $urlFound = false;
		
		public static function start() {
			self::initialize();
		}
		
		public static function stop() {
			BaseModel::disconnect();
			die();
		}
		
		public static function getCSRFKey() {
			return Security::genereateCSRFKey();
		}
		
		private static function initialize() {
		Global $__app;
		Global $__modules;
		Global $__urlPatterns;
		Global $_adminName;
		Global $__db;
		
			self::$settings = $__app;
			self::$dbSettings = $__db;
			self::$modules = $__modules;
			self::$urlPatterns = $__urlPatterns;
			self::$adminName = $_adminName;
			self::$fileRoot = new File(app_root);
			self::$request = new Request();
			
			BaseModel::connect();
			Controller::initialize();
				
			self::manageSession();
			
			self::defender();
			
			if (self::$settings['url_converter_enabled']) self::urlConverter();
			

			self::serviceUrlPatterns();
		}
		
		private static function manageSession() {
			if (self::$settings['use_session']) session_start();
		}
		
		private static function urlConverter() {
			$urlsToConvert = UrlConverter::all();
			foreach ($urlsToConvert as $url) {
				if (preg_match('/^' . $url->urlIn->value . '$/im', Controller::$request)) self::toPattern(self::$urlPatterns['app'][$url->patternIndex->value]);
				
			}
		}
		
		private static function toPattern($url) {
			$vars = isset($url[4]) ? $url[4] : Array();
			$middleWareCount = count(self::$settings["middleware_list"]);
			for ($i = 0; $i < $middleWareCount; $i++) {
				$m = self::$settings["middleware_list"][$i];
				$path = (isset($m[2]) && !empty($m[2])) ? ds . $m[2] : "";
				require_once self::$settings["middleware_folder"] . $path . ds . $m[0] . self::$settings["middleware_ext"];
				$m[0]::$m[1](self::$request, $vars);
			}
			$path = (isset($url[3]) && !empty($url[3])) ? ds . $url[3] : "";
			require_once self::$settings["controllers_folder"] . $path . ds . $url[1] . self::$settings["controllers_ext"];
			if (method_exists($url[1], "before")) {
				$url[1]::before(self::$request, $vars);
			}
			$url[1]::$url[2](self::$request, $vars);
			
			self::$urlFound = true;
			die();
		}
		
		private static function serviceModulesUrls() {
			$modules = array_keys(self::$modules);
			$mCount = count($modules);
			for ($j = 0; $j < $mCount; $j++) {
				// count module url patterns
				$urlPatternsCount = count(self::$urlPatterns[$modules[$j]]);
				for ($i = 0; $i < $urlPatternsCount; $i++) {
					$url = self::$urlPatterns[$modules[$j]][$i];
					$patterns = self::correctUrlPattern($url[0]);
					foreach ($patterns as $p) {
						$defVars = isset($url[4]) ? $url[4] : Array();
						if (preg_match_all($p, Controller::$request, $vars, PREG_SET_ORDER)) {
							$vars[0] = array_merge($defVars, $vars[0]);
							$middleWareCount = count(self::$modules[$modules[$j]]["middleware_list"]);
							for ($i = 0; $i < $middleWareCount; $i++) {
								$m = self::$modules[$modules[$j]]["middleware_list"][$i];
								$path = (isset($m[2]) && !empty($m[2])) ? ds . $m[2] : "";
								require_once self::$modules[$modules[$j]]["middleware_folder"] . $path . ds . $m[0] . self::$settings["middleware_ext"];
								$m[0]::$m[1](self::$request, $vars[0]);
							}
							$path = (isset($url[3]) && !empty($url[3])) ? ds . $url[3] : "";
							require_once self::$modules[$modules[$j]]["controllers_folder"] . $path . ds . $url[1] . self::$settings["controllers_ext"];
							
							if (method_exists($url[1], "before")) {
								$url[1]::before(self::$request, $vars[0]);
							}
							$url[1]::$url[2](self::$request, $vars[0]);
							self::$urlFound = true;
							break;
						}
					}
					if (self::$urlFound) break;
				}
				if (self::$urlFound) break;
			}
		}
		
		private static function startController() {
				// count module url patterns
				$urlPatternsCount = count(self::$urlPatterns['app']);
				for ($i = 0; $i < $urlPatternsCount; $i++) {
					$url = self::$urlPatterns['app'][$i];
					$patterns = self::correctUrlPattern($url[0]);
					foreach ($patterns as $p) {
						$defVars = isset($url[4]) ? $url[4] : Array();
						if (preg_match_all($p, Controller::$request, $vars, PREG_SET_ORDER)) { 
							$vars[0] = array_merge($defVars, $vars[0]);
							$middleWareCount = count(self::$settings["middleware_list"]);
							for ($i = 0; $i < $middleWareCount; $i++) {
								$m = self::$settings["middleware_list"][$i];
								$path = (isset($m[2]) && !empty($m[2])) ? ds . $m[2] : "";
								require_once self::$settings["middleware_folder"] . $path . ds . $m[0] . self::$settings["middleware_ext"];
								$m[0]::$m[1](self::$request, $vars[0]);
							}
							$path = (isset($url[3]) && !empty($url[3])) ? ds . $url[3] : "";
							require_once self::$settings["controllers_folder"] . $path . ds . $url[1] . self::$settings["controllers_ext"];
							if (method_exists($url[1], "before")) {
								$url[1]::before(self::$request, $vars[0]);
							}
							$url[1]::$url[2](self::$request, $vars[0]);
							
							self::$urlFound = true;
							break;
						}
					}
					if (self::$urlFound) break;
				}
		}
		
		private static function correctUrlPattern($pattern) {
			$patterns = explode('|', $pattern);
			$c = count($patterns);
			$out = Array();
			for ($i = $c; $i > 0; $i--) {
				$o = '/^';
				for ($j = 0; $j < $i; $j++) $o .= $patterns[$j];
				$o .= '$/im';
				$out[] = $o;
			}
			return $out;
		}
		
		private static function serviceUrlPatterns() {
			self::mergeUrlPatterns();
			
			self::serviceModulesUrls();
			
			if (!self::$urlFound) self::startController();
		}
		
		private static function mergeUrlPatterns() {
			$count = count(self::$modules);
			$modules = array_keys(self::$modules);
			for ($j = 0; $j < $count; $j++) {
				require_once self::$modules[$modules[$j]]["folder"] . ds . "urls.php";
			}
		}
		
		private static function defender() {
			self::checkSQLInjection();
		}
	
		private static function checkSQLInjection() {
			// check for quotes
			if (Security::filterSql(Controller::$request, BaseModel::$mysqli) != Controller::$request) {
				Security::sendMail();
			}
		}
		
	}

?>
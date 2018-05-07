<?php

	class StaticController extends Controller {
		
		public static function getStatic($request, $vars = Array()) {
			
			$fileType = $vars['file_type'];
			if (!in_array($fileType, Application::$settings['static_files_ext'])) Application::pageNotFound();
			$moduleName = isset($vars['module_name']) ? $vars['module_name'] : '';
			
			$folder = '';
			$files = '';
			$out = '';
			
			if (!empty($moduleName)) {
				if (!in_array($moduleName, array_keys(Application::$modules))) Application::pageNotFound();
				$files = Application::$modules[$moduleName]['static_files'];
				$folder = Application::$modules[$moduleName]['static_folder'] . ds . 'dark_theme';
			} else {
				$files = Application::$settings['static_files'];
				$folder = Application::$settings['static_folder'];
			}
			
			$c = count($files);
			for ($i = 0; $i < $c; $i++) {
				$out .= "; \n" . file_get_contents($folder . ds . $fileType . ds . $files[$i]);
			}
			
			echo $out;
			
		}
		
	}

?>
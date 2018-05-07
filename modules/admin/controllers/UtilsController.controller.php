<?php
	
	class UtilsController extends Controller {
		
		public static function view($request, $vars = Array()) {
			self::renderTemplate('utils' . ds . 'get-link.tpl', Array(
				'pages' => MenuModel::getAllRecords('az'),
				'app_url' => Application::$settings['url']
			));
		}
		
	}
	
?>
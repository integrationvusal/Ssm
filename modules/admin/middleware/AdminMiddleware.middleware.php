<?php
	
    class AdminMiddleware extends Controller {
        public static function initializeAdmin() {
        Global $_adminName;
            BaseModel::connect();
			
			Application::$messages = Controller::parseLangFile('az', true, '', 'admin');
			self::$smarty->assign('messages', Application::$messages);
			
            self::$smarty->setTemplateDir(Application::$modules['admin']['folder'] . ds . 'views');
			self::$smarty->caching = 0;
            self::$smarty->assign('theme_folder', 'dark_theme');
			self::$smarty->assign('static_url', Application::$modules['admin']['static_url']);
			self::$smarty->assign('public_url', Application::$settings['public_url']);
			self::$smarty->assign('public_folder', Application::$settings['public_folder']);
            self::$smarty->assign('app_url', Application::$settings['url']);
            self::$smarty->assign('admin_title', $_adminName);
        }
		
		public static function checkLoggedIn($request, $vars = Array()) {
			self::authorized($request, $vars);
			AdminAuthController::checkLoggedIn($request, $vars);
		}
		
		public static function authorized($request, $vars = Array()) {
			self::$smarty->assign('user_info', AdminAuthController::getUserInfo());
			self::$smarty->assign('users_model_index', 4);
		}
		
    }

?>

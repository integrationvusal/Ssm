<?php

class AdminMainApp extends Controller {
    
	
	public static function before($request, $vars = Array()) {
		if (isset($vars['model_id'])) {
			$data = SessionStorage::get('admin');
			if (!(isset($data['allowed_models']) && in_array($vars['model_id'], $data['allowed_models']))) {
				self::pageNotFound();
				Application::stop();
			}
		}
	}
	
	public static function pageNotFound() {
		echo '404';
	}
	
    public static function main($request, $vars = Array()) {
		$ordersCount = OrdersModel::getOrdersCountByStatus(1);
		$topPanelIcons = Array();
		/*
		// oders smart icon
		$topPanelIcons[] = self::renderTemplate('orders' . ds . 'top-panel-icon.tpl',Array(
			'ordersCount' => $ordersCount
		),true);
		*/
		
		self::$smarty->assign('user_info', AdminAuthController::getUserInfo());
		self::$smarty->assign('users_model_index', 4);
        
		self::renderTemplate('base.tpl', Array(
            'main_model_icons' => self::getModelIcons(),
			'user' => self::getUserInfo(),
			'top_panel_icons' => join("",$topPanelIcons)
        ));
    }
	
	private static function getUserInfo() {
		$userId = intval($_SESSION['admin']['user_id']);
		$user = AdminUsersModel::get($userId);
		return $user;
	}

    public static function view($request, $vars = Array()) {
        $model_id = $vars['model_id'];
		$model = Application::$modules['admin']['models'][$model_id];
		$model::initialize();
		$out = $model::view($request, self::$smarty, $vars);
		$tplFile = (isset($model::$tplViewFile)) ? $model::$tplViewFile : 'model' . ds . 'model-view.tpl';
		self::renderTemplate($tplFile, is_array($out) ? $out : Array());
    }
	
	public static function edit($request, $vars = Array()) {
		$modelId = $vars['model_id'];
		$model = Application::$modules[Application::$adminName]['models'][$modelId];
		$result = $model::edit($request, self::$smarty, $vars);
		$result['data']['url'] = Application::$settings['url'] . '/' . Controller::$request;
		self::renderTemplate($result['tpl'], $result['data']);
	}
	
	public static function add($request, $vars = Array()) {
		$modelId = $vars['model_id'];
		$model = Application::$modules[Application::$adminName]['models'][$modelId];
		$result = $model::add($request, self::$smarty, $vars);
		$result['data']['url'] = Application::$settings['url'] . '/' . Controller::$request;
		self::renderTemplate($result['tpl'], $result['data']);
	}
	
	public static function delete($request, $vars = Array()) {
		if (isset($_POST['delete_id'])) {
			$modelId = $vars['model_id'];
			$model = Application::$modules[Application::$adminName]['models'][$modelId];
			$model::deleteItem($request, self::$smarty, $vars);
		}
		
	}
	
    private static function getModelIcons() {
    Global $__modules;
        $adminModels = $__modules['admin']['models'];
        $c = count($adminModels);
        $modelIcons = Array();
        for ($i = 0 ; $i < $c; $i++) {
			if (!in_array($i, $_SESSION['admin']['allowed_models'])) continue;
			$adminModels[$i]::initialize();
            self::$smarty->assign('model_main_title', $adminModels[$i]::$title);
            self::$smarty->assign('model_main_icon', $adminModels[$i]::$iconPath);
            self::$smarty->assign('model_main_id', $i);
			self::$smarty->assign('model_use_own_view_url', $adminModels[$i]::$useOwnViewUrl);
			self::$smarty->assign('model_own_view_url', $adminModels[$i]::$ownViewUrl);
            $modelIcons[] = self::$smarty->fetch('model' . ds . 'model-shortcut.tpl');
        }
        return $modelIcons;
    }

}

?>

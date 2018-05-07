<?php

    class Middleware extends Controller {
		
		public static function initializeApp($request, $vars = Array()) {
			self::$smarty->assign('user', SessionStorage::get('user'));
			self::$smarty->assign('subject', SessionStorage::get('subject'));
		}
		
        /* for assigning vars for template */
        public static function forSmarty($request, $vars = Array()) {
            self::$smarty->assign('static_url', Application::$settings['static_url']);
			self::$smarty->assign('app_url', Application::$settings['url']);
			self::$smarty->assign('public_url', Application::$settings['public_url']);
			self::$smarty->assign('request_url', Application::$settings['url'] . '/' . Controller::$request);
			self::$smarty->assign('session_name', session_name());
			self::$smarty->assign('session_id', session_id());
			$param = false;
			if($user = SessionStorage::get('user')) {
				$param = ['user_id' => $user['id'], 'operator_id' => $user['operator']['id']];
			}
			$permissions = PermissionController::getPermissions($param);
			$sell_only = true;
			if(is_array($permissions)){
                foreach ($permissions as $key => $permission){
                    if($permission == 1 && !in_array($key, ['change_subject', 'sell_read'])){
                        $sell_only = false;
                    }
                }
            }
            if(session_status() == PHP_SESSION_NONE) {
			    session_start();
            }
            $_SESSION['sell_only'] = $sell_only;
            self::$smarty->assign('sell_only', $sell_only);
            self::$smarty->assign('permissions', PermissionController::getPermissions($param));
        }
		
		public static function getLangsUrl($request, $vars = Array()) {
			$currentLang = Application::$storage['lang'];
			$langUrls = Array();
			$languages = Application::$settings['languages'];
			$url = Controller::$request;
			$urlData = explode("/", $url);
			unset($urlData[0]);
			
			self::$smarty->assign('langUrl', join('/', $urlData));
			self::$smarty->assign('languages', $languages);
			self::$smarty->assign('currentLang', $currentLang);
			return;
		}
		
		public static function getMenu($request, $vars = Array()) {
			$curLang = Application::$storage['lang'];
			$menuTreeItems = MenuModel::getTreeItems();
			$menuModelItems = MenuModel::getAllRecords($curLang, 'treeItemId', true);
			$c = count($menuModelItems);
			foreach ($menuModelItems as $k => $mItem) {
				switch ($mItem->type->value) {
					case 1:
						$menuModelItems[$k]->url = Application::$settings['url'] . '/' . Application::$storage['lang'] . '/' . 'view-page/' . $mItem->r_id->value . '/' . urlencode($mItem->menuItemTitle->value);
						break;
					case 2:
						$model = Application::$modules[Application::$adminName]['models'][$mItem->modelId->value];
						$model::initialize();
						$menuModelItems[$k]->url = Application::$settings['url'] . '/' . Application::$storage['lang'] . '/' . $model::$viewUrl;
						break;
					case 3:
						$menuModelItems[$k]->url = $mItem->link->value;
						break;
					case 4:
						$menuModelItems[$k]->url = Application::$settings['url'] . '/' . Application::$storage['lang'] . '/' . 'view-page/' . $mItem->pageId->value;
						break;
				}
			}
			
			self::$smarty->assign('menuTreeItems', $menuTreeItems);
			self::$smarty->assign('menuModelItems', $menuModelItems);
		}
		
		private static function getCurrentLang($vars) {
			return (isset($vars['lang']) && in_array($vars['lang'], array_keys(Application::$settings['languages']))) ? $vars['lang'] : Application::$settings['default_language'];
		}
    }

?>
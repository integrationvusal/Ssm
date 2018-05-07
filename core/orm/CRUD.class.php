<?php
	class CRUD {
		// model view action
		public static function view($request, &$smarty, $vars = Array()) {
			$__modules = Application::$modules;
			$_adminName = Application::$adminName;
			$model_id = $vars['model_id'];
			$model = $__modules['admin']['models'][$model_id];
			
			$smarty->assign('model_id', $model_id);
			if ($request->isAjax() || 1) {
				$headers = Array();
				$m = new $model();
				$model::initialize();
				
				foreach ($model::$displayFields as $f) {
					$headers['title'][] = $m->$f->title;
				}
				$page = (isset($vars['page'])) ? $vars['page'] : 0;
				
				$content = $model::getDisplayInfo($page * $model::$perPage, $model::$perPage);
				$pagesCount = $content['count'];
				$smarty->assign('count', $content['count']);
				$smarty->assign('current_page', $page);
				$smarty->assign('table_headers', $headers);
				$smarty->assign('table_content', $content);
				
				$content = $smarty->fetch('model' . ds . 'model-data-list.tpl');
				$out = Array();
				$out['model_title'] = $model::$title;
				$out['model_content'] = $content;
				$out['model_id'] = $model_id;
				$out['admin_title'] = $_adminName;
				
				// pagination
				if ($pagesCount > 1) $out['paginator'] = $smarty->fetch('model' . ds . 'paginator.tpl');
				else $out['paginator'] = '';
				return $out;
			}
		}

		public static function edit($request, &$smarty, $vars = Array(), $saveItem = true, $model = false) {
			$modelItemId = $vars['model_item_id'];
			if ($model === false) {
				$modelId = $vars['model_id'];
				$model = Application::$modules[Application::$adminName]['models'][$modelId];
			}
			
			$model::initialize();
			
			$objects = Array();
			$result = Array();
			if (isset($_POST['saveItem']) && $saveItem) {
				$result = $model::saveItem(true, $modelItemId);
				if ($result['success'] && ($model::$searchable)) self::synchronizeDataInSearchModel($result['data'], $model, $modelId);
				
				if ($result['success']) {
					$recordId = ($model::$multiLang) ? $result['data'][0]->r_id->value : $result['data'][0]->id->value;
					self::addActionToHistory($recordId, $model, $modelId, 2);
				}
			}

			$fieldName = $model::$multiLang ? 'r_id' : 'id';
			$data = $model::find(" WHERE `".$fieldName."` = '{#1}'", Array($modelItemId));
			
			$whereCondition = "";

			$form = "";
			$c = count($data);
			if ($model::$multiLang) {
				$dataWithLangKey = Array();
				for ($i = 0; $i < $c; $i++) {
					$dataWithLangKey[$data[$i]->lang_id->value] = $data[$i];
				}
				$tabIndex = 0;
				
				foreach (Application::$settings['languages'] as $langIso => $langTitle) {
					$smarty->assign('tab_content', $model::getForm($smarty, $dataWithLangKey[$langIso]));
					$smarty->assign('tab_title', $langTitle);
					$smarty->assign('tab_index', $tabIndex);
					$form .= $smarty->fetch('model' . ds . 'tab-content.tpl');
					$tabIndex++;
				}
			} else {
				for ($i = 0; $i < $c; $i++) {
					$form .= $model::getForm($smarty, $data[$i]);
				}
			}
			
			$context = Array();
			$context['modelForm'] = $form;
			$context['multilang'] = $model::$multiLang;
			if (isset($result['success'])) {
				$context['success'] = $result['success'];
				$context['errors'] = json_encode($result['data']);
				$context['randId'] = rand(9999,99999);
			}
			
			return Array('tpl' => 'model'. ds .'action.tpl', 'data' => $context, 'result' => $result);
		}
		
		private static function addActionToHistory($recordId, $model, $modelId, $event) {
			$userInfo = SessionStorage::get('admin');
			$action = new UserActions();
			$action->userId->value = $userInfo['user_id'];
			$action->classId->value = $modelId;
			$action->recordId->value = $recordId;
			$action->multilang->value = $model::$multiLang;
			$action->action->value = $event;
			$action->time->value = time();
			$action->active->value = 1;
			$action->save();
			if ($event == 3) {
				UserActions::query("UPDATE `".UserActions::getTableName('UserActions')."` SET `active` = 0 WHERE `recordId` = '{#1}' AND `classId` = '{#2}'", Array($recordId, $modelId));
			}
		}
		
		public static function add($request, &$smarty, $vars = Array(), $saveItem = true, $model = false) {
			if (!$model) {
				$modelId = $vars['model_id'];
				$model = Application::$modules[Application::$adminName]['models'][$modelId];
			}
			
			$model::initialize();
			
			$errors = Array();
			$result = Array();
			if (isset($_POST['saveItem']) && $saveItem) {
				$result = $model::saveItem();
				if ($result['success'] && ($model::$searchable)) self::synchronizeDataInSearchModel($result['data'], $model, $modelId);
				
				// add record to user actions
				if ($result['success']) {
					$recordId = ($model::$multiLang) ? $result['data'][0]->r_id->value : $result['data'][0]->id->value;
					self::addActionToHistory($recordId, $model, $modelId, 1);
				}
			}
			
			$form = "";
			if (!$model::$multiLang) {
				$object = new $model();
				$form = $model::getForm($smarty, $object);
			} else {
				$tabIndex = 0;
				foreach (Application::$settings['languages'] as $langIso => $langTitle) {
					$object = new $model();
					$object->lang_id = new stdClass();
					$object->lang_id->value = $langIso;
					$smarty->assign('tab_content', $model::getForm($smarty, $object));
					$smarty->assign('tab_title', $langTitle);
					$smarty->assign('tab_index', $tabIndex);
					$form .= $smarty->fetch('model' . ds . 'tab-content.tpl');
					$tabIndex++;
				}
			}
			
			$context = Array();
			$context['modelForm'] = $form;
			$context['multilang'] = $model::$multiLang;
			if (isset($result['success'])) {
				$context['success'] = $result['success'];
				$context['errors'] = json_encode($result['data']);
				$context['randId'] = rand(9999,99999);
			}
			
			return Array('tpl' => 'model'. ds .'action.tpl', 'data' => $context, 'result' => $result);
		}
		
		public static function delete($request, &$smarty, $vars = Array(), $itemsToDelete = Array()) {
			$modelId = $vars['model_id'];
			$model = Application::$modules[Application::$adminName]['models'][$modelId];
			$model::initialize();
			$fieldName = $model::$multiLang ? 'r_id' : 'id';
			$deleteId = isset($_POST['delete_id']) ? $_POST['delete_id'] : $itemsToDelete;
			$c = count($deleteId);
			for ($i = 0; $i < $c; $i++) {
				self::deleteDataFromSearchModel($deleteId[$i], $modelId, $model);
				$model::delete(" WHERE `".$fieldName."` = '{#1}'", Array($deleteId[$i]));
				self::addActionToHistory($deleteId[$i], $model, $modelId, 3);
			}
		}
	
		public static function synchronizeDataInSearchModel($objects, $model, $modelId) {
			
			$lang = Application::$settings['default_language'];
			$model::initialize();
			$titleField = $model::$searchSettings['title_field'];
			$contentField = $model::$searchSettings['content_field'];
			$c = count($objects);
			
			for ($i = 0; $i < $c; $i++) {
				$searchObject = SearchModel::find(" WHERE `modelId` = '{#1}' AND `elementId` = '{#2}'", Array($modelId, $objects[$i]->id->value));
				$obj = 0;
				if (count($searchObject)) {
					$obj = $searchObject[0];
				} else {
					$obj = new SearchModel();
					$obj->lang->value = isset($objects[$i]->lang_id) ? $objects[$i]->lang_id->value : $lang;
					$obj->elementId->value = $objects[$i]->id->value;
					$obj->modelId->value = $modelId;
				}
				$obj->recordTitle->value = $objects[$i]->$titleField->value;
				$obj->content->value = $objects[$i]->$contentField->value;
				$obj->url->value = $objects[$i]->getSearchUrl();
				$obj->save();
			}
		}
		
		public static function deleteDataFromSearchModel($deleteId, $modelId, $model, $recordsIn = Array()) {
			if ($model::$multiLang) {
				if (count($recordsIn)) $records = $recordsIn;
				else $records = $model::find(" WHERE `r_id` = '{#1}'", Array($deleteId));
				$rCount = count($records);
				for ($j = 0; $j < $rCount; $j++) SearchModel::delete(" WHERE `elementId` = '{#1}' AND `modelId` = '{#2}'", Array($records[$j]->id->value, $modelId));
			} else {
				SearchModel::delete(" WHERE `elementId` = '{#1}' AND `modelId` = '{#2}'", Array($deleteId, $modelId));
			}
		}
	}
?>
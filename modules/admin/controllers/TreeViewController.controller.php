<?php

	class TreeViewController extends Controller {
		
		public static function view($request, $vars) {
			$treeModel = Application::$modules['admin']['tree_models'][$vars['tree_model_id']];
			$data = $treeModel[0]::view($request, self::$smarty, $vars);
			self::renderTemplate('model' . ds . 'tree' . ds . 'tree-view.tpl', Array(
				'tree_items' => json_encode($data['items']),
				'tree_model_id' => $vars['tree_model_id'],
				'randomNum' => rand(9999,99999),
			));
		}
		
		public static function add($request, $vars) {
			$treeModel = Application::$modules['admin']['tree_models'][$vars['tree_model_id']];
			
			if (isset($_POST['saveItem'])) {
				$treeItem = new $treeModel[0]();
				$parentId = $vars['tree_item_id'];
				$parentItem = $treeModel[0]::get($parentId);
				$treeItem->itemTitle->getSqlData();
				$treeItem->parentId->value = $parentId;
				$treeItem->order->value = ($parentItem) ? $parentItem->order->value + 1 : 0;
				$treeItem->save();
				
				$relatedModel = $treeModel[1];
				if (!$relatedModel::$multiLang) {
					$relatedModelInfo = $relatedModel::getClassInfo();
					$tmpObj = new $relatedModel();
					
					foreach($relatedModelInfo['classVars'] as $v) {
						if ($v == 'id' || $tmpObj->$v->hidden) continue;
						$tmpObj->$v->getSqlData();
					}
					$tmpObj->treeItemId->value = $treeItem->id->value;
					
					$tmpObj->save();
				} else {
					$relatedModelInfo = $relatedModel::getClassInfo();
					
					$tmpObjs = Array();
					$searchObjs = Array();
					$rId = $relatedModel::getMax('r_id') + 1;
					foreach (Application::$settings['languages'] as $k => $v) {
						$tmpObjs[$k] = new $relatedModel();
						$tmpObjs[$k]->r_id = new stdClass();
						$tmpObjs[$k]->r_id->value = $rId;
						$tmpObjs[$k]->lang_id = new stdClass();
						$tmpObjs[$k]->lang_id->value = $k;
						
						foreach($relatedModelInfo['classVars'] as $v) {
							if (($v == 'id') || ($v == 'r_id') || ($v == 'lang_id') || $tmpObjs[$k]->$v->hidden) continue;
							$tmpObjs[$k]->$v->getSqlData($k);
						}
						$tmpObjs[$k]->treeItemId->value = $treeItem->id->value;
						$tmpObjs[$k]->save();
						$searchObjs[] = $tmpObjs[$k];
					}
					if ($relatedModel::$searchable) CRUD::synchronizeDataInSearchModel($searchObjs, $relatedModel, $treeModel[2]);
				}
			}
			
			$out = "";
			$relatedModelData = $treeModel[0]::add($request, self::$smarty, Array(), false);

			$itemTitle = new ModelTextField("itemTitle", Application::$messages['model_tree_relation']['field_item_title'], true, false);
			$treeItemHtml = $itemTitle->getHtml();
			$context = Array();
			$context['treeForm'] = self::renderTemplate('model' . ds . 'fields' . ds . $treeItemHtml['file'], $treeItemHtml['data'], true);
			$context['modelForm'] = $relatedModelData['data']['modelForm'];
			$context['url'] = Application::$settings['url'] . '/' . Controller::$request;
			$out = self::renderTemplate('model' . ds . 'tree' . ds . 'tree-action.tpl', $context, true);
			
			echo $out;
		}
		
		public static function edit($request, $vars) {
			$treeModel = Application::$modules['admin']['tree_models'][$vars['tree_model_id']];
			$itemId = $vars['tree_item_id'];
			
			$out = "";
			
			if (isset($_POST['saveItem'])) {
				
				$treeItem = $treeModel[0]::get($itemId);
				$treeItem->itemTitle->getSqlData();
				$treeItem->save();
				
				$relatedModel = $treeModel[1];
				
				if (!$relatedModel::$multiLang) {
					$relObj = $treeModel[1]::find(" WHERE `treeItemId` = '{#1}'", Array($itemId));
					$relObj = $relObj[0];
					
					$relatedModelInfo = $treeModel[1]::getClassInfo();
					
					foreach($relatedModelInfo['classVars'] as $v) {
						if (($v == 'id') || $relObj->$v->hidden) continue;
						$relObj->$v->getSqlData();
					}
					
					$relObj->save();
				} else {
					$relObj = $treeModel[1]::find(" WHERE `treeItemId` = '{#1}'", Array($itemId));
					$relatedModelInfo = $treeModel[1]::getClassInfo();
					$searchObjs = Array();
					foreach ($relObj as $k => $obj) {
						foreach($relatedModelInfo['classVars'] as $v) {
							if (($v == 'id') || ($v == 'r_id') || ($v == 'lang_id') || $obj->$v->hidden) continue;
							$obj->$v->getSqlData($obj->lang_id->value);
						}
						$obj->save();
						$searchObjs[] = $obj;
					}
					if ($relatedModel::$searchable) CRUD::synchronizeDataInSearchModel($searchObjs, $relatedModel, $treeModel[2]);
				}
			}

			$obj = $treeModel[0]::get($itemId);
			$itemTitle = $obj->itemTitle;
			$treeItemHtml = $itemTitle->getHtml();
			$context = Array();
			$context['treeForm'] = self::renderTemplate('model' . ds . 'fields' . ds . $treeItemHtml['file'], $treeItemHtml['data'], true);
			
			$relObj = $treeModel[1]::find(" WHERE `treeItemId` = '{#1}'", Array($itemId));
			$relObj = $relObj[0];
			
			$vars['model_item_id'] = ($treeModel[1]::$multiLang) ? $relObj->r_id->value : $relObj->id->value;
			$relatedModelData = $treeModel[1]::edit($request, self::$smarty, $vars, false, $treeModel[1]);
			
			$context['modelForm'] = $relatedModelData['data']['modelForm'];
			$context['url'] = Application::$settings['url'] . '/' . Controller::$request;
			$out = self::renderTemplate('model' . ds . 'tree' . ds . 'tree-action.tpl', $context, true);

			echo $out;
		}
		
		public static function delete($request, $vars) {
			
			$treeModel = Application::$modules['admin']['tree_models'][$vars['tree_model_id']];
			$itemId = $vars['tree_item_id'];
			
			$objs = $treeModel[1]::find(" WHERE `treeItemId` = '{#1}'", Array($itemId));
			
			
			$treeModel[1]::delete(" WHERE `treeItemId` = '{#1}'", Array($itemId));
			
			if ($treeModel[1]::$searchable) CRUD::deleteDataFromSearchModel(0, $treeModel[2], $treeModel[1], $objs);
			
			// correct
			$treeModel[0]::delete(" WHERE `id` = '{#1}'", Array($itemId));
			
		}
		
		public static function sort($request, $vars) {
			$treeModel = Application::$modules['admin']['tree_models'][$vars['tree_model_id']];
			if (isset($_POST['treeInfo'])) {
				$treeInfo = json_decode($_POST['treeInfo']);
				$id = $treeModel[0]::$multiLang ? 'r_id' : 'id';
				foreach ($treeInfo as $t) {
					if ($t->rId != 0) {
						$treeItem = $treeModel[0]::find(" WHERE `".$id."` = '{#1}'", Array($t->rId));
						$c = count($treeItem);
						for ($i = 0; $i < $c; $i++) {
							$treeItem[$i]->parentId->value = $t->parentId;
							$treeItem[$i]->itemTitle->value = addslashes($treeItem[$i]->itemTitle->value);
							$treeItem[$i]->order->value = $t->order;
							$treeItem[$i]->save();
						}
					}
				}
			}
		}
		
	}

?>
<?php
	class MenuModel extends CRUDModel {
		public $r_id;
		public $lang_id;
		public $treeItemId;
		public $type;
		public $menuItemTitle;
		public $link;
		public $modelId;
		public $content;
		public $pageId;
		public $visible;
		
		public static $tplViewFile;
		public static $multiLang = true;
		
		private static $pages;
		
		public function __construct() {
		
			$this->r_id = new stdClass();
			$this->r_id->value = null;
			$this->lang_id = new stdClass();
			$this->lang_id->value = null;
		
			$this->treeItemId = new ModelIntegerField("treeItemId", Application::$messages['model_category']['field_parentId'], true, true);
			$this->treeItemId->hidden = true;
			
			$this->type = new ModelSelectField("type", Application::$messages['model_menu']['field_type'], Array(
				"1" => Application::$messages['model_menu']['content'], 
				"2" => Application::$messages['model_menu']['module'], 
				"3" => Application::$messages['model_menu']['link'],
				"4" => Application::$messages['model_menu']['page'],
			), true, true);
			$this->type->tplFile = 'content-selectfield.tpl';
			$this->type->common = true;
			
			$this->menuItemTitle = new ModelTextField("menuItemTitle", Application::$messages['model_menu']['field_itemTitle'], true, true);
			$this->menuItemTitle->htmlCss = Array(
				'class' => 'menu-item-title'
			);
			
			$this->link = new ModelTextField("link", Application::$messages['model_menu']['field_link'], true, true);
			$this->link->htmlCss = Array(
				'class' => 'menu-item-link'
			);

			$modelTitles = Array();
			foreach (Application::$modules['admin']['models'] as $k => $v) {
				$v::initialize();
				$modelTitles[$k] = $v::$title;
			}
			$this->modelId = new ModelSelectField("modelId", Application::$messages['model_menu']['field_modelId'], $modelTitles, true, true);
			$this->modelId->common = true;
			$this->modelId->htmlCss = Array(
				'class' => 'menu-item-model-id'
			);
			
			$this->content = new ModelTinyMce("content", Application::$messages['model_menu']['field_content'], true, true);
			$this->content->htmlCss = Array(
				'class' => 'menu-item-content'
			);
			
			$this->pageId = new ModelSelectField("pageId", Application::$messages['model_menu']['field_pageId'], self::$pages, true, true);
			$this->pageId->common = true;
			$this->pageId->htmlCss = Array(
				'class' => 'menu-item-page-id'
			);
			
			$this->visible = new ModelSelectField("visible", Application::$messages['model_menu']['field_visible'], Array(
				1 => Application::$messages['common_messages']['yyes'],
				0 => Application::$messages['common_messages']['nno'],
			), true, true);
			$this->visible->common = true;
		}

		public static function initialize() {
			self::$title = Application::$messages['model_menu']['title'];
			self::$iconPath = 'category-icons.png';
			self::$searchSettings = Array(
				'title_field' => 'menuItemTitle',
				'content_field' => 'content',
			);
			self::$searchable = true;
			
			$pagesData = self::fQuery("SELECT * FROM `".self::getTableName()."` WHERE `lang_id` = '{#1}'", Array(Application::$settings['default_language']));
			$pages = Array();
			$c = count($pagesData);
			for ($i = 0; $i < $c; $i++) {
				$pages[$pagesData[$i]['r_id']] = $pagesData[$i]['menuItemTitle'];
			}
			self::$pages = $pages;
		}
		
		public function getSearchUrl() {
			return 'view-page/' . $this->r_id->value;
		}
		
		public static function view($request, &$smarty, $vars = Array()) {
			return false;
		}
		
		public static function getTreeItems() {
			$items = MenuRelationsModel::find(" ORDER BY `order`, `parentId`");
			$items = self::buildTree($items);
			return $items;
		}
		
		public static function getChildsFor($pageId, $lang = false) {
			$currentPage = self::get($pageId, $lang);
			$menuRelationsTable = MenuRelationsModel::getTableName();
			$menuModelTable = MenuModel::getTableName();
			$sql = "SELECT `".$menuRelationsTable."`.`order`, 
							`".$menuModelTable."`.* 
					FROM `".$menuRelationsTable."` INNER JOIN `".$menuModelTable."` 
					ON `".$menuModelTable."`.`treeItemId` = `".$menuRelationsTable."`.`id`
					WHERE `".$menuModelTable."`.`visible` = '1' AND `".$menuRelationsTable."`.`parentId` = '{#1}' AND `".$menuModelTable."`.`lang_id` = '{#2}' 
					ORDER BY `".$menuRelationsTable."`.`order`";
			$pages = self::fQuery($sql, Array($currentPage->treeItemId->value, $lang));
			$pages = self::getDataAsObject($pages, self::getClassInfo());
			return self::getPagesUrl($pages);
		}
		
		public static function getSiblingsFor($pageId, $lang = false) {
			$currentPage = self::get($pageId, $lang);
			$menuRelationsTable = MenuRelationsModel::getTableName();
			$menuModelTable = MenuModel::getTableName();
			$sql = "SELECT `".$menuRelationsTable."`.`parentId` FROM `".$menuRelationsTable."`
					INNER JOIN `".$menuModelTable."` 
					ON `".$menuRelationsTable."`.`id` = `".$menuModelTable."`.`treeItemId`
					WHERE `".$menuModelTable."`.`visible` = '1' AND `".$menuModelTable."`.`treeItemId` = '{#1}' AND `".$menuModelTable."`.`lang_id` = '{#2}'";
			$parentId = self::fQuery($sql, Array($currentPage->treeItemId->value, $lang));
			
			if (count($parentId)) $parentId = $parentId[0]['parentId'];
			else $parentId = 0;
			
			if ($parentId == 0) {
				return Array();
			} else {
				$sql = "SELECT `".$menuRelationsTable."`.`order`, 
					`".$menuModelTable."`.* 
					FROM `".$menuRelationsTable."` INNER JOIN `".$menuModelTable."` 
					ON `".$menuModelTable."`.`treeItemId` = `".$menuRelationsTable."`.`id`
					WHERE `".$menuRelationsTable."`.`parentId` = '{#1}' AND `".$menuModelTable."`.`lang_id` = '{#2}' 
					ORDER BY `".$menuRelationsTable."`.`order`";
				$pages = self::fQuery($sql, Array($parentId, $lang));
				$pages = self::getDataAsObject($pages, self::getClassInfo());
				return self::getPagesUrl($pages);
			}
		}
		
		public static function getPagesUrl($pages) {
			foreach ($pages as $k => $mItem) {
				
				$pages[$k]->url = new stdClass();
				switch ($mItem->type->value) {
					case 1:
						$pages[$k]->url->value = Application::$settings['url'] . '/' . Application::$storage['lang'] . '/' . 'view-page/' . $mItem->r_id->value . '/' . urlencode($mItem->menuItemTitle->value);
						break;
					case 2:
						$model = Application::$modules[Application::$adminName]['models'][$mItem->modelId->value];
						$model::initialize();
						$pages[$k]->url->value = Application::$settings['url'] . '/' . Application::$storage['lang'] . '/' . $model::$viewUrl;
						break;
					case 3:
						$pages[$k]->url->value = $mItem->link->value;
						break;
					case 4:
						$pages[$k]->url->value = Application::$settings['url'] . '/' . Application::$storage['lang'] . '/' . 'view-page/' . $mItem->pageId->value;
						break;
				}
			}
			return $pages;
		}
		
	}
?>
<?php

    class SearchModel extends CRUDModel {
		public $lang;
        public $url;
        public $recordTitle;
        public $content;
		public $elementId;
		public $modelId;

        public function  __construct() {
            
			$this->lang = new ModelTextField("lang", "Lang", true, false);
			$this->lang->allowNull = false;
			
			$this->url = new ModelTextField("url", "URL", true, false);
			$this->url->allowNull = false;
			
			$this->recordTitle = new ModelTextField("recordTitle", "Title", true, false);
			$this->recordTitle->allowNull = false;
			
			$this->elementId = new ModelIntegerField("elementId", "Element id", true, false);
			$this->elementId->allowNull = false;
			
			$this->modelId = new ModelIntegerField("modelId", "Model id", true, false);
			$this->modelId->allowNull = false;
			
			$this->content = new ModelTinyMce("content", "TextArea", true, false);
			$this->content->allowNull = false;
        }
		
		public static function initialize() {
			self::$title = 'Search Table';
			self::$iconPath = 'default-icon.png';
			self::$multiLang = false;
			self::$displayFields = Array('url','recordTitle');
		}
		
		public static function searchText($text, $lang, $start, $limit) {
			
			$data = self::find(" 
				WHERE 
				(
					LOWER(CONVERT(`recordTitle` USING utf8)) LIKE '%".strtolower(Security::filterSql(Security::filterString($text), BaseModel::$mysqli))."%' 
					OR 
					LOWER(CONVERT(`content` USING utf8)) LIKE '%".strtolower(Security::filterSql(Security::filterString($text), BaseModel::$mysqli))."%'
				) 
				AND 
				`lang` = '{#1}'
				LIMIT " . $start . "," . $limit, Array($lang));
			
			$c = count($data);
			for ($i = 0; $i < $c; $i++) {
				$data[$i]->content->value = Utils::markWords($data[$i]->content->value, $text);
				$s = Utils::toUpper($text, $lang);
				//$s1 = $text;
				$data[$i]->content->value = preg_replace('/('.$s.')/i', '<span class="searched-keyword">${1}</span>', $data[$i]->content->value);
				$data[$i]->recordTitle->value = preg_replace('/('.$s.')/i', '<span class="searched-keyword">${1}</span>', $data[$i]->recordTitle->value);
				//$data[$i]->content->value = preg_replace('/('.$s1.')/i', '<span class="searched-keyword">${1}</span>', $data[$i]->content->value);
				//$data[$i]->recordTitle->value = preg_replace('/('.$s1.')/i', '<span class="searched-keyword">${1}</span>', $data[$i]->recordTitle->value);
			}
			
			$count = self::count(" 
				WHERE 
				(
					LOWER(CONVERT(`recordTitle` USING utf8)) LIKE '%".strtolower(Security::filterSql(Security::filterString($text), BaseModel::$mysqli))."%' 
					OR 
					LOWER(CONVERT(`content` USING utf8)) LIKE '%".strtolower(Security::filterSql(Security::filterString($text), BaseModel::$mysqli))."%'
				) 
				AND 
				`lang` = '{#1}'
				", Array($lang));
			
			return Array(
				'data' => $data,
				'count' => $count
			);
		}
		
    }

?>

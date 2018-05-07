<?php
	
	require_once 'CRUD.class.php';
	require_once 'SyncDB.class.php';
	
    class CRUDModel extends BaseModel {
	
		public $id;

        public static $iconPath;
        public static $title;
        public static $displayFields;
        public static $multiLang = false;
		public static $searchable = false;
		public static $searchSettings;
		public static $filterSettings = Array();
		public static $perPage = 10;
		public static $useOwnViewUrl = false;
		public static $ownViewUrl = '';
		public static $viewUrl = '';
		private static $initialized = false;
		
		// syncdb
		public static $dbEngine = 'myisam';
		
		private static $commons = Array();

		#region model
		
		public function save($whereCondition = "", $fields = "") {
			if ($this->id == null) $this->insert($fields);
			else $this->update($whereCondition, $fields);
		}
		
		private function insert($fields = "") {
			$classInfo = $this->getClassInfo();
			$sqlData = $this->getSqlData($classInfo);
			
			self::query("INSERT INTO `". self::getTableName($classInfo["className"]) ."` (". $sqlData["fields"] . $fields . ") VALUES (". $sqlData["values"] .")");
			//echo "INSERT INTO `". self::getTableName($classInfo["className"]) ."` (". $sqlData["fields"] . $fields . ") VALUES (". $sqlData["values"] .")";
			$this->id = new stdClass();
			$this->id->value = BaseModel::getLastId(self::getTableName($classInfo['className']));
		}

		private function update($whereCondition = "", $fields = "") {
			$classInfo = $this->getClassInfo();
			$sqlData = $this->getSqlData($classInfo, true);
			$sql = Array();
			foreach ($sqlData as $k => $v) {
				$sql[] = "`". $k ."` = '". $v ."'";
			}
			if (trim($whereCondition) == "") {
				self::query("UPDATE `". self::getTableName($classInfo["className"]) ."` SET ". join(",", $sql) . $fields ." WHERE `id` = '". $this->id->value ."'");
			}
			else {
				self::query("UPDATE `". self::getTableName($classInfo["className"]) ."` SET ". join(",", $sql) . $fields . $whereCondition . " AND `id` = '". $this->id->value ."'");
			}
		}
	
		public function remove() {
			$classInfo = self::getClassInfo();
			if (in_array('deleted', $classInfo['classVars'])) {
				self::query("UPDATE `". self::getTableName() ."` SET `deleted` = '1' WHERE `id` = '{#1}'", Array($this->id->value));
			} else {
				self::query("DELETE FROM `". self::getTableName() ."` WHERE `id` = '{#1}'", Array($this->id->value));
			}
		}
		
		// returns data['field'] = fieldValue; for building sql query
		// возвращает данные для sql запроса
		private function getSqlData($classInfo, $update = false) {
			$sql = Array();

			foreach ($classInfo["classVars"] as $field) {
				if ($field == "id") continue;
				if (($field != "r_id") && ($field != "lang_id") && $this->$field->hidden && $update) {
					continue;
				}
				
				if (isset($this->$field->value)) {
					$sql[$field] = stripslashes($this->$field->value);
				} else {
					//echo $classInfo["className"] . ' - ' . $field . '  ; ';
				}
			}
			if ($update) return $sql;
			
			$fields = "`" . join("`,`",array_keys($sql)) . "`";
			$values = "'" . join("','",$sql) . "'";
			
			return Array("fields" => $fields, "values" => $values);
		}
	
        public function asArray() {
            $classInfo = self::getClassInfo();
            $output = Array();
            foreach ($classInfo["classVars"] as $f) {
                $output[$f] = $this->$f->value;
            }
            return $output;
        }
		
		public function is_valid() {
			// если модель валидная
            return true;
        }

        public static function all($start = false, $length = false) {
			$className = get_called_class();
			$limit = "";
			if (is_numeric($start)) {
				$limit = " LIMIT " . $start;
				if (is_numeric($length)) $limit .= "," . $length;
			}
			$sql = "SELECT * FROM `". self::getTableName($className) ."`";
			$classInfo = self::getClassInfo();
			if (in_array('deleted', $classInfo['classVars'])) $sql .= " WHERE `deleted` = '0'";
			$data = self::fQuery($sql . $limit);
			$output = self::getDataAsObject($data, $classInfo);
			return $output;
		}
		
		/* added after */
		
		public static function getBy($field, $value) {
			$class = get_called_class();
			return $class::find(" WHERE `".$field."` = '{#1}'", Array($value));
		}
		
		public static function getAllRecords($lang = false, $baseField = false, $menu = false) {
			$class = get_called_class();
			$class::initialize();
			$cond = Array();
			$values = Array();
			if ($class::$multiLang && $lang) {
				$cond[] = "`lang_id` = '{#1}'";
				$values[] = $lang;
			}
			if ($menu) $cond[] = "`visible` = '1'";
			
			$out = ($class::$multiLang) ? $class::find(" WHERE " . join(" AND ", $cond), $values) : $class::all();
			
			if ($baseField) {
				$newOut = Array();
				foreach ($out as $k => $v) $newOut[$v->$baseField->value] = $v;
				return $newOut;
			}
			return $out;
		}
		
		public static function getAsKeyVal($key, $val, $lang = false) {
			$class = get_called_class();
			$class::initialize();
			$cond = Array();
			$values = Array();
			if ($class::$multiLang && $lang) {
				$cond[] = "`lang_id` = '{#1}'";
				$values[] = $lang;
			}
			$out = ($class::$multiLang) ? $class::find(" WHERE " . join(" AND ", $cond), $values) : $class::all();
			$newOut = Array();
			foreach ($out as $k => $v) $newOut[$v->$key->value] = $v->$val->value;
			return $newOut;
		}
		
		public static function get($id, $lang = false) {
			$class = get_called_class();
			if ($class::$multiLang) {
				$data = $class::find(" WHERE `r_id` = '{#1}' AND `lang_id` = '{#2}'", Array($id, $lang));
				if (count($data) > 0) return $data[0];
				return false;
			} else {
				$data = $class::find(" WHERE `id` = '{#1}'", Array($id));
				if (count($data) > 0) return $data[0];
				return false;
			}
		}
		
		public static function getMax($field) {
			$class = get_called_class();
			$table = $class::getTableName();
			$r = BaseModel::fQuery("SELECT MAX(`".$field."`) as `max_v` FROM `".$table."`");
			return count($r) ? $r[0]['max_v'] : 0;
		}
		
		public static function getOnlyLastChilds($level, $titleField = false, $onlyTitle = true, $lang = false) {
			$out = Array();
			$class = get_called_class();
			
			$values = ($class::$multiLang) ? Array($lang) : Array();
			$sql = ($class::$multiLang) ? " WHERE `lang_id` = '{#1}'" : "";
			$idField = ($class::$multiLang) ? 'r_id' : 'id';
			$sql .= " ORDER BY `parentId`";
			
			$categories = $class::find($sql, $values);
			$c = count($categories);
			$categoriesKeys = Array();
			for ($i = 0; $i < $c; $i++) $categoriesKeys[$categories[$i]->$idField->value] = $categories[$i]->parentId->value;
			$categoriesId = Array();
			foreach ($categories as $c) {
				if (!isset($categoriesId[$c->$idField->value])) $categoriesId[$c->$idField->value] = 0;
				$parentId = $c->parentId->value;
				$id = $c->$idField->value;
				while ($parentId > 0) {
					$categoriesId[$c->$idField->value]++;
					$id = $parentId;
					if (isset($categoriesKeys[$id])) $parentId = $categoriesKeys[$id];
					else break;
				}
				if ($categoriesId[$c->$idField->value] >= $level) $out[$c->$idField->value] = ($onlyTitle) ? $c->$titleField->value : $c;
			}
			return $out;
		}
		
		public static function getById($id) {
			$class = get_called_class();
			$r = $class::find(" WHERE `id` = '{#1}'", Array($id));
			if (count($r)) return $r[0];
			else return Array();
		}
		
		public static function buildTree($items) {
			$newItems = Array();
			foreach ($items as $item) {
				if ($item->parentId->value == 0) {
					$newItems[] = Array(
						'id' => $item->id->value,
						'parentId' => $item->parentId->value,
						'title' => $item->itemTitle->value,
					);
				} else {
					$newItems = self::addTreeItem($newItems, $item);
				}
			}
			return $newItems;
		}
		
		public static function addTreeItem($newItems, $item) {
			$c = count($newItems);
			for ($i = 0; $i < $c; $i++) {
				if ($newItems[$i]['id'] == $item->parentId->value) {
					$newItems[$i]['items'][] = Array(
						'id' => $item->id->value,
						'parentId' => $item->parentId->value,
						'title' => $item->itemTitle->value,
					);
				} else {
					if (isset($newItems[$i]['items'])) {
						$newItems[$i]['items'] = self::addTreeItem($newItems[$i]['items'], $item);
					}
				}
			}
			return $newItems;
		}
		
		/* added after end */
		
		public static function getTableName($className = false) {
			if (!$className) $className = get_called_class();
            return Application::$dbSettings["prefix"] . "_" . $className;
        }

		public static function count($where = "", $values = Array()) {
			$className = get_called_class();
			$row = self::fQuery("SELECT count(*) as `rowCount` FROM `" . self::getTableName($className) . "` " . $where , $values);
			return $row[0]["rowCount"];
		}

		public static function find($sql = "", $values = Array()) {
			$className = get_called_class();
			$query = "SELECT `".self::getTableName($className)."`.* FROM `" . self::getTableName($className) . "` " . $sql;
			$data = self::fQuery($query , $values);
			return self::getDataAsObject($data, self::getClassInfo());
		}
		
		public static function delete($sql = "", $values = Array()) {
			$className = get_called_class();
			$classInfo = self::getClassInfo();
			if (in_array('deleted', $classInfo['classVars'])) return self::query("UPDATE `" . self::getTableName($className) . "` SET `deleted` = 1 " . $sql , $values);
			else return self::query("DELETE FROM `" . self::getTableName($className) . "` " . $sql , $values);
		}
		
		public static function deleteById($id, $lang = false) {
			$sql = (self::$multiLang) ? " WHERE `r_id` = '{#1}' AND `lang_id` = '{#2}'" : " WHERE `id` = '{#1}'";
			$values = (self::$multiLang) ? Array($id, $lang) : Array($id);
			$classInfo = self::getClassInfo();
			$className = $classInfo['className'];
			if (in_array('deleted', $classInfo['classVars'])) return self::query("UPDATE `" . self::getTableName($className) . "` SET `deleted` = 1 " . $sql , $values);
			else return self::query("DELETE FROM `" . self::getTableName($className) . "` " . $sql , $values);
		}

        public static function getClassInfo() {
			$classInfo = Array();
			$classVars = Array();
			$class = get_called_class();
			$reflect = new ReflectionClass($class);
			$props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
            
			foreach ($props as $p) {
                if (!$p->isStatic()) {
                    $classVars[] = $p->getName();
                }
			}
			return Array("className" => $class, "classVars" => $classVars);
		}
		
		protected static function getDataAsObject($data, $classInfo) {
			$out = Array();
			$i = 0;
			foreach ($data as $row) {
				$out[$i] = new $classInfo["className"]();
				$out[$i]->id = new stdClass();
				$out[$i]->r_id = new stdClass();
				$out[$i]->lang_id = new stdClass();
				foreach ($classInfo["classVars"] as $var) {
					if (!is_object($out[$i]->$var)) {
						$out[$i]->$var = new stdClass();
						$out[$i]->$var->value = 0;
					}
					$out[$i]->$var->value = $row[$var];
				}
				$i++;
			}
			
			return $out;
		}
		
		#endregion
		
		# region admin
		
		// возвращает данные для показа на странице (например в виде таблицы записей из бд)
        public static function getDisplayInfo($start, $count) {
        Global $__app;
			$query = Array();
			$classInfo = self::getClassInfo();
			
            if ($classInfo['className']::$multiLang) $query[] = " `lang_id` = '". $__app["default_language"] ."'";
			if (in_array('deleted', $classInfo['classVars'])) $query[] = " `deleted` = 0";
			if (count($query)) $query = "WHERE " . join(" AND ", $query);
			else $query = "";
			
            $queryLimit = $query . " LIMIT " . $start . ", " . $count;
			
            $objects = $classInfo['className']::find($queryLimit);
			
            $fields = Array();
			$id = "";
			if ($classInfo['className']::$multiLang) $id = "r_id";
			else $id = "id";
			$c = count($objects);
			$classInfo['className']::initialize();
			for ($i = 0; $i < $c; $i++) {
				$fields[$i]['id'] = $objects[$i]->$id->value;
				foreach ($classInfo['className']::$displayFields as $v) {
                    $fields[$i][$v] = $objects[$i]->$v->getDisplayValue();
                }
			}
            $count = $classInfo['className']::count($query, Array($__app["default_language"]));
			
			return Array(
				'fields' => $fields, 
				'count' => ceil($count / $classInfo['className']::$perPage)
			);
        }
		
		
		// сохраняет данные в базу данных, вызывается при вставке и обновлении записи
		// $isEditAction - является ли действие редактированием
		// $itemId - id либо r_id записи(обьекта) модели
        public static function saveItem($isEditAction = false, $itemId = false) {
        Global $__app;
			$errors = Array();
            $classInfo = self::getClassInfo();
			if (!self::$initialized) {
				$classInfo["className"]::initialize();
				self::$initialized = true;
			}
			// по умолчанию id не используется
            $unusedFields = Array("id");
			if (in_array('deleted', $classInfo['classVars'])) $unusedFields[] = "deleted";
            if ($classInfo["className"]::$multiLang) {
                $obj = Array();
				// если мультиязычный в список не используемых в цикле полей добавляем r_id, lang_id
                $unusedFields[] = "r_id";
                $unusedFields[] = "lang_id";
				$lastId = self::getLastId(self::getTableName());
				$r_id = ($isEditAction) ? $itemId : $lastId + 1;
				
				// если мультиязыный создаётся несколько обьектов для каждого языка один
				$obj = Array();
				$i = 0;
				// для всех языков системы
                foreach ($__app["languages"] as $langIso => $v) {
					// имзеняем занчение переменной $id для того чтобы метод save() произвёл запрос update а не insert
					if (!$isEditAction) {
						$obj[$i] = new $classInfo["className"]();
						// это делается чтобы обойти предупреждения
						$obj[$i]->r_id = new stdClass();
						$obj[$i]->lang_id = new stdClass();
						$obj[$i]->r_id->value = $r_id;
						$obj[$i]->lang_id->value = $langIso;
						
						if (in_array('deleted', $classInfo['classVars'])) {
							$obj[$i]->deleted = new stdClass();
							$obj[$i]->deleted->value = 0;
						}
					} else {
						$tmpObj = self::find(" WHERE `lang_id` = '{#1}' AND `r_id` = '{#2}'", Array($langIso, $itemId));
						$obj[$i] = $tmpObj[0];
					}
					
					foreach ($classInfo["classVars"] as $v) {
						// если $v не находится среди не используемых полей
						if (!in_array($v, $unusedFields)) {
							$result = $obj[$i]->$v->getSqlData($langIso);
							if ($result["success"] == false) {
								//$errors[$langIso][] = $result["name"];
								$errors[$langIso][] = $obj[$i]->$v->title;
							}
						}
					}
					$i++;
                }
				
				// если не произошло ошибок сохраняем данные в базу данных
				if (count($errors) == 0) {
					$c = count($obj);
					for ($i = 0; $i < $c; $i++)	{
						// условие на котором будет производиться изменение в базе данных
						$whereCondition = "";
						if ($isEditAction) {
							$whereCondition = " WHERE `r_id` = '". $r_id ."' AND `lang_id` = '". $obj[$i]->lang_id->value ."'";
						}
						$obj[$i]->save($whereCondition);
					}
					return Array('success' => true, 'data' => $obj);
				} else {
					return Array('success' => false, 'data' => $errors);
				}
            } else {
                $obj = new $classInfo["className"]();
				if ($isEditAction) {
					$obj->id = new stdClass();
					$obj->id->value = $itemId;
				}
				if (in_array('deleted', $classInfo['classVars'])) {
					$obj->deleted = new stdClass();
					$obj->deleted->value = 0;
				}
                foreach ($classInfo["classVars"] as $v) {
                    if (!in_array($v, $unusedFields)) {
						if ($obj->$v->hidden) continue;
						$result = $obj->$v->getSqlData();
						//if ($result["success"] == false) $errors[] = $result["name"];
						if ($result["success"] == false) $errors[] = $obj->$v->title;
					}
                }
                if (count($errors) == 0) {
					$obj->save();
					return Array('success' => true, 'data' => Array($obj));
				} else {
					return Array('success' => false, 'data' => $errors);
				}
            }
        }
		
		/*
			returns html form for model data manipulation
		*/
		
        public static function getForm(&$smarty, $data = false, $lang = false) {
        Global $__app;
            $classInfo = self::getClassInfo();
			$obj = $data ? $data : (new $classInfo["className"]());
            $output = "";
			//$obj->initialize();
			/*
				$unusedFields - field which will not be use in loop
			*/
            $unusedFields = Array("id");
            if ($classInfo["className"]::$multiLang) {
                $unusedFields[] = "r_id";
                $unusedFields[] = "lang_id";
				
            }
			if (in_array('deleted', $classInfo['classVars'])) $unusedFields[] = "deleted";
			
			foreach ($classInfo["classVars"] as $var) {
				// если $var не находится среди не используемых
				if (!in_array($var, $unusedFields)) {
					// if hidden
					if ($obj->$var->hidden) continue;
					if ($obj->$var->common) {
						// если уже не был использован, для мультиязычных полей
						if (!in_array($var, self::$commons)) {
							// добавляем в список общих полей данное поле
							self::$commons[] = $var;
							$returned = $obj->$var->getHTML();
							foreach ($returned['data'] as $k => $v) $smarty->assign($k, $v);
							$output .= $smarty->fetch('model'. ds .'fields'. ds .$returned['file']);
						}
					} else {
						// если поле не является общим
						// достаем язык и передаем для получения html
						$langId = false;
						// не понятно почему у не мультиязычного $obj есть поле lang_id
						// обошёл поставив условие мультиязычности в условие
						if (isset($obj->lang_id) && $obj::$multiLang) {
							//$obj->lang_id = new stdClass();
							//if (!$obj::$multiLang) echo "ok";
							$langId = $obj->lang_id->value;
						}
						$returned = $obj->$var->getHTML($langId);
						foreach ($returned['data'] as $k => $v) $smarty->assign($k, $v);
						$output .= $smarty->fetch('model'. ds .'fields'. ds .$returned['file']);
					}
				}
			}
            return $output;
        }
		
		#endregion
		
		#region model actions
		
		public static function view($request, &$smarty, $vars = Array()) {
			return CRUD::view($request, $smarty, $vars);
		}

		public static function edit($request, &$smarty, $vars = Array(), $saveItem = true, $model = false) {
			return CRUD::edit($request, $smarty, $vars, $saveItem, $model);
		}
		
		public static function add($request, &$smarty, $vars = Array(), $saveItem = true, $model = false) {
			return CRUD::add($request, $smarty, $vars, $saveItem, $model);
		}
		
		public static function deleteItem($request, &$smarty, $vars = Array(), $itemsToDelete = Array()) {
			CRUD::delete($request, $smarty, $vars, $itemsToDelete);
		}
		
		#endregion
		
		#region syncdb
		
		public static function synchronize($returnSql = false) {
			return SyncDB::synchronize(self::getClassInfo(), $returnSql);
		}
		
		public static function getDBTables() {
			SyncDB::getDBTables();
		}
		
		final public static function synchronizeAll() {
			return SyncDB::synchronizeAll();
		}
		
		#endregion
		
	}

	require_once 'TreeViewModel.class.php';
	require_once 'ContentWithBlocks.class.php';
	
?>
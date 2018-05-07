<?php

    class Model extends BaseModel {
        public $id;

        public function save() {
            if ($this->id == null) $this->insert();
            else $this->update();
        }

        public function asArray() {
            $classInfo = self::getClassInfo();
            $output = Array();
            foreach ($classInfo["classVars"] as $f) {
                $output[$f] = $this->$f;
            }
            return $output;
        }

        // returns data['field'] = fieldValue; for building sql query
        private function getSqlData($classInfo, $update = false) {
            $sql = Array();
            foreach ($classInfo["classVars"] as $field) {
                if ($field != "id")	$sql[$field] = $this->$field;
            }
            if ($update) return $sql;
            $fields = "`" . join("`,`",array_keys($sql)) . "`";
            $values = "'" . join("','",$sql) . "'";
            return Array("fields" => $fields, "values" => $values);
        }

        private function insert() {
            $classInfo = $this->getClassInfo();
            $sqlData = $this->getSqlData($classInfo);
            self::query("INSERT INTO `". self::getTableName($classInfo["className"]) ."` (". $sqlData["fields"] .") VALUES (". $sqlData["values"] .")");
        }

        private function update() {
            $classInfo = $this->getClassInfo();
            $sqlData = $this->getSqlData($classInfo, true);
            $sql = Array();
            foreach ($sqlData as $k => $v) $sql[] = "`". $k ."` = '". $v ."'";
            self::query("UPDATE `". self::getTableName($classInfo["className"]) ."` SET ". join(",", $sql) ." WHERE `id` = '". $this->id ."'");
        }

        public static function all($start = false, $length = false) {
            $className = get_called_class();
            $limit = "";
            if (is_numeric($start)) {
                $limit = " LIMIT " . $start;
                if (is_numeric($length)) $limit .= "," . $length;
            }
            $data = self::fquery("SELECT * FROM `". self::getTableName($className) ."`" . $limit);
            $classInfo = self::getClassInfo();
            $output = self::getDataAsObject($data, $classInfo);
            return $output;
        }

        public static function count($where = "", $values = Array()) {
            $className = get_called_class();
            $row = self::fQuery("SELECT count(*) as `rowCount` FROM `". self::getTableName($className) . "` " . $where , $values);
            return $row[0]["rowCount"];
        }

        public static function find($sql = "", $values = Array()) {
            $className = get_called_class();
            $data = self::fQuery("SELECT * FROM `" . self::getTableName($className) . "` " . $sql , $values);
            return self::getDataAsObject($data, self::getClassInfo());
        }

        private static function getTableName($className) {
        Global $__db;
            return $__db["prefix"] . "_" . $className;
        }


        private static function getClassInfo() {
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

        private static function getDataAsObject($data, $classInfo) {
            $out = Array();
            foreach ($data as $row) {
                $object = new $classInfo["className"]();
                foreach ($classInfo["classVars"] as $var) {
                    $object->$var = $row[$var];
                }
                $out[] = $object;
            }
            return $out;
        }
        
    }

?>
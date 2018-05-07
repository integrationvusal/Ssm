<?php
    abstract class BaseModel {
        public static $queryCount;
        public static $connected = false;
		public static $mysqli;
		public static $connections;
        // connect to db manager and select db
        public static function connect($connectionName = false) {
        Global $__db;
			if (!$connectionName) $connectionName = 'default';
			self::$mysqli = new mysqli($__db[$connectionName]['host'], $__db[$connectionName]['user'], $__db[$connectionName]['password'], $__db[$connectionName]['name']);
			self::$connections[$connectionName] = self::$mysqli;
            self::query("SET NAMES utf8");
            self::$connected = true;
        }
		
		public static function setConnection($connectionName) {
			self::$mysqli = self::$connections[$connectionName];
		}
		
		public static function getLastId($table) {
			$r = self::fQuery("SELECT MAX(`id`) as `id` FROM `". $table ."`");
			return $r[0]['id'];
		}

        protected static function filterValues($values) {
            foreach ($values as $k => $v) {
                $values[$k] = Security::filterSql(Security::filterString($v), self::$mysqli);
            }
            return $values;
        }

        protected static function parseSql($sql,$values) {
            $values = self::filterValues($values);
            $matches = Array();
            preg_match_all("/'({#[0-9]}{1,3})'/",$sql,$matches);
            $sql = preg_replace($matches[0],$values,$sql);
            return $sql;
        }

        // disconnect from db manager
        public static function disconnect() {
            if (self::$connected) {
                self::$mysqli->close();
                $connected = false;
            }
        }

        public function __destruct() {

        }

        public static function query($query,$values = Array()) {
            self::$queryCount++;
            if (count($values)) $query = self::parseSql($query,$values);
			if (Application::$settings['debug']) echo '<!--'.$query.' ; -->';
			//echo $query.'<br/>';
			//echo "<!--";
			//echo $query;
			//echo "-->";
            return self::$mysqli->query($query);
        }

        public static function fQuery($query,$values = Array()) {
            $result = self::query($query,$values);
            $out = Array();
			try {
				while($row = $result->fetch_array(MYSQLI_ASSOC)) {
					$out[] = $row;
				}
			} catch (Exception $e) {
				print_r($e);
			}
			$result->free();
            return $out;
        }
    }
?>
<?php

	class HighLightSQL {
		public static $keyWords = Array(
			Array('sql-command-green bold','CREATE'),
			Array('sql-command-green bold','IF'),
			Array('sql-command-green bold','NOT'),
			Array('sql-command-green bold','EXISTS'),
			Array('sql-command-green bold','DEFAULT'),
			Array('sql-command-green bold','null'),
			Array('sql-command-green bold','NULL'),
			Array('sql-command-green bold','int'),
			Array('sql-command-green bold','blob'),
			Array('sql-command-green bold','varchar'),
			Array('sql-command-green bold','char'),
			Array('sql-command-green bold','KEY'),
			Array('sql-command-green bold','PRIMARY'),
			Array('sql-command-green bold','CHARSET'),
			Array('sql-command-green bold','ENGINE'),
			Array('sql-command-green bold','AUTO_INCREMENT'),
			Array('sql-command-green bold','TABLE'),
			Array('sql-command-green bold','ALTER'),
			Array('sql-command-green bold','DROP'),
			Array('sql-command-green bold',')'),
			Array('sql-command-green bold','('),
		);
		
		public static function highlight($sql) {
			foreach (self::$keyWords as $command) {
				$sql = str_replace($command[1], '<span class="'.$command[0].'">'. $command[1] .'</span>', $sql);
			}
			return $sql;
		}
	}

?>
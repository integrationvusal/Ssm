<?php

	class Utils {

		public static function remap($key, $val, $array){

			$new_array = array();
			foreach($array as $arr){
				$new_array[$arr[$key]] = $arr[$val];
			}
			return $new_array;

		}

		public static function getCurrentDate($format = false){
			if(!$format) $format = "Y-m-d H:i:s";
			$date = new DateTime();
			$date->setTimezone(new DateTimeZone("Asia/Baku"));
			return $date->format($format);
		}

		public static function generatePassword($length, $symbols = Array()) {
			$symbols = count($symbols) ? $symbols : Array('a','b','c','d','e','f','g','h','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9','!','@','#','$','%','&','*');
			$max = count($symbols) - 1;
			$password = '';
			for ($i = 0; $i < $length; $i++) {
				$password .= $symbols[rand(0,$max)];
			}
			return $password;
		}

		public static function printIt($data, $exit = false){
			echo "<pre>";
			print_r($data);
			echo  "</pre>";
			if($exit) exit;
		}

		public static function getCurrency($code){
			$currencies = self::getCurrencies();
			return $currencies[$code];
		}

		public static function convertCurrencies($amount, $from, $to){
			$currencies = self::getCurrencies();
			$first = isset($currencies[$from])?$currencies[$from]:1;
			$second = isset($currencies[$to])?$currencies[$to]:1;
			return ($first/$second)*$amount;
		}

		private static function getCurrencies(){
			$currencies = SessionStorage::get('currencies');
			if(empty($currencies)){
				$currencies = [];
				$xml = simplexml_load_file('http://cbar.az/currencies/'.date('d.m.Y').'.xml');
				foreach($xml->ValType[1] as $currency){
					$value = round((float)$currency->Value, 2);
					$currencies[(string)$currency['Code']] = $value == 0?0.01:$value;
				}
				SessionStorage::add('currencies', $currencies);
			}
			return $currencies;
		}

		public static function generateToken() {
			return md5(self::generatePassword(5));
		}

		public static function toUpper($string, $b = false) {
			$string = strtoupper($string);
			if ($b) {
				$string = str_replace('I', 'İ', $string);
			}
			return $string;
		}

		public static function getFileExtension($string) {
			return substr(strrchr($string, "."), 1);
		}

		public static function getAgeByBirthDay($birthDay) {
			return intval(date('Y-m-d') - $birthDay);
		}

		public static function pasteToTag($text, $value, $tagName) {
			$tagName = 'name';

			$pattern = '/\['.$tagName.'\]([a-zA-Zəöğçşüiı;&]+)\[\/'.$tagName.'\]/i';
			if (empty($value)) $value = '<span class="ibanner-reg-text ibanner-reg" >${1}</span>';
			return preg_replace($pattern, $value, $text);
		}

		public static function getFileNameFromWindowsPath($string) {
			$fileName = substr(strrchr($string, "/"), 1);
			if ($fileName == "") {
				return $string;
			}
			else return $fileName;
		}

		public static function getFileNameWithoutExt($string) {
			return substr($string, 0, strrpos($string, "."));
		}

		public static function fileupload($dir, $fileName){
			$input = fopen("php://input", "r");
			$temp = tmpfile();
			$realSize = stream_copy_to_stream($input, $temp);
			fclose($input);

			if(!isset($_SERVER["CONTENT_LENGTH"]) || $realSize != (int)$_SERVER["CONTENT_LENGTH"]) return array('status'=>'err','msg'=>'The actual size of the file does not match the passed');
			if(is_dir($dir)) {
				$file = $dir . ds . $fileName;
			}
			$target = fopen($file, "w");

			fseek($temp, 0, SEEK_SET);
			stream_copy_to_stream($temp, $target);
			fclose($target);
			return $file;
		}

		public static function uploadImage($uploadDir, $elemName, $fileName = null){

			if(isset($_FILES)){
				$tmpName = $_FILES[$elemName]['tmp_name'];
				$type = explode("/", $_FILES[$elemName]['type']);

				if(isset($type[1]) && in_array($type[1], Application::$settings['image_extensions'])){
					if($fileName === null) $fileName = time() . "." . $type[1];
					@mkdir($uploadDir, 0777, true);
					if(move_uploaded_file($tmpName, $uploadDir . ds . $fileName)) return $uploadDir . ds . $fileName;
					return false;
				}
				return false;
			}
			return false;

		}

		public static function uploadImages($uploadDir, $elemName){

			if(isset($_FILES) && is_array($_FILES[$elemName]['error'])){
				$moved = array();
				foreach($_FILES[$elemName]['error'] as $key => $errCount){
					if($errCount == 0){
						$tmpName = $_FILES[$elemName]['tmp_name'][$key];
						$type = explode("/", $_FILES[$elemName]['type'][$key]);

						if(isset($type[1]) && in_array($type[1], Application::$settings['image_extensions'])){
							$fileName = $key . time() . "." . $type[1];
							@mkdir($uploadDir, 0777, true);
							if(move_uploaded_file($tmpName, $uploadDir . ds . $fileName)) {
								$moved[] = $uploadDir . ds . $fileName;
							} else {
								self::unlinkSet($moved);
								return false;
							}
						} else {

							self::unlinkSet($moved);
							return false;
						}
					}
				}
				$str = "";
				foreach($moved as $fName){
					$str .= $fName . ";";
				}
				return $str;

			}
			return false;

		}

		public static function unlinkSet($array){
			$flag = true;
			foreach($array as $arr){
				if(!empty($arr) && !unlink($arr)) $flag = false;
			}
			return $flag;
		}

		public static function isImage($fileName) {
			if (in_array(self::getFileExtension($fileName), Application::$settings['image_extensions'])) return true;
			return false;
		}


		public static function rmdir($dir) {
			foreach(glob($dir . '/*') as $file) {
				if(is_dir($file)) self::rmdir($file);
				else unlink($file);
			}
			rmdir($dir);
		}

		public static function sendMail($mails, $subject, $body, $from, $fromName, $cc=false) {

			require_once Application::$settings['libs_folder'] . ds . 'swiftmailer' . ds . "swift_required.php";

			$host = Application::$settings['smtp']['host'];
			$port = Application::$settings['smtp']['port'];
			$user = Application::$settings['smtp']['user'];
			$password = Application::$settings['smtp']['password'];
			$security = Application::$settings['smtp']['security'];

			$transport = Swift_SmtpTransport::newInstance($host, $port, $security)
				->setUsername($user)
				->setPassword($password);

			$mailer = Swift_Mailer::newInstance($transport);
			$message = Swift_Message::newInstance($subject)
				->setFrom(array($from => $fromName))
				->setTo($mails)
				->setBody($body);
			if($cc) $message->setCc($cc);
			$numSent = $mailer->send($message);
			return $numSent;
		}

		public static function parseWords($string, $count) {
			$out = "";
			$string = strip_tags($string);
			$words = explode(" ", $string);
			$i = 0;
			foreach ($words as $w) {
				$out .= $w . ' ';
				$i++;
				if ($i > 25) break;
			}
			return $out;
		}

		public static function cutToShort($string, $count) {
			$out = "";
			$string = strip_tags($string);
			$words = explode(" ", $string);
			$i = 0;
			foreach ($words as $w) {
				$out .= $w . ' ';
				$i++;
				if ($i > $count) break;
			}
			return $out;
		}

		public static function debug($data, $append = true){
			file_put_contents(
				$_SERVER['DOCUMENT_ROOT'].'/debug',
				print_r($data,true)."\n\r",
				$append?FILE_APPEND:null
			);
		}

		public static function markWords($text, $key) {
			$text = strip_tags($text);
			$before = self::cutToShort(stristr($text, $key, true), 10);
			$after = self::cutToShort(stristr($text, $key), 10);
			return $before . ' ' . $after;
		}

		/*
		public static function generatePaginator($count, $limit, $page) {
			$pagesCount = ceil($count / $limit);
			$paginator = Array();
			$i = 0;
			$paginator[$i] = Array(
				'key' => $page - 1,
				'title' => '&laquo;&laquo;',
			);
			if ($page == 0) $paginator[$i]['inactive'] = '1';

			$l = $limit;
			$p = intval($l / 2);
			$left = ($page - $p >= 0) ? $p : $page;
			$right = (($l - $left) + $page < $pagesCount) ? $l - $left : $pagesCount - $page;


			while ($left > 0) {
				$paginator[] = Array(
					'key' => $page - $left,
					'title' => ($page - $left) + 1,
				);
				$left--;
			}

			$i = 0;
			while (($i <= $right) && ($page + $i < $pagesCount)) {
				$paginator[] = Array(
					'key' => $page + $i,
					'title' => ($page + $i) + 1,
				);
				$i++;
			}

			$paginator[] = Array(
				'key' => $page + 1,
				'title' => '&raquo;&raquo;',
			);
			if (!($page < ($pagesCount - 1))) $paginator[$i]['inactive'] = '1';

			return $paginator;
		}
		*/

		public static function generatePaginator($count, $limit, $page, $paginatorLimit = 3){

			$pagesCount = ceil($count / $limit);

			$paginator = Array();

			if($page < 1 || $page > $pagesCount) $page = 1;
			if($paginatorLimit < 1 || $paginatorLimit > $pagesCount) $paginatorLimit = $pagesCount;

			/**
			 *  Laquo button
			 */
			$paginator[0] = array(
				'title' => 	'&laquo;',
			);

			if($page - 1 <= 0) {
				$paginator[0]['page'] = 0;
				$paginator[0]['disabled'] = true;
			} else {
				$paginator[0]['page'] = $page - 1;
			}
			// Laquo button

			for($i = 1; $i <= $pagesCount; $i++){
				$paginator[$i]['page'] = $i;
				$paginator[$i]['title'] = $i;
				if($i == $page) $paginator[$i]['active'] = true;
				else $paginator[$i]['active'] = false;
			}

			/**
			 *  Raquo button
			 */
			$paginator[] = array(
				'title' => 	'&raquo;',
			);
			end($paginator);         // move the internal pointer to the end of the array
			$key = key($paginator);

			if($page + 1 > $pagesCount) {
				$paginator[$key]['page'] = $pagesCount;
				$paginator[$key]['disabled'] = true;
			} else {
				$paginator[$key]['page'] = $page + 1;
			}
			// Raquo button

			$res_paginator = array($paginator[0]);

			/*
			if($paginatorLimit < $pagesCount)
			{
				$pages = $paginatorLimit-1;
				$incs = $paginatorLimit;
				while($incs > 0){
					if($page - $pages > 0){
						$res_paginator[] = $paginator[$page - $pages];
						$pages--;
						$incs--;
					} else {
						$pages--;
					}
				}
			}

			*/

			if($paginatorLimit <= $pagesCount){
				$pagesLimit = $paginatorLimit - 1;
				$pagesLimit = $pagesLimit / 2;
				$left = floor($pagesLimit);
				$right = ceil($pagesLimit);
				for($i = 1; $i <= $left; $i++){
					if($page - $left <= 0){
						$left--;
						$right++;
					}
				}
				for($i = 1; $i <= $right; $i++){
					if($page + $right > $pagesCount){
						$right--;
						$left++;
					}
				}
				for($i = $page - $left; $i <= $page+ $right; $i++){
					$res_paginator[$i] = $paginator[$i];
				}

			}
			$res_paginator[] = $paginator[$key];
			return $res_paginator;
		}

	}

?>
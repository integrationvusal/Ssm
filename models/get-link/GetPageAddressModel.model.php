<?php

	class GetPageAddressModel extends CRUDModel {
		
		public function __construct() {
		
		}
		
		public static function initialize() {
		Global $_adminName;
			self::$title = Application::$messages['model_get_link']['title'];
			self::$iconPath = 'links-icon.png';
			self::$useOwnViewUrl = true;
			self::$ownViewUrl = Application::$settings['url'] . '/' . $_adminName . '/get-page-url';
		}
		
	}

?>
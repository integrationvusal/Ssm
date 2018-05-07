<?php

	class AdminUsersModel extends CRUDModel {
		public $name;
		public $email;
		public $password;
		public $avatar;
		public $groupId;
		public $cookieToken;
		
		public static $multiLang = false;
		
		public function __construct() {
			$this->name = new ModelTextField("name", Application::$messages['model_admin_users']['field_name'], true, false);
			$this->email = new ModelTextField("email", Application::$messages['model_admin_users']['field_email'], true, false);
			$this->password = new ModelPasswordField("password", Application::$messages['model_admin_users']['field_password'], true, false);
			$this->password->required = false;
			$this->avatar = new ModelFMImageField("avatar", Application::$messages['model_admin_users']['field_avatar'], false, true, false);
			$this->avatar->multiLang = false;
			
			$allGroups = Array();
			$groups = AdminUsersGroupModel::all();
			foreach ($groups as $g) {
				$allGroups[$g->id->value] = $g->groupTitle->value;
			}
			$this->groupId = new ModelSelectField("groupId", Application::$messages['model_admin_users']['field_group'], $allGroups, true, false);
			$this->cookieToken = new ModelTextField("cookieToken", "Cookie token", true, false);
			$this->cookieToken->hidden = true;
			$this->cookieToken->required = false;
		}
		
		public static function initialize() {
			self::$title = Application::$messages['model_admin_users']['title'];
			self::$iconPath = 'users.png';
			self::$displayFields = Array('avatar','name','email','groupId');
			self::$useOwnViewUrl = false;
		}
		
		public static function getUserByEmailAndPassword($email, $password) {
			$r = self::find(" WHERE `email` = '{#1}' AND `password` = '{#2}'", Array($email, $password));
			if (count($r)) return Array('exists' => true, 'record' => $r);
			else return Array('exists' => false);
		}
		
		public static function getUserByToken($token) {
			$r = self::find(" WHERE `cookieToken` = '{#1}'", Array($token));
			if (count($r)) return Array('exists' => true, 'record' => $r);
			else return Array('exists' => false);
		}
	}

?>
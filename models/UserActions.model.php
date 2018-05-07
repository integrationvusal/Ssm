<?php

	/*
		* Actions
		*
		* 1 <=> Add
		* 2 <=> Edit
		* 3 <=> Delete
	*/

	class UserActions extends CRUDModel {
		public $userId;
		public $recordId;
		public $classId;
		public $multilang;
		public $active;
		public $action;
		public $time;

		public function __construct() {
			$this->userId = new ModelIntegerField("userId", "User id", true, false);
			$this->recordId = new ModelIntegerField("recordId", "Record id", true, false);
			$this->classId = new ModelIntegerField("classId", "Class id", true, false);
			
			$this->active = new ModelBooleanField("active", "Is active", true, false);
			
			$this->action = new ModelIntegerField("action", "Action", true, false);
			$this->action->dbLength = 2;
			
			$this->multilang = new ModelBooleanField("multilang", "Multilang", true, false);
			
			$this->time = new ModelIntegerField("time", "Happened time", true, false);
		}

		public static function initialize() {
			self::$multiLang = false;
			self::$searchable = false;
			self::$useOwnViewUrl = false;
		}
		
	}

?>
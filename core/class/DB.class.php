<?php

class DB{

    public $dbh;

    public function start($options = null){

        if($options === null){
            $options = Application::$dbSettings['default'];
        }

        $user = isset($options['user']) ? $options['user'] : "";
        $password = isset($options['password']) ? $options['password'] : "";
        $name = isset($options['name']) ? $options['name'] : "";
        $host = isset($options['host']) ? $options['host'] : "";

        try {
			$this->dbh = new PDO('mysql:host=' . $host . ';dbname=' . $name, $user, $password,
								 array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->dbh;
        } catch (PDOException $e) {
            $this->dbh = null;
            return false;
        }

    }

    public function stop(){

        $this->dbh = null;
        return true;

    }

}
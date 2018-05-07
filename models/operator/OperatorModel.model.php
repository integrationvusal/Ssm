<?php

class OperatorModel{

    public $id;
    public $name;
    public $login;
    public $password;
    public $description;
    public $user_id;

    private $tableName = "vl1_OperatorModel";

    protected $db;

    public function __construct(){

        $this->db = (new DB())->start();

    }

    public function getByLoginAndPassword($array){

        try{

            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . "
            WHERE login=:login AND password=:password");
            $stmt->bindValue(":login", $array['login']);
            $stmt->bindValue(":password", Security::getPasswordHash($array['login'] . $array['password']));
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(count($res) > 1){
                Logger::writeSpecialLog("Double login password set", $array);
                return false;
            } elseif(count($res) == 1) {

                return $res[0];

            } else {

                return false;

            }

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function createOperator($array){

        try{

            $stmt = $this->db->prepare("INSERT INTO " . $this->tableName . "(user_id, name, login, password, description)
            VALUES(:user_id, :name, :login, :password, :description)");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":name", $array['name']);
            $stmt->bindValue(":login", $array['login']);
            $stmt->bindValue(":password", Security::getPasswordHash($array['login']. $array['password']));
            $stmt->bindValue(":description", $array['description']);
            $stmt->execute();

            return $stmt->rowCount();

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function updateOperator($array){

        try{

            if(isset($array['password'])){
                $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET name=:name, login=:login, description=:description,
                password=:password WHERE id=:id AND user_id=:user_id");
                $stmt->bindValue(":password", Security::getPasswordHash($array['login']. $array['password']));
            } else {
                $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET name=:name, login=:login, description=:description
                WHERE id=:id AND user_id=:user_id");
            }
            $stmt->bindValue(":name", $array['name']);
            $stmt->bindValue(":login", $array['login']);
            $stmt->bindValue(":description", $array['description']);
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":id", $array['operator_id']);
            return $stmt->execute();

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function deleteOperator($array){
        try{

            $stmt = $this->db->prepare("DELETE FROM " . $this->tableName . " WHERE user_id = :user_id AND id = :operator_id");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":operator_id", $array['operator_id']);
            $stmt->execute();

            return $stmt->rowCount();

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }
    }

    public function getOne($array){

        try{

            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id = :user_id AND id = :operator_id");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":operator_id", $array['operator_id']);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getAll($array){

        try{

            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id = :user_id");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function changeUserPassword($array){

        try{

            $stmt = $this->db->prepare("UPDATE vl1_UserModel SET password = :new_password WHERE id=:user_id AND password = :old_password");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":new_password", Security::getPasswordHash($array['login'] . $array['new_password']));
            $stmt->bindValue(":old_password", Security::getPasswordHash($array['login'] . $array['old_password']));
            Logger::writeLog('New = ' . Security::getPasswordHash($array['new_password']) . "\n Old = " . Security::getPasswordHash($array['old_password']));
            $stmt->execute();
            return $stmt->rowCount();

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

}
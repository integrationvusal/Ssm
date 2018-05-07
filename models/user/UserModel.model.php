<?php

class UserModel extends CRUDModel{

    public $id;
    public $name;
    public $email;
    public $login;
    public $password;
    public $birthDate;
    public $description;
    public $image;

    private $tableName = "vl1_UserModel";

    protected $db;

    public function __construct(){
        $this->db = (new DB())->start();
    }

    public function createUser($array){
        try{
            $stmt = $this->db->prepare("INSERT INTO " . $this->tableName . "(`name`, email, login, password, birthdate, description, image)
            VALUES(:name,:email,:login,:password,:birthdate,:description,:image)");
            $stmt->bindValue(":name", $array['name']);
            $stmt->bindValue(":email", $array['email']);
            $stmt->bindValue(":login", $array['login']);
            $stmt->bindValue(":password", hash("sha256", $array['login'] . $array['password']));
            $stmt->bindValue(":birthdate", $array['birthdate']);
            $stmt->bindValue(":description", $array['description']);
            $stmt->bindValue(":image", $array['image']);
            return $stmt->execute();
        } catch(Exception $e) {
            return false;
        }
    }

    public function updateUser($array){
        if(isset($array['id']) && (int)$array['id'] > 0){
            try{
                $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET `name`=:name, email=:email, login=:login,
                birthdate=:birthdate, description=:description, image=:image WHERE id=:id");
                $stmt->bindValue(":id", $array['id']);
                $stmt->bindValue(":name", $array['name']);
                $stmt->bindValue(":email", $array['email']);
                $stmt->bindValue(":login", $array['login']);
                $stmt->bindValue(":birthdate", $array['birthdate']);
                $stmt->bindValue(":description", $array['description']);
                $stmt->bindValue(":image", $array['image']);
                return $stmt->execute();
            } catch(Exception $e) {
                return false;
            }
        }
        return false;
    }

    public function getByLoginPassword($login, $password){
        try{
            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE login=:login AND password=:password");
            $stmt->bindValue(":login", $login);
            $stmt->bindValue(":password", Security::getPasswordHash($login . $password));
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(Exception $e) {
            return false;
        }
    }

    public function getOperatorByLoginPassword($login, $password){

        try{

            $operatorModel = new OperatorModel();
            return $operatorModel->getByLoginAndPassword([
                'login' => $login,
                'password' => $password
            ]);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getOneById($id){
        try{
            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE id=:id");
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(Exception $e) {
            return false;
        }
    }

    public function getByAuthToken($token){

        try{

            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE authtoken=:token");
            $stmt->bindValue(":token", $token, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();

        } catch(Exception $e){

            return false;

        }

    }


}
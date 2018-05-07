<?php

class PermissionModel{

    private $tableName = "vl1_PermissionModel";
    protected $db;

    public function __construct(){
        $this->db = (new DB())->start();
    }

    public function createPermission($array){

        try{

            $this->db->beginTransaction();
            $stmt = $this->db->prepare("DELETE FROM " . $this->tableName . " WHERE user_id=:user_id AND operator_id=:operator_id");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":operator_id", $array['operator_id']);
            $stmt->execute();

            $permissionsSet = Application::$settings['permissions_set'];
            foreach($permissionsSet as $key => $set){
                $stmt = $this->db->prepare("INSERT INTO " . $this->tableName . "(user_id, operator_id, ps_key, val)
                VALUES(:user_id, :operator_id, :ps_key, :val)");
                if(!array_key_exists($key, $array)) $array[$key] = 0;
                if(is_array($array[$key])){
                    foreach($array[$key] as $k => $v){
                        $stmt->bindValue(":user_id", $array['user_id']);
                        $stmt->bindValue(":operator_id", $array['operator_id']);
                        $stmt->bindValue(":ps_key", $key);
                        $stmt->bindValue(":val", $k);
                        if(!$stmt->execute()) throw new Exception("Error while creating permission");
                    }
                } else {
                    $stmt->bindValue(":user_id", $array['user_id']);
                    $stmt->bindValue(":operator_id", $array['operator_id']);
                    $stmt->bindValue(":ps_key", $key);
                    $stmt->bindValue(":val", $array[$key]);
                    if(!$stmt->execute()) throw new Exception("Error while creating permission");
                }
            }

            $this->db->commit();
            return true;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            $this->db->rollBack();
            return false;

        }

    }

    public function getAll($array){

        $permissionsSet = Application::$settings['permissions_set'];
        if($array['user_id'] == $array['operator_id']){
            $user = (new UserModel())->getOneById($array['user_id']);
            $permissions = [];
            if($user['spc'] == 1) {
                $permissions = array();
                foreach($permissionsSet as $key => $val){
                    $permissions[$key] = 1;
                }
            } else {
                $permissions = (new UserPermissionModel)->getAll(['user_id' => $array['user_id']]);
            }
            return $permissions;
        }

        try{

            $stmt = $this->db->prepare("SELECT ps_key, val FROM " . $this->tableName . " WHERE user_id = :user_id AND operator_id = :operator_id");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":operator_id", $array['operator_id']);
            $stmt->execute();
            $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $n_permissions = array();
            foreach($permissions as $row){
                if(array_key_exists($row['ps_key'], $n_permissions)){
                    if(is_array($n_permissions[$row['ps_key']])){
                        $n_permissions[$row['ps_key']][] = $row['val'];
                    } else {
                        $n_permissions[$row['ps_key']] = array($n_permissions[$row['ps_key']]);
                        $n_permissions[$row['ps_key']][] = $row['val'];
                    }
                } else {
                    $n_permissions[$row['ps_key']] = $row['val'];
                }
            }

            foreach($permissionsSet as $k => $v){
                if(!array_key_exists($k, $n_permissions)) $n_permissions[$k] = 0;
            }

            if(!is_array($n_permissions['subject'])){
                if(!empty($n_permissions['subject'])){
                    $n_permissions['subject'] = array($n_permissions['subject']);
                } else {
                    $n_permissions['subject'] = array(0);
                }
            }

            $permissions = (new UserPermissionModel())->getAll($array);
            foreach ($n_permissions as $k => $ps){
                if(array_key_exists($k, $permissions)){
                    if(!$permissions[$k]) $n_permissions[$k] = 0;
                }
            }

            return $n_permissions;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

}
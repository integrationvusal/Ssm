<?php

class CategoryModel{

    public $id;
    public $user_id;
    public $goods_type;
    public $name;
    public $description;

    private $tableName = "vl1_CategoryModel";

    protected $db;

    public function __construct(){

        $this->db = (new DB())->start();

    }

    public function createCategory($array){
        try{

            $stmt = $this->db->prepare("INSERT INTO " . $this->tableName . "(user_id, goods_type, `name`, description, uindex)
            VALUES(:user_id, :goods_type, :name, :description, :uindex)");
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":goods_type", $array['goods_type'], PDO::PARAM_INT);
            $stmt->bindValue(":name", $array['name'], PDO::PARAM_STR);
            $stmt->bindValue(":description", $array['description'], PDO::PARAM_STR);
            $stmt->bindValue(":uindex", $array['user_id'] . $array['goods_type'] . $array['name'], PDO::PARAM_STR);
            return $stmt->execute();

        } catch(Exception $e) {

            return false;

        }
    }

    public function updateCategory($array){
        try{
            $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET name=:name, description=:description, uindex=:uindex WHERE id=:id AND user_id=:user_id");
            $stmt->bindValue(":name", $array['name'], PDO::PARAM_STR);
            $stmt->bindValue(":description", $array['description'], PDO::PARAM_STR);
            $stmt->bindValue(":uindex", $array['user_id'] . $array['goods_type'] . $array['name'], PDO::PARAM_STR);
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":id", $array['id'], PDO::PARAM_INT);
            return $stmt->execute();
        } catch(Exception $e) {
            return false;
        }
    }

    public function deleteCategory($array){
        try{

            $modelName = Application::$settings['goods_types'][$array['goods_type']]['model_name'];
            $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM " . $modelName::$tableName . " WHERE user_id=:user_id AND category_id=:category_id");
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":category_id", $array['category_id'], PDO::PARAM_INT);
            if($stmt->execute()){

                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                if(isset($res['cnt']) && (int)$res['cnt'] > 0) return 2;

                $stmt = $this->db->prepare("DELETE FROM " . $this->tableName . " WHERE id=:category_id AND user_id=:user_id");
                $stmt->bindValue(":category_id", $array['category_id'], PDO::PARAM_INT);
                $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
                if($stmt->execute()) return 1;
            }
            return 0;
        } catch(Exception $e) {
            Logger::writeExceptionLog($e);
            return 0;
        }
    }

    public function getOne($array){
        try{
            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . "
            WHERE id=:id AND user_id=:user_id AND goods_type=:goods_type");
            $stmt->bindValue(":id", $array['id'], PDO::PARAM_INT);
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":goods_type", $array['goods_type'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(Exception $e) {
            return false;
        }
    }

    public function getAll($array){

        try{

            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id=:user_id AND goods_type=:goods_type");
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":goods_type", $array['goods_type'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            return false;

        }

    }

}
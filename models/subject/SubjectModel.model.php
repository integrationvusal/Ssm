<?php

class SubjectModel{

    public $id;
    public $user_id;
    public $type;
    public $goods_type;
    public $name;
    public $description;

    private $tableName = "vl1_SubjectModel";

    protected $db;


    public function __construct(){
        $this->db = (new DB())->start();
    }

    public function getEmptySubject(){
        return [
            'id' => 0,
            'user_id' => 0,
            'type' => null,
            'goods_type' => null,
            'name' => null,
            'description' => null
        ];
    }

    public function getAll($array){

        try{

            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id=:user_id");
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getAllByType($array){

        try{

            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id=:user_id AND goods_type=:goods_type");
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":goods_type", $array['goods_type'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getOne($array){

        try{

            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id=:user_id AND id=:id");
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":id", $array['id'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function createSubject($array){
        try{

            $this->db->beginTransaction();

            $stmt = $this->db->prepare("INSERT INTO " . $this->tableName . "(user_id, type, goods_type, name, description)
            VALUES(:user_id, :type, :goods_type, :name, :description)");
            $stmt->bindValue(":user_id", $array['manager_id'], PDO::PARAM_INT);
            $stmt->bindValue(":type", $array['type'], PDO::PARAM_INT);
            $stmt->bindValue(":goods_type", $array['goods_type'], PDO::PARAM_INT);
            $stmt->bindValue(":name", $array['name'], PDO::PARAM_STR);
            $stmt->bindValue(":description", $array['description'], PDO::PARAM_STR);
            $stmt->execute();

            $subject_id = $this->db->lastInsertId();

            $createContragentData = [
                'subject_id' => $subject_id,
                'name' => ['Kontragentsiz'],
                'address' => 'spec1',
                'email' => '',
                'user_id' => $array['manager_id'],
                'description' => '',
                'prefix' => '',
                'phone' => '',
            ];

            if(!((new ContragentModel())->createContragent($createContragentData) && $stmt->rowCount())){
                $this->db->rollBack();
                return ['status' => 0, 'message' => 'Yeni obyekti əlavə etmək mümkün olmadı'];
            }

            $model = Application::$settings['goods_types'][$array['goods_type']]['model_name'];
            $formAttrs = $model::getFormAttrs();
            foreach($formAttrs as $key => $val){
                $formAttrs[$key] = 1;
            }
            $defaultFormAttrs = [
                'user_id' => $array['manager_id'],
                'subject_id' => $subject_id,
                'view_type' => 'form',
                'set' => $formAttrs
            ];
            $tableAttrs = $model::getTableAttrs();
            foreach($tableAttrs as $key => $val){
                $tableAttrs[$key] = 1;
            }
            $defaultTableAttrs = [
                'user_id' => $array['manager_id'],
                'subject_id' => $subject_id,
                'view_type' => 'table',
                'set' => $tableAttrs
            ];

            if((new ViewSettingModel())->saveProperties($defaultFormAttrs) && (new ViewSettingModel())->saveProperties($defaultTableAttrs)){
                $this->db->commit();
                return ['status' => 1, 'message' => 'Yeni obyekt əlavə olundu'];
            }
            $this->db->rollBack();
            return ['status' => 0, 'message' => 'Yeni obyekti əlavə etmək mümkün olmadı'];

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            $this->db->rollBack();
            return false;

        }
    }

    public function updateSubject($array){
        try{

            $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET type=:type, goods_type=:goods_type,
            name=:name, description=:description WHERE user_id = :user_id AND id = :subject_id");
            $stmt->bindValue(":user_id", $array['manager_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $array['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":type", $array['type'], PDO::PARAM_INT);
            $stmt->bindValue(":goods_type", $array['goods_type'], PDO::PARAM_INT);
            $stmt->bindValue(":name", $array['name'], PDO::PARAM_STR);
            $stmt->bindValue(":description", $array['description'], PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount()) return ['status' => 1, 'message' => 'Obyekt uğurla yeniləndi'];
            return ['status' => 1, 'message' => 'Obyekti yeniləmək mümükün olmadı'];

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }
    }

    public function getSearchAll($array){

        try{

            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE name LIKE :name AND user_id = :user_id");
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":name", '%' . $array['subject_search'] . '%', PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

}
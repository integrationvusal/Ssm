<?php

class ViewSettingModel{

    public $id;
    public $user_id;
    public $subject_id;
    public $view_type;
    public $prop;
    public $val;

    public static $tableName = "vl1_ViewSettingModel";

    protected $db;

    public function __construct(){
        $this->db = (new DB())->start();
    }

    public function getProperties($type, $subject){

        $model = Application::$settings['goods_types'][$subject['goods_type']]['model_name'];
        $static = [];
        $attrs = array();
        if($type == 'form'){
            $attrs = $model::getFormAttrs();
            if(method_exists($model, 'getStaticFormAttrs')){
                $static = $model::getStaticFormAttrs();
            }
        } elseif($type == 'table'){
            $attrs = $model::getTableAttrs();
            if(method_exists($model, 'getStaticTableAttrs')){
                $static = $model::getStaticTableAttrs();
            }
        } else {
            return false;
        }

        try{

            $stmt = $this->db->prepare("SELECT * FROM " . self::$tableName . " WHERE view_type = :type AND subject_id = :subject_id AND user_id = :user_id");
            $stmt->bindValue(":type", $type, PDO::PARAM_STR);
            $stmt->bindValue(":subject_id", $subject['id'], PDO::PARAM_INT);
            $stmt->bindValue(":user_id", $subject['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            $tmp = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $tmp = Utils::remap('prop', 'val', $tmp);

            foreach($attrs as $key => $attr){

                if(!array_key_exists($key, $tmp)) $tmp[$key] = 0;
                if(!array_key_exists($key, $static)) $static[$key] = false;
                $attrs[$key] = ['title' => $attr, 'val' => $tmp[$key], 'static' => $static[$key]];

            }

            return $attrs;


        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function saveProperties($data){

        try{

            $this->db->beginTransaction();

            $stmt = $this->db->prepare("DELETE FROM " . self::$tableName . " WHERE view_type = :type AND subject_id = :subject_id AND user_id = :user_id");
            $stmt->bindValue(":type", $data['view_type'], PDO::PARAM_STR);
            $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->db->prepare("INSERT INTO " . self::$tableName . "(user_id, subject_id, view_type, prop, val)
            VALUES(:user_id, :subject_id, :view_type, :prop, :val)");

            foreach($data['set'] as $k => $v){
                $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
                $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
                $stmt->bindValue(":view_type", $data['view_type'], PDO::PARAM_STR);
                $stmt->bindValue(":prop", $k, PDO::PARAM_STR);
                $stmt->bindValue(":val", $v, PDO::PARAM_INT);
                $stmt->execute();
            }

            $this->db->commit();
            return true;


        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            $this->db->rollBack();
            return false;

        }

    }

}
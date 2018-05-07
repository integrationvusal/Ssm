<?php

class CurrencyModel{

    public $id;
    public $name;
    public $value;

    private $tableName = "vl1_CurrencyModel";

    protected $db;

    public function __construct(){

        $this->db = (new DB())->start();

    }

    public function create($array){
        try{

            $stmt = $this->db->prepare("INSERT INTO " . $this->tableName . "(name, value)
            VALUES(:name, :value)");
            $stmt->bindValue(":name", $array['name'], PDO::PARAM_STR);
            $stmt->bindValue(":value", $array['value'], PDO::PARAM_STR);
            return $stmt->execute();

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }
    }

    public function update($array){
        try{
            $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET value=:value WHERE id=:data_id");
            $stmt->bindValue(":value", $array['value'], PDO::PARAM_INT);
            $stmt->bindValue(":data_id", $array['data_id'], PDO::PARAM_INT);
            return $stmt->execute();
        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;
        }
    }

    public function delete($array){
        try{

            $this->db->beginTransaction();
            $stmt = $this->db->prepare("DELETE FROM " . $this->tableName . " WHERE id=:currency_id");
            $stmt->bindValue(":currency_id", $array['currency_id'], PDO::PARAM_INT);
            $stmt->execute();

            $this->db->commit();
            return true;

        } catch(Exception $e) {
            Logger::writeExceptionLog($e);
            $this->db->rollBack();
            return false;
        }
    }

    public function getOne($array){
        try{
            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . "
            WHERE id=:currency_id");
            $stmt->bindValue(":currency_id", (isset($array['currency_id'])?$array['currency_id']:$array['currency']), PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(Exception $e) {
            Logger::writeExceptionLog($e);
            return false;
        }
    }

    public function getAll($limit = 1000000, $offset = 0){
        try{

            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " LIMIT :limit OFFSET :offset");
            $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(":offset",(int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getAllWithAZN($limit = 1000000, $offset = 0){
        $res = $this->getAll($limit, $offset);
        array_unshift($res, ['id'=>0, 'name'=> 'AZN', 'value'=> 0]);
        return $res;
    }

    public function getAllCount(){

        try{

            $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM " . $this->tableName);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res['cnt'];

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getNoticesForUser($user){

        try{

            $currentDate = date("Y-m-d H:i:s");
            if($user['type'] == 0){
                $stmt = $this->db->prepare("SELECT notice.* FROM " . $this->tableName . " AS notice
                LEFT JOIN " . $this->secondTable . " AS views ON notice.id = views.notice_id AND views.user_id = :user_id
                WHERE :currentDate BETWEEN notice.start_date AND '3000-00-00' AND views.notice_id IS NULL");
                $stmt->bindValue(":user_id", $user['id']);
            } else {
                $stmt = $this->db->prepare("SELECT notice.* FROM " . $this->tableName . " AS notice
                LEFT JOIN " . $this->secondTable . " AS views ON notice.id = views.notice_id AND views.operator_id = :operator_id
                WHERE :currentDate BETWEEN notice.start_date AND '3000-00-00' AND views.notice_id IS NULL");
                $stmt->bindValue(":operator_id", $user['operator']['id']);
            }
            $stmt->bindValue(":currentDate", $currentDate);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function expireForUser($array){

        try{

            $currentDate = date("Y-m-d H:i:s");
            if($array['user']['type'] == 0){
                $stmt = $this->db->prepare("INSERT INTO " . $this->secondTable . "(notice_id, user_id)
                VALUES(:notice_id, :user_id)");
                $stmt->bindValue(":user_id", $array['user']['id']);
            } else {
                $stmt = $this->db->prepare("INSERT INTO " . $this->secondTable . "(notice_id, operator_id)
                VALUES(:notice_id, :operator_id)");
                $stmt->bindValue(":operator_id", $array['user']['operator']['id']);
            }
            $stmt->bindValue(":notice_id", $array['notice_id']);
            return $stmt->execute();

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

}
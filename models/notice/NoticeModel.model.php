<?php

class NoticeModel{

    public $id;
    public $title;
    public $content;
    public $start_date;
    public $create_date;

    private $tableName = "vl1_NoticeModel";
    private $secondTable = "vl1_NoticesViewedBy";

    protected $db;

    public function __construct(){

        $this->db = (new DB())->start();

    }

    public function createNotice($array){
        try{

            $stmt = $this->db->prepare("INSERT INTO " . $this->tableName . "(title, content, start_date)
            VALUES(:title, :content, :start_date)");
            $stmt->bindValue(":title", $array['title'], PDO::PARAM_STR);
            $stmt->bindValue(":content", $array['content'], PDO::PARAM_STR);
            $stmt->bindValue(":start_date", $array['start_date'], PDO::PARAM_STR);
            return $stmt->execute();

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }
    }

    public function updateNotice($array){
        try{
            $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET title=:title, content=:content, start_date=:start_date WHERE id=:notice_id");
            $stmt->bindValue(":title", $array['title'], PDO::PARAM_STR);
            $stmt->bindValue(":content", $array['content'], PDO::PARAM_STR);
            $stmt->bindValue(":start_date", $array['start_date'], PDO::PARAM_STR);
            $stmt->bindValue(":notice_id", $array['notice_id'], PDO::PARAM_INT);
            return $stmt->execute();
        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;
        }
    }

    public function deleteNotice($array){
        try{

            $this->db->beginTransaction();
            $stmt = $this->db->prepare("DELETE FROM " . $this->tableName . " WHERE id=:notice_id");
            $stmt->bindValue(":notice_id", $array['notice_id'], PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->db->prepare("DELETE FROM " . $this->secondTable . " WHERE notice_id=:notice_id");
            $stmt->bindValue(":notice_id", $array['notice_id'], PDO::PARAM_INT);
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
            WHERE id=:notice_id");
            $stmt->bindValue(":notice_id", $array['notice_id'], PDO::PARAM_INT);
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
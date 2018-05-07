<?php

class ReceiptAttributesModel{

    public $id;
    public $user_id;
    public $subject_id;
    public $company_logo;
    public $company_name;
    public $company_voen;
    public $company_address;
    public $other_top;
    public $other_bottom;

    private $db;
    protected $tableName = "vl1_ReceiptAttributesModel";

    public function __construct(){

        $this->db = (new DB())->start();

    }

    public function create($data){

        try{

            if(array_key_exists('attribute_id', $data)){
                if(empty($_FILES['company_logo']['error'])) {
                    $imagePath = Utils::uploadImage("public" . ds . "user" . $data['user_id'] . ds .  "logo", "company_logo");
                } else {
                    $imagePath = $data['old_logo'];
                }

                $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET company_logo = :company_logo,company_name = :company_name,company_voen = :company_voen,company_address = :company_address,other_top = :other_top,other_bottom = :other_bottom
                WHERE user_id = :user_id AND subject_id = :subject_id AND id = :attribute_id");
                $stmt->bindValue("user_id", $data['user_id'], PDO::PARAM_INT);
                $stmt->bindValue("subject_id", $data['subject_id'], PDO::PARAM_INT);
                $stmt->bindValue("company_logo", $imagePath, PDO::PARAM_STR);
                $stmt->bindValue("company_name", $data['company_name'], PDO::PARAM_STR);
                $stmt->bindValue("company_voen", $data['company_voen'], PDO::PARAM_STR);
                $stmt->bindValue("company_address", $data['company_address'], PDO::PARAM_STR);
                $stmt->bindValue("other_top", trim($data['other_top']), PDO::PARAM_STR);
                $stmt->bindValue("other_bottom", trim($data['other_bottom']), PDO::PARAM_STR);
                $stmt->bindValue("attribute_id", $data['attribute_id'], PDO::PARAM_INT);
                return $stmt->execute();
            } else {
                $imagePath = Utils::uploadImage("public" . ds . "user" . $data['user_id'] . ds .  "logo", "company_logo");

                $stmt = $this->db->prepare("INSERT INTO " . $this->tableName . "(user_id, subject_id, company_logo, company_name, company_voen, company_address, other_top, other_bottom) VALUES(:user_id, :subject_id, :company_logo, :company_name, :company_voen, :company_address, :other_top, :other_bottom)");
                $stmt->bindValue("user_id", $data['user_id'], PDO::PARAM_INT);
                $stmt->bindValue("subject_id", $data['subject_id'], PDO::PARAM_INT);
                $stmt->bindValue("company_logo", $imagePath, PDO::PARAM_STR);
                $stmt->bindValue("company_name", $data['company_name'], PDO::PARAM_STR);
                $stmt->bindValue("company_voen", $data['company_voen'], PDO::PARAM_STR);
                $stmt->bindValue("company_address", $data['company_address'], PDO::PARAM_STR);
                $stmt->bindValue("other_top", trim($data['other_top']), PDO::PARAM_STR);
                $stmt->bindValue("other_bottom", trim($data['other_bottom']), PDO::PARAM_STR);
                return $stmt->execute();
            }

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function get($data){

        try{

            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id = :user_id AND subject_id = :subject_id");
            $stmt->bindValue("user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue("subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function __destruct(){

        $this->db = null;

    }

}
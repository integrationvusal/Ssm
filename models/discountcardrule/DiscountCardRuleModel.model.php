<?php

class DiscountCardRuleModel{

    public $id;
    public $user_id;
    public $subject_id;
    public $rule_name;
    public $card_type;
    public $clear_on_expire;
    public $save_remaining;

    private $tableName = "vl1_DiscountCardRuleModel";
    private $relatedTableName = "vl1_DiscountRuleModel";

    protected $db;

    public function __construct(){
        $this->db = (new DB())->start();
    }

    public function createDiscountCardRule($data){

        try{

            $this->db->beginTransaction();
            $stmt = $this->db->prepare("INSERT INTO " . $this->tableName . "(user_id, subject_id, rule_name, card_type, clear_on_expire, save_remaining, currency, currency_archive)
             VALUES(:user_id, :subject_id, :rule_name, :card_type, :clear_on_expire, :save_remaining, :currency, :currency_archive)");
            $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":rule_name", $data['rule_name'], PDO::PARAM_STR);
            $stmt->bindValue(":card_type", $data['card_type'], PDO::PARAM_STR);
            $stmt->bindValue(":currency", $data['currency'], PDO::PARAM_INT);
            $stmt->bindValue(":currency_archive", $data['currency_archive'], PDO::PARAM_STR);
            $stmt->bindValue(":clear_on_expire", array_key_exists("clear_on_expire", $data) ? $data['clear_on_expire'] : 0, PDO::PARAM_INT);
            $stmt->bindValue(":save_remaining", array_key_exists("save_remaining", $data) ? $data['save_remaining'] : 0, PDO::PARAM_INT);
            $stmt->execute();

            $rule_id = $this->db->lastInsertId();

            $stmt = $this->db->prepare("INSERT INTO " . $this->relatedTableName . "(rule_id, rule_type, first_param, second_param)
             VALUES(:rule_id, :rule_type, :first_param, :second_param)");
            foreach($data['rule_type'] as $key => $rule_type){
                $stmt->bindValue(":rule_id", $rule_id, PDO::PARAM_INT);
                $stmt->bindValue(":rule_type", $rule_type, PDO::PARAM_STR);
                $stmt->bindValue(":first_param", $data['first_param'][$key], PDO::PARAM_STR);
                $stmt->bindValue(":second_param", $data['second_param'][$key], PDO::PARAM_STR);
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

    public function updateDiscountCardRule($data){

        try{

            $this->db->beginTransaction();
            $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET rule_name = :rule_name, card_type = :card_type,
            clear_on_expire = :clear_on_expire, save_remaining = :save_remaining, currency = :currency, currency_archive = :currency_archive
            WHERE user_id = :user_id AND subject_id = :subject_id AND id = :rule_id");
            $stmt->bindValue(":rule_id", $data['rule_id'], PDO::PARAM_INT);
            $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":rule_name", $data['rule_name'], PDO::PARAM_STR);
            $stmt->bindValue(":card_type", $data['card_type'], PDO::PARAM_STR);
            $stmt->bindValue(":currency", $data['currency'], PDO::PARAM_INT);
            $stmt->bindValue(":currency_archive", $data['currency_archive'], PDO::PARAM_STR);
            $stmt->bindValue(":clear_on_expire", array_key_exists("clear_on_expire", $data) ? $data['clear_on_expire'] : 0, PDO::PARAM_INT);
            $stmt->bindValue(":save_remaining", array_key_exists("save_remaining", $data) ? $data['save_remaining'] : 0, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->db->prepare("DELETE FROM " . $this->relatedTableName . " WHERE rule_id = :rule_id");
            $stmt->bindValue(":rule_id", $data['rule_id'], PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->db->prepare("INSERT INTO " . $this->relatedTableName . "(rule_id, rule_type, first_param, second_param)
             VALUES(:rule_id, :rule_type, :first_param, :second_param)");
            foreach($data['rule_type'] as $key => $rule_type){
                $stmt->bindValue(":rule_id", $data['rule_id'], PDO::PARAM_INT);
                $stmt->bindValue(":rule_type", $rule_type, PDO::PARAM_STR);
                $stmt->bindValue(":first_param", $data['first_param'][$key], PDO::PARAM_STR);
                $stmt->bindValue(":second_param", $data['second_param'][$key], PDO::PARAM_STR);
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

    public function isExists($data){

        try{

            $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM vl1_DiscountCardModel WHERE user_id = :user_id AND subject_id = :subject_id AND rule_id = :rule_id");
            $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":rule_id", $data['rule_id'], PDO::PARAM_INT);
            $stmt->execute();
            $rule = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($rule['cnt'] > 0);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function deleteDiscountCardRule($data){

        try{

            $this->db->beginTransaction();

            $stmt = $this->db->prepare("DELETE FROM " . $this->relatedTableName . " WHERE rule_id = :rule_id");
            $stmt->bindValue(":rule_id", $data['rule_id'], PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->db->prepare("DELETE FROM " . $this->tableName . " WHERE user_id = :user_id AND subject_id = :subject_id AND id = :rule_id");
            $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":rule_id", $data['rule_id'], PDO::PARAM_INT);
            $stmt->execute();

            return $this->db->commit();

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            $this->db->rollBack();
            return false;

        }

    }

    public function getOne($data){

        try{

            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id = :user_id AND subject_id = :subject_id AND id = :rule_id");
            $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":rule_id", $data['rule_id'], PDO::PARAM_INT);
            $stmt->execute();
            $rule = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $this->db->prepare("SELECT * FROM " . $this->relatedTableName . " WHERE rule_id = :rule_id AND rule_type = 'plus'");
            $stmt->bindValue(":rule_id", $rule['id'], PDO::PARAM_INT);
            $stmt->execute();
            $rule['plus_rules'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $this->db->prepare("SELECT * FROM " . $this->relatedTableName . " WHERE rule_id = :rule_id AND rule_type = 'minus'");
            $stmt->bindValue(":rule_id", $rule['id'], PDO::PARAM_INT);
            $stmt->execute();
            $rule['minus_rules'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $rule;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getAllByType($data){

        try{

            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id = :user_id AND subject_id = :subject_id AND card_type = :card_type");
            $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":card_type", $data['card_type'], PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getAll($data){

        try{

            $stmt = $this->db->prepare("SELECT s.*, IFNULL(cur.name, 'AZN') currency FROM " . $this->tableName . " s
                LEFT JOIN vl1_CurrencyModel cur ON cur.id = s.currency
                WHERE user_id = :user_id AND subject_id = :subject_id");
            $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

}
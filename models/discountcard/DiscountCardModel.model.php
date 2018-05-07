<?php

class DiscountCardModel{

    public $id;
    public $user_id;
    public $subject_id;
    public $card_number;
    public $card_type;
    public $rule_id;
    public $client_name;
    public $client_surname;
    public $client_patronymic;
    public $client_phone;
    public $client_description;
    public $discount;
    public $bonus;
    public $remaining_amount;
    public $created_at;
    public $expire_at;

    private $tableName = "vl1_DiscountCardModel";

    protected $db;

    public function __construct(){
        $this->db = (new DB())->start();
    }

    public function createDiscountCard($data){

        try{

            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id = :user_id AND subject_id = :subject_id AND card_number = :card_number");
            $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":card_number", $data['card_number'], PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if(!empty($result)) return false;

            $rules = [];
            if($data['card_type'] == 'discount'){
                $stmt = $this->db->prepare("SELECT * FROM vl1_DiscountRuleModel WHERE rule_id = :rule_id");
                $stmt->bindValue(":rule_id", $data['rule_id'], PDO::PARAM_INT);
                $stmt->execute();
                $rules = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            $stmt = $this->db->prepare("INSERT INTO " . $this->tableName . "(user_id, subject_id, card_number,
            card_type, rule_id, client_name, client_surname, client_patronymic,
            client_phone, client_description, created_at, expire_at, discount)
             VALUES(:user_id, :subject_id, :card_number, :card_type, :rule_id, :client_name, :client_surname,
             :client_patronymic, :client_phone, :client_description, :created_at, :expire_at, :discount)");

            $discount = 0;
            if(count($rules) > 0 && $rules[0]['first_param'] == 0) $discount = $rules[0]['second_param'];

            $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":card_number", $data['card_number'], PDO::PARAM_STR);
            $stmt->bindValue(":card_type", $data['card_type'], PDO::PARAM_STR);
            $stmt->bindValue(":rule_id", $data['rule_id'], PDO::PARAM_INT);
            $stmt->bindValue(":client_name", $data['client_name'], PDO::PARAM_STR);
            $stmt->bindValue(":client_surname", $data['client_surname'], PDO::PARAM_STR);
            $stmt->bindValue(":client_patronymic", $data['client_patronymic'], PDO::PARAM_STR);
            $stmt->bindValue(":client_phone", $data['client_phone'], PDO::PARAM_STR);
            $stmt->bindValue(":client_description", $data['client_description'], PDO::PARAM_STR);
            $stmt->bindValue(":created_at", date("Y-m-d H:i:s"), PDO::PARAM_STR);
            $stmt->bindValue(":expire_at", $data['expire_at'], PDO::PARAM_STR);
            $stmt->bindValue(":discount", $discount, PDO::PARAM_STR);
            return $stmt->execute();

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function updateDiscountCard($data){

        try{

            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id = :user_id AND subject_id = :subject_id AND card_number = :card_number AND id != :card_id");
            $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":card_id", $data['card_id'], PDO::PARAM_INT);
            $stmt->bindValue(":card_number", $data['card_number'], PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if(!empty($result)) return false;

            if($data['card_type'] == 'bonus'){
                $data['discount'] = 0;
                $data['remaining_amount'] = 0;
            } elseif($data['card_type'] == 'discount'){
                $data['bonus'] = 0;
                $data['remaining_amount'] = 0;
            }

            $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET
            card_number = :card_number,
            card_type = :card_type,
            rule_id = :rule_id,
            client_name = :client_name,
            client_surname = :client_surname,
            client_patronymic = :client_patronymic,
            client_phone = :client_phone,
            client_description = :client_description,
            discount = :discount,
            bonus = :bonus,
            remaining_amount = :remaining_amount,
            expire_at = :expire_at,
            card_status = 1
            WHERE user_id = :user_id AND subject_id = :subject_id AND id = :card_id");
            $stmt->bindValue(":card_number", $data['card_number'], PDO::PARAM_STR);
            $stmt->bindValue(":card_type", $data['card_type'], PDO::PARAM_STR);
            $stmt->bindValue(":rule_id", $data['rule_id'], PDO::PARAM_INT);
            $stmt->bindValue(":client_name", $data['client_name'], PDO::PARAM_STR);
            $stmt->bindValue(":client_surname", $data['client_surname'], PDO::PARAM_STR);
            $stmt->bindValue(":client_patronymic", $data['client_patronymic'], PDO::PARAM_STR);
            $stmt->bindValue(":client_phone", $data['client_phone'], PDO::PARAM_STR);
            $stmt->bindValue(":client_description", $data['client_description'], PDO::PARAM_STR);
            $stmt->bindValue(":discount", $data['discount'], PDO::PARAM_STR);
            $stmt->bindValue(":bonus", $data['bonus'], PDO::PARAM_STR);
            $stmt->bindValue(":remaining_amount", $data['remaining_amount'], PDO::PARAM_STR);
            $stmt->bindValue(":expire_at", $data['expire_at'], PDO::PARAM_STR);
            $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":card_id", $data['card_id'], PDO::PARAM_INT);
            return $stmt->execute();

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function deleteDiscountCard($data){

        try{

            $stmt = $this->db->prepare("DELETE FROM " . $this->tableName . " WHERE user_id = :user_id AND subject_id = :subject_id AND id = :card_id");
            $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":card_id", $data['card_id'], PDO::PARAM_INT);
            return $stmt->execute();

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function checkExpired($data){

        $date_time = new DateTime();
        $date_time->setTimezone(new DateTimeZone('Asia/Baku'));
        try{
            $stmt = $this->db->prepare("SELECT card.*, rule.clear_on_expire FROM " . $this->tableName . " AS card
            LEFT JOIN vl1_DiscountCardRuleModel AS rule ON card.rule_id = rule.id
            WHERE card.user_id = :user_id AND card.subject_id = :subject_id AND card.expire_at BETWEEN :date_from AND :date_to AND card.card_status = 1");
            $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":date_from", '1970-01-01 00:00:00', PDO::PARAM_STR);
            $stmt->bindValue(":date_to", $date_time->format("Y-m-d H:i:s"), PDO::PARAM_STR);
            $stmt->execute();
            $expired_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $dchm = new DiscountCardHistoryModel();
            foreach($expired_cards as $ec){
                if($ec['clear_on_expire'] == 1){
                    $ustmt = $this->db->prepare("UPDATE " . $this->tableName . " SET bonus = 0, discount = 0, remaining_amount = 0, card_status = 0  WHERE user_id = :user_id AND subject_id = :subject_id AND id = :card_id");
                } else {
                    $ustmt = $this->db->prepare("UPDATE " . $this->tableName . " SET card_status = 0 WHERE user_id = :user_id AND subject_id = :subject_id AND id = :card_id");
                }
                $ustmt->bindValue(":user_id", $ec['user_id'], PDO::PARAM_INT);
                $ustmt->bindValue(":subject_id", $ec['subject_id'], PDO::PARAM_INT);
                $ustmt->bindValue(":card_id", $ec['id'], PDO::PARAM_INT);
                $ustmt->execute();

                $tmp_data = $data;
                $tmp_data['card_number'] = $ec['card_number'];
                $tmp_data['operation_type'] = '0';
                $tmp_data['previous_discount'] = $ec[$ec['card_type']];
                $tmp_data['current_discount'] = 0;
                $tmp_data['amount'] = 0;
                $tmp_data['discounted_amount'] = 0;
                $tmp_data['remaining_amount'] = 0;

                $dchm->addHistoryElement($tmp_data, $this->db);
            }
            return true;

        } catch(Exception $e) {
            Logger::writeExceptionLog($e);
            return false;
        }
    }

    public function getOne($data){

        try{

            $this->checkExpired($data);
            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id = :user_id AND subject_id = :subject_id AND id = :card_id");
            $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":card_id", $data['card_id'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getCardInfoByNumber($data){

        try{

            $this->checkExpired($data);
            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id = :user_id AND subject_id = :subject_id AND card_number = :card_number");
            $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":card_number", $data['card_number'], PDO::PARAM_STR);
            $stmt->execute();
            $card = $stmt->fetch(PDO::FETCH_ASSOC);
            if($card['card_type'] == 'discount'){
                return $card;
            } elseif($card['card_type'] == 'bonus') {
                $rule = (new DiscountCardRuleModel())->getOne(['rule_id' => $card['rule_id'], 'user_id' => $data['user_id'], 'subject_id' => $data['subject_id']]);
                $card['rule'] = $rule;
                $card['bonus_per'] = $rule['minus_rules'][0]['second_param'];
                return $card;
            }
            return false;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }


    public function getAll($data, $limit, $offset){

        try{

            $this->checkExpired($data);
            $stmt = $this->db->prepare("SELECT card.*, rule.rule_name AS rule_name FROM " . $this->tableName . " AS card
            LEFT JOIN vl1_DiscountCardRuleModel AS rule ON card.rule_id = rule.id
            WHERE card.user_id = :user_id AND card.subject_id = :subject_id LIMIT :limit OFFSET :offset");
            $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }
    }

    public function getCountAll($data){

        try{
            $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM " . $this->tableName . " WHERE user_id = :user_id AND subject_id = :subject_id");
            $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res['cnt'];
        } catch(Exception $e) {
            Logger::writeExceptionLog($e);
            return false;
        }

    }

    public function searchAll($data, $limit, $offset){

        try{

            $this->checkExpired($data);
            $stmt = $this->db->prepare("SELECT card.*, rule.rule_name AS rule_name FROM " . $this->tableName . " AS card
            LEFT JOIN vl1_DiscountCardRuleModel AS rule ON card.rule_id = rule.id
            WHERE
            card.user_id = :user_id
            AND card.subject_id = :subject_id
            AND card.card_number LIKE :card_number
            AND card.card_type LIKE :card_type
            AND CONCAT_WS(' ', card.client_name, card.client_surname, card.client_patronymic) LIKE :client_name
            AND card.client_phone LIKE :client_phone
            AND (card.discount LIKE :bonus_or_discount OR card.bonus LIKE :bonus_or_discount)
            AND card.remaining_amount LIKE :remaining_amount
            AND card.created_at LIKE :created_at
            AND card.expire_at LIKE :expire_at
            LIMIT :limit OFFSET :offset");
            $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);

            $stmt->bindValue(":card_number", '%' . $data['card_number'] . '%', PDO::PARAM_STR);
            $stmt->bindValue(":card_type", $data['card_type'], PDO::PARAM_STR);
            $stmt->bindValue(":client_name", '%' . $data['client_name'] . '%', PDO::PARAM_STR);
            $stmt->bindValue(":client_phone", '%' . $data['client_phone'] . '%', PDO::PARAM_STR);
            $stmt->bindValue(":bonus_or_discount", '%' . $data['bonus_or_discount'] . '%', PDO::PARAM_STR);
            $stmt->bindValue(":remaining_amount", '%' . $data['remaining_amount'] . '%', PDO::PARAM_STR);
            $stmt->bindValue(":created_at", '%' . $data['created_at'] . '%', PDO::PARAM_STR);
            $stmt->bindValue(":expire_at", '%' . $data['expire_at'] . '%', PDO::PARAM_STR);
            $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }
    }

    public function countSearchAll($data){

        try{
            $stmt = $this->db->prepare("SELECT COUNT(card.id) AS cnt FROM " . $this->tableName . " AS card
            LEFT JOIN vl1_DiscountCardRuleModel AS rule ON card.rule_id = rule.id
            WHERE
            card.user_id = :user_id
            AND card.subject_id = :subject_id
            AND card.card_number LIKE :card_number
            AND card.card_type LIKE :card_type
            AND CONCAT_WS(' ', card.client_name, card.client_surname, card.client_patronymic) LIKE :client_name
            AND card.client_phone LIKE :client_phone
            AND (card.discount LIKE :bonus_or_discount OR card.bonus LIKE :bonus_or_discount)
            AND card.remaining_amount LIKE :remaining_amount
            AND card.created_at LIKE :created_at
            AND card.expire_at LIKE :expire_at");
            $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);

            $stmt->bindValue(":card_number", '%' . $data['card_number'] . '%', PDO::PARAM_STR);
            $stmt->bindValue(":card_type", $data['card_type'], PDO::PARAM_STR);
            $stmt->bindValue(":client_name", '%' . $data['client_name'] . '%', PDO::PARAM_STR);
            $stmt->bindValue(":client_phone", '%' . $data['client_phone'] . '%', PDO::PARAM_STR);
            $stmt->bindValue(":bonus_or_discount", '%' . $data['bonus_or_discount'] . '%', PDO::PARAM_STR);
            $stmt->bindValue(":remaining_amount", '%' . $data['remaining_amount'] . '%', PDO::PARAM_STR);
            $stmt->bindValue(":created_at", '%' . $data['created_at'] . '%', PDO::PARAM_STR);
            $stmt->bindValue(":expire_at", '%' . $data['expire_at'] . '%', PDO::PARAM_STR);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res['cnt'];
        } catch(Exception $e) {
            Logger::writeExceptionLog($e);
            return false;
        }

    }

    public function process($data, $db){

        try{
            $discountCard = $this->getCardInfoByNumber($data);
            if(!$discountCard) return false;

            if($discountCard['card_type'] == 'discount'){
                $data['rule_id'] = $discountCard['rule_id'];

                $rules = (new DiscountCardRuleModel())->getOne($data);
                $rules = $rules['plus_rules'];
                $expected_amount = $discountCard['remaining_amount'] + $data['amount'];
                $new_discount = $discountCard['discount'];
                $max_amount = 0;
                foreach($rules as $rule){
                    if(($expected_amount >= $rule['first_param']) && ($rule['first_param'] >= $max_amount)){
                        $max_amount = $rule['first_param'];
                        $new_discount = $rule['second_param'];
                    }
                }

                $stmt = $db->prepare("UPDATE " . $this->tableName . " SET discount=:discount, remaining_amount=:remaining_amount
                WHERE user_id=:user_id AND subject_id=:subject_id AND id=:card_id");
                $stmt->bindValue(":discount", $new_discount, PDO::PARAM_STR);
                $stmt->bindValue(":remaining_amount", $expected_amount, PDO::PARAM_STR);
                $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
                $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
                $stmt->bindValue(":card_id", $discountCard['id'], PDO::PARAM_INT);
                $stmt->execute();
                $data['previous_discount'] = $discountCard['discount'];
                $data['current_discount'] = $new_discount;
                $data['remaining_amount'] = $expected_amount;
                return (new DiscountCardHistoryModel())->addHistoryElement($data, $db);

            } elseif($discountCard['card_type'] == 'bonus') {

                $data['rule_id'] = $discountCard['rule_id'];
                $rule = (new DiscountCardRuleModel())->getOne($data);
                $minus_rule = $rule['minus_rules'][0];
                $plus_rule = $rule['plus_rules'][0];
                $save_remaining = $rule['save_remaining'];

                $dec_bonus = $discountCard['bonus'] - $data['discount_or_bonus'];
                $remaining_amount = $discountCard['remaining_amount'] + $data['amount'];

                $data['previous_discount'] = $discountCard['bonus'];
                $data['current_discount'] = $dec_bonus;
                $data['remaining_amount'] = $remaining_amount;
                $data['operation_type'] = '-';
                $ret_arr['minus'] = (new DiscountCardHistoryModel())->addHistoryElement($data, $db);

                $dif = intval($remaining_amount / $plus_rule['first_param']);
                $inc_bonus = $dec_bonus + $dif;
                if($save_remaining == 1) $remaining_amount = $remaining_amount - ($dif * $plus_rule['first_param']);
                else $remaining_amount = 0;

                $data['previous_discount'] = $dec_bonus;
                $data['current_discount'] = $inc_bonus;
                $data['remaining_amount'] = $remaining_amount;
                $data['operation_type'] = '+';
                $ret_arr['plus'] = (new DiscountCardHistoryModel())->addHistoryElement($data, $db);

                $stmt = $db->prepare("UPDATE " . $this->tableName . " SET bonus=:bonus, remaining_amount=:remaining_amount
                WHERE user_id=:user_id AND subject_id=:subject_id AND id=:card_id");
                $stmt->bindValue(":bonus", $inc_bonus, PDO::PARAM_STR);
                $stmt->bindValue(":remaining_amount", $remaining_amount, PDO::PARAM_STR);
                $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
                $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
                $stmt->bindValue(":card_id", $discountCard['id'], PDO::PARAM_INT);
                $stmt->execute();

                return $ret_arr;
            }
            return false;

        } catch(Exception $e) {
            throw $e;
        }

    }

}
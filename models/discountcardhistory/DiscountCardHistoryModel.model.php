<?php

class DiscountCardHistoryModel{

    public $id;
    public $user_id;
    public $subject_id;
    public $invoice_id;
    public $card_id;
    public $card_number;
    public $card_user;
    public $card_type;
    public $operation_type;
    public $previous_discount;
    public $current_discount;
    public $total_amount;
    public $discounted_amount;
    public $remaining_amount;
    public $created_at;

    private $tableName = "vl1_DiscountCardHistoryModel";

    protected $db;

    public function __construct(){
        $this->db = (new DB())->start();
    }

    public function addHistoryElement($data, $db){
        try{
            $discountCard = (new DiscountCardModel())->getCardInfoByNumber($data);
            if(!$discountCard) return false;
            if($discountCard['card_type'] == 'discount'){

                $stmt = $db->prepare("INSERT INTO " . $this->tableName . "(user_id, subject_id, invoice_id, card_id, card_number, card_user, card_type, operation_type, previous_discount, current_discount, total_amount, discounted_amount, remaining_amount, created_at, currency, currency_archive)
                VALUES(:user_id, :subject_id, :invoice_id, :card_id, :card_number, :card_user, :card_type, :operation_type, :previous_discount, :current_discount, :total_amount, :discounted_amount, :remaining_amount, :created_at, :currency, :currency_archive)");
                $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
                $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
                $stmt->bindValue(":invoice_id", array_key_exists('invoice_id', $data) ? $data['invoice_id'] : 0, PDO::PARAM_INT);
                $stmt->bindValue(":card_id", $discountCard['id'], PDO::PARAM_INT);
                $stmt->bindValue(":card_number", $discountCard['card_number'], PDO::PARAM_STR);
                $stmt->bindValue(":card_user", $discountCard['client_name'] . " " . $discountCard['client_surname'] . " " . $discountCard['client_patronymic'] . "(" . $discountCard['created_at'] . ")", PDO::PARAM_STR);
                $stmt->bindValue(":card_type", $discountCard['card_type'], PDO::PARAM_STR);
                $stmt->bindValue(":operation_type", '+', PDO::PARAM_STR);
                $stmt->bindValue(":previous_discount", $data['previous_discount'], PDO::PARAM_STR);
                $stmt->bindValue(":current_discount", $data['current_discount'], PDO::PARAM_STR);
                $stmt->bindValue(":total_amount", $data['amount'], PDO::PARAM_STR);
                $stmt->bindValue(":discounted_amount", $data['discounted_amount'], PDO::PARAM_STR);
                $stmt->bindValue(":remaining_amount", $data['remaining_amount'], PDO::PARAM_STR);
                $stmt->bindValue(":created_at", Utils::getCurrentDate(), PDO::PARAM_STR);
                $stmt->bindValue(":currency", $data['currency'], PDO::PARAM_INT);
                $stmt->bindValue(":currency_archive", $data['currency_archive'], PDO::PARAM_STR);
                $stmt->execute();

                $ret_arr['user_id'] = $data['user_id'];
                $ret_arr['subject_id'] = $data['subject_id'];
                $ret_arr['invoice_id'] = array_key_exists('invoice_id', $data) ? $data['invoice_id'] : 0;
                $ret_arr['card_id'] = $discountCard['id'];
                $ret_arr['card_number'] = $discountCard['card_number'];
                $ret_arr['card_user'] = $discountCard['client_name'] . " " . $discountCard['client_surname'] . " " . $discountCard['client_patronymic'] . "(" . $discountCard['created_at'] . ")";
                $ret_arr['card_type'] = $discountCard['card_type'];
                $ret_arr['operation_type'] = '+';
                $ret_arr['previous_discount'] = $data['previous_discount'];
                $ret_arr['current_discount'] = $data['current_discount'];
                $ret_arr['total_amount'] = $data['amount'];
                $ret_arr['discounted_amount'] = $data['discounted_amount'];
                $ret_arr['remaining_amount'] = $data['remaining_amount'];
                $ret_arr['created_at'] = Utils::getCurrentDate();

                return $ret_arr;

            } elseif($discountCard['card_type'] == 'bonus'){

                $stmt = $db->prepare("INSERT INTO " . $this->tableName . "(user_id, subject_id, invoice_id, card_id, card_number, card_user, card_type, operation_type, previous_discount, current_discount, total_amount, discounted_amount, remaining_amount, created_at, currency, currency_archive)
                VALUES(:user_id, :subject_id, :invoice_id, :card_id, :card_number, :card_user, :card_type, :operation_type, :previous_discount, :current_discount, :total_amount, :discounted_amount, :remaining_amount, :created_at, :currency, :currency_archive)");
                $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
                $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
                $stmt->bindValue(":invoice_id", array_key_exists('invoice_id', $data) ? $data['invoice_id'] : 0, PDO::PARAM_INT);
                $stmt->bindValue(":card_id", $discountCard['id'], PDO::PARAM_INT);
                $stmt->bindValue(":card_number", $discountCard['card_number'], PDO::PARAM_STR);
                $stmt->bindValue(":card_user", $discountCard['client_name'] . " " . $discountCard['client_surname'] . " " . $discountCard['client_patronymic'] . "(" . $discountCard['created_at'] . ")", PDO::PARAM_STR);
                $stmt->bindValue(":card_type", $discountCard['card_type'], PDO::PARAM_STR);
                $stmt->bindValue(":operation_type", $data['operation_type'], PDO::PARAM_STR);
                $stmt->bindValue(":previous_discount", $data['previous_discount'], PDO::PARAM_STR);
                $stmt->bindValue(":current_discount", $data['current_discount'], PDO::PARAM_STR);
                $stmt->bindValue(":total_amount", $data['amount'], PDO::PARAM_STR);
                $stmt->bindValue(":discounted_amount", $data['discounted_amount'], PDO::PARAM_STR);
                $stmt->bindValue(":remaining_amount", $data['remaining_amount'], PDO::PARAM_STR);
                $stmt->bindValue(":created_at", Utils::getCurrentDate(), PDO::PARAM_STR);
                $stmt->bindValue(":currency", $data['currency'], PDO::PARAM_INT);
                $stmt->bindValue(":currency_archive", $data['currency_archive'], PDO::PARAM_STR);
                $stmt->execute();

                $ret_arr['user_id'] = $data['user_id'];
                $ret_arr['subject_id'] = $data['subject_id'];
                $ret_arr['invoice_id'] = array_key_exists('invoice_id', $data) ? $data['invoice_id'] : 0;
                $ret_arr['card_id'] = $discountCard['id'];
                $ret_arr['card_number'] = $discountCard['card_number'];
                $ret_arr['card_user'] = $discountCard['client_name'] . " " . $discountCard['client_surname'] . " " . $discountCard['client_patronymic'] . "(" . $discountCard['created_at'] . ")";
                $ret_arr['card_type'] = $discountCard['card_type'];
                $ret_arr['operation_type'] = $data['operation_type'];
                $ret_arr['previous_discount'] = $data['previous_discount'];
                $ret_arr['current_discount'] = $data['current_discount'];
                $ret_arr['total_amount'] = $data['amount'] ;
                $ret_arr['discounted_amount'] = $data['discounted_amount'];
                $ret_arr['remaining_amount'] = $data['remaining_amount'];
                $ret_arr['created_at'] = Utils::getCurrentDate();

                return $ret_arr;

            }
            return false;
        } catch(Exception $e) {
            Logger::writeExceptionLog($e);
            throw $e;
        }
    }

    public function getAllForInvoiceId($data){

        try{

            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id = :user_id AND subject_id = :subject_id AND invoice_id = :invoice_id");
            $stmt->bindValue(":user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":invoice_id", $data['invoice_id'], PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            throw $e;

        }

    }

    public function __destruct(){
        $this->db = null;
    }

}
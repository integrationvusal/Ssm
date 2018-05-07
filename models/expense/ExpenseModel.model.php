<?php

class ExpenseModel{

    public $id;
    public $user_id;
    public $subject_id;
    public $name;
    public $price;
    public $description;

    private $tableName = "vl1_ExpenseModel";

    protected $db;

    public function __construct(){

        $this->db = (new DB())->start();

    }

    public function createExpense($array){
        try{
            $stmt = $this->db->prepare("INSERT INTO " . $this->tableName . "(name, user_id, subject_id, price, description, currency, currency_archive)
            VALUES(:name, :user_id, :subject_id, :price, :description, :currency, :currency_archive)");
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $array['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":name", $array['name'], PDO::PARAM_STR);
            $stmt->bindValue(":price", $array['price'], PDO::PARAM_STR);
            $stmt->bindValue(":description", $array['description'], PDO::PARAM_STR);
            $stmt->bindValue(":currency", $array['currency'], PDO::PARAM_INT);
            $stmt->bindValue(":currency_archive", $array['currency_archive'], PDO::PARAM_STR);
            return $stmt->execute();

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }
    }

    public function updateExpense($array){
        try{
            $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET name=:name, price=:price, description=:description, currency=:currency, currency_archive=:currency_archive WHERE id=:id AND user_id=:user_id");
            $stmt->bindValue(":name", $array['name'], PDO::PARAM_STR);
            $stmt->bindValue(":price", $array['price'], PDO::PARAM_STR);
            $stmt->bindValue(":description", $array['description'], PDO::PARAM_STR);
            $stmt->bindValue(":currency", $array['currency'], PDO::PARAM_INT);
            $stmt->bindValue(":currency_archive", $array['currency_archive'], PDO::PARAM_STR);
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":id", $array['id'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        } catch(Exception $e) {
            Logger::writeExceptionLog($e);
            return false;
        }
    }

    public function deleteexpense($array){
        try{

            $stmt = $this->db->prepare("DELETE FROM " . $this->tableName . " WHERE id=:expense_id AND user_id=:user_id");
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":expense_id", $array['expense_id'], PDO::PARAM_INT);
            return $stmt->execute();

        } catch(Exception $e) {
            Logger::writeExceptionLog($e);
            return false;
        }
    }

    public function sellApprove($array){

        try{
            $expense = $this->getOne(['user_id' => $array['user_id'], 'id' => $array['expense_id']]);

            $this->db->beginTransaction();

            $array['invoice_status'] = 1;
            $array['invoice_id'] = (new InvoiceModel())->createInvoice($array, $this->db);

            $array['goods_id'] = $expense['id'];
            $array['short_info'] = $expense['name'];
            $array['count'] = 1;
            $array['buy_price'] = $array['amount'];
            $array['sell_price'] = 0;
            (new InvoiceDetailModel())->createInvoiceDetail($array, $this->db);

            (new CashboxModel())->decreaseAmount($array, $this->db);

            $this->db->commit();
            return true;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getOne($array){
        try{
            $stmt = $this->db->prepare("SELECT *, st.name name, st.id uid FROM " . $this->tableName . " st
            LEFT JOIN vl1_CurrencyModel cr ON st.currency = cr.id
            WHERE st.id=:id AND user_id=:user_id");
            $stmt->bindValue(":id", $array['id'], PDO::PARAM_INT);
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(Exception $e) {
            return false;
        }
    }

    public function getAll($array){

        try{

            $stmt = $this->db->prepare("SELECT *, cr.name currency, st.name name, st.id uid FROM " . $this->tableName . " st
            LEFT JOIN vl1_CurrencyModel cr ON st.currency = cr.id
            WHERE user_id=:user_id AND subject_id=:subject_id");
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $array['subject_id'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            return false;

        }

    }

    public function getAllByName($array){

        try{

            if($array['subject_id'] > 0){
                $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " st
                LEFT JOIN vl1_CurrencyModel cr ON st.currency = cr.id
                WHERE user_id=:user_id AND subject_id=:subject_id AND name LIKE :search_expense");
                $stmt->bindValue(":subject_id", $array['subject_id'], PDO::PARAM_INT);
            } else {
                $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " st
                LEFT JOIN vl1_CurrencyModel cr ON st.currency = cr.id
                WHERE user_id=:user_id AND name LIKE :search_expense");
            }
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":search_expense", '%' . $array['search_expense'] . '%', PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            return false;

        }

    }

}
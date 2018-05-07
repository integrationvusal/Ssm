<?php

class ServiceModel{

    public $id;
    public $user_id;
    public $subject_id;
    public $name;
    public $price;
    public $description;

    private $tableName = "vl1_ServiceModel";

    protected $db;

    public function __construct(){

        $this->db = (new DB())->start();

    }

    public function createService($array){
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

    public function updateService($array){
        try{
            $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET name=:name, price=:price, description=:description, currency=:currency, currency_archive=:currency_archive WHERE id=:id AND user_id=:user_id");
            $stmt->bindValue(":name", $array['name'], PDO::PARAM_STR);
            $stmt->bindValue(":price", $array['price'], PDO::PARAM_STR);
            $stmt->bindValue(":description", $array['description'], PDO::PARAM_STR);
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":currency", $array['currency'], PDO::PARAM_INT);
            $stmt->bindValue(":currency_archive", $array['currency_archive'], PDO::PARAM_STR);
            $stmt->bindValue(":id", $array['id'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        } catch(Exception $e) {
            Logger::writeExceptionLog($e);
            return false;
        }
    }

    public function deleteService($array){
        try{

            $stmt = $this->db->prepare("DELETE FROM " . $this->tableName . " WHERE id=:service_id AND user_id=:user_id");
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":service_id", $array['service_id'], PDO::PARAM_INT);
            return $stmt->execute();

        } catch(Exception $e) {
            Logger::writeExceptionLog($e);
            return false;
        }
    }

    public function sellApprove($array){

        try{

            $service = $this->getOne(['user_id' => $array['user_id'], 'id' => $array['service_id']]);

            $this->db->beginTransaction();

            $array['invoice_status'] = 1;
            $array['invoice_id'] = (new InvoiceModel())->createInvoice($array, $this->db);

            $array['goods_id'] = $service['id'];
            $array['short_info'] = $service['name'];
            $array['count'] = 1;
            $array['buy_price'] = 0;
            $array['sell_price'] = $array['amount'];
            (new InvoiceDetailModel())->createInvoiceDetail($array, $this->db);

            (new CashboxModel())->increaseAmount($array, $this->db);

            $this->db->commit();
            return true;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getOne($array){
        try{
            $stmt = $this->db->prepare("SELECT st.id, st.name, st.price, st.description, cr.id currency_id, cr.name currency FROM " . $this->tableName . " st
            LEFT JOIN vl1_CurrencyModel cr ON cr.id = st.currency
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

            $stmt = $this->db->prepare("SELECT st.id, st.name, st.price, st.description, cr.id currency_id, cr.name currency FROM " . $this->tableName . " st
            LEFT JOIN vl1_CurrencyModel cr ON cr.id = st.currency
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
                $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id=:user_id AND subject_id=:subject_id AND name LIKE :search_service");
                $stmt->bindValue(":subject_id", $array['subject_id'], PDO::PARAM_INT);
            } else {
                $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id=:user_id AND name LIKE :search_service");
            }
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":search_service", '%' . $array['search_service'] . '%', PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            return false;

        }

    }

}
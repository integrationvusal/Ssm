<?php

class CashboxModel{

    public $id;
    public $user_id;
    public $subject_id;
    public $amount;

    private $tableName = "vl1_CashboxModel";

    protected $db;

    public function __construct(){

        $this->db = (new DB())->start();

    }

    public function createCB($array){

        $stmt = $this->db->prepare("INSERT INTO " . $this->tableName . "(user_id, subject_id)
        VALUES(:user_id, :subject_id)");
        $stmt->bindValue(":user_id", $array['user_id']);
        $stmt->bindValue(":subject_id", $array['subject_id']);
        if($stmt->execute()){
            $array['id'] = $this->db->lastInsertId();
            $array['amount'] = 0;
            return $array;
        } else {
            return false;
        }

    }

    public function increaseAmount($array, $db){
        try{

            /**
             * Discount card state
             */

            if(array_key_exists('discount', $array) && $array['discount'] == 1){
                if(!array_key_exists('discounted_amount', $array)) return false;
                $array['amount'] = doubleval($array['amount']) - doubleval($array['discounted_amount']);
            }

            // Discount card state
            if(!isset($array['debtamount']))    $array['debtamount'] = $array['payed'];

            $amount = (isset($array['client']) && $array['client'] > 0)?$array['debtamount']:$array['amount'];

            $stmt = $db->prepare("SELECT id FROM " . $this->tableName . " WHERE user_id=:user_id AND subject_id=:subject_id AND id=:cashbox_id");

            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->bindValue(":cashbox_id", $array['cashbox_id']);
            $stmt->execute();

            if($stmt->rowCount() < 1){
                $stmt = $db->prepare("INSERT INTO " . $this->tableName . "(user_id, subject_id)
                VALUES(:user_id, :subject_id)");
                $stmt->bindValue(":user_id", $array['user_id']);
                $stmt->bindValue(":subject_id", $array['subject_id']);
                $stmt->execute();
                $array['cashbox_id'] = $db->lastInsertId();
            }

            $currencyQuery = "(currency IS NULL OR currency = :currency)";
            if($array['currency']>0)   $currencyQuery = "currency=:currency";

            $stmt = $db->prepare("SELECT history.total_amount FROM " . $this->tableName . " box
            LEFT JOIN vl1_CashboxHistoryModel history ON history.cashbox_id = box.id
            WHERE user_id=:user_id AND subject_id=:subject_id AND {$currencyQuery} ORDER BY history.id DESC LIMIT 1");

            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->bindValue(":currency", $array['currency']);

            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            $array['operation_type'] = '+';

            $mid_amount = isset($res['total_amount'])?$res['total_amount']:0;
            
            if(!array_key_exists('total_amount', $array))
                $array['total_amount'] = $mid_amount + $amount;
            return (new CashboxHistoryModel())->createCH($array, $db);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            throw $e;

        }

    }

    public function decreaseAmount($array, $db){

        try{

            $cur=$array['currency']>0?'':'_azn';
            $amount = (isset($array['client']) && $array['client'] > 0)?$array['payed']:$array['amount'];

            $stmt = $db->prepare("SELECT id FROM " . $this->tableName . " WHERE user_id=:user_id AND subject_id=:subject_id AND id=:cashbox_id");

            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->bindValue(":cashbox_id", $array['cashbox_id']);
            $stmt->execute();

            if($stmt->rowCount() <= 0){
                $stmt = $db->prepare("INSERT INTO " . $this->tableName . "(user_id, subject_id)
                VALUES(:user_id, :subject_id");

                $stmt->bindValue(":user_id", $array['user_id']);
                $stmt->bindValue(":subject_id", $array['subject_id']);
                $stmt->execute();

                $array['cashbox_id'] = $db->lastInsertId();
            }

            $currencyQuery = "(currency IS NULL OR currency = :currency)";
            if($array['currency']>0)   $currencyQuery = "currency=:currency";

            $stmt = $db->prepare("SELECT history.total_amount FROM " . $this->tableName . " box
            LEFT JOIN vl1_CashboxHistoryModel history ON history.cashbox_id = box.id
            WHERE user_id=:user_id AND subject_id=:subject_id AND {$currencyQuery} ORDER BY history.id DESC LIMIT 1");

            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->bindValue(":currency", $array['currency']);

            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if($res){
                $array['operation_type'] = '-';
                if(!array_key_exists('total_amount', $array))
                    $array['total_amount'] = $res['total_amount'] - $amount;
                return (new CashboxHistoryModel())->createCH($array, $db);
            }

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            throw $e;

        }

    }

    public function getCurrent($array){
        try{

            $stmt = $this->db->prepare("
            SELECT 
                h.cashbox_id id, h.currency, h.total_amount 
            FROM 
                vl1_CashboxHistoryModel h
            INNER JOIN
                (SELECT 
                    MAX(IF(history.currency=0 ,history.id,0)) azn,
                    MAX(IF(history.currency=1,history.id,0)) usd,
                    MAX(IF(history.currency=2,history.id,0)) eur,
                    MAX(IF(history.currency=3 ,history.id,0)) rur
                FROM 
                    ".$this->tableName." box
                LEFT JOIN 
                    vl1_CashboxHistoryModel history ON box.id = history.cashbox_id
                WHERE 
                    user_id=:user_id 
                        AND 
                    subject_id=:subject_id
                        AND
                    history.currency IS NOT NULL
                ) a 
            ON 
                h.id IN(a.azn, a.usd, a.eur, a.rur) 
            ORDER BY h.currency");


            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $amounts =[];

            foreach((new CurrencyModel())->getAllWithAZN() as $k=>$cur)
                $amounts[$k] = (isset($res[$k]) && $res[$k]['total_amount'])?$res[$k]['total_amount']:0;
            
            if($res) {
                return ['id'=>$res[0]['id'], 'currencies_amounts'=>$amounts];
            } else {
                $subject = SubjectController::getCurrentSubject();
                if ($subject['type'] > 1) return $this->createCB($array);
                else return false;
            }

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getAllCBForUser($array){

        try{

            $stmt = $this->db->prepare("
                SELECT aa.id,
                   name,
                   subject_id,
                   IFNULL(total_amount_azn, 0) total_amount_azn,
                   IFNULL(total_amount, 0) total_amount
                FROM
                  (SELECT *
                   FROM
                     (SELECT history.id history_id,
                             box.id,
                             subject.name,
                             subject.id subject_id,
                             history.total_amount total_amount_azn
                      FROM vl1_CashboxModel AS box
                      LEFT JOIN vl1_CashboxHistoryModel history ON box.id = history.cashbox_id
                      LEFT JOIN vl1_SubjectModel AS subject ON box.subject_id = subject.id
                      WHERE subject.user_id = :user_id
                        AND (currency = 0 || currency IS NULL)
                      ORDER BY history.id DESC) a
                   GROUP BY name) aa
                LEFT JOIN
                  (SELECT id,
                          total_amount
                   FROM
                     (SELECT box.id,
                             subject.name,
                             history.total_amount
                      FROM vl1_CashboxModel AS box
                      LEFT JOIN vl1_CashboxHistoryModel history ON box.id = history.cashbox_id
                      LEFT JOIN vl1_SubjectModel AS subject ON box.subject_id = subject.id
                      WHERE subject.user_id = :user_id
                        AND currency > 0
                      ORDER BY history.id DESC) b
                   GROUP BY name) bb ON aa.id = bb.id
                ORDER BY aa.history_id DESC
            ");

            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }


    }

    public function getSearchAll($array){

        try{

            $stmt = $this->db->prepare("SELECT box.id, subject.name, box.amount, subject.id AS subject_id FROM vl1_CashboxModel AS box
            LEFT JOIN vl1_SubjectModel AS subject ON box.subject_id = subject.id
            WHERE subject.user_id = :user_id AND subject.name LIKE :cashbox_search");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":cashbox_search", '%' . $array['cashbox_search'] . '%');
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }


    }

    public function getAll($array, $limit = 0, $offset = 0){

        try{

            $stmt = $this->db->prepare("SELECT history.*, invoice.id AS invoice_id, invoice.type AS invoice_type,
            invoice.serial, invoice.date, invoice.operator, user.name
            FROM vl1_CashboxHistoryModel AS history
            LEFT JOIN vl1_InvoiceModel AS invoice ON history.invoice_id = invoice.id
            LEFT JOIN vl1_InvoiceDetailModel AS detail ON detail.invoice_id = invoice.id
            LEFT JOIN vl1_OperatorModel AS user ON invoice.operator = user.id
            WHERE history.cashbox_id = :cashbox_id GROUP BY serial ORDER BY history.id DESC LIMIT :limit OFFSET :offset");
            
            
            $stmt->bindValue(":cashbox_id", $array['cashbox_id']);
            $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getAllCashboxes($array){
        try{

            $stmt = $this->db->prepare("SELECT cb.*, subject.name FROM " . $this->tableName . " AS cb
            LEFT JOIN vl1_SubjectModel AS subject ON cb.subject_id = subject.id
            WHERE cb.user_id = :user_id");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }
    }

    public function getCountAll($array){

        try{

            $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM vl1_CashboxHistoryModel
            WHERE cashbox_id = :cashbox_id");
            $stmt->bindValue(":cashbox_id", $array['cashbox_id']);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if($res) return $res['cnt'];

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function processIncome($array){

        try{

            $this->db->beginTransaction();

            $array['invoice_status'] = 1;
            $array['invoice_id'] = (new InvoiceModel())->createInvoice($array, $this->db);

            $currencyQuery = "(currency IS NULL OR currency = :currency)";
            if($array['currency']>0)   $currencyQuery = "currency=:currency";

            $stmt = $this->db->prepare("SELECT history.total_amount FROM " . $this->tableName . " box
            LEFT JOIN vl1_CashboxHistoryModel history ON history.cashbox_id = box.id
            WHERE user_id=:user_id AND subject_id=:subject_id AND {$currencyQuery} ORDER BY history.id DESC LIMIT 1");

            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->bindValue(":currency", $array['currency']);

            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if($res){
                $array['operation_type'] = '+';
                $array['total_amount'] = $res['total_amount'] + $array['amount'];
                (new CashboxHistoryModel())->createCH($array, $this->db);

                $array['sell_price'] = $array['buy_price'] = $array['count'] = $array['goods_id'] = 0;
                $array['short_info'] = '';
                (new InvoiceDetailModel())->createInvoiceDetail($array, $this->db);
            } else {
                throw new Exception("Not found amount");
            }

            $this->db->commit();
            return true;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            $this->db->rollBack();
            return false;

        }

    }

    public function processOutgoing($array){

        try{

            $this->db->beginTransaction();

            $array['invoice_status'] = 1;
            $array['invoice_id'] = (new InvoiceModel())->createInvoice($array, $this->db);

            $currencyQuery = "(currency IS NULL OR currency = :currency)";
            if($array['currency']>0)   $currencyQuery = "currency=:currency";

            $stmt = $this->db->prepare("SELECT history.total_amount FROM " . $this->tableName . " box
            LEFT JOIN vl1_CashboxHistoryModel history ON history.cashbox_id = box.id
            WHERE user_id=:user_id AND subject_id=:subject_id AND {$currencyQuery} ORDER BY history.id DESC LIMIT 1");

            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->bindValue(":currency", $array['currency']);

            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if($res){
                $array['operation_type'] = '-';
                $array['total_amount'] = $res['total_amount'] - $array['amount'];
                (new CashboxHistoryModel())->createCH($array, $this->db);

                $array['sell_price'] = $array['buy_price'] = $array['count'] = $array['goods_id'] = 0;
                $array['short_info'] = '';
                (new InvoiceDetailModel())->createInvoiceDetail($array, $this->db);
            }

            $this->db->commit();
            return true;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            $this->db->rollBack();
            return false;

        }

    }

    public function processTransfer($array){

        try{

            $this->db->beginTransaction();

            $array['invoice_status'] = 1;
            $array['invoice_id'] = (new InvoiceModel())->createInvoice($array, $this->db);

            $currencyQuery = "(currency IS NULL OR currency = :currency)";
            if($array['currency']>0)   $currencyQuery = "currency=:currency";

            $stmt = $this->db->prepare("SELECT history.total_amount FROM " . $this->tableName . " box
            LEFT JOIN vl1_CashboxHistoryModel history ON history.cashbox_id = box.id
            WHERE user_id=:user_id AND subject_id=:subject_id AND {$currencyQuery} ORDER BY history.id DESC LIMIT 1");

            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->bindValue(":currency", $array['currency']);

            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$res) $res['total_amount'] = 0;
            $array['operation_type'] = '-';
            $array['total_amount'] = $res['total_amount'] - $array['amount'];
            (new CashboxHistoryModel())->createCH($array, $this->db);

            $array['sell_price'] = $array['buy_price'] = $array['count'] = $array['goods_id'] = 0;
            $array['short_info'] = '';
            (new InvoiceDetailModel())->createInvoiceDetail($array, $this->db);


            // Into another cashbox

            $destination_cb = $this->getById(['user_id' => $array['user_id'], 'cashbox_id' => $array['destination_cb']], $this->db);

            $array['subject_id'] = $destination_cb['subject_id'];
            $array['cashbox_id'] = $destination_cb['id'];
            $array['invoice_id'] = (new InvoiceModel())->createInvoice($array, $this->db);

            $stmt = $this->db->prepare("SELECT history.total_amount FROM " . $this->tableName . " box
            LEFT JOIN vl1_CashboxHistoryModel history ON history.cashbox_id = box.id
            WHERE user_id=:user_id AND subject_id=:subject_id AND {$currencyQuery} ORDER BY history.id DESC LIMIT 1");

            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->bindValue(":currency", $array['currency']);

            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$res) $res['total_amount'] = 0;
            $array['total_amount'] = $res['total_amount'] + $array['amount'];
            (new CashboxHistoryModel())->createCH($array, $this->db);
            (new InvoiceDetailModel())->createInvoiceDetail($array, $this->db);

            $this->db->commit();
            return true;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            $this->db->rollBack();
            return false;

        }

    }

    public function getById($array, $db){

        try{

            $stmt = $db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id = :user_id AND id = :cashbox_id");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":cashbox_id", $array['cashbox_id']);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            throw $e;

        }

    }

}
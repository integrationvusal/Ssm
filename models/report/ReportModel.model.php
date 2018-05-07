<?php

class ReportModel{

    protected $db;

    private $start_date;
    private $end_date;

    public function __construct(){

        $this->db = (new DB())->start();
        $this->end_date = date("Y-m-d H:i:s");
        $this->start_date =  date("Y-m-d H:i:s", time() - 30*24*60*60);

    }

    public function getContragentInvoices($array, $limit = 0, $offset = 0){

        try{

            if(array_key_exists("report_search", $array)){

                $contragent = "";
                if($array['contragent_id']){
                    $contragent = " invoice.contragent_id = " . (int)$array['contragent_id'];
                }  else {
                    $contragent = " invoice.contragent_id >= 0";
                }
                if(empty($array['date_from'])) $array['date_from'] = "0000-00-00";
                if(empty($array['date_to'])) $array['date_to'] = "3000-00-00";
                $stmt = $this->db->prepare("SELECT invoice.*, detail.currency, contragent.name FROM vl1_InvoiceModel AS invoice
                LEFT JOIN vl1_ContragentModel AS contragent ON contragent.id = invoice.contragent_id
                LEFT JOIN vl1_InvoiceDetailModel AS detail ON detail.invoice_id = invoice.id
                WHERE invoice.user_id=:user_id AND invoice.contragent_id IS NOT NULL
                AND invoice.date BETWEEN :date_from AND :date_to AND contragent.subject_id = :subject_id AND " . $contragent . "
                ORDER BY invoice.id DESC, invoice.date DESC");
                $stmt->bindValue(":date_from", $array['date_from']);
                $stmt->bindValue(":date_to", $array['date_to']);
            } else {

                $stmt = $this->db->prepare("SELECT invoice.*, detail.currency, contragent.name FROM vl1_InvoiceModel AS invoice
                LEFT JOIN vl1_ContragentModel AS contragent ON contragent.id = invoice.contragent_id
                LEFT JOIN vl1_InvoiceDetailModel AS detail ON detail.invoice_id = invoice.id
                WHERE invoice.user_id=:user_id AND contragent.subject_id = :subject_id AND invoice.contragent_id  IS NOT NULL
                ORDER BY invoice.id DESC, invoice.date DESC
                LIMIT :limit OFFSET :offset");
                $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
                $stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);

            }
            $stmt->bindValue(":user_id", (int)$array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", (int)$array['subject_id'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getContragentInvoicesSummary($array){

        try{

            $stmt = $this->db->prepare("
            SELECT
                SUM(IF(detail.currency>0 && invoice.type<>5,invoice.amount,0)) AS debt,
                SUM(IF((!detail.currency || detail.currency IS NULL) && invoice.type<>5,invoice.amount,0)) AS debt_azn,
                SUM(IF(detail.currency>0, invoice.payed, 0)) AS payed,
                SUM(IF(!detail.currency || detail.currency IS NULL,invoice.payed, 0)) AS payed_azn
            FROM vl1_InvoiceModel AS invoice
            LEFT JOIN vl1_ContragentModel AS contragent ON contragent.id = invoice.contragent_id
            LEFT JOIN vl1_InvoiceDetailModel AS detail ON detail.invoice_id = invoice.id
            WHERE invoice.user_id=:user_id AND contragent.subject_id = :subject_id AND invoice.contragent_id  IS NOT NULL");
            $stmt->bindValue(":user_id", (int)$array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", (int)$array['subject_id'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getCountContragentInvoices($array){

        try{

            $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM vl1_InvoiceModel AS invoice
            LEFT JOIN vl1_ContragentModel AS contragent ON invoice.contragent_id = contragent.id
            WHERE contragent.subject_id = :subject_id AND invoice.user_id=:user_id AND invoice.contragent_id IS NOT NULL");
            $stmt->bindValue(":user_id", (int)$array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", (int)$array['subject_id'], PDO::PARAM_INT);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res['cnt'];

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }


    public function getClientInvoices($array, $limit = 0, $offset = 0){

        try{

            if(array_key_exists("report_search", $array)){

                $client = "";
                if($array['client_id']){
                    $client = " invoice.client_id = " . (int)$array['client_id'];
                }  else {
                    $client = " invoice.client_id >= 0";
                }
                if(empty($array['date_from'])) $array['date_from'] = "0000-00-00";
                if(empty($array['date_to'])) $array['date_to'] = "3000-00-00";
                $stmt = $this->db->prepare("SELECT invoice.*, detail.currency, client.name FROM vl1_InvoiceModel AS invoice
                LEFT JOIN vl1_ClientModel AS client ON client.id = invoice.client_id
                LEFT JOIN vl1_InvoiceDetailModel AS detail ON invoice.id = detail.invoice_id
                WHERE invoice.user_id=:user_id AND invoice.client_id IS NOT NULL AND invoice.client_id > 0 AND client.subject_id = :subject_id
                AND invoice.date BETWEEN :date_from AND :date_to AND " . $client . "
                ORDER BY invoice.id DESC, invoice.date DESC");
                $stmt->bindValue(":date_from", $array['date_from']);
                $stmt->bindValue(":date_to", $array['date_to']);
            } else {

                $stmt = $this->db->prepare("SELECT invoice.*, detail.currency, client.name FROM vl1_InvoiceModel AS invoice
                LEFT JOIN vl1_ClientModel AS client ON client.id = invoice.client_id
                LEFT JOIN vl1_InvoiceDetailModel AS detail ON invoice.id = detail.invoice_id
                WHERE invoice.user_id=:user_id AND invoice.client_id IS NOT NULL AND invoice.client_id > 0 AND client.subject_id = :subject_id
                ORDER BY invoice.id DESC, invoice.date DESC
                LIMIT :limit OFFSET :offset");
                $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
                $stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);

            }
            $stmt->bindValue(":user_id", (int)$array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", (int)$array['subject_id'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getClientInvoicesSummary($array){

        try{

            $stmt = $this->db->prepare("
            SELECT
                SUM(IF(detail.currency > 0, invoice.amount, 0)) AS debt,
                SUM(IF((!detail.currency || detail.currency IS NULL), invoice.amount, 0)) AS debt_azn,
                SUM(IF(detail.currency > 0, invoice.payed, 0)) AS payed,
                SUM(IF((!detail.currency || detail.currency IS NULL), invoice.payed, 0)) AS payed_azn
            FROM vl1_InvoiceModel AS invoice
            LEFT JOIN vl1_ClientModel AS client ON client.id = invoice.client_id
            LEFT JOIN vl1_InvoiceDetailModel AS detail ON detail.invoice_id = invoice.id
            WHERE invoice.user_id=:user_id AND invoice.client_id IS NOT NULL AND invoice.client_id > 0 AND client.subject_id = :subject_id");
            $stmt->bindValue(":user_id", (int)$array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", (int)$array['subject_id'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getCountClientInvoices($array){

        try{

            $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM vl1_InvoiceModel AS invoice
            LEFT JOIN vl1_ClientModel AS client ON invoice.client_id = client.id
            WHERE invoice.user_id=:user_id AND invoice.client_id IS NOT NULL AND client.subject_id = :subject_id
            AND invoice.client_id > 0");
            $stmt->bindValue(":user_id", (int)$array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", (int)$array['subject_id'], PDO::PARAM_INT);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res['cnt'];

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getCashboxInvoices($array, $limit = 0, $offset = 0){

        try{

            $cashbox_search = " history.cashbox_id = :cashbox_id ";
            if($array['cashbox_id'] == 0) $cashbox_search = " history.cashbox_id > :cashbox_id ";

            if(array_key_exists("report_search", $array)) {

                if(empty($array['date_from'])) $array['date_from'] = "0000-00-00";
                if(empty($array['date_to'])) $array['date_to'] = "3000-00-00";
                $stmt = $this->db->prepare("SELECT history.*, subject.name AS cashbox_name, invoice.id AS invoice_id, invoice.type AS invoice_type,
                invoice.serial, invoice.date, invoice.operator, user.name
                FROM vl1_CashboxHistoryModel AS history
                LEFT JOIN vl1_InvoiceModel AS invoice ON history.invoice_id = invoice.id
                LEFT JOIN vl1_UserModel AS user ON invoice.operator = user.id
                LEFT JOIN vl1_SubjectModel AS subject ON subject.id = invoice.subject_id
                WHERE invoice.user_id = :user_id AND " . $cashbox_search . " AND
                invoice.date BETWEEN :date_from AND :date_to
                ORDER BY history.date DESC, invoice.id DESC");
                $stmt->bindValue(":date_from", $array['date_from']);
                $stmt->bindValue(":date_to", $array['date_to']);

            } else {
                $stmt = $this->db->prepare("SELECT history.*, subject.name AS cashbox_name, invoice.id AS invoice_id, invoice.type AS invoice_type,
                invoice.serial, invoice.date, invoice.operator, user.name
                FROM vl1_CashboxHistoryModel AS history
                LEFT JOIN vl1_InvoiceModel AS invoice ON history.invoice_id = invoice.id
                LEFT JOIN vl1_UserModel AS user ON invoice.operator = user.id
                LEFT JOIN vl1_SubjectModel AS subject ON subject.id = invoice.subject_id
                WHERE invoice.user_id = :user_id AND " . $cashbox_search . "
                ORDER BY history.date DESC, invoice.id DESC LIMIT :limit OFFSET :offset");
                $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
                $stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);
            }
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":cashbox_id", $array['cashbox_id']);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getCountCashboxInvoices($array){

        try{

            $subject = "";
            if(isset($array['subject_id'])){
                $subject = " AND invoice.subject_id = " . $array['subject_id'];
            }
            $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM vl1_CashboxHistoryModel AS history
            LEFT JOIN vl1_InvoiceModel AS invoice ON history.invoice_id = invoice.id
            WHERE invoice.user_id = :user_id" . $subject);
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res['cnt'];

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getSoldGoodsList($array, $limit = 0, $offset = 0){

        try{

            if(array_key_exists("report_search", $array)) {

                if($array['subject_id'] == 0) { $subject = " sell.subject_id > :subject_id "; }
                else { $subject = " sell.subject_id = :subject_id "; }
                if($array['goods_id'] == 0) { $goods = " sell.goods_id > :goods_id "; }
                else { $goods = " sell.goods_id = :goods_id "; }
                if(empty($array['date_from'])) $array['date_from'] = "0000-00-00";
                if(empty($array['date_to'])) $array['date_to'] = "3000-00-00";
                $stmt = $this->db->prepare("SELECT sell.*, detail.currency, invoice.serial, invoice.type AS invoice_type, invoice.id AS invoice_id, operator.name AS operator_name,
                invoice.date
                FROM vl1_SellModel AS sell
                LEFT JOIN vl1_InvoiceModel AS invoice ON sell.invoice_id = invoice.id
                LEFT JOIN vl1_InvoiceDetailModel AS detail ON sell.invoice_id = detail.invoice_id
                LEFT JOIN vl1_OperatorModel AS operator ON sell.seller_id = operator.id
                WHERE sell.user_id = :user_id AND sell.status = 1 AND invoice.date BETWEEN :date_from AND :date_to AND " . $subject . " AND " . $goods . "
                ORDER BY invoice.date DESC, sell.id DESC");
                $stmt->bindValue(":date_from", $array['date_from']);
                $stmt->bindValue(":date_to", $array['date_to']);
                $stmt->bindValue(":goods_id", $array['goods_id']);

            } else {
                $stmt = $this->db->prepare("SELECT sell.*, detail.currency, invoice.serial, invoice.type AS invoice_type, invoice.id AS invoice_id, operator.name AS operator_name,
                invoice.date
                FROM vl1_SellModel AS sell
                LEFT JOIN vl1_InvoiceModel AS invoice ON sell.invoice_id = invoice.id
                LEFT JOIN vl1_InvoiceDetailModel AS detail ON sell.invoice_id = detail.invoice_id
                LEFT JOIN vl1_OperatorModel AS operator ON sell.seller_id = operator.id
                WHERE sell.user_id = :user_id AND sell.status = 1 AND sell.subject_id = :subject_id
                ORDER BY invoice.date DESC, sell.id DESC
                LIMIT :limit OFFSET :offset");
                $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
                $stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);
            }
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getSoldGoodsListSummary($array){

        try{

            $stmt = $this->db->prepare("SELECT SUM(sell.count) AS count, SUM(ROUND(sell.count * sell.buy_price, 2)) AS total_buy_price,
            SUM( IF( detail.currency>0, ROUND(sell.count * sell.sell_price, 2), 0) ) AS total_sell_price,
            SUM( IF( !detail.currency || detail.currency IS NULL, ROUND(sell.count * sell.sell_price, 2), 0) ) AS total_sell_price_azn
            FROM vl1_SellModel AS sell
            LEFT JOIN vl1_InvoiceModel AS invoice ON sell.invoice_id = invoice.id
            LEFT JOIN vl1_InvoiceDetailModel AS detail ON sell.invoice_id = detail.invoice_id
            LEFT JOIN vl1_OperatorModel AS operator ON sell.seller_id = operator.id
            WHERE sell.user_id = :user_id AND sell.status = 1");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getCountSoldGoodsList($array){

        try{

            $subject = "";

            if($array['subject_id']){
                $subject = " AND subject_id = " . $array['subject_id'];
            }

            $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM vl1_SellModel AS sell WHERE user_id = :user_id" . $subject);
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res['cnt'];

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getRemainingGoodsLits($array, $limit = 0, $offset = 0){

        try{

            if(array_key_exists("report_search", $array)) {

                if($array['subject_id'] == 0) { $subject = " store.subject_id > :subject_id "; }
                else { $subject = " store.subject_id = :subject_id "; }
                if($array['goods_id'] == 0) { $goods = " store.goods_id > :goods_id "; }
                else { $goods = " store.goods_id = :goods_id "; }
                if(empty($array['date_from'])) $array['date_from'] = "0000-00-00";
                if(empty($array['date_to'])) $array['date_to'] = "3000-00-00";
                $stmt = $this->db->prepare("SELECT store.*, invoice.serial, invoice.type AS invoice_type, invoice.id AS invoice_id, subject.name AS subject_name,
                invoice.date
                FROM vl1_StoreModel AS store
                LEFT JOIN vl1_SubjectModel AS subject ON store.subject_id= subject.id
                LEFT JOIN vl1_InvoiceModel AS invoice ON store.invoice_id = invoice.id
                WHERE store.user_id = :user_id AND store.status != '2' AND store.count > 0  AND invoice.date BETWEEN :date_from AND :date_to AND " . $subject . " AND " . $goods . "
                ORDER BY invoice.date DESC, store.id DESC");
                $stmt->bindValue(":date_from", $array['date_from']);
                $stmt->bindValue(":date_to", $array['date_to']);
                $stmt->bindValue(":goods_id", $array['goods_id']);

            } else {
                $stmt = $this->db->prepare("SELECT store.*, invoice.serial, invoice.type AS invoice_type, invoice.id AS invoice_id, subject.name AS subject_name,
                invoice.date
                FROM vl1_StoreModel AS store
                LEFT JOIN vl1_SubjectModel AS subject ON store.subject_id= subject.id
                LEFT JOIN vl1_InvoiceModel AS invoice ON store.invoice_id = invoice.id
                WHERE store.user_id = :user_id AND store.status != '2' AND store.count > 0 AND store.subject_id = :subject_id
                ORDER BY invoice.date DESC, store.id DESC
                LIMIT :limit OFFSET :offset");
                $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
                $stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);
            }
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getRemainingGoodsLitsSummary($array){

        try{
            $stmt = $this->db->prepare("
                SELECT
            SUM(IF(currency>0, store.count,0)) AS count,
            SUM(IF(!currency || currency IS NULL, store.count,0)) AS count_azn,
            SUM(IF(currency>0, ROUND(store.count * store.buy_price, 2), 0)) AS total_buy_price,
            SUM(IF(!currency || currency IS NULL, ROUND(store.count * store.buy_price, 2), 0)) AS total_buy_price_azn,
            SUM(IF(currency>0, ROUND(store.count * store.sell_price, 2), 0)) AS total_sell_price,
            SUM(IF(currency = 0 || currency IS NULL, ROUND(store.count * store.sell_price, 2), 0)) AS total_sell_price_azn

            FROM vl1_StoreModel AS store
            LEFT JOIN vl1_SubjectModel AS subject ON store.subject_id= subject.id
            LEFT JOIN vl1_InvoiceModel AS invoice ON store.invoice_id = invoice.id
            WHERE store.user_id = :user_id AND store.subject_id=:subject_id AND store.status != '2' AND store.count > 0");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getCountRemainingGoods($array){

        try{

            $subject = "";

            if($array['subject_id']){
                $subject = " AND subject_id = " . $array['subject_id'];
            }

            $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM vl1_StoreModel AS sell WHERE user_id = :user_id" . $subject);
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res['cnt'];

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }


    public function getSoldServicesList($array, $limit = 0, $offset = 0){

        try{

            if(array_key_exists("report_search", $array)) {

                if($array['subject_id'] == 0) { $subject = " invoice.subject_id > :subject_id "; }
                else { $subject = " invoice.subject_id = :subject_id "; }
                if($array['service_id'] == 0) { $goods = " detail.goods_id > :service_id "; }
                else { $goods = " detail.goods_id = :service_id "; }
                if(empty($array['date_from'])) $array['date_from'] = "0000-00-00";
                if(empty($array['date_to'])) $array['date_to'] = "3000-00-00";
                $stmt = $this->db->prepare("SELECT invoice.*, operator.name, currency, detail.count, detail.short_info, detail.sell_price, subject.name AS subject_name FROM vl1_InvoiceModel AS invoice
                LEFT JOIN vl1_InvoiceDetailModel AS detail ON invoice.id = detail.invoice_id
                LEFT JOIN vl1_OperatorModel AS operator ON invoice.operator = operator.id
                LEFT JOIN vl1_SubjectModel AS subject ON invoice.subject_id = subject.id
                WHERE invoice.user_id = :user_id AND invoice.type = '11'
                AND invoice.date BETWEEN :date_from AND :date_to AND " . $subject . " AND " . $goods . "
                ORDER BY invoice.date DESC, invoice.id DESC");
                $stmt->bindValue(":date_from", $array['date_from']);
                $stmt->bindValue(":date_to", $array['date_to']);
                $stmt->bindValue(":service_id", $array['service_id']);

            } else {
                $stmt = $this->db->prepare("SELECT invoice.*, operator.name, currency, detail.count, detail.short_info, detail.sell_price, subject.name AS subject_name FROM vl1_InvoiceModel AS invoice
                LEFT JOIN vl1_InvoiceDetailModel AS detail ON invoice.id = detail.invoice_id
                LEFT JOIN vl1_OperatorModel AS operator ON invoice.operator = operator.id
                LEFT JOIN vl1_SubjectModel AS subject ON invoice.subject_id = subject.id
                WHERE invoice.user_id = :user_id AND invoice.type = '11' AND invoice.subject_id = :subject_id
                ORDER BY invoice.date DESC, invoice.id DESC
                LIMIT :limit OFFSET :offset");
                $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
                $stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);
            }
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getSoldServicesListSummary($array){

        try{

            $stmt = $this->db->prepare("SELECT SUM(detail.count) AS count,
            SUM(IF(currency, ROUND(detail.sell_total, 2), 0)) AS total_sell_price,
            SUM(IF(!currency || currency IS NULL, ROUND(detail.sell_total, 2), 0)) AS total_sell_price_azn
            FROM vl1_InvoiceModel AS invoice
            LEFT JOIN vl1_InvoiceDetailModel AS detail ON detail.invoice_id = invoice.id
            LEFT JOIN vl1_OperatorModel AS operator ON invoice.operator = operator.id
            WHERE invoice.user_id = :user_id AND invoice.type = '11'");
            $stmt->bindValue(":user_id", $array['user_id']);
            //$stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getCountSoldServicesList($array){

        try{

            $subject = "";

            if($array['subject_id']){
                $subject = " AND subject_id = " . $array['subject_id'];
            }

            $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM vl1_InvoiceModel AS invoice WHERE invoice.type = '11' AND user_id = :user_id" . $subject);
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res['cnt'];

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    /**
     * @param $array
     * @param int $limit
     * @param int $offset
     * @return array|bool
     *
     * Discount card report
     *
     */

    public function getDiscountCardsHistoryList($array, $limit = 0, $offset = 0){

        try{

            $subject_id = $array['subject_id'];
            if(array_key_exists("report_search", $array)) {

                if(empty($array['date_from'])) $array['date_from'] = "0000-00-00";
                if(empty($array['date_to'])) $array['date_to'] = "3000-00-00";

                $subject_id = $array['subject_sb'];
                if($array['subject_sb'] == '%') $subject_id = $array['subject_id'];

                $stmt = $this->db->prepare("SELECT history.*, IFNULL(cur.name, 'AZN') currency, invoice.type AS invoice_type, invoice.id AS invoice_id, invoice.serial AS invoice_serial
                FROM vl1_DiscountCardHistoryModel AS history
                LEFT JOIN vl1_InvoiceModel AS invoice ON invoice.id = history.invoice_id
                LEFT JOIN vl1_CurrencyModel AS cur ON cur.id = history.currency
                WHERE history.user_id = :user_id
                AND history.subject_id = :subject_id
                AND history.created_at BETWEEN :date_from AND :date_to
                AND history.card_number LIKE :card_number
                AND history.card_type LIKE :card_type
                AND history.operation_type LIKE :operation_type
                ORDER BY created_at DESC");
                $stmt->bindValue(":date_from", $array['date_from'], PDO::PARAM_STR);
                $stmt->bindValue(":date_to", $array['date_to'], PDO::PARAM_STR);
                $stmt->bindValue(":card_number", "%" . $array['card_number'] . "%", PDO::PARAM_STR);
                $stmt->bindValue(":card_type", $array['card_type'], PDO::PARAM_STR);
                $stmt->bindValue(":operation_type", $array['operation_type'], PDO::PARAM_STR);

            } else {

                $stmt = $this->db->prepare("SELECT history.*, IFNULL(cur.name, 'AZN') currency, invoice.type AS invoice_type, invoice.id AS invoice_id, invoice.serial AS invoice_serial
                FROM vl1_DiscountCardHistoryModel AS history
                LEFT JOIN vl1_InvoiceModel AS invoice ON invoice.id = history.invoice_id
                LEFT JOIN vl1_CurrencyModel AS cur ON cur.id = history.currency
                WHERE history.user_id = :user_id AND history.subject_id = :subject_id ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
                $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
                $stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);

            }
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $subject_id);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getCountDiscountCardsHistory($array){

        try{

            $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM vl1_DiscountCardHistoryModel WHERE subject_id = :subject_id AND user_id = :user_id");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res['cnt'];

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getDifferenceGoodsList($array, $limit = 0, $offset = 0){

        try{

            if(array_key_exists("report_search", $array)) {

                if($array['subject_id'] == 0) { $subject = " sell.subject_id > :subject_id "; }
                else { $subject = " sell.subject_id = :subject_id "; }
                if($array['goods_id'] == 0) { $goods = " sell.goods_id > :goods_id "; }
                else { $goods = " sell.goods_id = :goods_id "; }
                if(empty($array['date_from'])) $array['date_from'] = "0000-00-00";
                if(empty($array['date_to'])) $array['date_to'] = "3000-00-00";
                $stmt = $this->db->prepare("SELECT sell.*, detail.currency, invoice.serial, invoice.type AS invoice_type, invoice.id AS invoice_id, operator.name AS operator_name,
                invoice.date
                FROM vl1_SellModel AS sell
                LEFT JOIN vl1_InvoiceModel AS invoice ON sell.invoice_id = invoice.id
                LEFT JOIN vl1_InvoiceDetailModel AS detail ON detail.invoice_id = invoice.id
                LEFT JOIN vl1_OperatorModel AS operator ON sell.seller_id = operator.id
                WHERE sell.user_id = :user_id AND sell.status = 1 AND invoice.date BETWEEN :date_from AND :date_to AND " . $subject . " AND " . $goods . "
                ORDER BY invoice.date DESC, sell.id DESC");
                $stmt->bindValue(":date_from", $array['date_from']);
                $stmt->bindValue(":date_to", $array['date_to']);
                $stmt->bindValue(":goods_id", $array['goods_id']);

            } else {
                $stmt = $this->db->prepare("SELECT sell.*, detail.currency, invoice.serial, invoice.type AS invoice_type, invoice.id AS invoice_id, operator.name AS operator_name,
                invoice.date
                FROM vl1_SellModel AS sell
                LEFT JOIN vl1_InvoiceModel AS invoice ON sell.invoice_id = invoice.id
                LEFT JOIN vl1_InvoiceDetailModel AS detail ON detail.invoice_id = invoice.id
                LEFT JOIN vl1_OperatorModel AS operator ON sell.seller_id = operator.id
                WHERE sell.user_id = :user_id AND sell.status = 1 AND sell.subject_id = :subject_id
                ORDER BY invoice.date DESC, sell.id DESC
                LIMIT :limit OFFSET :offset");
                $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
                $stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);
            }
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getDifferenceGoodsListSummary($array){

        try{

            $stmt = $this->db->prepare("
                SELECT
            SUM(IF(detail.currency, sell.count,0)) AS count,
            SUM(IF(!detail.currency || detail.currency IS NULL, sell.count,0)) AS count_azn,
            SUM(IF(detail.currency, ROUND(sell.count * sell.buy_price, 2), 0)) AS total_buy_price,
            SUM(IF(!detail.currency || detail.currency IS NULL, ROUND(sell.count * sell.buy_price, 2), 0)) AS total_buy_price_azn,
            SUM(IF(detail.currency, ROUND(sell.count * sell.sell_price, 2), 0)) AS total_sell_price,
            SUM(IF(!detail.currency || detail.currency IS NULL, ROUND(sell.count * sell.sell_price, 2), 0)) AS total_sell_price_azn

            FROM vl1_SellModel AS sell
            LEFT JOIN vl1_InvoiceModel AS invoice ON sell.invoice_id = invoice.id
            LEFT JOIN vl1_InvoiceDetailModel AS detail ON detail.invoice_id = invoice.id
            LEFT JOIN vl1_OperatorModel AS operator ON sell.seller_id = operator.id
            WHERE sell.user_id = :user_id AND sell.status = 1");
            $stmt->bindValue(":user_id", $array['user_id']);

            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getCountDifferenceGoodsList($array){

        try{

            $subject = "";

            if($array['subject_id']){
                $subject = " AND subject_id = " . $array['subject_id'];
            }

            $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM vl1_SellModel AS sell WHERE user_id = :user_id" . $subject);
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res['cnt'];

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }


    /**
     * Expenses
     */
    public function getSoldExpensesList($array, $limit = 0, $offset = 0){

        try{

            if(array_key_exists("report_search", $array)) {

                if($array['subject_id'] == 0) { $subject = " invoice.subject_id > :subject_id "; }
                else { $subject = " invoice.subject_id = :subject_id "; }
                if($array['expense_id'] == 0) { $goods = " detail.goods_id > :expense_id "; }
                else { $goods = " detail.goods_id = :expense_id "; }
                if(empty($array['date_from'])) $array['date_from'] = "0000-00-00";
                if(empty($array['date_to'])) $array['date_to'] = "3000-00-00";
                $stmt = $this->db->prepare("SELECT invoice.*, currency, operator.name, detail.count, detail.short_info, detail.sell_price, detail.buy_price, subject.name AS subject_name FROM vl1_InvoiceModel AS invoice
                LEFT JOIN vl1_InvoiceDetailModel AS detail ON invoice.id = detail.invoice_id
                LEFT JOIN vl1_OperatorModel AS operator ON invoice.operator = operator.id
                LEFT JOIN vl1_SubjectModel AS subject ON invoice.subject_id = subject.id
                WHERE invoice.user_id = :user_id AND invoice.type = '12'
                AND invoice.date BETWEEN :date_from AND :date_to AND " . $subject . " AND " . $goods . "
                ORDER BY invoice.date DESC, invoice.id DESC");
                $stmt->bindValue(":date_from", $array['date_from']);
                $stmt->bindValue(":date_to", $array['date_to']);
                $stmt->bindValue(":expense_id", $array['expense_id']);

            } else {
                $stmt = $this->db->prepare("SELECT invoice.*, operator.name, currency, detail.count, detail.short_info, detail.sell_price, detail.buy_price, subject.name AS subject_name FROM vl1_InvoiceModel AS invoice
                LEFT JOIN vl1_InvoiceDetailModel AS detail ON invoice.id = detail.invoice_id
                LEFT JOIN vl1_OperatorModel AS operator ON invoice.operator = operator.id
                LEFT JOIN vl1_SubjectModel AS subject ON invoice.subject_id = subject.id
                WHERE invoice.user_id = :user_id AND invoice.type = '12' AND invoice.subject_id = :subject_id
                ORDER BY invoice.date DESC, invoice.id DESC
                LIMIT :limit OFFSET :offset");
                $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
                $stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);
            }
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getSoldExpensesListSummary($array){

        try{

            $stmt = $this->db->prepare("SELECT SUM(detail.count) AS count,
            SUM(IF(currency, ROUND(detail.buy_total, 2), 0)) AS total_sell_price,
            SUM(IF(!currency, ROUND(detail.buy_total, 2), 0)) AS total_sell_price_azn
            FROM vl1_InvoiceModel AS invoice
            LEFT JOIN vl1_InvoiceDetailModel AS detail ON detail.invoice_id = invoice.id
            LEFT JOIN vl1_OperatorModel AS operator ON invoice.operator = operator.id
            WHERE invoice.user_id = :user_id AND invoice.type = '12'");
            $stmt->bindValue(":user_id", $array['user_id']);
            //$stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getCountSoldExpensesList($array){

        try{

            $subject = "";

            if($array['subject_id']){
                $subject = " AND subject_id = " . $array['subject_id'];
            }

            $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM vl1_InvoiceModel AS invoice WHERE invoice.type = '12' AND user_id = :user_id" . $subject);
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res['cnt'];

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

}
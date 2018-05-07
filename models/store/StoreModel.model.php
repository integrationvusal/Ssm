<?php

class StoreModel{

    public $id;
    public $user_id;
    public $subject_id;
    public $short_info;
    public $goods_id;
    public $count;
    public $buy_price;
    public $sell_price;
    public $invoice_id;
    public $contragent;
    public $pending_count;
    public $status;

    private $tableName = "vl1_StoreModel";

    protected $db;

    public function __construct(){
        $this->db = (new DB())->start();
    }

    public function createStoreItem($array){

        try{
            $this->db->beginTransaction();

            $array['invoice_id'] = (new InvoiceModel())->processInvoiceNumber($array, $this->db);
            if(!$array['invoice_id']) {
                $this->db->rollBack();
                return false;
            }

            $stmt = $this->db->prepare("SELECT id FROM " . $this->tableName . " WHERE user_id=:user_id AND subject_id=:subject_id
            AND barcode=:barcode AND goods_code=:goods_code AND goods_id=:goods_id AND buy_price=:buy_price AND sell_price=:sell_price");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->bindValue(":barcode", $array['barcode']);
            $stmt->bindValue(":goods_code", $array['goods_code']);
            $stmt->bindValue(":goods_id", $array['goods_id']);
            $stmt->bindValue(":buy_price", $array['buy_price']);
            $stmt->bindValue(":sell_price", $array['sell_price']);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!empty($res) && isset($res['id'])){

                (new InvoiceDetailModel())->processInvoiceDetail($array, $this->db);

                $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET subject_id=:subject_id, barcode=:barcode, goods_code=:goods_code,
                short_info=:short_info, goods_id=:goods_id,
                buy_price=:buy_price, sell_price=:sell_price, invoice_id=:invoice_id, contragent=:contragent, currency=:currency,  currency_archive=:currency_archive, pending_count=pending_count + :pending_count,
                status=CASE WHEN status = '1' THEN '3' WHEN status = '2' THEN '2' WHEN status = '3' THEN '3' END
                WHERE id=:id AND user_id=:user_id");
                $stmt->bindValue(":user_id", $array['user_id']);
                $stmt->bindValue(":subject_id", $array['subject_id']);
                $stmt->bindValue(":barcode", $array['barcode']);
                $stmt->bindValue(":goods_code", $array['goods_code']);
                $stmt->bindValue(":short_info", $array['short_info']);
                $stmt->bindValue(":goods_id", $array['goods_id']);
                $stmt->bindValue(":buy_price", $array['buy_price']);
                $stmt->bindValue(":sell_price", $array['sell_price']);
                $stmt->bindValue(":invoice_id", $array['invoice_id']);
                $stmt->bindValue(":contragent", $array['contragent']);
                $stmt->bindValue(":currency", $array['currency']);
                $stmt->bindValue(":currency_archive", $array['currency_archive']);
                $stmt->bindValue(":pending_count", $array['count']);
                $stmt->bindValue(":id", $res['id']);

                $st = $stmt->execute();
                $storeItemId = $res['id'];

            } else {
                (new InvoiceDetailModel())->createInvoiceDetail($array, $this->db);

                $stmt = $this->db->prepare("INSERT INTO " . $this->tableName . "(user_id, subject_id, barcode, goods_code, short_info, goods_id, pending_count,
                buy_price, sell_price, invoice_id, contragent, currency, currency_archive, status) VALUES(:user_id, :subject_id, :barcode, :goods_code, :short_info, :goods_id, :count,
                :buy_price, :sell_price, :invoice_id, :contragent, :currency, :currency_archive, :status)");
                $stmt->bindValue(":user_id", $array['user_id']);
                $stmt->bindValue(":subject_id", $array['subject_id']);
                $stmt->bindValue(":barcode", $array['barcode']);
                $stmt->bindValue(":goods_code", $array['goods_code']);
                $stmt->bindValue(":short_info", $array['short_info']);
                $stmt->bindValue(":goods_id", $array['goods_id']);
                $stmt->bindValue(":count", $array['count']);
                $stmt->bindValue(":buy_price", $array['buy_price']);
                $stmt->bindValue(":sell_price", $array['sell_price']);
                $stmt->bindValue(":invoice_id", $array['invoice_id']);
                $stmt->bindValue(":contragent", $array['contragent']);
                $stmt->bindValue(":currency", $array['currency']);
                $stmt->bindValue(":currency_archive", $array['currency_archive']);
                $stmt->bindValue(":status", "2");

                $st = $stmt->execute();
                $storeItemId = $this->db->lastInsertId();

            }

            if($st){

                $this->db->commit();
                return [
                    'item_id' => $storeItemId,
                    'invoice_id' => $array['invoice_id'],
                    'goods_code' => $array['goods_code'],
                    'barcode' => $array['barcode'],
                    'short_info' => $array['short_info'],
                    'count' => $array['count'],
                    'buy_price' => $array['buy_price'],
                    'sell_price' => $array['sell_price'],
                    'currency' => $array['currency'],
                    'currency_archive' => $array['currency_archive']
                ];

            } else {

                $this->db->rollBack();
                return false;

            }

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            $this->db->rollBack();
            return false;

        }

    }

    public function returnGoods($array){
        try{

            $this->db->beginTransaction();

            $array['invoice_status'] = '1';
            $array['client'] = 0;

            $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET count = count + :count WHERE id = :id");

            $updateIds = "";
            $cnt = $array['ids'];
            foreach($cnt as $key => $count){
                $stmt->bindValue(":count", $count);
                $stmt->bindValue(":id", $key);
                $stmt->execute();
                $updateIds .= ", " . $key;
            }
            $updateIds = trim(trim($updateIds, ","));
            Logger::writeLog($updateIds);

            $stmt = $this->db->prepare("SELECT s.*, IFNULL(c.value, 0) new_currency_archive  FROM " . $this->tableName . "  s LEFT JOIN vl1_CurrencyModel c ON c.id= s.currency WHERE s.id IN (" . $updateIds . ")");
            $stmt->execute();

            $res = [];
            foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $d) $res[$d['id']] = $d;

            $i=0;
            $invoiceModel = new InvoiceModel();
            $invoiceDetailModel = new InvoiceDetailModel();
            $cashboxModel = new CashboxModel();
            $oldAmount = $array['amount'];


            foreach($array['count'] as $_currency => $storeItems) {
                if($i > 0)
                    $array['invoice_serial'] = $invoiceModel->getNextInvoiceNumber(['serial'=>$array['invoice_serial']], true);

                $array['amount'] =  $oldAmount[$_currency];
                $array['invoice_id'] = $invoiceModel->createInvoice($array, $this->db);

                foreach($storeItems as $storeID=>$_count){
                    $array['goods_id'] = $res[$storeID]['goods_id'];
                    $array['short_info'] = $res[$storeID]['short_info'];
                    $array['count'] =   $_count;
                    $array['buy_price'] = $res[$storeID]['buy_price'];
                    $array['buy_total'] = $_count * $res[$storeID]['buy_price'];
                    $array['sell_price'] = $res[$storeID]['sell_price'];
                    $array['sell_total'] = $_count * $res[$storeID]['sell_price'];
                    $array['currency'] = $res[$storeID]['currency'];
                    $array['currency_archive'] = $res[$storeID]['new_currency_archive'];
                    $invoiceDetailModel->createInvoiceDetail($array, $this->db);
                }
                $cashboxModel->decreaseAmount($array, $this->db);
                $i++;
            }

            $this->db->commit();
            return true;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            $this->db->rollBack();
            return false;

        }

    }

    public function deleteStoreItem($array){

        try{

            $this->db->beginTransaction();

            (new InvoiceDetailModel())->decreaseInvoiceDetail($array, $this->db);

            $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET pending_count=pending_count - :count
            WHERE user_id=:user_id AND id=:id AND subject_id=:subject_id
            AND invoice_id=:invoice_id AND buy_price=:buy_price AND sell_price=:sell_price
            AND goods_id=:goods_id");
            $stmt->bindValue(":count", $array['count']);
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":id", $array['item_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->bindValue(":invoice_id", $array['invoice_id']);
            $stmt->bindValue(":buy_price", $array['buy_price']);
            $stmt->bindValue(":sell_price", $array['sell_price']);
            $stmt->bindValue(":goods_id", $array['goods_id']);

            if($stmt->execute()){
                $this->db->commit();
                return true;
            } else {

                $this->db->rollBack();
                return false;
            }

        } catch(Exception $e) {


            Logger::writeExceptionLog($e);
            $this->db->rollBack();
            return false;

        }

    }

    public function getAll($array, $limit = 0, $offset = 0){

        try{

            if(array_key_exists("store_search", $array)){

                $count_condition = '';
                if(!empty($array['search_count'])){
                    $count_sign = substr($array['search_count'], 0, 1);
                    $count_range = explode("-", $array['search_count']);
                    if($count_sign == '>' || $count_sign == '<' || $count_sign == '='){
                        $search_count = str_replace($count_sign, "", $array['search_count']);
                        $count_condition = " AND `count` " . stripslashes($count_sign) . " " . $search_count;
                    } elseif(count($count_range) > 1) {
                        $count_from = $count_range[0];
                        $count_to = $count_range[1];
                        $count_condition = " AND `count` BETWEEN " . $count_from . " AND " . $count_to;
                    } else {
                        $search_count = "'%" . $array["search_count"] . "%'";
                        $count_condition = " AND `count` LIKE " . $search_count;
                    }
                }

                $buy_price_condition = '';
                if(!empty($array['search_currency'])){
                    $operator = !empty($array['search_buy_price'])?' AND ((':' AND (';
                    $buy_price_sign = substr($array['search_currency'], 0, 1);
                    $buy_price_range = explode("-", $array["search_currency"]);
                    if($buy_price_sign == '>' || $buy_price_sign == '<' || $buy_price_sign == '='){
                        $search_currency = str_replace($buy_price_sign, "", $array['search_currency']);
                        $buy_price_condition = $operator."currency<>0 AND buy_price " . stripslashes($buy_price_sign) . " " . $search_currency.')';
                    } elseif(count($buy_price_range) > 1) {
                        $buy_price_from = $buy_price_range[0];
                        $buy_price_to = $buy_price_range[1];
                        $buy_price_condition = $operator."currency<>0 AND buy_price BETWEEN " . $buy_price_from . " AND " . $buy_price_to.')';
                    }  else {
                        $search_currency = "'%" . $array["search_currency"] . "%'";
                        $buy_price_condition = $operator."currency<>0 AND buy_price LIKE " . $search_currency.')';
                    }
                }

                $buy_price_condition_azn = '';
                if(!empty($array['search_buy_price'])){
                    $operator_azn = !empty($buy_price_condition)?' OR (':' AND (';
                    $brackets_azn = !empty($buy_price_condition)?'))':')';
                    $buy_price_sign_azn = substr($array['search_buy_price'], 0, 1);
                    $buy_price_range_azn = explode("-", $array["search_buy_price"]);
                    if($buy_price_sign_azn == '>' || $buy_price_sign_azn == '<' || $buy_price_sign_azn == '='){
                        $search_buy_price = str_replace($buy_price_sign_azn, "", $array['search_buy_price']);
                        $buy_price_condition_azn = $operator_azn."currency=0 AND buy_price " . stripslashes($buy_price_sign_azn) . " " . $search_buy_price.$brackets_azn;
                    } elseif(count($buy_price_range_azn) > 1) {
                        $buy_price_from_azn = $buy_price_range_azn[0];
                        $buy_price_to_azn = $buy_price_range_azn[1];
                        $buy_price_condition_azn = $operator_azn."currency=0 AND buy_price BETWEEN " . $buy_price_from_azn . " AND " . $buy_price_to_azn.$brackets_azn;
                    }  else {
                        $search_buy_price = "'%" . $array["search_buy_price"] . "%'";
                        $buy_price_condition_azn = $operator_azn."currency=0 AND buy_price LIKE " . $search_buy_price.$brackets_azn;
                    }
                }

                $sell_price_condition = '';
                if(!empty($array['search_sell_currency'])){
                    $operator = !empty($array['search_sell_price'])?' AND ((':' AND (';
                    $sell_price_sign = substr($array['search_sell_currency'], 0, 1);
                    $sell_price_range = explode("-", $array["search_sell_currency"]);
                    if($sell_price_sign == '>' || $sell_price_sign == '<' || $sell_price_sign == '='){
                        $search_sell_currency = str_replace($sell_price_sign, "", $array['search_sell_currency']);
                        $sell_price_condition = $operator."currency<>0 AND sell_price " . stripslashes($sell_price_sign) . " " . $search_sell_currency.')';
                    } elseif(count($sell_price_range) > 1) {
                        $sell_price_from = $sell_price_range[0];
                        $sell_price_to = $sell_price_range[1];
                        $sell_price_condition = $operator."currency<>0 AND sell_price BETWEEN " . $sell_price_from . " AND " . $sell_price_to.')';
                    }  else {
                        $search_sell_currency = "'%" . $array["search_sell_currency"] . "%'";
                        $sell_price_condition = $operator."currency<>0 AND sell_price LIKE " . $search_sell_currency.')';
                    }
                }

                $sell_price_condition_azn = '';
                if(!empty($array['search_sell_price'])){
                    $operator_azn = !empty($sell_price_condition)?' OR (':' AND (';
                    $brackets_azn = !empty($sell_price_condition)?'))':')';
                    $sell_price_sign_azn = substr($array['search_sell_price'], 0, 1);
                    $sell_price_range_azn = explode("-", $array["search_sell_price"]);
                    if($sell_price_sign_azn == '>' || $sell_price_sign_azn == '<' || $sell_price_sign_azn == '='){
                        $search_sell_price = str_replace($sell_price_sign_azn, "", $array['search_sell_price']);
                        $sell_price_condition_azn = $operator_azn."currency=0 AND sell_price " . stripslashes($sell_price_sign_azn) . " " . $search_sell_price.$brackets_azn;
                    } elseif(count($sell_price_range_azn) > 1) {
                        $sell_price_from_azn = $sell_price_range_azn[0];
                        $sel_price_to_azn = $sell_price_range_azn[1];
                        $sell_price_condition_azn = $operator_azn."currency=0 AND sell_price BETWEEN " . $sel_price_to_azn . " AND " . $sel_price_to_azn.$brackets_azn;
                    }  else {
                        $search_sell_price = "'%" . $array["search_sell_price"] . "%'";
                        $sell_price_condition_azn = $operator_azn."currency=0 AND sell_price LIKE " . $search_sell_price.$brackets_azn;
                    }
                }


                $stmt = $this->db->prepare("SELECT *, cr.name as currency, cr.value currency_value FROM " . $this->tableName . " st
                    LEFT JOIN vl1_CurrencyModel cr ON cr.id = st.currency
                    WHERE user_id=:user_id
                    AND subject_id=:subject_id
                    AND count > 0 AND (status='1' OR status='3')
                    AND goods_code LIKE :search_code
                    AND barcode LIKE :search_barcode
                    AND short_info LIKE :search_short_info"
                    .$count_condition
                    .$buy_price_condition
                    .$buy_price_condition_azn
                    .$sell_price_condition
                    .$sell_price_condition_azn
                );

                $stmt->bindValue(":search_code", '%' . $array['search_code'] .'%');
                $stmt->bindValue(":search_barcode", '%' . $array['search_barcode'] .'%');
                $stmt->bindValue(":search_short_info", '%' . $array['search_short_info'] .'%');

            } else {
                $stmt = $this->db->prepare("SELECT *, cr.name currency, cr.value currency_value FROM " . $this->tableName . " st LEFT JOIN vl1_CurrencyModel cr ON cr.id = st.currency WHERE
                user_id=:user_id AND subject_id=:subject_id AND count > 0 AND (status='1' OR status='3') LIMIT :limit OFFSET :offset");
                $stmt->bindValue(":limit",(int)$limit, PDO::PARAM_INT);
                $stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);
            }
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getCountAll($array, $limit = 0, $offset = 0){

        try{

            $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM " . $this->tableName . " WHERE
            user_id=:user_id AND subject_id=:subject_id AND count > 0 AND (status='1' OR status='3')");
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

    public function getSummaryAll($array, $limit = 0, $offset = 0){

        try{

            $stmt = $this->db->prepare("SELECT SUM(`count`) AS total_count, SUM(`count` * IF(st.currency=0, buy_price, 0)) AS total_buy_price_azn, SUM(`count` * IF(st.currency<>0, buy_price, 0)) AS total_buy_price, SUM(`count` * IF(st.currency=0, sell_price, 0)) AS total_sell_price_azn, SUM(`count` * IF(st.currency<>0, sell_price, 0)) AS total_sell_price
            FROM " . $this->tableName . " st
            LEFT JOIN vl1_CurrencyModel cr ON cr.id = st.currency
            WHERE user_id=:user_id AND subject_id=:subject_id AND count > 0 AND (status='1' OR status='3')");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);

            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getAllToSell($array){

        try{

            $modelName = Application::$settings['goods_types'][$array['subject_id']]['model_name'];
            $tableName = $modelName::$tableName;
            $stmt = $this->db->prepare("SELECT store.*, model.image FROM " . $this->tableName . " AS store
            LEFT JOIN " . $tableName. " AS model ON store.goods_id = model.id
            WHERE store.user_id=:user_id AND store.subject_id=:subject_id AND count > 0 AND (store.status='1' OR store.status='3')");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getAllByBarcode($array, $one = false){

        try{

            $subject = SubjectController::getCurrentSubject();
            $modelName = Application::$settings['goods_types'][$subject['goods_type']]['model_name'];
            $tableName = $modelName::$tableName;

            $limit = "";
            if($one){
                $limit = " ORDER BY store.sell_price ASC LIMIT 1";
            }
            $stmt = $this->db->prepare("SELECT store.*, model.image FROM " . $this->tableName . " AS store
            LEFT JOIN " . $tableName. " AS model ON store.goods_id = model.id
            WHERE store.user_id=:user_id AND store.subject_id=:subject_id AND count > 0 AND (store.status='1' OR store.status='3')
            AND store.barcode = :barcode" . $limit);
            $stmt->bindValue(":barcode", $array['barcode']);

            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);

            $stmt->execute();
            if($one) return $stmt->fetch(PDO::FETCH_ASSOC);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getAllByCode($array){

        try{

            $subject = SubjectController::getCurrentSubject();
            $modelName = Application::$settings['goods_types'][$subject['goods_type']]['model_name'];
            $tableName = $modelName::$tableName;

            $stmt = $this->db->prepare("SELECT store.*, model.image FROM " . $this->tableName . " AS store
            LEFT JOIN " . $tableName. " AS model ON store.goods_id = model.id
            WHERE store.user_id=:user_id AND store.subject_id=:subject_id AND count > 0 AND (store.status='1' OR store.status='3')
            AND store.goods_code LIKE :goods_code");
            $stmt->bindValue(":goods_code", '%' . $array['goods_code'] . '%');

            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getAllByCodeAndBarcode($array){

        try{

            if(array_key_exists('goods_code', $array)){
                $array['search_code'] = $array['goods_code'];
            }
            $subject = SubjectController::getCurrentSubject();
            $modelName = Application::$settings['goods_types'][$subject['goods_type']]['model_name'];
            $tableName = $modelName::$tableName;

            $stmt = $this->db->prepare("SELECT store.*, cr.name as currency, model.image FROM " . $this->tableName . " AS store
            LEFT JOIN " . $tableName. " AS model ON store.goods_id = model.id
            LEFT JOIN vl1_CurrencyModel cr ON cr.id = store.currency
            WHERE store.user_id=:user_id AND store.subject_id=:subject_id AND store.status != '2' AND store.count > 0
            AND (store.goods_code LIKE :search_code OR store.barcode LIKE :search_code)");
            $stmt->bindValue(":search_code", '%' . $array['search_code'] . '%');

            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getAllPendings($array){

        try{

            $stmt = $this->db->prepare("SELECT store.*, invoice.date, invoice.serial, invoice.contragent_id, invoice.notes FROM " . $this->tableName . " AS store
            LEFT JOIN vl1_InvoiceModel AS invoice ON store.invoice_id = invoice.id
            WHERE store.user_id = :user_id
            AND store.subject_id = :subject_id
            AND (store.status = '2' OR store.status = '3') AND store.pending_count > 0");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);

            if($stmt->execute()) return $stmt->fetchAll(PDO::FETCH_ASSOC);
            return false;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function approve($array){
        try{

            $this->db->beginTransaction();

            $stmt = $this->db->prepare("DELETE FROM " . $this->tableName . "
            WHERE user_id=:user_id AND subject_id=:subject_id
            AND pending_count <= 0 AND status = '2'");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->execute();

            $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET pending_count = 0, status = '1'
            WHERE user_id=:user_id AND subject_id=:subject_id
            AND pending_count <= 0 AND status = '3'");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->execute();

            $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET count = count + pending_count, pending_count = 0, status = '1'
            WHERE user_id=:user_id AND subject_id=:subject_id
            AND pending_count > 0 AND (status = '2' OR status = '3')");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->execute();

            if($stmt->rowCount()){
                $invoice_ids = $array['invoice_id'];
                $invoice_archives = $array['invoice_archive'];
                $array = array_slice($array,0,-2);
                foreach ($invoice_ids as $cur=>$invoiceid) {
                    $array['invoice_id'] = $invoiceid;
                    $array['currency'] = $cur;
                    $array['currency_archive'] = $invoice_archives[$cur];
                    (new InvoiceModel())->approveInvoice($array, $this->db);
                }
            } else {
                $this->db->rollBack();
                return false;
            }

            $this->db->commit();
            return true;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            $this->db->rollBack();
            return false;

        }

    }

    public function reject($array){

        try{

            $this->db->beginTransaction();

            $stmt = $this->db->prepare("DELETE FROM " . $this->tableName . "
            WHERE user_id=:user_id AND subject_id=:subject_id AND status = '2'");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->execute();

            $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET pending_count = 0, status = '1'
            WHERE user_id=:user_id AND subject_id=:subject_id AND status = '3'");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->execute();

            $invoice_ids = $array['invoice_id'];
            $invoice_archives = $array['invoice_archive'];
            $array = array_slice($array,0,-2);
            foreach ($invoice_ids as $cur=>$invoiceid) {
                $array['invoice_id'] = $invoiceid;
                $array['currency'] = $cur;
                $array['currency_archive'] = $invoice_archives[$cur];
                (new InvoiceModel())->rejectInvoice($array, $this->db);
            }

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

            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id=:user_id AND subject_id=:subject_id AND id=:id");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->bindValue(":id", $array['store_item_id']);
            if($stmt->execute()) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return false;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getOneOf($array){
        try{
            $data = array();
            $stmt = $this->db->prepare("SELECT *, cr.name as currency FROM " . $this->tableName . " st LEFT JOIN vl1_CurrencyModel cr ON cr.id = st.currency WHERE st.id = :id AND st.user_id = :user_id");
            $stmt->bindValue(":id", $array['id']);
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if($res){
                $data['common'] = $res;

                $stmt = $this->db->prepare("SELECT * FROM vl1_GoodsModel WHERE id = :id AND user_id = :user_id");
                $stmt->bindValue(":id", $res['goods_id']);
                $stmt->bindValue(":user_id", $array['user_id']);
                $stmt->execute();
                $res1 = $stmt->fetch(PDO::FETCH_ASSOC);
                if($res1){
                    $modelName = Application::$settings['goods_types'][$res1['goods_type']]['model_name'];
                    $tmp = $modelName::getOne(['id' => $res1['goods_id'], 'user_id' => $array['user_id']], $this->db);

                    $attrs = $modelName::getStructuredInfoAttrs();
                    if($tmp){
                        $data['model'] = $tmp;
                        $data['attrs'] = $attrs;
                        return $data;
                    }
                }
            }
            return false;
        } catch(Exception $e) {

            return false;

        }
    }

    public function sellGoods($array, $db){

        try{
            $stmt = $db->prepare("UPDATE " . $this->tableName . " SET count=count-:count WHERE id=:id");
            foreach($array as $arr){
                $stmt->bindValue(":count", $arr['count']);
                $stmt->bindValue(":id", $arr['store_item_id']);
                if(!$stmt->execute()) throw new Exception("No store item found");
            }
            return true;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            throw $e;

        }

    }

    public function approveTransfer($array){

        $response = ['status' => 0, 'message' => 'Əməliyyatı başa çatdırmaq mümkün olmadı'];
        try{

            $this->db->beginTransaction();

            $array['invoice_status'] = 1;
            $oldAmount = $array['amount'];

            $i = 0;
            $invoiceModel = new InvoiceModel();
            $ids = implode(',', array_keys($array['ids']));
            $stmt = $this->db->prepare("SELECT s.*, IFNULL(c.value, 0) new_currency_archive  FROM " . $this->tableName . "  s LEFT JOIN vl1_CurrencyModel c ON c.id= s.currency WHERE s.id IN ({$ids})");
            $stmt->execute();
            $res = [];
            foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $d) $res[$d['id']] = $d;

            if(is_array($array['count'])){
                foreach ($array['count'] as $_currency => $storeItems) {

                    $array['amount'] = $oldAmount[$_currency];

                    if($i > 0)
                        $array['invoice_serial'] = $invoiceModel->getNextInvoiceNumber(['serial'=>$array['invoice_serial']], true);

                    $array['invoice_id'] = $invoiceModel->createInvoice($array, $this->db);

                    foreach($storeItems as $storeItemId => $_count){
                        if(!isset($res[$storeItemId]))
                            throw new Exception("Element with id = " . $storeItemId . " is not store object");
                        $storeItem = $res[$storeItemId];

                        if($_count > $storeItem['count']) {
                            $this->db->rollBack();
                            $response = ['status' => 0, 'message' => 'Anbarda istədiyiniz sayda mal yoxdur'];
                            return $response;
                        }

                        $decreaseInStockCountStmt = $this->db->prepare("UPDATE " . $this->tableName . " SET count = count - :count WHERE user_id = :user_id AND id = :id");
                        $decreaseInStockCountStmt->bindValue(":count", $_count, PDO::PARAM_INT);
                        $decreaseInStockCountStmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
                        $decreaseInStockCountStmt->bindValue(":id", $storeItemId, PDO::PARAM_INT);
                        $decreaseInStockCountStmt->execute();
                        if(!$decreaseInStockCountStmt->rowCount()) throw new Exception("Couldn't decrease in stock count");

                        $invoiceDetailArray = [
                            "user_id" => $array['user_id'],
                            "invoice_id" => $array['invoice_id'],
                            "goods_id" => $storeItem['goods_id'],
                            "short_info" => $storeItem['short_info'],
                            "count" => $_count,
                            "buy_price" => $storeItem['buy_price'],
                            "buy_total" => $storeItem['buy_price'] * $_count,
                            "sell_price" => $storeItem['sell_price'],
                            "sell_total" => $storeItem['short_info'] * $_count,
                            "currency" => $storeItem['currency'],
                            "currency_archive" => $storeItem['new_currency_archive'],
                            "date" => $array['date'],
                        ];

                        (new InvoiceDetailModel())->createInvoiceDetail($invoiceDetailArray, $this->db);

                        $updateStmt = $this->db->prepare("UPDATE " . $this->tableName . " SET count = count + :count, status =
                        CASE
                            WHEN pending_count = 0 THEN '1'
                            WHEN pending_count > 0  THEN '3'
                            ELSE '0'
                        END
                        WHERE user_id = :user_id AND subject_id = :subject_id AND barcode = :barcode AND goods_code = :goods_code
                        AND goods_id = :goods_id AND buy_price = :buy_price AND sell_price = :sell_price AND currency = :currency");
                        $updateStmt->bindValue(":count", $_count);
                        $updateStmt->bindValue(":user_id", $array['user_id']);
                        $updateStmt->bindValue(":subject_id", $array['subject_to']);
                        $updateStmt->bindValue(":barcode", $storeItem['barcode']);
                        $updateStmt->bindValue(":goods_code", $storeItem['goods_code']);
                        $updateStmt->bindValue(":goods_id", $storeItem['goods_id']);
                        $updateStmt->bindValue(":buy_price", $storeItem['buy_price']);
                        $updateStmt->bindValue(":sell_price", $storeItem['sell_price']);
                        $updateStmt->bindValue(":currency", $storeItem['currency']);
                        $updateStmt->execute();

                        if(!$updateStmt->rowCount()){
                            $insertStatement = $this->db->prepare("INSERT INTO " . $this->tableName . "(user_id, subject_id,
                            barcode, goods_code, short_info, goods_id, count, buy_price, sell_price, invoice_id, contragent,
                            pending_count, status, currency, currency_archive) VALUES(:user_id, :subject_id,
                            :barcode, :goods_code, :short_info, :goods_id, :count, :buy_price, :sell_price, :invoice_id, :contragent,
                            :pending_count, :status, :currency, :currency_archive)");

                            $insertStatement->bindValue(":user_id", $array['user_id']);
                            $insertStatement->bindValue(":subject_id", $array['subject_to']);
                            $insertStatement->bindValue(":barcode", $storeItem['barcode']);
                            $insertStatement->bindValue(":goods_code", $storeItem['goods_code']);
                            $insertStatement->bindValue(":short_info", $storeItem['short_info']);
                            $insertStatement->bindValue(":goods_id", $storeItem['goods_id']);
                            $insertStatement->bindValue(":count", $_count);
                            $insertStatement->bindValue(":buy_price", $storeItem['buy_price']);
                            $insertStatement->bindValue(":sell_price", $storeItem['sell_price']);
                            $insertStatement->bindValue(":invoice_id", $array['invoice_id']);
                            $insertStatement->bindValue(":contragent", "Anbar");
                            $insertStatement->bindValue(":pending_count", '0');
                            $insertStatement->bindValue(":status", '1');
                            $insertStatement->bindValue(":currency", $storeItem['currency']);
                            $insertStatement->bindValue(":currency_archive", $storeItem['new_currency_archive']);
                            $insertStatement->execute();
                            if(!$insertStatement->rowCount()) throw new Exception("Could'n add new store item");
                        }
                    }

                    $i++;
                }

            } else {
                throw new Exception("Count is not array");
            }

            $this->db->commit();
            $response = ['status' => 1, 'message' => "Əməliyyat uğurla başa çatdı"];
            return $response;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            $this->db->rollBack();
            return $response;

        }

    }

    public function deleteGoods($data){

        try{

            $this->db->beginTransaction();

            /**
             *  Invoice Detail Section
             */
            $stmt = $this->db->prepare("UPDATE vl1_InvoiceDetailModel SET `count` = `count` - :count, buy_total = buy_price * `count`, sell_total = sell_price * `count`
            WHERE user_id = :user_id AND invoice_id = :invoice_id AND id = :invoice_detail_id");
            $stmt->bindValue(":count", $data['count']);
            $stmt->bindValue(":user_id", $data['user_id']);
            $stmt->bindValue(":invoice_id", $data['invoice_id']);
            $stmt->bindValue(":invoice_detail_id", $data['invoice_detail_id']);
            $stmt->execute();

            if($stmt->rowCount() <= 0){
                $this->db->rollBack();
                return false;
            }

            $stmt = $this->db->prepare("DELETE FROM vl1_InvoiceDetailModel WHERE `count` = 0 AND invoice_id = :invoice_id AND user_id = :user_id");
            $stmt->bindValue(":user_id", $data['user_id']);
            $stmt->bindValue(":invoice_id", $data['invoice_id']);
            $stmt->execute();
            // Invoice detail

            /**
             *  Invoice model
             */
            $stmt = $this->db->prepare("SELECT COUNT(id) AS cnt FROM vl1_InvoiceDetailModel WHERE invoice_id = :invoice_id AND user_id = :user_id");
            $stmt->bindValue(":user_id", $data['user_id']);
            $stmt->bindValue(":invoice_id", $data['invoice_id']);
            $stmt->execute();
            $tmp = $stmt->fetch(PDO::FETCH_ASSOC);
            if($tmp['cnt'] > 0){
                $stmt = $this->db->prepare("UPDATE vl1_InvoiceModel SET amount = amount - :amount WHERE id = :invoice_id AND user_id = :user_id");
                $stmt->bindValue(":amount", $data['count'] * $data['buy_price']);
                $stmt->bindValue(":user_id", $data['user_id']);
                $stmt->bindValue(":invoice_id", $data['invoice_id']);
                $stmt->execute();
            } else {
                $stmt = $this->db->prepare("UPDATE vl1_InvoiceModel SET status = '0', amount = 0 WHERE id = :invoice_id AND user_id = :user_id");
                $stmt->bindValue(":user_id", $data['user_id']);
                $stmt->bindValue(":invoice_id", $data['invoice_id']);
                $stmt->execute();
            }
            // Invoice model

            /**
             *  Store model
             */
            $stmt = $this->db->prepare("UPDATE vl1_StoreModel SET `count` = `count` - :count WHERE id = :store_item_id
            AND user_id = :user_id AND subject_id = :subject_id");
            $stmt->bindValue(":count", $data['count']);
            $stmt->bindValue(":store_item_id", $data['store_item_id']);
            $stmt->bindValue(":user_id", $data['user_id']);
            $stmt->bindValue(":subject_id", $data['subject_id']);
            $stmt->execute();
            // Store model

            /**
             *  Conrtagent model
             */

            $stmt = $this->db->prepare("SELECT contragent_id FROM vl1_InvoiceModel WHERE id = :id");
            $stmt->bindValue(":id", $data['invoice_id']);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            $contragent_id = $res['contragent_id'];
            if($contragent_id > 0){
                $stmt = $this->db->prepare("UPDATE vl1_ContragentModel SET debt = debt - :amount WHERE id = :contragent_id AND user_id = :user_id");
                $stmt->bindValue(":amount", $data['count'] * $data['buy_price']);
                $stmt->bindValue(":user_id", $data['user_id']);
                $stmt->bindValue(":contragent_id", $contragent_id);
                $stmt->execute();
            }
            // Contragent model

            $this->db->commit();
            return true;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            $this->db->rollBack();
            return false;

        }

    }

}
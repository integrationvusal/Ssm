<?php

class InvoiceModel{

    public $id;
    public $user_id;
    public $contragent_id;
    public $subject_id;
    public $type;
    public $serial;
    public $date;
    public $amount;
    public $status;
    public $notes;

    private $tableName = "vl1_InvoiceModel";

    protected $db;

    public function __construct(){

        $this->db = (new DB())->start();

    }

    public function createInvoice($array, $db){
        try{
            $totalAmountBinded = false;
            if(isset($array['contragent_id'])){
                
                $contragent = (new ContragentModel())->getOne($array);

                $stmt = $db->prepare("INSERT INTO " . $this->tableName . "(user_id, contragent_id, operator, subject_id, `type`, serial, `date`, status, notes, amount, payed, client_id, total_amount, subject_to)
                VALUES(:user_id, :contragent_id, :operator, :subject_id, :type, :serial, :date, :status, :notes, :amount, :payed, :client_id, :total_amount, :subject_to)");
                $stmt->bindValue(":contragent_id", $array['contragent_id']);
                $stmt->bindValue(":total_amount", $contragent['debt']);
                $totalAmountBinded = true;
            } else {
                $stmt = $db->prepare("INSERT INTO " . $this->tableName . "(user_id, operator, subject_id, `type`, serial, `date`, status, notes, amount, payed, client_id, total_amount, subject_to)
                VALUES(:user_id, :operator, :subject_id, :type, :serial, :date, :status, :notes, :amount, :payed, :client_id, :total_amount, :subject_to)");
            }
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":operator", $array['operator']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->bindValue(":type", $array['invoice_type']);
            $stmt->bindValue(":serial", $array['invoice_serial']);
            $stmt->bindValue(":date", $array['date']);
            if(array_key_exists("invoice_status", $array)){
                $stmt->bindValue(":status", $array['invoice_status']);
            } else {
                $stmt->bindValue(":status", "2");
            }
            if(array_key_exists("amount", $array)){
                $stmt->bindValue(":amount", $array['amount']);
            } else {
                $stmt->bindValue(":amount", "0");
            }
            if(array_key_exists("debtamount", $array)){
                $stmt->bindValue(":payed", $array['debtamount']);
            } else {
                $stmt->bindValue(":payed", "0");
            }

            if(array_key_exists("client_id", $array) && $array['client_id']){
                $stmt->bindValue(":client_id", $array['client_id']);

                $client = (new ClientModel())->getOne([
                    'id'=>$array['client_id'],
                    'currency'=>$array['currency']
                ]);

                if(!$client['debt']){
                    $stmt = $db->prepare("INSERT INTO vl1_ClientDebtModel (client_id, debt, currency, currency_archive)VALUES(:client_id, :debt, :currency, :currency_archive)");
                    $stmt->bindValue(":debt", 0, PDO::PARAM_STR);
                    $stmt->bindValue(":currency", $array['currency'], PDO::PARAM_INT);
                    $stmt->bindValue(":currency_archive", $array['currency_archive'], PDO::PARAM_STR);
                    $stmt->bindValue(":client_id", $array['client_id'], PDO::PARAM_INT);
                    $stmt->execute();
                    $client['debt'] = 0;
                }

                $stmt->bindValue(":total_amount", $client['debt'], PDO::PARAM_STR);

            } else {
                $stmt->bindValue(":client_id", 0);
                if(!$totalAmountBinded) $stmt->bindValue(":total_amount", 0);
            }

            if(array_key_exists("notes", $array)){
                $stmt->bindValue(":notes", $array['notes']);
            } else {
                $stmt->bindValue(":notes", "");
            }

            if(array_key_exists("subject_to", $array)){
                $stmt->bindValue(":subject_to", $array['subject_to']);
            } else {
                $stmt->bindValue(":subject_to", "0");
            }


            if($stmt->execute()){
                return $db->lastInsertId();

            } else {
                throw new Exception("Couldn't insert invoice");

            }

        } catch(Exception $e){

            throw $e;
        }

    }

    public function processInvoiceNumber($array, $db){

        try{

            $stmt = $db->prepare("SELECT * FROM " . $this->tableName . " WHERE serial=:serial AND user_id=:user_id AND status='1'");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":serial", $array['invoice_serial']);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if($res) return false;

            if(isset($array['contragent_id'])) {
                $stmt = $db->prepare("SELECT id FROM " . $this->tableName . " WHERE serial=:serial AND user_id=:user_id AND contragent_id=:contragent_id");
                $stmt->bindValue(":contragent_id", $array['contragent_id']);
            } else {
                $stmt = $db->prepare("SELECT id FROM " . $this->tableName . " WHERE serial=:serial AND user_id=:user_id");}
                $stmt->bindValue(":user_id", $array['user_id']);
                $stmt->bindValue(":serial", $array['invoice_serial']);
                $stmt->execute();
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if(isset($res['id']) && $res['id'] > 0) return $res['id'];
            else {
                return $this->createInvoice($array, $db);
            }

        } catch(Exception $e){
            throw $e;
        }

    }

    public function getNextInvoiceNumber($array, $serial = false){

        try{

            if($serial){
                $serial = $array['serial'];
                $firstSide = substr($serial, 0, 8);
                $numberCode = substr($serial, -6);
                $numberCode = (int)$numberCode + 1;
                $zeros = "";
                for($i = 0; $i < 6-strlen($numberCode); $i++){
                    $zeros .= "0";
                }
                return $firstSide . $zeros . $numberCode;
            }

            $stmt = $this->db->prepare("SELECT serial FROM " . $this->tableName . " WHERE `type`=:type AND user_id=:user_id ORDER BY id DESC LIMIT 1");
            $stmt->bindValue(":type", $array['type']);
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if($res){

                $serial = $res['serial'];
                $firstSide = substr($serial, 0, 8);
                $numberCode = substr($serial, -6);
                $numberCode = (int)$numberCode + 1;
                $zeros = "";
                for($i = 0; $i < 6-strlen($numberCode); $i++){
                    $zeros .= "0";
                }
                return $firstSide . $zeros . $numberCode;

            } else {

                $invoiceType = Application::$settings['invoice_types'][$array['type']];
                $serial = 10000 + $array['user_id'];
                $serial = $invoiceType['code'] . $serial . "000000";
                return $serial;

            }

        } catch(Exception $e) {

            return false;

        }

    }

    public function approveInvoice($array, $db){

        try{

            $invoiceDetailModel = new InvoiceDetailModel();

            $total_buy = $invoiceDetailModel->getTotalAmount($array, $db);

            $invoiceDetailModel->clearInvoiceDetails($array, $db);

            $contragentModel = new ContragentModel();

            if(isset($array['contragent_id'])){

                $contragent = $contragentModel->getOne($array);
                $stmt = $db->prepare("UPDATE " . $this->tableName . " SET status = '1', amount = :amount, total_amount = :total_amount WHERE user_id=:user_id AND id = :invoice_id");
                $stmt->bindValue(":total_amount", $contragent['debt']);

            } else {
                $stmt = $db->prepare("UPDATE " . $this->tableName . " SET status = '1', amount = :amount WHERE user_id=:user_id AND id = :invoice_id");
            }
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":invoice_id", $array['invoice_id']);
            $stmt->bindValue(":amount", $total_buy);
            $stmt->execute();

            $array['debt'] = $total_buy;

            return $contragentModel->increaseDebt($array, $db);

        } catch(Exception $e){

            throw $e;

        }

    }

    public function rejectInvoice($array, $db){

        try{

            $invoiceDetailModel = new InvoiceDetailModel();

            $invoiceDetailModel->clearInvoiceDetails($array, $db);
            $invoiceDetailModel->deleteDetails($array, $db);

            $stmt = $db->prepare("DELETE FROM " . $this->tableName . " WHERE user_id=:user_id AND id = :invoice_id");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":invoice_id", $array['invoice_id']);
            $stmt->execute();

            return $stmt->rowCount();

        } catch(Exception $e){

            throw $e;

        }

    }

    public function deleteInvoice($array, $db){

        try{

            if(!isset($array['invoice_id']) || (int)$array['invoice_id'] <= 0) {
                throw new Exception("Invoice id not found");
            }
            $stmt = $db->prepare("DELETE FROM " . $this->tableName . " WHERE user_id=:user_id AND subject_id=:subject_id AND serial=:serial
            AND status = '2' AND id=:invoice_id");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->bindValue(":serial", $array['invoice_serial']);
            $stmt->bindValue(":invoice_id", $array['invoice_id']);
            $stmt->execute();

            return $stmt->rowCount();

        } catch(Exception $e) {

            throw $e;

        }

    }

    public function approveSellInvoice($array, $db){
        try{

            if(isset($array['client']) && $array['client'] > 0) {
                $client = (new ClientModel())->getOne([
                    'id' => $array['client'],
                    'currency' => $array['currency']
                ]);
                if(!$client['debt']){
                    $stmt = $db->prepare("INSERT INTO vl1_ClientDebtModel (client_id, debt, currency, currency_archive)VALUES(:client_id, :debt, :currency, :currency_archive)");
                    $stmt->bindValue(":debt", 0, PDO::PARAM_STR);
                    $stmt->bindValue(":currency", $array['currency'], PDO::PARAM_INT);
                    $stmt->bindValue(":currency_archive", $array['currency_archive'], PDO::PARAM_STR);
                    $stmt->bindValue(":client_id", $array['client'], PDO::PARAM_INT);
                    $stmt->execute();
                    $client['debt'] = 0;
                }
                $stmt = $db->prepare("UPDATE " . $this->tableName . " SET operator = :operator, status = '1', client_id=:client_id, amount = :amount, payed = :payed, total_amount = :total_amount, date = :date
                    WHERE user_id=:user_id AND id = :invoice_id");
                $stmt->bindValue(":total_amount", $client['debt'], PDO::PARAM_STR);

            } elseif(array_key_exists('discount', $array) && $array['discount'] == 1) {

                $discountCard = (new DiscountCardModel())->getCardInfoByNumber(['user_id' => $array['user_id'], 'subject_id' => $array['subject_id'], 'card_number' => $array['card_number']]);

                $stmt = $db->prepare("UPDATE " . $this->tableName . " SET operator = :operator, status = '1', client_id=:client_id, amount = :amount, payed = :payed, discount = :discount, discount_card_number = :discount_card_number, discount_type = :discount_type, discount_value = :discount_value, discounted_amount = :discounted_amount, date = :date
                WHERE user_id=:user_id AND id = :invoice_id");
                $stmt->bindValue(":discount", $array['discount']);
                $stmt->bindValue(":discount_card_number", $array['card_number']);
                $stmt->bindValue(":discount_type", $discountCard['card_type']);
                $stmt->bindValue(":discount_value", $array['discount_or_bonus']);
                $stmt->bindValue(":discounted_amount", $array['discounted_amount']);
                $array['amount'] = $array['amount'] - $array['discounted_amount'];
            } else {
                $stmt = $db->prepare("UPDATE " . $this->tableName . " SET operator = :operator, status = '1', client_id=:client_id, amount = :amount, payed = :payed, date = :date
                WHERE user_id=:user_id AND id = :invoice_id");
            }

            $user_tmp = RBACController::getUser();
            $operator = 0;
            if($user_tmp['type'] == 1){
                $operator = $user_tmp['operator']['id'];
            }

            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":invoice_id", $array['invoice_id']);
            $stmt->bindValue(":payed", $array['debtamount']);
            $stmt->bindValue(":amount", $array['amount']);
            $stmt->bindValue(":client_id", $array['client']);
            $stmt->bindValue(":operator", $operator);
			$stmt->bindValue(":date", $array['date']);
            $stmt->execute();


            return (new ClientModel())->increaseDebt($array, $db);

        } catch(Exception $e){

            Logger::writeExceptionLog($e);
            throw $e;

        }
    }

    public function getOne($array){

        try{

            $invoice_status = '1';
            if(array_key_exists('invoice_status', $array)) $invoice_status = $array['invoice_status'];
            $stmt = $this->db->prepare("SELECT invoice.*, contragent.name AS contragent_name, client.name AS client_name
            FROM vl1_InvoiceModel AS invoice
            LEFT JOIN vl1_ContragentModel AS contragent ON invoice.contragent_id = contragent.id
            LEFT JOIN vl1_ClientModel AS client ON invoice.client_id = client.id
            WHERE invoice.user_id = :user_id  AND invoice.status LIKE :invoice_status AND (invoice.subject_id = :subject_id OR invoice.subject_to = :subject_id)
            AND invoice.id = :invoice_id");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->bindValue(":invoice_id", $array['invoice_id']);
            $stmt->bindValue(":invoice_status", $array['invoice_status']);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch(Exception $e){

            Logger::writeExceptionLog($e);
            throw $e;

        }

    }

    public function getAll($array, $limit = 0, $offset = 0){

        try{

            $stmt = $this->db->prepare("SELECT detail.currency, detail.currency_archive, invoice.*, contragent.name AS contragent_name, client.name AS client_name FROM vl1_InvoiceModel AS invoice
            LEFT JOIN vl1_ContragentModel AS contragent ON invoice.contragent_id = contragent.id
            LEFT JOIN vl1_ClientModel AS client ON invoice.client_id = client.id
            LEFT JOIN vl1_InvoiceDetailModel AS detail ON invoice.id = detail.invoice_id
            WHERE invoice.user_id = :user_id  AND invoice.status = '1' AND (invoice.subject_id = :subject_id OR invoice.subject_to = :subject_id) ORDER BY invoice.date DESC, id DESC
            LIMIT :limit OFFSET :offset");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e){

            Logger::writeExceptionLog($e);
            throw $e;

        }

    }

    public function getAllForGoodsId($array, $limit = 0, $offset = 0){

        try{

            $stmt = $this->db->prepare("SELECT invoice.id AS invoice_id,
            detail.id AS invoice_detail_id,
            detail.count AS goods_count,
            detail.buy_price AS goods_buy_price,
            CONCAT(invoice.serial, ' | ', LEFT(invoice.date, 10), ' | ', contragent.name, ' | ', detail.short_info, ' | ', detail.buy_price) AS invoice_info
            FROM vl1_InvoiceModel AS invoice
            LEFT JOIN vl1_ContragentModel AS contragent ON invoice.contragent_id = contragent.id
            LEFT JOIN vl1_ClientModel AS client ON invoice.client_id = client.id
            LEFT JOIN vl1_InvoiceDetailModel AS detail ON invoice.id = detail.invoice_id AND detail.goods_id = :goods_id
            WHERE detail.buy_price = :buy_price AND invoice.type = 0 AND detail.id IS NOT NULL AND contragent.id IS NOT NULL AND
            invoice.user_id = :user_id AND (invoice.subject_id = :subject_id OR invoice.subject_to = :subject_id)
            ORDER BY invoice.date DESC, invoice.id DESC LIMIT :limit OFFSET :offset");
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $array['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":goods_id", $array['goods_id'], PDO::PARAM_INT);
            $stmt->bindValue(":buy_price", $array['buy_price'], PDO::PARAM_STR);
            $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e){

            Logger::writeExceptionLog($e);
            throw $e;

        }

    }

    public function searchAll($array){

        try{

            Logger::writeLog($array);
            if($array['date_from'] == "") $array['date_from'] = "0000-00-00";
            if($array['date_to'] == "") $array['date_to'] = "3000-00-00";
            if($array['invoice_type'] == "") {
                $array['invoice_type'] = 0;
                $invoice_type = "invoice.type >= :invoice_type";
            } else {
                $invoice_type = "invoice.type = :invoice_type";
            }

            $stmt = $this->db->prepare("SELECT detail.currency, detail.currency_archive, invoice.*, contragent.name AS contragent_name, client.name AS client_name FROM vl1_InvoiceModel AS invoice
            LEFT JOIN vl1_ContragentModel AS contragent ON invoice.contragent_id = contragent.id
            LEFT JOIN vl1_ClientModel AS client ON invoice.client_id = client.id
            LEFT JOIN vl1_InvoiceDetailModel AS detail ON invoice.id = detail.invoice_id
            WHERE invoice.user_id = :user_id AND invoice.subject_id = :subject_id
            AND invoice.date BETWEEN :date_from AND :date_to
            AND " . $invoice_type . "
            AND invoice.serial LIKE :invoice_serial AND invoice.status = '1'
            ORDER BY invoice.date DESC, invoice.id DESC");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->bindValue(":date_from", $array['date_from']);
            $stmt->bindValue(":date_to", $array['date_to']);
            $stmt->bindValue(":invoice_type", $array['invoice_type']);
            $stmt->bindValue(":invoice_serial", '%' . $array['invoice_serial'] .'%');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e){

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getCountSearchAll($array){

        try{

            if(!$array['date_from']) $array['date_from'] = "0000-00-00";
            if(!$array['date_to']) $array['date_to'] = "3000-00-00";
            if(!$array['invoice_type']) {
                $array['invoice_type'] = 0;
                $array['invoice_type'] = "invoice.type >= :invoice_type";
            } else {
                $array['invoice_type'] = "invoice.type = :invoice_type";
            }

            $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM vl1_InvoiceModel AS invoice
            WHERE invoice.user_id = :user_id AND invoice.subject_id = :subject_id
            AND invoice.date BETWEEN :date_from AND :date_to
            AND " . $array['invoice_type'] . "
            AND invoice.serial LIKE :invoice_serial");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->bindValue(":date_from", $array['date_from']);
            $stmt->bindValue(":date_to", $array['date_to']);
            $stmt->bindValue(":invoice_type", $array['invoice_type']);
            $stmt->bindValue(":invoice_serial", '%' . $array['invoice_serial'] .'%');
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res['cnt'];

        } catch(Exception $e){

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function checkInvoice($serial){
        try{
            $stmt = $this->db->prepare("SELECT id FROM vl1_InvoiceModel WHERE serial = :serial");
            $stmt->bindValue(":serial", $serial);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res?$res['id']:0;
        } catch(Exception $e){

            Logger::writeExceptionLog($e);
            throw $e;

        }
    }

    public function getCountAll($array){
        try{

            $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM vl1_InvoiceModel WHERE user_id = :user_id AND subject_id = :subject_id");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if($res) return $res['cnt'];

        } catch(Exception $e){

            Logger::writeExceptionLog($e);
            throw $e;

        }
    }

    public function getInvoiceDetails($array){

        try{

            $response['status'] = 0;
            $permissions = PermissionController::getPermissions();
            switch($array['invoice_type']){
                case 0:
                    $stmt = $this->db->prepare("SELECT detail.*, invoice.notes FROM vl1_InvoiceDetailModel AS detail
                    LEFT JOIN vl1_InvoiceModel AS invoice ON invoice.id = detail.invoice_id
                    WHERE detail.user_id = :user_id AND detail.invoice_id = :invoice_id");
                    $stmt->bindValue(":user_id", $array['user_id']);
                    $stmt->bindValue(":invoice_id", $array['invoice_id']);
                    $stmt->execute();
                    $response['status'] = 1;
                    $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $response['attrs'] = [
                        'short_info' => 'Məlumat',
                        'count' => 'Say',
                        'buy_price' => 'Alış qiyməti',
                        'buy_total' => 'Ümumi alış qiyməti',
                        'sell_price' => 'Satış qiyməti',
                        'sell_total' => 'Ümumi satış qiyməti',
                        'notes' => 'Qeyd',
                        'date' => 'Tarix'
                    ];
                    if(!$permissions['buy_price']) {
                        unset($response['attrs']['buy_price']);
                        unset($response['attrs']['buy_total']);
                    }
                    return $response;
                    break;
                case 1:
                    $stmt = $this->db->prepare("SELECT store.*, invoice.notes, invoice.date, invoice.discount, invoice.discount_card_number,
                    invoice.discount_type, invoice.discount_value, invoice.discounted_amount
                    FROM vl1_SellModel AS store
                    LEFT JOIN vl1_InvoiceModel AS invoice ON invoice.id = store.invoice_id
                    WHERE store.user_id = :user_id AND store.invoice_id = :invoice_id");
                    $stmt->bindValue(":user_id", $array['user_id']);
                    $stmt->bindValue(":invoice_id", $array['invoice_id']);
                    $stmt->execute();
                    $response['status'] = 1;
                    $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $response['attrs'] = [
                        'barcode' => 'Barkod',
                        'goods_code' => 'Malın kodu',
                        'short_info' => 'Məlumat',
                        'count' => 'Say',
                        'buy_price' => 'Alış qiyməti',
                        'sell_price' => 'Satış qiyməti',
                        'seller_id' => 'Satıcı',
                        'notes' => 'Qeyd',
                        'date' => 'Tarix'
                    ];
                    if(!$permissions['buy_price']) unset($response['attrs']['buy_price']);
                    return $response;
                    break;
                case 2:
                case 3:
                case 4:
                    $stmt = $this->db->prepare("SELECT boxh.*, invoice.notes, invoice.subject_id, invoice.serial FROM vl1_CashboxHistoryModel AS boxh
                            LEFT JOIN vl1_InvoiceModel AS invoice ON invoice.id = boxh.invoice_id
                            WHERE boxh.invoice_id = :invoice_id");
                    $stmt->bindValue(":invoice_id", $array['invoice_id']);
                    $stmt->execute();
                    $response['status'] = 1;
                    $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $response['attrs'] = [
                        'operation_type' => 'Əməliyyat növü',
                        'amount' => 'Məbləğ',
                        'subject_name' => 'Ödənilən kassa',
                        'total_amount' => 'Balans',
                        'notes' => 'Qeyd'
                    ];

                    $stmt = $this->db->prepare("SELECT invoice.subject_id, subject.name FROM vl1_InvoiceModel AS invoice
                            LEFT JOIN vl1_SubjectModel AS subject ON invoice.subject_id = subject.id
                            WHERE invoice.serial = :serial AND invoice.user_id = :user_id AND invoice.subject_id != :subject_id");
                    $stmt->bindValue(":serial", $response['data'][0]['serial']);
                    $stmt->bindValue(":user_id", $array['user_id']);
                    $stmt->bindValue(":subject_id", $response['data'][0]['subject_id']);
                    $stmt->execute();
                    $res = $stmt->fetch(PDO::FETCH_ASSOC);

                    $response['data'][0]['destionation_id'] = $res['subject_id'];
                    $response['data'][0]['subject_name'] = $res['name'];

                    return $response;
                    break;
                case 5:
                    $stmt = $this->db->prepare("SELECT boxh.*, invoice.notes, invoice.payed, contragent.name AS contragent_name
                            FROM vl1_CashboxHistoryModel AS boxh
                            LEFT JOIN vl1_InvoiceModel AS invoice ON invoice.id = boxh.invoice_id
                            LEFT JOIN vl1_ContragentModel AS contragent ON contragent.id = invoice.contragent_id
                            WHERE boxh.invoice_id = :invoice_id");
                    $stmt->bindValue(":invoice_id", $array['invoice_id']);
                    $stmt->execute();
                    $response['status'] = 1;
                    $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $response['attrs'] = [
                        'operation_type' => 'Əməliyyat növü',
                        'contragent_name' => 'Kontragent',
                        'amount' => 'Məbləğ',
                        'payed' => 'Qalıq borc',
                        'total_amount' => 'Balans',
                        'notes' => 'Qeyd'
                    ];
                    return $response;
                    break;
                case 6:
                    $stmt = $this->db->prepare("SELECT boxh.*, invoice.notes, invoice.payed, client.name AS client_name
                            FROM vl1_CashboxHistoryModel AS boxh
                            LEFT JOIN vl1_InvoiceModel AS invoice ON invoice.id = boxh.invoice_id
                            LEFT JOIN vl1_ClientModel AS client ON client.id = invoice.client_id
                            WHERE boxh.invoice_id = :invoice_id");
                    $stmt->bindValue(":invoice_id", $array['invoice_id']);
                    $stmt->execute();
                    $response['status'] = 1;
                    $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $response['attrs'] = [
                        'operation_type' => 'Əməliyyat növü',
                        'client_name' => 'Müştəri',
                        'amount' => 'Məbləğ',
                        'payed' => 'Qalıq borc',
                        'total_amount' => 'Balans',
                        'notes' => 'Qeyd'
                    ];
                    return $response;
                    break;
                case 7:
                    $stmt = $this->db->prepare("SELECT detail.*, invoice.notes FROM vl1_InvoiceDetailModel AS detail
                    LEFT JOIN vl1_InvoiceModel AS invoice ON invoice.id = detail.invoice_id
                    WHERE detail.user_id = :user_id AND detail.invoice_id = :invoice_id");
                    $stmt->bindValue(":user_id", $array['user_id']);
                    $stmt->bindValue(":invoice_id", $array['invoice_id']);
                    $stmt->execute();
                    $response['status'] = 1;
                    $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $response['attrs'] = [
                        'short_info' => 'Məlumat',
                        'count' => 'Say',
                        'buy_price' => 'Alış qiyməti',
                        'buy_total' => 'Ümumi alış qiyməti',
                        'sell_price' => 'Satış qiyməti',
                        'sell_total' => 'Ümumi satış qiyməti',
                        'notes' => 'Qeyd',
                        'date' => 'Tarix'
                    ];

                    if(!$permissions['buy_price']) {
                        unset($response['attrs']['buy_price']);
                        unset($response['attrs']['buy_total']);
                    }
                    return $response;
                    break;
                case 8:
                    $stmt = $this->db->prepare("SELECT detail.*, subject.name AS subject_name, invoice.notes FROM vl1_InvoiceDetailModel AS detail
                        LEFT JOIN vl1_InvoiceModel AS invoice ON invoice.id = detail.invoice_id
                        LEFT JOIN vl1_SubjectModel AS subject ON invoice.subject_id = subject.id
                        WHERE detail.user_id = :user_id AND detail.invoice_id = :invoice_id");
                    $stmt->bindValue(":user_id", $array['user_id']);
                    $stmt->bindValue(":invoice_id", $array['invoice_id']);
                    $stmt->execute();
                    $response['status'] = 1;
                    $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $response['attrs'] = [
                        'subject_name' => 'Anbar',
                        'short_info' => 'Məlumat',
                        'count' => 'Say',
                        'buy_price' => 'Alış qiyməti',
                        'buy_total' => 'Ümumi alış qiyməti',
                        'sell_price' => 'Satış qiyməti',
                        'sell_total' => 'Ümumi satış qiyməti',
                        'notes' => 'Qeyd',
                        'date' => 'Tarix'
                    ];

                    if(!$permissions['buy_price']) {
                        unset($response['attrs']['buy_price']);
                        unset($response['attrs']['buy_total']);
                    }
                    return $response;
                    break;
                case 9:
                    $stmt = $this->db->prepare("SELECT detail.*, invoice.notes FROM vl1_InvoiceDetailModel AS detail
                    LEFT JOIN vl1_InvoiceModel AS invoice ON invoice.id = detail.invoice_id
                    WHERE detail.user_id = :user_id AND detail.invoice_id = :invoice_id");
                    $stmt->bindValue(":user_id", $array['user_id']);
                    $stmt->bindValue(":invoice_id", $array['invoice_id']);
                    $stmt->execute();
                    $response['status'] = 1;
                    $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $response['attrs'] = [
                        'short_info' => 'Məlumat',
                        'count' => 'Say',
                        'buy_price' => 'Alış qiyməti',
                        'buy_total' => 'Ümumi alış qiyməti',
                        'sell_price' => 'Satış qiyməti',
                        'sell_total' => 'Ümumi satış qiyməti',
                        'notes' => 'Qeyd',
                        'date' => 'Tarix'
                    ];

                    if(!$permissions['buy_price']) {
                        unset($response['attrs']['buy_price']);
                        unset($response['attrs']['buy_total']);
                    }
                    return $response;
                    break;
                case 10:
                    $stmt = $this->db->prepare("SELECT detail.*, invoice.notes FROM vl1_InvoiceDetailModel AS detail
                        LEFT JOIN vl1_InvoiceModel AS invoice ON invoice.id = detail.invoice_id
                        WHERE detail.user_id = :user_id AND detail.invoice_id = :invoice_id");
                    $stmt->bindValue(":user_id", $array['user_id']);
                    $stmt->bindValue(":invoice_id", $array['invoice_id']);
                    $stmt->execute();
                    $response['status'] = 1;
                    $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $response['attrs'] = [
                        'short_info' => 'Məlumat',
                        'count' => 'Say',
                        'buy_price' => 'Alış qiyməti',
                        'buy_total' => 'Ümumi alış qiyməti',
                        'sell_price' => 'Satış qiyməti',
                        'sell_total' => 'Ümumi satış qiyməti',
                        'notes' => 'Qeyd',
                        'date' => 'Tarix'
                    ];

                    if(!$permissions['buy_price']) {
                        unset($response['attrs']['buy_price']);
                        unset($response['attrs']['buy_total']);
                    }
                    return $response;
                    break;
                case 11:
                    $stmt = $this->db->prepare("SELECT detail.*, invoice.notes FROM vl1_InvoiceDetailModel AS detail
                        LEFT JOIN vl1_InvoiceModel AS invoice ON invoice.id = detail.invoice_id
                        WHERE detail.user_id = :user_id AND detail.invoice_id = :invoice_id");
                    $stmt->bindValue(":user_id", $array['user_id']);
                    $stmt->bindValue(":invoice_id", $array['invoice_id']);
                    $stmt->execute();
                    $response['status'] = 1;
                    $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $response['attrs'] = [
                        'short_info' => 'Məlumat',
                        'sell_total' => 'Ümumi qiymət',
                        'notes' => 'Qeyd',
                        'date' => 'Tarix'
                    ];
                    return $response;
                    break;
                case 12:
                    $stmt = $this->db->prepare("SELECT detail.*, invoice.notes FROM vl1_InvoiceDetailModel AS detail
                        LEFT JOIN vl1_InvoiceModel AS invoice ON invoice.id = detail.invoice_id
                        WHERE detail.user_id = :user_id AND detail.invoice_id = :invoice_id");
                    $stmt->bindValue(":user_id", $array['user_id']);
                    $stmt->bindValue(":invoice_id", $array['invoice_id']);
                    $stmt->execute();
                    $response['status'] = 1;
                    $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $response['attrs'] = [
                        'short_info' => 'Məlumat',
                        'buy_total' => 'Ümumi qiymət',
                        'notes' => 'Qeyd',
                        'date' => 'Tarix'
                    ];
                    return $response;
                    break;
                default:
                    return $response;
                break;
            }

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

}
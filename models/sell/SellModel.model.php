<?php

class SellModel{

    public $id;
    public $user_id;
    public $subject_id;
    public $store_item_id;
    public $invoice_id;
    public $goods_type;
    public $goods_id;
    public $barcode;
    public $goods_code;
    public $short_info;
    public $count;
    public $buy_price;
    public $sell_price;
    public $seller_id;
    public $status;

    private $tableName = "vl1_SellModel";

    protected $db;

    public function __construct(){

        $this->db = (new DB())->start();

    }

    public function getAllPendings($array){

        try{

            $stmt = $this->db->prepare("SELECT sell.*, invoice.serial, invoice.date, invoice.notes, invoice.id AS invoice_id, store.count AS remain_count, store.currency, store.currency_archive
            FROM " . $this->tableName . " AS sell
            LEFT JOIN vl1_InvoiceModel AS invoice ON sell.invoice_id = invoice.id
            LEFT JOIN vl1_StoreModel AS store ON sell.store_item_id = store.id
            WHERE sell.user_id=:user_id AND sell.subject_id=:subject_id AND sell.status='2'");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            if($stmt->execute()){
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            return false;
        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getAllByCodeAndBarcode($array){

        try{

            $subject = SubjectController::getCurrentSubject();
            $modelName = Application::$settings['goods_types'][$subject['goods_type']]['model_name'];
            $tableName = $modelName::$tableName;

            $stmt = $this->db->prepare("SELECT sell.*, IFNULL(cur.name, 'AZN') currency, LEFT(invoice.date, 10) AS `date`, client.name AS cname FROM " . $this->tableName . " AS sell
            LEFT JOIN vl1_InvoiceModel AS invoice ON sell.invoice_id = invoice.id
            LEFT JOIN vl1_InvoiceDetailModel AS detail ON detail.invoice_id = invoice.id
            LEFT JOIN vl1_CurrencyModel AS cur ON cur.id = detail.currency
            LEFT JOIN vl1_ClientModel AS client ON invoice.client_id = client.id
            WHERE sell.user_id=:user_id AND sell.subject_id=:subject_id AND sell.status != '2'
            AND (sell.goods_code LIKE :search_code OR sell.barcode LIKE :search_code) ORDER BY date DESC");
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

    public function getAllByCodeAndBarcodeAndInfo($array){

        try{

            $subject = SubjectController::getCurrentSubject();
            $modelName = Application::$settings['goods_types'][$subject['goods_type']]['model_name'];
            $tableName = $modelName::$tableName;

            if($array['subject_id'] == 0) $subject = ' sell.subject_id > :subject_id';
            else $subject = ' sell.subject_id = :subject_id ';

            $stmt = $this->db->prepare("SELECT sell.*, LEFT(invoice.date, 10) AS `date`, client.name AS cname FROM " . $this->tableName . " AS sell
            LEFT JOIN vl1_InvoiceModel AS invoice ON sell.invoice_id = invoice.id
            LEFT JOIN vl1_ClientModel AS client ON invoice.client_id = client.id
            WHERE sell.user_id=:user_id AND " . $subject . " AND sell.status != '2'
            AND (sell.goods_code LIKE :search_code OR sell.barcode LIKE :search_code OR sell.short_info LIKE :search_code) GROUP BY sell.goods_id ORDER BY date DESC");
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

    public function createSell($array){

        try{

            $this->db->beginTransaction();

            $storeItem = (new StoreModel())->getOne($array);

            if(!$storeItem){
                $this->db->rollBack();
                return false;
            }

            if(!isset($array['invoice_id']) || empty($array['invoice_id']) || $array['invoice_id'] == 0){
                $array['invoice_id'] = (new InvoiceModel())->processInvoiceNumber($array, $this->db);
            }

            if($array['add_type'] == 'update'){
                $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET count = :count + 1, sell_price = :sell_price
                    WHERE user_id=:user_id AND subject_id=:subject_id AND store_item_id = :store_item_id AND status = '2' AND id = :id");
                $stmt->bindValue(":count", $array['count']);
                $stmt->bindValue(":sell_price", $array['sell_price']);
                $stmt->bindValue(":user_id", $array['user_id']);
                $stmt->bindValue(":subject_id", $array['subject_id']);
                $stmt->bindValue(":store_item_id", $array['store_item_id']);
                $stmt->bindValue(":id", $array['sell_id']);
                $stmt->execute();
                if($stmt->rowCount()){
                    $this->db->commit();

                    $storeItem['sell_id'] = $array['sell_id'];
                    $storeItem['sell_invoice_id'] = $array['invoice_id'];
                    $storeItem['store_item_id'] = $array['store_item_id'];
                    return $storeItem;

                } else {

                    $this->db->rollBack();
                    return false;

                }
            }


            $stmt = $this->db->prepare("INSERT INTO " . $this->tableName . "(user_id, subject_id, store_item_id, invoice_id, goods_id, barcode, goods_code,
            short_info, `count`, buy_price, sell_price, seller_id, status)
            VALUES(:user_id, :subject_id, :store_item_id, :invoice_id, :goods_id, :barcode, :goods_code,
            :short_info, :count, :buy_price, :sell_price, :seller_id, :status)");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->bindValue(":store_item_id", $array['store_item_id']);
            $stmt->bindValue(":invoice_id", $array['invoice_id']);
            $stmt->bindValue(":goods_id", $storeItem['goods_id']);
            $stmt->bindValue(":barcode", $storeItem['barcode']);
            $stmt->bindValue(":goods_code", $storeItem['goods_code']);
            $stmt->bindValue(":short_info", $storeItem['short_info']);
            $stmt->bindValue(":count", '1');
            $stmt->bindValue(":buy_price", $storeItem['buy_price']);
            $stmt->bindValue(":sell_price", $storeItem['sell_price']);
            $stmt->bindValue(":seller_id", $array['operator']);
            $stmt->bindValue(":status", '2');

            if($stmt->execute()){

                $storeItem['sell_id'] = $this->db->lastInsertId();
                $storeItem['sell_invoice_id'] = $array['invoice_id'];
                $storeItem['store_item_id'] = $array['store_item_id'];
                $this->db->commit();
                return $storeItem;
            } else {

                $this->db->rollBack();
                return false;

            }

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function deleteSell($array){

        try{

            $stmt = $this->db->prepare("DELETE FROM " . $this->tableName . " WHERE user_id=:user_id AND subject_id=:subject_id AND id=:id");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->bindValue(":id", $array['sell_id']);
            $stmt->execute();
            return $stmt->rowCount();

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function rejectSell($array){

        try{

            $this->db->beginTransaction();

            (new InvoiceModel())->deleteInvoice($array, $this->db);

            $stmt = $this->db->prepare("DELETE FROM " . $this->tableName . " WHERE user_id=:user_id AND subject_id=:subject_id AND status = '2' AND id > 0");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->execute();

            $this->db->commit();
            return $stmt->rowCount();

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            $this->db->rollBack();
            return false;

        }

    }

    public function updateCountAndPrice($array){

        try{

            $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET count =:count, sell_price=:sell_price
            WHERE user_id=:user_id AND subject_id = :subject_id AND id = :id");
            $stmt->bindValue(":count", $array['count']);
            $stmt->bindValue(":sell_price", $array['sell_price']);
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":subject_id", $array['subject_id']);
            $stmt->bindValue(":id", $array['sell_id']);
            return $stmt->execute();

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function approveSell($array) {
        
        try{
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT `count`, store_item_id FROM " . $this->tableName . "
            WHERE user_id=:user_id AND invoice_id =:invoice_id AND subject_id=:subject_id AND status='2'");
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $array['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":invoice_id", $array['invoice_id'], PDO::PARAM_INT);
            $stmt->execute();


            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if((new StoreModel())->sellGoods($res, $this->db) && (new CashboxModel())->increaseAmount($array, $this->db) && (new InvoiceModel())->approveSellInvoice($array, $this->db)){     
                $discount_info = "";
                
                $currency = (new CurrencyModel())->getOne($array)['name']; 

                if(array_key_exists('discount', $array) && $array['discount'] == 1){
                    $discount_card_history = (new DiscountCardModel())->process($array, $this->db);
                    if(!$discount_card_history){
                        $this->db->rollBack();
                        return false;
                    }

                    $discount_card = (new DiscountCardModel())->getCardInfoByNumber($array);
                    if($discount_card['card_type'] == 'discount'){
                        $discount_info = "<table>";
                        $discount_info .= "<tr><td style='text-align: left;'>Endirim kartı</td><td style='text-align: right;'>" . $discount_card_history['card_number'] . "</td></tr>";
                        $discount_info .= "<tr><td style='text-align: left;'>Dərəcə</td><td style='text-align: right;'>" . $discount_card_history['previous_discount'] . " %</td></tr>";
                        $discount_info .= "<tr><td style='text-align: left;'>Endirim məbləği</td><td style='text-align: right;'>" . $discount_card_history['discounted_amount'] . "</td></tr>";
                        $discount_info .= "<tr><td style='text-align: left;'>Ödəniləcək məbləğ</td><td style='text-align: right;'>" . ($discount_card_history['total_amount'] - $discount_card_history['discounted_amount']) . "</td></tr>";
                        $discount_info .= "<tr><td style='text-align: left;'>Yeni dərəcə</td><td style='text-align: right;'>" . $discount_card_history['current_discount'] . " %</td></tr>";
                        $discount_info .= "<tr><td style='text-align: left;'>Qalıq</td><td style='text-align: right;'>" . $discount_card_history['remaining_amount'] . " AZN</td></tr>";
                        $discount_info .= "</table>";
                    } elseif($discount_card['card_type'] == 'bonus') {

                        $plus = $discount_card_history['plus'];
                        $minus = $discount_card_history['minus'];

                        $discount_info = "<table>";
                        $discount_info .= "<tr><td style='text-align: left;'>Bonus kartı</td><td style='text-align: right;'>" . $plus['card_number'] . "</td></tr>";
                        $discount_info .= "<tr><td style='text-align: left;'>İstifadə olunub</td><td style='text-align: right;'>" . ($minus['previous_discount'] - $minus['current_discount']) . "</td></tr>";
                        $discount_info .= "<tr><td style='text-align: left;'>Endirim məbləği</td><td style='text-align: right;'>" . $minus['discounted_amount'] . "</td></tr>";
                        $discount_info .= "<tr><td style='text-align: left;'>Ödəniləcək məbləğ</td><td style='text-align: right;'>" . ($minus['total_amount'] - $minus['discounted_amount']) . "</td></tr>";
                        $discount_info .= "<tr><td style='text-align: left;'>Qazanılıb</td><td style='text-align: right;'>" . ($plus['current_discount'] - $plus['previous_discount']) . "</td></tr>";
                        $discount_info .= "<tr><td style='text-align: left;'>Balans</td><td style='text-align: right;'>" . $plus['current_discount'] . "</td></tr>";
                        if($discount_card['rule']['save_remaining'] == 1)
                        $discount_info .= "<tr><td style='text-align: left;'>Qalıq</td><td style='text-align: right;'>" . $plus['remaining_amount'] . " AZN</td></tr>";
                        $discount_info .= "</table>";
                    }
					
                }


    
                /**
                 *  Receipt service code
                 */
                $stmt = $this->db->prepare("SELECT t.goods_id, t.goods_code, t.short_info, t.count, t.sell_price, s.currency, s.currency_archive FROM " . $this->tableName . " t
                    LEFT JOIN vl1_StoreModel s ON t.store_item_id = s.id
                    WHERE t.user_id=:user_id AND t.subject_id=:subject_id AND t.invoice_id = :invoice_id");
                $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
                $stmt->bindValue(":subject_id", $array['subject_id'], PDO::PARAM_INT);
                $stmt->bindValue(":invoice_id", $array['invoice_id'], PDO::PARAM_INT);
                $stmt->execute();
                $sold_goods = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $sold_goods_table = "<table class='table'><tr><td>Malın adı</td><td>Sayı</td><td>Qiymət</td><td>Ümumi</td></tr>";
                $tmp_sum = 0;
				
                foreach($sold_goods as $rec_goods){
                    $tmp_name = str_replace($rec_goods['goods_code'], "", $rec_goods['short_info']);
                    if(empty(trim(str_replace([","," "]," ", $tmp_name)))) $tmp_name = ucfirst($rec_goods['goods_code']);
                    $tmp_name = ucfirst(trim(trim($tmp_name, ",")));
                    $sold_goods_table .= "<tr><td>" . $tmp_name . "</td><td style='text-align: right;'>" . $rec_goods['count'] . "</td><td style='text-align: right;'>" . $rec_goods['sell_price'] . "</td><td style='text-align: right'>" . ($rec_goods['count'] * $rec_goods['sell_price']) . "</td></tr>";
                    $tmp_sum += $rec_goods['count'] * $rec_goods['sell_price'];

                    $invoiceDetailArray = [
                        "user_id" => $array['user_id'],
                        "invoice_id" => $array['invoice_id'],
                        "goods_id" => $rec_goods['goods_id'],
                        "short_info" => $rec_goods['short_info'],
                        "count" => $rec_goods['count'],
                        "buy_price" => 0,
                        "buy_total" => 0,
                        "sell_price" => $rec_goods['sell_price'],
                        "sell_total" => $rec_goods['sell_price'] * $rec_goods['count'],
                        "date" => $array['date'],
                        "currency" => $array['currency'],
                        "currency_archive" => $array['currency_archive'],
                    ];
					
                    (new InvoiceDetailModel())->createInvoiceDetail($invoiceDetailArray, $this->db);
                }
				
                $sold_goods_table .= "<tr><td style='text-align: left' colspan='2'><h4 style='color: black'>Cəmi:</h4></td><td style='text-align: right' colspan='2'><h4 style='color: black'>" . $tmp_sum . ' '.$currency."</h4></td></tr>";
                if($array['client'] <= 0){
                    $sold_goods_table .= "<tr><td style='text-align: left' colspan='2'>Ödənilib:</td><td style='text-align: right' colspan='2'>" . $array['received_payment'] . "</td></tr>";
                    $sold_goods_table .= "<tr><td style='text-align: left' colspan='2'>Qaytarılıb:</td><td style='text-align: right' colspan='2'>" . ($array['received_payment'] - $tmp_sum) . "</td></tr>";
                }
                if($array['client'] > 0){
                    $tmp_client = (new ClientModel())->getOne(['id' => $array['client'], 'currency'=>$array['currency']]);
                    $sold_goods_table .= "<tr><td style='text-align: left' colspan='2'>Müştəri</td><td style='text-align: right' colspan='2'>" . $tmp_client['name'] . "</td></tr>";
                    $sold_goods_table .= "<tr><td style='text-align: left' colspan='2'>İlkin ödəniş</td><td style='text-align: right' colspan='2'>" . $array['debtamount'] . "</td></tr>";
                    $sold_goods_table .= "<tr><td style='text-align: left' colspan='2'>Borc</td><td style='text-align: right' colspan='2'>" . ($array['amount'] - $array['debtamount']) . "</td></tr>";
                    $sold_goods_table .= "<tr><td style='text-align: left' colspan='2'>Ümumi borc</td><td style='text-align: right' colspan='2'>" . $tmp_client['debt'] . "</td></tr>";
                }
                $sold_goods_table .= "</table>";

                $receipt_attributes = (new ReceiptAttributesModel())->get(['user_id' => $array['user_id'], 'subject_id' => $array['subject_id']]);
                if($receipt_attributes){
                    $data['company_logo'] = $receipt_attributes['company_logo'];
                    $data['company_name'] = $receipt_attributes['company_name'];
                    $data['company_voen'] = $receipt_attributes['company_voen'];
                    $data['company_address'] = $receipt_attributes['company_address'];
                    $data['other_top'] = $receipt_attributes['other_top'];
                    $data['other_bottom'] = $receipt_attributes['other_bottom'];
                } else {
                    $data['company_logo'] = "";
                    $data['company_name'] = "";
                    $data['company_voen'] = "";
                    $data['company_address'] = "";
                    $data['other_top'] = "";
                    $data['other_bottom'] = "";
                }

                $invoice_tmp = (new InvoiceModel())->getOne(['user_id' => $array['user_id'], 'subject_id' => $array['subject_id'], 'invoice_id' => $array['invoice_id'], 'invoice_status' => '%']);

                $user_tmp = RBACController::getUser();
                $operator = 0;
                if($user_tmp['type'] == 1){
                    $operator = $user_tmp['operator']['id'];
                }
                if($operator <= 0 || $operator == null){
                    $tmp_data_user = RBACController::getUser();
                    $operator = $tmp_data_user['name'];
                } else {
                    $tmp_data_user = (new OperatorModel())->getOne(['user_id' => $array['user_id'], 'operator_id' => $operator]);
                    $operator = $tmp_data_user['name'];
                }

                $data['user_id'] = $array['user_id'];
                $data['subject_id'] = $array['subject_id'];
                $data['operator'] = $operator;
                $data['invoice'] = $invoice_tmp['serial'];
                $data['date_time'] = $array['date'];
                $data['product_info'] = $sold_goods_table;
                $data['discount_info'] = $discount_info;

                $sell_receipt = (new ReceiptModel())->create($data);
                $sell_receipt = (new ReceiptModel())->buildReceipt($data + ['receipt_id' => $sell_receipt]);

                // Receipt service code

                $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET status = '1'
                WHERE user_id=:user_id AND invoice_id=:invoice_id AND subject_id=:subject_id AND id > 0");
                $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
                $stmt->bindValue(":subject_id", $array['subject_id'], PDO::PARAM_INT);
                $stmt->bindValue(":invoice_id", $array['invoice_id'], PDO::PARAM_INT);
                $stmt->execute();

                $stmt = $this->db->prepare("UPDATE vl1_InvoiceModel SET notes = :notes WHERE id = :invoice_id AND user_id = :user_id");
                $stmt->bindValue(":notes", $array['notes']);
                $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
                $stmt->bindValue(":invoice_id", $array['invoice_id'], PDO::PARAM_INT);
                $stmt->execute();

                $this->db->commit();

                return $sell_receipt;
            }

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            $this->db->rollBack();
            return false;

        }

    }


    public function updateInvoiceItems($ids, $invoice_number){
        try{
            $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET invoice_id=:invoice_id
            WHERE id IN(".implode(',', $ids).")");
            $stmt->bindValue(":invoice_id", $invoice_number);
            return $stmt->execute();

        } catch(Exception $e) {
            Logger::writeExceptionLog($e);
            return false;
        }
    }

    public function getDB(){
        return $this->db;
    }

}
<?php

class ContragentModel extends CRUDModel{

    public $id;
    public $name;
    public $address;
    public $phone;
    public $email;
    public $image;
    public $user_id;
    public $description;
    public $debt;

    private $tableName = "vl1_ContragentModel";

    protected $db;

    public function __construct(){
        $this->db = (new DB())->start();
    }

    public function createContragent($array){

        $prefixes = $array['prefix'];
        $phones = $array['phone'];
        array_pop($prefixes);
        array_pop($phones);
        $phone = "";
        $k = count($prefixes);
        foreach($prefixes as $key => $val){
            if($key == $k) $delim = ""; else $delim = ";";
            $phone .= $val . " - " . $phones[$key] . $delim;
        }

        foreach($array['name'] as $key => $val){
            $array['name'][$key] = trim($val);
        }

        $imagePath = Utils::uploadImages("public" . ds . "user" . $array['user_id'] . ds .  "contragents", "image");

        try{
            $stmt = $this->db->prepare("INSERT INTO " . $this->tableName . "(subject_id, `name`, address, phone, email, image, user_id, description)
            VALUES(:subject_id, :name, :address, :phone, :email, :image, :user_id, :description)");
            $stmt->bindValue(":subject_id", $array['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":name", join(" ", $array['name']), PDO::PARAM_STR);
            $stmt->bindValue(":address", $array['address'], PDO::PARAM_STR);
            $stmt->bindValue(":phone", $phone, PDO::PARAM_STR);
            $stmt->bindValue(":email", $array['email'], PDO::PARAM_STR);
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":description", $array['description'], PDO::PARAM_STR);
            $stmt->bindValue(":image", $imagePath, PDO::PARAM_STR);
            return $stmt->execute();

        } catch(Exception $e) {
            return false;
        }

    }

    public function updateContragent($array){

        $prefixes = $array['prefix'];
        $phones = $array['phone'];
        array_pop($prefixes);
        array_pop($phones);
        $phone = "";
        $k = count($prefixes);
        foreach($prefixes as $key => $val){
            if($key == $k) $delim = ""; else $delim = ";";
            $phone .= $val . " - " . $phones[$key] . $delim;
        }

        $oldImagesStr = $array['old_images_str'];
        $oldImages = "";
        if(!empty($oldImagesStr) && $oldImagesStr != "" && $oldImagesStr != ";"){
            if(isset($array['old_image']) && is_array($array['old_image'])){
                foreach($array['old_image'] as $oldImage){
                    $oldImagesStr = str_replace($oldImage . ";", "", $oldImagesStr);
                    $oldImages .= $oldImage . ";";
                }
            }
            $toDelete = explode(";", trim($oldImagesStr, ";"));
            Logger::writeLog($toDelete);
            Utils::unlinkSet($toDelete);
        }

        $imagePath = Utils::uploadImages("public" . ds . "user" . $array['user_id'] . ds . "contragents", "image");
        $imagePath .= $oldImages;

        try{
            $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET name=:name, address=:address, phone=:phone, email=:email,
            image=:image WHERE id=:id AND user_id=:user_id AND subject_id=:subject_id");

            $stmt->bindValue(":name", join(" ", $array['name']), PDO::PARAM_STR);
            $stmt->bindValue(":address", $array['address'], PDO::PARAM_STR);
            $stmt->bindValue(":phone", $phone, PDO::PARAM_STR);
            $stmt->bindValue(":email", $array['email'], PDO::PARAM_STR);
            $stmt->bindValue(":image", $imagePath, PDO::PARAM_STR);
            $stmt->bindValue(":id", $array['id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $array['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            return $stmt->execute();
        } catch(Exception $e) {
            return false;
        }

    }

    public function deleteContragent($array){
        try{

            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT image FROM " . $this->tableName . " WHERE id=:id");
            $stmt->bindValue(":id", $array['id'], PDO::PARAM_INT);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            $images = $res['image'];

            $stmt = $this->db->prepare("SELECT debt FROM " . $this->tableName . " c LEFT JOIN vl1_ContragentDebtModel d ON d.contragent_id = c.id
                    WHERE c.id=:id AND debt > 0");
            $stmt->bindValue(":id", $array['id'], PDO::PARAM_INT);
            $stmt->execute();

            if($stmt->fetch())
                return ['status' => 0, 'message' => 'Bu kontragentə borcunuz mövcuddur'];
            else{
                $stmt = $this->db->prepare("DELETE c,d FROM " . $this->tableName . " c LEFT JOIN vl1_ContragentDebtModel d ON d.contragent_id = c.id WHERE c.id=:id");
                $stmt->bindValue(":id", $array['id'], PDO::PARAM_INT);
                $stmt->execute();
                if($stmt->rowCount()) {
                    $this->db->commit();
                    if(!empty($images)) Utils::unlinkSet(explode(";", $images));
                    return ['status' => 1];
                }

            }

            return ['status' => 0, 'message' => 'Əməliyyat zamanı xəta'];

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            $this->db->rollBack();
            return false;

        }
    }

    public function getSearchAll($array){

        try{
            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id = :user_id AND name LIKE :name ORDER BY id DESC");
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":name", '%' . $array['contragent_search'] . '%', PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(Exception $e) {
            return false;
        }

    }
    
    public function check($user_id, $subject_id){
        try{
            $stmt = $this->db->prepare("SELECT id FROM ".$this->tableName." WHERE subject_id=:subject_id AND user_id=:user_id AND not_contragent = 1 LIMIT 1");
            
            $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $subject_id, PDO::PARAM_INT);
            $stmt->execute();
            if(!$stmt->rowCount()){
                  $stmt = $this->db->prepare("INSERT INTO " . $this->tableName . "(subject_id, `name`, user_id, not_contragent)
                VALUES(:subject_id, :name, :user_id, 1)");
                $stmt->bindValue(":subject_id", $subject_id, PDO::PARAM_INT);
                $stmt->bindValue(":name", 'Kontragentsiz', PDO::PARAM_STR);
                $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
                return $stmt->execute();
            }
        }catch(Exception $e) {
            return false;
        }
    }

    public function getAll($array){

        try{
            $stmt = $this->db->prepare("SELECT c.*, d.currency, d.debt FROM " . $this->tableName . " c
                LEFT JOIN vl1_ContragentDebtModel d ON d.contragent_id = c.id
            WHERE user_id = :user_id AND subject_id = :subject_id ORDER BY d.currency");
            
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $array['subject_id'], PDO::PARAM_INT);
            $stmt->execute();
            $data = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $key => $contragent) {
                if(!array_key_exists($contragent['id'], $data)){
                    $contragent['debts'][$contragent['currency']] = $contragent['debt'];
                    unset($contragent['debt'], $contragent['currency']);
                    $data[$contragent['id']] = $contragent;
                }
                else    $data[$contragent['id']]['debts'][$contragent['currency']] = $contragent['debt'];
            }

            return array_values($data);
        } catch(Exception $e) {
            return false;
        }

    }

    public function getOne($array){
        $curr ='';
        try{
            if(!isset($array['id'])) $array['id'] = $array['contragent_id'];

            if(isset($array['currency'])) $curr = "AND d.currency = :currency";
            $stmt = $this->db->prepare("SELECT c.*, d.currency, d.currency_archive, d.debt FROM " . $this->tableName . " c
                LEFT JOIN vl1_ContragentDebtModel d ON d.contragent_id = c.id {$curr}
                WHERE c.id = :id");
            $stmt->bindValue(":id", $array['id'], PDO::PARAM_INT);
            if(!empty($cur))
                $stmt->bindValue(":currency", $array['currency'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(Exception $e) {
            return false;
        }

    }

    public function increaseDebt($array, $db){

        try{

            if($array['contragent_id'] == 0) return true;

            $debt = $array['debt'];
            $stmt = $db->prepare("UPDATE vl1_ContragentDebtModel SET debt = debt + :debt WHERE currency = :currency AND contragent_id = :id");
            $stmt->bindValue(":debt", $debt, PDO::PARAM_STR);
            $stmt->bindValue(":currency", $array['currency'], PDO::PARAM_INT);
            $stmt->bindValue(":id", $array['contragent_id'], PDO::PARAM_INT);
            $stmt->execute();

            if(!$stmt->rowCount()){
                $stmt = $db->prepare("INSERT INTO vl1_ContragentDebtModel (contragent_id, debt, currency, currency_archive)VALUES(:contragent_id, :debt, :currency, :currency_archive)");
                $stmt->bindValue(":debt", $debt, PDO::PARAM_STR);
                $stmt->bindValue(":currency", $array['currency'], PDO::PARAM_INT);
                $stmt->bindValue(":currency_archive", $array['currency_archive'], PDO::PARAM_STR);
                $stmt->bindValue(":contragent_id", $array['contragent_id'], PDO::PARAM_INT);
                $stmt->execute();
            }

            return $stmt->rowCount();

        } catch(Exception $e) {

            throw $e;

        }

    }

    public function decreaseDebt($array, $db){

        try{

            $stmt = $db->prepare("UPDATE vl1_ContragentDebtModel SET debt = debt - :debt
            WHERE contragent_id = :contragent_id AND currency = :currency");
            $stmt->bindValue(":debt", $array['debt'], PDO::PARAM_STR);
            $stmt->bindValue(":contragent_id", $array['contragent_id'], PDO::PARAM_INT);
            $stmt->bindValue(":currency", $array['currency'], PDO::PARAM_INT);
            $stmt->execute();

        } catch(Exception $e) {
            throw $e;
        }

    }

    public function payToContragent($array){

        try{

            $this->db->beginTransaction();
            $invoiceModel = new InvoiceModel();
            $cashboxModel = new CashboxModel();

            $array['debt'] = $array['amount'];
            $array['invoice_status'] = '1';
            $array['payed'] = $array['debt'];
            $array['invoice_id'] = $invoiceModel->createInvoice($array, $this->db);
            $this->decreaseDebt($array, $this->db);

            $array['cashbox_id'] = $cashboxModel->getCurrent($array)['id'];

            $cashboxModel->decreaseAmount($array, $this->db);

            $this->db->commit();
            return true;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            $this->db->rollBack();
            return false;

        }

    }

    public function approveReturn($array){
        $response = ['status' => 0, 'message' => 'Əməliyyatı başa çatdırmaq mümkün olmadı'];
        try{
            $this->db->beginTransaction();
            $array['invoice_status'] = 1;

            $invoiceModel = new InvoiceModel();

            $firstSerial = $array['invoice_serial'];
            $firstAttempt = true;
            $firstAmount = $array['amount'];

            foreach($array['count'] as $currency=>$product){
                if($currency == 'azn' && $array['amount_azn'] > 0)
                    $array['amount'] = $array['amount_azn'];
                else
                    $array['amount'] = $firstAmount;

                if(!$firstAttempt)
                    $array['invoice_serial'] = $invoiceModel->getNextInvoiceNumber(['serial'=>$firstSerial], true);

                $array['invoice_id'] = $invoiceModel->createInvoice($array, $this->db);

                if(is_array($product)){
                    $stmt = $this->db->prepare("SELECT * FROM vl1_StoreModel WHERE user_id = :user_id AND id = :id");
                    foreach($product as $storeItemId => $count){
                        $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
                        $stmt->bindValue(":id", $storeItemId, PDO::PARAM_INT);
                        $stmt->execute();
                        $storeItem = $stmt->fetch(PDO::FETCH_ASSOC);
                        if(!$storeItem) {
                            Logger::writeLog("Element with id = " . $storeItemId . " is not store object");
                            $this->db->rollBack();
                            return $response;
                        }

                        if($count > $storeItem['count']) {
                            $this->db->rollBack();
                            $response = ['status' => 0, 'message' => 'Mağazada istədiyiniz sayda mal yoxdur'];
                            return $response;
                        }

                        $ot_status = '0';
                        $ot_count = $storeItem['count'];
                        $ot_pending_count = $storeItem['pending_count'];

                        if($ot_pending_count == 0 && ($ot_count - $count) > 0) $ot_status = '1';
                        elseif ($ot_pending_count > 0 && ($ot_count - $count) > 0) $ot_status = '3';
                        elseif ($ot_pending_count > 0 && ($ot_count - $count) == 0) $ot_status = '2';

                        $decreaseInStockCountStmt = $this->db->prepare("UPDATE vl1_StoreModel SET count = count - :count, status = :status
                        WHERE user_id = :user_id AND id = :id");
                        $decreaseInStockCountStmt->bindValue(":count", $count, PDO::PARAM_INT);
                        $decreaseInStockCountStmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
                        $decreaseInStockCountStmt->bindValue(":status", $ot_status, PDO::PARAM_STR);
                        $decreaseInStockCountStmt->bindValue(":id", $storeItemId, PDO::PARAM_INT);
                        $decreaseInStockCountStmt->execute();

                        if(!$decreaseInStockCountStmt->rowCount()) {
                            Logger::writeLog("Couldn't decrease in stock count");
                            $this->db->rollBack();
                            return $response;
                        }

                        $invoiceDetailArray = [
                            "user_id" => $array['user_id'],
                            "invoice_id" => $array['invoice_id'],
                            "goods_id" => $storeItem['goods_id'],
                            "short_info" => $storeItem['short_info'],
                            "count" => $count,
                            "buy_price" => $storeItem['buy_price'],
                            "buy_total" => $storeItem['buy_price'] * $count,
                            "sell_price" => $storeItem['sell_price'],
                            "sell_total" => $storeItem['sell_price'] * $count,
                            "date" => $array['date'],
                            "currency" => $storeItem['currency'],
                            "currency_archive" => $storeItem['currency_archive'],
                        ];
                        (new InvoiceDetailModel())->createInvoiceDetail($invoiceDetailArray, $this->db);

                        $array['debt'] = $array['amount'];
                        $this->decreaseDebt($array, $this->db);
                    }

                } else {
                    Logger::writeLog("Count is not array");
                    $this->db->rollBack();
                    return $response;
                }
                $firstAttempt = false;
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

}
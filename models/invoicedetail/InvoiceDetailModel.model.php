<?php

class InvoiceDetailModel{

    public $id;
    public $user_id;
    public $invoice_id;
    public $goods_id;
    public $short_info;
    public $count;
    public $buy_price;
    public $buy_total;
    public $sell_price;
    public $sell_total;
    public $date;

    private $tableName = "vl1_InvoiceDetailModel";

    protected $db;

    public function __contruct(){

        $this->db = (new DB())->start();

    }

    public function processInvoiceDetail($array, $db){

        try {

            $stmt = $db->prepare("SELECT id FROM " . $this->tableName . "
            WHERE invoice_id=:invoice_id AND goods_id=:goods_id AND short_info=:short_info AND user_id=:user_id");
            $stmt->bindValue(":invoice_id", $array['invoice_id']);
            $stmt->bindValue(":goods_id", $array['goods_id']);
            $stmt->bindValue(":short_info", $array['short_info']);
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if($res && !empty($res) && isset($res['id'])){

                $array['invoice_detail_id'] = $res['id'];
                return $this->increaseInvoiceDetail($array, $db);

            } else {

                return $this->createInvoiceDetail($array, $db);

            }

        } catch(Exception $e) {

            throw $e;

        }

    }

    public function createInvoiceDetail($array, $db){

        try{

            $stmt = $db->prepare("INSERT INTO " . $this->tableName . "(user_id, invoice_id, goods_id, short_info, `count`, buy_price, buy_total, sell_price, sell_total, `date`, currency, currency_archive)
            VALUES(:user_id, :invoice_id, :goods_id, :short_info, :count, :buy_price, :buy_total, :sell_price, :sell_total, :date, :currency, :currency_archive)");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":invoice_id", $array['invoice_id']);
            $stmt->bindValue(":goods_id", $array['goods_id']);
            $stmt->bindValue(":short_info", $array['short_info']);
            $stmt->bindValue(":count", $array['count']);
            $stmt->bindValue(":buy_price", $array['buy_price']);
            $stmt->bindValue(":buy_total", $array['count'] * $array['buy_price']);
            $stmt->bindValue(":sell_price", $array['sell_price']);
            $stmt->bindValue(":sell_total", $array['count'] * $array['sell_price']);
            $stmt->bindValue(":date", $array['date']);
            $stmt->bindValue(":currency", $array['currency']);
            $stmt->bindValue(":currency_archive", $array['currency_archive']);

            return $stmt->execute();

        } catch(Exception $e) {

            throw $e;

        }


    }

    public function increaseInvoiceDetail($array, $db){

        try{

            $stmt = $db->prepare("UPDATE " . $this->tableName . " SET `count` = `count` + :count, buy_total = buy_total + :buy_total,
            sell_total = sell_total + :sell_total, `date`=:date, currency =:currency, currency_archive=:currency_archive
            WHERE id=:id");
            $stmt->bindValue(":count", $array['count']);
            $stmt->bindValue(":buy_total", $array['count'] * $array['buy_price']);
            $stmt->bindValue(":sell_total", $array['count'] * $array['sell_price']);
            $stmt->bindValue(":date", $array['date']);
            $stmt->bindValue(":currency", $array['currency']);
            $stmt->bindValue(":currency_archive", $array['currency_archive']);
            $stmt->bindValue(":id", $array['invoice_detail_id']);


            return $stmt->execute();

        } catch(Exception $e) {

            throw $e;

        }

    }

    public function decreaseInvoiceDetail($array, $db) {

        try{

            $stmt = $db->prepare("UPDATE " . $this->tableName . " SET `count` = `count` - :count, buy_total = buy_total - :buy_total,
            sell_total = sell_total - :sell_total
            WHERE user_id=:user_id AND invoice_id=:invoice_id AND buy_price=:buy_price AND sell_price=:sell_price
            AND goods_id=:goods_id AND id > 0");
            $stmt->bindValue(":count", $array['count']);
            $stmt->bindValue(":buy_total", $array['count'] * $array['buy_price']);
            $stmt->bindValue(":sell_total", $array['count'] * $array['sell_price']);
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":invoice_id", $array['invoice_id']);
            $stmt->bindValue(":buy_price", $array['buy_price']);
            $stmt->bindValue(":sell_price", $array['sell_price']);
            $stmt->bindValue(":goods_id", $array['goods_id']);


            return $stmt->execute();

        } catch(Exception $e) {

            throw $e;

        }

    }

    public function deleteDetails($array, $db){

        try{

            $stmt = $db->prepare("DELETE FROM " . $this->tableName . " WHERE user_id = :user_id AND invoice_id = :invoice_id AND id > 0");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":invoice_id", $array['invoice_id']);
            return $stmt->execute();

        } catch(Exception $e) {

            throw $e;

        }

    }

    public function clearInvoiceDetails($array, $db){

        try{

            $stmt = $db->prepare("DELETE FROM " . $this->tableName . " WHERE user_id = :user_id AND invoice_id = :invoice_id AND `count` <= 0");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":invoice_id", $array['invoice_id']);
            return $stmt->execute();

        } catch(Exception $e) {

            throw $e;

        }

    }

    public function getTotalAmount($array, $db){

        try{

            $stmt = $db->prepare("SELECT SUM(buy_total) AS total_buy FROM " . $this->tableName . " WHERE invoice_id = :invoice_id AND user_id = :user_id");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":invoice_id", $array['invoice_id']);
            if($stmt->execute()){

                return $stmt->fetch(PDO::FETCH_ASSOC)['total_buy'];

            }

        } catch(Exception $e) {

            throw $e;

        }

    }

}
<?php

class ObjectModel extends CRUDModel{

    public $id;
    public $baza_id;
    public $goods_type;
    public $goods_id;
    public $count;
    public $contragent_id;
    public $invoice_id;

    private $tableName = "vl1_ObjectModel";

    protected $db;

    public function __construct(){

        $this->db = (new DB())->start();

    }

    public function createObject($array){

        try{

            $this->db->beginTransaction();

            $invoicegoods = new InvoicegoodsModel();
            $invoicegoods->createInvoicegoods($array, $this->db);

            $stmt = $this->db->prepare("INSERT INTO " . $this->tableName . "(user_id, baza_id, goods_type, goods_id, `count`, contragent_id, invoice_id)
             VALUES(:user_id, :baza_id, :goods_type, :goods_id, :count, :contragent_id, :invoice_id)");
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":baza_id", $array['baza_id'], PDO::PARAM_INT);
            $stmt->bindValue(":goods_type", $array['goods_type'], PDO::PARAM_INT);
            $stmt->bindValue(":goods_id", $array['goods_id'], PDO::PARAM_INT);
            $stmt->bindValue(":count", $array['count'], PDO::PARAM_INT);
            $stmt->bindValue(":contragent_id", $array['contragent_id'], PDO::PARAM_INT);
            $stmt->bindValue(":invoice_id", $array['invoice_id'], PDO::PARAM_INT);
            $stmt->execute();
            $this->db->commit();
            return true;

        } catch(Exception $e) {

            file_put_contents("log.txt", $e->getMessage() . "\n", FILE_APPEND);
            file_put_contents("log.txt", $e->getTraceAsString() . "\n", FILE_APPEND);
            $this->db->rollBack();
            return false;

        }

    }

    public function getAll($array){

        try{

            $stmt = $this->db->prepare("SELECT contragent.name as cname, object.goods_type, goods.name as gname, object.count, goods.buy_price, goods.sell_price, invoice.serial
            FROM vl1_ObjectModel AS object
            LEFT JOIN vl1_ContragentModel AS contragent ON contragent.id = object.contragent_id
            LEFT JOIN vl1_GoodsModel AS goods ON goods.id = object.goods_id
            LEFT JOIN vl1_InvoiceModel AS invoice ON invoice.id = object.invoice_id
            WHERE object.user_id = :user_id AND object.baza_id = :baza_id");

            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":baza_id", $array['baza_id'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            return false;

        }

    }

}
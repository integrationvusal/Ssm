<?php

class InvoicegoodsModel extends CRUDModel{

    public $id;
    public $user_id;
    public $invoice_id;
    public $contragent_id;
    public $count;
    public $debt;
    public $description;
    public $date;

    private $tableName = "vl1_invoicegoodsmodel";

    protected $db;

    public function __construct(){

        $this->db = (new DB())->start();

    }

    public function createInvoicegoods($array, $db){

        try{

            $goods = (new GoodsModel())->getOne(['user_id' => $array['user_id'], 'id' => $array['goods_id']]);
            $debt = intval($array['count']) * floatval($goods['common']['buy_price']);

            $stmt = $db->prepare("UPDATE vl1_invoicemodel SET real_debt = real_debt + :real_debt WHERE id = :id");
            $stmt->bindValue(":real_debt", $debt);
            $stmt->bindValue(":id", $array['invoice_id']);
            if(!$stmt->execute()) throw new Exception("Couldn't write into invoice real debt");

            $stmt = $db->prepare("INSERT INTO " . $this->tableName . "(user_id, invoice_id, contragent_id, `count`, debt, description)
             VALUES(:user_id, :invoice_id, :contragent_id, :count, :debt, :description)");
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":invoice_id", $array['invoice_id'], PDO::PARAM_INT);
            $stmt->bindValue(":contragent_id", $array['contragent_id'], PDO::PARAM_INT);
            $stmt->bindValue(":count", $array['count'], PDO::PARAM_INT);
            $stmt->bindValue(":debt", $debt, PDO::PARAM_STR);
            $stmt->bindValue(":description", $array['goods_desc'], PDO::PARAM_STR);

            return $stmt->execute();

        } catch(Exception $e) {

            throw $e;

        }

    }

    public function getByInvoiceId($array){

        try{

            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id=:user_id AND invoice_id=:invoice_id");
            $stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(":invoice_id", $array['invoice_id'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            return false;

        }

    }

}
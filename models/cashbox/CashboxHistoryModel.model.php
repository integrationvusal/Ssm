<?php

class CashboxHistoryModel{

    public $id;
    public $cashbox_id;
    public $operation_type;
    public $amount;
    public $invoice_id;

    private $tableName = "vl1_CashboxHistoryModel";

    protected $db;

    public function __construct(){

        $this->db = (new DB())->start();

    }

    public function createCH($array, $db){

        try{

            $stmt = $db->prepare("INSERT INTO " . $this->tableName . "(cashbox_id, operation_type, amount, invoice_id, total_amount, date, currency)
            VALUES(:cashbox_id, :operation_type, :amount, :invoice_id, :total_amount, :date, :currency)");
            $stmt->bindValue(":cashbox_id", $array['cashbox_id']);
            $stmt->bindValue(":operation_type", $array['operation_type']);

            $amount = (isset($array['client']) && $array['client'] > 0)?$array['debtamount']:$array['amount'];

            $stmt->bindValue(":amount", $amount);
            $stmt->bindValue(":invoice_id", $array['invoice_id']);
            $stmt->bindValue(":total_amount", $array['total_amount']);
            $stmt->bindValue(":currency", isset($array['currency'])?$array['currency']:0);
            $stmt->bindValue(":date", date("Y-m-d H:i:s"));

            return $stmt->execute();

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            throw $e;

        }

    }

}
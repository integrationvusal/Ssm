<?php

class ReceiptModel{

    public $user_id;
    public $subject_id;
    public $company_logo;
    public $company_name;
    public $company_voen;
    public $company_address;
    public $other_top;
    public $other_bottom;
    public $operator;
    public $invoice;
    public $date_time;
    public $product_info;
    public $discount_info;

    private $tableName = "vl1_ReceiptModel";
    protected $db;

    public function __construct(){

        $this->db = (new DB())->start();

    }

    public function create($data){

        try {

            $stmt = $this->db->prepare("INSERT INTO " . $this->tableName . "(user_id, subject_id, company_logo, company_name, company_voen, company_address, other_top, other_bottom, operator, invoice, date_time, product_info, discount_info)
             VALUES(:user_id, :subject_id, :company_logo, :company_name, :company_voen, :company_address, :other_top, :other_bottom, :operator, :invoice, :date_time, :product_info, :discount_info)");
            $stmt->bindValue("user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue("subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue("company_logo", $data['company_logo'], PDO::PARAM_STR);
            $stmt->bindValue("company_name", $data['company_name'], PDO::PARAM_STR);
            $stmt->bindValue("company_voen", $data['company_voen'], PDO::PARAM_STR);
            $stmt->bindValue("company_address", $data['company_address'], PDO::PARAM_STR);
            $stmt->bindValue("other_top", $data['other_top'], PDO::PARAM_STR);
            $stmt->bindValue("other_bottom", $data['other_bottom'], PDO::PARAM_STR);
            $stmt->bindValue("operator", $data['operator'], PDO::PARAM_STR);
            $stmt->bindValue("invoice", $data['invoice'], PDO::PARAM_STR);
            $stmt->bindValue("date_time", $data['date_time'], PDO::PARAM_STR);
            $stmt->bindValue("product_info", $data['product_info'], PDO::PARAM_STR);
            $stmt->bindValue("discount_info", $data['discount_info'], PDO::PARAM_STR);
            $stmt->execute();

            return $this->db->lastInsertId();

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getAll($data, $limit, $offset){

        try {

            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id = :user_id AND subject_id = :subject_id  ORDER BY date_time DESC
            LIMIT :limit OFFSET :offset");
            $stmt->bindValue("user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue("subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue("limit", (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue("offset", (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getCountAll($data){

        try {

            $stmt = $this->db->prepare("SELECT COUNT(id) AS cnt FROM " . $this->tableName . " WHERE user_id = :user_id AND subject_id = :subject_id");
            $stmt->bindValue("user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue("subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res['cnt'];

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function buildReceipt($data){

        try {

            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id = :user_id AND subject_id = :subject_id AND id = :receipt_id");
            $stmt->bindValue("user_id", $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue("subject_id", $data['subject_id'], PDO::PARAM_INT);
            $stmt->bindValue("receipt_id", $data['receipt_id'], PDO::PARAM_INT);
            $stmt->execute();
            $receipt = $stmt->fetch(PDO::FETCH_ASSOC);

            $receipt_html = '<div class="receipt"><table>';
            if(!empty($receipt['company_logo'])) $receipt_html .= '<tr><td colspan="2" style="text-align: center"><img src="' . $receipt['company_logo'] . '" style="width: 30%"></td></tr>';
            if(!empty($receipt['company_name'])) $receipt_html .= '<tr><td colspan="2" style="text-align: center"><h4 color="black">' . $receipt['company_name'] . '</h4></td></tr>';
            if(!empty($receipt['company_voen'])) $receipt_html .= '<tr><td style="width: 30%; text-align: left">VÖEN</td><td style="text-align: right">' . $receipt['company_voen'] . '</td></tr>';
            if(!empty($receipt['company_address'])) $receipt_html .= '<tr><td style="width: 30%; text-align: left">Ünvan</td><td style="text-align: right">' . $receipt['company_address'] . '</td></tr>';
            if(!empty($receipt['operator'])) $receipt_html .= '<tr style="border-bottom: 1px solid grey"><td style="width: 30%; text-align: left">Operator</td><td style="text-align: right">' . $receipt['operator'] . '</td></tr>';
            if(!empty($receipt['invoice'])) $receipt_html .= '<tr><td style="width: 30%; text-align: left">Qaimə №</td><td style="text-align: right">' . $receipt['invoice'] . '</td></tr>';
            if(!empty($receipt['date_time'])) $receipt_html .= '<tr style="border-bottom: 1px solid grey"><td style="width: 30%; text-align: left;">Tarix</td><td style="text-align: right">' . $receipt['date_time'] . '</td></tr>';
            if(!empty($receipt['other_top'])) $receipt_html .= '<tr><td colspan="2" style="text-align: left">' . $receipt['other_top'] . '</td></tr>';
            if(!empty($receipt['product_info'])) $receipt_html .= '<tr style="border-bottom: 1px solid grey"><td colspan="2">' . $receipt['product_info'] . '</td></tr>';
            if(!empty($receipt['discount_info'])) $receipt_html .= '<tr style="border-bottom: 1px solid grey"><td colspan="2">' . $receipt['discount_info'] . '</td></tr>';
            if(!empty($receipt['other_bottom'])) $receipt_html .= '<tr><td colspan="2" style="text-align: left">' . $receipt['other_bottom'] . '</td></tr>';

            $receipt_html .= '</table></div>';

            return $receipt_html;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

}
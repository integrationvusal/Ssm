<?php

class StatisticsModel{

    protected $db;

    public function __construct(){

        $this->db = (new DB())->start();

    }

    public function getSoldGoodsCount($user_id, $subject_id, $date_from = null, $date_to = null){

        try{

            if($date_from == null) $date_from = "0000-00-00";
            if($date_to == null) $date_to = "3000-00-00";

            $stmt = $this->db->prepare("SELECT SUM(sell.count) AS cnt FROM vl1_SellModel AS sell
            LEFT JOIN vl1_InvoiceModel AS invoice ON sell.invoice_id = invoice.id
            WHERE sell.user_id = :user_id AND sell.subject_id = :subject_id AND
            invoice.date BETWEEN :date_from AND :date_to");
            $stmt->bindValue(":user_id", $user_id);
            $stmt->bindValue(":subject_id", $subject_id);
            $stmt->bindValue(":date_from", $date_from);
            $stmt->bindValue(":date_to", $date_to);
            $stmt->execute();
            $sum = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
            return $sum ? $sum : 0;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getSoldGoodsCountMonthly($user_id, $subject_id){

        try{

            $currentYear = date("Y");
            $count = false;
            for($i = 1; $i <= 12; $i++) {
                $date_from = $currentYear . "-" . ($i < 10 ? "0" . $i : $i) . "-00";
                $date_to = $currentYear . "-" . ($i < 10 ? "0" . $i : $i) . "-31";
                $stmt = $this->db->prepare("SELECT SUM(sell.count) AS cnt FROM vl1_SellModel AS sell
                LEFT JOIN vl1_InvoiceModel AS invoice ON sell.invoice_id = invoice.id
                WHERE sell.user_id = :user_id AND sell.subject_id = :subject_id AND
                invoice.date BETWEEN :date_from AND :date_to");
                $stmt->bindValue(":user_id", $user_id);
                $stmt->bindValue(":subject_id", $subject_id);
                $stmt->bindValue(":date_from", $date_from);
                $stmt->bindValue(":date_to", $date_to);
                $stmt->execute();
                $sum = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
                $count[$i] = $sum ? $sum : 0;
            }
            return $count;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getSellCount($user_id, $subject_id, $date_from = null, $date_to = null){

        try{

            if($date_from == null) $date_from = "0000-00-00";
            if($date_to == null) $date_to = "3000-00-00";

            $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM vl1_InvoiceModel
            WHERE user_id = :user_id AND subject_id = :subject_id AND type = 1 AND
            date BETWEEN :date_from AND :date_to");
            $stmt->bindValue(":user_id", $user_id);
            $stmt->bindValue(":subject_id", $subject_id);
            $stmt->bindValue(":date_from", $date_from);
            $stmt->bindValue(":date_to", $date_to);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getTotalSellAmount($user_id, $subject_id, $date_from = null, $date_to = null){

        try{

            if($date_from == null) $date_from = "0000-00-00";
            if($date_to == null) $date_to = "3000-00-00";

            $stmt = $this->db->prepare("SELECT SUM(amount) AS cnt FROM vl1_InvoiceModel
            WHERE user_id = :user_id AND subject_id = :subject_id AND
            type = 1 AND (client_id IS NULL OR client_id = 0) AND
            date BETWEEN :date_from AND :date_to");
            $stmt->bindValue(":user_id", $user_id);
            $stmt->bindValue(":subject_id", $subject_id);
            $stmt->bindValue(":date_from", $date_from);
            $stmt->bindValue(":date_to", $date_to);
            $stmt->execute();
            $sum1 = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
            if(!$sum1) $sum1 = 0;

            $stmt = $this->db->prepare("SELECT SUM(payed) AS cnt FROM vl1_InvoiceModel
            WHERE user_id = :user_id AND subject_id = :subject_id AND
            type = 1 AND (client_id IS NOT NULL OR client_id > 0) AND
            date BETWEEN :date_from AND :date_to");
            $stmt->bindValue(":user_id", $user_id);
            $stmt->bindValue(":subject_id", $subject_id);
            $stmt->bindValue(":date_from", $date_from);
            $stmt->bindValue(":date_to", $date_to);
            $stmt->execute();
            $sum2 = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
            if(!$sum2) $sum2 = 0;

            return $sum1 + $sum2;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getTotalSellMonthlyData($user_id, $subject_id){

        try{

            $currentYear = date("Y");
            $sum = false;
            for($i = 1; $i <= 12; $i++){
                $date_from = $currentYear . "-" . ($i < 10 ? "0" . $i : $i) . "-00";
                $date_to = $currentYear . "-" . ($i < 10 ? "0" . $i : $i) . "-31";
                $stmt = $this->db->prepare("SELECT SUM(amount) AS cnt FROM vl1_InvoiceModel
                WHERE user_id = :user_id AND subject_id = :subject_id AND
                type = 1 AND (client_id IS NULL OR client_id = 0) AND
                date BETWEEN :date_from AND :date_to");
                $stmt->bindValue(":user_id", $user_id);
                $stmt->bindValue(":subject_id", $subject_id);
                $stmt->bindValue(":date_from", $date_from);
                $stmt->bindValue(":date_to", $date_to);
                $stmt->execute();
                $sum1 = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
                if(!$sum1) $sum1 = 0;

                $stmt = $this->db->prepare("SELECT SUM(payed) AS cnt FROM vl1_InvoiceModel
                WHERE user_id = :user_id AND subject_id = :subject_id AND
                type = 1 AND (client_id IS NOT NULL OR client_id > 0) AND
                date BETWEEN :date_from AND :date_to");
                $stmt->bindValue(":user_id", $user_id);
                $stmt->bindValue(":subject_id", $subject_id);
                $stmt->bindValue(":date_from", $date_from);
                $stmt->bindValue(":date_to", $date_to);
                $stmt->execute();
                $sum2 = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
                if(!$sum2) $sum2 = 0;

                $sum[$i] = $sum1 + $sum2;
            }
            return $sum;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getTotalContragentMonthlyData($user_id, $subject_id){

        try{

            $currentYear = date("Y");
            $debt = false;
            $contragents = (new ContragentModel())->getAll(['user_id' => $user_id]);
            for($i = 1; $i <= 12; $i++){
                $date_from = $currentYear . "-" . ($i < 10 ? "0" . $i : $i) . "-00";
                $date_to = $currentYear . "-" . ($i < 10 ? "0" . $i : $i) . "-31";
                $contragentDebt = 0;
                foreach($contragents as $contragent){
                    $stmt = $this->db->prepare("SELECT type, amount, total_amount FROM vl1_InvoiceModel
                    WHERE user_id = :user_id AND subject_id = :subject_id AND contragent_id = :contragent_id AND
                    type in (0,5,10) AND date BETWEEN :date_from AND :date_to ORDER BY `date` DESC LIMIT 1");
                    $stmt->bindValue(":user_id", $user_id);
                    $stmt->bindValue(":subject_id", $subject_id);
                    $stmt->bindValue(":date_from", $date_from);
                    $stmt->bindValue(":date_to", $date_to);
                    $stmt->bindValue(":contragent_id", $contragent['id']);
                    $stmt->execute();
                    $debt1 = $stmt->fetch(PDO::FETCH_ASSOC);
                    if(!$debt1) $debt2 = 0;
                    else {
                        if($debt1['type'] == 0){
                            $debt2 = $debt1['amount'] + $debt1['total_amount'];
                        } else if($debt1['type'] == 5 || $debt1['type'] == 10){
                            $debt2 = $debt1['total_amount'] - $debt1['amount'];
                        } else{
                            $debt2 = 0;
                        }
                    }

                    $contragentDebt += $debt2;
                }

                $debt[$i] = $contragentDebt;

            }
            return $debt;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getCashboxMonthlyData($cashbox_id){

        try{

            $currentYear = date("Y");
            $data = false;
            for($i = 1; $i <= 12; $i++){
                $date_from = $currentYear . "-" . ($i < 10 ? "0" . $i : $i) . "-00";
                $date_to = $currentYear . "-" . ($i < 10 ? "0" . $i : $i) . "-31";

                $stmt = $this->db->prepare("SELECT SUM(amount) AS income FROM vl1_CashboxHistoryModel WHERE cashbox_id = :cashbox_id AND operation_type = '+'
                AND date BETWEEN :date_from AND :date_to");
                $stmt->bindValue(":cashbox_id", $cashbox_id);
                $stmt->bindValue(":date_from", $date_from);
                $stmt->bindValue(":date_to", $date_to);
                $stmt->execute();
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                $data['income'][$i] = $res['income'];

                $stmt = $this->db->prepare("SELECT SUM(amount) AS outgoing FROM vl1_CashboxHistoryModel WHERE cashbox_id = :cashbox_id AND operation_type = '-'
                AND date BETWEEN :date_from AND :date_to");
                $stmt->bindValue(":cashbox_id", $cashbox_id);
                $stmt->bindValue(":date_from", $date_from);
                $stmt->bindValue(":date_to", $date_to);
                $stmt->execute();
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                $data['outgoing'][$i] = $res['outgoing'];
            }
            return $data;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getTopGoods($user_id, $subject_id, $goods_type){

        try{

            $model = Application::$settings['goods_types'][$goods_type]['model_name'];
            $modelTable = $model::$tableName;
            $stmt = $this->db->prepare("SELECT SUM(sell.count) AS total_count, SUM(sell.count*sell.sell_price) AS total_amount, sell.short_info, sell.goods_id, model.image FROM vl1_SellModel AS sell
            LEFT JOIN vl1_GoodsModel AS goods ON sell.goods_id = goods.id
            LEFT JOIN " . $modelTable . " AS model ON goods.goods_id = model.id
            WHERE sell.user_id = :user_id AND sell.subject_id = :subject_id GROUP BY sell.goods_id ORDER BY total_count DESC, total_amount DESC LIMIT 5;");
            $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
            $stmt->bindValue(":subject_id", $subject_id, PDO::PARAM_INT);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach($res as $key => $val){
                $images = explode(";", trim($val['image'], ";"));
                $tmp = "";
                foreach($images as $k => $img){
                    if(!empty($img))
                    $tmp .= '<a class="fancybox-thumbs" data-fancybox-group="thumb' . $key . '" href="' . Application::$settings['url'] . '/' . $img . '"
                    ' . (($k > 0) ? 'style="display: none"' : '') . '>
                        <img src="' . Application::$settings['url'] . '/' . $img . '" width="60">
                    </a>';
                }
                $res[$key]['image'] = $tmp;
            }
            return $res;

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

}
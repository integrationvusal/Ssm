<?php

class ManagerModel{

    private $tableName = "vl1_UserModel";

    protected $db;

    public function __construct(){

        $this->db = (new DB())->start();

    }

    public function getAll($limit = 0, $offset = 0){

        try{

            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE spc != 1 LIMIT :limit OFFSET :offset");
            $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getCountAll(){

        try{

            $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM " . $this->tableName . " WHERE spc != 1");
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res['cnt'];

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function getOne($array){

        try{

            $stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE spc != 1 AND id = :id");
            $stmt->bindValue(":id", $array['manager_id'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }

    }

    public function createManager($array){

        try{

            $this->db->beginTransaction();
            $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM " . $this->tableName . " WHERE login=:login");
            $stmt->bindValue(":login", $array['login']);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if($res['cnt'] > 0) return ['status' => 0, 'message' => 'Login artıq mövcuddur'];

            $stmt = $this->db->prepare("INSERT INTO " . $this->tableName . "(name, email, login, password, birthdate, description)
            VALUES(:name, :email, :login, :password, :birthdate, :description)");
            $stmt->bindValue(":name", $array['name'], PDO::PARAM_STR);
            $stmt->bindValue(":email", $array['email'], PDO::PARAM_STR);
            $stmt->bindValue(":login", $array['login'], PDO::PARAM_STR);
            $stmt->bindValue(":password", Security::getPasswordHash($array['login'] . $array['password']), PDO::PARAM_STR);
            $stmt->bindValue(":birthdate", $array['birthdate'], PDO::PARAM_STR);
            $stmt->bindValue(":description", $array['description'], PDO::PARAM_STR);
            $stmt->execute();

            if($stmt->rowCount()) {
                $this->db->commit();
                return ['status' => 1, 'message' => 'İstifadəçi uğurla əlavə olundu'];
            } else {
                $this->db->rollBack();
                return ['status' => 0, 'message' => 'İstifadəçini əlavə etmək mümkün olmadı'];
            }

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            $this->db->rollBack();
            return false;

        }


    }

    public function updateManager($array){

        try{

            if(!empty($array['password'])){
                $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET name = :name, email = :email,
                login = :login, password = :password, birthdate = :birthdate, description = :description
                WHERE id = :manager_id AND spc != 1");
                $stmt->bindValue(":password", Security::getPasswordHash($array['login'] . $array['password']), PDO::PARAM_STR);
            } else {
                $stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET name = :name, email = :email,
                login = :login, birthdate = :birthdate, description = :description
                WHERE id = :manager_id AND spc != 1");
            }
            $stmt->bindValue(":name", $array['name'], PDO::PARAM_STR);
            $stmt->bindValue(":email", $array['email'], PDO::PARAM_STR);
            $stmt->bindValue(":login", $array['login'], PDO::PARAM_STR);
            $stmt->bindValue(":birthdate", $array['birthdate'], PDO::PARAM_STR);
            $stmt->bindValue(":description", $array['description'], PDO::PARAM_STR);
            $stmt->bindValue(":manager_id", $array['manager_id'], PDO::PARAM_INT);
            $stmt->execute();

            if($stmt->rowCount()) return ['status' => 1, 'message' => 'İstifadəçi uğurla yeniləndi'];
            return ['status' => 0, 'message' => 'İstifadəçini yeniləmək mümkün olmadı'];

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            return false;

        }


    }

    public function deleteManager($array){

        $response = ['status' => 0, 'message' => 'İstifadəçini silmək mümkün olmadı'];
        try{
            $this->db->beginTransaction();

            /**
             *  delete all from goods catalog
             */
            $goods_types = Application::$settings['goods_types'];
            foreach($goods_types as $goods_type){
                $stmt = $this->db->prepare("DELETE FROM vl1_" . $goods_type['model_name'] . " WHERE user_id = :user_id");
                $stmt->bindValue(":user_id", $array['user_id']);
                $stmt->execute();
            }

            $deleteCashBoxHistoryStmt = $this->db->prepare("DELETE FROM vl1_CashboxHistoryModel WHERE cashbox_id IN
            (SELECT id FROM vl1_CashboxModel WHERE user_id = :user_id)");
            $deleteCashBoxHistoryStmt->bindValue(":user_id", $array['user_id']);
            $deleteCashBoxHistoryStmt->execute();

            $deleteCashboxStmt = $this->db->prepare("DELETE FROM vl1_CashboxModel WHERE user_id = :user_id");
            $deleteCashboxStmt->bindValue(":user_id", $array['user_id']);
            $deleteCashboxStmt->execute();

            $deleteCategoryStmt = $this->db->prepare("DELETE FROM vl1_CategoryModel WHERE user_id = :user_id");
            $deleteCategoryStmt->bindValue(":user_id", $array['user_id']);
            $deleteCategoryStmt->execute();

            $deleteClientStmt = $this->db->prepare("DELETE FROM vl1_ClientModel WHERE user_id = :user_id");
            $deleteClientStmt->bindValue(":user_id", $array['user_id']);
            $deleteClientStmt->execute();

            $deleteContactStmt = $this->db->prepare("DELETE FROM vl1_ContactModel WHERE user_id = :user_id");
            $deleteContactStmt->bindValue(":user_id", $array['user_id']);
            $deleteContactStmt->execute();

            $deleteContragentStmt = $this->db->prepare("DELETE FROM vl1_ContragentModel WHERE user_id = :user_id");
            $deleteContragentStmt->bindValue(":user_id", $array['user_id']);
            $deleteContragentStmt->execute();

            $deleteGoodsStmt = $this->db->prepare("DELETE FROM vl1_GoodsModel WHERE user_id = :user_id");
            $deleteGoodsStmt->bindValue(":user_id", $array['user_id']);
            $deleteGoodsStmt->execute();

            $deleteInvoiceDetailStmt = $this->db->prepare("DELETE FROM vl1_InvoiceDetailModel WHERE user_id = :user_id");
            $deleteInvoiceDetailStmt->bindValue(":user_id", $array['user_id']);
            $deleteInvoiceDetailStmt->execute();

            $deleteInvoiceStmt = $this->db->prepare("DELETE FROM vl1_InvoiceModel WHERE user_id = :user_id");
            $deleteInvoiceStmt->bindValue(":user_id", $array['user_id']);
            $deleteInvoiceStmt->execute();

            $deleteNVBStmt = $this->db->prepare("DELETE FROM vl1_NoticesViewedBy WHERE vl1_NoticesViewedBy.user_id = :user_id OR
            operator_id IN (SELECT id FROM vl1_OperatorModel WHERE vl1_OperatorModel.user_id = :user_id)");
            $deleteNVBStmt->bindValue(":user_id", $array['user_id']);
            $deleteNVBStmt->execute();

            $deleteOperatorStmt = $this->db->prepare("DELETE FROM vl1_OperatorModel WHERE user_id = :user_id");
            $deleteOperatorStmt->bindValue(":user_id", $array['user_id']);
            $deleteOperatorStmt->execute();

            $deletePermissionStmt = $this->db->prepare("DELETE FROM vl1_PermissionModel WHERE user_id = :user_id");
            $deletePermissionStmt->bindValue(":user_id", $array['user_id']);
            $deletePermissionStmt->execute();

            $deleteSellStmt = $this->db->prepare("DELETE FROM vl1_SellModel WHERE user_id = :user_id");
            $deleteSellStmt->bindValue(":user_id", $array['user_id']);
            $deleteSellStmt->execute();

            $deleteServiceStmt = $this->db->prepare("DELETE FROM vl1_ServiceModel WHERE user_id = :user_id");
            $deleteServiceStmt->bindValue(":user_id", $array['user_id']);
            $deleteServiceStmt->execute();

            $deleteStoreStmt = $this->db->prepare("DELETE FROM vl1_StoreModel WHERE user_id = :user_id");
            $deleteStoreStmt->bindValue(":user_id", $array['user_id']);
            $deleteStoreStmt->execute();

            $deleteSubjectStmt = $this->db->prepare("DELETE FROM vl1_SubjectModel WHERE user_id = :user_id");
            $deleteSubjectStmt->bindValue(":user_id", $array['user_id']);
            $deleteSubjectStmt->execute();

            $deleteUserStmt = $this->db->prepare("DELETE FROM vl1_UserModel WHERE id = :user_id");
            $deleteUserStmt->bindValue(":user_id", $array['user_id']);
            $deleteUserStmt->execute();

            $this->db->commit();
            return ['status' => 1, 'message' => 'İstifadəçi uğurla silindi'];

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            $this->db->rollBack();
            return $response;

        }


    }

}
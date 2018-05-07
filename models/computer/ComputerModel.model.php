<?php

class ComputerModel extends CRUDModel implements GTDAO{

    public $id;
    public $user_id;
    public $identity;
    public $name;
    public $brand;
    public $country;
    public $type;
    public $color;
    public $image;
    public $description;

    public static $tableName = "vl1_ComputerModel";
    private static $typeIndex = 4;

    protected $db;

    public function __construct(){
        $this->db = (new DB())->start();
    }

    public static function __form($controller, $params = array()){

        return $controller::renderTemplate("goods" . ds . "computer.tpl", $params, true);

    }

    public static function createGT($array, $db){

        try{

            $imagePath = Utils::uploadImages("public" . ds . "user" . $array['user_id'] . ds . "goods_computer", "image");

            $stmt = $db->prepare("INSERT INTO " . self::$tableName . "(user_id, code, category_id, `name`, brand, model, country, `type`, color, image, description)
             VALUES(:user_id, :code, :category_id,:name, :brand, :model, :country, :type, :color, :image, :description)");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":code", $array['code']);
            $stmt->bindValue(":category_id", $array['category_id']);
            $stmt->bindValue(":name", $array['name']);
            $stmt->bindValue(":brand", $array['brand']);
            $stmt->bindValue(":model", $array['model']);
            $stmt->bindValue(":country", $array['country']);
            $stmt->bindValue(":type", $array['type']);
            $stmt->bindValue(":color", $array['color']);
            $stmt->bindValue(":image", $imagePath);
            $stmt->bindValue(":description", $array['description']);
            $stmt->execute();
            return $db->lastInsertId();

        } catch(Exception $e){
            throw $e;
        }

    }

    public static function updateGT($array, $db){

        try{

            $oldImagesStr = $array['old_images_str'];
            $oldImages = "";
            if(!empty($oldImagesStr) && $oldImagesStr != "" && $oldImagesStr != ";"){
                if(isset($array['old_image']) && is_array($array['old_image'])){
                    foreach($array['old_image'] as $oldImage){
                        $oldImagesStr = str_replace($oldImage . ";", "", $oldImagesStr);
                        $oldImages .= $oldImage . ";";
                    }
                }
                $toDelete = explode(";", $oldImagesStr);
                array_pop($toDelete);
                file_put_contents("log.txt", json_encode($toDelete));
                Utils::unlinkSet($toDelete);
            }

            $imagePath = Utils::uploadImages("public" . ds . "user" . $array['user_id'] . ds . "goods_computer", "image");
            $imagePath .= $oldImages;

            $stmt = $db->prepare("UPDATE " . self::$tableName . "
            SET category_id=:category_id, code=:code, `name`=:name, brand=:brand, model=:model, country=:country, `type`=:type,
            color=:color, image=:image, description=:description
            WHERE id=:id AND user_id=:user_id");
            $stmt->bindValue(":category_id", $array['category_id']);
            $stmt->bindValue(":code", $array['code']);
            $stmt->bindValue(":name", $array['name']);
            $stmt->bindValue(":brand", $array['brand']);
            $stmt->bindValue(":model", $array['model']);
            $stmt->bindValue(":country", $array['country']);
            $stmt->bindValue(":type", $array['type']);
            $stmt->bindValue(":color", $array['color']);
            $stmt->bindValue(":image", $imagePath);
            $stmt->bindValue(":description", $array['description']);
            $stmt->bindValue(":id", $array['model_id']);
            $stmt->bindValue(":user_id", $array['user_id']);
            return $stmt->execute();

        } catch(Exception $e){
            throw $e;
        }

    }

    public static function getAll($array, $db){
        try{


            $stmt = $db->prepare("SELECT * FROM " . self::$tableName . " AS sm
            JOIN vl1_GoodsModel AS gm ON sm.id = gm.goods_id AND gm.goods_type = " . self::$typeIndex . " WHERE sm.user_id=:user_id");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e){

            throw $e;

        }
    }

    public static function getOne($array, $db){

        try{

            $stmt = $db->prepare("SELECT cat.name AS category, obj.*, concat(obj.code, ', ',obj.`name`, ', ',obj.brand, ', ',obj.model) AS str FROM " . self::$tableName . " AS obj
            LEFT JOIN vl1_CategoryModel AS cat ON cat.id = obj.category_id
            WHERE obj.id = :id AND obj.user_id = :user_id");
            $stmt->bindValue(":id", $array['id']);
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            throw $e;

        }

    }

    public static function getByCode($array, $db){
        try{

            $sql = "SELECT goods.id AS gid, goods.buy_price, goods.barcode, goods.sell_price, obj.*,concat(obj.code, ', ',obj.`name`, ', ',obj.brand, ', ',obj.model, ', ',obj.type) as str
            FROM " . self::$tableName . " AS obj
            JOIN vl1_GoodsModel AS goods ON goods.goods_id = obj.id AND goods.goods_type = " . self::$typeIndex . "
            WHERE obj.user_id=:user_id AND code LIKE :code";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":code", "%" . $array['code'] . "%");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e){

            return false;

        }
    }

    public static function getByCodeAndBarcode($array, $db){
        try{

            $sql = "SELECT goods.id AS gid, goods.buy_price, goods.barcode, goods.sell_price, obj.*,concat(obj.code, ', ',obj.`name`, ', ',obj.brand, ', ',obj.model, ', ',obj.type) as str
            FROM " . self::$tableName . " AS obj
            JOIN vl1_GoodsModel AS goods ON goods.goods_id = obj.id AND goods.goods_type = " . self::$typeIndex . "
            WHERE obj.user_id=:user_id AND (code LIKE :code OR barcode LIKE :code)";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":code", "%" . $array['code'] . "%");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e){

            return false;

        }
    }

    public static function getTableAttrs(){
        return [
            'category' => 'Kategoriya',
            'barcode' => "Barkod",
            'code' => "Malın kodu",
            'name' => "Malın adı",
            'image' => "Şəkli",
            'brand' => "Markası (Brend)",
            'model' => "Modeli",
            'country' => "Ölkə",
            'type' => "Təsvir",
            'color' => "Rəngi",
			'currency' => "Valyuta",
            'buy_price' => "Alış qiyməti",
            'sell_price' => "Satış qiyməti"
        ];
    }

    public static function getFormAttrs(){
        return [
            'barcode' => "Barkod",
            'code' => "Malın kodu",
            'category' => 'Kategoriya',
            'name' => "Malın adı",
            'country' => "Ölkə",
            'brand' => "Markası (Brend)",
            'model' => "Modeli",
            'type' => "Təsvir",
            'currency' => "Valyuta",
            'color' => "Rəngi",
            'buy_price' => "Alış qiyməti",
            'sell_price' => "Satış qiyməti",
            'description' => "Əlavə məlumat",
            'image' => "Şəkli",
        ];
    }

    public static function getStaticFormAttrs(){
        return [
            'code' => "Malın kodu"
        ];
    }


    public static function getStructuredInfoAttrs(){

        return [

            'category' => 'Kategoriya',
            'barcode' => "Barkod",
            'code' => "Malın kodu",
            'name' => "Malın adı",
            'brand' => "Markası (Brend)",
            'model' => "Modeli",
            'country' => "Ölkə",
            'type' => "Təsvir",
            'color' => "Rəngi",
//            'buy_price' => "Alış qiyməti",
            'sell_price' => "Satış qiyməti",
            'description' => "Qeyd"

        ];

    }

}
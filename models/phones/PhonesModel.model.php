<?php

class PhonesModel extends CRUDModel implements GTDAO{

    public $id;
    public $user_id;
    public $category_id;
    public $code;
    public $imei;
    public $name;
    public $brand;
    public $model;
    public $country;
    public $type;
    public $color;
    public $image;
    public $description;

    public static $tableName = "vl1_PhonesModel";
    private static $typeIndex = 1;

    protected $db;

    public function __construct(){
        $this->db = (new DB())->start();
    }

    public static function __form($controller, $params = array()){

        return $controller::renderTemplate("goods" . ds . "phones.tpl", $params, true);

    }

    public static function createGT($array, $db){

        try{

            $imagePath = Utils::uploadImages("public" . ds . "user" . $array['user_id'] . ds . "goods_phones", "image");

            $stmt = $db->prepare("INSERT INTO " . self::$tableName . "(user_id, code, category_id, imei, `name`, brand, model, country, `type`, color, image, description)
             VALUES(:user_id, :code, :category_id, :imei, :name, :brand, :model, :country, :type, :color, :image, :description)");
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":code", $array['code']);
            $stmt->bindValue(":category_id", $array['category_id']);
            $stmt->bindValue(":imei", $array['imei']);
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

            $imagePath = Utils::uploadImages("public" . ds . "user" . $array['user_id'] . ds . "goods_phones", "image");
            $imagePath .= $oldImages;

            $stmt = $db->prepare("UPDATE " . self::$tableName . "
            SET category_id=:category_id, code=:code, imei=:imei, `name`=:name, brand=:brand, model=:model, country=:country, `type`=:type,
            color=:color, image=:image, description=:description
            WHERE id=:id AND user_id=:user_id");
            $stmt->bindValue(":category_id", $array['category_id']);
            $stmt->bindValue(":code", $array['code']);
            $stmt->bindValue(":imei", $array['imei']);
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

            $stmt = $db->prepare("SELECT * FROM " . self::$tableName . " AS sm JOIN vl1_GoodsModel AS gm ON sm.id = gm.goods_id AND gm.goods_type = " . self::$typeIndex . " WHERE sm.user_id=:user_id");
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

            $sql = "SELECT goods.id AS gid, goods.barcode, goods.buy_price, goods.sell_price, obj.*,concat(obj.code, ', ',obj.`name`, ', ',obj.brand, ', ',obj.model) as str
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

            $sql = "SELECT goods.id AS gid, goods.buy_price, goods.barcode, goods.sell_price, obj.*,concat(obj.code, ', ',obj.`name`, ', ',obj.brand, ', ',obj.model) as str
            FROM " . self::$tableName . " AS obj
            JOIN vl1_GoodsModel AS goods ON goods.goods_id = obj.id AND goods.goods_type = " . self::$typeIndex . "
            WHERE obj.user_id=:user_id AND (code LIKE :code OR barcode LIKE :code)";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":code", "%" . $array['code'] . "%");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e){

            Logger::writeExceptionLog($e);
            return false;

        }
    }

    public static function getTableAttrs(){
        return [
            'category' => 'Kategoriya',
            'barcode' => "IMEI",
            'code' => "Malın kodu",
            'name' => "Malın adı",
            'image' => "Şəkli",
            'brand' => "Markası (Brend)",
            'model' => "Modeli",
            'country' => "Ölkə",
            'type' => "Növü",
            'color' => "Rəngi",
			'currency' => "Valyuta",
            'buy_price' => "Alış qiyməti",
            'sell_price' => "Satış qiyməti"

        ];
    }

    public static function getFormAttrs(){
        return [
            'barcode' => "IMEI",
            'code' => "Malın kodu",
            'category' => 'Kategoriya',
            'name' => "Malın adı",
            'country' => "Ölkə",
            'brand' => "Markası (Brend)",
            'model' => "Modeli",
            'type' => "Növü",
            'currency' => "Valyuta",
            'color' => "Rəngi",
            'description' => "Əlavə məlumat",
            'buy_price' => "Alış qiyməti",
            'sell_price' => "Satış qiyməti",
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
            'imei' => "IMEI",
            'name' => "Malın adı",
            'brand' => "Markası (Brend)",
            'model' => "Modeli",
            'color' => "Rəngi",
            'type' => "Növü",
//            'buy_price' => "Alış qiyməti",
            'sell_price' => "Satış qiyməti",
            'description' => "Qeyd"

        ];

    }

    /*
    public static function getStructuredInfo($array, $db)
    {

        try{

            $stmt = $db->prepare("SELECT category.name AS category_name, model.imei, model.code, model.name, model.brand,
            model.model, model.country, model.type, model.color, model.image,
            model.description, goods.barcode, goods.sell_price, goods.buy_price
            FROM " . self::$tableName . " AS model
            LEFT JOIN vl1_CategoryModel AS category ON model.category_id = category.id
            LEFT JOIN vl1_GoodsModel AS goods ON goods.goods_id = model.id AND goods.goods_type = '" . self::$typeIndex . "'
            WHERE model.id=:id");
            $stmt->bindValue(":id", $array['goods_id']);
            if($stmt->execute()){

                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                if($res){

                    return [
                        'category' => ['title' => 'Kategoriya', 'val' => $res['category_name']],
                        'barcode' => ['title' => "Barkod", 'val' => ""],
                        'imei' => ['title' => "IMEI", 'val' => $res['imei']],
                        'code' => ['title' => "Malın kodu", 'val' => $res['code']],
                        'name' => ['title' => "Malın adı", 'val' => $res['name']],
                        'image' => ['title' => "Şəkli", 'val' => $res['image']],
                        'brand' => ['title' => "Markası (Brend)", 'val' => $res['brand']],
                        'model' => ['title' => "Modeli", 'val' => $res['model']],
                        'country' => ['title' => "Ölkə", 'val' => $res['country']],
                        'type' => ['title' => "Növü", 'val' => $res['type']],
                        'color' => ['title' => "Rəngi", 'val' => $res['color']],
                        'buy_price' => ['title' => "Alış qiyməti", 'val' => $res['buy_price']],
                        'sell_price' => ['title' => "Satış qiyməti", 'val' => $res['sell_price']],
                        'description' => ['title' => "Qeyd", 'val' => $res['description']],
                    ];

                }

            }
            throw new Exception("Nothing found");

        } catch(Exception $e) {

            Logger::writeExceptionLog($e);
            throw $e;

        }

    }
    */


}
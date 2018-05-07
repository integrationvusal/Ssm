<?php

class GoodsModel extends CRUDModel{

	public $id;
	public $user_id;
	public $goods_type;
	public $goods_id;

	private $tableName = "vl1_GoodsModel";

	protected $db;

	public function __construct(){
		$this->db = (new DB())->start();
	}

	public function createGoods($array){

		try{
			$this->db->beginTransaction();
			$goodsType = $array['goods_type'];
			$model = Application::$settings['goods_types'][$goodsType];
			$goodsId = $model['model_name']::createGT($array['post'], $this->db);
			$stmt = $this->db->prepare("INSERT INTO " . $this->tableName . "(user_id, `name`, goods_type, goods_id, barcode, buy_price, sell_price, currency)
			 VALUES(:user_id, :name, :goods_type, :goods_id, :barcode, :buy_price, :sell_price, :currency)");
			$stmt->bindValue(":user_id", $array['post']['user_id'], PDO::PARAM_INT);
			$stmt->bindValue(":name", $array['post']['name'], PDO::PARAM_INT);
			$stmt->bindValue(":goods_type", $goodsType, PDO::PARAM_INT);
			$stmt->bindValue(":goods_id", $goodsId, PDO::PARAM_INT);
			$stmt->bindValue(":barcode", $array['post']['barcode'], PDO::PARAM_STR);
			$stmt->bindValue(":buy_price", $array['post']['buy_price'], PDO::PARAM_STR);
			$stmt->bindValue(":sell_price", $array['post']['sell_price'], PDO::PARAM_STR);
			$stmt->bindValue(":currency", $array['post']['currency'], PDO::PARAM_INT);
			$stmt->execute();
			$this->db->commit();
			return true;
		} catch(Exception $e) {
			$this->db->rollBack();
			return false;
		}

	}

	public function updateGoods($array){

		try{
			$this->db->beginTransaction();
			$goodsType = $array['model_type'];
			$model = Application::$settings['goods_types'][$goodsType];
			$model['model_name']::updateGT($array, $this->db);
			$stmt = $this->db->prepare("UPDATE " . $this->tableName . "
			SET `name`=:name, barcode=:barcode, buy_price=:buy_price, sell_price=:sell_price, currency=:currency
			WHERE user_id=:user_id AND id=:id");
			$stmt->bindValue(":name", $array['name'], PDO::PARAM_INT);
			$stmt->bindValue(":barcode", $array['barcode'], PDO::PARAM_STR);
			$stmt->bindValue(":buy_price", $array['buy_price'], PDO::PARAM_STR);
			$stmt->bindValue(":sell_price", $array['sell_price'], PDO::PARAM_STR);
			$stmt->bindValue(":currency", $array['currency'], PDO::PARAM_INT);
			$stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_STR);
			$stmt->bindValue(":id", $array['goods_id'], PDO::PARAM_STR);
			$stmt->execute();
			$this->db->commit();
			return true;
		} catch(Exception $e) {
			$this->db->rollBack();
			return false;
		}

	}

	public function checkUsage($array){

		try{

			$stmt = $this->db->prepare("SELECT * FROM vl1_StoreModel WHERE user_id=:user_id AND goods_id = :goods_id");
			$stmt->bindValue(":user_id", $array['user_id']);
			$stmt->bindValue(":goods_id", $array['goods_id']);
			$stmt->execute();
			$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($res){
				foreach($res as $goods){
					if(($goods['status'] == 1 || $goods['status'] == 3) && $goods['count'] > 0){
						return ['status' => 0, 'message' => 'Bu mal dükanda mövcuddur'];
					} elseif(($goods['status'] == 2 || $goods['status'] == 3) && $goods['pending_count'] > 0) {
						return ['status' => 0, 'message' => 'Bu mal dükana əlavə olunma növbəsində mövcuddur'];
					}
				}

				$stmt = $this->db->prepare("SELECT * FROM vl1_SellModel WHERE user_id=:user_id AND goods_id = :goods_id AND status = '2'");
				$stmt->bindValue(":user_id", $array['user_id']);
				$stmt->bindValue(":goods_id", $array['goods_id']);
				$stmt->execute();
				$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if($res) return ['status' => 0, 'message' => 'Bu mal satış növbəsində mövcuddur'];
			}
			return ['status' => 1];

		} catch(Exception $e) {

			Logger::writeExceptionLog($e);
			return ['status' => 0, 'message' => 'Əmaliyyat zamanı səhv'];

		}

	}

	public function deleteGoods($array){

		try{
			$this->db->beginTransaction();

			$stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id=:user_id AND id=:goods_id");
			$stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_STR);
			$stmt->bindValue(":goods_id", $array['goods_id'], PDO::PARAM_STR);
			$stmt->execute();
			$goods = $stmt->fetch(PDO::FETCH_ASSOC);
			$images = "";
			if($goods) {

				$model = Application::$settings['goods_types'][$goods['goods_type']]['model_name'];

				$stmt = $this->db->prepare("SELECT image FROM " . $model::$tableName . " WHERE user_id = :user_id AND id = :goods_id");
				$stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_STR);
				$stmt->bindValue(":goods_id", $goods['goods_id'], PDO::PARAM_STR);
				$stmt->execute();
				$res = $stmt->fetch(PDO::FETCH_ASSOC);
				if($res) $images = $res['image'];


				$stmt = $this->db->prepare("DELETE FROM " . $model::$tableName . " WHERE user_id = :user_id AND id = :goods_id");
				$stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_STR);
				$stmt->bindValue(":goods_id", $goods['goods_id'], PDO::PARAM_STR);
				$stmt->execute();

			} else {
				throw new Exception("Couldn't get info of goods");
			}

			$stmt = $this->db->prepare("DELETE FROM vl1_StoreModel WHERE user_id = :user_id AND goods_id = :goods_id AND count <= 0 AND status = '1'");
			$stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_STR);
			$stmt->bindValue(":goods_id", $array['goods_id'], PDO::PARAM_STR);
			$stmt->execute();

			$stmt = $this->db->prepare("DELETE FROM " . $this->tableName . " WHERE user_id = :user_id AND id = :goods_id");
			$stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_STR);
			$stmt->bindValue(":goods_id", $array['goods_id'], PDO::PARAM_STR);
			$stmt->execute();

			$this->db->commit();

			Utils::unlinkSet(explode(";", $images));
			return true;
		} catch(Exception $e) {
			$this->db->rollBack();
			Logger::writeExceptionLog($e);
			return false;
		}

	}

	public function getAll($array, $limit = 0, $offset = 0){

		try{

			$modelName = Application::$settings['goods_types'][$array['goods_type']]['model_name'];
			$tableName = 'vl1_' . $modelName;
			$stmt = $this->db->prepare("SELECT *, model.name AS `name`, goods.id AS id, cr.name as currency, cat.name AS category  FROM " . $this->tableName . " AS goods
			LEFT JOIN " . $tableName . " AS model ON goods.goods_id = model.id
			LEFT JOIN vl1_CategoryModel AS cat ON model.category_id=cat.id
			LEFT JOIN vl1_CurrencyModel AS cr ON goods.currency=cr.id
			WHERE goods.user_id = :user_id AND goods.goods_type=:goods_type ORDER BY goods.id DESC
			LIMIT :limit OFFSET :offset");
			$stmt->bindValue(":goods_type", $array['goods_type'], PDO::PARAM_INT);
			$stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
			$stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
			$stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);

		} catch(Exception $e) {

			Logger::writeExceptionLog($e);
			return false;

		}

	}

	public function getSearchAll($array){

		try{
			$searchLine = "";
			if(array_key_exists('search_goods', $array)){
				$modelName = Application::$settings['goods_types'][$array['goods_type']]['model_name'];
				$searchAttrs = $modelName::getTableAttrs();
				foreach($searchAttrs as $searchAttr => $title){
					if(array_key_exists('search_' . $searchAttr, $array)){
						if($searchAttr == 'category'){
							if($array['search_category'] > 0) $searchLine .= " AND model.category_id = " . ((int)$array['search_category']);
						} elseif($searchAttr == 'buy_price' || $searchAttr == 'sell_price') {
							$tmpStr = $array['search_' . $searchAttr];
							$sign = substr($tmpStr, 0, 1);
							$range = explode("-", $tmpStr);
							if($sign == '>' || $sign == '<' || $sign == '='){
								$value = str_replace($sign, "", $tmpStr);
								$searchLine .= sprintf(" AND IFNULL(goods.%s, '') %s %s", $searchAttr, $sign, $value);
							} elseif(count($range) > 1) {
								$from = $range[0];
								$to = $range[1];
								$searchLine .= sprintf(" AND IFNULL(goods.%s, '') BETWEEN %s AND %s", $searchAttr, $from, $to);
							}  else {
								$searchLine .= sprintf(" AND IFNULL(goods.%s, '') LIKE '%%%s%%'", $searchAttr, $tmpStr);
							}
						} elseif($searchAttr == 'barcode') {
							$searchLine .= sprintf(" AND IFNULL(goods.%s, '') LIKE '%%%s%%'", $searchAttr, $array['search_' . $searchAttr]);
						} else {
							$searchLine .= sprintf(" AND IFNULL(model.%s, '') LIKE '%%%s%%'", $searchAttr, $array['search_' . $searchAttr]);
						}
					}
				}
			}

			$tableName = 'vl1_' . $modelName;
			$query = "SELECT *, model.name AS `name`, goods.id AS id, cat.name AS category  FROM " . $this->tableName . " AS goods
			LEFT JOIN " . $tableName . " AS model ON goods.goods_id = model.id
			LEFT JOIN vl1_CategoryModel AS cat ON model.category_id=cat.id
			LEFT JOIN vl1_CurrencyModel AS cr ON goods.currency=cr.id
			WHERE goods.user_id = :user_id AND goods.goods_type=:goods_type " . $searchLine . " ORDER BY goods.id DESC";

			Logger::writeLog($query);

			$stmt = $this->db->prepare($query);
			$stmt->bindValue(":goods_type", $array['goods_type'], PDO::PARAM_INT);
			$stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);

		} catch(Exception $e) {

			Logger::writeExceptionLog($e);
			return false;

		}

	}

	public function getCountAll($array){
		try{

			$modelName = Application::$settings['goods_types'][$array['goods_type']]['model_name'];
			$tableName = 'vl1_' . $modelName;
			$stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM " . $this->tableName . "
			WHERE user_id = :user_id AND goods_type=:goods_type");
			$stmt->bindValue(":goods_type", $array['goods_type'], PDO::PARAM_INT);
			$stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
			$stmt->execute();
			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			return $res['cnt'];

		} catch(Exception $e) {

			return false;

		}
	}

	public function getOne($array){

		try{
			$data = array();
			$stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE id = :id AND user_id = :user_id");
			$stmt->bindValue(":id", $array['id']);
			$stmt->bindValue(":user_id", $array['user_id']);
			$stmt->execute();
			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			if($res){
				$data['common'] = $res;
				$modelName = Application::$settings['goods_types'][$res['goods_type']]['model_name'];
				$tmp = $modelName::getOne(['id' => $res['goods_id'], 'user_id' => $array['user_id']], $this->db);
				$attrs = $modelName::getStructuredInfoAttrs();
				if($tmp){
					$data['model'] = $tmp;
					$data['attrs'] = $attrs;
					return $data;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} catch(Exception $e) {

			return false;

		}

	}

	public function getByCode($array){
		$model = Application::$settings['goods_types'][$array['goods_type']]['model_name'];

		try{

            $sql = "SELECT goods.id AS gid, goods.currency, goods.buy_price, goods.barcode, goods.sell_price, obj.*,concat(obj.code, ', ',obj.`name`, ', ',obj.brand, ', ',obj.model, ', ',obj.`size`) as str
            FROM " . $model::$tableName . " AS obj
            JOIN vl1_GoodsModel AS goods ON goods.goods_id = obj.id AND goods.goods_type = " . $array['goods_type'] . "
            WHERE obj.user_id=:user_id AND code LIKE :code";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":code", "%" . $array['code'] . "%");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e){

            return false;

        }

	}

	public function getByCodeAndBarcode($array){
		$model = Application::$settings['goods_types'][$array['goods_type']]['model_name'];
		try{

            $sql = "SELECT goods.id AS gid, goods.currency , goods.buy_price, goods.barcode, goods.sell_price, obj.*,concat(obj.code, ', ',obj.`name`, ', ',obj.brand, ', ',obj.model, ', ',obj.`size`) as str
            FROM " . $model::$tableName . " AS obj
            JOIN vl1_GoodsModel AS goods ON goods.goods_id = obj.id AND goods.goods_type = " . $array['goods_type'] . "
            WHERE obj.user_id=:user_id AND (code LIKE :code OR barcode LIKE :code)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":user_id", $array['user_id']);
            $stmt->bindValue(":code", "%" . $array['code'] . "%");
            $stmt->execute();

            file_put_contents(__DIR__.'/loading',  print_R($array, true));
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e){

            return false;

        }

	}

}
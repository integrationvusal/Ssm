<?php

class ClientModel extends CRUDModel{

	public $id;
	public $name;
	public $address;
	public $phone;
	public $email;
	public $image;
	public $user_id;

	private $tableName = "vl1_ClientModel";

	protected $db;

	public function __construct(){
		$this->db = (new DB())->start();
	}

	public function createClient($array){

		$prefixes = $array['prefix'];
		$phones = $array['phone'];
		array_pop($prefixes);
		array_pop($phones);
		$phone = "";
		$k = count($prefixes);
		foreach($prefixes as $key => $val){
			if($key == $k) $delim = ""; else $delim = ";";
			$phone .= $val . " - " . $phones[$key] . $delim;
		}

		foreach($array['name'] as $key => $val){
			$array['name'][$key] = trim($val);
		}

		$imagePath = Utils::uploadImages("public" . ds . "user" . $array['user_id'] . ds .  "clients", "image");

		try{
			$stmt = $this->db->prepare("INSERT INTO " . $this->tableName . "(subject_id ,`name`, address, phone, email, image, user_id)
            VALUES(:subject_id, :name, :address, :phone, :email, :image, :user_id)");
			$stmt->bindValue(":subject_id", $array['subject_id'], PDO::PARAM_INT);
			$stmt->bindValue(":name", join(" ", $array['name']), PDO::PARAM_STR);
			$stmt->bindValue(":address", $array['address'], PDO::PARAM_STR);
			$stmt->bindValue(":phone", $phone, PDO::PARAM_STR);
			$stmt->bindValue(":email", $array['email'], PDO::PARAM_STR);
			$stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_STR);
			$stmt->bindValue(":image", $imagePath, PDO::PARAM_STR);
			return $stmt->execute();

		} catch(Exception $e) {
			return false;
		}

	}

	public function updateClient($array){

		$prefixes = $array['prefix'];
		$phones = $array['phone'];
		array_pop($prefixes);
		array_pop($phones);
		$phone = "";
		$k = count($prefixes);
		foreach($prefixes as $key => $val){
			if($key == $k) $delim = ""; else $delim = ";";
			$phone .= $val . " - " . $phones[$key] . $delim;
		}

		$imagePath = "";
		$toDelete = $array['image'];
		if(isset($array['old_image'])){
			foreach($array['old_image'] as $key => $oldImage){
				$imagePath .= $oldImage . ";";
				$toDelete = str_replace($oldImage . ";", "", $toDelete);
			}
			trim($toDelete, ";");
			$toDelete = explode(";", $toDelete);
			Utils::unlinkSet($toDelete);
			if(isset($_FILES['image'])){
				$imagePath .= Utils::uploadImages("public" . ds . "user" . $array['user_id'] . ds .  "clients", "image");
			}
		} else {
			trim($toDelete, ";");
			$toDelete = explode(";", $toDelete);
			Utils::unlinkSet($toDelete);
			$imagePath = "";
			if(isset($_FILES['image'])){
				$imagePath .= Utils::uploadImages("public" . ds . "user" . $array['user_id'] . ds .  "clients", "image");
			}
		}

		try{
			$stmt = $this->db->prepare("UPDATE " . $this->tableName . " SET name=:name, address=:address, phone=:phone, email=:email,
            image=:image WHERE id=:id AND user_id=:user_id AND subject_id = :subject_id");

			$stmt->bindValue(":name", join(" ", $array['name']), PDO::PARAM_STR);
			$stmt->bindValue(":address", $array['address'], PDO::PARAM_STR);
			$stmt->bindValue(":phone", $phone, PDO::PARAM_STR);
			$stmt->bindValue(":email", $array['email'], PDO::PARAM_STR);
			$stmt->bindValue(":image", $imagePath, PDO::PARAM_STR);
			$stmt->bindValue(":id", $array['id'], PDO::PARAM_INT);
			$stmt->bindValue(":subject_id", $array['subject_id'], PDO::PARAM_INT);
			$stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
			return $stmt->execute();
		} catch(Exception $e) {
			return false;
		}

	}

	public function deleteClient($array){
		try{
			$this->db->beginTransaction();

			$stmt = $this->db->prepare("SELECT image FROM " . $this->tableName . " WHERE id=:id");
			$stmt->bindValue(":id", $array['id'], PDO::PARAM_INT);
			$stmt->execute();
			$res = $stmt->fetch(PDO::FETCH_ASSOC);

			$images = $res['image'];

			$stmt = $this->db->prepare("SELECT debt FROM " . $this->tableName . " c LEFT JOIN vl1_ClientDebtModel d ON d.client_id = c.id
					WHERE c.id=:id AND debt > 0");
			$stmt->bindValue(":id", $array['id'], PDO::PARAM_INT);
			$stmt->execute();

			if($stmt->fetch())
				return ['status' => 0, 'message' => 'Bu müştərinin ödənilməmiş borcu mövcuddur'];
			else{
				$stmt = $this->db->prepare("DELETE c,d FROM " . $this->tableName . " c LEFT JOIN vl1_ClientDebtModel d ON d.client_id = c.id WHERE c.id=:id");
				$stmt->bindValue(":id", $array['id'], PDO::PARAM_INT);
				$stmt->execute();
				if($stmt->rowCount()) {
					$this->db->commit();
					if(!empty($images))	Utils::unlinkSet(explode(";", $images));
					return ['status' => 1];
				}

			}

			return ['status' => 0, 'message' => 'Əməliyyat zamanı xəta'];

		} catch(Exception $e) {

			Logger::writeExceptionLog($e);
			$this->db->rollBack();
			return false;

		}
	}

	public function getAll($array){

		try{
			$stmt = $this->db->prepare("SELECT c.*, d.currency, d.debt FROM " . $this->tableName . " c LEFT JOIN vl1_ClientDebtModel d ON d.client_id = c.id WHERE user_id = :user_id AND subject_id = :subject_id ORDER BY d.currency, d.debt");
			$stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
			$stmt->bindValue(":subject_id", $array['subject_id'], PDO::PARAM_INT);
			$stmt->execute();
			$data = [];
			foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $key => $client) {
				if(!array_key_exists($client['id'], $data)){
					$client['debts'][$client['currency']] = $client['debt'];
					unset($client['debt'], $client['currency']);
					$data[$client['id']] = $client;
				}
				else 	$data[$client['id']]['debts'][$client['currency']] = $client['debt'];
			}

			return array_values($data);

		} catch(Exception $e) {
			return false;
		}

	}



	public function getSearchAll($array){

		try{
			$stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id = :user_id AND name LIKE :name ORDER BY id DESC");
			$stmt->bindValue(":user_id", $array['user_id'], PDO::PARAM_INT);
			$stmt->bindValue(":name", '%' . $array['client_search'] . '%', PDO::PARAM_STR);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(Exception $e) {
			return false;
		}

	}

	public function getOne($array){

		try{
			$stmt = $this->db->prepare("SELECT c.*, d.currency, d.currency_archive, d.debt  FROM " . $this->tableName . " c
			LEFT JOIN vl1_ClientDebtModel d ON d.client_id = c.id AND d.currency = :currency
			WHERE c.id = :id");
			$stmt->bindValue(":id", $array['id'], PDO::PARAM_INT);
			$stmt->bindValue(":currency", $array['currency'], PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(Exception $e) {
			return false;
		}

	}

	public function increaseDebt($array, $db){
		try{
			if($array['client'] == 0) return true;
			$debt = $array['amount'] - $array['debtamount'];

			$stmt = $db->prepare("UPDATE vl1_ClientDebtModel SET debt = debt + :debt WHERE currency = :currency AND client_id = :id");
			$stmt->bindValue(":debt", $debt, PDO::PARAM_STR);
			$stmt->bindValue(":currency", $array['currency'], PDO::PARAM_INT);
			$stmt->bindValue(":id", $array['client'], PDO::PARAM_INT);
			$stmt->execute();

			if(!$stmt->rowCount()){
				$stmt = $db->prepare("INSERT INTO vl1_ClientDebtModel (client_id, debt, currency, currency_archive)VALUES(:client_id, :debt, :currency, :currency_archive)");
				$stmt->bindValue(":debt", $debt, PDO::PARAM_STR);
				$stmt->bindValue(":currency", $array['currency'], PDO::PARAM_INT);
				$stmt->bindValue(":currency_archive", $array['currency_archive'], PDO::PARAM_STR);
				$stmt->bindValue(":client_id", $array['client'], PDO::PARAM_INT);
				$stmt->execute();
			}

			return $stmt->rowCount();

		} catch(Exception $e) {
			Logger::writeExceptionLog($e);
			throw $e;
		}

	}

	public function decreaseDebt($array, $db){
		try{

			$stmt = $db->prepare("UPDATE vl1_ClientDebtModel SET debt = debt - :debt
            WHERE client_id = :client_id AND currency = :currency");
			$stmt->bindValue(":debt", $array['debt'], PDO::PARAM_STR);
			$stmt->bindValue(":client_id", $array['client'], PDO::PARAM_INT);
			$stmt->bindValue(":currency", $array['currency'], PDO::PARAM_INT);
			$stmt->execute();

		} catch(Exception $e) {
			throw $e;
		}

	}

	public function payToCashbox($array){

		try{
			$this->db->beginTransaction();
			$invoiceModel = new InvoiceModel();
			$cashboxModel = new CashboxModel();

			$array['client'] = $array['client_id'];
			$array['debt'] = $array['amount'];
			$array['invoice_status'] = '1';
			$array['payed'] = $array['debt'];
			$array['amount'] = 0;
			$array['invoice_id'] = $invoiceModel->createInvoice($array, $this->db);
			$this->decreaseDebt($array, $this->db);

			$array['cashbox_id'] = $cashboxModel->getCurrent($array)['id'];
			$cashboxModel->increaseAmount($array, $this->db);
			$this->db->commit();
			return true;

		} catch(Exception $e) {

			Logger::writeExceptionLog($e);
			$this->db->rollBack();
			return false;

		}

	}

}
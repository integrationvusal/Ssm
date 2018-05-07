<?php

class ClientController extends Controller{

	public static function main($request, $main, $context = null){

		$user = RBACController::getUser('client_read');
		$subject = SubjectController::getCurrentSubject();

		$client = new ClientModel();
		try{
			$flash = SessionStorage::getFlash("client");
		} catch(Exception $e) {
			$flash = null;
		}

		$currencies = (new CurrencyModel)->getAllWithAZN();

		$clients = $client->getAll(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

		$invoiceModel = new InvoiceModel();
		$invoice['type'] = 6;
		$invoice['serial'] = $invoiceModel->getNextInvoiceNumber(['user_id' => $user['id'], 'type' => $invoice['type']]);

		$cbsOfUser = (new CashboxModel())->getAllCBForUser(['user_id' => $user['id']]);

		$operator = $user['id'];

		self::renderTemplate("client" . ds . "client.tpl", [
			'user' => $user,
			'context' => $context,
			'flash' => $flash,
			'clients' => $clients,
			'currencies' => $currencies,
			'currentDate' => date("Y-m-d"),
			'invoice' => $invoice,
			'cashboxes' => $cbsOfUser,
			'cashbox_id' => (new CashboxModel())->getCurrent(['user_id' => $user['id'], 'subject_id' => $subject['id']]),
			'operator' => $operator,
			'current_menu' => 'client'
		]);
	}

	public static function create($request, $vars){
		$user = RBACController::getUser('client_create');
		$subject = SubjectController::getCurrentSubject();
		if($request->isPost()){

			$_POST['subject_id'] = $subject['id'];
			$client = new ClientModel();
			$context['type'] = 0;
			$context['status'] = $client->createClient($_POST);
			$context['title'] = "Yeni müştəri";
			if($context['status']){
				$context['message'] = "Yeni müştəri əlavə olundu";
			} else {
				$context['message'] = "Yeni müştərini əlavə etmək mümkün olmadı";
			}
			SessionStorage::addFlash("client", $context);
			header("Location: " . Application::$settings['url'] . "/client");

		} else {

			ApplicationController::pageNotFound();

		}
	}

	public static function update($request, $vars){

		$user = RBACController::getUser('client_update');
		$subject = SubjectController::getCurrentSubject();
		$client = new ClientModel();
		if($request->isPost()){

			$_POST['subject_id'] = $subject['id'];
			$context['type'] = 0;
			$context['status'] = $client->updateClient($_POST);
			$context['title'] = "Redaktə et";
			if($context['status']){
				$context['message'] = "Müştəri yeniləndi";
			} else {
				$context['message'] = "Müştərini yeniləmək mümkün olmadı";
			}
			SessionStorage::addFlash("client", $context);
			header("Location: " . Application::$settings['url'] . "/client");

		} else {

			$client_id = isset($vars['client_id']) ? $vars['client_id']: 0;

			$client = $client->getOne(['id' => $client_id, 'user_id' => $user['id'], 'subject_id' => $subject['id']]);

			if($client){
				$nc = explode(" ", $client['name']);
				$pc = explode(";", $client['phone']);
				$prefixes = array();
				$phones = array();
				foreach($pc as $p){
					$pt = explode(" - ", $p);
					$prefixes[] = isset($pt[0]) ? $pt[0] : "";
					$phones[] = isset($pt[1]) ? $pt[1] : "";
				}
				unset($prefixes[count($prefixes) - 1]);
				unset($phones[count($phones) - 1]);
				$names = array('firstname' => isset($nc[0]) ? $nc[0] : "",
					'lastname' => isset($nc[1]) ? $nc[1] : "",
					'fathername' => isset($nc[2]) ? $nc[2] : "");
				$client['name'] = $names;
				$client['phone'] = array('prefixes' => $prefixes, 'phones' => $phones);
				$images = explode(";", $client['image']);
				if(count($images) > 1) unset($images[count($images)-1]);
				$client['images'] = $images;
			}

			$context['type'] = 1;

			if($client) $context['client'] = $client;
			self::main($request, $vars, $context);
		}

	}

	public static function delete($request, $vars){

		$user = RBACController::getUser('client_delete');
		$subject = SubjectController::getCurrentSubject();
		if($request->isPost()){
			$client = new ClientModel();

			$response = $client->deleteClient($_POST);
			$_POST['subject_id'] = $subject['id'];
			$context['type'] = 0;
			$context['status'] = $response['status'];
			$context['title'] = "Müştərini sil";
			if($context['status']){
				$context['message'] = "Müştəri silindi";
			} else {
				$context['message'] = $response['message'];
				//$context['message'] = "Müştərini silmək mümkün olmadı";
			}
			SessionStorage::addFlash("client", $context);
			header("Location: " . Application::$settings['url'] . "/client");
		} else {
			ApplicationController::pageNotFound();
		}

	}

	public static function pay($request, $vars){

		$user = RBACController::getUser('client_pay');
		$subject = SubjectController::getCurrentSubject();
		if($request->isPost()){

			$client = new ClientModel();
			$_POST['subject_id'] = $subject['id'];
			$response = $client->payToCashbox($_POST);
			$context['type'] = 0;
			$context['status'] = $response;
			$context['title'] = "Müştəridən ödəniş";
			if($context['status']){
				$context['message'] = "Ödəniş qəbul olundu";
			} else {
				$context['message'] = "Ödənişi qəbul etmək mümkün olmadı";
			}
			SessionStorage::addFlash("client", $context);
			header("Location: " . Application::$settings['url'] . "/client");

		} else {
			ApplicationController::pageNotFound();
		}

	}

	public static function search($request, $vars){

		$user = RBACController::getUser();
		$subject = SubjectController::getCurrentSubject();
		if($request->isPost() && $request->isAjax()){

			$response['status'] = 0;
			$_POST['subject_id'] = $subject['id'];
			$data = (new ClientModel())->getSearchAll($_POST);
			if($data){
				$response['status'] = 1;
				$response['data'] = $data;
			}
			echo json_encode($response);
		} else {

			ApplicationController::pageNotFound();

		}

	}

}
<?php

class SellController extends Controller{

    public static function main($request, $vars){

        $user = RBACController::getUser('sell_read');
        $subject = SubjectController::getCurrentSubject();
        $invoice['type'] = 1;

        $clients = (new ClientModel())->getAll(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

        $sellModel = new SellModel();
        $sellPendings = $sellModel->getAllPendings(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

        $cashbox = (new CashboxModel())->getCurrent(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

        if(!$cashbox || !isset($cashbox['id']) || $cashbox['id'] <= 0){
            $cashbox = null;
        }

        $flash = SessionStorage::getFlash('sell');

        if($sellPendings){

            $invoice['serial'] = $sellPendings[0]['serial'];
            $invoice['notes'] = $sellPendings[0]['notes'];
            $invoice['date'] = $sellPendings[0]['date'];
            $invoice['id'] = $sellPendings[0]['invoice_id'];

        } else {

            $invoice['serial'] = (new InvoiceModel())->getNextInvoiceNumber(['type' => 1, 'user_id' => $user['id']]);
            $invoice['notes'] = "";
            $invoice['date'] = date("Y-m-d");
            $invoice['id'] = 0;

        }

        $data = [
            'user' => $user,
            'subject' => $subject,
            'invoice' => $invoice,
            'pendings' => $sellPendings,
            'pendings_count' => count($sellPendings),
            'flash' => $flash,
            'clients' => $clients,
            'cashbox' => $cashbox,
            'currentDate' => date("Y-m-d"),
            'current_menu' => 'sell',
            'currencies'=>(new CurrencyModel())->getAllWithAZN()
        ];

        self::renderTemplate("sell" . ds . "store.tpl", $data);

    }

    public static function returnGoods($request, $vars){

        $user = RBACController::getUser();
        $subject = SubjectController::getCurrentSubject();
        $invoice['type'] = 7;

        $clients = (new ClientModel())->getAll(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

        $sellModel = new SellModel();
        $sellPendings = $sellModel->getAllPendings(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

        $cashbox = (new CashboxModel())->getCurrent(['user_id' => $user['id'], 'subject_id' => $subject['id']]);
        if(!$cashbox || !isset($cashbox['id']) || $cashbox['id'] <= 0){
            $cashbox = null;
        }

        $operator = $user['id'];

        $flash = SessionStorage::getFlash('return');

        if($sellPendings){

            $invoice['serial'] = $sellPendings[0]['serial'];
            $invoice['notes'] = $sellPendings[0]['notes'];
            $invoice['date'] = $sellPendings[0]['date'];
            $invoice['id'] = $sellPendings[0]['invoice_id'];

        } else {

            $invoice['serial'] = (new InvoiceModel())->getNextInvoiceNumber(['type' => $invoice['type'], 'user_id' => $user['id']]);
            $invoice['notes'] = "";
            $invoice['date'] = date("Y-m-d");
            $invoice['id'] = 0;

        }

        $data = [
            'user' => $user,
            'subject' => $subject,
            'invoice' => $invoice,
            'pendings' => $sellPendings,
            'pendings_count' => count($sellPendings),
            'flash' => $flash,
            'clients' => $clients,
            'cashbox' => $cashbox,
            'operator' => $operator,
            'currentDate' => date("Y-m-d"),
            'current_menu' => 'return'
        ];

        self::renderTemplate("sell" . ds . "return.tpl", $data);
    }

    public static function returnGoodsSearch($request, $vars){

        $user = RBACController::getUser();
        $subject = SubjectController::getCurrentSubject();
        if($request->isAjax() && $request->isPost()){

            $response = ['status' => 0, 'data' => null];
            $sellModel = new SellModel();
            $storeGoods = $sellModel->getAllByCodeAndBarcode($_POST);

            if($storeGoods){
                $response = ['status' => 1, 'data' => $storeGoods];
            }
            echo json_encode($response);

        } else {

            ApplicationController::pageNotFound();

        }
    }

    public static function returnGoodsApprove($request, $vars){

        $user = RBACController::getUser();
        $subject = SubjectController::getCurrentSubject();
        if($request->isAjax() && $request->isPost()){

            $response = ['status' => 0];
            $storeModel = new StoreModel();
            $context['type'] = 0;
            $context['status'] = $storeModel->returnGoods($_POST);
            $context['title'] = "Müştəridən geri";
            if($context['status']){
                $response['status'] = 1;
                $context['message'] = "Əməliyyat uğurla başa çatdı";
            } else {
                $context['message'] = "Əməliyyatı başa çatdırmaq mümkün olmadı";
            }

            SessionStorage::addFlash("return", $context);
            echo json_encode($response);

        } else {

            ApplicationController::pageNotFound();

        }

    }

    public static function search($request, $vars){

        $user = RBACController::getUser();
        $subject = SubjectController::getCurrentSubject();
        if($request->isAjax() && $request->isPost()){

            $response = ['status' => 0, 'data' => null];
            $storeModel = new StoreModel();
            $storeGoods = $storeModel->getAllByCodeAndBarcode($_POST);

            if($storeGoods){
                $response = ['status' => 1, 'data' => $storeGoods];
            }
            echo json_encode($response);

        } else {

            ApplicationController::pageNotFound();

        }

    }

    public static function add($request, $vars){

        $user = RBACController::getUser();
        $subject = SubjectController::getCurrentSubject();
        if($request->isAjax() && $request->isPost()){

            $response = ['status' => 0, 'data' => null];
            $sellModel = new SellModel();
            $pendingSell = $sellModel->createSell($_POST);

            if($pendingSell){
                $response = ['status' => 1, 'data' => $pendingSell];
            }
            echo json_encode($response);

        } else {

            ApplicationController::pageNotFound();

        }
    }

    public static function delete($request, $vars){

        $user = RBACController::getUser();
        $subject = SubjectController::getCurrentSubject();
        if($request->isAjax() && $request->isPost()){

            $response = ['status' => 0];
            $sellModel = new SellModel();

            if($sellModel->deleteSell($_POST)){
                $response = ['status' => 1];
            }
            echo json_encode($response);

        } else {

            ApplicationController::pageNotFound();

        }
    }

    public static function reject($request, $vars){

        $user = RBACController::getUser();
        $subject = SubjectController::getCurrentSubject();

        if($request->isPost()){

            $sellModel = new SellModel();

            $context['type'] = 0;
            $context['status'] = $sellModel->rejectSell($_POST);
            $context['title'] = "Satış";
            if($context['status']){
                $context['message'] = "Satışdan İmtina olundu";
            } else {
                $context['message'] = "İmtina etmək mümkün olmadı";
            }

            SessionStorage::addFlash("sell", $context);

            header("Location: " . Application::$settings['url'] . "/sell");

        } else {

            ApplicationController::pageNotFound();

        }


    }

    public static function approve($request, $vars){

        $user = RBACController::getUser();
        $subject = SubjectController::getCurrentSubject();
        if($request->isAjax() && $request->isPost()){

            $response = ['status' => 0];

            $sellModel = new SellModel();
            
            $invoiceModel = new InvoiceModel();

            $context['type'] = 0;

            $status ='';
            
            $_arr = $_POST;

			unset($_arr['mydata']);

			$new_arr=[];
			foreach($_POST['mydata'] as $currency=>$mydata){
				$_arr['client_id'] = $_arr['client'] = $mydata['cash'] == 1?0:$mydata['client'];
				$_arr['currency'] = $mydata['currency'];
				$_arr['currency_archive'] = $mydata['currency_archive'];
				$_arr['received_payment'] = $mydata['received_payment'];
				$_arr['debtamount'] = $mydata['debtamount'];
				$_arr['cash'] = $mydata['cash'];
				$_arr['amount'] = $mydata['amount'];
				$_arr['invoice_serial'] = $mydata['invoice_serial'];
				$_arr['invoice_status'] = 1;
				
				
				if($mydata['invoice_id']){
				    $_arr['invoice_id'] = $mydata['invoice_id'];
				}elseif(!$_arr['invoice_id'] = $invoiceModel->checkInvoice($mydata['invoice_serial'])){
				    $_arr['invoice_id'] = $invoiceModel->createInvoice($_arr, $sellModel->getDB());
				}

				$sellModel->updateInvoiceItems($mydata['ids'], $_arr['invoice_id']);
                $new_arr[] = $_arr;
			}
            foreach($new_arr as $a){
                $status .= $sellModel->approveSell($a);
            }
            

            $context['status'] = $status;
            $context['title'] = "Satış";
            if($context['status']){
                $context['message'] = "Satış təsdiqləndi";
                $context['sell_data'] = $context['status'];
                $response = ['status' => 1];
                SessionStorage::addFlash("sell", $context);
            } else {
                $context['message'] = "Satışı təsdiqləmək mümkün olmadı";
            }

            echo json_encode($response);

        } else {

            ApplicationController::pageNotFound();

        }
    }

    public static function updatepriceandcount($request, $vars){

        $user = RBACController::getUser();
        $subject = SubjectController::getCurrentSubject();
        if($request->isAjax() && $request->isPost()){

            $response = ['status' => 0];

            $sellModel = new SellModel();
            if($sellModel->updateCountAndPrice($_POST)) $response['status'] = 1;

            echo json_encode($response);

        } else {

            ApplicationController::pageNotFound();

        }
    }

    public static function searchgoods($request, $vars){

        $user = RBACController::getUser();
        if($request->isPost() && $request->isAjax()){

            $response['status'] = 0;
            $data = (new SellModel())->getAllByCodeAndBarcodeAndInfo($_POST);
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
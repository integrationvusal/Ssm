<?php

class StoreController extends Controller{

    public static function main($request, $vars){
        $user = RBACController::getUser('store_read');
        $subject = SubjectController::getCurrentSubject();

        $invoiceType = 0;
        if($subject['type'] == 1) $invoiceType = 9;

        $pendingGoods = (new StoreModel())->getAllPendings(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

        $flash = SessionStorage::getFlash("store");

        $operator = $user['id'];

        $data = array();

        $currencies = (new CurrencyModel())->getAll();

        if($pendingGoods){

            $invoice_ids= [];
            $invoice_archives= [];
            foreach ($pendingGoods as $pg){
                $invoice_ids[$pg['currency']] = $pg['invoice_id'];
                $invoice_archives[$pg['currency']] = $pg['currency_archive'];
            }

            $contragent = ['id' => $pendingGoods[0]['contragent_id'], 'name' => $pendingGoods[0]['contragent']];
            $invoice = [
                'archives'=>$invoice_archives,
                'ids'=>$invoice_ids,
                'serial' => $pendingGoods[0]['serial']
            ];
            $date = $pendingGoods[0]['date'];
            $notes = $pendingGoods[0]['notes'];

            $data = [
                'subject' => $subject,
                'user' => $user,
                'contragent' => $contragent,
                'currencies' => $currencies,
                'invoice' => $invoice,
                'invoiceType' => $invoiceType,
                'date' => $date,
                'notes' => $notes,
                'pendingGoods' => $pendingGoods,
                'pendingGoodsCount' => count($pendingGoods),
                'flash' => $flash,
                'operator' => $operator,
                'current_menu' => 'store'
            ];

        } else {
            $contragents = (new ContragentModel())->getAll(['user_id' => $user['id'], 'subject_id' => $subject['id']]);
            $date = date("Y-m-d");

            $invoice = new InvoiceModel();
            $nextInvoiceNumber = $invoice->getNextInvoiceNumber(['type' => $invoiceType, 'user_id' => $user['id']]);

            $data = [
                'contragents' => $contragents,
                'currencies' => $currencies,
                'date' => $date,
                'subject' => $subject,
                'user' => $user,
                'invoiceType' => $invoiceType,
                'nextInvoiceNumber' => $nextInvoiceNumber,
                'flash' => $flash,
                'operator' => $operator,
                'current_menu' => 'store'
            ];
        }

        $page = 1;
        $limit = 20;
        if(isset($vars['page'])){
            $page = $vars['page'];
        }
        $offset = ($page-1) * $limit;

        if(array_key_exists("store_search", $_POST)){

            $data['goods'] = (new StoreModel())->getAll($_POST, $limit, $page);
            $data['search_params'] = $_POST;

        } else {

            $data['goods'] = (new StoreModel())->getAll(['user_id' => $user['id'], 'subject_id' => $subject['id']], $limit, $offset);
            $data['search_params'] = null;

        }
        $goodsCount = (new StoreModel())->getCountAll(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

        $data['paginator'] = Utils::generatePaginator($goodsCount, $limit, $page, 5);
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['summary'] = (new StoreModel())->getSummaryAll(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

        self::renderTemplate('store' . ds . 'shop.tpl', $data);

    }

    public static function add($request, $vars){


        if($request->isAjax() && $request->isPost()){

            $store = new StoreModel();
            $response = ['status' => 0, 'message' => "", 'data' => false];
            $data = $store->createStoreItem($_POST);
            if($data){
                $response = ['status' => 1, 'message' => "Əlavə olundu", 'data' => $data];
            }
            echo json_encode($response);

        } else {

            ApplicationController::pageNotFound();

        }

    }

    public static function delete($request, $vars){

        if($request->isAjax() && $request->isPost()){

            $store = new StoreModel();
            $response = ['status' => 0, 'message' => ""];
            $data = $store->deleteStoreItem($_POST);
            if($data){
                $response = ['status' => 1, 'message' => "Silindi"];
            }
            echo json_encode($response);

        } else {

            ApplicationController::pageNotFound();

        }

    }

    public static function approve($request, $vars){

        $user = RBACController::getUser();
        $subject = SubjectController::getCurrentSubject();

        if($request->isPost()){

            $storeModel = new StoreModel();

            $context['type'] = 0;
            $context['status'] = $storeModel->approve($_POST);
            $context['title'] = "Yeni malların əlavə olunması";
            if($context['status']){
                $context['message'] = "Yeni mallar əlavə olundu";
            } else {
                $context['message'] = "Yeni malları əlavə etmək mümkün olmadı";
            }

            SessionStorage::addFlash("store", $context);

            header("Location: " . Application::$settings['url'] . "/store");

        } else {

            ApplicationController::pageNotFound();

        }

    }

    public static function reject($request, $vars){

        $user = RBACController::getUser();
        $subject = SubjectController::getCurrentSubject();

        if($request->isPost()){

            $storeModel = new StoreModel();

            $context['type'] = 0;
            $context['status'] = $storeModel->reject($_POST);
            $context['title'] = "Yeni malların əlavə olunması";
            if($context['status']){
                $context['message'] = "Yeni malların əlavə olunmasından İmtina olundu";
            } else {
                $context['message'] = "İmtina etmək mümkün olmadı";
            }

            SessionStorage::addFlash("store", $context);

            header("Location: " . Application::$settings['url'] . "/store");

        } else {

            ApplicationController::pageNotFound();

        }


    }

    public static function getbybarcode($request, $vars){

        if($request->isAjax() && $request->isPost()){

            $store = new StoreModel();
            $response = ['status' => 0, 'data' => null];
            $data = $store->getAllByBarcode($_POST, true);
            if($data){
                $response = ['status' => 1, 'data' => $data];
            }
            echo json_encode($response);

        } else {

            ApplicationController::pageNotFound();

        }
    }


    /**
     * Delete goods api
     * Main prefix _DG_
     *
     * Finding all invoices containing
     * @goods_id
     * @subject_id
     * @user_id
     */

    public static function _DG_getAllInvoices($request, $vars){

        if($request->isPost() && $request->isAjax()){

            $invoiceModel = new InvoiceModel();
            $invoices = $invoiceModel->getAllForGoodsId($_POST, 1000000);
            echo json_encode($invoices);

        } else {
            ApplicationController::pageNotFound();
        }

    }

    public static function _DG_deleteGoods($request, $vars){

        if($request->isPost()){

            $storeModel = new StoreModel();

            $context['type'] = 0;
            $context['status'] = $storeModel->deleteGoods($_POST);
            $context['title'] = "Malların silinməsi";
            if($context['status']){
                $context['message'] = "Əməliyyat müvəffəqiyyətlə başa çatdı";
            } else {
                $context['message'] = "Əməliyyatı başa çatdırmaq mümkün olmadı";
            }

            SessionStorage::addFlash("store", $context);

            header("Location: " . Application::$settings['url'] . "/store");

        } else {
            ApplicationController::pageNotFound();
        }

    }

    // Delete goods

}
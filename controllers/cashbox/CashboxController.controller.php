<?php

class CashboxController extends Controller{

    public static function main($request, $vars){

        $user = RBACController::getUser('cashbox_read');
        $subject = SubjectController::getCurrentSubject();

        $page = 1;
        $limit = 20;
        if(isset($vars['page'])){
            $page = $vars['page'];
        }
        $offset = ($page-1) * $limit;

        $cashbox = (new CashboxModel())->getCurrent(['user_id' => $user['id'], 'subject_id' => $subject['id']]);
        

        $currencies = (new CurrencyModel)->getAll();

        $cashboxHistoryCount = (new CashboxModel())->getCountAll(['cashbox_id' => $cashbox['id']]);
        $cashboxHistory = (new CashboxModel())->getAll(
                ['user_id' => $user['id'], 'subject_id' => $subject['id'], 'cashbox_id' => $cashbox['id']], $limit, $offset);

        $paginator = Utils::generatePaginator($cashboxHistoryCount, $limit, $page);

        $invoiceModel = new InvoiceModel();
        $nextInvoice = [
            'income' => $invoiceModel->getNextInvoiceNumber(['user_id' => $user['id'], 'type' => 2]),
            'outgoing' => $invoiceModel->getNextInvoiceNumber(['user_id' => $user['id'], 'type' => 3]),
            'transfer' => $invoiceModel->getNextInvoiceNumber(['user_id' => $user['id'], 'type' => 4])
        ];

        $cbsOfUser = (new CashboxModel())->getAllCBForUser(['user_id' => $user['id']]);

        $operator = $user['id'];

        $flash = SessionStorage::getFlash("cashbox");
        
        self::renderTemplate("cashbox" . ds . "cashbox.tpl", [
            'user' => $user,
            'subject' => $subject,
            'cashbox' => $cashbox,
            'cashboxHistory' => $cashboxHistory,
            'currentDate' => date("Y-m-d"),
            'nextInvoice' => $nextInvoice,
            'cbsOfUser' => $cbsOfUser,
            'operator' => $operator,
            'currencies' => $currencies,
            'flash' => $flash,
            'current_menu' => 'cashbox',
            'paginator' => $paginator,
            'page' => $page,
            'limit' => $limit
        ]);

    }

    public static function income($request, $vars){

        $user = RBACController::getUser('cashbox_income');
        $subject = SubjectController::getCurrentSubject();

        if($request->isPost()){

            $cashboxModel = new CashboxModel();
            $context['type'] = 0;
            $context['status'] = $cashboxModel->processIncome($_POST);
            $context['title'] = "Mədaxil əməliyyatı";
            if($context['status']){
                $context['message'] = "Məbləğ uğurla əlavə olundu";
            } else {
                $context['message'] = "Məbləği əlavə etmək mümkün olmadı";
            }
            SessionStorage::addFlash("cashbox", $context);
            header("Location: " . Application::$settings['url'] . "/cashbox");

        } else {

            ApplicationController::pageNotFound();

        }

    }

    public static function outgoing($request, $vars){

        $user = RBACController::getUser('cashbox_outgoing');
        $subject = SubjectController::getCurrentSubject();

        if($request->isPost()){

            $cashboxModel = new CashboxModel();
            $context['type'] = 0;
            $context['status'] = $cashboxModel->processOutgoing($_POST);
            $context['title'] = "Məxaric əməliyyatı";
            if($context['status']){
                $context['message'] = "Əməliyyat müvəfəqiyyətlə başa çatdı";
            } else {
                $context['message'] = "Əməliyyatı başa çatdırmaq mümkün olmadı";
            }
            SessionStorage::addFlash("cashbox", $context);
            header("Location: " . Application::$settings['url'] . "/cashbox");

        } else {

            ApplicationController::pageNotFound();

        }

    }

    public static function transfer($request, $vars){

        $user = RBACController::getUser('cashbox_transfer');
        $subject = SubjectController::getCurrentSubject();

        if($request->isPost()){

            $cashboxModel = new CashboxModel();
            $context['type'] = 0;
            $context['status'] = $cashboxModel->processTransfer($_POST);
            $context['title'] = "Transfer əməliyyatı";
            if($context['status']){
                $context['message'] = "Əməliyyat müvəfəqiyyətlə başa çatdı";
            } else {
                $context['message'] = "Əməliyyatı başa çatdırmaq mümkün olmadı";
            }
            SessionStorage::addFlash("cashbox", $context);
            header("Location: " . Application::$settings['url'] . "/cashbox");

        } else {

            ApplicationController::pageNotFound();

        }

    }

    public static function search($request, $vars){

        $user = RBACController::getUser();
        if($request->isPost() && $request->isAjax()){

            $response['status'] = 0;
            $data = (new CashboxModel())->getSearchAll($_POST);
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
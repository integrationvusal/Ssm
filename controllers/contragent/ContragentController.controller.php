<?php

class ContragentController extends Controller{

    public static function main($request, $vars, $context = null){

        $user = RBACController::getUser('contragent_read');
        $subject = SubjectController::getCurrentSubject();

        $contragent = new ContragentModel();

       ${0} = ['user_id' => $user['id'], 'subject_id' => $subject['id']];
        
 
        try{
            $flash = SessionStorage::getFlash("contragent");
        } catch(Exception $e) {
            $flash = null;
        }

        $currencies = (new CurrencyModel)->getAllWithAZN();

        $contragents = $contragent->getAll(${0});

        $invoiceModel = new InvoiceModel();
        $invoice['type'] = 5;
        $invoice['serial'] = $invoiceModel->getNextInvoiceNumber(['user_id' => $user['id'], 'type' => $invoice['type']]);

        $cbsOfUser = (new CashboxModel())->getAllCBForUser(['user_id' => $user['id']]);

        $operator = $user['id'];

        self::renderTemplate('contragent' . ds . 'contragent.tpl', [
            'context' => $context,
            'flash' => $flash,
            'contragents' => $contragents,
            'user' => $user,
            'currencies' => $currencies,
            'currentDate' => date("Y-m-d"),
            'invoice' => $invoice,
            'cashboxes' => $cbsOfUser,
            'cashbox_id' => (new CashboxModel())->getCurrent(${0}),
            'operator' => $operator,
            'current_menu' => 'contragent'
        ]);

    }

    public static function create($request, $vars){

        $user = RBACController::getUser('contragent_create');
        $subject = SubjectController::getCurrentSubject();

        if($request->isPost()){

            $_POST['subject_id'] = $subject['id'];
            $contragent = new ContragentModel();
            $context['type'] = 0;
            $context['status'] = $contragent->createContragent($_POST);
            $context['title'] = "Yeni kontragent";
            if($context['status']){
                $context['message'] = "Yeni kontragent əlavə olundu";
            } else {
                $context['message'] = "Yeni kontragenti əlavə etmək mümkün olmadı";
            }
            SessionStorage::addFlash("contragent", $context);
            header("Location: " . Application::$settings['url'] . "/contragent");

        } else {

            ApplicationController::pageNotFound();

        }
    }

    public static function update($request, $vars){

        $user = RBACController::getUser('contragent_update');
        $subject = SubjectController::getCurrentSubject();

        $contragent = new ContragentModel();
        if($request->isPost()){

            $_POST['subject_id'] = $subject['id'];
            $context['type'] = 0;
            $context['status'] = $contragent->updateContragent($_POST);
            $context['title'] = "Redaktə et";
            if($context['status']){
                $context['message'] = "Kontragent yeniləndi";
            } else {
                $context['message'] = "Kontragenti yeniləmək mümkün olmadı";
            }
            SessionStorage::addFlash("contragent", $context);
            header("Location: " . Application::$settings['url'] . "/contragent");

        } else {

            $contragent_id = isset($vars['contragent_id']) ? $vars['contragent_id']: 0;
            $contragent = $contragent->getOne(['id' => $contragent_id, 'user_id' => $user['id'], 'subject_id' => $subject['id']]);

            if($contragent){
                $nc = explode(" ", $contragent['name']);
                $pc = explode(";", $contragent['phone']);
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
                $contragent['name'] = $names;
                $contragent['phone'] = array('prefixes' => $prefixes, 'phones' => $phones);
                $images = explode(";", trim($contragent['image'], ";"));
                if($images[0] == "") $images = array();
                $contragent['images'] = $images;
            }

            $context['type'] = 1;

            if($contragent) $context['contragent'] = $contragent;
            self::main($request, $vars, $context);
        }

    }

    public static function delete($request, $vars){

        $user = RBACController::getUser('contragent_delete');
        $subject = SubjectController::getCurrentSubject();
        if($request->isPost()){

            $contragent = new ContragentModel();

            $_POST['subject_id'] = $subject['id'];
            $response = $contragent->deleteContragent($_POST);

            $context['type'] = 0;
            $context['status'] = $response['status'];
            $context['title'] = "Kontragent sil";
            if($context['status']){
                $context['message'] = "Kontragent silindi";
            } else {
                $context['message'] = $response['message'];
                //$context['message'] = "Kontragenti silmək mümkün olmadı";
            }
            SessionStorage::addFlash("contragent", $context);
            header("Location: " . Application::$settings['url'] . "/contragent");
        } else {
            ApplicationController::pageNotFound();
        }

    }

    public static function pay($request, $vars){

        $user = RBACController::getUser('contragent_pay');
        if($request->isPost()){

            $contragent = new ContragentModel();

            $response = $contragent->payToContragent($_POST);

            $context['type'] = 0;
            $context['status'] = $response;
            $context['title'] = "Kontragentə ödəniş";
            if($context['status']){
                $context['message'] = "Ödəniş qəbul olundu";
            } else {
                $context['message'] = "Ödənişi qəbul etmək mümkün olmadı";
            }
            SessionStorage::addFlash("contragent", $context);
            header("Location: " . Application::$settings['url'] . "/contragent");

        } else {
            ApplicationController::pageNotFound();
        }

    }

    public static function search($request, $vars){

        $user = RBACController::getUser();
        $subject = SubjectController::getCurrentSubject();
        if($request->isPost() && $request->isAjax()){

            $_POST['subject_id'] = $subject['id'];
            $response['status'] = 0;
            $data = (new ContragentModel())->getSearchAll($_POST);
            if($data){
                $response['status'] = 1;
                $response['data'] = $data;
            }
            echo json_encode($response);
        } else {

            ApplicationController::pageNotFound();

        }

    }

    public static function returnTo($request, $vars){

        $user = RBACController::getUser();
        $subject = SubjectController::getCurrentSubject();
        $invoice['type'] = 10;

        $contragents = (new ContragentModel())->getAll(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

        $flash = SessionStorage::getFlash('return');

        $invoice['serial'] = (new InvoiceModel())->getNextInvoiceNumber(['type' => $invoice['type'], 'user_id' => $user['id']]);
        $invoice['date'] = date("Y-m-d");

        $data = [
            'user' => $user,
            'subject' => $subject,
            'invoice' => $invoice,
            'flash' => $flash,
            'currentDate' => date("Y-m-d"),
            'current_menu' => 'contragent/return',
            'contragents' => $contragents
        ];

        self::renderTemplate("contragent" . ds . "return.tpl", $data);

    }

    public static function returnApprove($request, $vars){

        $user = RBACController::getUser();
        $subject = SubjectController::getCurrentSubject();
        if($request->isAjax() && $request->isPost()){

            $contragentModel = new ContragentModel();
            $_POST['subject_id'] = $subject['id'];
            $response = $contragentModel->approveReturn($_POST);
            $context['type'] = 0;
            $context['status'] = $response['status'];
            $context['title'] = "Kontragentə geri";
            $context['message'] = $response['message'];

            SessionStorage::addFlash("return", $context);
            echo json_encode($response);

        } else {

            ApplicationController::pageNotFound();

        }

    }

}
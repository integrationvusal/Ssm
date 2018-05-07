<?php

class ServiceController extends Controller{

    public static function main($request, $vars){

        $user = RBACController::getUser('service_read');
        $subject = SubjectController::getCurrentSubject();
        $flash = SessionStorage::getFlash('service');

        $service = new ServiceModel();

        $services = $service->getAll(['user_id' => $user['id'], 'subject_id' => $subject['id']]);
        $currencies = (new CurrencyModel)->getAll();

        self::renderTemplate('service' . ds . 'service.tpl', [
            'user' => $user,
            'subject' => $subject,
            'flash' => $flash,
            'services' => $services,
            'currencies' => $currencies,
            'current_menu' => 'service'
        ]);

    }

    public static function create($request, $vars){

        if($request->isPost()){

            $service = new ServiceModel('service_create');
            $context['type'] = 0;
            $context['status'] = $service->createService($_POST);
            $context['title'] = "Yeni xidmət";
            if($context['status']){
                $context['message'] = "Yeni xidmət əlavə olundu";
            } else {
                $context['message'] = "Yeni xidməti əlavə etmək mümkün olmadı";
            }
            SessionStorage::addFlash("service", $context);
            header("Location: " . Application::$settings['url'] . "/service");

        } else {

            ApplicationController::pageNotFound();

        }

    }

    public static function update($request, $vars){

        $user = RBACController::getUser('service_update');
        $subject = SubjectController::getCurrentSubject();
        $serviceModel = new ServiceModel();

        if($request->isPost()){

            $context['type'] = 0;
            $context['status'] = $serviceModel->updateService($_POST);
            $context['title'] = "Redaktə et";
            if($context['status']){
                $context['message'] = "Xidmət yeniləndi";
            } else {
                $context['message'] = "Xidməti yeniləmək mümkün olmadı";
            }
            SessionStorage::addFlash("service", $context);
            header("Location: " . Application::$settings['url'] . "/service");

        } else {

            $service_id = isset($vars['service_id']) ? $vars['service_id']: 0;
            $services = $serviceModel->getAll(['user_id' => $user['id'], 'subject_id' => $subject['id']]);
            $currencies = (new CurrencyModel)->getAll();

            if($service_id){

                $service = $serviceModel->getOne([
                    'id' => $service_id,
                    'user_id' => $user['id'],
                    'subject_id' => $subject['id']
                ]);

                if(!$service){

                    ApplicationController::pageNotFound();

                }

                self::renderTemplate('service' . ds . 'service.tpl', [
                    'user' => $user,
                    'subject' => $subject,
                    'services' => $services,
                    'currencies' => $currencies,
                    'service' => $service
                ]);

            } else {

                ApplicationController::pageNotFound();

            }

        }

    }

    public static function delete($request, $vars){

        $user = RBACController::getUser('service_delete');
        if($request->isPost()){

            $service = new ServiceModel();
            $context['type'] = 0;
            $context['status'] = $service->deleteService($_POST);
            $context['title'] = "Xidməti sil";
            if($context['status'] == 1){
                $context['message'] = "Xidmət silindi";
            } else {
                $context['message'] = "Xidməti silmək mümkün olmadı";
            }
            SessionStorage::addFlash("service", $context);
            header("Location: " . Application::$settings['url'] . "/service");
        } else {
            ApplicationController::pageNotFound();
        }

    }

    public static function sellview($request, $vars){

        $user = RBACController::getUser('service_sell');
        $subject = SubjectController::getCurrentSubject();
        $flash = SessionStorage::getFlash('service_sell');

        $serviceModel = new ServiceModel();
        $services = $serviceModel->getAll(['user_id' => $user['id'], 'subject_id' => $subject['id']]);
        $currencies = (new CurrencyModel)->getAll();

        $invoice = [
            'type' => 11,
            'serial' => (new InvoiceModel())->getNextInvoiceNumber(['type' => 11, 'user_id' => $user['id']]),
        ];

        self::renderTemplate('service' . ds . 'service_sell.tpl', [
            'user' => $user,
            'subject' => $subject,
            'cashbox' => (new CashboxModel())->getCurrent(['user_id' => $user['id'], 'subject_id' => $subject['id']]),
            'flash' => $flash,
            'services' => $services,
            'invoice' => $invoice,
            'currencies' => $currencies,
            'current_menu' => 'service_sell',
            'currentDate' => date("Y-m-d"),
        ]);

    }

    public static function sellapprove($request, $vars){

        $user = RBACController::getUser('service_sell');
        $subject = SubjectController::getCurrentSubject();

        if($request->isPost()){

            $serviceModel = new ServiceModel();
            $context['type'] = 0;
            $context['status'] = $serviceModel->sellApprove($_POST);
            $context['title'] = "Xidmət satışı";
            if($context['status']){
                $context['message'] = "Əməliyyat müvəfəqiyyətlə başa çatdı";
            } else {
                $context['message'] = "Əməliyyatı başa çatdırmaq mümkün olmadı";
            }
            SessionStorage::addFlash("service_sell", $context);
            header("Location: " . Application::$settings['url'] . "/service/sell");

        } else {

            ApplicationController::pageNotFound();

        }

    }

    public static function getbyname($request, $vars){

        $user = RBACController::getUser('service_read');
        $subject = SubjectController::getCurrentSubject();

        if ($request->isAjax() && $request->isPost()) {

            $response = ['status' => 0, 'data' => null];

            $services = (new ServiceModel())->getAllByName($_POST);
            if($services) $response = ['status' => 1, 'data' => $services];

            echo json_encode($response);

        } else {

            ApplicationController::pageNotFound();

        }

    }

}
<?php

class OperatorController extends Controller{

    public static function main($request, $vars){

        $user = RBACController::getUser('operator_read');

        $flash = SessionStorage::getFlash("operator");

        $operatorModel = new OperatorModel();
        $operators = $operatorModel->getAll(['user_id' => $user['id']]);

        self::renderTemplate("operator" . ds . "operator.tpl", [
            'user' => $user,
            'flash' => $flash,
            'operators' => $operators,
            'current_menu' => "operator"
        ]);

    }

    public static function create($request, $vars){

        $user = RBACController::getUser('operator_create');
        if($request->isPost()){

            $operatorModel = new OperatorModel();
            $context['type'] = 0;
            $context['status'] = $operatorModel->createOperator($_POST);
            $context['title'] = "Yeni istifadəçı";
            if($context['status']){
                $context['message'] = "Yeni isitfadəçi əlavə olundu";
            } else {
                $context['message'] = "Yeni istifadəçini əlavə etmək mümkün olmadı";
            }
            SessionStorage::addFlash("operator", $context);
            header("Location: " . Application::$settings['url'] . "/operator");

        } else {

            ApplicationController::pageNotFound();

        }

    }

    public static function edit($request, $vars){

        $user = RBACController::getUser('operator_update');
        $operatorModel = new OperatorModel();
        if($request->isPost()){

            $context['type'] = 0;
            $context['status'] = $operatorModel->updateOperator($_POST);
            $context['title'] = "İstifadəçıni yenilə";
            if($context['status']){
                $context['message'] = "İsitfadəçi məlumatları yeniləndi";
            } else {
                $context['message'] = "İsitfadəçi məlumatlarını yeniləmək mümkün olmadı";
            }
            SessionStorage::addFlash("operator", $context);
            header("Location: " . Application::$settings['url'] . "/operator");

        } else {

            $flash = SessionStorage::getFlash("operator");

            if(isset($vars['operator_id'])) $operator_id = $vars['operator_id'];
            else {
                ApplicationController::pageNotFound();
            }

            $operators = $operatorModel->getAll(['user_id' => $user['id']]);
            $operator = $operatorModel->getOne(['user_id' => $user['id'], 'operator_id' => $operator_id]);

            self::renderTemplate("operator" . ds . "operator.tpl", [
                'user' => $user,
                'flash' => $flash,
                'operators' => $operators,
                'operator_update' => true,
                'operator' => $operator
            ]);
        }

    }

    public static function delete($request, $vars){

        $user = RBACController::getUser('operator_delete');
        $operatorModel = new OperatorModel();
        if($request->isPost()){

            $context['type'] = 0;
            $context['status'] = $operatorModel->deleteOperator($_POST);
            $context['title'] = "İstifadəçı silinməsi";
            if($context['status']){
                $context['message'] = "İstifadəçı silindi";
            } else {
                $context['message'] = "İstifadəçıni silmək mümkün olmadı";
            }
            SessionStorage::addFlash("operator", $context);
            header("Location: " . Application::$settings['url'] . "/operator");

        } else {

              ApplicationController::pageNotFound();

        }
    }

    public static function passwordchange($request, $vars){

        $user = RBACController::getUser();
        $_POST['user_id'] = $user['id'];
        $_POST['login'] = $user['login'];
        $operatorModel = new OperatorModel();
        if($request->isAjax() && $request->isPost()){

            $context['type'] = 0;
            $context['status'] = $operatorModel->changeUserPassword($_POST);
            $context['title'] = "Şifrə dəyişməsi";
            if($context['status']){
                $context['message'] = "Zəhmət olmasa sitemə yeni şifrə ilə daxil olun";
            } else {
                $context['message'] = "Şifrəni dəyişmək mümkün olmadı";
            }
            echo json_encode($context);
        } else {

            ApplicationController::pageNotFound();

        }

    }

}
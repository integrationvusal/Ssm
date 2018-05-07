<?php

class SettingController extends Controller{

    public static function formsetting($request, $vars){

        $user = RBACController::getUser('setting');
        $subject = SubjectController::getCurrentSubject();
        $flash = SessionStorage::getFlash('setting');

        self::renderTemplate("setting" . ds . "formsetting.tpl", [
            'subject' => $subject,
            'user' => $user,
            'attrs' => (new ViewSettingModel())->getProperties('form', $subject),
            'flash' => $flash,
            'current_menu' => 'formsetting'
        ]);

    }

    public static function tablesetting($request, $vars){

        $user = RBACController::getUser('setting');
        $subject = SubjectController::getCurrentSubject();
        $flash = SessionStorage::getFlash('setting');

        self::renderTemplate("setting" . ds . "tablesetting.tpl", [
            'subject' => $subject,
            'user' => $user,
            'attrs' => (new ViewSettingModel())->getProperties('table', $subject),
            'flash' => $flash,
            'current_menu' => 'tablesetting'
        ]);

    }

    public static function tablesave($request, $vars){

        if($request->isPost()){

            $viewSettingModel = new ViewSettingModel();

            $context['type'] = 0;
            $context['status'] = $viewSettingModel->saveProperties($_POST);
            $context['title'] = "Cədvəl sazlamaları";
            if($context['status']){
                $context['message'] = "Əməliyyat müvəffəqiyyətlə başa çatdı";
            } else {
                $context['message'] = "Əməliyyatı başa çatdırmaq mümkün olmadı";
            }

            SessionStorage::addFlash("setting", $context);

            header("Location: " . Application::$settings['url'] . "/settings/" . $_POST['view_type']);

        } else {
            ApplicationController::pageNotFound();
        }

    }

    public static function formsave($request, $vars){

        if($request->isPost()){

            $viewSettingModel = new ViewSettingModel();

            $context['type'] = 0;
            $context['status'] = $viewSettingModel->saveProperties($_POST);
            $context['title'] = "Forma sazlamaları";
            if($context['status']){
                $context['message'] = "Əməliyyat müvəffəqiyyətlə başa çatdı";
            } else {
                $context['message'] = "Əməliyyatı başa çatdırmaq mümkün olmadı";
            }

            SessionStorage::addFlash("setting", $context);

            header("Location: " . Application::$settings['url'] . "/settings/" . $_POST['view_type']);

        } else {
            ApplicationController::pageNotFound();
        }

    }

}
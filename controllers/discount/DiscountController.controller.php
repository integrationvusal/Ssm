<?php

class DiscountController extends Controller{

    public static function rule($request, $vars){
        $user = RBACController::getUser('discount_rule');
        $subject = SubjectController::getCurrentSubject();

        $ruleModel = new DiscountCardRuleModel();
        $rules = $ruleModel->getAll(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

        $currencies = (new CurrencyModel())->getAll();

        $rule_id = 0;
        $rule = false;
        if(isset($vars['rule_id'])){
            $rule_id = (int)$vars['rule_id'];
            if($rule_id <= 0) ApplicationController::pageNotFound();
            $rule = $ruleModel->getOne(['user_id' => $user['id'], 'subject_id' => $subject['id'], 'rule_id' => $rule_id]);
        }

        self::renderTemplate("discount" . ds . "rule.tpl", [
            'card_types' => Application::$settings['discount_card_types'],
            'yesno_trigger' => Application::$settings['yesno_trigger'],
            'user' => $user,
            'subject' => $subject,
            'currencies' => $currencies,
            'flash' => SessionStorage::getFlash("discount_rule"),
            'current_menu' => "discount_rule",
            'rules' => $rules,
            'rule' => $rule
        ]);
    }

    public static function rulecreate($request, $vars){

        if($request->isPost()){

            $ruleModel = new DiscountCardRuleModel();

            $context['type'] = 0;
            $context['status'] = $ruleModel->createDiscountCardRule($_POST);
            $context['title'] = "Bonus xüsusiyyətləri";
            if($context['status']){
                $context['message'] = "Əməliyyat müvəffəqiyyətlə başa çatdı";
            } else {
                $context['message'] = "Əməliyyatı başa çatdırmaq mümkün olmadı";
            }

            SessionStorage::addFlash("discount_rule", $context);

            header("Location: " . Application::$settings['url'] . "/discount/rule");

        } else {
            ApplicationController::pageNotFound();
        }

    }

    public static function ruleupdate($request, $vars){

        if($request->isPost()){

            $ruleModel = new DiscountCardRuleModel();

            $context['type'] = 0;
            $context['status'] = $ruleModel->updateDiscountCardRule($_POST);
            $context['title'] = "Bonus xüsusiyyətləri";
            if($context['status']){
                $context['message'] = "Əməliyyat müvəffəqiyyətlə başa çatdı";
            } else {
                $context['message'] = "Əməliyyatı başa çatdırmaq mümkün olmadı";
            }

            SessionStorage::addFlash("discount_rule", $context);

            header("Location: " . Application::$settings['url'] . "/discount/rule");

        } else {
            ApplicationController::pageNotFound();
        }

    }

    public static function ruledelete($request, $vars){

        if($request->isPost()){

            $ruleModel = new DiscountCardRuleModel();

            $context['type'] = 0;
            $context['status'] = $ruleModel->deleteDiscountCardRule($_POST);
            $context['title'] = "Bonus xüsusiyyətləri";
            if($context['status']){
                $context['message'] = "Əməliyyat müvəffəqiyyətlə başa çatdı";
            } else {
                $context['message'] = "Əməliyyatı başa çatdırmaq mümkün olmadı";
            }

            SessionStorage::addFlash("discount_rule", $context);

            header("Location: " . Application::$settings['url'] . "/discount/rule");

        } else {
            ApplicationController::pageNotFound();
        }

    }

    public static function ruleexists($request, $vars){

        if($request->isPost()){

            $ruleModel = new DiscountCardRuleModel();

            $status = $ruleModel->isExists($_POST);

            echo json_encode($status);

        } else {
            ApplicationController::pageNotFound();
        }

    }

    public static function card($request, $vars)
    {
        $user = RBACController::getUser('discount_card');
        $subject = SubjectController::getCurrentSubject();

        $limit = 20;
        $page = 1;
        if (isset($vars['page'])) $page = (int)$vars['page'];
        if ($page < 1) ApplicationController::pageNotFound();
        $offset = ($page - 1) * $limit;

        $cardModel = new DiscountCardModel();

        $search = false;
        if (array_key_exists('search', $_POST) && ($_POST['search'] == 1)) {
            $search = true;
            $cards = $cardModel->searchAll($_POST, $limit, $offset);
            $cardsCount = $cardModel->countSearchAll($_POST);
            $searchData = $_POST;
            SessionStorage::add('discount_card_search_data', $_POST);
        } elseif(array_key_exists('search', $vars) && $vars['search']){
            $search = true;
            $searchData = SessionStorage::get('discount_card_search_data');
            $cards = $cardModel->searchAll($searchData, $limit, $offset);
            $cardsCount = $cardModel->countSearchAll($searchData);
            SessionStorage::add('discount_card_search_data', $searchData);
        } else {
            $cards = $cardModel->getAll(['user_id' => $user['id'], 'subject_id' => $subject['id']], $limit, $offset);
            $cardsCount = $cardModel->getCountAll(['user_id' => $user['id'], 'subject_id' => $subject['id']]);
            $search = false;
            $searchData =  false;
        }

        $card_id = 0;
        $card = false;
        if(isset($vars['card_id'])){
            $card_id = (int)$vars['card_id'];
            if($card_id <= 0) ApplicationController::pageNotFound();
            $card = $cardModel->getOne(['user_id' => $user['id'], 'subject_id' => $subject['id'], 'card_id' => $card_id]);
        }

        $paginator = Utils::generatePaginator($cardsCount, $limit, $page);


        self::renderTemplate("discount" . ds . "card.tpl", [
            'card_types' => Application::$settings['discount_card_types'],
            'yesno_trigger' => Application::$settings['yesno_trigger'],
            'user' => $user,
            'subject' => $subject,
            'flash' => SessionStorage::getFlash("discount_card"),
            'current_menu' => "discount_card",
            'cards' => $cards,
            'card' => $card,
            'discount_rules' => (new DiscountCardRuleModel())->getAllByType(['user_id' => $user['id'], 'subject_id' => $subject['id'], 'card_type' => 'discount']),
            'bonus_rules' => (new DiscountCardRuleModel())->getAllByType(['user_id' => $user['id'], 'subject_id' => $subject['id'], 'card_type' => 'bonus']),
            'paginator' => $paginator,
            'search' => $search,
            'search_data' => $searchData
        ]);
    }

    public static function cardcreate($request, $vars){

        if($request->isPost()){

            $cardModel = new DiscountCardModel();

            $context['type'] = 0;
            $context['status'] = $cardModel->createDiscountCard($_POST);
            $context['title'] = "Bonus kart";
            if($context['status']){
                $context['message'] = "Əməliyyat müvəffəqiyyətlə başa çatdı";
            } else {
                $context['message'] = "Əməliyyatı başa çatdırmaq mümkün olmadı";
            }

            SessionStorage::addFlash("discount_card", $context);

            header("Location: " . Application::$settings['url'] . "/discount/card");

        } else {
            ApplicationController::pageNotFound();
        }

    }

    public static function cardupdate($request, $vars){

        if($request->isPost()){

            $cardModel = new DiscountCardModel();

            $context['type'] = 0;
            $context['status'] = $cardModel->updateDiscountCard($_POST);
            $context['title'] = "Bonus kart";
            if($context['status']){
                $context['message'] = "Əməliyyat müvəffəqiyyətlə başa çatdı";
            } else {
                $context['message'] = "Əməliyyatı başa çatdırmaq mümkün olmadı";
            }

            SessionStorage::addFlash("discount_card", $context);

            header("Location: " . Application::$settings['url'] . "/discount/card");

        } else {
            ApplicationController::pageNotFound();
        }

    }

    public static function carddelete($request, $vars){

        if($request->isPost()){

            $cardModel = new DiscountCardModel();

            $context['type'] = 0;
            $context['status'] = $cardModel->deleteDiscountCard($_POST);
            $context['title'] = "Bonus kart";
            if($context['status']){
                $context['message'] = "Əməliyyat müvəffəqiyyətlə başa çatdı";
            } else {
                $context['message'] = "Əməliyyatı başa çatdırmaq mümkün olmadı";
            }

            SessionStorage::addFlash("discount_card", $context);

            header("Location: " . Application::$settings['url'] . "/discount/card");

        } else {
            ApplicationController::pageNotFound();
        }

    }

    public static function cardinfo($request, $vars){

        if($request->isPost() && $request->isAjax()){

            $cardModel = new DiscountCardModel();
            echo json_encode($cardModel->getCardInfoByNumber($_POST));

        } else {
            ApplicationController::pageNotFound();
        }

    }

}
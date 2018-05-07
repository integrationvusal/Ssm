<?php

class ReceiptController extends Controller{

    public static function main($request, $vars){
        $user = RBACController::getUser('receipt_read');
        $subject = SubjectController::getCurrentSubject();

        $limit = 20;
        $page = 1;
        if(isset($vars['page'])) $page = $vars['page'];
        $offset = ($page - 1) * $limit;

        $receiptModel = new ReceiptModel();
        $receipts = $receiptModel->getAll(['user_id' => $user['id'], 'subject_id' => $subject['id']], $limit, $offset);
        $receiptsCount = $receiptModel->getCountAll(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

        $paginator = Utils::generatePaginator($receiptsCount, $limit, $page);

        self::renderTemplate("receipt" . ds . "receipt.tpl", [
            'user' => $user,
            'subject' => $subject,
            'receipts' => $receipts,
            'flash' => SessionStorage::getFlash("receipt"),
            'current_menu' => 'receipt',
            'paginator' => $paginator,
            'start_from' => $offset
        ]);
    }

    public static function attributes($request, $vars){

        $user = RBACController::getUser('receipt_attributes');
        $subject = SubjectController::getCurrentSubject();

        self::renderTemplate("receipt" . ds . "attributes.tpl", [
            'attr' => (new ReceiptAttributesModel())->get(['user_id' => $user['id'], 'subject_id' => $subject['id']]),
            'flash' => SessionStorage::getFlash("receipt_attributes"),
            'current_menu' => 'receipt_attributes'
        ]);

    }

    public static function getcontent($request, $vars) {

        if($request->isPost() && $request->isAjax()){

            $receipt_content = (new ReceiptModel())->buildReceipt($_POST);
            echo json_encode($receipt_content);

        } else {
            ApplicationController::pageNotFound();
        }

    }

    public static function create($request, $vars) {

        if($request->isPost()){

            $receiptAttributesModel = new ReceiptAttributesModel();

            $context['type'] = 0;
            $context['status'] = $receiptAttributesModel->create($_POST);
            $context['title'] = "Qəbz atributları";
            if($context['status']){
                $context['message'] = "Əməliyyat müvəffəqiyyətlə başa çatdı";
            } else {
                $context['message'] = "Əməliyyatı başa çatdırmaq mümkün olmadı";
            }

            SessionStorage::addFlash("receipt_attributes", $context);

            header("Location: " . Application::$settings['url'] . "/receipt/attributes");

        } else {
            ApplicationController::pageNotFound();
        }

    }

}
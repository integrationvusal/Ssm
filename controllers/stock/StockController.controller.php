<?php

class StockController extends Controller{

    public static function main($request, $vars){

        $user = RBACController::getUser();
        $subject = SubjectController::getCurrentSubject();
        $invoice['type'] = 8;

        $operator = $user['id'];

        $flash = SessionStorage::getFlash('stock');

        $invoice['serial'] = (new InvoiceModel())->getNextInvoiceNumber(['type' => $invoice['type'], 'user_id' => $user['id']]);
        $invoice['notes'] = "";
        $invoice['date'] = date("Y-m-d");
        $invoice['id'] = 0;

        $data = [
            'user' => $user,
            'subject' => $subject,
            'invoice' => $invoice,
            'flash' => $flash,
            'operator' => $operator,
            'currentDate' => date("Y-m-d"),
            'current_menu' => 'stock',
            'subjects' => (new SubjectModel())->getAllByType(['user_id' => $user['id'], 'goods_type' => $subject['goods_type']])
        ];

        self::renderTemplate("sell" . ds . "stock.tpl", $data);

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

    public static function approve($request, $vars){

        $user = RBACController::getUser();
        $subject = SubjectController::getCurrentSubject();
        if($request->isAjax() && $request->isPost()){

            $storeModel = new StoreModel();
            $response = $storeModel->approveTransfer($_POST);
            $context['type'] = 0;
            $context['status'] = $response['status'];
            $context['title'] = "Transfer";
            $context['message'] = $response['message'];

            SessionStorage::addFlash("stock", $context);
            echo json_encode($response);

        } else {

            ApplicationController::pageNotFound();

        }

    }

}
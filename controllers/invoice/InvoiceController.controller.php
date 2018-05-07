<?php

class InvoiceController extends Controller{

    public static function main($request, $vars){

        $user = RBACController::getUser('invoice_read');
        $subject = SubjectController::getCurrentSubject();

        $page = 1;
        $limit = 20;
        if(isset($vars['page'])){
            $page = $vars['page'];
        }
        $offset = ($page-1) * $limit;

        $invoiceModel = new InvoiceModel();

        if(isset($_POST['search_invoice']) && $_POST['search_invoice']){
            $invoicesCount = $invoiceModel->getCountSearchAll($_POST);
            $invoices = $invoiceModel->searchAll($_POST, $invoicesCount, $offset);

        } else {

            $invoices = $invoiceModel->getAll(['user_id' => $user['id'], 'subject_id' => $subject['id']], $limit, $offset);
            $invoicesCount = $invoiceModel->getCountAll(['user_id' => $user['id'], 'subject_id' => $subject['id']]);
            if(!$invoices) ApplicationController::pageNotFound();
        }

        $paginator = Utils::generatePaginator($invoicesCount, $limit, $page);

        self::renderTemplate("invoice" . ds . "invoice.tpl", [
            'user' => $user,
            'subject' => $subject,
            'invoices' => $invoices,
            'invoice_types' => Application::$settings['invoice_types'],
            'current_menu' => 'invoice',
            'paginator' => $paginator,
            'page' => $page,
            'limit' => $limit,
            'invoiceTypes' => Application::$settings['invoice_types'],
            'searchData' => isset($_POST) ? $_POST : false
        ]);

    }

    public static function getdetail($request, $vars){

        $user = RBACController::getUser('invoice_read');
        $subject = SubjectController::getCurrentSubject();

        if($request->isAjax() && $request->isPost()){

            $invoiceModel = new InvoiceModel();
            $invoiceDetail = $invoiceModel->getInvoiceDetails($_POST);
            echo json_encode($invoiceDetail);

        } else {

            ApplicationController::pageNotFound();

        }

    }

}
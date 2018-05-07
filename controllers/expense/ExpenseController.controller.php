<?php

class ExpenseController extends Controller{

    public static function main($request, $vars){

        $user = RBACController::getUser('expense_read');
        $subject = SubjectController::getCurrentSubject();
        $flash = SessionStorage::getFlash('expense');

        $expense = new ExpenseModel();

        $expenses = $expense->getAll(['user_id' => $user['id'], 'subject_id' => $subject['id']]);
        $currencies = (new CurrencyModel())->getAll();

        self::renderTemplate('expense' . ds . 'expense.tpl', [
            'user' => $user,
            'subject' => $subject,
            'flash' => $flash,
            'currencies'=>$currencies,
            'expenses' => $expenses,
            'current_menu' => 'expense'
        ]);

    }

    public static function create($request, $vars){

        if($request->isPost()){

            $expense = new ExpenseModel('expense_create');
            $context['type'] = 0;
            $context['status'] = $expense->createExpense($_POST);
            $context['title'] = "Yeni xərc";
            if($context['status']){
                $context['message'] = "Yeni xərc əlavə olundu";
            } else {
                $context['message'] = "Yeni xərci əlavə etmək mümkün olmadı";
            }
            SessionStorage::addFlash("expense", $context);
            header("Location: " . Application::$settings['url'] . "/expense");

        } else {

            ApplicationController::pageNotFound();

        }

    }

    public static function update($request, $vars){

        $user = RBACController::getUser('expense_update');
        $subject = SubjectController::getCurrentSubject();
        $expenseModel = new ExpenseModel();

        if($request->isPost()){
            $context['type'] = 0;
            $context['status'] = $expenseModel->updateExpense($_POST);
            $context['title'] = "Redaktə et";
            if($context['status']){
                $context['message'] = "Xərc yeniləndi";
            } else {
                $context['message'] = "Xərci yeniləmək mümkün olmadı";
            }
            SessionStorage::addFlash("expense", $context);
            header("Location: " . Application::$settings['url'] . "/expense");

        } else {

            $expense_id = isset($vars['expense_id']) ? $vars['expense_id']: 0;
            $expenses = $expenseModel->getAll(['user_id' => $user['id'], 'subject_id' => $subject['id']]);
            $currencies = (new CurrencyModel())->getAll();

            if($expense_id){
                $expense = $expenseModel->getOne([
                    'id' => $expense_id,
                    'user_id' => $user['id'],
                    'subject_id' => $subject['id']
                ]);

                if(!$expense)   ApplicationController::pageNotFound();

                self::renderTemplate('expense' . ds . 'expense.tpl', [
                    'user' => $user,
                    'subject' => $subject,
                    'expenses' => $expenses,
                    'currencies' => $currencies,
                    'expense' => $expense
                ]);

            } else {

                ApplicationController::pageNotFound();

            }

        }

    }

    public static function delete($request, $vars){

        $user = RBACController::getUser('expense_delete');
        if($request->isPost()){

            $expense = new ExpenseModel();
            $context['type'] = 0;
            $context['status'] = $expense->deleteExpense($_POST);
            $context['title'] = "Xərci sil";
            if($context['status'] == 1){
                $context['message'] = "Xərc silindi";
            } else {
                $context['message'] = "Xərci silmək mümkün olmadı";
            }
            SessionStorage::addFlash("expense", $context);
            header("Location: " . Application::$settings['url'] . "/expense");
        } else {
            ApplicationController::pageNotFound();
        }

    }

    public static function sellview($request, $vars){

        $user = RBACController::getUser('expense_sell');
        $subject = SubjectController::getCurrentSubject();
        $flash = SessionStorage::getFlash('expense_sell');

        $expenses = (new ExpenseModel())->getAll(['user_id' => $user['id'], 'subject_id' => $subject['id']]);
        $currencies = (new CurrencyModel())->getAll();

        $invoice = [
            'type' => 12,
            'serial' => (new InvoiceModel())->getNextInvoiceNumber(['type' => 12, 'user_id' => $user['id']]),
        ];

        self::renderTemplate('expense' . ds . 'expense_sell.tpl', [
            'user' => $user,
            'subject' => $subject,
            'cashbox' => (new CashboxModel())->getCurrent(['user_id' => $user['id'], 'subject_id' => $subject['id']]),
            'flash' => $flash,
            'expenses' => $expenses,
            'currencies' => $currencies,
            'invoice' => $invoice,
            'current_menu' => 'expense_sell',
            'currentDate' => date("Y-m-d"),
        ]);

    }

    public static function sellapprove($request, $vars){

        $user = RBACController::getUser('expense_sell');
        $subject = SubjectController::getCurrentSubject();

        if($request->isPost()){

            $expenseModel = new ExpenseModel();
            $context['type'] = 0;
            $context['status'] = $expenseModel->sellApprove($_POST);
            $context['title'] = "Xərclər";
            if($context['status']){
                $context['message'] = "Əməliyyat müvəfəqiyyətlə başa çatdı";
            } else {
                $context['message'] = "Əməliyyatı başa çatdırmaq mümkün olmadı";
            }
            SessionStorage::addFlash("expense_sell", $context);
            header("Location: " . Application::$settings['url'] . "/expense/sell");

        } else {

            ApplicationController::pageNotFound();

        }

    }

    public static function getbyname($request, $vars){

        $user = RBACController::getUser('expense_read');
        $subject = SubjectController::getCurrentSubject();

        if ($request->isAjax() && $request->isPost()) {

            $response = ['status' => 0, 'data' => null];

            $expenses = (new ExpenseModel())->getAllByName($_POST);
            if($expenses) $response = ['status' => 1, 'data' => $expenses];

            echo json_encode($response);

        } else {

            ApplicationController::pageNotFound();

        }

    }

}
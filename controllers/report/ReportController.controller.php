<?php

	class ReportController extends Controller {

		public static function contragent($request, $vars) {

			$user = RBACController::getUser('report_read');
			$subject = SubjectController::getCurrentSubject();

			$limit = 20;
			$page = 1;
			if(isset($vars['page'])) $page = (int)$vars['page'];
			$offset = ($page - 1) * $limit;

			if(isset($_POST['report_search'])){
				$_POST['subject_id'] = $subject['id'];
				$invoices = (new ReportModel())->getContragentInvoices($_POST);
			} else {
				$invoices = (new ReportModel())->getContragentInvoices(['user_id' => $user['id'], 'subject_id' => $subject['id']], $limit, $offset);
				if(!is_array($invoices)) ApplicationController::pageNotFound();
			}

			$invoiceCount = (new ReportModel())->getCountContragentInvoices(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

			$contragents = (new ContragentModel())->getAll(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

			$paginator = Utils::generatePaginator($invoiceCount, $limit, $page);

			$summary = (new ReportModel())->getContragentInvoicesSummary(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

			SessionStorage::addFlash("contragent-print", ['summary' => $summary, 'invoices' => $invoices]);

			self::renderTemplate('report' . ds . 'contragent.tpl',[
				'current_menu' => 'report_contragent',
				'summary' => $summary,
				'user' => $user,
				'subject' => $subject,
				'contragents' => $contragents,
				'invoices' => $invoices,
				'page' => $page,
				'limit' => $limit,
				'paginator' => $paginator,
				'searchData' => !empty($_POST) ? $_POST : false
			]);

		}

		public static function contragentPrint($request, $vars) {

			$data = SessionStorage::getFlash("contragent-print");

			if($data == null) ApplicationController::pageNotFound();

			self::renderTemplate('report' . ds . 'contragent-print.tpl', $data);

		}


		public static function client($request, $vars) {

			$user = RBACController::getUser('report_read');
			$subject = SubjectController::getCurrentSubject();

			$limit = 20;
			$page = 1;
			if(isset($vars['page'])) $page = (int)$vars['page'];
			$offset = ($page - 1) * $limit;

			if(isset($_POST['report_search'])){
				$_POST['subject_id'] = $subject['id'];
				$invoices = (new ReportModel())->getClientInvoices($_POST);
			} else {
				$invoices = (new ReportModel())->getClientInvoices(['user_id' => $user['id'], 'subject_id' => $subject['id']], $limit, $offset);
				if(!is_array($invoices)) ApplicationController::pageNotFound();
			}

			$invoiceCount = (new ReportModel())->getCountClientInvoices(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

			$clients = (new ClientModel())->getAll(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

			$paginator = Utils::generatePaginator($invoiceCount, $limit, $page);
			$summary = (new ReportModel())->getClientInvoicesSummary(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

			SessionStorage::addFlash("client-print", ['summary' => $summary, 'invoices' => $invoices]);

			self::renderTemplate('report' . ds . 'client.tpl',[
				'current_menu' => 'report_client',
				'summary' => $summary,
				'user' => $user,
				'subject' => $subject,
				'clients' => $clients,
				'invoices' => $invoices,
				'page' => $page,
				'limit' => $limit,
				'paginator' => $paginator,
				'searchData' => !empty($_POST) ? $_POST : false
			]);
		}


		public static function clientPrint($request, $vars) {

			$data = SessionStorage::getFlash("client-print");

			if($data == null) ApplicationController::pageNotFound();

			self::renderTemplate('report' . ds . 'client-print.tpl', $data);

		}


		public static function cashbox($request, $vars) {

			$user = RBACController::getUser('report_read');
			$subject = SubjectController::getCurrentSubject();

			$cashbox = (new CashboxModel())->getCurrent(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

			$limit = 20;
			$page = 1;
			if(isset($vars['page'])) $page = (int)$vars['page'];
			$offset = ($page - 1) * $limit;

			if(isset($_POST['report_search'])){
				$invoices = (new ReportModel())->getCashboxInvoices($_POST);
			} else {
				$invoices = (new ReportModel())->getCashboxInvoices(['user_id' => $user['id'], 'cashbox_id' => $cashbox['id']], $limit, $offset);
				if(!is_array($invoices)) ApplicationController::pageNotFound();
			}

			$invoiceCount = (new ReportModel())->getCountCashboxInvoices(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

			$cashboxes = (new CashboxModel())->getAllCashboxes(['user_id' => $user['id']]);

			$paginator = Utils::generatePaginator($invoiceCount, $limit, $page);

			SessionStorage::addFlash("cashbox-print", ['invoices' => $invoices]);

			self::renderTemplate('report' . ds . 'cashbox.tpl',[
				'current_menu' => 'report_client',
				'cashbox_id' => $cashbox['id'],
				'user' => $user,
				'subject' => $subject,
				'cashboxes' => $cashboxes,
				'invoices' => $invoices,
				'page' => $page,
				'limit' => $limit,
				'paginator' => $paginator,
				'searchData' => !empty($_POST) ? $_POST : false
			]);

		}

		public static function cashboxPrint($request, $vars) {

			$data = SessionStorage::getFlash("cashbox-print");

			if($data == null) ApplicationController::pageNotFound();

			self::renderTemplate('report' . ds . 'cashbox-print.tpl', $data);

		}

		public static function sell($request, $vars) {

			$user = RBACController::getUser('report_read');
			$subject = SubjectController::getCurrentSubject();

			$limit = 20;
			$page = 1;
			if(isset($vars['page'])) $page = (int)$vars['page'];
			$offset = ($page - 1) * $limit;

			if(isset($_POST['report_search'])){
				$invoices = (new ReportModel())->getSoldGoodsList($_POST);
			} else {
				$invoices = (new ReportModel())->getSoldGoodsList(['user_id' => $user['id'], 'subject_id' => $subject['id']], $limit, $offset);
			}

			$invoiceCount = (new ReportModel())->getCountSoldGoodsList(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

			$paginator = Utils::generatePaginator($invoiceCount, $limit, $page);

			$goods = (new SellModel())->getAllByCodeAndBarcodeAndInfo(['user_id' => $user['id'], 'subject_id' => $subject['id'], 'search_code' => ""]);

			$summary = (new ReportModel())->getSoldGoodsListSummary(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

			SessionStorage::addFlash("sell-print", ['summary' => $summary, 'invoices' => $invoices]);

			self::renderTemplate('report' . ds . 'sell.tpl',[
				'current_menu' => 'report_sell',
				'summary' => $summary,
				'subjects' => (new SubjectModel)->getAll(['user_id' => $user['id']]),
				'user' => $user,
				'subject' => $subject,
				'invoices' => $invoices,
				'page' => $page,
				'limit' => $limit,
				'paginator' => $paginator,
				'goods' => $goods,
				'searchData' => !empty($_POST) ? $_POST : false
			]);

		}

		public static function sellPrint($request, $vars) {

			$data = SessionStorage::getFlash("sell-print");

			if($data == null) ApplicationController::pageNotFound();

			self::renderTemplate('report' . ds . 'sell-print.tpl', $data);

		}

		public static function remain($request, $vars) {

			$user = RBACController::getUser('report_read');
			$subject = SubjectController::getCurrentSubject();

			$limit = 20;
			$page = 1;
			if(isset($vars['page'])) $page = (int)$vars['page'];
			$offset = ($page - 1) * $limit;

			if(isset($_POST['report_search'])){
				$invoices = (new ReportModel())->getRemainingGoodsLits($_POST);
			} else {
				$invoices = (new ReportModel())->getRemainingGoodsLits(['user_id' => $user['id'], 'subject_id' => $subject['id']], $limit, $offset);
			}

			$invoiceCount = (new ReportModel())->getCountRemainingGoods(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

			$paginator = Utils::generatePaginator($invoiceCount, $limit, $page);

			$summary = (new ReportModel())->getRemainingGoodsLitsSummary(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

			SessionStorage::addFlash("remain-print", ['summary' => $summary, 'invoices' => $invoices]);

			self::renderTemplate('report' . ds . 'remain.tpl',[
				'current_menu' => 'report_client',
				'summary' => $summary,
				'subjects' => (new SubjectModel)->getAll(['user_id' => $user['id']]),
				'user' => $user,
				'subject' => $subject,
				'invoices' => $invoices,
				'goods' => (new SellModel())->getAllByCodeAndBarcodeAndInfo(['user_id' => $user['id'], 'subject_id' => $subject['id'], 'search_code' => ""]),
				'page' => $page,
				'limit' => $limit,
				'paginator' => $paginator,
				'searchData' => !empty($_POST) ? $_POST : false
			]);

		}

		public static function remainPrint($request, $vars) {

			$data = SessionStorage::getFlash("remain-print");

			if($data == null) ApplicationController::pageNotFound();

			self::renderTemplate('report' . ds . 'remain-print.tpl', $data);

		}

		public static function serviceSell($request, $vars) {

			$user = RBACController::getUser('report_read');
			$subject = SubjectController::getCurrentSubject();

			$limit = 20;
			$page = 1;
			if(isset($vars['page'])) $page = (int)$vars['page'];
			$offset = ($page - 1) * $limit;

			if(isset($_POST['report_search'])){
				$invoices = (new ReportModel())->getSoldServicesList($_POST);
				$services = (new ServiceModel())->getAllByName(['user_id' => $_POST['user_id'], 'subject_id' => $_POST['subject_id'],
					'search_service' => '']);
			} else {
				$invoices = (new ReportModel())->getSoldServicesList(['user_id' => $user['id'], 'subject_id' => $subject['id']], $limit, $offset);
				$services = (new ServiceModel())->getAll(['user_id' => $user['id'], 'subject_id' => $subject['id']]);
			}

			$invoiceCount = (new ReportModel())->getCountSoldServicesList(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

			$paginator = Utils::generatePaginator($invoiceCount, $limit, $page);

			$summary = (new ReportModel())->getSoldServicesListSummary(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

			SessionStorage::addFlash("service-sell-print", ['summary' => $summary, 'invoices' => $invoices]);

			self::renderTemplate('report' . ds . 'service_sell.tpl',[
				'current_menu' => 'report_service',
				'summary' => $summary,
				'subjects' => (new SubjectModel)->getAll(['user_id' => $user['id']]),
				'user' => $user,
				'subject' => $subject,
				'invoices' => $invoices,
				'page' => $page,
				'limit' => $limit,
				'paginator' => $paginator,
				'services' => $services,
				'searchData' => !empty($_POST) ? $_POST : false
			]);

		}

		public static function serviceSellPrint($request, $vars) {

			$data = SessionStorage::getFlash("service-sell-print");

			if($data == null) ApplicationController::pageNotFound();

			self::renderTemplate('report' . ds . 'service-sell-print.tpl', $data);

		}



		public static function discount($request, $vars) {

			$user = RBACController::getUser('report_read');
			$subject = SubjectController::getCurrentSubject();

			$limit = 20;
			$page = 1;
			if(isset($vars['page'])) $page = (int)$vars['page'];
			$offset = ($page - 1) * $limit;

			if(isset($_POST['report_search'])){
				$history = (new ReportModel())->getDiscountCardsHistoryList($_POST);
				$history_count = (new ReportModel())->getCountDiscountCardsHistory($_POST);
			} else {
				$history = (new ReportModel())->getDiscountCardsHistoryList(['user_id' => $user['id'], 'subject_id' => $subject['id']], $limit, $offset);
				$history_count = (new ReportModel())->getCountDiscountCardsHistory(['user_id' => $user['id'], 'subject_id' => $subject['id']]);
			}

			$paginator = Utils::generatePaginator($history_count, $limit, $page);

			SessionStorage::addFlash("discount-print", ['histories' => $history, 'discount_card_types' => Application::$settings['discount_card_types']]);

			self::renderTemplate('report' . ds . 'discount.tpl',[
				'current_menu' => 'report_discount',
				'subjects' => (new SubjectModel)->getAll(['user_id' => $user['id']]),
				'user' => $user,
				'subject' => $subject,
				'page' => $page,
				'limit' => $limit,
				'histories' => $history,
				'paginator' => $paginator,
				'discount_card_types' => Application::$settings['discount_card_types'],
				'searchData' => !empty($_POST) ? $_POST : false
			]);

		}

		public static function discountPrint($request, $vars) {

			$data = SessionStorage::getFlash("discount-print");

			if($data == null) ApplicationController::pageNotFound();

			self::renderTemplate('report' . ds . 'discount-print.tpl', $data);

		}

        public static function difference($request, $vars) {

            $user = RBACController::getUser('report_read');
            $subject = SubjectController::getCurrentSubject();

            $limit = 20;
            $page = 1;
            if(isset($vars['page'])) $page = (int)$vars['page'];
            $offset = ($page - 1) * $limit;

            if(isset($_POST['report_search'])){
                $invoices = (new ReportModel())->getDifferenceGoodsList($_POST);
            } else {
                $invoices = (new ReportModel())->getDifferenceGoodsList(['user_id' => $user['id'], 'subject_id' => $subject['id']], $limit, $offset);
            }

            $invoiceCount = (new ReportModel())->getCountDifferenceGoodsList(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

            $paginator = Utils::generatePaginator($invoiceCount, $limit, $page);

            $goods = (new SellModel())->getAllByCodeAndBarcodeAndInfo(['user_id' => $user['id'], 'subject_id' => $subject['id'], 'search_code' => ""]);

            $summary = (new ReportModel())->getDifferenceGoodsListSummary(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

            SessionStorage::addFlash("difference-print", ['summary' => $summary, 'invoices' => $invoices]);

            self::renderTemplate('report' . ds . 'difference.tpl',[
                'current_menu' => 'report_difference',
                'summary' => $summary,
                'subjects' => (new SubjectModel)->getAll(['user_id' => $user['id']]),
                'user' => $user,
                'subject' => $subject,
                'invoices' => $invoices,
                'page' => $page,
                'limit' => $limit,
                'paginator' => $paginator,
                'goods' => $goods,
                'searchData' => !empty($_POST) ? $_POST : false
            ]);

        }

        public static function differencePrint($request, $vars) {

            $data = SessionStorage::getFlash("difference-print");

            if($data == null) ApplicationController::pageNotFound();

            self::renderTemplate('report' . ds . 'difference-print.tpl', $data);

        }


        public static function expenseSell($request, $vars) {

            $user = RBACController::getUser('report_read');
            $subject = SubjectController::getCurrentSubject();

            $limit = 20;
            $page = 1;
            if(isset($vars['page'])) $page = (int)$vars['page'];
            $offset = ($page - 1) * $limit;

            if(isset($_POST['report_search'])){
                $invoices = (new ReportModel())->getSoldExpensesList($_POST);
                $expenses = (new ExpenseModel())->getAllByName(['user_id' => $_POST['user_id'], 'subject_id' => $_POST['subject_id'],
                    'search_expense' => '']);
            } else {
                $invoices = (new ReportModel())->getSoldExpensesList(['user_id' => $user['id'], 'subject_id' => $subject['id']], $limit, $offset);
                $expenses = (new ExpenseModel())->getAll(['user_id' => $user['id'], 'subject_id' => $subject['id']]);
            }

            $invoiceCount = (new ReportModel())->getCountSoldExpensesList(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

            $paginator = Utils::generatePaginator($invoiceCount, $limit, $page);

            $summary = (new ReportModel())->getSoldExpensesListSummary(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

            SessionStorage::addFlash("expense-sell-print", ['summary' => $summary, 'invoices' => $invoices]);

            self::renderTemplate('report' . ds . 'expense_sell.tpl',[
                'current_menu' => 'report_expense',
                'summary' => $summary,
                'subjects' => (new SubjectModel)->getAll(['user_id' => $user['id']]),
                'user' => $user,
                'subject' => $subject,
                'invoices' => $invoices,
                'page' => $page,
                'limit' => $limit,
                'paginator' => $paginator,
                'expenses' => $expenses,
                'searchData' => !empty($_POST) ? $_POST : false
            ]);

        }

        public static function expenseSellPrint($request, $vars) {

            $data = SessionStorage::getFlash("expense-sell-print");

            if($data == null) ApplicationController::pageNotFound();

            self::renderTemplate('report' . ds . 'expense-sell-print.tpl', $data);

        }

	}

?>
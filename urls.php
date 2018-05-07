<?php
    $lang = join('|', array_keys($__app['languages']));

    $__urlPatterns = Array(
        'app' => Array(
			Array('|\/','ApplicationController','main', 'app'),

			Array('statistics\/gettop|\/','ApplicationController','getTopProducts', 'app'),

			// SSM User
			Array('user\/passwordchange|\/', 'OperatorController', 'passwordchange', 'operator'),

			// SSM Contact
			Array('contact|\/','ContactController','main', 'contact'),
			Array('contact\/process|\/','ContactController','process', 'contact'),

			// SSM Authentication
			Array('signin|\/','AuthController','signIn', 'auth'),
			Array('signout|\/','AuthController','signOut', 'auth'),

			// SSM Contragent
			Array('contragent|\/','ContragentController','main', 'contragent'),
			Array('contragent\/create|\/','ContragentController','create', 'contragent'),
			Array('contragent\/edit|\/','ContragentController','update', 'contragent'),
			Array('contragent\/edit\/(?P<contragent_id>[0-9]{1,5})','ContragentController','update', 'contragent'),
			Array('contragent\/delete|\/','ContragentController','delete', 'contragent'),
			Array('contragent\/pay|\/','ContragentController','pay', 'contragent'),
			Array('contragent\/search|\/','ContragentController','search', 'contragent'),
			Array('contragent\/return|\/','ContragentController','returnTo', 'contragent'),
			Array('contragent\/return\/approve|\/','ContragentController','returnApprove', 'contragent'),

			// SSM Clients
			Array('client|\/','ClientController', 'main', 'client'),
			Array('client\/create|\/','ClientController', 'create', 'client'),
			Array('client\/edit|\/','ClientController','update', 'client'),
			Array('client\/edit\/(?P<client_id>[0-9]{1,5})','ClientController','update', 'client'),
			Array('client\/delete|\/','ClientController','delete', 'client'),
			Array('client\/pay|\/','ClientController','pay', 'client'),
			Array('client\/search|\/','ClientController','search', 'client'),

			// SSM Goods
			Array('goods|\/','GoodsController', 'main', 'goods', ['page' => 1]),
			Array('goods\/(?P<page>[0-9]+)','GoodsController', 'main', 'goods', ['page' => 1]),
			Array('goods\/getform|\/','GoodsController', 'getForm', 'goods'),
			Array('goods\/create|\/','GoodsController', 'create', 'goods'),
			Array('goods\/bycode|\/','GoodsController', 'getbycode', 'goods'),
			Array('goods\/edit\/(?P<goods_id>[0-9]{1,5})|\/','GoodsController', 'update', 'goods', ['page' => 1]),
			Array('goods\/edit\/(?P<goods_id>[0-9]{1,5})\/(?P<page>[1-9]+)','GoodsController', 'update', 'goods', ['page' => 1]),
			Array('goods\/getinfo|\/','GoodsController', 'getInfo', 'goods'),
			Array('goods\/delete|\/','GoodsController', 'delete', 'goods'),

			// SSM Subjects
			Array('subject|\/','SubjectController','main', 'subject'),
			Array('subject\/change|\/','SubjectController','change', 'subject'),
			Array('subject\/search|\/','SubjectController','search', 'subject'),

			// SSM Categories
			Array('category|\/','CategoryController','main', 'category'),
			Array('category\/create|\/','CategoryController','create', 'category'),
			Array('category\/edit|\/','CategoryController','update', 'category'),
			Array('category\/edit\/(?P<category_id>[0-9]{1,5})','CategoryController','update', 'category'),
			Array('category\/delete|\/','CategoryController','delete', 'category'),

			// SSM Store
			Array('store|\/','StoreController','main', 'store', ['page' => 1]),
			Array('store\/(?P<page>[0-9]+)','StoreController','main', 'store', ['page' => 1]),
			Array('store\/add|\/','StoreController','add', 'store'),
			Array('store\/delete|\/','StoreController','delete', 'store'),
			Array('store\/approve|\/','StoreController','approve', 'store'),
			Array('store\/reject|\/','StoreController','reject', 'store'),
			Array('store\/getbybarcode|\/','StoreController','getbybarcode', 'store'),
			Array('store\/_DG_getAllInvoices|\/','StoreController','_DG_getAllInvoices', 'store'),
			Array('store\/_DG_deleteGoods|\/','StoreController','_DG_deleteGoods', 'store'),

			// SSM Sell
			Array('sell|\/', 'SellController', 'main', 'sell'),
			Array('sell\/search', 'SellController', 'search', 'sell'),
			Array('sell\/add', 'SellController', 'add', 'sell'),
			Array('sell\/delete', 'SellController', 'delete', 'sell'),
			Array('sell\/reject', 'SellController', 'reject', 'sell'),
			Array('sell\/approve', 'SellController', 'approve', 'sell'),
			Array('sell\/updatepriceandcount', 'SellController', 'updatepriceandcount', 'sell'),
			Array('sell\/return', 'SellController', 'returnGoods', 'sell'),
			Array('sell\/return\/search|\/', 'SellController', 'returnGoodsSearch', 'sell'),
			Array('sell\/return\/approve|\/', 'SellController', 'returnGoodsApprove', 'sell'),
			Array('sell\/searchgoods|\/', 'SellController', 'searchgoods', 'sell'),

			// SSM Stock
			Array('stock|\/', 'StockController', 'main', 'stock'),
			Array('stock\/search', 'StockController', 'search', 'stock'),
			Array('stock\/transfer\/approve|\/', 'StockController', 'approve', 'stock'),

			// SSM Cashbox
			Array('cashbox|\/', 'CashboxController', 'main', 'cashbox'),
			Array('cashbox\/(?P<page>[0-9]+)', 'CashboxController', 'main', 'cashbox'),
			Array('cashbox\/income|\/', 'CashboxController', 'income', 'cashbox'),
			Array('cashbox\/outgoing|\/', 'CashboxController', 'outgoing', 'cashbox'),
			Array('cashbox\/transfer|\/', 'CashboxController', 'transfer', 'cashbox'),
			Array('cashbox\/search|\/', 'CashboxController', 'search', 'cashbox'),

			// SSM Invoice
			Array('invoice|\/', 'InvoiceController', 'main', 'invoice'),
			Array('invoice\/(?P<page>[0-9]+)', 'InvoiceController', 'main', 'invoice'),
			Array('invoice\/getdetail|\/', 'InvoiceController', 'getdetail', 'invoice'),

			// SSM Operator
			Array('operator|\/', 'OperatorController', 'main', 'operator'),
			Array('operator\/create|\/', 'OperatorController', 'create', 'operator'),
			Array('operator\/edit|\/', 'OperatorController', 'edit', 'operator'),
			Array('operator\/edit\/(?P<operator_id>[0-9]{1,5})', 'OperatorController', 'edit', 'operator'),
			Array('operator\/delete|\/', 'OperatorController', 'delete', 'operator'),

			// SSM Permission
			Array('permission\/(?P<operator_id>[0-9]{1,5})', 'PermissionController', 'main', 'permission'),
			Array('permission\/create|\/', 'PermissionController', 'create', 'permission'),

			// SSM Report
			Array('report\/contragent|\/', 'ReportController', 'contragent', 'report', ['page' => 1]),
			Array('report\/contragent\/(?P<page>[0-9]+)', 'ReportController', 'contragent', 'report', ['page' => 1]),
			Array('report\/contragent\/print', 'ReportController', 'contragentPrint', 'report', ['page' => 1]),

			Array('report\/client|\/', 'ReportController', 'client', 'report', ['page' => 1]),
			Array('report\/client\/(?P<page>[0-9]+)', 'ReportController', 'client', 'report', ['page' => 1]),
			Array('report\/client\/print', 'ReportController', 'clientPrint', 'report', ['page' => 1]),

			Array('report\/cashbox|\/', 'ReportController', 'cashbox', 'report', ['page' => 1]),
			Array('report\/cashbox\/(?P<page>[0-9]+)', 'ReportController', 'cashbox', 'report', ['page' => 1]),
			Array('report\/cashbox\/print', 'ReportController', 'cashboxPrint', 'report', ['page' => 1]),

			Array('report\/sell|\/', 'ReportController', 'sell', 'report', ['page' => 1]),
			Array('report\/sell\/(?P<page>[0-9]+)', 'ReportController', 'sell', 'report', ['page' => 1]),
			Array('report\/sell\/print', 'ReportController', 'sellPrint', 'report', ['page' => 1]),

			Array('report\/remain|\/', 'ReportController', 'remain', 'report', ['page' => 1]),
			Array('report\/remain\/(?P<page>[0-9]+)', 'ReportController', 'remain', 'report', ['page' => 1]),
			Array('report\/remain\/print', 'ReportController', 'remainPrint', 'report', ['page' => 1]),

			Array('report\/difference|\/', 'ReportController', 'difference', 'report', ['page' => 1]),
			Array('report\/difference\/(?P<page>[0-9]+)', 'ReportController', 'difference', 'report', ['page' => 1]),
			Array('report\/difference\/print', 'ReportController', 'differencePrint', 'report', ['page' => 1]),

			Array('report\/service|\/', 'ReportController', 'serviceSell', 'report', ['page' => 1]),
			Array('report\/service\/(?P<page>[0-9]+)', 'ReportController', 'serviceSell', 'report', ['page' => 1]),
			Array('report\/service\/print', 'ReportController', 'serviceSellPrint', 'report', ['page' => 1]),

			Array('report\/expense|\/', 'ReportController', 'expenseSell', 'report', ['page' => 1]),
			Array('report\/expense\/(?P<page>[0-9]+)', 'ReportController', 'expenseSell', 'report', ['page' => 1]),
			Array('report\/expense\/print', 'ReportController', 'expenseSellPrint', 'report', ['page' => 1]),

			Array('report\/discount|\/', 'ReportController', 'discount', 'report', ['page' => 1]),
			Array('report\/discount\/(?P<page>[0-9]+)', 'ReportController', 'discount', 'report', ['page' => 1]),
			Array('report\/discount\/print', 'ReportController', 'discountPrint', 'report', ['page' => 1]),

			// SSM Service
			Array('service|\/','ServiceController','main', 'service'),
			Array('service\/create|\/','ServiceController','create', 'service'),
			Array('service\/edit|\/','ServiceController','update', 'service'),
			Array('service\/edit\/(?P<service_id>[0-9]{1,5})','ServiceController','update', 'service'),
			Array('service\/delete|\/','ServiceController','delete', 'service'),
			Array('service\/sell|\/','ServiceController','sellview', 'service'),
			Array('service\/sell\/approve|\/','ServiceController','sellapprove', 'service'),
			Array('service\/getbyname|\/','ServiceController','getbyname', 'service'),

            // SSM Expense
			Array('expense|\/','ExpenseController','main', 'expense'),
			Array('expense\/create|\/','ExpenseController','create', 'expense'),
			Array('expense\/edit|\/','ExpenseController','update', 'expense'),
			Array('expense\/edit\/(?P<expense_id>[0-9]{1,5})','ExpenseController','update', 'expense'),
			Array('expense\/delete|\/','ExpenseController','delete', 'expense'),
			Array('expense\/sell|\/','ExpenseController','sellview', 'expense'),
			Array('expense\/sell\/approve|\/','ExpenseController','sellapprove', 'expense'),
			Array('expense\/getbyname|\/','ExpenseController','getbyname', 'expense'),

			// Settings
			Array('settings\/form|\/','SettingController','formsetting', 'setting'),
			Array('settings\/form\/create|\/','SettingController','formsave', 'setting'),
			Array('settings\/table|\/','SettingController','tablesetting', 'setting'),
			Array('settings\/table\/create|\/','SettingController','tablesave', 'setting'),

			// Discount
			Array('discount\/rule|\/','DiscountController','rule', 'discount'),
			Array('discount\/rule\/create|\/','DiscountController','rulecreate', 'discount'),
			Array('discount\/rule\/update|\/','DiscountController','ruleupdate', 'discount'),
			Array('discount\/rule\/edit\/(?P<rule_id>[0-9]+)','DiscountController','rule', 'discount'),
			Array('discount\/rule\/delete|\/','DiscountController','ruledelete', 'discount'),
			Array('discount\/rule\/exists|\/','DiscountController','ruleexists', 'discount'),
			Array('discount\/card|\/','DiscountController','card', 'discount'),
			Array('discount\/card\/(?P<page>[0-9]+)','DiscountController','card', 'discount', ['page' => 1]),
			Array('discount\/card\/search|\/','DiscountController','card', 'discount'),
			Array('discount\/card\/search\/(?P<page>[0-9]+)','DiscountController','card', 'discount', ['page' => 1, 'search' => true]),
			Array('discount\/card\/create|\/','DiscountController','cardcreate', 'discount'),
			Array('discount\/card\/update|\/','DiscountController','cardupdate', 'discount'),
			Array('discount\/card\/edit\/(?P<card_id>[0-9]+)','DiscountController','card', 'discount'),
			Array('discount\/card\/delete|\/','DiscountController','carddelete', 'discount'),
			Array('discount\/card\/info|\/','DiscountController','cardinfo', 'discount'),

			// Receipt
			Array('receipt|\/', 'ReceiptController', 'main', 'receipt'),
			Array('receipt\/(?P<page>[0-9]+)','ReceiptController','main', 'receipt', ['page' => 1]),
			Array('receipt\/getcontent|\/','ReceiptController','getcontent', 'receipt'),
			Array('receipt\/attributes|\/', 'ReceiptController', 'attributes', 'receipt'),
			Array('receipt\/attributes\/create|\/', 'ReceiptController', 'create', 'receipt'),


			// SSM Manager
			Array('manager|\/', 'ManagerController', 'main', 'manager', ['page' => 1]),
			Array('manager\/(?P<page>[0-9]+)', 'ManagerController', 'main', 'manager', ['page' => 1]),
			Array('manager\/user\/create|\/', 'ManagerController', 'create', 'manager'),
			Array('manager\/user\/edit|\/', 'ManagerController', 'update', 'manager'),
			Array('manager\/user\/edit\/(?P<manager_id>[0-9]+)', 'ManagerController', 'update', 'manager'),
			Array('manager\/user\/delete|\/', 'ManagerController', 'delete', 'manager'),
			Array('manager\/user\/permission\/(?<user_id>[0-9]+)', 'ManagerController', 'permission', 'manager'),


			Array('manager\/subject\/(?P<manager_id>[0-9]+)', 'ManagerController', 'subject', 'manager'),
			Array('manager\/subject\/create|\/', 'ManagerController', 'createSubject', 'manager'),
			Array('manager\/subject\/edit|\/', 'ManagerController', 'updateSubject', 'manager'),
			Array('manager\/subject\/(?P<manager_id>[0-9]+)\/edit\/(?P<subject_id>[0-9]+)', 'ManagerController', 'updateSubject', 'manager'),



			Array('manager\/currency|\/', 'ManagerController', 'currency', 'manager'),
			Array('manager\/currency\/edit|\/', 'ManagerController', 'updateCurrency', 'manager'),
			Array('manager\/currency\/edit\/(?P<currency_id>[0-9]+)', 'ManagerController', 'updateCurrency', 'manager'),
			Array('manager\/currency\/create|\/', 'ManagerController', 'createCurrency', 'manager'),
			Array('manager\/currency\/delete|\/', 'ManagerController', 'deleteCurrency', 'manager'),

			Array('manager\/notice|\/', 'ManagerController', 'notice', 'manager'),
			Array('manager\/notice\/(?P<page>[0-9]+)', 'ManagerController', 'notice', 'manager', ['page' => 1]),
			Array('manager\/notice\/create|\/', 'ManagerController', 'createNotice', 'manager'),
			Array('manager\/notice\/edit|\/', 'ManagerController', 'updateNotice', 'manager'),
			Array('manager\/notice\/edit\/(?P<notice_id>[0-9]+)', 'ManagerController', 'updateNotice', 'manager'),
			Array('manager\/notice\/delete|\/', 'ManagerController', 'deleteNotice', 'manager'),
			Array('manager\/notice\/get|\/', 'ManagerController', 'getNotice', 'manager'),
			Array('manager\/notice\/expire|\/', 'ManagerController', 'expireNotice', 'manager'),
			Array('manager\/notice\/viewers\/(?P<notice_id>[0-9]+)', 'ManagerController', 'getWhoViewed', 'manager'),

			// search
			Array('(?P<lang>[a-z]{2})\/search\/(?P<search_text>[0-9а-яёструфхцчшщыюьъa-zəöğüçşА-ЯЁСТРУФХЦЧШЩЫЮАA-ZƏÖĞÜÇŞ,\.\s]{1,20})|\/page\/(?P<page_number>[0-9]{1,5})','SearchController','search', 'search', Array('page_number' => '0')),
			// image resizer
			Array('imageresizer\/resize\/(?P<width>[0-9]{1,4})\/(?P<height>[0-9]{1,4})\/(?P<file_path>.*+)','ImageResizer','resize', 'utils'),
			// static files
			Array('get_static\/(?P<file_type>[a-z0-9_-]{1,30})|\/|(?P<module_name>[a-z0-9_-]{1,30})','StaticController','getStatic', 'utils'),
			// page not found
            Array('.*','ApplicationController','pageNotFound', 'app'),

        ),
    );

?>
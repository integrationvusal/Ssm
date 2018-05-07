<?php

class GoodsController extends Controller {

	public static function main($request, $vars, $context = null) {

		$user = RBACController::getUser('catalog_read');
		$subject = SubjectController::getCurrentSubject();

		$model = Application::$settings['goods_types'][$subject['goods_type']];
		$table = $model['model_name']::getTableAttrs();

		$categories = (new CategoryModel())->getAll(['user_id' => $user['id'], 'goods_type' => $subject['goods_type']]);
		$currencies = (new CurrencyModel)->getAll();

		$permissions = PermissionController::getPermissions();
		$formattrs = (new ViewSettingModel())->getProperties('form', $subject);
		$tableattrs = (new ViewSettingModel())->getProperties('table', $subject);
        if($permissions['buy_price'] == 0) {
            $formattrs['buy_price']['val'] = 0;
            $tableattrs['buy_price']['val'] = 0;
        }

		$form = $model['model_name']::__form(self::class, [
			'model' => $model,
			'currencies' => $currencies,
			'countries' => Application::$settings['countries'],
			'colors' => Application::$settings['colors'],
			'categories' => $categories,
			'formattrs' => $formattrs,
		]);

		$page = 1;
		$limit = 20;
		if(isset($vars['page'])){
			$page = $vars['page'];
		}
		$offset = ($page-1) * $limit;

		$goods = new GoodsModel();
		$goodsCount = $goods->getCountAll(['user_id' => $user['id'], 'goods_type' => $subject['goods_type']]);

		if(!empty($_POST) && isset($_POST['search_goods'])){
			$goods = $goods->getSearchAll($_POST);
		} else {
			$goods = $goods->getAll(['user_id' => $user['id'], 'goods_type' => $subject['goods_type']], $limit, $offset);
		}

		$paginator = Utils::generatePaginator($goodsCount, $limit, $page);

		$colors = Application::$settings['colors'];

		$flash = SessionStorage::getFlash("goods");

		self::renderTemplate('goods' . ds . 'goods.tpl', [
			'context' => $context,
			'model' => $model,
			'flash' => $flash,
			'goods' => $goods,
			'colors' => $colors,
			'form' => $form,
			'table' => $table,
			'goodsTypes' => Application::$settings['goods_types'],
			'current_menu' => 'goods',
            'paginator' => $paginator,
            'page' => $page,
            'limit' => $limit,
			'searchData' => isset($_POST['search_goods']) ? $_POST : false,
			'tableattrs' => $tableattrs,
		]);

	}

	public static function create($request, $vars){

		$user = RBACController::getUser('catalog_create');
		$subject = SubjectController::getCurrentSubject();

		if($request->isPost()){

			$goods = new GoodsModel();
			$context['type'] = 0;
			$context['status'] = $goods->createGoods(['post' => $_POST, 'goods_type' => $subject['goods_type']]);
			$context['title'] = "Yeni malın əlavə olunması";
			if($context['status']){
				$context['message'] = "Yeni mal əlavə olundu";
			} else {
				$context['message'] = "Yeni malı əlavə etmək mümkün olmadı";
			}
			SessionStorage::addFlash("goods", $context);
			header("Location: " . Application::$settings['url'] . "/goods");

		} else {
			ApplicationController::pageNotFound();
		}

	}

	public static function update($request, $vars){

		$user = RBACController::getUser('catalog_update');
		$subject = SubjectController::getCurrentSubject();

		$goods = new GoodsModel();
		if($request->isPost()){

			$ref = $_SERVER['HTTP_REFERER'];
			$ref = explode("/", $ref);
			$page = intval(array_pop($ref));
			Logger::writeLog("Cur page: " . $page);
			if($page > 0) $page = "/" . $page; else $page = "";

			$context['type'] = 0;
			$context['status'] = $goods->updateGoods($_POST);
			$context['title'] = "Redaktə et";
			if($context['status']){
				$context['message'] = "Dəyişikliklər qəbul olundu";
			} else {
				$context['message'] = "Dəyişiklikləri yadda saxlamaq mümkün olmadı";
			}
			SessionStorage::addFlash("goods", $context);
			header("Location: " . Application::$settings['url'] . "/goods" . $page);

		} else {

			if(isset($vars['goods_id'])){

				$goodsData = $goods->getOne(['id' => $vars['goods_id'], 'user_id' => $user['id']]);

				$goodsTypes = Application::$settings['goods_types'];

				$page = 0;
				$limit = 20;
				if(isset($vars['page'])){
					$page = $vars['page'];
				}
				$offset = ($page-1) * $limit;

				$goods = new GoodsModel();
				$goodsCount = $goods->getCountAll(['user_id' => $user['id'], 'goods_type' => $subject['goods_type']]);

				$goods = $goods->getAll(['user_id' => $user['id'], 'goods_type' => $subject['goods_type']], $limit, $offset);

				$oldImages = explode(";", $goodsData['model']['image']);
				array_pop($oldImages);
				$colors = Application::$settings['colors'];

				$categories = (new CategoryModel())->getAll(['user_id' => $user['id'], 'goods_type' => $subject['goods_type']]);
				$currencies = (new CurrencyModel)->getAll();

				$paginator = Utils::generatePaginator($goodsCount, $limit, $page);

				$updateForm = $goodsTypes[$goodsData['common']['goods_type']]['model_name']::__form(self::class,[
					'model' => $goodsTypes[$goodsData['common']['goods_type']],
					'user' => $user,
					'countries' => Application::$settings['countries'],
					'goodsData' => $goodsData,
					'oldImages' => $oldImages,
					'colors' => $colors,
					'currencies'=> $currencies,
					'categories' => $categories,
					'formattrs' => (new ViewSettingModel())->getProperties('form', $subject),
				]);

				$model = Application::$settings['goods_types'][$subject['goods_type']];
				$table = $model['model_name']::getTableAttrs();

				self::renderTemplate('goods' . ds . 'goods.tpl', [
					'goodsTypes' => $goodsTypes,
					'goods' => $goods,
					'updateForm' => $updateForm,
					'colors' => $colors,
					'table' => $table,
					'current_menu' => 'goods',
					'paginator' => $paginator,
					'page' => $page,
					'limit' => $limit,
					'searchData' => false,
					'tableattrs' => (new ViewSettingModel())->getProperties('table', $subject),
				]);

			} else {

				ApplicationController::pageNotFound();

			}

		}

	}

	public static function delete($request, $vars){

		$user = RBACController::getUser('catalog_delete');
		$subject = SubjectController::getCurrentSubject();

		$goods = new GoodsModel();

		if ($request->isPost()) {

			$ref = $_SERVER['HTTP_REFERER'];
			$ref = explode("/", $ref);
			$page = intval(array_pop($ref));
			if($page > 0) $page = "/" . $page; else $page = "";

			$status = $goods->checkUsage($_POST);

			if($status['status']) {
				$context['type'] = 0;
				$context['status'] = $goods->deleteGoods($_POST);
				$context['title'] = "Silinmə";
				if($context['status']){
					$context['message'] = "Əməliyyat uğurla başa çatdi";
				} else {
					$context['message'] = "Əməliyyatı başa çatdırmaq mümkün olmadı";
				}
			} else {
				$context['type'] = 0;
				$context['status'] = 0;
				$context['title'] = "Silinmə";
				$context['message'] = $status['message'];
			}

			SessionStorage::addFlash("goods", $context);
			header("Location: " . Application::$settings['url'] . "/goods" . $page);

		} else {

			ApplicationController::pageNotFound();

		}
	}


	public static function getbycode($request, $vars)
	{
		$user = RBACController::getUser('catalog_read');
		$subject = SubjectController::getCurrentSubject();

		if ($request->isAjax() && $request->isPost()) {

			$response = ['status' => 0, 'data' => null];

			$goods = new GoodsModel();

			$goods = $goods->getByCodeAndBarcode($_POST);
			if($goods) $response = ['status' => 1, 'data' => $goods];

			echo json_encode($response);

		} else {

			ApplicationController::pageNotFound();

		}
	}

	public static function getInfo($request, $vars){

		$user = RBACController::getUser('catalog_read');
		$subject = SubjectController::getCurrentSubject();

		if ($request->isAjax() && $request->isPost()) {

			$response = ['status' => 0, 'data' => null];

			$store = new StoreModel();
			$goods = $store->getOneOf($_POST);
			if($goods) $response = ['status' => 1, 'data' => $goods];

			echo json_encode($response);

		} else {

			ApplicationController::pageNotFound();

		}

	}


}

?>
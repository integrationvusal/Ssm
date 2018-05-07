<?php
	
    class ApplicationController extends Controller {
		
		public static function before($request, $vars = Array()) {

        }
		
		public static function main($request, $vars = Array()) {

		    if($_SESSION['sell_only']){
                header("Location: " . Application::$settings['url'] . "/sell");
            }

			$user = RBACController::getUser();
			$subject = SubjectController::getCurrentSubject();

			$previousMonth = (int)date("m") - 1;
			$currentDay = date("d");
			if($previousMonth < 10) $previousMonth = '0' . $previousMonth;
			$date_from = date("Y-m-00");
			$date_to = date("Y-m-31");
			$date_from_prev = date("Y-" . $previousMonth . "-00");
			$date_to_prev = date("Y-" . $previousMonth . "-" . $currentDay);
			$date_to_current = date("Y-m-" . $currentDay);

			$statisticsModel = new StatisticsModel();
			$total_sell_amount_prev = $statisticsModel->getTotalSellAmount($user['id'], $subject['id'], $date_from_prev, $date_to_prev);
			$total_sell_amount = $statisticsModel->getTotalSellAmount($user['id'], $subject['id'], $date_from, $date_to_current);
			if($total_sell_amount_prev == 0) {
				$difference = 100;
			} else {
				$difference = round((($total_sell_amount * 100) / $total_sell_amount_prev),2) - 100;
			}

			$cashbox = (new CashboxModel())->getCurrent(['user_id' => $user['id'], 'subject_id' => $subject['id']]);

			$statistics = [
				'total_sold_goods_count' => $statisticsModel->getSoldGoodsCount($user['id'], $subject['id'], $date_from, $date_to),
				'total_sell' => $statisticsModel->getSellCount($user['id'], $subject['id'], $date_from, $date_to),
				'total_sell_amount' => $total_sell_amount,
				'difference' => round($difference, 2),
				'total_sell_monthly_data' => $statisticsModel->getTotalSellMonthlyData($user['id'], $subject['id']),
				//'total_contragent_monthly_data' => $statisticsModel->getTotalContragentMonthlyData($user['id'], $subject['id']),
				'cashbox_monthly_data' => $statisticsModel->getCashboxMonthlyData($cashbox['id']),
				'sold_goods_count_monthly' => $statisticsModel->getSoldGoodsCountMonthly($user['id'], $subject['id'])
			];

			self::renderTemplate('main' . ds . 'main.tpl', Array(
				'user' => $user,
				'csrf_key' => Application::getCSRFKey(),
				'stat' => $statistics,
				'current_menu' => 'home'
			));
        }

		public static function getTopProducts($request, $vars){

			$user = RBACController::getUser();
			$subject = SubjectController::getCurrentSubject();
			if($request->isAjax()){

				$goods = (new StatisticsModel())->getTopGoods($user['id'], $subject['id'], $subject['goods_type']);
				echo json_encode($goods);

			} else {

				self::pageNotFound();

			}

		}

        public static function pageNotFound() {
			RBACController::getUser();
            self::renderTemplate('errors' . ds . '404.tpl');
			die();
        }

		public static function forbidden() {
			RBACController::getUser();
			self::renderTemplate('errors' . ds . '403.tpl');
			die();
		}
		
		public static function getCaptcha($request, $vars) {
        	require app_root . ds . 'libs' . ds . 'kcaptcha' . ds . 'kcaptcha.php';
			
			$captcha = new KCAPTCHA();
			$_SESSION['captcha_keystring'] = $captcha->getKeyString();
        }
    }
?>
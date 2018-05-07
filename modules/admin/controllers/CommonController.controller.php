<?php

	class CommonController extends Controller {
		
		public static function getOrderCheck($request, $vars) {
			$out = Array();
			
			$orderId = $vars['order_id'];
			$order = OrdersModel::get($orderId);
			
			$out['order'] = $order;
			
			$productsId = explode(",", $order->products->value);
			
			$pC = count($productsId);
			$totalPrice = 0;
			for ($j = 1; $j < $pC - 1; $j++) {
				if (!empty($productsId[$j])) {
					$productInfo = explode(":", $productsId[$j]);
					$productId = $productInfo[0];
					$productCount = $productInfo[1];
					$product = ProductModel::get($productId);
					$product->count = $productCount;
					$totalPrice += $product->price->value * $productCount;
					$out['product'][] = $product;
				}
			}
			
			self::renderTemplate('orders' . ds . 'print.tpl', Array(
				'order' => $out
			));
		}
		
		public static function removeOrder($request, $vars) {
			$orderId = $vars['order_id'];
			OrdersModel::delete(" WHERE `id` = '{#1}'", Array($orderId));
			echo '{success: true}';
		}
		
	}

?>
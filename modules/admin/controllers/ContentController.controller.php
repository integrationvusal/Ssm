<?php
	
	class ContentController extends Controller {
		
		public static function deleteBlockTemplate($request, $vars = Array()) {
			$id = $vars['recordId'];
			BlockFieldTemplateModel::delete(" WHERE `id` = '{#1}'", Array($id));
		}
		
		public static function getBlock($request, $vars = Array()) {
			$data = ContentModel::getBlock($vars['blockId'], $_POST['lang']);
		}
		
		public static function removeBlock($request, $vars = Array()) {
			$data = ContentModel::removeBlock($vars['blockId']);
		}
		
	}
	
?>
<?php
    
	$lang = join('|', array_keys(self::$settings['languages']));
    
    $adminTitle = self::$modules['admin']['name'];
    
    $__adminUrlPatterns = Array(
        $adminTitle => Array(
            Array($adminTitle .'|\/','AdminMainApp','main'),
			Array($adminTitle .'\/view\/(?P<model_id>[0-9]{1,2})|\/page\/(?P<page>[0-9]{1,6})','AdminMainApp','view'),
			//Array($adminTitle .'\/view\/(?P<model_id>[0-9]{1,2})','AdminMainApp','view'),
			Array($adminTitle .'\/edit\/(?P<model_id>[0-9]{1,3})\/(?P<model_item_id>[0-9]{1,10})','AdminMainApp','edit'),
			Array($adminTitle .'\/add\/(?P<model_id>[0-9]{1,3})','AdminMainApp','add'),
			Array($adminTitle .'\/delete\/(?P<model_id>[0-9]{1,3})','AdminMainApp','delete'),
			Array($adminTitle .'\/logout|\/','AdminAuthController','logout'),
			
			// content and blocks
			Array($adminTitle .'\/content\/blocktemplate\/deletefield\/(?P<recordId>[0-9]{1,3})','ContentController','deleteBlockTemplate'),
			Array($adminTitle .'\/content\/block\/getblock\/(?P<blockId>[0-9]{1,3})','ContentController','getBlock'),
			Array($adminTitle .'\/content\/block\/removeblock\/(?P<blockId>[0-9]{1,3})','ContentController','removeBlock'),
			
			// structure
			Array($adminTitle .'\/structure\/view\/(?P<tree_model_id>[0-9]{1,5})','TreeViewController','view'),
			Array($adminTitle .'\/structure\/add\/(?P<tree_model_id>[0-9]{1,5})\/(?P<tree_item_id>[0-9]{1,5})','TreeViewController','add'),
			Array($adminTitle .'\/structure\/edit\/(?P<tree_model_id>[0-9]{1,5})\/(?P<tree_item_id>[0-9]{1,5})','TreeViewController','edit'),
			Array($adminTitle .'\/structure\/delete\/(?P<tree_model_id>[0-9]{1,5})\/(?P<tree_item_id>[0-9]{1,5})','TreeViewController','delete'),
			Array($adminTitle .'\/structure\/sort\/(?P<tree_model_id>[0-9]{1,5})','TreeViewController','sort'),
			
			// file manager
			Array($adminTitle . '\/filemanager','FileManagerController','main'),
			Array($adminTitle . '\/filemanager\/to','FileManagerController','toPath'),
			Array($adminTitle . '\/filemanager\/reload','FileManagerController','reload'),
			Array($adminTitle . '\/filemanager\/create_folder','FileManagerController','createFolder'),
			Array($adminTitle . '\/filemanager\/remove_item','FileManagerController','removeItem'),
			Array($adminTitle . '\/filemanager\/upload_file\/(?P<file_name>[A-Za-z0-9\.\-\_\:\/\(\)\s]+)','FileManagerController','uploadFile'),
			
			// utils
			Array($adminTitle . '\/get-page-url','UtilsController','view'),
			
			// order
			Array($adminTitle . '\/get_order_check\/(?P<order_id>[0-9]{1,3})','CommonController','getOrderCheck'),
			Array($adminTitle . '\/remove_order\/(?P<order_id>[0-9]{1,3})','CommonController','removeOrder'),
        )
    );

    self::$urlPatterns = array_merge($__adminUrlPatterns, self::$urlPatterns); 

?>
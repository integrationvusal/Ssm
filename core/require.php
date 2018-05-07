<?php
	// require classes
	require core_path . ds . "orm" . ds . "BaseModel.class.php";
    require core_path . ds . "orm" . ds . "CRUDModel.class.php";
	require core_path . ds . "orm" . ds . "Model.class.php";
	
    require core_path . ds . "orm" . ds . "fields" . ds . "Field.class.php";

	require core_path . ds . "form" . ds . "Form.class.php";
	
	require core_path . ds . "libs" . ds . "smarty" . ds . "Smarty.class.php";

	require core_path . ds . "app" . ds . "Controller.class.php";
	require core_path . ds . "app" . ds . "Application.class.php";

	require core_path . ds . "class" . ds . "Request.class.php";
	require core_path . ds . "class" . ds . "SessionStorage.class.php";
    require core_path . ds . "class" . ds . "File.class.php";
	require core_path . ds . "class" . ds . "Security.class.php";
	require core_path . ds . "class" . ds . "HighLightSQL.class.php";
	require core_path . ds . "class" . ds . "Utils.class.php";
	require core_path . ds . "class" . ds . "DB.class.php";
	require core_path . ds . "class" . ds . "Logger.class.php";


	require "interfaces" . ds . "GTDAO.interface.php";

	// require url patterns
	require app_root . ds . "urls.php";
?>
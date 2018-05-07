<?php   
	
	date_default_timezone_set('Asia/Baku');
	/*
    if (!$__app["debug"]) error_reporting(0);
    else {
        error_reporting(E_ALL);
        $time_start = microtime(true);
    }*/
	
	
	function errorHandler($errno, $errstr, $errfile, $errline) {
		echo '<div style="padding: 10px; background-color: #333; border: solid 1px #ff0000; color: #fff; font-weight: bold;">' .
            $errstr . '<br />' . $errfile . ' &ndash; ' . $errline . '</div><br />';
	}

    require_once core_path . ds . "require.php";

	function loadClasses($class) {
	Global $__app;
		$f = $__app['autoload_folders'];
		$c = count($f);
		for ($i = 0; $i < $c; $i++) {
			if (file_exists($f[$i][0] . ds . $class . '.' . $f[$i][1] . '.php')) {
				require_once $f[$i][0] . ds . $class . '.' . $f[$i][1] . '.php';
				break;
			}
		}
	}
	
	spl_autoload_register('loadClasses');
	
	Application::start();
	
    if ($__app["debug"] && !Application::$request->isAjax()) {
        $time_end = microtime(true);
        echo "<!-- Execution time : " . ($time_end - $time_start) . "-->";
        echo "<!-- Memory usage : " . memory_get_peak_usage() . " bytes -->";
        echo "<!-- Queries count : " . BaseModel::$queryCount . " -->";
    }
	
?>
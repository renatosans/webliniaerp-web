<?php
	# Enable session garbage collection with a 1% chance of
	# running on each session_start()
	ini_set('session.gc_probability', 1);
	ini_set('session.gc_divisor', 100);

	ini_set('max_execution_time', 1800);
    ini_set('session.cookie_lifetime', 28800);
    ini_set('session.cache_expire', 28800);
    ini_set('session.gc_maxlifetime', 28800);

	define('AMBIENTE','CLIENTES');
	define('PAGE', substr($_SERVER['SCRIPT_NAME'],strripos($_SERVER['SCRIPT_NAME'],'/')+1));
	
	if($_SERVER['SERVER_NAME'] == 'localhost' || strpos($_SERVER['SERVER_NAME'], "192.168.") === 0 || strpos($_SERVER['SERVER_NAME'], "120.1.") === 0){
<<<<<<< HEAD
		define('URL_API','http://'. $_SERVER['SERVER_NAME'] .'/~filipecoelho/webliniaerp-api-pagare/');
		define('URL_BASE','http://'. $_SERVER['SERVER_NAME'] .'/~filipecoelho/');
=======
		define('URL_API','http://'. $_SERVER['SERVER_NAME'] .'/~filipecoelho/webliniaerp-api/');
		define('URL_BASE','http://'. $_SERVER['SERVER_NAME'] .'/~filipecoelho/webliniaerp-web/');
>>>>>>> 154963f432da83d7facab8eaf8a956c0b14b9adf
	}else{
		define('URL_API','http://'.$_SERVER['SERVER_NAME'].'/api/');
		define('URL_BASE','http://'.$_SERVER['SERVER_NAME'].'/');	
	}

	if(isset($_GET['nickname']))
	    define('NICKNAME',$_GET['nickname']);	
	else
		define('NICKNAME','');

	if(isset($_GET['template']) && $_GET['template'] != "")
	    define('TEMPLATE',$_GET['template']);	
	else
		define('TEMPLATE',"vitrine");

?>

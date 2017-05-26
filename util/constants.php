<?php

	define('AMBIENTE','CLIENTES');
	define('PAGE', substr($_SERVER['SCRIPT_NAME'],strripos($_SERVER['SCRIPT_NAME'],'/')+1));
	
	if($_SERVER['SERVER_NAME'] == 'localhost' || strpos($_SERVER['SERVER_NAME'], "192.168.") === 0){
		define('URL_API','http://'. $_SERVER['SERVER_NAME'] .'/webliniaerp-api/');
		define('URL_BASE','http://'. $_SERVER['SERVER_NAME'] .'/webliniaerp-web/');
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
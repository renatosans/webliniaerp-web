<?php 
	@session_start();
	include_once "util/constants.php";
	include_once "util/loja/loja.php";

	$emp = validNickName(NICKNAME);

	if($emp){
		@session_start();
		$_SESSION['loja'] = $emp;
		include(TEMPLATE.".php");
	}else{
		include("loja-404.php");
	}
?>
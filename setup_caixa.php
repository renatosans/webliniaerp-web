<?php
	if(isset($_GET['exists'])){
		if(isset($_COOKIE['pth_local'])){
			header('Content-type: application/json');
			header("HTTP/1.1 200");
			echo json_encode(array('pth_local' => $_COOKIE['pth_local']));
		}
		else
			header("HTTP/1.1 404");
		die;
	}

	if(!filter_var($_POST['pth_local'], FILTER_VALIDATE_IP) === FALSE){
		setcookie('pth_local', $_POST['pth_local'] ,time()+3600*24*30*12*5);
		header("HTTP/1.1 201");
	}	
	else{
		header("HTTP/1.1 406");
	}
?>
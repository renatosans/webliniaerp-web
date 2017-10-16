<?php
	session_start();
	if(isset($_POST['acao']) && $_POST['acao'] == 'add'){
		if(isset($_POST['produto'])){
			$id  = $_POST['produto']['id_produto'];
			$_SESSION['carrinho'][$id] = $_POST['produto']; 
		}
	}

	if(isset($_POST['acao']) && $_POST['acao'] == "get_carrinho"){
		header('Content-type: application/json');
		if(isset($_SESSION['carrinho']))
			echo json_encode($_SESSION['carrinho']);
		else
			echo json_encode(array());
	}

	if(isset($_POST['acao']) && $_POST['acao'] == "del"){
		unset($_SESSION['carrinho'][$_POST['id_produto']]);
	}

	if(isset($_POST['acao']) && $_POST['acao'] == "exists"){
		if(isset($_SESSION['carrinho'][$_POST['id_produto']])){

		}else{
			header("HTTP/1.1 404");
		}
	}

	if(isset($_POST['acao']) && $_POST['acao'] == "cancelar"){
		unset($_SESSION['carrinho']);
	}


?>
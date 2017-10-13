<?php
	include_once "util/login/restrito.php";
	header('Content-type: application/json');
	echo json_encode($_SESSION['user']);
?>

<?php
	include_once 'util/constants.php';
    @session_start();
    date_default_timezone_set('America/Sao_Paulo');
	function restrito($perfis = array(),$page=null){
		if(isset($_SESSION['user']['modulos']) && is_array($_SESSION['user']['modulos']))
			$pages = getPages($_SESSION['user']['modulos']);
		else
			$pages = array();
		if(isset($_SESSION['user'])){
			if($_SESSION['user']['flg_teste'] == 1 && $_SESSION['user']['status_teste'] == false){
				include_once('dashboard_static.php');
				die;
			}
				

			if(!in_array(PAGE, $pages) && !isset($_SESSION['loja'])){
				include_once('acesso_negado.php');
				die;
			}

			if(PAGE == 'controle-mesas.php'){
				if(isset($_SESSION['user']['dispositivo'])){
					$num_serie = isset($_SESSION['user']['dispositivo']['num_serie']) ? $_SESSION['user']['dispositivo']['num_serie'] : 'null'  ;
					$num_imei = isset($_SESSION['user']['dispositivo']['num_imei']) ? $_SESSION['user']['dispositivo']['num_imei'] : 'null' ;
					$num_mac_address = isset($_SESSION['user']['dispositivo']['num_mac_address']) ? $_SESSION['user']['dispositivo']['num_mac_address'] : 'null'  ;

					$ch = curl_init();
					$url = URL_API."dispositivo/".$num_serie."/".$num_imei."/".$num_mac_address ;
					curl_setopt($ch, CURLOPT_URL,$url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$modulos  = curl_exec($ch);
					$info 	 = curl_getinfo($ch);
					curl_close ($ch);

					if($info['http_code'] != 200){
						$_SESSION['user']['flg_dispositivo'] = 0 ;
					}else
						$_SESSION['user']['flg_dispositivo'] = 1 ;

				}else{
					$_SESSION['user']['flg_dispositivo'] = 0 ;
				}
			}

		}else{
			header('location:login.php');
		}
	}

	function getPages($modulos){
		$pages = array();
		foreach ($modulos as $key => $value) {
			$pages[] = $value['url_modulo'] ;
		}
		return $pages ;
	}
?>

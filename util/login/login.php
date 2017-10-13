<?php
	include_once '../constants.php';
	
	function getPaginaPrincipal($modulos){
		$pages = array();
		foreach ($modulos as $key => $value) {
			$pages[] = $value['url_modulo'] ;
		}
		if(in_array('dashboard.php', $pages))
			return 'dashboard.php';
		elseif (in_array('produtos.php', $pages))
			return 'produtos.php';
		elseif (in_array('pdv.php', $pages))
			return 'pdv.php';
		elseif (in_array('lancamentos.php', $pages))
			return 'lancamentos.php';
		elseif (in_array('clientes.php', $pages))
			return 'clientes.php';
		elseif (in_array('vendas.php', $pages))
			return 'vendas.php';
		elseif (in_array('controle-atendimento.php', $pages))
			return 'controle-atendimento.php';
		else
			return empty($pages[0]) ? $pages[1] : $pages[0];
	}

	function validaEmpreendimentoPeriodoTeste($id_empreendimento){
		$url = URL_API.'empreendimentos?id'. $id_empreendimento;

		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$output  = curl_exec($ch);
		$info 	 = curl_getinfo($ch);
		
		curl_close ($ch);

		$saida     = array() ;
		$output    = json_decode($output,true);
		$output    = $output[0];
		$flg_teste = (int)$output['flg_teste'];
		
		if($flg_teste == 1){
			$dias_passados  = is_numeric($output['dias_passados']) && $output['dias_passados']   >= 0  ? (int)$output['dias_passados'] : false;
			$qtd_dias_teste = is_numeric($output['qtd_dias_teste']) && $output['qtd_dias_teste'] >= 0  ? (int)$output['qtd_dias_teste'] : false;
			
			if($dias_passados && $qtd_dias_teste) {
				if($dias_passados <= $qtd_dias_teste) {
					$saida['flg_teste'] 			 = 1 ;
					$saida['status_teste'] 			 = true ;
					$saida['qtd_dias_teste']        = $dias_passados ;
					$saida['dias_passados_em_teste'] = $qtd_dias_teste ;
					$saida['dta_cadastro']           = $output['dta_cadastro'];
				}
				else {
					$saida['flg_teste'] 			 = 1   ;
					$saida['status_teste'] 			 = false ;
					$saida['qtd_dias_teste']        = $dias_passados ;
					$saida['dias_passados_em_teste'] = $qtd_dias_teste ;
					$saida['dta_cadastro']           = $output['dta_cadastro'] ;
				}
			}
			else
				return false;
		}
		else {
			$saida['flg_teste'] 			 = 0   ;
			$saida['dta_cadastro']           = $output['dta_cadastro'] ;
		}

		return $saida ;
	}

	session_start();

	$url = URL_API.'logar';

	if($_SERVER['REQUEST_METHOD'] == "POST") {
		$senha = isset($_POST['senha']) ? $_POST['senha'] : "" ;
		$login = isset($_POST['login']) ? $_POST['login'] : "" ;

		if(isset($_POST['dispositivo'])){
			$_SESSION['dispositivo'] = $_POST['dispositivo'];
		}

		/*$_SESSION['dispositivo'] = array(
			'num_serie' => 'RQ1H601NWMD',
			'num_imei' => '357097074984860|357098074984868',
			'num_mac_address' => '30:CB:F8:6D:4F:29'
		);*/

		$ch = curl_init();
		$url = $url;

		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "login=".$login."&senha=".$senha."");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$output  = curl_exec($ch);
		$info 	 = curl_getinfo($ch);
		
		curl_close($ch);

		if($info['http_code'] == 200){
			$_SESSION['user_emp'] = json_decode($output);
		}

		if($info['http_code'] == 406)
			header('Content-type: application/json');

		header("HTTP/1.1 ". $info['http_code'] ."");

		echo $output;
	}
	else if(isset($_GET['id_empreendimento']) && isset($_GET['nome_empreendimento'])){
		$saida = array();

		foreach ($_SESSION['user_emp'] as $key => $value) {
			$saida[$key] = $value;
		}

		$dados_teste = validaEmpreendimentoPeriodoTeste($_GET['id_empreendimento']);

		$saida['id']   					= (int)$_GET['id'];
		$saida['id_empreendimento'] 	= (int)$_GET['id_empreendimento'];
		$saida['end_email']   			= $_GET['end_email'];
		$saida['nme_usuario']   		= $_GET['nme_usuario'];
		$saida['nome_empreendimento'] 	= $_GET['nome_empreendimento'];
		$saida['nickname'] 				= $_GET['nickname'];
		$saida['nme_logo'] 				= $_GET['nme_logo'];
		$nickname						= isset($_GET['nickname']) ? $_GET['nickname'] : null ;
		
		$ch = curl_init();
		$url = $url;

		curl_setopt($ch, CURLOPT_URL,URL_API.'usuario/'.$saida['id_empreendimento'].'/'.$saida['id']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$usuario  = curl_exec($ch);
		$usuarioInfo = curl_getinfo($ch);
		
		curl_close ($ch);

		if($usuarioInfo['http_code'] != 200){
			header("HTTP/1.1 404");
			return ;
		}

		$ch = curl_init();
		$url = $url;

		curl_setopt($ch, CURLOPT_URL,URL_API.'empreendimentos?id_usuario='.$saida['id']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$empreendimentoUsuario  = curl_exec($ch);
		$empreendimentoUsuarioInfo 	 = curl_getinfo($ch);
		
		curl_close ($ch);

		if($empreendimentoUsuarioInfo['http_code'] != 200){
			header("HTTP/1.1 404");
			return ;
		}

		$usuario = json_decode($usuario,true);
		$saida['id_perfil'] = $usuario['id_perfil'];

		$empreendimentoUsuario = json_decode($empreendimentoUsuario,true);
		$saida['empreendimento_usuario'] = $empreendimentoUsuario;

		if($dados_teste['flg_teste'] == 1){
			$saida['flg_teste']              = 1 ;
			$saida['status_teste']           = $dados_teste['status_teste'];
			$saida['qtd_dias_teste']         = $dados_teste['qtd_dias_teste'];
			$saida['dias_passados_em_teste'] = $dados_teste['dias_passados_em_teste'];
			$saida['dta_cadastro']           = $dados_teste['dta_cadastro'];
		}
		elseif ($dados_teste['flg_teste'] == 0) {
			$saida['flg_teste']              = 0 ;
			$saida['dta_cadastro']           = $dados_teste['dta_cadastro'];
		}
		else
			header("HTTP/1.1 500");

		$ch = curl_init();
		$url = $url;
		$paramsGetModulos = array("cplSql"=>" WHERE flg_permissao = 1  ORDER BY psc_menu_modulo ASC");
		curl_setopt($ch, CURLOPT_URL,URL_API.'modulos/'.$saida['id_empreendimento'].'/null/'.$saida['id'].'?'.http_build_query($paramsGetModulos));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$modulos  = curl_exec($ch);
		$info 	 = curl_getinfo($ch);
		curl_close ($ch);

		if($info['http_code'] == 200 && $usuario['flg_tipo'] == 'usuario'){ 
			$saida['modulos'] = json_decode($modulos,true);
			$saida['modulosAssociatePage'] = array();
			foreach ($saida['modulos'] as $key => $value) {
				if(!isset($saida['modulosAssociatePage'][$value['url_modulo']]))
					$saida['modulosAssociatePage'][trim($value['url_modulo'])] = array();
				$saida['modulosAssociatePage'][trim($value['url_modulo'])] = $value;
			}
			$saida['pagina_principal'] = getPaginaPrincipal($saida['modulos']);
		}
		else{
			if($usuario['flg_tipo'] == 'usuario'){
				header("HTTP/1.1 404");
				die;
			}else{
				$saida['pagina_principal'] = $nickname;
				$saida['perc_venda'] = $usuario['perc_venda'];
			}
		} 

		if($info['http_code'] == 200){
			$ch = curl_init();
			$url = $url;
			curl_setopt($ch, CURLOPT_URL,URL_API.'modulos/menu/by_user/'.$saida['id_empreendimento'].'/'.$saida['id']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$menu  = curl_exec($ch);

			$info 	 = curl_getinfo($ch);
			curl_close ($ch);

			if($info['http_code'] == 200) $saida['menu'] = json_decode($menu,true);
			else $saida['menu'] = array();
		}

		if(isset($_SESSION['dispositivo']))
			$saida['dispositivo'] = $_SESSION['dispositivo'] ;

		$_SESSION['user'] = $saida;

		header('Content-type: application/json');
		header("HTTP/1.1 200");
		echo json_encode(array("pagina_principal"=>$saida['pagina_principal']));
	}
	else
		header("HTTP/1.1 500");
?>

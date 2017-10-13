<?php
	session_start();
	//ini_set("memory_limit","120M");

	include_once('util/PHPExcel/PHPExcel.php');

	$prd_pg = 2 ;

	$id_empreendimento = $_SESSION['user']['id_empreendimento'];

	if(!( isset($_GET['offset']) && isset($_GET['limit']) && isset($_GET['pasta']) )){

		if($_SERVER['SERVER_NAME'] == 'localhost'){
			$url = 'http://localhost/webliniaerp-api/produtos/export/'.$id_empreendimento.'/0/'.$prd_pg;
		}else{
			$url = 'http://www.hageerp.com.br/api/produtos/export/'.$id_empreendimento.'/0/'.$prd_pg;		
		}

		$cURL = curl_init();
		curl_setopt($cURL, CURLOPT_URL, $url);
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER , true );
		$result = curl_exec($cURL);
		curl_close($cURL);
		$dados = json_decode($result,true);
		$paginacao_aux = $dados['paginacao'];

		foreach ($paginacao_aux as $key => $value) {
			if(is_numeric($value['index']))
				$paginacao[$value['index']] = $value;
		}

		header('Content-type: application/json');
		
		echo json_encode(array('paginacao' => $paginacao, 'pasta'=>$pasta = md5(uniqid(rand(), true)) ));
		exit;

	}else{

	$offset = $_GET['offset'];
	$limit  = $_GET['limit'];
	$pasta  = $_GET['pasta'];

	$dir   = "assets/export_excel/".$pasta."/";

	if(!is_dir($dir)){
		mkdir($dir, 0777);
		chmod($dir, 0777);
	}
	
	$objPHPExcel = new PHPExcel();
	$indexColunas = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	$repeat       = 26;
	$duplicar     = 0 ;
	$count = 1 ;

	if($_SERVER['SERVER_NAME'] == 'localhost'){
		$url = 'http://localhost/hage-api/produtos/export/'.$id_empreendimento.'/'.$offset.'/'.$limit;
	}else{
		$url = 'http://www.hageerp.com.br/api/produtos/export/'.$id_empreendimento.'/'.$offset.'/'.$limit;		
	}
	
	$cURL = curl_init();
	curl_setopt($cURL, CURLOPT_URL, $url);
	curl_setopt($cURL, CURLOPT_RETURNTRANSFER , true );
	$result = curl_exec($cURL);
	curl_close($cURL);
	$dados = json_decode($result,true);

	$export_teste[] = $dados['dados'];
	$export         = $dados['dados'];
	
	$headExport   = $export['head'];
	$corpoExport  = $export['produtos'];
	  

	$extra		  = isset($_SESSION['export']['extra']) ? $_SESSION['export']['extra'] : array() ;

	$numRowsBody  = count($corpoExport);
	$salRow       = 2 ;

	$style_num = array('font' =>
                                    array('color' =>
                                      array('rgb' => '000000'),
                                      'bold' => false,
                                    ),
                           'alignment' => array(
                                            'wrap'       => true,
                                      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                      'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                                        ),
                     );

	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Dados referentes aos produtos  até o dia '.date('d/m/Y á\s H:i:s'));
	$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');

	$objPHPExcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($style_num);


	$default_border = array(
        'style' => PHPExcel_Style_Border::BORDER_THIN,
        'color' => array('rgb'=>'000000')
    );

	$styleArray = array(
				        'borders' => array(
				                'bottom' => $default_border,
				                'left' => $default_border,
				                'top' => $default_border,
				                'right' => $default_border,
				        ),
				        'fill' => array(
				                'type' => PHPExcel_Style_Fill::FILL_SOLID,
				                'color' => array('rgb'=>'A8A8A8'),
				        ),
				        'font' => array(
				                'bold' => true,
				        'size' => '10',
				        )
				    );

	$data_export = date('d-m-Y');

	foreach ($headExport as $key => $strCel){

		$index       = ($count-1) % 26 ;
		$celRow      = 1 + $salRow;
		$cel = $duplicar > 0 ? (string) $indexColunas[$duplicar-1].$indexColunas[$index].$celRow : $indexColunas[$index].$celRow ;

		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cel, $strCel);
		$objPHPExcel->getActiveSheet()->getColumnDimension(str_replace($celRow,'', $cel))->setAutoSize(true);

		$styleArray['fill']['color']['rgb'] = 'A8A8A8';
		$objPHPExcel->getActiveSheet()->getStyle($cel)->applyFromArray($styleArray);
		$styleArray['fill']['color']['rgb'] = 'F0F1F4';

		for($cRows = 2+$salRow ; $cRows <= ($numRowsBody+$salRow+1) ; $cRows ++){
			$objPHPExcel->getActiveSheet()->getStyle(preg_replace("/[^A-Z]/",'',$cel).$cRows)->applyFromArray($styleArray);

		}

		if($count % 26 == 0)
			$duplicar ++ ;

		$count++;

	}

	$linha = 0 ;

	foreach ($corpoExport as $cliente) {
		$coluna = 0;
		foreach ($cliente as  $dados) {
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha+2+$salRow, $dados);
			$coluna ++;
		}
		$linha ++;
	}




	// Podemos renomear o nome das planilha atual, lembrando que um único arquivo pode ter várias planilhas
	$tituloPlanilha = isset($_SESSION['export']['titulo']) ? substr($_SESSION['export']['titulo'],0,27) : 'relatório' ;
	$nomePlanilha = 'relatorio_'.date('d/m/Y').'_as_'.date('H:m');		
	$objPHPExcel->getActiveSheet()->setTitle($tituloPlanilha.'...');


	if(isset($_POST['export'])){
		$_SESSION['export'] = $session_anterior ;
	}


	// Cabeçalho do arquivo para ele baixar
	//header('Content-Type: application/vnd.ms-excel');
	//header('Content-Disposition: attachment;filename="'.$nomePlanilha.'.xlsx"');
	//header('Cache-Control: max-age=0');
	// Se for o IE9, isso talvez seja necessário
	//header('Cache-Control: max-age=1');
	 
	// Acessamos o 'Writer' para poder salvar o arquivo
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	 
	// Salva diretamente no output, poderíamos mudar arqui para um nome de arquivo em um diretório ,caso não quisessemos jogar na tela
	$concat = '_'.$offset.'_('.$data_export.')';
	$objWriter->save($dir."lista_produtos$concat.xlsx"); 
	chmod($dir."lista_produtos$concat.xlsx",0777);
	//readfile('assets/export_excel/teste.xlsx');
	}

	 
?>
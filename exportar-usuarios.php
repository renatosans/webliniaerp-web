<?php
	session_start();

	include_once('util/PHPExcel/PHPExcel.php');
	
	


	$objPHPExcel = new PHPExcel();

	$indexColunas = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	$repeat       = 26;
	$duplicar     = 0 ;



	$count = 1 ;

	if($_SERVER['SERVER_NAME'] == 'localhost'){
		$url = 'http://localhost/hage-api/usuarios/export';
	}else{
		$url = 'http://www.hageerp.com.br/api/usuarios/export';		
	}


	$data = array('nome','email','perfil','tel_fixo','celular','nextel');
	$fields = "";

	foreach ($data as $key => $value) {
		$fields .= "campos[]=".$value."&";
	}

	$fields = rtrim($fields,"&");

	$cURL = curl_init();
	curl_setopt($cURL, CURLOPT_URL, $url."?".$fields);
	curl_setopt($cURL, CURLOPT_RETURNTRANSFER , true );
	$result = curl_exec($cURL);
	curl_close($cURL);
	$dados = json_decode($result,true);




	$headExport   = $dados['head'];
	$corpoExport  = $dados['usuarios'];



	$extra		  = isset($_SESSION['export']['extra']) ? $_SESSION['export']['extra'] : array() ;

	$numRowsBody  = count($corpoExport);
	$salRow       = isset($extra['saltExtra']) ? $extra['saltExtra'] : 0  ;

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

	foreach ($headExport as $key => $strCel){
		$index       = ($count-1) % 26 ;
		$celRow      = 1;
		$cel = $duplicar > 0 ? (string) $indexColunas[$duplicar-1].$indexColunas[$index].$celRow : $indexColunas[$index].$celRow ;

		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cel, $strCel);
		$objPHPExcel->getActiveSheet()->getColumnDimension(str_replace($celRow,'', $cel))->setAutoSize(true);

		$styleArray['fill']['color']['rgb'] = 'A8A8A8';
		$objPHPExcel->getActiveSheet()->getStyle($cel)->applyFromArray($styleArray);
		$styleArray['fill']['color']['rgb'] = 'F0F1F4';

		for($cRows = $salRow ; $cRows <= ($numRowsBody+1) ; $cRows ++){
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
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha+$salRow, $dados);
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
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$nomePlanilha.'.xlsx"');
	header('Cache-Control: max-age=0');
	// Se for o IE9, isso talvez seja necessário
	header('Cache-Control: max-age=1');
	 
	// Acessamos o 'Writer' para poder salvar o arquivo
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	 
	// Salva diretamente no output, poderíamos mudar arqui para um nome de arquivo em um diretório ,caso não quisessemos jogar na tela
	$objWriter->save(str_replace('.php', '.xlsx', __FILE__)); 

	readfile(str_replace('.php', '.xlsx', __FILE__));

	 
?>
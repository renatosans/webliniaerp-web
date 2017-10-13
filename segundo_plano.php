<?php
	$cont = 0 ;
	while ($cont < 10) {
		if(is_file('segundo_plano.txt')){
			$stringTXT = file_get_contents('segundo_plano.txt');
			$json = json_decode($stringTXT,true);
			if(!is_array($json)){
				$json = array() ;
			}
		}

		$json[] =  date('Y-m-d H:m:s') ;
		$json = json_encode($json);

		$fp = fopen("segundo_plano.txt", "w");	
		$escreve = fwrite($fp, $json);
		fclose($fp); 
		sleep(2);
		$cont ++ ;
	}
?>
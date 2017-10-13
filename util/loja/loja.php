<?php
	function validNickName($nickname){
		$nickname = $nickname;
		$url 	  = URL_API.'empreendimentos?nickname='.$nickname; 
		$ch 	  = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output   = curl_exec ($ch);
		$info 	  = curl_getinfo($ch);
		curl_close ($ch);
		
		$status = $info['http_code'];

		if ($status == 200) {
			$emp = json_decode($output,true);
			$emp = $emp[0];
			return $emp;
		}else{
			return false;
		}
	}
?>
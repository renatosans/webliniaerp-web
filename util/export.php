<?php

ini_set('default_charset','UTF-8');
date_default_timezone_set('America/Sao_Paulo');

function map_column_names($input) {
	global $colnames;

	return isset($colnames[$input]) ? utf8_decode($colnames[$input]) : utf8_decode($input);
}

function clean_data(&$str) {
	$str = utf8_decode($str);

	// escape tab caracters
	$str = preg_replace("/\t/", "\\t", $str);

	// escape new lines
	$str = preg_replace("/\r?\n/", "\\n", $str);

	// convert 't' and 'f' to boolean values

	if($str == 't') $str = 'TRUE';
	if($str == 'f') $str = 'FALSE';

	// force certain number/date formats to be imported as strings
	if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
		$str = "'$str";
	}

	// escape fields that include double quotes
	if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
}

function getData($context, $id_empreendimento) {
	$ch = curl_init();

	if($context === "produtos")
		$url = 'http://api.hageerp.com.br/produtos/export/'. $id_empreendimento;
	else
		$url = "http://api.hageerp.com.br/usuarios/export?tue->id_empreendimento=". $id_empreendimento;

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output  = curl_exec($ch);
	$info 	 = curl_getinfo($ch);
	curl_close ($ch);

	if($info['http_code'] == 200)
		return json_decode($output);

	return false;
}

if(!isset($_GET['c'])){
	echo "Contexto não repassado";
	die;
}

if(!isset($_GET['id_empreendimento'])){
	echo "Empreendimento não repassado";
	die;
}

$context = $_GET['c'];
$id_empreendimento = $_GET['id_empreendimento'];

$arr = getData($context, $id_empreendimento);

$colnames = (!is_array($arr->head)) ? get_object_vars($arr->head) : $arr->head;
$data = $arr->$context;

$file_name = "hageerp_". $context ."_" . date('Ymd') . ".xls";

header("Content-Disposition: attachment; filename=\"$file_name\"");
header("Content-Type: application/vdn.ms-excel; charset=utf-8");
// header("Content-Type: text/plain");

$flg = false;

foreach ($data as $row => $value) {
	$value = get_object_vars($value);
	if(!$flg) {
		// display field/column names as first row
		$first_line = array_map("map_column_names", array_keys($value));
		echo implode("\t", array_values($first_line)) . "\r\n";
		$flg = true;
	}

	array_walk($value, 'clean_data');
	echo implode("\t", array_values($value)) . "\r\n";
}

exit;

?>

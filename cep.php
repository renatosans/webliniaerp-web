<?php
 $a = file('http://economia.uol.com.br/cotacoes/cambio/dolar-comercial-estados-unidos/');
 $html = str_get_html($a)
 $ret = $html->find('body');
 var_dump($ret);
/*$dom = new DOMDocument();
$dom->loadHTML($a);
echo $dom->getElementById('row1Time')->nodeValue . "<br>";
echo $dom->getElementById('rowTitle1')->nodeValue . "<br>";
echo $dom->getElementsByTagName('a')->item(1)->nodeValue;*/
?>
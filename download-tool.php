<?php

function sanitizeString($str) {
	$str = preg_replace('/[áàãâä]/ui', 'a', $str);
	$str = preg_replace('/[éèêë]/ui', 'e', $str);
	$str = preg_replace('/[íìîï]/ui', 'i', $str);
	$str = preg_replace('/[óòõôö]/ui', 'o', $str);
	$str = preg_replace('/[úùûü]/ui', 'u', $str);
	$str = preg_replace('/[ç]/ui', 'c', $str);
	// $str = preg_replace('/[,(),;:|!"#$%&/=?~^><ªº-]/', '_', $str);
	$str = preg_replace('/[^a-z0-9]/i', '_', $str);
	$str = preg_replace('/_+/', '_', $str); // ideia do Bacco :)
	return $str;
}

if(isset($_GET) && !empty($_GET)) {
	$fields 			= $_GET["fields"];
	$input_file 		= fopen($_GET["input_filename"], "r");
	$output_format 		= $_GET["output_format"];
	$output_filename 	= fopen($_GET["output_filename"], "w");
	$table_name 		= $_GET["table_name"];

	$str_sql = "CREATE TABLE ". $table_name ." (";

	foreach ($fields as $key => $field) {
		$str_sql .= $field['field_name'] ." ". $field['field_type'];
		if($key < (count($fields)-1))
			$str_sql .= ", ";
	}

	$str_sql .= ");\n\n";
	
	fwrite($output_filename, $str_sql);
	// echo $str_sql . "<br/>";

	$count = -1;
	while(!feof($input_file)) {
		$count++;
		$line = fgets($input_file, 4096);
		if(strlen($line) === 0)
			continue;
		if($count > 0) {
			$str_sql = "INSERT INTO ". $table_name ." ( ";

			foreach ($fields as $key => $field) {
				$str_sql .= $field['field_name'];
				if($key < (count($fields)-1))
					$str_sql .= ", ";
			}

			$str_sql .=" ) VALUES ( ";

			$values = explode(";", $line);

			foreach ($values as $key => $value) {
				$value = trim($value);
				$value = str_replace("'", "", $value);
				if(isset($fields[$key])) {
					switch ($fields[$key]['field_type']) {
						case 'VARCHAR(255)':
							$new_value = "'";
							$new_value .= $value;
							$new_value .= "'";
							$value = $new_value;
							break;
						case 'INT':
							$value = (int)str_replace(array("R$ ", ",", ".", '"'), "", $value);
							break;
						case 'DOUBLE':
							$value = str_replace(array("R$ ","R$", '"'), "", $value);
							$value = (double)str_replace(",", ".", $value);
							$value = number_format($value, 2);
							$value = str_replace(",", "", $value);
							break;
					}
				}
				
				$str_sql .= $value;
				if($key < (count($values)-1))
					$str_sql .= ", ";
			}
			
			$str_sql .= " );\n";
			
			fwrite($output_filename, $str_sql);
			// echo $str_sql . "<br/>";
		}
		else
			continue;
	}

	fclose($input_file);
	fclose($output_filename);
	
	header('Content-Type: application/octet-stream');
	header("Content-Transfer-Encoding: Binary"); 
	header("Content-disposition: attachment; filename=\"" . basename($_GET["output_filename"]) . "\"");

	readfile($_GET["output_filename"]);
	unlink($_GET["output_filename"]);
}

?>
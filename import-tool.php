<!DOCTYPE html>
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
	
?>
<html>
<head>
	<meta charset="utf-8">
	<title>WebliniaERP - Importação de dados</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<link rel='stylesheet prefetch' href='bootstrap/css/bootstrap.min.css'>
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/endless.min.css">
	<link rel="stylesheet" href="css/endless-skin.css">
	<link rel="stylesheet" href="css/custom.css">
	<style type="text/css">
		.container { max-width: 2100px; margin: 0 auto; }
	</style>
</head>
<body>
	<div class="container">
		<div class="page-header clearfix">
			<h3 class="page-title">
				<div class="pull-left">
					Tabela de Produtos p/ Importação
					<br/>
					<small>WebliniaERP Master Tools</small>
				</div>
				<div class="pull-right">
					<button id="updateTableStructure" class="btn btn-info">
						<i class="fa fa-refresh"></i> Atualizar Estrutura de Dados
					</button>
					<button id="exportToExcel" class="btn btn-primary">
						<i class="fa fa-file-excel-o"></i> Baixar Planilha de Excel
					</button>
					<button id="exportToSQL" class="btn btn-success">
						<i class="fa fa-file-code-o"></i> Baixar Script SQL
					</button>
				</div>
			</h3>
		</div>

		<div class="panel Textoringel-default">
			<div class="panel-body table-responsive">
				<?php

				$lines = array();
				$pointer = fopen("produtos-mandelao.csv", "r");

				?>
				<table id="produtos" class="table table-hover table-condesed">
					<thead>
						<form method="POST">
							<?php
								$count = 0;
								while(!feof($pointer)) {
									$line = fgets($pointer, 4096);

									if(strlen($line) === 0)
										continue;
									else if($count > 0)
										$lines[] = $line;

									if($count === 0) {
										$fields = explode(";", $line);

										foreach ($fields as $key => $field) {
											$field = $field;
											$field = trim($field);
											$field = sanitizeString($field);
											$field = str_replace(".", "", $field);
											$field = str_replace("$", "s", $field);
											$field = str_replace(array(" ", "-"), "_", $field);
											$field = strtolower($field);

											$selectedValue = "";

											if(isset($_POST) && !empty($_POST))
												$selectedValue = $_POST[$key]['field_type'];
							?>
								<th>
									<?=($field)?>
									<input type="hidden" name="<?=($key)?>[field_name]" value="<?=($field)?>"/>
									<select name="<?=($key)?>[field_type]" class="form-control clearfix">
										<option value="VARCHAR(255)"
											<?php
												if($selectedValue == "VARCHAR(255)")
													echo "selected='selected'";
											?>
										>
											Texto
										</option>
										<option value="INT"
											<?php
												if($selectedValue == "INT")
													echo "selected='selected'";
											?>
										>
											Inteiro
										</option>
										<option value="DOUBLE"
											<?php
												if($selectedValue == "DOUBLE")
													echo "selected='selected'";
											?>
										>
											Número
										</option>
									</select>
								</th>
							<?php
										}
									}

									$count++;
								}
							?>
						</form>
					</thead>
					<tbody>
						<?php
							foreach ($lines as $key => $line) {
								if(strlen($line) === 0)
									continue;
						?>
						<tr>
						<?php
								$values = explode(";", $line);
								foreach ($values as $key => $value) {
									$value = utf8_encode($value);

									if(isset($_POST) && !empty($_POST)) {
										switch ($_POST[$key]['field_type']) {
											case 'VARCHAR(255)':
												$value = (string)$value;
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
											default:
												$value = $value;
												break;
										}
									}
						?>
							<td><?=($value)?></td>
						<?php
								}
						?>
						</tr>
						<?php
							}

							fclose($pointer);
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<script src="js/jquery-1.10.2.min.js"></script>
	<script src="js/table2excel.js"></script>
	<script src="js/moment/moment.min.js"></script>
	<script src="bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			function exportToExcel(tableElement, worksheetName, filename) {
				$(tableElement).table2excel({
					name: worksheetName,
					filename: filename
				});
			}

			$("button#exportToExcel").on("click", function() {
				exportToExcel('table#produtos', 'Produtos p/ Importação', moment().format('YYYY-MM-DD') +"_Produtos para Importacao");
			});

			$("button#exportToSQL").on("click", function() {
				var data = {
					fields: [],
					input_filename: "produtos-mandelao.csv",
					output_format: "sql",
					output_filename: "ordem.sql",
					table_name: "tmp_ordem"
				};
				$.each($("select"), function(i, field){
					data.fields.push({
						field_type: $("[name='"+ i +"[field_type]']").val(),
						field_name: $("[name='"+ i +"[field_name]']").val()
					});
				});

				window.location.href = "download-tool.php?"+ $.param(data);
			});

			$("button#updateTableStructure").on("click", function() {
				$("form").submit();
			});
		});
	</script>
</body>
</html>
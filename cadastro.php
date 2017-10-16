<?php
	include_once "util/login/restrito.php";
	 if(!isset($_SESSION['loja'])){
	 	header('location:'.URL_BASE.'login.php');
	 }
?>
<!DOCTYPE html>
<html lang="en" ng-app="HageERP">
  <head>
    <meta charset="utf-8">
    <title>WebliniaERP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Bootstrap core CSS -->
    <link href="<?php echo URL_BASE ?>bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- Font Awesome -->
	<link href="<?php echo URL_BASE ?>css/font-awesome.min.css" rel="stylesheet">

	<!-- Pace -->
	<link href="<?php echo URL_BASE ?>css/pace.css" rel="stylesheet">

	<!-- Endless -->
	<link href="<?php echo URL_BASE ?>css/endless.min.css" rel="stylesheet">
	<link href="<?php echo URL_BASE ?>css/endless-landing.css" rel="stylesheet">
	<link href="<?php echo URL_BASE ?>css/custom.css" rel="stylesheet">
  </head>


  <body ng-controller="CadastroController" ng-cloak>
	<div class="login-wrapper">
		<div class="text-center">
			<h2 class="fadeInUp animation-delay10" style="font-weight:bold">
				<span class="text-danger"><?php echo $_SESSION['loja']['nome_empreendimento'] ?></span>
			</h2>
	    </div>
		<div class="login-widget animation-delay1" style="width:1000px">
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-plus-circle fa-lg"></i> Cadastro
				</div>
				<div class="panel-body">
						<div class="row">
							<div class="col-sm-12">
								<div class="alert alert-sistema" style="display:none"></div>
							</div>
						</div>
						<form role="form"  ng-init="id_empreendimento = <?php echo $_SESSION['loja']['id'] ?>">
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label for="" class="control-label">Tipo de Cadastro</label>
										<div class="form-group">
											<label class="label-radio inline">
												<input ng-model="cliente.tipo_cadastro" value="pf" type="radio" class="inline-radio">
												<span class="custom-radio"></span>
												<span>Pessoa Física</span>
											</label>

											<label class="label-radio inline">
												<input ng-model="cliente.tipo_cadastro" value="pj" type="radio" class="inline-radio">
												<span class="custom-radio"></span>
												<span>Pessoa Jurídica</span>
											</label>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-5">
									<div id="nome" class="form-group">
										<label for="nome" class="control-label">Nome <span style="color:red;font-weight: bold;">*</span></label>
										<input type="text" class="form-control" id="nome" ng-model="cliente.nome">
									</div>
								</div>
								<div class="col-sm-3">
									<div id="dta_nacimento" class="form-group">
										<label class="control-label">Data de nacimento </label>
										<input class="form-control" ui-mask="99/99/9999" id="dta_nacimento" ng-model="cliente.dta_nacimento">
									</div>
								</div>
								<div class="col-sm-4">
									<div id="apelido" class="form-group">
										<label for="apelido" class="control-label">Apelido</label>
										<input type="text" class="form-control" id="apelido" ng-model="cliente.apelido">
									</div>
								</div>



							</div>

							<div class="row" ng-if="cliente.tipo_cadastro == 'pf'">
								<div class="col-sm-2">
									<div id="rg" class="form-group">
										<label class="control-label">RG</label>
										<input class="form-control" ui-mask="99.999.999-9" ng-model="cliente.rg"></input>
									</div>
								</div>

								<div class="col-sm-2">
									<div id="cpf" class="form-group">
										<label class="control-label">CPF</label>
										<input class="form-control" ui-mask="999.999.999-99" ng-model="cliente.cpf"></input>
									</div>
								</div>
							</div>

							<div class="row" ng-if="cliente.tipo_cadastro == 'pj'">
								<div class="col-lg-4">
									<div id="razao_social" class="form-group">
										<label class="control-label">Razão Social</label>
										<input class="form-control" ng-model="cliente.razao_social">
									</div>
								</div>

								<div class="col-sm-4">
									<div id="nome_fantasia" class="form-group">
										<label class="control-label">Nome Fantasia</label>
										<input class="form-control" ng-model="cliente.nome_fantasia">
									</div>
								</div>

								<div class="col-sm-2">
									<div id="cnpj" class="form-group">
										<label class="control-label">CNPJ</label>
										<input class="form-control" ui-mask="99.999.999/9999-99" ng-model="cliente.cnpj">
									</div>
								</div>

								<div class="col-sm-2">
									<div id="inscricao_estadual" class="form-group">
										<label class="control-label">I.E.</label>
										<input class="form-control" ng-model="cliente.inscricao_estadual">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-2">
									<div id="tel_fixo" class="form-group">
										<label for="" class="control-label">Telefone Fixo</label>
										<input type="text" ui-mask="(99) 99999999" class="form-control" ng-model="cliente.tel_fixo">
									</div>
								</div>

								<div class="col-sm-2">
									<div id="celular" class="form-group">
										<label for="" class="control-label">Celular</label>
										<input type="text" ui-mask="(99) 99999999?9" class="form-control" ng-model="cliente.celular">
									</div>
								</div>

								<div class="col-sm-2">
									<div id="nextel" class="form-group">
										<label for="" class="control-label">ID Nextel</label>
										<input type="text" class="form-control" ng-model="cliente.nextel">
									</div>
								</div>

								<div class="col-sm-4">
									<div id="email" class="form-group">
										<label for="" class="control-label">End. Email</label>
										<input type="text" class="form-control" ng-model="cliente.email">
									</div>
								</div>

								<div class="col-sm-2">
									<div id="email_marketing" class="form-group">
										<label for="" class="control-label">Receber Newsletter?</label>
										<div class="form-group">
											<label class="label-radio inline">
												<input ng-model="cliente.email_marketing" value="1" type="radio" class="inline-radio">
												<span class="custom-radio"></span>
												<span>Sim</span>
											</label>

											<label class="label-radio inline">
												<input ng-model="cliente.email_marketing" value="0" type="radio" class="inline-radio">
												<span class="custom-radio"></span>
												<span>Não</span>
											</label>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-2">
									<div id="cep" class="form-group">
										<label class="control-label">CEP</label>
										<input type="text" class="form-control" ui-mask="99999-999" ng-model="cliente.cep" ng-keyUp="validCep(cliente.cep)" ng-blur="validCep(cliente.cep)">
									</div>
								</div>

								<div class="col-sm-7">
									<div id="endereco" class="form-group">
										<label class="control-label">Endereço</label>
										<input type="text" class="form-control" ng-model="cliente.endereco">
									</div>
								</div>

								<div class="col-sm-1">
									<div id="numero" class="form-group">
										<label class="control-label">Número</label>
										<input id="num_logradouro" type="text" class="form-control" ng-model="cliente.numero">
									</div>
								</div>

								<div class="col-sm-2">
									<div id="bairro" class="form-group">
										<label class="control-label">Bairro</label>
										<input type="text" class="form-control" ng-model="cliente.bairro">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div id="complemento" class="form-group">
										<label class="control-label">complemento:</label>
										<input type="text" class="form-control" ng-model="cliente.end_complemento">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-4">
									<div id="ponto_referencia" class="form-group">
										<label class="control-label">Ponto Referência</label>
										<input type="text" class="form-control" ng-model="cliente.ponto_referencia">
									</div>
								</div>
								<div class="col-sm-2">
									<div id="regiao" class="form-group">
										<label class="control-label">Região</label>
										<input type="text" class="form-control" ng-model="cliente.regiao">
									</div>
								</div>

								<div class="col-sm-2">
									<div id="id_estado" class="form-group">
										<label class="control-label">Estado</label>
										<select class="form-control" readonly="readonly" ng-model="cliente.id_estado" ng-options="item.id as item.nome for item in estados" ng-change="loadCidadesByEstado()"></select>
									</div>
								</div>

								<div class="col-sm-4">
									<div id="id_cidade" class="form-group">
										<label class="control-label">Cidade</label>
										<select class="form-control" readonly="readonly" ng-model="cliente.id_cidade" ng-options="a.id as a.nome for a in cidades"></select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-2">
									<div id="indicacao" class="form-group">
										<label class="control-label">Indicação?</label>
										<div class="form-group">
											<label class="label-radio inline">
												<input ng-model="cliente.indicacao" value="1" type="radio" class="inline-radio">
												<span class="custom-radio"></span>
												<span>Sim</span>
											</label>

											<label class="label-radio inline">
												<input ng-model="cliente.indicacao" value="0" type="radio" class="inline-radio">
												<span class="custom-radio"></span>
												<span>Não</span>
											</label>
										</div>
									</div>
								</div>

								<div class="col-sm-4" ng-if="cliente.indicacao == 1">
									<div id="indicado_por_quem" class="form-group">
										<label class="control-label">Indicado por Quem?</label>
										<input type="text" class="form-control" ng-model="cliente.indicado_por_quem">
									</div>
								</div>

								<div class="col-sm-2">
									<div id="id_como_encontrou" class="form-group">
										<label class="control-label">Como nos encontrou?</label>
										<select class="form-control" ng-model="cliente.id_como_encontrou" ng-options="a.id as a.nome for a in comoencontrou"></select>
									</div>
								</div>

								<div class="col-sm-4">
									<div id="como_entrou_outros" class="form-group">
										<label class="control-label">Descreva</label>
										<input type="text" class="form-control" ng-model="cliente.como_entrou_outros">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-3">
									<div id="id_finalidade" class="form-group">
										<label class="control-label">Finalidade</label>
										<select class="form-control" ng-model="cliente.id_finalidade" ng-options="a.id as a.nome for a in finalidades"></select>
									</div>
								</div>

								<div class="col-sm-2">
									<div id="id_banco" class="form-group">
										<label class="control-label">Banco</label>
										<select class="form-control" ng-model="cliente.id_banco" ng-options="a.id as a.nome for a in bancos"></select>
									</div>
								</div>

								<div class="col-sm-2">
									<div id="agencia" class="form-group">
										<label class="control-label">Agência</label>
										<input type="text" class="form-control" ng-model="cliente.agencia">
									</div>
								</div>

								<div class="col-sm-2">
									<div id="conta" class="form-group">
										<label class="control-label">C/C</label>
										<input type="text" class="form-control" ng-model="cliente.conta">
									</div>
								</div>

								<div class="col-sm-3">
									<div id="cliente_desde" class="form-group">
										<label for="cliente_desde" class="control-label">Cliente Bancário desde</label>
										<input type="text" class="form-control" ui-mask="99/9999" ng-model="cliente.cliente_desde">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<div class="pull-right">
											<button ng-click="showBoxNovo(); reset();" type="submit" class="btn btn-danger btn-sm">
												<i class="fa fa-times-circle"></i> Cancelar
											</button>
											<button data-loading-text=" Aguarde..." id="btn-salvar" ng-click="salvar()" type="submit" class="btn btn-success btn-sm">
												<i class="fa fa-save"></i> Salvar
											</button>
										</div>
									</div>
								</div>
							</div>
						</form>

				</div>
			</div><!-- /panel -->
		</div><!-- /login-widget -->
	</div><!-- /login-wrapper -->

	<!-- /Modal load CEP-->
		<div class="modal fade" id="busca-cep" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
    				<div class="modal-header">
						<h4>Aguarde</h4>
      				</div>

				    <div class="modal-body">
				   		<strong>buscando CEP ...</strong>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

	<a href="<?php echo URL_BASE ?>" id="scroll-to-top" class="hidden-print"><i class="fa fa-chevron-up"></i></a>

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->

	<!-- Jquery -->
	<script src="<?php echo URL_BASE ?>js/jquery-1.10.2.min.js"></script>

	<!-- Bootstrap -->
    <script src="<?php echo URL_BASE ?>bootstrap/js/bootstrap.min.js"></script>

	<!-- Waypoint -->
	<script src='<?php echo URL_BASE ?>js/waypoints.min.js'></script>

	<!-- LocalScroll -->
	<script src='<?php echo URL_BASE ?>js/jquery.localscroll.min.js'></script>

	<!-- ScrollTo -->
	<script src='<?php echo URL_BASE ?>js/jquery.scrollTo.min.js'></script>

	<!-- Modernizr -->
	<script src='<?php echo URL_BASE ?>js/modernizr.min.js'></script>

	<!-- Pace -->
	<script src='<?php echo URL_BASE ?>js/pace.min.js'></script>

	<!-- Popup Overlay -->
	<script src='<?php echo URL_BASE ?>js/jquery.popupoverlay.min.js'></script>

	<!-- Slimscroll -->
	<script src='<?php echo URL_BASE ?>js/jquery.slimscroll.min.js'></script>

	<!-- Cookie -->
	<script src='<?php echo URL_BASE ?>js/jquery.cookie.min.js'></script>

	<!-- Endless -->
	<script src="<?php echo URL_BASE ?>js/endless/endless.js"></script>

	<script>
		// $(function()	{
		// 	$('.animated-element').waypoint(function() {

		// 		$(this).removeClass('no-animation');

		// 	}, { offset: '70%' });

		// 	$('.nav').localScroll({duration:800});
		// });
	</script>

	<!-- Extras -->
	<script src="<?php echo URL_BASE ?>js/constants.js"></script>
	<script src="<?php echo URL_BASE ?>js/extras.js"></script>

	<!-- Google Maps API -->
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>

	<!-- AngularJS -->
	<script type="text/javascript" src="<?php echo URL_BASE ?>bower_components/angular/angular.js"></script>
	<script src="js/angular-strap.min.js"></script>
	<script src="js/angular-strap.tpl.min.js"></script>
	<script type="text/javascript" src="<?php echo URL_BASE ?>bower_components/angular-ui-utils/mask.min.js"></script>
    <script src="<?php echo URL_BASE ?>js/angular-sanitize.min.js"></script>
    <script src="<?php echo URL_BASE ?>js/ui-bootstrap-tpls-0.6.0.js" type="text/javascript"></script>
    <script src="<?php echo URL_BASE ?>js/dialogs.v2.min.js" type="text/javascript"></script>
    <script src="<?php echo URL_BASE ?>js/auto-complete/ng-sanitize.js"></script>
    <script src="<?php echo URL_BASE ?>js/app.js"></script>
    <script src="<?php echo URL_BASE ?>js/auto-complete/AutoComplete.js"></script>
    <script src="<?php echo URL_BASE ?>js/angular-services/user-service.js"></script>
	<script src="<?php echo URL_BASE ?>js/angular-controller/cadastro-controller.js"></script>
	<script type="text/javascript">
		//$(".chzn-select").chosen();
		$('.foto-produto').change(function()	{
			var filename = $(this).val().split('\\').pop();
			$(this).parent().find('span').attr('data-title',filename);
			$(this).parent().find('label').attr('data-title','Trocar foto');
			$(this).parent().find('label').addClass('selected');
		});
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

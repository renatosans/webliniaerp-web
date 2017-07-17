<?php
	include_once "util/login/restrito.php";
	restrito(array(1));
?>
<!DOCTYPE html>
<html lang="en" ng-app="HageERP" ng-cloak>
  <head>
    <meta charset="utf-8">
    <title>WebliniaERP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Bootstrap core CSS -->
      <link rel='stylesheet prefetch' href='bootstrap/css/bootstrap.min.css'>

	<!-- Font Awesome -->
	<link href="css/font-awesome-4.1.0.min.css" rel="stylesheet">

	<!-- Pace -->
	<link href="css/pace.css" rel="stylesheet">

		<!-- Datepicker -->
	<link href="css/datepicker.css" rel="stylesheet"/>

	<!-- Timepicker -->
	<link href="css/bootstrap-timepicker.css" rel="stylesheet"/>

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">
	<style type="text/css">

		/* Fix for Bootstrap 3 with Angular UI Bootstrap */

		.modal {
			display: block;
		}

		/* Custom dialog/modal headers */

		.dialog-header-error { background-color: #d2322d; }
		.dialog-header-wait { background-color: #428bca; }
		.dialog-header-notify { background-color: #eeeeee; }
		.dialog-header-confirm { background-color: #333333; }
		.dialog-header-error span, .dialog-header-error h4,
		.dialog-header-wait span, .dialog-header-wait h4,
		.dialog-header-confirm span, .dialog-header-confirm h4 { color: #ffffff; }

		/* Ease Display */

		.pad { padding: 25px; }

		@media screen and (min-width: 768px) {

			#list_proodutos.modal-dialog  {width:900px;}

		}

		#list_produtos .modal-dialog  {width:70%;}

		#list_produtos .modal-content {min-height: 640px;;}


	</style>
  </head>

  <body class="overflow-hidden" ng-controller="relPagamentosController">

	<div id="wrapper" class="preload">
		<div id="top-nav" class="fixed skin-1">
			<a href="#" class="brand">
				<span>WebliniaERP</span>
				<span class="text-toggle"> Admin</span>
			</a><!-- /brand -->
			<button type="button" class="navbar-toggle pull-left" id="sidebarToggle">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<ul class="nav-notification clearfix">
				<?php include("alertas.php"); ?>
				<li class="profile dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">
						<strong>{{ userLogged.nme_usuario }}</strong>
						<span><i class="fa fa-chevron-down"></i></span>
					</a>
					<ul class="dropdown-menu">
						<li>
							<a class="clearfix" href="#">
								<img src="img/hage.png" alt="User Avatar">
								<div class="detail">
									<strong>{{ userLogged.nme_usuario }}</strong>
									<p class="grey" style="font-size: 7px;">{{ userLogged.end_email }}</p>
								</div>
							</a>
						</li>
						<li><a tabindex="-1" href="#" class="main-link"><i class="fa fa-inbox fa-lg"></i> {{ userLogged.nome_empreendimento }}</a></li>
						<li><a tabindex="-1" href="#" class="main-link"><i class="fa fa-list-alt fa-lg"></i> Meus Pedidos</a></li>
						<li class="divider"></li>
						<li><a tabindex="-1" class="main-link logoutConfirm_open" href="#logoutConfirm"><i class="fa fa-lock fa-lg"></i> Log out</a></li>
					</ul>
				</li>
			</ul>
		</div><!-- /top-nav-->

		<aside class="fixed skin-1">
			<div class="sidebar-inner scrollable-sidebar">
				<div class="size-toggle">
					<a class="btn btn-sm" id="sizeToggle">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<?php include("menu-bar-buttons.php"); ?>
				</div><!-- /size-toggle -->
				<div class="user-block clearfix">
					<img src="img/hage.png" alt="User Avatar">
					<div class="detail">
						<strong>{{ userLogged.nme_usuario }}</strong>
						<ul class="list-inline">
							<li><a href="#">{{ userLogged.nome_empreendimento }}</a></li>
						</ul>
					</div>
				</div><!-- /user-block -->

				<!--<div class="search-block">
					<div class="input-group">
						<input type="text" class="form-control input-sm" placeholder="search here...">
						<span class="input-group-btn">
							<button class="btn btn-default btn-sm" type="button"><i class="fa fa-search"></i></button>
						</span>
					</div>--><!-- /input-group -->
				<!--</div>--><!-- /search-block -->

				<?php include_once('menu-modulos.php') ?>
				
			</div><!-- /sidebar-inner -->
		</aside>

		<div id="main-container">
			<div class="padding-md">
				<div class="clearfix">
					<div class="pull-left">
						<span class="img-demo">
							<img src="assets/imagens/logos/{{ userLogged.nme_logo }}">
						</span>

						<div class="pull-left m-left-sm">
							<h3 class="m-bottom-xs m-top-xs">Relatório de Contas a Receber</h3>
							<small><?php echo date("d/m/Y H:i:s"); ?></small>
						</div>
					</div>

					<div class="pull-right text-right">
						<h6>{{ dados_empreendimento.num_cnpj }} - {{ dados_empreendimento.nome_empreendimento }}</h6>
						<h6>{{ dados_empreendimento.nme_logradouro }}, {{ dados_empreendimento.num_logradouro }}</h6>
						<h6>CEP: {{ dados_empreendimento.num_cep }} - {{ dados_empreendimento.nme_cidade }} - {{ dados_empreendimento.uf }}</h6>
						<h6>Telefone: {{ dados_empreendimento.num_telefone }}</h6>
					</div>
				</div>

				<hr>

				<div class="panel panel-default hidden-print" style="margin-top: 15px;">
					<div class="panel-heading"><i class="fa fa-calendar"></i> Filtros</div>				
					<div class="panel-body">
						<div class="alert-sistema alert errorBusca" style="display:none"></div>
						<form role="form" class="ng-pristine ng-valid">
							<div class="row">
								<div class="col-sm-2">
									<div class="form-group">
										<label class="control-label">Inicial</label>
										<div class="input-group">
											<input readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="dtaInicial" class="datepicker form-control">
											<span class="input-group-addon" id="cld_dtaInicial"><i class="fa fa-calendar"></i></span>
										</div>
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="control-label">Final</label>
										<div class="input-group">
											<input readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="dtaFinal" class="datepicker form-control">
											<span class="input-group-addon" id="cld_dtaFinal"><i class="fa fa-calendar"></i></span>
										</div>
									</div>
								</div>

								<div class="col-lg-3">
									<div class="form-group">
										<label class="control-label">Controle de Datas:</label>
										<select class="form-control ng-pristine ng-valid ng-touched" ng-model="busca_aux.tipoData">
											<option value="" selected="selected">Lançado ou Pago no Periodo</option>
											<option value="lan">Lançado no Periodo</option>
											<option value="pag">Pago/Pagar no Periodo</option>
										</select>
									</div>
								</div>

								<div class="col-lg-3">
									<div class="form-group">
										<label class="control-label">Forma de pagamento:</label>
										<select class="form-control ng-pristine ng-valid ng-touched" ng-model="busca_aux.id_forma_pagamento">
											<option value="">Todas</option>
											<option value="4">Boleto Bancário</option>
											<option value="6">Cartão de Crédito</option>
											<option value="5">Cartão de Débito</option>
											<option value="2">Cheque</option>
											<option value="3">Dinheiro</option>
											<option value="8">Transferência Bancária</option>
											<option value="7">Vale-Troca</option>
										</select>
									</div>
								</div>
								<div class="col-lg-2">
									<div class="form-group">
										<label class="control-label">Status:</label>
										<select class="form-control ng-pristine ng-valid ng-touched" ng-model="busca_aux.status_pagamento">
											<option value="" selected="selected">Todos</option>
											<option value="0">Pendente</option>
											<option value="1">Pago</option>
										</select>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-5">
									<div class="form-group">
										<label class="control-label">Cliente</label>
										<input ng-model="busca_aux.cliente" ng-keyup="loadMovimentacoes()" type="text" class="form-control input-sm ng-pristine ng-valid ng-touched">
									</div>
								</div>
								<div class="col-lg-2" ng-if="busca_aux.tipoData == 'lan'">
									<div class="form-group">
										<label for="" class="control-label">Detalhes C/C</label>
										<div class="form-group">
											<label class="label-radio inline">
												<input  ng-click="changeDetalhesCC(true)" name="changeDetalhesCC"  value="true" type="radio" class="inline-radio">
												<span class="custom-radio"></span>
												<span>Sim</span>
											</label>

											<label class="label-radio inline">
												<input  ng-click="changeDetalhesCC(false)" name="changeDetalhesCC" checked="checked" value="false" type="radio" class="inline-radio">
												<span class="custom-radio"></span>
												<span>Não</span>
											</label>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>

					<div class="panel-footer clearfix">
						<div class="pull-right">
							<button type="button" class="btn btn-sm btn-primary" ng-click="loadMovimentacoes()"><i class="fa fa-filter"></i> Aplicar Filtro</button>
							<button type="button" class="btn btn-sm btn-default" ng-click="resetFilter()"><i class="fa fa-times-circle"></i> Limpar Filtro</button>
							<button class="btn btn-sm btn-success hidden-print" ng-show="movimentacoes.length > 0"  id="invoicePrint"><i class="fa fa-print"></i> Imprimir</button>
							<button class="btn btn-sm btn-success hidden-print" ng-click="doExportExcel('registro')" ng-show="movimentacoes.length > 0"><i class="fa fa-file-excel-o"></i> Exportar p/ Excel</button>
						</div>
					</div>
				</div>
				<div class="row" id="divImprimir">
				<div class="col-sm-12">
					<table id="registro" class="table table-bordered table-condensed table-striped table-hover" ng-if="(movimentacoes != null)">
						<thead>
							<tr>
								<th rowspan="2" style="line-height: 46px;">Data Lançamento</th>
								<th rowspan="2" class="text-center" style="line-height: 46px;">Cliente</th>
								<th rowspan="2" class="text-center" style="line-height: 46px;">Descrição</th>
								<th rowspan="2" class="text-center" style="line-height: 46px;">Status</th>
								<th rowspan="2" class="text-center" style="line-height: 46px;width: 100px;">Valor</th>
								<th rowspan="" class="text-center" colspan="3" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">Taxa Maquineta</th>
							</tr>
							<tr ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">
								<th class="text-center" rowspan="1" style="width: 75px;">% Perc.</th>
								<th class="text-center" rowspan="1" style="width: 75px;">R$ Desc.</th>
								<th class="text-center" style="width: 100px;" rowspan="1">Valor c/ Desc.</th>
							</tr>
						</thead>
						<tbody>
							<tr ng-if="movimentacoes.length == null">
								<td class="text-center" colspan="7">
									<i class="fa fa-refresh fa-spin"></i> Aguarde, carregando movimentações...
								</td>
							</tr>
							<tr ng-repeat-start="item in movimentacoes">
								<td>{{ item.dta_lacamento | dateFormat:'dateTime' }}</td>
								<td>#{{ item.id_cliente | uppercase }} {{ item.nome_cliente | uppercase }}</td>


								<td ng-if="item.id_forma_pagamento == 6 && item.id_venda != null && busca.tipoData != 'lan' ">
								 ven.:#{{ item.id_venda }} <b>( Pago em : C.C - {{ item.current_parcela }}/{{ item.total_parcelas }} para {{ item.data_pagamento | dateFormat:'date' }} )</b>
								</td>

								<td ng-if="item.id_forma_pagamento == 6 && item.id_venda == null && busca.tipoData != 'lan' ">
									<b>Pag. em : C.C - {{ item.current_parcela }}/{{ item.total_parcelas }} para {{ item.data_pagamento | dateFormat:'date' }}</b>
								</td>

								<td ng-if="item.id_forma_pagamento == 6 && item.id_venda != null && busca.tipoData == 'lan' ">
								 ven.:#{{ item.id_venda }} <b>( Pago em : C.C - {{ item.parcelas.length }} X R$ {{ item.valor_pagamento | numberFormat:2:',':'.' }} )</b>
								</td>
								<td ng-if="item.id_forma_pagamento == 6 && item.id_venda == null && busca.tipoData == 'lan' ">
									<b>Pag. em : C.C - {{ item.parcelas.length }} X R$ {{ item.valor_pagamento | numberFormat:2:',':'.' }}</b>
								</td>

							
								<td ng-if="item.id_forma_pagamento != 6 && item.id_venda != null ">
									ven.:#{{ item.id_venda }} <b>( Pago em : {{ item.descricao_forma_pagamento }} para {{ item.data_pagamento | dateFormat:'date' }} )</b>
								</td>

								<td ng-if="item.id_forma_pagamento != 6 &&  item.id_venda == null ">
									<b>Pag. em : {{ item.descricao_forma_pagamento }} para {{ item.data_pagamento | dateFormat:'date' }}  </b>
								</td>
								<td class="text-center">
									{{ item.status_pagamento == 1 && 'Pago'  || 'Pendente' }}
								</td>
								<td  ng-if="item.id_forma_pagamento == 6 && busca.tipoData == 'lan'"  ng-attr-rowspan="{{ item.id_forma_pagamento == 6 && ccDetalhes ? item.parcelas.length + 1 : 1 }}" ng-style="{ 'line-height': ((item.id_forma_pagamento == 6 && ccDetalhes) && item.parcelas.length * 36.5+'px' || '' )}" style="color: #118A2E;" class="text-right">
									<strong>R$ {{ item.valor_pagamento * item.parcelas.length | numberFormat:2:',':'.' }}</strong>
								</td>
								<td ng-if="item.id_forma_pagamento == 6 && busca.tipoData != 'lan'" style="color:#118A2E;" class="text-right">
									<strong>R$ {{ item.valor_pagamento | numberFormat:2:',':'.' }}</strong>
								</td>
								<td ng-if="item.id_forma_pagamento != 6" style="color:#118A2E;" class="text-right">
									<strong>R$ {{ item.valor_pagamento | numberFormat:2:',':'.' }}</strong>
								</td>

								<td ng-attr-rowspan="{{ item.id_forma_pagamento == 6 && ccDetalhes ? item.parcelas.length + 1 : 1 }}" ng-style="{ 'line-height': ((item.id_forma_pagamento == 6 && ccDetalhes) && item.parcelas.length * 36.5+'px' || '' )}"  class="text-right" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')" >
									 {{ item.taxa_maquineta * 100 | numberFormat:2:',':'.' }}%
								</td>
								<td ng-attr-rowspan="{{ item.id_forma_pagamento == 6 && ccDetalhes ? item.parcelas.length + 1 : 1 }}" ng-style="{ 'line-height': ((item.id_forma_pagamento == 6 && ccDetalhes) && item.parcelas.length * 36.5+'px' || '' )}" class="text-right" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">
									 R$ {{ item.vlr_taxa_maquineta | numberFormat:2:',':'.' }}
								</td>
								<td ng-attr-rowspan="{{ item.id_forma_pagamento == 6 && ccDetalhes ? item.parcelas.length + 1 : 1 }}" ng-style="{ 'line-height': ((item.id_forma_pagamento == 6 && ccDetalhes) && item.parcelas.length * 36.5+'px' || '' )}" class="text-right" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">
									 R$ {{ item.valor_desconto_maquineta | numberFormat:2:',':'.' }}
								</td>
							</tr>
							<tr ng-repeat-end ng-if="item.id_forma_pagamento == 6 && ccDetalhes" ng-repeat="parcela in item.parcelas">
								<td class="text-right" colspan="4">{{ parcela.current_parcela+"/"+parcela.total_parcelas }} em {{ parcela.data_pagamento | dateFormat:'date' }}</td>
							</tr>
							<tr ng-if="movimentacoes.length > 0">
								<td colspan="4" class="text-right"> Total</td>
								<td style="color:#000;" class="text-right">
									<strong>R$ {{ totais.total | numberFormat:2:',':'.'}}</strong>
								</td>
								<td ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">

								</td>
								<td  style="color:#000;" class="text-right" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">
									<strong>R$ {{ total_desconto_taxa_maquineta | numberFormat:2:',':'.'}}</strong>
								</td>
								<td colspan="3" style="color:#000;" class="text-right" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">
									<strong>R$ {{ totais.total - total_desconto_taxa_maquineta | numberFormat:2:',':'.'}}</strong>
								</td>
							</tr>
						</tbody>
					</table>
					<span ng-if="(msg_error)" class="alert alert-{{ (status == 404) ? 'warning' : ((status == 500) ? 'danger' : '') }}">{{ msg_error }}</span>
				</div>
				</div>

			</div>
		</div>
		<!-- /main-container -->

		<!-- Footer
		================================================== -->
		<footer>
			<div class="row">
				<div class="col-sm-6">
					<span class="footer-brand">
						<strong class="text-danger">WebliniaERP</strong> Admin
					</span>
					<p class="no-margin">
						&copy; 2014 <strong>Weblinia Co.</strong> Todos os Direitos Reservados.
					</p>
				</div><!-- /.col -->
			</div><!-- /.row-->
		</footer>
	</div><!-- /wrapper -->

	<a href="" id="scroll-to-top" class="hidden-print"><i class="fa fa-chevron-up"></i></a>

	<!-- Logout confirmation -->
	<?php include("logoutConfirm.php"); ?>

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->

	<!-- Jquery -->
	<script src="js/jquery-1.10.2.min.js"></script>

	<!-- Bootstrap -->
    <script src="bootstrap/js/bootstrap.min.js"></script>

	<!-- Modernizr -->
	<script src='js/modernizr.min.js'></script>

    <!-- Pace -->
	<script src='js/pace.min.js'></script>

	<!-- Popup Overlay -->
	<script src='js/jquery.popupoverlay.min.js'></script>

    <!-- Slimscroll -->
	<script src='js/jquery.slimscroll.min.js'></script>

	<!-- Cookie -->
	<script src='js/jquery.cookie.min.js'></script>

	<!-- Endless -->
	<script src="js/endless/endless.js"></script>

		<!-- Datepicker -->
	<script src='js/bootstrap-datepicker.min.js'></script>

	<!-- Extras -->
	<script src="js/extras.js"></script>

	<!-- AngularJS -->
	<script src="js/tableExport/jquery.base64.js" type="text/javascript"></script>  
	<script src="js/tableExport/tableExport.js" type="text/javascript"></script>
	<script type="text/javascript" src="bower_components/angular/angular.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/angular-strap/2.1.2/angular-strap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/angular-strap/2.1.2/angular-strap.tpl.min.js"></script>
	<script type="text/javascript" src="bower_components/angular-ui-utils/mask.min.js"></script>
    <script src="js/angular-sanitize.min.js"></script>
    <script src="js/ui-bootstrap-tpls-0.6.0.js" type="text/javascript"></script>
    <script src="js/dialogs.v2.min.js" type="text/javascript"></script>
    <script src="js/auto-complete/ng-sanitize.js"></script>
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/rel_pagamentos-controller.js"></script>
	<script type="text/javascript"></script>

		<script id="printFunctions">
		$('.datepicker').datepicker();
			$("#cld_pagameto").on("click", function(){ $("#pagamentoData").trigger("focus"); });
			$("#cld_dtaInicial").on("click", function(){ $("#dtaInicial").trigger("focus"); });
			$("#cld_dtaFinal").on("click", function(){ $("#dtaFinal").trigger("focus"); });

			$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
			$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});
		function printDiv(id, pg) {
			var contentToPrint, printWindow;

			contentToPrint = window.document.getElementById(id).innerHTML;
			printWindow = window.open(pg);

		    printWindow.document.write("<link href='bootstrap/css/bootstrap.min.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/font-awesome.min.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/pace.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/endless.min.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/endless-skin.css' rel='stylesheet'>");

			printWindow.document.write("<style type='text/css' media='print'>@page { size: portrait; } th, td { font-size: 8pt; }</style><style type='text/css'>#invoicePrint{ display:none }</style>");

			printWindow.document.write(contentToPrint);

			printWindow.window.print();
			printWindow.document.close();
			printWindow.focus();
		}

		$(function()	{
			$('#invoicePrint').click(function()	{
				printDiv("divImprimir", "");
			});
		});

	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

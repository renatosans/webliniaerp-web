<?php
	include_once "util/login/restrito.php";
	restrito(array(1));
	date_default_timezone_set('America/Sao_Paulo');
?>
<!DOCTYPE html>
<html lang="en" ng-app="HageERP">
  <head>
    <meta charset="utf-8">
    <title>WebliniaERP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- Font Awesome -->
	<link href="css/font-awesome-4.1.0.min.css" rel="stylesheet">

	<!-- Pace -->
	<link href="css/pace.css" rel="stylesheet">

	<!-- Datepicker -->
	<link href="css/datepicker.css" rel="stylesheet"/>

	<!-- Timepicker -->
	<link href="css/bootstrap-timepicker.css" rel="stylesheet"/>

	<!-- Bower Components -->	
	<link href="bower_components/noty/lib/noty.css" rel="stylesheet">

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/custom.css">
  </head>

  <body class="overflow-hidden" ng-controller="RelatorioVendasPeriodo" ng-cloak>
	<div id="wrapper" class="bg-white preload">
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
							<h3 class="m-bottom-xs m-top-xs">Relatório de Vendas Diario</h3>
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
						<form role="form">
							<div class="row">
								<div class="col-lg-2">
									<div class="form-group">
										<label class="control-label">Data Inicial</label>
										<div class="input-group">
											<input type="text" id="dtaInicial" class="datepicker form-control" name="dtaInicial" style="text-align: center;">
											<span id="cld_dtaInicial" class="input-group-addon"><i class="fa fa-calendar"></i></span>
										</div>
									</div>
								</div>

								<div class="col-lg-2">
									<div class="form-group">
										<label class="control-label">Hora Inicial</label>
										<input type="time" id="hraInicial" class="form-control">
									</div>
								</div>

								<div class="col-lg-2">
									<div class="form-group">
										<label class="control-label">Data Final</label>
										<div class="input-group">
											<input type="text" id="dtaFinal" class="datepicker form-control" name="dtaFinal" style="text-align: center;">
											<span id="cld_dtaFinal" class="input-group-addon"><i class="fa fa-calendar"></i></span>
										</div>
									</div>
								</div>

								<div class="col-lg-2">
									<div class="form-group">
										<label class="control-label">Hora Final</label>
										<input type="time" id="hraFinal" class="form-control">
									</div>
								</div>
							</div>
						</form>
					</div>

					<div class="panel-footer clearfix">
						<div class="pull-right">
							<button type="button" class="btn btn-sm btn-primary" ng-click="aplicarFiltro()"><i class="fa fa-filter"></i> Aplicar Filtro</button>
							<button type="button" class="btn btn-sm btn-default" ng-click="limparBusca()"><i class="fa fa-times-circle"></i> Limpar Filtro</button>
							<button class="btn btn-sm btn-success hidden-print" ng-show="itens.length > 0" id="invoicePrint"><i class="fa fa-print"></i> Imprimir</button>
							<button class="btn btn-sm btn-success hidden-print" ng-click="doExportExcel('data')"><i class="fa fa-file-excel-o"></i> Exportar p/ Excel</button>
						</div>
					</div>
				</div>

				<ul class="pagination pagination-md m-top-none pull-right hidden-print" ng-show="paginacao.itens.length > 1">
					<li ng-repeat="item in paginacao.itens" ng-class="{'active': item.current}">
						<a href="" h ng-click="loadItens(item.offset,item.limit)">{{ item.index }}</a>
					</li>
				</ul>

				<br>

				<table id="data" class="table table-condensed table-bordered table-striped table-hover"
					ng-if="(vendas != null)">
					<thead>
						<tr>
							<th class="text-center" width="80">#</th>
							<th class="text-center" width="80">Data</th>
							<th class="text-center" width="80" ng-if="configuracoes.flg_modo_controle_mesas == 'mesas_comandas'">Mesa</th>
							<th class="text-center">Vendedor</th>
							<th class="text-center">Cliente</th>
							<th class="text-center" width="80">Status</th>
							<th class="text-center" width="100">Vlr. Venda</th>
						</tr>
					</thead>
					<tbody>
						<tr bs-tooltip ng-repeat="item in vendas">
							<td class="text-center">{{ item.id }}</td>
							<td class="text-center">{{ item.dta_group | dateFormat : 'time' }}</td>
							<td class="text-center" ng-if="configuracoes.flg_modo_controle_mesas == 'mesas_comandas'">{{ item.dsc_mesa }}</td>
							<td>{{ item.nme_vendedor }}</td>
							<td>{{ item.nme_cliente }}</td>
							<td class="text-center">
								{{ item.dsc_status }}
							</td>
							<td class="text-right">R$ {{ item.vlr_total_venda | numberFormat : '2' : ',' : '.'}}</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td class="text-right text-bold" colspan="{{ configuracoes.flg_modo_controle_mesas == 'mesas_comandas' ? '6' : '5'}}">Total Vendido</td>
							<td class="text-right">R$ {{ vlr_total_vendido | numberFormat : 2 : ',' : '.'  }}</td>
						</tr>
						<tr>
							<td class="text-right text-bold" colspan="{{ configuracoes.flg_modo_controle_mesas == 'mesas_comandas' ? '6' : '5'}}">Ticket Medio</td>
							<td class="text-right">R$ {{ vlr_ticket_medio | numberFormat : 2 : ',' : '.'  }}</td>
						</tr>
						<tr>
							<td class="text-center text-bold" colspan="{{ configuracoes.flg_modo_controle_mesas == 'mesas_comandas' ? '7' : '6'}}">Total por Forma de Pagamento</td>
						</tr>
						<tr ng-repeat="item in formas_pagamento">
							<td class="text-right" colspan="{{ configuracoes.flg_modo_controle_mesas == 'mesas_comandas' ? '6' : '5'}}">{{ item.dsc_forma_pagamento }} ({{ item.prc_respectivo | numberFormat : 0 : ',' : '.' }}%)</td>
							<td class="text-right">R$ {{ item.vlr_soma_pagamento | numberFormat : 2 : ',' : '.'  }}</td>
						</tr>
						<tr>
							<td class="text-right text-bold" colspan="{{ configuracoes.flg_modo_controle_mesas == 'mesas_comandas' ? '6' : '5'}}">Total Recebido</td>
							<td class="text-right">R$ {{ vlr_total_formas_pagamento | numberFormat : 2 : ',' : '.'  }}</td>
						</tr>
					</tfoot>
				</table>

				<span ng-if="(msg_error)" class="alert alert-{{ (status == 404) ? 'warning' : ((status == 500) ? 'danger' : '') }}">{{ msg_error }}</span>
			</div><!-- /.padding20 -->
		</div><!-- /main-container -->
	</div><!-- /wrapper -->

	<div class="modal fade" id="modal-aguarde">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><i class="fa fa-refresh fa-spin"></i> Aguarde!</h4>
				</div>
				<div class="modal-body">
					Carregando dados do relatório...
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

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

	<!-- Datepicker -->
	<script src='js/bootstrap-datepicker.min.js'></script>

	<!-- Timepicker -->
	<script src='js/bootstrap-timepicker.min.js'></script>

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

	<!-- Moment -->
	<script src="js/moment/moment.min.js"></script>

	<script src="js/jquery.noty.packaged.js"></script>

	<!-- Bower Components -->	
	<script src="bower_components/noty/lib/noty.min.js" type="text/javascript"></script>
    <script src="bower_components/mojs/build/mo.min.js" type="text/javascript"></script>

	<!-- Extras -->
	<script src="js/extras.js"></script>

	<!-- UnderscoreJS -->
	<script type="text/javascript" src="bower_components/underscore/underscore.js"></script>

	<!-- AngularJS -->
	<script src="js/tableExport/jquery.base64.js" type="text/javascript"></script>  
	<script src="js/tableExport/tableExport.js" type="text/javascript"></script>
	<script type="text/javascript" src="bower_components/angular/angular.js"></script>
	<script src="js/angular-strap.min.js"></script>
	<script src="js/angular-strap.tpl.min.js"></script>
	<script type="text/javascript" src="bower_components/angular-ui-utils/mask.min.js"></script>
    <script src="js/angular-sanitize.min.js"></script>
    <script src="js/ui-bootstrap-tpls-0.6.0.js" type="text/javascript"></script>
    <script src="js/dialogs.v2.min.js" type="text/javascript"></script>
    <script src="js/auto-complete/ng-sanitize.js"></script>
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/relatorio_vendas_periodo_controller.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('.datepicker').datepicker();
			$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});

			$("#cld_dtaInicial").on("click", function(){ $("#dtaInicial").trigger("focus"); });

			$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});
		});
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

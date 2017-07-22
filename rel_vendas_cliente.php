<?php
	include_once "util/login/restrito.php";
	restrito();
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

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/custom.css">
  </head>

  <body class="overflow-hidden" ng-controller="RelatorioVendasClienteController" ng-cloak>
	<!-- Overlay Div -->
	<!-- <div id="overlay" class="transparent"></div>

	<a href="" id="theme-setting-icon" class="hidden-print"><i class="fa fa-cog fa-lg"></i></a>
	<div id="theme-setting" class="hidden-print">
		<div class="title">
			<strong class="no-margin">Skin Color</strong>
		</div>
		<div class="theme-box">
			<a class="theme-color" style="background:#323447" id="default"></a>
			<a class="theme-color" style="background:#efefef" id="skin-1"></a>
			<a class="theme-color" style="background:#a93922" id="skin-2"></a>
			<a class="theme-color" style="background:#3e6b96" id="skin-3"></a>
			<a class="theme-color" style="background:#635247" id="skin-4"></a>
			<a class="theme-color" style="background:#3a3a3a" id="skin-5"></a>
			<a class="theme-color" style="background:#495B6C" id="skin-6"></a>
		</div>
		<div class="title">
			<strong class="no-margin">Sidebar Menu</strong>
		</div>
		<div class="theme-box">
			<label class="label-checkbox">
				<input type="checkbox" checked id="fixedSidebar">
				<span class="custom-checkbox"></span>
				Fixed Sidebar
			</label>
		</div>
	</div> --><!-- /theme-setting -->

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
							<h3 class="m-bottom-xs m-top-xs">Relatório Analítico de Vendas</h3>
							<span class="text-muted">Extrato de Vendas do Cliente</span>
						</div>
					</div>

					<div class="pull-right text-right">
						<h5><strong>#<?php echo rand(); ?></strong></h5>
						<strong><?php echo date("d/m/Y H:i:s"); ?></strong>
					</div>
				</div>

				<hr>

				<div class="row">
					<div class="col-sm-12">
						<div class="panel panel-default">
							<div class="panel-body">
								<div class="row">
									<div class="col-sm-4">
										<a href="rel_total_vendas_cliente.php" class="btn btn-sm btn-primary hidden-print">
											<i class="fa fa-chevron-circle-left"></i> Voltar
										</a>
									</div>
									<div class="col-sm-4 text-center">
										<h4>Cliente: {{cliente.nome}}</h4>
									</div>
									<div class="col-sm-4 text-right">
										<button class="btn btn-sm btn-success hidden-print" id="invoicePrint">
											<i class="fa fa-print"></i> Imprimir
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>


				<ul class="pagination pagination-sm m-top-none pull-right hidden-print" ng-show="paginacao.vendas.length > 1">
					<li ng-repeat="item in paginacao.vendas" ng-class="{'active': item.current}">
						<a href="" h ng-click="loadVendas(item.offset,item.limit)">{{ item.index }}</a>
					</li>
				</ul>

				<br>

				<table id="data" class="table table-bordered table-hover table-striped table-condensed">
					<thead>
						<tr>
							<th width="100" class="text-center">ID da Venda</th>
							<th class="text-center">Data</th>
							<th width="100" class="text-center">Qtd. Itens</th>
							<th width="100" class="text-center">Total</th>
							<th width="100" class="text-center hidden-print">Ações</th>
						</tr>
					</thead>
					<tbody>
						<tr ng-if="vendas.length == 0 && vendas != null">
							<td class="text-center" colspan="8">
								<i class="fa fa-refresh fa-spin"></i> Aguarde, carregando itens...
							</td>
						</tr>
						<tr ng-if="vendas.length < 0 && vendas == null">
							<td colspan="8">
								Nenhuma venda encontrada para este cliente.
							</td>
						</tr>
						<tr ng-repeat="item in vendas">
							<td class="text-center">{{ item.id }}</td>
							<td class="text-center">{{ item.dta_venda | dateFormat:'date'}}</td>
							<td class="text-center">{{ item.qtd_itens }}</td>
							<td class="text-right">R$ {{ item.vlr_total_venda | numberFormat:2:',':'.' }}</td>
							<td class="text-center hidden-print">
								<button ng-click="view(item.id)" type="button" class="btn btn-xs btn-primary"><i class="fa fa-tasks"></i> Detalhes da Venda</button>
							</td>
						</tr>
					</tbody>
				</table>
			</div><!-- /.padding20 -->
		</div><!-- /main-container -->

		<!-- /Modal clientes-->
		<div class="modal fade" id="list_clientes" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Detalhes da Venda</h4>
						<p class="muted">Cliente : {{cliente.nome}}</p>
						<p class="muted">Venda #{{ id_venda }}</p>
      				</div>
				    <div class="modal-body">
				   		<div class="row">
				   			<div class="col-sm-12">
				   				<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(clientes.length != 0)">
										<tr>
											<th class="text-center">Produto</th>
											<th>Fabricante</th>
											<th class="text-center" width="60">Tamanho</th>
											<th class="text-center" width="50">Qtd</th>
											<th class="text-center" width="70">Valor</th>
											<th class="text-center" colspan="2" width="60">Desconto</th>
											<th class="text-center" width="100">Subtotal</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in detalhes">
											<td>{{ item.nome_produto }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td class="text-center">{{ item.peso }}</td>
											<td class="text-center">{{ item.qtd }}</td>
											<td class="text-right">R$ {{ item.valor_real_item  | numberFormat:2:',':'.' }}</td>
											<td class="text-center" width="60">{{ item.valor_desconto * 100 | numberFormat:2:',':'.' }}%</td>
											<td class="text-center" width="20">
												<i class="fa fa-dot-circle-o" ng-if="item.css_cor.length > 0" style="color: {{item.css_cor}}"
													tooltip="(>=) a {{ item.perc_desconto_min * 100 | numberFormat:2:',':'.' }}% e (<=) a {{ item.perc_desconto_max * 100 | numberFormat:2:',':'.' }}%"></i>
											</td>
											<td class="text-right">R$ {{ item.sub_total | numberFormat:2:',':'.'}}</td>
										</tr>
									</tbody>
								</table>
				   			</div>
				   		</div>
				    </div>
				    <div class="modal-footer" ng-if="paginacao_clientes != null">
				    	<ul class="pagination pagination-xs m-top-none pull-right">
							<li ng-repeat="item in paginacao_clientes" ng-class="{'active': item.current}">
								<a href="" ng-click="loadCliente(item.offset,item.limit)">{{ item.index }}</a>
							</li>
						</ul>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->
	</div><!-- /wrapper -->

	<div class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Aguarde</h4>
				</div>
				<div class="modal-body">
					<p>Carregando dados do relatório...</p>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<a href="" id="scroll-to-top" class="hidden-print"><i class="fa fa-chevron-up"></i></a>

	<!-- Logout confirmation -->
	<div class="custom-popup width-100" id="logoutConfirm">
		<div class="padding-md">
			<h4 class="m-top-none"> Do you want to logout?</h4>
		</div>

		<div class="text-center">
			<a class="btn btn-success m-right-sm" href="login.html">Logout</a>
			<a class="btn btn-danger logoutConfirm_close">Cancel</a>
		</div>
	</div>

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

	<!-- Extras -->
	<script src="js/extras.js"></script>

	<!-- UnderscoreJS -->
	<script type="text/javascript" src="bower_components/underscore/underscore.js"></script>

	<!-- AngularJS -->
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
	<script src="js/angular-controller/relatorio-vendas-cliente-controller.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#cld_pagameto").on("click", function(){ $("#pagamentoData").trigger("focus"); });
			$("#cld_dtaInicial").on("click", function(){ $("#dtaInicial").trigger("focus"); });
			$("#cld_dtaFinal").on("click", function(){ $("#dtaFinal").trigger("focus"); });

			$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
			$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});


			$("#btn_x").on("click", function() {
				$('#list_clientes').modal('show');
			});
		});
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

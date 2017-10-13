<!DOCTYPE html>
<html lang="en" ng-app="HageERP">
  <head>
    <meta charset="utf-8">
    <title>WebliniaERP Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- Font Awesome -->
	<link href="css/font-awesome-4.1.0.min.css" rel="stylesheet">

	<!-- Pace -->
	<link href="css/pace.css" rel="stylesheet">

	<!-- Gritter -->
	<link href="css/gritter/jquery.gritter.css" rel="stylesheet">

	<!-- Datepicker -->
	<link href="css/datepicker.css" rel="stylesheet"/>

	<!-- Timepicker -->
	<link href="css/bootstrap-timepicker.css" rel="stylesheet"/>

	<!-- Color box -->
	<link href="css/colorbox/colorbox.css" rel="stylesheet">

	<!-- Morris -->
	<link href="css/morris.css" rel="stylesheet"/>

	<!-- Endless -->
	<link href="css/endless.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">

	<link href="css/custom.css" rel="stylesheet">
  </head>

  <body class="overflow-hidden" ng-controller="DashboardController" ng-cloak>

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
						<li><a tabindex="-1" href="profile.html" class="main-link"><i class="fa fa-inbox fa-lg"></i> {{ userLogged.nome_empreendimento }}</a></li>
						<li><a tabindex="-1" href="profile.html" class="main-link"><i class="fa fa-list-alt fa-lg"></i> Meus Pedidos</a></li>
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
					<a class="btn btn-sm pull-right logoutConfirm_open"  href="#logoutConfirm">
						<i class="fa fa-power-off"></i>
					</a>
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

				<div class="main-menu">
					<ul>
						<!-- Dashboard (index) -->
						<li class="active">
							<a href="dashboard.php">
								<span class="menu-icon"><i class="fa fa-dashboard fa-lg"></i></span>
								<span class="text">Dashboard</span>
								<span class="menu-hover"></span>
							</a>
						</li>

						<!-- Módulos -->
						<li class="openable">
							<a href="#">
								<span class="menu-icon"><i class="fa fa-th fa-lg"></i></span>
								<span class="text">Módulos</span>
								<span class="menu-hover"></span>
							</a>
							<ul class="submenu">
								<!--<?php include("menu-modulos.php") ?>-->
							</ul>
						</li>

						<!-- Relatórios -->
						<li class="openable">
							<a href="#">
								<span class="menu-icon"><i class="fa fa-copy fa-lg"></i></span>
								<span class="text">Relatórios</span>
								<span class="menu-hover"></span>
							</a>
							<ul class="submenu">
								<!--<?php include("menu-relatorios.php") ?>-->
							</ul>
						</li>
					</ul>

					<!-- Exemplos de Alerta -->
					<!-- <div class="alert alert-info">Welcome to Endless Admin. Do not forget to check all my pages.</div>
					<div class="alert alert-danger">Welcome to Endless Admin. Do not forget to check all my pages.</div>
					<div class="alert alert-warning">Welcome to Endless Admin. Do not forget to check all my pages.</div> -->
				</div><!-- /main-menu -->
			</div><!-- /sidebar-inner -->
		</aside>

		<div id="main-container">
			<div id="breadcrumb">
				<ul class="breadcrumb">
					 <li><i class="fa fa-home"></i> <a href="dashboard.php"> Home</a></li>
					 <li class="active"><i class="fa fa-dashboard"></i> Dashboard</li>
				</ul>
			</div><!-- /breadcrumb-->

			<div class="main-header">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-dashboard"></i> Dashboard</h3>
					<span>Bem vindo de volta Sr. {{ userLogged.nme_usuario }}</span>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md">
				<div class="row">
					<div class="col-lg-12">
						<div class="panel panel-default hidden-print" style="margin-top: 15px;">
							<div class="panel-heading"><i class="fa fa-calendar"></i> Filtros</div>

							<div class="panel-body">
								<form id="filterForm" class="form" role="form">
									<div class="row">
										<div class="col-lg-2">
											<div class="form-group divDtaInicial">
												<label class="control-label">Inicial</label>
												<div class="input-group">
													<input type="text" id="dtaInicial" name="dta_inicial" class="datepicker form-control text-center" ng-model="filtro.dta_inicio">
													<span id="btnDtaInicial" class="input-group-addon"><i class="fa fa-calendar"></i></span>
												</div>
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group divDtaFinal">
												<label class="control-label">Final</label>
												<div class="input-group">
													<input type="text" id="dtaFinal" name="dta_inicial" class="datepicker form-control text-center" ng-model="filtro.dta_fim">
													<span id="btnDtaFinal" class="input-group-addon"><i class="fa fa-calendar"></i></span>
												</div>
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label class="control-label"><br></label>
												<button type="button" class="btn form-control btn-primary" ng-click="aplicarFiltro()"><i class="fa fa-filter"></i> Aplicar Filtro</button>
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label class="control-label"><br></label>
												<button type="button" class="btn form-control btn-default" ng-click="resetFilter()"><i class="fa fa-times-circle"></i> Limpar Filtro</button>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-3 col-sm-6 col-md-6">
						<div class="panel-stat3 bg-primary fadeInDown animation-delay6">
							<h2 class="m-top-none">R$ <span id="clientsSalesCount">{{ total.vlrTotalFaturamento }}</span></h2>
							<h5>Total Faturamento</h5>
							(no período)
							<div class="stat-icon">
								<i class="fa fa-shopping-cart fa-3x"></i>
							</div>
						</div>
					</div><!-- /.col -->

					<div class="col-lg-3 col-sm-6 col-md-6">
						<div class="panel-stat3 bg-warning fadeInDown animation-delay5">
							<h2 class="m-top-none">R$ <span id="suppliersSalesCount">{{ total.vlrSaldoDevedorFornecedores }}</span></h2>
							<h5>Pagamentos Agendados</h5>
							(a fornecedores)
							<div class="stat-icon">
								<i class="fa fa-truck fa-3x"></i>
							</div>
						</div>
					</div><!-- /.col -->

					<div class="col-lg-3 col-sm-6 col-md-6">
						<div class="panel-stat3 bg-danger fadeInDown animation-delay4">
							<h2 class="m-top-none">R$ <span id="negativeCount">{{ total.vlrSaldoDevedorClientes }}</span></h2>
							<h5>Saldo Devedor</h5>
							(de clientes)
							<div class="stat-icon">
								<i class="fa fa-usd fa-3x"></i>
							</div>
						</div>
					</div><!-- /.col -->

					<div class="col-lg-3 col-sm-6 col-md-6">
						<div class="panel-stat3 bg-info fadeInDown animation-delay3">
							<h2 class="m-top-none">R$ <span id="clientesCount">{{ total.vlrCustoTotalEstoque }}</span></h2>
							<h5>Custo Total</h5>
							(Produtos em Estoque)
							<div class="stat-icon">
								<i class="fa fa-sitemap fa-3x"></i>
							</div>
						</div>
					</div><!-- /.col -->
				</div>

				<div class="row">
					<div class="col-lg-3 col-sm-6 col-md-6">
						<div class="panel-stat3 bg-success fadeInDown animation-delay3">
							<h2 class="m-top-none">R$ <span id="clientsOkPaymentsCount">{{ total.vlrTotalPagamentosConfirmados }}</span></h2>
							<h5>Pagamentos Confirmados</h5>
							(no período)
							<div class="stat-icon">
								<i class="fa fa-check-square-o fa-3x"></i>
							</div>
						</div>
					</div><!-- /.col -->

					<div class="col-lg-3 col-sm-6 col-md-6">
						<div class="panel-stat3 bg-warning fadeInDown animation-delay4">
							<h2 class="m-top-none">R$ <span id="clientsNonOkPaymentsChequeCount">{{ total.vlrTotalPagamentosNaoConfirmados.cheque }}</span></h2>
							<h5>Cheques a Compensar</h5>
							(no período)
							<div class="stat-icon">
								<i class="fa fa-money fa-3x"></i>
							</div>
						</div>
					</div><!-- /.col -->

					<div class="col-lg-3 col-sm-6 col-md-6">
						<div class="panel-stat3 bg-warning fadeInDown animation-delay5">
							<h2 class="m-top-none">R$ <span id="clientsNonOkPaymentsBoletoCount">{{ total.vlrTotalPagamentosNaoConfirmados.boleto }}</span></h2>
							<h5>Boletos a Compensar</h5>
							(no período)
							<div class="stat-icon">
								<i class="fa fa-barcode fa-3x"></i>
							</div>
						</div>
					</div><!-- /.col -->

					<div class="col-lg-3 col-sm-6 col-md-6">
						<div class="panel-stat3 bg-warning fadeInDown animation-delay6">
							<h2 class="m-top-none">R$ <span id="clientsNonOkPaymentsCreditoCount">{{ total.vlrTotalPagamentosNaoConfirmados.credito }}</span></h2>
							<h5>C. Crédito a Compensar</h5>
							(no período)
							<div class="stat-icon">
								<i class="fa fa-credit-card fa-3x"></i>
							</div>
						</div>
					</div><!-- /.col -->
				</div>

				<div class="row">
					<div class="col-lg-3 col-md-6 col-sm-6">
						<div class="panel panel-info fadeInUp animation-delay4">
							<div class="panel-heading">
								<i class="fa fa-tags fa-lg"></i> Vendas por Categoria
							</div>
							<div class="panel-body">
								<donutchart data="top_10_vendas_categoria" height="200"></donutchart>
							</div>
						</div><!-- /panel -->
					</div>

					<div class="col-lg-3 col-md-6 col-sm-6">
						<div class="panel panel-info fadeInUp animation-delay5">
							<div class="panel-heading">
								<i class="fa fa-puzzle-piece fa-lg"></i> Top 10 Vendas por Fabricante
							</div>
							<div class="panel-body">
								<donutchart data="top_10_vendas_fabricante" height="200"></donutchart>
							</div>
						</div>
					</div>

					<div class="col-lg-3 col-md-6 col-sm-6">
						<div class="panel panel-info fadeInUp animation-delay6">
							<div class="panel-heading">
								<i class="fa fa-users fa-lg"></i> Top 10 Vendas por Produto
							</div>
							<div class="panel-body">
								<donutchart data="top_10_vendas_produto" height="200"></donutchart>
							</div>
						</div>
					</div>

					<div class="col-lg-3 col-md-6 col-sm-6">
						<div class="panel panel-info fadeInUp animation-delay6">
							<div class="panel-heading">
								<i class="fa fa-users fa-lg"></i> Top 10 Vendas por Clientes
							</div>
							<div class="panel-body">
								<donutchart data="top_10_vendas_cliente" height="200"></donutchart>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-3 col-sm-6 col-md-6">
						<div class="panel panel-default panel-stat2 fadeInDown animation-delay5">
							<div class="panel-body">
								<span class="stat-icon">
									<i class="fa fa-shopping-cart"></i>
								</span>
								<div class="pull-right text-right">
									<div class="value">{{ count.vendas }}</div>
									<div class="title">Vendas</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-lg-3 col-sm-6 col-md-6">
						<div class="panel panel-default panel-stat2 fadeInDown animation-delay4">
							<div class="panel-body">
								<span class="stat-icon">
									<i class="fa fa-ticket"></i>
								</span>
								<div class="pull-right text-right">
									<div class="value">{{ count.orcamentos }}</div>
									<div class="title">Orcamentos</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-lg-3 col-sm-6 col-md-6">
						<div class="panel panel-default panel-stat2 fadeInDown animation-delay3">
							<div class="panel-body">
								<span class="stat-icon">
									<i class="fa fa-user"></i>
								</span>
								<div class="pull-right text-right">
									<div class="value">{{ count.clientes }}</div>
									<div class="title">Clientes</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-lg-3 col-sm-6 col-md-6">
						<div class="panel panel-default panel-stat2 fadeInDown animation-delay2">
							<div class="panel-body">
								<span class="stat-icon">
									<i class="fa fa-archive"></i>
								</span>
								<div class="pull-right text-right">
									<div class="value">{{ count.produtos }}</div>
									<div class="title">Produtos</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-12">
						<div class="panel panel-primary fadeInUp animation-delay5">
							<div class="panel-heading">
								<i class="fa fa-sitemap fa-lg"></i> Estoque p/ Depósito
							</div>
							<div class="panel-body">
								<table class="table table-striped table-hover table-condensed table-bordered">
									<thead>
										<tr>
											<td>Depósito</td>
											<td class="text-center">Qtd.</td>
											<td class="text-center">Custo Total</td>
											<td class="text-center">Atacado Total</td>
											<td class="text-center">Interm. Total</td>
											<td class="text-center">Varejo Total</td>
										</tr>
									</thead>
									<tbody>
										<tr ng-if="estoqueDepositos == null || estoqueDepositos.length == 0">
											<td class="text-center" colspan="6">Informações insuficientes p/ exibir o indicador</td>
										</tr>
										<tr ng-repeat="item in estoqueDepositos">
											<td>{{ item.nme_deposito }}</td>
											<td class="text-center">{{ item.qtd_item }}</td>
											<td class="text-right">R$ {{ item.vlr_custo_total }}</td>
											<td class="text-right">R$ {{ item.vlr_total_venda_atacado }}</td>
											<td class="text-right">R$ {{ item.vlr_total_venda_intermediario }}</td>
											<td class="text-right">R$ {{ item.vlr_total_venda_varejo }}</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div><!-- /.col -->
				</div>

				<div class="row">
					<div class="col-lg-12">
						<div class="panel bg-info fadeInDown animation-delay4">
							<div class="panel-heading">
								<i class="fa fa-signal fa-lg"></i> Comparativo de Vendas (Ano Atual)
							</div>
							<div class="panel-body">
								<barchart
									xkey="comparativo_vendas_xkey"
									ykeys="comparativo_vendas_ykeys"
									labels="comparativo_vendas_labels"
									data="comparativo_vendas_data"
									gridtextcolor="#fff"
									height="200">
								</barchart>
							</div>
							<div class="panel-footer">
								<div class="row">
									<div class="col-xs-12 text-right">
										<span><i class="fa fa-shopping-cart"></i> Total Vendas R$ {{ vlrTotalVendasPeriodoComparativo }}</span>
									</div><!-- /.col -->
								</div><!-- /.row -->
							</div>
						</div><!-- /panel -->
					</div>
				</div>
			</div><!-- /.padding-md -->
		</div><!-- /main-container -->
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

	<!-- Gritter -->
	<script src="js/jquery.gritter.min.js"></script>

	<!-- Datepicker -->
	<script src='js/bootstrap-datepicker.min.js'></script>

	<!-- Timepicker -->
	<script src='js/bootstrap-timepicker.min.js'></script>

	<!-- Flot -->
	<script src='js/jquery.flot.min.js'></script>

	<!-- Morris -->
	<script src='js/rapheal.min.js'></script>
	<script src='js/morris.min.js'></script>

	<!-- Colorbox -->
	<script src='js/jquery.colorbox.min.js'></script>

	<!-- Sparkline -->
	<script src='js/jquery.sparkline.min.js'></script>

	<!-- Endless -->
	<script src="js/endless/endless_dashboard.js"></script>
	<script src="js/endless/endless.js"></script>

	<!-- Extras -->
	<script src="js/extras.js"></script>

	<!-- AngularJS -->
	<script type="text/javascript" src="js/angular.js"></script>
	<script type="text/javascript" src="js/hage-erp-app.js"></script>
	<script type="text/javascript" src="js/angular-services/user-service.js"></script>
	<script type="text/javascript" src="js/angular-controller/1-dashboard-controller.js"></script>
	<script type="text/javascript">
		$(function() {
			$('.datepicker').datepicker();
			$('.timepicker').timepicker();
			$("#btnDtaInicial").on("click", function(){ $("#dtaInicial").trigger("focus"); });
			$("#btnDtaFinal").on("click", function(){ $("#dtaFinal").trigger("focus"); });
			$('.datepicker').on('changeDate', function(ev){
				$(this).datepicker('hide');
				$(this).trigger("change");
			});
		});
	</script>

  </body>
</html>

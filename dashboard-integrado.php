<?php
	//include_once "util/login/restrito.php";
	//restrito(array(1));
?>
<!DOCTYPE html>
<html lang="en" ng-app="HageERP" ng-cloak>
  <head>
    <meta charset="utf-8">
    <title>WebliniaERP Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

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

	<link href="css/fileinput/fileinput.css" media="all" rel="stylesheet" type="text/css" />

	<link href="css/custom.css" rel="stylesheet">
	<style type="text/css">
		 .kv-avatar .file-preview-frame,.kv-avatar .file-preview-frame:hover {
            margin: 0;
            padding: 0;
            border: none;
            box-shadow: none;
            text-align: center;
        }
        .kv-avatar .file-input {
            display: table-cell;
            max-width: 220px;
        }
        .kv-avatar .file-drag-handle{
            display: none
        }
        .file-drop-zone.clickable:hover {
            border: 1px dashed #aaa !important ;
        }
        </style>
	</style>

	<link href="css/isteven-multi-select.css" rel="stylesheet">
  </head>

<body class="overflow-hidden" ng-controller="DashboardIntegradoController">

  	<!-- Overlay Div -->
	<div id="overlay" class="transparent"></div>

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
						<li>
							<a tabindex="-1" href="profile.html" class="main-link">
								<i class="fa fa-inbox fa-lg"></i> {{ userLogged.nome_empreendimento }}
							</a>
						</li>
						<li>
							<a tabindex="-1" href="profile.html" class="main-link">
								<i class="fa fa-list-alt fa-lg"></i> Meus Pedidos
							</a>
						</li>
						<li class="divider"></li>
						<li>
							<a tabindex="-1" class="main-link logoutConfirm_open" href="#logoutConfirm">
								<i class="fa fa-lock fa-lg"></i> Log out
							</a>
						</li>
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

				<?php include_once('menu-modulos.php') ?>
				
			</div><!-- /sidebar-inner -->
		</aside>

		<div id="main-container">
			<div id="breadcrumb">
				<ul class="breadcrumb">
					 <li><i class="fa fa-home"></i> <a href="dashboard.php"> Home</a></li>
					 <li class="active"><i class="fa fa-desktop"></i> Dashboard Integrado</li>
				</ul>
			</div><!-- /breadcrumb-->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-desktop"></i> Dashboard Integrado</h3>
					<span>Bem vindo de volta Sr. {{ userLogged.nme_usuario }}</span>
				</div><!-- /page-title -->

				<ul class="page-stats">
					<li>
			    		<div class="value">
			    			<span>Faturamento</span>
			    			<h4>R$ <strong id="faturamentoNumber">0</strong></h4>
			    		</div>
			    		<span id="faturamentoChart" class="sparkline"></span>
			    	</li>
			    	<li>
			    		<div class="value">
			    			<span>Despesas</span>
			    			<h4>R$ <strong id="despesasNumber">0</strong></h4>
			    		</div>
						<span id="despesasChart" class="sparkline"></span>
			    	</li>
			    </ul><!-- /page-stats -->
			</div><!-- /main-header -->

			<div class="padding-md">
				<div class="row">
					<div class="col-lg-8">
						<div class="row">
							<div class="col-lg-12">
								<div class="panel panel-default">
									<div class="panel-heading clearfix">
										<span class="pull-left"><i class="fa fa-cog"></i> Configurações do Filtro</span>
										<ul class="tool-bar">
											<li><a href="#filtro" data-toggle="collapse"><i class="fa fa-arrows-v"></i></a></li>
										</ul>
									</div>
									<ul class="list-group collapse" id="filtro">
										<li class="list-group-item clearfix">
											<div class="row">
												<div class="col-lg-12">
													<div class="form-group">
														<label class="control-label">Empreendimentos</label>
														<div     
															isteven-multi-select
															input-model="empreendimentos"
															output-model="empreendimentos_selecionados"
															translation="localLang"
															helper-elements="all none filter"
															button-label="icon name"
															item-label="icon name"
															tick-property="ticked"
														></div>
													</div>
												</div>
											</div>
										</li>
										<li class="list-group-item clearfix">
											<div class="pull-right">
												<button class="btn btn-sm btn-default" ng-click="resetFilter()">
													<i class="fa fa-undo"></i> Reset
												</button>
												<button class="btn btn-sm btn-primary" ng-click="applyFilter()">
													<i class="fa fa-filter"></i> Aplicar Filtro
												</button>
											</div>
										</li>
									</ul>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-sm-6 col-md-6">
								<div class="panel-stat3 bg-info vlr_receber_hoje">
									<h2 class="m-top-none">R$ <span class="value">0,00</span></h2>
									<h5>A receber hoje</h5>
									<i class="fa fa-arrow-circle-o-up fa-lg hide"></i>
									<span class="m-left-xs hide">15% do restante do mês</span>
									<div class="stat-icon">
										<i class="fa fa-money fa-3x"></i>
									</div>
									<div class="refresh-button hide">
										<i class="fa fa-refresh"></i>
									</div>
									<div class="loading-overlay">
										<i class="loading-icon fa fa-refresh fa-spin fa-lg"></i>
									</div>
								</div>
							</div><!-- /.col -->
							<div class="col-sm-6 col-md-6">
								<div class="panel-stat3 bg-warning vlr_pagar_hoje">
									<h2 class="m-top-none">R$ <span class="value">0,00</span></h2>
									<h5>A pagar hoje</h5>
									<i class="fa fa-arrow-circle-o-up fa-lg hide"></i>
									<span class="m-left-xs hide">28% do restante do mês</span>
									<div class="stat-icon">
										<i class="fa fa-area-chart fa-3x"></i>
									</div>
									<div class="refresh-button hide">
										<i class="fa fa-refresh"></i>
									</div>
									<div class="loading-overlay">
										<i class="loading-icon fa fa-refresh fa-spin fa-lg"></i>
									</div>
								</div>
							</div><!-- /.col -->
						</div>

						<div class="row">
							<div class="col-sm-6 col-md-6">
								<div class="panel-stat3 bg-success vlr_receber_atrasado">
									<h2 class="m-top-none">R$ <span class="value">0,00</span></h2>
									<h5>Recebimentos em atraso</h5>
									<i class="fa fa-arrow-circle-o-up fa-lg hide"></i>
									<span class="m-left-xs hide">3% Maior que o mês anterior</span>
									<div class="stat-icon">
										<i class="fa fa-warning fa-3x"></i>
									</div>
									<div class="refresh-button">
										<i class="fa fa-refresh"></i>
									</div>
									<div class="loading-overlay">
										<i class="loading-icon fa fa-refresh fa-spin fa-lg"></i>
									</div>
								</div>
							</div><!-- /.col -->
							<div class="col-sm-6 col-md-6">
								<div class="panel-stat3 bg-danger vlr_pagar_atrasado">
									<h2 class="m-top-none">R$ <span class="value">0,00</span></h2>
									<h5>Despesas em atraso</h5>
									<i class="fa fa-arrow-circle-o-up fa-lg hide"></i>
									<span class="m-left-xs hide">15% Maior que o mês anterior</span>
									<div class="stat-icon">
										<i class="fa fa-warning fa-3x"></i>
									</div>
									<div class="refresh-button">
										<i class="fa fa-refresh"></i>
									</div>
									<div class="loading-overlay">
										<i class="loading-icon fa fa-refresh fa-spin fa-lg"></i>
									</div>
								</div>
							</div><!-- /.col -->
						</div>

						<div class="row">
							<div class="col-lg-12">
								<div class="panel panel-default">
									<div class="panel-heading clearfix">
										<span class="pull-left"><i class="fa fa-bar-chart-o fa-lg"></i> Fluxo de Caixa Diário</span>
										<ul class="tool-bar">
											<li><a href="#" class="refresh-widget" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Refresh"><i class="fa fa-refresh"></i></a></li>
										</ul>
									</div>
									<div class="panel-body" id="trafficWidget">
										<div id="container" class="graph" style="height:400px"></div>
									</div>
									<div class="loading-overlay">
										<i class="loading-icon fa fa-refresh fa-spin fa-lg"></i>
									</div>
								</div><!-- /panel -->
							</div>
						</div>

						<div class="row">
							<div class="col-lg-12">
								<div class="panel panel-default">
									<div class="panel-heading">
										<i class="fa fa-archive"></i> Controle de Estoque Mínimo
									</div>
									<table class="table table-striped">
										<thead>
											<tr>
												<th>Produto</th>
												<th>% p/ Estoque Mínimo</th>
												<th></th>
												<th>Qtd. Atual</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>Produto A</td>
												<td>
													<div class="progress progress-striped active" style="height:8px; margin:5px 0 0 0;">
														<div class="progress-bar" style="width: 45%">
															<span class="sr-only">45% Complete</span>
														</div>
													</div>
												</td>
												<td>45%</td>
												<td><span class="badge badge-info">2hr</span></td>
											</tr>
											<tr>
												<td>Produto B</td>
												<td>
													<div class="progress progress-striped active" style="height:8px; margin:5px 0 0 0;">
														<div class="progress-bar progress-bar-success" style="width: 61%">
															<span class="sr-only">61% Complete</span>
														</div>
													</div>
												</td>
												<td>61%</td>
												<td><span class="badge badge-info">1hr</span></td>
											</tr>
											<tr>
												<td>Produto C</td>
												<td>
													<div class="progress progress-striped active" style="height:8px; margin:5px 0 0 0;">
														<div class="progress-bar progress-bar-danger" style="width: 97%">
															<span class="sr-only">97% Complete</span>
														</div>
													</div>
												</td>
												<td>97%</td>
												<td><span class="badge badge-info">5m</span></td>
											</tr>
											<tr>
												<td>Produto C</td>
												<td>
													<div class="progress progress-striped active" style="height:8px; margin:5px 0 0 0;">
														<div class="progress-bar progress-bar-warning" style="width: 18%">
															<span class="sr-only">18% Complete</span>
														</div>
													</div>
												</td>
												<td>18%</td>
												<td><span class="badge badge-info">12hr</span></td>
											</tr>
										</tbody>
									</table>
									<div class="panel-footer clearfix">
										<div class="pull-right">
											<ul class="pagination pagination-xs m-top-none pull-right">
												<li class="disabled"><a href="#">Previous</a></li>
												<li class="active"><a href="#">1</a></li>
												<li><a href="#">2</a></li>
												<li><a href="#">3</a></li>
												<li><a href="#">4</a></li>
												<li><a href="#">5</a></li>
												<li><a href="#">Next</a></li>
											</ul>
										</div>
									</div>
								</div><!-- /panel -->
							</div>
						</div>	
					</div>
					<div class="col-lg-4">
						<div class="row">
							<div class="panel panel-default">	
								<div class="panel-heading clearfix">
									<span class="pull-left">Atividades recentes</span>
									<ul class="tool-bar">
										<li><a href="#" class="refresh-widget" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Refresh"><i class="fa fa-refresh"></i></a></li>
										<li><a href="#feedList" data-toggle="collapse"><i class="fa fa-arrows-v"></i></a></li>
									</ul>
								</div>		
								<ul class="list-group collapse in" id="feedList">
									<li class="list-group-item clearfix">
										<div class="activity-icon small">
											<i class="fa fa-archive"></i>
										</div>
										<div class="pull-left m-left-sm">
											<span>John Doe cadastrou um novo produto</span><br/>
											<div class="clearfix">
												<small class="text-muted"><i class="fa fa-clock-o"></i> 2m atrás - Hage Suplementos</small>
											</div>
										</div>
									</li>
									<li class="list-group-item clearfix">
										<div class="activity-icon bg-success small">
											<i class="fa fa-shopping-cart"></i>
										</div>
										<div class="pull-left m-left-sm">
											<span>1 Venda realizada</span><br/>
											<div class="clearfix">
												<small class="text-muted"><i class="fa fa-clock-o"></i> 10m atrás - ForceFit Suplementos</small>
											</div>
										</div>	
									</li>
									<li class="list-group-item clearfix">
										<div class="activity-icon bg-danger small">
											<i class="fa fa-usd"></i>
										</div>
										<div class="pull-left m-left-sm">
											<span>John Doe incluiu um Lanç. Financ. <span class="text-danger">(despesa)</span><br/>
											<div class="clearfix">
												<small class="text-muted"><i class="fa fa-clock-o"></i> 20m atrás - Clube D</small>
											</div>
										</div>
									</li>
									<li class="list-group-item clearfix">
										<div class="activity-icon bg-info small">
											<i class="fa fa-usd"></i>
										</div>
										<div class="pull-left m-left-sm">
											<span>John Doe incluiu um Lanç. Financ. <span class="text-info">(receita)</span><br/>
											<div class="clearfix">
												<small class="text-muted"><i class="fa fa-clock-o"></i> 2h atrás - Weblinia</small>
											</div>
										</div>	
									</li>
								</ul><!-- /list-group -->	
								<div class="loading-overlay">
									<i class="loading-icon fa fa-refresh fa-spin fa-lg"></i>
								</div>
							</div><!-- /panel -->
						</div>
						<div class="row">
							<div class="panel bg-info fadeInDown animation-delay4">
								<div class="panel-body">
									<div id="lineChart" style="height: 150px;"></div>
									<div class="pull-right text-right">
										<strong class="font-14">Balanço R$ 3.210,00</strong><br/>
										<span><i class="fa fa-shopping-cart"></i> Total Vendas 867</span>
										<div class="seperator"></div>
									</div>
								</div>
								<div class="panel-footer">
									<div class="row">
										<div class="col-xs-4">
											Vendas em Janeiro
											<strong class="block">R$ 664,00</strong>
										</div><!-- /.col -->
										<div class="col-xs-4">
											Vendas em Fevereiro
											<strong class="block">R$ 731,00</strong>
										</div><!-- /.col -->
										<div class="col-xs-4">
											Vendas em Março
											<strong class="block">R$ 912,00</strong>
										</div><!-- /.col -->
									</div><!-- /.row -->
								</div>
							</div><!-- /panel -->
						</div>
					</div><!-- /.col -->
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
	<script src='js/jquery.animateNumber.min.js'></script>

	<!-- Endless -->
	<script src="js/endless/endless_dashboard.js"></script>
	<script src="js/endless/endless.js"></script>

	<!-- Extras -->
	<script src="js/extras.js"></script>

	<!-- HighCharts -->
	<script src="https://code.highcharts.com/highcharts.js"></script>
	<script src="https://code.highcharts.com/modules/exporting.js"></script>

	<!-- AngularJS -->
	<script type="text/javascript" src="js/angular.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/angular-strap/2.1.2/angular-strap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/angular-strap/2.1.2/angular-strap.tpl.min.js"></script>
	<script type="text/javascript" src="bower_components/angular-ui-utils/mask.min.js"></script>
    <script src="js/angular-sanitize.min.js"></script>
    <script src="js/ui-bootstrap-tpls-0.6.0.js" type="text/javascript"></script>
    <script src="js/dialogs.v2.min.js" type="text/javascript"></script>
    <script src="js/auto-complete/ng-sanitize.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/isteven-multi-select.js"></script>
    <script type="text/javascript">
    	var addParamModule = ['isteven-multi-select'] ;
    	$('.datepicker').datepicker();
		$('.timepicker').timepicker();

		$("#btnDtaInicial").on("click", function(){ $("#dtaInicial").trigger("focus"); });
		$("#btnDtaFinal").on("click", function(){ $("#dtaFinal").trigger("focus"); });

		$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
    </script>
    <script src="js/app.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/dashboard-integrado-controller.js?<?php echo filemtime('js/angular-controller/dashboard-integrado-controller.js')?>"></script>
	<?php include("google_analytics.php"); ?>
</body>
</html>

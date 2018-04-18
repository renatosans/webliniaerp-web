<?php
	include_once "util/login/restrito.php";
	restrito();
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
      <link rel='stylesheet prefetch' href='bootstrap/css/bootstrap.min.css'>

	<!-- Font Awesome -->
	<link href="css/font-awesome-4.1.0.min.css" rel="stylesheet">

	<!-- Pace -->
	<link href="css/pace.css" rel="stylesheet">

	<!-- Bower Components -->	
	<link href="bower_components/noty/lib/noty.css" rel="stylesheet">

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

		#map_canvas img {
			max-width: none;
		}

		.panel-stat3 {
			padding: 20px;
		}

		.panel-inverse {
			border-color: #000;
		}

		.panel-inverse>.panel-heading {
			background-color: #000;
			border-color: #000;
		}
	</style>
  </head>

  <body class="overflow-hidden" ng-controller="MapaController" ng-cloak>
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
			<div id="breadcrumb">
				<ul class="breadcrumb">
					 <li><i class="fa fa-home"></i> <a href="dashboard.php">Home</a></li>
					 <li class="active"><i class="fa fa-map-marker"></i> Mapa de Clientes</li>
				</ul>
			</div>
			<!-- breadcrumb -->

			<div class="padding-md">
				<div class="alert alert-sistema" style="display:none"></div>

				<div class="panel panel-inverse">
					<div class="panel-heading">
						<i class="fa fa-map-marker"></i> Mapa de Clientes
						<ul class="tool-bar">
							<li>
								<a style="color:#fff; cursor: pointer;" ng-click="resizeMap()"><i class="fa fa-arrows-alt"></i> Tela Inteira</a>
							</li>
						</ul>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-9">
								<div id="map_canvas" style="width: 100%; height: 550px"></div>
							</div>
							<div class="col-sm-3">
								<div class="row">
									<div class="col-lg-6 col-lg-offset-3">
										<img src="assets/imagens/logos/{{ userLogged.nme_logo }}">
									</div>
								</div>

								<div class="row">
									<div class="col-lg-12">
										<div class="panel-stat3 bg-primary fadeInUp animation-delay4">
											<h1 class="m-top-none">{{ qtd_clientes_total | numberFormat : 0 : ',' : '.' }}</h1>
											<h6>Total de Clientes</h6>
											<div class="stat-icon">
												<i class="fa fa-users fa-2x"></i>
											</div>
											<!-- <div class="refresh-button">
												<i class="fa fa-refresh"></i>
											</div>-->
											<div class="loading-overlay" ng-class="{'active':total.vlrTotalFaturamentoClinicas == 'loading'}">
												<i class="loading-icon fa fa-refresh fa-spin fa-lg"></i>
											</div> 
										</div>
									</div><!-- /.col -->
								</div>

								<div class="row">
									<div class="col-lg-12">
										<div class="panel-stat3 bg-success fadeInUp animation-delay3">
											<h1 class="m-top-none">{{ qtd_visitantes_total | numberFormat : 0 : ',' : '.' }}</h1>
											<h6>Total de Visitantes</h6>
											<div class="stat-icon">
												<i class="fa fa-map-marker fa-2x"></i>
											</div>
											<!-- <div class="refresh-button">
												<i class="fa fa-refresh"></i>
											</div>-->
											<div class="loading-overlay" ng-class="{'active':total.vlrTotalFaturamentoClinicas == 'loading'}">
												<i class="loading-icon fa fa-refresh fa-spin fa-lg"></i>
											</div> 
										</div>
									</div><!-- /.col -->
								</div>

								<div class="row">
									<div class="col-lg-12">
										<div class="panel-stat3 bg-info fadeInUp animation-delay2">
											<h1 class="m-top-none">{{ qtd_visitantes_hoje | numberFormat : 0 : ',' : '.' }}</h1>
											<h6>Visitantes Hoje</h6>
											<div class="stat-icon">
												<i class="fa fa-map-marker fa-2x"></i>
											</div>
											<!-- <div class="refresh-button">
												<i class="fa fa-refresh"></i>
											</div>-->
											<div class="loading-overlay" ng-class="{'active':total.vlrTotalFaturamentoClinicas == 'loading'}">
												<i class="loading-icon fa fa-refresh fa-spin fa-lg"></i>
											</div> 
										</div>
									</div><!-- /.col -->
								</div>

								<div class="row">
									<div class="col-lg-12">
										<div class="panel-stat3 bg-warning fadeInUp animation-delay1">
											<h1 class="m-top-none">{{ qtd_novos_cadastros | numberFormat : 0 : ',' : '.' }}</h1>
											<h6>Novos Clientes Cadastrados</h6>
											<div class="stat-icon">
												<i class="fa fa-plus-circle fa-2x"></i>
											</div>
											<!-- <div class="refresh-button">
												<i class="fa fa-refresh"></i>
											</div>-->
											<div class="loading-overlay" ng-class="{'active':total.vlrTotalFaturamentoClinicas == 'loading'}">
												<i class="loading-icon fa fa-refresh fa-spin fa-lg"></i>
											</div> 
										</div>
									</div><!-- /.col -->
								</div>
							</div>
						</div>
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

	<!-- Moment -->
	<script src="js/moment/moment.min.js"></script>

	<script src="js/jquery.noty.packaged.js"></script>

	<!-- Bower Components -->	
	<script src="bower_components/noty/lib/noty.min.js" type="text/javascript"></script>
    <script src="bower_components/mojs/build/mo.min.js" type="text/javascript"></script>

    <!-- UnderscoreJS -->
	<script type="text/javascript" src="bower_components/underscore/underscore.js"></script>

	<!-- Extras -->
	<script src="js/extras.js"></script>

	<!-- Google Maps API -->
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&key=AIzaSyANVfzQYwKLB7LJuKBi4_aoCj9n52ZYi-U"></script>

	<!-- AngularJS -->
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
	<script src="js/angular-controller/mapa-visitantes-controller.js"></script>
	<script type="text/javascript"></script>>
	<?php include("google_analytics.php"); ?>
  </body>
</html>
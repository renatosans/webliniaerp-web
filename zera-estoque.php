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

		/*--------------------------------------*/
		.chosen-choices{
			border-bottom-left-radius: 4px;
			border-bottom-right-radius: 4px;
			border-top-left-radius: 4px;
			border-top-right-radius: 4px;
			font-size: 12px;
			border-color: #ccc;
		}
	</style>
  </head>

  <body class="overflow-hidden" ng-controller="ZeraEstoqueController" ng-cloak>

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

				<?php include_once('menu-modulos.php') ?>
				
			</div><!-- /sidebar-inner -->
		</aside>

		<div id="main-container">
			<div id="breadcrumb">
				<ul class="breadcrumb">
					 <li><i class="fa fa-home"></i> <a href="dashboard.php">Home</a></li>
					 <li><i class="fa fa-sitemap"></i> <a href="depositos.php">Depósitos</a></li>
					 <li><i class="fa fa-list-ol"></i> <a href="estoque.php">Controle de Estoque</a></li>
					 <li class="active"><i class="fa fa-trash-o"></i> Limpeza de Estoque</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-trash-o"></i> Limpeza de Estoque</h3>
				</div>
			</div>

			<div class="padding-md">
				<div class="row">
					<div class="col-sm-12">
						<div class="alert alert-danger">
							<i class="fa fa-3x fa-exclamation-triangle pull-left"></i>
							<strong>Atenção!!!</strong>
							<br>
							As ações realizadas nesta tela são ireversíveis, portanto, estimamos que tenha ciência e domínio das mesmas.
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<i class="fa fa-filter"></i> Filtros
							</div>
							<div class="panel-body">
								<form class="form" role="form">
									<div class="row">
										<div class="col-sm-3">
											<div class="form-group">
												<label class="control-label">Depósito</label>
												<select class="form-control input-sm filters" ng-enter="aplicarFiltro()" ng-model="deposito" ng-options="i.nme_deposito for i in depositos"></select>
											</div>
										</div>

										<div class="col-sm-4">
											<div class="form-group">
												<label class="control-label">Produto</label>
												<input ng-model="busca.produto" class="form-control input-sm" ng-keyup="aplicarFiltro()" ng-enter="aplicarFiltro()">
											</div>
										</div>

										<div class="col-sm-3">
											<div class="form-group">
												<label class="control-label">Fabricante</label>
												<select class="form-control input-sm" ng-model="fabricante" ng-options="i.nome_fabricante for i in fabricantes" ng-enter="aplicarFiltro()"></select>
											</div>
										</div>

										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">Itens por Página</label>
												<select class="form-control input-sm" ng-model="itensPorPagina" ng-options="i.label for i in itensPorPaginaArr"></select>
											</div>
										</div>
									</div>
								</form>
							</div>
							<div class="panel-footer clearfix">
								<div class="pull-right">
									<button type="button" class="btn btn-sm btn-default" ng-click="resetFilter()">
										<i class="fa fa-times-circle"></i> Limpar Filtros
									</button>
									<button type="button" class="btn btn-sm btn-primary" ng-click="aplicarFiltro()">
										<i class="fa fa-filter"></i> Aplicar Filtros
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<i class="fa fa-tasks"></i> Resultado da Pesquisa</strong>
								<span ng-if="deposito.id > 0">| Depósito: <strong>{{ deposito.nme_deposito }}</strong></span>
								<span ng-if="fabricante.id > 0">| Fabricante: <strong>{{ fabricante.nome_fabricante }}</strong></span>
							</div>

							<div class="panel-body">
								<div class="row">
									<div class="col-sm-12">
										<table class="table table-bordered table-condensed table-striped table-hover">
											<thead>
												<tr>
													<th>Depósito</th>
													<th>Produto</th>
													<th>Fabricante</th>
													<th class="text-center">Tamanho</th>
													<th class="text-center" width="120">Validade</th>
													<th class="text-center" width="100">Qtd</th>
													<th class="text-center" width="140">
														<button type="button" class="btn btn-xs btn-primary" ng-click="unselectAll()" ng-show="(allSelected == true)">
															<i class="fa fa-square"></i> Desmarcar Todos
														</button>
														<button type="button" class="btn btn-xs btn-primary" ng-click="selectAll()" ng-show="(allSelected == false)">
															<i class="fa fa-check-square"></i> Marcar Todos
														</button>
													</th>
												</tr>
											</thead>
											<tbody>
												<tr ng-show="(itens.length == 0) && (zerado == false)">
													<td class="text-center" colspan="7">
														<i class="fa fa-refresh fa-spin"></i> Aguarde, carregando itens...
													</td>
												</tr>
												<tr ng-show="(zerado == true)">
													<td class="text-center text-danger" colspan="7">
														<i class="fa fa-times-circle"></i> Nenhum registro encontrado para os filtros selecionados!
													</td>
												</tr>
												<tr ng-repeat="(i, item) in itens">
													<td>{{ item.nme_deposito }}</td>
													<td>{{ item.nome }}</td>
													<td>{{ item.nome_fabricante }}</td>
													<td class="text-center">{{ item.peso }}</td>
													<td class="text-center" ng-if="item.dta_validade != '2099-12-31' ">{{ item.dta_validade | dateFormat: 'date' }}</td>
													<td class="text-center" ng-if="item.dta_validade == '2099-12-31' "></td>
													<td class="text-center">{{ item.qtd_item }}</td>
													<td class="text-center">
														<label class="label-checkbox">
															<input type="checkbox" class="regular-checkbox" ng-model="item.excluir" ng-click="selectItemExcluir(i, item)"/>
															<span class="custom-checkbox"></span>
														</label>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>

							<div class="panel-footer clearfix">
								<div class="pull-left">
									<button id="btExcluirSelecionados" type="button" data-loading-text="Aguarde, processando requisição..." class="btn btn-sm btn-danger" ng-click="deleteSelected()" ng-show="(hasSelected == true)">
										<i class="fa fa-trash-o"></i> Excluir Selecionados
									</button>
									<span class="text-danger text-center text-uppercase span-delete hide"><i class="fa fa-exclamation-triangle"></i> <strong class="text-delete"></strong></span>
									<span class="text-center text-uppercase span-ok hide"><i class="fa fa-check-square-o"></i> <strong class="text-ok"></strong></span>
								</div>
								<div class="pull-right">
									<ul class="pagination pagination-sm m-top-none">
										<li ng-repeat="item in paginacao.itens" ng-class="{'active': item.current}">
											<a href="" ng-click="aplicarFiltro(item.offset, item.limit,item)">{{ item.index }}</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
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

	<!-- Chosen -->
	<script src='js/chosen.jquery.min.js'></script>

	<!-- Jquery Form-->
	<script src='js/jquery.form.js'></script>

	<!-- Mask-input -->
	<script src='js/jquery.maskedinput.min.js'></script>
	<script src='js/jquery.maskMoney.js'></script>

	<!-- Datepicker -->
	<script src='js/bootstrap-datepicker.min.js'></script>

	<!-- Timepicker -->
	<script src='js/bootstrap-timepicker.min.js'></script>

	<!-- Slider -->
	<script src='js/bootstrap-slider.min.js'></script>

	<!-- Tag input -->
	<script src='js/jquery.tagsinput.min.js'></script>

	<!-- WYSIHTML5 -->
	<script src='js/wysihtml5-0.3.0.min.js'></script>
	<script src='js/uncompressed/bootstrap-wysihtml5.js'></script>

	<!-- Dropzone -->
	<script src='js/dropzone.min.js'></script>

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
	<script src="js/endless/endless_form.js"></script>
	<script src="js/endless/endless.js"></script>

	<!-- Mascaras para o formulario de produtos -->
	<script src="js/scripts/mascaras.js"></script>

	<!-- Extras -->
	<script src="js/extras.js"></script>

	<!-- Underscore -->
	<script type="text/javascript" src="bower_components/underscore/underscore.js"></script>

	<!-- AngularJS -->
	<script type="text/javascript" src="bower_components/angular/angular.js"></script>
	<script type="text/javascript" src="bower_components/angular-ui-utils/mask.min.js"></script>
    <script src="js/angular-sanitize.min.js"></script>
    <script src="js/ui-bootstrap-tpls-0.6.0.js" type="text/javascript"></script>
    <script src="js/dialogs.v2.min.js" type="text/javascript"></script>
    <script src="js/auto-complete/ng-sanitize.js"></script>
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/zera-estoque-controller.js"></script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

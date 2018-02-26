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


		</style>
	</head>

	<body class="overflow-hidden" ng-controller="PlanoContasController" ng-cloak>
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
						<li class="active"><i class="fa fa-code-fork"></i> Naturezas de Operação</li>
					</ul>
				</div><!-- breadcrumb -->

				<div class="main-header clearfix">
					<div class="page-title">
						<h3 class="no-margin"><i class="fa fa-code-fork"></i> Naturezas de Operação</h3>
						<!-- <a class="btn btn-info" id="btn-novo" ng-disabled="editing" ng-click="showBoxNovo()"><i class="fa fa-plus-circle"></i> Nova Natureza</a> -->
					</div>
					<!-- /page-title -->
				</div>
				<!-- /main-header -->

				<div class="padding-md">
					<div class="alert alert-sistema" style="display:none"></div>

					<div class="panel panel-default" id="box-novo">
						<div class="panel-heading"><i class="fa fa-plus-circle"></i> Naturezas de Operação</div>

						<div class="panel-body">
							<form role="form">
								<div class="row">
									<div class="col-sm-5" id="id_plano_conta">
										<div class="panel panel-default no-border">
											<div class="panel-body">
												<div id="blockTree" style="width: 100%; height: 100%; position: absolute; background-color: #000; display: none; opacity: 0.1; z-index: 100;"></div>

												<div id="tree"
													data-angular-treeview="true"
													data-tree-model="planoContas"
													data-node-id="id"
													data-node-label="nme_completo"
													data-node-children="children">
												</div>
											</div>
										</div>
									</div>

									<div class="col-sm-7 well">

										<div class="row">
											<div class="col-sm-12">
												<div class="form-group">
													<label class="control-label">Item Pai</label>
													<input type="text" class="form-control input-sm" readonly="readonly" ng-model="currentNode.nme_completo"></input>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-sm-3">
												<div class="form-group" id="cod_plano">
													<label class="control-label">Código da Natureza</label>
													<input type="text" class="form-control input-sm" ng-model="planoConta.cod_plano" ng-disabled="editing">
												</div>
											</div>

											<div class="col-sm-9">
												<div class="form-group" id="dsc_plano">
													<label class="control-label">Descrição</label>
													<input type="text" class="form-control input-sm" ng-model="planoConta.dsc_plano">
												</div>
											</div>
										</div>

										<div class="row" ng-if="currentNode.children == ''">
											<div class="col-sm-3">
												<div class="form-group">
													<label class="control-label">Tipo de Lançamento</label>
													<select class="form-control" ng-change="loadModelosDRE(planoConta.flg_tipo_lancamento)" chosen allow-single-deselect="true" ng-model="planoConta.flg_tipo_lancamento" ng-options="item.value as item.dsc for item in tipo_lancamento">
													</select>
												</div>
											</div>
											<div class="col-sm-9" ng-if="planoConta.flg_tipo_lancamento != null">
												<div class="form-group">
													<label class="control-label">Modelo DRE</label>
													<select class="form-control" chosen option="modelo_dre" allow-single-deselect="true" ng-model="planoConta.id_modelo_dre" ng-options="item.id as item.descricao for item in modelo_dre">
													</select>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-sm-12 clearfix">
												<div class="pull-right">
													<button type="button" class="btn btn-sm btn-warning" ng-click="editar()" ng-hide="editing" ng-if="currentNode.children.length == 0">
														<i class="fa fa-edit"></i> Editar
													</button>
													<button type="button" class="btn btn-sm btn-danger" ng-click="delete(id_delete)" ng-show="editing">
														<i class="fa fa-trash-o"></i> Excluir
													</button>
													<button type="submit" class="btn btn-danger btn-sm" ng-click="reset();">
														<i class="fa fa-times-circle"></i> Cancelar
													</button>
													<button type="submit" class="btn btn-success btn-sm" ng-click="salvar()">
														<i class="fa fa-save"></i> Salvar
													</button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
					<!-- /panel -->

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

	<!-- Extras -->
	<script src="js/extras.js"></script>

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
	<script src="js/angular-controller/plano_contas-controller.js?<?php echo filemtime("js/angular-controller/plano_contas-controller.js")?>"></script>
	<?php include("google_analytics.php"); ?>
	</body>
</html>

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

  <body class="overflow-hidden" ng-controller="RelConsCxaBorderoController" ng-cloak>
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
			<div id="breadcrumb">
				<ul class="breadcrumb">
					 <li><i class="fa fa-home"></i> <a href="dashboard.php">Home</a></li>
					 <li class="active"><i class="fa fa-desktop"></i> Caixas</li>
					 <li class="active"><i class="fa fa-list-alt"></i> Conferência de Caixa</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-list-alt"></i> Conferência de Caixa</h3>
					<br/>
					<a class="btn btn-info" id="btn-novo"  ng-click="showBoxNovo()" ng-if="funcioalidadeAuthorized('inclusao_alteracao_produto')"><i class="fa fa-plus-circle"></i> Novo Produto</a>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md">
				<p class="text-muted alert alert-info" ng-show="showBoxEdit == true">
					<i class="fa fa-warning"></i>
					Ao confirmar o fechamento do caixa, os valores informados na coluna Conferido serão os valores apresentados no módulo Lançamentos Financeiros e no fluxo de caixa.
				</p>

				<div class="panel panel-primary hidden-print" ng-show="showBoxEdit == true">
					<div class="panel-heading">
						Conferência do Caixa #{{ borderoSelected.bordero.id }} <br/>
						Operador: {{ borderoSelected.bordero.nome }}<br/>
						Data do Movimento: {{ borderoSelected.bordero.dta_abertura }}
					</div>
					
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<div class="controls">
										<table class="table table-bordered table-hover table-striped table-condensed">
											<thead>
												<tr>
													<th class="text-middle text-center">
														<h5>Forma de Pagamento</h5>
													</th>
													<th class="text-middle text-center" width="180">
														<h5>Bandeira</h5>
													</th>
													<th class="text-middle text-center" width="180">
														<h5>Maquineta</h5>
													</th>
													<th class="text-middle text-center" width="120">
														<h5>Frente de Caixa</h5>
													</th>
													<th class="text-middle text-center" width="120">
														<h5>Operador</h5>
													</th>
													<th class="text-middle text-center" width="120">
														<h5>Conferido</h5>
													</th>
													<th class="text-middle text-center" width="120">
														<h5>Diferença</h5>
													</th>
												</tr>
											</thead>
											
											<tbody>
												<tr ng-repeat="lancamento in borderoSelected.lancamentos">
													<td class="text-middle" 
														colspan="{{ colspanFp(lancamento.id_forma_pagamento) }}">
														{{ lancamento.descricao_forma_pagamento }}
													</td>
													<td class="text-middle" 
														ng-if="canShowCardColumns(lancamento)">
														<div class="form-group" style="margin-bottom: 0px;">
															<select option="bandeiras" class="form-control input-sm"
																	ng-model="lancamento.id_bandeira"
																	ng-options="bandeira.id as bandeira.nome for bandeira in bandeiras"
																	ng-disabled="edit == 1">
															</select>
														</div>
													</td>
													<td class="text-middle" 
														ng-if="canShowCardColumns(lancamento)">
														<div class="form-group" style="margin-bottom: 0px;">
															<select option="maquinetas" class="form-control input-sm"
																	ng-model="lancamento.id_maquineta" 
																	ng-options="maquineta.id_maquineta as maquineta.num_serie_maquineta for maquineta in maquinetas"
																	ng-disabled="edit == 1">
															</select>
														</div>
													</td>
													<td class="text-middle text-right">
														R$ {{ lancamento.vlr_lancamento_sistema | numberFormat : 2 : ',' : '.' }}
													</td>
													<td class="text-middle text-right">
														R$ {{ lancamento.vlr_lancamento_operador | numberFormat : 2 : ',' : '.' }}
													</td>
													<td>
														<div class="form-group" style="margin-bottom: 0px;">
															<input type="text" class="form-control input-sm text-right"
																ng-model="lancamento.vlr_lancamento_conferencista"
																ng-change="calculateTotais()"
																thousands-formatter
																ng-disabled="edit == 1">
														</div>
													</td>
													<td class="text-middle text-right">
														<span ng-if="(lancamento.vlr_lancamento_conferencista - lancamento.vlr_lancamento_sistema) = 0">
															R$ {{ (lancamento.vlr_lancamento_conferencista - lancamento.vlr_lancamento_sistema) | numberFormat : 2 : ',' : '.' }}
														</span>
														<span ng-if="(lancamento.vlr_lancamento_conferencista - lancamento.vlr_lancamento_sistema) < 0" class="text-danger">
															R$ {{ (lancamento.vlr_lancamento_conferencista - lancamento.vlr_lancamento_sistema) | numberFormat : 2 : ',' : '.' }}
														</span>
														<span ng-if="(lancamento.vlr_lancamento_conferencista - lancamento.vlr_lancamento_sistema) > 0" class="text-success">
															R$ {{ (lancamento.vlr_lancamento_conferencista - lancamento.vlr_lancamento_sistema) | numberFormat : 2 : ',' : '.' }}
														</span>
													</td>
												</tr>
											</tbody>

											<tfoot>
												<tr>
													<td class="text-middle text-bold" colspan="3">
														<h4>Totais</h4>
													</td>
													<td class="text-middle text-bold text-right" width="120">
														<h4>R$ {{ totais.vlr_lancamento_sistema | numberFormat : 2 : ',' : '.' }}</h4>
													</td>
													<td class="text-middle text-bold text-right" width="120">
														<h4>R$ {{ totais.vlr_lancamento_operador | numberFormat : 2 : ',' : '.' }}</h4>
													</td>
													<td class="text-middle text-bold text-right" width="120">
														<h4>R$ {{ totais.vlr_lancamento_conferencista | numberFormat : 2 : ',' : '.' }}</h4>
													</td>
													<td class="text-middle text-bold text-right" width="120">
														<h4 ng-if="(totais.vlr_lancamento_conferencista - totais.vlr_lancamento_sistema) = 0">
															R$ {{ (totais.vlr_lancamento_conferencista - totais.vlr_lancamento_sistema) | numberFormat : 2 : ',' : '.' }}
														</h4>
														<h4 ng-if="(totais.vlr_lancamento_conferencista - totais.vlr_lancamento_sistema) < 0" class="text-danger">
															R$ {{ (totais.vlr_lancamento_conferencista - totais.vlr_lancamento_sistema) | numberFormat : 2 : ',' : '.' }}
														</h4>
														<h4 ng-if="(totais.vlr_lancamento_conferencista - totais.vlr_lancamento_sistema) > 0" class="text-success">
															R$ {{ (totais.vlr_lancamento_conferencista - totais.vlr_lancamento_sistema) | numberFormat : 2 : ',' : '.' }}
														</h4>
													</td>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-4">
								<div class="form-group">
									<label class="control-label">Conta Bancária p/ Destinação de Dinheiro</label>
									<select option="contas_bancarias" class="form-control input-sm"
											ng-model="borderoSelected.fechamento.id_conta_bancaria_destino" 
											ng-options="conta_bancaria.id as conta_bancaria.dsc_conta_bancaria for conta_bancaria in contas_bancarias" 
											ng-disabled="edit == 1">
									</select>
								</div>
							</div>
						</div>
					</div>

					<div class="panel-footer">
						<div class="clearfix">
							<div class="pull-right text-right">
								<button class="btn btn-default" ng-click="hideBoxEdit()">
									<i class="fa fa-times-circle"></i> Cancelar fechamento
								</button>
								<button class="btn btn-primary" ng-click="fecharBordero()" id="btn-salvar" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde, salvando" ng-disabled="edit == 1">
									<i class="fa fa-save"></i> Salvar e Fechar Caixa
								</button>
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-tasks"></i> Borderôs
					</div>
					<div class="panel-body">
						<table class="table table-bordered table-condensed table-striped table-hover">
							<thead>
								<tr>
									<td class="text-center text-bold">ID</td>
									<td class="text-center text-bold">Data Abertura</td>
									<td class="text-center text-bold">Data Fechamento</td>
									<td class="text-center text-bold">Operador</td>
									<td class="text-center text-bold" colspan="2">Ações</td>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="item in borderos">
									<td class="text-center">{{ item.id }}</td>
									<td class="text-center">{{ item.dta_abertura }}</td>
									<td class="text-center">{{ item.dta_fechamento }}</td>
									<td class="text-center">{{ item.nome }}</td>
									<td class="text-center">
										<span ng-if="item.dta_fechamento == null">
											<i class="fa fa-circle text-warning"></i>
										</span>
										<span ng-if="item.dta_fechamento != null">
											<i class="fa fa-circle text-danger"></i>
										</span>
									</td>
									<td class="text-center">
										<button class="btn btn-warning btn-xs" ng-click="editBordero(item)" ng-if="item.dta_fechamento == null">
											<i class="fa fa-edit"></i>
										</button>
										<button class="btn btn-primary btn-xs" ng-click="editBordero(item)" ng-if="item.dta_fechamento != null">
											<i class="fa fa-eye"></i>
										</button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="panel-footer clearfix">
						<div class="pull-right">
							<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.length > 1">
								<li ng-repeat="item in paginacao" ng-class="{'active': item.current}">
									<a href="" ng-click="getAllBorderos(item.offset,item.limit)">{{ item.index }}</a>
								</li>
							</ul>
						</div>
					</div>
				</div>

				<span ng-if="(msg_error)" 
					class="alert alert-{{ (status == 404) ? 'warning' : ((status == 500) ? 'danger' : '') }}">
					{{ msg_error }}
				</span>
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
    <script src="js/angular-controller/<?=(pathinfo(__FILE__, PATHINFO_FILENAME))?>.controller.js"></script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

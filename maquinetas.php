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
	<link rel="stylesheet" type="text/css" href="css/custom.css">
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

  <body class="overflow-hidden" ng-controller="MaquinetasController">
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
					 <li class="active"><i class="fa fa-fax"></i> Maquinetas</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-fax"></i> Maquinetas</h3>
					<br/>
					<a class="btn btn-info" id="btn-novo" ng-disabled="editing" ng-click="showBoxNovo()"><i class="fa fa-plus-circle"></i> Nova Maquineta</a>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md">
				<div class="alert alert-sistema" style="display:none"></div>

				<div class="panel panel-default" id="box-novo" style="display:none">
					<div class="panel-heading"><i class="fa fa-plus-circle"></i> Nova maquineta</div>

					<div class="panel-body">
						<form role="form">
							<div class="row">
								<div class="col-sm-1" ng-if="editing">
									<div class="form-group" id="num_serie_maquineta">
										<label class="control-label">identificação</label>
										<input value="#{{ maquineta.id_maquineta }}" ng-disabled="true" class="form-control"/>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group" id="num_serie_maquineta">
										<label class="control-label">Número de série</label>
										<input ng-model="maquineta.num_serie_maquineta" class="form-control"/>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group" id="id_conta_bancaria">
										<label class="control-label">Conta</label>
										<select  ng-model="maquineta.id_conta_bancaria" class="form-control">
										<option ng-repeat="item in contas" value="{{ item.id }}">{{item.dsc_conta_bancaria}} - {{item.nome_banco}}</option>
										</select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group" id="per_margem_debito">
										<label class="control-label">Margem débito</label>
										<input thousands-formatter  ng-model="maquineta.per_margem_debito" class="form-control"/>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<table class="table table-bordered table-condensed table-striped table-hover">
										<thead>
											<tr>
												<th colspan="3">Taxas de crédito</th>
												<th width="50" class="text-center">
													<button  ng-click="modalAddtaxa()" class="btn btn-xs btn-primary"><i class="fa fa-plus-circle"></i></button>
												</th>
											</tr>
										</thead>
										<tr ng-if="taxa_maquineta.length <= 0">
											<td class="text-center" colspan="6">
												Não há nenhuma taxa cadastrada.
											</td>
										</tr>
										<thead ng-show="taxa_maquineta.length > 0" >
											<tr>
												<th>Qtd. parcelas início</th>
												<th>Qtd. parcelas fim</th>
												<th>taxa</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<tr ng-repeat="item in taxa_maquineta">
												<td>{{ item.qtd_parcelas_inicio }}</td>
												<td>{{ item.qtd_parcelas_fim }}</td>
												<td>{{ item.prc_taxa | numberFormat:2:',':'.' }}%</td>
												<td align="center">
													<button type="button" ng-click="deleteTaxa($index,item)" tooltip="Excluir" class="btn btn-xs btn-danger delete" data-toggle="tooltip">
														<i class="fa fa-trash-o"></i>
													</button>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</form>
					</div>

					<div class="panel-footer clearfix">
						<div class="pull-right">
							<button ng-click="reset(true);" type="submit" class="btn btn-danger btn-sm">
								<i class="fa fa-times-circle"></i> Cancelar
							</button>
							<button ng-click="salvar()" type="submit" class="btn btn-success btn-sm">
								<i class="fa fa-save"></i> Salvar
							</button>
						</div>
					</div>
				</div><!-- /panel -->

				<div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-tasks"></i> Maquinetas Cadastradas</div>

					<div class="panel-body">
						<div class="row">
							<div class="col-sm-11">
								<div class="input-group">
						            <input ng-model="busca.text" type="text" class="form-control input-sm" ng-enter="loadMaquinetas(0,10)">
						            <div class="input-group-btn">
						            	<button ng-click="loadMaquinetas(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div>
						        </div>
							</div>
							<div class="col-sm-1">
								<button type="button" class="btn btn-sm btn-default" ng-click="resetFilter()">Limpar</button>
							</div>
						</div>

						<br>

						<div class="alert alert-delete" style="display:none"></div>
						<table class="table table-bordered table-condensed table-striped table-hover">
							<tr ng-if="contas.length <= 0">
								<td class="text-center" colspan="6">
									Não há contas cadastradas .
								</td>
							</tr>
							<thead ng-show="contas.length > 0 && conta != null" >
								<tr>
									<th>Identificação</th>
									<th>Número de série</th>
									<th>Conta</th>
									<th width="80" style="text-align: center;">Opções</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="item in maquinetas">
									<td>#{{ item.id_maquineta }}</td>
									<td>{{ item.num_serie_maquineta }}</td>
									<td>{{ item.dsc_conta_bancaria }}</td>
									<td align="center">
										<button type="button" ng-click="editar(item)" tooltip="Editar" class="btn btn-xs btn-warning" data-toggle="tooltip">
											<i class="fa fa-edit"></i>
										</button>
										<button type="button" ng-click="delete(item)" tooltip="Excluir" class="btn btn-xs btn-danger delete" data-toggle="tooltip">
											<i class="fa fa-trash-o"></i>
										</button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="panel-footer clearfix">
						<div class="pull-right">
							<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.conta.length > 1">
								<li ng-repeat="item in paginacao.conta" ng-class="{'active': item.current}">
									<a href="" h ng-click="loadContas(item.offset,item.limit)">{{ item.index }}</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /main-container -->

		<!-- Modal Add Taxas  -->
		<div class="modal fade" id="modal-add-taxa" style="display:none">
  			<div class="modal-dialog error modal-md">
    			<div class="modal-content">
      				<div class="modal-header">
						<h4>taxa</h4>
      				</div>

				    <div class="modal-body">
				    	<div class="alert alert-add-taxa" style="display:none"></div>

				    	<div class="row">
				    		<div class="col-sm-4" id="inicio_taxa">
				    			<label class="control-label">Qtd. parcelas início</label>
				    			<div class="form-group ">
				    					<input ng-model="nova_taxa.qtd_parcelas_inicio"  type="text"  class="form-control input-sm" >
				    			</div>
				    		</div>
				    		<div class="col-sm-4" id="fim_taxa">
				    			<label class="control-label">Qtd. parcelas fim</label>
				    			<div class="form-group ">
				    					<input ng-model="nova_taxa.qtd_parcelas_fim"  type="text"  class="form-control input-sm" >
				    			</div>
				    		</div>
				    		<div class="col-sm-4" id="prc_taxa">
				    			<label class="control-label">Taxa</label>
				    			<div class="form-group ">
				    					<input ng-model="nova_taxa.prc_taxa" thousands-formatter type="text"  class="form-control input-sm" >
				    			</div>
				    		</div>

				    	</div>
				    </div>

				    <div class="modal-footer">
				    	<button type="button" data-loading-text=" Aguarde..." class="btn btn-block btn-md btn-success"
				    		id="btn-aplicar-sangria" ng-click="addtaxa()">
				    		<i class="fa fa-minus-circle"></i> inserir
				    	</button>

				    	<button type="button" data-loading-text=" Aguarde..."
				    		class="btn btn-block btn-md btn-default" ng-click="cancelarModal('#modal-add-taxa');" id="btn-aplicar-sangria">
				    		<i class="fa fa-times-circle"></i> Cancelar
				    	</button>
				    </div>
			  	</div>
			  	<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->



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
	<script src="js/angular-controller/maquinetas-controller.js"></script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

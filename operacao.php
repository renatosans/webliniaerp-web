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

	<!-- Chosen -->
	<link href="css/chosen/chosen.min.css" rel="stylesheet"/>

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">

	<link href="css/custom.css" rel="stylesheet">

	<style type="text/css">
		.panel.panel-default {
		    overflow: visible !important;
		}
		/* Fix for Bootstrap 3 with Angular UI Bootstrap */

		.modal {
			display: block;
		}
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

  <body class="overflow-hidden" ng-controller="OperacaoController" ng-cloak>
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
					<li><i class="fa fa-home"></i><a href="dashboard.php">Home</a></li>
					<li><i class="fa fa-building-o"></i> Empreendimento</li>
					<li><i class="fa fa-cog"></i> <a href="empreendimento_config.php">Configurações</a></li>
					<li class="active"><i class="fa fa-tags"></i> Operação </li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-tags"></i> Operação</h3>
					<br/>
					<a class="btn btn-info" id="btn-novo" ng-disabled="editing" ng-click="showBoxNovo()"><i class="fa fa-plus-circle"></i> Nova Operação</a>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md">
				<div class="alert alert-sistema" style="display:none"></div>

				<div class="panel panel-default" id="box-novo" style="display:none">
					<div class="panel-heading"><i class="fa fa-plus-circle"></i> Nova Operação</div>

					<div class="panel-body">
						<div  class="row">
							<div class="col-sm-12">
								<div id="dsc_operacao" class="form-group">
									<label class="control-label">Descrição <span style="color:red;font-weight: bold;">*</span></label>
									<input type="text" class="form-control" ng-model="operacao.dsc_operacao">
								</div>
							</div>
						</div>
						<div  class="row">
					</div>

					<div class="row">
							<div class="col-sm-12">
								<div class="form-group" id="num_cfop_produto">
									<label class="control-label">N° CFOP Produto</label> 
									<select chosen
								    option="lista_cfop"
								    allow-single-deselect="true"
								    ng-model="operacao.num_cfop_produto"
								    ng-options="cfop.num_item as cfop.dsc_completa for cfop in lista_cfop">
									</select>
								</div>
								<!--<div id="num_cfop_produto" class="form-group">
									<label class="control-label">N° CFOP Produto</label>
									<input maxlength="4" onKeyPress="return SomenteNumero(event);" type="text" class="form-control" ng-model="operacao.num_cfop_produto">
								</div>-->
							</div>
						</div>

						<div class="row">
							<div class="col-sm-12">
								<div class="form-group" id="num_cfop_produto_st">
									<label class="control-label">N° CFOP Produto ST</label> 
									<select chosen
								    option="lista_cfop"
								    allow-single-deselect="true"
								    ng-model="operacao.num_cfop_produto_st"
								    ng-options="cfop.num_item as cfop.dsc_completa for cfop in lista_cfop">
									</select>
								</div>
								<!--<div id="num_cfop_produto_st" class="form-group">
									<label class="control-label">N° CFOP Produto ST</label>
									<input  maxlength="4" onKeyPress="return SomenteNumero(event);" type="text" class="form-control" ng-model="operacao.num_cfop_produto_st">
								</div>-->
							</div>
						</div>

						<div class="row">
							<div class="col-sm-12">
								<div class="form-group" id="num_cfop_mercadoria">
									<label class="control-label">N° CFOP Mercadoria</label> 
									<select chosen
								    option="lista_cfop"
								    allow-single-deselect="true"
								    ng-model="operacao.num_cfop_mercadoria"
								    ng-options="cfop.num_item as cfop.dsc_completa for cfop in lista_cfop">
									</select>
								</div>
								<!--<div id="num_cfop_mercadoria" class="form-group">
									<label class="control-label">N° CFOP Mercadoria</label>
									<input  maxlength="4" onKeyPress="return SomenteNumero(event);" type="text" class="form-control" ng-model="operacao.num_cfop_mercadoria">
								</div>-->
							</div>
						</div>

						<div class="row">
							<div class="col-sm-12">
								<div class="form-group" id="num_cfop_mercadoria_st">
									<label class="control-label">N° CFOP Mercadoria ST</label> 
									<select chosen
								    option="lista_cfop"
								    allow-single-deselect="true"
								    ng-model="operacao.num_cfop_mercadoria_st"
								    ng-options="cfop.num_item as cfop.dsc_completa for cfop in lista_cfop">
									</select>
								</div>
								<!--<div id="num_cfop_mercadoria_st" class="form-group">
									<label class="control-label">N° CFOP Mercadoria ST</label>
									<input  maxlength="4" onKeyPress="return SomenteNumero(event);" type="text" class="form-control" ng-model="operacao.num_cfop_mercadoria_st">
								</div>-->
							</div>
						</div>

						<div class="row">
							<div class="col-sm-12">
								<div class="form-group" id="num_cfop_mercadoria_st">
									<label class="control-label">N° CFOP Mercadoria ST Substituído</label> 
									<select chosen
								    option="lista_cfop"
								    allow-single-deselect="true"
								    ng-model="operacao.num_cfop_mercadoria_st_subst"
								    ng-options="cfop.num_item as cfop.dsc_completa for cfop in lista_cfop">
									</select>
								</div>
								<!--<div id="num_cfop_mercadoria_st" class="form-group">
									<label class="control-label">N° CFOP Mercadoria ST</label>
									<input  maxlength="4" onKeyPress="return SomenteNumero(event);" type="text" class="form-control" ng-model="operacao.num_cfop_mercadoria_st">
								</div>-->
							</div>
						</div>

						<div  class="row">
							<div class="col-sm-6">
								<div class="form-group" id="cod_operacao_estorno">
									<label class="control-label">Operação Para Estorno</label> 
									<select chosen ng-change="ClearChosenSelect('cod_regime_tributario')"
								    option="chosen_operacao"
								    allow-single-deselect="true"
								    ng-model="operacao.cod_operacao_estorno"
								    ng-options="operacao.cod_operacao as operacao.dsc_operacao for operacao in chosen_operacao" change-chosen>
									</select>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group" id="cod_operacao_devolucao">
									<label class="control-label">Operação Para Devolução</label> 
									<select chosen ng-change="ClearChosenSelect('cod_regime_tributario')"
								    option="chosen_operacao"
								    allow-single-deselect="true"
								    ng-model="operacao.cod_operacao_devolucao"
								    ng-options="operacao.cod_operacao as operacao.dsc_operacao for operacao in chosen_operacao">
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4">
								<div class="form-group" id="num_local_destino">
									<label class="control-label">Local de Destino</label> 
									<select ng-disabled="processando_autorizacao || autorizado" chosen
									    option="lista_local_destino"
									    ng-model="operacao.num_local_destino"
									    ng-options="item.num_item as item.nme_item for item in lista_local_destino">
									</select>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group" id="num_finalidade_emissao">
									<label class="control-label">Finalidade Emissão</label> 
									<select ng-disabled="processando_autorizacao || autorizado" chosen
									    option="lista_finalidade_emissao"
									    ng-model="operacao.num_finalidade_emissao"
									    ng-options="item.num_item as item.nme_item for item in lista_finalidade_emissao">
									</select>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group" id="num_consumidor_final">
									<label class="control-label">Consumidor Final</label> 
									<select ng-disabled="processando_autorizacao || autorizado" chosen
									    option="lista_consumidor_final"
									    ng-model="operacao.num_consumidor_final"
									    ng-options="item.num_item as item.nme_item for item in lista_consumidor_final">
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4">
								<div class="form-group" id="num_tipo_documento">
									<label class="control-label">Tipo de Documento</label> 
									<select ng-disabled="processando_autorizacao || autorizado" chosen
									    option="lista_tipo_documento"
									    allow-single-deselect="true"
									    ng-model="operacao.num_tipo_documento"
									    ng-options="documento.num_item as documento.nme_item for documento in lista_tipo_documento">
									</select>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group" id="num_presenca_comprador">
									<label class="control-label">Presença Comprador</label> 
									<select ng-disabled="processando_autorizacao || autorizado" chosen
									    option="lista_presenca_comprador"
									    ng-model="operacao.num_presenca_comprador"
									    ng-options="item.num_item as item.nme_item for item in lista_presenca_comprador">
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<div class="pull-right">
									<button ng-click="showBoxNovo(); reset();" type="submit" class="btn btn-danger btn-sm">
										<i class="fa fa-times-circle"></i> Cancelar
									</button>
									<button  ng-click="salvar()" id="salvar-operacao" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." type="button" class="btn btn-success btn-sm">
										<i class="fa fa-save"></i> Salvar
									</button>
								</div>
							</div>
						</div>

					</div>
				</div><!-- /panel -->

				<div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-tasks"></i> Operações Cadastradas</div>

					<div class="panel-body">
						<div  class="alert alert-list" style="display:none"></div>
						<table class="table table-bordered table-condensed table-striped table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Descrição</th>
									<th width="80" style="text-align: center;">Opções</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="item in operacoes" bs-tooltip>
									<td width="80">{{ item.cod_operacao }}</td>
									<td>{{ item.dsc_operacao }}</td>
									<td align="center">
										<button type="button" ng-click="editar(item)" tooltip="Editar" title="Editar" class="btn btn-xs btn-warning" data-toggle="tooltip">
											<i class="fa fa-edit"></i>
										</button>
										<button type="button" ng-click="delete(item)" tooltip="Excluir" title="Excluir" class="btn btn-xs btn-danger delete" data-toggle="tooltip">
											<i class="fa fa-trash-o"></i>
										</button>
									</td>
								</tr>
								<tr>
									<td colspan="3" class="text-center" ng-if="operacoes.length == 0 && operacoes != null">
										Nenhuma Operação Encontrada
									</td>
								</tr>
								<tr>
									<td colspan="3" class="text-center" ng-if="operacoes == null">
										<i class='fa fa-refresh fa-spin'></i> Carregando
									</td>
								</tr>
							</tbody>
						</table>	
						<div class="pull-right">
							<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.operacao.length > 1">
								<li ng-repeat="item in paginacao.operacao" ng-class="{'active': item.current}">
									<a href="" h ng-click="load(item.offset,item.limit)">{{ item.index }}</a>
								</li>
							</ul>
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
	<script src="//cdnjs.cloudflare.com/ajax/libs/angular-strap/2.1.2/angular-strap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/angular-strap/2.1.2/angular-strap.tpl.min.js"></script>
	<script type="text/javascript" src="bower_components/angular-ui-utils/mask.min.js"></script>
    <script src="js/angular-sanitize.min.js"></script>
    <script src="js/ui-bootstrap-tpls-0.6.0.js" type="text/javascript"></script>
    <script src="js/dialogs.v2.min.js" type="text/javascript"></script>
    <script src="js/auto-complete/ng-sanitize.js"></script>
    <script src="js/angular-chosen.js"></script>
    <script type="text/javascript">
    	var addParamModule = ['angular.chosen'] ;
    </script>
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/operacao-controller.js"></script>
	<script type="text/javascript"></script>>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

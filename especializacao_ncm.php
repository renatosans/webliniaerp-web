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

		@media screen and (min-width: 768px) {

			#list_proodutos.modal-dialog  {width:900px;}

		}

		#list_produtos .modal-dialog  {width:70%;}

		#list_produtos .modal-content {min-height: 640px;;}


	</style>
  </head>

  <body class="overflow-hidden" ng-controller="EspecializacaoNcmController" ng-cloak>
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
					<li class="active"><i class="fa fa-tags"></i> Especialização NCM</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-tags"></i> Especialização NCM </h3>
					<br/>
					<a class="btn btn-info" id="btn-novo" ng-disabled="editing" ng-click="showBoxNovo()"><i class="fa fa-plus-circle"></i> Nova Especialização NCM </a>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md">
				<div class="alert alert-sistema" style="display:none"></div>

				<div class="panel panel-default" id="box-novo" style="display:none">
					<div class="panel-heading"><i  ng-class="{'fa fa-edit':editing,'fa fa-plus-circle':editing==false}"></i> {{ editing && 'Editar Especialização NCM'  || 'Nova Especialização NCM' }}  </div>

					<div class="panel-body">
						<div  class="row">
							<div class="col-sm-6">
								<div id="dsc_especializacao_ncm" class="form-group">
									<label class="control-label">Descrição</label>
									<input type="text" class="form-control" ng-model="especializacao_ncm.dsc_especializacao_ncm">
								</div>
							</div>
							<div class="col-sm-5" id="cod_ncm">
								<label class="control-label">NCM</label>
								<div class="input-group">
									<input ng-click="selNcm()" type="text" class="form-control" ng-model="especializacao_ncm.ncm_view" readonly="readonly" style="cursor: pointer;" />
									<span class="input-group-btn">
										<button ng-click="selNcm()"  type="button" class="btn"><i class="fa-search fa"></i></button>
									</span>
								</div>
							</div>
							<div class="col-sm-1" id="dsc_especializacao_ncm">
								<div  class="form-group">
									<label class="control-label">Ex Tipi</label>
									<input type="text" class="form-control" onKeyPress="return SomenteNumero(event);" ng-model="especializacao_ncm.ex_tipi">
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-12">
								<div class="pull-right">
									<button ng-click="showBoxNovo(); reset();" type="submit" class="btn btn-danger btn-sm">
										<i class="fa fa-times-circle"></i> Cancelar
									</button>
									<button  ng-click="salvar()" id="salvar-especializacao-ncm" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." type="button" class="btn btn-success btn-sm">
										<i class="fa fa-save"></i> Salvar
									</button>
								</div>
							</div>
						</div>

					</div>
				</div><!-- /panel -->

				<div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-tasks"></i> Especialização NCM Cadastradas</div>

					<div class="panel-body">
						<div  class="alert alert-list" style="display:none"></div>
						<table class="table table-bordered table-condensed table-striped table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Descrição</th>
									<th>NCM</th>
									<th>Ex Tipi</th>
									<th width="80" style="text-align: center;">Opções</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="item in lista_especializacao_ncm" bs-tooltip>
									<td>{{ item.cod_especializacao_ncm }}</td>
									<td>{{ item.dsc_especializacao_ncm }}</td>
									<td>{{ item.cod_ncm }}</td>
									<td>{{ item.ex_tipi == 0 && '' || item.ex_tipi }}</td>
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
									<td colspan="5" class="text-center" ng-if="lista_especializacao_ncm.length == 0 && lista_especializacao_ncm != null">
										Nenhum Regime Especial Encontrado 
									</td>
								</tr>
								<tr>
									<td colspan="5" class="text-center" ng-if="lista_especializacao_ncm == null">
										<i class='fa fa-refresh fa-spin'></i> Carregando
									</td>
								</tr>
							</tbody>
						</table>	
						<div class="pull-right">
							<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.lista_especializacao_ncm.length > 1">
								<li ng-repeat="item in paginacao.lista_especializacao_ncm" ng-class="{'active': item.current}">
									<a href="" h ng-click="load(item.offset,item.limit)">{{ item.index }}</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /main-container -->

		<!-- /Modal Vendedor -->
		<div class="modal fade" id="list-ncm" style="display:none">
  			<div class="modal-dialog modal-lg">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>NCM</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.ncm"  ng-enter="loadNcm(0,10)" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadNcm(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div> <!-- /input-group-btn -->
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>
						<br />
						<div class="row">
							<div class="col-sm-12">
								<table class="table table-bordered table-condensed table-striped table-hover">
									<tr ng-if="lista_ncm != false && (lista_ncm.length <= 0 || lista_ncm == null)">
										<th class="text-center" colspan="9" style="text-align:center"><strong>Carregando</strong><img src="assets/imagens/progresso_venda.gif"></th>
									</tr>
									<tr ng-if="lista_ncm == false">
										<th class="text-center" colspan="9" colspan="9" style="text-align:center">Não a resultados para a busca</th>
									</tr>
									<thead ng-show="(lista_ncm.length != 0)">
										<tr>
											<th >NCM</th>
											<th  width="56">EX TIPI</th>
											<th >Descrição</th>
											<th >Perc. IPI</th>
											<th colspan="2">selecionar</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in lista_ncm">
											<td>{{ item.cod_ncm }}</td>
											<td ng-if="item.ex_tipi == 0"></td>
											<td ng-if="item.ex_tipi > 0">{{ item.ex_tipi  }}</td>
											<td>{{ item.dsc_ncm | limitTo : 95 : 0 }} <a href="" style="text-decoration: underline;color: #000;" ng-if="item.dsc_ncm.length > 95">...</a></td>
											<td ng-if="item.num_percentual_ipi !=null">{{ item.num_percentual_ipi | numberFormat:2:',':'.' }}%</td>
											<td ng-if="item.num_percentual_ipi ==null">NT</td>
											<td width="50" align="center">
												<button type="button" class="btn btn-xs btn-success" ng-click="changeNcm(item)">
													<i class="fa fa-check-square-o"></i> Selecionar
												</button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>

						<div class="row">
				    		<div class="col-sm-12">
				    			<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao.especializacao_ncm.length > 1">
									<li ng-repeat="item in paginacao.especializacao_ncm" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadNcm(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
				    		</div>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
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
	<script src="js/angular-controller/especializacao_ncm-controller.js"></script>
	<script type="text/javascript"></script>>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

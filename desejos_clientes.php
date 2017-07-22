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

	<!-- Datepicker -->
	<link href="css/datepicker.css" rel="stylesheet"/>

	<!-- Timepicker -->
	<link href="css/bootstrap-timepicker.css" rel="stylesheet"/>

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">
	<style type="text/css">

		/* Fix for Bootstrap 3 with Angular UI Bootstrap */

		.has-error-plano{
			border: 1px solid #b94a48;
			background: #E5CDCD;
		}

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

  <body class="overflow-hidden" ng-controller="DesejosClienteController" ng-cloak>
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
		</div>
		<!-- /top-nav-->

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
					 <li><i class="fa fa-home"></i> <a href="dashboard.php"> Home</a></li>
					 <li><i class="fa fa-users"></i> <a href="clientes.php"> Clientes/Usuários</a></li>
					 <li class="active"><i class="fa fa-paste"></i> Desejos de Clientes</li>
				</ul>
			</div>
			<!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-paste"></i> Desejos de Clientes</h3>
				</div>
			</div>
			<!-- /main-header -->

			<div class="padding-md">
				<div class="alert alert-sistema" style="display:none"></div>

				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-tasks"></i> Vendas
					</div>

					<div class="panel-body">
						<div class="row">
							<div class="col-sm-11">
								<div class="input-group">
						            <input ng-model="busca.text" type="text" class="form-control input-sm" ng-enter="load(0,10)">
						            <div class="input-group-btn">
						            	<button ng-click="load(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
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
						<table class="table table-condensed table-bordered table-striped table-hover">
							<thead>
								<tr>
									<th class="text-center" width="30">#</th>
									<th class="text-center">Cliente</th>
									<th class="text-center">Produto</th>
									<th class="text-center">Fabricante</th>
									<th class="text-center" width="80">Tamanho</th>
									<th class="text-center" width="110">Qtd. Solicitada</th>
									<th class="text-center" width="120">Sabor Desejado</th>
									<th class="text-center" width="110">Dt. Solicitação</th>
									<th class="text-center" width="80">
										<label class="label-checkbox">
											<input type="checkbox" id="toggleLine" ng-model="allSelected" ng-click="selectAll()">
											<span class="custom-checkbox"></span>  Excluir
										</label>
									</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="item in desejos">
									<td>{{ item.id }}</td>
									<td>{{ item.cliente }}</td>
									<td>{{ item.nome }}</td>
									<td>{{ item.nome_fabricante }}</td>
									<td class="text-center">{{ item.peso }}</td>
									<td class="text-center">{{ item.qtd }}</td>
									<td class="text-center">{{ item.sabor_desejado }}</td>
									<td class="text-center">{{ item.data_enviado | dateFormat: 'date' }}</td>
									<td class="text-center">
										<label class="label-checkbox">
											<input type="checkbox" id="toggleLine" ng-model="item.flgExcluir" ng-change="select(item)">
											<span class="custom-checkbox"></span>
										</label>
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="panel-footer clearfix">
						<div class="pull-left">
							<button type="button" class="btn btn-sm btn-danger" ng-if="selected" ng-click="excluir()"><i class="fa fa-trash-o"></i> Excluir Selecionados</button>
						</div>

						<div class="pull-right">
							<ul class="pagination pagination-sm m-top-none">
								<li ng-repeat="item in paginacao.desejos" ng-class="{'active': item.current}">
									<a href="" ng-click="loadDesejos(item.offset,item.limit)">{{ item.index }}</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /main-container -->

		<!-- /Modal clientes-->
		<div class="modal fade" id="list_clientes" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Detalhes da Venda</h4>
						<p class="muted">Cliente : {{ venda.nme_cliente }}</p>
						<p class="muted">Venda #{{ venda.id }}</p>
      				</div>
				    <div class="modal-body">
				   		<div class="row">
				   			<div class="col-sm-12">
				   				<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(clientes.length != 0)">
										<tr>
											<th class="text-center">Produto</th>
											<th class="text-center" width="50">Qtd</th>
											<th class="text-center" width="70">Valor</th>
											<th class="text-center" colspan="2" width="60">Desconto</th>
											<th class="text-center" width="100">Subtotal</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in detalhes">
											<td>{{ item.nome_produto }}</td>
											<td class="text-center">{{ item.qtd }}</td>
											<td class="text-right">R$ {{ item.valor_real_item  | numberFormat:2:',':'.' }}</td>
											<td class="text-center" width="60">{{ item.valor_desconto * 100 | numberFormat:2:',':'.' }}%</td>
											<td class="text-center" width="20">
												<i class="fa fa-dot-circle-o text-{{item.css_cor}}"
													ng-if="item.css_cor.length > 0"
													tooltip="Desconto maior ou igual (>=) que {{ item.perc_desconto_min }}% e menor ou igual (<=) que {{ item.perc_desconto_max }}%"></i>
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

		<!-- /Modal clientes-->
		<div class="modal fade" id="list_clientes" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>clientes</h4>
      				</div>
				    <div class="modal-body">
				    	<ul class="pagination pagination-xs m-top-none pull-right">
							<li ng-repeat="item in paginacao_clientes" ng-class="{'active': item.current}">
								<a href="" h ng-click="loadCliente(item.offset,item.limit)">{{ item.index }}</a>
							</li>
						</ul>
						</br></br>
				   		<table class="table table-bordered table-condensed table-striped table-hover">
							<thead ng-show="(clientes.length != 0)">
								<tr>
									<th>#</th>
									<th>nome</th>
									<th>perfil</th>
									<th>selecionar</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="item in clientes">
									<td>{{ item.id }}</td>
									<td>{{ item.nome }}</td>
									<th>{{ item.nome_perfil }}</th>
									<td>
									<button ng-click="addCliente(item)" style="margin-bottom:10px" class="btn btn-success btn-xs" type="button">
											<i class="fa fa-plus-circle fa-lg"></i>
									</button>
									</td>
								</tr>
							</tbody>
						</table>
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

	<!-- Chosen -->
	<script src='js/chosen.jquery.min.js'></script>

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

	<!-- Moment -->
	<script src="js/moment/moment.min.js"></script>

	<script src="js/jquery.noty.packaged.js"></script>

	<!-- Extras -->
	<script src="js/extras.js"></script>

	<!-- Mascaras para o formulario de produtos -->
	<script src="js/scripts/mascaras.js"></script>

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
	<script src="js/angular-controller/desejos_clientes-controller.js"></script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

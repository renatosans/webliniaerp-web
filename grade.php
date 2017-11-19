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

  <body class="overflow-hidden" ng-controller="GradeController" ng-cloak>
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

				<?php include_once('menu-modulos.php') ?>
				
			</div><!-- /sidebar-inner -->
		</aside>

		<div id="main-container">
			<div id="breadcrumb">
				<ul class="breadcrumb">
					 <li><i class="fa fa-home"></i> <a href="dashboard.php">Home</a></li>
					 <li class="active"><i class="fa fa-th"></i> Vitrine de Produtos</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-th"></i> Vitrine de Produtos</h3>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md">
				<div class="alert alert-sistema" style="display:none"></div>
				<div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-tasks"></i> Produtos Disponíveis na Vitrine Virtual</div>

					<div class="panel-body">
						<div class="row">
							<div class="col-sm-11">
								<div class="input-group">
						            <input type="text" class="form-control input-sm"
						            	ng-model="busca.produtos"
						            	ng-enter="loadProdutos(0,10)" 
						            	ng-keyup="loadProdutos(0,10)"
						            	>
						            <div class="input-group-btn">
						            	<button ng-click="loadProdutos(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div>
						        </div>
							</div>
							<div class="col-sm-1">
								<button type="button" class="btn btn-sm btn-default" ng-click="busca.produtos='';loadProdutos(0,10)">Limpar</button>
							</div>
						</div>

						<br>
						<div class="row">
							<div class="col-sm-12">
								<table class="table table-bordered table-condensed table-striped table-hover">
									<thead>
										<tr>
											<th class="text-center">#</th>
											<th class="text-center">Descrição</th>
											<th class="text-center">Fabricante</th>
											<th class="text-center" width="80">Tamanho</th>
											<th class="text-center" >Sabor/Cor</th>
											<th class="text-center" width="100" ng-if="existeTabelaPreco('atacado')">Vlr. Atacado</th>
											<th class="text-center" width="100" ng-if="existeTabelaPreco('intermediario')">Vlr. Interm.</th>
											<th class="text-center" width="100" ng-if="existeTabelaPreco('varejo')">Vlr. Varejo</th>
											<th width="80" style="text-align: center;"><button class="btn btn-xs btn-primary" ng-click="showProdutos()"><i class="fa fa-plus-circle"></i> Adicionar Item</button></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in produtos">
											<td class="text-center" width="80">{{ item.id_produto }}</td>
											<td>{{ item.nome}}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td class="text-center">{{ item.peso }}</td>
											<td class="text-center">{{ item.sabor }}</td>
											<td class="text-right" ng-if="existeTabelaPreco('atacado')">R$ {{ item.vlr_venda_atacado | numberFormat: 2 : ',' : '.' }}</td>
											<td class="text-right" ng-if="existeTabelaPreco('intermediario')">R$ {{ item.vlr_venda_intermediario | numberFormat: 2 : ',' : '.' }}</td>
											<td class="text-right" ng-if="existeTabelaPreco('varejo')">R$ {{ item.vlr_venda_varejo | numberFormat: 2 : ',' : '.' }}</td>
											<td align="center">
												<button type="button" ng-click="delete(item)" tooltip="Excluir" class="btn btn-xs btn-danger delete" data-toggle="tooltip">
													<i class="fa fa-trash-o"></i> Remover item
												</button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="panel-footer clearfix">
						<div class="pull-right">
							<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.itens.length > 1">
								<li ng-repeat="item in paginacao.itens" ng-class="{'active': item.current}">
									<a href="" h ng-click="loadProdutos(item.offset,item.limit)">{{ item.index }}</a>
								</li>
							</ul>
						</div>
					</div>

				</div>
			</div>
		</div><!-- /main-container -->

		<!-- /Modal Produtos-->
		<div class="modal fade" id="list_produtos" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Produtos</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-sm-12">
								<div class="input-group">
						            <input ng-model="busca.produtosModal" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadProdutosModal(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button"><i class="fa fa-search"></i> Buscar</button>
						            </div> <!-- /input-group-btn -->
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>

						<br>

						<div class="row">
							<div class="col-md-12">
								<div class="alert alert-produtos" style="display:none"></div>
						   		<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(produtosModal.length != 0)">
										<tr>
											<th>#</th>
											<th>Nome</th>
											<th>Fabricante</th>
											<th class="text-center" >Tamanho</th>
											<th class="text-center" >Sabor/Cor</th>
											<th width="80"></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(produtosModal.length == 0)">
											<td colspan="6">Não a Produtos cadastrados</td>
										</tr>
										<tr ng-show="produtosModal.length == null">
											<td colspan="6" class="text-center"><i class='fa fa-refresh fa-spin'></i> Carregando...</td>
										</tr>
										<tr ng-repeat="item in produtosModal">
											<td>{{ item.id }}</td>
											<td>{{ item.nome }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td class="text-center">{{ item.peso }}</td> 
											<td class="text-center">{{ item.sabor }}</td>
											<td>
												<button ng-click="addProduto(item)" class="btn btn-success btn-xs" type="button">
													<i class="fa fa-check-square-o"></i> Adicionar
												</button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>

					    <div class="row">
					    	<div class="col-md-12">
								<div class="input-group pull-right">
						             <ul class="pagination pagination-xs m-top-none" ng-show="paginacao.produtosModal.length > 1">
										<li ng-repeat="item in paginacao.produtosModal" ng-class="{'active': item.current}">
											<a href="" ng-click="loadProdutosModal(item.offset,item.limit)">{{ item.index }}</a>
										</li>
									</ul>
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
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
	<script src="js/angular-controller/grade-controller.js"></script>
	<script type="text/javascript">
		//$(".chzn-select").chosen();
		$('.foto-produto').change(function()	{
			var filename = $(this).val().split('\\').pop();
			$(this).parent().find('span').attr('data-title',filename);
			$(this).parent().find('label').attr('data-title','Trocar foto');
			$(this).parent().find('label').addClass('selected');
		});
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

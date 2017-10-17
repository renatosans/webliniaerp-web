<?php
	include_once "util/login/restrito.php";
	//restrito(array(1));
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

	<!-- Datepicker -->
	<link href="css/datepicker.css" rel="stylesheet"/>

	<!-- Timepicker -->
	<link href="css/bootstrap-timepicker.css" rel="stylesheet"/>

	<!-- Chosen -->
	<link href="css/chosen/chosen.min.css" rel="stylesheet"/>
	<link href="bower_components/angular-ui-switch/angular-ui-switch.min.css" rel="stylesheet"/>

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">

	<style type="text/css">
		.panel.panel-default {
		    overflow: visible !important;
		}	
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

		.datepicker {
  		  z-index: 100000;
		}

		.panel.middle-frame {
	      margin-bottom: 10px;
	    }

	    .panel.middle-frame div.container {
	      min-height: 90px;
	      padding-left: 0px !important;
	      padding-right: 0px !important;
	      width: 100%;
	    }

	    .panel.middle-frame div.container img {
	      margin: 0 auto;
	      max-height: 100px;
	      padding-top: 10px;
	    }

	    .panel.middle-frame div.container span {
	      line-height: 20px;
	      vertical-align: middle;
	      display: inline-block;
	    }

	    .product-name, 
	    .product-price {
	      display: block !important;
	    }

	    .product-price {
	      font-weight: bold;
	      font-size: 1.7em;
	      margin-top: 10px;
	    }

	    .grade-produtos {
	    	max-height: 500px;
	    	overflow-y: scroll;
	    	overflow-x: hidden;
	    }

	    .error-estoque td {
			background: #FF9191
		}
	</style>
  </head>

  <body class="overflow-hidden" ng-controller="PdvSimpleController" ng-cloak>
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
				<div id="breadcrumb">
				<ul class="breadcrumb">
					 <li><i class="fa fa-home"></i> <a href="dashboard.php">Home</a></li>
					 <li><i class="fa fa-signal"></i> <a href="vendas.php">Vendas</a></li>
					 <li class="active"><i class="fa fa-desktop"></i> Frente de Caixa (PDV)</li>
				</ul>
			</div>
			</div><!-- breadcrumb -->

			<div class="padding-md" ng-if="statusConfig==false">
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-12">
								<div class="alert alert-danger">
									Não exsite as configurações necessarias para funcionamento do SIMPLE PDV
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
					

			<div class="padding-md" ng-if="statusConfig">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title clearfix">
							<i class="fa fa-desktop"></i> Frente de Caixa (PDV)
							<div class="pull-right">
								<button type="button" class="btn btn-xs btn-default" 
									ng-click="voltarListaCategorias()"
									ng-if="tipo_view == 'grade-produtos'">
									<i class="fa fa-arrow-left"></i> Voltar
								</button>
								<button type="button" class="btn btn-xs btn-default" ng-click="resizeScreen()">
									<i class="fa fa-arrows-alt"></i> Tela Inteira
								</button>
								<div class="btn-group btn-group-xs" role="group" aria-label="Opções de Visualização">
									<button type="button" class="btn btn-default" ng-class="{'btn-default':tipo_view == 'lista','btn-primary':tipo_view == 'grade-produtos'}" ng-click="changeTipoView('grade')">Grade</button>
									<button type="button" class="btn btn-default" ng-class="{'btn-default':tipo_view == 'grade-produtos','btn-primary':tipo_view == 'lista'}" ng-click="changeTipoView('lista')">Lista</button>
								</div>
								<i data-toggle="tooltip" data-placement="left" title="Caixa não configurado!" 
									class="fa fa-circle {{ (caixa != null) ? 'text-success' : 'text-danger' }}"></i>
							</div>
						</h3>
					</div>
					<div class="panel-body">
						<div class="row" ng-if="out_produtos.length>0">
							<div class="col-lg-12">
								<div class="alert alert-danger">
									Os produtos marcados em vermelho não tem estoque suficiente para a venda
								</div>
							</div>
						</div>

						<style type="text/css">
							.product-selected { border-color: #f3ce85 !important; background-color: #fff5e0; }
						</style>

						<div class="row">
							<div class="col-lg-8">
								<div class="row grade-produtos" ng-show="tipo_view == 'grade-categorias'">
									<div class="col-xs-3" ng-repeat="categoria in categoriasProduto" ng-click="setBuscaCategoria(categoria)">
										<div class="panel panel-default middle-frame">
											<div class="panel-body">
												<div class="text-center container clearfix">
													<span class="product-name">{{ categoria.descricao_categoria }}</span>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="row grade-produtos" ng-show="tipo_view == 'grade-produtos'">
									<div class="col-xs-3" ng-repeat="produto in produtos" ng-click="addProduto(produto)">
										<div class="panel panel-default middle-frame {{ isSelected(produto) }}"
											style="min-height: 202px;">
											<div class="panel-body">
												<div class="text-center container clearfix">
													<img src="{{ produto.imgProduto }}">
													<span class="product-name">{{ produto.nome_produto }}</span>
													<span class="product-price">R$ {{ produto.vlr_venda_varejo | numberFormat:2:',':'.' }}</span>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="row lista-produtos" ng-show="tipo_view == 'lista'">
									<div class="col-lg-12">
										<table class="table table-hover table-condensed">
											<thead>
												<tr>
													<th></th>
													<th>Descrição</th>
													<th>Categoria</th>
													<th>Fabricante</th>
													<th>Cor/Sabor</th>
													<th>Tamanho</th>
													<th>Valor Unitário</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												<tr ng-repeat="produto in produtos" ng-class="{'error-estoque':isOutEstoque(produto.id)}">
													<td>
														<img src="" class="img-responsive">
													</td>
													<td>{{ produto.nome_produto }}</td>
													<td>{{ produto.nome_categoria }}</td>
													<td>{{ produto.nome_fabricante }}</td>
													<td>{{ produto.sabor }}</td>
													<td>{{ produto.peso }}</td>
													<td>R$ {{ produto.vlr_venda_varejo | numberFormat:2:',':'.' }}</td>
													<td>
														<button class="btn btn-block btn-xs btn-danger" ng-show="isSelect(produto)" ng-click="removeProduto(produto)">
															<i class="fa fa-trash-o"></i> Remover
														</button>
														<button class="btn btn-block btn-xs btn-primary" ng-show="!isSelect(produto)" ng-click="addProduto(produto)">
															<i class="fa fa-check-circle"></i> Selecionar
														</button>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="col-lg-4 visible-lg clearfix">
								<div class="row">
									<div class="col-lg-12">
										<h5 class="text-right">{{ total_itens }} iten(s) selecionados</h5>
										<h3 class="text-right">R$ {{ vlrTotalCompra | numberFormat:2:',':'.' }}</h3>

										<div class="clearfix padding-sm"></div>

										<button type="button" 
											id="btn-fazer-compra" 
											class="btn btn-lg btn-success"
											ng-if="total_itens>0"
											ng-click="efetivarCompra()">
											<i class="fa fa-money"></i> Finalizar Venda
										</button>

										<button  type="button" 
											id="btn-fazer-compra" 
											class="btn btn-lg btn-success"
											ng-if="total_itens==0" 
											ng-disabled="true">
											<i class="fa fa-money"></i> Finalizar Venda
										</button>

										<button type="button" 
											class="btn btn-lg btn-danger"
											ng-disabled="total_itens==0" 
											ng-click="cancelarVenda()">
											<i class="fa fa-times-circle"></i> Cancelar Venda
										</button>
									</div>
								</div>

								<div class="row" style="margin-top: 15px;">
									<div class="col-lg-12">
										<div class="table-responsive" style="max-height: 290px; overflow-x: hidden; overflow-y: scroll;">
											<table class="table table-bordered table-condensed table-condensed table-hover">
												<thead>
													<th>Produto</th>
													<th width="80">Subtotal</th>
													<th width="50"></th>
												</thead>
												<tbody>
													<tr ng-repeat="item in carrinho">
														<td class="text-middle">{{ item.nome_produto }}</td>
														<td class="text-middle text-right">R$ {{ item.vlr_venda_varejo | numberFormat:2:',':'.' }}</td>
														<td class="text-middle">
															<button class="btn btn-block btn-xs btn-danger" 
																ng-click="removeProduto(item)">
																<i class="fa fa-trash-o"></i>
															</button>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-footer clearfix visible-xs">
						<h4 class="text-right">Qtd. Itens: 3</h4>
						<h4 class="text-right">Valor da Venda: R$ 999.999,99</h4>

						<div class="clearfix padding-sm"></div>

						<button type="button" class="btn btn-block btn-lg btn-success">
							<i class="fa fa-money"></i> Finalizar Venda
						</button>

						<button type="button" class="btn btn-block btn-lg btn-danger">
							<i class="fa fa-times-circle"></i> Cancelar Venda
						</button>
					</div>
				</div>
			</div>
		</div><!-- /main-container -->

		<!-- /Modal Processando Venda-->
		<div class="modal fade" id="modal_progresso_venda" style="display:none">
  			<div class="modal-dialog error modal-md">
    			<div class="modal-content">
      				<div class="modal-header">
						<h4>Processando Venda</h4>
      				</div>

				    <div class="modal-body">
				    	<div class="alert alert-reforco" style="display:none"></div>

				    	<div class="row">
				    		<div class="col-sm-6" id="valor_pagamento">
				    		<p>
				    			<strong id="text_status_venda">Verificando estoque</strong><img src="assets/imagens/progresso_venda.gif">
				    		</p>
							</div>
				    	</div>
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

    <!-- Chosen -->
	<script src='js/chosen.jquery.min.js'></script>

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

	<!-- Datepicker -->
	<script src='js/bootstrap-datepicker.min.js'></script>

	<!-- Slimscroll -->
	<script src='js/jquery.slimscroll.min.js'></script>

	<!-- Cookie -->
	<script src='js/jquery.cookie.min.js'></script>

	<!-- Endless -->
	<script src="js/endless/endless_form.js"></script>
	<script src="js/endless/endless.js"></script>

	<!-- Extras -->
	<script src="js/extras.js"></script>

	<!-- Mascaras para o formulario de produtos -->
	<script src="js/scripts/mascaras.js"></script>

	<!-- UnderscoreJS -->
	<script type="text/javascript" src="bower_components/underscore/underscore.js"></script>

	<!-- fixedHeadTable -->
	<script type="text/javascript" src="js/fixedHeadTable/fixedHeadTable.js"></script>

	<script src='js/agenda/lib/moment.min.js'></script>

	<!-- accounting -->
	<script type="text/javascript" src="js/accounting.min.js"></script>

	<!-- AngularJS -->
	<script type="text/javascript" src="bower_components/angular/angular.js"></script>
	<script type="text/javascript" src="bower_components/angular-ui-utils/mask.min.js"></script>
	<script type="text/javascript" src="bower_components/angular-ui-switch/angular-ui-switch.min.js"></script>
    <script src="js/angular-sanitize.min.js"></script>
    <script src="js/ui-bootstrap-tpls-0.6.0.js" type="text/javascript"></script>
    <script src="js/dialogs.v2.min.js" type="text/javascript"></script>
    <script src="js/auto-complete/ng-sanitize.js"></script>
    <script src="js/angular-chosen.js"></script>
    <script type="text/javascript">
    	var addParamModule = ['angular.chosen', 'uiSwitch'] ;
    </script>
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/pdv_simple-controller.js?v=<?php echo filemtime('js/angular-controller/pdv_simple-controller.js') ?>"></script>
	<script src="js/angular-strap.min.js"></script>
	<script src="js/angular-strap.tpl.min.js"></script>
	<script type="text/javascript"></script>

	<script type="text/javascript">
		$(document).ready(function() {
			$("#cld_pagameto").on("click", function(){ $("#pagamentoData").trigger("focus"); });
			$("#cld_dtaInicial").on("click", function(){ $("#dtaInicial").trigger("focus"); });
			$("#cld_dtaFinal").on("click", function(){ $("#dtaFinal").trigger("focus"); });
			$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
			$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});
		});
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

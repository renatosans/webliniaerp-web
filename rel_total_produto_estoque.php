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

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/custom.css">
  </head>

  <body class="overflow-hidden" ng-controller="RelatorioTotalProdutoEstoque" ng-cloak>
	<!-- Overlay Div -->
	<!-- <div id="overlay" class="transparent"></div>

	<a href="" id="theme-setting-icon" class="hidden-print"><i class="fa fa-cog fa-lg"></i></a>
	<div id="theme-setting" class="hidden-print">
		<div class="title">
			<strong class="no-margin">Skin Color</strong>
		</div>
		<div class="theme-box">
			<a class="theme-color" style="background:#323447" id="default"></a>
			<a class="theme-color" style="background:#efefef" id="skin-1"></a>
			<a class="theme-color" style="background:#a93922" id="skin-2"></a>
			<a class="theme-color" style="background:#3e6b96" id="skin-3"></a>
			<a class="theme-color" style="background:#635247" id="skin-4"></a>
			<a class="theme-color" style="background:#3a3a3a" id="skin-5"></a>
			<a class="theme-color" style="background:#495B6C" id="skin-6"></a>
		</div>
		<div class="title">
			<strong class="no-margin">Sidebar Menu</strong>
		</div>
		<div class="theme-box">
			<label class="label-checkbox">
				<input type="checkbox" checked id="fixedSidebar">
				<span class="custom-checkbox"></span>
				Fixed Sidebar
			</label>
		</div>
	</div> --><!-- /theme-setting -->

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
			<div class="padding-md">
				<div class="clearfix">
					<div class="pull-left">
						<span class="img-demo">
							<img src="assets/imagens/logos/{{ userLogged.nme_logo }}">
						</span>

						<div class="pull-left m-left-sm">
							<h3 class="m-bottom-xs m-top-xs">Relatório Produto em Estoque</h3>
						</div>
					</div>

					<div class="pull-right text-right">
						<h5><strong>#<?php echo rand(); ?></strong></h5>
						<strong><?php echo date("d/m/Y H:i:s"); ?></strong>
					</div>
				</div>

				<hr>

				<div class="panel panel-default hidden-print" style="margin-top: 15px;">
					<div class="panel-heading"><i class="fa fa-calendar"></i> Filtros</div>

					<div class="panel-body">
						<form role="form">
							<div class="row">
								<div class="col-lg-4">
									<div class="form-group">
										<label class="control-label">Produto</label>
										<div class="input-group">
											<input ng-click="modalProdutos()" type="text" class="form-control" ng-model="busca.nome_produto" readonly="readonly" style="cursor: pointer;"></input>
											<span class="input-group-btn">
												<button ng-click="modalProdutos(0,10)" ng-click="modalProdutos(0,10)" type="button" class="btn"><i class="fa fa-archive"></i></button>
											</span>
										</div>
									</div>
								</div>
								<div class="col-lg-4">
									<div class="form-group">
										<label class="control-label">Depósitos</label>
										<div class="input-group">
											<input ng-click="modalDepositos()" type="text" class="form-control" ng-model="busca.nome_deposito" readonly="readonly" style="cursor: pointer;"></input>
											<span class="input-group-btn">
												<button ng-click="modalDepositos(0,10)" ng-click="modalDepositos(0,10)" type="button" class="btn"><i class="fa fa-sitemap"></i></button>
											</span>
										</div>
									</div>
								</div>
								<div class="col-lg-2">
									<div class="form-group">
										<label class="control-label">Agrupar por:</label>
										<select class="form-control" ng-model="grupo_busca">
											<option value=""></option>
											<option value="produto">Produto</option>
											<option value="deposito">Depósito</option>
										</select>
									</div>
								</div>
							
							</div>
						</form>
					</div>

					<div class="panel-footer clearfix">
						<div class="pull-right">
							<button type="button" class="btn btn-sm btn-primary" ng-click="aplicarFiltro()"><i class="fa fa-filter"></i> Aplicar Filtro</button>
							<button type="button" class="btn btn-sm btn-default" ng-click="resetFilter()"><i class="fa fa-times-circle"></i> Limpar Filtro</button>
							<button class="btn btn-sm btn-success hidden-print"  id="invoicePrint"><i class="fa fa-print"></i> Imprimir</button>
							<button class="btn btn-sm btn-success hidden-print" ng-click="doExportExcel('data')"><i class="fa fa-file-excel-o"></i> Exportar p/ Excel</button>
						</div>
					</div>
				</div>

				<br>

				<table id="data" class="table table-bordered table-hover table-striped table-condensed" ng-if="agrupar == false">
					<thead>
						<tr>
							<th class="text-center" width="100">ID</th>
							<th>Cód. Barras</th>
							<th>Produto</th>
							<th width="100" class="text-center">Fabricante</th>
							<th width="100" class="text-center">Tamanho</th>
							<th width="100" class="text-center">Sabor/Cor</th>
							<th width="100" class="text-center">R$ Custo Unit.</th>
							<th width="100" class="text-center">R$ Atacado</th>
							<th width="100" class="text-center">R$ Interm.</th>
							<th width="100" class="text-center">R$ Varejo</th>
							<th width="100" class="text-center" >Depósito</th>
							<th width="100" class="text-center" ng-if="grupo_tabela == 'validade'">Validade</th>
							<th width="100" class="text-center">Estoque</th>
							<th width="100" class="text-center hide">R$ Custo Total</th>
						</tr>
					</thead>
					<tbody>
						<tr ng-if="produtos.length == 0 && produtos != null">
							<td class="text-center" colspan="14">
								<i class="fa fa-refresh fa-spin"></i> Aguarde, carregando itens...
							</td>
						</tr>
						<tr ng-if="produtos.length < 0 && produtos == null">
							<td colspan="14">
								Nenhuma venda encontrada.
							</td>
						</tr>
						<tr ng-repeat="item in produtos">
							<td class="text-center">
								{{ item.id }}
							</td>
							<td class="text-center">{{ item.codigo_barra }}</td>
							<td>{{ item.nome }}</td>
							<td class="text-center">{{ item.nome_fabricante }}</td>
							<td class="text-center">{{ item.peso }}</td>
							<td class="text-center">{{ item.sabor }}</td>
							
							<td class="text-right">R$ {{ item.vlr_custo_real | numberFormat : 2 : ',' : '.' }}</td>
							<td class="text-right">R$ {{ item.vlr_venda_atacado | numberFormat : 2 : ',' : '.' }}</td>
							<td class="text-right">R$ {{ item.vlr_venda_intermediario | numberFormat : 2 : ',' : '.' }}</td>
							<td class="text-right">R$ {{ item.vlr_venda_varejo | numberFormat : 2 : ',' : '.' }}</td>

							<td class="text-center" >{{ (grupo_tabela == 'validade' || grupo_tabela == 'deposito' || busca_deposito) &&  item.nome_deposito  || 'Todos' }}</td>
							<td class="text-center" ng-if="grupo_tabela == 'validade'">{{ item.dta_validade != '2099-12-31' && (item.dta_validade | dateFormat:'date') || ' ' }}</td>

							<td class="text-center">{{ item.qtd_item }}</td>
							<td class="text-right hide">R$ {{ (item.vlr_custo_real * item.qtd_item) | numberFormat : 2 : ',' : '.' }}</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td class="text-right text-bold" colspan="11">TOTAL</td>
							<td class="text-center">{{ qtd_total_estoque }}</td>
							<td class="text-right hide">R$ {{ vlr_total_estoque | numberFormat : 2 : ',' : '.'  }}</td>
						</tr>
					</tfoot>
				</table>
				<table id="data" class="table table-bordered table-hover table-striped table-condensed" ng-if="agrupar == 'produto' ">
						<thead>
							<tr>
								<th class="text-right" >Depósito</th>
								<th width="100" class="text-center">Estoque</th>
							</tr>
						</thead>
						<tbody ng-repeat="(key, value) in produtos">
							<tr   class="info text-left">
								<td colspan="2" >{{ key+" | "+value.nome}} {{ value.nome_fabricante != ""  &&  "| "+value.nome_fabricante || "" }} {{ value.peso != ""  &&  "| "+value.peso || "" }} </td>
							</tr>
							<tr ng-repeat="produto in value">
								<td class="text-right">{{ produto.nome_deposito }}</td>
								<td class="text-right">{{ produto.qtd_item }}</td>
							</tr>
						</tbody>
				</table>

				<table id="data" class="table table-bordered table-hover table-striped table-condensed" ng-if="agrupar == 'deposito' ">
						<thead>
							<tr>
								<th class="text-right" >produto</th>
								<th width="200" class="text-center">Fabricante</th>
								<th width="100" class="text-center">Tamanho</th>
								<th width="100" class="text-center">Estoque</th>
							</tr>
						</thead>
						<tbody ng-repeat="(key, value) in produtos">
							<tr   class="info text-left">
								<td colspan="4" >{{ key }}</td>
							</tr>
							<tr ng-repeat="produto in value">
								<td class="text-right">{{ produto.nome }}</td>
								<td class="text-right">{{ produto.nome_fabricante }}</td>
								<td class="text-right">{{ produto.peso }}</td>
								<td class="text-right">{{ produto.qtd_item }}</td>
							</tr>
						</tbody>
				</table>

				<div class="pull-right hidden-print">
						<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.produtos.length > 1">
							<li ng-repeat="item in paginacao.produtos" ng-class="{'active': item.current}">
								<a href="" h ng-click="loadProdutos(item.offset,item.limit)">{{ item.index }}</a>
							</li>
						</ul>
				</div>
				</div>
			</div><!-- /.padding20 -->
		</div><!-- /main-container -->
	</div><!-- /wrapper -->

	<div class="modal fade" id="modal-aguarde">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Aguarde</h4>
				</div>
				<div class="modal-body">
					<p>Carregando dados do relatório...</p>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<!-- /Modal Produtos-->
		<div class="modal fade" id="list_produtos" style="display:none">
  			<div class="modal-dialog modal-lg">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Produtos</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.produto_modal" ng-enter="loadProdutosModal(0,10)" type="text" class="form-control input-sm">

						            <div class="input-group-btn">
						            	<button tabindex="-1" class="btn btn-sm btn-primary" type="button"
						            		ng-click="loadProdutosModal(0,10)">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div> <!-- /input-group-btn -->
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>

						<br>

						<div class="row">
							<div class="col-md-12">
								<div class="alert alert-produtos" style="display:none"></div>
						   		<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(produtos_modal.length != 0)">
										<tr>
											<th>#</th>
											<th>Nome</th>
											<th>Fabricante</th>
											<th>Tamanho</th>
											<th>Sabor/Cor</th>
											<th width="80"></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(produtos_modal.length == 0)">
											<td colspan="3">Não a resultados para a busca</td>
										</tr>
										<tr ng-repeat="item in produtos_modal">
											<td>{{ item.id_produto }}</td>
											<td>{{ item.nome }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td>{{ item.peso }}</td>
											<td>{{ item.sabor }}</td>			
											<td>
											<button ng-click="addProduto(item)" class="btn btn-success btn-xs" type="button">
												<i class="fa fa-check-square-o"></i> Selecionar
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
						             <ul class="pagination pagination-xs m-top-none" ng-show="paginacao.produtos_modal.length > 1">
										<li ng-repeat="item in paginacao.produtos_modal" ng-class="{'active': item.current}">
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
		<!-- /Modal depositos-->
		<div class="modal fade" id="modal-depositos" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Depositos</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.depositos" ng-enter="loadDepositos(0,10)" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadDepositos(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div> <!-- /input-group-btn -->
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>

						<br/>

				   		<div class="row">
				   			<div class="col-sm-12">
				   				<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(depositos.length != 0)">
										<tr>
											<th colspan="2">Nome</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(depositos.length == 0)">
											<td colspan="3">{{ busca_vazia.depositos  && 'Não há resultado para a busca' || 'Não há Depositos cadastrados' }}</td>
										</tr>
										<tr ng-repeat="item in depositos">
											<td>{{ item.nme_deposito }}</td>
											<td width="50" align="center">
												<button type="button" class="btn btn-xs btn-success" ng-click="addDeposito(item)">
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
					    		<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao.depositos.length > 1">
									<li ng-repeat="item in paginacao.depositos" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadDepositos(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
					    	</div>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

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

	<!-- UnderscoreJS -->
	<script type="text/javascript" src="bower_components/underscore/underscore.js"></script>

	<!-- Extras -->
	<script src="js/extras.js"></script>

	<!-- AngularJS -->
	<script src="js/tableExport/jquery.base64.js" type="text/javascript"></script>  
	<script src="js/tableExport/tableExport.js" type="text/javascript"></script>
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
	<script src="js/angular-controller/relatorio-total-produto-estoque-controller.js"></script>

	<script type="text/javascript">
		$(document).ready(function() {
			$('.datepicker').datepicker();
			$("#cld_pagameto").on("click", function(){ $("#pagamentoData").trigger("focus"); });
			$("#cld_dtaInicial").on("click", function(){ $("#dtaInicial").trigger("focus"); });
			$("#cld_dtaFinal").on("click", function(){ $("#dtaFinal").trigger("focus"); });

			$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
			$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});
		});
		$('.datepicker').datepicker();
			$("#cld_pagameto").on("click", function(){ $("#pagamentoData").trigger("focus"); });
			$("#cld_dtaInicial").on("click", function(){ $("#dtaInicial").trigger("focus"); });
			$("#cld_dtaFinal").on("click", function(){ $("#dtaFinal").trigger("focus"); });

			$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
			$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});
		function printDiv(id, pg) {
			var contentToPrint, printWindow;

			contentToPrint = window.document.getElementById(id).innerHTML;
			printWindow = window.open(pg);

		    printWindow.document.write("<link href='bootstrap/css/bootstrap.min.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/font-awesome.min.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/pace.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/endless.min.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/endless-skin.css' rel='stylesheet'>");

			printWindow.document.write("<style type='text/css' media='print'>@page { size: portrait; } th, td { font-size: 8pt; }</style><style type='text/css'>#invoicePrint{ display:none }</style>");

			printWindow.document.write(contentToPrint);

			printWindow.window.print();
			printWindow.document.close();
			printWindow.focus();
		}

		$(function()	{
			$('#invoicePrint').click(function()	{
				printDiv("main-container", "");
			});
		});
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

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
	<link href="css/datepicker/bootstrap-datepicker.css" rel="stylesheet"/>

	<!-- Timepicker -->
	<link href="css/bootstrap-timepicker.css" rel="stylesheet"/>

	<!-- Bower Components -->	
	<link href="bower_components/noty/lib/noty.css" rel="stylesheet">

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet" >
	<style type="text/css">
		/*Redimencionando PopOver*/
		.popover{
		    display:block !important;
		    max-width: 400px!important;
		    width: 400px!important;
		    width:auto;
		}
	</style>
  </head>
  <body ng-click="limparPopOver($event)" class="overflow-hidden" ng-controller="RelatorioTotalVendasCliente" ng-cloak>
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
							<h3 class="m-bottom-xs m-top-xs">Relatório de Vendas por Produto por Mês</h3>
							<small><?php echo date("d/m/Y H:i:s"); ?></small>
						</div>
					</div>

					<div class="pull-right text-right">
						<h6>{{ dados_empreendimento.num_cnpj }} - {{ dados_empreendimento.nome_empreendimento }}</h6>
						<h6>{{ dados_empreendimento.nme_logradouro }}, {{ dados_empreendimento.num_logradouro }}</h6>
						<h6>CEP: {{ dados_empreendimento.num_cep }} - {{ dados_empreendimento.nme_cidade }} - {{ dados_empreendimento.uf }}</h6>
						<h6>Telefone: {{ dados_empreendimento.num_telefone }}</h6>
					</div>
				</div>

				<hr>

				<div class="panel panel-default hidden-print" style="margin-top: 15px;">
					<div class="panel-heading"><i class="fa fa-calendar"></i> Filtros</div>				
					<div class="panel-body">
						<div class="alert-sistema alert errorBusca" style="display:none"></div>
						<form role="form">
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
										<label class="control-label">Datas</label>
										<div class="input-group">
											<input readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="dtaInicial" class="datepicker1 form-control">
											<span class="input-group-addon" id="cld_dtaInicial"><i class="fa fa-calendar"></i></span>
										</div>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="control-label">Produtos</label>
										<div class="input-group">
											<input ng-click="showProdutos(0,10)" type="text" class="form-control" ng-model="produto.nome" readonly="readonly" style="cursor: pointer;"></input>
											<span class="input-group-btn">
												<button ng-enter="showProdutos(0,10)" ng-click="showProdutos(0,10)" type="button" class="btn"><i class="fa fa-archive"></i></button>
											</span>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>

					<div class="panel-footer clearfix">
						<div class="pull-right">
							<button type="button" class="btn btn-sm btn-primary" ng-click="aplicarFiltro()"><i class="fa fa-filter"></i> Aplicar Filtro</button>
							<button type="button" class="btn btn-sm btn-default" ng-click="resetFilter()"><i class="fa fa-times-circle"></i> Limpar Filtro</button>
							<button class="btn btn-sm btn-success hidden-print" ng-show="itens.length > 0" id="invoicePrint"><i class="fa fa-print"></i> Imprimir</button>
							<button class="btn btn-sm btn-success hidden-print" ng-click="doExportExcel('data')" ng-if="(vendas != null)"><i class="fa fa-file-excel-o"></i> Exportar p/ Excel</button>
						</div>
					</div>
				</div>

				<ul class="pagination pagination-sm m-top-none pull-right hidden-print" ng-show="paginacao.itens.length > 1">
					<li ng-repeat="item in paginacao.itens" ng-class="{'active': item.current}">
						<a href="" h ng-click="loadItens(item.offset,item.limit)">{{ item.index }}</a>
					</li>
				</ul>
				<br>
				<table id="data" class="table table-bordered table-hover table-striped table-condensed" ng-if="(vendas != null)">
					<thead>
						<tr>
							<th width="50"></th>
							<th>Produto</th>
							<th>Venda</th>
							<th>Margem</th>
							<th>Dta. Venda</th>
							<th>Fabricante</th>
							<th >Tamanho</th>
							<th >Sabor/Cor</th>
							<th class="text-center">Qtd. Vendida</th>
							<th class="text-right" width="100" >Custo Total</th>
							<th class="text-right" width="100">Total Vendido</th>
							<th class="text-right">Margem Lucro</th>
							<th class="text-right" width="100">Lucro Bruto</th>
						</tr>
					</thead>
						<tr ng-if="vendas.length == 0 && vendas != null">
							<td class="text-center" colspan="13">
								<i class="fa fa-refresh fa-spin"></i> Aguarde, carregando itens...
							</td>
						</tr>
						<tbody ng-repeat="(key, item) in vendas">
							<tr class="info">
								<td colspan="13">{{ key | dateFormat:'date-m/y' }} - <span class="badge ">{{ countQtdItens(item.itens) }}</span><span class="badge pull-right"><a style="cursor:pointer" ng-click="ancoraSaldo(key)">Ir ao saldo do periodo</a></span></td>
							</tr>
							<tr ng-repeat="venda in item.itens" bs-popover>
								<td class="text-cente">{{ venda.cod_produto }}</td>
								<td>{{ venda.nme_produto }}</td>
								<td>#{{ venda.id_venda }}</td>
								<td>{{ venda.margem_aplicada | uppercase }}</td>
								<td>{{ venda.dta_venda | dateFormat }}</td>
								<td>{{ venda.nome_fabricante }}</td>
								<td>{{ venda.peso }}</td>
								<td>{{ venda.sabor }}</td>
								<td class="text-center">{{ venda.qtd_vendida }}</td>
								<td class="text-right">
									 <div class="cardBody"><a style="cursor:pointer;text-decoration: underline;" style="font-size: 12px;color: #777" ng-click="detalCustoProduto(venda)" href="#" id="pop{{venda.nme_produto }}" rel="popover" data-content="<i class='fa fa-refresh fa-spin'></i> Aguarde, carregando itens..." data-trigger="focus" >
										R$ {{venda.vlr_custo_total | numberFormat:config.qtd_casas_decimais:',':'.'}}
									 </a>
								</td>
								<td class="text-right">R$ {{venda.vlr_vendido | numberFormat:config.qtd_casas_decimais:',':'.'}}</td>
								<td class="text-right">{{ venda.med_margem_lucro * 100 | numberFormat:config.qtd_casas_decimais:',':'.'}}%</td>
								<td class="text-right" ng-if="venda.vlr_lucro_bruto > 0">{{ venda.vlr_lucro_bruto | numberFormat:config.qtd_casas_decimais:',':'.' }}</td>
								<td class="text-right" ng-if="venda.vlr_lucro_bruto < 0"><a style="cursor:pointer;text-decoration: underline;" ng-click="showProdutoDebito(venda)">{{ venda.vlr_lucro_bruto | numberFormat:config.qtd_casas_decimais:',':'.' }}</a></td>
							</tr>
							<tr class="warning" id="saldo_{{key}}">
							<td class="text-right" colspan="8"><strong class="ng-binding">Totais</strong></td>
							<td class="text-center">
								{{ countQtdItens(item.itens) }}
							</td>
							<td class="text-right">
								<span class="label label-success ng-binding ng-scope" ng-if="item.saldo_vlr_custo_total >= 0">
									R$ {{ item.saldo_vlr_custo_total | numberFormat:config.qtd_casas_decimais:',':'.' }}
								</span>
								<span class="label label-danger ng-binding ng-scope" ng-if="item.saldo_vlr_custo_total < 0">
									R$ {{ item.saldo_vlr_custo_total | numberFormat:config.qtd_casas_decimais:',':'.' }}
								</span>
							</td>
							<td class="text-right">
								<span class="label label-success ng-binding ng-scope" ng-if="item.saldo_vlr_vendido >= 0">
									R$ {{ item.saldo_vlr_vendido | numberFormat:config.qtd_casas_decimais:',':'.' }}
								</span>
								<span class="label label-danger ng-binding ng-scope" ng-if="item.saldo_vlr_vendido < 0">
									R$ {{ item.saldo_vlr_vendido | numberFormat:config.qtd_casas_decimais:',':'.' }}
								</span>
							</td>
							<td class="text-right">
								
							</td>
							<td class="text-right">
								<span class="label label-success ng-binding ng-scope" ng-if="item.saldo_vlr_lucro_bruto >= 0">
									R$ {{ item.saldo_vlr_lucro_bruto | numberFormat:config.qtd_casas_decimais:',':'.' }}
								</span>
								<span class="label label-danger ng-binding ng-scope" ng-if="item.saldo_vlr_lucro_bruto < 0">
									R$ {{ item.saldo_vlr_lucro_bruto | numberFormat:config.qtd_casas_decimais:',':'.' }}
								</span>
							</td>
						 </tr>
					</tbody>
				</table>
				<span ng-if="(msg_error)" class="alert alert-{{ (status == 404) ? 'warning' : ((status == 500) ? 'danger' : '') }}">{{ msg_error }}</span>
				<!--<div class="pull-right">
						<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.vendas.length > 1">
							<li ng-repeat="item in paginacao.vendas" ng-class="{'active': item.current}">
								<a href="" h ng-click="loadVendas(item.offset,item.limit)">{{ item.index }}</a>
							</li>
						</ul>
				</div>-->
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

	<div class="modal fade" id="list_produtos" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Produtos</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.produtos" ng-keyup="loadProdutos(0,10)" ng-enter="loadProdutos(0,10)" type="text" class="form-control input-sm">

						            <div class="input-group-btn">
						            	<button ng-click="loadProdutos(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button"><i class="fa fa-search"></i> Buscar</button>
						            </div> <!-- /input-group-btn -->
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>

						<br>

						<div class="row">
							<div class="col-md-12">
								<div class="alert alert-produtos" style="display:none"></div>
						   		<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(produtos.length != 0)">
										<tr>
											<th>#</th>
											<th>Nome</th>
											<th>Fabricante</th>
											<th>Tamanho</th>
											<th width="80"></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(produtos.length == 0)">
											<td colspan="3">Não a Produtos cadastrados</td>
										</tr>
										<tr ng-repeat="item in produtos">
											<td>{{ item.id_produto }}</td>
											<td>{{ item.nome }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td>{{ item.peso }}</td>
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
						             <ul class="pagination pagination-xs m-top-none" ng-show="paginacao.produtos.length > 1">
										<li ng-repeat="item in paginacao.produtos" ng-class="{'active': item.current}">
											<a href="" ng-click="loadProdutos(item.offset,item.limit)">{{ item.index }}</a>
										</li>
									</ul>
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>
					</div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>

	<div class="modal fade" id="list_produtos_debito" style="display:none">
  			<div class="modal-dialog modal-lg">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4><span>Itens em debito</span></h4>
						<span class="text-muted">lista de itens do produto <b>{{ produto_debito.nome_produto }}</b> que foram vendidos a baixo do valor de custo</span>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="alert alert-produtos" style="display:none"></div>
						   		<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(produtos.length != 0)">
										<tr>
											<th>ID Venda</th>
											<th>Data venda</th>
											<th>Operador</th>
											<th>Cliente</th>
											<th>Vlr. Custo</th>
											<th>Qtd.</th>
											<th>Vlr. Vendido</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(produto_debito.itens.length == 0)">
											<td colspan="3">Não a itens vendido a baixo do valor de custo</td>
										</tr>
										<tr ng-repeat="item in produto_debito.itens">
											<td>{{ item.id_venda }}</td>
											<td>{{ item.dta_venda | dateFormat:'dateTime' }}</td>
											<td>{{ item.nome_usuario }}</td>
											<td>{{ item.nome_cliente }}</td>
											<td class="text-right">R${{ item.vlr_custo_total | numberFormat:config.qtd_casas_decimais:',':'.' }}</td>
											<td class="text-center">{{ item.qtd }}</td>
											<td class="text-right">R${{ item.valor_real_item | numberFormat:config.qtd_casas_decimais:',':'.' }}</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>

					    <div class="row">
					    	<div class="col-md-12">
								<div class="input-group pull-right">
						             <ul class="pagination pagination-xs m-top-none" ng-show="produto_debito.paginacao.length > 1">
										<li ng-repeat="item in produto_debito.paginacao" ng-class="{'active': item.current}">
											<a href="" ng-click="loadProdutoDebito(item.offset,item.limit)">{{ item.index }}</a>
										</li>
									</ul>
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>
					</div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>

	<a href="" id="scroll-to-top" class="hidden-print"><i class="fa fa-chevron-up"></i></a>

	<!-- Logout confirmation -->
	<?php include("logoutConfirm.php"); ?>
</body>



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
	<script src='js/datepicker/bootstrap-datepicker.js'></script>
	<script src='js/datepicker/bootstrap-datepicker.pt-BR.js'></script>
	

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

	<!-- Bower Components -->	
	<script src="bower_components/noty/lib/noty.min.js" type="text/javascript"></script>
    <script src="bower_components/mojs/build/mo.min.js" type="text/javascript"></script>

	<!-- Extras -->
	<script src="js/extras.js"></script>

	<!-- Mascaras para o formulario de produtos -->
	<script src="js/scripts/mascaras.js"></script>

	<!-- UnderscoreJS -->
	<script type="text/javascript" src="bower_components/underscore/underscore.js"></script>

	<!-- WYSIHTML5 -->
	<script src='js/wysihtml5-0.3.0.min.js'></script>
	<script src='js/uncompressed/bootstrap-wysihtml5.js'></script>

<!-- AngularJS -->
	<script src="js/tableExport/jquery.base64.js" type="text/javascript"></script>  
	<script src="js/tableExport/tableExport.js" type="text/javascript"></script>
	<script type="text/javascript" src="js/js-ui-popover/angular.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="bower_components/angular-ui-utils/mask.min.js"></script>
    <script src="js/angular-sanitize.min.js"></script>
    <script src='js/js-ui-popover/ui-bootstrap-tpls.min.js'></script>
    <script src="js/dialogs.v2.min.js" type="text/javascript"></script>
     <script src="js/auto-complete/ng-sanitize.js"></script>
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/relatorio_vendas_produtos-mes-controller.js"></script>


	<script id="printFunctions">
		function printDiv(id, pg) {
			var contentToPrint, printWindow;

			contentToPrint = window.document.getElementById(id).innerHTML;
			printWindow = window.open(pg);

		    printWindow.document.write("<link href='bootstrap/css/bootstrap.min.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/font-awesome.min.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/pace.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/endless.min.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/endless-skin.css' rel='stylesheet'>");

			printWindow.document.write("<style type='text/css' media='print'>@page { size: portrait; } th, td { font-size: 8pt; }</style>");

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
	<script type="text/javascript">
		$(document).ready(function() {
			var options =  {
		        format: "mm/yyyy",
		        language: "pt-BR",
		        minViewMode: 'months',
		        clearBtn:true,
		        multidate:true
		    }
			$('.datepicker1').datepicker(options);
			$('.datepicker2').datepicker(options);
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
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

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet" >
  </head>

  <body class="overflow-hidden" ng-controller="RelatorioContasPagar" ng-cloak>
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
			<div id="breadcrumb">
				<ul class="breadcrumb">
					 <li><i class="fa fa-home"></i> <a href="dashboard.php">Home</a></li>
					 <li class="active"><i class="fa fa-users"></i><a href="clientes.php">Clientes/Usuários</a></li>
					 <li class="active"><i class="fa fa-clipboard"></i> Extrato</li>
				</ul>
			</div><!-- breadcrumb -->
			<div class="padding-md">
				<div class="clearfix">
					<div class="pull-left">
						<span class="img-demo">
							<img src="assets/imagens/logos/{{ userLogged.nme_logo }}">
						</span>

						<div class="pull-left m-left-sm">
							<h3 class="m-bottom-xs m-top-xs">Extrato</h3>
							<span class="text-muted">Detalhes de entradas e saidas</span>
						</div>
					</div>
				</div>

				<hr>

				<div class="panel panel-default hidden-print" style="margin-top: 15px;">
					<div class="panel-heading"><i class="fa fa-calendar"></i> Filtros</div>

					<div class="panel-body">
						<form role="form">
							<div class="row">
								<div class="col-sm-12">
									<div class="panel-tab clearfix">
										<ul class="tab-bar">
											<li class="active"><a href="#home1" data-toggle="tab" onclick="return false" ng-click="setBusca('periodo')"> Período</a></li>
											<li><a href="#profile1" data-toggle="tab" onclick="return false" onclick="return false" ng-click="setBusca('intervalo')"> Intervalo</a></li>
										</ul>
									</div>
									<div class="panel-body">
										<div class="tab-content">
											<div class="tab-pane fade in active" id="home1">
													<div class="row">
														<div class="col-sm-6">
															<div class="form-group" id="form_cliente">
																<label class="control-label">Cliente</label>
																<div class="input-group">
																	<input ng-click="selCliente(0,10)" ng-disabled="finalizarOrcamento" type="text" class="form-control ng-pristine ng-untouched ng-valid" ng-model="cliente.nome" readonly="readonly" style="cursor: pointer;">
																	<span class="input-group-btn">
																		<button ng-click="selCliente(0,10)" type="button" ng-disabled="finalizarOrcamento" class="btn btn-info"><i class="fa fa-users"></i></button>
																	</span>
																</div>
															</div>
														</div>
														<div class="col-sm-2">
															<div class="form-group" id="form_dta_inicial">
																<label class="control-label">Inicial</label>
																<div class="input-group">
																	<input readonly="readonly" style="background:#FFF;cursor:pointer" ng-model="busca.dataInicial" date-picker type="text" id="dtaInicial" class="form-control">
																	<span class="input-group-addon" id="cld_dtaInicial"><i class="fa fa-calendar"></i></span>
																</div>
															</div>
														</div>

														<div class="col-sm-2">
															<div class="form-group" id="form_dta_final">
																<label class="control-label">Final</label>
																<div class="input-group">
																	<input readonly="readonly" style="background:#FFF;cursor:pointer" ng-model="busca.dataFinal" date-picker type="text" id="dtaFinal" class="form-control">
																	<span class="input-group-addon" id="cld_dtaFinal"><i class="fa fa-calendar"></i></span>
																</div>
															</div>
														</div>
													</div>
											</div>
											<div class="tab-pane fade" id="profile1">
												<div class="row">
													<div class="col-sm-6">
														<div class="form-group" id="form_cliente_2">
															<label class="control-label">Cliente:</label>
															<div class="input-group">
																<input ng-click="selCliente(0,10)" ng-disabled="finalizarOrcamento" type="text" class="form-control ng-pristine ng-untouched ng-valid" ng-model="cliente.nome" readonly="readonly" style="cursor: pointer;">
																<span class="input-group-btn">
																	<button ng-click="selCliente(0,10)" type="button" ng-disabled="finalizarOrcamento" class="btn btn-info"><i class="fa fa-users"></i></button>
																</span>
															</div>
														</div>
													</div>
													<div class="col-sm-3">
														<div class="form-group" id="form_busca_interval">
															<label class="control-label">busca nos ultimos</label>
															<input ng-model="busca.intervalo" onkeypress="return SomenteNumero(event);" id="busca_interval" class="form-control" type="text" placeholder="dias ... "> 
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>	
							</div>						
						</form>
					</div>

					<div class="panel-footer clearfix">
						<div class="pull-right">
							<button type="button" class="btn btn-sm btn-primary" ng-click="getExtrato()"><i class="fa fa-filter"></i> Aplicar Filtro</button>
							<button ng-if="false" type="button" class="btn btn-sm btn-default" ng-click="resetFilter()"><i class="fa fa-times-circle"></i> Limpar Filtro</button>
							<button class="btn btn-sm btn-success" ng-show="dadosExtrato.extrato.length > 0" id="invoicePrint"><i class="fa fa-print"></i> Imprimir</button>
						</div>
					</div>
				</div>


				

				
				<div class="panel panel-default" style="margin-top: 15px;">
					<div class="panel-heading"><i class="fa fa-bars"></i> Extrato  </div>
					<div class="panel-body">
					<div ng-show="dadosExtrato.extrato.length > 0">
						<p>
							<b>data:</b> {{currentDate | date:'dd/MM/yyyy'}}
						</p>
						<p>
							<b>Cliente:</b> {{ dadosBusca.cliente.nome }}
						</p>
						<p ng-if="dadosBusca.tipoBusca == 'intervalo' && dadosBusca.intervalo == 1">
							<b>Periodo:</b> no ultimo dia
						</p>
						<p ng-if="dadosBusca.tipoBusca == 'intervalo' && dadosBusca.intervalo > 1">
							<b>Periodo:</b> nos utimos {{ cliente.intervalo }} dias
						</p>
						<p ng-if="dadosBusca.tipoBusca == 'periodo'">
							<b>Periodo:</b> Entre {{ dadosBusca.dataInicial }} e {{ dadosBusca.dataFinal }}
						</p>
					</div>
					<table id="data" class="table table-striped table-condensed">
						<thead>
							<tr ng-if="dadosExtrato.extrato.length > 0">
								<th class="text-center">Data do Evento</th>
								<th >Evento</th>
								<th class="text-right">Valor</th>
								<th class="text-right">Saldo</th>
							</tr>
							<tr ng-if="dadosExtrato == null">
								<th class="text-center" colspan="4">Selecione um período ou intervalo para a busca</th>
							</tr>
							<tr ng-if="dadosExtrato.length == 0">
								<th class="text-center" colspan="4"><i class="fa fa-loading fa-sping"></i> Aguarde, carregando extrato...</th>
							</tr>
							<tr ng-if="dadosExtrato.extrato == false">
								<th class="text-center" colspan="4">Nenhum lançamento foi relalizado para o cliente <b style="text-decoration: underline;">{{ cliente.nome }}</b> no periodo selecionado</th>
							</tr>
						</thead>
						<tbody ng-mouseenter="popover()" >
							<tr ng-repeat="item in dadosExtrato.extrato" >
								<td class="text-center" ng-if="item.tipo != 'saldo'">{{ item.dta_entrada | dateFormat: 'dateTime' }}</td>
								<td class="text-center" ng-if="item.tipo == 'saldo'"></td>

								<td ng-if="item.tipo == 'venda'" >COMPRA (<a href="vendas.php?id_venda={{ item.id }}" target="_blank" class="text-info">#{{ item.id }}</a>)</td>
								<td ng-if="item.tipo == 'saldo' "style="font-weight: bold;" >{{ item.tipo |uppercase }}</td>
								<td ng-if="item.tipo == 'pagamento' && item.id_forma_pagamento != 6" >{{ item.tipo | uppercase }}  EM {{item.descricao_forma_pagamento | uppercase}} <b>PARA O DIA : {{ item.dta_pagamento | dateFormat: 'date' }}</b> </td>
								<td ng-if="item.tipo == 'pagamento' && item.id_forma_pagamento == 6" >{{ item.tipo | uppercase}} EM {{ item.n_parcelas | uppercase }}X  NO {{item.descricao_forma_pagamento | uppercase }} <a style="cursor:pointer;font-weight: bold;text-decoration: underline;" href="#" rel="popover"  data-content="{{ item.template_popover }}" data-trigger="focus">(DATA DAS PARCELAS)</a> </td>

								<td ng-if="item.tipo == 'venda'" 	 class="text-right" style="color: red;">R$ {{item.valor | numberFormat:2:',':'.'}}-</td>
								<td ng-if="item.tipo == 'pagamento'" class="text-right" style="color: green;">R$ {{item.valor | numberFormat:2:',':'.'}}</td>
								<td ng-if="item.tipo == 'saldo' " 	 class="text-right"></td>

								<td ng-if="item.tipo == 'saldo' && item.valor < 0" class="text-right"  style="color: red;font-weight: bold;">R$ {{item.valor | numberFormat:2:',':'.'}}</td>
								<td ng-if="item.tipo == 'saldo' && item.valor > 0" class="text-right"  style="color: green;font-weight: bold;">R$ {{item.valor | numberFormat:2:',':'.'}}</td>
								<td ng-if="item.tipo == 'saldo' && item.valor == 0" class="text-right" style="color: blue;font-weight: bold;">R$ {{item.valor | numberFormat:2:',':'.'}}</td>
								<td ng-if="item.tipo != 'saldo' " class="text-right"></td>
							</tr>
						</tbody>
					</table>
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
					<p><i class="fa fa-refresh fa-spin"></i> Carregando Extrato</p>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<a href="" id="scroll-to-top" class="hidden-print"><i class="fa fa-chevron-up"></i></a>

		<!-- /Modal Clientes-->
		<div class="modal fade" id="list_clientes" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Clientes</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input type="text" class="form-control input-sm"
						            	 ng-enter="loadCliente(0,10)" 
						            	 ng-model="busca.clientes"
						            	 ng-keyup="loadCliente(0,10)" 
						            >
						            <div class="input-group-btn">
						            	<button ng-click="loadCliente(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
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
									<tr ng-if="clientes.length <= 0 || clientes == null">
										<th class="text-center" colspan="9" style="text-align:center"><strong>Carregando</strong><img src="assets/imagens/progresso_venda.gif"></th>
									</tr>
									<thead ng-show="(clientes.length != 0)">
										<tr>
											<th >Nome</th>
											<th >Apelido</th>
											<th >Perfil</th>
											<th colspan="2">selecionar</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in clientes">
											<td>{{ item.nome }}</td>
											<td>{{ item.apelido }}</td>
											<td>{{ item.nome_perfil }}</td>
											<td width="50" align="center">
												<button type="button" class="btn btn-xs btn-success" ng-click="addCliente(item)">
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
				    			<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao_clientes.length > 1">
									<li ng-repeat="item in paginacao_clientes" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadCliente(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
				    		</div>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

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
	<script type="text/javascript" src="bower_components/angular/angular.js"></script>
	<script type="text/javascript" src="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="bower_components/angular-ui-utils/mask.min.js"></script>
    <script src="js/angular-sanitize.min.js"></script>
    <script src='js/js-ui-popover/ui-bootstrap-tpls.min.js'></script>
    <script src="js/dialogs.v2.min.js" type="text/javascript"></script>
     <script src="js/auto-complete/ng-sanitize.js"></script>
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/extrato-controller.js"></script>

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

			printWindow.document.write("<style type='text/css' media='print'>@page { size: portrait; } th, td { font-size: 8pt; } </style>");

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
			/*	var options =  {
		        format: "dd/mm/yyyy",
		        language: "pt-BR",
		        clearBtn:true,
		    }
			$('.datepicker1').datepicker(options);
			$("#cld_pagameto").on("click", function(){ $("#pagamentoData").trigger("focus"); });
			$("#cld_dtaInicial").on("click", function(){ $("#dtaInicial").trigger("focus"); });
			$("#cld_dtaFinal").on("click", function(){ $("#dtaFinal").trigger("focus"); });

			$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
			$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});*/
		});
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

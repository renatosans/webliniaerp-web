<?php
	include_once "util/login/restrito.php";
	restrito();
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

  <body class="overflow-hidden" ng-controller="RelatorioPosicaoEstoqueController" ng-cloak>
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
							<h3 class="m-bottom-xs m-top-xs">Relatório Posição de Estoque</h3>
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
						<form role="form">
							<div class="row">
								<div class="col-sm-2">
									<div class="form-group">
										<label class="control-label">Data Inicial</label>
										<div class="input-group">
											<input readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="dta_inicial" class="datepicker form-control text-center" ng-model="dta_inicial">
											<span class="input-group-addon" id="cld_dtaInicial"><i class="fa fa-calendar"></i></span>
										</div>
									</div>	
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="control-label">Data Final</label>
										<div class="input-group">
											<input readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="dta_final" class="datepicker form-control text-center" ng-model="dta_final">
											<span class="input-group-addon" id="cld_dtaFinal"><i class="fa fa-calendar"></i></span>
										</div>
									</div>	
								</div>

								<!--<div class="col-sm-4">
									<div class="form-group">
										<label class="control-label">Depósito</label>
										<div class="input-group">
											<input ng-click="selDepositoNewPosicao()" type="text" class="form-control" ng-model="busca.nome_deposito" readonly="readonly" style="cursor: pointer;"/>
											<span class="input-group-btn">
												<button ng-click="selDepositoNewPosicao()" type="button"  class="btn"><i class="fa fa-sitemap"></i></button>
											</span>
										</div>
									</div>
								</div>-->
							</div>
						</form>
					</div>

					<div class="panel-footer clearfix">
						<div class="pull-right">
							<button type="button" class="btn btn-sm btn-primary" ng-click="loadPosicoes()"><i class="fa fa-filter"></i> Aplicar Filtro</button>
							<button type="button" class="btn btn-sm btn-default" ng-click="resetFilter()"><i class="fa fa-times-circle"></i> Limpar Filtro</button>
							<button class="btn btn-sm btn-success hidden-print" ng-show="vendas.length > 0" id="invoicePrint"><i class="fa fa-print"></i> Imprimir</button>
							<button class="btn btn-sm btn-success hidden-print" ng-click="doExportExcel('data')" ng-if="(vendas != null)"><i class="fa fa-file-excel-o"></i> Exportar p/ Excel</button>
						</div>
					</div>
				</div>

				<br>

				<table id="data" class="table table-bordered table-hover table-striped table-condensed">
					<thead>
						<tr ng-if="(posicoes.length > 0)">
							<th class="text-center">ID</th>
							<th>Nome</th>
							<th class="text-center">Estoque Inicial</th>
							<th class="text-center">Compras</th>
							<th class="text-center">Vendas</th>
							<th class="text-center">Baixas</th>
							<th class="text-center">Saldo</th>
							<th class="text-center">Estoque</th>
							<th class="text-center">Diferença</th>
							<th class="text-center">Quebra Total</th>
							<th class="text-center"></th>
						</tr>
					</thead>
					<tbody>
						<tr ng-if="posicoes.length == 0">
							<td class="text-center" colspan="11">
								<i class="fa fa-refresh fa-spin"></i> Aguarde, carregando itens...
							</td>
						</tr>
						<tr ng-repeat="item in posicoes">
							<td class="text-center">{{ item.cod_produto }}</td>
							<td width="300">{{ item.nme_produto }}</td>
							<td class="text-center">{{ item.qtd_estoque_inicial | numberFormat : ',' : '.' : 0  }}</td>
							<td class="text-center">{{ item.qtd_compras | numberFormat : ',' : '.' : 0  }}</td>
							<td class="text-center">{{ item.qtd_vendas | numberFormat : ',' : '.' : 0  }}</td>
							<td class="text-center">{{ item.qtd_baixas | numberFormat : ',' : '.' : 0  }}</td>
							<td class="text-center">{{ item.qtd_saldo | numberFormat : ',' : '.' : 0  }}</td>
							<td class="text-center">{{ item.qtd_estoque | numberFormat : ',' : '.' : 0  }}</td>
							<td class="text-center">{{ item.qtd_diferenca | numberFormat : ',' : '.' : 0  }}</td>
							<td class="text-center">{{ item.qtd_quebra_total | numberFormat : ',' : '.' : 0  }}</td>
							<td class="text-center text-success" ng-if="(item.prc_quebra_faturamento <= 0.30)"><i class="fa fa-circle"></i></td>
							<td class="text-center text-warning" ng-if="(item.prc_quebra_faturamento > 0.30) && (item.prc_quebra_faturamento <= 0.49)"><i class="fa fa-circle"></i></td>
							<td class="text-center text-danger" ng-if="(item.prc_quebra_faturamento > 0.50)"><i class="fa fa-circle"></i></td>
						</tr>
						<tr ng-if="(posicoes.length > 0)">
							<td colspan="11" class="text-right text-success" ng-if="(med_prc_quebra_total <= 0.30)">
								<h3><small>Media de Quebra Total</small><br/><i class="fa fa-circle"></i> {{ med_prc_quebra_total | numberFormat : ',' : '.' : 2 }}%</h3>
							</td>
							<td colspan="11" class="text-right text-warning" ng-if="(med_prc_quebra_total > 0.30) && (med_prc_quebra_total <= 0.49)">
								<h3><small>Media de Quebra Total</small><br/><i class="fa fa-circle"></i> {{ med_prc_quebra_total | numberFormat : ',' : '.' : 2 }}%</h3>
							</td>
							<td colspan="11" class="text-right text-danger" ng-if="(med_prc_quebra_total > 0.50)">
								<h3><small>Media de Quebra Total</small><br/><i class="fa fa-circle"></i> {{ med_prc_quebra_total | numberFormat : ',' : '.' : 2 }}%</h3>
							</td>
						</tr>
					</tbody>
				</table>
				<span ng-if="(msg_error)" class="alert alert-{{ (status == 404) ? 'warning' : ((status == 500) ? 'danger' : '') }}">{{ msg_error }}</span>
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

	<!-- /Modal depositos-->
	<div class="modal fade" id="list_depositos_new" style="display:none">
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
								<tr ng-show="depositos == null">
                                    <th class="text-center" colspan="9" style="text-align:center"><i class='fa fa-refresh fa-spin'></i> Carregando ...</th>
                                </tr>
                                <tr ng-show="depositos.length == 0">
                                    <th colspan="4" class="text-center">Não a resultados para a busca</th>
                                </tr>
									<tr ng-repeat="item in depositos">
										<td>{{ item.nme_deposito }}</td>
										<td width="50" align="center">
											<button type="button" class="btn btn-xs btn-success" ng-click="addDepositoNewPosicao(item)">
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
				    		<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao_depositos.length > 1">
								<li ng-repeat="item in paginacao_depositos" ng-class="{'active': item.current}">
									<a href="" ng-click="loadDepositos(item.offset,item.limit)">{{ item.index }}</a>
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

	<!-- Moment -->
	<script src="js/moment/moment.min.js"></script>

	<script src="js/jquery.noty.packaged.js"></script>

	<!-- Bower Components -->	
	<script src="bower_components/noty/lib/noty.min.js" type="text/javascript"></script>
    <script src="bower_components/mojs/build/mo.min.js" type="text/javascript"></script>

	<!-- Extras -->
	<script src="js/extras.js"></script>

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
	<script src="js/angular-controller/rel_posicao_estoque-controller.js"></script>

	<script type="text/javascript">
		$(document).ready(function() {
			$('.datepicker').datepicker();
			$("#cld_pagameto").on("click", function(){ $("#pagamentoData").trigger("focus"); });
			$("#cld_dtaInicial").on("click", function(){ $("#dtaInicial").trigger("focus"); });
			$("#cld_dtaFinal").on("click", function(){ $("#dtaFinal").trigger("focus"); });

			$('.datepicker').on('changeDate', function(ev){
				$(this).datepicker('hide');
				$(this).trigger('change');
			});
			$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});
		});
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>
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

	<!-- Datepicker -->
	<link href="css/datepicker/bootstrap-datepicker.css" rel="stylesheet"/>

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
		.panel.panel-default {
		    overflow: visible !important;
		}

	</style>
  </head>

  <body class="overflow-hidden" ng-controller="PedidoTransferenciaController" ng-cloak>
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
					 <li class="active"><i class="fa fa-sitemap"></i><a href="depositos.php"> Depósitos</a></li>
					 <li class="active"><i class="fa fa-list-ol"></i><a href="estoque.php"> Controle de Estoque</a></li>
					 <li class="active"><i class="fa fa-arrows-h"></i> Transferências</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-arrows-h "></i> Transferência</h3>
					<br/>
					<a ng-if="!isNumeric(transferencia.id)" class="btn btn-info" id="btn-novo"  ng-click="showBoxNovo()"><i class="fa fa-plus-circle"></i> Solicitar Transferência</a>
					<a  class="btn btn-primary"  href="pedido_transferencia_recebido.php"><i class="fa fa-paper-plane-o"></i> Enviar Mercadoria</a>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md">
				<div class="alert alert-sistema" style="display:none"></div>

				<div class="panel panel-default" id="box-novo" style="display:none">
					<div class="panel-heading" ng-if="!isNumeric(transferencia.id)"> <i class="fa fa-plus-circle"></i> Nova Transferência</div>
					<div class="panel-heading" ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3"> <i class="fa fa-check-circle-o "></i> Recebendo Pedido de Transferência #{{transferencia.id }} </div>
					<div class="panel-heading" ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 4"> <i class="fa fa-edit "></i> Editando Pedido de Transferência #{{transferencia.id }} </div>
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-12"><div style="display: none" class="alert alert-transferencia-form"></div></div>
						</div>
						<div class="row" ng-if="!isNumeric(transferencia.id) || transferencia.id_status_transferencia == 4">
							<div class="col-sm-5" id="id_empreendimento_transferencia">
								<label class="control-label">Selecione o empreendimento para o qual deseja solicitar produtos:</label>
								<div class="input-group">
						            <input ng-model="transferencia.nome_empreendimento" ng-disabled="true" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="showEmpreendimentos()" tabindex="-2" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-building-o"></i>
						            	</button>
						            </div>
						        </div>
							</div>
						</div>
						<!--<div class="row" ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3">
							<div class="col-sm-4">
								<div class="form-group" id="id_deposito_principal">
									<label class="control-label">Deposito</label>
									<div class="input-group">
										<input ng-click="selDeposito()" type="text" class="form-control" ng-model="nome_deposito_principal" readonly="readonly" style="cursor: pointer;" />
										<span class="input-group-btn">
											<button ng-click="selDeposito()" type="button"  class="btn"><i class="fa fa-sitemap"></i></button>
										</span>
									</div>
								</div>
							</div>
						</div>
						 -->
						 <br/>
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group" id="produtos">
										<table ng-if="transferencia.flg_controle_validade!=1" class="table table-bordered table-condensed table-striped table-hover">
											<thead>
												<tr>
													<td colspan="{{ ( isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3 ) && 12 || 6 }}"><i class="fa fa-archive"></i> Produtos</td>
													<td width="60" align="center"  ng-if="!isNumeric(transferencia.id) || transferencia.id_status_transferencia == 4">
													<button class="btn btn-xs btn-primary" tooltip title="Selecionar produto(s)" ng-disabled="!isNumeric(transferencia.id_empreendimento_transferencia)" ng-click="showProdutos()"><i class="fa fa-plus-circle"></i></button>
													</td>
												</tr>
											</thead>
											<tbody>
												<thead>
													<tr>
														<th rowspan="2" style=" {{ (isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3) && 'line-height: 40px' || '' }} ">ID</th>
														<th rowspan="2" style=" {{ (isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3) && 'line-height: 40px' || '' }} ">Produto</th>
														<th rowspan="2" style=" {{ (isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3) && 'line-height: 40px' || '' }} ">Fabricante</th>
														<th rowspan="2" style=" {{ (isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3) && 'line-height: 40px' || '' }} ">Peso</th>
														<th rowspan="2" style=" {{ (isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3) && 'line-height: 40px' || '' }} ">Sabor</th>
														
														<th colspan="3" style="width:450px" class="text-center" 
															ng-if="showProductCost(transferencia)">
															Valor de Custo
														</th>
														
														<th rowspan="2" style=" {{ (isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3) && 'line-height: 40px' || '' }} ">Qtd.Pedida</th>
														<th rowspan="2" style=" {{ (isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3) && 'line-height: 40px' || '' }} ;width:100px" ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3">Qtd.Transferida</th>
														<th rowspan="2" style=" {{ (isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3) && 'line-height: 40px' || '' }} " ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3">Qtd.Recebida</th>
														<th rowspan="2" style=" {{ (isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3) || '' }} " ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3" width="150" class="text-center">
															Depósito de entrada <br>
															<button class="btn btn-xs btn-info" style="margin-top:5px;" ng-click="selDeposito()" tooltip data-placement="top" title="Selecionar depósito para todos os produtos"><i class="fa fa-sitemap"></i></button>
														</th>
														<th rowspan="2" ng-if="!isNumeric(transferencia.id) || transferencia.id_status_transferencia == 4" ></th>
													</tr>
													<tr ng-if="showProductCost(transferencia)">
														<th class="text-center">Atual</th>
														<th class="text-center">Sugerido</th>
														<th class="text-center">Atualizar?</th>
													</tr>
												</thead>
												<tr ng-show="(transferencia.produtos.length == 0)">
													<td colspan="{{ ( isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3 ) && 7 || 8 }}" align="center">Nenhum produto selecionado</td>
												</tr>
												<tr ng-repeat="item in transferencia.produtos">
													<td>{{ item.id_produto }}</td>
													<td>{{ item.nome }}</td>
													<td>{{ item.nome_fabricante }}</td>
													<td>{{ item.peso }}</td>
													<td>{{ item.sabor }}</td>
													<td ng-if="showProductCost(transferencia)">
														R$ {{ item.vlr_custo_real | numberFormat:2:',':'.' }}
													</td>
													<td ng-if="showProductCost(transferencia)">
														R$ {{ item.vlr_custo_sugerido | numberFormat:2:',':'.' }}
													</td>
													<td ng-if="showProductCost(transferencia)"> 
														<div class="form-group">
															<label class="label-radio inline">
																<input ng-model="item.atualizar_custo" value="1" type="radio" class="inline-radio"/>
																<span class="custom-radio"></span>
																<span>Sim</span>
															</label>
															<label class="label-radio inline">
																<input ng-model="item.atualizar_custo" value="0" type="radio" class="inline-radio"/>
																<span class="custom-radio"></span>
																<span>Não</span>
															</label>
														</div>
													</td>
													<td ng-if="!isNumeric(transferencia.id) || transferencia.id_status_transferencia == 4" width="75" id="td-trasnferencia-qtd-pedida-{{ item.id_produto }}">
														<input onKeyPress="return SomenteNumero(event);"  ng-model="item.qtd_pedida" type="text" class="form-control input-xs" />
													</td>
													<td class="text-center" ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3">{{ item.qtd_pedida }}</td>
													<td class="text-center" ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3">{{ item.qtd_transferida }}</td>
													<td ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3" width="100" id="td-trasnferencia-qtd-recebida-{{ item.id_produto }}">
														<input onKeyPress="return SomenteNumero(event);"  ng-model="item.qtd_recebida" type="text" class="form-control input-xs" />
													</td>
													<td ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3" id="td-trasnferencia-id-deposito-entrada-{{ item.id_produto }}" >
														<select chosen ng-change="" 
													    option="depositos_chosen"
													    ng-model="item.id_deposito_entrada"
													    ng-options="deposito.id as deposito.nme_deposito for deposito in depositos_chosen">
														</select>
													</td>
													<td ng-if="!isNumeric(transferencia.id) || transferencia.id_status_transferencia == 4" align="center">
														<button class="btn btn-xs btn-danger" ng-click="excluirProdutoLista($index)"><i class="fa fa-trash-o"></i></button>
													</td>
												</tr>
											</tbody>
										</table>
										<table ng-if="transferencia.flg_controle_validade==1" class="table table-bordered table-condensed table-striped table-hover">
											<thead>
												<tr>
													<td colspan="{{ ( isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3 ) && 13 || 7 }}"><i class="fa fa-archive"></i> Produtos</td>
													<td width="60" align="center"  ng-if="!isNumeric(transferencia.id) || transferencia.id_status_transferencia == 4">
													<button class="btn btn-xs btn-primary" ng-disabled="!isNumeric(transferencia.id_empreendimento_transferencia)" ng-click="showProdutos()"><i class="fa fa-plus-circle"></i></button>
													</td>
												</tr>
											</thead>
											<tbody>
												<thead>
													<tr>
														<th rowspan="2" style=" {{ (isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3) && 'line-height: 40px' || '' }} ">ID</th>
														<th rowspan="2" style=" {{ (isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3) && 'line-height: 40px' || '' }} ">Produto</th>
														<th rowspan="2" style=" {{ (isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3) && 'line-height: 40px' || '' }} ">Fabricante</th>
														<th rowspan="2" style=" {{ (isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3) && 'line-height: 40px' || '' }} ">Peso</th>
														<th rowspan="2" style=" {{ (isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3) && 'line-height: 40px' || '' }} ">sabor</th>
														<th colspan="3" style="width:450px" class="text-center" ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3">Valor de Custo</th>
														<th rowspan="2" style=" {{ (isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3) && 'line-height: 40px' || '' }} ">Qtd.Pedida</th>
														<th rowspan="2" style=" {{ (isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3) && 'line-height: 40px' || '' }} ;text-align:center" ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3">Validade</th>
														<th rowspan="2" style=" {{ (isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3) && 'line-height: 40px' || '' }} ;width:100px" ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3">Qtd. trans.</th>
														<th rowspan="2" style=" {{ (isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3) && 'line-height: 40px' || '' }} " ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3">Qtd. recebida</th>
														<th rowspan="2" style=" {{ (isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3) && 'line-height: 40px' || '' }} " ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3" width="150">
															Deposito
															<button style="float:right" class="btn btn-xs btn-info" ng-click="selDeposito()" tooltip data-placement="top" title="Selecionar deposito para todos os itens"><i class="fa fa-sitemap"></i></button>
														</th>
														<th rowspan="2" ng-if="!isNumeric(transferencia.id) || transferencia.id_status_transferencia == 4" ></th>
													</tr>
													<tr ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3">
														<th class="text-center">Atual</th>
														<th class="text-center">Sugerido</th>
														<th class="text-center">atualizar?</th>
													</tr>
												</thead>
												<tr ng-show="(transferencia.produtos.length == 0)">
													<td colspan="{{ ( isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3 ) && 7 || 8 }}" align="center">Nenhum produto selecionado</td>
												</tr>
												<tr ng-repeat="item in transferencia.produtos">
													<td>{{ item.id_produto }}</td>
													<td>{{ item.nome }}</td>
													<td>{{ item.nome_fabricante }}</td>
													<td>{{ item.peso }}</td>
													<td>{{ item.sabor }}</td>
													<td ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3">R$ {{ item.vlr_custo_real | numberFormat:2:',':'.' }}</td>
													<td ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3">R$ {{ item.vlr_custo_sugerido | numberFormat:2:',':'.' }}</td>
													<td ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3"> 
														<div class="form-group">
															<label class="label-radio inline">
																<input ng-model="item.atualizar_custo" value="1" type="radio" class="inline-radio"/>
																<span class="custom-radio"></span>
																<span>Sim</span>
															</label>
															<label class="label-radio inline">
																<input ng-model="item.atualizar_custo" value="0" type="radio" class="inline-radio"/>
																<span class="custom-radio"></span>
																<span>Não</span>
															</label>
														</div>
													</td>
													<td ng-if="!isNumeric(transferencia.id) || transferencia.id_status_transferencia == 4" width="75" id="td-trasnferencia-qtd-pedida-{{ item.id_produto }}">
														<input onKeyPress="return SomenteNumero(event);"  ng-model="item.qtd_pedida" type="text" class="form-control input-xs" />
													</td>
													<td class="text-center" ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3">{{ item.qtd_pedida }}</td>
													<td class="text-center" ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3 && item.dta_validade != '2099-12-31'">{{ item.dta_validade | date }}</td>
													<td class="text-center" ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3 && item.dta_validade == '2099-12-31'"> </td>
													<td class="text-center" ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3">{{ item.qtd_transferida }}</td>
													
													<!-- <td ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3" width="100" id="td-trasnferencia-qtd-recebida-{{ item.id_produto }}">
														<input onKeyPress="return SomenteNumero(event);"  ng-model="item.qtd_recebida" type="text" class="form-control input-xs" />
													</td>-->


													<td  width="100" align="center" ng-if="item.qtd_transferida > 0" id="td-trasnferencia-qtd-recebida-{{ item.id_produto }}">
														<div class="input-group" id="dtaInicialDiv">
														<input ng-disabled="true" onKeyPress="return SomenteNumero(event);" style="width: 75px" ng-value="somarQtdRecebida(item)"  type="text" class="form-control input-sm" />
														<span  id="btnDtaInicial" class="input-group-addon"
														 href=""
														 popover2
														 model="item.validades"
														 title="Validades"
														 func="ctrl"
														 placement="left"
														 content='
															 <table class="table table-bordered table-condensed table-striped table-hover">
															 	<thead>
															 		<th class="text-center">Validade</th>
															 		<th class="text-center">Qtd. Transferida</th>
															 		<th class="text-center">Qtd. Recebida</th>
															 	</thead>
															 	<tr ng-repeat="item in model">
															 		<td class="text-center" ng-bind="item.dta_validade|date" ng-if="item.dta_validade != %272099-12-31%27"></td>
															 		<td class="text-center" ng-if="item.dta_validade == %272099-12-31%27"></td>
															 		<td class="text-center" ng-bind="item.qtd_transferida"></td>
															 		<td width="100" ng-class="{%27has-error%27: item.tooltip != undefined }" >
															 			<input controll-tooltip="item.tooltip" ng-blur="func.clearTooltip(item)"  somente-numeros ng-keyUp="func.vericarQtdByValidade(item,%27body%27)"   ng-model="item.qtd_recebida"  type="text" class="form-control input-xs text-center">
					           										</td>
															 	</tr>
															 </table>
													 	'
														><i class="fa fa-calendar"></i></span>
														</div>
													</td>
													<td  width="100" align="center" ng-if="item.qtd_transferida == 0">
													</td>



													<td ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3" id="td-trasnferencia-id-deposito-entrada-{{ item.id_produto }}" >
														<select chosen ng-change="" 
													    option="depositos_chosen"
													    ng-model="item.id_deposito_entrada"
													    ng-options="deposito.id as deposito.nme_deposito for deposito in depositos_chosen">
														</select>
													</td>
													<td ng-if="!isNumeric(transferencia.id) || transferencia.id_status_transferencia == 4" align="center">
														<button class="btn btn-xs btn-danger" ng-click="excluirProdutoLista($index)"><i class="fa fa-trash-o"></i></button>
													</td>
												</tr>
											</tbody>
										</table>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-footer">
						<div class="row">
							<span class="pull-right">
							<div class="col-sm-12 pull-right">
								<button ng-click="cancelar()" class="btn btn-danger btn-sm"><i class="fa fa-times-circle"></i> Cancelar</button>
							<button ng-if="!isNumeric(transferencia.id) || transferencia.id_status_transferencia == 4" ng-click="salvarTransferencia(4,$event)" class="btn btn-success btn-sm" id="salvar-transferencia" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde...">
								<i class="fa fa-save"></i> Salvar
							</button>
							<button ng-if="!isNumeric(transferencia.id) || transferencia.id_status_transferencia == 4 " ng-click="salvarTransferencia(1,$event)" class="btn btn-primary btn-sm" id="salvar-transferencia" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde...">
								<i class="fa fa-paper-plane-o"></i> Salvar e solicitar
							</button>
							<button ng-if="isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3" ng-click="receberTransferencia()" class="btn btn-success btn-sm" id="receber-transferencia" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde...">
								<i class="fa fa-save"></i> Receber
							</button>
							</div>
							</span>
						</div>
					</div>
				</div><!-- /panel -->

				<div  class="panel panel-default hidden-print" style="margin-top: 15px;">
					<div class="panel-heading"><i class="fa fa-calendar"></i> Filtros</div>

					<div class="panel-body">
						<form role="form">
							<div class="row">
								<div class="col-lg-2">
									<div class="form-group">
										<label class="control-label">Data</label>
										<div class="input-group">
											<input date-picker ng-model="busca.data" readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="dtaInicial" class="datepicker form-control text-center">
											<span class="input-group-addon" id="cld_dtaInicial"><i class="fa fa-calendar"></i></span>
										</div>
									</div>
								</div>

								<div class="col-lg-2">
									<div class="form-group">
										<label class="control-label">Etapa</label>
										<select chosen
									    	option="etapas"
									    	ng-model="busca.id_etapa_data"
									    	ng-options="etapa.id as etapa.nme_etapa for etapa in etapas">
										</select>
									</div>
								</div>

								<div class="col-lg-2">
									<div class="form-group">
										<label class="control-label">Status</label>
										<select chosen
									    	option="status"
									    	ng-model="busca.id_status"
									    	ng-options="banco.id_status_transferencia_estoque as banco.dsc_status_transferencia_estoque for banco in status">
										</select>
									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label class="control-label">Usuário</label>
										<div class="input-group">
											<input ng-click="showCliente()" type="text" class="form-control" ng-model="busca.usuario_pedido.nome" readonly="readonly" style="cursor: pointer;">
											<span class="input-group-btn">
												<button ng-click="showCliente()"  type="button" class="btn"><i class="fa fa-archive"></i></button>
											</span>
										</div>
									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label class="control-label">Empreendimento</label>
										<div class="input-group">
											<input ng-click="showEmpreendimentosBusca()" type="text" class="form-control" ng-model="busca.empreendimento_busca.nome_empreendimento" readonly="readonly" style="cursor: pointer;">
											<span class="input-group-btn">
												<button ng-click="showEmpreendimentosBusca(0,10)" type="button" class="btn"><i class="fa fa-archive"></i></button>
											</span>
										</div>
									</div>
								</div>
							</div>
							
						</form>
					</div>

					<div class="panel-footer clearfix">
						<div class="pull-right">
							<button type="button" class="btn btn-sm btn-primary" ng-click="loadtransferencias(0,10)"><i class="fa fa-filter"></i> Aplicar Filtro</button>
							<button type="button" class="btn btn-sm btn-default" ng-click="resetFilter()"><i class="fa fa-times-circle"></i> Limpar Filtro</button>
						</div>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-tasks"></i> Pedidos de Transferência Realizados</div>

					<div class="panel-body">
						<div class="row">
							<div class="col-sm-12"><div style="display: none" class="alert alert-transferencia-lista"></div></div>
						</div>
						<div class="table-responsive">
							<table class="table table-bordered table-condensed table-striped table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Data Pedido</th>
									<th>Data Transferência</th>
									<th>Data Recebimento</th>
									<th>Solicitante</th>
									<th>Empreendimento</th>
									<th>Status</th>
									<th width="80" style="text-align: center;">Ações</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-show="listaTransferencias.transferencias == null">
									<td colspan="7" class="text-center">
										<i class='fa fa-refresh fa-spin'></i> Carregando...
									</td>
								</tr>
								<tr ng-show="listaTransferencias.transferencias.length == 0">
									<td colspan="7" class="text-center">
										Nenhuma transferência encontrada
									</td>
								</tr>
								<tr ng-repeat="item in listaTransferencias.transferencias" bs-tooltip>
									<td width="80">{{ item.id }}</td>
									<td>{{ item.dta_pedido | dateFormat : 'dateTime' }}</td>
									<td>{{ item.dta_transferencia | dateFormat : 'dateTime' }}</td>
									<td>{{ item.dta_recebido | dateFormat : 'dateTime' }}</td>
									<td>{{ item.nome_usuario_pedido }}</td>
									<td>{{ item.nome_empreendimento_transferencia }}</td>
									<td>{{ item.dsc_status_transferencia_estoque }}</td>
									<td align="center">
										<button type="button" ng-show="item.id != transferencia.id && item.id_status_transferencia == 2" data-loading-text="<i class='fa fa-refresh fa-spin'></i>" ng-click="editTransferencia($index,$event,3)" title="Receber pedido" class="btn btn-xs btn-info" data-toggle="tooltip">
											<i class="fa fa-check-square-o"></i>
										</button>
										<button type="button" ng-show="item.id == transferencia.id && item.id_status_transferencia == 2" data-loading-text="<i class='fa fa-refresh fa-spin'></i>"  title="Em edição" class="btn btn-xs btn-success" data-toggle="tooltip">
											<i class="fa fa-check-square-o"></i>
										</button>

										<button type="button" ng-show="item.id != transferencia.id && item.id_status_transferencia == 4" data-loading-text="<i class='fa fa-refresh fa-spin'></i>" ng-click="editTransferencia($index,$event,4)" title="editar pedido" class="btn btn-xs btn-warning" data-toggle="tooltip">
											<i class="fa fa-edit"></i>
										</button>
										<button type="button" ng-show="item.id == transferencia.id && item.id_status_transferencia == 4" data-loading-text="<i class='fa fa-refresh fa-spin'></i>"  title="Em edição" class="btn btn-xs btn-success" data-toggle="tooltip">
											<i class="fa fa-edit"></i>
										</button>

										<button type="button" ng-click="detalhesPedido(item)" title="Detalhes" class="btn btn-xs btn-primary" data-toggle="tooltip">
											<i class="fa fa-tasks"></i>
										</button>
									</td>
								</tr>
							</tbody>
						</table>
						</div>
					</div>
					<div class="panel-footer">
						<div class="row">
							<div class="col-sm-12">
								<ul class="pagination pagination-xs m-top-none pull-right" ng-show="listaTransferencias.paginacao.length > 1">
									<li ng-repeat="item in listaTransferencias.paginacao" ng-class="{'active': item.current}">
										<a href="" ng-click="loadtransferencias(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /main-container -->

		<!-- Modais
		================================================== -->

		<!-- /Modal empreendimento-->
		<div class="modal fade" id="list_empreendimentos" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Empreendimentos</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.empreendimento" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadAllEmpreendimentos(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div> <!-- /input-group-btn -->
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>

						<br>

						<div class="row">
							<div class="col-sm-12">
								<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(empreendimento.length != 0)">
										<tr>
											<th colspan="2">Nome</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(empreendimento.length == 0)">
											<td colspan="2">Não há empreendimentos cadastrados</td>
										</tr>
										<tr ng-repeat="item in empreendimentos">
											<td>{{ item.nome_empreendimento }}</td>
											<td width="50" align="center">
												<button data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." ng-show="transferencia.id_empreendimento != item.id" type="button" class="btn btn-xs btn-success" ng-click="addEmpreendimento(item,$event)">
													<i class="fa fa-check-square-o"></i> Selecionar
												</button>
												<button ng-show="transferencia.id_empreendimento == item.id" ng-show="existsAcessorio(item)" ng-disabled="true" class="btn btn-primary btn-xs" type="button">
                                                	<i class="fa fa-check-circle-o"></i> Selecionado
                                            	</button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>

						<div class="row">
				    		<div class="col-sm-12">
				    			<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao.empreendimentos.length > 1">
									<li ng-repeat="item in paginacao.empreendimentos" ng-class="{'active': item.current}">
										<a href="" ng-click="loadAllEmpreendimentos(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
				    		</div>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal produtos-->
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
						            <input ng-model="busca.produto" id="foco" ng-enter="loadProdutos(0,10)" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadProdutos(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
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
									<thead ng-show="(produtos.length != 0)">
										<tr>
											<th >ID</th>
											<th >Nome</th>
											<th >Fabricante</th>
											<th >Tamanho</th>
											<th >Sabor/Cor</th>
											<th >Quantidade</th>
											<th >Ações</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(produtos.length == 0)" class="text-center">
											<td colspan="7">Nenhum produto encontrado</td>
										</tr>
										<tr ng-show="produtos == null" class="text-center">
											<td colspan="7" ><i class='fa fa-refresh fa-spin'></i> Carregando...</td>
										</tr>
										<tr ng-repeat="item in produtos">
											<td>{{ item.id }}</td>
											<td>{{ item.nome }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td>{{ item.peso }}</td>
											<td>{{ item.sabor }}</td>
											<td  width="50"><input  ng-model="item.qtd_pedida" type="text" class="form-control input-xs" /></td>
											<td width="50" align="center">
												<button ng-show="!produtoSelected(item.id)" type="button" id="selecionar" class="btn btn-xs btn-success" ng-click="addProduto(item)">
													<i class="fa fa-check-square-o"></i> Selecionar
												</button>
												<button ng-show="produtoSelected(item.id)" ng-show="existsAcessorio(item)" ng-disabled="true" class="btn btn-primary btn-xs" type="button">
                                                	<i class="fa fa-check-circle-o"></i> Selecionado
                                            	</button>
											</td>
										</tr>
									</tbody>
								</table>
				   			</div>
				   		</div>

				   		<div class="row">
					    	<div class="col-sm-12">
					    		<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao.produtos.length > 1">
									<li ng-repeat="item in paginacao.produtos" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadProdutos(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
					    	</div>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal Detalhes transferencia -->
		<div class="modal fade" id="modal-detalhes-transferencia" style="display:none">
  			<div class="modal-dialog modal-lg">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Detalhes da Transferência</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-sm-12">
								<b>ID: </b> {{view.transferencia.id}}<br/>
								<b>Dta. Pedido: </b> {{view.transferencia.dta_pedido | dateFormat : 'dateTime'}}<br/>
								<b>Usuario: </b> {{view.transferencia.nome_usuario_pedido}}<br/>
								<b>Empreendimento: </b> {{view.transferencia.nome_empreendimento_transferencia}}<br/>
								<b>Status: </b> {{view.transferencia.dsc_status_transferencia_estoque}}<br/><br/>
							</div>
						</div>
				   		<div class="row">
				   			<div class="col-sm-12">
				   				<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(view.transferencia.itens.length != 0)">
										<tr>
											<th >ID produto</th>
											<th >Produto</th>
											<th class="text-center" >Qtd. Pedida</th>
											<th ng-show="item.id_status_transferencia == 2" class="text-center">Qtd. Transferida</th>
											<th ng-show="item.id_status_transferencia == 3" class="text-center">Qtd. Entregue</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in view.transferencia.itens">
											<td>{{ item.id_produto }}</td>
											<td>{{ item.nome_produto }}</td>
											<td class="text-center">{{ item.qtd_pedida }}</td>
											<td ng-show="item.id_status_transferencia == 2" class="text-center">{{ item.qtd_transferida }}</td>
											<td ng-show="item.id_status_transferencia == 3" class="text-center">{{ item.qtd_entregeue }}</td>
										</tr>
									</tbody>
								</table>
				   			</div>
				   		</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal depositos-->
		<div class="modal fade" id="list_depositos" style="display:none">
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
					    		<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao_depositos.length > 1">
									<li ng-repeat="item in paginacao_depositos" ng-class="{'active': item.current}">
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
						            <input ng-model="busca.clientes"  ng-enter="loadCliente(0,10)" type="text" class="form-control input-sm">
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
									<thead>
										<tr>
											<th >Nome</th>
											<th >Apelido</th>
											<th >Perfil</th>
											<th colspan="2">Selecionar</th>
										</tr>
									</thead>
									<tr ng-if="usuarios.itens == null">
										<th class="text-center" colspan="9" style="text-align:center">
											<i class='fa fa-refresh fa-spin'></i>
											<strong>Carregando...</strong>
										</th>
									</tr>
									<tr ng-if="usuarios.itens.length == 0">
										<th colspan="4" class="text-center">Nenhum cliente encontrado</th>
									</tr>
									<tbody>
										<tr ng-repeat="item in usuarios.itens">
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
				    			<ul class="pagination pagination-xs m-top-none pull-right" ng-show="usuarios.paginacao.length > 1">
									<li ng-repeat="item in usuarios.paginacao" ng-class="{'active': item.current}">
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

		<!-- /Modal empreendimento Busca -->
		<div class="modal fade" id="list_empreendimentos_busca" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Empreendimentos</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.str_empreendimento_busca" ng-enter="loadEmpreendimentosBusca(0,10)" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadEmpreendimentosBusca(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div> <!-- /input-group-btn -->
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>

						<br>

						<div class="row">
							<div class="col-sm-12">
								<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(empreendimentos_busca.itens.length != 0)">
										<tr>
											<th colspan="2">Nome</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-if="empreendimentos_busca.itens == null">
											<th class="text-center" colspan="9" style="text-align:center">
												<i class='fa fa-refresh fa-spin'></i>
												<strong>Carregando...</strong>
											</th>
										</tr>
										<tr ng-show="(empreendimentos_busca.itens.length == 0)">
											<td colspan="2">Não há resultado para a busca</td>
										</tr>
										<tr ng-repeat="item in empreendimentos_busca.itens">
											<td>{{ item.nome_empreendimento }}</td>
											<td width="50" align="center">
												<button data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." ng-show="busca.empreendimento_busca.id != item.id" type="button" class="btn btn-xs btn-success" ng-click="addEmpreendimentoBusca(item,$event)">
													<i class="fa fa-check-square-o"></i> Selecionar
												</button>
												<button ng-show="busca.empreendimento_busca.id == item.id" ng-show="existsAcessorio(item)" ng-disabled="true" class="btn btn-primary btn-xs" type="button">
                                                	<i class="fa fa-check-circle-o"></i> Selecionado
                                            	</button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>

						<div class="row">
				    		<div class="col-sm-12">
				    			<ul class="pagination pagination-xs m-top-none pull-right" ng-show="empreendimentos_busca.paginacao.length > 1">
									<li ng-repeat="item in empreendimentos_busca.paginacao" ng-class="{'active': item.current}">
										<a href="" ng-click="loadEmpreendimentosBusca(item.offset,item.limit)">{{ item.index }}</a>
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

	<!-- Easy Modal -->
    <script src="js/eModal.js"></script>

	<!-- Cookie -->
	<script src='js/jquery.cookie.min.js'></script>

	<!-- Endless -->
	<script src="js/endless/endless.js"></script>

	<!-- Chosen -->
	<script src='js/chosen.jquery.min.js'></script>

	<!-- Moment -->
	<script src="js/moment/moment.min.js"></script>

	<!-- Datepicker -->
	<script src='js/datepicker/bootstrap-datepicker.js'></script>
	<script src='js/datepicker/bootstrap-datepicker.pt-BR.js'></script>

	<!-- UnderscoreJS -->
	<script type="text/javascript" src="bower_components/underscore/underscore.js"></script>

	<!-- Extras -->
	<script src="js/extras.js"></script>

	<!-- Moment -->
	<script src="js/moment/moment.min.js"></script>

	<script src="js/jquery.noty.packaged.js"></script>

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
	<script src="js/angular-controller/pedido_transferencia-controller.js?version=<?php echo date("dmY-His", filemtime("js/angular-controller/pedido_transferencia-controller.js")) ?>"></script>
	<script type="text/javascript"></script>>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

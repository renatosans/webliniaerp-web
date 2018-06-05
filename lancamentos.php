<?php
	include_once "util/login/restrito.php";
	restrito(array(1));
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

	<!-- Bower Components -->	
	<link href="bower_components/noty/lib/noty.css" rel="stylesheet">
	<link href="bower_components/np-autocomplete/dist/np-autocomplete.min.css" rel="stylesheet">


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

		.list-group-item {
			width: 100% !important;
			text-align: left;
		}

	</style>
  </head>

  <body class="overflow-hidden" ng-controller="LancamentosController" ng-cloak>
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
					 <li class="active"><i class="fa fa-money"></i> Lanç. Financeiros</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-money"></i> Lançamentos Financeiros</h3>
					<h6>Contas a Pagar e Receber</h6>
					<br/>
					<a class="btn btn-info hidden-print" id="btn-novo" ng-disabled="editing" ng-click="showBoxNovo()"><i class="fa fa-plus-circle"></i> Novo Lançamento</a>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md">
				<div class="alert alert-sistema" style="display:none"></div>

				<div class="panel panel-default hidden-print" id="box-novo" style="display:none">
					<div class="panel-heading"><i class="fa fa-plus-circle"></i> Novo Lançamento</div>

					<div class="panel-body">
						<div class="panel-body">
							<div class="alert alert-pagamento" style="display:none"></div>
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label class="label-radio inline">
												<input name="tipoLancamento" ng-model="flgTipoLancamento" value="1" type="radio" class="inline-radio">
												<span class="custom-radio"></span>
												<span class="text-danger">Despesa</span>
											</label>

											<label class="label-radio inline">
												<input name="tipoLancamento" ng-model="flgTipoLancamento" value="0" type="radio" class="inline-radio">
												<span class="custom-radio"></span>
												<span class="text-success">Receita</span>
											</label>

											<label class="label-radio inline">
												<input name="tipoLancamento" ng-model="flgTipoLancamento" value="2" type="radio" class="inline-radio">
												<span class="custom-radio"></span>
												<span class="text-bold">Transferência</span>
											</label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-12" ng-if="flgTipoLancamento == 0" id="id_cliente">
										<div class="form-group">
											<label class="control-label">Cliente</label>
											<div class="input-group">
												<input ng-click="selCliente(0,10)"  type="text" class="form-control" ng-model="cliente.nome" readonly="readonly" style="cursor: pointer;" />
												<span class="input-group-btn">
													<button ng-click="selCliente(0,10)" type="button" class="btn btn-info"><i class="fa fa-users"></i></button>
												</span>
											</div>
										</div>
									</div>
									<div class="col-sm-12" ng-if="flgTipoLancamento == 1"  id="id_fornecedor">
										<div class="form-group">
											<label class="control-label">Fornecedores</label>
											<div class="input-group">
												<input ng-click="selFornecedor(0,10)"  type="text" class="form-control" ng-model="fornecedor.nome_fornecedor" readonly="readonly" style="cursor: pointer;" />
												<span class="input-group-btn">
													<button ng-click="selFornecedor(0,10)" type="button" class="btn btn-info"><i class="fa fa-users"></i></button>
												</span>
											</div>
										</div>
									</div>
								</div>
								<div class="row" ng-if="flgTipoLancamento == 0 && cliente.vlr_saldo_devedor < 0">
									<div class="col-sm-12">
										<span style="font-weight:bold;color:#777">Saldo: </span>
										<span style="font-weight:bold;color:#E62C2C">{{cliente.vlr_saldo_devedor | numberFormat:2:',':'.'}}</span>
									</div>
								</div>
								<div class="row" ng-if="flgTipoLancamento == 0 && cliente.vlr_saldo_devedor >= 0">
									<div class="col-sm-12">
										<span style="font-weight:bold;color:#777">Saldo: </span>
										<span style="font-weight:bold;color:#1A7204">{{cliente.vlr_saldo_devedor | numberFormat:2:',':'.'}}</span>
									</div>
								</div>
								<div ng-show="flgTipoLancamento == 2">
									<div class="row">
										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">Data Transferência</label>
												<div class="input-group">
													<input readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="dta_transferencia" class="datepicker form-control text-center" ng-model="dta_pagamento">
													<span class="input-group-addon" id="cld_dtaInicial"><i class="fa fa-calendar"></i></span>
												</div>
											</div>	
										</div>
									</div>
									<div class="row">
										<div class="col-sm-3">
											<div class="form-group" id="id_conta_bancaria_origem">
												<label class="control-label">Conta de origem</label>
												<select ng-model="transferencia.id_conta_bancaria_origem" class="form-control">
													<option ></option>
													<option ng-repeat="item in contas" value="{{ item.id }}">{{ item.dsc_conta_bancaria }}</option>
												</select>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group" id="id_conta_bancaria_destino">
												<label class="control-label">Conta de destino</label>
												<select ng-model="transferencia.id_conta_bancaria_destino" class="form-control">
													<option ></option>
													<option ng-repeat="item in contas" value="{{ item.id }}">{{ item.dsc_conta_bancaria }}</option>
												</select>
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">Valor</label>
												<input class="form-control text-right" type="text" name="valor" ng-model="transferencia.vlr_transferencia" thousands-formatter>
											</div>
										</div>
										
									</div>
									<div class="row">
										<div class="col-sm-8">
											<div class="form-group">
												<label class="control-label">Observação</label>
												<textarea class="form-control" ng-model="transferencia.obs_transferencia" rows="5"></textarea>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-12">
										<hr>
									</div>
								</div>
								<div class="row" ng-if="flgTipoLancamento == 2">
									<div class="col-sm-12">
										<div class="pull-right">
											<button data-loading-text="<i class='fa fa-refresh fa-spin></i> Aguarde, salvando..." type="submit" class="btn btn-success" ng-click="salvarTransferencia()">
												<i class="fa fa-save"></i> Salvar
											</button>
										</div>
									</div>
								</div>
								<div class="row" ng-show="flgTipoLancamento == 0 || flgTipoLancamento == 1">
									<div class="col-sm-9">
										<div class="row">
											<div class="col-sm-4" id="pagamento_forma_pagamento">
												<label class="control-label">Forma de Pagamento</label>
												<select ng-model="pagamento.id_forma_pagamento" ng-change="selectChange(pagamento.id_forma_pagamento)" class="form-control">
													<option ng-if="pagamento.id_forma_pagamento != null" value=""></option>
													<option ng-repeat="item in formas_pagamento"  value="{{ item.id }}">{{ item.nome }}</option>
												</select>
											</div>
											<div class="col-sm-8">
												<div class="form-group" id="regimeTributario">
													<label class="ccontrol-label">Plano de conta </label> 
													<select chosen ng-change="ClearChosenSelect('cod_regime_tributario')"
													option="plano_contas"
													ng-model="pagamento.id_plano_conta"
													ng-options="plano.id as plano.dsc_completa for plano in plano_contas">
												</select>
											</div>
										</div>
										<div class="col-sm-4" id="pagamento_id_banco" ng-if="pagamento.id_forma_pagamento == 8">
											<div class="form-group" >
												<label class="control-label">Banco</label>
												<select chosen
												option="bancos"
												ng-model="pagamento.id_banco"
												ng-options="banco.id as banco.nome for banco in bancos">
											</select>
										</div>
									</div>

									<div class="col-sm-4" id="pagamento_agencia_transferencia" ng-if="pagamento.id_forma_pagamento == 8">
										<label class="control-label">Agência</label>
										<div class="form-group ">
											<input ng-model="pagamento.agencia_transferencia"  type="text" class="form-control" />
										</div>
									</div>

									<div class="col-sm-4" id="pagamento_conta_transferencia" ng-if="pagamento.id_forma_pagamento == 8">
										<label class="control-label">Conta</label>
										<div class="form-group ">
											<input ng-model="pagamento.conta_transferencia"  type="text" class="form-control" />
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-4" ng-if="pagamento.id_forma_pagamento != 8 ">
										<div class="form-group" id="id_conta_bancaria">
											<label class="control-label">Conta</label>
											<select ng-model="pagamento.id_conta_bancaria" class="form-control" ng-disabled="(pagamento.id_forma_pagamento == 5 || pagamento.id_forma_pagamento == 6) && (flgTipoLancamento == 0)">
												<option ></option>
												<option ng-repeat="item in contas" value="{{ item.id }}">{{ item.dsc_conta_bancaria }}</option>
											</select>
										</div>
									</div>
									<div class="col-sm-3" id="proprietario_conta_transferencia" ng-if="pagamento.id_forma_pagamento == 8">
										<label class="control-label">Proprietário</label>
										<div class="form-group ">
											<input ng-model="pagamento.proprietario_conta_transferencia" type="text" class="form-control" />
										</div>
									</div>
									<div  ng-class="{'col-sm-3':pagamento.id_forma_pagamento == 8,'col-sm-4':pagamento.id_forma_pagamento != 8}" ng-show="pagamento.id_forma_pagamento != 6 && pagamento.id_forma_pagamento != 2 && pagamento.id_forma_pagamento != 4">
										<div class="form-group cheque_data">
											<label class="control-label">Data do pagamento</label>
											<div class="input-group">
												<input readonly="readonly" style="background:#FFF;cursor:pointer" ng-model="pagamento.data" type="text" id="pagamentoData" class="datepicker form-control data-cc">
												<span class="input-group-addon" class="cld_pagameto"><i class="fa fa-calendar"></i></span>
											</div>
										</div>
									</div>
									<div class="col-sm-3" id="pagamento_id_conta_transferencia_destino" ng-if="pagamento.id_forma_pagamento == 8 ">
										<label class="control-label">Conta de Destino</label>
										<select ng-model="pagamento.id_conta_transferencia_destino" class="form-control input-sm">
											<option ng-repeat="item in contas" value="{{ item.id }}">#{{ item.id }} - {{ item.dsc_conta_bancaria }}</option>
										</select>
									</div>
									<!--<div class="col-sm-3" ng-show="pagamento.id_forma_pagamento == 8">
										<div class="form-group cheque_data">
											<label class="control-label">Data do pagamento</label>
											<div class="input-group">
												<input readonly="readonly" style="background:#FFF;cursor:pointer" ng-model="pagamento.data" type="text" id="pagamentoData" class="datepicker form-control data-cc">
												<span class="input-group-addon" class="cld_pagameto"><i class="fa fa-calendar"></i></span>
											</div>
										</div>
									</div>-->
									<div class="col-sm-4" id="pagamento_valor" ng-if="pagamento.id_forma_pagamento != 8">
										<label class="control-label">Valor</label>
										<div class="form-group ">
											<input ng-disabled="pagamento.id_forma_pagamento == 2 || pagamento.id_forma_pagamento == 4 " ng-model="pagamento.valor" thousands-formatter type="text" class="form-control" />
										</div>
									</div>
									<div class="col-sm-3" id="pagamento_valor" ng-if="pagamento.id_forma_pagamento == 8">
										<label class="control-label">Valor</label>
										<div class="form-group ">
											<input ng-disabled="pagamento.id_forma_pagamento == 2 || pagamento.id_forma_pagamento == 4 " ng-model="pagamento.valor" thousands-formatter type="text" class="form-control" />
										</div>
									</div>
									<div class="col-sm-4" id="numero_parcelas" ng-if="pagamento.id_forma_pagamento == 6 || pagamento.id_forma_pagamento == 2 || pagamento.id_forma_pagamento == 4">
										<label class="control-label">parcelas</label>
										<div class="form-group ">
											<input ng-blur="pushCheques()" ng-focus="qtdCheque()" ng-model="pagamento.parcelas" type="text" class="form-control" >
										</div>
									</div>

								</div>

						    	<div class="row">
						    		<div class="col-sm-4" ng-show="pagamento.id_forma_pagamento == 6">
											<div class="form-group cheque_data">
												<label class="control-label">Data da primeira parcela</label>
												<div class="input-group">
													<input readonly="readonly" style="background:#FFF;cursor:pointer" ng-model="pagamento.data" type="text" id="pagamentoData" class="datepicker form-control data-cc">
													<span class="input-group-addon" class="cld_pagameto"><i class="fa fa-calendar"></i></span>
												</div>
											</div>
									</div>
						    		<div class="col-sm-4" id="pagamento_maquineta" ng-if="(pagamento.id_forma_pagamento == 5 || pagamento.id_forma_pagamento == 6) && (flgTipoLancamento == 0) ">
						    			<label class="control-label">Maquineta</label>
										<select ng-model="pagamento.id_maquineta" ng-change="selIdMaquineta()" class="form-control">
											<option ng-repeat="item in maquinetas" value="{{ item.id_maquineta }}">#{{ item.id_maquineta }} - {{ item.dsc_conta_bancaria }}</option>
										</select>
									</div>
									<div class="col-sm-4" id="bandeiras" ng-if="(pagamento.id_forma_pagamento == 5 || pagamento.id_forma_pagamento == 6) && (flgTipoLancamento == 0) ">
						    			<label class="control-label">Bandeira</label>
										<select ng-model="pagamento.id_bandeira" class="form-control">
											<option ng-repeat="item in bandeiras" value="{{ item.id }}">{{ item.nome }}</option>
										</select>
									</div>
									<div style="clear: both;" ng-if="pagamento.id_forma_pagamento == 5">&nbsp;</div>

						    	</div>
						    	<div class="alert error-cheque" style="display:none"></div>
									<div class="row" ng-show="pagamento.id_forma_pagamento == 2" ng-repeat="item in cheques">
										<div class="col-sm-3">
											<div class="form-group cheque_data">
												<label class="control-label">Data</label>
												<div class="input-group">
													<input readonly="readonly" style="background:#FFF;cursor:pointer" ng-model="pagamento.data" type="text" id="pagamentoData" class="datepicker form-control chequeData">
													<span class="input-group-addon" class="cld_pagameto" ng-click="focusData($index)"><i class="fa fa-calendar"></i></span>
												</div>
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-group cheque_valor">
												<label class="control-label">Valor</label>
												<div class="form-group ">
						    						<input ng-blur="pushCheques()" ng-keyUp="calTotalCheque()"  thousands-formatter ng-model="item.valor_pagamento" type="text" class="form-control" >
						    					</div>
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-group cheque_banco" >
												<label class="control-label">Banco</label>
													<select chosen
													    option="bancos"
													    ng-model="item.id_banco"
													    ng-options="banco.id as banco.nome for banco in bancos">
													</select>
											</div>
										</div>

										<div class="col-sm-2">
											<div class="form-group cheque_cc">
												<label class="control-label">Núm. C/C</label>
												<input ng-model="item.num_conta_corrente" type="text" class="form-control">
											</div>
										</div>

										<div class="col-sm-2">
											<div class="form-group cheque_num">
												<label class="control-label">Núm. Cheque</label>
												<input ng-model="item.num_cheque" type="text" class="form-control">
											</div>
										</div>

										<div class="col-sm-1">
											<div class="row">
					    						<div class="col-sm-6">
													<label class="control-label"><br></label>
													<label class="label-checkbox">
														<input ng-model="item.flg_cheque_predatado" value="1" type="checkbox" id="toggleLine" ng-true-value="1" ng-false-value="0">
														<span class="custom-checkbox"></span>
														Pré?
													</label>
												</div>
												<div class="col-sm-6" ng-if="cheques.length > 1">
													<div class="form-group">
														<label class="control-label"><br></label>
														<label class="control-label">
															<i ng-click="delItemCheque($index)" class="fa fa-times-circle-o fa-lg" style="color: red;cursor:pointer"></i>
														</label>
													</div>
												</div>
											</div>
										</div>

									</div>
									<div class="row" ng-show="pagamento.id_forma_pagamento == 4" ng-repeat="item in boletos">
										<div class="col-sm-3">
											<div class="form-group boleto_data">
												<label class="control-label">Data</label>
												<div class="input-group">
													<input readonly="readonly" style="background:#FFF;cursor:pointer" ng-model="pagamento.data" type="text" id="pagamentoData" class="datepicker form-control boletoData">
													<span class="input-group-addon" class="cld_pagameto" ng-click="focusData($index)"><i class="fa fa-calendar"></i></span>
												</div>
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-group boleto_valor">
												<label class="control-label">Valor</label>
												<div class="form-group ">
						    						<input ng-blur="pushCheques()" ng-keyUp="calTotalBoleto()"  thousands-formatter ng-model="item.valor_pagamento" type="text" class="form-control" >
						    					</div>
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-group boleto_banco" >
												<label class="control-label">Banco</label>
												<select chosen
													    option="bancos"
													    ng-model="item.id_banco"
													    ng-options="banco.id as banco.nome for banco in bancos">
													</select>
											</div>
										</div>

										<div class="col-sm-2">
											<div class="form-group boleto_doc">
												<label class="control-label">Doc. Boleto</label>
												<input ng-model="item.doc_boleto" type="text" class="form-control">
											</div>
										</div>

										<div class="col-sm-2">
											<div class="form-group boleto_num">
												<label class="control-label">Núm. Boleto</label>
												<input ng-model="item.num_boleto" type="text" class="form-control">
											</div>
										</div>

										<div class="col-sm-1">
											<div class="row">
					    						<div class="col-sm-6">
													<label class="control-label"><br></label>
													<label class="label-checkbox">
														<input ng-model="item.status_pagamento" value="1" type="checkbox" id="toggleLine" ng-true-value="1" ng-false-value="0">
														<span class="custom-checkbox"></span>
														Pago?
													</label>
												</div>
												<div class="col-sm-6" ng-if="boletos.length > 1">
													<div class="form-group">
														<label class="control-label"><br></label>
														<label class="control-label">
															<i ng-click="delItemBoleto($index)" class="fa fa-times-circle-o fa-lg" style="color: red;cursor:pointer"></i>
														</label>
													</div>
												</div>
											</div>
										</div>

									</div>


									<div class="row" ng-if="pagamento.id_forma_pagamento != 2 && pagamento.id_forma_pagamento != 6 && pagamento.id_forma_pagamento != 4">
										<div class="col-sm-12">
											<div class="form-group">
												<label class="control-label">Status</label><br/>
												<label class="label-radio inline">
													<input name="status" ng-model="pagamento.status" value="1" type="radio" class="inline-radio">
													<span class="custom-radio"></span>
													<span>pago</span>
												</label>

												<label class="label-radio inline">
													<input name="status" ng-model="pagamento.status" value="0" type="radio" class="inline-radio">
													<span class="custom-radio"></span>
													<span>pendente</span>
												</label>
											</div>
										</div>
								</div>
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
										  <label for="comment">Observação:</label>
										  <textarea class="form-control" rows="3" ng-model="pagamento.obs_pagamento" id="comment"></textarea>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-12">
										<button type="button" class="btn btn-sm btn-default" ng-click="detalhar = !detalhar">
											Detalhar 
											<i ng-if="detalhar==false" class="fa fa-sort-down"></i>
											<i ng-if="detalhar==true" class="fa fa-sort-up"></i>
										</button>
										<br/><br/>
										<table ng-if="detalhar==true" class="table table-bordered table-condensed table-striped table-hover">
											<thead>
												<tr>
													<td colspan="2"><i class="fa fa-list-ol	"></i> Detalhamento</td>
													<td width="60" align="center">
														<button class="btn btn-xs btn-primary" ng-click="showModalDetalhamento()"><i class="fa fa-plus-circle"></i></button>
													</td>
												</tr>
											</thead>
											<tbody>
												<tr ng-show="(pagamento.detalhamento.length == 0) || (pagamento.detalhamento.length == undefined)">
													<td colspan="3" align="center">Nenhum detalhamento lançado</td>
												</tr>
												<tr ng-repeat="item in pagamento.detalhamento">
													<td>{{ item.nome_plano_conta }}</td>
													<td>{{ item.valor | numberFormat:2:',':'.' }}</td>
													<td align="center">
														<button class="btn btn-xs btn-danger" ng-click="delDetalhamento($index)"><i class="fa fa-trash-o"></i></button>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>

					    		<div class="row">
					    			<div class="col-sm-12 text-center">
					    				<label class="control-label">&nbsp;</label>
						    			<div class="form-group ">
						    				<button type="button" class="btn btn-md btn-primary btn-block" ng-click="aplicarRecebimento()">Receber</button>
						    			</div>
						    		</div>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4">
											<div class="controls">
												<label class="control-label">&nbsp;</label>
											</div>
											<div>
												<button class="btn btn-default btn-upload btn-sm">
													<i class="fa fa-pdf-o"></i> Importar Comprovante
													<input type="file" data-model="anx"></input>
												</button>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-12" ng-if="(anexo_comprovante.anx)">
											<p>{{ anexo_comprovante.anx.name }}</p>
										</div>
									</div>
								</div>
								<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(clientes.length != 0)">
										<tr>
											<th colspan="2" class="text-center">Recebidos</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-if="(recebidos.length == 0)">
											<td colspan="2">Não há nenhum pagamento recebido</td>
										</tr>
										<tr ng-repeat="item in recebidos">
											<td ng-if="item.id_forma_pagamento != 6 && item.id_forma_pagamento != 2 ">{{ item.forma_pagamento  }} <strong class="pull-right">R$ {{ item.valor | numberFormat:2:',':'.' }}</strong></td>
											<td ng-if="item.id_forma_pagamento == 6">C/C em {{item.parcelas}}x <strong class="pull-right">R$ {{ item.valor | numberFormat:2:',':'.' }}</strong></td>
											<td ng-if="item.id_forma_pagamento == 2">Cheque em {{ cheques.length }}x <strong class="pull-right">R$ {{ item.valor | numberFormat:2:',':'.' }}</strong></td>
											<td width="50" align="center">
												<button type="button" class="btn btn-xs btn-danger" ng-click="deleteRecebidos($index)">
													<i class="fa fa-times"></i>
												</button>
											</td>
										</tr>
										<tr>
											<td colspan="2" style="background: #A2A2A2;">

											</td>
										</tr>
										<tr>
											<td colspan="2">
												Total Recebido <strong class="pull-right">R$ {{ total_pg | numberFormat:2:',':'.' }}</strong>
											</td>
										</tr>
									</tbody>
								</table>
								<div class="row">
					    			<div class="col-sm-12 text-center">
					    				<label class="control-label">&nbsp</label>
						    			<div class="form-group ">
						    				<button ng-disabled="recebidos.length == 0" type="button" class="btn btn-md btn-success btn-block"   ng-click="salvarPagamento()">Salvar</button>
						    			</div>
						    		</div>
								</div>
							</div>
						</div>
					</div>
					</div>
				</div><!-- /panel -->

				<div class="panel panel-default hidden-print">
					<div class="panel-heading">
						<i class="fa fa-cogs"></i> Filtros 
						<i class="pull-right fa fa-cog fa-lg" style="cursor:pointer" data-toggle="tooltip" data-placement="left" title="Opções de Exibição" ng-click="configTable()"></i>
					</div>

					<div class="panel-body">
						<div class="row">
							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label">Inicial</label>
									<div class="input-group">
										<input readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="dtaInicial" class="datepicker form-control text-center">
										<span class="input-group-addon" id="cld_dtaInicial"><i class="fa fa-calendar"></i></span>
									</div>
								</div>
							</div>

							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label">Final</label>
									<div class="input-group">
										<input readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="dtaFinal" class="datepicker form-control text-center">
										<span class="input-group-addon" id="cld_dtaFinal"><i class="fa fa-calendar"></i></span>
									</div>
								</div>
							</div>

							<div class="col-sm-5">
								<div class="form-group">
									<label class="control-label" style="display: block;"><br></label>
									<button type="button" class="btn btn-sm btn-primary" ng-click="load(0,20)"><i class="fa fa-filter"></i> Filtrar</button>
									<button type="button" class="btn btn-sm btn-default" ng-click="limparBusca()"><i class="fa fa-times-circle"></i> Limpar</button>
									<button type="button" id="invoicePrint" class="btn btn-sm btn-success"><i class="fa fa-print"></i> Imprimir</button>
									<buttom ng-click="buscaAvancada()" class="btn btn-sm btn-primary">
										Pequisa Avançada
										<i ng-show="busca_avancada==false" class="fa fa-sort-down"></i>
										<i ng-show="busca_avancada" class="fa fa-sort-up"></i>
									</buttom>
								</div>
							</div>
						</div>

						<div class="busca_avancada hidden-print" ng-show="busca_avancada">
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
										<label class="control-label">Conta</label>
										<select chosen ng-change="ClearChosenSelect('cod_regime_tributario')"
										    option="contas"
										    ng-model="busca.dsc_conta_bancaria"
										    ng-options="item.id as item.dsc_conta_bancaria for item in contas">
										</select>
									</div>
								</div>

								<div class="col-sm-7">
									<div class="form-group" id="regimeTributario">
										<label class="ccontrol-label">Natureza da Operação</label> 
										<select chosen ng-change="ClearChosenSelect('cod_regime_tributario')"
										    option="plano_contas"
										    ng-model="busca.id_plano_conta"
										    ng-options="plano.id as plano.dsc_completa for plano in plano_contas">
										</select>
									</div>
								</div>
							</div>
							<div class="row" >
								<div class="col-sm-2">
									<label class="control-label">Tipo</label>
									<select class="form-control input-sm" ng-model="busca.flg_tipo_lancamento">
										<option value="" ></option>
										<option value="C">Receita</option>
										<option value="D">Despesa</option>
									</select>
								</div>
								<div class="col-sm-3" ng-if="busca.flg_tipo_lancamento == '' || busca.flg_tipo_lancamento == null ">
									<div class="form-group">
										<label class="control-label">Cliente/Fornecedor</label>
										<input ng-model="busca.nome_clienteORfornecedor" ng-enter="" type="text" class="form-control input-sm ng-pristine ng-valid ng-touched">
									</div>
								</div>
								<div class="col-sm-3" ng-if="busca.flg_tipo_lancamento == 'C'">
									<div class="form-group">
										<label class="control-label">Cliente</label>
										<input ng-model="busca.nome_clienteORfornecedor" ng-enter="" type="text" class="form-control input-sm ng-pristine ng-valid ng-touched">
									</div>
								</div>
								<div class="col-sm-3"  ng-if="busca.flg_tipo_lancamento == 'D'">
									<div class="form-group">
										<label class="control-label">Fornecedor</label>
										<input ng-model="busca.nome_clienteORfornecedor" ng-enter="" type="text" class="form-control input-sm ng-pristine ng-valid ng-touched">
									</div>
								</div>
								
								<div class="col-sm-2">
									<div class="form-group">
										<label class="control-label">Forma de Pagamento</label>
										<!--<select ng-options="item.id as item.nome for item in formas_pagamento"  class="form-control input-sm">
										    
										</select>-->
										<select ng-model="busca.id_forma_pagamento" class="form-control input-sm">
											<option value=""></option>
											<option ng-repeat="item in formas_pagamento"  value="{{ item.id }}">{{ item.nome }}</option>
										</select>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label class="control-label">Status</label>
										<select ng-model="busca.status_pagamento" class="form-control input-sm">
											<option value=""></option>
											<option value="0">Pendente</option>
											<option value="1">Pago</option>
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6" style="margin-bottom:8px;">
									<div class="control-label text-center" style="font-weight: 700;background: #D6D5D5;">Valor</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-2 valor_inicio" ng-show="busca.op_valor == 'between'" >
									<input thousands-formatter ng-model="busca.valor_inicio" type="text" class="form-control input-sm ng-pristine ng-valid ng-touched">
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<select ng-model="busca.op_valor" ng-change="limparErrorValor()" class="form-control input-sm">
											<option value=""></option>
											<option value="between">Entre</option>
											<option value=">">Maior</option>
											<option value=">=">Maior igual</option>
											<option value="<">Menor</option>
											<option value="<=">Menor igual</option>
											<option value="=">Igual</option>
										</select>
									</div>
								</div>

								<div class="col-sm-2 valor_fim">
									<input thousands-formatter ng-model="busca.valor_fim" type="text" class="form-control input-sm ng-pristine ng-valid ng-touched">
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="clearfix">
					<div class="row hidden-print">
						<div class="col-sm-12">
							<div class="alert alert-delete" style="display:none"></div>
						</div>
					</div>
					<span ng-if="(msg_error)" class="alert alert-warning }}">{{ msg_error }}</span>
					<div class="row" ng-show="(pagamentos != null)">
						<div class="col-sm-12">
							<div class="form-group" id="container-tabela" style="overflow: auto">
								<table id="tabela-lancamentos" class="table table-condensed table-bordered table-hover table-sm">
									<thead>
										<tr>
											<th ng-show="!config_table.groupPerDay" rowspan="2">Data</td>
											<th class="text-center" rowspan="2" ng-if="config_table.conta_bancaria">Conta Bancária</th>
											<th class="text-center" rowspan="2">Cliente/Fornecedor</th>
											<th class="text-center" rowspan="2">Natureza da Operação</th>
											<th class="text-center" rowspan="2" ng-if="config_table.forma_pagamento">Forma de Pgto.</th>
											<th class="text-center" rowspan="2" ng-if="config_table.bandeira">Bandeira</th>

											<th class="text-center" rowspan="2" ng-if="config_table.observacao == true">Observação</th>

											<th class="text-center" rowspan="2" ng-if="config_table.cheque">Banco</th>
											<th class="text-center" colspan="3" ng-if="config_table.cheque">Dados Cheque</th>
											<th class="text-center" colspan="2" ng-if="config_table.boleto">Dados Boleto</th>
											<th class="text-center" colspan="3" ng-if="config_table.transferencia">Dados Transferência</th>
											
											<th class="text-center" rowspan="2">Status</th>
											<th class="text-center" rowspan="2" width="90">Crédito</th>
											<th class="text-center" rowspan="2" width="90">Débito</th>
											<th class="text-center" rowspan="2" width="90">Saldo</th>
											<th class="text-center" width="110" rowspan="2">Ações</th>
										</tr>
										<tr>
											<th class="text-center" ng-if="config_table.cheque">C/C</th>
											<th class="text-center" ng-if="config_table.cheque">Nº</th>
											<th class="text-center" ng-if="config_table.cheque">Pre?</th>

											<th class="text-center" ng-if="config_table.boleto">Nº Doc.</th>
											<th class="text-center" ng-if="config_table.boleto">Nº</th>

											<th class="text-center" ng-if="config_table.transferencia">Agência</th>
											<th class="text-center" ng-if="config_table.transferencia">C/C</th>
											<th class="text-center" ng-if="config_table.transferencia">Proprietário</th>

										</tr>
									</thead>
									<tr>
										<td class="text-right text-bold" colspan="{{ (config_table.groupPerDay) ? calculaColspan(3) : calculaColspan(4) }}">Saldo Anterior</td>
										<td class="text-right text-bold text-success">R$ {{ saldo_anterior_receita.vlr_total_despesa | numberFormat : 2 : ',' : '.' }}</td>
										<td class="text-right text-bold text-danger"> R$ {{ saldo_anterior_despesa.vlr_total_despesa | numberFormat : 2 : ',' : '.' }}</td>
										<td class="text-right">
											<span class="text-bold text-danger" ng-if="(saldo_anterior_receita.vlr_total_despesa - saldo_anterior_despesa.vlr_total_despesa ) < 0">
												R$ {{ (saldo_anterior_receita.vlr_total_despesa - saldo_anterior_despesa.vlr_total_despesa ) | numberFormat: '2' : ',' : '.' }}
											</span>

											<span class="text-bold text-primary" ng-if="(saldo_anterior_receita.vlr_total_despesa - saldo_anterior_despesa.vlr_total_despesa ) = 0">
												R$ {{ (saldo_anterior_receita.vlr_total_despesa - saldo_anterior_despesa.vlr_total_despesa ) | numberFormat: '2' : ',' : '.' }}
											</span>

											<span class="text-bold text-success" ng-if="(saldo_anterior_receita.vlr_total_despesa - saldo_anterior_despesa.vlr_total_despesa ) > 0">
												R$ {{ (saldo_anterior_receita.vlr_total_despesa - saldo_anterior_despesa.vlr_total_despesa ) | numberFormat: '2' : ',' : '.' }}
											</span>
										</td>
										<td></td>
									</tr>
									<tr  ng-if="dataGroups.length <= 0 && dataGroups != null">
											<td colspan="{{ (config_table.groupPerDay) ? calculaColspan(7) : calculaColspan(8) }} " style="text-align:center">
												<i class="fa fa-refresh fa-spin"></i> Aguarde, carregando itens...
											</td>
									</tr>
									<tr  ng-if="dataGroups == null">
											<td colspan="{{ (config_table.groupPerDay) ? calculaColspan(7) : calculaColspan(8) }}" style="text-align:center">
												Nenhum lançamento encontrado
											</td>
									</tr>
									<tbody ng-repeat="(key, value) in dataGroups">
										<tr class="info" ng-show="config_table.groupPerDay">
											<td colspan="{{ (config_table.groupPerDay) ? calculaColspan(7) : calculaColspan(8) }}">{{ key | dateFormat: 'date' }} <span class="badge pull-right">{{ value.items.length }}</span></td>
										</tr>
										<tr ng-repeat="item in value.items">
											<td ng-show="!config_table.groupPerDay">{{ key | dateFormat: 'date' }} </td>
											<td class="text-center" ng-if="config_table.conta_bancaria">{{ item.dsc_conta_bancaria }}</td>
											<td class="text-middle" ng-if="!(item.nome == null || item.nome=='')">{{ item.nome | uppercase }}</td>
											<td class="text-middle" ng-if="(item.nome == null || item.nome=='')">{{ item.cpf }}</td>
											<td>{{ item.cod_plano }} - {{ item.dsc_natureza_operacao | uppercase}}</td>
											<td ng-if="config_table.forma_pagamento">{{ item.descricao_forma_pagamento }}</td>
											<td ng-if="config_table.bandeira">{{ item.nme_bandeira }}</td>

											<td ng-if="config_table.observacao == true" control-size-string content="{{ item.obs_pagamento }}" size="16"></td>

											<th class="text-center" ng-if="config_table.cheque">{{ item.nome_banco }}</th>
											<th class="text-center" ng-if="config_table.cheque">{{ item.num_conta_corrente }}</th>
											<th class="text-center" ng-if="config_table.cheque">{{ item.num_cheque }}</th>
											<th class="text-center" ng-if="item.flg_cheque_predatado == 1 && config_table.cheque == true">Sim</th>
											<th class="text-center" ng-if="item.flg_cheque_predatado == 0 && config_table.cheque == true">Não</th>
											<th class="text-center" ng-if="(item.flg_cheque_predatado == null || item.flg_cheque_predatado == '') && config_table.cheque == true "></th>
											<th class="text-center" ng-if="config_table.boleto">{{ item.doc_boleto }}</th>
											<th class="text-center" ng-if="config_table.boleto">{{ item.num_boleto }}</th>
											<th class="text-center" ng-if="config_table.transferencia">{{ item.agencia_transferencia }}</th>
											<th class="text-center" ng-if="config_table.transferencia">{{ item.conta_transferencia }}</th>
											<th class="text-center" ng-if="config_table.transferencia">{{ item.proprietario_conta_transferencia }}</th>


											<td class="text-center">
												<button ng-disabled="(item.id_tipo_conta==5 && configuracao.flg_permitir_alterar_mov_caixa_aberto == 0)" type="button" class="btn btn-xs btn-success"
													ng-if="item.status_pagamento == 1" ng-click="loadVendaByIdLancamento(item)"
													tooltip="Clique para alterar o status do lançamento" data-toggle="tooltip">
													<i class="fa fa-check-circle"></i>
												</button>
												<button ng-disabled="(item.id_tipo_conta==5 && configuracao.flg_permitir_alterar_mov_caixa_aberto == 0)" type="button" class="btn btn-xs btn-warning"
													ng-if="item.status_pagamento == 0" ng-click="modalChangeStatusPagamento(item)"
													tooltip="Clique para alterar o status do lançamento" data-toggle="tooltip">
													<i class="fa fa-warning"></i>
												</button>
											</td>
											<td class="text-right">
												<span class="text-success" ng-if="item.flg_tipo_lancamento == 'C'">
													R$ {{ item.valor_pagamento | numberFormat : 2 : ',' : '.' }}
												</span>
											</td>
											<td class="text-right">
												<span class="text-danger" ng-if="item.flg_tipo_lancamento == 'D'">
													R$ {{ item.valor_pagamento | numberFormat : 2 : ',' : '.' }}
												</span>
											</td>
											<td class="text-right">
												<span class="text-{{ (item.vlr_saldo < 0) ? 'danger' : ((item.vlr_saldo > 0) ? 'success' : 'primary') }}">
													R$ {{ item.vlr_saldo | numberFormat : 2 : ',' : '.' }}
												</span>
											</td>
											<!-- <td class="text-center" width="30">
												<button type="button" tooltip="Editar" data-toggle="tooltip" class="btn btn-xs btn-warning">
													<i class="fa fa-edit"></i>
												</button>
											</td> -->
											<td class="text-left">
												<button ng-disabled="item.id_tipo_conta==5" type="button" ng-click="delete(item,'cliente')" ng-if="item.flg_tipo_lancamento == 'D'" tooltip="Excluir" data-toggle="tooltip" class="btn btn-xs btn-danger">
													<i class="fa fa-trash-o"></i>
												</button>
												<button ng-disabled="item.id_tipo_conta==5" type="button" ng-click="delete(item,'fornecedor')" ng-if="item.flg_tipo_lancamento == 'C'" tooltip="Excluir" data-toggle="tooltip" class="btn btn-xs btn-danger">
													<i class="fa fa-trash-o"></i>
												</button>
												<button type="button" ng-click="printPagamentos(item)" tooltip="Imprimir" data-toggle="tooltip" class="btn btn-xs">
													<i class="fa fa-print"></i>
												</button>
												<button type="button" ng-click="showAnexo(item)" ng-if="!(item.pth_anexo)" tooltip="Ver/Download Anexo" class="btn btn-xs btn-primary" data-toggle="tooltip" disabled>
													<i class="fa fa-paperclip"></i>
												</button>
												<button type="button" ng-click="showAnexo(item)" ng-if="(item.pth_anexo)" tooltip="Ver/Download Anexo" class="btn btn-xs btn-primary" data-toggle="tooltip">
													<i class="fa fa-paperclip"></i>
												</button>

												<!--<button type="button" ng-click="editar(item)" tooltip="Editar" data-toggle="tooltip" class="btn btn-xs btn-warning">
													<i class="fa fa-edit"></i>
												</button>-->
												<button type="button" ng-if="(item.flg_transferencia_financeiro == 1)" disabled="disabled" tooltip="Transferência" class="btn btn-xs btn-info">
													<i class="fa fa-exchange"></i>
												</button>
											</td>
										</tr>
										<tr ng-show="config_table.overviewOfDay">
											<td class="text-center" colspan="{{ (config_table.groupPerDay) ? calculaColspan(7) : calculaColspan(8) }}" style="background-color: #898989;"><strong style="color: #FFF;">Totais - {{ key | dateFormat: 'date' }}</strong></td>
										</tr>
										<tr ng-show="config_table.overviewOfDay">
											<td class="text-right" colspan="{{ (config_table.groupPerDay) ? calculaColspan(5) : calculaColspan(6) }}"><strong>A Receber</strong></td>
											<td class="text-right">
												<span class="label label-success">
													R$ {{ value.a_receber | numberFormat: '2' : ',' : '.' }}
												</span>
											</td>
											<td></td>
										</tr>
										<tr ng-show="config_table.overviewOfDay">
											<td class="text-right" colspan="{{ (config_table.groupPerDay) ? calculaColspan(5) : calculaColspan(6) }}"><strong>A Pagar</strong></td>
											<td class="text-right">
												<span class="label label-danger">
													R$ {{ value.a_pagar | numberFormat: '2' : ',' : '.' }}
												</span>
											</td>
											<td></td>
										</tr>
										<tr class="warning" ng-show="config_table.overviewOfDay">
											<td class="text-right" colspan="{{ (config_table.groupPerDay) ? calculaColspan(5) : calculaColspan(6) }}"><strong>Saldo</strong></td>
											<td class="text-right">
												<span class="label label-danger" ng-if="(value.a_receber - value.a_pagar ) < 0">
													R$ {{ (value.a_receber - value.a_pagar ) | numberFormat: '2' : ',' : '.' }}
												</span>
												<span class="label label-success" ng-if="(value.a_receber - value.a_pagar ) >= 0">
													R$ {{ (value.a_receber - value.a_pagar ) | numberFormat: '2' : ',' : '.' }}
												</span>
											</td>
											<td></td>
										</tr>

										<tr ng-show="config_table.overviewOfDay">
											<td class="text-center" colspan="{{ (config_table.groupPerDay) ? calculaColspan(7) : calculaColspan(8) }}" style="background-color: #898989;padding: 2px;"><strong style="color: #FFF;"></strong></td>
										</tr>

										<tr ng-show="config_table.overviewOfDay">
											<td class="text-right" colspan="{{ (config_table.groupPerDay) ? calculaColspan(5) : calculaColspan(6) }}"><strong>Recebido</strong></td>
											<td class="text-right">
												<span class="label label-success">
													R$ {{ value.recebido | numberFormat: '2' : ',' : '.' }}
												</span>
											</td>
											<td></td>
										</tr>
										<tr ng-show="config_table.overviewOfDay">
											<td class="text-right" colspan="{{ (config_table.groupPerDay) ? calculaColspan(5) : calculaColspan(6) }}"><strong>Pago</strong></td>
											<td class="text-right">
												<span class="label label-danger">
													R$ {{ value.pago | numberFormat: '2' : ',' : '.' }}
												</span>
											</td>
											<td></td>
										</tr>
										<!--<tr class="warning">
											<td class="text-right" colspan="14"><strong>Saldo</strong></td>
											<td class="text-right">
												<span class="label label-danger" ng-if="(value.recedido - value.pago ) < 0">
													R$ {{ (value.recebido - value.pago ) | numberFormat: '2' : ',' : '.' }}
												</span>
												<span class="label label-success" ng-if="(value.recedido - value.pago ) >= 0">
													R$ {{ (value.recebido - value.pago ) | numberFormat: '2' : ',' : '.' }}
												</span>
											</td>
											<td></td>
										</tr>-->
										<tr class="warning" ng-show="config_table.groupPerDay">
											<td class="text-right" colspan="{{ (config_table.groupPerDay) ? calculaColspan(5) : calculaColspan(6) }}"><strong>Saldo</strong></td>
											<td class="text-right">
												<span class="label label-success" ng-if="value.vlr_total_item > 0">
													R$ {{ value.vlr_total_item | numberFormat: '2' : ',' : '.' }}
												</span>
												<span class="label label-danger" ng-if="value.vlr_total_item < 0">
													R$ {{ value.vlr_total_item | numberFormat: '2' : ',' : '.' }}
												</span>
												<span class="label label-money-blue" ng-if="value.vlr_total_item == 0">
													R$ {{ value.vlr_total_item | numberFormat: '2' : ',' : '.' }}
												</span>
											</td>
											<td></td>
										</tr>
									</tbody>
									<tr ng-hide="dataGroups.length <= 0 || dataGroups == null">
										<td colspan="{{ (config_table.groupPerDay) ? calculaColspan(3) : calculaColspan(4) }}" class="text-right">Saldo do Período</td>
										<td class="text-right">
											<span class="text-success">
												R$ {{ vlr_total_credito | numberFormat: '2' : ',' : '.' }}
											</span>
										</td>
										<td class="text-right">
											<span class="text-danger">
												R$ {{ vlr_total_debito | numberFormat: '2' : ',' : '.' }}
											</span>
										</td>
										<td class="text-right">
											<span class="text-{{ (vlr_total_periodo > 0) ? 'success' : ((vlr_total_periodo < 0) ? 'danger' : 'primary') }}">
												R$ {{ vlr_total_periodo | numberFormat: '2' : ',' : '.' }}
											</span>
										</td>
										<td></td>
									</tr>
									<tr ng-hide="dataGroups.length <= 0 || dataGroups == null">
										<td colspan="{{ (config_table.groupPerDay) ? calculaColspan(3) : calculaColspan(4) }}" class="text-right">Saldo Final</td>
										<td class="text-right">
											
										</td>
										<td class="text-right">
											
										</td>
										<td class="text-right">
											<span class="text-{{ (((saldo_anterior_receita.vlr_total_despesa - saldo_anterior_despesa.vlr_total_despesa ) + vlr_total_periodo) > 0) ? 'success' : ((((saldo_anterior_receita.vlr_total_despesa - saldo_anterior_despesa.vlr_total_despesa ) + vlr_total_periodo) < 0) ? 'danger' : 'primary') }}">
												R$ {{ ((saldo_anterior_receita.vlr_total_despesa - saldo_anterior_despesa.vlr_total_despesa ) + vlr_total_periodo) | numberFormat: '2' : ',' : '.' }}
											</span>
										</td>
										<td></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>

				<div class="clearfix">
					<div class="pull-right">
						<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.pagamentos.length > 1">
							<li ng-repeat="item in paginacao.pagamentos" ng-class="{'active': item.current}">
								<a href="" h ng-click="load(item.offset,item.limit)">{{ item.index }}</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div><!-- /main-container -->

		<!-- /Modal fornecedor-->
		<div class="modal fade" id="list_fornecedores" style="display:none">
  			<div class="modal-dialog modal-lg">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Fornecedores</h4>
      				</div>
				    <div class="modal-body">
				    	<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input type="text" class="form-control input-sm" 
						            	ng-enter="loadFornecedor(0,10)" 
						            	ng-model="busca.fornecedores" 
						            	ng-disabled="enableNewFormFornecedor">
						            <div class="input-group-btn">
						            	<button type="button" class="btn btn-sm btn-primary" 
						            		ng-click="loadFornecedor(0,10)" 
						            		ng-disabled="enableNewFormFornecedor">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>

						            	<button type="button" class="btn btn-sm btn-info" ng-click="enableNewFormFornecedor = !enableNewFormFornecedor">
						            		<i class="fa {{ (!enableNewFormFornecedor) ? 'fa-plus-circle' : 'fa-minus-circle' }} "></i> Cadastrar
						            	</button>
						            </div> <!-- /input-group-btn -->
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>
						<br/>
						
						<div class="row" ng-show="enableNewFormFornecedor">
							<div class="col-sm-12">
								<div class="form-group">
									<label for="" class="control-label">Tipo de Cadastro</label>
									<div class="form-group">
										<label class="label-radio inline">
											<input ng-model="new_fornecedor.tipo_cadastro" value="pf" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Pessoa Física</span>
										</label>

										<label class="label-radio inline">
											<input ng-model="new_fornecedor.tipo_cadastro" value="pj" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Pessoa Jurídica</span>
										</label>
									</div>

									<div class="row">
										<div class="col-lg-6" ng-if="new_fornecedor.tipo_cadastro">
											<div id="nome_fornecedor" class="form-group">
												<label class="control-label">{{ (new_fornecedor.tipo_cadastro == 'pj' ? 'Razão Social' : 'Nome') }}</label>
												<input class="form-control" ng-model="new_fornecedor.nome_fornecedor">
											</div>
										</div>

										<div class="col-sm-6" ng-if="new_fornecedor.tipo_cadastro == 'pj'">
											<div id="nme_fantasia" class="form-group">
												<label class="control-label">Nome Fantasia</label>
												<input class="form-control" ng-model="new_fornecedor.nme_fantasia">
											</div>
										</div>

										<div class="col-sm-3" ng-if="new_fornecedor.tipo_cadastro == 'pf'">
											<div id="num_cpf" class="form-group">
												<label class="control-label">CPF</label>
												<input class="form-control" ui-mask="999.999.999-99" ng-model="new_fornecedor.num_cpf"/>
											</div>
										</div>

										<div class="col-sm-3" ng-if="new_fornecedor.tipo_cadastro == 'pf'">
											<div id="celular" class="form-group">
												<label for="" class="control-label">Telefone</label>
												<input type="text" ui-mask="(99) 99999999?9" class="form-control" ng-model="new_fornecedor.telefones[0].num_telefone">
											</div>
										</div>
									</div>

									<div class="row" ng-if="new_fornecedor.tipo_cadastro == 'pj'">
										<div class="col-sm-3">
											<div id="num_cnpj" class="form-group">
												<label class="control-label">CNPJ</label>
												<input class="form-control" ui-mask="99.999.999/9999-99" ng-model="new_fornecedor.num_cnpj">
											</div>
										</div>

										<div class="col-sm-3">
											<div id="num_inscricao_estadual" class="form-group">
												<label class="control-label">I.E. </label>
												<input class="form-control" ng-model="new_fornecedor.num_inscricao_estadual">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="row" ng-show="!enableNewFormFornecedor">
							<div class="col-sm-12">
						   		<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(fornecedores.length != 0)">
										<tr>
											<th>Nome</th>
											<th>Nome Fant.</th>
											<th>CNPJ</th>
											<th>CPF</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(fornecedores.length == 0)">
											<td colspan="5">Nenhum fornecedor encontrado</td>
										</tr>
										<tr ng-repeat="item in fornecedores">
											<td>{{ item.nome_fornecedor }}</td>
											<td>{{ item.nme_fantasia }}</td>
											<td>{{ item.num_cnpj | cnpjFormat }}</td>
											<td>{{ item.num_cpf | cpfFormat }}</td>
											<td width="80">
												<button ng-click="addFornecedor(item)" class="btn btn-success btn-xs" type="button">
														<i class="fa fa-check-square-o"></i> Selecionar
												</button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>

						<div class="row" ng-show="!enableNewFormFornecedor">
				    		<div class="col-sm-12">
				    			<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao_fornecedores.length > 1">
									<li ng-repeat="item in paginacao_fornecedores" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadFornecedor(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
				    		</div>
				    	</div>
				    </div>
				    <div class="modal-footer clearfix" ng-show="enableNewFormFornecedor && new_fornecedor.tipo_cadastro">
				    	<button type="button" id="btn-salvar-fornecedor" class="btn btn-primary btn-sm"
				    		ng-click="salvarFornecedor()">
				    		<i class="fa fa-save"></i> Salvar e selecionar
			    		</button>
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
						            <input type="text" class="form-control input-sm"
						            	ng-enter="loadCliente(0,10)" 
						            	ng-model="busca.clientes" 
						            	ng-keyup="loadCliente(0,10)"
						            	ng-disabled="enableNewFormCliente">
						            	
						            <div class="input-group-btn">
						            	<button type="button" class="btn btn-sm btn-primary" 
						            		ng-click="loadCliente(0,10)" 
						            		ng-disabled="enableNewFormCliente">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            	<button type="button" class="btn btn-sm btn-info" ng-click="enableNewFormCliente = !enableNewFormCliente">
						            		<i class="fa {{ (!enableNewFormCliente) ? 'fa-plus-circle' : 'fa-minus-circle' }} "></i> Cadastrar
						            	</button>
						            </div> <!-- /input-group-btn -->
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>
						<br />

						<div class="row" ng-show="enableNewFormCliente">
							<div class="col-sm-12">
								<div class="form-group">
									<label for="" class="control-label">Tipo de Cadastro</label>
									<div class="form-group">
										<label class="label-radio inline">
											<input ng-model="new_cliente.tipo_cadastro" value="pf" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Pessoa Física</span>
										</label>

										<label class="label-radio inline">
											<input ng-model="new_cliente.tipo_cadastro" value="pj" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Pessoa Jurídica</span>
										</label>
									</div>

									<div class="row">
										<div class="col-lg-6" ng-if="new_cliente.tipo_cadastro == 'pj'">
											<div id="razao_social" class="form-group">
												<label class="control-label">Razão Social</label>
												<input class="form-control" ng-model="new_cliente.razao_social">
											</div>
										</div>

										<div class="col-sm-6" ng-if="new_cliente.tipo_cadastro == 'pj'">
											<div id="nome_fantasia" class="form-group">
												<label class="control-label">Nome Fantasia</label>
												<input class="form-control" ng-model="new_cliente.nome_fantasia">
											</div>
										</div>

										<div class="col-sm-9" ng-if="new_cliente.tipo_cadastro == 'pf'">
											<div id="nome" class="form-group">
												<label for="nome" class="control-label">Nome</label>
												<input type="text" class="form-control" id="nome" ng-model="new_cliente.nome">
											</div>
										</div>

										<div class="col-sm-3" ng-if="new_cliente.tipo_cadastro == 'pf'">
											<div id="dta_nacimento" class="form-group">
												<label class="control-label">Data de Nacimento</label>
												<input class="form-control" ui-mask="99/99/9999" id="dta_nacimento" ng-model="new_cliente.dta_nacimento">
											</div>
										</div>
									</div>

									<div class="row" ng-if="new_cliente.tipo_cadastro == 'pj'">
										<div class="col-sm-4">
											<div id="cnpj" class="form-group">
												<label class="control-label">CNPJ</label>
												<input class="form-control" ui-mask="99.999.999/9999-99" ng-model="new_cliente.cnpj">
											</div>
										</div>

										<div class="col-sm-4">
											<div id="inscricao_estadual" class="form-group">
												<label class="control-label">I.E. </label>
												<input class="form-control" ng-model="new_cliente.inscricao_estadual">
											</div>
										</div>
									</div>

								    <div class="row" ng-if="new_cliente.tipo_cadastro == 'pf'">
										<div class="col-sm-3">
											<div id="cpf" class="form-group">
												<label class="control-label">CPF</label>
												<input class="form-control" ui-mask="999.999.999-99" ng-model="new_cliente.cpf"/>
											</div>
										</div>

										<div class="col-sm-3">
											<div id="rg" class="form-group">
												<label class="control-label">RG</label>
												<input class="form-control" ng-model="new_cliente.rg"/>
											</div>
										</div>

										<div class="col-sm-3">
											<div id="celular" class="form-group">
												<label for="" class="control-label">Telefone</label>
												<input type="text" ui-mask="(99) 99999999?9" class="form-control" ng-model="new_cliente.celular">
											</div>
										</div>
									</div>

									<div class="row" ng-if="new_cliente.tipo_cadastro">
										<div class="col-sm-8">
											<div id="email" class="form-group">
												<label for="email" class="control-label">E-mail</label>
												<input type="text" class="form-control" id="email" ng-model="new_cliente.email">
											</div>
										</div>

										<div class="col-md-4">
											<div id="id_perfil" class="form-group" id="regimeTributario">
												<label for="" class="control-label">Perfil</label>
												<select chosen option="perfisNewCliente"
											    	ng-model="new_cliente.id_perfil"
											    	ng-options="perfil.id as perfil.nome for perfil in perfisNewCliente">
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="row" ng-show="!enableNewFormCliente">
							<div class="col-sm-12">
								<table class="table table-bordered table-condensed table-striped table-hover">
									<tr ng-if="clientes.length <= 0 || clientes == null">
										<th class="text-center" colspan="9" style="text-align:center"><strong>Carregando</strong><img src="assets/imagens/progresso_venda.gif"></th>
									</tr>
									<thead ng-show="(clientes.length != 0)">
										<tr>
											<th >Nome</th>
											<th >perfil</th>
											<th colspan="2">selecionar</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in clientes">
											<td class="text-middle" ng-if="!(item.nome == null || item.nome=='')">{{ item.nome | uppercase }}</td>
											<td class="text-middle" ng-if="(item.nome == null || item.nome=='')" ng-bind-html="item.cpf | cpfFormat:'<b>CPF:</b> '"></td>
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

						<div class="row" ng-show="!enableNewFormCliente">
				    		<div class="col-sm-12">
				    			<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao_clientes.length > 1">
									<li ng-repeat="item in paginacao_clientes" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadCliente(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
				    		</div>
				    	</div>
				    </div>
				    <div class="modal-footer clearfix" ng-show="enableNewFormCliente && new_cliente.tipo_cadastro">
				    	<button type="button" id="btn-salvar-cliente" class="btn btn-primary btn-sm"
				    		ng-click="salvarCliente()">
				    		<i class="fa fa-save"></i> Salvar e selecionar
			    		</button>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal Processando Pagamento-->
		<div class="modal fade" id="modal_progresso_pagamento" style="display:none">
  			<div class="modal-dialog error modal-md">
    			<div class="modal-content">
      				<div class="modal-header">
						<h4>Processando Pagamento</h4>
      				</div>

				    <div class="modal-body">
				    	<div class="alert alert-reforco" style="display:none"></div>

				    	<div class="row">
				    		<div class="col-sm-6" id="valor_pagamento">
				    		<p>
				    			<strong id="text_status_venda">Salvando pagamento</strong><img src="assets/imagens/progresso_venda.gif">
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

		<div class="modal fade" id="modal-print" style="display:none"  data-keyboard="false">
  			<div class="modal-dialog error modal-lg">
    			<div class="modal-content">
      				<div class="modal-header" id="topo_print">
						<div class="clearfix">
							<div class="pull-left">
								<span class="img-demo">
									<img src="assets/imagens/logos/{{ userLogged.nme_logo }}" height="40" width="40">
								</span>

								<div class="pull-left m-left-sm">
									<h3 class="m-bottom-xs m-top-xs" >Comprovante de Pagamento</h3>
									<span class="text-muted">{{ userLogged.nome_empreendimento }}</span>
								</div>
							</div>

							<div class="pull-right text-right">
								<strong><?php echo date("d/m/Y H:i:s"); ?></strong>
							</div>
						</div>
      				</div>

				    <div class="modal-body">
				    	<div class="row">
				    		<div class="col-sm-12">

				    		</div>
				    	</div>
				    	<div class="row" id="tbl_print" ng-if="itensPrint.length > 0">
				    		<div class="col-sm-12" id="valor_pagamento">
				    			<strong style="font-size:14px;margin-bottom:5px">Cliente    : {{ vendaPrint.nome_cliente }}</strong>
				    			<br>
				    			<strong style="font-size:14px;margin-bottom:5px">ID         : {{ vendaPrint.id_cliente  }}</strong>
				    			<br>
				    			<strong style="font-size:14px;margin-bottom:5px;color:#2C800C" ng-if="vendaPrint.vlr_saldo_devedor >= 0 && ( vendaPrint.id_cliente != configuracao.id_cliente_movimentacao_caixa) ">Saldo : R${{ vendaPrint.vlr_saldo_devedor | numberFormat : 2 : ',' : '.' }} </strong>
				    			<strong style="font-size:14px;margin-bottom:5px;color:#D82121" ng-if="vendaPrint.vlr_saldo_devedor < 0  && (vendaPrint.id_cliente != configuracao.id_cliente_movimentacao_caixa)">Saldo : R$ {{ vendaPrint.vlr_saldo_devedor | numberFormat : 2 : ',' : '.' }} </strong>
				    			<br>
				    			<br>
				    		</div>
				    	</div>

				    		<div class="row" id="tbl_print_pg">
					    		<div class="col-sm-12" id="valor_pagamento">
					    			<table class="table table-bordered table-condensed table-striped table-hover">
					    				<thead ng-show="itensPrint.length  > 0">
											<tr>
												<th>Forma de pagamento</th>
												<th>Status</th>
												<th ng-if="itensPrint[0].id_forma_pagamento == 4">Doc. Boleto</th>
												<th ng-if="itensPrint[0].id_forma_pagamento == 4">Num. Boleto</th>
												<th class="hidden-print">Conta bancaria</th>
												<th ng-if="itensPrint[0].id_forma_pagamento == 2">N° Conta Corrente</th>
												<th ng-if="itensPrint[0].id_forma_pagamento == 2">N° do cheque</th>
												<th ng-if="itensPrint[0].id_forma_pagamento == 2">Predatado</th>
												<th>Data do pagamento</th>
												<th>Valor</th>
											<tr>
										</thead>
										<tbody>
											<tr  ng-if="itensPrint.length <= 0">
												<td colspan="4" style="text-align:center">
													<i class="fa fa-refresh fa-spin"></i> Aguarde, carregando itens...
												</td>
											</tr>
											<tr ng-repeat="item in itensPrint">
												<td ng-if="item.id_forma_pagamento != 6">{{ item.descricao_forma_pagamento  }} </td>
												<td ng-if="item.id_forma_pagamento == 6">{{ item.descricao_forma_pagamento  }}  {{item.current_parcela}}/{{item.total_parcelas}} </td>
												<td ng-if="item.status_pagamento == 0">Pendente</td>
												<td ng-if="item.status_pagamento == 1">Pago</td>
												<td ng-if="item.id_forma_pagamento == 4">{{ item.doc_boleto }}</td>
												<td ng-if="item.id_forma_pagamento == 4">{{ item.num_boleto }}</td>
												<td class="hidden-print"> {{ item.dsc_conta_bancaria }}</td>
												<td ng-if="item.id_forma_pagamento == 2">{{ item.num_conta_corrente  }} </td>
												<td ng-if="item.id_forma_pagamento == 2">{{ item.num_cheque }} </td>
												<td ng-if="item.id_forma_pagamento == 2 && item.flg_cheque_predatado == 0"> Não </td>
												<td ng-if="item.id_forma_pagamento == 2 && item.flg_cheque_predatado == 1"> Sim </td>
												<td >{{ item.data_pagamento | dateFormat:'date' }}</td>
												<td ng-if="item.id_forma_pagamento != 6">R$ {{ item.valor_pagamento | numberFormat:2:',':'.' }}</td>
												<td ng-if="item.id_forma_pagamento == 6">R$ {{ item.valor_pagamento | numberFormat:2:',':'.' }}</td>
											</tr>
										</tbody>
									</table>
					    		</div>
				    		</div>

				    </div>

				    <div class="modal-footer" ng-show="itensPrint.length  > 0">
				    	<button type="button" data-loading-text=" Aguarde..." id="btn-imprimir"
				    		class="btn btn-md btn-block btn-success" ng-click="printDiv('modal-print')">
				    		<i class="fa fa-print"></i> Imprimir
				    	</button>
				    	<a ng-click="cancelar()" class="btn btn-md btn-block btn-default">
				    		<i class="fa fa-reply"></i> Cancelar
				    	</a>
				    </div>
			  	</div>
			  	<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		
		<!-- /Modal Print-->
		<div class="modal fade" id="modal-print-lancamento" style="display:none"  data-keyboard="false">
  			<div class="modal-dialog error modal-lg">
    			<div class="modal-content">
      				<div class="modal-header" id="topo_print">
						<div class="clearfix">
							<div class="pull-left">
								<span class="img-demo">
									<img src="assets/imagens/logos/{{ userLogged.nme_logo }}" height="40" width="40">
								</span>

								<div class="pull-left m-left-sm">
									<h3 class="m-bottom-xs m-top-xs">Comprovante de Pagamento</h3>
									<span class="text-muted">{{ userLogged.nome_empreendimento }}</span>
								</div>
							</div>

							<div class="pull-right text-right">
								<h5><strong>#{{ id_controle_pagamento }}</strong></h5>
								<strong><?php echo date("d/m/Y H:i:s"); ?></strong>
							</div>
						</div>
      				</div>

				    <div class="modal-body">
				    	<div class="row">
				    		<div class="col-sm-12">

				    		</div>
				    	</div>
				    	<div class="row" id="tbl_print">
				    		<div class="col-sm-12" id="valor_pagamento">
				    			<strong style="font-size:14px;margin-bottom:5px">Atendente : {{ userLogged.nme_usuario }}</strong>
				    			<br>
				    			<strong style="font-size:14px" ng-if="(cliente.id    != undefined && cliente.id != '') && (flgTipoLancamento == 0)">Cliente : {{ cliente.nome }}</strong>
				    			<strong style="font-size:14px" ng-if="(fornecedor.id != undefined && fornecedor.id != '') && (flgTipoLancamento == 1)">Fornecedor : {{ fornecedor.nome_fornecedor }}</strong>
				    			<br>
				    			<strong style="font-size:14px;margin-bottom:5px;color:#2C800C" ng-if="vlr_saldo_devedor >= 0 && (cliente.id != undefined && cliente.id != '')">Saldo : R${{ vlr_saldo_devedor | numberFormat : 2 : ',' : '.' }} </strong>
				    			<strong style="font-size:14px;margin-bottom:5px;color:#D82121" ng-if="vlr_saldo_devedor < 0 && (cliente.id != undefined && cliente.id != '')">Saldo : R$ {{ vlr_saldo_devedor | numberFormat : 2 : ',' : '.' }} </strong>
				    			<br>
				    			<br>
				    		</div>
				    	</div>

				    		<div class="row" id="tbl_print_pg">
					    		<div class="col-sm-12" id="valor_pagamento">
					    			<table class="table table-bordered table-condensed table-striped table-hover">
										<tbody>
											<tr ng-if="(recebidos.length == 0)">
												<td colspan="1">Não foi recebido nenhum pagamento</td>
											</tr>
											<tr ng-repeat="item in recebidos">
												<td ng-if="item.id_forma_pagamento != 6 &&  item.id_forma_pagamento != 2">{{ item.forma_pagamento  }} <strong class="pull-right">R$ {{ item.valor | numberFormat:2:',':'.' }}</strong></td>
												<td ng-if="item.id_forma_pagamento == 6">{{ item.forma_pagamento  }} em {{item.parcelas}}x <strong class="pull-right">R$ {{ item.valor | numberFormat:2:',':'.' }}</strong></td>
												<td ng-if="item.id_forma_pagamento == 2">{{ item.forma_pagamento  }} em {{ cheques.length }}x<strong class="pull-right">R$ {{ item.valor | numberFormat:2:',':'.' }}</strong></td>
											</tr>
										</tbody>
									</table>
					    		</div>
				    		</div>

				    </div>

				    <div class="modal-footer">
				    	<button type="button" data-loading-text=" Aguarde..." id="btn-imprimir"
				    		class="btn btn-md btn-block btn-success" ng-click="printDiv('modal-print')">
				    		<i class="fa fa-plus-circle"></i> Imprimir
				    	</button>
				    	<a ng-click="refresh()" class="btn btn-md btn-block btn-default">
				    		<i class="fa fa-times-circle"></i> Voltar
				    	</a>
				    </div>
			  	</div>
			  	<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal Configutração Tabela-->
		<div class="modal fade" id="modal_config_table" style="display:none">
  			<div class="modal-dialog error modal-md">
    			<div class="modal-content">
      				<div class="modal-header">
						<h4>Opções de Exibição</h4>
      				</div>

				    <div class="modal-body">
				    	<div class="row">
				    		<div class="col-sm-6">
				    			<b>Colunas extras:</b>
							</div>
							<div class="col-sm-6">
				    			<b>Linhas extras:</b>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-6">
								<label class="label-checkbox">
									<input type="checkbox" ng-model="config_table.conta_bancaria">
									<span class="custom-checkbox"></span>
									Conta Bancária
								</label>
								<label class="label-checkbox">
									<input type="checkbox" ng-model="config_table.forma_pagamento">
									<span class="custom-checkbox"></span>
									Forma de Pagamento
								</label>
								<label class="label-checkbox">
									<input type="checkbox" ng-model="config_table.bandeira">
									<span class="custom-checkbox"></span>
									Bandeira
								</label>
								<label class="label-checkbox">
									<input type="checkbox" ng-model="config_table.cheque">
									<span class="custom-checkbox"></span>
									Cheque
								</label>
								<label class="label-checkbox">
									<input type="checkbox" ng-model="config_table.boleto">
									<span class="custom-checkbox"></span>
									Boleto	
								</label>
								<label class="label-checkbox"> 
									<input type="checkbox" ng-model="config_table.transferencia">
									<span class="custom-checkbox"></span>
									Tranferência 	
								</label>
								<label class="label-checkbox"> 
									<input type="checkbox" ng-model="config_table.observacao">
									<span class="custom-checkbox"></span>
									Observação 	
								</label>
							</div>

							<div class="col-lg-6">
								<label class="label-checkbox">
									<input type="checkbox" ng-model="config_table.groupPerDay">
									<span class="custom-checkbox"></span>
									Agrupamento por dia
								</label>
								<label class="label-checkbox">
									<input type="checkbox" ng-model="config_table.overviewOfDay">
									<span class="custom-checkbox"></span>
									Totais por dia
								</label>
							</div>
				    	</div>
				    	<div class="row">
				    		<div class="col-sm-6">
				    			<b>Configurações Gerais:</b>
							</div>
				    	</div>
				    	<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<label class="label-checkbox">
										<input type="checkbox" ng-model="config_table.flg_considerar_pendente_saldo">
										<span class="custom-checkbox"></span>
										Considerar lançamento pendente na coluna de saldo
									</label>
								</div>
						    </div>
					    </div>
					</div>
					<div class="modal-footer">
				    	<button type="button" data-loading-text=" Aguarde..." ng-click="cancelarModal('modal_config_table')" id="btn-aplicar-reforco"
				    		class="btn btn-md btn-block btn-success fechar-modal">
				    		<i class="fa fa-reply fa-lg"></i> Voltar
				    	</button>
				  	</div>
				  	<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
		</div>
		<!-- /.modal -->
		
		<!-- /Modal modal_change_date_pagamento-->
		<div class="modal fade" id="modal_change_date_pagamento" style="display:none">
  			<div class="modal-dialog error modal-lg">
    			<div class="modal-content">
    				<div class="modal-header">
    					<h4>Edição Lançamento</h4>
    				</div>
				    <div class="modal-body">
				    	<div class="alert alert-edit" style="display:none"></div>
				    	<div class="row">
							<div class="col-sm-3" ng-if="flg_valid_venda == 0">
								<div class="form-group">
									<label class="control-label">Tipo de Lançamento</label>
									<div class="clearfix"></div>
									<label class="label-radio inline" >
										<input name="flg_tipo_lancamento" ng-model="pagamento_edit.flg_tipo_lancamento" ng-click="changeIdLancamento('D')" value="D" type="radio" class="inline-radio">
										<span class="custom-radio"></span>
										<span class="text-danger">Despesa</span>
									</label>
									
									<label class="label-radio inline">
										<input name="flg_tipo_lancamento" ng-model="pagamento_edit.flg_tipo_lancamento" ng-click="changeIdLancamento('C')" value="C" type="radio" class="inline-radio">
										<span class="custom-radio"></span>
										<span class="text-success">Receita</span>
									</label>
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label">Pago?</label><br/>
									<label class="label-radio inline">
										<input name="status" ng-model="pagamento_edit.status_pagamento" value="1" type="radio" class="inline-radio">
										<span class="custom-radio"></span>
										<span>Sim</span>
									</label>

									<label class="label-radio inline">
										<input name="status" ng-model="pagamento_edit.status_pagamento" value="0" type="radio" class="inline-radio">
										<span class="custom-radio"></span>
										<span>Não</span>
									</label>
								</div>
							</div>
							<div class="pull-right col-sm-6">
								<div class="form-group" id="plano_contas">
									<label class="control-label">Plano de conta </label> 
									<select chosen
										option="plano_contas"
										ng-model="pagamento_edit.id_plano_conta"
										ng-options="plano.id as plano.dsc_completa for plano in plano_contas">
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group" 
									ng-show="pagamento_edit.flg_tipo_lancamento == 'D'"
									np-autocomplete="npAutocompleteFornecedorOptions" 
									ng-model="pagamento_edit.id_clienteORfornecedor">
									<label class="control-label" style="color: #777 !important;">Fornecedor</label>
									<input class="form-control" id="txtNomeFornecedor" type="text" style="border-color: #ccc !important; box-shadow: none !important;"/>
								</div>
								<div class="form-group" 
									ng-show="pagamento_edit.flg_tipo_lancamento == 'C'"
									np-autocomplete="npAutocompleteClienteOptions" 
									ng-model="pagamento_edit.id_clienteORfornecedor">
									<label class="control-label" style="color: #777 !important;">Cliente</label>
									<input class="form-control" id="txtNomeCliente" type="text" style="border-color: #ccc !important; box-shadow: none !important;"/>
								</div>
							</div>
						
					    	<div class="col-sm-3">
								<div class="form-group" id="id_conta_bancaria">
									<label class="control-label">Conta Bancária</label>
									<select class="form-control" ng-model="pagamento_edit.id_conta_bancaria"
									    option="plano_contas"
									    ng-options="conta.id as conta.dsc_conta_bancaria for conta in contas">
									</select>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group" id="pagamento_forma_pagamento">
				    				<label class="control-label">Forma de Pagamento</label>
									<select class="form-control" ng-model="pagamento_edit.id_forma_pagamento" ng-click="clearFieldsByPaymentMethod(pagamento_edit.id_forma_pagamento)">
										<option ng-if="pagamento.id_forma_pagamento != null" value=""></option>
										<option ng-repeat="item in formas_pagamento"  value="{{ item.id }}">{{ item.nome }}</option>
									</select>
								</div>
					    	</div>
					    </div>

						<div class="row">
							<div class="col-lg-3">
								<div class="form-group">
									<label class="control-label">Data Vencimento</label>
									<div class="input-group">
										<input readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="dta_vencimento" class="datepicker form-control text-center">
										<span class="input-group-addon" id="cld_dtaInicial"><i class="fa fa-calendar"></i></span>
									</div>
								</div>	
							</div>
							<div class="col-lg-3">
								<div class="form-group">
									<label class="control-label">Data Competência</label>
									<div class="input-group">
										<input readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="dta_competencia" class="datepicker form-control text-center">
										<span class="input-group-addon" id="cld_dtaInicial"><i class="fa fa-calendar"></i></span>
									</div>
								</div>	
							</div>
							<div class="col-lg-3">
								<div class="form-group">
									<label class="control-label">Data Pagamento</label>
									<div class="input-group">
										<input readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="dta_change_pagamento" class="datepicker form-control text-center">
										<span class="input-group-addon" id="cld_dtaInicial"><i class="fa fa-calendar"></i></span>
									</div>
								</div>	
							</div>
					    </div>
					    
						<div class="row">
					    	<div class="col-sm-2">
					    		<div class="form-group">
					    			<label class="control-label">Valor</label>
					    			<input class="form-control text-right" type="text" name="" ng-model="pagamento_edit.valor_pagamento" thousands-formatter></input>
					    		</div>
					    	</div>
					    	<div class="col-sm-2">
					    		<div class="form-group">
					    			<label class="control-label">Juros</label>
					    			<input class="form-control text-right" type="text" name="" ng-model="pagamento_edit.vlr_juros" thousands-formatter></input>
					    		</div>
					    	</div>
					    	<div class="col-sm-2">
					    		<div class="form-group">
					    			<label class="control-label">Multa</label>
					    			<input class="form-control text-right" type="text" name="" ng-model="pagamento_edit.vlr_multa" thousands-formatter></input>
					    		</div>
					    	</div>
					    	<div class="col-sm-2">
					    		<div class="form-group">
					    			<label class="control-label">Final</label>
					    			<input class="form-control text-right" type="text"
					    				value="R$ {{ (pagamento_edit.valor_pagamento + pagamento_edit.vlr_juros + pagamento_edit.vlr_multa) | numberFormat : 2 : ',' : '.' }}"
					    				disabled="disabled"></input>
					    		</div>
					    	</div>
					    </div>
					    <div class="alert error-cheque" style="display:none"></div>
						<div class="row" ng-show="pagamento_edit.id_forma_pagamento == 2" ng-repeat="item in cheques">
							<div class="col-sm-4">
								<div class="form-group cheque_banco" >
									<label class="control-label">Banco</label>
									<select chosen
									    option="bancos"
									    ng-model="pagamento_edit.id_banco"
									    ng-options="banco.id as banco.nome for banco in bancos">
									</select>
								</div>
							</div>

							<div class="col-sm-2">
								<div class="form-group cheque_cc">
									<label class="control-label">Núm. C/C</label>
									<input ng-model="pagamento_edit.num_conta_corrente" type="text" class="form-control">
								</div>
							</div>

							<div class="col-sm-2">
								<div class="form-group cheque_num">
									<label class="control-label">Núm. Cheque</label>
									<input ng-model="pagamento_edit.num_cheque" type="text" class="form-control">
								</div>
							</div>
						</div>
						<div class="row" ng-show="pagamento_edit.id_forma_pagamento == 4" ng-repeat="item in boletos">
							<div class="col-sm-4">
								<div class="form-group boleto_banco" >
									<label class="control-label">Banco</label>
									<select chosen
										    option="bancos"
										    ng-model="pagamento_edit.id_banco"
										    ng-options="banco.id as banco.nome for banco in bancos">
									</select>
								</div>
							</div>

							<div class="col-sm-2">
								<div class="form-group boleto_doc">
									<label class="control-label">Doc. Boleto</label>
									<input ng-model="pagamento_edit.doc_boleto" type="text" class="form-control">
								</div>
							</div>

							<div class="col-sm-2">
								<div class="form-group boleto_num">
									<label class="control-label">Núm. Boleto</label>
									<input ng-model="pagamento_edit.num_boleto" type="text" class="form-control">
								</div>
							</div>
						</div>
						<div class="row" id="pagamento_maquineta" ng-if="(pagamento_edit.id_forma_pagamento == 5 || pagamento_edit.id_forma_pagamento == 6) && (flgTipoLancamento == 0) && pagamento_edit.flg_tipo_lancamento == 'C'">
							<div class="col-sm-3">
				    			<label class="control-label">Maquineta</label>
								<select ng-model="pagamento_edit.id_maquineta" ng-change="selIdMaquineta()" class="form-control">
									<option ng-repeat="item in maquinetas" value="{{ item.id_maquineta }}">#{{ item.id_maquineta }} - {{ item.dsc_conta_bancaria }}</option>
								</select>
							</div>
							<div class="col-sm-6" id="bandeiras">
				    			<label class="control-label">Bandeira</label>
								<select ng-model="pagamento_edit.id_bandeira" class="form-control">
									<option ng-repeat="item in bandeiras" value="{{ item.id_bandeira }}">{{ item.nome }}</option>
								</select>
							</div>
						</div>
						<div class="row" ng-if="pagamento_edit.id_forma_pagamento == 8">
							<div class="col-sm-4" id="pagamento_id_banco" >
								<div class="form-group" >
									<label class="control-label">Banco</label>
									<select chosen
									    option="bancos"
									    ng-model="pagamento_edit.id_banco"
									    ng-options="banco.id as banco.nome for banco in bancos">
									</select>
								</div>
							</div>

							<div class="col-sm-4" id="pagamento_agencia_transferencia">
				    			<label class="control-label">Agência</label>
				    			<div class="form-group ">
				    					<input ng-model="pagamento_edit.agencia_transferencia"  type="text" class="form-control" />
				    			</div>
				    		</div>

				    		<div class="col-sm-4" id="pagamento_conta_transferencia" >
				    			<label class="control-label">Conta</label>
				    			<div class="form-group ">
				    					<input ng-model="pagamento_edit.conta_transferencia"  type="text" class="form-control" />
				    			</div>
				    		</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<label class="control-label">Observação</label>
									<textarea class="form-control" rows="5" ng-model="pagamento_edit.obs_pagamento"></textarea>
								</div>
							</div>
						</div>
		    		</div>
					<div class="modal-footer" style="   margin-top: 0px;">
				    	<button type="button" 
				    			data-loading-text=" Aguarde..." 
				    			ng-click="cancelarModal('modal_change_date_pagamento')" 
				    			id="btn-aplicar-reforco"
				    		class="btn btn-md  btn-danger fechar-modal">
				    		</i> Cancelar
				    	</button>
				    	<button type="button" 
			    			data-loading-text=" Aguarde..." 
			    			ng-click="updateStatusLancamento(pagamento_edit)" 
			    			id="btn-aplicar-reforco"
			    			class="btn btn-md btn-success fechar-modal">
				    		</i> Salvar
				    	</button>
				    </div>
			  	</div>
			  	<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->


















		<!-- /Modal Processando Pagamento-->
		<div class="modal fade" id="modal_add_detalhamento" style="display:none">
  			<div class="modal-dialog error modal-md">
    			<div class="modal-content">
      				<div class="modal-header">
      					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Detalhar Valor - {{ pagamento.valor | numberFormat:2:',':'.' }} </h4>
      				</div>

				    <div class="modal-body">
				    	<div class="alert alert-detalhamento alert-warning"  ng-show="mensagem_detalhamento != ''" ng-bind-html="mensagem_detalhamento">
		
				    	</div>
				    	<div class="row">
				    		<div class="col-sm-8">
									<div class="form-group">
										<label class="ccontrol-label">Plano de conta </label> 
										<select chosen ng-change="ClearChosenSelect('cod_regime_tributario')"
										option="plano_contas"
										ng-model="novo_detalhamento.id_plano_conta"
										ng-options="plano.id as plano.dsc_completa for plano in plano_contas">
									</select>
								</div>
							</div>

							<div class="col-sm-3">
								<label class="control-label">Valor</label>
								<div class="form-group ">
									<input ng-model="novo_detalhamento.valor" thousands-formatter type="text" class="form-control text-right" />
								</div>
							</div>
				    	</div>
				    </div>
				    <div class="modal-footer">
		    	<button type="button" id="btn-salvar-detalhe-pagamento" class="btn btn-primary btn-sm"
		    		ng-click="addDetalhePagamento(true)">
		    		<i class="fa fa-save"></i> Salvar e ficar
	    		</button>
	    		<button type="button" id="btn-salvar-detalhe-pagamento" class="btn btn-primary btn-sm"
		    		ng-click="addDetalhePagamento()">
		    		<i class="fa fa-save"></i> Salvar
	    		</button>
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

	<!-- Bower Components -->	
	<script src="bower_components/noty/lib/noty.min.js" type="text/javascript"></script>
    <script src="bower_components/mojs/build/mo.min.js" type="text/javascript"></script>

	<!-- Extras -->
	<script src="js/extras.js"></script>

	<!-- Mascaras para o formulario de produtos -->
	<script src="js/scripts/mascaras.js"></script>

	<!-- UnderscoreJS -->
	<script type="text/javascript" src="bower_components/underscore/underscore.js"></script>

	<!-- fixedHeadTable -->
	<script type="text/javascript" src="js/fixedHeadTable/fixedHeadTable.js"></script>

	<script src='js/agenda/lib/moment.min.js'></script>
	<script src='js/jquery.noty.packaged.js'></script>


	<!-- AngularJS -->
	<script type="text/javascript" src="bower_components/angular/angular.js"></script>
	<script type="text/javascript" src="bower_components/angular-ui-utils/mask.min.js"></script>
	<script type="text/javascript" src="bower_components/np-autocomplete/src/np-autocomplete.js"></script>
    <script src="js/angular-sanitize.min.js"></script>
    <script src="js/ui-bootstrap-tpls-0.6.0.js" type="text/javascript"></script>
    <script src="js/dialogs.v2.min.js" type="text/javascript"></script>
    <script src="js/auto-complete/ng-sanitize.js"></script>
    <script src="js/angular-chosen.js"></script>
    <script type="text/javascript">
    	var addParamModule = ['angular.chosen', 'ng-pros.directive.autocomplete'] ;
    </script>
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/lancamentos-controller.js?<?php echo filemtime('js/angular-controller/lancamentos-controller.js')?>"></script>
	<script src="js/angular-strap.min.js"></script>
	<script src="js/angular-strap.tpl.min.js"></script>
	<script type="text/javascript"></script>

	<script type="text/javascript">
		$(document).ready(function() {
			//$("#tabela-lancamentos").fixedHeadTable(45,"<div class='form-group' id='container-topo-tabela' style='overflow: auto;width: 1044px'></div>",["#container-tabela","#container-topo-tabela"]);
			$("#cld_pagameto").on("click", function(){ $("#pagamentoData").trigger("focus"); });
			$("#cld_dtaInicial").on("click", function(){ $("#dtaInicial").trigger("focus"); });
			$("#cld_dtaFinal").on("click", function(){ $("#dtaFinal").trigger("focus"); });
			$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
			$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});
			$( "body" ).on( "click", ".input-group-addon", function() {
  				console.log($(this).prev().trigger("focus"));
			});
		});
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

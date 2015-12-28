<?php
	include_once "util/login/restrito.php";
	restrito(array(8,1));
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

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">

	<!-- autocomplete -->
	<link href="css/autocomplete.css" rel="stylesheet">
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

		/*@media screen and (min-width: 768px) {

			#list_validades.modal-dialog  {width:900px;}

		}

		#list_validades .modal-dialog  {width:70%;}

		#list_validades .modal-content {min-height: 640px;}*/

		.error-estoque td {
			background: #FF9191
		}
	</style>
  </head>

  <body ng-click="closeAutoComplete($event)" class="overflow-hidden" ng-controller="PDVController" ng-cloak>
  	<!-- Overlay Div -->
	<div id="overlay" class="transparent"></div>

	<div id="wrapper" class="preload">
		<div id="top-nav" class="fixed skin-6">
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

		<aside class="fixed skin-6">
			<div class="sidebar-inner scrollable-sidebar">
				<div class="size-toggle">
					<a class="btn btn-sm" id="sizeToggle">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<a class="btn btn-sm pull-right logoutConfirm_open"  href="#logoutConfirm">
						<i class="fa fa-power-off"></i>
					</a>
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

				<div class="main-menu">
					<ul>
						<!-- Dashboard (index) -->
						<li>
							<a href="dashboard.php">
								<span class="menu-icon"><i class="fa fa-dashboard fa-lg"></i></span>
								<span class="text">Dashboard</span>
								<span class="menu-hover"></span>
							</a>
						</li>

						<!-- Módulos -->
						<li class="active openable">
							<a href="#">
								<span class="menu-icon"><i class="fa fa-th fa-lg"></i></span>
								<span class="text">Módulos</span>
								<span class="menu-hover"></span>
							</a>
							<ul class="submenu">
								<?php include("menu-modulos.php") ?>
							</ul>
						</li>

						<!-- Relatórios -->
						<li class="openable">
							<a href="#">
								<span class="menu-icon"><i class="fa fa-copy fa-lg"></i></span>
								<span class="text">Relatórios</span>
								<span class="menu-hover"></span>
							</a>
							<ul class="submenu">
								<?php include("menu-relatorios.php"); ?>
							</ul>
						</li>
					</ul>

					<!-- Exemplos de Alerta -->
					<!-- <div class="alert alert-info">Welcome to Endless Admin. Do not forget to check all my pages.</div>
					<div class="alert alert-danger">Welcome to Endless Admin. Do not forget to check all my pages.</div>
					<div class="alert alert-warning">Welcome to Endless Admin. Do not forget to check all my pages.</div> -->
				</div><!-- /main-menu -->
			</div><!-- /sidebar-inner -->
		</aside>

		<div id="main-container">
			<div id="breadcrumb">
				<ul class="breadcrumb">
					 <li><i class="fa fa-home"></i> <a href="dashboard.php">Home</a></li>
					 <li><i class="fa fa-signal"></i> <a href="vendas.php">Vendas</a></li>
					 <li class="active"><i class="fa fa-desktop"></i> Frente de Caixa (PDV)</li>
				</ul>
			</div>
			<!-- breadcrumb -->

			<div class="padding-md" ng-if="caixa_aberto == false && abrir_pdv ==false && caixa_configurado == true && caixa_other_operador == false && operador_other_caixa == false">

				<div class="panel panel-primary" style="width:500px;margin:0 auto">
					<div class="panel-heading">
						<i class="fa fa-desktop"></i> Frente de Caixa | PDV - {{ caixa.dsc_conta_bancaria }}
					</div>

					<div class="panel-body">
						<h1 class="text-center">Caixa Fechado</h1>
					</div>

					<div class="panel-footer clearfix">
						<div class="text-center">
							<button data-loading-text=" Aguarde..." ng-click="abrirCaixa()"  id="btn-abrir-caixa" type="button" class="btn btn-lg btn-success" ng-click="salvar()"><i class="fa fa-unlock "></i> Abrir Caixa</button>
						</div>
					</div>
				</div>
			</div>

			<div class="padding-md" ng-if="caixa_other_operador == true">

				<div class="panel panel-warning" style="width:500px;margin:0 auto">
					<div class="panel-heading">
						<i class="fa fa-desktop"></i> Frente de Caixa | PDV - {{ caixa.dsc_conta_bancaria }}
					</div>

					<div class="panel-body">
						<h1 class="text-center">Caixa ocupado</h1>
						<div class="col-sm-12" style="text-align:center">
							<p>
								{{ msg_caixa }}
							</p>
						</div>
					</div>
				</div>
			</div>

			<div class="padding-md" ng-if="operador_other_caixa == true">

				<div class="panel panel-warning" style="width:500px;margin:0 auto">
					<div class="panel-heading">
						<i class="fa fa-desktop"></i> Frente de Caixa | PDV - {{ caixa.dsc_conta_bancaria }}
					</div>

					<div class="panel-body">
						<h1 class="text-center">Operador ocupado</h1>
						<div class="col-sm-12" style="text-align:center">
							<p>
								{{ msg_caixa }}
							</p>
						</div>
					</div>
				</div>
			</div>

			<div class="padding-md" ng-if="caixa_configurado == false">

				<div class="panel panel-danger" style="width:500px;margin:0 auto">
					<div class="panel-heading">
						<i class="fa fa-desktop"></i> Frente de Caixa
					</div>

					<div class="panel-body">
						<h1 class="text-center">Caixa não configurado</h1>
						<div class="row">
							<div class="col-sm-12">
								<p>
									As configuraçãoes necessarias para que o caixa funcione corretamente ainda não foram efetuadas.
									<br/>
									Solicite que seu administrador as faça.
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="padding-md" ng-if="abrir_pdv">
				<div class="panel panel-primary" style="width:500px;margin:0 auto">
					<div class="panel-heading">
						<i class="fa fa-desktop"></i> Frente de Caixa | PDV - {{ caixa.dsc_conta_bancaria }}
					</div>


					<div class="panel-body">
						<div class="row text-center">
						    <div class="col-sm-12">
								<div class="form-group text-center">
									<div>
										<h3>Entrada</h3>
									</div>
								</div>
							</div>
						</div>
						<div class="row text-center">
							 <div class="col-sm-2">
							 </div>
						    <div class="col-sm-4">
						    	<label class="control-label">Valor</label>
								<div class="form-group text-center" id="entrada_valor_pagamento">
									<input ng-model="abertura_reforco.valor" thousands-formatter type="text" class="form-control input-md">
								</div>
							</div>
							<div class="col-sm-4" id="entrada_conta_origem">
						    			<label class="control-label">Conta de origem</label>
							    		<select class="form-control input-md" ng-model="abertura_reforco.conta_origem">
							    					<option value=""></option>
													<option ng-repeat="item in contas" value="{{ item.id }}">{{ item.dsc_conta_bancaria }}</option>
										</select>
							</div>
							 <div class="col-sm-2">
							 </div>
						</div>
					</div>

					<div class="panel-footer clearfix">
						<div class="text-center">
							<button data-loading-text=" Aguarde..." ng-click="aplicarReforcoEntrada()"  id="btn-aplicar-abertura_reforco-entrada" type="button" class="btn btn-lg btn-success" ng-click="salvar()"><i class="fa fa-unlock "></i> Abrir PDV</button>
						</div>
					</div>
				</div>
			</div>

			<div class="padding-md" id="content-pdv" ng-if="caixa_aberto && abrir_pdv == false && caixa_configurado == true">

				<div class="panel panel-primary">
					<div class="panel-heading">
						<i class="fa fa-desktop"></i> Frente de Caixa | PDV - {{ caixa.dsc_conta_bancaria }}
						<div class="btn-group"  style="margin-left: 40px;" >
							<i class="fa fa-user"></i> Vendedor - {{ vendedor.nome_vendedor }} <i style="cursor:pointer" ng-click="selVendedor()" class="fa fa-retweet fa-lg"></i>
						</div>
						<div class="btn-group pull-right">
							<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
								<i class="fa fa-chevron-down"></i>
							</button>
							<ul class="dropdown-menu slidedown">
								<li><a href="#" ng-click="resizeScreen()"><i class="fa fa-arrows-alt"></i> Tela Inteira</a></li>
								<li><a href="#" ng-click="modalTraferencia()"><i class="fa fa-external-link"></i> Transferência (deposito)</a></li>
								<li ng-if="venda_aberta == false && finalizarOrcamento == false "><a href="#" ng-click="pagamentoFulso()"><i class="fa fa-money"></i> Pagamento</a></li>
								<li ><a href="#" ng-click="showCadastroRapido()"><i class="fa fa-users"></i> Novo Cliente</a></li>
								<li><a href="#" ng-click="modalReforco()"><i class="fa fa-download"></i> Incluir Reforço</a></li>
								<li><a href="#" ng-click="modalSangria()"><i class="fa fa-upload"></i> Efetuar Sangria</a></li>
								<li><a href="#" ng-click="modalFechar()"><i class="fa fa-sign-out"></i> Fechar Caixa</a></li>
							</ul>
						</div>
					</div>

					<div class="panel-body" ng-if="receber_pagamento">
							<div class="alert alert-pagamento" style="display:none"></div>
					    	<div class="row">
					    		<div class="col-sm-9">
					    		<div class="row" ng-if="pagamento_fulso">
					    			<div class="col-sm-12">
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
					    		</div>
					    		<div class="row">
					    			<div class="col-sm-12">
					    				<div class="form-group">
											<div style="font-weight: bold;font-size: 15px;" ng-if="cliente.vlr_saldo_devedor>0">
												<span style="color:#000">Saldo Devedor :</span> <span style="color:green">R$ {{ cliente.vlr_saldo_devedor | numberFormat:2:',':'.' }}</span>
											</div>
											<div style="font-weight: bold;font-size: 15px;" ng-if="cliente.vlr_saldo_devedor<0">
												<span style="color:#000">Saldo Devedor :</span> <span style="color:red">R$ {{ cliente.vlr_saldo_devedor | numberFormat:2:',':'.' }}</span>
											</div>
											<div style="font-weight: bold;font-size: 15px;" ng-if="cliente.vlr_saldo_devedor==0">
												<span style="color:#000">Saldo Devedor :</span> <span style="color:blue">R$ {{ cliente.vlr_saldo_devedor | numberFormat:2:',':'.' }}</span>
											</div>
											</div>
										</div>
					    		</div>
						    	<div class="row">
						    		<div class="col-sm-6" id="pagamento_forma_pagamento">
						    			<label class="control-label">Forma de Pagamento</label>
										<select ng-model="pagamento.id_forma_pagamento" ng-change="selectChange()" class="form-control input-sm">
											<option ng-if="pagamento.id_forma_pagamento != null" value=""></option>
											<option ng-repeat="item in formas_pagamento"  value="{{ item.id }}">{{ item.nome }}</option>
										</select>
									</div>
									<div class="col-sm-6" id="pagamento_valor" ng-if="pagamento.id_forma_pagamento == 7" >
										<label class="control-label">Vale troca</label>
										<div class="input-group">
											<input ng-click="showValeTroca()" thousands-formatter type="text" class="form-control input-sm" ng-model="pagamento.valor" readonly="readonly" style="cursor: pointer;" />
											<span class="input-group-btn">
												<button ng-click="showValeTroca()" type="button" class="btn btn-info btn-sm"><i class="fa fa-exchange"></i></button>
											</span>
										</div>
									</div>

									<div class="col-sm-2" id="pagamento_id_banco" ng-if="pagamento.id_forma_pagamento == 8">
										<div class="form-group" >
											<label class="control-label">Banco</label>
											<select ng-model="pagamento.id_banco" class="form-control">
												<option ng-repeat="banco in bancos" value="{{ banco.id }}">{{ banco.nome }}</option>
											</select>
										</div>
									</div>

									<div class="col-sm-2" id="pagamento_agencia_transferencia" ng-if="pagamento.id_forma_pagamento == 8">
						    			<label class="control-label">Agência</label>
						    			<div class="form-group ">
						    					<input ng-model="pagamento.agencia_transferencia"  type="text" class="form-control input-sm" />
						    			</div>
						    		</div>

						    		<div class="col-sm-2" id="pagamento_conta_transferencia" ng-if="pagamento.id_forma_pagamento == 8">
						    			<label class="control-label">Conta</label>
						    			<div class="form-group ">
						    					<input ng-model="pagamento.conta_transferencia"  type="text" class="form-control input-sm" />
						    			</div>
						    		</div>

						    		<div class="col-sm-6" id="pagamento_valor" ng-if="pagamento.id_forma_pagamento != 7 && pagamento.id_forma_pagamento != 8">
						    			<label class="control-label">Valor</label>
						    			<div class="form-group ">
						    					<input ng-disabled="pagamento.id_forma_pagamento == 2 || pagamento.id_forma_pagamento == 4" ng-model="pagamento.valor" thousands-formatter type="text" class="form-control input-sm" />
						    			</div>
						    		</div>
						    	</div>

						    	<div class="row">
						    		<div class="col-sm-6" id="pagamento_maquineta" ng-if="pagamento.id_forma_pagamento == 5 || pagamento.id_forma_pagamento == 6 ">
						    			<label class="control-label">Maquineta</label>
										<select ng-model="pagamento.id_maquineta" class="form-control input-sm">
											<option ng-repeat="item in maquinetas" value="{{ item.id_maquineta }}">#{{ item.id_maquineta }} - {{ item.dsc_conta_bancaria }}</option>
										</select>
									</div>
						    		<div class="col-sm-6" id="numero_parcelas" ng-if="pagamento.id_forma_pagamento == 6 || pagamento.id_forma_pagamento == 2 || pagamento.id_forma_pagamento == 4">
						    			<label class="control-label">parcelas</label>
						    			<div class="form-group ">
						    					<input ng-blur="pushCheques()" ng-focus="qtdCheque()" ng-model="pagamento.parcelas" type="text" class="form-control input-sm" >
						    			</div>
						    		</div>
						    		<div class="col-sm-4" id="proprietario_conta_transferencia" ng-if="pagamento.id_forma_pagamento == 8">
						    			<label class="control-label">Proprietário</label>
						    			<div class="form-group ">
						    					<input ng-model="pagamento.proprietario_conta_transferencia" type="text" class="form-control input-sm" />
						    			</div>
						    		</div>
						    		<div class="col-sm-4" id="pagamento_id_conta_transferencia_destino" ng-if="pagamento.id_forma_pagamento == 8 ">
						    			<label class="control-label">Conta de Destino</label>
										<select ng-model="pagamento.id_conta_transferencia_destino" class="form-control input-sm">
											<option ng-repeat="item in contas" value="{{ item.id }}">#{{ item.id }} - {{ item.dsc_conta_bancaria }}</option>
										</select>
									</div>
						    		<div class="col-sm-4" id="pagamento_valor" ng-if="pagamento.id_forma_pagamento == 8">
						    			<label class="control-label">Valor</label>
						    			<div class="form-group ">
						    					<input ng-model="pagamento.valor" thousands-formatter type="text" class="form-control input-sm" />
						    			</div>
						    		</div>
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
												<label class="control-label">valor</label>
												<div class="form-group ">
						    						<input ng-blur="pushCheques()" ng-keyUp="calTotalCheque()"  thousands-formatter ng-model="item.valor_pagamento" type="text" class="form-control" >
						    					</div>
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-group cheque_banco" >
												<label class="control-label">Banco</label>
												<select ng-model="item.id_banco" class="form-control">
													<option ng-repeat="banco in bancos" value="{{ banco.id }}">{{ banco.nome }}</option>
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
												<label class="control-label">valor</label>
												<div class="form-group ">
						    						<input ng-blur="pushCheques()" ng-keyUp="calTotalBoleto()"  thousands-formatter ng-model="item.valor_pagamento" type="text" class="form-control" >
						    					</div>
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-group boleto_banco" >
												<label class="control-label">Banco</label>
												<select ng-model="item.id_banco" class="form-control">
													<option ng-repeat="banco in bancos" value="{{ banco.id }}">{{ banco.nome }}</option>
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
					    		<div class="row">
					    			<div class="col-sm-12 text-center">
					    				<label class="control-label">&nbsp</label>
						    			<div class="form-group ">
						    				<button type="button" class="btn btn-md btn-success btn-block"   ng-click="aplicarRecebimento()">Receber</button>
						    			</div>
						    		</div>
								</div>
							</div>
							<div class="col-sm-3">
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
										<tr>
											<td colspan="2" ng-if="total_pg <= vlrTotalCompra">
												Total a Receber <strong class="pull-right">R$ {{ vlrTotalCompra - total_pg | numberFormat:2:',':'.' }}</strong>
											</td>
											<td colspan="2" ng-if="total_pg > vlrTotalCompra" >
												Total a Receber <strong class="pull-right">R$ 0,00</strong>
											</td>
										</tr>
										<tr ng-if="modo_venda == 'pdv'">
											<td colspan="2">
												Troco <strong class="pull-right">R$ {{ troco | numberFormat:2:',':'.' }}</strong>
											</td>
										</tr>
										<tr ng-if="modo_venda == 'est' && (total_pg > vlrTotalCompra)">
											<td colspan="2">
												Troco sugerido<strong class="pull-right">R$ {{ ((vlrTotalCompra - total_pg) * (-1)) | numberFormat:2:',':'.' }}</strong>
											</td>
										</tr>
										<tr ng-if="modo_venda == 'est' && (total_pg > vlrTotalCompra)">
											<td colspan="2">
												<div class="row">
													<div class="col-sm-10">
														Troco
													</div>
													<div class="col-sm-2">
														  <input ng-model="troco_opcional" thousands-formatter class="form-control input-sm" >
													</div>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<style type="text/css">
						.panel-menor{
							width:500px;
							margin:0 auto;
						}
					</style>

					<div class="panel-body" ng-if="receber_pagamento == false">

						<form role="form" ng-if="venda_aberta == false" class="panel-menor" >
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label class="control-label"><h4>Cliente</h4></label>
										<div class="input-group">
											<input ng-click="selCliente(0,10)" ng-disabled="finalizarOrcamento"   type="text" class="form-control" ng-model="cliente.nome" readonly="readonly" style="cursor: pointer;" />
											<span class="input-group-btn">
												<button ng-click="selCliente(0,10)" type="button" ng-disabled="finalizarOrcamento" class="btn btn-info"><i class="fa fa-users"></i></button>
											</span>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<button data-loading-text=" Aguarde..."  type="button" class="btn btn-lg btn-success btn-block" ng-click="abrirVenda('pdv')">Nova Venda (Modo Loja)</button>
									<button data-loading-text=" Aguarde..."  type="button" class="btn btn-lg  btn-block" ng-click="abrirVenda('est')">Nova Venda (Modo Depósito)</button>
								</div>
							</div>
						</form>

						<form role="form" ng-if="venda_aberta">
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<input type="text" class="text-center form-control input-xg" readonly="readonly" ng-model="nome_ultimo_produto">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-3">
									<div class="row">
										<div class="col-sm-12">
											<div class="form-group">
												<input type="text" class="text-center form-control input-xg" readonly="readonly" ng-value="vezes_valor">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-12">
											<div class="form-group text-center">
												<img src="{{ imgProduto }}" style="max-height: 50%;">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-12">
											<div class="form-group">
												<label class="control-label"><i class="fa fa-barcode"></i> Pesquisa por Cód. Barras</label>
												<div class="input-group">
													<input id="buscaCodigo" type="text" class="form-control input-lg" ng-model="busca.codigo" sync-focus-with="!busca.ok" ng-enter="findProductByBarCode();">
													<span class="input-group-btn">
														<button type="button" class="btn btn-lg btn-primary" ng-click="findProductByBarCode();"><i class="fa fa-search"></i></button>
													</span>
												</div>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-12">
											<div class="alert alert-warning" ng-if="msg != ''">{{ msg }}</div>
										</div>
									</div>
								</div>

								<div class="col-sm-9">
									<div class="row">
										<div class="col-sm-10" id="col-sm-auto-complete-cliente">
											<div class="form-group">
												<label class="control-label"><h4>Cliente <span> <button style="cursor:auto;height: 18px;padding-top: 0;
" class="btn btn-xs btn-success" type="button" ng-if="cliente.id != '' && esconder_cliente">{{ cliente.nome }} <i style="cursor:pointer;" ng-click="removeCliente()" class="fa fa-times fa-lg fa-danger"></i></button></h4></label>
												<div class="input-group">
													<input id="input_auto_complete_cliente" ng-focus="outoCompleteCliente(busca.cliente_outo_complete,$event)" ng-disabled="finalizarOrcamento" ng-keyUp="outoCompleteCliente(busca.cliente_outo_complete)" type="text" class="form-control" ng-model="busca.cliente_outo_complete"/>
													<div class="content-outo-complete-cliente-pdv" ng-show="clientes_auto_complete.length > 0 && clientes_auto_complete_visible">
														<table class="table table-striped itens-outo-complete">
															<thead>
																<tr>
																	<th width="80" >ID</th>
																	<th class="text-center">Nome</th>
																	<th class="text-center">Apelido</th>
																	<th width="140">CPF/CNPJ</th>

																	
																</tr>
															</thead>
															<tbody>
																<tr ng-repeat="item in clientes_auto_complete" ng-click="addClienteAutoComplete(item)">
																	<td>{{ item.id }}</td>
																	<td class="text-center">{{ item.nome    | uppercase }}</td>
																	<td class="text-center">{{ item.apelido | uppercase }}</td>
																	<td ng-if="item.tipo_cadastro == 'pf'">{{ item.cpf | maskCpf }}</td>
																	<td ng-if="item.tipo_cadastro == 'pj'">{{ item.cnpj | maskCnpj }}</td>
																</tr>
															</tbody>
														</table>
													</div>
													<span class="input-group-btn">
														<button ng-click="selCliente(0,10)" ng-disabled="finalizarOrcamento" type="button" class="btn btn-info"><i class="fa fa-users"></i></button>
													</span>
												</div>
											</div>
										</div>
										<div class="col-sm-2" id="col-sm-auto-complete-produto">
											<div class="form-group">
												<label class="control-label"><h4>Produto</h4></label>
												<div class="input-group">
													<input id="input_auto_complete_produto" ng-focus="outoCompleteProduto(busca.produto_outo_complete,$event)" ng-disabled="finalizarOrcamento" ng-keyUp="outoCompleteProduto(busca.produto_outo_complete)" type="text" class="form-control" ng-model="busca.produto_outo_complete"/>
													<div class="content-outo-complete-produto-pdv" ng-show="produtos_auto_complete.length > 0 && produtos_auto_complete_visible">
														<table class="table table-striped itens-outo-complete">
														<thead>
																<tr>
																	<th width="80" >ID</th>
																	<th width="80" >Cod. Barra</th>
																	<th class="text-center">Nome</th>
																	<th class="text-center">Fabricante</th>
																	<th class="text-center"width="140">Tamanho</th>
																	<th class="text-center"width="140">Cor/Sabor</th>

																	
																</tr>
															</thead>
															<tbody>
																<tr ng-repeat="item in produtos_auto_complete" ng-click="addProdutoAutoComplete(item)">
																	<td>{{ item.id_produto }}</td>
																	<td>{{ item.codigo_barra }}</td>
																	<td class="text-center">{{ item.nome_produto    | uppercase }}</td>
																	<td class="text-center">{{ item.nome_fabricante | uppercase }}</td>
																	<td class="text-center">{{ item.peso | uppercase }}</td>
																	<td class="text-center">{{ item.sabor | uppercase }}</td>
																</tr>
															</tbody>
														</table>
													</div>
													<span class="input-group-btn">
														<button ng-click="findProductByBarCode()" ng-disabled="finalizarOrcamento" type="button" class="btn btn-primary"><i class="fa fa fa-archive"></i></button>
													</span>
												</div>
											</div>
										</div>
										
									</div>

									<div class="row" ng-if="out_produtos.length > 0">
										<div class="col-sm-12">
											<div class="alert alert-out alert-warning">
												Desculpe, os produtos marcados em <span style="color:#FF9191">vermelho</span>,
											    não estão mais disponivel em nosso estoque, para continuar a venda basta
											    retira-los do carrinho.
											</div>
										</div>
									</div>

									<div class="row" ng-if="userLogged.id_perfil == 1 ">
										<div class="col-sm-6">
											<button ng-if="show_vlr_real == false" ng-click="showVlrReal()" class="btn btn-xs btn-success" type="button">
												<i class="fa fa-eye fa-lg"></i>
											</button>
											<button ng-if="show_vlr_real" ng-click="showVlrReal()" class="btn btn-xs btn-success" type="button">
												<i class="fa fa-eye-slash fa-lg"></i>
											</button>
										</div>
										<div class="col-sm-6">
											<div style="float: right;font-weight: bold;font-size: 15px;" ng-if="cliente.vlr_saldo_devedor>0">
												<span style="color:#000">Saldo Devedor :</span> <span style="color:green">R$ {{ cliente.vlr_saldo_devedor | numberFormat:2:',':'.' }}</span>
											</div>
											<div style="float: right;font-weight: bold;font-size: 15px;" ng-if="cliente.vlr_saldo_devedor<0">
												<span style="color:#000">Saldo Devedor :</span> <span style="color:red">R$ {{ cliente.vlr_saldo_devedor | numberFormat:2:',':'.' }}</span>
											</div>
											<div style="float: right;font-weight: bold;font-size: 15px;" ng-if="cliente.vlr_saldo_devedor==0">
												<span style="color:#000">Saldo Devedor :</span> <span style="color:blue">R$ {{ cliente.vlr_saldo_devedor | numberFormat:2:',':'.' }}</span>
											</div>
										</div>
									</div>
									<br/>
									<div class="row" style="min-height:200px">
										<div class="col-sm-12">
											<div class="form-group">
												<table id="tbl_carrinho" class="table table-condensed table-bordered">
													<tr ng-hide="carrinho.length > 0" class="hidden-print">
														<td colspan="4">
															Não há Produtos para a venda
														</td>
													</tr>
													<thead ng-show="carrinho.length  > 0">
														<tr>
															<th>Produto</th>
															<th>Fabricante</th>
															<th>Tamanho</th>
															<th>Sabor/Cor</th>
															<th class="text-center" style="width: 100px;" ng-if="show_vlr_real" >RV</th>
															<th class="text-center" style="width: 80px;" >Qtd</th>
															<th class="text-center" style="width: 100px;">Vlr. uni.</th>
															<th class="text-center" style="width: 230px;" colspan="3">Desconto</th>
															<th class="text-center" style="width: 100px;">Vlr ven.</th>
															<th class="text-center" style="width: 100px;">Subtotal</th>
															<th class="text-center" style="width: 20px;" class="hidden-print"></th>
														</tr>
													</thead>
													<tbody>
														<tr ng-repeat="item in carrinho" id="{{ item.id_produto }}" ng-class="{'error-estoque': verificaOutEstoque(item) }">
															<td>{{ item.nome_produto }}</td>
															<td>{{ item.nome_fabricante }}</td>
															<td>{{ item.peso }}</td>
															<td>{{ item.sabor }}</td>
															<td class="text-center" ng-if="show_vlr_real" > R${{ item.vlr_custo_real | numberFormat : 2 : ',' : '.' }}</td>
															<td class="text-center" width="20">
																<input ng-keyUp="calcSubTotal(item)" ng-disabled="finalizarOrcamento" ng-model="item.qtd_total" type="text" class="form-control input-xs" width="50" />
															</td>

															<td class="text-right">R$ {{ item.vlr_real | numberFormat : 2 : ',' : '.' }}</td>
															<td class="text-center" style="width: 30px;">
																<label class="label-checkbox">
																	<input ng-model="item.flg_desconto" ng-disabled="finalizarOrcamento" type="checkbox" id="toggleLine" ng-true-value="1" ng-false-value="0" ng-click="aplicarDesconto($index,$event)" />
																	<span class="custom-checkbox"></span>
																</label>
															</td>
															<td class="text-right" style="width:100px;">
																<span style="display: block;float: left;" ng-if="item.flg_desconto == 1">%</span>
																<input style="width:50px;float:right" ng-disabled="finalizarOrcamento" ng-keyUp="aplicarDesconto($index,$event,false,false)" ng-if="item.flg_desconto == 1" thousands-formatter ng-model="item.valor_desconto" type="text" class="form-control input-xs" />
															</td>
															<td class="text-right" style="width:100px;">
																<span style="display: block;float: left;" ng-if="item.flg_desconto == 1">R$</span>
																<input style="width:50px;float:right" ng-disabled="finalizarOrcamento" ng-keyUp="aplicarDesconto($index,$event,false,true)" ng-if="item.flg_desconto == 1" thousands-formatter ng-model="item.valor_desconto_real" type="text" class="form-control input-xs" id="teste_teste" />
															</td>
															<td class="text-right">R$ {{ item.vlr_unitario    | numberFormat : 2 : ',' : '.' }}</td>
															<td class="text-right">R$ {{ item.sub_total    | numberFormat : 2 : ',' : '.' }}</td>
															<td class="text-center" class="hidden-print">
																<button type="button" class="btn btn-xs btn-danger" ng-disabled="finalizarOrcamento" ng-click="delItem($index)"><i class="fa fa-trash-o"></i></button>
															</td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-12">
											<div class="form-group">
												<div class="pull-right" ng-show="carrinho.length > 0">
													<h2>R$ {{ vlrTotalCompra | numberFormat : 2 : ',' : '.' }}</h2>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-12">
											<div class="form-group">
												<div class="pull-right" ng-show="carrinho.length > 0">
													<h2 style="font-size: 17px;">total de Itens : {{ total_itens }} </h2>
												</div>
											</div>
										</div>
									</div>
									<!--
									<div class="row" ng-show="carrinho.length > 0">
									    <div class="col-sm-6">
											<div class="form-group">
												<div class="pull-right" >
													<h3>Valor Pago</h3>
												</div>
											</div>
										</div>

										<div class="col-sm-6">
											<div class="form-group">
												<div class="pull-right">
													<h3>R$ {{ total_pg | numberFormat : 2 : ',' : '.' }}</h3>
												</div>
											</div>
										</div>

									</div>
									<div class="row" ng-show="troco > 0">
									    <div class="col-sm-6">
											<div class="form-group">
												<div class="pull-right" >
													<h3>Troco</h3>
												</div>
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group">
												<div class="pull-right">
													<h3>R$ {{ troco | numberFormat : 2 : ',' : '.' }}</h3>
												</div>
											</div>
										</div>
									</div>
									-->
								</div>
							</div>
						</form>
			</div>

					<div class="panel-footer clearfix" ng-if="venda_aberta" >
						<!--<div class="pull-left" ng-if="receber_pagamento == false">
							<button type="button" class="btn btn-lg btn-primary" ng-click="modalReforco()"> Reforço</button>
							<button type="button" class="btn btn-lg btn-primary" ng-click="modalSangria()"> Sangria</button>
							<button type="button" class="btn btn-lg btn-primary" ng-click="modalFechar()"> Fechar</button>
						</div>-->
						<div class="pull-right">
							<button type="button" class="btn btn-lg btn-danger" ng-if="receber_pagamento == false" ng-click="cancelar()"><i class="fa fa-times-circle"></i> Cancelar Venda</button>
							<button type="button" class="btn btn-lg btn-warning" ng-if="receber_pagamento" ng-click="cancelarPagamento()"><i class="fa fa-times-circle"></i> Cancelar Pagamento</button>
							<button type="button" class="btn btn-lg btn-success" ng-if="receber_pagamento || modo_venda == 'est'" data-loading-text=" Aguarde..." id="btn-fazer-compra" ng-click="salvar()" ng-disabled=" (modo_venda == 'pdv' && (total_pg == 0 || total_pg < vlrTotalCompra)) || (modo_venda == 'est' && (carrinho.length <= 0))"><i class="fa fa-save"></i> Finalizar</button>
							<button type="button" class="btn btn-lg btn-primary" ng-if="receber_pagamento == false" ng-disabled="carrinho.length == 0" ng-click="receberPagamento()"><i class="fa fa-money"></i> Receber</button>
							<button type="button" class="btn btn-lg btn-success" ng-if="receber_pagamento == false && finalizarOrcamento == false" data-loading-text=" Aguarde..." id="btn-fazer-orcamento" ng-click="salvarOrcamento()" ng-disabled="carrinho.length <= 0"><i class="fa fa-save"></i> Orçamento</button>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /main-container -->



		<!-- Modal Pagamento  -->
		<div class="modal fade" id="modal-receber" style="display:none">
  			<div class="modal-dialog error modal-lg">
    			<div class="modal-content">
      				<div class="modal-header">
						<h4>Pagamento</h4>
      				</div>

				    <div class="modal-body">
				    	<div class="alert alert-pagamento" style="display:none"></div>
					    	<div class="row">
					    		<div class="col-sm-6">
						    	<div class="row">
						    		<div class="col-sm-6" id="pagamento_forma_pagamento">
						    			<label class="control-label">Forma de Pagamento</label>
										<select ng-model="pagamento.id_forma_pagamento" class="form-control input-sm">
											<option ng-if="pagamento.id_forma_pagamento != null" value=""></option>
											<option ng-repeat="item in formas_pagamento" value="{{ item.id }}">{{ item.nome }}</option>
										</select>
									</div>

						    		<div class="col-sm-6" id="pagamento_valor">
						    			<label class="control-label">Valor</label>
						    			<div class="form-group ">
						    					<input ng-model="pagamento.valor" thousands-formatter type="text" class="form-control input-sm" >
						    			</div>
						    		</div>
						    	</div>

						    	<div class="row">
						    		<div class="col-sm-6" id="pagamento_maquineta" ng-if="pagamento.id_forma_pagamento == 5 || pagamento.id_forma_pagamento == 6 ">
						    			<label class="control-label">Maquineta</label>
										<select ng-model="pagamento.id_maquineta" class="form-control input-sm">
											<option ng-repeat="item in maquinetas" value="{{ item.id_maquineta }}">#{{ item.id_maquineta }} - {{item.dsc_conta_bancaria }}</option>
										</select>
									</div>
						    		<div class="col-sm-6" id="numero_parcelas" ng-if="pagamento.id_forma_pagamento == 6">
						    			<label class="control-label">parcelas</label>
						    			<div class="form-group ">
						    					<input ng-model="pagamento.parcelas" type="text" class="form-control input-sm" >
						    			</div>
						    		</div>
						    	</div>
					    		<div class="row">
					    			<div class="col-sm-12 text-center">
					    				<label class="control-label">&nbsp</label>
						    			<div class="form-group ">
						    				<button type="button" class="btn btn-md btn-success btn-block"   ng-click="aplicarRecebimento()">Receber</button>
						    			</div>
						    		</div>
								</div>
							</div>
							<div class="col-sm-6">
								<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(clientes.length != 0)">
										<tr>
											<th colspan="2" class="text-center">Recebidos</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(recebidos.length == 0)">
											<td colspan="2">Não há nenhum pagamento recebido</td>
										</tr>
										<tr ng-repeat="item in recebidos">
											<td ng-if="item.id_forma_pagamento != 6">{{ item.forma_pagamento  }} <strong class="pull-right">R$ {{ item.valor | numberFormat:2:',':'.' }}</strong></td>
											<td ng-if="item.id_forma_pagamento == 6">{{ item.forma_pagamento  }} em {{item.parcelas}}x <strong class="pull-right">R$ {{ item.valor | numberFormat:2:',':'.' }}</strong></td>
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
												total Recebido <strong class="pull-right">R$ {{ total_pg | numberFormat:2:',':'.' }}</strong>
											</td>
										</tr>
										<tr>
											<td colspan="2" ng-if="troco <= 0">
												total à receber <strong class="pull-right">R$ {{ vlrTotalCompra - total_pg | numberFormat:2:',':'.' }}</strong>
											</td>
											<td colspan="2" ng-if="troco > 0">
												total à receber <strong class="pull-right">R$ 0,00</strong>
											</td>
										</tr>
										<tr>
											<td colspan="2">
												troco <strong class="pull-right">R$ {{ troco | numberFormat:2:',':'.' }}</strong>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
				    </div>
			  	</div>
			  	<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal reforço-->
				<div class="modal fade" id="modal-reforco" style="display:none">
		  			<div class="modal-dialog error modal-sm">
		    			<div class="modal-content">
		      				<div class="modal-header">
								<h4>Reforço</h4>
		      				</div>

						    <div class="modal-body">
						    	<div class="alert alert-reforco" style="display:none"></div>

						    	<div class="row">
						    		<div class="col-sm-6" id="valor_pagamento">
						    			<label class="control-label">Valor</label>
						    			<div class="form-group ">
						    					<input ng-model="reforco.valor" thousands-formatter type="text"  class="form-control input-sm" >
						    			</div>
						    		</div>
						    		<div class="col-sm-6" id="conta_origem">
						    			<label class="control-label">Conta de origem</label>
							    		<select class="form-control input-sm" ng-model="reforco.conta_origem">
													<option ng-repeat="item in contas" value="{{ item.id }}">{{ item.dsc_conta_bancaria }}</option>
										</select>
									</div>
						    	</div>
						    </div>

						    <div class="modal-footer">
						    	<button type="button" data-loading-text=" Aguarde..." id="btn-aplicar-reforco"
						    		class="btn btn-md btn-block btn-success" ng-click="aplicarReforco()">
						    		<i class="fa fa-plus-circle"></i> Aplicar reforço
						    	</button>
						    	<button type="button" data-loading-text=" Aguarde..." ng-click="cancelarModal('modal-reforco')" id="btn-aplicar-reforco"
						    		class="btn btn-md btn-block btn-default fechar-modal">
						    		<i class="fa fa-times-circle"></i> Cancelar
						    	</button>
						    </div>
					  	</div>
					  	<!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
				</div>
				<!-- /.modal -->

				<!-- /Modal de Fechamento de caixa-->
				<div class="modal fade" id="modal-fechamento" style="display:none">
		  			<div class="modal-dialog error modal-sm">
		    			<div class="modal-content">
		      				<div class="modal-header">
								<h4>Fechar PDV</h4>
		      				</div>

						    <div class="modal-body">
						    	<div class="alert alert-reforco" style="display:none"></div>

						    	<div class="row">
						    		<div class="col-sm-12">
						    				<table class="table table-bordered table-condensed table-striped table-hover">
												<thead>
													<tr>
														<th  class="text-center">Forma de Pagamento</th>
														<th  class="text-center">Valor</th>
													</tr>
												</thead>
												<tbody>
													<tr ng-repeat="item in lacamentos_formas_pagamento">
														<td class="text-center"><strong>{{ item.forma_pagamento }}</strong></td>
														<td class="text-center"><strong>R$ {{ item.total| numberFormat:2:',':'.' }}</strong></td>
													</tr>
												</tbody>
											</table>
						    		</div>
						    		<div class="col-sm-12" id="conta_destino">
						    			<label class="control-label text-center">Conta de destino dos valores em dinheiro</label>
							    		<select class="form-control input-sm" ng-model="fechamento.id_conta_bancaria">
											<option ng-repeat="item in contas" value="{{ item.id }}">{{ item.dsc_conta_bancaria }}</option>
										</select>
									</div>
						    	</div>
						    </div>
						    <div class="modal-footer">
						  		<button type="button"  data-loading-text=" Aguarde..." id="btn-fechar-caixa"
			    					class="btn btn-md btn-success btn-block" ng-click="fecharPDV()">
			    					<i class="fa fa-lock"></i> Fechar PDV
			    				</button>

			    				<button type="button" data-loading-text=" Aguarde..." ng-click="cancelarModal('modal-fechamento')" id="btn-fechar-caixa"
			    					class="btn btn-md btn-default btn-block fechar-modal">
			    					<i class="fa fa-times-circle"></i> Cancelar
			    				</button>
						  	</div>
					  	</div>
					</div>
					<!-- /.modal-dialog -->
				</div>
				<!-- /.modal -->

		<!-- Modal Sangria  -->
		<div class="modal fade" id="modal-sangria" style="display:none">
  			<div class="modal-dialog error modal-sm">
    			<div class="modal-content">
      				<div class="modal-header">
						<h4>Sangria</h4>
      				</div>

				    <div class="modal-body">
				    	<div class="alert alert-sangria" style="display:none"></div>

				    	<div class="row">
				    		<div class="col-sm-6" id="valor_retirada_sangria">
				    			<label class="control-label">Valor</label>
				    			<div class="form-group ">
				    					<input ng-model="sangria.valor" thousands-formatter type="text" placeholder="Valor" class="form-control input-sm" >
				    			</div>
				    		</div>
				    		<div class="col-sm-6" id="conta_destino_sangria">
				    			<label class="control-label">Conta de destino</label>
					    		<select class="form-control input-sm" ng-model="sangria.conta_destino">
											<option ng-repeat="item in contas" value="{{ item.id }}">{{ item.dsc_conta_bancaria }}</option>
								</select>
							</div>

				    	</div>
				    </div>

				    <div class="modal-footer">
				    	<button type="button" data-loading-text=" Aguarde..." class="btn btn-block btn-md btn-success"
				    		id="btn-aplicar-sangria" ng-click="aplicarSangria()">
				    		<i class="fa fa-minus-circle"></i> Aplicar sangria
				    	</button>

				    	<button type="button" data-loading-text=" Aguarde..."
				    		class="btn btn-block btn-md btn-default" ng-click="cancelarModal('modal-sangria')" id="btn-aplicar-sangria">
				    		<i class="fa fa-times-circle"></i> Cancelar
				    	</button>
				    </div>
			  	</div>
			  	<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
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
									<tr ng-if="clientes != false && (clientes.length <= 0 || clientes == null)">
										<th class="text-center" colspan="9" style="text-align:center"><strong>Carregando</strong><img src="assets/imagens/progresso_venda.gif"></th>
									</tr>
									<tr ng-if="clientes == false">
										<th colspan="4" class="text-center">Não a resultados para a busca</th>
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

		<!-- /Modal Vale Troca-->
		<div class="modal fade" id="list_vl_troca" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Vale Troca para o cliente <b>{{ cliente.nome }}</b> </span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-sm-12">
								<table class="table table-bordered table-condensed table-striped table-hover">
									<tr ng-if="vales == null">
										<th class="text-center" colspan="9" style="text-align:center"><strong>Carregando</strong><img src="assets/imagens/progresso_venda.gif"></th>
									</tr>
									<tr ng-if="vales.length <= 0 && vales != undefined ">
										<th class="text-center" colspan="9" style="text-align:center"><strong>Este cliente não possui nenhum vale troca em aberto</strong></th>
									</tr>
									<thead ng-show="(vales.length != 0)">
										<tr>
											<th >ID</th>
											<th >Data</th>
											<th >valor</th>
											<th colspan="2">selecionar</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in vales">
											<td>#{{ item.id }}</td>
											<td>{{ item.dta_devolucao | dateFormat:'dateTime' }}</td>
											<td>R${{ item.vlr_disponivel  | numberFormat:2:',':'.' }}</td>
											<td width="50" align="center">
												<button type="button" ng-disabled="valeTrocaExistis(item.id)" class="btn btn-xs btn-success" ng-click="addValeTroca(item)">
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
				    			<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao.vales.length > 1">
									<li ng-repeat="item in paginacao.vales" ng-class="{'active': item.current}">
										<a href="" ng-click="loadValeTroca(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
				    		</div>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal Produtos-->
		<div class="modal fade" id="list_produtos" style="display:none">
  			<div class="modal-dialog modal-lg">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 ng-if="cdb_busca.status==false">Produtos</span></h4>
						<h4 ng-if="cdb_busca.status==true" style="margin-bottom: 0px;">Produtos</span></h4>
						<span ng-if="cdb_busca.status==true" class="text-muted">Produtos relaionados ao codigo de barra {{ cdb_busca.codigo }}</span>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.produtos" ng-enter="loadProdutos(0,10)" type="text" class="form-control input-sm">

						            <div class="input-group-btn">
						            	<button tabindex="-1" class="btn btn-sm btn-primary" type="button"
						            		ng-click="loadProdutos(0,10)">
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
									<thead ng-show="(produtos.length != 0)">
										<tr>
											<th>#</th>
											<th>Nome</th>
											<th>Fabricante</th>
											<th>Qtd.</th>
											<th>Tamanho</th>
											<th>Sabor/Cor</th>
											<th width="80">qtd</th>
											<th width="80"></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(produtos.length == 0)">
											<td colspan="3">Não a resultados para a busca</td>
										</tr>
										<tr ng-repeat="item in produtos">
											<td>{{ item.id_produto }}</td>
											<td>{{ item.nome_produto }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td>{{ item.qtd_real_estoque }}</td>
											<td>{{ item.peso }}</td>
											<td>{{ item.sabor }}</td>
											<td><input ng-keyUp="" ng-model="item.qtd_total" type="text" class="form-control input-xs" width="50" /></td>
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
		<!-- /.modal -->

		<!-- /Modal tranferencia-->
		<div class="modal fade" id="modal-transferencia" style="display:none">
  			<div class="modal-dialog  modal-lg">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Transferências entre depositos</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.estoqueDep" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadEstoqueDep(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div> <!-- /input-group-btn -->
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>
						<br />
						<div class="row">
							<div class="col-sm-12">
								<div class="alert alert-transferencia" style="display:none"></div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(estoqueDep.length != 0)">
										<tr>
											<th >Produto</th>
											<th >Fabricante</th>
											<th >Tamanho</th>
											<th >Deposito</th>
											<th style="width:40px">Qtd</th>
											<th >Validade</th>
											<th style="width:30px">Qtd para tranferência</th>
											<th >Dep. tranferência</th>
											<th colspan="2">selecionar</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(estoqueDep.length == 0 && transferencia == false)">
											<td colspan="2">Não há produtos cadastrados</td>
										</tr>
										<tr ng-show="(estoqueDep.length == 0 && transferencia == true)">
											<td colspan="2" style="background: #FBFFA5;color: #000;">Aguarde, a transferencia está sendo realizada ...</td>
										</tr>
										<tr ng-repeat="item in estoqueDep">
											<td>{{ item.nome }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td>{{ item.peso }}</td>
											<td>{{ item.nme_deposito }}</td>
											<td style="width:40px;font-weight: bold;">{{ item.qtd_item }}</td>
											<td>{{ item.dta_validade | dateFormat:"" }}</td>
											<td>
												<input ng-model="item.qtd_transferencia" type="text" class="form-control input-sm" ng-if="item.qtd_item > 0">
											</td>
											<td>
												<select class="form-control input-sm" ng-model="item.id_deposito_trasferencia" ng-if="item.qtd_item > 0">
													<option ng-repeat="deposito in depositos" value="{{ deposito.id }}" ng-if="item.id_deposito != deposito.id">{{ deposito.nme_deposito }}
													</option>
												</select>
											</td>
											<td width="50" align="center">
												<button ng-disabled="(item.qtd_transferencia == undefined || item.qtd_transferencia == '') || (item.id_deposito_trasferencia == undefined || item.id_deposito_trasferencia == '')" type="button" class="btn btn-xs btn-success" ng-click="transferenciaEst(item)" ng-if="item.qtd_item > 0">
													<i class="fa fa fa-external-link"></i> Transferir
												</button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>

						<div class="row">
				    		<div class="col-sm-12">
				    			<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao_estoqueDep.length > 1">
									<li ng-repeat="item in paginacao_estoqueDep" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadEstoqueDep(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
				    		</div>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>

		<div class="row"  id="imprimir-div" style="display: none;padding: 10px;">
				<div class="col-sm-12" style="font-size:14px">
					<strong>Cliente:</strong> {{ cliente.nome }}
					<br/><br/>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<table class="table table-condensed table-striped table-hover table-bordered">
							<thead ng-show="carrinho.length  > 0">
								<tr>
									<th>Produto</th>
									<th>Fabricante</th>
									<th>Tamanho</th>
									<th class="text-center" style="width: 80px;" >Quantidade</th>
									<th class="text-center" style="width: 100px;">Valor Unitário</th>
									<th class="text-center" style="width: 60px;">Valor desconto</th>
									<th class="text-center" style="width: 100px;">Subtotal</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="item in carrinho" id="{{ item.id_produto }}">
									<td>{{ item.nome_produto }}</td>
									<td>{{ item.nome_fabricante }}</td>
									<td>{{ item.peso }}</td>
									<td class="text-center" width="20">{{ item.qtd_total }}</td>
									<td class="text-right">R$ {{ item.vlr_unitario | numberFormat : 2 : ',' : '.' }}</td>
									<td class="text-center"><span ng-if="item.valor_desconto_real > 0 && item.valor_desconto_real != undefined">R$<span> {{ item.valor_desconto_real }}</td>
									<td class="text-right">R$ {{ item.sub_total | numberFormat : 2 : ',' : '.' }}</td>
								</tr>
								<tr>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td><b>TOTAL</b></td>
									<td>R$ {{ vlrTotalCompra }}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>


			<!-- /Modal Print-->
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
											<h3 class="m-bottom-xs m-top-xs" ng-if="pagamento_fulso != true">Comprovante de Venda</h3>
											<h3 class="m-bottom-xs m-top-xs" ng-if="pagamento_fulso == true">Comprovante de Pagamento</h3>
											<span class="text-muted">{{ userLogged.nome_empreendimento }}</span>
										</div>
									</div>

									<div class="pull-right text-right">
										<h5 ng-if="pagamento_fulso != true && finalizarOrcamento == false"><strong>#{{ id_venda }}</strong></h5>
										<h5 ng-if="finalizarOrcamento"><strong>#{{ id_orcamento }}</strong></h5>
										<h5 ng-if="pagamento_fulso == true"><strong>#{{ id_controle_pagamento }}</strong></h5>
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
						    			<strong style="font-size:14px;margin-bottom:5px">Vendedor : {{ vendedor.nome_vendedor }}</strong>
						    			<br>
						    			<strong style="font-size:14px" ng-if="cliente.id != undefined && cliente.id != ''">Cliente : {{ cliente.nome }}</strong>
						    			<br>
						    			<strong style="font-size:14px;margin-bottom:5px;color:#2C800C" ng-if="vlr_saldo_devedor >= 0 && (cliente.id != undefined && cliente.id != '') && (orcamento == false)">Saldo : R${{ vlr_saldo_devedor | numberFormat : 2 : ',' : '.' }} </strong>
						    			<strong style="font-size:14px;margin-bottom:5px;color:#D82121" ng-if="vlr_saldo_devedor < 0 && (cliente.id != undefined && cliente.id != '') && (orcamento == false)">Saldo : R$ {{ vlr_saldo_devedor | numberFormat : 2 : ',' : '.' }} </strong>
						    			<strong style="font-size:14px;margin-bottom:5px;color:#2C800C" ng-if="(cliente.id != undefined && cliente.id != '' && cliente.vlr_saldo_devedor >= 0) && (orcamento == true)">Saldo : R$ {{ cliente.vlr_saldo_devedor | numberFormat : 2 : ',' : '.' }} </strong>
						    			<strong style="font-size:14px;margin-bottom:5px;color:#D82121" ng-if="(cliente.id != undefined && cliente.id != '' && cliente.vlr_saldo_devedor < 0) && (orcamento == true)">Saldo : R$ {{ cliente.vlr_saldo_devedor | numberFormat : 2 : ',' : '.' }} </strong>
						    			<br>
						    			<br>
						    			<table class="table table-bordered" ng-if="pagamento_fulso != true">
											<thead ng-show="carrinho.length  > 0">
												<tr>
													<th>Produto</th>
													<th>Fabricante</th>
													<th>Tamanho</th>
													<th class="text-center" style="width: 80px;" >Quantidade</th>
													<th class="text-center" style="width: 100px;">Valor Unitário</th>
													<th class="text-center" style="width: 60px;">Valor desconto</th>
													<th class="text-center" style="width: 100px;">Subtotal</th>
												</tr>
											</thead>
											<tbody>
												<tr ng-repeat="item in carrinho" id="{{ item.id_produto }}">
													<td>{{ item.nome_produto }}</td>
													<td>{{ item.nome_fabricante }}</td>
													<td>{{ item.peso }}</td>
													<td class="text-center" width="20">{{ item.qtd_total }}</td>
													<td class="text-right">R$ {{ item.vlr_unitario | numberFormat : 2 : ',' : '.' }}</td>
													<td class="text-center"><span ng-if="item.valor_desconto_real > 0 && item.valor_desconto_real != undefined">R$<span> {{ item.valor_desconto_real }}</td>
													<td class="text-right">R$ {{ item.sub_total | numberFormat : 2 : ',' : '.' }}</td>
												</tr>
												<tr>
													<td colspan="6"><b>TOTAL</b></td>
													<td style="text-align:right">R$ {{ vlrTotalCompra }}</td>
												</tr>
											</tbody>
										</table>
						    		</div>
						    	</div>

						    		<div class="row" id="tbl_print_pg">
							    		<div class="col-sm-12" id="valor_pagamento">
							    			<table class="table table-bordered table-condensed table-striped table-hover">
												<thead ng-if="pagamento_fulso != true" >
													<tr>
														<th colspan="2" class="text-center" >Pagamentos referentes a venda</th>
													</tr>
												</thead>
												<tbody>
													<tr ng-if="(recebidos.length == 0)">
														<td colspan="1">Não foi recebido nenhum pagamento</td>
													</tr>
													<tr ng-repeat="item in recebidos">
														<td ng-if="item.id_forma_pagamento != 6">{{ item.forma_pagamento  }} <strong class="pull-right">R$ {{ item.valor | numberFormat:2:',':'.' }}</strong></td>
														<td ng-if="item.id_forma_pagamento == 6">{{ item.forma_pagamento  }} em {{item.parcelas}}x <strong class="pull-right">R$ {{ item.valor | numberFormat:2:',':'.' }}</strong></td>
													</tr>
												</tbody>
											</table>
							    		</div>
						    		</div>

						    </div>

						    <div class="modal-footer">
						    	<a href="client/launch.jnlp" class="btn btn-md btn-block btn-primary" ng-click="printTermic()">
						    		<i class="fa fa-print"></i> Imprimir (via Impressora Térmica)
						    	</a>
						    	<button type="button" data-loading-text=" Aguarde..." id="btn-imprimir"
						    		class="btn btn-md btn-block btn-success" ng-click="printDiv('modal-print')">
						    		<i class="fa fa-print"></i> Imprimir (via Papel A4)
						    	</button>
						    	<a ng-click="cancelar()" class="btn btn-md btn-block btn-default">
						    		<i class="fa fa-reply"></i> Voltar ao PDV
						    	</a>
						    </div>
					  	</div>
					  	<!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
				</div>
				<!-- /.modal -->

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

				<!-- /Modal Cadastro rapido de clientes-->
				<div class="modal fade" id="modal_cadastro_rapido_cliente" style="display:none">
		  			<div class="modal-dialog error modal-lg">
		    			<div class="modal-content">
		      				<div class="modal-header">
								<h4>Cadastro Rápido de cliente</h4>
		      				</div>

						    <div class="modal-body">
						    	<div class="alert alert-cadastro-rapido" style="display:none"></div>
									<div class="row">
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
													<div class="col-sm-4">
														<div id="nome" class="form-group">
															<label for="nome" class="control-label">Nome <span style="color:red;font-weight: bold;">*</span></label>
															<input type="text" class="form-control" id="nome" ng-model="new_cliente.nome">
														</div>
													</div>
													<div class="col-sm-4">
														<div id="email" class="form-group">
															<label for="email" class="control-label">E-mail</label>
															<input type="text" class="form-control" id="email" ng-model="new_cliente.email">
														</div>
													</div>
													<div class="col-sm-4">
														<div id="id_perfil" class="form-group">
															<label class="control-label">Perfil  <span style="color:red;font-weight: bold;">*</span></label>
															<select class="form-control" ng-model="new_cliente.id_perfil" ng-options="a.id as a.nome for a in perfis"></select>
														</div>
													</div>
											    </div>
											    <div class="row" ng-if="new_cliente.tipo_cadastro == 'pj'">
													<div class="col-lg-4">
														<div id="razao_social" class="form-group">
															<label class="control-label">Razão Social</label>
															<input class="form-control" ng-model="new_cliente.razao_social">
														</div>
													</div>

													<div class="col-sm-4">
														<div id="nome_fantasia" class="form-group">
															<label class="control-label">Nome Fantasia</label>
															<input class="form-control" ng-model="new_cliente.nome_fantasia">
														</div>
													</div>

													<div class="col-sm-2">
														<div id="cnpj" class="form-group">
															<label class="control-label">CNPJ  <span style="color:red;font-weight: bold;">*</span></label>
															<input class="form-control" ui-mask="99.999.999/9999-99" ng-model="new_cliente.cnpj">
														</div>
													</div>

													<div class="col-sm-2">
														<div id="inscricao_estadual" class="form-group">
															<label class="control-label">I.E. </label>
															<input class="form-control" ng-model="new_cliente.inscricao_estadual">
														</div>
													</div>
												</div>
											    <div class="row" ng-if="new_cliente.tipo_cadastro == 'pf'">
													<div class="col-sm-2">
														<div id="rg" class="form-group">
															<label class="control-label">RG</label>
															<input class="form-control" ui-mask="99.999.999-9" ng-model="new_cliente.rg"/>
														</div>
													</div>

													<div class="col-sm-2">
														<div id="cpf" class="form-group">
															<label class="control-label">CPF <span style="color:red;font-weight: bold;">*</span></label>
															<input class="form-control" ui-mask="999.999.999-99" ng-model="new_cliente.cpf"/>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
						    </div>
						      <div class="modal-footer">
						      		<button type="button" class="btn btn-danger btn-sm" ng-click="cancelarModal('modal_cadastro_rapido_cliente')">
										<i class="fa fa-times-circle"></i> Cancelar
									</button>
									<button data-loading-text=" Aguarde..." id="btn-salvar-cliente" type="submit" class="btn btn-success btn-sm" ng-click="salvarCliente()">
										<i class="fa fa-save"></i> Salvar Cliente
									</button>
						    </div>
					  	</div>
					  	<!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
				</div>
				<!-- /.modal -->

				<!-- /Modal Vendedor -->
				<div class="modal fade" id="list-vendedor" style="display:none">
		  			<div class="modal-dialog">
		    			<div class="modal-content" ng-if="modal_senha_vendedor.show == false">
		      				<div class="modal-header">
		        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4>Vendedores</span></h4>
		      				</div>
						    <div class="modal-body">
								<div class="row">
									<div class="col-md-12">
										<div class="input-group">
								            <input ng-model="busca.vendedor"  ng-enter="loadVendedor(0,10)" type="text" class="form-control input-sm">
								            <div class="input-group-btn">
								            	<button ng-click="loadVendedor(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
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
											<tr ng-if="clientes != false && (clientes.length <= 0 || clientes == null)">
												<th class="text-center" colspan="9" style="text-align:center"><strong>Carregando</strong><img src="assets/imagens/progresso_venda.gif"></th>
											</tr>
											<tr ng-if="clientes == false">
												<th class="text-center" colspan="9" colspan="9" style="text-align:center">Não a resultados para a busca</th>
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
														<button type="button" class="btn btn-xs btn-success" ng-click="modalSenhaVendedor(item)">
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
												<a href="" h ng-click="loadVendedor(item.offset,item.limit)">{{ item.index }}</a>
											</li>
										</ul>
						    		</div>
						    	</div>
						    </div>
					  	</div><!-- /.modal-content -->
					  	<div class="modal-content" ng-if="modal_senha_vendedor.show">
					  		<div class="modal-header">
		        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4>Selecionar Vendedor</span></h4>
		      				</div>
		      				<div class="modal-body" ng-if="modal_senha_vendedor.show == true">
		      					<div class="row">
		      						<div class="col-sm-12">
		      							<div class="alert alert-vendedor" style="display:none">
		      								
		      							</div>
		      						</div>
		      					</div>
		      					<form class="form-horizontal">
									<div class="form-group">
										<label for="inputEmail1" class="col-lg-2 control-label">Vendedor</label>
										<div class="col-lg-10">
											<label class="label-checkbox">{{ modal_senha_vendedor.nome_vendedor }}</label>
										</div><!-- /.col -->
									</div><!-- /form-group -->
									<div class="form-group" id="senha_vendedor">
										<label for="inputPassword1" class="col-lg-2 control-label">Senha</label>
										<div class="col-lg-6">
											<input type="password" ng-model="modal_senha_vendedor.senha_vendedor" class="form-control input-sm">
										</div><!-- /.col -->
									</div><!-- /form-group -->
									<div class="form-group">
										<div class="col-lg-offset-2 col-lg-10">
											<button ng-click="mudarVendedor()" class="btn btn-success btn-sm">Selecionar</button>
										</div><!-- /.col -->
									</div><!-- /form-group -->
								</form>
		      				</div>
					  	</div>
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

	<form style="display:none" id="enviar-print" action="print.php" method="POST" target="_blank">
				<input  id="content_print" name="content_print" />
	</form>

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

	<!-- Datepicker -->
	<script src='js/bootstrap-datepicker.min.js'></script>

    <!-- Slimscroll -->
	<script src='js/jquery.slimscroll.min.js'></script>

	<!-- Cookie -->
	<script src='js/jquery.cookie.min.js'></script>

	<!-- Endless -->
	<script src="js/endless/endless.js"></script>

	<!-- Extras -->
	<script src="js/extras.js"></script>

	<!-- UnderscoreJS -->
	<script type="text/javascript" src="bower_components/underscore/underscore.js"></script>


	<!-- ScrennFull  -->
	<script type="text/javascript" src="js/screenfull/screenfull.js"></script>

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
	<script src="js/angular-controller/pdv-controller.js?<?php echo filemtime('js/angular-controller/pdv-controller.js')?>"></script>
	<?php include("google_analytics.php"); ?>

  </body>
</html>

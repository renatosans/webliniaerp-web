<?php
	include_once "util/login/restrito.php";
	restrito(array(8,1,4));
?>
<!DOCTYPE html>
<html lang="en" ng-app="HageERP">
  <head>
    <meta charset="utf-8">
    <title>PDV | WebliniaERP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Bootstrap core CSS -->
    <link rel='stylesheet prefetch' href='bootstrap/css/bootstrap.min.css?version=<?php echo date("dmY-His", filemtime("bootstrap/css/bootstrap.min.css")) ?>'>

     <!-- ui treeview -->
    <link rel="stylesheet" href="css/bootstrap-treeview.css"/>

	<!-- Font Awesome -->
	<link href="css/font-awesome-4.6.2/css/font-awesome.min.css?version<?php  echo date("dmY-His", filemtime("css/font-awesome-4.1.0.min.css")) ?>" rel="stylesheet">

	<!-- Pace -->
	<link href="css/pace.css" rel="stylesheet">

	<!-- Gritter -->
	<link href="css/gritter/jquery.gritter.css" rel="stylesheet">

	<!-- Datepicker -->
	<link href="css/datepicker.css" rel="stylesheet"/>

	<!-- Chosen -->
	<link href="css/chosen/chosen.min.css" rel="stylesheet"/>

	<!-- Bower Components -->	
	<link href="bower_components/noty/lib/noty.css" rel="stylesheet">
	<link href="bower_components/print-js/dist/print.min.css" rel="stylesheet">

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">

	<!-- autocomplete -->
	<link href="css/autocomplete.css" rel="stylesheet">

	<!-- Tags Input -->
	<link href="css/ng-tags-input.min.css" rel="stylesheet"/>
	<link href="css/ng-tags-input.bootstrap.min.css" rel="stylesheet"/>

	<style type="text/css">
		/* Fix for Bootstrap 3 with Angular UI Bootstrap */

		.modal {
			display: block;
		}

		/* Small Devices, Tablets */
		@media only screen and (min-width : 768px) {
			.modal-xl {
				width: 100%;
			}
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

		<div id="main-container" style="min-height: 0px !important;">
			<div id="breadcrumb">
				<ul class="breadcrumb">
					 <li><i class="fa fa-home"></i> <a href="dashboard.php">Home</a></li>
					 <li><i class="fa fa-signal"></i> <a href="vendas.php">Vendas</a></li>
					 <li class="active"><i class="fa fa-desktop"></i> Frente de Caixa (PDV)</li>
				</ul>
			</div>
			<!-- breadcrumb -->

			<div class="padding-md" ng-show="caixa_aberto == false && abrir_pdv == false && caixa_configurado == true && caixa_other_operador == false && operador_other_caixa == false">
				<div class="panel panel-primary" style="width:500px;margin:0 auto">
					<div class="panel-heading">
						<i class="fa fa-desktop"></i>
						Frente de Caixa | PDV - {{ caixa.dsc_conta_bancaria }}
						<i class="fa fa-circle {{ (caixa_aberto.flg_imprimir_sat_cfe == 1) ? 'text-success' : 'text-danger' }}" ng-if="(configuracoes.flg_tipo_documento_fiscal_consumidor == 'SAT')"></i>
						<i class="fa fa-circle {{ (caixa_aberto.flg_imprimir_nfce == 1) ? 'text-success' : 'text-danger' }}" ng-if="(configuracoes.flg_tipo_documento_fiscal_consumidor == 'NFCe')"></i>
						<div class="btn-group pull-right">
							<a href="#" class="btn btn-xs btn-primary hidden-xs" ng-click="resizeScreen()">
								<i class="fa fa-arrows-alt"></i> Tela Inteira
							</a>
						</div>
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
						<i class="fa fa-desktop"></i> 
						Frente de Caixa | PDV - {{ caixa.dsc_conta_bancaria }}
						<i class="fa fa-circle {{ (caixa_aberto.flg_imprimir_sat_cfe == 1) ? 'text-success' : 'text-danger' }}" ng-if="(configuracoes.flg_tipo_documento_fiscal_consumidor == 'SAT')"></i>
						<i class="fa fa-circle {{ (caixa_aberto.flg_imprimir_nfce == 1) ? 'text-success' : 'text-danger' }}" ng-if="(configuracoes.flg_tipo_documento_fiscal_consumidor == 'NFCe')"></i>
						<div class="btn-group pull-right">
							<a href="#" class="btn btn-xs btn-warning hidden-xs" ng-click="resizeScreen()">
								<i class="fa fa-arrows-alt"></i> Tela Inteira
							</a>
						</div>
					</div>

					<div class="panel-body">
						<h1 class="text-center">Caixa Ocupado</h1>
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
						<i class="fa fa-desktop"></i> 
						Frente de Caixa | PDV - {{ caixa.dsc_conta_bancaria }}
						<i class="fa fa-circle {{ (caixa_aberto.flg_imprimir_sat_cfe == 1) ? 'text-success' : 'text-danger' }}" ng-if="(configuracoes.flg_tipo_documento_fiscal_consumidor == 'SAT')"></i>
						<i class="fa fa-circle {{ (caixa_aberto.flg_imprimir_nfce == 1) ? 'text-success' : 'text-danger' }}" ng-if="(configuracoes.flg_tipo_documento_fiscal_consumidor == 'NFCe')"></i>
						<div class="btn-group pull-right">
							<a href="#" class="btn btn-xs btn-warning hidden-xs" ng-click="resizeScreen()">
								<i class="fa fa-arrows-alt"></i> Tela Inteira
							</a>
						</div>
					</div>

					<div class="panel-body">
						<h1 class="text-center">Operador Ocupado</h1>
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
						<i class="fa fa-desktop"></i> 
						Frente de Caixa
						<i class="fa fa-circle {{ (caixa_aberto.flg_imprimir_sat_cfe == 1) ? 'text-success' : 'text-danger' }}" ng-if="(configuracoes.flg_tipo_documento_fiscal_consumidor == 'SAT')"></i>
						<i class="fa fa-circle {{ (caixa_aberto.flg_imprimir_nfce == 1) ? 'text-success' : 'text-danger' }}" ng-if="(configuracoes.flg_tipo_documento_fiscal_consumidor == 'NFCe')"></i>
						<div class="btn-group pull-right">
							<a href="#" class="btn btn-xs btn-danger hidden-xs" ng-click="resizeScreen()">
								<i class="fa fa-arrows-alt"></i> Tela Inteira
							</a>
						</div>
					</div>

					<div class="panel-body">
						<h1 class="text-center">Caixa Não Configurado</h1>
						<div class="row">
							<div class="col-sm-12">
								<p>
									As configurações necessárias para que o caixa funcione corretamente ainda não foram efetuadas.
									<br/><br/>
									Entre em contato com o Administrador do sistema para obter apoio.
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="padding-md" ng-if="abrir_pdv">
				<div class="panel panel-primary" style="width:500px;margin:0 auto">
					<div class="panel-heading">
						<i class="fa fa-desktop"></i> 
						Frente de Caixa | PDV - {{ caixa.dsc_conta_bancaria }}
						<i class="fa fa-circle {{ (caixa_aberto.flg_imprimir_sat_cfe == 1) ? 'text-success' : 'text-danger' }}" ng-if="(configuracoes.flg_tipo_documento_fiscal_consumidor == 'SAT')"></i>
						<i class="fa fa-circle {{ (caixa_aberto.flg_imprimir_nfce == 1) ? 'text-success' : 'text-danger' }}" ng-if="(configuracoes.flg_tipo_documento_fiscal_consumidor == 'NFCe')"></i>
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

			<div class="padding-md" id="content-pdv" ng-show="caixa_aberto && abrir_pdv == false && caixa_configurado == true" style="padding-bottom: 0px !important;">
				<div class="panel panel-primary" style="margin-bottom: 0px !important;">
					<div class="panel-heading">
						<i style="cursor: pointer" id="dados-websocket" class="fa fa-desktop" 
							ng-class="{'text-danger':  (status_websocket == 0), 'text-warning': (status_websocket == 1), 'text-success': (status_websocket == 2)}"></i> 
						Frente de Caixa | PDV - {{ caixa.dsc_conta_bancaria }}
						<i class="fa fa-circle {{ (caixa_aberto.flg_imprimir_sat_cfe == 1) ? 'text-success' : 'text-danger' }}"
							ng-if="(configuracoes.flg_tipo_documento_fiscal_consumidor == 'SAT')"
							ng-click="changeFlagImprimirSATCFe()"></i>
						<i class="fa fa-circle {{ (caixa_aberto.flg_imprimir_nfce == 1) ? 'text-success' : 'text-danger' }}"
							ng-if="(configuracoes.flg_tipo_documento_fiscal_consumidor == 'NFCe')"
							ng-click="changeFlagImprimirNFCe()"></i>
						<div class="btn-group"  style="margin-left: 40px;" >
							<i class="fa fa-user"></i> Vendedor - {{ vendedor.nome_vendedor }}
						</div>
						<div class="btn-group pull-right">
							<a href="#" class="btn btn-xs btn-default hidden-xs" ng-click="selVendedor()">
								<i class="fa fa-retweet fa-lg"></i> Trocar Vendedor
							</a>
							<a href="#" class="btn btn-xs btn-default hidden-xs" ng-click="resizeScreen()">
								<i class="fa fa-arrows-alt"></i> Tela Inteira
							</a>
							<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
								<i class="fa fa-chevron-down"></i> Mais Opções
							</button>
							<ul class="dropdown-menu slidedown">
								<li><a href="vendas.php"><i class="fa fa-signal"></i> Minhas Vendas</a></li>
								<li><a ng-if="modo_venda == 'est'" href="#" ng-click="abrirVenda('pdv')"><i class="fa fa-desktop"></i> Nova Venda (Modo Loja)</a></li>
								<li><a ng-if="modo_venda == 'pdv'" href="#" ng-click="abrirVenda('est')"><i class="fa fa-desktop"></i> Nova Venda (Modo Depósito)</a></li>
								<li ng-show="(caixa_aberto.flg_imprimir_sat_cfe == 1 || caixa_aberto.flg_imprimir_nfce == 1)">
									<a href="#" ng-click="modalListaReenviarSat()">
										<i class="fa fa-file-text-o"></i>
										Reprocessar Cupom
										<span ng-if="(caixa_aberto.flg_imprimir_sat_cfe)">SAT</span>
										<span ng-if="(caixa_aberto.flg_imprimir_nfce)">NFC-e</span>
									</a>
								</li>
								<li ng-show="(caixa_aberto.flg_imprimir_sat_cfe == 1 || caixa_aberto.flg_imprimir_nfce == 1)">
									<a href="#" ng-click="modalCancelarCupomSat()">
										<i class="fa fa-times-circle"></i> 
										Cancelar Cupom
										<span ng-if="(caixa_aberto.flg_imprimir_sat_cfe)">SAT</span>
										<span ng-if="(caixa_aberto.flg_imprimir_nfce)">NFC-e</span>
									</a>
								</li>
								<li ng-show="caixa_aberto"><a href="#" ng-click="showModalReimpressaoCNF()"><i class="fa fa-file-text-o"></i> Re-imprimir Cupom Não Fiscal</a></li>
								<li ng-show="finalizarOrcamento == false"><a href="#" ng-click="pagamentoFulso()"><i class="fa fa-money"></i> Pagamento</a></li>
								<li class="hidden-lg"><a href="#" ng-click="resizeScreen()"><i class="fa fa-arrows-alt"></i>Tela Inteira</a></li>
								<li class="hidden-lg"><a href="#" ng-click="selVendedor()"><i class="fa fa-retweet fa-lg"></i>  Trocar Vendedor</a></li>
								<li><a href="pedido_transferencia.php"><i class="fa fa-arrows-h fa-lg"></i> Transferência</a></li>
								<li ><a href="#" ng-click="showCadastroRapido()"><i class="fa fa-users"></i> Novo Cliente</a></li>
								<li><a href="#" ng-click="modalComandas()"><i class="fa fa-table"></i> Comandas</a></li>
								<li><a href="#" ng-click="modalFechamentosCaixa()"><i class="fa fa-file-text-o"></i> Fechamentos de Caixa</a></li>
								<li><a href="#" ng-click="modalReforco()"><i class="fa fa-download"></i> Incluir Reforço</a></li>
								<li><a href="#" ng-click="modalSangria()"><i class="fa fa-upload"></i> Efetuar Sangria</a></li>
								<li><a href="#" ng-click="modalFechar()"><i class="fa fa-sign-out"></i> Fechar Caixa</a></li>
							</ul>
						</div>
					</div>

					<div class="panel-body" ng-if="receber_pagamento">
						<div class="alert alert-pagamento" style="display: none;"></div>
				    	<div class="row">
				    		<div class="col-sm-7">
					    		<div class="row">
					    			<div class="col-sm-12">
					    				<div class="form-group">
											<div style="float: right; font-weight: bold;font-size: 15px;" ng-if="cliente.vlr_limite_credito">
												<span style="color:#000"> / Limite de Crédito :</span> <span style="color:blue">R$ {{ cliente.vlr_limite_credito | numberFormat:2:',':'.' }}</span>
											</div>
											<div style="font-weight: bold;font-size: 15px;" ng-if="cliente.vlr_saldo_devedor>0">
												<span style="color:#000">Saldo Devedor :</span> <span style="color:green">R$ {{ cliente.vlr_saldo_devedor | numberFormat:2:',':'.' }} </span>
											</div>
											<div style="font-weight: bold;font-size: 15px;" ng-if="cliente.vlr_saldo_devedor<0">
												<span style="color:#000">Saldo Devedor :</span> <span style="color:red">R$ {{ cliente.vlr_saldo_devedor | numberFormat:2:',':'.' }} </span>
											</div>
											<div style="font-weight: bold;font-size: 15px;" ng-if="cliente.vlr_saldo_devedor==0">
												<span style="color:#000">Saldo Devedor :</span> <span style="color:blue">R$ {{ cliente.vlr_saldo_devedor | numberFormat:2:',':'.' }} </span>
											</div>
										</div>
									</div>
				    			</div>

			    				<div class="row" ng-repeat="(key,formas) in formas_pagamento">
					    			<div class="col-sm-3" ng-repeat="forma in formas" >
					    				<a id="btn-logar" class="bounceIn btn btn-block btn-default btn-sm" ng-class="{'active':frmPagIsSel(forma.id)}"  data-loading-text=" Aguarde..."
					    					ng-click="selectChange(forma)"
					    					style="word-wrap: break-word; white-space: inherit; padding-left: 5px; padding-right: 5px; min-height: 92px; margin-bottom: 10px;">
					    					<i class="fa fa-3x {{ forma.icon }}" style="margin-bottom: 5px; margin-top: 5px; "></i>
					    					<span style="" class="clearfix">{{ forma.descricao_forma_pagamento }}</span>
				    					</a>
					    			</div>
				    			</div>

						    	<div class="row">
						    		<!--<div class="col-sm-6" id="pagamento_forma_pagamento">
						    			<label class="control-label">Forma de Pagamento</label>
										<select ng-model="pagamento.id_forma_pagamento" ng-change="selectChange()" class="form-control input-sm">
											<option ng-if="pagamento.id_forma_pagamento != null" value=""></option>
											<option ng-repeat="item in formas_pagamento"  value="{{ item.id }}">{{ item.descricao_forma_pagamento }}</option>
										</select>
									</div>-->
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

						    		<div class="col-sm-6" id="pagamento_valor" ng-if="pagamento.id_forma_pagamento != 7 && pagamento.id_forma_pagamento != 8 && pagamento.id_forma_pagamento != 10 && pagamento.id_forma_pagamento != 4">
						    			<label class="control-label">Valor</label>
						    			<div class="form-group ">
					    					<input type="text" class="form-control input-sm" thousands-formatter
					    						ng-model="pagamento.valor"
					    						ng-disabled="pagamento.id_forma_pagamento == 2 || pagamento.id_forma_pagamento == 9"/>
						    			</div>
						    		</div>

						    		<div class="col-sm-6" id="pagamento_valor" ng-if="pagamento.id_forma_pagamento == 4">
						    			<label class="control-label">Valor</label>
						    			<div class="form-group ">
					    					<input type="text" class="form-control input-sm" thousands-formatter
					    						ng-model="pagamento.valor"
					    						ng-blur="pushCheques()" 
					    						ng-disabled="pagamento.id_forma_pagamento == 2 || pagamento.id_forma_pagamento == 9"/>
						    			</div>
						    		</div>


						    		<div class="col-sm-3" ng-if="pagamento.id_forma_pagamento == 4" >
										<div class="form-group element-group">
											<label class="control-label">Periodicidade</label>
											<select class="form-control" ng-model="pagamento.periodicidade" ng-change="pushCheques()">
												<option value="1">Mensal</option>
												<option value="2">Bimestral</option>
												<option value="3">Trimestral</option>
												<option value="4">Quadrimestral</option>
												<option value="6">Semestral</option>
												<option value="12">Anual</option>
											</select>
										</div>
									</div>

						    		<div class="col-sm-3" id="numero_parcelas" 
						    			ng-if="pagamento.id_forma_pagamento == 2 || pagamento.id_forma_pagamento == 9 || pagamento.id_forma_pagamento == 4">
						    			<label class="control-label">Parcelas</label>
						    			<div class="form-group ">
					    					<input type="text" class="form-control input-sm"
					    						ng-blur="pushCheques()" 
					    						ng-focus="qtdCheque()" 
					    						ng-model="pagamento.parcelas">
						    			</div>
						    		</div>
						    	</div>

						    	<div class="row" id="pagamento_valor" ng-if="pagamento.id_forma_pagamento == 10">
						    		<div class="col-sm-4">
						    			<label class="control-label">Valor</label>
						    			<div class="form-group ">
						    				<input ng-model="pagamento.valor" thousands-formatter type="text" class="form-control input-sm" />
						    			</div>
						    		</div>
						    		<div class="col-sm-4" id="pagamento_maquineta">
						    			<label class="control-label">Maquineta</label>
										<select ng-model="pagamento.id_maquineta" class="form-control input-sm">
											<option ng-repeat="item in maquinetas" value="{{ item.id_maquineta }}">{{ item.num_serie_maquineta }} - {{ item.dsc_conta_bancaria }}</option>
										</select>
									</div>
						    		<div class="col-sm-4" id="bandeiras">
						    			<label class="control-label">Bandeira</label>
										<select ng-model="pagamento.id_bandeira" class="form-control input-sm">
											<option ng-repeat="item in bandeiras" value="{{ item.id }}">{{ item.nome }}</option>
										</select>
									</div>
						    	</div>

						    	<div class="row">
						    		<div class="col-sm-6" id="pagamento_maquineta" ng-if="pagamento.id_forma_pagamento == 5 || pagamento.id_forma_pagamento == 6 ">
						    			<label class="control-label">Maquineta</label>
										<select ng-model="pagamento.id_maquineta" class="form-control input-sm">
											<option ng-repeat="item in maquinetas" value="{{ item.id_maquineta }}">{{ item.num_serie_maquineta }} - {{ item.dsc_conta_bancaria }}</option>
										</select>
									</div>
									<div class="col-sm-6" id="bandeiras" ng-if="pagamento.id_forma_pagamento == 5 || pagamento.id_forma_pagamento == 6 ">
						    			<label class="control-label">Bandeira</label>
										<select ng-model="pagamento.id_bandeira" class="form-control input-sm">
											<option ng-repeat="item in bandeiras" value="{{ item.id }}">{{ item.nome }}</option>
										</select>
									</div>
						    		<div class="col-sm-6" id="numero_parcelas" ng-if="pagamento.id_forma_pagamento == 6">
						    			<label class="control-label">Parcelas</label>
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
											<label class="control-label">Data :</label>
											<div class="input-group">
												<input  ng-model="item.data_pagamento" date-picker style="background:#FFF;cursor:pointer"  type="text" id="pagamentoData" class=" form-control chequeData input-cheque-date-{{$index}}">
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
												<input readonly="readonly" style="background:#FFF;cursor:pointer" date-init="{{ item.date_init }}"  type="text" id="pagamentoData" class="datepicker form-control boletoData">
												<span class="input-group-addon" class="cld_pagameto" ng-click="focusData($index)"><i class="fa fa-calendar"></i></span>
											</div>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group boleto_valor">
											<label class="control-label">Valor</label>
											<div class="form-group ">
					    						<input ng-keyUp="calTotalBoleto()"  thousands-formatter ng-model="item.valor_pagamento" type="text" class="form-control" >
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

								<div class="row" ng-show="pagamento.id_forma_pagamento == 9" ng-repeat="item in promessas_pagamento">
									<div class="col-sm-3">
										<div class="form-group cheque_data">
											<label class="control-label">Data :</label>
											<div class="input-group">
												<input  ng-model="item.data_pagamento" date-picker style="background:#FFF;cursor:pointer"  type="text" class=" form-control">
												<span class="input-group-addon" class="" ng-click="focusData($index)"><i class="fa fa-calendar"></i></span>
											</div>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group cheque_valor">
											<label class="control-label">Valor</label>
											<div class="form-group ">
					    						<input  ng-keyUp="calTotalPromessa()" thousands-formatter ng-model="item.valor_pagamento" type="text" class="form-control" >
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
							<div class="col-sm-5">
					    		<div class="row">
					    			<div class="col-sm-12" id="col-sm-auto-complete-cliente">
										<div class="form-group">
											<label class="control-label"><h4>Cliente <span><button style="cursor:auto;height: 18px;padding-top: 0;" class="btn btn-xs btn-success" type="button" ng-if="isNumeric(cliente.id) && !esconder_cliente">{{ getIdentificadorCliente() }} <i style="cursor:pointer;" ng-click="removeCliente()" class="fa fa-times fa-lg fa-danger"></i></button></h4></label>
											<div class="input-group">
												<input id="input_auto_complete_cliente" type="text" class="form-control" 
													onKeyPress="return SomenteNumeroLetras(event);"
													ng-focus="outoCompleteCliente(busca.cliente_outo_complete,$event,false)" 
													ng-keyUp="outoCompleteCliente(busca.cliente_outo_complete)"
													ng-model="busca.cliente_outo_complete"/>
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
													<button ng-click="selCliente(0,10)"  type="button" class="btn btn-info"><i class="fa fa-users"></i></button>
												</span>
											</div>
										</div>
									</div>
					    		</div>

					    		<div class="row" ng-show="!pagamento_fulso">
					    			<div class="col-lg-4">
					    				<div class="form-group cheque_data">
											<label class="control-label">Data do Lançamento:</label>
											<div class="input-group">
												<input type="text" id="dta_venda" class="form-control" date-picker
													style="background: #FFF; cursor: pointer;"
													ng-model="data_venda">
												<span class="input-group-addon" ng-click="focusData($index)">
													<i class="fa fa-calendar"></i>
												</span>
											</div>
										</div>
					    			</div>
					    			<div class="col-sm-8">
										<div class="form-group">
											<label class="control-label">Observação:</label>
											<textarea id="dsc_observacoes_gerais" class="form-control" ng-model="dados_venda.dsc_observacoes_gerais" rows="2"></textarea>
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
											<td ng-if="not_in(item.id_forma_pagamento,'6,2,9')">{{ item.forma_pagamento  }} <strong class="pull-right">R$ {{ item.valor | numberFormat:2:',':'.' }}</strong></td>
											<td ng-if="item.id_forma_pagamento == 6">C/C em {{item.parcelas}}x <strong class="pull-right">R$ {{ item.valor | numberFormat:2:',':'.' }}</strong></td>
											<td ng-if="item.id_forma_pagamento == 2">Cheque em {{ cheques.length }}x <strong class="pull-right">R$ {{ item.valor | numberFormat:2:',':'.' }}</strong></td>
											<td ng-if="item.id_forma_pagamento == 9">Promessa pag. em {{ item.parcelas }}x <strong class="pull-right">R$ {{ item.valor | numberFormat:2:',':'.' }}</strong></td>
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
										<tr ng-if="(dadosOrcamento.qtd_pessoas > 0 && configuracoes.controlar_quantidade_pessoas == 1)">
											<td colspan="2">
												Total por pessoa <strong class="pull-right">R$ {{ vlrTotalCompra / dadosOrcamento.qtd_pessoas | numberFormat:2:',':'.' }}</strong>
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

					<div id="painel-principal" class="panel-body" ng-if="receber_pagamento == false">

						<form role="form" ng-if="venda_aberta == false" class="panel-menor" >
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label class="control-label"><h4>Cliente</h4></label>
										<div class="input-group">
											<input ng-click="selCliente(0,10)"    type="text" class="form-control" value="{{ getIdentificadorCliente() }}" readonly="readonly" style="cursor: pointer;" />
											<span class="input-group-btn">
												<button ng-click="selCliente(0,10)" type="button"  class="btn btn-info"><i class="fa fa-users"></i></button>
											</span>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<button type="button" 
										class="btn btn-lg btn-block btn-success" 
										data-loading-text=" Aguarde..."
										ng-click="abrirVenda('pdv', false)">
										Nova Venda (Modo Loja)
									</button>
									<button type="button" 
										class="btn btn-lg btn-block btn-default" 
										data-loading-text=" Aguarde..."
										ng-click="abrirVenda('est', false)">
										Nova Venda (Modo Depósito)
									</button>
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
								<div class="col-sm-2">
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
												<img pre-load-img="imgProduto" src="{{ imgProduto }}" imgpreload="img/imagem_padrao_produto.gif" notimg="img/imagem_padrao_produto.gif" style="max-height: 50%;" ng-click="abreModalFotoProduto(produto_selecionado)">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-12">
											<div class="form-group">
												<label class="control-label"><i class="fa fa-barcode"></i> <span class="hidden-xs hidden-sm hidden-md">Pesquisa por </span>Código Barras</label>
												<div class="input-group">
													<input id="buscaCodigo" type="text" class="form-control input-sm" ng-model="busca.codigo" sync-focus-with="!busca.ok" ng-enter="findProductByBarCode();">
													<span class="input-group-btn">
														<button type="button" class="btn btn-sm btn-primary" ng-click="findProductByBarCode();"><i class="fa fa-search"></i></button>
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

								<div class="col-sm-10" style="padding-left: 0px;">
									<div class="row">
										<div class="col-sm-2" id="col-sm-auto-complete-cliente">
											<div class="form-group">
												<label class="control-label"><h4>Cliente <span> <button  style="cursor:auto;height: 18px;padding-top: 0;
" class="btn btn-xs btn-success" type="button" ng-if="isNumeric(cliente.id) && !esconder_cliente">{{ getIdentificadorCliente() }} <i style="cursor:pointer;" ng-click="removeCliente()" class="fa fa-times fa-lg fa-danger"></i></button></h4></label>
												<div class="input-group">
													<input id="input_auto_complete_cliente" 
														type="text" class="form-control" 
														onKeyPress="return SomenteNumeroLetras(event);" 
														ng-focus="outoCompleteCliente(busca.cliente_outo_complete,$event)" 
														ng-keyUp="outoCompleteCliente(busca.cliente_outo_complete)" 
														ng-model="busca.cliente_outo_complete"
														ng-click="openModalSelCliente()"
														ng-readonly="(!configuracoes.flg_ativar_auto_complete_clientes)"/>
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
														<button ng-click="selCliente(0,10)"  type="button" class="btn btn-info"><i class="fa fa-users"></i></button>
													</span>
												</div>
											</div>
										</div>

										<div class="col-sm-10" id="col-sm-auto-complete-produto">
											<div class="form-group">
												<label class="control-label"><h4>Produto</h4></label>
												<div class="input-group">
													<input id="input_auto_complete_produto" 
														type="text" class="form-control" 
														ng-focus="outoCompleteProduto(busca.produto_outo_complete,$event)" 
														ng-keyUp="outoCompleteProduto(busca.produto_outo_complete)" 
														ng-model="busca.produto_outo_complete"
														ng-click="openModalSelProduto()"
														ng-readonly="(!configuracoes.flg_ativar_auto_complete_produtos)"/>
													<div class="content-outo-complete-produto-pdv" ng-show="produtos_auto_complete.length > 0 && produtos_auto_complete_visible">
														<table class="table table-striped itens-outo-complete">
														<thead>
																<tr>
																	<th width="80" >ID</th>
																	<th width="80" >Código Barra</th>
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
														<button ng-click="findProductByBarCode()"  type="button" class="btn btn-primary"><i class="fa fa fa-archive"></i></button>
													</span>
												</div>
											</div>
										</div>
										
									</div>

									<div class="row" ng-if="out_produtos.length > 0">
										<div class="col-sm-12">
											<div class="alert alert-out alert-warning">
												Desculpe, os produtos marcados em <span style="color:#FF9191">vermelho</span>,
											    não estão mais disponível em nosso estoque, para continuar a venda basta
											    retira-los do carrinho.
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-6">
											<div class="col-xs-1 no-padding">
												<button ng-click="showVlrReal()" class="btn btn-xs btn-success" type="button" ng-if="funcioalidadeAuthorized('ver_valor_custo_produto')" tooltip data-original-title="Mostrar Custo">
													<i ng-if="show_vlr_real == false" class="fa fa-eye fa-lg"></i>
													<i ng-if="show_vlr_real == true" class="fa fa-eye-slash fa-lg"></i>
												</button>
											</div>

											<div class="col-xs-1 no-padding">
												<button  ng-click="showAditionalColumns()" class="btn btn-xs btn-default" type="button" tooltip title="Ordenar">
													<i ng-if="show_aditional_columns == true" class="fa fa-th-list fa-lg"></i>
													<i ng-if="show_aditional_columns == false" class="fa fa-align-justify fa-lg"></i>
												</button>
											</div>
											
											<div class="col-xs-1 no-padding">
												<button  class="btn btn-xs btn-default" id="pop-over-desconto-venda" 
													tooltip title="Desconto na venda inteira"  type="button" 
													init-popover placement="bottom" 
													content='
														<div class="input-group">
									            			<input ng-model="descontoAllItens.valor"  placeholder="R$" thousands-formatter  type="text" class="form-control input-sm" ng-enter="DesAllVenda(descontoAllItens.valor)">
												            <div class="input-group-btn">
												            	<button ng-click="DesAllVenda(descontoAllItens.valor,descontoAllItens.vlr)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
												            		Aplicar
												            	</button>
												            </div> 
												        </div> 
												        <br/>
												        <div class="input-group">
									            			<input ng-model="descontoAllItens.porcentagem"  placeholder="%" thousands-formatter  type="text" class="form-control input-sm" ng-enter="DesAllVenda(descontoAllItens.porcentagem)">
												            <div class="input-group-btn">
												            	<button ng-click="DesAllVenda(descontoAllItens.porcentagem,descontoAllItens.per)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
												            		Aplicar
												            	</button>
												            </div> 
												        </div> '
													>
													<i class="fa fa-minus-square-o fa-lg fa-align-justify"></i>
												</button>
											</div>
											
											<div class="col-xs-1 no-padding">
												<button  class="btn btn-xs btn-default" id="popover-mudar-margem" 
													tooltip title="Selecione a margem" type="button" 
													init-popover placement="bottom" 
													content='
														<div class="row">
															<div class="col-sm-12">
																<button 
																ng-if="existeTabelaPreco({atacado:true})" ng-click="changeMargemAplicada({atacado:true,intermediario:false,varejo:false,parceiro:false})" class="btn btn-sm btn-primary btn-block " type="button">
																	<i ng-if="margemAplicada.atacado" class="fa fa-check-circle-o" aria-hidden="true"></i>
																	Atacado
																</button>
															</div>
														</div>
														<div class="row" style="margin-top:5px">
															<div class="col-sm-12">
																<button 
																ng-if="existeTabelaPreco({intermediario:true})" ng-click="changeMargemAplicada({atacado:false,intermediario:true,varejo:false,parceiro:false})" class="btn btn-sm btn-primary btn-block" type="button">
																	<i ng-if="margemAplicada.intermediario" class="fa fa-check-circle-o" aria-hidden="true"></i>
																	Intermediario
																</button>
															</div>
														</div>
														<div class="row" style="margin-top:5px">
															<div class="col-sm-12">
																<button 
																ng-if="existeTabelaPreco({intermediario_ii:true})" ng-click="changeMargemAplicada({atacado:false,intermediario_ii:true,varejo:false,parceiro:false})" class="btn btn-sm btn-primary btn-block" type="button">
																	<i ng-if="margemAplicada.intermediario_ii" class="fa fa-check-circle-o" aria-hidden="true"></i>
																	Intermediario II
																</button>
															</div>
														</div>
														<div class="row" style="margin-top:5px">
															<div class="col-sm-12">
																<button 
																ng-if="existeTabelaPreco({varejo:true})" ng-click="changeMargemAplicada({atacado:false,intermediario:false,varejo:true,parceiro:false})" class="btn btn-sm btn-primary btn-block" type="button">
																	<i ng-if="margemAplicada.varejo" class="fa fa-check-circle-o" aria-hidden="true"></i>
																	Varejo
																</button>
															</div>
														</div>
														<div class="row" style="margin-top:5px" ng-show="funcioalidadeAuthorized(35)">
															<div class="col-sm-12">
																<button ng-click="changeMargemAplicada({atacado:false,intermediario:false,varejo:false,parceiro:true})" class="btn btn-sm btn-primary btn-block" type="button">
																	<i ng-if="margemAplicada.parceiro"  class="fa fa-check-circle-o" aria-hidden="true"></i> 
																	Parceiro
																</button>
															</div>
														</div>'
													>
													<i class="fa fa-usd" aria-hidden="true"></i>
												</button>
											</div>

											<div class="col-xs-5 no-padding" style="padding-top: 2px;">
												<label class="label-checkbox">
													<input type="checkbox" id="toggleLine"
														ng-model="is_venda_bonificada" 
														ng-click="setVendaBonificada(is_venda_bonificada)"
														ng-true-value="true" ng-false-value="false" />
													<span class="custom-checkbox"></span> Venda Bonificada
												</label>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-12 text-right">
											<span ng-if="cliente.vlr_saldo_devedor > 0" style="color:#000">Saldo Devedor :</span> 
											<span ng-if="cliente.vlr_saldo_devedor > 0" style="color:green">R$ {{ cliente.vlr_saldo_devedor | numberFormat:2:',':'.' }}</span>
											
											<span ng-if="cliente.vlr_saldo_devedor < 0" style="color:#000">Saldo Devedor :</span> 
											<span ng-if="cliente.vlr_saldo_devedor < 0" style="color:red">R$ {{ cliente.vlr_saldo_devedor | numberFormat:2:',':'.' }}</span>
											
											<span ng-if="cliente.vlr_saldo_devedor == 0" style="color:#000">Saldo Devedor :</span> 
											<span ng-if="cliente.vlr_saldo_devedor == 0" style="color:blue">R$ {{ cliente.vlr_saldo_devedor | numberFormat:2:',':'.' }}</span>
											
											<span ng-if="cliente.vlr_limite_credito" style="color:#000">/ Limite de Crédito :</span> 
											<span ng-if="cliente.vlr_limite_credito" style="color:blue">R$ {{ cliente.vlr_limite_credito | numberFormat:2:',':'.' }}</span>
											
											<span ng-if="(cliente.vlr_limite_credito) && (cliente.vlr_saldo_devedor)" style="color:#000">/ Saldo Final:</span> 
											<span ng-if="(cliente.vlr_limite_credito) && (cliente.vlr_saldo_devedor)" style="color:#000">R$ {{ cliente.vlr_saldo_devedor + cliente.vlr_limite_credito | numberFormat:2:',':'.' }}</span>
										</div>
									</div>
									<br/>
									<div class="row">
									
										<div class="col-sm-12">
											<div class="form-group">
												<table id="tbl_carrinho" 
													class="table table-condensed table-bordered table-hover table-striped">
													<tr ng-hide="carrinho.length > 0" class="hidden-print">
														<td colspan="4">
															Carrinho de compras vazio
														</td>
													</tr>
													<thead ng-show="carrinho.length  > 0">
														<tr>
															<th>Produto</th>
															<th ng-show="show_aditional_columns">Fabricante</th>
															<th ng-show="show_aditional_columns">Tamanho</th>
															<th ng-show="show_aditional_columns">Sabor/Cor</th>
															<th class="text-center" style="width: 100px;" ng-if="existeProdutoValidade()" >Validade</th>
															<th class="text-center" style="width: 80px;" >Qtd</th>
															<th class="text-center" style="width: 100px;" ng-if="show_vlr_real" >RV</th>
															<th class="text-center" style="width: 100px;">Valor Unit.</th>
															<th class="text-center" style="width: 230px;" colspan="3"
																ng-if="!(is_venda_bonificada)">Desconto</th>
															<th class="text-center" style="width: 100px;">Vlr. c/ Desc.</th>
															<th class="text-center" style="width: 100px;">Subtotal</th>
															<th class="text-center" style="width: 20px;" class="hidden-print"></ul>
															</th>
														</tr>
													</thead>
													<tbody>
														<tr ng-repeat="item in carrinho" id="{{ getIdprodutoCarrinho(item) }}" 
															ng-class="{'error-estoque': verificaOutEstoque(item) }"
															ng-click="showFotoProduto(item)">
															<td>{{ item.nome_produto }}</td>
															<td ng-show="show_aditional_columns">{{ item.nome_fabricante }}</td>
															<td ng-show="show_aditional_columns">{{ item.peso }}</td>
															<td ng-show="show_aditional_columns">{{ item.sabor }}</td>
															<td class="text-center"  ng-if="existeProdutoValidade()" >{{ item.flg_controlar_validade == 1 && (item.validade | dateFormat:'date') || '' }}</td>
															<td class="text-center" width="80">
																<input type="text" 
																	class="form-control text-center input-xs"
																	onKeyPress="return SomenteNumero(event);"
																	ng-keyUp="calcSubTotal(item)"  
																	ng-model="item.qtd_total"
																	ng-if="item.flg_unidade_fracao != 1"/>
																<input type="text" 
																	class="form-control text-center input-xs"
																	onKeyPress="return SomenteNumero(event);"
																	thousands-formatter precision="3" 
																	ng-keyUp="calcSubTotal(item)"  
																	ng-model="item.qtd_total"
																	ng-if="item.flg_unidade_fracao == 1"/>
															</td>
															<td class="text-center" ng-if="show_vlr_real" >R${{ item.vlr_custo_real | numberFormat : configuracoes.qtd_casas_decimais : ',' : '.' }}</td>
															<td class="text-right">R$ {{ item.vlr_real | numberFormat : configuracoes.qtd_casas_decimais : ',' : '.' }}</td>
															<td class="text-center" style="width: 30px;"
																ng-if="!(is_venda_bonificada)">
																<!-- <label class="label-checkbox" id="label-checkbox-{{ $index }}">
																	<input type="checkbox" id="toggleLine-{{ $index }}"
																		ng-model="item.flg_desconto" 
																		ng-true-value="1" ng-false-value="0" 
																		ng-click="aplicarDesconto($index,$event)" />
																	<span class="custom-checkbox" id="custom-checkbox-{{ $index }}"></span>
																</label> -->

																<input type="checkbox" class="checkbox-old" id="toggleLine-{{ $index }}"
																	ng-model="item.flg_desconto" 
																	ng-true-value="1" ng-false-value="0" 
																	ng-click="aplicarDesconto($index,$event)" />
															</td>
															<td class="text-right" style="width:100px;"
																ng-if="!(is_venda_bonificada)">
																<span style="display: block;float: left;" ng-if="item.flg_desconto == 1">%</span>
																<input style="width:80%;float:right"  ng-keyUp="aplicarDesconto($index,$event,false,false)" ng-if="item.flg_desconto == 1" thousands-formatter ng-model="item.valor_desconto" type="text" class="form-control text-right input-xs" />
															</td>
															<td class="text-right" style="width:100px;"
																ng-if="!(is_venda_bonificada)">
																<span style="display: block;float: left;" ng-if="item.flg_desconto == 1">R$</span>
																<input style="width:80%;float:right"  ng-keyUp="aplicarDesconto($index,$event,false,true)" ng-if="item.flg_desconto == 1" thousands-formatter ng-model="item.valor_desconto_real" type="text" class="form-control text-right input-xs" id="teste_teste" />
															</td>
															<td class="text-right">R$ {{ item.vlr_unitario    | numberFormat : configuracoes.qtd_casas_decimais : ',' : '.' }}</td>
															<td class="text-right">R$ {{ item.sub_total    | numberFormat : configuracoes.qtd_casas_decimais : ',' : '.' }}</td>
															<td class="text-center" class="hidden-print">
																<button type="button" class="btn btn-xs btn-danger"  ng-click="delItem($index)"><i class="fa fa-trash-o"></i></button>
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
													<h2 class="no-margin">
														{{ total_itens }} Itens
														-
														R$ {{ vlrTotalCompra | numberFormat : configuracoes.qtd_casas_decimais : ',' : '.' }}
													</h2>
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
							<button type="button" class="btn btn-lg btn-success" ng-if="receber_pagamento || modo_venda == 'est'" data-loading-text=" Aguarde..." id="btn-fazer-compra" ng-click="salvar()" ng-disabled="(modo_venda == null && total_pg == 0) || (modo_venda == 'pdv' && !dadosOrcamento.flg_comanda == 1 && (total_pg == 0 || total_pg < vlrTotalCompra )) || (modo_venda == 'pdv' && total_pg == 0) || (modo_venda == 'est' && (carrinho.length <= 0))"><i class="fa fa-save"></i> Finalizar</button>
							<button type="button" class="btn btn-lg btn-primary" ng-if="receber_pagamento == false" ng-disabled="carrinho.length == 0" ng-click="receberPagamento()"><i class="fa fa-money"></i> Receber</button>
							<button  type="button" class="btn btn-lg btn-success" ng-show="receber_pagamento == false && modo_venda == 'est'" data-loading-text=" Aguarde..." id="btn-fazer-orcamento" ng-click="salvarOrcamento()" ng-disabled="carrinho.length <= 0 || !isNumeric(cliente.id)"><i class="fa fa-save"></i> Orçamento</button>

							<button  type="button" class="btn btn-lg btn-success" ng-show="receber_pagamento == false &&(funcioalidadeAuthorized('fazer_orcamento') &&  modo_venda != 'est')" data-loading-text=" Aguarde..." id="btn-fazer-orcamento" ng-click="salvarOrcamento()" ng-disabled="carrinho.length <= 0 || !isNumeric(cliente.id)"><i class="fa fa-save"></i> Orçamento</button>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /main-container -->

		<div class="modal fade" id="modal-lembrete-troca-vendedor" style="display:none">
  			<div class="modal-dialog error modal-md">
    			<div class="modal-content">
      				<div class="modal-header">
						<h4>Lembrete</h4>
      				</div>

				    <div class="modal-body">
				    	<div class="row">
				    		<div class="col-sm-12" id="valor_retirada_sangria">
				    			Deseja trocar de vendedor ? 
				    		</div>
				    	</div>
				    </div>

				    <div class="modal-footer">
				    	<button type="button" class="btn btn-md btn-success" ng-click="selVendedor(true)">
				    		<i class="fa fa-times-circle"></i> SIM
				    	</button>
				    	<button type="button"  class="btn btn-md btn-default" id="btn-aplicar-sangria" ng-click="fecharModalLembreteTrocaVendedor()">
				    		<i class="fa fa-minus-circle"></i> Não
				    	</button>
				    </div>
			  	</div>
			  	<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>


















		<!-- Modal Pagamento  -->
		<div class="modal fade" id="modal-receber" style="display:none">
  			<div class="modal-dialog error modal-xl">
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
						    			<label class="control-label">Parcelas</label>
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
											<td ng-if="not_in(item.id_forma_pagamento,'6,9,4,2')">{{ item.forma_pagamento  }} <strong class="pull-right">R$ {{ item.valor | numberFormat:2:',':'.' }}</strong></td>
											<td ng-if="_in(item.id_forma_pagamento,'6,9,4,2')">{{ item.forma_pagamento  }} em {{item.parcelas}}x <strong class="pull-right">R$ {{ item.valor | numberFormat:2:',':'.' }}</strong></td>
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
										<tr ng-if="(dadosOrcamento.qtd_pessoas > 0 && configuracoes.controlar_quantidade_pessoas == 1)">
											<td colspan="2">
												Total por pessoa <strong class="pull-right">R$ {{ vlrTotalCompra / dadosOrcamento.qtd_pessoas | numberFormat:2:',':'.' }}</strong>
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
		  			<div class="modal-dialog error modal-md">
		    			<div class="modal-content">
		      				<div class="modal-header">
								<h4>Reforço</h4>
		      				</div>

						    <div class="modal-body">
						    	<div class="alert alert-reforco" style="display:none"></div>

						    	<div class="row">
						    		<div class="col-sm-6" id="reforco_valor_pagamento">
						    			<label class="control-label">Valor</label>
						    			<div class="form-group">
						    				<input ng-model="reforco.valor" thousands-formatter type="text"  class="form-control input-sm" >
						    			</div>
						    		</div>
						    		<div class="col-sm-6" id="reforco_conta_origem">
						    			<label class="control-label">Conta de origem</label>
							    		<select class="form-control input-sm" ng-model="reforco.conta_origem">
											<option ng-repeat="item in contas" value="{{ item.id }}">{{ item.dsc_conta_bancaria }}</option>
										</select>
									</div>
						    	</div>
						    	<div class="row">
						    		<div class="col-sm-12">
										<div class="form-group" id="reforco_id_plano_conta">
											<label class="ccontrol-label">Plano de conta </label> 
											<select chosen ng-change="ClearChosenSelect('cod_regime_tributario')"
										    option="plano_contas"
										    ng-model="reforco.id_plano_conta"
										    ng-options="plano.id as plano.dsc_completa for plano in plano_contas">
											</select>
										</div>
									</div>
						    	</div>
						    	<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
										  <label for="comment">Observação:</label>
										  <textarea class="form-control" rows="3" ng-model="reforco.obs_pagamento" id="comment"></textarea>
										</div>
									</div>
								</div>
						    </div>

						    <div class="modal-footer">
						    	<button type="button" data-loading-text=" Aguarde..." ng-click="cancelarModal('modal-reforco')"
						    		class="btn btn-md  btn-default fechar-modal">
						    		<i class="fa fa-times-circle"></i> Cancelar
						    	</button>
						    	<button type="button" data-loading-text=" Aguarde..." id="btn-aplicar-reforco"
						    		class="btn btn-md  btn-success" ng-click="aplicarReforco()">
						    		<i class="fa fa-plus-circle"></i> Aplicar reforço
						    	</button>
						    </div>
					  	</div>
					  	<!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
				</div>
				<!-- /.modal -->

				<!-- /Modal de Fechamento de caixa-->
				<div class="modal fade" id="modal-fechamento" style="display:none" data-backdrop="static" data-keyboard="false">
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
													<td class="text-center"><strong>R$ {{ item.total | numberFormat:2:',':'.' }}</strong></td>
												</tr>
												<tr ng-show="!lacamentos_formas_pagamento">
													<td colspan="2" class="text-center">
														<i class="fa fa-spin fa-spinner"></i> Aguarde, carregando...
													</td>
												</tr>
											</tbody>
										</table>
						    		</div>
					    		</div>

					    		<div class="row" ng-show="lacamentos_formas_pagamento">
						    		<div class="col-sm-12" id="conta_destino">
						    			<label class="control-label text-center">Conta de destino dos valores em dinheiro</label>
							    		<select class="form-control input-sm" ng-model="fechamento.id_conta_bancaria">
											<option ng-repeat="item in contas" value="{{ item.id }}">{{ item.dsc_conta_bancaria }}</option>
										</select>
									</div>
						    	</div>

						    	<div class="row" style="margin-top: 10px;" ng-show="lacamentos_formas_pagamento">
						    		<div class="col-sm-12">
						    			<label class="label-checkbox">
											<input type="checkbox" 
												ng-true-value="true" ng-false-value="false" 
												ng-model="print_report_thermal_printer" 
												ng-disabled="!enable_print_report_thermal_printer"/>
											<span class="custom-checkbox"></span> Fechamento na Impressora Térmica
										</label>
									</div>
						    	</div>

								<div class="row" style="margin-top: 10px;" ng-show="print_report_thermal_printer">
									<div class="col-sm-12">
										<label class="label-checkbox">
											<input type="checkbox"
												   ng-true-value="true" ng-false-value="false"
												   ng-model="complete_report_thermal_printer"/>
											<span class="custom-checkbox"></span> Incluir Movimentações
										</label>
									</div>
								</div>
						    </div>

						    <div class="modal-footer" ng-show="lacamentos_formas_pagamento">
						  		<button type="button"  data-loading-text=" Aguarde..." id="btn-fechar-caixa"
			    					class="btn btn-md btn-success btn-block" ng-click="fecharPDV()">
			    					<i class="fa fa-lock"></i> Fechar PDV
			    				</button>

			    				<button id="btn-fechar-caixa" type="button" 
			    					class="btn btn-md btn-default btn-block fechar-modal"
			    					data-loading-ftext=" Aguarde..." 
			    					ng-click="cancelarModal('modal-fechamento')"
			    					ng-if="show_cancel_button_fechamento_caixa">
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
  			<div class="modal-dialog error modal-md">
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
				    	<div class="row">
				    		<div class="col-sm-12">
								<div class="form-group" id="sangria_id_plano_conta">
									<label class="ccontrol-label">Favorecidos</label> 
									<select chosen ng-change="ClearChosenSelect('cod_regime_tributario')"
								    option="favorecidos"
								    ng-model="sangria.id_fornecedor"
								    ng-options="favorecido.id as favorecido.nome_fornecedor for favorecido in favorecidos">
									</select>
								</div>
							</div>
				    	</div>
				    	<div class="row">
				    		<div class="col-sm-12">
								<div class="form-group" id="sangria_id_plano_conta">
									<label class="ccontrol-label">Plano de conta </label> 
									<select chosen ng-change="ClearChosenSelect('cod_regime_tributario')"
								    option="plano_contas"
								    ng-model="sangria.id_plano_conta"
								    ng-options="fornecedor.id as fornecedor.dsc_completa for fornecedor in plano_contas">
									</select>
								</div>
							</div>
				    	</div>
				    	<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
								  <label for="comment">Observação:</label>
								  <textarea class="form-control" rows="3" ng-model="sangria.obs_pagamento" id="comment"></textarea>
								</div>
							</div>
						</div>
				    </div>

				    <div class="modal-footer">
				    	<button type="button" data-loading-text=" Aguarde..."
				    		class="btn btn-md btn-default" ng-click="cancelarModal('modal-sangria')">
				    		<i class="fa fa-times-circle"></i> Cancelar
				    	</button>
				    	<button type="button" data-loading-text=" Aguarde..." class="btn btn-md btn-success"
				    		id="btn-aplicar-sangria" ng-click="aplicarSangria()">
				    		<i class="fa fa-minus-circle"></i> Aplicar sangria
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
						            <input ng-model="busca.clientes" ng-keyup="loadCliente(0,10)" ng-enter="loadCliente(0,10)" type="text" class="form-control input-sm">
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
									<tr ng-if="clientes == null">
										<th class="text-center" colspan="9" style="text-align:center"><strong>Carregando</strong><img src="assets/imagens/progresso_venda.gif"></th>
									</tr>
									<tr ng-if="clientes.length == 0">
										<th colspan="4" class="text-center">Nenhum cliente encontrado</th>
									</tr>
									<tbody>
										<tr ng-repeat="item in clientes">
											<td class="text-middle" ng-if="!(item.nome == null || item.nome=='')">{{ item.nome | uppercase }}</td>
											<td class="text-middle" ng-if="(item.nome == null || item.nome=='')" ng-bind-html="item.cpf | cpfFormat:'<b>CPF:</b> '"></td>
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
											<th colspan="2">Selecionar</th>
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

		<div class="modal fade" id="modal_foto_produto" style="display:none; z-index: 2000;">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title">{{ produto_foto.nome_produto | uppercase }}</h4>
					</div>
					<div class="modal-body text-center">
						<img class="img-responsive" style="display: initial;"
							src="assets/imagens/produtos/{{produto_foto.img}}">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->

		<!-- /Modal Produtos-->
		<div class="modal fade" id="list_produtos" style="display:none">
  			<div class="modal-dialog modal-xl">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
        					<i class="fa fa-times-circle-o"></i> Fechar Janela
        				</button>
        				<h4 ng-if="mostrar_validades">Selecione a validade</span></h4>
						<h4 ng-if="cdb_busca.status==false && mostrar_validades == false">Pesquisa de Produtos</span></h4>
						<h4 ng-if="cdb_busca.status==true && mostrar_validades == false" style="margin-bottom: 0px;">Pesquisa de Produtos</span></h4>
						<span ng-if="cdb_busca.status==true && mostrar_validades == false" class="text-muted">Produtos relacionados ao codigo de barra {{ cdb_busca.codigo }}</span>
      				</div>

				    <div class="modal-body">
						<div class="row" ng-if="mostrar_validades == false">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.produtos" id="foco" ng-enter="loadProdutos(0,configuracoes.qtd_registros_pesquisa_produtos)" type="text" 
						            	class="form-control">

						            <div class="input-group-btn">
						            	<button type="button" class="btn btn-primary"
						            		ng-click="loadProdutos(0,configuracoes.qtd_registros_pesquisa_produtos)">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div> <!-- /input-group-btn -->
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>

						<br>

						<div class="row" ng-if="mostrar_validades">
							<div class="col-md-12 table-responsive">
								<div class="alert alert-produtos" style="display:none"></div>
									<table style="width: auto !important;margin: 0 auto;" class="table table-bordered table-striped table-hover">
										<thead>
											<tr>
												<td colspan="4" style="text-align: center">
													{{ produto_selecionar_validade.nome_produto }}	
												</td>
											</tr>
											<tr>
												<td style="text-align: center;width: 150px">
													Validade
												</td>
												<td style="text-align: center;width: 150px">
													Estoque
												</td>

												<td style="text-align: center;width: 100px">
													Qtd
												</td>
												<td style="text-align: center;width: 50px">
													
												</td>
											</tr>
											<tr ng-repeat="item in lista_produto_selecionar_validade">
												<td style="text-align: center">{{ item.dta_validade | dateFormat:'date' }}</td>
												<td style="text-align: center">{{ item.qtd_item }}</td>
												<td >
													<input type="text" class="form-control text-center no-padding" ng-model="item.qtd_total" style="font-size: 10px;" />
												</td>
												<td>
													<button type="button" class="btn btn-{{ !(isProdutoSelecionado(item)) ? 'success' : 'info'}} data-loading-text="Aguarde..." ng-click="addProduto(item, index)">
														<span>
																<span ng-if="!(isProdutoSelecionado(item)) && !((add_index === index && add_validade === item.dta_validade) && (loading_add_produto))">
															<i class="fa fa-check-square-o"></i>
														</span>
														<span ng-if="(isProdutoSelecionado(item)) && !((add_index === index && add_validade === item.dta_validade) && (loading_add_produto))">
															<i class="fa fa-refresh"></i>
														</span>
														<span ng-if="((add_index === index && add_validade === item.dta_validade) && (loading_add_produto))">
															<i class="fa fa-spinner fa-spin"></i>
														</span>
														</span>
													</button>
												</td>
											</tr>
										</thead>
									</table>
								</div>
							</div>

						<div class="row">
							<div class="col-md-12 table-responsive">
								<div class="alert alert-produtos" style="display:none"></div>

								<div class="table-responsive" ng-if="mostrar_validades == false">
							   		<table class="table table-bordered table-striped table-hover">
										<thead ng-show="(produtos.length != 0)">
											<tr>
												<th rowspan="2" style="line-height: 46px;" class="text-center"
													ng-if="exibeColunaPesquisaProduto('id_produto')">
													#
												</th>
												<th rowspan="2" style="line-height: 46px;" class="text-center" 
													ng-if="exibeColunaPesquisaProduto('foto_produto')">
													Foto
												</th>
												<th rowspan="2" style="min-width: 100px; line-height: 46px;" class="text-center"
													ng-if="exibeColunaPesquisaProduto('codigo_barra')">
													Cod. barra
												</th>
												<th rowspan="2" style="min-width: 180px; line-height: 46px;" class="text-center">
													Nome
												</th>
												<th rowspan="2" style="line-height: 46px;" class="text-center"
													ng-if="exibeColunaPesquisaProduto('nome_categoria')">
													Categoria
												</th>
												<th rowspan="2" style="line-height: 46px;" class="text-center"
													ng-if="exibeColunaPesquisaProduto('nome_fabricante')">
													Fabricante
												</th>
												<th rowspan="2" style="line-height: 46px;" class="text-center"
													ng-if="exibeColunaPesquisaProduto('nome_tamanho')">
													Tamanho
												</th>
												<th rowspan="2" style="line-height: 46px;" class="text-center"
													ng-if="exibeColunaPesquisaProduto('nome_cor_sabor')">
													Cor/Sabor
												</th>
												<th colspan="3" class="text-center"
													ng-if="funcioalidadeAuthorized('ver_disponibilidade_estoque')">
													Disponibilidade de Estoque
												</th>
												<th colspan="3" class="text-center" style="line-height: 46px;"
													ng-if="exibeColunaPesquisaProduto('desconto')">
													Desconto
												</th>
												<th rowspan="2" width="{{ (configuracoes.flg_botoes_quantidade_pesquisa_produto) ? '140' : '100' }}px" 
													class="text-center" style="line-height: 46px;">
													Qtd
												</th>
												<th rowspan="2" width="80" class="text-center" style="line-height: 46px;">
													R$ Unit.
												</th>
												<th rowspan="2" style="line-height: 46px;"></th>
											</tr>
											<tr ng-if="funcioalidadeAuthorized('ver_disponibilidade_estoque') || exibeColunaPesquisaProduto('desconto')">
												<td width="50" 
													ng-if="funcioalidadeAuthorized('ver_disponibilidade_estoque')">
													Estoque
												</td>
												<td width="50" 
													ng-if="funcioalidadeAuthorized('ver_disponibilidade_estoque')">
													Reservado
												</td>
												<td width="50" 
													ng-if="funcioalidadeAuthorized('ver_disponibilidade_estoque')">
													Disponível
												</td>
												<td ng-if="exibeColunaPesquisaProduto('desconto')"></td>
												<td width="80" class="text-center" 
													ng-if="exibeColunaPesquisaProduto('desconto')">
													%
												</td>
												<td width="80" class="text-center" 
													ng-if="exibeColunaPesquisaProduto('desconto')">
													R$
												</td>
											</tr>
										</thead>
										<tbody>
											<tr ng-if="produtos == null">
												<th class="text-center" colspan="14" style="text-align:center">
													<strong>Carregando</strong>
													<img src="assets/imagens/progresso_venda.gif">
												</th>
											</tr>
											<tr ng-show="(produtos.length == 0)">
												<td colspan="11">Nenhum produto encontrado</td>
											</tr>
											<tr ng-repeat="(index, item) in produtos">
												<td class="text-center text-middle"
													ng-if="exibeColunaPesquisaProduto('id_produto')">
													{{ item.id_produto }}
												</td>
												<td class="text-center text-middle" ng-if="exibeColunaPesquisaProduto('foto_produto')">
													<button type="button" class="btn btn-primary btn-xs"
														ng-disabled="!(item.img)"
														ng-click="abreModalFotoProduto(item)">
														<i class="fa fa-camera"></i>
													</button>
												</td>
												<td style="min-width: 100px;" class="text-center text-middle"
													ng-if="exibeColunaPesquisaProduto('codigo_barra')">
													{{ item.codigo_barra }}
												</td>
												<td style="min-width: 180px;" class="text-middle">
													{{ item.nome_produto }}
												</td>
												<td class="text-center text-middle"
													ng-if="exibeColunaPesquisaProduto('nome_categoria')">
													{{ item.nome_categoria }}
												</td>
												<td class="text-center text-middle"
													ng-if="exibeColunaPesquisaProduto('nome_fabricante')">
													{{ item.nome_fabricante }}
												</td>
												<td class="text-center text-middle"
													ng-if="exibeColunaPesquisaProduto('nome_tamanho')">
													{{ item.peso }}
												</td>
												<td class="text-center text-middle"
													ng-if="exibeColunaPesquisaProduto('nome_cor_sabor')">
													{{ item.sabor }}
												</td>

												<td class="text-center text-middle"
													ng-if="funcioalidadeAuthorized('ver_disponibilidade_estoque')">
													{{ item.qtd_item }}
												</td>
												<td class="text-center text-middle"
													ng-if="funcioalidadeAuthorized('ver_disponibilidade_estoque')">
													<a id="prd_item_{{ index }}" href="#" 
														ng-click="showPopoverOrcamentosProdutoReservado(item, index, $event)">
														{{ item.qtd_reservada }}
													</a>
												</td>
												<td class="text-center text-middle"
													ng-if="funcioalidadeAuthorized('ver_disponibilidade_estoque')">
													{{ item.qtd_item - item.qtd_reservada  }}
												</td>
												<td class="text-center text-middle" style="width: 30px;"
													ng-if="exibeColunaPesquisaProduto('desconto')">
													<label class="label-checkbox">
														<input type="checkbox" id="toggleLine"
															ng-model="item.flg_desconto" 
															ng-click="aplicarDescontoPesquisaProdutos(index)"
															ng-true-value="1" ng-false-value="0" />
														<span class="custom-checkbox"></span>
													</label>
												</td>
												<td class="text-right text-middle"
													ng-if="exibeColunaPesquisaProduto('desconto')"
													style="min-width: 80px;">
													<input type="text" class="form-control text-right" thousands-formatter 
														ng-if="item.flg_desconto == 1"
														ng-keyUp="aplicarDescontoPesquisaProdutos(index, false); item.tipo_desconto = 'perc';"
														ng-model="item.valor_desconto"/>
												</td>
												<td class="text-right text-middle"
													ng-if="exibeColunaPesquisaProduto('desconto')"
													style="min-width: 80px;">
													<input type="text" class="form-control text-right" thousands-formatter 
														ng-if="item.flg_desconto == 1"
														ng-keyUp="aplicarDescontoPesquisaProdutos(index, true); item.tipo_desconto = 'vlr';"
														ng-model="item.valor_desconto_real"/>
												<td style="min-width: {{ (configuracoes.flg_botoes_quantidade_pesquisa_produto) ? '140' : '100' }}px;">
													<input type="text" class="form-control text-center"
														ng-if="!(configuracoes.flg_botoes_quantidade_pesquisa_produto) && !(item.flg_unidade_fracao == 1)"
														ng-model="item.qtd_total" ng-enter="addProduto(item, index)"
														ng-disabled="item.flg_controlar_validade == 1"/>
													<input type="text" class="form-control text-center"
														thousands-formatter precision="3"
														ng-if="!(configuracoes.flg_botoes_quantidade_pesquisa_produto) && (item.flg_unidade_fracao == 1)"
														ng-model="item.qtd_total" ng-enter="addProduto(item, index)"
														ng-disabled="item.flg_controlar_validade == 1"/>

													<div class="input-group" ng-if="(configuracoes.flg_botoes_quantidade_pesquisa_produto)">
														<span class="input-group-btn">
															<button class="btn btn-primary" type="button"
																ng-click="diminuirQuantidadeProduto(item)">
																<i class="fa fa-minus-square"></i>
															</button>
														</span>
														<input type="text" class="form-control text-center no-padding"
															ng-model="item.qtd_total" ng-enter="addProduto(item, index)"
															style="font-size: 10px;" />
														<span class="input-group-btn">
															<button class="btn btn-primary" type="button"
																ng-click="aumentarQuantidadeProduto(item)">
																<i class="fa fa-plus-square"></i>
															</button>
														</span>
													</div>
												</td>
												<td class="text-right text-middle" style="min-width: 80px;">
													R$ {{ item.vlr_unitario | numberFormat : configuracoes.qtd_casas_decimais : ',' : '.' }}
												</td>
												<td class="text-center text-middle">
													<button type="button" ng-if="(item.flg_controlar_validade != 1)"
														ng-click="addProduto(item, index)"
														class="btn btn-{{ !(isProdutoSelecionado(item)) ? 'success' : 'info'}}"
														data-loading-text="Aguarde...">
														<span ng-if="!(isProdutoSelecionado(item)) && !((add_index === index) && (loading_add_produto))">
															<i class="fa fa-check-square-o"></i>
														</span>
														<span ng-if="(isProdutoSelecionado(item)) && !((add_index === index) && (loading_add_produto))">
															<i class="fa fa-refresh"></i>
														</span>
														<span ng-if="((add_index === index) && (loading_add_produto))">
															<i class="fa fa-spinner fa-spin"></i>
														</span>
													</button>
													<button
														class="btn"
														type="button" ng-if="(item.flg_controlar_validade == 1)"
														ng-click="mostrarValidades(item, index)"
													>
														
															<i class="fa fa-calendar"></i>
														</span>
													</button>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>

					<div class="modal-footer">
						<div class="pull-left">
			    			<button class="btn btn-default" data-dismiss="modal">
			    				Fechar Janela
			    			</button>

			    			<button ng-if="mostrar_validades" ng-click="voltarListaProdutos()" class="btn btn-info">
			    				Voltar a lista
			    			</button>

			    		</div>
			    		<div class="pull-right" ng-if="mostrar_validades == false">
							<div class="input-group">
					             <ul class="pagination m-top-none" ng-show="paginacao.produtos.length > 1">
									<li ng-repeat="item in paginacao.produtos" ng-class="{'active': item.current}">
										<a href="" ng-click="loadProdutos(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
					        </div> <!-- /input-group -->
				        </div>
					</div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal tranferencia-->
		<div class="modal fade" id="modal-transferencia" style="display:none">
  			<div class="modal-dialog  modal-xl">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Transferências entre depósitos</span></h4>
      				</div>
				    <div class="modal-body" style="min-height:500px">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.estoqueDep" ng-enter="loadEstoqueDep(0,10)" type="text" class="form-control input-sm">
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
											<th >Depósito</th>
											<th style="width:40px">Quantidade</th>
											<th >Validade</th>
											<th style="width:30px">Qtd para tranferência</th>
											<th style="width: 200px;">Dep. tranferência</th>
											<th colspan="2">Selecionar</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(estoqueDep == null)">
											<td colspan="9" class="text-center"><i class='fa fa-refresh fa-spin'></i> Aguarde, carregando ... </td>
										</tr>
										<tr ng-show="(estoqueDep.length == 0 && transferencia == false)">
											<td colspan="2">Nenhum produto foi encontrado.</td>
										</tr>
										<tr ng-show="(estoqueDep.length == 0 && transferencia == true)">
											<td colspan="2" style="background: #FBFFA5;color: #000;"><i class='fa fa-refresh fa-spin'></i>Aguarde, a transferência está sendo realizada ...</td>
										</tr>
										<tr ng-repeat="item in estoqueDep">
											<td>{{ item.nome }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td>{{ item.peso }}</td>
											<td>{{ item.nme_deposito }}</td>
											<td style="width:40px;font-weight: bold;">{{ item.qtd_item }}</td>
											<td>{{ item.dta_validade | dateFormat:"" }}</td>
											<td>
												<input onKeyPress="return SomenteNumero(event);" ng-model="item.qtd_transferencia" type="text" class="form-control input-sm" ng-if="item.qtd_item > 0">
											</td>
											<td>
												<select class="form-control input-sm" ng-model="item.id_deposito_trasferencia" ng-if="item.qtd_item > 0">
													<option ng-repeat="deposito in depositos" value="{{ deposito.id }}" ng-if="item.id_deposito != deposito.id">{{ deposito.nme_deposito }}
													</option>
												</select>
											</td>
											<td width="50" align="center">
												<button ng-disabled="(item.qtd_transferencia == undefined || item.qtd_transferencia == '') || (item.id_deposito_trasferencia == undefined || item.id_deposito_trasferencia == '')" type="button" class="btn btn-xs btn-success" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Transferindo" ng-click="transferenciaEst(item,$event)" ng-if="item.qtd_item > 0">
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
									<td class="text-right">R$ {{ item.vlr_unitario | numberFormat : configuracoes.qtd_casas_decimais : ',' : '.' }}</td>
									<td class="text-center"><span ng-if="item.valor_desconto_real > 0 && item.valor_desconto_real != undefined">R$<span> {{ item.valor_desconto_real }}</td>
									<td class="text-right">R$ {{ item.sub_total | numberFormat : configuracoes.qtd_casas_decimais : ',' : '.' }}</td>
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
  			<div class="modal-dialog error modal-md">
    			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="cancelar()">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title">Venda finalizada com sucesso!</h4>
					</div>
					<div class="modal-body">
						<p>Utilize os botões abaixo para prosseguir.</p>
					</div>
				    <div class="modal-footer">
				    	<div ng-if="!sendEmailPdf">
					    	<a class="btn btn-md btn-block btn-primary" 
					    		href="{{ url_pdf }}" target="_blank" 
					    		ng-show="!emitirNfe"
					    		ng-click="setvalue('sendEmailPdf',false); emitirNfe = false;">
					    		<i class="fa fa-file-pdf-o"></i> Visualizar PDF (Comprovante de <span ng-if="!pagamento_fulso">Venda</span><span ng-if="pagamento_fulso">Pagamento</span>)
					    	</a>
					    	<a ng-show="!emitirNfe" id="printTermic" class="btn btn-md btn-block btn-primary" 
					    		data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." 
					    		ng-click="printTermic(false)" >
					    		<i class="fa fa-print"></i> Imprimir (via Impressora Térmica)
					    	</a>
					    	<button ng-show="!emitirNfe" type="button" data-loading-text=" Aguarde..." id="btn-imprimir"
					    		class="btn btn-md btn-block btn-success" ng-click="setvalue('sendEmailPdf',true)">
					    		<i class="fa fa-envelope-o"></i> Enviar por E-mail
					    	</button>
					    	<button ng-show="!emitirNfe" ng-click="set('emitirNfe',true)" ng-if="configuracoes.flg_emitir_nfe_pdv == 1" type="button" data-loading-text=" Aguarde..." 
					    		class="btn btn-md btn-block btn-info">
					    		<i class="fa fa-print"></i> Emitir NF-e
					    	</button>
					    	<a ng-show="!emitirNfe" ng-click="cancelar()" class="btn btn-md btn-block btn-default">
					    		<i class="fa fa-reply"></i> Voltar ao PDV
					    	</a>
					    	<a ng-show="emitirNfe==true" ng-disabled="configuracoes.id_operacao_padrao_venda == undefined || configuracoes.id_operacao_padrao_venda == '' " href="nota-fiscal.php?id_venda={{ id_venda }}&&cod_operacao={{configuracoes.id_operacao_padrao_venda}}"  type="button" data-loading-text=" Aguarde..." 
					    		class="btn btn-md btn-block btn-info" >
					    		<i class="fa fa-print"></i> Confirmar Emissão NF-e
					    	</a>
					    	<a ng-show="emitirNfe" ng-click="emitirNfe = false" class="btn btn-md btn-block btn-default">
					    		<i class="fa fa-reply"></i> Voltar
					    	</a>
				    	</div>
				    	<div ng-if="sendEmailPdf">
				    		<div class="col-sm-12">
					    		<div class="alert text-center" style="display:none" id="alert-enviar-email-comprovante-pdf"></div>
							</div>
				    		<div class="col-sm-8" id="emails-enviar-email-comprovante-pdf">
					    		<tags-input
								 ng-model="emailSendPdfVenda"
								 allowed-tags-pattern="^[a-zA-Z0-9][a-zA-Z0-9\._-]+@([a-zA-Z0-9\._-]+\.)[a-zA-Z-0-9]{2}"
								  placeholder="Add email" >
								</tags-input>
							</div>
							<div class="col-sm-4">
					    		<button ng-show="!emitirNfe" type="button" ng-click="enviarEmailPdfVenda($event)" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde" id="btn-imprimir"
						    		class="btn btn-md  btn-success">
						    		<i class="fa fa-paper-plane-o"></i> Enviar
						    	</button>
						    	<button ng-click="setvalue('sendEmailPdf',false); emitirNfe = false;" class="btn btn-md  btn-default">
						    		<i class="fa fa-reply"></i> Voltar
						    	</button>
					    	</div>
				    	</div>
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
  			<div class="modal-dialog error modal-xl">
    			<div class="modal-content">
      				<div class="modal-header">
						<h4>Cadastro Rápido de Cliente</h4>
      				</div>

				    <div class="modal-body">
				    	<div class="alert alert-cadastro-rapido" style="display:none"></div>
				    	<div class="alert alert-cadastro-rapido-error" style="display:none"></div>
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
											<div class="col-lg-3" ng-if="new_cliente.tipo_cadastro == 'pj'">
												<div id="razao_social" class="form-group">
													<label class="control-label">Razão Social</label>
													<input class="form-control" ng-model="new_cliente.razao_social">
												</div>
											</div>

											<div class="col-sm-3" ng-if="new_cliente.tipo_cadastro == 'pj'">
												<div id="nome_fantasia" class="form-group">
													<label class="control-label">Nome Fantasia</label>
													<input class="form-control" ng-model="new_cliente.nome_fantasia">
												</div>
											</div>
											<div class="col-sm-4" ng-if="new_cliente.tipo_cadastro == 'pf'">
												<div id="nome" class="form-group">
													<label for="nome" class="control-label">Nome</label>
													<input type="text" class="form-control" id="nome" ng-model="new_cliente.nome">
												</div>
											</div>
											<div class="col-sm-3" ng-if="new_cliente.tipo_cadastro == 'pf'">
												<div id="dta_nacimento" class="form-group">
													<label class="control-label">Data de Nascimento</label>
													<input class="form-control input-sm" ui-mask="99/99/9999" id="dta_nacimento" ng-model="new_cliente.dta_nacimento">
												</div>
											</div>
											<div class="col-sm-4">
												<div id="email" class="form-group">
													<label for="email" class="control-label">E-mail <span style="color:red;font-weight: bold;">*</label>
													<input type="text" class="form-control" id="email" ng-model="new_cliente.email">
												</div>
											</div>
									    </div>
									    <div class="row" ng-if="new_cliente.tipo_cadastro == 'pj'">
									    	<div class="col-sm-2">
												<div id="celular" class="form-group">
													<label for="" class="control-label">Telefone <span style="color:red;font-weight: bold;">*</label>
													<input type="text" ui-mask="(99) 99999999?9" class="form-control input-sm" ng-model="new_cliente.celular">
												</div>
											</div>
											<div class="col-sm-2">
												<div id="cnpj" class="form-group">
													<label class="control-label">CNPJ</span></label>
													<input class="form-control" ui-mask="99.999.999/9999-99" ng-model="new_cliente.cnpj">
												</div>
											</div>

											<div class="col-sm-2">
												<div id="inscricao_estadual" class="form-group">
													<label class="control-label">Inscrição Estadual</label>
													<input class="form-control" ng-model="new_cliente.inscricao_estadual">
												</div>
											</div>
										</div>
									    <div class="row" ng-if="new_cliente.tipo_cadastro == 'pf'">
									    	<div class="col-sm-2">
												<div id="celular" class="form-group">
													<label for="" class="control-label">Telefone </label>
													<input type="text" ui-mask="(99) 99999999?9" class="form-control input-sm" ng-model="new_cliente.celular">
												</div>
											</div>
											<div class="col-sm-3">
												<div id="rg" class="form-group">
													<label class="control-label">RG</label>
													<input class="form-control" ng-model="new_cliente.rg"/>
												</div>
											</div>

											<div class="col-sm-3">
												<div id="cpf" class="form-group">
													<label class="control-label">CPF</label>
													<input class="form-control" ui-mask="999.999.999-99" ng-model="new_cliente.cpf"/>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-4">
												<div class="form-group" id="regimeTributario">
													<label for="" class="control-label">Perfil </label>
													<select chosen
												    option="perfisCadastroRapido"
												    ng-model="new_cliente.id_perfil"
												    ng-options="perfil.id as perfil.nome for perfil in perfisCadastroRapido">
													</select>
												</div>
											</div>
											<div class="col-sm-2">
												<div id="id_como_encontrou" class="form-group">
													<label class="control-label">Como Encontrou?</label>
													<select class="form-control input-sm" ng-model="new_cliente.id_como_encontrou" ng-options="a.id as a.nome for a in comoencontrou"><option value=""></option></select>
												</div>
											</div>
											<div class="col-sm-4" ng-show="new_cliente.id_como_encontrou == 'outros' ">
											<div id="como_entrou_outros" class="form-group">
												<label class="control-label">Descreva</label>
												<input type="text" class="form-control input-sm" ng-model="cliente.como_entrou_outros">
											</div>
										</div>
										</div>
										<div class="row">
										<div class="col-sm-2">
											<div id="cep" class="form-group">
												<label class="control-label">CEP</label>
												<input type="text" class="form-control input-sm" ui-mask="99999-999" ng-model="new_cliente.cep" ng-keyUp="validCep(new_cliente.cep)" ng-blur="validCep(new_cliente.cep)">
											</div>
										</div>

										<div class="col-sm-7">
											<div id="endereco" class="form-group">
												<label class="control-label">Endereço</label>
												<input type="text" class="form-control input-sm" ng-model="new_cliente.endereco">
											</div>
										</div>

										<div class="col-sm-1">
											<div id="numero" class="form-group">
												<label class="control-label">No.</label>
												<input id="num_logradouro" type="text" class="form-control input-sm" ng-model="new_cliente.numero" ng-blur="consultaLatLog()">
											</div>
										</div>

										<div class="col-sm-2">
											<div id="bairro" class="form-group">
												<label class="control-label">Bairro</label>
												<input type="text" class="form-control input-sm" ng-model="new_cliente.bairro">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-12">
											<div id="complemento" class="form-group">
												<label class="control-label">Complemento:</label>
												<input type="text" class="form-control input-sm" ng-model="new_cliente.end_complemento">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-4">
											<div id="ponto_referencia" class="form-group">
												<label class="control-label">Ponto Referência</label>
												<input type="text" class="form-control input-sm" ng-model="new_cliente.ponto_referencia">
											</div>
										</div>
										<div class="col-sm-2">
											<div id="regiao" class="form-group">
												<label class="control-label">Região</label>
												<input type="text" class="form-control input-sm" ng-model="new_cliente.regiao">
											</div>
										</div>

										<div class="col-sm-2">
											<div id="id_estado" class="form-group">
												<label class="control-label">Estado</label>
												<select chosen id="id_select_estado" class="form-control input-sm" readonly="readonly" ng-model="new_cliente.id_estado" ng-options="item.id as item.nome for item in estados" ng-change="new_cliente.id_cidade=null;loadCidadesByEstado()"></select>
											</div>
										</div>


										<div class="col-sm-4">
											<div id="id_cidade" class="form-group">
												<label class="control-label">Cidade</label>
												<select chosen class="form-control input-sm" readonly="readonly" ng-model="new_cliente.id_cidade" ng-options="a.id as a.nome for a in cidades"></select>
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
											<th colspan="2">Selecionar</th>
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

		<!-- /Modal Processando Venda-->
		<div class="modal fade" id="modal-sat-cfe" style="display:none">
  			<div class="modal-dialog error modal-md">
    			<div class="modal-content">
      				<div class="modal-header">
						<h4>Aguarde!</h4>
      				</div>

				    <div class="modal-body">
				    	<p><i class="fa fa-spin fa-spinner"></i> {{ mensagem_sat_modal }}</p>
				    </div>
			  	</div>
			  	<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal Processando Venda-->
		<div class="modal fade" id="modal-cnf" style="display:none">
  			<div class="modal-dialog error modal-md">
    			<div class="modal-content">
      				<div class="modal-header">
						<h4>Aguarde!</h4>
      				</div>

				    <div class="modal-body">
				    	<div class="alert alert-reforco" style="display:none"></div>

				    	<div class="row">
				    		<p>
				    			<img src="assets/imagens/progresso_venda.gif"> <span id="text_status_cnf"></span>
				    		</p>
				    	</div>
				    </div>
			  	</div>
			  	<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal Processando erro nfce -->
		<div class="modal fade" id="modal-erro-nfce" style="display:none">
  			<div class="modal-dialog error modal-md">
    			<div class="modal-content">
      				<div class="modal-header">
      					<h4>Ocorreu um erro ao processar o NFC-e</h4>
      				</div>
				    <div class="modal-body">
				    	<ul><li ng-repeat="erro in erros_nfce">{{ erro.mensagem }}</li></ul>
				    </div>
				     <div class="modal-footer">
			    		<button type="button" class="btn btn-md btn-default" data-dismiss="modal">
			    			Fechar
			    		</button>
			    		<button type="button" class="btn btn-md btn-primary" 
			    			ng-if="(id_venda && cod_nota_fiscal)"
			    			ng-click="processNFCe(id_venda, cod_nota_fiscal)">
			    			<i class="fa fa-refresh"></i> Reprocessar Cupom NFC-e
			    		</button>
			    	</div>
			  	</div>
			</div>
		</div>
		<!-- /.modal -->

		<!-- /Modal Processando erro nfce -->
		<div class="modal fade" id="modal-danfe-nfce" style="display:none">
  			<div class="modal-dialog error modal-md">
    			<div class="modal-content">
      				<div class="modal-header">
      					<h4>DANFE NFC-e</h4>
      				</div>
				    <div class="modal-body">
				    	<iframe id="iframe-nfce" src="{{ trustSrc(nfce_data.caminho_danfe) }}" 
				    		frameborder="0" allowtransparency="true"
				    		width="100%" height="380">
				    	</iframe>  
				    </div>
				     <div class="modal-footer">
			    		<button type="button" class="btn btn-md btn-default" data-dismiss="modal">
			    			Fechar
			    		</button>
			    		<button type="button" class="btn btn-md btn-info" ng-click="printDANFENFCe()">
			    			<i class="fa fa-print"></i> Imprimir DANFE NFC-e
			    		</button>
			    	</div>
			  	</div>
			</div>
		</div>
		<!-- /.modal -->

		<!-- /Modal Processando erro sat -->
		<div class="modal fade" id="modal-erro-sat" style="display:none">
  			<div class="modal-dialog error modal-md">
    			<div class="modal-content">
      				<div class="modal-header">
      					<h4>Ocorreu um erro ao processar o CF-e SAT</h4>
      				</div>
				    <div class="modal-body">
				    	<div class="row">
				    		<div class="col-sm-12">
				    			<b>Código de Erro: </b> {{ erro_sat.codigoErro | Utf8Decode }} <br/>
				    			<b>Mensagem: </b> {{ erro_sat.msgErro | Utf8Decode }} <br/>
				    			<b>Problemas: </b> <br/>
				    			<p ng-repeat="item in erro_sat.problemas track by $index"><i class="fa fa-circle text-danger"></i> {{item | Utf8Decode}}</p>	
							</div>
				    	</div>
				    </div>
				     <div class="modal-footer">
			    	<button type="button" data-loading-text=" Aguarde..."
			    		class="btn btn-md btn-default" ng-click="location('pdv.php')">
			    		 OK
			    	</button>
			    </div>
			  	</div>
			  	<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal Processando erro sat -->
		<div class="modal fade" id="modal-erro-cacular-impostos" style="display:none">
  			<div class="modal-dialog error modal-md">
    			<div class="modal-content">
      				<div class="modal-header">
      					<h4>Erro ao Calcular os Impostos e Tributos</h4>
      				</div>
				    <div class="modal-body">
				    	<div class="row">
				    		<div class="col-sm-12">
				    			<span ng-if="(mensagem_erro_calculo_impostos)">{{ mensagem_erro_calculo_impostos }}</span>	
				    			<span ng-if="(json_erro_calculo_impostos)">{{ json_erro_calculo_impostos }}</span>	
							</div>
				    	</div>
				    </div>
				     <div class="modal-footer">
			    	<button type="button" data-loading-text=" Aguarde..."
			    		class="btn btn-md btn-default" ng-click="location('pdv.php')">
			    		 OK
			    	</button>
			    </div>
			  	</div>
			  	<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal Processando erro sat -->
		<div class="modal fade" id="modal-conexao-websocket" style="display:none">
  			<div class="modal-dialog error modal-md">
    			<div class="modal-content">
      				<div class="modal-header">
      					<h4>Conexão com WebSocket</h4>
      				</div>
				    <div class="modal-body">
				    	<div class="row">
				    		<div class="col-sm-12">
				    			<strong>Não foi possível emitir o cupom SAT CF-e pois o aplicativo cliente (WebliniaERP Client) não está aberto.<br/>Após iniciar o aplicativo tente reprocessar este cupom.</strong>	
							</div>
				    	</div>
				    </div>
				     <div class="modal-footer">
			    	<button type="button" data-loading-text=" Aguarde..."
			    		class="btn btn-md btn-default" ng-click="resetPdv('venda')">
			    		 OK
			    	</button>
			    </div>
			  	</div>
			  	<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal Vendas para reenviar SAT-->
		<div class="modal fade" id="modal-vendas-reenviar-sat" style="display:none">
  			<div class="modal-dialog modal-md">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 ng-if="cdb_busca.status==false">Vendas</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input type="text" class="form-control input-sm"
						            	ng-model="busca.vendas_sat" ng-enter="loadVendasReenviarSat(0,10)">

						            <div class="input-group-btn">
						            	<button tabindex="-1" class="btn btn-sm btn-primary" type="button"
						            		ng-click="loadVendasReenviarSat(0,10)">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div> 
						        </div>
							</div>
						</div><br>
						<div class="row">
							<div class="col-md-12">
								<div class="alert alert-produtos" style="display:none"></div>
						   		<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(vendas_reenviar_sat.length != 0)">
										<tr>
											<th>#</th>
											<th>Data</th>
											<th>Vendedor</th>
											<th>Cliente</th>
											<th>Valor</th>
											<th width="40"></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-if="vendas_reenviar_sat == null">
											<th class="text-center" colspan="9" style="text-align:center"><i class="fa fa-refresh fa-spin"></i> <strong>Carregando</strong></th>
										</tr>
										<tr ng-show="(vendas_reenviar_sat.length == 0)">
											<td colspan="3">Nenhum venda encontrada</td>
										</tr>
										<tr ng-repeat="item in vendas_reenviar_sat" bs-tooltip >
											<td>{{ item.id }}</td>
											<td>{{ item.dta_venda }}</td>
											<td>{{ item.nme_vendedor }}</td>
											<td>{{ configuracoes.id_cliente_movimentacao_caixa == item.id_cliente && 'S/N'  ||  item.nme_cliente }}</td>
											<td>R${{ item.vlr_total_venda | numberFormat:2:',':'.'}}</td>
											<td>
												<button class="btn btn-success btn-xs" type="button"
													data-toggle="tooltip" title="Enviar SAT" 
													data-loading-text='<i class="fa fa-refresh fa-spin"></i>' 
													ng-disabled="process_reeviar_sat" 
													ng-click="reenviarSat(item,$event)"
													ng-if="(caixa_aberto.flg_imprimir_sat_cfe)">
													<i class="fa fa-paper-plane-o"></i>
												</button>
												<button class="btn btn-success btn-xs" type="button"
													data-toggle="tooltip" title="Enviar SAT" 
													data-loading-text='<i class="fa fa-refresh fa-spin"></i>' 
													ng-disabled="process_reeviar_sat" 
													ng-click="processNFCe(item.id, item.cod_nota_fiscal); process_reeviar_sat = true;"
													ng-if="(caixa_aberto.flg_imprimir_nfce)">
													<i class="fa fa-paper-plane-o"></i>
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
						             <ul class="pagination pagination-xs m-top-none" ng-show="paginacao.vendas_reenviar_sat.length > 1">
										<li ng-repeat="item in paginacao.vendas_reenviar_sat" ng-class="{'active': item.current}">
											<a href="" ng-click="loadVendasReenviarSat(item.offset,item.limit)">{{ item.index }}</a>
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

		<!-- /Modal Vendas para re-imprimir CNF-->
		<div class="modal fade" id="modal-vendas-reimprimir-cnf" style="display:none">
  			<div class="modal-dialog modal-md">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 ng-if="cdb_busca.status==false">Vendas</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input type="text" class="form-control input-sm"
						            	ng-model="busca.vendas_cnf" ng-enter="loadVendasCaixaAberto(0,10)">

						            <div class="input-group-btn">
						            	<button tabindex="-1" class="btn btn-sm btn-primary" type="button"
						            		ng-click="loadVendasCaixaAberto(0,10)">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div> 
						        </div>
							</div>
						</div><br>
						<div class="row">
							<div class="col-md-12">
								<div class="alert alert-produtos" style="display:none"></div>
						   		<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(vendas_caixa_aberto.length != 0)">
										<tr>
											<th>#</th>
											<th>Data</th>
											<th>Vendedor</th>
											<th>Cliente</th>
											<th>Valor</th>
											<th width="40"></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-if="vendas_caixa_aberto == null">
											<th class="text-center" colspan="9" style="text-align:center"><i class="fa fa-refresh fa-spin"></i> <strong>Carregando</strong></th>
										</tr>
										<tr ng-show="(vendas_caixa_aberto.length == 0)">
											<td colspan="3">Nenhuma venda encontrada</td>
										</tr>
										<tr ng-repeat="item in vendas_caixa_aberto" bs-tooltip >
											<td>{{ item.id }}</td>
											<td>{{ item.dta_venda }}</td>
											<td>{{ item.nme_vendedor }}</td>
											<td>{{ configuracoes.id_cliente_movimentacao_caixa == item.id_cliente && 'S/N'  ||  item.nme_cliente }}</td>
											<td>R${{ item.vlr_total_venda | numberFormat:2:',':'.'}}</td>
											<td>
											<button ng-disabled="process_reimprimir_cnf" data-toggle="tooltip" title="Re-imprimir" data-loading-text='<i class="fa fa-refresh fa-spin"></i>' ng-click="reimprimir_cnf(item,$event)" class="btn btn-success btn-xs" type="button">
												<i class="fa fa-paper-plane-o"></i>
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
						             <ul class="pagination pagination-xs m-top-none" ng-show="paginacao.vendas_caixa_aberto.length > 1">
										<li ng-repeat="item in paginacao.vendas_caixa_aberto" ng-class="{'active': item.current}">
											<a href="" ng-click="loadVendasCaixaAberto(item.offset,item.limit)">{{ item.index }}</a>
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

		<!-- Modal Cancelamento SAT -->
		<div class="modal fade" id="modal-cancelar-cupom-sat" style="display:none">
  			<div class="modal-dialog modal-lg">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Cancelamento de CF-e SAT</h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="alert alert-produtos" style="display:none"></div>
						   		<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(vendas_reenviar_sat.length != 0)">
										<tr>
											<th width="70">#</th>
											<th width="150">Data</th>
											<th>Vendedor</th>
											<th>Cliente</th>
											<th class="text-center" width="90">Valor</th>
											<th width="40"></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(vendas_cancelar_sat.notas.length == 0)">
											<td class="text-center" colspan="6">Nenhuma venda encontrada</td>
										</tr>
										<tr ng-repeat="item in vendas_cancelar_sat.notas" bs-tooltip >
											<td>{{ item.cod_venda }}</td>
											<td>{{ item.dta_venda | dateFormat: 'dateTime' }}</td>
											<td>{{ item.nme_vendedor }}</td>
											<td>{{ item.nme_cliente }}</td>
											<td class="text-right">R$ {{ item.vlr_total_venda | numberFormat:2:',':'.' }}</td>
											<td>
												<button type="button" class="btn btn-danger btn-xs"
													data-loading-text='<i class="fa fa-refresh fa-spin"></i> Aguarde...' 
													ng-if="(configuracoes.flg_tipo_documento_fiscal_consumidor == 'SAT')"
													ng-click="cancelarSat(item)" >
													<i class="fa fa-times-circle"></i> Cancelar SAT CF-e
												</button>
												<button type="button" id="btn-cancel-nfce-{{ $index }}" class="btn btn-danger btn-xs"
													data-loading-text='<i class="fa fa-refresh fa-spin"></i> Aguarde...' 
													ng-if="(configuracoes.flg_tipo_documento_fiscal_consumidor == 'NFCe')"
													ng-click="cancelNFCe(item.cod_nota_fiscal, $index)" >
													<i class="fa fa-times-circle"></i> Cancelar NFC-e
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
						             <ul class="pagination pagination-xs m-top-none" ng-show="vendas_cancelar_sat.paginacao.length > 1">
										<li ng-repeat="item in vendas_cancelar_sat.paginacao" ng-class="{'active': item.current}">
											<a href="" ng-click="loadVendasCancelarSat(item.offset,item.limit)">{{ item.index }}</a>
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

		<!-- /Modal Fechamentos de Caixa-->
		<div class="modal fade" id="list_fechamentos_caixa" style="display:none">
				<div class="modal-dialog modal-xl">
				<div class="modal-content">
						<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Fechamentos de Caixa p/ Impressão</span></h4>
						</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-2">
								<div class="form-group">
									<label class="control-label">Data</label>
									<div class="input-group">
										<input readonly="readonly" style="background:#FFF;cursor:pointer" 
											type="text" id="dta_fechamento" class="datepicker form-control input-sm">
										<span class="input-group-addon" id="cld_dta_fechamento">
											<i class="fa fa-calendar"></i>
										</span>
									</div>
								</div>
							</div>

							<div class="col-md-1">
								<div class="form-group">
									<label class="control-label"><br/></label>
									<button type="button" class="btn btn-sm btn-primary"
										ng-click="getFechamentosCaixa(0,10)">
										<i class="fa fa-filter"></i> Filtrar
									</button>
								</div>
							</div>

							<div class="col-md-1">
								<div class="form-group">
									<label class="control-label"><br/></label>
									<button type="button" class="btn btn-sm btn-default"
										ng-click="clearDtaFechamento();">
										<i class="fa fa-ban"></i> Limpar
									</button>
								</div>
							</div>
						</div>

						<br>

						<div class="row">
							<div class="col-md-12">
						   		<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(fechamento.aberturas != 0)">
										<tr>
											<th class="text-center">Caixa</th>
											<th class="text-center">Operador</th>
											<th class="text-center">Dta. Abertura</th>
											<th class="text-center">Dta. Fechamento</th>
											<th class="text-center" width="100">Ações</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-if="fechamento.aberturas == null">
											<th class="text-center" colspan="5">
												<i class="fa fa-spin fa-spinner"></i>
												<strong>Aguarde, carregando...</strong>
											</th>
										</tr>
										<tr class="text-center" ng-show="(fechamento.aberturas.length == 0)">
											<td colspan="5">Nenhum registro encontrado.</td>
										</tr>
										<tr ng-repeat="item in fechamento.aberturas">
											<td class="text-center">{{ item.dsc_conta_bancaria }}</td>
											<td class="text-center">{{ item.operador }}</td>
											<td class="text-center">{{ item.dta_abertura }}</td>
											<td class="text-center">{{ item.dta_fechamento }}</td>
											<td class="text-center">
												<a href="rel_movimentacao_caixa.php?id={{ item.id }}" 
													target="_blank" 
													class="btn btn-xs btn-default"
													tooltip title="Visualizar">
													<i class="fa fa-file-pdf-o"></i>
												</a>
												<button type="button" class="btn btn-xs btn-primary"
													tooltip title="Imprimir"
													ng-click="printRFC(item.id)">
													<i class="fa fa-print"></i>
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
						             <ul class="pagination pagination-xs m-top-none" ng-show="fechamento.paginacao.length > 1">
										<li ng-repeat="item in fechamento.paginacao" ng-class="{'active': item.current}">
											<a href="" ng-click="getFechamentosCaixa(item.offset, item.limit)">{{ item.index }}</a>
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

		<!-- /Modal Comandas-->
		<div class="modal fade" id="list_comandas" style="display:none">
				<div class="modal-dialog modal-xl">
				<div class="modal-content">
						<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Comandas em aberto</span></h4>
						</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-4" ng-if="(configuracoes.flg_modo_controle_mesas == 'mesas_comandas')">
								<div class="form-group" id="regimeTributario">
									<!--<label class="control-label">Operação</label> -->
									<select chosen
								    option="mesas"
								    ng-model="busca.id_mesa_comanda"
								    ng-change="loadComandas(0,10)"
								    data-placeholder="Selecione uma mesa"
								    ng-options="mesa.id_mesa as mesa.dsc_mesa for mesa in mesas">
									</select>
								</div>
							</div>
							<div class="col-md-8">
								<div class="input-group">
						            <input ng-model="busca.comandas" ng-enter="loadComandas(0,10)" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button tabindex="-1" class="btn btn-sm btn-primary" type="button"
						            		ng-click="loadComandas(0,10)">
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
									<thead ng-show="(comandas.dados != 0)">
										<tr>
											<th class="text-center">Nº Comanda</th>
											<th class="text-center" ng-if="(configuracoes.flg_usa_cartao_magnetico == 1)">Nº Cartão</th>
											<th class="text-center" ng-if="(configuracoes.flg_modo_controle_mesas == 'mesas_comandas')">Mesa</th>
											<th class="text-center">Cliente</th>
											<th class="text-center">Quantidade itens</th>
											<th width="100" class="text-center">Valor</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-if="comandas.dados == null">
											<th class="text-center" colspan="9" style="text-align:center"><strong>Carregando</strong><img src="assets/imagens/progresso_venda.gif"></th>
										</tr>
										<tr class="text-center" ng-show="(comandas.dados.length == 0)">
											<td colspan="3">Nenhuma comanda encontrada</td>
										</tr>
										<tr ng-repeat="item in comandas.dados">
											<td class="text-center">#{{ item.id_comanda }}</td>
											<td class="text-center">{{ item.num_cartao_fisico }}</td>
											<td class="text-center" ng-if="(configuracoes.flg_modo_controle_mesas == 'mesas_comandas')">{{ item.dsc_mesa }}</td>
											<td ng-if="config.id_cliente_movimentacao_caixa != item.id_cliente">{{ item.nome_cliente }}</td>
											<td ng-if="config.id_cliente_movimentacao_caixa == item.id_cliente">(Não informado)</td>
											<td class="text-center">{{ item.qtd_total }}</td>
											<td class="text-right">R$ {{ item.valor_total | numberFormat:2:',':'.' }}</td>
											<td width="100" align="center">
												<a type="button" class="btn btn-xs btn-success"
													href="pdv.php?id_orcamento={{item.id_comanda}}" 
													tooltip title="Fechar Comanda">
													<i class="fa fa-dollar"></i>
												</a>
												<button type="button" class="btn btn-xs btn-primary" 
													tooltip title="Juntar à Comanda"
													ng-click="juntarComanda(item.id_comanda)"
													ng-if="(flg_comanda)">
													<i class="fa fa-link"></i>
												</button>
												<button type="button" class="btn btn-xs btn-info" 
													tooltip title="Imprimir Comanda"
													ng-click="printComanda(item.id_comanda)">
													<i class="fa fa-print"></i>
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
						             <ul class="pagination pagination-xs m-top-none" ng-show="comandas.paginacao.length > 1">
										<li ng-repeat="item in comandas.paginacao" ng-class="{'active': item.current}">
											<a href="" ng-click="loadComandas(item.offset,item.limit)">{{ item.index }}</a>
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

		<!-- /Modal info lote-->
		<div class="modal fade" id="modal-info-lote" style="display:none">
			<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>#{{ modal_lote.id }} {{ modal_lote.nome_produto }} {{ modal_lote.peso }} {{ modal_lote.sabor }}</h4>
					</div>
					<div class="modal-body">
						<div class="alert" id="alert-modal-lote" style="display:none"></div>
						<div class="row">
							<div class="col-sm-4" id="modal_lote-lote">
								<label class="control-label">Lote</label>
								<div class="form-group text-center">
									<input type="text" class="form-control input-md" ng-model="modal_lote.lote"/>
								</div>
							</div>
							<div class="col-sm-4" id="modal_lote-validade">
								<label class="control-label">Validade</label>
								<div class="form-group text-center">
									<input type="text" class="form-control input-md"  ui-mask="99/99/9999" ng-model="modal_lote.validade"/>
								</div>
							</div>
							<div class="col-sm-4" id="modal_lote-qtd">
								<label class="control-label">Qtd</label>
								<div class="form-group text-center">
									<input type="text" class="form-control input-md" onKeyPress="return SomenteNumeroLetras(event);" ng-model="modal_lote.qtd"/>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" data-loading-text=" Aguarde..." ng-click="cancelarModal('modal-info-lote')" class="btn btn-md  btn-default fechar-modal">
							<i class="fa fa-times-circle"></i> Cancelar
						</button>
						<button type="button" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." id="btn-modal-lote-inserir" class="btn btn-md  btn-success" ng-click="inserirProdutoLote(modal_lote)">
							<i class="fa fa-plus-circle"></i> inserir
						</button>
					</div>
				</div>
			</div>
		</div>
		<!-- /.modal -->

		<!-- /Modal password desconto-->
		<div class="modal fade" id="modal-password-discount" style="display:none">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Desbloqueio de Desconto</h4>
					</div>
					<div class="modal-body" style="padding-top: 10px; padding-bottom: 0px;">
						<div class="alert" id="alert-modal-password-discount" style="display:none"></div>
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<label class="control-label">Informe o login</label>
									<input type="text" class="form-control" ng-model="autorizador.login">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<label class="control-label">Informe a senha</label>
									<input type="password" class="form-control" ng-model="autorizador.senha">
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer" style="margin-top: 0px;">
						<button type="button" class="btn btn-md btn-default fechar-modal"
							data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..."
							ng-click="cancelarModal('modal-password-discount')">
							<i class="fa fa-times-circle"></i> Cancelar
						</button>
						<button type="button" class="btn btn-md btn-success" 
							data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..."
							ng-click="autorizarDesconto()">
							<i class="fa fa-unlock"></i> Autorizar
						</button>
					</div>
				</div>
			</div>
		</div>
		<!-- /.modal -->

		<!-- /Modal CPF na venda-->
		<div class="modal fade" id="modal-cpf-venda" style="display:none">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>CPF do Cliente</h4>
					</div>
					<div class="modal-body" style="padding-top: 10px; padding-bottom: 0px;">
						<div class="form-group">
							<label class="control-label">Insira o CPF do cliente:</label>
							<input type="text" class="form-control" 
								ng-model="cliente.cpf"
								ng-keyup="validaDocumentoCliente()"/>
						</div>
					</div>
					<br>
					<div class="modal-footer" style="margin-top: 0px;">
						<button type="button" class="btn btn-md btn-default fechar-modal"
							data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..."
							ng-click="cancelarModal('modal-cpf-venda')">
							<i class="fa fa-times-circle"></i> Cancelar
						</button>
					</div>
				</div>
			</div>
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

    <!-- Chosen -->
	<script src='js/chosen.jquery.min.js'></script>

    <!-- Gritter -->
	<script src="js/jquery.gritter.min.js"></script>

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

	<!-- accounting -->
	<script type="text/javascript" src="js/accounting.min.js"></script>

	<!-- fold-to-ascii -->
	<script type="text/javascript" src="js/fold-to-ascii.js"></script>

	<!-- Bower Components -->	
	<script src="bower_components/noty/lib/noty.min.js" type="text/javascript"></script>
    <script src="bower_components/mojs/build/mo.min.js" type="text/javascript"></script>

	<!-- Extras -->
	<script src="js/extras.js?version=<?php echo date("dmY-His", filemtime("js/extras.js")) ?>"></script>

	<!-- UnderscoreJS -->
	<script type="text/javascript" src="bower_components/underscore/underscore.js"></script>
	<script type="text/javascript" src="bower_components/print-js/dist/print.min.js"></script>

	<!-- Moment -->
	<script src="js/moment/moment.min.js"></script>

	<script src="js/jquery.noty.packaged.js"></script>


	<!-- ScrennFull  -->
	<script type="text/javascript" src="js/screenfull/screenfull.js"></script>

	<!-- AngularJS -->
	<script type="text/javascript" src="bower_components/angular/angular.js"></script>
	<script src="js/angular-strap.min.js"></script>
	<script src="js/angular-strap.tpl.min.js"></script>
	<script type="text/javascript" src="bower_components/angular-ui-utils/mask.min.js"></script>
    <script src="js/angular-sanitize.min.js"></script>
    <script src="js/ui-bootstrap-tpls-0.6.0.js" type="text/javascript"></script>
    <script src="js/dialogs.v2.min.js" type="text/javascript"></script>
  	<script src="js/auto-complete/ng-sanitize.js"></script>
  	<script src="js/angular-chosen.js"></script>
  	<script src="js/ng-tags-input.min.js"></script>
    <script type="text/javascript">
   	 var addParamModule = ['angular.chosen','ngTagsInput'] ;

   	 $(document).ready(function() {
   	 	$('.datepicker').datepicker();
		$("#cld_dta_fechamento").on("click", function(){$("#dta_fechamento").trigger("focus");});
		$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
   	 });
    </script>
    <script src="js/app.js?version=<?php echo date("dmY-His", filemtime("js/app.js")) ?>"></script>
    <script src="js/auto-complete/AutoComplete.js?version=<?php echo date("dmY-His", filemtime("js/auto-complete/AutoComplete.js")) ?>"></script>
    <script src="js/angular-services/user-service.js?version=<?php echo date("dmY-His", filemtime("js/angular-services/user-service.js")) ?>"></script>
	<script src="js/angular-controller/pdv-controller.js?version=<?php echo date("dmY-His", filemtime("js/angular-controller/pdv-controller.js")) ?>"></script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

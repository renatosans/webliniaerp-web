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

		<!-- Datepicker -->
		<link href="css/datepicker.css" rel="stylesheet"/>

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

			@media screen and (min-width: 768px) {

				#list_proodutos.modal-dialog  {width:900px;}

			}

			#list_produtos .modal-dialog  {width:70%;}

			#list_produtos .modal-content {min-height: 640px;;}

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

	<body class="overflow-hidden" ng-controller="NotaFiscalController" ng-cloak>
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
						 <li><i class="fa fa-signal"></i> <a href="vendas.php">Vendas</a></li>
						 <li><i class="fa fa-barcode"></i> <a href="notas-fiscais.php">Notas Fiscais</a></li>
						 <li class="active"><i class="fa fa-file-text-o"></i> Visualização de Nota Fiscal</li>
					</ul>
				</div><!-- breadcrumb -->

				<div class="main-header clearfix">
					<div class="page-title">
						<h3 class="no-margin"><i class="fa fa-file-text-o"></i> Visualização de Nota Fiscal</h3>
					</div><!-- /page-title -->
				</div><!-- /main-header -->

				<div class="padding-md">
					<div class="alert alert-sistema alert-info" ng-if="processando_autorizacao || autorizado">
						<p>
							<strong><i class="fa fa-info-circle"></i> Atenção!</strong>
							<br/>
							{{ processando_autorizacao && 'Venda já com nota em processamento' || 'Venda com nota já autorizada' }}
						</p>
					</div>

					<div class="panel panel-primary">
						<div class="panel-heading">
							<h3 class="panel-title">NF-e Nº {{ NF.dados_emissao.num_documento_fiscal }}</h3>
						</div>

						<div class="panel-tab clearfix">
							<ul class="tab-bar">
								<li class="active"><a href="#geral" data-toggle="tab"><i class="fa fa-gear"></i> Dados de Emissão</a></li>
								<li><a href="#emitente" data-toggle="tab"><i class="fa fa-building-o"></i> Dados do Emitente</a></li>
								<li><a href="#destinatario" data-toggle="tab"><i class="fa fa-user"></i> Dados do Destinatário</a></li>
								<li ng-show="(NF.transportadora.modalidade_frete != '' && NF.transportadora.modalidade_frete != '9')"><a href="#transportadora" data-toggle="tab"><i class="fa fa-truck"></i> Dados da Transportadora</a></li>
								<li><a href="#produtos" data-toggle="tab"><i class="fa fa-list"></i> Produtos</a></li>
								<li><a href="#resumo" data-toggle="tab"><i class="fa fa-bars"></i> Resumo da NF-e</a></li>
							</ul>
						</div>
						<div class="panel-body">
							<div class="tab-content">
								<div class="tab-pane fade in active" id="geral">
									<div class="alert" style="display:none"></div>
									<div class="row" ng-if="!(processando_autorizacao || autorizado)">
										<div class="col-sm-6">
											<div class="form-group" id="cod_operacao">
												<label class="control-label">Operação</label> 
												<select chosen ng-change="setDadosEmissao()"
											    	option="lista_operacao"
											    	ng-model="NF.dados_emissao.cod_operacao"
											    	ng-options="operacao.cod_operacao as operacao.dsc_operacao for operacao in lista_operacao">
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-3">
											<div class="form-group">
												<label class="control-label">Local de Destino</label> 
												<select ng-disabled="processando_autorizacao || autorizado" chosen
												    option="lista_local_destino"
												    ng-model="NF.dados_emissao.local_destino"
												    ng-options="item.num_item as item.nme_item for item in lista_local_destino">
												</select>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label class="control-label">Finalidade Emissão</label> 
												<select ng-disabled="processando_autorizacao || autorizado" chosen
												    option="lista_finalidade_emissao"
												    ng-model="NF.dados_emissao.finalidade_emissao"
												    ng-options="item.num_item as item.nme_item for item in lista_finalidade_emissao">
												</select>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label class="control-label">Consumidor Final</label> 
												<select ng-disabled="processando_autorizacao || autorizado" chosen
												    option="lista_consumidor_final"
												    ng-model="NF.dados_emissao.consumidor_final"
												    ng-options="item.num_item as item.nme_item for item in lista_consumidor_final">
												</select>
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">Forma Pagamento</label> 
												<select ng-disabled="processando_autorizacao || autorizado" chosen
												    option="lista_forma_pagamento"
												    allow-single-deselect="true"
												    ng-model="NF.dados_emissao.forma_pagamento"
												    ng-options="item.num_item as item.nme_item for item in lista_forma_pagamento">
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-3">
											<div class="form-group">
												<label class="control-label">Tipo de Documento</label> 
												<select ng-disabled="processando_autorizacao || autorizado" chosen
												    option="lista_tipo_documento"
												    allow-single-deselect="true"
												    ng-model="NF.dados_emissao.tipo_documento"
												    ng-options="documento.num_item as documento.nme_item for documento in lista_tipo_documento">
												</select>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label class="control-label">Presença Comprador</label> 
												<select ng-disabled="processando_autorizacao || autorizado" chosen
												    option="lista_presenca_comprador"
												    ng-model="NF.dados_emissao.presenca_comprador"
												    ng-options="item.num_item as item.nme_item for item in lista_presenca_comprador">
												</select>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label class="control-label">Modalidade de Frete</label> 
												<select ng-disabled="processando_autorizacao || autorizado" chosen
												    option="lista_modalidade_frete"
												    ng-model="NF.transportadora.modalidade_frete"
												    ng-options="mod_frete.num_item as mod_frete.nme_item for mod_frete in lista_modalidade_frete">
												</select>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">Data de Emissão</label>
												<div class="input-group">
													<input ng-disabled="processando_autorizacao || autorizado" id="inputDtaEmissao" readonly="readonly" style="background:#FFF;cursor:pointer" type="text" class="datepicker form-control">
													<ng-disabled="processando_autorizacao || autorizado"   id="btnDtaEmissao" class="input-group-addon"><i class="fa fa-calendar"></i></span>
												</div>
											</div>
										</div>

										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">Data de Saída</label>
												<div class="input-group">
													<input ng-disabled="processando_autorizacao || autorizado" id="inputDtaSaida" readonly="readonly" style="background:#FFF;cursor:pointer" type="text" class="datepicker form-control">
													<span ng-disabled="processando_autorizacao || autorizado" id="btnDtaSaida" class="input-group-addon" ><i class="fa fa-calendar"></i></span>
												</div>
											</div>
										</div>

										<div class="col-sm-3">
											<div class="form-group">
												<label class="control-label">Hora de Saída</label>
												<input ng-disabled="processando_autorizacao || autorizado" id="InputhrsSaida" type="time" class="form-control input-sm" style="width: 50%;">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-2 col-sm-2">
											<div class="form-group">
												<label class="control-label">Série</label>
												<input ng-model="NF.dados_emissao.serie_documento_fiscal" type="text" class="form-control input-sm" readonly="readonly">
											</div>
										</div>

										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">Número</label>
												<input ng-model="NF.dados_emissao.num_documento_fiscal" type="text" class="form-control input-sm" readonly="readonly">
											</div>
										</div>

										<div class="col-sm-8">
											<div class="form-group">
												<label class="control-label">Natureza da Operação</label>
												<input type="text" ng-model="NF.dados_emissao.dsc_operacao" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-12">
											<div class="form-group">
												<label class="control-label">Observações</label>
												<textarea class="form-control" ng-model="NF.informacoes_adicionais_contribuinte" ng-disabled="processando_autorizacao || autorizado" rows="5"></textarea>
											</div>
										</div>
									</div>
								</div>

								<div class="tab-pane fade in" id="emitente">
									<div class="alert" style="display:none"></div>

									<div class="row">
										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">CPF / CNPJ</label>
												<input type="text" value="{{ NF.emitente.CNPJ }}" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">I.E.</label>
												<input type="text" value="{{ NF.emitente.IE }}" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">I.E. Sub. Tributária</label>
												<input type="text" value="{{ NF.emitente.IEST }}" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label class="control-label">Regime Tributário</label> 
												<input type="text" value="{{ NF.emitente.CRT }}" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-group" data-toggle="tooltip" title="Apenas Simples Nacional">
												<label class="control-label">% Crédito</label>
												<input type="text" value="{{ NF.emitente.PercCreditoSimples }}" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-5">
											<div class="form-group">
												<label class="control-label">Razão Social</label>
												<input type="text" value="{{ NF.emitente.xNome }}" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label class="control-label">Nome Fantasia</label>
												<input type="text" value="{{ NF.emitente.xFant }}" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">CEP</label>
												<input type="text" ng-model="NF.emitente.CEP"  class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-5">
											<div class="form-group">
												<label class="control-label">Endereço</label>
												<input type="text" ng-model="NF.emitente.nme_logradouro" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-1">
											<div class="form-group">
												<label class="control-label">Número</label>
												<input type="text"  ng-model="NF.emitente.num_logradouro" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label class="control-label">Bairro</label>
												<input type="text"  ng-model="NF.emitente.nme_bairro_logradouro" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">Estado</label>
												<input type="text"  ng-model="NF.emitente.estado.uf" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label class="control-label">Cidade</label>
												<input type="text" ng-model="NF.emitente.cidade.nome" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
									</div>
								</div>

								<div class="tab-pane fade in" id="destinatario">
									<div class="alert" style="display:none"></div>
									<div class="row">
										<div class="col-sm-2">
											<div class="form-group" ng-if="NF.destinatario.tipo_cadastro == 'pj'">
												<label class="control-label">CNPJ</label>
												<input type="text" value="{{ NF.destinatario.CNPJ }}" class="form-control input-sm" readonly="readonly">
											</div>
											<div class="form-group" ng-if="NF.destinatario.tipo_cadastro == 'pf'">
												<label class="control-label">CPF</label>
												<input type="text" value="{{ NF.destinatario.CPF }}" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-2" ng-if="NF.destinatario.tipo_cadastro == 'pj'">
											<div class="form-group">
												<label class="control-label">I.E.</label>
												<input type="text" value="{{ NF.destinatario.IE }}" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-2" ng-if="NF.destinatario.tipo_cadastro == 'pj'">
											<div class="form-group">
												<label class="control-label">ID Sub. Tributária</label>
												<input type="text" value="{{ NF.destinatario.IEST }}" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-2" ng-if="NF.destinatario.tipo_cadastro == 'pj'">
											<div class="form-group">
												<label class="control-label">Inscrição Municipal</label>
												<input type="text" value="{{ NF.destinatario.IM }}" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-2" ng-if="NF.destinatario.tipo_cadastro == 'pj'">
											<div class="form-group">
												<label class="control-label">ID Estrangeiro</label>
												<input type="text" value="{{ NF.destinatario.num_registro_estrangeiro }}" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-5" ng-if="NF.destinatario.tipo_cadastro == 'pj'">
											<div class="form-group">
												<label class="control-label">Nome Fantasia</label>
												<input type="text" value="{{ NF.destinatario.xFant }}" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-5" ng-if="NF.destinatario.tipo_cadastro == 'pf'">
											<div class="form-group">
												<label class="control-label">Nome</label>
												<input type="text" value="{{ NF.destinatario.xNome }}" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label class="control-label">E-mail</label>
												<input type="text" value="{{ NF.destinatario.email }}" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">CEP</label>
												<input type="text" ng-model="NF.destinatario.CEP"   class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-5">
											<div class="form-group">
												<label class="control-label">Endereço</label>
												<input type="text" ng-model="NF.destinatario.nme_logradouro"  class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-1">
											<div class="form-group">
												<label class="control-label">Número</label>
												<input type="text" ng-model="NF.destinatario.num_logradouro"  class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label class="control-label">Bairro</label>
												<input type="text" ng-model="NF.destinatario.nme_bairro_logradouro"  class="form-control input-sm" readonly="readonly">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">Estado</label>
												<input type="text" ng-model="NF.destinatario.estado.uf" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label class="control-label">Cidade</label>
												<input type="text" ng-model="NF.destinatario.cidade.nome" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
									</div>
								</div>

								<div class="tab-pane fade in" id="transportadora">
									<div class="alert" style="display:none"></div>

									<div class="row" ng-if="!(processando_autorizacao || autorizado)">
										<div class="col-sm-10">
											<div class="form-group">
												<label class="control-label">Transportadora</label> 
												<select chosen ng-change="selTransportadora()"
												    option="lista_traportadoras"
												    ng-model="NF.transportadora.id"
												    ng-options="transportadora.id as transportadora.nome_fornecedor for transportadora in lista_traportadoras">
												</select>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">CNPJ</label>
												<input type="text" ng-model="NF.transportadora.CNPJ" class="form-control input-sm" readonly="readonly">
											</div>
										</div>

										<div class="col-sm-5">
											<div class="form-group">
												<label class="control-label">Nome Fantasia</label>
												<input type="text" ng-model="NF.transportadora.xFant" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">CEP</label>
												<input type="text" ng-model="NF.transportadora.CEP" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-5">
											<div class="form-group">
												<label class="control-label">Endereço</label>
												<input type="text" ng-model="NF.transportadora.nme_logradouro" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-1">
											<div class="form-group">
												<label class="control-label">Número</label>
												<input type="text" ng-model="NF.transportadora.num_logradouro" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label class="control-label">Bairro</label>
												<input type="text" ng-model="NF.transportadora.nme_bairro_logradouro" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">Estado</label>
												<input type="text" ng-model="NF.transportadora.estado.nome" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label class="control-label">Cidade</label>
												<input type="text" ng-model="NF.transportadora.cidade.nome" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-12 table-responsive">
											<table class="table table-hover table-striped table-condensed table-bordered">
												<caption class="text-left text-bold">Volumes</caption>
												<thead>
													<th class="text-center" width="80">Quantidade</th>
													<th>Espécie</th>
													<th>Marca</th>
													<th class="text-center" width="100">Numeração</th>
													<th class="text-right" width="100">Peso Liq. (Kg)</th>
													<th class="text-right" width="100">Peso Bruto. (Kg)</th>
													<th class="text-center" width="100" ng-if="!(processando_autorizacao || autorizado)">
														<button type="button" class="btn btn-success btn-xs" 
															ng-click="abreModalInclusaoVolume()">
															<i class="fa fa-plus-circle"></i> Incluir
														</button>
													</th>
												</thead>
												<tbody>
													<tr ng-repeat="item in NF.volumes">
														<td class="text-center">{{ item.quantidade }}</td>
														<td>{{ item.especie }}</td>
														<td>{{ item.marca }}</td>
														<td class="text-center">{{ item.numero }}</td>
														<td class="text-right">{{ item.peso_liquido }}</td>
														<td class="text-right">{{ item.peso_bruto }}</td>
														<td class="text-center" ng-if="!(processando_autorizacao || autorizado)">
															<button type="button" class="btn btn-danger btn-xs" 
																ng-click="removeVolume(item)">
																<i class="fa fa-trash-o"></i>
															</button>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>

								<div class="tab-pane fade in" id="produtos">
									<div class="table-responsive" style="overflow-x: scroll; overflow-y: scroll; width: 100%; max-height: 450px;">
										<table class="table table-bordered table-condensed table-striped table-hover" style="width: 1500px;">
											<thead>
												<tr>
													<th class="text-middle text-center" rowspan="2">EAN</th>
													<th class="text-middle" rowspan="2" width="300">Descrição</th>
													<th class="text-middle text-center" rowspan="2">NCM</th>
													<th class="text-middle text-center" rowspan="2">CST/CSOSN</th>
													<th class="text-middle text-center" rowspan="2">CFOP</th>
													<th class="text-middle text-center" rowspan="2">Un.</th> 
													<th class="text-middle text-center" rowspan="2">Qtd.</th> 
													<th class="text-middle text-center" rowspan="2" width="100">Valor Unit.</th>
													<th class="text-middle text-center" rowspan="2" width="100">Valor Total</th>
													<th class="text-middle text-center" rowspan="2" width="100">B.Calc. ICMS</th> 
													<th class="text-middle text-center" colspan="4">Valores</th>
													<th class="text-middle text-center" colspan="4">Aliquotas</th>
												</tr>
												<tr>
													<th class="text-middle text-center" width="100">ICMS</th>
													<th class="text-middle text-center" width="100">IPI</th>
													<th class="text-middle text-center" width="100">PIS</th>
													<th class="text-middle text-center" width="100">COFINS</th>
													<th class="text-middle text-center" width="100">ICMS</th>
													<th class="text-middle text-center" width="100">IPI</th>
													<th class="text-middle text-center" width="100">PIS</th>
													<th class="text-middle text-center" width="100">COFINS</th>
												</tr>
											</thead>
											<tbody>
												<tr ng-repeat="item in NF.itens">
													<td class="text-middle text-center">{{ item.prod.cEAN }}</td>
													<td class="text-middle">{{ item.prod.xProd }}</td>
													<td class="text-middle text-center">{{ item.prod.NCM }}</td>
													<td class="text-middle text-center">
														<span ng-show="(item.imposto.ICMS.CST)">{{ item.imposto.ICMS.CST }}/</span>
														{{ item.imposto.ICMS.CSOSN }}
													</td>
													<td class="text-middle text-center">
														<select chosen
															option="lista_operacao"
															ng-if="!autorizado"
															ng-model="item.Operacao.identificador"
															ng-options="ope.cod_operacao as ((item.cod_forma_aquisicao == 30) ? ope.num_cfop_mercadoria : ope.num_cfop_produto) for ope in lista_operacao">
														</select>
														<span ng-if="autorizado">{{ item.prod.CFOP }}</span>
													</td>
													<td class="text-middle text-center">
														{{ item.prod.uCom }}
													</td>
													<td class="text-middle text-center">
														{{ item.prod.qCom }}
													</td>
													<td class="text-middle text-right">
														<span>R$ {{ item.prod.vUnCom | numberFormat : 2 : ',' : '.' }}</span>
														<input type="text" class="form-control input-sm" ng-model="item.prod.vUnCom" thousands-formatter precision='{{ configuracoes.qtd_casas_decimais }}'>
													</td>
													<td class="text-middle text-right">
														R$ {{ item.prod.vProd | numberFormat : 2 : ',' : '.' }}
													</td>
													<td class="text-middle text-right">
														R$ {{ item.imposto.ICMS.vBC | numberFormat : 2 : ',' : '.' }}
													</td>
													<td class="text-middle text-right">
														R$ {{ item.imposto.ICMS.vICMS | numberFormat : 2 : ',' : '.' }}
													</td>
													<td class="text-middle text-right">
														R$ {{ item.imposto.IPI.vIPI | numberFormat : 2 : ',' : '.' }}
													</td>
													<td class="text-middle text-right">
														R$ {{ item.imposto.PIS.vPIS | numberFormat : 2 : ',' : '.' }}
													</td>
													<td class="text-middle text-right">
														R$ {{ item.imposto.COFINS.vCOFINS | numberFormat : 2 : ',' : '.' }}
													</td>
													<td class="text-middle text-center">
														{{ item.imposto.ICMS.pICMS | numberFormat : 2 : ',' : '.' }}%
													</td>
													<td class="text-middle text-center">
														{{ item.imposto.IPI.pIPI | numberFormat : 2 : ',' : '.' }}%
													</td>
													<td class="text-middle text-center">
														{{ item.imposto.PIS.pPIS | numberFormat : 2 : ',' : '.' }}%
													</td>
													<td class="text-middle text-center">
														{{ item.imposto.COFINS.pCOFINS | numberFormat : 2 : ',' : '.' }}%
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>

								<div class="tab-pane fade in" id="resumo">
									<div class="alert" style="display:none"></div>

									<div class="row">
										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">B. Cálc. ICMS</label>
												<input type="text" thousands-formatter ng-model="NF.ICMSTot.vBC" class="form-control input-sm" readonly="readonly">
											</div>
										</div>

										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">V. Total ICMS</label>
												<input type="text" thousands-formatter ng-model="NF.ICMSTot.vICMS" class="form-control input-sm" readonly="readonly">
											</div>
										</div>

										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">V. Total ICMS Deson.</label>
												<input type="text" thousands-formatter ng-model="NF.ICMSTot.vICMSDeson" class="form-control input-sm" readonly="readonly">
											</div>
										</div>

										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">B. Cálc. ICMS ST</label>
												<input type="text" thousands-formatter ng-model="NF.ICMSTot.vBCST" class="form-control input-sm" readonly="readonly">
											</div>
										</div>

										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">V. Total ICMS ST</label>
												<input type="text" thousands-formatter ng-model="NF.ICMSTot.vST" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">V. Total IPI</label>
												<input type="text" thousands-formatter ng-model="NF.ICMSTot.vIPI" class="form-control input-sm" readonly="readonly">
											</div>
										</div>

										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">V. Total COFINS</label>
												<input type="text" thousands-formatter ng-model="NF.ICMSTot.vCOFINS" class="form-control input-sm" readonly="readonly">
											</div>
										</div>

										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">V. Outros</label>
												<input type="text" thousands-formatter ng-model="NF.ICMSTot.vOutro" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">V. Total Produtos</label>
												<input type="text" thousands-formatter ng-model="NF.ICMSTot.vProd" class="form-control input-sm" readonly="readonly">
											</div>
										</div>

										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">V. Total Frete</label>
												<input type="text" thousands-formatter ng-model="NF.ICMSTot.vFrete" class="form-control input-sm" readonly="readonly">
											</div>
										</div>

										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">V. Total Seguros</label>
												<input type="text" thousands-formatter ng-model="NF.ICMSTot.vSeg" class="form-control input-sm" readonly="readonly">
											</div>
										</div>

										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">V. Total Descontos</label>
												<input type="text" thousands-formatter ng-model="NF.ICMSTot.vDesc" class="form-control input-sm" readonly="readonly">
											</div>
										</div>

										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">V. Total do II</label>
												<input type="text" thousands-formatter ng-model="NF.ICMSTot.vII" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">V. Total NF</label>
												<input type="text" thousands-formatter ng-model="NF.ICMSTot.vNF" class="form-control input-sm" readonly="readonly">
											</div>
										</div>

										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">V. Aprox. Tributos</label>
												<input type="text" thousands-formatter ng-model="NF.ICMSTot.vTotTrib" class="form-control input-sm" readonly="readonly">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel-footer clearfix">
							<div class="pull-right">
								<button type="button" id="calcularNfe" ng-if="!(processando_autorizacao || autorizado)" ng-click="calcularNfe($event,NF.dados_emissao.cod_venda,NF.dados_emissao.cod_operacao)" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde, Atualizando Informações e Recalculando Impostos" class="btn btn-sm btn-default"><i class="fa fa-refresh"></i> Atualizar Informações e Recalcular Impostos</button>
								<button type="button"  class="btn btn-sm btn-success" ng-if="!(processando_autorizacao || autorizado)" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde, Enviando..." ng-click="sendNfe($event)"><i class="fa fa-send"></i> Transmitir NF-e</button>
								<button type="button"  class="btn btn-sm btn-primary" ng-click="showDANFEModal(NF.dados_emissao)" ng-if="autorizado"><i class="fa fa-file-pdf-o"></i> Visualizar DANFE (PDF)</button>
								<a class="btn btn-sm btn-primary" href="{{ NF.dados_emissao.caminho_xml_nota_fiscal }}" target="_blank" ng-if="autorizado"><i class="fa fa-file-pdf-o"></i> Visualizar DANFE (XML)</a>
								<button type="button"  class="btn btn-sm btn-danger"  ng-if="false"><i class="fa fa-times-circle"></i> Cancelar NF-e</button>
							</div>
						</div>
					</div>

					<div class="alert alert-sistema alert-warning">
						<p>
							<strong><i class="fa fa-info-circle"></i> Atenção!</strong>
							<br/>
							Abaixo constam informações do emitente, destinatário e dos produtos, conforme os dados cadastrais.
							<br/>
							Caso alguma informação esteja incorreta, você deve realizar as alterações em seus respectivos cadastros e ao voltar a esta tela, solicitar a atualização dos dados da NF-e.
						</p>
					</div>
				</div>
			</div><!-- /main-container -->

			<!-- /Modal Processando-->
			<div class="modal fade" id="modal-calculando" style="display:none">
				<div class="modal-dialog error modal-sm">
					<div class="modal-content">
		  				<div class="modal-header"></div>
					    <div class="modal-body">
					    	<div class="row">
					    		<div class="col-sm-12">
					    			<i class='fa fa-refresh fa-spin'></i> Aguarde! Calculando Nota
								</div>
					    	</div>
					    </div>
				  	</div>
				  	<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal -->

			<!-- /Modal Processando-->
			<div class="modal fade" id="modal-volume" style="display:none">
				<div class="modal-dialog modal-md">
					<div class="modal-content">
		  				<div class="modal-header">
		  					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		  					<h4 class="modal-title">Inclusão de Volume</h4>
		  				</div>
					    <div class="modal-body">
					    	<div class="row">
					    		<div class="col-sm-2">
					    			<div class="form-group">
					    				<label class="control-label">Qtd.</label>
					    				<input type="text" class="form-control input-sm" ng-model="volume.quantidade">
					    			</div>
					    		</div>
					    		<div class="col-sm-5">
					    			<div class="form-group">
					    				<label class="control-label">Espécie</label>
					    				<input type="text" class="form-control input-sm" ng-model="volume.especie">
					    			</div>
					    		</div>
					    		<div class="col-sm-5">
					    			<div class="form-group">
					    				<label class="control-label">Marca</label>
					    				<input type="text" class="form-control input-sm" ng-model="volume.marca">
					    			</div>
					    		</div>
					    	</div>
					    	<div class="row">
					    		<div class="col-sm-2">
					    			<div class="form-group">
					    				<label class="control-label">Numeração</label>
					    				<input type="text" class="form-control input-sm" ng-model="volume.numero">
					    			</div>
					    		</div>
					    		<div class="col-sm-3">
					    			<label class="control-label">Peso Liq. (Kg)</label>
					    			<div class="form-group">
					    				<input type="text" class="form-control input-sm" ng-model="volume.peso_liquido" thousands-formatter precision="3">
					    			</div>
					    		</div>
					    		<div class="col-sm-3">
					    			<label class="control-label">Peso Bruto. (Kg)</label>
					    			<div class="form-group">
					    				<input type="text" class="form-control input-sm" ng-model="volume.peso_bruto" thousands-formatter precision="3">
					    			</div>
					    		</div>
					    	</div>
					    </div>
					    <div class="modal-footer clearfix">
					    	<div class="pull-right">
					    		<button type="button" class="btn btn-sm btn-default" ng-click="cancelaInclusaoVolume()" data-dismiss="modal">
					    			<i class="fa fa-times-circle"></i> Cancelar
					    		</button>
					    		<button type="button" class="btn btn-sm btn-success" ng-click="incluiVolume()">
					    			<i class="fa fa-plus-circle"></i> Incluir
					    		</button>
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

		<!-- Easy Modal -->
		<script src="js/eModal.js"></script>

		<!-- Modernizr -->
		<script src='js/modernizr.min.js'></script>

		<!-- Pace -->
		<script src='js/pace.min.js'></script>

		<!-- Popup Overlay -->
		<script src='js/jquery.popupoverlay.min.js'></script>

		<!-- Slimscroll -->
		<script src='js/jquery.slimscroll.min.js'></script>

		<!-- Datepicker -->
		<script src='js/bootstrap-datepicker.min.js'></script>

		<!-- Cookie -->
		<script src='js/jquery.cookie.min.js'></script>

		<!-- Endless -->
		<script src="js/endless/endless.js"></script>

		<!-- Chosen -->
		<script src='js/chosen.jquery.min.js'></script>

		<!-- Extras -->
		<script src="js/extras.js"></script>

		<!-- UnderscoreJS -->
		<script type="text/javascript" src="bower_components/underscore/underscore.js"></script>

		<!-- Moment -->
		<script src="js/moment/moment.min.js"></script>

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
			$('.datepicker').datepicker();
			$("#btnDtaEmissao").on("click", function(){ $("#inputDtaEmissao").trigger("focus"); });
			$("#btnDtaSaida").on("click", function(){ $("#inputDtaSaida").trigger("focus"); });

			$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
			var addParamModule = ['angular.chosen'] ;
		</script>
		<script src="js/app.js"></script>
		<script src="js/auto-complete/AutoComplete.js"></script>
		<script src="js/angular-services/user-service.js"></script>
		<script src="js/angular-controller/nota_fiscal-controller.js?version=<?php echo date("dmY-His", filemtime("js/angular-controller/nota_fiscal-controller.js")) ?>"></script>
		<script type="text/javascript"></script>>
		<?php include("google_analytics.php"); ?>
	</body>
</html>

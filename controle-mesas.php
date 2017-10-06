<?php
	include_once "util/login/restrito.php";
	/*restrito(array());*/
?>
<!DOCTYPE html>
<html lang="en" ng-app="HageERP">
  <head>
    <meta charset="utf-8">
    <title>WebliniaERP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <link rel="apple-touch-icon-precomposed" href="img/logo-marca-novo.png"/>
	<link rel="apple-touch-icon" href="img/logo-marca-novo.png"/>
	<meta name="apple-mobile-web-app-capable" content="yes" />

    <!-- Bootstrap core CSS -->
      <link rel='stylesheet prefetch' href='bootstrap/css/bootstrap.min.css'>

	<!-- Font Awesome -->
	<link href="css/font-awesome-4.6.2/css/font-awesome.min.css" rel="stylesheet">

	<!-- Pace -->
	<link href="css/pace.css" rel="stylesheet">

	<!-- Gritter -->
	<link href="css/gritter/jquery.gritter.css" rel="stylesheet">

	<!-- Datepicker -->
	<link href="css/datepicker.css" rel="stylesheet"/>

	<!-- Datepicker -->
	<link href="css/datepicker.css" rel="stylesheet"/>

	<!-- Timepicker -->
	<link href="css/bootstrap-timepicker.css" rel="stylesheet"/>

	<!-- Chosen -->
	<link href="css/chosen/chosen.min.css" rel="stylesheet"/>

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">
	<link href="css/animate.css" rel="stylesheet">
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


		.mesa-container {
			padding: 20px !important;
		}

		@media (max-width: 768px) {
			.mesa-container .panel-body {
				padding: 5px !important;
			}

			.mesa-container {
				padding: 5px !important;
			}

			.mesa-container .total table {
				font-size: 1.2em;
			}

			caption.mesa-caption {
				padding-left: 8px;
			}
		}

		@media (min-width: 769px){
			.mesa-container {
				padding: 15px !important;
			}
		}

		.mesa>.panel-heading {
			color: #FFF !important;
    		text-shadow: 1px 1px 1px rgba(150, 150, 150, 1);
		}

		.mesa.panel-warning {
			border-color: #F5C561 !important;
		}

		.mesa.panel-warning>.panel-heading {
			background-color: #F5E076 !important;
    		border-color: #F5C561 !important;
		}

		.mesa.panel-warning>.panel-footer {
			background-color: #FCF8E3 !important;
    		border-top: 1px solid #F5C561 !important;
		}

		.mesa.panel-success {
			border-color: #91B970 !important;
		}

		.mesa.panel-success>.panel-heading {
			background-color: #9BD483 !important;
    		border-color: #91B970 !important;
		}

		.mesa.panel-success>.panel-footer {
			background-color: #DFF0D8 !important;
    		border-top: 1px solid #91B970 !important;
		}

		.mesa .btn-xs {
			padding-top: 5px !important;
			padding-bottom: 5px !important;
		}

		.mesa-container .total {
			padding-left: 0px !important;
			padding-right: 0px !important;
		}

		.mesa-container .total table {
			margin-bottom: 0px !important;
			border-collapse: inherit;
			font-weight: bold;
		}

		caption.mesa-caption {
    		padding-bottom: 5px;
		}

		legend {
			font-size: 1.2em;
			margin-bottom: 10px !important;
		}

		.client-list {
			margin-top: 10px;
		}

		.panel.middle-frame {
	      margin-bottom: 10px;
	    }

	    .panel.middle-frame div.container {
	      min-height: 200px;
	      line-height: 200px;
	      padding-left: 0px !important;
	      padding-right: 0px !important;
	    }

	    .panel.middle-frame div.container img {
	      margin: 0 auto;
	      max-height: 100px;
	      padding-top: 10px;
	    }

	    .panel.middle-frame div.container span {
	      line-height: normal;
	      vertical-align: middle;
	      display: inline-block;
	      font-weight: bold;
	    }

	    .product-name {
	      margin-top: 10px;
	    }

	    .product-name, 
	    .product-price {
	      display: block !important;
	    }

	    .product-price {
	      font-weight: bold;
	      font-size: 1.2em;
	    }

	</style>
  </head>

  <body class="overflow-hidden" ng-controller="ControleMesasController" ng-cloak>
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

				<?php include_once('menu-modulos.php') ?>
				
			</div><!-- /sidebar-inner -->
		</aside>

		<div id="main-container">
			<div id="breadcrumb">
				<ul class="breadcrumb">
					<li><i class="fa fa-home"></i> <a href="dashboard.php">Home</a></li>
					<li class="active" ng-click="showAvaliableKitchens()">
						<i class="fa fa-table"></i> Controle de Posições
					</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix hidden-xs">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-table"></i> Controle de Posições</h3>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="mesa-container">
				<div class="alert alert-sistema" style="display:none"></div>

				<div class="panel panel-default"> <!-- SE ESTIVER NA VISUALIZAÇÃO DA MESA OU COMANDA TROCAR POR 'panel-warning' --> 
					<!-- INICIO - EXIBIR APENAS NA VISUALIZAÇÃO DE TODAS AS MESAS -->
					<div class="panel-body" ng-show="layout.mesas">
						<div class="row">
							<div ng-repeat="(index, mesa) in mesas" class="col-xs-6 col-sm-3 col-md-3 col-lg-2">
								<div class="panel panel-{{ (mesa.flg_livre) ? 'success' : 'warning' }} mesa">
									<div class="panel-heading text-center">
										<h3 class="panel-title">{{ mesa.dsc_mesa }}</h3>
									</div>
									<div class="panel-body text-center">
										<span class="unlocked text-bold {{ (!mesa.flg_livre) ? 'hide' : '' }}">LIVRE</span>
										<span class="vlr_total_mesa text-bold {{ (mesa.flg_livre) ? 'hide' : '' }}">
											R$ {{ mesa.vlr_total_mesa | numberFormat : 2 : ',' : '.' }}
										</span>
										<div class="clearfix"></div>
										<span class="qtd_comandas">{{ mesa.qtd_comandas_abertas }} Comanda(s)</span>
									</div>
									<div class="panel-footer text-center">
										<button ng-if="mesa.flg_livre" type="button" class="btn btn-xs btn-block btn-success" ng-click="abrirMesa(mesa,$index)">
											ABRIR MESA
										</button>
										<button ng-if="!mesa.flg_livre" type="button" class="btn btn-xs btn-block btn-warning" ng-click="abrirMesa(mesa,$index)">
											VISUALIZAR
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- FIM - EXIBIR APENAS NA VISUALIZAÇÃO DE TODAS AS MESAS -->

					<!-- INICIO - EXIBIR APENAS QUANDO ESTIVER VISUALIZANDO A MESA SELECIONADA -->
					<div ng-show="layout.detMesa">
						<div class="panel-heading ">
							<h3 class="panel-title">
								{{ mesaSelecionada.mesa.dsc_mesa }}
								<div class="pull-right">
									<button ng-click="changeTela('mesas')" type="button" class="btn btn-xs btn-primary">
									<i class="fa fa-chevron-circle-left fa-2 yexy" aria-hidden="true"></i> Voltar</button>

									<button ng-if="userLogged.flg_dispositivo==1" ng-click="changeTela('SelCliente')" type="button" class="btn btn-xs btn-info hidden-xs"><i class="fa fa-plus-circle"></i> Abrir Comanda</button>
									
									<button ng-if="userLogged.flg_dispositivo==1"  ng-click="changeTela('SelCliente')" type="button" class="btn btn-xs btn-info hidden-sm hidden-md hidden-lg"><i class="fa fa-plus-circle"></i> Abrir Comanda</button>
								</div>
							</h3>
						</div>

						<div class="panel-body ">
							<table class="table table-bordered table-hover mesa">
								<caption class="text-left text-bold mesa-caption">Comandas da Mesa</caption>
								<thead ng-show="mesaSelecionada.comandas.length > 0">
									<th>Cliente</th>
									<th class="text-center">Itens</th>
									<th class="text-center">Subtotal</th>
								</thead>
								<thead ng-show="mesaSelecionada.comandas.length == 0">
									<th colspan="3">Não existe nenhuma comanda aberta para esta mesa</th>
								</thead>
								<thead ng-show="mesaSelecionada.comandas == null">
									<th colspan="3" class="text-center"><i class='fa fa-refresh fa-spin'></i> Carregando comandas</th>
								</thead>
								<tbody>
									<tr ng-repeat="comanda in mesaSelecionada.comandas" style="cursor:pointer" ng-click="abrirDetalhesComanda(comanda.id_comanda)">
										<td ng-if="comanda.id_cliente != configuracao.id_cliente_movimentacao_caixa" >{{ comanda.nome_cliente }}</td>
										<td ng-if="comanda.id_cliente == configuracao.id_cliente_movimentacao_caixa" ><b>(Cliente não informado)</b></td>

										<td class="text-center">{{ comanda.qtd_total }}</td>
										<td class="text-right">R$ {{ comanda.valor_total | numberFormat:2:',':'.' }}</td>
									</tr>
								</tbody>
							</table>
						</div>

						<div class="panel-footer total ">
							<div class="row">
								<div class="col-xs-12">
									<table class="table">
										<thead>
											<tr>
												<td>Total de Comandas</td>
												<td class="text-right">{{ mesaSelecionada.comandas.length }}</td>
											</tr>
											<tr>
												<td>Total da Mesa</td>
												<td class="text-right">R$ {{ vlrTotalComanda() | numberFormat:2:',':'.' }}</td>
											</tr>
										</thead>
									</table>
								</div>
							</div>
						</div>
					</div>
					<!-- FIM - EXIBIR APENAS QUANDO ESTIVER VISUALIZANDO A MESA SELECIONADA -->

					<!-- INICIO - EXIBIR APENAS QUANDO ESTIVER VISUALIZANDO A TELA DE SELECIONAR O CLIENTE -->
					<div ng-show="layout.SelCliente">
						<div class="panel-heading">
							<h3 class="panel-title clearfix">
								Informe o Cliente
								<div class="pull-right">
									<button ng-if="!editComanda" ng-click="changeTela('detMesa')" type="button" class="btn btn-xs btn-primary">
									<i class="fa fa-chevron-circle-left fa-2 yexy" aria-hidden="true"></i> Voltar</button>
									<button ng-if="editComanda" ng-click="changeTela('detComanda',{'editComanda':false});" type="button" class="btn btn-xs btn-primary">
									<i class="fa fa-chevron-circle-left fa-2 yexy" aria-hidden="true"></i> Voltar</button>

									<button ng-click="changeTela('cadCliente')" type="button" class="btn btn-xs btn-success hidden-xs">
										<i class="fa fa-plus-circle"></i>
										Cadastrar Novo
									</button>
									<button type="button" class="btn btn-xs btn-default hidden-xs" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." ng-click="abrirComanda(configuracao.id_cliente_movimentacao_caixa,$event)">
										<i class="fa fa-times-circle"></i>
										Não Informar
									</button>
								</div>
							</h3>
						</div>

						<div class="panel-body">
							<div class="row">
								<div class="col-lg-12">
									<input ng-keyup="autoCompleteCliente(busca.cliente)" ng-model="busca.cliente" type="text" class="form-control" placeholder="Pesquisar">
								</div>
							</div>

							<div class="row client-list">
								<div class="col-lg-12">
									<table class="table">
										<tbody>
											<tr ng-repeat="item in clientes">
												<td class="text-middle">{{ item.nome | uppercase }}</td>
												<td ng-if="item.tipo_cadastro=='pf'" class="text-middle text-center hidden-xs" width="120">{{ item.cpf | cpfFormat }}</td>
												<td ng-if="item.tipo_cadastro=='pj'"  class="text-middle text-center hidden-xs" width="120">{{ item.cpf | cnpjFormat }}</td>
												<td class="text-middle">{{ item.tel_fixo }}</td>
												<td class="text-middle">{{ item.celular }}</td>
												<td class="text-middle">{{ item.endereco }}</td>
												<td class="text-middle">{{ item.numero }}</td>
												<td class="text-middle">{{ item.end_complemento }}</td>
												<td class="text-middle">{{ item.bairro }}</td>
												<td class="text-middle text-center" width="50">
													<button ng-if="!editComanda" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." ng-click="abrirComanda(item.id,$event)" type="button" class="btn btn-sm btn-info">
														<i class="fa fa-check-square-o"></i>
														<span class="hidden-xs">Selecionar</span>
													</button>
													<button ng-if="editComanda" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." ng-click="changeCliente(item.id,$event)" type="button" class="btn btn-sm btn-info">
														<i class="fa fa-check-square-o"></i>
														<span class="hidden-xs">Selecionar</span>
													</button>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>

						<div class="panel-footer clearfix hidden-sm hidden-md hidden-lg">
							<button ng-click="changeTela('cadCliente')" type="button" class="btn btn-md btn-block btn-success">
								<i class="fa fa-plus-circle"></i>
								Cadastrar Novo
							</button>
							<button ng-if="!editComanda" ng-click="abrirComanda(configuracao.id_cliente_movimentacao_caixa,$event)" type="button" class="btn btn-md btn-block btn-default">
								<i class="fa fa-times-circle"></i>
								Não Informar
							</button>
							<button ng-if="editComanda" ng-click="changeCliente(configuracao.id_cliente_movimentacao_caixa,$event)" type="button" class="btn btn-md btn-block btn-default">
								<i class="fa fa-times-circle"></i>
								Não Informar
							</button>
						</div>
					</div>
					<!-- FIM - EXIBIR APENAS QUANDO ESTIVER VISUALIZANDO A TELA DE SELECIONAR O CLIENTE -->

					<!-- INICIO - EXIBIR APENAS QUANDO ESTIVER VISUALIZANDO A TELA DE CADASTRAR O CLIENTE -->
					<div ng-show="layout.cadCliente">
						<div class="panel-heading">
							<h3 class="panel-title clearfix">Cadastro de Cliente
								<div class="pull-right">
									<button  ng-click="changeTela('SelCliente')" type="button" class="btn btn-xs btn-primary">
									<i class="fa fa-chevron-circle-left fa-2 yexy" aria-hidden="true"></i> Voltar</button>
								</div>
							</h3>
						</div>

						<div class="panel-body">
							<div class="row">
								<div class="col-sm-12 col-md-1 col-lg-1"></div>
								<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
									<div class="form-group">
										<input type="text" ng-model="new_cliente.nome" class="form-control" placeholder="Nome">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-1 col-lg-1"></div>
								<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
									<div class="form-group">
										<input type="text" ng-model="new_cliente.cpf" class="form-control" placeholder="CPF">
									</div>
								</div>

								<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
									<div class="form-group">
										<input type="text" ng-model="new_cliente.email" class="form-control" placeholder="E-mail">
									</div>
								</div>

								<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
									<div class="form-group">
										<input type="text" ng-model="new_cliente.celular" class="form-control" placeholder="Telefone/Celular">
									</div>
								</div>
							</div>
						</div>

						<div class="panel-footer clearfix hidden-sm hidden-md hidden-lg">
							<button ng-click="cadastrarCliente($event)" type="button" class="btn btn-md btn-block btn-primary">
								<i class="fa fa-save"></i>
								Salvar
							</button>
						</div>

						<div class="panel-footer clearfix hidden-xs">
							<button data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." ng-click="cadastrarCliente($event)" type="button" class="btn btn-md btn-primary pull-right">
								<i class="fa fa-save"></i>
								Salvar
							</button>
						</div>
					</div>
					<!-- FIM - EXIBIR APENAS QUANDO ESTIVER VISUALIZANDO A TELA DE CADASTRAR O CLIENTE -->

					<!-- INICIO - EXIBIR APENAS QUANDO ESTIVER VISUALIZANDO A TELA DE ITENS DA COMANDA -->
					<div ng-show="layout.detComanda">
						<div class="panel-heading">
							<h3 class="panel-title clearfix">
								Comanda <small>#{{ comandaSelecionada.comanda.id }}</small>
								<div class="pull-right">
									<button ng-click="changeTela('detMesa')" type="button" class="btn btn-xs btn-primary">
										<i class="fa fa-chevron-circle-left fa-2 yexy" aria-hidden="true"></i>
										<span class="hidden-xs">Voltar</span>
									</button>
									<button ng-if="userLogged.flg_dispositivo==1"  type="button" class="btn btn-xs btn-default" ng-click="goChangeCliente()">
										<i class="fa fa-user"></i>
										<span class="hidden-xs">Informar Cliente</span>
									</button>
									<button ng-if="userLogged.flg_dispositivo==1" type="button" class="btn btn-xs btn-info hidden-sm hidden-md hidden-lg" ng-click="changeTela('escTipoProduto')">
										<i class="fa fa-plus-circle"></i>
										Add Produto
									</button>
									<button ng-if="userLogged.flg_dispositivo==1" type="button" class="btn btn-xs btn-info hidden-xs" ng-click="openModalProdutos()">
										<i class="fa fa-plus-circle"></i>
										Adicionar Produto
									</button>
								</div>
							</h3>
						</div>

						<div class="panel-body">
							<div class="row client-list">
								<div class="col-lg-12">
									<table class="table">
										<caption ng-if="comandaSelecionada.cliente.id != configuracao.id_cliente_movimentacao_caixa" class="text-bold text-left mesa-caption">Cliente: {{ comandaSelecionada.cliente.nome }}</caption>
										<caption ng-if="comandaSelecionada.cliente.id == configuracao.id_cliente_movimentacao_caixa" class="text-bold text-left mesa-caption"><b>Cliente: (Cliente não informado)</b></caption>

										<thead>
											<th class="text-middle">Produto</th>
											<th class="text-middle text-center hidden-xs" width="200">Fabricante</th>
											<th class="text-middle text-center hidden-xs">Tamanho</th>
											<th class="text-middle text-center hidden-xs">Cor/Sabor</th>
											<th class="text-middle text-center">Qtd.</th>
											<th class="text-middle text-center">Valor</th>
											<th class="text-middle text-right hidden-xs" width="100" ng-if="userLogged.flg_dispositivo==1">Ações</th>
											<th class="text-middle text-right hidden-sm hidden-sm hidden-lg" ng-if="userLogged.flg_dispositivo==1" width="50">Ações</th>
										</thead>
										<tbody>
											<tr ng-repeat="item in comandaSelecionada.comanda.itens">
												<td class="text-middle">{{ item.nome }}</td>
												<td class="text-middle text-center hidden-xs" width="200">{{ item.nome_fabricante }}</td>
												<td class="text-middle text-center hidden-xs">{{ item.peso }}</td>
												<td class="text-middle text-center hidden-xs">{{ item.sabor }}</td>
												<td class="text-middle text-center">{{ item.qtd_total }}</td>
												<td class="text-middle text-right">R$ {{ item.vlr_venda_varejo | numberFormat:2 : ',' : '.' }}</td>
												<td class="text-middle text-right" ng-if="userLogged.flg_dispositivo==1">
													<button ng-click="selProduto(item,true)" type="button" class="btn btn-sm btn-warning">
														<i class="fa fa-edit"></i>
														<span class="hidden-xs">Alterar item</span>
													</button>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>

						<div class="panel-footer total">
							<div class="row">
								<div class="col-xs-12">
									<table class="table">
										<thead>
											<tr>
												<td>Total de Itens</td>
												<td class="text-right">{{ totalItensComanda()  }}</td>
											</tr>
											<tr>
												<td>Total Consumo</td>
												<td class="text-right">R$ {{ vlrTotalItensComanda() | numberFormat:2:',':'.' }}</td>
											</tr>
											<tr>
												<td>Taxa de Serviço ({{ configuracao.prc_taxa_servico | numberFormat:2:',':'.'}}%)</td>
												<td class="text-right">R$ {{ getValorTaxaServico() | numberFormat:2:',':'.' }}</td>
											</tr>
											<tr>
												<td>Total Comanda</td>
												<td class="text-right">R$ {{ vlrTotalItensComanda() + getValorTaxaServico() | numberFormat:2:',':'.' }}</td>
											</tr>
										</thead>
									</table>
								</div>
							</div>

							<!--<div class="row" ng-if="funcioalidadeAuthorized('fechar_comanda')">-->
							<div class="row">
								<div class="col-sm-12 col-md-12 col-lg-12 hidden-xs clearfix"> <!-- EXIBIR APENAS AO PERFIL DE CAIXA -->
									<div class="pull-right">
										<button type="button" class="btn btn-info"
											ng-click="openModalMesasTrocar(comandaSelecionada.comanda)">
											<i class="fa fa-exchange"></i>
											Trocar de Mesa
										</button>
										<button type="button" class="btn btn-danger"
											ng-click="cancelarComanda(comandaSelecionada.comanda.id)">
											<i class="fa fa-times-circle"></i>
											Cancelar Comanda
										</button>
										<a href="pdv.php?id_orcamento={{ comandaSelecionada.comanda.id }}" 
											target="_blank" 
											class="btn btn-success">
											<i class="fa fa-dollar"></i>
											Fechar Comanda
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- FIM - EXIBIR APENAS QUANDO ESTIVER VISUALIZANDO A TELA DE CADASTRAR O CLIENTE -->

					<!-- INICIO - EXIBIR APENAS QUANDO ESTIVER VISUALIZANDO A TELA DE DETALHES DO PRODUTO -->
					<div ng-show="layout.detItemComanda">
						<div class="panel-heading">
							<h3 class="panel-title">{{ produto.nome | uppercase }}</h3>
						</div>

						<div class="panel-body">
							<fieldset>
								<legend>Informe a quantidade</legend>

								<div class="row">
									<div class="col-lg-12">
										<input ng-disabled="produto.id_ordem_producao!=null" type="number" ng-model="produto.qtd" class="form-control" onKeyPress="return SomenteNumero(event);">
									</div>
								</div>

								<div class="row">
									<div class="col-lg-12">
										<label class="control-label">Observações</label>
										<textarea rows="5" class="form-control" ng-disabled="produto.id_ordem_producao!=null" ng-model="produto.observacoes"></textarea>
									</div>
								</div>
							</fieldset>
						</div>

						<div class="panel-footer">
							<div class="row">
								<div class="col-sm-12 col-md-12 col-lg-12 hidden-sm hidden-md hidden-lg"> <!-- EXIBIR APENAS AO PERFIL DE CAIXA -->
									<!--ng-if="EditProduto && funcioalidadeAuthorized('editar_item_comanda')"-->
									<button type="button" class="btn btn-primary btn-block"
										ng-disabled="produto.id_ordem_producao!=null" 
										ng-click="editItemComanda($event)" 
										
										data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde...">
										<i class="fa fa-trash-o"></i> Atualizar Item
									</button>
									<!--ng-if="EditProduto && funcioalidadeAuthorized('excluir_item_comanda')" -->
									<button type="button" class="btn btn-danger btn-block" 
										ng-click="excluirItemComanda($event)" 
										
										data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde...">
										<i class="fa fa-trash-o"></i> Excluir Item
									</button>

									<button ng-if="!EditProduto"  ng-click="incluirItemComanda($event)"  type="button" class="btn btn-primary btn-block" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..."><i class="fa fa-cart-plus "></i>&nbsp;Incluir no carrinho</button>
									<button  ng-click="cancelarProduto()"  type="button" class="btn btn-default btn-block"><i class="fa fa-ban"></i>&nbsp;Cancelar</button>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-12 col-md-12 col-lg-12 hidden-xs clearfix"> <!-- EXIBIR APENAS AO PERFIL DE CAIXA -->
									<div class="pull-right">
										<!--ng-if="EditProduto && funcioalidadeAuthorized('excluir_item_comanda')"-->
										<button type="button" class="btn btn-danger"
											ng-click="excluirItemComanda($event)" 
											
											data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde...">
											<i class="fa fa-trash-o"></i>&nbsp;Excluir Item
										</button>

										<!--ng-if="EditProduto && funcioalidadeAuthorized('editar_item_comanda')"-->
										<button type="button" class="btn btn-primary"
											ng-disabled="produto.id_ordem_producao!=null" 
											ng-click="editItemComanda($event)" 

											data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde...">
											<i class="fa fa-trash-o"></i>&nbsp;Atualizar Item
										</button>

										<button ng-if="!EditProduto" ng-click="incluirItemComanda($event)" type="button" class="btn btn-primary" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..."><i class="fa fa-trash-o"></i>&nbsp;Incluir no carrinho</button>
										<button ng-click="cancelarProduto()" type="button" class="btn btn-default"><i class="fa fa-ban"></i>&nbsp;Cancelar</button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- FIM - EXIBIR APENAS QUANDO ESTIVER VISUALIZANDO A TELA DE DETALHES DO PRODUTO -->

					<!-- INICIO - EXIBIR APENAS QUANDO ESTIVER VISUALIZANDO A TELA DE INCLUIR DO PRODUTO -->
					<div ng-show="layout.escTipoProduto">
						<div class="panel-body">
							<fieldset>
								<legend style="height: 30px;">
									Incluir Produto
									<button  ng-click="changeTela('detComanda')" type="button" class="btn btn-xs btn-primary pull-right">
									<i class="fa fa-chevron-circle-left fa-2 yexy" aria-hidden="true"></i> Voltar</button>
								</legend>

								<div class="row">
									<div class="col-xs-12">
										<button ng-click="bucaTipoProduto('categoria')" type="button" class="btn btn-lg btn-block btn-info">
											PESQUISAR POR <br/>CATEGORIA
										</button>

										<button ng-click="bucaTipoProduto('fabricante')" type="button" class="btn btn-lg btn-block btn-info">
											PESQUISAR POR <br/>FABRICANTE
										</button>

										<button ng-click="bucaTipoProduto(null)" type="button" class="btn btn-lg btn-block btn-info">
											PESQUISAR POR <br/>DESCRIÇÃO
										</button>
									</div>
								</div>
							</fieldset>
						</div>
					</div>
					<!-- FIM - EXIBIR APENAS QUANDO ESTIVER VISUALIZANDO A TELA DE INCLUIR DO PRODUTO -->

					<!-- INICIO - EXIBIR APENAS QUANDO ESTIVER VISUALIZANDO A TELA DE PESQUISA POR ALGUM TIPO -->
					<div class="panel-body" ng-show="layout.selTipoProduto">
						<fieldset>
							<legend style="height: 30px;">
								Pesquisa por {{ getTipoBuscaProduto() }} 
								<button ng-click="changeTela('escTipoProduto')" type="button" class="btn btn-xs btn-primary pull-right">
									<i class="fa fa-chevron-circle-left fa-2 yexy" aria-hidden="true"></i> Voltar
								</button>
							</legend>

							<!-- LISTA CATEGORIA -->
							<div class="row" ng-show="getTipoBuscaProduto()=='categoria'">
								<div class="col-xs-6" ng-repeat="categoria in categoriasProduto">
									<div class="panel panel-primary middle-frame" ng-click="setBuscaCategoria(categoria)">
										<div class="panel-body">
											<div class="text-center container">
												<span>{{ categoria.descricao_categoria | uppercase }}</span>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- LISTA FABRICANTE -->	
							<div class="row" ng-show="getTipoBuscaProduto()=='fabricante'">
								<div class="col-xs-6" ng-repeat="fabricante in fabricantesProduto">
									<div class="panel panel-primary middle-frame" ng-click="setBuscaFabricante(fabricante)">
										<div class="panel-body">
											<div class="text-center container">
												<span>{{ fabricante.nome_fabricante | uppercase }}</span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</fieldset>
					</div>
					<!-- FIM - EXIBIR APENAS QUANDO ESTIVER VISUALIZANDO A TELA DE PESQUISA POR ALGUM TIPO -->

					<!-- INICIO - EXIBIR APENAS QUANDO ESTIVER VISUALIZANDO A TELA DE PESQUISA DE PRODUTO -->
					<div class="panel-body" ng-show="layout.escProduto">
						<fieldset>
							<legend style="height: 30px;">
								{{ ( getTipoBuscaProduto()==null && 'PRODUTOS' || ( getTipoBuscaProduto()=='categoria' && buscaTipoProduto.categoria.descricao_categoria || buscaTipoProduto.fabricante.nome_fabricante ) ) }}
								<button ng-if="getTipoBuscaProduto() != null" ng-click="changeTela('selTipoProduto')" type="button" class="btn btn-xs btn-primary pull-right">
									<i class="fa fa-chevron-circle-left fa-2 yexy" aria-hidden="true"></i> Voltar
								</button>
								<button ng-if="getTipoBuscaProduto()==null" ng-click="changeTela('escTipoProduto')" type="button" class="btn btn-xs btn-primary pull-right">
									<i class="fa fa-chevron-circle-left fa-2 yexy" aria-hidden="true"></i> Voltar
								</button>

								
							</legend>
							

							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<input type="text" ng-keyup="autoCompleteProdutos(busca.produtos)" ng-model="busca.produtos" class="form-control" placeholder="Pesquisar">
									</div>
								</div>
							</div>
							<div class="row" ng-if="produtos.itens.length==0">
								<div  class="col-sm-12 col-md-12 col-lg-12 hidden-sm hidden-md hidden-lg text-center">
									Nenhum produto encontrato
								</div>
							</div>
							<div class="row" ng-if="produtos.itens == null">
								<div class="col-sm-12 col-md-12 col-lg-12 hidden-sm hidden-md hidden-lg text-center">
									<i class='fa fa-refresh fa-spin'></i> Carregando...
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6" ng-repeat="produto in produtos.itens">
									<div ng-click="selProduto(produto)" class="panel panel-primary middle-frame">
										<div class="panel-body">
											<div class="text-center container">
												<img pre-load-img imgpreload="img/img-preload-app.jpg" notimg="img/sem-imagem-app.png" datasrc="{{ baseUrl()+'assets/imagens/produtos/'+produto.img }}"  class="img-responsive">
												<span class="product-name">{{ produto.nome }} - {{ produto.sabor }}</span>
												<span class="product-price">R$ {{ produto.vlr_venda_varejo | numberFormat:2:',':'.' }}</span>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row" ng-if="loadingMoreProdutos">
								<div class="col-sm-12 col-md-12 col-lg-12 hidden-sm hidden-md hidden-lg"> <!-- EXIBIR APENAS AO PERFIL DE CAIXA -->
									<button type="button" class="btn btn-default btn-block">
										<i class='fa fa-refresh fa-spin'></i> Carregando...
									</button>
								</div>
							</div>

						</fieldset>
					</div>
					<!-- FIM - EXIBIR APENAS QUANDO ESTIVER VISUALIZANDO A TELA DE PESQUISA DE PRODUTO -->
				</div>
			</div>
		</div><!-- /main-container -->

		<!--MODAIS-->

		<!-- /Modal Produtos-->
		<div class="modal fade" id="list_produtos" style="display:none">
  			<div class="modal-dialog modal-xl">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Produtos</h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.produtosModal" ng-enter="loadProdutosModal(0,10)" type="text" class="form-control input-sm">

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
									<thead ng-show="(produtosModal.itens.length != 0)">
										<tr>
											<th>#</th>
											<th>Nome</th>
											<th>Fabricante</th>
											<th>Tamanho</th>
											<th>Sabor</th>
											<th>Preço</th>
											<th width="80">qtd</th>
											<th width="80"></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-if="produtosModal.itens == null">
											<th class="text-center" colspan="9" style="text-align:center">
												<i class='fa fa-refresh fa-spin'></i> Carregando... 
											</th>
										</tr>
										<tr ng-show="(produtosModal.itens.length == 0)">
											<td colspan="3">Nenhum produto encontrado</td>
										</tr>
										<tr ng-repeat="item in produtosModal.itens">
											<td>{{ item.id_produto }}</td>
											<td>{{ item.nome }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td>{{ item.peso }}</td>
											<td>{{ item.sabor }}</td>
											<td class="text-right">R$ {{ item.vlr_venda_varejo  | numberFormat : 2 : ',' : '.' }}</td>
											<td><input onKeyPress="return SomenteNumero(event);" ng-keyUp="" ng-model="item.qtd" type="text" class="form-control input-xs" width="50" /></td>
											<td>
											<button  ng-click="incluirItemComandaModal(item,$event)" class="btn btn-success btn-xs" type="button" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde...">
												<i class="fa fa-check-square-o"></i> Adicionar
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
						             <ul class="pagination pagination-xs m-top-none" ng-show="produtosModal.paginacao.length > 1">
										<li ng-repeat="item in produtosModal.paginacao" ng-class="{'active': item.current}">
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

		<div class="modal fade" id="avaliableKitchens" style="display: none;">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Cozinhas</h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-lg-12">
								<table class="table table-condensed table-hover table-bordered table-striped">
									<thead>
										<th class="text-middle text-center">#</th>
										<th>Local</th>
										<th class="text-middle text-center">ID Conexão WebSocket</th>
									</thead>
									<tbody>
										<tr ng-repeat="cozinha in allCozinhas">
											<td class="text-middle text-center">{{ cozinha.cod_cozinha }}</td>
											<td>{{ cozinha.nme_cozinha }}</td>
											<td class="text-middle">
												<span ng-if="(!cozinha.id_ws_dsk)">
													<i class="fa fa-lg fa-circle text-danger"></i>
													Desconectada
												</span>
												<span ng-if="(cozinha.id_ws_dsk)">
													<i class="fa fa-lg fa-circle text-success"
														data-toggle="tooltip" title="{{ cozinha.id_ws_dsk }}"></i>
													Conectada
												</span>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="modal-footer clearfix">
						<div class="pull-right">
							<button class="btn btn-default btn-sm" data-dismiss="modal">
								Fechar
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="changeComandaMesa" style="display: none;">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Mesas</h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-lg-12">
								<table class="table table-condensed table-hover table-bordered table-striped">
									<thead>
										<th>Mesa</th>
										<th width="90"></th>
									</thead>
									<tbody>
										<tr ng-repeat="mesa in mesas">
											<td class="text-middle">{{ mesa.dsc_mesa }}</td>
											<td class="text-middle text-center">
												<button type="button" class="btn btn-sm btn-primary"
													ng-click="trocarComandaMesa(mesa)">
													Selecionar
												</button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="modal-footer clearfix">
						<div class="pull-right">
							<button class="btn btn-default btn-sm" data-dismiss="modal">
								Fechar
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>

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

	<!-- CACHE DE IMAGENS -->
	<img src="img/sem-imagem-app.png" style="display:none" />
	<img src="img/img-preload-app.jpg" style="display:none" />

	<!-- Logout confirmation -->
	<?php include("logoutConfirm.php"); ?>

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->

	<!-- Jquery -->
	<script src="js/jquery-1.10.2.min.js"></script>

	<!-- Bootstrap -->
    <script src="bootstrap/js/bootstrap.min.js"></script>

    <!-- Gritter -->
	<script src="js/jquery.gritter.min.js"></script>

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

	<!-- Moment -->
	<script src="js/moment/moment.min.js"></script>

	<script src="js/jquery.noty.packaged.js"></script>

	<!-- ease -->
	<script src="js/jquery.ease.js"></script>

	<!-- accounting -->
	<script type="text/javascript" src="js/accounting.min.js"></script>

	<script src="bower_components/noty/js/noty/packaged/jquery.noty.packaged.min.js"></script>

	<!-- AngularJS -->
	<script type="text/javascript" src="bower_components/angular/angular.js"></script>
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
	<script src="js/angular-controller/controle-mesas_controller.js?<?php echo filemtime('js/angular-controller/controle-mesas_controller.js')?>"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/angular-strap/2.1.2/angular-strap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/angular-strap/2.1.2/angular-strap.tpl.min.js"></script>
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

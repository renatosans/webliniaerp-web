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
      <link rel='stylesheet prefetch' href='bootstrap/css/bootstrap.min.css'>

	<!-- Font Awesome -->
	<link href="css/font-awesome-4.1.0.min.css" rel="stylesheet">

	<!-- Pace -->
	<link href="css/pace.css" rel="stylesheet">

	<!-- Chosen -->
	<link href="css/chosen/chosen.min.css" rel="stylesheet"/>

	<!-- Datepicker -->
	<link href="css/datepicker.css" rel="stylesheet"/>

	<!-- Timepicker -->
	<link href="css/bootstrap-timepicker.css" rel="stylesheet"/>

	<!-- Bower Components -->	
	<link href="bower_components/noty/lib/noty.css" rel="stylesheet">

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">
	<style type="text/css">
		/*Redimencionando PopOver
		.popover-content {
            width: 200px;
        }*/
		/* Fix for Bootstrap 3 with Angular UI Bootstrap */

		.has-error-plano{
			border: 1px solid #b94a48;
			background: #E5CDCD;
		}

		.modal {
			display: block;
		}

        .notification-label-acessorios{
            display: inline-block;
            background: #ec2525;
            width: 15px;
            height: 15px;
            padding: 2px;
            color: #fff;
            font-size: 9px;
            text-align: center;
            border-radius: 50em;
            -moz-border-radius: 50em;
            -webkit-border-radius: 50em;
            position: absolute;
            margin-top: -9px;
            margin-left: -25px;
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

			#list_proodutos.modal-dialog  {width:900px;}

		}

		#list_produtos .modal-dialog  {width:70%;}

		#list_produtos .modal-content {min-height: 640px;;}*/

		tr.tr-acessorio td {
   			 background-color: rgba(80, 79, 99, 0.23);
		}
		tr.tr-tira td {
   			 background-color: rgba(154, 210, 104, 0.54);
		}

		.tr-error-estoque{
			background-color: #FFB1B1;
		}		


	</style>
  </head>

  <body class="overflow-hidden" ng-controller="PedidoPersonalizadoController" ng-cloak>

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
					 <li><i class="fa fa-tag"></i> <a href="lista_pedidos_personalizados.php">Pedidos Personalizados</a></li>
					 <li class="active"><i class="fa fa-plus-circle"></i> Novo Pedido</li>
				</ul>
			</div>

			<!-- breadcrumb -->
			<!--<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-plus-circle"></i> Novo Pedido</h3>
				</div>
			</div>-->
			<!-- /main-header -->

			<div class="padding-md">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-plus-circle"></i> Novo Pedido Personalizado {{ tela == 'receber_pagamento' && ' - Pagamento' || '' }}</h3>
					</div>
					<div class="panel-body" ng-show="tela == 'pedido'">
						<div class="form form-horizontal">
							<fieldset>
								<legend class="clearfix">
									<span class="">Dados do Cliente</span>
									<div class="pull-right">
										<button type="button" class="btn btn-xs btn-default" ng-click="selCliente()"><i class="fa fa-users"></i> Selecionar Cliente Existente</button>
										<button type="button" class="btn btn-xs btn-primary" ng-click="btnInsertCliente()"><i class="fa fa-plus-circle"></i> Cadastrar Novo Cliente</button>
									</div>
								</legend>

								<div class="novo-cliente" ng-show="cliente.acao_cliente == 'insert' || cliente.acao_cliente =='update'">
									<div class="form-group">
										<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Nome:</label> 
										<div class="col-xs-12 col-sm-8 col-md-8 col-lg-7" id="nome">
											<input type="text" class="form-control input-sm" ng-model="cliente.nome">
										</div>
									</div>

									<div class="form-group">
										<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">E-mail:</label> 
										<div class="col-xs-12 col-sm-7 col-md-7 col-lg-6" id="email">
											<input type="text" class="form-control input-sm" ng-model="cliente.email">
										</div>
									</div>

									<div class="form-group">
										<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label" style="padding-top: 0;">Telefone<br class="hidden-xs"/><span class="hidden-sm hidden-md hidden-lg"> </span>(Fixo):</label> 
										<div class="col-xs-12 col-sm-3 col-md-3 col-lg-2" id="tel_fixo">
											<input ui-mask="(99) 99999999" ng-model="cliente.tel_fixo" type="text" class="form-control input-sm">
										</div>

										<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="padding-top: 0;">Telefone<br class="hidden-xs"/><span class="hidden-sm hidden-md hidden-lg"> </span>(Celular):</label> 
										<div class="col-xs-12 col-sm-3 col-md-3 col-lg-2" id="celular">
											<input ui-mask="(99) 99999999?9" ng-model="cliente.celular" type="text" class="form-control input-sm">
										</div>
									</div>

									<div class="form-group">
										<label class="col-lg-2 control-label">Indicação?</label>
										<div class="col-lg-2" style="padding-top: 5px;">
											<label class="label-radio inline">
												<input ng-model="cliente.indicacao" value="1" type="radio" class="inline-radio">
												<span class="custom-radio"></span>
												<span>Sim</span>
											</label>

											<label class="label-radio inline">
												<input ng-model="cliente.indicacao" value="0" type="radio" class="inline-radio">
												<span class="custom-radio"></span>
												<span>Não</span>
											</label>
										</div>

										<label class="col-lg-1 control-label">Como encontrou?</label>
										<div class="col-lg-3" style="padding-top: 5px;">
											<select class="form-control" ng-model="cliente.id_como_encontrou" ng-options="a.id as a.nome for a in comoencontrou">	
												<option value=""></option>
											</select>
										</div>
									</div>
								</div>
							</fieldset>

							<fieldset id="fieldset-item-pedido">
								<legend>Itens do Pedido</legend>
								<div class="form-group">
									<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Tipo Base?</label> 
									<div class="col-xs-12 col-sm-12 col-md-4 col-lg-3" style="margin-top: 5px;">
										<label class="label-radio inline">
											<input ng-model="pedido.tipo_base" ng-change="setTipoBase()" value="quadrada" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Quadrada</span>
										</label>

										<label class="label-radio inline">
											<input ng-model="pedido.tipo_base" ng-change="setTipoBase()" value="redonda" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Redonda</span>
										</label>
									</div>
									<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2  control-label">Modelo Base?</label> 
									<div class="col-xs-12 col-sm-12 col-md-5 col-lg-4" style="margin-top: 5px;">
										<label class="label-radio inline" >
											<input ng-model="pedido.modelo_base" ng-change="setTipoBase()" value="linha_lika_shoes" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Linha Lika Shoes</span>
										</label>

										<label class="label-radio inline">
											<input ng-model="pedido.modelo_base" ng-change="setTipoBase()" value="linha_personalizada" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Linha Personalizada</span>
										</label>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Cor das Bases:</label> 
									<div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
										<div class="form-group" id="id_cor_base">
											<select chosen ng-change="montarGradePedido()"
										    option="chosen_cor_base"
										    ng-model="pedido.id_cor_base"
										    ng-options="cor_base.id as cor_base.nome_cor for cor_base in chosen_cor_base">
											</select>
										</div>
									</div>
								
									<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Cor das Tiras<br class="hidden-xs"/><span class="hidden-sm hidden-md hidden-lg"> </span>(Feminina):</label> 
									<div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
										<div class="form-group" id="chosen_cor_tira">
											<select chosen ng-change="montarGradePedido()"
										    option="chosen_cor_tira"
										    ng-model="pedido.id_cor_tira_feminina"
										    ng-options="cor_tira.id as cor_tira.nome_cor for cor_tira in chosen_cor_tira">
											</select>
										</div>
									</div>

									<label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">Cor das Tiras<br class="hidden-xs"/><span class="hidden-sm hidden-md hidden-lg"> </span>(Masculina):</label> 
									<div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
										<select chosen ng-change="montarGradePedido()"
										    option="chosen_cor_tira"
										    ng-model="pedido.id_cor_tira_masculina"
										    ng-options="cor_tira.id as cor_tira.nome_cor for cor_tira in chosen_cor_tira">
										</select>
									</div>
								</div>

								<div class="form-group">
									<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label" >Modelo da Estampa:</label> 
									<div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
										<div class="input-group">
											<input ng-click="openModalModelosEstampas()" ng-model="pedido.modeloEstampa.nome_produto" type="text" class="form-control"  readonly="readonly" style="cursor: pointer;" />
											<span class="input-group-btn">
												<button ng-click="openModalModelosEstampas()"  type="button" class="btn"><i class="fa fa-search"></i></button>
											</span>
										</div>
									</div>


									<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Cores da Estampa:</label> 
									<div class="col-xs-12 col-sm-12 col-md-5 col-lg-4">
										<table class="table table-bordered table-hover table-striped table-condensed">
											<thead>
												<th>Cor</th>
												<th>Local</th>
												<th width="20">
													<button ng-click="openModalCoresEstampa()" type="button" class="btn btn-xs btn-success"><i class="fa fa-plus-circle"></i></button>
												</th>
											</thead>
                                            <tr ng-show="pedido.coresEstampa.length == 0">
                                                <td class="text-center" colspan="3">
                                                    Nenhuma Cor selecionada
                                                </td>
                                            </tr>
											<tbody>
												<tr ng-repeat="item in pedido.coresEstampa">
													<td class="text-middle">{{ item.nome_cor }}</td>
													<td>
														<input type="text" ng-model="item.dsc_local" class="form-control input-xs">
													</td>
													<td>
														<button type="button" ng-click="deleteItemCorEstampa($index)" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></button>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Brinde?:</label> 
									<div class="col-xs-12 col-sm-12 col-md-5 col-lg-4">
										<label class="label-radio inline">
											<input ng-model="pedido.flg_brinde" value="1" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Sim</span>
										</label>

										<label class="label-radio inline">
											<input ng-model="pedido.flg_brinde" value="0" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Não</span>
										</label>
									</div>
								</div>
								<div class="form-group" ng-show="!(gradeInfantil.length > 0 || gradeAdulto.length > 0)">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
										<span class="alert alert-warning">
											Informe os campos acima para configurar o pedido
										</span>
									</div>
								</div>

								<div class="form-group" ng-show="(gradeInfantil.length > 0 || gradeAdulto.length > 0)">
									<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Pedido:</label> 
									<div class="col-xs-12 col-sm-12 col-md-5 col-lg-4" ng-show="(gradeInfantil.length > 0)" jheizer="">
										<table class="table table-bordered table-hover table-striped table-condensed">
											<caption class="text-bold">Infantil</caption>
											<thead>
												<th class="text-center">Numeração</th>
												<th class="text-center danger" colspan="2">Feminino</th>
												<th class="text-center info" colspan="2">Masculino</th>
											</thead>
											<tbody>
												<tr bs-tooltip ng-repeat="item in gradeInfantil" ng-show="(item.fem_valid || item.mas_valid)">
													<td class="text-middle text-center">{{ item.nome_tamanho }}</td>
													<td class="danger" width="65">
														<input ng-model="item.fem_qtd" onKeyPress="return SomenteNumero(event);" ng-focus="hidePopOver()" type="text" ng-disabled="!item.fem_valid" class="form-control input-xs text-center">
													</td>
													<td class="text-center text-middle danger" width="70">
														<button class="btn btn-xs btn-default" role="button" ng-disabled="!item.fem_valid"  ng-click="popoverAcessorios(item.acessoriosFemininos,$event,$index,'feminino-infantil')" data-popover-visible="0" id="popover-acessorio-feminino-infantil-{{ $index }}">
														<i class="fa fa-tags"></i>
                                                        <span ng-if="item.acessoriosFemininos != null && item.acessoriosFemininos.length > 0" class="notification-label-acessorios">{{ qtdtotalAcessorios(item.acessoriosFemininos) }}</span>
														</button>
														<button ng-click="openModalAcessorios(item,'feminino')" class="btn btn-xs btn-success" ng-disabled="!item.fem_valid" data-toggle="tooltip" title="Incluir Acessório" ng-click="openModal('list_produtos')">
															<i class="fa fa-plus-square"></i>
														</button>
													</td>
													<td class="info" width="65">
														<input ng-model="item.mas_qtd" onKeyPress="return SomenteNumero(event);" ng-focus="hidePopOver()" type="text" ng-disabled="!item.mas_valid" class="form-control input-xs text-center">
													</td>
													<td class="text-center text-middle info" width="70">
														<button class="btn btn-xs btn-default" role="button" ng-disabled="!item.mas_valid"  ng-click="popoverAcessorios(item.acessoriosMasculinos,$event,$index,'masculino-infantil')" data-popover-visible="0" id="popover-acessorio-masculino-infantil-{{ $index }}">
														<i class="fa fa-tags"></i>
                                                        <span ng-if="item.acessoriosMasculinos != null && item.acessoriosMasculinos.length > 0" class="notification-label-acessorios">{{ qtdtotalAcessorios(item.acessoriosMasculinos) }}</span>
														</button>
														<button ng-click="openModalAcessorios(item, 'masculino')" class="btn btn-xs btn-success" ng-disabled="!item.mas_valid" data-toggle="tooltip" title="Incluir Acessório" ng-click="openModal('list_produtos')">
															<i class="fa fa-plus-square"></i>
														</button>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								
									<div class="col-xs-12 col-sm-12 col-md-5 col-lg-4">
										<table class="table table-bordered table-hover table-striped table-condensed">
											<caption class="text-bold">Adulto</caption>
											<thead>
												<th class="text-center">Numeração</th>
												<th class="text-center danger" colspan="2">Feminino</th>
												<th class="text-center info" colspan="2">Masculino</th>
											</thead>
											<tbody>
												<tr bs-tooltip ng-repeat="item in gradeAdulto" bs-popover  ng-show="(item.fem_valid || item.mas_valid)">
													<td class="text-middle text-center">{{ item.nome_tamanho }}</td>
													<td class="danger" width="65">
														<input type="text" ng-model="item.fem_qtd" onKeyPress="return SomenteNumero(event);" ng-focus="hidePopOver()" ng-disabled="!item.fem_valid" class="form-control input-xs text-center">
													</td>
													<td class="text-center text-middle danger" width="70">
														<button class="btn btn-xs btn-default" role="button" ng-disabled="!item.fem_valid"  ng-click="popoverAcessorios(item.acessoriosFemininos,$event,$index,'feminino-adulto')" data-popover-visible="0" id="popover-acessorio-feminino-adulto-{{ $index }}">
															<i class="fa fa-tags"></i>
                                                            <span ng-if="item.acessoriosFemininos != null && item.acessoriosFemininos.length > 0" class="notification-label-acessorios">{{ qtdtotalAcessorios(item.acessoriosFemininos) }}</span>
														</button>
														<button ng-click="openModalAcessorios(item,'feminino')" class="btn btn-xs btn-success" ng-disabled="!item.fem_valid" data-toggle="tooltip" title="Incluir Acessório" ng-click="openModal('list_produtos')">
															<i class="fa fa-plus-square"></i>
														</button>
													</td>
													<td class="info" width="65">
														<input type="text"  ng-model="item.mas_qtd" onKeyPress="return SomenteNumero(event);" ng-focus="hidePopOver()" ng-disabled="!item.mas_valid" class="form-control input-xs text-center">
													</td>
													<td class="text-center text-middle info" width="70">
														<button class="btn btn-xs btn-default" role="button" ng-disabled="!item.mas_valid"  ng-click="popoverAcessorios(item.acessoriosMasculinos,$event,$index,'masculino-adulto')" data-popover-visible="0" id="popover-acessorio-masculino-adulto-{{ $index }}">
														<i class="fa fa-tags"></i>
                                                        <span ng-if="item.acessoriosMasculinos != null && item.acessoriosMasculinos.length > 0" class="notification-label-acessorios">{{ qtdtotalAcessorios(item.acessoriosMasculinos) }}</span>
														</button>
														<button ng-click="openModalAcessorios(item,'masculino')" class="btn btn-xs btn-success" ng-disabled="!item.mas_valid" data-toggle="tooltip" title="Incluir Acessório" ng-click="openModal('list_produtos')">
															<i class="fa fa-plus-square"></i>
														</button>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>

								<div class="form-group">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 clearfix">
										<div class="pull-right">
											<button ng-click="inserirPedido()" id="inserir-pedido" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." type="button" class="btn btn-sm btn-info"><i class="fa fa-plus-circle"></i> Incluir no Pedido</button>
										</div>
									</div>
								</div>
							</fieldset>

							<fieldset id="fieldset-resumo-pedido">
								<legend>Resumo do Pedido</legend>

								<div class="form-group">

									<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label" id="label-dta-venda">Data do Pedido:</label> 
									<div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
										<div class="input-group" id="form-dta-venda">
											<input  style="background:#FFF;cursor:pointer" type="text" id="dtaVenda" class="datepicker form-control text-center" value="<?php echo date("d/m/Y"); ?>">
											<span class="input-group-addon" id="cld_dtaVenda"><i class="fa fa-calendar"></i></span>
										</div>
									</div>

									<label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label" id="label-dta-entrega">Data de Entrega:</label> 
									<div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
										<div class="input-group" id="form-dta-entrega">
											<input  style="background:#FFF;cursor:pointer" type="text" id="dtaEntrega" class="datepicker form-control text-center">
											<span class="input-group-addon" id="cld_dtaEntrega"><i class="fa fa-calendar"></i></span>
										</div>
									</div>

									<label class="col-lg-2 control-label">Canal de Vendas</label>
									<div class="col-lg-2" style="padding-top: 5px;">
										<label class="label-radio inline">
											<input ng-model="pedido.canal_venda" value="Loja" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Loja</span>
										</label>

										<label class="label-radio inline">
											<input ng-model="pedido.canal_venda" value="Internet" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Internet</span>
										</label>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>
									<div class="col-xs-12 col-sm-9 col-md-8 col-lg-10">
										<div class="alert alert-item-pedido" style="display:none">
											
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Desconto(R$)</label>
									<div class="col-xs-12 col-sm-9 col-md-8 col-lg-2">
										<input ng-model="pedido.desconto" type="text" thousands-formatter class="form-control input-sm">
									</div>
									<div class="col-xs-12 col-sm-9 col-md-8 col-lg-2">
										<button type="button" ng-click="calcularDesconto()" class="btn btn-sm btn-info"></i>Aplicar</button>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Discriminação</label>
									<div class="col-xs-12 col-sm-9 col-md-8 col-lg-10">
										<table class="table table-bordered table-hover table-striped table-condensed">
											<thead ng-show="length(carrinhoPedido) > 0">
												<th class="text-center">Qtd.</th>
												<th class="text-center">Produto</th>
												<th class="text-center">Vlr. Unitário</th>
												<th class="text-center">Total</th>
                                                <th class="text-center" width="80">Opções</th>
											</thead>
											<tr ng-show="length(carrinhoPedido) == 0">
												<td class="text-center">
													Nenhum Item Adicionado ao Pedido 
												</td>
											</tr>
											<tbody>
												<tr ng-repeat="(key, item)  in carrinhoPedido" bs-tooltip>
													<td class="text-center">{{ item.qtd }}</td>
													<td>
														{{ item.nome }}
														<i ng-if="item.flg_brinde == 1"  title="Produto dado como brinde" data-toggle="tooltip" class="fa fa-gift fa-lg" style="float: right;color:rgba(128, 0, 82, 0.72);"></i>
													</td>
													<td class="text-right" ng-if="item.flg_brinde == 0">{{ item.valor_real_item | numberFormat:2:',':'.' }}</td>
													<td class="text-right" ng-if="item.flg_brinde == 0">{{ (item.qtd * item.valor_real_item) | numberFormat:2:',':'.' }}</td>
													<td class="text-right" ng-if="item.flg_brinde == 1">{{ 0 | numberFormat:2:',':'.' }}</td>
													<td class="text-right" ng-if="item.flg_brinde == 1">{{ 0 | numberFormat:2:',':'.' }}</td>
                                                    <td align="center">
                                                        <button type="button" ng-click="editarItemPedido(item)" tooltip="Detalhes" data-toggle="tooltip" class="btn btn-xs btn-warning" title="editar">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <button type="button" ng-click="deleteItemPedido(key)" tooltip="Detalhes" data-toggle="tooltip"  class="btn btn-xs btn-danger delete" title="Excluir">
                                                            <i class="fa fa-trash-o"></i>
                                                        </button>
                                                    </td>
												</tr>
												<tr ng-show="length(carrinhoPedido) > 0">
													<td class="text-center" >{{ qtdtotalItensPedido() }}</td>
													<td style="border: none;" colspan="2" class="text-right">
														Total do Pedido
													</td>
													<td style="border: none;" ng-bind-html="totalPedido()" class="text-right"></td>
                                                    <td style="border: none;"></td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>

                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Observações:</label> 
                                    <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                                        <textarea class="form-control" rows="4" ng-model="pedido.observacao"></textarea>
                                    </div>
                                </div>

							</fieldset>
						</div>
					</div>
                    <div class="panel-body" ng-show="tela == 'receber_pagamento'">
                    	<div class="row">
							<div class="col-sm-11">
								<div class="input-group">
						            <input ng-model="busca.text" type="text" class="form-control input-sm" ng-enter="load(0,10)">
						            <div class="input-group-btn">
						            	<button ng-click="load(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div>
						        </div>
							</div>
							<div class="col-sm-1">
								<button type="button" class="btn btn-sm btn-default" ng-click="resetFilter()">Limpar</button>
							</div>
						</div>
						<br>
                            <div class="alert alert-pagamento" style="display:none"></div>
                            <div class="row">
                                <div class="col-sm-9">
                                <div class="row" ng-show="pagamento_fulso">
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
                                            <div style="font-weight: bold;font-size: 15px;" ng-show="cliente.vlr_saldo_devedor>0">
                                                <span style="color:#000">Saldo Devedor :</span> <span style="color:green">R$ {{ cliente.vlr_saldo_devedor | numberFormat:2:',':'.' }}</span>
                                            </div>
                                            <div style="font-weight: bold;font-size: 15px;" ng-show="cliente.vlr_saldo_devedor<0">
                                                <span style="color:#000">Saldo Devedor :</span> <span style="color:red">R$ {{ cliente.vlr_saldo_devedor | numberFormat:2:',':'.' }}</span>
                                            </div>
                                            <div style="font-weight: bold;font-size: 15px;" ng-show="cliente.vlr_saldo_devedor==0">
                                                <span style="color:#000">Saldo Devedor :</span> <span style="color:blue">R$ {{ cliente.vlr_saldo_devedor | numberFormat:2:',':'.' }}</span>
                                            </div>
                                            </div>
                                        </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6" id="pagamento_forma_pagamento">
                                        <label class="control-label">Forma de Pagamento</label>
                                        <select ng-model="pagamento.id_forma_pagamento" ng-change="selectChange()" class="form-control input-sm">
                                            <option ng-show="pagamento.id_forma_pagamento != null" value=""></option>
                                            <option ng-repeat="item in formas_pagamento"  value="{{ item.id }}">{{ item.nome }}</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6" id="pagamento_valor" ng-show="pagamento.id_forma_pagamento == 7" >
                                        <label class="control-label">Vale troca</label>
                                        <div class="input-group">
                                            <input ng-click="showValeTroca()" thousands-formatter type="text" class="form-control input-sm" ng-model="pagamento.valor" readonly="readonly" style="cursor: pointer;" />
                                            <span class="input-group-btn">
                                                <button ng-click="showValeTroca()" type="button" class="btn btn-info btn-sm"><i class="fa fa-exchange"></i></button>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-sm-2" id="pagamento_id_banco" ng-show="pagamento.id_forma_pagamento == 8">
                                        <div class="form-group" >
                                            <label class="control-label">Banco</label>
                                            <select ng-model="pagamento.id_banco" class="form-control">
                                                <option ng-repeat="banco in bancos" value="{{ banco.id }}">{{ banco.nome }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-2" id="pagamento_agencia_transferencia" ng-show="pagamento.id_forma_pagamento == 8">
                                        <label class="control-label">Agência</label>
                                        <div class="form-group ">
                                                <input ng-model="pagamento.agencia_transferencia"  type="text" class="form-control input-sm" />
                                        </div>
                                    </div>

                                    <div class="col-sm-2" id="pagamento_conta_transferencia" ng-show="pagamento.id_forma_pagamento == 8">
                                        <label class="control-label">Conta</label>
                                        <div class="form-group ">
                                                <input ng-model="pagamento.conta_transferencia"  type="text" class="form-control input-sm" />
                                        </div>
                                    </div>

                                    <div class="col-sm-6" id="pagamento_valor" ng-show="pagamento.id_forma_pagamento != 7 && pagamento.id_forma_pagamento != 8">
                                        <label class="control-label">Valor</label>
                                        <div class="form-group ">
                                                <input ng-disabled="pagamento.id_forma_pagamento == 2 || pagamento.id_forma_pagamento == 4" ng-model="pagamento.valor" thousands-formatter type="text" class="form-control input-sm" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6" id="pagamento_maquineta" ng-show="pagamento.id_forma_pagamento == 5 || pagamento.id_forma_pagamento == 6 ">
                                        <label class="control-label">Maquineta</label>
                                        <select ng-model="pagamento.id_maquineta" class="form-control input-sm">
                                            <option ng-repeat="item in maquinetas" value="{{ item.id_maquineta }}">#{{ item.id_maquineta }} - {{ item.dsc_conta_bancaria }}</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6" id="numero_parcelas" ng-show="pagamento.id_forma_pagamento == 6 || pagamento.id_forma_pagamento == 2 || pagamento.id_forma_pagamento == 4">
                                        <label class="control-label">parcelas</label>
                                        <div class="form-group ">
                                                <input ng-blur="pushCheques()" ng-focus="qtdCheque()" ng-model="pagamento.parcelas" type="text" class="form-control input-sm" >
                                        </div>
                                    </div>
                                    <div class="col-sm-4" id="proprietario_conta_transferencia" ng-show="pagamento.id_forma_pagamento == 8">
                                        <label class="control-label">Proprietário</label>
                                        <div class="form-group ">
                                                <input ng-model="pagamento.proprietario_conta_transferencia" type="text" class="form-control input-sm" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4" id="pagamento_id_conta_transferencia_destino" ng-show="pagamento.id_forma_pagamento == 8 ">
                                        <label class="control-label">Conta de Destino</label>
                                        <select ng-model="pagamento.id_conta_transferencia_destino" class="form-control input-sm">
                                            <option ng-repeat="item in contas" value="{{ item.id }}">#{{ item.id }} - {{ item.dsc_conta_bancaria }}</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4" id="pagamento_valor" ng-show="pagamento.id_forma_pagamento == 8">
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
                                                <div class="col-sm-6" ng-show="cheques.length > 1">
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
                                                <div class="col-sm-6" ng-show="boletos.length > 1">
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
                                        <tr ng-show="(recebidos.length == 0)">
                                            <td colspan="2">Não há nenhum pagamento recebido</td>
                                        </tr>
                                        <tr ng-repeat="item in recebidos">
                                            <td ng-show="item.id_forma_pagamento != 6 && item.id_forma_pagamento != 2 ">{{ item.forma_pagamento  }} <strong class="pull-right">R$ {{ item.valor | numberFormat:2:',':'.' }}</strong></td>
                                            <td ng-show="item.id_forma_pagamento == 6">C/C em {{item.parcelas}}x <strong class="pull-right">R$ {{ item.valor | numberFormat:2:',':'.' }}</strong></td>
                                            <td ng-show="item.id_forma_pagamento == 2">Cheque em {{ cheques.length }}x <strong class="pull-right">R$ {{ item.valor | numberFormat:2:',':'.' }}</strong></td>
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
                                            <td colspan="2" ng-show="total_pg <= vlrTotalCompra">
                                                Total a Receber <strong class="pull-right">R$ {{ vlrTotalCompra - total_pg | numberFormat:2:',':'.' }}</strong>
                                            </td>
                                            <td colspan="2" ng-show="total_pg > vlrTotalCompra" >
                                                Total a Receber <strong class="pull-right">R$ 0,00</strong>
                                            </td>
                                        </tr>
                                        <tr ng-show="modo_venda == 'pdv'">
                                            <td colspan="2">
                                                Troco <strong class="pull-right">R$ {{ troco | numberFormat:2:',':'.' }}</strong>
                                            </td>
                                        </tr>
                                        <tr ng-show="modo_venda == 'est' && (total_pg > vlrTotalCompra)">
                                            <td colspan="2">
                                                Troco sugerido<strong class="pull-right">R$ {{ ((vlrTotalCompra - total_pg) * (-1)) | numberFormat:2:',':'.' }}</strong>
                                            </td>
                                        </tr>
                                        <tr ng-show="modo_venda == 'est' && (total_pg > vlrTotalCompra)">
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
                    </div><!-- /panel -->
					<div class="panel-footer clearfix">
						<div class="pull-right" ng-show="tela=='pedido'">
                            <button type="button" class="btn btn-primary" ng-disabled="vlrTotalCompra <= 0" ng-click="telaPagamento()">
                                 <i class="fa fa-money"></i> Receber
                            </button>
							<button data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde" type="button" ng-click="salvar()" class="btn btn-success" id="btn-salvar"><i class="fa fa-save"></i> Salvar Pedido</button>
						</div>
                          <div class="pull-right" ng-show="tela=='receber_pagamento'">
                                <button type="button" class="btn btn-warning" ng-disabled="length(carrinhoPedido) == 0"  ng-click="cancelarPagamento()"><i class="fa fa-times-circle"></i> Cancelar Pagamento</button>
                                <button id ng-disabled="total_pg == 0 || total_pg < vlrTotalCompra" data-loading-text="Aguarde ... " ng-click="salvar()" type="submit" id="btn-salvar" class="btn btn-success ">
                                    <i class="fa fa-save"></i> Salvar Pedido/Pagamento
                                </button>
                                                
                          </div>
					</div>
				</div>
			</div>
		</div>
		<!-- /main-container -->

        <!-- /Modal Cores de Estampa-->
        <div class="modal fade" id="modal-cor-estampa" style="display:none">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4>Cores Estampa</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input ng-model="busca.coresEstampa" ng-enter="loadCoresEstampa(0,10)" type="text" class="form-control input-sm">

                                    <div class="input-group-btn">
                                        <button tabindex="-1" class="btn btn-sm btn-primary" type="button"
                                            ng-click="loadCoresEstampa(0,10)">
                                            <i class="fa fa-search"></i> Buscar
                                        </button>
                                    </div> <!-- /input-group-btn -->
                                </div> <!-- /input-group -->
                            </div><!-- /.col -->
                        </div>

                        <br>

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-condensed table-striped table-hover">
                                    <thead ng-show="(coresEstampa.length != 0)">
                                        <tr>
                                            <th>#</th>
                                            <th>Nome</th>
                                            <th width="200">Local</th>
                                            <th width="80"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-show="(coresEstampa.length == 0)">
                                            <td colspan="3" class="text-center"><i class='fa fa-refresh fa-spin'></i> Carregando...</td>
                                        </tr>
                                        <tr ng-show="(coresEstampa == null)">
                                            <td colspan="3">Não a resultados para a busca</td>
                                        </tr>
                                        <tr ng-repeat="item in coresEstampa.cores">
                                            <td>{{ item.id }}</td>
                                            <td>{{ item.nome_cor }}</td>
                                            <td>
                                                <input type="text" ng-model="item.dsc_local" class="form-control input-xs">
                                            </td>
                                            <td>
                                            <button ng-show="!existsCorEstampa(item)"  ng-click="addCorEstampa(item)"  class="btn btn-success btn-xs" type="button">
                                                <i class="fa fa-check-square-o"></i> Selecionar
                                            </button>
                                            <button ng-show="existsCorEstampa(item)" ng-disabled="true" class="btn btn-primary btn-xs" type="button">
                                                <i class="fa fa-check-circle-o"></i> Selecionado
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
                                     <ul class="pagination pagination-xs m-top-none" ng-show="coresEstampa.paginacao.length > 1">
                                        <li ng-repeat="item in coresEstampa.paginacao" ng-class="{'active': item.current}">
                                            <a href="" ng-click="loadCoresEstampa(item.offset,item.limit)">{{ item.index }}</a>
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

		<!-- /Modal Acessorios-->
		<div class="modal fade" id="modal-acessorios" style="display:none">
  			<div class="modal-dialog modal-lg">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Acessórios</h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.acessorios" ng-enter="loadAcessorios(0,10)" type="text" class="form-control input-sm">

						            <div class="input-group-btn">
						            	<button tabindex="-1" class="btn btn-sm btn-primary" type="button"
						            		ng-click="loadAcessorios(0,10)">
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
									<thead ng-show="(acessorios.length != 0)">
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
										<tr ng-show="(acessorios.length == 0)">
											<td colspan="3" class="text-center"><i class='fa fa-refresh fa-spin'></i> Carregando...</td>
										</tr>
										<tr ng-show="(acessorios == null)">
											<td colspan="3">Não a resultados para a busca</td>
										</tr>
										<tr ng-repeat="item in acessorios.acessorios">
											<td>{{ item.id_produto }}</td>
											<td>{{ item.nome_produto }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td>{{ item.qtd_real_estoque }}</td>
											<td>{{ item.peso }}</td>
											<td>{{ item.sabor }}</td>
											<td><input ng-keyUp="" onKeyPress="return SomenteNumero(event);" ng-model="item.qtd" type="text" class="form-control input-xs" width="50" /></td>
											<td>
											<button ng-show="!existsAcessorio(item)" ng-disabled="item.qtd < 0 || item.qtd  == null || item.qtd  == undefined || existsAcessorio(item)"  ng-click="addAcessorio(item)"  class="btn btn-success btn-xs" type="button">
												<i class="fa fa-check-square-o"></i> Selecionar
											</button>
                                            <button ng-show="existsAcessorio(item)" ng-disabled="true" class="btn btn-primary btn-xs" type="button">
                                                <i class="fa fa-check-circle-o"></i> Selecionado
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
						             <ul class="pagination pagination-xs m-top-none" ng-show="acessorios.paginacao.length > 1">
										<li ng-repeat="item in acessorios.paginacao" ng-class="{'active': item.current}">
											<a href="" ng-click="loadAcessorios(item.offset,item.limit)">{{ item.index }}</a>
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
                                    <tr ng-show="clientes != false && (clientes.length <= 0 || clientes == null)">
                                        <th class="text-center" colspan="9" style="text-align:center"><strong>Carregando</strong><img src="assets/imagens/progresso_venda.gif"></th>
                                    </tr>
                                    <tr ng-show="clientes == false">
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

		<!-- /Modal Pedido-->
		<div class="modal fade" id="modal-pedido" style="display:none">
  			<div class="modal-dialog modal-lg">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Pedido</h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="alert alert-produtos" style="display:none"></div>
						   		<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(carrinhoPedido.length != 0)">
										<tr>
											<th class="text-center">Nome</th>
											<th class="text-center">Qtd.</th>
											<th class="text-center">Valor</th>
											<th width="120" class="text-center">Total</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in carrinhoPedido">
											<td>{{ item.nome }}</td>
											<td class="text-center">{{ item.qtd }}</td>
											<td class="text-right">{{ item.valor_real_item | numberFormat:2:',':'.' }}</td>
											<td class="text-right">{{ (item.qtd * item.valor_real_item) | numberFormat:2:',':'.' }}</td>
										</tr>
										<tr>
											<td colspan="3" class="text-right">
												Total do Pedido
											</td>
											<td ng-bind-html="totalPedido()" class="text-right"></td>
                                            <td></td>
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

		  <!-- /Modal Cores de Estampa-->
        <div class="modal fade" id="modal-modelos-estampas" style="display:none">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4>Modelo de Estampas</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input ng-model="busca.modeloEstampa" ng-enter="loadModeloEstampas(0,10)" type="text" class="form-control input-sm">

                                    <div class="input-group-btn">
                                        <button tabindex="-1" class="btn btn-sm btn-primary" type="button"
                                            ng-click="loadModeloEstampas(0,10)">
                                            <i class="fa fa-search"></i> Buscar
                                        </button>
                                    </div> <!-- /input-group-btn -->
                                </div> <!-- /input-group -->
                            </div><!-- /.col -->
                        </div>

                        <br>

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-condensed table-striped table-hover">
                                    <thead ng-show="(modeloEstampas.itens.length != 0)">
                                        <tr>
                                            <th width="100">#</th>
                                            <th>Nome</th>
                                            <th width="80"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-show="(modeloEstampas.itens.length == 0)">
                                            <td colspan="3" class="text-center"><i class='fa fa-refresh fa-spin'></i> Carregando...</td>
                                        </tr>
                                        <tr ng-show="(modeloEstampas.itens == null)">
                                            <td colspan="3">Não a resultados para a busca</td>
                                        </tr>
                                        <tr ng-repeat="item in modeloEstampas.itens">
                                            <td>{{ item.id_produto }}</td>
                                            <td>
	                                        	  <a href="" init-popover container="#modal-modelos-estampas" content='<img width="100" src="{{ item.img }}"><img>"'>
	                                        	  	{{ item.nome_produto }}
	                                        	  </a>
                                            </td>
                                            <td>
                                            <button ng-show="item.id_produto != pedido.id_modelo_estampa"  ng-click="addModeloEstampa(item)"  class="btn btn-success btn-xs" type="button">
                                                <i class="fa fa-check-square-o"></i> Selecionar
                                            </button>
                                            <button ng-show="item.id_produto == pedido.id_modelo_estampa" ng-disabled="true" class="btn btn-primary btn-xs" type="button">
                                                <i class="fa fa-check-circle-o"></i> Selecionado
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
                                     <ul class="pagination pagination-xs m-top-none" ng-show="modeloEstampas.paginacao.length > 1">
                                        <li ng-repeat="item in modeloEstampas.paginacao" ng-class="{'active': item.current}">
                                            <a href="" ng-click="loadModeloEstampas(item.offset,item.limit)">{{ item.index }}</a>
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

		<!-- /Modal Processando Venda-->
		<div class="modal fade" id="modal-bases-tiras" style="display:none">
  			<div class="modal-dialog error modal-sm">
    			<div class="modal-content">
      				<div class="modal-header"></div>
				    <div class="modal-body">
				    	<div class="row">
				    		<div class="col-sm-12">
				    			<i class='fa fa-refresh fa-spin'></i> Aguarde! Carregando Bases e Tiras.
							</div>
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
	<script src="js/angular-controller/pedido-personalizado-controller.js?<?php echo filemtime('js/angular-controller/pedido-personalizado-controller.js') ?>"></script>
	<script type="text/javascript">
		$("#cld_dtaVenda").on("click", function(){ $("#dtaInicial").trigger("focus"); });
        $("#cld_dtaEntrega").on("click", function(){ $("#dtaInicial").trigger("focus"); });
		$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

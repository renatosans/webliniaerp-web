<?php
	include_once 'util/constants.php';
	include_once "util/login/restrito.php";
	restrito();
?>
<!DOCTYPE html>
<html lang="en" ng-app="HageERP">
  <head>
    <meta charset="utf-8">
    <title>Produtos | WebliniaERP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Bootstrap core CSS -->
      <link rel='stylesheet prefetch' href='bootstrap/css/bootstrap.min.css'>

     <!-- ui treeview -->
    <link rel="stylesheet" href="css/bootstrap-treeview.css"/>

	<!-- Font Awesome -->
	<link href="css/font-awesome-4.1.0.min.css" rel="stylesheet">

	<!-- Pace -->
	<link href="css/pace.css" rel="stylesheet">

	<!-- Chosen -->
	<link href="css/chosen/chosen.min.css" rel="stylesheet"/>

	<link href="css/fileinput/fileinput.css" media="all" rel="stylesheet" type="text/css" />

	<link href="js/Trumbowyg-master/dists/ui/trumbowyg.css" media="all" rel="stylesheet" type="text/css" />

	<!-- Bower Components -->	
	<link href="bower_components/noty/lib/noty.css" rel="stylesheet">

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">
	<style type="text/css">
		/* Fix for Bootstrap 3 with Angular UI Bootstrap */
		.panel.panel-default {
		    overflow: visible !important;
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

		/*--------------------------------------*/
		.chosen-choices{
			border-bottom-left-radius: 4px;
			border-bottom-right-radius: 4px;
			border-top-left-radius: 4px;
			border-top-right-radius: 4px;
			font-size: 12px;
			border-color: #ccc;
		}

		 .kv-avatar .file-preview-frame,.kv-avatar .file-preview-frame:hover {
            margin: 0;
            padding: 0;
            border: none;
            box-shadow: none;
            text-align: center;
        }
        .kv-avatar .file-input {
            display: table-cell;
            max-width: 220px;
        }
        .kv-avatar .file-drag-handle{
            display: none
        }
        .file-drop-zone.clickable:hover {
            border: 1px dashed #aaa !important ;
        }
	</style>
  </head>

  <body class="overflow-hidden" ng-controller="ProdutosController" ng-cloak>
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
					 <li class="active"><i class="fa fa-archive"></i> Produtos</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-archive"></i> Produtos</h3>
					<br/>
					<a class="btn btn-info" id="btn-novo"  ng-click="showBoxNovo()" ng-if="funcioalidadeAuthorized('inclusao_alteracao_produto')"><i class="fa fa-plus-circle"></i> Novo Produto</a>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md">
				<div class="alert alert-sistema" style="display:none"></div>

				<div class="panel panel-default" id="box-novo" style="display:none">
					<div class="panel-heading"><i class="fa fa-plus-circle"></i> Novo Produto</div>
					<div class="panel-body">
						<div class="panel panel-default" id="box-novo">
								<div class="panel-tab clearfix">
									<ul class="tab-bar">
										<li class="active"><a href="#informacoes_basicas" data-toggle="tab"><i class="fa fa-archive"></i>  Informações Básicas</a></li>
										<li><a href="#Empreendimentos" data-toggle="tab"><i class="fa fa-building-o"></i> Empreendimentos</a></li>
										<li ng-if="funcioalidadeAuthorized('alterar_preco')"><a href="#preco" data-toggle="tab"><i class="fa fa-archive"></i>  Preço</a></li>
										<li><a href="#estoque" data-toggle="tab"><i class="fa fa-list-ol"></i> Estoque</a></li>
										<li><a href="#fornecedores" data-toggle="tab"><i class="fa fa-truck"></i> Fornecedores</a></li>
										<li><a ng-click="timeCheckedTreeview()" href="#informacoes_complemetares" data-toggle="tab"><i class="fa fa-cubes"></i> Informações Complementares</a></li>
										<li><a href="#fiscal" data-toggle="tab"><i class="fa fa-file-text-o"></i> &nbsp;Fiscal</a></li>
										<li ng-if="isNumeric(produto.id)"><a href="#combinacoes" data-toggle="tab"><i class="fa fa-file-text-o"></i> &nbsp;Combinações</a></li>
									</ul>
								</div>
								<form id="formProdutos" role="form" enctype="multipart/form-data">
								<div class="panel-body">
									<div class="tab-content">
										<div class="tab-pane fade in active" id="informacoes_basicas">
											<div class="row">
												<div class="col-sm-3">
													<div class="form-group">
														<label for="" class="control-label">Tipo do produto</label>
														<div class="form-group">
															<label class="label-radio inline">
																<input ng-model="produto.flg_produto_composto" ng-click="changeTipoProduto(null,'tipo')" name="tipo_produto" value="0" type="radio" class="inline-radio">
																<span class="custom-radio"></span>
																<span>Normal</span>
															</label>
															<label class="label-radio inline">
																<input ng-model="produto.flg_produto_composto" ng-click="changeTipoProduto('flg_produto_composto','tipo')" name="tipo_produto" value="1" type="radio" class="inline-radio">
																<span class="custom-radio"></span>
																<span>Composto</span>
															</label>
														</div>
													</div>
												</div>
												<div class="col-sm-3" ng-if="userLogged.id_empreendimento == 51">
													<div class="form-group">
														<label for="" class="control-label">Sub Tipo do produto</label>
														<select ng-change="changeTipoProduto(produto.campo_extra_selected,'sub_tipo')" chosen ng-change="ClearChosenSelect('produto')"
													    option="chosen_campo_extra"
													    ng-model="produto.campo_extra_selected"
													    ng-options="campo.nome_campo as campo.label for campo in chosen_campo_extra">
														</select>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-2">
													<div class="form-group" id="codigo_barra">
														<label class="control-label"><i class="fa fa-barcode"></i> Código de Barras</label>
														<input type="text" class="form-control input-sm"
															ng-disabled="configuracao.id_produto_debito_anterior_cliente == produto.id_produto" 
															ng-model="produto.codigo_barra" ng-enter="fixBarcodeScanner()">
													</div>
												</div>

												<div class="col-sm-6">
													<div class="form-group" id="nome">
														<label class="control-label">Descrição do Produto <span style="color:red;font-weight: bold;">*</span></label>
														<input ng-disabled="configuracao.id_produto_debito_anterior_cliente == produto.id_produto" ng-model="produto.nome" ng-enter="salvar()" type="text" class="form-control input-sm">
													</div>
												</div>
												<div class="col-sm-2">
													<div class="form-group" id="peso">
															<label class="control-label">Tamanho </label> <i ng-click="showModalNovoTamanho()" style="cursor:pointer;color: #9ad268;" class="fa fa-plus-circle fa-lg"></i>
															<select chosen ng-change="ClearChosenSelect('produto');incluirCombinacaoDefault()"
														    option="tamanhos"
														    ng-model="produto.id_tamanho"
														    ng-options="tamanho.id as tamanho.nome_tamanho for tamanho in tamanhos">
															</select>
													</div>
												</div>
												<div class="col-sm-2">
													<div class="form-group" id="peso">
															<label class="control-label">Sabor/Cor</label> <i ng-click="showModalNovaCor()" style="cursor:pointer;color: #9ad268;" class="fa fa-plus-circle fa-lg"></i>
															<select chosen ng-change="ClearChosenSelect('cor');incluirCombinacaoDefault()"
														    option="cores"
														    ng-model="produto.id_cor"
														    ng-options="cor.id as cor.nome_cor for cor in cores">
															</select>
													</div>
												</div>
											</div>
											
											<br/>
											<div class="row" ng-if="produto.flg_produto_composto == 1">
												<div class="col-sm-12">
													<div class="empreendimentos form-group" id="insumos">
														
															<table class="table table-bordered table-condensed table-striped table-hover">
																<thead>
																	<tr>
																		<td colspan="7"><i class="fa fa fa-th fa-lg"></i> Insumos</td>
																		<td width="60" align="center">
																			<button class="btn btn-xs btn-primary" ng-click="showInsumos('insumos')"><i class="fa fa-plus-circle"></i></button>
																		</td>
																	</tr>
																</thead>
																<tbody>
																	<tr ng-show="(insumos.length == 0)">
																		<td colspan="8" align="center">Nenhum insumo selecionado!</td>
																	</tr>
																	<tr ng-show="(insumos.length > 0)">
																		<td>#</td>
																		<td class="text-center">Produto</td>
																		<td class="text-center">Fabricante</td>
																		<td class="text-center">Tamanho</td>
																		<td class="text-center">Sabor/Cor</td>
																		<td class="text-center">Custo</td>
																		<td class="text-center" width="50">Qtd.</td>
																		<td class="text-center" align="center"></td>
																	</tr>
																	<tr ng-repeat="item in insumos">
																		<td>{{ item.id }}</td>
																		<td>{{ item.nome }}</td>
																		<td>{{ item.nome_fabricante }}</td>
																		<td>{{ item.peso }}</td>
																		<td>{{ item.sabor }}</td>
																		<td class="text-right" width="70">R$ {{ item.vlr_custo_real | numberFormat:2:',':'.' }}</td>
																		<td width="80">
																			<input type="text" class="form-control input-xs text-center"
																				onKeyPress="return SomenteNumero(event);" 
																				ng-model="item.qtd" 
																				ng-keyup="calVlrCustoInsumos()" 
																				ng-if="item.flg_unidade_fracao != 1"/>

																			<input type="text" class="form-control input-xs text-center"
																				thousands-formatter precision="3"
																				onKeyPress="return SomenteNumero(event);" 
																				ng-model="item.qtd" 
																				ng-keyup="calVlrCustoInsumos()" 
																				ng-if="item.flg_unidade_fracao == 1"/>
																		</td>
																		<td align="center">
																			<button class="btn btn-xs btn-danger" ng-click="delInsumo($index,item)"><i class="fa fa-trash-o"></i></button>
																		</td>
																	</tr>
																</tbody>
															</table>
												
													</div>
												</div>
											</div>

											<br/>
											<div class="row" ng-if="produto.flg_produto_composto == 1">
												<div class="col-sm-12">
													<div class="empreendimentos form-group" id="adicionais">
														
															<table class="table table-bordered table-condensed table-striped table-hover">
																<thead>
																	<tr>
																		<td colspan="5"><i class="fa fa fa-th fa-lg"></i> Adicionais</td>
																		<td width="60" align="center">
																			<button class="btn btn-xs btn-primary" ng-click="showInsumos('adicionais')"><i class="fa fa-plus-circle"></i></button>
																		</td>
																	</tr>
																</thead>
																<tbody>
																	<tr ng-show="(adicionais.length == 0)">
																		<td colspan="8" align="center">Nenhum adicional selecionado!</td>
																	</tr>
																	<tr ng-show="(adicionais.length > 0)">
																		<td class="text-center">#</td>
																		<td class="text-center">Produto</td>
																		<td class="text-center">Fabricante</td>
																		<td class="text-center">Tamanho</td>
																		<td class="text-center">Sabor/Cor</td>
																		<td class="text-center" align="center"></td>
																	</tr>
																	<tr ng-repeat="item in adicionais">
																		<td class="text-center">{{ item.id }}</td>
																		<td>{{ item.nome }}</td>
																		<td>{{ item.nome_fabricante }}</td>
																		<td>{{ item.peso }}</td>
																		<td>{{ item.sabor }}</td>
																		<td align="center">
																			<button class="btn btn-xs btn-danger" 
																				ng-click="delAdicional($index,item)">
																				<i class="fa fa-trash-o"></i>
																			</button>
																		</td>
																	</tr>
																</tbody>
															</table>
												
													</div>
												</div>
											</div>

											<div class="row">
												<div class="col-sm-12">
													<div class="pull-right">
														<button ng-click="showBoxNovo(); reset();" type="submit" class="btn btn-danger btn-sm">
															<i class="fa fa-times-circle"></i> Cancelar
														</button>
														<button type="submit" class="btn btn-success btn-sm" id="btn-salvar-informacoes-basicas"
															data-loading-text="<i class='fa fa-refresh fa-spin'></i> Salvando, Aguarde..." 
															ng-click="salvar('btn-salvar-informacoes-basicas')">
															<i class="fa fa-save"></i> Salvar
														</button>
													</div>
												</div>
											</div>
										</div>
										<div ng-if="funcioalidadeAuthorized('alterar_preco')" class="tab-pane fade" id="preco">
											<div class="table-responsive">
												<table class="table table-bordered table-condensed table-striped table-hover">
													<thead>
														<tr>
															<th class="text-center" rowspan="2" style="line-height: 46px;width: 200px">Empreendimento</th>
															<th class="text-center" rowspan="2" style="line-height: 46px">Custo </th>
															<th class="text-center" colspan="2" ng-if="existeTabelaPreco('atacado')">Venda (Atacado)</th>
															<th class="text-center" colspan="2" ng-if="existeTabelaPreco('intermediario')">Venda (Intermediário)</th>
															<th class="text-center" colspan="2" ng-if="existeTabelaPreco('varejo')">Venda (Varejo)</th>
														</tr>
														<tr>
															<td class="text-center" ng-if="existeTabelaPreco('atacado')">%</td>
															<td class="text-center" ng-if="existeTabelaPreco('atacado')">R$</td>
															<td class="text-center" ng-if="existeTabelaPreco('intermediario')">%</td>
															<td class="text-center" ng-if="existeTabelaPreco('intermediario')">R$</td>
															<td class="text-center" ng-if="existeTabelaPreco('varejo')">%</td>
															<td class="text-center" ng-if="existeTabelaPreco('varejo')">R$</td>
														</tr>
													</thead>
													<tbody ng-if="produto.precos.length == 0">
														<tr>
															<td colspan="9" class="text-center">Nenhum empreendimento vinculado ao produto</td>
														</tr>
													</tbody>
													<tbody ng-repeat="preco in produto.precos">
														<tr>
															<td>
																#{{preco.id_empreendimento}} - {{ preco.nome_empreendimento }}
															</td>
															<td>
																<div class="row">
																	<div class="col-sm-9">
																		<input ng-model="preco.vlr_custo" ng-keyup="calcularAllMargens(preco)"  thousands-formatter precision='{{ configuracao.qtd_casas_decimais }}' type="text" class="form-control input-xs parsley-validated">
																	</div>
																	<div class="col-sm-1" style="padding-left: 0px;">
																		<button type="button" tooltip title="Replicar para todos" class="btn btn-success btn-xs" ng-click="replicarCusto(preco)">
																			<i class="fa fa-arrows-v"></i>
																		</button>
																	</div>
																</div>
															</td>
															<td ng-if="existeTabelaPreco('atacado')" >
																<input ng-model="preco.perc_venda_atacado" ng-keyup="calculaMargens('atacado','margem',preco)"  ng-disabled="preco.vlr_custo == null || preco.vlr_custo == ''"  thousands-formatter precision='{{ configuracao.qtd_casas_decimais }}'   type="text" class="form-control input-xs parsley-validated maskPorcentagem">
															</td>
															<td ng-if="existeTabelaPreco('atacado')" >
																<input ng-model="preco.valor_venda_atacado" ng-keyup="calculaMargens('atacado','valor',preco)" 
																 ng-disabled="preco.vlr_custo == null || preco.vlr_custo == ''"  thousands-formatter precision='{{ configuracao.qtd_casas_decimais }}'   type="text" class="form-control input-xs parsley-validated maskPorcentagem">
															</td>

															<td ng-if="existeTabelaPreco('intermediario')">
																<input ng-model="preco.perc_venda_intermediario" ng-keyup="calculaMargens('intermediario','margem',preco)"  ng-disabled="preco.vlr_custo == null || preco.vlr_custo == ''"  thousands-formatter precision='{{ configuracao.qtd_casas_decimais }}'    type="text" class="form-control input-xs parsley-validated maskPorcentagem">
															</td>
															<td ng-if="existeTabelaPreco('intermediario')">
																<input ng-model="preco.valor_venda_intermediario" ng-keyup="calculaMargens('intermediario','valor',preco)"  ng-disabled="preco.vlr_custo == null || preco.vlr_custo == ''"  thousands-formatter precision='{{ configuracao.qtd_casas_decimais }}'   type="text" class="form-control input-xs parsley-validated maskPorcentagem">
															</td>
															<td ng-if="existeTabelaPreco('varejo')">
																<input ng-if="existeTabelaPreco('varejo')" ng-model="preco.perc_venda_varejo" ng-keyup="calculaMargens('varejo','margem',preco)"  ng-disabled="preco.vlr_custo == null || preco.vlr_custo == ''"  thousands-formatter precision='{{ configuracao.qtd_casas_decimais }}'   type="text" class="form-control input-xs parsley-validated maskPorcentagem">
															</td>
															<td ng-if="existeTabelaPreco('varejo')">
																<input ng-if="existeTabelaPreco('varejo')" ng-model="preco.valor_venda_varejo" ng-keyup="calculaMargens('varejo','valor',preco)"  ng-disabled="preco.vlr_custo == null || preco.vlr_custo == ''"  thousands-formatter precision='{{ configuracao.qtd_casas_decimais }}'   type="text" class="form-control input-xs parsley-validated maskPorcentagem">
															</td>
														</tr>
													</tbody>
												</table>
											</div>
											<div class="row">
												<div class="col-sm-12">
													<div class="pull-right">
														<button ng-click="showBoxNovo(); reset();" type="submit" class="btn btn-danger btn-sm">
															<i class="fa fa-times-circle"></i> Cancelar
														</button>
														<button data-loading-text="<i class='fa fa-refresh fa-spin'></i> Salvando, Aguarde..." ng-click="salvar('btn-salvar-preco')"type="submit" id="btn-salvar-preco" class="btn btn-success btn-sm">
															<i class="fa fa-save"></i> Salvar
														</button>
													</div>
												</div>
											</div>
										</div>
										<div class="tab-pane fade" id="estoque">
											<div class="row">
												<div class="col-sm-3">
													<div class="form-group">
														<label for="" class="control-label">Estoque controlado por lote?</label>
														<div class="form-group">
															<label class="label-radio inline">
																<input ng-model="produto.flg_controlar_lote"  name="controlar_lote" value="1" type="radio" class="inline-radio">
																<span class="custom-radio"></span>
																<span>sim</span>
															</label>
															<label class="label-radio inline">
																<input ng-model="produto.flg_controlar_lote"  name="controlar_lote" value="0" type="radio" class="inline-radio">
																<span class="custom-radio"></span>
																<span>não</span>
															</label>
														</div>
													</div>
												</div>
												<div class="col-sm-3">
													<div class="form-group">
														<label for="" class="control-label">Estoque controlado por validade?</label>
														<div class="form-group">
															<label class="label-radio inline">
																<input ng-model="produto.flg_controlar_validade"  name="controlar_validade" value="1" type="radio" class="inline-radio">
																<span class="custom-radio"></span>
																<span>sim</span>
															</label>
															<label class="label-radio inline">
																<input ng-model="produto.flg_controlar_validade"  name="controlar_validade" value="0" type="radio" class="inline-radio">
																<span class="custom-radio"></span>
																<span>não</span>
															</label>
														</div>
													</div>
												</div>
												<div class="col-sm-3">
													<div class="form-group">
														<label for="" class="control-label">Unidade de Medida</label>
														<div class="form-group">
															<label class="label-radio inline">
																<input ng-model="produto.flg_unidade_fracao"  name="unidade_fracao" value="0" type="radio" class="inline-radio">
																<span class="custom-radio"></span>
																<span>Unidade</span>
															</label>
															<label class="label-radio inline">
																<input ng-model="produto.flg_unidade_fracao"  name="unidade_fracao" value="1" type="radio" class="inline-radio">
																<span class="custom-radio"></span>
																<span>Fração</span>
															</label>
														</div>
													</div>
												</div>
											</div>
											<div ng-if="funcioalidadeAuthorized('alterar_quantidade')" class="row">
												<div class="col-sm-3" id="inventario_novo_deposito">
													<label class="control-label">Depósito</label>
													<div class="input-group">
											            <input ng-model="inventario_novo.nome_deposito" ng-disabled="true" type="text" class="form-control input-xs" ng-enter="loadDepositos(0,10)">
											            <div class="input-group-btn">
											            	<button ng-click="modalDepositos()" tabindex="-2" class="btn btn-xs btn-primary" type="button">
											            		<i class="fa fa-sitemap"></i>
											            	</button>
											            </div>
											        </div>
												</div>
												<div class="col-sm-4" id="inventario_novo_id">
													<label class="control-label">Combinação</label>
													<div class="input-group">
											            <input ng-model="inventario_novo.dsc_combinacao" ng-disabled="true" type="text" class="form-control input-xs" ng-enter="modalCombinacao(0,10)">
											            <div class="input-group-btn">
											            	<button ng-click="modalCombinacao()" tabindex="-2" class="btn btn-xs btn-primary" type="button">
											            		<i class="fa fa-sitemap"></i>
											            	</button>
											            </div>
											        </div>
												</div>
												<div class="col-sm-2">
													<div class="form-group" id="inventario_novo_validade">
														<label class="control-label" >Data de Validade</label>
														<input ng-model="inventario_novo.dta_validade" ui-mask="99/99/9999"  type="text" class="form-control input-xs">
													</div>
												</div>
												<div class="col-sm-1" ng-if="produto.flg_controlar_lote==1">
													<div class="form-group" id="inventario_novo_lote">
														<label class="control-label" >Lote</label>
														<input ng-model="inventario_novo.lote" type="text" class="form-control input-xs">
													</div>
												</div>
												<div class="col-sm-1" ng-if="produto.flg_unidade_fracao != 1">
													<div class="form-group" id="inventario_novo_qtd">
														<label class="control-label">Qtd.</label>
														<input ng-model="inventario_novo.qtd_ivn" onkeypress="return SomenteNumero(event);" type="text" class="form-control input-xs">
													</div>
												</div>
												<div class="col-sm-2" ng-if="produto.flg_unidade_fracao == 1">
													<div class="form-group" id="inventario_novo_qtd">
														<label class="control-label">Qtd.</label>
														<input ng-model="inventario_novo.qtd_ivn" thousands-formatter precision="3" onkeypress="return SomenteNumero(event);" type="text" class="form-control input-xs">
													</div>
												</div>
												<div class="col-sm-1">
													<label class="control-label">&nbsp;</label>
													<div class="form-group" >
														<button data-loading-text="Aguarde..." ng-click="addNovoInventario()" type="submit" id="btn-novo_inventario" class="btn btn-success btn-xs">
															Adicionar
														</button>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-12">
													<table class="table table-bordered table-condensed table-striped table-hover">
														<thead>
															<tr>
																<th class="text-center">Tamanho</th>
																<th class="text-center">Peso/Sabor</th>
																<th class="text-center">Depósito</th>
																<th class="text-center">Validade</th>
																<th class="text-center" ng-if="produto.flg_controlar_lote==1">Lote</th>
																<th class="text-center" width="100">Quantidade</th>
															</tr>
														</thead>
														<tbody>
															<tr ng-repeat="(key, value) in produto.estoque | orderBy:'+nome_deposito'">
																<td>{{ value.peso }}</td>
																<td>{{ value.sabor }}</td>
																<td>{{ value.nome_deposito }}</td>
																<td class="text-center" ng-if="value.dta_validade != '2099-12-31'">{{ value.dta_validade | dateFormat:'date' }}</td>
																<td class="text-center" ng-if="value.dta_validade == '2099-12-31'"></td>
																<td class="text-center" ng-if="produto.flg_controlar_lote==1">{{ value.lote }}</td>
																<td  ng-if="funcioalidadeAuthorized('alterar_quantidade')" class="text-center" >
																	<input type="text" ng-if="value.flg_visivel == 1 && produto.flg_unidade_fracao != 1" onkeypress="return SomenteNumero(event);"   class="form-control input-xs text-center" ng-model="value.qtd_ivn" >
																	<input type="text" ng-if="value.flg_visivel == 1 && produto.flg_unidade_fracao == 1" thousands-formatter precision="3" onkeypress="return SomenteNumero(event);"   class="form-control input-xs text-center" ng-model="value.qtd_ivn" >
																	<span ng-if="value.flg_visivel != 1">{{ value.qtd_ivn }}</span>
																</td>
																<td ng-if="!funcioalidadeAuthorized('alterar_quantidade')" class="text-center">{{ value.qtd_ivn }} </td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-12">
													<div class="pull-right">
														<button ng-click="showBoxNovo(); reset();" type="submit" class="btn btn-danger btn-sm">
															<i class="fa fa-times-circle"></i> Cancelar
														</button>
														<button data-loading-text="<i class='fa fa-refresh fa-spin'></i> Salvando, Aguarde..." ng-click="salvar('btn-salvar-estoque')"type="submit" id="btn-salvar-estoque" class="btn btn-success btn-sm">
															<i class="fa fa-save"></i> Salvar
														</button>
													</div>
												</div>
											</div>
										</div>
										<div class="tab-pane fade" id="informacoes_complemetares">
											<div class="row">
												<div class="col-sm-4">
													<div class="form-group" id="img">
														<label class="control-label" ng-show="editing == false || (produto.img == '' || produto.img == null)" ><i class="fa fa-camera"></i> Foto do Produto</label>
														<a href="assets/imagens/produtos/{{ produto.img }}"  target="_blanck">
															<label style="cursor:pointer" class="control-label" ng-hide="editing == false || (produto.img == '' || produto.img == null)" ><i class="fa fa-camera"></i> Foto do Produto</label>
														</a>
														<div class="upload-file">
															<input  id="foto-produto" ng-disabled="configuracao.id_produto_debito_anterior_cliente == produto.id_produto" name="img"  class="foto-produto" type="file" data-file="produto.foto" accept="image/*"  />
															<!-- <input ng-model=""   name="image" type="file" id="foto-produto" class="foto-produto" ng-model="fotoProduto"> -->
															<label ng-disabled="configuracao.id_produto_debito_anterior_cliente == produto.id_produto" data-title="Selecionar" for="foto-produto">
																<span data-title="{{ produto.img }}"></span>
															</label>

														</div>
													</div>
												</div>
												<div class="col-sm-1  no-pad">
													<div class="form-group">
														<label class="control-label">&nbsp;</label>
														<div class="controls clearfix text-left">
															<button type="button" tooltip title="Excluir Foto do Produto" class="btn btn-danger btn-xs" ng-click="limpa_fp()">
															<i class="fa fa-trash-o"></i>
														</button>
														</div>
													</div>
												</div>
												<div class="col-sm-2">
													<div class="form-group" id="qtd_minima_estoque">
														<label class="control-label">Estoque Mínimo</label>
														<input ng-disabled="configuracao.id_produto_debito_anterior_cliente == produto.id_produto" ng-model="produto.qtd_minima_estoque" type="text" class="form-control input-sm" onKeyPress="return SomenteNumero(event);">
													</div>
												</div>
												<div class="col-sm-4">
													<div class="form-group" id="nme_arquivo_nutricional">
														<label class="control-label" ng-show="editing == false || (produto.nme_arquivo_nutricional == '' || produto.nme_arquivo_nutricional == null)"><i class="fa fa-camera"></i> Arquivo Nutricional</label>
														<a href="assets/arquivos/produtos/{{ produto.nme_arquivo_nutricional }}"  target="_blanck">
															<label style="cursor:pointer" class="control-label" ng-hide="editing == false || (produto.nme_arquivo_nutricional == '' || produto.nme_arquivo_nutricional == null)"><i class="fa fa-camera"></i> Arquivo Nutricional</label>
														</a>
														<div class="upload-file">
															<input ng-disabled="configuracao.id_produto_debito_anterior_cliente == produto.id_produto"  id="arquivo-produto" name="arquivo-produto"  class="foto-produto" type="file" data-file="produto.foto" accept="image/*" />
															<!-- <input ng-model=""   name="image" type="file" id="foto-produto" class="foto-produto" ng-model="fotoProduto"> -->
															<label data-title="Selecione" for="arquivo-produto">
																<span data-title="{{ produto.nme_arquivo_nutricional }}"></span>
															</label>
														</div>
													</div>
												</div>
												<div class="col-sm-1  no-pad">
													<div class="form-group">
														<label class="control-label">&nbsp;</label>
														<div class="controls clearfix text-left">
															<button type="button" tooltip title="Excluir Arquivo Nutricional" class="btn btn-danger btn-xs" ng-click="limpa_an">
															<i class="fa fa-trash-o"></i>
														</button>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-4">
													<div class="form-group" id="peso">
															<label class="control-label">Fabricante</label> <i ng-click="modal('show','modal-novo-fabricante')" style="cursor:pointer;color: #9ad268;" class="fa fa-plus-circle fa-lg"></i>
															<select chosen ng-change="ClearChosenSelect('fabricante')"
														    option="fabricantes"
														    ng-model="produto.id_fabricante"
														    ng-options="fabricante.id as fabricante.nome_fabricante for fabricante in fabricantes">
															</select>
													</div>
												</div>
												<div class="col-sm-4">
													<div class="form-group" id="peso">
															<label class="control-label">Importador</label> <i ng-click="modal('show','modal-novo-importador')" style="cursor:pointer;color: #9ad268;" class="fa fa-plus-circle fa-lg"></i>
															<select chosen ng-change="ClearChosenSelect('importador')"
														    option="importadores"
														    ng-model="produto.id_importador"
														    ng-options="importador.id as importador.nome_importador for importador in importadores">
															</select>
													</div>													
												</div>
												<div class="col-sm-2">
													<div class="form-group">
														<label for="" class="control-label">Venda Obrigatória?</label>
														<div class="form-group">
															<label class="label-radio inline">
																<input ng-model="produto.flg_venda_obrigatoria"  name="venda_obrigatoria" value="1" type="radio" class="inline-radio">
																<span class="custom-radio"></span>
																<span>Sim</span>
															</label>
															<label class="label-radio inline">
																<input ng-model="produto.flg_venda_obrigatoria"  name="venda_obrigatoria" value="0" type="radio" class="inline-radio">
																<span class="custom-radio"></span>
																<span>Não</span>
															</label>
														</div>
													</div>
												</div>
												<div class="col-sm-2">
													<div class="form-group" id="id_referencia_externo">
														<label class="control-label">ID Externo</label>
														<input ng-model="produto.id_referencia_externo" type="text" class="form-control input-sm">
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-2">
													<div class="form-group" id="qtd_minima_estoque">
														<label class="control-label">Peso KG</label>
														<input ng-disabled="configuracao.id_produto_debito_anterior_cliente == produto.id_produto" thousands-formatter precision="6" ng-model="produto.peso_frete" type="text" class="form-control input-sm">
													</div>
												</div>
												<div class="col-sm-2">
													<div class="form-group" id="qtd_minima_estoque">
														<label class="control-label">Largura do pacote</label>
														<input ng-disabled="configuracao.id_produto_debito_anterior_cliente == produto.id_produto" thousands-formatter precision="6" ng-model="produto.largura_pacote" type="text" class="form-control input-sm">
													</div>
												</div>
												<div class="col-sm-2">
													<div class="form-group" id="qtd_minima_estoque">
														<label class="control-label">Altura do pacote</label>
														<input ng-disabled="configuracao.id_produto_debito_anterior_cliente == produto.id_produto" thousands-formatter precision="6" ng-model="produto.altura_pacote" type="text" class="form-control input-sm">
													</div>
												</div>
												<div class="col-sm-2">
													<div class="form-group" id="qtd_minima_estoque">
														<label class="control-label">Profundidade do pacote</label>
														<input ng-disabled="configuracao.id_produto_debito_anterior_cliente == produto.id_produto" thousands-formatter precision="6" 
															ng-model="produto.profundidade_pacote" type="text" class="form-control input-sm">
													</div>
												</div>
												<div class="col-sm-4">
													<div class="form-group" id="peso">
														<label class="control-label">Natureza</label>
														<select chosen
															option="plano_contas"
															ng-model="produto.id_natureza"
															ng-options="plano.id as plano.dsc_completa for plano in plano_contas">
														</select>
													</div>													
												</div>
											</div>
											<div class="row">
												<div class="col-sm-6">
													<div class="form-group">
															<label class="control-label">Categoria Padrão</label> <i ng-click="modal('show','modal-nova-categoria')" style="cursor:pointer;color: #9ad268;" class="fa fa-plus-circle fa-lg"></i>
															<select chosen ng-change="ClearChosenSelect('categoria')"
														    option="categorias"
														    ng-model="produto.id_categoria"
														    ng-options="categoria.id as categoria.descricao_categoria for categoria in categorias">
															</select>
													</div>
													<div class="padding-md" style="padding:0 !important">
														<div class="panel panel-default" id="modulos">
															<div class="panel-heading"><i class="fa fa-th fa-lg"></i> Categorias
																	<span ng-show="checando_categorias" class="pull-right">
																	<i class='fa fa-refresh fa-spin'></i> checando categorias
																	</span>
																	<span ng-show="perfil.modulos.length > 0" class="pull-right">
																	<i class='fa fa-refresh fa-spin'></i> checando categorias
																	<!--Selecionados: <span style="background:#504f63" class="badge badge-primary">{{ produto.modulos.length }}</span>--></span>
															</div>
															<div style="max-height:305px;min-height:305px;overflow:auto" class="panel-body" id="treeview-modulos">
																
													        </div>
													    </div>
													</div>
												</div>
												<div class="col-sm-6">
													<div class="form-group" style="height:200px">
														<label class="control-label">Descrição</label>
														<div id="descricao_html"></div>
													</div>
													<div class="form-group"  style="height:200px">
														<label class="control-label">Descrição Curta</label>
														<!-- <div id="descricao_html_curta"></div> -->
														<textarea class="form-control" rows="5" ng-model="produto.descricao_curta"></textarea>
													</div>
												</div>
											</div>
											<div style="padding: 10px 15px 10px 0px; margin-bottom:10px" class="panel-heading">
												SEO (OTIMIZAÇÃO PARA MOTORES DE BUSCA)
											</div>
											<div class="row">
												<div class="col-sm-12">
													<div class="form-group" id="codigo_barra">
														<label class="control-label"> Meta Title</label>
														<input maxlength="70" ng-disabled="configuracao.id_produto_debito_anterior_cliente == produto.id_produto"  ng-model="produto.meta_title" type="text"  class="form-control input-sm">
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-12">
													<div class="form-group" id="codigo_barra">
														<label class="control-label"> Meta Description</label>
														<input maxlength="160" ng-disabled="configuracao.id_produto_debito_anterior_cliente == produto.id_produto"  ng-model="produto.meta_description" type="text"  class="form-control input-sm">
													</div>
												</div>
											</div>
											<!--<div class="row">
												<div class="col-sm-12">
													<div class="form-group" id="descricao">
														<label class="control-label">Descrição</label>
														<textarea ng-disabled="configuracao.id_produto_debito_anterior_cliente == produto.id_produto" ng-model="produto.descricao" class="form-control" rows="5"></textarea>
													</div>
												</div>
											</div>-->
											<div class="row">
												<div class="col-sm-12">
													<div class="pull-right">
														<button ng-click="showBoxNovo(); reset();" type="submit" class="btn btn-danger btn-sm">
															<i class="fa fa-times-circle"></i> Cancelar
														</button>
														<button data-loading-text="<i class='fa fa-refresh fa-spin'></i> Salvando, Aguarde..." ng-click="salvar('btn-salvar-informacoes-complemetares')"type="submit" id="btn-salvar-informacoes-complemetares" class="btn btn-success btn-sm">
															<i class="fa fa-save"></i> Salvar
														</button>
													</div>
												</div>
											</div>
										</div>
										<div class="tab-pane fade" id="Empreendimentos">
											<div class="row">
												<div class="col-sm-12">
													<div class="empreendimentos form-group" id="empreendimentos">
															<table class="table table-bordered table-condensed table-striped table-hover">
																<thead>
																	<tr>
																		<td><i class="fa fa-building-o"></i> Empreendimentos</td>
																		<td width="60" align="center">
																			<button class="btn btn-xs btn-primary" ng-click="showEmpreendimentos()"><i class="fa fa-plus-circle"></i></button>
																		</td>
																	</tr>
																</thead>
																<tbody>
																	<tr ng-show="(empreendimentosAssociados.length == 0)">
																		<td colspan="2" align="center">Nenhum empreendimento selecionado</td>
																	</tr>
																	<tr ng-repeat="item in empreendimentosAssociados">
																		<td>{{ item.nome_empreendimento }}</td>
																		<td align="center">
																			<button class="btn btn-xs btn-danger" ng-click="delEmpreendimento($index,item)"><i class="fa fa-trash-o"></i></button>
																		</td>
																	</tr>
																</tbody>
															</table>
												
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-12">
													<div class="pull-right">
														<button ng-click="showBoxNovo(); reset();" type="submit" class="btn btn-danger btn-sm">
															<i class="fa fa-times-circle"></i> Cancelar
														</button>
														<button data-loading-text="<i class='fa fa-refresh fa-spin'></i> Salvando, Aguarde..." ng-click="salvar('btn-salvar-empreendimentos')"type="submit" id="btn-salvar-empreendimentos" class="btn btn-success btn-sm">
															<i class="fa fa-save"></i> Salvar
														</button>
													</div>
												</div>
											</div>
										</div>
										<div class="tab-pane fade" id="fornecedores">
											<div class="row">
												<div class="col-sm-12">
													<div class="form-group" id="fornecedores">
														<table class="table table-bordered table-condensed table-striped table-hover">
															<thead>
																<tr>
																	<td><i class="fa fa-truck"></i> Fornecedores <!--<i ng-click="modal('show','modal-novo-fornecedor')" style="cursor:pointer;color: #9ad268;" class="fa fa-plus-circle fa-lg"></i>--></td>
																	<td width="60" align="center">
																		<button ng-disabled="configuracao.id_produto_debito_anterior_cliente == produto.id_produto" ng-click="showFornecedores()"  class="btn btn-xs btn-primary"><i class="fa fa-plus-circle"></i></button>
																	</td>
																</tr>
															</thead>
															<tbody>
																<tr ng-show="(produto.fornecedores.length == 0)">
																		<td colspan="2" align="center">Nenhum fornecedor selecionado</td>
																	</tr>
																<tr ng-repeat="item in produto.fornecedores" >
																	<td>{{ item.nome_fornecedor }}</td>
																	<td align="center">
																		<button ng-click="delFornecedor($index)" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></button>
																	</td>
																</tr>
															</tbody>
														</table>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-12">
													<div class="pull-right">
														<button ng-click="showBoxNovo(); reset();" type="submit" class="btn btn-danger btn-sm">
															<i class="fa fa-times-circle"></i> Cancelar
														</button>
														<button data-loading-text="<i class='fa fa-refresh fa-spin'></i> Salvando, Aguarde..." ng-click="salvar('btn-salvar-fornecedores')"type="submit" id="btn-salvar-fornecedores" class="btn btn-success btn-sm">
															<i class="fa fa-save"></i> Salvar
														</button>
													</div>
												</div>
											</div>
										</div>
										<div class="tab-pane fade" id="fiscal">
											<div class="row">
												<div class="col-sm-4" id="cod_ncm">
													<label class="control-label">NCM</label>
													<div class="input-group">
														<input ng-click="selNcm()" type="text" class="form-control input-sm" ng-model="produto.ncm_view" readonly="readonly" style="cursor: pointer;" />
														<span class="input-group-btn">
															<button ng-click="selNcm()"  type="button" class="btn btn-sm"><i class="fa-search fa"></i></button>
														</span>
													</div>
												</div>
												<div class="col-sm-1" id="ex_tipi">
													<div class="form-group">
														<label class="control-label">EX TIPI</label> 
															<input  ng-model="produto.ex_tipi" type="text" class="form-control input-sm">
													</div>
												</div>
												<div class="col-sm-4">
													<div class="form-group" id="cod_especializacao_ncm">
														<label class="control-label">Especialização NCM</label> 
														<select chosen ng-change="ClearChosenSelect('cod_especializacao_ncm')"
													    option="chosen_especializacao_ncm"
													    ng-model="produto.cod_especializacao_ncm"
													    allow-single-deselect="true"
													    ng-options="especializacao_ncm.cod_especializacao_ncm as especializacao_ncm.dsc_especializacao_ncm for especializacao_ncm in chosen_especializacao_ncm">
														</select>
													</div>
												</div>
												<div class="col-sm-3">
													<div class="form-group" id="cod_forma_aquisicao">
														<label class="control-label">Forma Aquisição</label> 
														<select chosen ng-change="ClearChosenSelect('cod_forma_aquisicao')"
													    option="chosen_forma_aquisicao"
													    ng-model="produto.cod_forma_aquisicao"
													    allow-single-deselect="true"
													    ng-options="forma_aquisicao.cod_controle_item_nfe as forma_aquisicao.nme_item for forma_aquisicao in chosen_forma_aquisicao">
														</select>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-5">
													<div class="form-group" id="cod_origem_mercadoria">
														<label class="control-label">Origem Mercadoria</label> 
														<select chosen ng-change="ClearChosenSelect('cod_origem_mercadoria')"
													    option="chosen_origem_mercadoria"
													    ng-model="produto.cod_origem_mercadoria"
													    allow-single-deselect="true"
													    ng-options="origem_mercadoria.cod_controle_item_nfe as origem_mercadoria.nme_item for origem_mercadoria in chosen_origem_mercadoria">
														</select>
													</div>
												</div>
												<div class="col-sm-2">
													<div class="form-group" id="cod_tipo_tributacao_ipi">
														<label class="control-label">Tipo Tributação IPI</label> 
														<select chosen ng-change="ClearChosenSelect('cod_tipo_tributacao_ipi')"
													    option="chosen_tipo_tributacao_ipi"
													    ng-model="produto.cod_tipo_tributacao_ipi"
													    allow-single-deselect="true"
													    ng-options="tipo_tributacao_ipi.cod_controle_item_nfe as tipo_tributacao_ipi.nme_item for tipo_tributacao_ipi in chosen_tipo_tributacao_ipi">
														</select>
													</div>
												</div>
												<div class="col-sm-2">
													<div class="form-group" id="cod_tipo_tributacao_ipi">
														<label class="control-label">Regra Tributos</label> 
														<select chosen 
													    option="chosen_regra_tributos"
													    ng-model="produto.cod_regra_tributos"
													    allow-single-deselect="true"
													    ng-options="regra.cod_regra_tributos as regra.dsc_regra_tributos for regra in chosen_regra_tributos">
														</select>
													</div>
												</div>
												<div class="col-sm-2">
													<div class="form-group" id="codigo_barra">
														<label class="control-label">Número Cest</label>
														<input ng-model="produto.num_cest" type="text"  class="form-control input-sm" onKeyPress="return SomenteNumero(event);">
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-2">
													<div class="form-group" id="dsc_unidade_medida">
														<label class="control-label">Uni Comercial</label>
														<input ng-model="produto.dsc_unidade_medida" type="text"  class="form-control input-sm">
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-12">
													<div class="pull-right">
														<button ng-click="showBoxNovo(); reset();" type="submit" class="btn btn-danger btn-sm">
															<i class="fa fa-times-circle"></i> Cancelar
														</button>
														<button data-loading-text="<i class='fa fa-refresh fa-spin'></i> Salvando, Aguarde..." ng-click="salvar('btn-salvar-fiscal')"type="submit" id="btn-salvar-fiscal" class="btn btn-success btn-sm">
															<i class="fa fa-save"></i> Salvar
														</button>
													</div>
												</div>
											</div>
										</div>
										<div class="tab-pane fade" id="combinacoes">
											<div class="row">	
												<div class="col-sm-12">
													<div class="empreendimentos form-group" id="insumos">
															<table class="table table-bordered table-condensed table-striped table-hover">
																<thead>
																	<tr>
																		<td colspan="2"><i class="fa fa fa-th fa-lg"></i>Combinações</td>
																		<td width="90" align="center">
																		<button type="button" data-loading-text=" Aguarde..." class="btn btn-xs btn-success"
																    		id="btn-salvar-importador" ng-click="showModalAddCombinacoes()">
																    		<i class="fa fa-save"></i>
																    	</button>
																			<!--<button class="btn btn-xs btn-primary" ng-click="showModalCombinacoes()"><i class="fa fa-plus-circle"></i></button>-->
																		</td>
																	</tr>
																</thead>
																<tbody>
																	<tr ng-show="(produto.combinacoes.length == 0)">
																		<td colspan="6" align="center">Nenhum combinação inserida</td>
																	</tr>

																	<tr ng-show="(produto.combinacoes.length > 0)">
																		<td class="text-center">Tamanho</td>
																		<td class="text-center">Sabor/Cor</td>
																		<td class="text-center" align="center"></td>
																	</tr>
																	<tr ng-repeat="item in produto.combinacoes">
																		<td>{{ item.peso }}</td>
																		<td>{{ item.sabor }}</td>
																		<td align="center">
																			<button  ng-if="produto.id != item.id" type="button" ng-click="ModalEditarCombinacao(item,$index)" class="btn btn-xs btn-warning">
																				<i class="fa fa-edit"></i>
																			</button>
																			<button ng-if="produto.id != item.id" class="btn btn-xs btn-danger" ng-click="delCombinacao($index,item)"><i class="fa fa-trash-o"></i></button>
																		</td>
																	</tr>
																</tbody>
															</table>
												
													</div>
												</div>
											</div>	
											<div class="row">
												<div class="col-sm-12">
													<div class="pull-right">
														<button ng-click="showBoxNovo(); reset();" type="submit" class="btn btn-danger btn-sm">
															<i class="fa fa-times-circle"></i> Cancelar
														</button>
														<button data-loading-text="<i class='fa fa-refresh fa-spin'></i> Salvando, Aguarde..." ng-click="salvar('btn-salvar-combinacoes')" type='submit' id="btn-salvar-combinacoes" class="btn btn-success btn-sm">
															<i class="fa fa-save"></i> Salvar
														</button>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								</form>
							</div><!-- /panel -->
					</div>
				</div><!-- /panel -->

				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-tasks"></i> Produtos Cadastrados
						<!--<a href="util/export.php?c=produtos&id_empreendimento={{ userLogged.id_empreendimento }}" target="_blank" class="btn btn-xs btn-success pull-right"  type="button">
							<i class="fa fa-download"></i>
							Exportar
						</a>-->
					</div>


					<div class="panel-body">
						<div class="row">
							<div class="col-sm-12">
								<div class="input-group">
						            <input type="text" class="form-control input-sm" 
						            	ng-model="busca.produtos"
						            	ng-enter="loadProdutos(0,10)">
						            <div class="input-group-btn">
						            	<button ng-click="loadProdutos(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            	<button type="button" class="btn btn-sm btn-default" ng-click="resetFilter()">Limpar</button>
						            	<button class="btn btn-sm btn-success hidden-print" ng-click="doExportExcel()"><i class="fa fa-file-excel-o"></i> Exportar p/ Excel</button>
						            </div>
						        </div>
							</div>
						</div>

						<br>

						<div class="row">
							<div class="col-sm-12 table-responsive">
								<table id="registro" class="table table-bordered table-condensed table-striped table-hover">
									<thead>
										<tr>
											<th class="text-center">#</th>
											<th>Código</th>
											<th class="text-center">Descrição</th>
											<th class="text-center">Categoria</th>
											<th class="text-center">Fabricante</th>
											<th class="text-center" width="80">Tamanho</th>
											<th class="text-center" width="100">Sabor/Cor</th>
											<th class="text-center" width="60">Estoque</th>
											<th class="text-center" width="100" ng-if="existeTabelaPreco('atacado')">Vlr. Atacado</th>
											<th class="text-center" width="100" ng-if="existeTabelaPreco('intermediario')">Vlr. Intermediário</th>
											<th class="text-center" width="100" ng-if="existeTabelaPreco('varejo')">Vlr. Varejo</th>
											<th width="80" style="text-align: center;">Opções</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-if="produtos != null && produtos.length == 0">
											<td class="text-center" colspan="11">
												<i class="fa fa-refresh fa-spin"></i> Aguarde, carregando itens...
											</td>
										</tr>
										<tr ng-if="produtos == null">
											<td class="text-center" colspan="11">
												Nenhum produto encontrado.
											</td>
										</tr>
										<tr ng-repeat="item in produtos" title="{{ configuracao.id_produto_debito_anterior_cliente == item.id_produto && 'este produto não pode ser deletado, ele faz parte das confurações internas do sistema' || '' }}">
											<!--<td class="text-center" width="80" 
												upload-File
												uploadextradata = '{"id_produto": {{ item.id }} }'
												deleteextradata = '{"id_produto": {{ item.id }} }'
												defaultPreviewContent = 'img/sem-imagem-produto.jpg'
												dataimg="{{ (item.img == null || item.img == '' ? null : 'assets/imagens/produtos/'+item.img) }}"
											>-->
											<td class="text-center" width="80">
											{{ item.id_produto }}
											</td>
											<td class="text-center">{{ item.codigo_barra }}</td>
											<td >{{ item.nome }}</td>
											<td>{{ item.descricao_categoria }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td class="text-center">{{ item.peso }}</td> 
											<td class="text-center">{{ item.sabor }}</td>
											<td class="text-center"><a href="#"  ng-click="qtdDepostito(item,$event)">{{ configuracao.id_produto_debito_anterior_cliente == item.id_produto && ' ' || item.qtd_item }}</a></td>
											<td class="text-right" ng-if="existeTabelaPreco('atacado')">R$ {{ item.vlr_venda_atacado | numberFormat: configuracoes.qtd_casas_decimais : ',' : '.' }}</td>
											<td class="text-right" ng-if="existeTabelaPreco('intermediario')">R$ {{ item.vlr_venda_intermediario | numberFormat: configuracoes.qtd_casas_decimais : ',' : '.' }}</td>
											<td class="text-right" ng-if="existeTabelaPreco('varejo')">R$ {{ item.vlr_venda_varejo | numberFormat: configuracoes.qtd_casas_decimais : ',' : '.' }}</td>
											<td align="center">
												<button type="button" ng-click="editar(item)" class="btn btn-xs btn-warning" title="editar" data-toggle="tooltip" ng-if="funcioalidadeAuthorized('inclusao_alteracao_produto')">
													<i class="fa fa-edit"></i>
												</button>
												<button type="button" ng-click="delete(item)" ng-if="funcioalidadeAuthorized('inclusao_alteracao_produto')"  ng-disabled="configuracao.id_produto_debito_anterior_cliente == item.id_produto" class="btn btn-xs btn-danger delete">
													<i class="fa fa-trash-o"></i>
												</button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="panel-footer clearfix">
						<div class="pull-right">
							<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.itens.length > 1">
								<li ng-repeat="item in paginacao.itens" ng-class="{'active': item.current}">
									<a href="" h ng-click="loadProdutos(item.offset,item.limit)">{{ item.index }}</a>
								</li>
							</ul>
						</div>
					</div>
					
				</div>
			</div>
		</div><!-- /main-container -->

		<!-- /Modal NCM -->
		<div class="modal fade" id="list-ncm" style="display:none">
  			<div class="modal-dialog modal-lg">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>NCM</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.ncm"  ng-enter="loadNcm(0,10)" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadNcm(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
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
									<tr ng-if="lista_ncm != false && (lista_ncm.length <= 0 || lista_ncm == null)">
										<th class="text-center" colspan="9" style="text-align:center"><strong>Carregando</strong><img src="assets/imagens/progresso_venda.gif"></th>
									</tr>
									<tr ng-if="lista_ncm == false">
										<th class="text-center" colspan="9" colspan="9" style="text-align:center">Não a resultados para a busca</th>
									</tr>
									<thead ng-show="(lista_ncm.length != 0)">
										<tr>
											<th >NCM</th>
											<th  width="56">EX TIPI</th>
											<th >Descrição</th>
											<th >Perc. IPI</th>
											<th colspan="2">selecionar</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in lista_ncm">
											<td>{{ item.cod_ncm }}</td>
											<td ng-if="item.ex_tipi == 0"></td>
											<td ng-if="item.ex_tipi > 0">{{ item.ex_tipi  }}</td>
											<td>{{ item.dsc_ncm | limitTo : 95 : 0 }} <a href="" style="text-decoration: underline;color: #000;" ng-if="item.dsc_ncm.length > 95">...</a></td>
											<td ng-if="item.num_percentual_ipi !=null">{{ item.num_percentual_ipi | numberFormat:2:',':'.' }}%</td>
											<td ng-if="item.num_percentual_ipi ==null">NT</td>
											<td width="50" align="center">
												<button type="button" class="btn btn-xs btn-success" ng-click="changeNcm(item)">
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
				    			<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao.especializacao_ncm.length > 1">
									<li ng-repeat="item in paginacao.especializacao_ncm" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadNcm(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
				    		</div>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal fornecedor-->
		<div class="modal fade" id="list_fornecedores" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Fornecedores</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.fornecedores" ng-enter="loadFornecedores(0,10)" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadFornecedores(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
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
									<thead ng-show="(fornecedores.length != 0)">
										<tr>
											<th colspan="2">Nome</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(fornecedores.length == 0)">
											<td colspan="3">Não há fornecedores cadastrados</td>
										</tr>
										<tr ng-repeat="item in fornecedores">
											<td>{{ item.nome_fornecedor }}</td>
											<td width="50" align="center">
												<button type="button" class="btn btn-xs btn-success" ng-click="addFornecedor(item)">
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
					    		<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao.fornecedores.length > 1">
									<li ng-repeat="item in paginacao.fornecedores" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadFornecedores(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
					    	</div>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal Insumos-->
		<div class="modal fade" id="list_insumos" style="display:none">
  			<div class="modal-dialog modal-lg">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Insumos</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.insumos" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadInsumos(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
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
									<thead>
										<tr>
											<th class="text-center">ID</th>
											<th>Nome</th>
											<th>Fabricante</th>
											<th>Tamanho</th>
											<th>Sabor/Cor</th>
											<th class="text-center" ng-if="(sublist_name == 'insumos')">R$ Custo</th>
											<th class="text-center" ng-if="(sublist_name == 'insumos')">Qtd.</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(!modal_insumos)">
											<td class="text-center" colspan="8">Nenhum produto encontrado!</td>
										</tr>
										<tr ng-show="(modal_insumos.length == 0)">
											<td class="text-center" colspan="8">
												<i class="fa fa-spin fa-spinner"></i>
												Aguarde, carregando produtos...
											</td>
										</tr>
										<tr ng-repeat="item in modal_insumos">
											<td class="text-center">{{ item.id }}</td>
											<td>{{ item.nome }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td>{{ item.peso }}</td>
											<td>{{ item.sabor }}</td>
											<td class="text-right" ng-if="(sublist_name == 'insumos')">
												R$ {{ item.vlr_custo_real | numberFormat : 2 : ',' : '.' }}
											</td>
											<td width="80" ng-if="(sublist_name == 'insumos')">
												<input type="text" class="form-control input-xs text-center" 
													onKeyPress="return SomenteNumero(event);" 
													ng-model="item.qtd"
													ng-if="item.flg_unidade_fracao != 1"/>

												<input type="text" class="form-control input-xs text-center" 
													onKeyPress="return SomenteNumero(event);" 
													ng-model="item.qtd"
													ng-if="item.flg_unidade_fracao == 1"
													thousands-formatter precision="3"/>
											</td>
											<td width="50" align="center">
												<button type="button" class="btn btn-xs btn-success" 
													ng-if="!existsInsumo(item.id)"
													ng-click="addInsumo(item)">
													<i class="fa fa-check-square-o"></i> Selecionar
												</button>
												<button type="button" class="btn btn-primary btn-xs" disabled="disabled" 
													ng-if="existsInsumo(item.id)">
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
					    		<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao.modal_insumos.length > 1">
									<li ng-repeat="item in paginacao.modal_insumos" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadInsumos(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
					    	</div>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal Combinacoes-->
		<div class="modal fade" id="modal_combinacoes" style="display:none">
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
						            <input ng-model="busca.insumos" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadModalCombinacoes(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
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
									<thead ng-show="(modal_combinacoes.length != 0)">
										<tr>
											<th >ID</th>
											<th >Nome</th>
											<th >Fabricante</th>
											<th >Tamanho</th>
											<th >Sabor/cor</th>
											<th ></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(modal_combinacoes.length == 0)">
											<td colspan="6">Nenhum Produto Encontrado</td>
										</tr>
										<tr ng-show="(modal_combinacoes == null)">
											<td colspan="6" align="center"><i class='fa fa-refresh fa-spin'></i> Carregando.</td>
										</tr>
										<tr ng-repeat="item in modal_combinacoes">
											<td>{{ item.id }}</td>
											<td>{{ item.nome }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td>{{ item.peso }}</td>
											<td>{{ item.sabor }}</td>
											<td width="50" align="center">
												<button ng-if="!existsCombinacao(item.id)" type="button" class="btn btn-xs btn-success" ng-disabled="" ng-click="addCombinacao(item)">
													<i class="fa fa-check-square-o"></i> Selecionar
												</button>
												<button ng-if="existsCombinacao(item.id)"  ng-disabled="true" class="btn btn-primary btn-xs" type="button">
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
					    		<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao.modal_combinacoes.length > 1">
									<li ng-repeat="item in paginacao.modal_combinacoes" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadInsumos(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
					    	</div>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->
		<!-- /Modal depositos-->
		<div class="modal fade" id="modal-depositos" style="display:none">
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
											<th width="50">#</th>
											<th colspan="2">Nome</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(depositos.length == 0)">
											<td colspan="3">{{ busca_vazia.depositos  && 'Não há resultado para a busca' || 'Não há Depositos cadastrados' }}</td>
										</tr>
										<tr ng-repeat="item in depositos">
											<td>{{item.id}}</td>
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
					    		<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao.depositos.length > 1">
									<li ng-repeat="item in paginacao.depositos" ng-class="{'active': item.current}">
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
											<th colspan="2">Nome 
												<button type="button" class="btn btn-xs btn-success pull-right" id="addAllEmpreendimentos" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." ng-disabled="empreendimentoSelected(item)" ng-click="addAllEmpreendimento(item)">
													<i class="fa fa-check-square-o"></i> Selecionar Todos
												</button>
											</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(empreendimento.length == 0)">
											<td colspan="2">Não há empreendimentos cadastrados</td>
										</tr>
										<tr ng-repeat="item in empreendimentos">
											<td>{{ item.nome_empreendimento }}</td>
											<td width="50" align="center">
												<button type="button" class="btn btn-xs btn-success" ng-disabled="empreendimentoSelected(item)" ng-click="addEmpreendimento(item)">
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
		<!-- /Modal novo importador-->
		<div class="modal fade" id="modal-novo-importador" style="display:none">
  			<div class="modal-dialog modal-sm">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Novo Importador</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-sm-12">
								<div class="row">
						    		<div class="col-sm-12" id="nome_importador">
						    			<label class="control-label">importador:</label>
						    			<div class="form-group ">
						    					<input ng-model="importador.nome_importador" type="text"  class="form-control input-sm" ng-enter="salvarImportador()">
						    			</div>
						    		</div>
						    	</div>
							</div>
						</div>
				    </div>
				    <div class="modal-footer clearfix">
				    	<div class="pull-right">
					    	<button type="button" data-loading-text=" Aguarde..."
					    		class="btn btn-md btn-default" ng-click="cancelarModal('modal-novo-importador')" id="btn-aplicar-sangria">
					    		<i class="fa fa-times-circle"></i> Cancelar
					    	</button>
				    		<button type="button" data-loading-text=" Aguarde..." class="btn btn-md btn-success"
					    		id="btn-salvar-importador" ng-click="salvarImportador()">
					    		<i class="fa fa-save"></i> Salvar
					    	</button>

				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->
		<!-- /Modal novo tamanho-->
		<div class="modal fade" id="modal-novo-tamanho" style="display:none">
  			<div class="modal-dialog modal-sm">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Novo Tamanho</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-sm-12">
								<div class="row">
						    		<div class="col-sm-12" id="nome_tamanho">
						    			<label class="control-label">tamanho:</label>
						    			<div class="form-group ">
						    					<input ng-model="tamanho.nome_tamanho" type="text"  class="form-control input-sm" ng-enter="salvarTamanho">
						    			</div>
						    		</div>
						    	</div>
							</div>
						</div>
				    </div>
				    <div class="modal-footer clearfix">
				    	<div class="pull-right">
				    		<button type="button" data-loading-text=" Aguarde..."
					    		class="btn btn-md btn-default" ng-click="cancelarModal('modal-novo-tamanho')" id="btn-aplicar-sangria">
					    		<i class="fa fa-times-circle"></i> Cancelar
					    	</button>
					    	<button type="button" data-loading-text=" Aguarde..." class="btn btn-md btn-success"
					    		id="btn-salvar-tamanho" ng-click="salvarTamanho()">
					    		<i class="fa fa-save"></i> Salvar
					    	</button>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->
		<!-- /Modal novo fabricante-->
		<div class="modal fade" id="modal-novo-fabricante" style="display:none">
  			<div class="modal-dialog modal-sm">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Novo Fabricante</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-sm-12">
								<div class="row">
						    		<div class="col-sm-12" id="nome_fabricante">
						    			<label class="control-label">Fabricante:</label>
						    			<div class="form-group ">
						    					<input ng-model="fabricante.nome_fabricante" type="text"  class="form-control input-sm" ng-enter="salvarFabricante()">
						    			</div>
						    		</div>
						    	</div>
							</div>
						</div>
				    </div>
				    <div class="modal-footer clearfix">
				    	<div class="pull-right">
					    	<button type="button" data-loading-text=" Aguarde..."
					    		class="btn btn-md btn-default" ng-click="cancelarModal('modal-novo-fabricante')">
					    		<i class="fa fa-times-circle"></i> Cancelar
					    	</button>
				    		<button type="button" data-loading-text=" Aguarde..." class="btn btn-md btn-success"
					    		id="btn-salvar-fabricante" ng-click="salvarFabricante()">
					    		<i class="fa fa-save"></i> Salvar
					    	</button>

				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->
		<!-- /Modal novo Sabor/Cor-->
		<div class="modal fade" id="modal-nova-cor" style="display:none">
  			<div class="modal-dialog modal-sm">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Nova Sabor/Cor</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-sm-12">
								<div class="row">
						    		<div class="col-sm-12" id="nome_cor">
						    			<label class="control-label">Sabor/Cor:</label>
						    			<div class="form-group ">
						    					<input ng-model="cor_produto.nome_cor" type="text"  class="form-control input-sm" ng-enter="salvarCorProduto()" >
						    			</div>
						    		</div>
						    	</div>
							</div>
						</div>
				    </div>
				    <div class="modal-footer clearfix">
				    	<div class="pull-right">
					    	<button type="button" data-loading-text=" Aguarde..."
					    		class="btn btn-md btn-default" ng-click="cancelarModal('modal-nova-cor')" id="btn-aplicar-sangria">
					    		<i class="fa fa-times-circle"></i> Cancelar
					    	</button>
					    	<button type="button" data-loading-text=" Aguarde..." class="btn btn-md btn-success"
					    		id="btn-salvar-cor" ng-click="salvarCorProduto()">
					    		<i class="fa fa-save"></i> Salvar
					    	</button>

				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal novo categoria-->
		<div class="modal fade" id="modal-nova-categoria" style="display:none">
  			<div class="modal-dialog modal-sm">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Nova Categoria</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-sm-12">
								<div class="row">
						    		<div class="col-sm-12" id="descricao_categoria">
						    			<label class="control-label">Categoria:</label>
						    			<div class="form-group ">
						    					<input ng-model="categoria.descricao_categoria" type="text" class="form-control input-sm" ng-enter="salvarCategoria()">
						    			</div>
						    		</div>
						    	</div>
							</div>
						</div>
				    </div>
				    <div class="modal-footer clearfix">
				    	<div class="pull-right">
					    	<button type="button" data-loading-text=" Aguarde..."
					    		class="btn btn-md btn-default" ng-click="cancelarModal('modal-novo-categoria')" id="btn-aplicar-sangria">
					    		<i class="fa fa-times-circle"></i> Cancelar
					    	</button>
					    	<button type="button" data-loading-text=" Aguarde..." class="btn btn-md btn-success"
					    		id="btn-salvar-categoria" ng-click="salvarCategoria()">
					    		<i class="fa fa-save"></i> Salvar
					    	</button>

				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->
		<!-- /Modal novo Combinações-->
		<div class="modal fade" id="modal-add-combinacao" style="display:none">
  			<div class="modal-dialog modal-lg">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Nova Combinação</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-sm-4">
								<div class="form-group" id="codigo_barra">
									<label class="control-label"><i class="fa fa-barcode"></i> Código de Barras</label>
									<input   ng-model="combinacao.codigo_barra" type="text"  class="form-control input-sm" onKeyPress="return SomenteNumero(event);">
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group" id="peso">
										<label class="control-label">Tamanho </label>
										<select chosen ng-change="ClearChosenSelect('combinacao');addCombinacaoDefault()"
									    option="tamanhos"
									    ng-model="combinacao.id_tamanho"
									    ng-options="tamanho.id as tamanho.nome_tamanho for tamanho in tamanhos">
										</select>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group" id="peso">
										<label class="control-label">Sabor/Cor</label>
										<select chosen ng-change="ClearChosenSelect('cor');addCombinacaoDefault()"
									    option="cores"
									    ng-model="combinacao.id_cor"
									    ng-options="cor.id as cor.nome_cor for cor in cores">
										</select>
								</div>
							</div>
						</div>
						<div class="table-responsive">
							<table class="table table-bordered table-condensed table-striped table-hover">
								<thead>
									<tr>
										<th class="text-center" rowspan="2" style="line-height: 46px;width: 200px">Empreendimento</th>
										<th class="text-center" rowspan="2" style="line-height: 46px" >Vlr. Tabela</th>
										<th class="text-center" colspan="2">Vlr. Atacado</th>
										<th class="text-center" colspan="2">Vlr. Intermediário</th>
										<th class="text-center" colspan="2">Vlr. Varejo</th>
									</tr>
									<tr>
										<td class="text-center">%</td>
										<td class="text-center">R$</td>
										<td class="text-center">%</td>
										<td class="text-center">R$</td>
										<td class="text-center">%</td>
										<td class="text-center">R$</td>
									</tr>
								</thead>
								<tbody ng-if="combinacao.precos.length == 0">
									<tr>
										<td colspan="9" class="text-center">Nenhum empreendimento vinculado ao produto</td>
									</tr>
								</tbody>
								<tbody ng-repeat="preco in combinacao.precos">
									<tr>
										<td>
											#{{preco.id_empreendimento}} - {{ preco.nome_empreendimento }}
										</td>
										<td>
											<input ng-model="preco.vlr_custo" ng-keyup="calcularAllMargens(preco)"  thousands-formatter type="text" class="form-control input-xs parsley-validated">
										</td>
										<td>
											<input ng-model="preco.perc_venda_atacado" ng-keyup="calculaMargens('atacado','margem',preco)"  ng-disabled="preco.vlr_custo == null || preco.vlr_custo == ''"  thousands-formatter   type="text" class="form-control input-xs parsley-validated maskPorcentagem">
										</td>
										<td>
											<input ng-model="preco.valor_venda_atacado" ng-keyup="calculaMargens('atacado','valor',preco)" 
											 ng-disabled="preco.vlr_custo == null || preco.vlr_custo == ''"  thousands-formatter   type="text" class="form-control input-xs parsley-validated maskPorcentagem">
										</td>

										<td>
											<input ng-model="preco.perc_venda_intermediario" ng-keyup="calculaMargens('intermediario','margem',preco)"  ng-disabled="preco.vlr_custo == null || preco.vlr_custo == ''"  thousands-formatter   type="text" class="form-control input-xs parsley-validated maskPorcentagem">
										</td>
										<td>
											<input ng-model="preco.valor_venda_intermediario" ng-keyup="calculaMargens('intermediario','valor',preco)"  ng-disabled="preco.vlr_custo == null || preco.vlr_custo == ''"  thousands-formatter   type="text" class="form-control input-xs parsley-validated maskPorcentagem">
										</td>
										<td>
											<input ng-model="preco.perc_venda_varejo" ng-keyup="calculaMargens('varejo','margem',preco)"  ng-disabled="preco.vlr_custo == null || preco.vlr_custo == ''"  thousands-formatter   type="text" class="form-control input-xs parsley-validated maskPorcentagem">
										</td>
										<td>
											<input ng-model="preco.valor_venda_varejo" ng-keyup="calculaMargens('varejo','valor',preco)"  ng-disabled="preco.vlr_custo == null || preco.vlr_custo == ''"  thousands-formatter   type="text" class="form-control input-xs parsley-validated maskPorcentagem">
										</td>
									</tr>
								</tbody>
							</table>
						</div>
				    </div>
				    <div class="modal-footer clearfix">
				    	<div class="pull-right">
				    		<button type="button" data-loading-text=" Aguarde..."
					    		class="btn  btn-md btn-default" ng-click="cancelarModal('modal-add-combinacao')">
					    		<i class="fa fa-times-circle"></i> Cancelar
					    	</button>
					    	<button type="button" data-loading-text=" Aguarde..." class="btn  btn-md btn-success"
					    		id="btn-salvar-fabricante" ng-click="incluirCombinacao()">
					    		<i class="fa fa-save"></i> Incluir
					    	</button>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->
		<!-- /Modal depositos-->
		<div class="modal fade" id="modal-combinacao" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Combinação</span></h4>
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
											<th width="50">#</th>
											<th>Tamanho</th>
											<th>Sabor/Cor</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(depositos.length == 0)">
											<td colspan="3">Não há Depositos cadastrados</td>
										</tr>
										<tr ng-repeat="item in combinacoes">
											<td>{{item.id}}</td>
											<td>{{ item.peso }}</td>
											<td>{{ item.sabor }}</td>
											<td width="50" align="center">
												<button type="button" class="btn btn-xs btn-success" ng-click="addCombinacaoEstoque(item)">
													<i class="fa fa-check-square-o"></i> Selecionar
												</button>
											</td>
										</tr>
										<tr ng-repeat="item in combinacoes_sem_cadastro">
											<td>{{item.id}}</td>
											<td>{{ item.peso }}</td>
											<td>{{ item.sabor }}</td>
											<td width="50" align="center">
												<button type="button" class="btn btn-xs btn-success" ng-click="addCombinacaoEstoque(item)">
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
					    		<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao.depositos.length > 1">
									<li ng-repeat="item in paginacao.depositos" ng-class="{'active': item.current}">
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

	<!-- Jquery Form-->
	<script src='js/jquery.form.js'></script>

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

	<!-- Mascaras para o formulario de produtos -->
	<script src="js/scripts/mascaras.js"></script>

	<script src="js/Trumbowyg-master/dists/trumbowyg.js"></script>

	<!-- Moment -->
	<script src="js/moment/moment.min.js"></script>

	<script src="js/jquery.noty.packaged.js"></script>

	<!-- Bower Components -->	
	<script src="bower_components/noty/lib/noty.min.js" type="text/javascript"></script>
    <script src="bower_components/mojs/build/mo.min.js" type="text/javascript"></script>

	<!-- Extras -->
	<script src="js/extras.js"></script>

	<!-- UnderscoreJS -->
	<script type="text/javascript" src="bower_components/underscore/underscore.js"></script>

	<script src="js/fileinput/fileinput.js" type="text/javascript"></script>
    <script src="js/fileinput/locales/pt-BR.js" type="text/javascript"></script>

    <!-- ease -->
	<script src="js/jquery.ease.js"></script>

	<!-- accounting -->
	<script type="text/javascript" src="js/accounting.min.js"></script>

    <script src="js/jquery.noty.packaged.js"></script>

    <script src="js/bootstrap-treeview.js"></script>

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
    <script src="js/auto-complete/ng-sanitize.js"></script>
    <script src="js/angular-chosen.js"></script>
    <script type="text/javascript">
    	$('#descricao_html').trumbowyg();
    	$('#descricao_html_curta').trumbowyg();
    	var addParamModule = ['angular.chosen'] ;
    </script>
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/produtos-controller.js?<?php echo filemtime('js/angular-controller/produtos-controller.js')?>"></script>
	<script type="text/javascript">
		//$(".chzn-select").chosen();
		 $('[data-toggle="tooltip"]').tooltip()
		/*$('.foto-produto').change(function()	{
			var filename = $(this).val().split('\\').pop();
			$(this).parent().find('span').attr('data-title',filename);
			$(this).parent().find('label').attr('data-title','Trocar foto');
			$(this).parent().find('label').addClass('selected');
		});*/
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

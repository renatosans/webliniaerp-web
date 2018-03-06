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
			#list_precos.modal-dialog  {width:900px;}
			#list_detalhes.modal-dialog  {width:900px;}
		}

		#list_detalhes .modal-dialog  {width:70%;}
		/*#list_detalhes .modal-content {min-height: 640px;}*/

		#list_precos .modal-dialog  {width:70%;}
		#list_precos .modal-content {min-height: 640px;}
	</style>
  </head>

  <body class="overflow-hidden" ng-controller="EstoqueController" ng-cloak>
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
					 <li><i class="fa fa-sitemap"></i> <a href="depositos.php">Depósitos</a></li>
					 <li class="active"><i class="fa fa-list-ol"></i> Controle de Estoque</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-list-ol"></i> Controle de Estoque</h3>
					<br/>
					<a class="btn btn-info" id="btn-novo" ng-disabled="editing" ng-click="showBoxNovo()"><i class="fa fa-plus-circle"></i> Nova Entrada</a>
					<a href="inventario.php" class="btn btn-primary"><i class="fa fa-tags"></i> Inventário</a>
					<a href="baixa_estoque.php" class="btn btn-primary"><i class="fa fa-caret-square-o-down"></i> Baixa Manual</a>
					<a href="pedido_transferencia.php" class="btn btn-primary"><i class="fa fa-arrows-h"></i> Transferência</a> 
					<a href="ordem_producao.php" class="btn btn-primary"><i class="fa fa-wrench"></i> Ordem de Produção</a>
					<a href="zera-estoque.php" class="btn btn-danger"><i class="fa fa-trash-o"></i> Limpeza de Estoque</a>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md">
				<div class="panel panel-default" id="box-novo" style="display:none;">
					<div class="panel-heading"><i class="fa fa-plus-circle"></i> Entre com os dados da Nota-Fiscal</div>

					<div class="panel-body">
						<form id="form-xml" class="form">
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group" id="xml_nfe">
										<label class="control-label" 
											ng-show="editing == false || (nota.xml_nfe == '' || nota.xml_nfe == null)">
											<i class="fa fa-file-code-o"></i> XML da NF-e
										</label>
										<a href="assets/arquivos_nfe/{{ nota.xml_nfe }}"  target="_blank">
											<label style="cursor: pointer;" class="control-label" 
												ng-hide="editing == false || (nota.xml_nfe == '' || nota.xml_nfe == null)">
												<i class="fa fa-file-code-o"></i> XML da NF-e
											</label>
										</a>
										<div class="upload-file">
											<input id="arquivo-nota" name="arquivo-nota" class="foto-nota" type="file" data-file="nota.foto" accept="text/xml"/>
											<label data-title="Selecione..." for="arquivo-nota" style="background-color: #eee;">
												<span data-title="{{ nota.xml_nfe }}"></span>
											</label>
										</div>
									</div>
								</div>

								<!--<div class="col-sm-3">
									<div class="form-group">
										<label for="" class="control-label">Cadastrar produtos não encontrados?</label>
										<div class="form-group">
											<label class="label-radio inline">
												<input name="flg_cadastra_produto_nao_encontrado" value="1" type="radio" class="inline-radio"
													ng-model="nota.flg_cadastra_produto_nao_encontrado">
												<span class="custom-radio"></span>
												<span>Sim</span>
											</label>
											<label class="label-radio inline">
												<input name="flg_cadastra_produto_nao_encontrado" value="0" type="radio" class="inline-radio"
													ng-model="nota.flg_cadastra_produto_nao_encontrado">
												<span class="custom-radio"></span>
												<span>Não</span>
											</label>
										</div>
									</div>
								</div>-->

								<div class="col-sm-4">
									<div class="form-group">
										<label class="control-label"><br/></label>
										<div class="controls">
											<button id="loadXMLButton" type="button" class="btn btn-sm btn-info" 
												ng-click="loadDataFromXML()" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Aguarde, carregando...">
												<i class="fa fa-file-code-o"></i> Carregar dados a partir do XML da NF-e
											</button>
										</div>
									</div>
								</div>
							</div>
						</form>
						
						<form role="form">
							<div class="row">
								<div class="col-sm-5">
									<div class="form-group" id="nme_fornecedor">
										<label class="control-label">Fornecedor</label>
										<div class="input-group">
											<input ng-model="nota.nme_fornecedor" ng-click="showFornecedores()" type="text" class="form-control input-sm" readonly="readonly" style="background-color: #FFF;cursor:pointer">
											<span class="input-group-addon" ng-click="showFornecedores()"><i class="fa fa-tasks"></i></span>
										</div>
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group" id="id_pedido_fornecedor">
										<label class="control-label">Pedido</label>
										<div class="input-group">
											<input ng-model="nota.id_pedido_fornecedor" ng-click="showPedidos()" type="text" class="form-control input-sm" readonly="readonly" style="background-color: #FFF;cursor:pointer">
											<span  ng-click="showPedidos()" class="input-group-addon"><i class="fa fa-tasks"></i></span>
										</div>
									</div>
								</div>

								<div class="col-sm-5">
									<div class="form-group" id="nme_deposito">
										<label class="control-label">Depósito</label>
										<div class="input-group">
											<input ng-model="nota.nme_deposito" ng-click="showDepositos()"   type="text" class="form-control input-sm" readonly="readonly" style="background-color: #FFF;cursor:pointer">
											<span  ng-click="showDepositos()" class="input-group-addon"><i class="fa fa-tasks"></i></span>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-2">
									<div class="form-group" id="dta_entrada">
										<label class="control-label">Data do Recebimento</label>
										<div class="input-group">
											<input readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="pagamentoData" class="datepicker form-control">
											<span class="input-group-addon" id="cld_pagameto"><i class="fa fa-calendar"></i></span>
										</div>
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group" id="num_nota_fiscal">
										<label class="control-label">Número NF</label>
										<input ng-model="nota.num_nota_fiscal" type="text" class="form-control">
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group" id="vlr_frete">
										<label class="control-label">Total Frete</label>
										<input type="text" class="form-control text-right" 
											ng-KeyUp="atualizaValorTotal()"
											ng-model="nota.vlr_frete" thousands-formatter>
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group" id="vlr_total_nota_fiscal">
										<label class="control-label">Total NF</label>
										<input type="text" class="form-control text-right" readonly="readonly"
											value="R$ {{ nota.vlr_total_nota_fiscal | numberFormat : 2 : ',' : '.' }}">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-2">
									<div class="form-group">
										<label class="control-label"><br></label>
										<button type="button" class="btn btn-default form-control" ng-click="selProduto()">
											<i class="fa fa-plus-circle"></i> Incluir Produto
										</button>
									</div>
								</div>

								<div class="col-sm-3">
									<div class="form-group">
										<label class="control-label"><br></label>
										<button class="btn btn-block form-control" ng-click="addFocus()" 
											ng-class="{ 'btn-info' : (busca_cod_barra == false), 'btn-success' : (busca_cod_barra == true) }">
											<i class="fa fa-barcode"></i> Ler Código de Barras
										</button>
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="control-label"><br></label>
										<label class="label-checkbox">
											<input ng-model="nota.flg_especificar_natureza_itens" type="checkbox" id="toggleLine" ng-true-value="1" ng-false-value="0">
											<span class="custom-checkbox"></span>
											Especificar natureza dos itens
										</label>
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="control-label"><br></label>
										<label class="label-checkbox">
											<input ng-model="nota.flg_alterar_valor_custo" type="checkbox" id="toggleLine" ng-true-value="1" ng-false-value="0">
											<span class="custom-checkbox"></span>
											Alterar valor de custo
										</label>
									</div>
								</div>
							</div>

							<br>

							<div class="row">
								<div class="col-sm-12">
									<table class="table table-bordered table-condensed table-striped table-hover">
										<caption>Itens recebidos</caption>
										<thead>
											<tr>
												<th>Produto</th>
												<th class="text-center"
													ng-if="(nota.flg_especificar_natureza_itens == 1)">
													Natureza
												</th>
												<th>Fabricante</th>
												<th>Tamanho</th>
												<th>Cor/Sabor</th>
												<th style="width: 60px; text-align: center;" colspan="2">Qtd</th>
												<th class="text-center" width="100" 
													ng-show="nota.flg_alterar_valor_custo == 1">
													Custo (R$)
												</th>
												<th style="width: 120px; text-align: center;"
													ng-show="nota.flg_alterar_valor_custo == 1">
													SubTotal
												</th>
												<th style="width: 100px; text-align: center;">
													<button ng-click="deleteItem()" type="button" class="btn btn-xs btn-danger">
														<i class="fa fa-trash-o"></i> Remover Todos
													</button>
												</th>
											</tr>
										</thead>
										<tbody>
											<tr ng-hide="nota.itens.length > 0">
												<td colspan="10">
													Nenhum item adicionado
												</td>
											</tr>
											<tr ng-class="{'danger': (item.flg_localizado == false)}"
												ng-repeat="($index, item) in nota.itens | orderBy: 'nome_produto' : false track by $index">
												<td class="text-middle clearfix">
													<span class="pull-left">#{{ item.nome_produto }} - {{ item.nome_produto }}</span>
													<span class="pull-right">
														<button type="button" 
															class="btn btn-xs btn-primary" 
															ng-click="selProduto(item)">
															<i class="fa fa-archive"></i>
														</button>
													</span>
												</td>
												<td class="text-center text-middle" width="200"
													ng-if="(nota.flg_especificar_natureza_itens == 1)">
													<select chosen
														option="plano_contas"
														ng-model="item.id_natureza"
														ng-options="plano.id as plano.dsc_completa for plano in plano_contas">
													</select>
												</td>
												<td class="text-middle">{{ item.nome_fabricante }}</td>
												<td class="text-middle">{{ item.peso }}</td>
												<td class="text-middle">{{ item.sabor }}</td>
												<td class="text-center text-middle">{{ item.qtd }}</td>
												<td class="text-middle" style="width: 32px;">
													<button type="button" 
														class="btn btn-xs btn-primary" 
														ng-click="showValidades(item)">
														<i class="fa fa-calendar"></i>
													</button>
												</td>
												<td class="text-middle" ng-show="nota.flg_alterar_valor_custo == 1">
													<input type="text" class="form-control input-xs text-right"
														thousands-formatter 
														precision='{{ configuracao.qtd_casas_decimais }}' 
														ng-model="item.custo" 
														ng-keyup="atualizaValores();" 
														ng-blur="atualizaValorTotal();">
												</td>
												<td class="text-middle text-right" 
													ng-show="nota.flg_alterar_valor_custo == 1">
													R$ {{ item.total | numberFormat : 2 : ',' : '.' }}
												</td>
												<td class="text-middle text-center">
													<button ng-click="deleteItem(item)" type="button" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Remover Item</button>
												</td>
											</tr>
											<tr style="font-weight: bold;" ng-show="nota.itens.length > 0">
												<td colspan="5" style="text-align: right;">TOTAIS</td>
												<td style="text-align: center;">{{ qtd_total_entrada }}</td>
												<td colspan="2" ng-show="nota.flg_alterar_valor_custo == 1"></td>
												<td style="text-align: right;"
													ng-show="nota.flg_alterar_valor_custo == 1">
													R$ {{ valor_total_entrada | numberFormat : 2 : ',' : '.' }}
												</td>
												<td colspan="{{ (nota.flg_alterar_valor_custo == 1) ? 1 : 2 }}"></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-5">
									<table class="table table-bordered table-condensed table-striped table-hover">
										<caption>Duplicatas</caption>
										<thead>
											<th class="text-center">Número</th>
											<th class="text-center">Vencimento</th>
											<th class="text-right">Valor</th>
											<th class="text-center" width="30">
												<button type="button" class="btn btn-xs btn-info" 
													ng-click="showModalDuplicatas()">
													<i class="fa fa-plus-circle"></i>
												</button>
											</th>
										</thead>
										<tbody>
											<tr ng-repeat="dup in nota.duplicatas">
												<td class="text-center">{{ dup.num_duplicata }}</td>
												<td class="text-center">{{ dup.dta_vencimento | dateFormat: 'date' }}</td>
												<td class="text-right">R$ {{ dup.vlr_duplicata | numberFormat : 2 : ',' : '.' }}</td>
												<td class="text-center">
													<button type="button" class="btn btn-xs btn-danger"
														tooltip="Excluir duplicata" data-toggle="tooltip"
														ng-click="deleteDuplicata(dup)">
														<i class="fa fa-trash-o"></i>
													</button>
												</td>
											</tr>
										</tbody>
									</table>
								</div>

								<div class="col-sm-7" ng-if="(nota.duplicatas)">
									<div class="row" style="height: 300px">
										<div class="col-lg-6">
											<div class="form-group" id="id_conta_bancaria_duplicatas">
												<label class="control-label">Conta Bancária p/ Lançamento das Duplicatas</label>
												<select  chosen
													option="contas_bancarias"
													ng-model="nota.id_conta_bancaria_duplicatas"
													ng-options="conta.id as conta.dsc_conta_bancaria for conta in contas_bancarias">
												</select>
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group" id="id_plano_contas_duplicatas">
												<label class="control-label">Plano de Contas p/ Lançamento das Duplicatas</label>
												<select chosen
													option="plano_contas"
													ng-model="nota.id_plano_contas_duplicatas"
													ng-options="plano.id as plano.dsc_completa for plano in plano_contas">
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-12">
									<div class="pull-right">
										<button ng-click="showBoxNovo(); reset();" id="btn-limpa-form" type="submit" class="btn btn-default btn-sm">
											<i class="fa fa-times-circle"></i> Cancelar
										</button>
										<button id="btn-salvar-entrada" type="submit" class="btn btn-success btn-sm"
											data-loading-text="<i class='fa fa-refresh fa-spin'></i> Salvando, Aguarde..." 
											ng-click="salvar()">
											<i class="fa fa-save"></i> Salvar
										</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div><!-- /panel -->

				<div class="alert alert-sistema" style="display:none"></div>

				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-filter"></i> Opções de Filtro
					</div>

					<div class="panel-body">
						<div class="row">
							<div class="col-sm-2">
								<div class="form-group">
								<label class="control-label">Data do Recebimento</label>
									<div class="input-group">
										<input readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="datarecebimento" class="datepicker form-control text-center">
										<span class="input-group-addon" id="cld_datarecebimento"><i class="fa fa-calendar"></i></span>
									</div>
								</div>
							</div>

							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label">Usuário</label>
									<input ng-model="busca.nme_usuario" ng-enter="loadEntradas(0,10)" ng-keyup="loadEntradas(0,10)"  type="text" class="form-control input-sm ng-pristine ng-valid ng-touched">
								</div>
							</div>

							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label">Fornecedor</label>
									<input ng-model="busca.fornecedor" ng-enter="loadEntradas(0,10)"  ng-keyup="loadEntradas(0,10)" type="text" class="form-control input-sm ng-pristine ng-valid ng-touched">
								</div>
							</div>

							<div class="col-sm-1">
								<div class="form-group">
									<label class="control-label">Nº NF</label>
									<input ng-model="busca.notafiscal" ng-enter="loadEntradas(0,10)" type="text"  ng-keyup="loadEntradas(0,10)" class="form-control input-sm ng-pristine ng-valid ng-touched">
								</div>
							</div>

							<div class="col-sm-1">
								<div class="form-group">
									<label class="control-label">Pedido</label>
									<input ng-model="busca.pedido" ng-enter="loadEntradas(0,10)" ng-keyup="loadEntradas(0,10)"  type="text" class="form-control input-sm ng-pristine ng-valid ng-touched">
								</div>
							</div>

							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label">Depósito</label>
									<input ng-model="busca.dep_entrada" ng-enter="loadEntradas(0,10)" ng-keyup="loadEntradas(0,10)" type="text" class="form-control input-sm ng-pristine ng-valid ng-touched">
								</div>
							</div>

							<div class="col-sm-1">
								<div class="form-group">
									<label class="control-label"><br></label>
									<button type="button" class="btn btn-sm btn-primary" ng-click="loadEntradas(0,10)" ><i class="fa fa-filter"></i> Filtrar</button>
								</div>
							</div>

							<div class="col-sm-1">
								<div class="form-group">
									<label class="control-label"><br></label>
									<button type="button" class="btn btn-sm btn-block btn-default" ng-click="resetFilter()">Limpar</button>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-tasks"></i> Entradas</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-12">
								<div class="alert alert-entrada-lista" style="display:none"></div>
							</div>
						</div>

						<table class="table table-bordered table-condensed table-striped table-hover">
							<thead ng-show="ultimasEntradas.length > 0">
								<tr>
									<th>#</th>
									<th width="150">Data do Recebimento</th>
									<th>Usuário</th>
									<th>Fornecedor</th>
									<th>N° NF-e</th>
									<th>Total NF-e</th>
									<th width="100">Pedido</th>
									<th>Depósito</th>
									<th width="80" style="text-align: center;">Detalhes</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-hide="ultimasEntradas.length > 0">
									<td colspan="6">
										Não há entradas cadastradas
									</td>
								</tr>
								<tr ng-repeat="item in ultimasEntradas" ng-show="ultimasEntradas.length > 0">
									<td>#{{ item.id }}</td>
									<td>{{ item.dta_entrada | dateFormat : 'date' }}</td>
									<td>{{ item.nome_usuario }}</td>
									<td>{{ item.nome_fornecedor }}</td>
									<td>{{ item.num_nota_fiscal }}</td>
									<td>{{ item.vlr_total_nota_fiscal | numberFormat : 2 : ',' : '.' }}</td>
									<td>{{ item.id_pedido_fornecedor }}</td>
									<td>{{ item.nme_deposito }}</td>
									<td align="center">
										<button type="button" ng-click="showDetalhes(item)" tooltip="Detalhes" class="btn btn-xs btn-info" data-toggle="tooltip">
											<i class="fa fa-tasks"></i>
										</button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="panel-footer clearfix">
						<div class="pull-right">
							<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.entradas.length > 1">
								<li ng-repeat="item in paginacao.entradas" ng-class="{'active': item.current}">
									<a href="" h ng-click="loadEntradas(item.offset,item.limit)">{{ item.index }}</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /main-container -->

		<!-- /Modal Produtos-->
		<div class="modal fade" id="list_produtos" style="display:none">
  			<div class="modal-dialog modal-lg">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Produtos</h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input type="text" class="form-control input-sm"
						            	ng-model="pesquisa.produto" 
						            	ng-enter="loadProdutos()"
						            	ng-disabled="enableNewFormProduto">
						            <div class="input-group-btn">
						            	<button type="button" class="btn btn-sm btn-primary" 
						            		ng-click="loadProdutos()"
						            		ng-disabled="enableNewFormProduto">
						            		<i class="fa fa-search"></i> Buscar
					            		</button>

					            		<button type="button" class="btn btn-sm btn-info" 
					            			ng-click="enableNewFormProduto = !enableNewFormProduto">
						            		<i class="fa {{ (!enableNewFormProduto) ? 'fa-plus-circle' : 'fa-minus-circle' }} "></i> Cadastrar
						            	</button>
						            </div> <!-- /input-group-btn -->
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>

						<br/>

						<div class="row" ng-show="enableNewFormProduto">
							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label">
										<i class="fa fa-barcode"></i> Código de Barras
									</label>
									<input class="form-control input-sm" ng-model="new_produto.codigo_barra"/>
								</div>
							</div>

							<div class="col-sm-6">
								<div class="form-group">
									<label class="control-label">
										Descrição do Produto
									</label>
									<input class="form-control input-sm" ng-model="new_produto.nome"/>
								</div>
							</div>

							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label">
										Tamanho
									</label>
									<select chosen option="tamanhos"
										ng-change="clearChosenSelect('tamanho', 'id')"
										ng-model="new_produto.id_tamanho"
										ng-options="tamanho.id as tamanho.nome_tamanho for tamanho in tamanhos">
									</select>
								</div>
							</div>

							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label">
										Sabor/Cor
									</label>
									<select chosen option="cores"
										ng-change="clearChosenSelect('cor', 'id')"
										ng-model="new_produto.id_cor"
										ng-options="cor.id as cor.nome_cor for cor in cores">
									</select>
								</div>
							</div>
						</div>

						<div class="row" ng-show="enableNewFormProduto">
							<div class="col-sm-4">
								<div class="form-group">
									<label class="control-label">
										Fabricante
									</label>
									<select chosen option="fabricantes"
										ng-change="clearChosenSelect('fabricante', 'id')"
										ng-model="new_produto.id_fabricante"
										ng-options="fabricante.id as fabricante.nome_fabricante for fabricante in fabricantes">
									</select>
								</div>
							</div>

							<div class="col-sm-4">
								<div class="form-group">
									<label class="control-label">
										Categoria
									</label>
									<select chosen option="categorias"
										ng-change="clearChosenSelect('categoria', 'id')"
										ng-model="new_produto.id_categoria"
										ng-options="categoria.id as categoria.descricao_categoria for categoria in categorias">
									</select>
								</div>
							</div>
						</div>

						<div class="row" ng-show="enableNewFormProduto">
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label">
										Forma de Aquisição
									</label>
									<select chosen option="formas_aquisicao"
										ng-change="clearChosenSelect('forma_aquisicao', 'cod')"
										ng-model="new_produto.cod_forma_aquisicao"
										ng-options="forma_aquisicao.cod_controle_item_nfe as forma_aquisicao.nme_item for forma_aquisicao in formas_aquisicao">
									</select>
								</div>
							</div>

							<div class="col-sm-6">
								<div class="form-group">
									<label class="control-label">
										Origem da Mercadoria
									</label>
									<select chosen option="origens_mercadoria"
										ng-change="clearChosenSelect('origem_mercadoria', 'cod')"
										ng-model="new_produto.cod_origem_mercadoria"
										ng-options="origem_mercadoria.cod_controle_item_nfe as origem_mercadoria.nme_item for origem_mercadoria in origens_mercadoria">
									</select>
								</div>
							</div>

							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label">
										Tipo de Tributação IPI
									</label>
									<select chosen option="tipos_tributacao_ipi"
										ng-change="clearChosenSelect('tipo_tributacao_ipi', 'cod')"
										ng-model="new_produto.cod_tipo_tributacao_ipi"
										ng-options="tipo_tributacao_ipi.cod_controle_item_nfe as tipo_tributacao_ipi.nme_item for tipo_tributacao_ipi in tipos_tributacao_ipi">
									</select>
								</div>
							</div>
						</div>

						<div class="row" ng-show="enableNewFormProduto">
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label">
										Regra de Tributação
									</label>
									<select chosen option="regras_tributacao"
										ng-change="clearChosenSelect('regra_tributos', 'cod')"
										ng-model="new_produto.cod_regra_tributos"
										ng-options="regra_tributos.id as regra_tributos.nome_regra_tributos for regra_tributos in regras_tributacao">
									</select>
								</div>
							</div>

							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label">
										Código NCM
									</label>
									<input class="form-control input-sm" ng-model="new_produto.cod_ncm"/>
								</div>
							</div>

							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label">
										Código CEST
									</label>
									<input class="form-control input-sm" ng-model="new_produto.num_cest"/>
								</div>
							</div>

							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label">
										Unidade Medida
									</label>
									<input class="form-control input-sm" ng-model="new_produto.dsc_unidade_medida"/>
								</div>
							</div>
						</div>

				   		<div class="row" ng-show="!enableNewFormProduto">
				   			<div class="col-sm-12">
				   				<table class="table table-bordered table-condensed table-striped table-hover">
									<thead>
										<tr>
											<th>Nome</th>
											<th>Fabricante</th>
											<th >Tamanho</th>
											<th >Sabor/Cor</th>
											<th class="text-center" width="80">Qtd.</th>
											<th width="80"></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in produtos">
											<td>{{ item.nome_produto }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td>{{ item.peso }}</td>
											<td>{{ item.sabor }}</td>
											<td>
												<input type="text" class="form-control text-center input-xs" width="50"
													onKeyPress="return SomenteNumero(event);" 
													ng-model="item.qtd"
													ng-disabled="isProdutoSelected(item)"
													ng-if="item.flg_unidade_fracao != 1"/>

												<input type="text" class="form-control input-xs" width="50"
													onKeyPress="return SomenteNumero(event);" 
													ng-model="item.qtd"
													ng-disabled="isProdutoSelected(item)"
													ng-if="item.flg_unidade_fracao == 1"
													thousands-formatter precision="3"/>
											</td>
											<td class="text-center">
												<button type="button"
													class="btn btn-xs btn-{{ (!isProdutoSelected(item)) ? 'success' : 'primary' }}"
													ng-click="addProduto(item)"
													ng-disabled="isProdutoSelected(item)">
													<span ng-if="!isProdutoSelected(item)">
														<i class="fa fa-check-square-o"></i> Selecionar
													</span>
													<span ng-if="isProdutoSelected(item)">
														<i class="fa fa-check-circle-o"></i> Selecionado
													</span>
												</button>
											</td>
										</tr>
									</tbody>
								</table>
				   			</div>
				   		</div>

						<div class="row" ng-show="!enableNewFormProduto">
							<div class="col-sm-12">
								<ul class="pagination pagination-xs m-top-none pull-right">
									<li ng-repeat="item in paginacao.produtos" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadProdutos(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
							</div>
						</div>
				    </div>
				    <div class="modal-footer clearfix" ng-show="enableNewFormProduto">
				    	<button type="button" id="btn-salvar-produto" class="btn btn-primary btn-sm"
				    		ng-click="salvarProduto()">
				    		<i class="fa fa-save"></i> Salvar e selecionar
			    		</button>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal tabela de validades-->
		<div class="modal fade" id="list_validades" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Quantidades Recebidas por Validade</span></h4>
						<p>{{ produto.nome_produto }}</p>
      				</div>
				    <div class="modal-body">
				    	<div class="alert alert-itens" style="display:none"></div>
				    	<div class="row">
				    		<div class="col-sm-12">
				    			<form role="form">
									<div class="row">
										<div class="col-sm-4">
											<div class="form-group" id="nme_fornecedor">
												<label class="control-label">Validade (Mês/Ano)</label>
												<input type="text" class="form-control" ui-mask="99/9999" ng-model="itemValidade.validade" ng-blur="validarDataValidade(itemValidade.validade)">
											</div>
										</div>

										<div class="col-sm-2">
											<div class="form-group" id="id_pedido_fornecedor">
												<label class="control-label">Quantidade</label>
												<input type="text" class="form-control" ng-model="itemValidade.qtd" ng-if="produto.flg_unidade_fracao != 1">
												<input type="text" class="form-control" ng-model="itemValidade.qtd" ng-if="produto.flg_unidade_fracao == 1" thousands-formatter precision="3">
											</div>
										</div>

										<div class="col-sm-3">
											<div class="form-group">
												<label class="control-label"><br></label>
												<button type="button" class="btn btn-sm btn-primary form-control" ng-click="addValidadeItem()"><i class="fa fa-plus-circle"></i> Adicionar</button>
											</div>
										</div>
									</div>
								</form>
				    		</div>
				    	</div>

				   		<div class="row">
				   			<div class="col-sm-12">
				   				<table class="table table-bordered table-condensed table-striped table-hover table-responsive">
									<thead>
										<tr>
											<th>Validade</th>
											<th>Quantidade</th>
											<th style="width:80px;">Ações</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in produto.validades">
											<td>{{ item.validade | dateFormat:'date-m/y'}}</td>
											<td>{{ item.qtd }}</td>
											<td align="center">
												<button type="button" class="btn btn-xs btn-danger" ng-click="deleteValidadeItem($index)"><i class="fa fa-trash-o"></i></button>
											</td>
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

		<!-- /Modal tabela de precos-->
		<div class="modal fade" id="list_precos" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Atualizar Tabela de Preços dos Produtos</span></h4>
      				</div>
				    <div class="modal-body">
					    <div class="row">
					  	    <div class="col-sm-12">
	      						<div class="alert alert-entrada" style="display:none"></div>
	      					</div>
	      				</div>
				   		<div class="row">
				   			<div class="col-sm-12">
				   				<table class="table table-bordered table-condensed table-striped table-hover table-responsive">
									<thead ng-show="(precoProduto.length != 0)">
										<tr>
											<th style="line-height: 40px;" rowspan="2">Nome</th>
											<th style="line-height: 40px;" rowspan="2">Fabricante</th>
											<th style="line-height: 40px;" rowspan="2">Tamanho</th>
											<th style="text-align: center;" colspan="3">Margem de Lucro (% sobre Custo)</th>
										</tr>
										<tr>
											<th style="text-align: center;width:100px">Atacado</th>
											<th style="text-align: center;width:100px">Intermediário</th>
											<th style="text-align: center;width:100px">Varejo</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in precoProduto">
											<td>{{ item.nome_produto }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td>{{ item.peso }}</td>
											<td>
												<div id="{{ $index }}-margem_atacado">
													<input  ng-model="item.margem_atacado" thousands-formatter  type="text" class="form-control input-xs" />
												</div>
											</td>
											<td>
												<div id="{{ $index }}-margem_intermediario">
													<input  ng-model="item.margem_intermediario" thousands-formatter type="text" class="form-control input-xs" />
												</div>
											</td>
											<td>
												<div id="{{ $index }}-margem_varejo">
													<input  ng-model="item.margem_varejo" thousands-formatter  type="text" class="form-control input-xs" />
												</div>
											</td>
										</tr>
									</tbody>
								</table>
				   			</div>
				   		</div>

				   		<div class="row">
					    	<div class="col-sm-12">
					    		<div class="pull-right">
					    			<button type="button" ng-click="salvarPrecoProduto()" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
					    		</div>
					    	</div>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

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
						            	ng-enter="loadFornecedores(0,10)" 
						            	ng-model="busca.fornecedores" 
						            	ng-disabled="enableNewFormFornecedor">
						            <div class="input-group-btn">
						            	<button type="button" class="btn btn-sm btn-primary" 
						            		ng-click="loadFornecedores(0,10)" 
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
											<th>Razão Social</th>
											<th>Nome/Nome Fantasia</th>
											<th>CPF/CNPJ</th>
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
											<td>
												<span ng-if="(item.tipo_cadastro == 'pf')">{{ item.num_cpf | cpfFormat }}</span>
												<span ng-if="(item.tipo_cadastro == 'pj')">{{ item.num_cnpj | cnpjFormat }}</span>
											</td>
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
										<a href="" h ng-click="loadFornecedores(item.offset,item.limit)">{{ item.index }}</a>
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

		<!-- /Modal Pedidos-->
		<div class="modal fade" id="list_pedidos" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Pedidos</span></h4>
      				</div>
				    <div class="modal-body">
				    	<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label">Busca pelo código do pedido</label>
									<div class="input-group">
							            <input ng-model="busca.pedidos" type="text" class="form-control input-sm">
							            <div class="input-group-btn">
							            	<button ng-click="loadPedidos(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button"><i class="fa fa-search"></i> Buscar</button>
							            </div>
							        </div>
								</div>
							</div>
						</div>

						<br/>

				   		<div class="row">
				   			<div class="col-sm-12">
				   				<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(pedidos.length != 0)">
										<tr>
											<th>#</th>
											<th>Solicitante</th>
											<th>Data do pedido</th>
											<th>Qtd de itens</th>
											<th>valor do pedido</th>
											<th>Ações</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(pedidos.length == 0)">
											<td colspan="3">Não a pedidos cadastrados</td>
										</tr>
										<tr ng-repeat="item in pedidos">
											<td>{{ item.id }}</td>
											<td>{{ item.nome_usuario }}</td>
											<td>{{ item.dta_pedido }}</td>
											<td>{{ item.qtd_pedido }}</td>
											<td>R$ {{ item.total_pedido | numberFormat : 2 : ',' : '.' }}</td>
											<td>
												<button ng-click="addPedido(item)" class="btn btn-success btn-xs" type="button">
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
						             <ul class="pagination pagination-xs m-top-none" ng-show="paginacao.pedidos.length > 1">
										<li ng-repeat="item in paginacao.pedidos" ng-class="{'active': item.current}">
											<a href="" ng-click="loadPedidos(item.offset,item.limit)">{{ item.index }}</a>
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

		<!-- /Modal Depositos-->
		<div class="modal fade" id="list_depositos" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Depósitos</span></h4>
      				</div>
				    <div class="modal-body">
				    	<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label">Busca pelo nome do Depósito</label>
									<div class="input-group">
							            <input ng-model="busca.depositos" type="text" class="form-control input-sm">
							            <div class="input-group-btn">
							            	<button ng-click="loadDepositos(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button"><i class="fa fa-search"></i> Buscar</button>
							            </div>
							        </div>
								</div>
							</div>
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
										<tr ng-show="(depositos.length == 0)">
											<td colspan="2">Não a Depósitos cadastrados</td>
										</tr>
										<tr ng-repeat="item in depositos">
											<td>{{ item.nme_deposito}}</td>
											<td width="50">
												<button ng-click="addDeposito(item)" class="btn btn-success btn-xs" type="button">
													<i class="fa fa-plus-circle"></i> Selecionar
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
						             <ul class="pagination pagination-xs m-top-none" ng-show="paginacao.depositos.length > 1">
										<li ng-repeat="item in paginacao.depositos" ng-class="{'active': item.current}">
											<a href="" ng-click="loadDepositos(item.offset,item.limit)">{{ item.index }}</a>
										</li>
									</ul>
						        </div>
							</div>
						</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal detalhes entrada-->
		<div class="modal fade" id="list_detalhes" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Itens</span></h4>
      				</div>
				    <div class="modal-body">
				    	<div class="loading-ajax" id="loading-ajax-lista-detalhes">
						 	<i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
						</div>
				   		<table class="table table-bordered table-condensed table-striped table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Produto</th>
									<th>Fabricante</th>
									<th>Tamanho</th>
									<th>Cor/Sabor</th>
									<th>Quantidade</th>
									<th>Custo</th>
									<th>Validade</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="item in detalhes">
									<td>{{ item.id_produto }}</td>
									<td>{{ item.nome_produto }}</td>
									<td>{{ item.nome_fabricante }}</td>
									<td>{{ item.nome_tamanho }}</td>
									<td>{{ item.nome_cor }}</td>
									<td>{{ item.qtd_item }}</td>
									<td>R$ {{ item.vlr_custo | numberFormat : 2 : ',' : '.' }}</td>
									<td>{{ item.dta_validade | dateFormat:'date' }}</td>
								</tr>
							</tbody>
						</table>
				    </div>
				    <div class="panel-footer clearfix">
						<div class="pull-right">
							<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.detalhes.length > 1">
								<li ng-repeat="item in paginacao.detalhes" ng-class="{'active': item.current}">
									<a href="" ng-click="loadItensEntrada(item.id_estoque_entrada,item.offset,item.limit,'#loading-ajax-lista-detalhes')">{{ item.index }}</a>
								</li>
							</ul>
						</div>
					</div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- Modal Duplicatas -->
		<div class="modal fade" id="list_duplicatas" style="display: none;">
			<div class="modal-dialog">
    			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Nova Duplicata</h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-sm-4 form-group">
								<label class="control-label">Número</label>
								<input class="form-control input-md" type="text" ng-model="new_duplicata.num_duplicata">
							</div>
							<div class="col-sm-4 form-group">
								<div class="form-group" id="dta_entrada">
									<label class="control-label">Vencimento</label>
									<div class="input-group">
										<input readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="vencimentoduplicatas" class="datepicker form-control text-center" ng-model="new_duplicata.dta_vencimento">
										<span class="input-group-addon" id="cld_vencimento_duplicatas"><i class="fa fa-calendar"></i></span>
									</div>
								</div>
							</div>
							<div class="col-sm-4 form-group">
								<label class="control-label">Valor</label>
								<input class="form-control input-md" type="text" ng-model="new_duplicata.vlr_duplicata" thousands-formatter precision="2">
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" 
							data-dismiss="modal">
							Cancelar
						</button>
						<button type="button" class="btn btn-primary" 
							ng-click="incluirDuplicata(item)">
							Incluir
						</button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->

		<input ng-model="cod_barra_busca" ng-blur="blurBuscaCodBarra(cod_barra_busca)"  class="form-control input-sm" style="position: absolute;top: -100px" id="focus" ng-enter="buscaCodBarra()"/>

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

	<!-- Chosen -->
	<script src='js/chosen.jquery.min.js'></script>

	<!-- Jquery Form-->
	<script src='js/jquery.form.js'></script>

	<!-- Bootstrap -->
    <script src="bootstrap/js/bootstrap.min.js"></script>

	<!-- Modernizr -->
	<script src='js/modernizr.min.js'></script>

	<!-- Datepicker -->
	<script src='js/bootstrap-datepicker.min.js'></script>

	<!-- Timepicker -->
	<script src='js/bootstrap-timepicker.min.js'></script>

	<!-- Moment -->
	<script src="js/moment/moment.min.js"></script>

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

	<!-- Bower Components -->	
	<script src="bower_components/noty/lib/noty.min.js" type="text/javascript"></script>
    <script src="bower_components/mojs/build/mo.min.js" type="text/javascript"></script>

	<!-- Extras -->
	<script src="js/extras.js"></script>

	<!-- Moment -->
	<script src="js/moment/moment.min.js"></script>

	<script src="js/jquery.noty.packaged.js"></script>

	<!-- UnderscoreJS -->
	<script type="text/javascript" src="bower_components/underscore/underscore.js"></script>

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
	<script type="text/javascript">
		var addParamModule = ['angular.chosen'] ;
	</script>
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/estoque-controller.js?<?php/* echo filemtime('js/angular-controller/estoque-controller.js')*/?>"></script>

	<script type="text/javascript">
		$(document).ready(function() {
			$('.datepicker').datepicker();
			$("#cld_pagameto").on("click", function(){ $("#pagamentoData").trigger("focus"); });
			$("#cld_datarecebimento").on("click", function(){ $("#datarecebimento").trigger("focus"); });
			$("#cld_vencimento_duplicatas").on("click", function(){ $("#vencimentoduplicatas").trigger("focus"); });

			$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
			$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});
		});
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

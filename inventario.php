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

	<!-- Timepicker -->
	<link href="css/bootstrap-timepicker.css" rel="stylesheet"/>

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
	</style>
  </head>

  <body class="overflow-hidden" ng-controller="InventarioController" ng-cloak>
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
					 <li><i class="fa fa-list-ol"></i> <a href="estoque.php">Controle de Estoque</a></li>
					 <li class="active"><i class="fa fa-tags"></i> Inventário de Estoque</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-tags"></i> Inventário de Estoque</h3>
					<br/>
					<a class="btn btn-info" id="btn-novo" ng-disabled="editing" ng-click="showBoxNovo()"><i class="fa fa-plus-circle"></i> Nova Contagem</a>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md">
				<div class="alert alert-sistema alert-preco" style="display:none"></div>

				<div class="panel panel-default" id="box-novo" style="display:none">
					<div class="panel-heading"><i class="fa fa-plus-circle"></i> Entre com as quantidades dos produtos</div>

					<div class="panel-body">
						<form id="form-csv" class="form">
							<div class="row">
								<div class="col-sm-2">
									<div class="form-group" id="dta_contagem">
										<label class="control-label">Data da Contagem</label>
										<div class="input-group" >
											<input readonly="readonly" ng-model="pagamento.data" type="text" id="inventarioData" class="datepicker form-control input-sm" style="background-color: #FFF;cursor:pointer">
											<span  class="input-group-addon" id="cld_pagameto"><i class="fa fa-calendar"></i></span>
										</div>
									</div>
								</div>

								<div class="col-sm-4">
									<div class="form-group" id="nome_deposito">
										<label class="control-label" >Depósito</label>
										<div class="input-group">
											<input ng-click="showDepositos()" ng-model="inventario.nome_deposito" type="text" class="form-control input-sm" readonly="readonly" style="background-color: #FFF;cursor:pointer">
											<span ng-click="showDepositos()" class="input-group-addon"><i class="fa fa-tasks"></i></span>
										</div>
									</div>
								</div>

								<div class="col-sm-6">
									<label class="control-label">Responsável</label>
									<input ng-model="inventario.nome_usuario" type="text" class="form-control input-sm" readonly="readonly" style="background-color: #FFF;">
								</div>
							</div>

							<div class="row">
								<div class="col-sm-4">
									<div class="form-group" id="csv_estoque">
										<label class="control-label" 
											ng-show="editing == false || (inventario.csv_estoque == '' || inventario.csv_estoque == null)">
											<i class="fa fa-file-text-o"></i> Arquivo CSV
										</label>
										<a href="assets/arquivos_nfe/{{ inventario.csv_estoque }}"  target="_blank">
											<label style="cursor: pointer;" class="control-label" 
												ng-hide="editing == false || (inventario.csv_estoque == '' || inventario.csv_estoque == null)">
												<i class="fa fa-file-text-o"></i> Arquivo CSV
											</label>
										</a>
										<div class="upload-file">
											<input id="stock-file" name="stock-file" class="foto-nota" type="file" data-file="inventario.foto" accept="text/xml"/>
											<label data-title="Selecione..." for="stock-file" style="background-color: #eee;">
												<span data-title="{{ inventario.csv_estoque }}"></span>
											</label>
										</div>
									</div>
								</div>

								<div class="col-sm-4">
									<div class="form-group">
										<label class="control-label"><br/></label>
										<div class="controls">
											<button id="loadDataFromCSV" type="button" class="btn btn-sm btn-info" 
												ng-click="loadDataFromCSV()" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Aguarde, carregando..."
												data-toggle="tooltip" title="Ao usar essa opção, o sistema só listará os produtos que encontrar ativos no seu empreendimento!">
												<i class="fa fa-file-text-o"></i> Carregar itens a partir do CSV
											</button>
										</div>
									</div>
								</div>
							</div>

							<br>
							<h5>Itens Inventariados</h5>
							<br>
							<div class="alert alert-itens" style="display:none"></div>

							<div class="row">
							
								<div class="col-sm-12">
									<table class="table table-bordered table-condensed table-striped table-hover">
										<thead >
											<tr>
												<th>Produto</th>
												<th>Fabricante</th>
												<th>Tamanho</th>
												<th style="width: 100px; text-align: center;" colspan="2">Quantidade</th>
												<td style="width: 200px; text-align: center;">
													<button class="btn btn-xs btn-primary" ng-click="showProdutos()"><i class="fa fa-plus-circle"></i> Adicionar Item</button>

													<button class="btn btn-xs" ng-click="addFocus()" 
														ng-class="{ 'btn-info' : (busca_cod_barra == false), 'btn-success' : (busca_cod_barra == true) }">
														<i class="fa fa-barcode"></i>
													</button>

												</td>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td colspan="6" ng-hide="inventario.itens.length > 0">
													Não há produtos selecionados
												</td>
											</tr>
											<tr ng-repeat="item in inventario.itens">
												<td style="line-height: 1.5; vertical-align: middle;">{{ item.nome }}</td>
												<td>{{ item.nome_fabricante }}</td>
												<td>{{ item.peso }}</td>
												<td width="32">
													<button type="button" class="btn btn-xs btn-primary" ng-click="showValidades(item)">
														<i class="fa fa-calendar"></i>
													</button>
												</td>
												<td style="text-align: center;">{{ item.qtd_ivn }}</td>
												<td>
													<button ng-click="deleteItem($index)" type="button" class="btn btn-xs btn-danger">
														<i class="fa fa-trash-o"></i> Remover Item
													</button>
												</td>
											</tr>
											<tr style="font-weight: bold;" ng-show="inventario.itens.length > 0">
												<td style="text-align: right;" colspan="4">TOTAIS</td>
												<td style="text-align: center;">{{ inventario.qtd_total }}</td>
												<td></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-12">
									<div class="pull-right">
										<button id="btCancelar" ng-click="showBoxNovo(); reset();" type="submit" class="btn btn-danger btn-sm">
											<i class="fa fa-times-circle"></i> Cancelar
										</button>
										<button id="btSalvar" ng-click="salvar()" type="submit" class="btn btn-success btn-sm" data-loading-text="Aguarde...">
											<i class="fa fa-save"></i> Salvar
										</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div><!-- /panel -->
				<div class="panel-body">
				<div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-filter"></i> Opções de Filtro</div>
					<div class="panel-body">
						<div class="row">

							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label">Data da Contagem</label>
									<div class="input-group">
										<input id="data_da_contagem" readonly="readonly" style="background:#FFF;cursor:pointer" type="text" class="datepicker form-control input-sm">
										<span  id="botao_do_lado" class="input-group-addon"><i class="fa fa-calendar"></i></span>
									</div>
								</div>
							</div>

							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label">Depósito</label>
									<input ng-model="busca.text" ng-enter="loadUltimosInventarios(0,10)" ng-keyup="loadUltimosInventarios(0,10)" type="text" class="form-control input-sm ng-pristine ng-valid ng-touched">
								</div>
							</div>

							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label">Responsável</label>
									<input ng-model="busca.responsavel" ng-enter="loadUltimosInventarios(0,10)" ng-keyup="loadUltimosInventarios(0,10)" type="text" class="form-control input-sm ng-pristine ng-valid ng-touched">
								</div>
							</div>

							

							<div class="col-sm-1">
								<div class="form-group">
									<label class="control-label"><br></label>
									<button type="button" class="btn btn-sm btn-primary" ng-click="loadUltimosInventarios(0,10)"><i class="fa fa-filter"></i> Filtrar</button>
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

					<div class="panel-heading"><i class="fa fa-tasks"></i> Últimas Contagens</div>
						<table class="table table-bordered table-condensed table-striped table-hover">
							<thead ng-show="utimosInventarios.length > 0">
								<tr>
									<th width="50" class="text-center">#</th>
									<th width="80">Data da Contagem</th>
									<th>Depósito</th>
									<th>Responsável pela Contagem</th>
									<th width="80" class="text-center">Opções</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-hide="utimosInventarios.length > 0">
									<td>
										Não há inventarios cadastrados
									</td>
								</tr>
								<tr ng-repeat="item in utimosInventarios">
									<td>{{ item.id }}</td>
									<td>{{ item.dta_contagem | dateFormat:'date' }}</td>
									<td>{{ item.nme_deposito }}</td>
									<td>{{ item.nme_usuario }}</td>
									<td align="center">
										<button type="button" ng-click="showDetalhes(item,0,20)" tooltip="detalhes" class="btn btn-xs btn-info" data-toggle="tooltip">
											<i class="fa fa-tasks"></i>
										</button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="panel-footer clearfix">
						<div class="pull-right">
							<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.inventarios.length > 1">
								<li ng-repeat="item in paginacao.inventarios" ng-class="{'active': item.current}">
									<a href="" h ng-click="loadUltimosInventarios(item.offset,item.limit)">{{ item.index }}</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /main-container -->

		<!-- /Modal detalhes inventario-->
		<div class="modal fade" id="list_detalhes" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Itens</span></h4>
      				</div>
				    <div class="modal-body">
				   		<table class="table table-bordered table-condensed table-striped table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Produto</th>
									<th>Fabricante</th>
									<th >Tamanho</th>
									<th >Sabor/cor</th>
									<th>Quantidade</th>
									<th>Dta validade</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="item in detalhes">
									<td>{{ item.id }}</td>
									<td>{{ item.nome }}</td>
									<td>{{ item.nome_fabricante }}</td>
									<td>{{ item.peso }}</td>
									<td>{{ item.sabor }}</td>
									<td>{{ item.qtd_contagem }}</td>
									<td ng-if="item.dta_validade == '2099-12-31'"></td>
									<td ng-if="item.dta_validade != '2099-12-31'">{{ item.dta_validade | dateFormat:'date'}}</td>
								</tr>
							</tbody>
						</table>
				    </div>
				    <div class="panel-footer clearfix">
						<div class="pull-right">
							<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.detalhes.length > 1">
								<li ng-repeat="item in paginacao.detalhes" ng-class="{'active': item.current}">
									<a href="" h ng-click="loadItensInventario(id_invetario_current,item.offset,item.limit)">{{ item.index }}</a>
								</li>
							</ul>
						</div>
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
											<div class="form-group" id="item-validade-add">
												<label class="control-label">Validade (Mês/Ano)</label>
												<input type="text" class="form-control" ui-mask="99/9999" ng-model="itemValidade.validade" ng-blur="validarDataValidade(itemValidade.validade)">
											</div>
										</div>

										<div class="col-sm-3">
											<div class="form-group" id="item-qtd-add">
												<label class="control-label">Quantidade</label>
												<input type="text" class="form-control"  ng-model="itemValidade.qtd" ng-enter="addValidadeItem()">
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
											<th>Qtd.</th>
											<th style="width:80px;">Ações</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in produto.validades">
											<td ng-if="item.validade == '122099'"></td>
											<td ng-if="item.validade != '122099'">{{ formatDate(item.validade) }}</td>
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


		<!-- /Modal Produtos-->
		<div class="modal fade" id="list_produtos" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Produtos</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.produtos" ng-enter="loadProdutos(0,10)" type="text" class="form-control input-sm">

						            <div class="input-group-btn">
						            	<button ng-click="loadProdutos(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button"><i class="fa fa-search"></i> Buscar</button>
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
											<th>Tamanho</th>
											<th>Fabricante</th>
											<th width="80"></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(produtos.length == 0)">
											<td colspan="3">Não a Produtos cadastrados</td>
										</tr>
										<tr ng-repeat="item in produtos">
											<td>{{ item.id }}</td>
											<td>{{ item.nome }}</td>
											<td>{{ item.peso }}</td>
											<td>{{ item.nome_fabricante }}</td>
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

		<!-- /Modal depositos-->
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
								<div class="input-group">
						            <input ng-model="busca.depositos" ng-enter="loadDepositos(0,10)" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadDepositos(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button"><i class="fa fa-search"></i> Buscar</button>
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
											<th>Nome</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(depositos.length == 0)">
											<td colspan="2">Não a Depósitos cadastrados</td>
										</tr>
										<tr ng-repeat="item in depositos">
											<td>{{ item.nme_deposito}}</td>
											<td width="80">
												<button ng-click="addDeposito(item)" class="btn btn-success btn-xs" type="button">
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
	      						<div class="alert alert-preco" style="display:none"></div>
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
											<th style="text-align: center;" colspan="3">Preços</th>
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
												<div class="form-group" id="{{ $index }}-margem_atacado">
													<input  ng-model="item.margem_atacado" thousands-formatter  type="text" class="form-control input-xs" />
												</div>
											</td>
											<td>
												<div class="form-group" id="{{ $index }}-margem_intermediario">
													<input  ng-model="item.margem_intermediario" thousands-formatter type="text" class="form-control input-xs" />
												</div>
											</td>
											<td>
												<div class="form-group" id="{{ $index }}-margem_varejo">
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
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/inventario-controller.js"></script>
	<script type="text/javascript"></script>

	<script type="text/javascript">
		$(document).ready(function() {
			$('.datepicker').datepicker();
			$("#cld_pagameto").on("click", function(){ $("#inventarioData").trigger("focus"); });
			$("#botao_do_lado").on("click", function(){ $("#data_da_contagem").trigger("focus"); });

			$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
			$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});
		});
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

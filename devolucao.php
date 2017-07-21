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

  <body class="overflow-hidden" ng-controller="DevolucaoController" ng-cloak>
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
					 <li class="active"><i class="fa fa-clipboard"></i> Devoluções</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-clipboard"></i> Devoluções</h3>
					<br/>
					<a class="btn btn-info" id="btn-nova-devolucao" ng-click="showBoxNovaDevolucao()"><i class="fa fa-plus-circle"></i> Nova Devolução</a>
				</div><!-- /page-title -->
			</div><!-- /main-header -->
			<div class="padding-md">

				<div class="alert alert-sistema" style="display:none"></div>

				<div class="panel panel-default" id="box-novo-pedido" style="display:none">
					<div class="panel-heading"><i class="fa fa-plus-circle"></i> Novo Pedido de Devolução</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-3">
								<div class="input-group">
									<input type="text" class="form-control input-sm" ng-enter="loadVenda(id_venda)" onKeyPress="return SomenteNumero(event);" placeholder="Informe o ID da Venda" ng-model="id_venda">
									<div class="input-group-btn">
										<button class="btn btn-sm btn-primary" ng-click="loadVenda(id_venda)" type="button">
											<i class="fa fa-search"></i> Buscar
										</button>
									</div>
								</div>
							</div>
						</div>
						<br/>
						<div class="row">
							<div class="col-sm-12" >
								<div style="display:none" class="alert alert-erro"></div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12" >
								<div style="display:none" class="alert alert-validade"></div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12" ng-if="venda.id != undefined && view.cadastar_cliente == false">
								<strong>Cliente:</strong> {{ venda.nome_cliente }} | <strong>Data da Venda:</strong> {{ venda.dta_venda | dateFormat }} | <strong>Vendedor:</strong> {{ venda.nome_usuario }}
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<div class="panel panel-default" id="box-licar-cliente" ng-if="view.cadastar_cliente" ><!-- style="display:none" -->
									<div class="panel-heading"><i class="fa fa-random"></i> Vincular cliente a venda</div>
									<div class="panel-body">
										<div class="row">
											<div class="col-sm-9">
												<div class="input-group" id="busca_cliente">
										            <input ng-disabled="true"  ng-model="cliente_selecionado.nome" ng-enter="loadClientes(0,10)"  type="text" class="form-control input-sm">
										            <div class="input-group-btn">
										            	<button ng-click="selCliente(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
										            		<i class="fa fa-group"></i> Selecionar
										            	</button>
										            </div>
										        </div>
											</div>
											<div class="col-sm-1">
												<button type="button" class="btn btn-sm btn-default" ng-click="busca.clientes='';loadClientes(0,10)">Limpar</button>
											</div>
											<div class="col-sm-2">
												<button type="button" class="btn btn-sm btn-success"  ng-click="view.cadastro_novo_cliente = true">Novo cliente</button>
											</div>
										</div>
										<br>
										<div class="panel panel-default" id="box-novo-cliente"  ng-if="view.cadastro_novo_cliente"><!-- style="display:none" -->
											<div class="panel-heading"><i class="fa fa-save"></i> Cadastro rápido de cliente</div>

											<div class="panel-body">
												<div class="row">
													<div class="col-sm-12">
														<div class="form-group">
															<label for="" class="control-label">Tipo de Cadastro</label>
															<div class="form-group">
																<label class="label-radio inline">
																	<input ng-model="cliente.tipo_cadastro" value="pf" type="radio" class="inline-radio">
																	<span class="custom-radio"></span>
																	<span>Pessoa Física</span>
																</label>

																<label class="label-radio inline">
																	<input ng-model="cliente.tipo_cadastro" value="pj" type="radio" class="inline-radio">
																	<span class="custom-radio"></span>
																	<span>Pessoa Jurídica</span>
																</label>
															</div>
															<div class="row">
																<div class="col-sm-8">
																	<div id="nome" class="form-group">
																		<label for="nome" class="control-label">Nome <span style="color:red;font-weight: bold;">*</span></label>
																		<input type="text" class="form-control" id="nome" ng-model="cliente.nome">
																	</div>
																</div>
																<div class="col-sm-4">
																	<div id="id_perfil" class="form-group">
																		<label class="control-label">Perfil  <span style="color:red;font-weight: bold;">*</span></label>
																		<select class="form-control" ng-model="cliente.id_perfil" ng-options="a.id as a.nome for a in perfis"></select>
																	</div>
																</div>
														    </div>
														    <div class="row" ng-if="cliente.tipo_cadastro == 'pj'">
																<div class="col-lg-4">
																	<div id="razao_social" class="form-group">
																		<label class="control-label">Razão Social  <span style="color:red;font-weight: bold;">*</span></label>
																		<input class="form-control" ng-model="cliente.razao_social">
																	</div>
																</div>

																<div class="col-sm-4">
																	<div id="nome_fantasia" class="form-group">
																		<label class="control-label">Nome Fantasia  <span style="color:red;font-weight: bold;">*</span></label>
																		<input class="form-control" ng-model="cliente.nome_fantasia">
																	</div>
																</div>

																<div class="col-sm-2">
																	<div id="cnpj" class="form-group">
																		<label class="control-label">CNPJ  <span style="color:red;font-weight: bold;">*</span></label>
																		<input class="form-control" ui-mask="99.999.999/9999-99" ng-model="cliente.cnpj">
																	</div>
																</div>

																<div class="col-sm-2">
																	<div id="inscricao_estadual" class="form-group">
																		<label class="control-label">I.E.  <span style="color:red;font-weight: bold;">*</span></label>
																		<input class="form-control" ng-model="cliente.inscricao_estadual">
																	</div>
																</div>
															</div>
														    <div class="row" ng-if="cliente.tipo_cadastro == 'pf'">
																<div class="col-sm-2">
																	<div id="rg" class="form-group">
																		<label class="control-label">RG <span style="color:red;font-weight: bold;">*</span></label>
																		<input class="form-control" ui-mask="99.999.999-9" ng-model="cliente.rg"/>
																	</div>
																</div>

																<div class="col-sm-2">
																	<div id="cpf" class="form-group">
																		<label class="control-label">CPF <span style="color:red;font-weight: bold;">*</span></label>
																		<input class="form-control" ui-mask="999.999.999-99" ng-model="cliente.cpf"/>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>

											</div>
											<div class="panel-footer clearfix">
													<div class="pull-right">
														<button type="button" class="btn btn-danger btn-sm" ng-click="view.cadastro_novo_cliente = false">
															<i class="fa fa-times-circle"></i> Cancelar
														</button>
														<button data-loading-text=" Aguarde..." id="btn-salvar-cliente" type="submit" class="btn btn-success btn-sm" ng-click="salvarCliente()">
															<i class="fa fa-save"></i> Salvar Cliente
														</button>
													</div>
												</div>
										</div>
									</div>
									<div class="panel-footer clearfix" ng-if="view.cadastro_novo_cliente == false">
										<div style="text-align:center">
											<button data-loading-text=" Aguarde..." ng-click="atualizarRegistros(venda.id,cliente_selecionado.id)" id="btn-salvar-vincular-cliente" type="submit" class="btn btn-success btn-md">
												<i class="fa fa-save"></i> Vincular usuario
											</button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-sm-12">
								<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-if="itens_venda.length > 0">
										<tr>
											<td class="text-center" style="line-height: 46px;" rowspan="2"  colspan="2">Produto</td>
											<td class="text-center" style="line-height: 46px;" rowspan="2" >Fabricante</td>
											<td class="text-center" style="line-height: 46px;" rowspan="2" >Tamanho</td>
											<td class="text-center" style="line-height: 46px;" rowspan="2" width="100">Vlr. Unit.</td>
											<td class="text-center" rowspan="1" colspan="3" width="80">Qtd</td>
											<td class="text-center" style="line-height: 46px;" rowspan="2" width="100">Subtotal</td>
											<td class="text-center" style="line-height: 46px;" rowspan="2" width="80">Qtd. Dev.</td>
											<td class="text-center" style="line-height: 46px;" rowspan="2" width="100">Dta. Val.</td>
											<td class="text-center" style="line-height: 46px;" rowspan="2" width="120">Ações</td>
										</tr>
										<tr>
											<td class="text-center" rowspan="1" width="47">Comp.</td>
											<td class="text-center" rowspan="1" width="47">Devol.</td>
											<td class="text-center" rowspan="1" width="47">Disp.</td>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td ng-if="itens_venda == null && view.busca_return_empty == false" class="text-center" colspan="6">
												Nenhum registro encontrado
											</td>
											<th class="text-center" colspan="6" ng-if="itens_venda.length == 0 && view.busca_return_empty == false">
												<i class="fa fa-refresh fa-spin"></i> Aguarde, buscando venda...
											</th>
											<th class="text-center" colspan="6" ng-if="view.busca_return_empty">
												Não foi encontrada nenhuma venda com o ID {{ view.busca_id_empty  }}
											</th>
										</tr>
										<tr ng-repeat="item in itens_venda">
											<td class="text-center" width="60">{{ item.id_produto }}</td>
											<td>{{ item.nome_produto }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td>{{ item.peso }}</td>
											<td class="text-right">R$ {{ item.valor_real_item | numberFormat:2:',':'.' }}</td>
											<td class="text-center">{{ item.qtd }}</td>
											<td class="text-center">{{ item.qtd_devolvida_real }}</td>
											<td class="text-center">{{ item.qtd - item.qtd_devolvida_real}}</td>
											<td class="text-right">{{ (item.qtd * item.valor_real_item) | numberFormat:2:',':'.' }}</td>
											<td class="text-center" >
												<input   ng-model="item.qtd_devolvida" ng-keyup="comparaQtd(item,$index,$event)" id="qtd-devolvida-{{ $index }}" type="text" class="form-control input-xs text-center" ng-disabled="view.cadastar_cliente || ((item.qtd - item.qtd_devolvida_real) < 1)" onKeyPress="return SomenteNumero(event);">
											</td>
											<td class="text-center">
												<input ng-model="item.dta_devolvida" type="text" class="form-control input-xs text-center" id="dta_validade-devolvida-{{ $index }}" ng-disabled="view.cadastar_cliente || ((item.qtd - item.qtd_devolvida_real) < 1)" ui-mask="99/9999" ng-model="devolucao.dta_vencimento">
											</td>
											<td class="text-center">
												<button type="button" ng-click="removeItemDevolucao($index)" class="btn btn-danger btn-xs">
													<i class="fa fa-trash-o"></i> Remover Item
												</button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="col-sm-">
							<div class="form-group">
								<label>Motivo da Devolução:</label>
								<textarea class="form-control" rows="3" ng-model="observacao"></textarea>
							</div>
						</div>
					</div>
					<div class="panel-footer clearfix" ng-if="view.cadastar_cliente == false">
						<div style="text-align:center">
							<button ng-click="lancarDevolucao()" type="submit" data-loading-text=" Aguarde..." id="btn-lancar-devolucao"  class="btn btn-success btn-md" 
							 ng-disabled="itens_venda == null || itens_venda.length == 0">
								<i class="fa fa-save"></i> Lançar Devolução
							</button>
						</div>
					</div>

				</div><!-- /panel -->

				<div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-filter"></i> Opções de Filtro</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label">Data</label>
									<div class="input-group">
										<input readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="data" class="datepicker form-control text-center">
										<span class="input-group-addon" id="cld_data"><i class="fa fa-calendar"></i></span>
									</div>
								</div>
							</div>

							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label">ID Venda</label>
									<input ng-model="busca.id_venda" ng-enter="loadDevolucoes(0,10)" type="text" class="form-control input-sm ng-pristine ng-valid ng-touched">
								</div>
							</div>

							<div class="col-sm-5">
								<div class="form-group">
									<label class="control-label">Cliente</label>
									<input type="text" class="form-control input-sm ng-pristine ng-valid ng-touched"
									ng-enter="loadDevolucoes(0,10)" 
									ng-model="busca.nome" 
									ng-keyup="loadDevolucoes(0,10)"	
									>
								</div>
							</div>

							<div class="col-sm-1">
								<div class="form-group">
									<label class="control-label"><br></label>
									<button type="button" class="btn btn-sm btn-primary" ng-click="loadDevolucoes(0,10)"><i class="fa fa-filter"></i> Filtrar</button>
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
					<div class="panel-heading"><i class="fa fa-tasks"></i> Lista de Devoluções</div>
					<div class="panel-body">
						<div style="display:none" class="alert alert-devolucao-sucesso"></div>
						<table class="table table-bordered table-condensed table-striped table-hover">
						<div class="loading-ajax" id="loading-ajax-lista-detalhes"><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></div>
							<tr ng-show="(devolucoes.length == 0)">
								<td colspan="4">Nenhum registro encontrado</td>
							<tr>
							<thead ng-hide="devolucoes.length == 0">
								<tr>
									<th>#</th>
									<th>ID venda</th>
									<th>Cliente</th>
									<th>Valor</th>
									<th>Data de devolução</th>
									<th>opções</th>
								</tr>
							</thead>
							<tbody ng-hide="devolucoes.length == 0">
								<tr ng-repeat="item in devolucoes" >
									<td>{{ item.id}}</td>
									<td>{{ item.id_venda}}</td>
									<td>{{ item.nome_cliente }}</td>
									<td >R$ {{ item.vlr_disponivel | numberFormat:2:',':'' }}</td>
									<td>{{ item.dta_devolucao | dateFormat }}</td>
									<td>
										<button type="button" class="btn btn-xs btn-info"
											ng-click="viewDetalhes(item)" tooltip="Detalhes" data-toggle="tooltip">
											<i class="fa fa-tasks fa-lg"></i>
										</button>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="row">
			    		<div class="col-sm-12">
			    			<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao_devolucoes.length > 1">
								<li ng-repeat="item in paginacao_devolucoes" ng-class="{'active': item.current}">
									<a href="" h ng-click="loadDevolucoes(item.offset,item.limit,'#loading-ajax-lista-detalhes')">{{ item.index }}</a>
								</li>
							</ul>
			    		</div>
				    </div>
					</div>
				</div>
			</div>
		</div><!-- /main-container -->


		<!-- /Modal clientes-->
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
						            <input ng-enter="loadCliente(0,10)" ng-model="busca.clientes" type="text" class="form-control input-sm">
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
									<tr ng-if="clientes.length <= 0 || clientes == null">
										<th class="text-center" colspan="9" style="text-align:center"><strong>Carregando</strong><img src="assets/imagens/progresso_venda.gif"></th>
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

				<!-- /Modal Itens Devolucao-->
		<div class="modal fade" id="list-itens-devolucao" style="display:none">
  			<div class="modal-dialog modal-lg">
    			<div class="modal-content modal-lg">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Itens Devolucão</h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-sm-12">
								<div ng-if="(viewDetalhes.itens.length > 0)">
									<p style="margin: 0 0 3px;"><strong>Operador:</strong> #{{ viewDetalhes.item.id_operador }} <strong>-</strong> {{ viewDetalhes.item.nme_operador }}</strong></p>
									<p style="margin: 0 0 3px;"><strong>Cliente:</strong> #{{  viewDetalhes.item.id_cliente }} <strong>-</strong> {{ viewDetalhes.item.nome_cliente }}</p>
									<p style="margin: 0 0 3px;"><strong>ID Venda:</strong> {{ viewDetalhes.item.id_venda }}</strong></p>
									<p style="margin: 0 0 3px;"><strong>Data Devolução:</strong> {{ viewDetalhes.item.dta_devolucao | dateFormat }}</strong></p>
									<p style="margin: 0 0 3px;"><strong>Motivo da Devolução:</strong> {{viewDetalhes.item.obs_devolucao}}</strong></p>
									<br/>
								</div>
								<table class="table table-bordered table-condensed table-striped table-hover">
									<tr ng-if="viewDetalhes.itens.length <= 0">
										<th class="text-center" colspan="9" style="text-align:center"><strong>Carregando</strong><img src="assets/imagens/progresso_venda.gif"></th>
									</tr>
									<thead ng-show="(viewDetalhes.itens.length != 0)">
										<tr>
											<th>ID Produto</th>
											<th>Produto</th>
											<th>Fabricante</th>
											<th>Tamanho</th>
											<th>Data validade</th>
											<th>Qtd.</th>
											<th>Valor</th>
											<th>Sub. Total</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in viewDetalhes.itens">
											<td style="text-align:center">#{{ item.id_produto }}</td>
											<td>{{ item.nome_produto }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td>{{ item.peso }}</td>
											<td style="text-align:center">{{ item.dta_validade | dateFormat:"date" }}</td>
											<td style="text-align:center">{{ item.qtd }}</td>
											<td style="text-align:right">R$ {{ item.valor_real_devolucao | numberFormat:2:',':'' }}</td>
											<td style="text-align:right">R${{ item.qtd*item.valor_real_devolucao | numberFormat:2:',':'' }}</td>
										</tr>
										<tr>
											<td colspan="7" style="text-align:right">Total</td>
											<td style="text-align:right">R$ {{ viewDetalhes.item.vlr_disponivel | numberFormat:2:',':'' }}</td>
										</tr>
										<tr>
											
										</tr>
									</tbody>
								</table>
							</div>
						</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
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

	<!-- Logout confirmation -->
	<?php include("logoutConfirm.php"); ?>

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->

	<!-- Jquery -->
	<script src="js/jquery-1.10.2.min.js"></script>

	<!-- Bootstrap -->
    <script src="bootstrap/js/bootstrap.min.js"></script>

    <!-- Datepicker -->
	<script src='js/bootstrap-datepicker.min.js'></script>

	<!-- Timepicker -->
	<script src='js/bootstrap-timepicker.min.js'></script>

    <!-- Moment -->
	<script src="js/moment/moment.min.js"></script>

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
	<script src="js/endless/endless.js"></script>

	<!-- Moment -->
	<script src="js/moment/moment.min.js"></script>

	<script src="js/jquery.noty.packaged.js"></script>

	<!-- Extras -->
	<script src="js/extras.js"></script>

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
	<script src="js/angular-controller/devolucao-controller.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#data").datepicker();
			$("#cld_data").on("click", function(){ $("#data").trigger("focus"); });
			$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
			$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});
		});
	</script>
	<?php include("google_analytics.php"); ?>

  </body>
</html>

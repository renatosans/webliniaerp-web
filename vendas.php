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

	<!-- Datepicker -->
	<link href="css/datepicker.css" rel="stylesheet"/>

	<!-- Timepicker -->
	<link href="css/bootstrap-timepicker.css" rel="stylesheet"/>

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">
	<style type="text/css">
		/*Redimencionando PopOver*/
		.popover-content {
			width: 200px;
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


	</style>
  </head>

  <body class="overflow-hidden" ng-controller="VendasController" ng-cloak>

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
					 <li class="active"><i class="fa fa-signal"></i> Vendas</li>
				</ul>
			</div>
			<!-- breadcrumb -->
			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-signal"></i> Vendas</h3>
					<br>
					<a href="pdv.php"  ng-if="userLogged.id_empreendimento !=75 " class="btn btn-info"><i class="fa fa-desktop"></i> Frente de Caixa (PDV)</a>
					<a href="devolucao.php" ng-if="userLogged.id_empreendimento !=75 " class="btn btn-primary"><i class="fa fa-clipboard"></i> Devolução de Produtos</a>
					<a href="notas-fiscais.php" class="btn btn-primary"><i class="fa fa-barcode"></i> Notas Fiscais</a>
					<a href="lista_pedidos_personalizados.php" class="btn btn-primary" ng-if="userLogged.id_empreendimento == 52 || userLogged.id_empreendimento == 51 || userLogged.id_empreendimento == 6 "><i class="fa fa-tag"></i> Pedidos Personalizados</a>
				</div>
			</div>
			<!-- /main-header -->

			<div class="padding-md">
				<div class="alert alert-sistema" style="display:none"></div>

				<div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-tasks"></i> Vendas</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-2">
								<div class="form-group" id="codigo_barra">
										<label class="control-label">ID</label>
										<input ng-model="busca.ven_id_venda" type="text" class="form-control  ng-pristine ng-valid ng-touched" onkeypress="return SomenteNumero(event);">
									</div>
								</div>
							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label">Data</label>
									<div class="input-group">
										<input readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="dtaInicial" class="datepicker form-control text-center">
										<span class="input-group-addon" id="cld_dtaInicial"><i class="fa fa-calendar"></i></span>
									</div>
								</div>
							</div>
							<div class="col-sm-3">
								<label class="control-label">Vendedor</label>
								<div class="input-group">
									<input ng-model="busca.ven_nome_vendedor" ng-click="selUsuario('vendedor')" type="text" class="form-control"  readonly="readonly" style="cursor: pointer;" />
									<span class="input-group-btn">
										<button ng-click="selUsuario('vendedor')"  type="button" class="btn"><i class="fa fa-user-md"></i></button>
									</span>
								</div>
							</div>
								<div class="col-sm-3">
								<label class="control-label">Cliente</label>
								<div class="input-group">
									<input ng-model="busca.ven_nome_cliente" ng-click="selUsuario('cliente')" type="text" class="form-control"  readonly="readonly" style="cursor: pointer;" />
									<span class="input-group-btn">
										<button ng-click="selUsuario('cliente')"  type="button" class="btn"><i class="fa fa-user"></i></button>
									</span>
								</div>
							</div>
							<div class="col-sm-1">
								<div class="form-group">
									<label class="control-label"><br></label>
									<button type="button" class="btn btn-sm btn-primary" ng-click="loadVendas(0,10)"><i class="fa fa-filter"></i> Filtrar</button>
								</div>
							</div>

							<div class="col-sm-1">
								<div class="form-group">
									<label class="control-label"><br></label>
									<button type="button" class="btn btn-sm btn-default" ng-click="limparBusca()">Limpar</button>
								</div>
							</div>

							<div class="col-sm-1" ng-if="false">
								<div class="form-group">
									<label class="control-label"><br></label>
								<buttom ng-click="buscaAvancada()" class="btn btn-sm btn-primary">Pequisa Avançada <i ng-show="busca_avancada==false" class="fa fa-sort-down"></i><i ng-show="busca_avancada" class="fa fa-sort-up"></i></buttom>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12 table-responsive">
								<div class="alert alert-sistema" id="alert-list-vendas" style="display:none"></div>
								<table class="table table-condensed table-bordered table-striped table-hover">
										<thead>
											<tr>
												<th class="text-center" width="80">#</th>
												<th class="text-center" width="80">Data</th>
												<th class="text-center">Vendedor</th>
												<th class="text-center">Cliente</th>
												<th class="text-center" width="80">Status</th>
												<th class="text-center" width="100">Total</th>
												<th class="text-center" width="190">Ações</th>
											</tr>
										</thead>
									<tbody>
										<tr bs-tooltip ng-repeat="item in vendas">
											<td class="text-center">{{ item.id }}</td>
											<td class="text-center">{{ item.dta_venda }}</td>
											<td>{{ item.nme_vendedor }} <i ng-if="funcioalidadeAuthorized('mudar_vendedor')" ng-if="userLogged.id_perfil == 1" tooltip="Finalizar Produção" data-toggle="tooltip" title="Alterar Vendedor" style="cursor:pointer ;float: right;color: green;" ng-click="selVendedor(item)" class="fa fa-retweet fa-lg"></i></td>
											<td>{{ item.nme_cliente }}</td>
											<td class="text-center">
												{{ item.dsc_status }}
											</td>
											<td class="text-right">R$ {{ item.vlr_total_venda | numberFormat : '2' : ',' : '.'}}</td>
											<td class="text-center">

												<a href="separar_venda.php?id_venda={{ item.id }}" ng-if="item.id_status_venda == 1 || item.id_status_venda == 6" type="button" title="Separar venda no estoque" data-toggle="tooltip" class="btn btn-xs btn-info">
													<i class="fa fa-th"></i>
												</a>

												<button ng-click="changeStatus(3,item.id)" ng-if="item.id_status_venda == 2" type="button" title="Enviar mercadoria" data-toggle="tooltip" class="btn btn-xs btn-info">
													<i class="fa fa-plane"></i>
												</button>

												<button ng-click="changeStatus(4,item.id)" ng-if="item.id_status_venda == 3" type="button" title="Mecadoria entregue" data-toggle="tooltip" class="btn btn-xs btn-info">
													<i class="fa fa-map-marker"></i>
												</button>

												<a class="btn btn-xs btn-primary" 
													href="{{ getUrlPDFVenda(item) }}" target="_blank" 
													title="Detalhes" data-toggle="tooltip">
													<i class="fa fa-tasks"></i>
												</a>

												<!--<button type="button" ng-click="loadDetalhesVenda(item)" title="Detalhes" data-toggle="tooltip" class="btn btn-xs btn-primary">
													<i class="fa fa-tasks"></i>
												</button>-->


												<!--<button type="button" ng-click="loadEditVenda(item)" ng-disabled="item.venda_confirmada == 1" title="Editar" data-toggle="tooltip" class="btn btn-xs btn-warning">
													<i class="fa fa-edit"></i>
												</button>-->

												<button type="button" ng-click="excluirOrcamento(item)" ng-if="item.venda_confirmada == 0"  title="Excluir Orçamento" data-toggle="tooltip" class="btn btn-xs btn-danger">
													<i class="fa fa-trash-o"></i>
												</button>
												<button type="button" ng-click="cancelarVenda(item)" ng-if="item.venda_confirmada == 1 && funcioalidadeAuthorized('excluir_venda')"  title="Cancelar Venda" data-toggle="tooltip" class="btn btn-xs btn-danger">
													<i class="fa fa-trash-o"></i>
												</button>
												<a ng-disabled="item.venda_confirmada == 1" href="pdv.php?id_orcamento={{ item.id }}" title="Finalizar/Editar orçamento" data-toggle="tooltip" class="btn btn-xs btn-success">
													<i class="fa fa-desktop"></i>
												</a>
												<a  href="nota-fiscal.php?id_venda={{ item.id }}" title="Emitir NF-e" data-toggle="tooltip" class="btn btn-xs btn-info">
													<i class="fa fa-file-text-o"></i>
												</a>
											</td>
										</tr>
									</tbody>
								</table>
								<div class="panel-footer clearfix" ng-if="paginacao.vendas.length > 1">
									<div class="pull-right">
										<ul class="pagination pagination-sm m-top-none">
											<li ng-repeat="item in paginacao.vendas" ng-class="{'active': item.current}">
												<a href="" h ng-click="loadVendas(item.offset,item.limit)">{{ item.index }}</a>
											</li>
										</ul>
									</div>
								</div>
							</div>	
						</div>
					</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /main-container -->

		<!-- /Modal detalhes da venda-->
		<!--<div class="modal fade" id="list_clientes" style="display:none">
  			<div class="modal-dialog modal-lg">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Detalhes de Venda</h4>
						<p class="muted" ng-if="venda.nme_cliente != null">
							Cliente : {{ venda.nme_cliente }}
							<button class="btn btn-sm btn-success hidden-print" style="float: right;" ng-show="vendas.length > 0" id="invoicePrint" ng-click="printModal('list_clientes_print')"><i class="fa fa-print"></i> Imprimir</button>
						</p>
						<p class="muted">Venda #{{ venda.id }}</p>
      				</div>
				    <div class="modal-body">
				   		<div class="row" >
				   			<div class="col-sm-12 table-responsive">
				   				<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(clientes.length != 0)">
										<tr>
											<th class="text-center">Produto</th>
											<th class="text-center">Fabricante</th>
											<th class="text-center">Tamanho</th>
											<th class="text-center">Sabor</th>
											<th class="text-center" width="50">Qtd</th>
											<th class="text-center" width="70">Valor</th>
											<th class="text-center" colspan="2" width="60">Desconto</th>
											<th class="text-center" width="90">Subtotal</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in detalhes">
											<td>{{ item.nome_produto }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td> {{ item.peso }} </td>
											<td> {{ item.sabor }} </td>
											<td class="text-center">{{ item.qtd }}</td>
											<td class="text-right">R$ {{ item.valor_real_item  | numberFormat:2:',':'.' }}</td>
											<td class="text-center" width="60">{{ item.valor_desconto * 100 | numberFormat:2:',':'.' }}%</td>
											<td class="text-center" width="20">
												<i class="fa fa-dot-circle-o" ng-if="item.css_cor.length > 0" style="color: {{item.css_cor}}"
													tooltip="(>=) a {{ item.perc_desconto_min * 100 | numberFormat:2:',':'.' }}% e (<=) a {{ item.perc_desconto_max * 100 | numberFormat:2:',':'.' }}%"></i>
											</td>
											<td class="text-right">R$ {{ item.sub_total | numberFormat:2:',':'.'}}</td>
										</tr>
										<tr>
											<td colspan="8" class="text-right">Total</td>
											<td class="text-right">R$ {{ venda.vlr_total_venda | numberFormat : '2' : ',' : '.'}}</td>
										</tr>
									</tbody>
								</table>
				   			</div>
				   		</div>
				    </div>
				    <div class="modal-footer" ng-if="paginacao_clientes != null">
				    	<ul class="pagination pagination-xs m-top-none pull-right">
							<li ng-repeat="item in paginacao_clientes" ng-class="{'active': item.current}">
								<a href="" ng-click="loadCliente(item.offset,item.limit)">{{ item.index }}</a>
							</li>
						</ul>
				    </div>
			  	</div>
			</div>
		</div>-->
		<!-- /.modal -->

		<!-- /Modal Print-->
		<div class="modal fade" id="modal-print-venda" style="display:none"  data-keyboard="false">
  			<div class="modal-dialog error modal-lg">
    			<div class="modal-content">
				    <div class="modal-body" >
				    	<div id="load-pdf-venda" class="text-center" style="height: 450px;line-height: 400px;vertical-align:middle;width: 100%;font-size: 15px;">
				    		<i class='fa fa-refresh fa-spin'></i> Aguarde, carregando ...
				    	</div>
				    	<div id="pdf-venda"></div>
				    </div>
			  	</div>
			  	<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal detalhes da venda-->
		    <div id="list_clientes_print" style="display:none">
		    	<div class="row">
		    		<div class="col-sm-12">
		    			<h4>Comprovante da Venda</h4>
						<p class="muted" ng-if="venda.nme_cliente != null">
							Cliente : {{ venda.nme_cliente }}
							<button class="btn btn-sm btn-success hidden-print" style="float: right;" ng-show="vendas.length > 0" id="invoicePrint" ng-click="printModal('list_clientes_print')"><i class="fa fa-print"></i> Imprimir</button>
						</p>
						<p class="muted">Venda #{{ venda.id }}</p>
		    		</div>
		    	</div>
		   		<div class="row" id=>
		   			<div class="col-sm-12">
		   				<table class="table table-bordered table-condensed table-striped table-hover">
							<thead ng-show="(clientes.length != 0)">
								<tr>
									<th class="text-center">Produto</th>
									<th class="text-center">Fabricante</th>
									<th class="text-center">Tamanho</th>
									<th class="text-center">Sabor</th>
									<th class="text-center" width="50">Qtd</th>
									<th class="text-center" width="70">Valor</th>
									<th class="text-center" colspan="2" width="60">Desconto</th>
									<th class="text-center" width="100">Subtotal</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="item in detalhes">
									<td>{{ item.nome_produto }}</td>
									<td>{{ item.nome_fabricante }}</td>
									<td> {{ item.peso }} </td>
									<td> {{ item.sabor }} </td>
									<td class="text-center">{{ item.qtd }}</td>
									<td class="text-right">R$ {{ item.valor_real_item  | numberFormat:2:',':'.' }}</td>
									<td class="text-center" width="60">{{ item.valor_desconto * 100 | numberFormat:2:',':'.' }}%</td>
									<td class="text-center" width="20">
										<i class="fa fa-dot-circle-o" ng-if="item.css_cor.length > 0" style="color: {{item.css_cor}}"
											tooltip="(>=) a {{ item.perc_desconto_min * 100 | numberFormat:2:',':'.' }}% e (<=) a {{ item.perc_desconto_max * 100 | numberFormat:2:',':'.' }}%"></i>
									</td>
									<td class="text-right">R$ {{ item.sub_total | numberFormat:2:',':'.'}}</td>
								</tr>
								<tr>
									<td colspan="8" class="text-right">Total</td>
									<td class="text-right">R$ {{ venda.vlr_total_venda | numberFormat : '2' : ',' : '.'}}</td>
								</tr>
							</tbody>
						</table>
		   			</div>
		   		</div>
		    </div>		
		<!-- /.modal -->

		<!-- /Modal Produtos-->
		<div class="modal fade" id="list_produtos" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
						<h4>Produtos</span></h4>
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
											<th>Tamanho</th>
											<th>Sabor/Cor</th>
											<th width="80"></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(produtos.length == 0)">
											<td colspan="3">Não a Produtos cadastrados</td>
										</tr>
										<tr ng-repeat="item in produtos">
											<td>{{ item.id_produto }}</td>
											<td>{{ item.nome_produto }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td>{{ item.peso }}</td>
											<td>{{ item.sabor }}</td>
											<td>
											<button ng-click="addProduto(item)" ng-disabled="produtoEditExistis(item.id_produto)" class="btn btn-success btn-xs" type="button">
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
					  <div class="modal-footer" style="margin-top: 0px;">
						    	<button type="button" data-loading-text=" Aguarde..." id="btn-imprimir"
						    		class="btn btn-md btn-block btn-success" ng-click="voltarVenda()">
						    		<i class="fa fa-reply fa-lg"></i>Voltar a venda
						    	</button>
						    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal editar venda-->
		<div class="modal fade" id="modal-edit-venda" style="display:none">
  			<div class="modal-dialog modal-lg">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Detalhes do Orçamento</h4>
						<div class="alert alert-orcamento-edit" style="display:none">Orçamento alterado com sucesso</div>
						<p class="muted" style="margin: 0 0 1px;" ng-if="venda.nme_cliente != null">Cliente : {{ venda.nme_cliente }}</p>
						<p class="muted" style="margin: 0 0 1px;" ng-if="venda.nme_cliente != null">Perfil : {{ venda.perfil_cliente }}</p>
						<p class="muted" style="margin: 0 0 1px;">ID #{{ venda.id }}</p>
      				</div>
				    <div class="modal-body" sty>
				   		<div class="row">
				   			<div class="col-sm-12">
				   				<table class="table table-bordered table-condensed table-striped table-hover">
									<thead>
										<tr>
											<th class="text-center">Produto</th>
											<th class="text-center">Fabricante</th>
											<th class="text-center">Peso/Cor</th>
											<th class="text-center">Vlr Produto</th>
											<th class="text-center" width="50">Qtd</th>
											<th class="text-center" width="60" colspan="3">Desconto(%)</th>
											<th class="text-center" width="90">Vlr Compra</th>
											<th class="text-center" width="100">Subtotal</th>
											<th class="text-center" width="80"><button ng-click="showProdutos()" class="btn btn-xs btn-success" type="button"><i class="fa fa-plus fa-lg"></i></button></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in edit">
											<td>{{ item.nome_produto }}</td>
											<td class="text-center">{{ item.nome_fabricante }}</td>
											<td class="text-center">{{ item.peso }}</td>
											<td class="text-center">{{ item.vlr_produto }}</td>
											<td class="text-center"> <input ng-model="item.qtd" ng-keyup="calTotalVendaEdit()" class="form-control input-xs" /></td>
											<td class="text-center" style="width: 30px;">
											<label class="label-checkbox">
												<input ng-model="item.flg_desconto" type="checkbox" id="toggleLine" ng-true-value="1" ng-false-value="0" ng-click="calDescVendaEdit(item)" />
												<span class="custom-checkbox"></span>
											</label>
										</td>
										<td class="text-right" style="width:100px;">
											<span style="display: block;float: left;" ng-if="item.flg_desconto == 1">%</span>
											<input style="width:67px;float:right" ng-keyUp="calDescVendaEdit(item,'per')" ng-if="item.flg_desconto == 1" thousands-formatter ng-model="item.valor_desconto" type="text" class="form-control input-xs" />
										</td>
										<td class="text-right" style="width:100px;">
											<span style="display: block;float: left;" ng-if="item.flg_desconto == 1">R$</span>
											<input style="width:67px;float:right" ng-keyUp="calDescVendaEdit(item,'vlr')" ng-if="item.flg_desconto == 1" thousands-formatter ng-model="item.valor_desconto_real" type="text" class="form-control input-xs" id="teste_teste" />
										</td>

											<!--<td class="text-center" width="60" ><input ng-keyup="calDescVendaEdit(item)" ng-model="item.valor_desconto" thousands-formatter class="form-control input-xs" /></td>
											--><td class="text-right">R$ {{ item.valor_real_item | numberFormat:2:',':'.'}} </td>
											<td class="text-right">R$ {{ item.sub_total | numberFormat:2:',':'.'}}</td>
											<td class="text-right">
												<button type="button" ng-click="excluirItemEdit($index,item)"  tooltip="Detalhes" data-toggle="tooltip" class="btn btn-xs btn-danger">
													<i class="fa fa-trash-o"></i>
												</button>
												<button type="button" class="btn btn-xs btn-primary" ng-click="getLastVendaProdutoByCliente(item)"  data-animation="am-flip-x" popover-placement="bottom" popover-trigger="focus"  popover-template="popover-template" class="btn btn-default">
													<i class="fa fa-eye fa-xs"></i>
												</button>
											</td>
										</tr>
										<tr>
											<td colspan="9" style="text-align:right">
												Total
											</td>
											<td style="text-align:right">
												R$ {{ total_venda_edit | numberFormat:2:',':'.' }}
											</td>
											<td>

											</td>
										</tr>
									</tbody>
								</table>
				   			</div>
				   		</div>
				    </div>
				    <div class="modal-footer" ng-if="paginacao_clientes != null">
				    	<ul class="pagination pagination-xs m-top-none pull-right">
							<li ng-repeat="item in paginacao_clientes" ng-class="{'active': item.current}">
								<a href="" ng-click="loadCliente(item.offset,item.limit)">{{ item.index }}</a>
							</li>
						</ul>
				    </div>
				     <div class="modal-footer">
						    	<button type="button" data-loading-text=" Aguarde..." id="salvar-orcamento"
						    		class="btn btn-md btn-block btn-success" ng-click="editarOrcamento()">
						    		<i class="fa fa-save"></i> Salvar
						    	</button>
						    	<button type="button" data-loading-text=" Aguarde..." ng-click="cancelarModal('modal-edit-venda')" id="btn-aplicar-reforco"
						    		class="btn btn-md btn-block btn-default fechar-modal">
						    		<i class="fa fa-times-circle"></i> Cancelar
						    	</button>
						    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal Clientes-->
		<div class="modal fade" id="list_usuarios" style="display:none">
  			<div class="modal-dialog modal-lg" >
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>{{ busca.tipo_usuario == 'vendedor' && 'Vendedores' || 'Clientes' }}</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.usuarios"   type="text" class="form-control input-sm"
						            	ng-enter="loadUsuarios(0,10,busca.tipo_usuario)"
						            	ng-model="loadUsuarios(0,10,busca.tipo_usuario)"
						            	ng-keyup="loadUsuarios(0,10,busca.tipo_usuario)"
						            	>
						            <div class="input-group-btn">
						            	<button ng-click="loadUsuarios(0,10,busca.tipo_usuario)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
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
									<tr ng-if="usuarios.length <= 0 || usuarios == null">
										<th ng-if="emptyBusca.usuarios == false"  class="text-center" colspan="9" style="text-align:center"><strong>Carregando</strong><img src="assets/imagens/progresso_venda.gif"></th>
										<th ng-if="emptyBusca.usuarios == true"  class="text-center" colspan="9" style="text-align:center">Não a resultado para a busca</th>
									</tr>
									<thead ng-show="(usuarios.length != 0)">
										<tr>
											<th >Nome</th>
											<th >Apelido</th>
											<th >Perfil</th>
											<th colspan="2">selecionar</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in usuarios">
											<td>{{ item.nome }}</td>
											<td>{{ item.apelido }}</td>
											<td>{{ item.nome_perfil }}</td>
											<td width="50" align="center">
												<button  type="button" class="btn btn-xs btn-success" ng-click="addUsuario(item)">
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
				    			<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao_usuarios.length > 1">
									<li ng-repeat="item in paginacao_usuarios" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadUsuarios(item.offset,item.limit,busca.tipo_usuario)">{{ item.index }}</a>
									</li>
								</ul>
				    		</div>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal clientes-->
		<div class="modal fade" id="list_clientes" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>clientes</h4>
      				</div>
				    <div class="modal-body">
				    	<ul class="pagination pagination-xs m-top-none pull-right">
							<li ng-repeat="item in paginacao_clientes" ng-class="{'active': item.current}">
								<a href="" h ng-click="loadCliente(item.offset,item.limit)">{{ item.index }}</a>
							</li>
						</ul>
						</br></br>
				   		<table class="table table-bordered table-condensed table-striped table-hover">
							<thead ng-show="(clientes.length != 0)">
								<tr>
									<th>#</th>
									<th>nome</th>
									<th>perfil</th>
									<th>selecionar</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="item in clientes">
									<td>{{ item.id }}</td>
									<td>{{ item.nome }}</td>
									<th>{{ item.nome_perfil }}</th>
									<td>
									<button ng-click="addCliente(item)" style="margin-bottom:10px" class="btn btn-success btn-xs" type="button">
											<i class="fa fa-plus-circle fa-lg"></i>
									</button>
									</td>
								</tr>
							</tbody>
						</table>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal Vendedor -->
		<div class="modal fade" id="list-vendedor" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
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
												<button type="button" class="btn btn-xs btn-success" ng-click="changeVendedor(item.id)">
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
  <script id="popover-template" type="text/ng-template">
    <div id="tip" ng-show="loadPopover">
      carregando
    </div>
    <div id="tip" ng-show="loadPopover == false">
      <b>Dta da venda:</b> R$ {{ last_venda.dta_venda | dateFormat:'dateTime' }}  <br/> 
      <b>Valor de custo:</b> R$ {{ last_venda.vlr_custo | numberFormat:2:',':'.' }}  <br/>
      <b>Valor de venda:</b> R$ {{ last_venda.vlr_produto | numberFormat:2:',':'.' }} <br/>
      <b>Valor desconto(%):</b> {{ last_venda.valor_desconto}}%<br/>
      <b>valor desconto(R$):</b> R$ {{ last_venda.vlr_real_desconto | numberFormat:2:',':'.' }} <br>  
      <b>Valor vendido:</b> R$ {{ last_venda.valor_real_item | numberFormat:2:',':'.' }}
    </div>
  </script>
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

	<!-- Extras -->
	<script src="js/extras.js"></script>

	<!-- Mascaras para o formulario de produtos -->
	<script src="js/scripts/mascaras.js"></script>

	<!-- UnderscoreJS -->
	<script type="text/javascript" src="bower_components/underscore/underscore.js"></script>

	<script src="js/jquery.noty.packaged.js"></script>

	<script type="text/javascript" src="bower_components/angular/angular.js"></script>
	<script type="text/javascript" src="bower_components/angular-ui-utils/mask.min.js"></script>
    <script src="js/angular-sanitize.min.js"></script>
    <script src="js/ui-bootstrap-tpls-0.6.0.js" type="text/javascript"></script>
    <script src="js/dialogs.v2.min.js" type="text/javascript"></script>
    <script src="js/auto-complete/ng-sanitize.js"></script>
    <script src="js/auto-complete/ng-sanitize.js"></script>
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/vendas-controller.js?<?php echo filemtime('js/angular-controller/vendas-controller.js') ?>"></script>
	<script type="text/javascript">
		$("#cld_dtaInicial").on("click", function(){ $("#dtaInicial").trigger("focus"); });
		$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

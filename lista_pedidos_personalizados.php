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

  <body class="overflow-hidden" ng-controller="PedidoVendaController" ng-cloak>

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
					 <li><i class="fa fa-tag"></i> <a>Pedidos Personalizados</a></li>
				</ul>
			</div>
			<!-- breadcrumb -->
			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-tag"></i> Pedidos Personalizados</h3>
					<br/>
					<a href="pedido-personalizado.php" class="btn btn-primary" ng-if="userLogged.id_empreendimento == 52 || userLogged.id_empreendimento == 51 || userLogged.id_empreendimento == 6 "><i class="fa fa-tag"></i> Novo Pedido</a>
				</div>
			</div>
			<!-- /main-header -->

			<div class="padding-md">
				<div class="alert alert-sistema" style="display:none"></div>
				<div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-tasks"></i> Pedidos</div>
					<div class="panel-body">
						<div class="row" ng-if="false">
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
									<input ng-click="selUsuario('vendedor')" type="text" class="form-control" ng-model="busca.ven_nome_vendedor" readonly="readonly" style="cursor: pointer;" />
									<span class="input-group-btn">
										<button ng-click="selUsuario('vendedor')"  type="button" class="btn"><i class="fa fa-user-md"></i></button>
									</span>
								</div>
							</div>
								<div class="col-sm-3">
								<label class="control-label">Cliente</label>
								<div class="input-group">
									<input ng-click="selUsuario('vendedor')" type="text" class="form-control" ng-model="busca.ven_nome_cliente" readonly="readonly" style="cursor: pointer;" />
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
							<div class="col-sm-12">
								<div class="alert alert-listagem" style="display:none"></div>
							</div>
							<div class="col-sm-12">
								<table class="table table-condensed table-bordered table-striped table-hover">
									<thead>
										<tr>
											<th class="text-center" width="160">ID</th>
											<th class="text-center" width="160">Data Lan.</th>
											<th class="text-center">Vendedor</th>
											<th class="text-center">Cliente</th>
											<th class="text-center" width="160">status</th>
											<th class="text-center" width="100">Total</th>
											<th class="text-center" width="200">Ações</th>
										</tr>
									</thead>
									<tbody> 
										<tr ng-if="vendas.length == 0">
											<td colspan="7" class="text-center">
												Nenhum pedido encontrado	
											</td>
										</tr>
										<tr ng-repeat="item in vendas" bs-tooltip>
											<td class="text-center">{{ item.id }}</td>
											<td class="text-center">{{ item.dta_lancamento | dateFormat }}</td>
											<td>{{ item.nme_vendedor }}</td>
											<td>{{ item.nme_cliente }}</td>
											<td class="text-center">
												{{  item.dsc_status }}
											</td>
											<td class="text-right">R$ {{ item.vlr_total_venda | numberFormat : '2' : ',' : '.'}}</td>
											<td class="text-center">
												<button type="button" ng-click="loadDetalhesPedido(item)" tooltip="Detalhes" data-toggle="tooltip" class="btn btn-xs btn-primary"  data-toggle="tooltip" title="Detalhes">
													<i class="fa fa-tasks"></i>
												</button>
												<a  href="pedido-personalizado.php?id_pedido={{ item.id }}" data-loading-text="<i class='fa fa-refresh fa-spin'>" type="button"  ng-if="item.id_status_pedido == 1"  tooltip="Editar pedido" data-toggle="tooltip" class="btn btn-xs btn-warning"  data-toggle="tooltip" title="Editar pedido">
													<i class="fa fa-edit"></i>
												</a>
												<button ng-click="imprimirRomaneio(item)"   type="button" tooltip="Imprimir Via Fábrica" data-toggle="tooltip" class="btn btn-xs"  data-toggle="tooltip" title="Imprimir Via Fábrica">
													<i class="fa fa-print"></i>
												</button>
												<button ng-click="imprimirRomaneioCliente(item)"  type="button" tooltip="Imprimir Via Cliente" data-toggle="tooltip" class="btn btn-xs"  data-toggle="tooltip" title="Imprimir Via Cliente">
													<i class="fa fa-print"></i>
												</button>

												<a href="vendas.php?id_venda={{ item.id_venda }}" target="_blank" type="button" ng-show="item.id_status_pedido >= 3"  tooltip="Ver venda" data-toggle="tooltip" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Ver Venda Gerada">
													<i class="fa fa-shopping-cart"></i>
												</a>

												<button data-loading-text="<i class='fa fa-refresh fa-spin'>" type="button" ng-click="changeStatus(item,2,'Tem Certeza que deseja eviar o pedido para produção?',$event)" ng-show="item.id_status_pedido == 1"  tooltip="Detalhes" data-toggle="tooltip" class="btn btn-xs btn-primary"  data-toggle="tooltip" title="Enviar p/ Produção">
													<i class="fa fa-cogs"></i>
												</button>

												<button data-loading-text="<i class='fa fa-refresh fa-spin'>" type="button" ng-click="finalizaPedido($index,$event)" ng-show="item.id_status_pedido == 2"  tooltip="Detalhes" data-toggle="tooltip" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Finalizar Pedido">
													<i class="fa fa-check"></i>
												</button>

												<button data-loading-text="<i class='fa fa-refresh fa-spin'>" type="button" ng-click="changeStatus(item,4,'Tem certeza que deseja enviar o pedido para transporte?',$event)" ng-show="item.id_status_pedido == 3"  tooltip="Detalhes" data-toggle="tooltip" class="btn btn-xs btn-warning"  data-toggle="tooltip" title="Enviar p/ Transporte">
													<i class="fa fa-truck"></i>
												</button>

												<button data-loading-text="<i class='fa fa-refresh fa-spin'>" type="button" ng-click="changeStatus(item,5,'Tem certeza que deseja marcar o pedido como entegue?',$event)" ng-show="item.id_status_pedido == 4"  tooltip="Detalhes" data-toggle="tooltip" class="btn btn-xs btn-success"  data-toggle="tooltip" title="Pedido Entegue">
													<i class="fa fa-thumbs-up"></i>
												</button>
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

		<!-- /Modal Tiras-->
		<div class="modal fade" id="list_tiras" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Tiras</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.tiras"  ng-enter="loadCliente(0,10)" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadTiras(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div> <!-- /input-group-btn -->
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>
						<br />
						<div class="row">

							<div class="col-sm-12">
								<div class="alert" id="alert-tiras" style="margin-bottom: 9px;display: none;">
									
								</div>
								<table class="table table-bordered table-condensed table-striped table-hover">
									<tr ng-if="tiras.length == 0">
										<th class="text-center" colspan="9" style="text-align:center"><strong>Carregando</strong><img src="assets/imagens/progresso_venda.gif"></th>
									</tr>
									<tr ng-if="tiras == null">
										<th colspan="5" class="text-center">Não a resultados para a busca</th>
									</tr>
									<thead ng-show="(tiras.length > 0)">
										<tr>
											<th class="text-center">ID</th>
											<th>Nome</th>
											<th>Tamanho</th>
											<th>Cor</th>
											<td width="80">Qtd</td>
											<th colspan="2">selecionar</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in tiras">
											<td>{{ item.id_produto }}</td>
											<td>{{ item.nome }}</td>
											<td>{{ item.nome_tamanho }}</td>
											<td>{{ item.nome_cor }}</td>
											<td><input ng-model="item.qtd" type="text" class="form-control input-xs text-center" /></td>
											<td width="50" align="center">
												<button type="button" ng-disabled="empty(item.qtd)" class="btn btn-xs btn-success" ng-click="selTira(item)">
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
				    			<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao.tiras.length > 1">
									<li ng-repeat="item in paginacao.tiras" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadTiras(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
				    		</div>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal Acessorios-->
		<div class="modal fade" id="list_acessorios" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Acessórios</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.tiras"  ng-enter="loadAcessorios(0,10)" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadAcessorios(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div> <!-- /input-group-btn -->
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>
						<br />
						<div class="row">

							<div class="col-sm-12">
								<div class="alert" id="alert-acessorios" style="margin-bottom: 9px;display: none;">
									
								</div>
								<table class="table table-bordered table-condensed table-striped table-hover">
									<tr ng-if="acessorios.length == 0">
										<th class="text-center" colspan="9" style="text-align:center"><strong>Carregando</strong><img src="assets/imagens/progresso_venda.gif"></th>
									</tr>
									<tr ng-if="acessorios == null">
										<th colspan="5" class="text-center">Não a resultados para a busca</th>
									</tr>
									<thead ng-show="(acessorios.length > 0)">
										<tr>
											<th class="text-center">ID</th>
											<th>Nome</th>
											<th>Tamanho</th>
											<th>Cor</th>
											<td width="80">Qtd</td>
											<th colspan="2">selecionar</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in acessorios">
											<td>{{ item.id_produto }}</td>
											<td>{{ item.nome }}</td>
											<td>{{ item.nome_tamanho }}</td>
											<td>{{ item.nome_cor }}</td>
											<td><input ng-model="item.qtd" type="text" class="form-control input-xs text-center" /></td>
											<td width="50" align="center">
												<button type="button" ng-disabled="empty(item.qtd)" class="btn btn-xs btn-success" ng-click="selAcessorio(item)">
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
				    			<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao.acessorios.length > 1">
									<li ng-repeat="item in paginacao.acessorios" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadAcessorios(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
				    		</div>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal detalhes da venda-->
		<div class="modal fade" id="list_detalhes" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content" >
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Detalhes do Pedido</h4>
						<p class="muted" style="margin: 0px 0 1px;" ng-if="pedido.nme_cliente != null">Cliente : {{ pedido.nme_cliente }}</p>
						<p class="muted" style="margin: 0px 0 1px;">ID pedido #{{ pedido.id }}</p>
						<p class="muted" style="margin: 0px 0 1px;" ng-if=" pedido.pedido_finalizado == 1 ">ID venda  <a href="vendas.php?id_venda={{ pedido.id_venda }}" target="_blank">#{{ pedido.id_venda }}</a></p>
      					<p class="muted" style="margin: 0px 0 1px;">Total Pedido : R$ {{ pedido.vlr_total_venda | numberFormat : '2' : ',' : '.'}}</p>
      				</div>
				    <div class="modal-body" style="max-height: 400px;overflow: auto;"	>
				   		<div class="row">
				   			<div class="col-sm-12">
								<div class="alert alert-out alert-warning" ng-if="pro_out_estoque.length > 0">
									Desculpe, os produtos marcados em <span style="color:#FF9191">vermelho</span>
								    não tem o estoque necessário para finalizar o pedido.
								</div>
							</div>
				   			<div class="col-sm-12">
				   				<div class="alert alert-detalhes-pedido" style="display:none">
				   					
				   				</div>
				   				<table class="table table-bordered table-condensed">
									<thead ng-show="(clientes.length != 0)">
										<tr>
											<th class="text-center"  width="100">ID</th>
											<th class="text-center">Produto</th>
											<th class="text-center" width="50">Tamanho</th>
											<th class="text-center" width="70">Cor</th>
											<th class="text-center" width="60">qtd</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in detalhes" ng-class="{'tr-error-estoque':outEstoque(item)}">
											<td class="text-center">{{ item.id_produto }}</td>
											<td>{{ item.nome_produto }}</td>
											<td>{{ item.nome_tamanho }}</td>
											<td>{{ item.nome_cor }}</td>
											<td class="text-center">{{ item.qtd }}</td>
										</tr>
									</tbody>
								</table>



				   			</div>
				   		</div>
				   		<div class="row">
				   			<div class="col-sm-12">
				   				<ul class="pagination pagination-xs m-top-none pull-right" ng-if="paginacao.detalhes.length > 1">
									<li ng-repeat="item in paginacao.detalhes" ng-class="{'active': item.current}">
										<a href="" ng-click="loadDetalhesPedido(venda,item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
				   			</div>
				   		</div>
				    </div>
				    <div class="modal-footer">
				 
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

















			<!-- /Modal Clientes-->
		<div class="modal fade" id="list_pdf" style="display:none">
  			<div class="modal-dialog modal-lg">
    			<div class="modal-content">
				    <div class="modal-body">
				    <iframe src="http://localhost/webliniaerp-api/relPDF?classe=PedidoVendaDao&metodo=getRelRomaneio&parametros%5B%5D=10&template=romaneio_pedido_personalizado#zoom=103" style="zoom:0.60;height:800px" width="99.6%" frameborder="0"></iframe>
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

    <!-- Easy Modal -->
    <script src="js/eModal.js"></script>

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
	<script src="js/angular-controller/pedido_venda-controller.js?<?php echo filemtime('js/angular-controller/pedido_venda-controller.js') ?>"></script>
	<script type="text/javascript">
		$("#cld_dtaInicial").on("click", function(){ $("#dtaInicial").trigger("focus"); });


		$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
		// $(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

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
			#list_proodutos.modal-dialog  {width:900px;}
		}

		#list_produtos .modal-dialog  {width:70%;}

		#list_produtos .modal-content {min-height: 640px;;}


	</style>
  </head>

  <body class="overflow-hidden" ng-controller="PedidosFornecedoresController" ng-cloak>
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
					 <li><i class="fa fa-truck"></i> <a href="fornecedores.php">Fornecedores</a></li>
					 <li class="active"><i class="fa fa-clipboard"></i> Pedidos</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-clipboard"></i> Pedidos</h3>
					<br/>
					<a class="btn btn-info" id="btn-novo-pedido" ng-click="showBoxNovoPedido()"><i class="fa fa-plus-circle"></i> Novo Pedido</a>
				</div><!-- /page-title -->
			</div><!-- /main-header -->
			<div class="padding-md">

				<div class="alert alert-sistema" style="display:none"></div>

				<div class="panel panel-default" id="box-novo-pedido" style="display:none">
					<div class="panel-heading">Novo pedido</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label class="control-label">Fornecedor</label>
									<div class="input-group">
										<input type="text" class="form-control" ng-model="fornecedor.nome_fornecedor">
										<spam class="input-group-addon" ng-click="selFornecedor()"><i class="fa fa-tasks"></i></spam>
									</div>
								</div>
							</div>

							<div class="col-sm-3">
								<div class="form-group">
									<div class="form-group">
										<label class="control-label">Este é um pedido real?</label>
										<div class="form-group">
											<label class="label-radio inline">
												<input name="pedido_real" ng-model="flg_pedido_real" value="1" type="radio" class="inline-radio">
												<span class="custom-radio"></span>
												Sim
											</label>

											<label class="label-radio inline">
												<input name="pedido_real" ng-model="flg_pedido_real" value="0" type="radio" class="inline-radio">
												<span class="custom-radio"></span>
												Não
											</label>

											<!-- <label class="control-radio">
												Sim  &nbsp;
							                    <input name="pedido_real" ng-model="flg_pedido_real" value="1" type="radio" style="opacity: 1;">
							                </label>
							                 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
							                <label class="control-radio">
												Não  &nbsp;
							                    <input name="pedido_real" ng-model="flg_pedido_real" value="0" type="radio" style="opacity: 1;">
							                </label> -->
							            </div>
							        </div>
								</div>
							</div>

							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label"><br></label>
									<button type="button" class="btn btn-default form-control" ng-click="selProduto()">
										<i class="fa fa-plus-circle"></i> Adicionar Produto
									</button>
								</div>
							</div>
						</div>

						<form class="clearfix">
							<table class="table table-bordered table-condensed table-striped table-hover">
								<thead ng-show="(novoPedido.length != 0 && novoPedido != null)">
									<tr>
										<th>Produto</th>
										<th>Fabricante</th>
										<th>Tamanho</th>
										<th style="width: 80px;">Quantidade</th>
										<th style="width: 100px; text-align: center;">Vlr. Unitário</th>
										<th style="width: 100px; text-align: center;">Subtotal</th>
										<th width="80" style="text-align: center;">Opções</th>
									</tr>
								</thead>
								<tbody>
									<tr ng-show="(novoPedido.length == 0 || novoPedido == null)">
										<td colspan="4">Adicione itens para o novo pedido</td>
									<tr>
									<tr ng-repeat="item in novoPedido">
										<td>{{ item.nome_produto }}</td>
										<td>{{ item.nome_fabricante }}</td>
										<td>{{ item.peso }}</td>
										<td><input ng-model="item.qtd" ng-keyup="changeQtd()" style="text-align: center;" type="text" class="form-control input-xs"/></td>
										<td style="text-align: right;">{{ item.custo_compra | numberFormat: 2 : ',' : '.' }}</td>
										<td style="text-align: right;">{{ item.qtd * item.custo_compra | numberFormat: 2 : ',' : '.' }}</td>
										<td width="80" style="text-align: center;">
											<button type="button" ng-click="deleteItem($index)" tooltip="excluir" class="btn btn-xs btn-danger delete" data-toggle="tooltip" data-placement="right" title="Tooltip on right">
												<i class="fa fa-trash-o"></i>
											</button>
										</td>
									</tr>
									<tr ng-hide="(novoPedido.length == 0 || novoPedido == null)">
										<td colspan="5" style="text-align:right">Total</td>
										<td style="text-align: right;">{{ total | numberFormat:2:',':'.'}}</td>
										<td></td>
									</tr>
								</tbody>
							</table>
							<div class="row pull-right">
								<div class="col-md-3">
									<div class="form-group">
										<button ng-click="salvarPedido()" type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i> Salvar</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div><!-- /panel -->
				<div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-filter"></i> Opções de Filtro</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-2">
								<div class="form-group">
								<label class="control-label">Data do Pedido</label>
									<div class="input-group">
										<input readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="datapedido" class="datepicker form-control text-center">
										<span class="input-group-addon" id="cld_datapedido"><i class="fa fa-calendar"></i></span>
									</div>
								</div>
							</div>

							<div class="col-sm-4">
								<div class="form-group">
									<label class="control-label">Fornecedor</label>
									<input ng-model="busca.fornecedor" ng-enter="loadPedidosFornecedores(0,10)" type="text" class="form-control input-sm ng-pristine ng-valid ng-touched">
								</div>
							</div>

							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label">Solicitante</label>
									<input ng-model="busca.solicitante" ng-enter="loadPedidosFornecedores(0,10)" type="text" class="form-control input-sm ng-pristine ng-valid ng-touched">
								</div>
							</div>

							<div class="col-sm-1">
								<div class="form-group">
									<label class="control-label"><br></label>
									<button type="button" class="btn btn-sm btn-primary" ng-click="loadPedidosFornecedores(0,10)"><i class="fa fa-filter"></i> Filtrar</button>
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
					<div class="panel-heading"><i class="fa fa-tasks"></i> Pedidos Realizados</div>
					<div class="panel-body">
						
						<table class="table table-bordered table-condensed table-striped table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Data do Pedido</th>
									<th>Fornecedor</th>
									<th>Solicitante</th>
									<th>Qtd</th>
									<th>Total</th>
									
									<th width="80" style="text-align: center;">Opções</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="item in pedidos">
									<td>{{ item.id }}</td>
									<td>{{ item.dta_pedido }}</td>
									<td>{{ item.nome_fornecedor }}</td>
									<td ng-if="item.id_venda == null " ><a target="_blank" href="{{ bseUrl }}usuarios.php?id_usuario={{ item.id_usuario }}">{{ item.nome_usuario }}</a></td>
									<td ng-if="item.id_venda != null " >Gerado automaticamente</td>
									<td>{{ item.qtd_pedido }}</td>
									<td>R$ {{ item.total_pedido | numberFormat:2:',':'.' }}</td>
									
									<td align="center">
										<button type="button" ng-click="viewDetalhes(item)" tooltip="Detalhes" class="btn btn-xs btn-info" data-toggle="tooltip">
											<i class="fa fa-tasks"></i>
										</button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="panel-footer clearfix">
						<ul class="pagination pagination-xs m-top-none pull-right">
							<li ng-repeat="item in paginacao_pedidos" ng-class="{'active': item.current}">
								<a href="" h ng-click="loadPedidosFornecedores(item.offset,item.limit)">{{ item.index }}</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div><!-- /main-container -->

		<!-- /Modal Produtos-->
		<div class="modal fade" id="list_produtos" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Produtos</h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="pesquisa.produto" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadProdutosBusca()" tabindex="-1" class="btn btn-sm btn-primary" type="button"><i class="fa fa-search"></i> Buscar</button>
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
											<th>Nome</th>
											<th>Fabricante</th>
											<th >Tamanho</th>
											<th >Sabor/cor</th>
											<th width="80"></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in produtos">
											<td>{{ item.nome }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td>{{ item.peso }}</td>
											<td>{{ item.sabor }}</td>
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
							<div class="col-sm-12">
								<ul class="pagination pagination-xs m-top-none pull-right">
									<li ng-repeat="item in paginacao_produtos" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadProdutos(item.offset,item.limit)">{{ item.index }}</a>
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
						<h4>Selecionar Fornecedor <span style="color:rgba(41, 145, 179, 1)">{{ nome_produto_form }}</span></h4>
      				</div>

				    <div class="modal-body">
				    	<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-enter="loadFornecedores(0,10)" ng-model="busca.fornecedores" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button  ng-click="loadFornecedores(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div> <!-- /input-group-btn -->
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>
						<br />
				   		<table class="table table-bordered table-condensed table-striped table-hover">
							<thead ng-show="(fornecedores.length != 0)">
								<tr>
									<th colspan="2">nome</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-show="(fornecedores.length == 0)">
									<td colspan="2">Nenhum fornecedor encontrado.</td>
								</tr>
								<tr ng-repeat="item in fornecedores">
									<td>{{ item.nome_fornecedor }}</td>
									<td width="50" align="center">
										<button ng-click="addFornecedor(item)" class="btn btn-success btn-xs" type="button">
											<i class="fa fa-check-square-o"></i> Selecionar
										</button>
									</td>
								</tr>
							</tbody>
						</table>
				    </div>

				    <div class="modal-footer">
				    	<div class="row">
				    		<div class="col-sm-12">
				    			<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao_fornecedores.length > 1">
									<li ng-repeat="item in paginacao_fornecedores" ng-class="{'active': item.current}">
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



		<div class="modal fade" id="list_fornecedores2" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Selecionar Fornecedor <span style="color:rgba(41, 145, 179, 1)">{{ nome_produto_form }}</span></h4>
      				</div>

				    <div class="modal-body">
				    	<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-enter="loadFornecedores(0,10)" ng-model="busca.fornecedores" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button  ng-click="loadFornecedores(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div> <!-- /input-group-btn -->
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>
						<br />
				   		<table class="table table-bordered table-condensed table-striped table-hover">
							<thead ng-show="(fornecedores.length != 0)">
								<tr>
									<th colspan="2">nome</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-show="(fornecedores.length == 0)">
									<td colspan="2">Nenhum fornecedor encontrado.</td>
								</tr>
								<tr ng-repeat="item in fornecedores">
									<td>{{ item.nome_fornecedor }}</td>
									<td width="50" align="center">
										<button ng-click="addFornecedor(item)" class="btn btn-success btn-xs" type="button">
											<i class="fa fa-check-square-o"></i> Selecionar
										</button>
									</td>
								</tr>
							</tbody>
						</table>
				    </div>

				    <div class="modal-footer">
				    	<div class="row">
				    		<div class="col-sm-12">
				    			<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao_fornecedores.length > 1">
									<li ng-repeat="item in paginacao_fornecedores" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadFornecedores(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
				    		</div>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>





		<!-- /Modal Itens do pedido-->
		<div class="modal fade" id="view-itens-pedido" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Itens do pedido</h4>
      				</div>
				    <div class="modal-body">
				   		<table class="table table-bordered table-condensed table-striped table-hover">
							<thead ng-show="(itensPedido.length != 0)">
								<tr>
									<th>#</th>
									<th>Produto</th>
									<th>Fabricante</th>
									<th>Tamanho</th>
									<th>Sabor/cor</th>
									<th>Quantidade</th>
									<th>valor</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-show="(itensPedido.length == 0)">
									<td colspan="3">Não a fornecedores relacionados para esté produto</td>
								</tr>
								<tr ng-repeat="item in itensPedido">
									<td>{{ item.id }}</td>
									<td>{{ item.nome_produto }}</td>
									<td>{{ item.nome_fabricante }}</td>
									<td>{{ item.peso }}</td>
									<td>{{ item.sabor }}</td>
									<td>{{ item.qtd}}</td>
									<td>R$ {{ item.vlr_custo_produto | numberFormat:2:',':'.'}}</td>
								</tr>
							</tbody>
						</table>
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

    <!-- Datepicker -->
	<script src='js/bootstrap-datepicker.min.js'></script>

	<!-- Timepicker -->
	<script src='js/bootstrap-timepicker.min.js'></script>

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

	<!-- Bower Components -->	
	<script src="bower_components/noty/lib/noty.min.js" type="text/javascript"></script>
    <script src="bower_components/mojs/build/mo.min.js" type="text/javascript"></script>

	<!-- Extras -->
	<script src="js/extras.js"></script>

	<!-- AngularJS -->
	<script type="text/javascript" src="bower_components/angular/angular.js"></script>
	<script src="js/angular-strap.min.js"></script>
	<script src="js/angular-strap.tpl.min.js"></script>
	<script type="text/javascript" src="bower_components/angular-ui-utils/mask.min.js"></script>
    <script src="js/angular-sanitize.min.js"></script>
    <script src="js/ui-bootstrap-tpls-0.6.0.js" type="text/javascript"></script>
    <script src="js/dialogs.v2.min.js" type="text/javascript"></script>
    <script src="js/auto-complete/ng-sanitize.js"></script>
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/pedidos_fornecedores-controller.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#datapedido").datepicker();
			$("#cld_datapedido").on("click", function(){ $("#datapedido").trigger("focus"); });
			$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
			$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});
		});
	</script>
	<?php include("google_analytics.php"); ?>

  </body>
</html>

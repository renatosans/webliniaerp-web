<?php
	include_once "util/login/restrito.php";
	restrito(array(8,1));
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
		    <link rel='stylesheet prefetch' href='bootstrap/css/bootstrap.min.css?version=<?php echo date("dmY-His", filemtime("bootstrap/css/bootstrap.min.css")) ?>'>

			<!-- Font Awesome -->
			<link href="css/font-awesome-4.6.2/css/font-awesome.min.css?version<?php  echo date("dmY-His", filemtime("css/font-awesome-4.1.0.min.css")) ?>" rel="stylesheet">

			<!-- Pace -->
			<link href="css/pace.css" rel="stylesheet">

			<!-- Gritter -->
			<link href="css/gritter/jquery.gritter.css" rel="stylesheet">

			<!-- Datepicker -->
			<link href="css/datepicker.css" rel="stylesheet"/>

			<!-- Chosen -->
			<link href="css/chosen/chosen.min.css" rel="stylesheet"/>

			<!-- Endless -->
			<link href="css/endless.min.css" rel="stylesheet">
			<link href="css/endless-skin.css" rel="stylesheet">
			<link href="css/custom.css" rel="stylesheet">

			<!-- autocomplete -->
			<link href="css/autocomplete.css" rel="stylesheet">
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

				/*@media screen and (min-width: 768px) {

					#list_validades.modal-dialog  {width:900px;}

				}

				#list_validades .modal-dialog  {width:70%;}

				#list_validades .modal-content {min-height: 640px;}*/

				.error-estoque td {
					background: #FF9191
				}

				.scrollableContainer {
		    height: 300px;
		    position: relative;
		    padding-top: 37px;
		    margin-bottom: 30px;
		}
		.scrollableContainer .headerSpacer {
		    border: 1px solid #d5d5d5;
		    border-bottom-color: #bbb;
		    position: absolute;
		    height: 36px;
		    top: 0;
		    right: 0;
		    left: 0;
		}
		.scrollableContainer th .th-inner .title > span {
		    display: block;
		    overflow: hidden;
		    text-overflow: ellipsis;
		}
		.scrollableContainer th .orderWrapper {
		    position: absolute;
		    top: 0;
		    right: 2px;
		    cursor: pointer;
		}
		.scrollArea {
		    height: 100%;
		    overflow-x: hidden;
		    overflow-y: auto;
		    border: 1px solid #d5d5d5;
		}
		.scrollArea table {
		    overflow-x: hidden;
		    overflow-y: auto;
		    margin-bottom: 0;
		    border: none;
		    border-collapse: separate;
		}
		.scrollArea table th {
		    padding: 0;
		    border: none;
		}
		.scrollArea table .th-inner {
		    overflow: hidden;
		    text-overflow: ellipsis;
		    white-space: nowrap;
		    position: absolute;
		    top: 0;
		    height: 36px;
		    line-height: 36px;
		    padding: 0 8px;
		    border-left: 1px solid #ddd;
		}
		.scrollArea table tr th:first-child .th-inner {
		    border-left: none;
		}
		.scrollArea table .th-inner.condensed {
		    padding: 0 3px;
		}
		.scrollArea table tbody tr td:first-child {
		    border-left: none;
		}
		.scrollArea table tbody tr td:last-child {
		    border-right: none;
		}
		.scrollArea table tbody tr:first-child td {
		    border-top: none;
		}
		.scrollArea table tbody tr:last-child td {
		    border-bottom: 1px solid #ddd;
		}

		table .cr {
		    min-width: 30px;    
		}
			</style>
    </head>

	<body ng-click="closeAutoComplete($event)" class="overflow-hidden" ng-controller="PDVController" ng-cloak>
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

			<div id="main-container" style="min-height: 0px !important;">
				<div class="padding-md">
					<div class="row">
						<div class="col-sm-10" id="col-sm-auto-complete-cliente">
							<div class="form-group">
								<label class="control-label"><h4>Cliente <span> <button  style="cursor:auto;height: 18px;padding-top: 0;" class="btn btn-xs btn-success" type="button" ng-if="isNumeric(cliente.id) && esconder_cliente">{{ cliente.nome }} <i style="cursor:pointer;" ng-click="removeCliente()" class="fa fa-times fa-lg fa-danger"></i></button></h4></label>
								<div class="input-group">
									<input id="input_auto_complete_cliente" onKeyPress="return SomenteNumeroLetras(event);" ng-focus="outoCompleteCliente(busca.cliente_outo_complete,$event)"  ng-keyUp="outoCompleteCliente(busca.cliente_outo_complete)" type="text" class="form-control" ng-model="busca.cliente_outo_complete"/>
									<div class="content-outo-complete-cliente-pdv" ng-show="clientes_auto_complete.length > 0 && clientes_auto_complete_visible">
										<table class="table table-striped itens-outo-complete">
											<thead>
												<tr>
													<th width="80" >ID</th>
													<th class="text-center">Nome</th>
													<th class="text-center">Apelido</th>
													<th width="140">CPF/CNPJ</th>

													
												</tr>
											</thead>
											<tbody>
												<tr ng-repeat="item in clientes_auto_complete" ng-click="addClienteAutoComplete(item)">
													<td>{{ item.id }}</td>
													<td class="text-center">{{ item.nome    | uppercase }}</td>
													<td class="text-center">{{ item.apelido | uppercase }}</td>
													<td ng-if="item.tipo_cadastro == 'pf'">{{ item.cpf | maskCpf }}</td>
													<td ng-if="item.tipo_cadastro == 'pj'">{{ item.cnpj | maskCnpj }}</td>
												</tr>
											</tbody>
										</table>
									</div>
									<span class="input-group-btn">
										<button ng-click="selCliente(0,10)"  type="button" class="btn btn-info"><i class="fa fa-users"></i></button>
									</span>
								</div>
							</div>
						</div>
						<div class="col-sm-2" id="col-sm-auto-complete-produto">
							<div class="form-group">
								<label class="control-label"><h4>Produto</h4></label>
								<div class="input-group">
									<input id="input_auto_complete_produto" ng-focus="outoCompleteProduto(busca.produto_outo_complete,$event)"  ng-keyUp="outoCompleteProduto(busca.produto_outo_complete)" type="text" class="form-control" ng-model="busca.produto_outo_complete"/>
									<div class="content-outo-complete-produto-pdv" ng-show="produtos_auto_complete.length > 0 && produtos_auto_complete_visible">
										<table class="table table-striped itens-outo-complete">
										<thead>
												<tr>
													<th width="80" >ID</th>
													<th width="80" >Cod. Barra</th>
													<th class="text-center">Nome</th>
													<th class="text-center">Fabricante</th>
													<th class="text-center"width="140">Tamanho</th>
													<th class="text-center"width="140">Cor/Sabor</th>

													
												</tr>
											</thead>
											<tbody>
												<tr ng-repeat="item in produtos_auto_complete" ng-click="addProdutoAutoComplete(item)">
													<td>{{ item.id_produto }}</td>
													<td>{{ item.codigo_barra }}</td>
													<td class="text-center">{{ item.nome_produto    | uppercase }}</td>
													<td class="text-center">{{ item.nome_fabricante | uppercase }}</td>
													<td class="text-center">{{ item.peso | uppercase }}</td>
													<td class="text-center">{{ item.sabor | uppercase }}</td>
												</tr>
											</tbody>
										</table>
									</div>
									<span class="input-group-btn">
										<button ng-click="findProductByBarCode()"  type="button" class="btn btn-primary"><i class="fa fa fa-archive"></i></button>
									</span>
								</div>
							</div>
						</div>	
					</div>
					<div class="row">						
						<div class="col-sm-12">
							<div class="form-group" >
							  	<scrollable-table watch="carrinho">
									<table id="tbl_carrinho" class="table table-condensed table-bordered">
										<tr ng-hide="carrinho.length > 0" class="hidden-print">
											<td colspan="4">
												Carrinho de compras vazio
											</td>
										</tr>
										<thead ng-show="carrinho.length  > 0">
											<tr>
												<th>Produto</th>
												<th ng-show="show_aditional_columns">Fabricante</th>
												<th ng-show="show_aditional_columns">Tamanho</th>
												<th ng-show="show_aditional_columns">Sabor/Cor</th>
												<th class="text-center" style="width: 80px;" >Qtd.</th>
												<th class="text-center" style="width: 100px;" ng-if="show_vlr_real" >RV</th>
												<th class="text-center" style="width: 100px;">Vlr. Unit.</th>
												<th class="text-center" style="width: 230px;" colspan="3">Desconto</th>
												<th class="text-center" style="width: 100px;">Vlr. c/ Desc.</th>
												<th class="text-center" style="width: 100px;">Subtotal</th>
												<th class="text-center" style="width: 20px;" class="hidden-print"></ul>
												</th>
											</tr>
										</thead>
										<tbody>
											<tr ng-repeat="item in carrinho track by $index" id="{{ item.id_produto }}" ng-class="{'error-estoque': verificaOutEstoque(item) }">
												<td>{{ item.nome_produto }} - {{ $index }}</td>
												<td ng-show="show_aditional_columns">{{ item.nome_fabricante }}</td>
												<td ng-show="show_aditional_columns">{{ item.peso }}</td>
												<td ng-show="show_aditional_columns">{{ item.sabor }}</td>
												<td class="text-center" width="20">
													<input onKeyPress="return SomenteNumero(event);" ng-keyUp="calcSubTotal(item)"  ng-model="item.qtd_total" type="text" class="form-control input-xs" width="50" />
												</td>
												<td class="text-center" ng-if="show_vlr_real" > R${{ item.vlr_custo_real | numberFormat : 2 : ',' : '.' }}</td>
												<td class="text-right">R$ {{ item.vlr_real | numberFormat : 2 : ',' : '.' }}</td>
												<td class="text-center" style="width: 30px;">
													<checkbox
														ng-class="{'btn-success':item.flg_desconto == '1','btn-default':item.flg_desconto == '0'}"
													    ng-model="item.flg_desconto"           
													    ng-true-value="1"       
													    ng-false-value="0"    
													    ng-click="aplicarDesconto($index,$event)"                       
													></checkbox>
												</td>
												<td class="text-right" style="width:100px;">
													<span style="display: block;float: left;" ng-if="item.flg_desconto == 1">%</span>
													<input style="width:80%;float:right"  ng-keyUp="aplicarDesconto($index,$event,false,false)" ng-if="item.flg_desconto == 1" thousands-formatter ng-model="item.valor_desconto" type="text" class="form-control input-xs" />
												</td>
												<td class="text-right" style="width:100px;">
													<span style="display: block;float: left;" ng-if="item.flg_desconto == 1">R$</span>
													<input style="width:80%;float:right"  ng-keyUp="aplicarDesconto($index,$event,false,true)" ng-if="item.flg_desconto == 1" thousands-formatter ng-model="item.valor_desconto_real" type="text" class="form-control input-xs" id="teste_teste" />
												</td>
												<td class="text-right">R$ {{ item.vlr_unitario    | numberFormat : 2 : ',' : '.' }}</td>
												<td class="text-right">R$ {{ item.sub_total    | numberFormat : 2 : ',' : '.' }}</td>
												<td class="text-center" class="hidden-print">
													<button type="button" class="btn btn-xs btn-danger"  ng-click="delItem($index)"><i class="fa fa-trash-o"></i></button>
												</td>
											</tr>
										</tbody>
									</table>
								<scrollable-table>
							</div>
						</div>
					</div>
				</div>
			</div><!-- /main-container -->
			<!-- /Modal Produtos-->
		<div class="modal fade" id="list_produtos" style="display:none">
  			<div class="modal-dialog modal-xl">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 ng-if="cdb_busca.status==false">Produtos</span></h4>
						<h4 ng-if="cdb_busca.status==true" style="margin-bottom: 0px;">Produtos</span></h4>
						<span ng-if="cdb_busca.status==true" class="text-muted">Produtos relaionados ao codigo de barra {{ cdb_busca.codigo }}</span>
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
											<th>Qtd.</th>
											<th>Tamanho</th>
											<th>Sabor/Cor</th>
											<th width="80">qtd</th>
											<th width="80"></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-if="produtos == null">
											<th class="text-center" colspan="9" style="text-align:center"><strong>Carregando</strong><img src="assets/imagens/progresso_venda.gif"></th>
										</tr>
										<tr ng-show="(produtos.length == 0)">
											<td colspan="3">Nenhum Cliente encontrado</td>
										</tr>
										<tr ng-repeat="item in produtos">
											<td>{{ item.id_produto }}</td>
											<td>{{ item.nome_produto }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td>{{ item.qtd_real_estoque }}</td>
											<td>{{ item.peso }}</td>
											<td>{{ item.sabor }}</td>
											<td><input onKeyPress="return SomenteNumero(event);" ng-keyUp="" ng-model="item.qtd_total" type="text" class="form-control input-xs" width="50" /></td>
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

	    <!-- Gritter -->
		<script src="js/jquery.gritter.min.js"></script>

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
		<script src="js/endless/endless.js"></script>

		<!-- accounting -->
		<script type="text/javascript" src="js/accounting.min.js"></script>

		<!-- fold-to-ascii -->
		<script type="text/javascript" src="js/fold-to-ascii.js"></script>

		<!-- Extras -->
		<script src="js/extras.js?version=<?php echo date("dmY-His", filemtime("js/extras.js")) ?>"></script>

		<!-- UnderscoreJS -->
		<script type="text/javascript" src="bower_components/underscore/underscore.js"></script>

		<!-- Moment -->
		<script src="js/moment/moment.min.js"></script>


		<!-- ScrennFull  -->
		<script type="text/javascript" src="js/screenfull/screenfull.js"></script>

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
	  	<script src="js/angular-scrollable-table.js"></script>
	  	<script src="js/ui.checkbox.js"></script>
	    <script type="text/javascript">
	    /*
	    	$(".datepicker").datepicker();
	        $("#btnDtaCalendar").on("click", function(){$("#data-atendimento").trigger("focus");});
	        $('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});*/
	    	var addParamModule = ['angular.chosen','scrollable-table','ui.checkbox'] ;
	    </script>
	    <script src="js/app.js?version=<?php echo date("dmY-His", filemtime("js/app.js")) ?>"></script>
	    <script src="js/auto-complete/AutoComplete.js?version=<?php echo date("dmY-His", filemtime("js/auto-complete/AutoComplete.js")) ?>"></script>
	    <script src="js/angular-services/user-service.js?version=<?php echo date("dmY-His", filemtime("js/angular-services/user-service.js")) ?>"></script>
		<script src="js/angular-controller/pdv-controller.js?version=<?php echo date("dmY-His", filemtime("js/angular-controller/pdv-controller.js")) ?>"></script>
		<?php include("google_analytics.php"); ?>
	</body>
</html>

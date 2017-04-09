<?php
	include_once "util/login/restrito.php";
	restrito(array(1));
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
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- Font Awesome -->
	<link href="css/font-awesome-4.1.0.min.css" rel="stylesheet">

	<!-- Pace -->
	<link href="css/pace.css" rel="stylesheet">

	<!-- Datepicker -->
	<link href="css/datepicker/bootstrap-datepicker.css" rel="stylesheet"/>

	<!-- Timepicker -->
	<link href="css/bootstrap-timepicker.css" rel="stylesheet"/>

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/custom.css">
  </head>

  <body class="overflow-hidden" ng-controller="RelatorioTotalProdutoEstoque" ng-cloak>
	<!-- Overlay Div -->
	<!-- <div id="overlay" class="transparent"></div>

	<a href="" id="theme-setting-icon" class="hidden-print"><i class="fa fa-cog fa-lg"></i></a>
	<div id="theme-setting" class="hidden-print">
		<div class="title">
			<strong class="no-margin">Skin Color</strong>
		</div>
		<div class="theme-box">
			<a class="theme-color" style="background:#323447" id="default"></a>
			<a class="theme-color" style="background:#efefef" id="skin-1"></a>
			<a class="theme-color" style="background:#a93922" id="skin-2"></a>
			<a class="theme-color" style="background:#3e6b96" id="skin-3"></a>
			<a class="theme-color" style="background:#635247" id="skin-4"></a>
			<a class="theme-color" style="background:#3a3a3a" id="skin-5"></a>
			<a class="theme-color" style="background:#495B6C" id="skin-6"></a>
		</div>
		<div class="title">
			<strong class="no-margin">Sidebar Menu</strong>
		</div>
		<div class="theme-box">
			<label class="label-checkbox">
				<input type="checkbox" checked id="fixedSidebar">
				<span class="custom-checkbox"></span>
				Fixed Sidebar
			</label>
		</div>
	</div> --><!-- /theme-setting -->

	<div id="wrapper" class="bg-white preload">
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
			<div class="padding-md">
				<div class="clearfix">
					<div class="pull-left">
						<span class="img-demo">
							<img src="assets/imagens/logos/{{ userLogged.nme_logo }}">
						</span>

						<div class="pull-left m-left-sm">
							<h3 class="m-bottom-xs m-top-xs">Relatório de Movimentação de Estoque</h3>
							<small><?php echo date("d/m/Y H:i:s"); ?></small>
						</div>
					</div>

					<div class="pull-right text-right">
						<h6>{{ dados_empreendimento.num_cnpj }} - {{ dados_empreendimento.nome_empreendimento }}</h6>
						<h6>{{ dados_empreendimento.nme_logradouro }}, {{ dados_empreendimento.num_logradouro }}</h6>
						<h6>CEP: {{ dados_empreendimento.num_cep }} - {{ dados_empreendimento.nme_cidade }} - {{ dados_empreendimento.uf }}</h6>
						<h6>Telefone: {{ dados_empreendimento.num_telefone }}</h6>
					</div>
				</div>

				<hr>

				<div class="panel panel-default hidden-print" style="margin-top: 15px;">
					<div class="panel-heading"><i class="fa fa-calendar"></i> Filtros</div>

					<div class="panel-body">
						<form role="form">
							<div class="row">
								<div class="col-sm-2" ng-show="false">
									<div class="form-group">
										<label class="control-label">Inicial</label>
										<div class="input-group">
											<input readonly="readonly" style="background:#FFF;cursor:pointer" ng-model="busca.dta_inicial" type="text" date-picker  class="form-control text-center">
											<span class="input-group-addon" id="cld_dtaInicial"><i class="fa fa-calendar"></i></span>
										</div>
									</div>
								</div>

								<div class="col-sm-2" ng-show="false">
									<div class="form-group">
										<label class="control-label">Final</label>
										<div class="input-group">
											<input readonly="readonly" style="background:#FFF;cursor:pointer" ng-model="busca.dta_final" type="text" date-picker type="text"  class="form-control text-center">
											<span class="input-group-addon" id="cld_dtaFinal"><i class="fa fa-calendar"></i></span>
										</div>
									</div>
								</div>

								<div class="col-sm-4">
									<div class="form-group">
										<label class="control-label">Produto</label>
										<div class="input-group">
											<input ng-click="modalProdutos()" type="text" class="form-control" ng-model="busca.nome_produto" readonly="readonly" style="cursor: pointer;">
											<span class="input-group-btn">
												<button ng-click="modalProdutos(0,10)" ng-click="modalProdutos(0,10)" type="button" class="btn"><i class="fa fa-archive"></i></button>
											</span>
										</div>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label class="control-label">Deposito</label>
										<div class="input-group">
											<input ng-click="modalDepositos()" type="text" class="form-control" ng-model="busca.deposito.nme_deposito" readonly="readonly" style="cursor: pointer;">
											<span class="input-group-btn">
												<button ng-click="modalDepositos()" ng-click="modalDepositos()" type="button" class="btn"><i class="fa fa-sitemap"></i></button>
											</span>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>

					<div class="panel-footer clearfix">
						<div class="pull-left" ng-show="(saldo_produto)">
							<h4>
								Saldo do Produto Selecionado:
								<span class="{{ saldo_produto < 0 ? 'text-danger' : (saldo_produto > 0 ? 'text-info' : 'text-primary')}}">{{ saldo_produto }}</span>
							</h4>
						</div>
						<div class="pull-right">
							<button type="button" class="btn btn-sm btn-primary" ng-click="aplicarFiltro()"><i class="fa fa-filter"></i> Aplicar Filtro</button>
							<button type="button" class="btn btn-sm btn-default" ng-click="resetFilter()"><i class="fa fa-times-circle"></i> Limpar Filtro</button>
							<button ng-if="false" class="btn btn-sm btn-success hidden-print"  id="invoicePrint"><i class="fa fa-print"></i> Imprimir</button>
							<button class="btn btn-sm btn-success hidden-print" ng-click="doExportExcel('registro')"><i class="fa fa-file-excel-o"></i> Exportar p/ Excel</button>
						</div>
					</div>
				</div>

				<br>

				<table id="registro" class="table table-bordered table-hover table-striped table-condensed">
					<thead ng-if="lengthObj(movimentacoes) > 0">
						<tr>
							<th class="text-center">Data/Hora</th>
							<th class="text-center">Usuário Responsável</th>
							<th>Descrição do Evento</th>
							<th>Depósito</th>
							<th class="text-center" width="80">Validade</th>
							<th class="text-center" width="80">Saldo Ant.</th>
							<th class="text-center" width="60">Entrada</th>
							<th class="text-center" width="60">Saída</th>
							<th class="text-center" width="60">Saldo</th>
							<th class="text-center" width="60">Total</th>
						</tr>
					</thead>
					<tr ng-if="saldo_anterior && lengthObj(movimentacoes) > 0">
						<td colspan="9" style="border-right: none;">Saldo</td>
						<td class="text-center" style="border-left: none;" >{{ saldo_anterior }}</td>
					</tr>
					<tbody ng-repeat="(dta,mov) in movimentacoes" >
						<tr class="info text-bold">
							<td class="text-center"></td>
							<td colspan="9">{{ dta | dateFormat:'date' }} <span class="badge" style="float: right;">{{ mov.length }} eventos</span></td>
						</tr>

						<tr ng-repeat="item in mov">
							<td class="text-center" width="100">{{ item.dta_movimentacao | dateFormat : 'time-HH:mm' }}</td>
							<td class="text-center">{{ item.nome_responsavel }}</td>
							<td> {{ item.nme_tipo_movimentacao_estoque }}</td>
							<td>{{ item.nme_deposito }}</td>

							<td class="text-center" ng-if="item.dta_validade != '2099-12-31'">{{ item.dta_validade | dateFormat:'date' }}</td>
							<td class="text-center" ng-if="item.dta_validade == '2099-12-31'"></td>

							<td class="text-center">{{ item.old_qtd }}</td>
							<td class="text-center text-success">{{ item.qtd_entrada }}</td>
							<td class="text-center text-danger">{{ item.qtd_saida }}</td>
							<td class="text-center text-info">{{ item.new_qtd }}</td>
							<td class="text-center text-info">{{ item.total }}</td>
						</tr>

						<!-- <tr>
							<td class="text-center" width="100">14:29:40</td>
							<td class="text-center">Filipe M. Coelho</td>
							<td>Incluiu estoque manualmente no cadastro do produto</td>
							<td class="text-center text-success">10</td>
							<td class="text-center text-danger"></td>
							<td class="text-center text-info">10</td>
						</tr>

						<tr>
							<td class="text-center" width="100">15:25:21</td>
							<td class="text-center">Filipe M. Coelho</td>
							<td>Baixa do estoque por motivo de venda</td>
							<td class="text-center text-success"></td>
							<td class="text-center text-danger">5</td>
							<td class="text-center text-info">5</td>
						</tr> -->
					</tbody>
					<tr ng-if="movimentacoes==false && movimentacoes.length != 0">
						<td class="text-center">
							Selecione um produto para a busca
						</td>
					</tr>
					<tr ng-if="movimentacoes.length == 0">
						<td class="text-center">
							Não existe  resultado para a busca
						</td>
					</tr>
				</table>

				<div class="pull-right hidden-print">
					<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.produtos.length > 1">
						<li ng-repeat="item in paginacao.produtos" ng-class="{'active': item.current}">
							<a href="" h ng-click="loadProdutos(item.offset,item.limit)">{{ item.index }}</a>
						</li>
					</ul>
				</div>
			</div><!-- /.padding20 -->
		</div><!-- /main-container -->
	</div><!-- /wrapper -->

	<div class="modal fade" id="modal-aguarde">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Aguarde</h4>
				</div>
				<div class="modal-body">
					<p>Carregando dados do relatório...</p>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<!-- /Modal Produtos-->
	<div class="modal fade" id="list_produtos" style="display: none;">
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
					            <input ng-model="busca.produto_modal" ng-keyup="loadProdutosModal(0,10)" ng-enter="loadProdutosModal(0,10)" type="text" class="form-control input-sm">

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
								<thead ng-show="(produtos_modal.length != 0)">
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
									<tr ng-show="(produtos_modal.length == 0)">
										<td colspan="6" class="text-center">Não a resultados para a busca</td>
									</tr>
									<tr ng-show="(produtos_modal == null)">
										<td colspan="6" class="text-center"><i class='fa fa-refresh fa-spin'></i> Carregando...</td>
									</tr>
									<tr ng-repeat="item in produtos_modal">
										<td>{{ item.id_produto }}</td>
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
				    	<div class="col-md-12">
							<div class="input-group pull-right">
					             <ul class="pagination pagination-xs m-top-none" ng-show="paginacao.produtos_modal.length > 1">
									<li ng-repeat="item in paginacao.produtos_modal" ng-class="{'active': item.current}">
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
					            <input ng-model="busca.depositos"  ng-keyup="loadDepositos(0,10)" ng-enter="loadDepositos(0,10)" type="text" class="form-control input-sm">
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
			   				<div class="alert" id="alert-modal-deposito" style="display:none" ></div>
			   				<table class="table table-bordered table-condensed table-striped table-hover">
								<thead ng-show="(depositos.length != 0)">
									<tr>
										<th class="text-center">#</th>
										<th>Nome</th>
										<th width="50"></th>
									</tr>
								</thead>
								<tbody>
									<tr ng-show="(depositos.itens == null)">
										<td colspan="4" class="text-center"><i class='fa fa-refresh fa-spin'></i> Carregando...</td>
									</tr>
									<tr ng-show="(depositos.itens == 0)">
										<td colspan="4" class="text-center">Nenhum Deposito encontrado</td>
									</tr>
									<tr ng-repeat="item in depositos.itens">
										<td class="text-center">{{ item.id }}</td>
										<td>{{ item.nme_deposito }}</td>
										<td align="center">
											<button  type="button" class="btn btn-xs btn-success" ng-click="addDeposito(item)">
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
				    		<ul class="pagination pagination-xs m-top-none pull-right" ng-show="depositos.paginacao.length > 1">
								<li ng-repeat="item in depositos.paginacao" ng-class="{'active': item.current}">
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

	<!-- Modernizr -->
	<script src='js/modernizr.min.js'></script>

	<!-- Datepicker -->
	<script src='js/datepicker/bootstrap-datepicker.js'></script>
	<script src='js/datepicker/bootstrap-datepicker.pt-BR.js'></script>

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

	<!-- UnderscoreJS -->
	<script type="text/javascript" src="bower_components/underscore/underscore.js"></script>

	<!-- Extras -->
	<script src="js/extras.js"></script>

	<!-- Moment -->
	<script src="js/moment/moment.min.js"></script>

	<!-- AngularJS -->
	<script src="js/tableExport/jquery.base64.js" type="text/javascript"></script>  
	<script src="js/tableExport/tableExport.js" type="text/javascript"></script>
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
	<script src="js/angular-controller/rel-movimentacao-estoque-controller.js?version=<?php echo date("dmY-His", filemtime("js/angular-controller/rel-movimentacao-estoque-controller.js")) ?>"></script>

	<script type="text/javascript">
		function printDiv(id, pg) {
			var contentToPrint, printWindow;

			contentToPrint = window.document.getElementById(id).innerHTML;
			printWindow = window.open(pg);

		    printWindow.document.write("<link href='bootstrap/css/bootstrap.min.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/font-awesome.min.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/pace.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/endless.min.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/endless-skin.css' rel='stylesheet'>");

			printWindow.document.write("<style type='text/css' media='print'>@page { size: portrait; } th, td { font-size: 8pt; }</style><style type='text/css'>#invoicePrint{ display:none }</style>");

			printWindow.document.write(contentToPrint);

			printWindow.window.print();
			printWindow.document.close();
			printWindow.focus();
		}

		$(function()	{
			$('#invoicePrint').click(function()	{
				printDiv("main-container", "");
			});
		});
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

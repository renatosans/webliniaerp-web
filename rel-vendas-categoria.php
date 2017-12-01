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

	<!-- Tags Input -->
	<link href="css/ng-tags-input.min.css" rel="stylesheet"/>
	<link href="css/ng-tags-input.bootstrap.min.css" rel="stylesheet"/>

	<!-- Bower Components -->	
	<link href="bower_components/noty/lib/noty.css" rel="stylesheet">	

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/custom.css">
	<style type="text/css">
		/* Redimencionando PopOver */
		.popover {
		    display: block !important;
		    max-width: 400px !important;
		    width: 400px !important;
		    width: auto;
		}
	</style>
  </head>
  <body ng-click="limparPopOver($event)" class="overflow-hidden" ng-controller="RelatorioVendasCategoriaCtrl" ng-cloak>
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
							<h3 class="m-bottom-xs m-top-xs">Relatório Vendas por Categorias</h3>
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
						<div class="alert-sistema alert errorBusca" style="display:none"></div>
						<form role="form">
							<div class="row">
								<div class="col-sm-2">
									<div class="form-group">
										<label class="control-label">Inicial</label>
										<div class="input-group">
											<input stardate="" date-picker ng-model="busca.dtaInicial" readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="dtaInicial" class="form-control text-center">
											<span class="input-group-addon" id="cld_dtaInicial"><i class="fa fa-calendar"></i></span>
										</div>
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="control-label">Final</label>
										<div class="input-group">
											<input ng-model="busca.dtaFinal" date-picker readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="dtaFinal" class="form-control text-center">
											<span class="input-group-addon" id="cld_dtaFinal"><i class="fa fa-calendar"></i></span>
										</div>
									</div>
								</div>

								<div class="col-sm-8">
									<div class="form-group">
										<label class="control-label">Categoria</label>
										<tags-input
										 ng-model="busca.arrCategorias"
										 ng-focus="showCategorias()";
										 key-press-false-tags-input-categorias
										 placeholder="Add Categoria" >
										</tags-input>
									</div>
								</div>
							</div>
						</form>
					</div>

					<div class="panel-footer clearfix">
						<div class="pull-right">
							<button type="button" class="btn btn-sm btn-primary" ng-click="aplicarFiltro()"><i class="fa fa-filter"></i> Aplicar Filtro</button>
							<button ng-if="false" type="button" class="btn btn-sm btn-default" ng-click="resetFilter()"><i class="fa fa-times-circle"></i> Limpar Filtro</button>
							<button class="btn btn-sm btn-success hidden-print" ng-show="items.length > 0" id="invoicePrint"><i class="fa fa-print"></i> Imprimir</button>
							<button class="btn btn-sm btn-success hidden-print" ng-click="doExportExcel('data')" ng-if="items.length > 0"><i class="fa fa-file-excel-o"></i> Exportar p/ Excel</button>
						</div>
					</div>
				</div>

				<br/>

				<table id="data" class="table table-bordered table-hover table-striped table-condensed" ng-if="(items != null)">
					<thead>
						<tr>
							<th>Categoria</th>
							<th>Produto</th>
							<th>Fabricante</th>
							<th>Tamanho</th>
							<th>Sabor/cor</th>
							<th class="text-center">Qtd. Vendida</th>
							<th class="text-right" width="100">Total Vendido</th>
						</tr>
					</thead>
					<tbody>
						<tr ng-if="items.length == 0 && items != null">
							<td class="text-center" colspan="7">
								<i class="fa fa-refresh fa-spin"></i> Aguarde, carregando itens...
							</td>
						</tr>
						<tr ng-repeat="item in items" bs-popover>
							<td>{{ item.nme_categoria }}</td>
							<td>{{ item.nme_produto }}</td>
							<td>{{ item.nome_fabricante }}</td>
							<td>{{ item.peso }}</td>
							<td>{{ item.sabor }}</td>
							<td class="text-center">{{ item.qtd_total_vendida }}</td>
							<td class="text-right">R$ {{ item.vlr_total_vendida | numberFormat : 2 : ',' : '.' }}</td>
						</tr>
					</tbody>
					<tfoot ng-if="items != null" >
						<td colspan="5"><strong>TOTAL</strong></td>
						<td class="text-center">{{ qtd_total_vendida }}</td>
						<td class="text-right">R$ {{ vlr_total_vendida | numberFormat : 2 : ',' : '.' }}</td>
					</tfoot>
				</table>
				<span ng-if="(msg_error)" class="alert alert-{{ (status == 404) ? 'warning' : ((status == 500) ? 'danger' : '') }}">{{ msg_error }}</span>
				</div>
			</div><!-- /.padding20 -->
		</div><!-- /main-container -->
	</div><!-- /wrapper -->

	<div class="modal fade" id="modal-aguarde">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Aguarde!</h4>
				</div>
				<div class="modal-body">
					<p>Carregando dados do relatório...</p>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<div class="modal fade" id="list_categorias" style="display:none">
		<div class="modal-dialog">
			<div class="modal-content">
  				<div class="modal-header">
    				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4><i class="fa fa-tags"></i> Categorias</h4>
  				</div>
			    <div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="input-group">
					            <input ng-model="busca.categorias" id="busca-modal-categorias" ng-enter="loadCategorias(0,10)" type="text" class="form-control input-sm">

					            <div class="input-group-btn">
					            	<button ng-click="loadCategorias(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button"><i class="fa fa-search"></i> Buscar</button>
					            </div> <!-- /input-group-btn -->
					        </div> <!-- /input-group -->
						</div><!-- /.col -->
					</div>

					<br>

					<div class="row">
						<div class="col-md-12">
							<div class="alert alert-produtos" style="display:none"></div>
					   		<table class="table table-bordered table-condensed table-striped table-hover">
								<thead ng-show="(categorias.length != 0)">
									<tr>
										<th>#</th>
										<th>Categoria</th>
										<th width="80"></th>
									</tr>
								</thead>
								<tbody>
									<tr ng-show="(categorias.length == 0)">
										<td colspan="3">Nenhum registro encontrado!</td>
									</tr>
									<tr ng-repeat="item in categorias">
										<td>{{ item.id }}</td>
										<td>{{ item.descricao_categoria }}</td>
										<td>
											<button ng-if="!categoriaIsSelected(item.id)" ng-click="selectCategoria(item)" class="btn btn-success btn-xs" type="button">
												<i class="fa fa-check-square-o"></i> Selecionar
											</button>
											<button ng-if="categoriaIsSelected(item.id)"  class="btn btn-primary btn-xs" type="button">
												<i class="fa fa-check-square-o"></i> Selecionado
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
					             <ul class="pagination pagination-xs m-top-none" ng-show="paginacao.categorias.length > 1">
									<li ng-repeat="item in paginacao.categorias" ng-class="{'active': item.current}">
										<a href="" ng-click="loadCategorias(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
					        </div> <!-- /input-group -->
						</div><!-- /.col -->
					</div>
				</div>
		  	</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div>

	<a href="" id="scroll-to-top" class="hidden-print"><i class="fa fa-chevron-up"></i></a>

	<!-- Logout confirmation -->
	<?php include("logoutConfirm.php"); ?>
</body>



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
	<script src='js/datepicker/bootstrap-datepicker.js'></script>
	<script src='js/datepicker/bootstrap-datepicker.pt-BR.js'></script>

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

	<!-- AngularJS -->
	<script src="js/tableExport/jquery.base64.js" type="text/javascript"></script>  
	<script src="js/tableExport/tableExport.js" type="text/javascript"></script>
	<script type="text/javascript" src="bower_components/angular/angular.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="bower_components/angular-ui-utils/mask.min.js"></script>
    <script src="js/angular-sanitize.min.js"></script>
    <script src='js/js-ui-popover/ui-bootstrap-tpls.min.js'></script>
    <script src="js/dialogs.v2.min.js" type="text/javascript"></script>
    <script src="js/auto-complete/ng-sanitize.js"></script>
    <script src="js/ng-tags-input.min.js"></script>
    <script type="text/javascript">
    	var addParamModule = ['ngTagsInput'] ;
    </script>
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/rel-vendas-categoria_controller.js"></script>


	<script id="printFunctions">
		function printDiv(id, pg) {
			var contentToPrint, printWindow;

			contentToPrint = window.document.getElementById(id).innerHTML;
			printWindow = window.open(pg);

		    printWindow.document.write("<link href='bootstrap/css/bootstrap.min.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/font-awesome.min.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/pace.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/endless.min.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/endless-skin.css' rel='stylesheet'>");

			printWindow.document.write("<style type='text/css' media='print'>@page { size: portrait; } th, td { font-size: 8pt; }</style>");

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
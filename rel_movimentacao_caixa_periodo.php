<?php
	include_once "util/login/restrito.php";
	restrito(array(1,8));
?>
<!DOCTYPE html>
<html lang="en" ng-app="HageERP" >
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

	<!-- Datepicker -->
	<link href="css/datepicker/bootstrap-datepicker.css" rel="stylesheet"/>

	<!-- Pace -->
	<link href="css/pace.css" rel="stylesheet">

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

  <body class="overflow-hidden ng-cloak" ng-controller="relMovimentacaoCaixaPeriodoController">

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
			<div class="padding-md">
				<div class="clearfix">
					<div class="pull-left">
						<span class="img-demo">
							<img src="assets/imagens/logos/{{ userLogged.nme_logo }}">
						</span>

						<div class="pull-left m-left-sm">
							<h3 class="m-bottom-xs m-top-xs">Relatório de Movimentação de Caixa por periodo</h3>
							<span class="text-muted">Detalhes das movimentações</span><br>
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
											<input readonly="readonly" ng-model="busca.dtaInicial" date-picker style="background:#FFF;cursor:pointer" type="text"  class=" form-control">
											<span class="input-group-addon" id="cld_dtaInicial"><i class="fa fa-calendar"></i></span>
										</div>
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="control-label">Final</label>
										<div class="input-group">
											<input readonly="readonly" ng-model="busca.dtaFinal" date-picker style="background:#FFF;cursor:pointer" type="text"  class="form-control">
											<span class="input-group-addon" ><i class="fa fa-calendar"></i></span>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>

					<div class="panel-footer clearfix">
						<div class="pull-right">
							<button type="button" class="btn btn-sm btn-primary" ng-click="loadMovimentacoes()"><i class="fa fa-filter"></i> Aplicar Filtro</button>
							<button type="button" class="btn btn-sm btn-default" ng-click="resetFilter()"><i class="fa fa-times-circle"></i> Limpar Filtro</button>
							<button class="btn btn-sm btn-success hidden-print"  id="invoicePrint" ng-if="movimentacoes.length > 0"><i class="fa fa-print" ></i> Imprimir</button>
							<button class="btn btn-sm btn-success hidden-print" ng-click="doExportExcel('registro')" ng-if="movimentacoes.length > 0"><i class="fa fa-file-excel-o"></i> Exportar p/ Excel</button>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-12">
						<span ng-if="movimentacoes == false " class="alert alert-warning">Selecione um periodo para a busca</span>
						<table id="registro" class="table table-bordered table-condensed table-striped table-hover" ng-if="(movimentacoes != null && movimentacoes != false)">
							<thead>
								<tr>
									<th rowspan="2" style="line-height: 46px;">Data</th>
									<th rowspan="2" class="text-center" style="line-height: 46px;">Operador</th>
									<th rowspan="2" class="text-center" style="line-height: 46px;">Caixa</th>
									<th rowspan="2" class="text-center" style="line-height: 46px;">Cliente</th>
									<th rowspan="2" class="text-center" style="line-height: 46px;">Tipo</th>
									<th rowspan="2" class="text-center" style="line-height: 46px;width: 300px;">Descrição</th>
									<th rowspan="2" class="text-center" style="line-height: 46px;">Valor</th>
									<th rowspan="1" class="text-center" colspan="3" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">Taxa Maquineta</th>
								</tr>
								<tr ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">
									<th class="text-center" rowspan="1">% Perc.</th>
									<th class="text-center" rowspan="1">R$ Desc.</th>
									<th class="text-center" rowspan="1">Valor c/ Desc.</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-if="movimentacoes == null">
									<td class="text-center" colspan="7">
										<i class="fa fa-refresh fa-spin"></i> Aguarde, carregando movimentações...
									</td>
								</tr>
								<tr ng-repeat="item in movimentacoes">
									<td>{{ item.dta_movimentacao | dateFormat:'dateTime' }}</td>
									<td>{{ item.nme_operador | uppercase }}</td>
									<td>{{ item.caixa | uppercase }}</td>
									<td>{{ item.nme_cliente | uppercase }}</td>
									<td class="text-center">{{ item.tipo_movimentacao }}</td>

									<td ng-if="item.id_forma_pagamento_entrada == 6 && item.id_venda != null">
										{{ item.dsc_movimentacao}} #{{ item.id_venda }} <b>( Pago em : C.C - {{ item.parcelas }} X R$ {{ item.vlr_parcela | numberFormat:2:',':'.' }} )</b>
									</td>

									<td ng-if="item.id_forma_pagamento_entrada == 6 && item.id_venda == null">
										{{ item.dsc_movimentacao}}  <b>( Pago em : C.C - {{ item.parcelas }} X R$ {{ item.vlr_parcela | numberFormat:2:',':'.' }} )</b>
									</td>

									<td ng-if="item.tipo_movimentacao == 'Sangria' || item.tipo_movimentacao == 'Reforco' ">
										<a ng-if="item.tipo_movimentacao == 'Sangria'" style="cursor:pointer" init-popover content="
										<div><b>Fornecedor:</b> {{ item.nome_fornecedor }} <br/> <b>Observação:</b> {{ item.obs_pagamento_saida }}<div/>
										" >{{ item.obs_pagamento_saida }} <small>({{ item.conta_saida }} >> {{ item.conta_entrada }})</small></a>

										<a ng-if="item.tipo_movimentacao == 'Reforco'" style="cursor:pointer" init-popover content="
										<div><b>Observação:</b> {{ item.obs_pagamento_entrada }}<div/>
										" >{{ item.obs_pagamento_entrada }} <small>({{ item.conta_saida }} >> {{ item.conta_entrada }})</small></a>
									</td>

									<td ng-if="item.id_tipo_movimentacao == 5">
										{{ item.dsc_movimentacao}} #{{ item.id_venda }}</b>
									</td>

									<td ng-if="item.id_forma_pagamento_entrada != 6 && item.tipo_movimentacao != 'Sangria' && item.tipo_movimentacao != 'Reforco' && item.id_tipo_movimentacao != 5 && item.id_venda != null ">
										{{ item.dsc_movimentacao}} #{{ item.id_venda }} <b>( Pago em : {{ item.forma_pagamento_entrada }} )</b>
									</td>

									<td ng-if="item.id_forma_pagamento_entrada != 6 && item.tipo_movimentacao != 'Sangria' && item.tipo_movimentacao != 'Reforco' && item.id_tipo_movimentacao != 5 && item.id_venda == null ">
										{{ item.dsc_movimentacao}} {{ item.id_venda }} <b>( Pago em : {{ item.forma_pagamento_entrada }} )</b>
									</td>

									<td ng-if="isEntrada(item) && item.id_tipo_movimentacao != 5" style="color: #118A2E;" class="text-right">
										<strong>R$ {{ item.valor_entrada | numberFormat:2:',':'.' }}</strong>
									</td>
									<td ng-if="isSaida(item)&& item.id_tipo_movimentacao != 5" style="color:red;" class="text-right">
										<strong>- R$ {{ item.valor_entrada | numberFormat:2:',':'.' }}</strong>
									</td>
									<td ng-if="item.id_tipo_movimentacao == 5" style="color:rgb(208, 216, 22);" class="text-right">
										<strong>R$ {{ item.para_receber | numberFormat:2:',':'.' }}</strong>
									</td>
									<td class="text-right" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')" >
										 {{ item.taxa_maquineta * 100 | numberFormat:2:',':'.' }}%
									</td>
									<td class="text-right" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">
										 R$ {{ item.vlr_taxa_maquineta | numberFormat:2:',':'.' }}
									</td>
									<td class="text-right" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">
										 R$ {{ item.valor_desconto_maquineta | numberFormat:2:',':'.' }}
									</td>
								</tr>

								<tr ng-if="movimentacoes.length > 0">
									<td colspan="6" class="text-right">Saldo Final</td>
									<td style="color:#000;" class="text-right">
										<strong>R$ {{ totais.total | numberFormat:2:',':'.'}}</strong>
									</td>
									<td ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">

									</td>
									<td  style="color:#000;" class="text-right" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">
										<strong>R$ {{ total_desconto_taxa_maquineta | numberFormat:2:',':'.'}}</strong>
									</td>
									<td colspan="4" style="color:#000;" class="text-right" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">
										<strong>R$ {{ totais.total - total_desconto_taxa_maquineta | numberFormat:2:',':'.'}}</strong>
									</td>
								</tr>
								<tr>
									<td colspan="6" class="text-right">Total de Vendas</td>
									<td class="text-right" style="color:#000;"><strong>R$ {{ (total_vendas | numberFormat : 2 : ',' : '.') }}</strong> </td>
									<td ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')"  ></td>
									<td ng-if="key != 'cartao_debito' && key != 'cartao_credito' && funcioalidadeAuthorized('ver_taxa_maquineta')" class="text-right" style="color:#000;">
										<strong>R$ {{ ((total_desconto_taxa_maquineta_debito+total_desconto_taxa_maquineta_credito) | numberFormat : 2 : ',' : '.') }}</strong>
									</td>
									<td ng-if="key != 'cartao_debito' && key != 'cartao_credito' && funcioalidadeAuthorized('ver_taxa_maquineta')" class="text-right" style="color:#000;">
										<strong>R$ {{ ((total_vendas-(total_desconto_taxa_maquineta_debito+total_desconto_taxa_maquineta_credito)) | numberFormat:2:',':'.') }}</strong>
									</td>
								</tr>
								<tr colspan="6" ng-if="movimentacoes.length > 0">
									<td style="background: #D5D5D5;" colspan="10" class="text-uppercase text-center" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">
										<span style="font-size: 14px;">Total por Forma de Pagamento</span>
									</td>
									<td style="background: #D5D5D5;" colspan="7" class="text-uppercase text-center" ng-if="!funcioalidadeAuthorized('ver_taxa_maquineta')">
										<span style="font-size: 14px;">Total por Forma de Pagamento</span>
									</td>
								</tr>
								<tr ng-repeat="(key, item) in totais.formas_pagamento" ng-if="item.valor > 0">
									<td colspan="6" class="text-right">
										{{ item.dsc == 'Dinheiro' && 'Dinheiro(Pagamentos)' || item.dsc }}
									</td>
									<td class="text-right" style="color:#000;">
										<strong>R$ {{  item.dsc == 'Dinheiro' && (item.valor - total_reforco_caixa | numberFormat:2:',':'.') || (item.valor | numberFormat:2:',':'.')  }}</strong>
									</td>
									<td ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">

									</td>
									<td ng-if="key == 'cartao_debito' && funcioalidadeAuthorized('ver_taxa_maquineta')" style="color:#000;" class="text-right">
										<strong>R$ {{ total_desconto_taxa_maquineta_debito | numberFormat:2:',':'.'}}</strong>
									</td>
									<td ng-if="key == 'cartao_credito' && funcioalidadeAuthorized('ver_taxa_maquineta')" style="color:#000;" class="text-right">
										<strong>R$ {{ total_desconto_taxa_maquineta_credito | numberFormat:2:',':'.'}}</strong>
									</td>
									<td ng-if="key != 'cartao_debito' && key != 'cartao_credito' && funcioalidadeAuthorized('ver_taxa_maquineta')" class="text-right" style="color:#000;">
										<strong>R$ {{ 0 | numberFormat:2:',':'.'}}</strong>
									</td>

									<td ng-if="key == 'cartao_debito' && funcioalidadeAuthorized('ver_taxa_maquineta')" style="color:#000;" class="text-right">
										<strong>R$ {{ item.valor - total_desconto_taxa_maquineta_debito | numberFormat:2:',':'.'}}</strong>
									</td>
									<td ng-if="key == 'cartao_credito' && funcioalidadeAuthorized('ver_taxa_maquineta')" style="color:#000;" class="text-right">
										<strong>R$ {{ item.valor - total_desconto_taxa_maquineta_credito | numberFormat:2:',':'.'}}</strong>
									</td>
									<td ng-if="key != 'cartao_debito' && key != 'cartao_credito' && funcioalidadeAuthorized('ver_taxa_maquineta')" class="text-right" style="color:#000;">
										<strong>R$ {{ item.valor | numberFormat:2:',':'.'}}</strong>
									</td>
								</tr>
								<tr>
									<td colspan="6" class="text-right">Dinheiro(Reforços)</td>
									<td class="text-right" style="color:#000;"><strong>R$ {{ (total_reforco_caixa | numberFormat:2:',':'.') }}</strong> </td>
									<td ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')"  ></td>
									<td ng-if="key != 'cartao_debito' && key != 'cartao_credito' && funcioalidadeAuthorized('ver_taxa_maquineta')" class="text-right" style="color:#000;"><strong>R$ {{ (0 | numberFormat:2:',':'.') }}</strong> </td>
									<td ng-if="key != 'cartao_debito' && key != 'cartao_credito' && funcioalidadeAuthorized('ver_taxa_maquineta')" class="text-right" style="color:#000;"><strong>R$ {{ (0 | numberFormat:2:',':'.') }}</strong> </td>
								</tr>
							</tbody>
						</table>
						<span ng-if="(msg_error)" class="alert alert-{{ (status == 404) ? 'warning' : ((status == 500) ? 'danger' : '') }}">{{ msg_error }}</span>
					</div>
				</div>


			</div>
		</div>
		<!-- /main-container -->

		<div class="modal fade" id="modal-aguarde" style="display:none">
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

	<!-- Modernizr -->
	<script src='js/modernizr.min.js'></script>

    <!-- Pace -->
	<script src='js/pace.min.js'></script>

	<!-- Popup Overlay -->
	<script src='js/jquery.popupoverlay.min.js'></script>

	<!-- Datepicker -->
	<script src='js/datepicker/bootstrap-datepicker.js'></script>
	<script src='js/datepicker/bootstrap-datepicker.pt-BR.js'></script>

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
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/rel_movimentacao_caixa_periodo-controller.js"></script>
	<script type="text/javascript"></script>

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

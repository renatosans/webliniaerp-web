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
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

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
	<link rel="stylesheet" type="text/css" href="css/custom.css">
  </head>
  <body ng-click="limparPopOver($event)" class="overflow-hidden" ng-controller="RelatorioRomaneioPedidoPersonalizadoController" ng-cloak>
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
					<a class="btn btn-sm pull-right logoutConfirm_open"  href="#logoutConfirm">
						<i class="fa fa-power-off"></i>
					</a>
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
							<h3 class="m-bottom-xs m-top-xs">Controle Interno de Produção</h3>
							<span class="text-muted">Romaneio de Pedido Personalizado</span>
						</div>
					</div>

					<div class="pull-right text-right">
						<h5><strong>Pedido Nº <?php echo rand(); ?></strong></h5>
						<strong data-toggle="tooltip" data-placement="left" title="Aqui vai a data do pedido"><?php echo date("d/m/Y H:i:s"); ?></strong>
					</div>
				</div>

				<hr>

				<div class="panel panel-default hidden-print" style="margin-top: 15px;">
					<div class="panel-heading clearfix">
						<div class="pull-right">
							<button class="btn btn-sm btn-success hidden-print" id="invoicePrint"><i class="fa fa-print"></i> Imprimir</button>
						</div>
					</div>
				</div>

				<div class="panel panel-default" style="margin-top: 15px;">
					<div class="panel-body">
						<fieldset>
							<legend>Dados do Pedido</legend>

							<span class="text-bold"><strong>Nome:</strong></span> <span>{{ pedido.cliente.nome }}</span>
						
							<div class="clearfix"></div>
							
							<span class="text-bold"><strong>Data da Entrega:</strong></span> <span>{{ pedido.dta_entrega }}</span>

							<span class="text-bold"><strong></strong>|</span>

							<span class="text-bold"><strong>Qtd. Total:</strong></span> <span>{{ pedido.qtd_total }}</span>
							
							<div class="clearfix"></div>
							
							<span class="text-bold"><strong>Cor da Base:</strong></span> <span>{{ pedido.cor_base }}</span>

							<span class="text-bold"><strong></strong>|</span>
							
							<span class="text-bold"><strong>Cor da Tira (Masculina):</strong></span> <span>{{ pedido.cor_tira_masculina }}</span>

							<span class="text-bold"><strong></strong>|</span>

							<span class="text-bold"><strong>Cor da Tira (Ferminina):</strong></span> <span>{{ pedido.cod_tira_feminina }}</span>
							
							<div class="clearfix"></div>
							
							<span class="text-bold"><strong>Observações:</strong></span>

							<div class="clearfix"></div>

							<span class="text-bold"><strong></strong>{{ pedido.dsc_observacoes }}</span>
							<br/>
						</fieldset>

						<fieldset>
							<legend>Itens do Pedido</legend>

							<div class="form form-horizontal">
								<div class="form-group">
									<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Consolidado:</label>
									<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
										<table class="table table-bordered table-condensed table-hover table-striped">
											<thead>
												<th class="text-center">Numeração</th>
												<th class="text-center">Masculino</th>
												<th class="text-center">Feminino</th>
											</thead>
											<tbody>
												<tr>
													<td class="text-center text-bold">23/24</td>
													<td class="text-center">52</td>
													<td class="text-center"></td>
												</tr>
												<tr>
													<td class="text-center text-bold">25/26</td>
													<td class="text-center"></td>
													<td class="text-center">12</td>
												</tr>
												<tr>
													<td class="text-center text-bold">27/28</td>
													<td class="text-center">2</td>
													<td class="text-center"></td>
												</tr>
												<tr>
													<td class="text-center text-bold">29/30</td>
													<td class="text-center"></td>
													<td class="text-center"></td>
												</tr>
												<tr>
													<td class="text-center text-bold">31/32</td>
													<td class="text-center">44</td>
													<td class="text-center">12</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>

								<div class="form-group">
									<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Detalhado:</label>
									<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
										<table class="table table-bordered table-condensed">
											<tbody>
												<tr class="warning">
													<td width="60" class="text-center text-bold">43</td>
													<td class="text-bold">Chinelo Personalizado 33/34 Base Vermelha Tiras Branca</td>
												</tr>
												<tr>
													<td colspan="2">
														<table class="table table-bordered table-hover table-condensed" style="margin-bottom: 0px;">
															<thead>
																<th width="80" class="text-center">Qtd.</th>
																<th>Insumo</th>
															</thead>
															<tbody>
																<tr>
																	<td class="text-center">2</td>
																	<td>Base Lika Shoes 33/34 Vermelha</td>
																</tr>
																<tr>
																	<td class="text-center">2</td>
																	<td>Tiras Finas 33/34 Branca</td>
																</tr>
																<tr>
																	<td class="text-center">1</td>
																	<td>Flor Mod. 1</td>
																</tr>
															</tbody>
														</table>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</div>

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

	<a href="" id="scroll-to-top" class="hidden-print"><i class="fa fa-chevron-up"></i></a>

	<!-- Logout confirmation -->
	<div class="custom-popup width-100" id="logoutConfirm">
		<div class="padding-md">
			<h4 class="m-top-none"> Do you want to logout?</h4>
		</div>

		<div class="text-center">
			<a class="btn btn-success m-right-sm" href="login.html">Logout</a>
			<a class="btn btn-danger logoutConfirm_close">Cancel</a>
		</div>
	</div>

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

	<!-- Extras -->
	<script src="js/extras.js"></script>

	<!-- Mascaras para o formulario de produtos -->
	<script src="js/scripts/mascaras.js"></script>

	<!-- UnderscoreJS -->
	<script type="text/javascript" src="bower_components/underscore/underscore.js"></script>

	<!-- AngularJS -->
	<script type="text/javascript" src="js/js-ui-popover/angular.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="bower_components/angular-ui-utils/mask.min.js"></script>
    <script src="js/angular-sanitize.min.js"></script>
    <script src='js/js-ui-popover/ui-bootstrap-tpls.min.js'></script>
    <script src="js/dialogs.v2.min.js" type="text/javascript"></script>
	<script src="js/auto-complete/ng-sanitize.js"></script>
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/relatorio_romaneio_pedido_personalizado-controller.js"></script>
	<script id="printFunctions">
		function printDiv(id, pg) {
			var contentToPrint,
				printWindow;
			contentToPrint = window.document.getElementById(id).innerHTML;
			printWindow = window.open(pg);
		    printWindow.document.write("<link href='bootstrap/css/bootstrap.min.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/font-awesome.min.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/pace.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/endless.min.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/endless-skin.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/custom.css' rel='stylesheet'>");
			printWindow.document.write("<style type='text/css' media='print'>@page { size: portrait; } th, td { font-size: 7pt; }</style>");
			printWindow.document.write(contentToPrint);
			printWindow.window.print();
			printWindow.document.close();
			printWindow.focus();
		}

		$(function(){
			$('#invoicePrint').click(function()	{
				printDiv("main-container", "");
			});
		});
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>
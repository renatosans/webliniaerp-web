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

<body class="overflow-hidden ng-cloak" ng-controller="relConferenciaCaixa">

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
				</div>

				<?php include_once('menu-modulos.php') ?>

			</div><!-- /sidebar-inner -->
			<!-- /user-block -->

			<!--<div class="search-block">
			<div class="input-group">
			<input type="text" class="form-control input-sm" placeholder="search here...">
			<span class="input-group-btn">
			<button class="btn btn-default btn-sm" type="button"><i class="fa fa-search"></i></button>
			</span>
			</div>--><!-- /input-group -->
			<!--</div>--><!-- /search-block -->
		</aside>

		<div id="main-container">
			<div class="padding-md">
				<div class="clearfix">
					<div class="pull-left"><span class="img-demo"><img src="assets/imagens/logos/{{ userLogged.nme_logo }}"></span>
						<div class="pull-left m-left-sm">
							<h3 class="m-bottom-xs m-top-xs">Conferência de Caixa</h3><small>Teste</small>
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
				<div style="margin-top: 50px;" class="panel panel-default hidden-print">
					<div class="panel-heading"><i class="fa fa-calendar"></i> Entradas</div>
					<div class="panel-body">
						<form role="form">
							<div class="row">
								<div class="col-sm-12 table-responsive">
									<table class="table table-bordered table-condensed table-striped table-hover">
										<thead>
											<tr>
												<th class="text-center">Forma de Pagamento</th>
												<th class="text-center">Valor</th>
											</tr>
										</thead>
										<tbody>
									<!-- tr
									td.text-center(colspan="2")
									i.fa.fa-refresh.fa-spin
									|  asdasd
								-->
									<!-- tr
									td.text-center(colspan="2")
									i.fa.fa-refresh.fa-spin
									|  asdasd
								-->
								<!-- tr(ng-repeat="item in itens")-->
								<tr>
									<td class="text-left">Dinheiro</td>
									<td class="text-right">R$ 310,00</td>
								</tr>
								<tr>
									<td class="text-left">Cartão de Débito</td>
									<td class="text-right">R$ 150,00</td>
								</tr>
								<tr>
									<td class="text-left">Cartão de Crédito</td>
									<td class="text-right">R$ 200,00</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<hr>
				<h4 class="text-bold">Débito</h4>
				<hr>
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<label class="control-label">Maquinetas</label>
							<select class="form-control">
								<option>-- Selecione uma Maquineta --</option>
								<option>Getnet</option>
								<option>Rede</option>
								<option>Cielo</option>
							</select>
							<label class="control-label"><br></label>
							<select class="form-control">
								<option>-- Selecione uma Maquineta --</option>
								<option>Getnet</option>
								<option>Rede</option>
								<option>Cielo</option>
							</select>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label class="control-label">Bandeiras</label>
							<select class="form-control">
								<option>-- Selecione uma Bandeira --</option>
								<option>Visa</option>
								<option>MasterCard</option>
							</select>
							<label class="control-label"><br></label>
							<select class="form-control">
								<option>-- Selecione uma Bandeira --</option>
								<option>Visa</option>
								<option>MasterCard</option>
							</select>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<label class="control-label">Valor:</label>
							<input class="form-control">
							<label class="control-label"></label>
							<input class="form-control">
						</div>
					</div>
					<div class="col-md-offset-1 col-sm-1">
						<buttom class="btn btn-xs btn-primary"><i class="fa fa-plus-circle"></i></buttom>
					</div>
				</div>
				<hr>
				<h4 class="text-bold">Crédito</h4>
				<hr>
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<label class="control-label">Maquinetas</label>
							<select class="form-control">
								<option>-- Selecione uma Maquineta --</option>
								<option>Getnet</option>
								<option>Rede</option>
								<option>Cielo</option>
							</select>
							<label class="control-label"><br></label>
							<select class="form-control">
								<option>-- Selecione uma Maquineta --</option>
								<option>Getnet</option>
								<option>Rede</option>
								<option>Cielo</option>
							</select>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label class="control-label">Bandeiras</label>
							<select class="form-control">
								<option>-- Selecione uma Bandeira --</option>
								<option>Visa</option>
								<option>MasterCard</option>
							</select>
							<label class="control-label"><br></label>
							<select class="form-control">
								<option>-- Selecione uma Bandeira --</option>
								<option>Visa</option>
								<option>MasterCard</option>
							</select>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<label class="control-label">Valor:</label>
							<input class="form-control">
							<label class="control-label"></label>
							<input class="form-control">
						</div>
					</div>
					<div class="col-md-offset-1 col-sm-1">
						<buttom class="btn btn-xs btn-primary"><i class="fa fa-plus-circle"></i></buttom>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
</div>
<!-- /main-container -->

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
	<script src="js/angular-controller/conferencia-caixa-controller.js"></script>
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

<!DOCTYPE html>
<html lang="en" ng-app="HageERP">
  <head>
    <meta charset="utf-8">
    <title>WebliniaERP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Bootstrap core CSS -->
    <link href="<?php echo URL_BASE?>bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- Font Awesome -->
	<link href="<?php echo URL_BASE?>css/font-awesome.min.css" rel="stylesheet">

	<!-- Pace -->
	<link href="<?php echo URL_BASE?>css/pace.css" rel="stylesheet">

	<!-- Endless -->
	<link href="<?php echo URL_BASE?>css/endless.min.css" rel="stylesheet">
	<link href="<?php echo URL_BASE?>css/endless-landing.css" rel="stylesheet">
	<link href="<?php echo URL_BASE?>css/custom.css" rel="stylesheet">
	<style type="text/css">
		tbody.dados tr td {
			line-height: 50px;
		}
	</style>
  </head>

  <body class="overflow-hidden" ng-controller="CarrinhoController" ng-cloak>
	<!-- Overlay Div -->
	<div id="overlay" class="transparent"></div>

	<div id="wrapper" class="preload">
		<header class="navbar navbar-fixed-top navbar-inverse">
			<div class="container">
				<div class="navbar-header">
					<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>

				    <a href="<?php echo URL_BASE.NICKNAME ?>" class="navbar-brand"><img style="max-height: 36px;" src="<?php echo URL_BASE ?>assets/imagens/logos/{{ empreendimento.nme_logo }}"></a>
				</div>

				<nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">
					<ul class="nav navbar-nav navbar-right">
						<li><a href="<?php echo URL_BASE.NICKNAME?>" class="top-link"><i class="fa fa-arrow-left"></i> Continuar Comprando</a></li>
						<li>
							<a href="<?php echo URL_BASE.NICKNAME ?>/carrinho" class="top-link">
								<i class="fa fa-shopping-cart"></i> Meu Carrinho <span class="badge badge-danger"><?php echo count($_SESSION['carrinho']); ?></span>
							</a>
						</li>
						<li><a href="<?php echo URL_BASE?>logout.php" class="top-link"><i class="fa fa-lock"></i> Sair</a></li>
					</ul>
				</nav>
			</div>
		</header>

		<div id="landing-content" style="max-width: 979px; margin: 0 auto; padding-top: 70px; padding-bottom: 10px;">
			<div class="section-header">
				<hr class="left visible-lg">
				<span>Carrinho de Compras</span>
				<hr class="right visible-lg">
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="alert alert-out alert-warning" ng-if="out_produtos.length > 0">
						Desculpe, os produtos marcados em <span style="color:#FF9191">vermelho</span>,
					    não estão mais disponivel em nosso estoque, para continuar a compra basta
					    retira-los do carrinho.
					</div>
					<div class="alert alert-sistema" style="display:none"></div>
					<table class="table table-condensed table-hover table-striped table-bordered">
						<tr>
							<td style="text-align:center" colspan="8" ng-hide="carrinho.length > 0">
								Não há produtos adicionados no carrinho.
							</td>
						</tr>
						<thead>
							<tr ng-show="carrinho.length > 0">
								<th class="text-center" width="70">#</th>
								<th class="text-center" colspan="2">Produto</th>
								<th class="text-center" width="170">Fabricante</th>
								<th class="text-center" width="100">Tamanho</th>
								<th class="text-center" width="100">R$ Unitário</th>
								<th class="text-center">Qtd.</th>
								<th class="text-center" width="100">R$ Subtotal</th>
								<th class="text-center" width="30"></th>
							</tr>
						</thead>
						<tbody class="dados">
							<tr ng-repeat="item in carrinho" id="{{ item.id_produto }}" class="tr_produtos_carrinho">
								<td class="text-center">{{item.id_produto}}</td>
								<td class="text-center" width="60" height="60">
									<img ng-if="(item.img == null || item.img == '')" src="http://via.placeholder.com/730x730" alt="portfolio">
									<img ng-if="!(item.img == null || item.img == '')" src="http://webliniaerp.com.br/assets/imagens/produtos/{{ item.img }}" alt="portfolio">
								</td>
								<td>{{item.nome}}</td>
								<td>{{item.nome_fabricante}}</td>
								<td class="text-center">{{item.peso}}</td>
								<td class="text-right">R$ {{ item.valor_produto | numberFormat:2:',':'.' }}</td>
								<td class="text-center" width="80" style="vertical-align: middle">
									<input ng-model="item.qtd" ng-keyUp="chageQtd(item)" class="form-control ng-pristine text-center">
								</td>
								<td class="text-right">R$ {{ item.qtd == '' && item.valor_produto || item.qtd * item.valor_produto | numberFormat:2:',':'.' }}</td>
								<td class="text-center">
									<button type="button" ng-click="delCarrinho(item.id_produto)" class="btn btn-danger btn-xs">
										<i class="fa fa-trash-o"></i> Remover
									</button>
								</td>
							</tr>
						</tbody>
						<tbody ng-show="carrinho.length > 0">
							<tr>
								<td colspan="6" class="text-right">TOTAL</td>
								<td class="text-center">{{ qtd_total }}</td>
								<td class="text-right">R$ {{ valor_total | numberFormat:2:',':'.' }}</td>
								<td></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<div class="row" ng-show="carrinho.length > 0">
				<div class="col-sm-12">
					<div class="pull-right">
						<button type="button" ng-click="limparCarrinho()" class="btn btn-danger"><i class="fa fa-times-circle"></i> Cancelar</button>
						<button type="button" ng-click="confirmar()"class="btn btn-success" data-loading-text=" Aguarde..." id="btn-confirmar-venda"><i class="fa fa-check-square"></i> Confirmar compra</button>
					</div>
				</div>
			</div>
		</div>

		<footer class=" bg-dark">
			<div class="container">
				<div class="row">
					<div class="col-sm-6 padding-md">
						<p class="font-lg">Sobre a {{ empreendimento.nome_empreendimento }}</p>
						<p>
							<small>
								{{ empreendimento.dsc_empreendimento }}
							</small>
						</p>
					</div>

					<div class="col-sm-3 padding-md">
						<p class="font-lg">Siga-nos</p>
						<a href="{{ empreendimento.url_facebook }}" ng-if="empreendimento.url_facebook != '' && empreendimento.url_facebook != null" target="_blank" class="social-connect tooltip-test facebook-hover" data-toggle="tooltip" data-original-title="Facebook"><i class="fa fa-facebook"></i></a>
						<a href="{{ empreendimento.url_twitter }}"  ng-if="empreendimento.url_facebookurl_twitter != '' && empreendimento.url_twitter != null" target="_blank" class="social-connect tooltip-test twitter-hover" data-toggle="tooltip" data-original-title="Twitter"><i class="fa fa-twitter"></i></a>
						<a href="{{ empreendimento.url_google_plus }}" ng-if="empreendimento.url_google_plus != '' && empreendimento.url_google_plus != null" target="_blank" class="social-connect tooltip-test google-plus-hover" data-toggle="tooltip" data-original-title="Google Plus"><i class="fa fa-google-plus"></i></a>
						<a href="{{ empreendimento.url_linkedin }}"  ng-if="empreendimento.url_linkedin != '' && empreendimento.url_linkedin != null" target="_blank" class="social-connect tooltip-test linkedin-hover" data-toggle="tooltip" data-original-title="Linkedin"><i class="fa fa-linkedin"></i></a>
						<a href="{{ empreendimento.url_pinterest }}" ng-if="empreendimento.url_pinterest != '' && empreendimento.url_pinterest != null" target="_blank" class="social-connect tooltip-test pinterest-hover" data-toggle="tooltip" data-original-title="Pinterest"><i class="fa fa-pinterest"></i></a>
					</div>

					<div class="col-sm-3 padding-md">
						<p class="font-lg">Entre em contato conosco</p>
						<span ng-if="empreendimento.end_email_contato != '' && empreendimento.end_email_contato != null" >E-mail  : {{ empreendimento.end_email_contato }}</span>
						<span ng-if="empreendimento.num_telefone != '' && empreendimento.num_telefone != null">Telefone : {{ empreendimento.num_telefone }}</span>
						<div class="seperator"></div>
						<a class="btn btn-info"><i class="fa fa-envelope"></i> Contate-nos</a>
					</div>

				</div>
				<!-- /.row -->
			</div>
		</footer>
	</div>
	<!-- /wrapper -->

	<a href="<?php echo URL_BASE?>" id="scroll-to-top" class="hidden-print"><i class="fa fa-chevron-up"></i></a>

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->

	<!-- Jquery -->
	<script src="<?php echo URL_BASE ?>js/jquery-1.10.2.min.js"></script>

	<!-- Bootstrap -->
    <script src="<?php echo URL_BASE ?>bootstrap/js/bootstrap.min.js"></script>

	<!-- Waypoint -->
	<script src='<?php echo URL_BASE ?>js/waypoints.min.js'></script>

	<!-- LocalScroll -->
	<script src='<?php echo URL_BASE ?>js/jquery.localscroll.min.js'></script>

	<!-- ScrollTo -->
	<script src='<?php echo URL_BASE ?>js/jquery.scrollTo.min.js'></script>

	<!-- Modernizr -->
	<script src='<?php echo URL_BASE ?>js/modernizr.min.js'></script>

	<!-- Pace -->
	<script src='<?php echo URL_BASE ?>js/pace.min.js'></script>

	<!-- Popup Overlay -->
	<script src='<?php echo URL_BASE ?>js/jquery.popupoverlay.min.js'></script>

	<!-- Slimscroll -->
	<script src='<?php echo URL_BASE ?>js/jquery.slimscroll.min.js'></script>

	<!-- Cookie -->
	<script src='<?php echo URL_BASE ?>js/jquery.cookie.min.js'></script>

	<!-- Endless -->
	<script src="<?php echo URL_BASE ?>js/endless/endless.js"></script>

	<!- COnstants-!>
	<script src="<?php echo URL_BASE ?>js/constants.js"></script>

	<!-- Extras -->
	<script src="<?php echo URL_BASE ?>js/extras.js"></script>

	<!-- AngularJS -->
	<script type="text/javascript" src="<?php echo URL_BASE ?>bower_components/angular/angular.js"></script>
	<script src="js/angular-strap.min.js"></script>
	<script src="js/angular-strap.tpl.min.js"></script>
	<script type="text/javascript" src="<?php echo URL_BASE ?>bower_components/angular-ui-utils/mask.min.js"></script>
    <script src="<?php echo URL_BASE ?>js/angular-sanitize.min.js"></script>
    <script src="<?php echo URL_BASE ?>js/ui-bootstrap-tpls-0.6.0.js" type="text/javascript"></script>
    <script src="<?php echo URL_BASE ?>js/dialogs.v2.min.js" type="text/javascript"></script>
    <script src="<?php echo URL_BASE ?>js/auto-complete/ng-sanitize.js"></script>
    <script src="<?php echo URL_BASE ?>js/app.js"></script>
    <script src="<?php echo URL_BASE ?>js/auto-complete/AutoComplete.js"></script>
    <script src="<?php echo URL_BASE ?>js/angular-services/user-service.js"></script>
	<script src="<?php echo URL_BASE ?>js/angular-controller/carrinho-controller.js"></script>
	<script type="text/javascript">
		//$(".chzn-select").chosen();
		$('.foto-produto').change(function()	{
			var filename = $(this).val().split('\\').pop();
			$(this).parent().find('span').attr('data-title',filename);
			$(this).parent().find('label').attr('data-title','Trocar foto');
			$(this).parent().find('label').addClass('selected');
		});
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

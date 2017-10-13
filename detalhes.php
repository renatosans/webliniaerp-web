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
  </head>

  <body class="overflow-hidden" ng-controller="DetalhesController" ng-cloak>
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
						<li><a href="<?php echo URL_BASE.NICKNAME ?>" class="top-link"><i class="fa fa-arrow-left"></i> Continuar Comprando</a></li>
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

		<div id="landing-content" style="max-width: 800px; margin: 0 auto; padding-top: 70px; padding-bottom: 10px;">
			<div class="row">
				<div class="col-sm-6">
					<div class="detail pull-left relative">
						<img src="<?php echo URL_BASE ?>/assets/imagens/produtos/{{produto.img}}"> <!--750x730-->
						<div class="ribbon-wrapper" style="width: 100px;height: 100px;" ng-if="(produto.qtd_item - produto.qtd_reservada) <= 0">
							<div style="width: 134px;" class="ribbon-inner shadow-pulse bg-danger">
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Esgotado
							</div>
						</div>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="row">
						<div class="col-sm-12">
							<h1>{{ produto.nome_produto }}</h1>
						</div>
					</div>

					<div class="row" ng-show="(produto.nome_fabricante)">
						<div class="col-sm-12">
							<h5>Fabricante: {{ produto.nome_fabricante }}</h5>
						</div>
					</div>

					<div class="row" ng-show="(produto.peso)">
						<div class="col-sm-12">
							<h5>Tamanho: {{ produto.peso }}</h5>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-12">
							<h2 class="text-danger" style="margin-top: 0px;">R$ {{ produto.valor_produto | numberFormat:2:',':'.' }}</h2>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-12">
							<button type="button" ng-if="exists == false && (produto.qtd_item - produto.qtd_reservada) > 0" ng-click="addCarrinho(produto.id_produto)" class="btn btn-lg btn-success btn-add-carrinho"><i class="fa fa-shopping-cart"></i> Adicionar no carrinho</button>
							<button type="button" ng-if="exists == true && (produto.qtd_item - produto.qtd_reservada) > 0"  ng-click="delCarrinho(produto.id_produto)" class="btn btn-lg btn-danger btn-del-carrinho"><i class="fa fa-shopping-cart"></i> Retirar do carrinho</button>
							<button type="button" ng-if="(produto.qtd_item - produto.qtd_reservada) <= 0"  ng-click="semEstoque(produto)" class="btn btn-lg btn-primary btn-del-carrinho"><i class="fa fa-shopping-cart"></i> Solicite já</button>
						</div>
					</div>
				</div>
			</div>

			<div class="row" style="margin-top: 10px;" ng-show="(produto.descricao)">
				<div class="col-sm-12">
					<h5>Descrição do Produto</h5>
					<hr style="margin-top: 0px;">
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12">
					<p ng-show="(produto.descricao)">
						{{ produto.descricao }}
					</p>

					<a ng-show="(produto.nme_arquivo_nutricional)" target="_blanck" style="font-size:12px;color:#0e92c1;text-decoration: underline;" href="<?php echo URL_BASE ?>{{ produto.nme_arquivo_nutricional }}">Veja as informaçãoes nutricionais</a>
				</div>
			</div>
		</div>

		<!-- /Modal desejo-->
		<div class="modal fade" id="modal-desejo">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>{{ desejo.nome_produto }}</h4>
						<p>Informe o tamanho, sabor e a quantidade desejado</p>
      				</div>
				    <div class="modal-body">
				    	<div class="row">
							<div class="col-sm-12">
								<div class="alert alert-desejo" style="display:none" ></div>
							</div>
						</div>
				   		<div class="row">
							<div class="col-sm-4">
								<div class="form-group" id="sabor_desejado">
									<label class="control-label">Sabor/Cor</label>
									<input ng-model="desejo.sabor_desejado" type="text"  class="form-control input-sm">
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group" id="qtd">
									<label class="control-label">Quantidade</label>
									<input ng-model="desejo.qtd" type="text"  class="form-control input-sm" onKeyPress="return SomenteNumero(event);">
								</div>
							</div>
						</div>
						<div class="row">
								<div class="col-sm-12">
									<div class="pull-right">
										<a href="<?php echo URL_BASE ?>/hage/detalhes?produto={{ desejo.id_produto }}" type="submit" class="btn btn-primary btn-sm">
											<i class="fa fa-plus-circle"></i> Detalhes
										</a>
										<button data-loading-text="Aguarde..." ng-click="salvarDesejo()" type="submit" id="btn-salvar-desejo" class="btn btn-success btn-sm">
											<i class="fa fa-save"></i> Incluir na lista de desejos
										</button>
									</div>
								</div>
							</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

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

	<!-- Extras -->
	<script src="<?php echo URL_BASE ?>js/constants.js"></script>
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
	<script src="<?php echo URL_BASE ?>js/angular-controller/detalhes-controller.js"></script>
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

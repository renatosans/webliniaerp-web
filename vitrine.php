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
    <link href="<?php echo URL_BASE ?>bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- Font Awesome -->
	<link href="<?php echo URL_BASE ?>css/font-awesome.min.css" rel="stylesheet">

	<!-- Pace -->
	<link href="<?php echo URL_BASE ?>css/pace.css" rel="stylesheet">

	<!-- Endless -->
	<link href="<?php echo URL_BASE ?>css/endless.min.css" rel="stylesheet">
	<link href="<?php echo URL_BASE ?>css/endless-landing.css" rel="stylesheet">
	<link href="<?php echo URL_BASE ?>css/custom.css" rel="stylesheet">

  </head>

  <body class="overflow-hidden" ng-controller="VitrineController" ng-cloak>

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
						<li>
							<a href="<?php echo URL_BASE.NICKNAME ?>/carrinho" class="top-link">
								<i class="fa fa-shopping-cart"></i> Meu Carrinho <span class="badge badge-danger"><?php echo count($_SESSION['carrinho']); ?></span>
							</a>
						</li>
						<li>
							<a href="<?php echo URL_BASE?>logout.php" class="top-link">
								<i class="fa fa-lock"></i> Sair
							</a>
						</li>
					</ul>
				</nav>
			</div>
		</header>

		<div id="landing-content">
			<div class="bg-white text-center content-padding" style="display: none">
				<div class="container">
					<img src="<?php echo URL_BASE ?>img/topo_loja.jpg" alt="" class="fadeInDownLarge animated-element animation-delay1">
				</div>
			</div>

			<div id="portfolio">
				<div class="section-header">
					<hr class="left visible-lg">
					<span>PORTFOLIO DE PRODUTOS</span>
					<hr class="right visible-lg">
				</div>

				<div class="container">
					<div class="row m-bottom-md">
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label">Nome:</label>
								<input type="text" ng-model="busca.nome" ng-enter="loadGrade(0,12)" class="form-control">
							</div>
						</div>

						<div class="col-sm-3">
							<div class="form-group">
								<label class="control-label">Categoria:</label>
								<select class="form-control" ng-model="busca.id_categoria">
									<option ng-if="busca.id_categoria != null" value=""></option>
									<option  ng-repeat="item in categorias" value="{{ item.id }}">{{ item.descricao_categoria }}</option>
								</select>
							</div>
						</div>

						<div class="col-sm-3">
							<div class="form-group">
								<label class="control-label">Fabricante:</label>
								<select class="form-control" ng-model="busca.id_fabricante">
									<option ng-if="busca.id_fabricante != null" value=""></option>
									<option  ng-repeat="item in fabricantes" value="{{ item.id }}">{{ item.nome_fabricante }}</option>
								</select>
							</div>
						</div>

						<div class="col-sm-2">
							<div class="form-group">
								<div class="controls">
									<label class="control-label">&nbsp;</label>
								</div>
								<button type="button" class="btn btn-primary btn-sm" ng-click="loadGrade(0,12)"><i class="fa fa-search"></i> Pesquisar</button>
								<button type="button" class="btn btn-default btn-sm" ng-click="resetFilter()"><i class="fa fa-close-o"></i> Limpar</button>
							</div>
						</div>
					</div>
					<div ng-show="errorBusca" class="row recent-work" style="text-align: center;min-height: 300px;">
						<h4>Nenhum produto foi encontrado para sua busca.</h4>
					</div>
					<div ng-show="(grade.length == '' && errorBusca == false)" class="row recent-work" style="text-align: center;min-height: 300px;">
						<h4>Buscando ...</h4>
					</div>
					<div class="row recent-work" ng-repeat="(key, value) in grade">
						<div class="col-sm-3" ng-repeat="item in value">
							<div class="detail pull-left relative">
								<a style="cursor:pointer" class="hoverBorder" ng-click="semEstoque(item)">
									<span class="hoverBorderWrapper">
									<img width="263" height="263" ng-if="(item.img == null || item.img == '')" src="http://via.placeholder.com/730x730" alt="portfolio">
									<img width="263" height="263" ng-if="!(item.img == null || item.img == '')" src="http://webliniaerp.com.br/assets/imagens/produtos/{{ item.img }}" alt="portfolio">
										<span class="hoverBorderInner"></span>
										<span ng-if="(item.qtd_item - item.qtd_reservada) > 0"  ng-click="showModalDesejo(item)" class="readMore">+ Detalhes</span>
										<span ng-if="(item.qtd_item - item.qtd_reservada) <= 0" ng-click="showModalDesejo(item)" class="readMore" style="background:#DB5959">solicite já!</span>
									</span>
								</a>
								<div class="seperator"></div>
								<p>
									<a style="cursor:pointer" class="hoverBorder" href="<?php echo URL_BASE.NICKNAME?>/detalhes?produto={{item.id_produto}}" >
										<h4>{{ item.nome_produto }} - {{ item.peso }} - {{ item.sabor }}</h4>
									</a>
									<small>{{ item.descricao }}</small>
									<h4 class="text-danger">R$ {{ item.valor_produto | numberFormat:2:',':'.' }}</h4>
								<p>
								<div class="ribbon-wrapper" style="width: 100px;height: 100px;" ng-if="(item.qtd_item - item.qtd_reservada) <= 0">
									<div style="width: 134px;" class="ribbon-inner shadow-pulse bg-danger">
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Esgotado
									</div>
								</div>
							</div>
						</div>
					</div>


					<div class="row m-bottom-md">
						<div class="col-sm-12">
							<div class="pull-right">
								<ul class="pagination pagination-sm" ng-show="paginacao.grade.length > 1">
									<li ng-repeat="item in paginacao.grade" ng-class="{'active': item.current}">
										<a href="" ng-click="loadGrade(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
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
										<a href="<?php echo URL_BASE ?>hage/detalhes?produto={{ desejo.id_produto }}" type="submit" class="btn btn-primary btn-sm">
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

	<a href="<?php echo URL_BASE ?>" id="scroll-to-top" class="hidden-print"><i class="fa fa-chevron-up"></i></a>


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
	<script src="<?php echo URL_BASE ?>js/scripts/mascaras.js"></script>

	<!-- AngularJS -->
	<script type="text/javascript" src="<?php echo URL_BASE ?>bower_components/angular/angular.js"></script>
	<script src="js/angular-strap.min.js"></script>
	<script src="js/angular-strap.tpl.min.js"></script>
	<script type="text/javascript" src="<?php echo URL_BASE ?>bower_components/angular-ui-utils/mask.min.js"></script>
    <script src="<?php echo URL_BASE ?>js/angular-sanitize.min.js"></script>
    <script src="<?php echo URL_BASE ?>js/ui-bootstrap-tpls-0.6.0.js" type="text/javascript"></script>
    <script src="<?php echo URL_BASE ?>js/dialogs.v2.min.js" type="text/javascript"></script>
    <script src="<?php echo URL_BASE ?>js/auto-complete/ng-sanitize.js"></script>
    <script src="<?php echo URL_BASE ?>js/auto-complete/ng-sanitize.js"></script>
    <script src="<?php echo URL_BASE ?>js/app.js"></script>
    <script src="<?php echo URL_BASE ?>js/auto-complete/AutoComplete.js"></script>
    <script src="<?php echo URL_BASE ?>js/auto-complete/AutoComplete.js"></script>
    <script src="<?php echo URL_BASE ?>js/angular-services/user-service.js"></script>
	<script src="<?php echo URL_BASE ?>js/angular-controller/vitrine-controller.js?<?php echo filemtime('js/angular-controller/vitrine-controller.js') ?>"></script>
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

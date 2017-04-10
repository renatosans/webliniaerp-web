<?php
	session_start();
	include_once "util/constants.php";
?>

<!DOCTYPE html>
<html lang="en" ng-app="HageERP">
  <head>
    <meta charset="utf-8">
    <title>WebliniaERP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">

	<link rel="apple-touch-icon-precomposed" href="img/logo-marca-novo.png"/>
	<link rel="apple-touch-icon" href="img/logo-marca-novo.png"/>
	<meta name="apple-mobile-web-app-capable" content="yes" />

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- Font Awesome -->
	<link href="css/font-awesome.min.css" rel="stylesheet">

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">

	<link href="css/custom.css" rel="stylesheet">
  </head>

  <body class="overflow-hidden" ng-controller="LoginController" ng-cloak>
	<div class="login-wrapper">
		<div class="text-center">
			<h2 class="fadeInUp animation-delay8" style="font-weight:bold">
				<?php if(isset($_SESSION['loja'])): ?>
					<span class="text-danger"><?php echo $_SESSION['loja']['nome_empreendimento'] ?></span>
					<!--<img src="assets/imagens/logos/<?php //echo $_SESSION['loja']['nme_logo'] ?>"/>-->
				<?php else: ?>
				<span class="text-danger">Weblinia</span><span style="color:#ccc; text-shadow:0 1px #fff">ERP</span>
				<?php endif ?>
			</h2>
		</div>
		<div class="login-widget animation-delay1">
			<div class="panel panel-default">
				<div class="panel-heading clearfix">
					<div class="pull-left">
						<i class="fa fa-lock fa-lg"></i> E-mail
					</div>
				<?php if(isset($_SESSION['loja'])): ?>
					<div class="pull-right">
						<span style="font-size:11px;display:none">Não tem cadastro?</span>
						<a class="btn btn-default btn-xs btn-success login-link" href="http://www.webliniaerp.com.br/hage_suplementos/<?php echo $_SESSION['loja']['nickname'] ?>/cadastro" style="margin-top:-2px;"><i class="fa fa-plus-circle"></i> Cadastre-se já</a>
					</div>
				<?php endif ?>
				</div>
				<div class="panel-body">
				<div class="alert" style="display:none"></div>
					<form class="form-login">
						<div class="form-group" id="login">
							<label>E-mail</label>
							<input ng-model="dados.login" type="text" placeholder="E-mail" class="form-control input-sm bounceIn animation-delay2" ng-enter="logar()">
						</div>
						<div class="form-group" id="senha">
							<label>Senha</label>
							<input type="password" ng-model="dados.senha" placeholder="Senha" class="form-control input-sm bounceIn animation-delay4" ng-enter="logar()">
						</div>
						<!--
						<div class="form-group">
							<label class="label-checkbox inline">
								<input type="checkbox" class="regular-checkbox chk-delete" />
								<span class="custom-checkbox info bounceIn animation-delay4"></span>
							</label>
							Remember me
						</div>
						<div class="seperator"></div>
						<div class="form-group">
							Forgot your password?<br/>
							Click <a href="#">here</a> to reset your password
						</div>
						<hr/>
						-->
						<div class="pull-right">
							<a data-loading-text=" Aguarde..." id="btn-logar" class="btn btn-primary btn-sm bounceIn animation-delay5" ng-click="logar()"><i class="fa fa-sign-in"></i> Logar</a>
						</div>
					</form>
				</div>
			</div><!-- /panel -->
		</div><!-- /login-widget -->
	</div><!-- /login-wrapper -->

	<!-- /Modal fornecedor-->
		<div class="modal fade" id="list_emp" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Empreendimentos</h4>
						<p>Selecione o empreendimento que deseja trabalhar</p>
      				</div>
				    <div class="modal-body" style="overflow-y: auto; max-height: 500px;">
				    	<div class="alert alert-warning" id="alert-acesso-negado" style="display:block">
				    		Acesso negado
				    	</div>
				   		<table class="table table-bordered table-condensed table-striped table-hover">
							<thead>
								<tr>
									<th colspan="2">Nome</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="item in empreendimentos">
									<td>{{ item.nome_empreendimento }}</td>
									<td width="80">
										<button id="btnSelEmp-{{item.id}}" ng-click="addEmp(item)" class="btn btn-success btn-xs" type="button" data-loading-text="Aguarde...">
												<i class="fa fa-check-square-o"></i> Selecionar
										</button>
									</td>
								</tr>
							</tbody>
						</table>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
	<!-- /.modal -->

	<!-- /Modal fornecedor-->
		<div class="modal fade" id="modal_debito" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content text-danger">
      				<div class="modal-header" style="color: #fff; background-color: #a94422;">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Empreendimento Bloqueado Temporariamente</h4>
      				</div>
				    <div class="modal-body">
				   		<p>Desculpe, o acesso a este empreendimento está temporariamente suspenso.<br/>
				   		   Para regularizar o acesso, entre em contato com a nossa equipe, através do e-mail: comercial@webliniaerp.com.br</p>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
	<!-- /.modal -->

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

	<!-- Extras -->
	<script src="js/extras.js"></script>

	<!-- AngularJS -->
	<script type="text/javascript" src="js/angular.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/angular-strap/2.1.2/angular-strap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/angular-strap/2.1.2/angular-strap.tpl.min.js"></script>
	<script type="text/javascript" src="bower_components/angular-ui-utils/mask.min.js"></script>
    <script src="js/angular-sanitize.min.js"></script>
    <script src="js/ui-bootstrap-tpls-0.6.0.js" type="text/javascript"></script>
    <script src="js/dialogs.v2.min.js" type="text/javascript"></script>
    <script src="js/auto-complete/ng-sanitize.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/app.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/login-controller.js?<?php echo filemtime('js/angular-controller/login-controller.js')?>"></script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>
<!DOCTYPE html>
<html lang="en" ng-app="HageERP" ng-cloak>
  <head>
    <meta charset="utf-8">
    <title>WebliniaERP Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- Font Awesome -->
	<link href="css/font-awesome-4.1.0.min.css" rel="stylesheet">

	<!-- Pace -->
	<link href="css/pace.css" rel="stylesheet">

	<!-- Gritter -->
	<link href="css/gritter/jquery.gritter.css" rel="stylesheet">

	<!-- Datepicker -->
	<link href="css/datepicker.css" rel="stylesheet"/>

	<!-- Timepicker -->
	<link href="css/bootstrap-timepicker.css" rel="stylesheet"/>

	<!-- Color box -->
	<link href="css/colorbox/colorbox.css" rel="stylesheet">

	<!-- Morris -->
	<link href="css/morris.css" rel="stylesheet"/>

	<!-- Endless -->
	<link href="css/endless.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">

	<link href="css/custom.css" rel="stylesheet">
  </head>

  <body class="overflow-hidden" ng-controller="AcessoNegadoController">

  	<!-- Overlay Div -->
	<div id="overlay" class="transparent"></div>

	<div id="wrapper" class="preload">
		<div class="modal fade" id="modal-fim-teste" style="display:none">
  			<div class="modal-dialog modal-md">
    			<div class="modal-content">
      				<div class="modal-header">
        				<!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
						<!--<h4><span>Itens em debito</span></h4>-->				
      				</div>
				    <div class="modal-body">
				    	<div class="row">
				    		<div class="col-sm-12">
				    			<p style="font-size: 13px;" ng-if="solitacao_enviada == false">
								Você não tem acesso ao modulo solicitado. <br/>
 								</p>
				    		</div>				
				    	</div>
					</div>
					<div class="modal-footer">
				    	<a type="button" class="btn btn-block btn-md btn-success" href="<?php echo $_SESSION['user']['pagina_principal'] ?>">
				    		<i class="fa fa-reply"></i> Sair
				    	</a>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>

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

	<!-- Gritter -->
	<script src="js/jquery.gritter.min.js"></script>

	<!-- Datepicker -->
	<script src='js/bootstrap-datepicker.min.js'></script>

	<!-- Timepicker -->
	<script src='js/bootstrap-timepicker.min.js'></script>

	<!-- Flot -->
	<script src='js/jquery.flot.min.js'></script>

	<!-- Morris -->
	<script src='js/rapheal.min.js'></script>
	<script src='js/morris.min.js'></script>

	<!-- Colorbox -->
	<script src='js/jquery.colorbox.min.js'></script>

	<!-- Sparkline -->
	<script src='js/jquery.sparkline.min.js'></script>

	<!-- Endless -->
	<script src="js/endless/endless_dashboard.js"></script>
	<script src="js/endless/endless.js"></script>

	<!-- Extras -->
	<script src="js/extras.js"></script>

	<!-- AngularJS -->
	<script type="text/javascript" src="js/angular.js"></script>
	<script src="js/angular-strap.min.js"></script>
	<script src="js/angular-strap.tpl.min.js"></script>
	<script type="text/javascript" src="bower_components/angular-ui-utils/mask.min.js"></script>
    <script src="js/angular-sanitize.min.js"></script>
    <script src="js/ui-bootstrap-tpls-0.6.0.js" type="text/javascript"></script>
    <script src="js/dialogs.v2.min.js" type="text/javascript"></script>
    <script src="js/auto-complete/ng-sanitize.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/app.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/acesso_negado-controller.js"></script>
	<script type="text/javascript">
		$('.datepicker').datepicker();
		$('.timepicker').timepicker();

		$("#btnDtaInicial").on("click", function(){ $("#dtaInicial").trigger("focus"); });
		$("#btnDtaFinal").on("click", function(){ $("#dtaFinal").trigger("focus"); });

		$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
		// $(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>
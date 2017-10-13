
<html ng-app="HageERP">
	 <head>
	    <meta charset="utf-8">
	    <title>HageERP</title>
	    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	    <meta name="description" content="">
	    <meta name="author" content="">

	    <!-- Bootstrap core CSS -->
	      <link rel='stylesheet prefetch' href='bootstrap/css/bootstrap.min.css'>

		<!-- Font Awesome -->
		<link href="css/font-awesome-4.1.0.min.css" rel="stylesheet">

		<!-- Pace -->
		<link href="css/pace.css" rel="stylesheet">

		<!-- Endless -->
		<link href="css/endless.min.css" rel="stylesheet">
		<link href="css/endless-skin.css" rel="stylesheet">
		<link href="css/custom.css" rel="stylesheet">
		<link  href="js/auto-complete/AutoComplete.css" rel="stylesheet" type="text/css"></link>
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

			/*@media screen and (min-width: 768px) {

				#list_validades.modal-dialog  {width:900px;}

			}

			#list_validades .modal-dialog  {width:70%;}

			#list_validades .modal-content {min-height: 640px;}*/


		</style>
 	 </head>
	<body ng-controller="autoCompeteExample">

		<div >
			<div nz-auto-complete get-results-fn="searchFunctionStaticData" ng-model="teste" min-char="0" silent-period="0" selection-required="true"></div>
			<div style="float: right"> {{teste}}</div>
		</div>
	</body>

	   <script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
        <script type="text/javascript" src="bower_components/angular/angular.js"></script>

        <script type="text/javascript" src="bower_components/angular/angular.js"></script>
		<script type="text/javascript" src="bower_components/angular-ui-utils/mask.min.js"></script>
	    <script src="js/angular-sanitize.min.js"></script>
	    <script src="js/ui-bootstrap-tpls-0.6.0.js" type="text/javascript"></script>
	    <script src="js/dialogs.v2.min.js" type="text/javascript"></script>
	    <script src="js/auto-complete/ng-sanitize.js"></script>
	     <script src="js/auto-complete/ng-sanitize.js"></script>
	    <script src="js/app.js"></script>
	    <script src="js/auto-complete/AutoComplete.js"></script>
		<script src="js/auto-complete/AutoComplete.js"></script>
		<script src="js/angular-controller/teste.js"></script>
<?php include("google_analytics.php"); ?>
</html>

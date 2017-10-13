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
      <link rel='stylesheet prefetch' href='bootstrap/css/bootstrap.min.css'>

	<!-- Font Awesome -->
	<link href="css/font-awesome-4.1.0.min.css" rel="stylesheet">

	<!-- Pace -->
	<link href="css/pace.css" rel="stylesheet">

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">

	<link href="css/custom.css" rel="stylesheet">

	<!-- Agenda -->
    <link href='js/agenda/fullcalendar.css' rel='stylesheet' />
    <link href='js/agenda/fullcalendar.print.css' rel='stylesheet' media='print' />

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

		/*Agenda*/
		.agenda-event{
			font-weight: bold;
		}
		.fc-day-number{
			cursor: pointer;
		}

		tr.green-td td{
			background-color: rgb(154, 210, 104);
 			color: #FFF;
  		    font-weight: bold;
		}
		tr.red-td td{
			background-color: rgb(247, 71, 71);
 			color: #FFF;
  		    font-weight: bold;
		}

		/*Redimencionando PopOver*/
		.popover{
		    display:block !important;
		    max-width: 400px!important;
		    width: 400px!important;
		    width:auto;
		}



	</style>
  </head>

  <body class="overflow-hidden" ng-controller="AgendaForncedoresController" ng-cloak>
  	<!-- Overlay Div -->
	<div id="overlay" class="transparent"></div>

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
			<div id="breadcrumb">
				<ul class="breadcrumb">
					 <li><i class="fa fa-home"></i> <a href="dashboard.php">Home</a></li>
					 <li class="active"><i class="fa fa-truck"></i> Fornecedores</li>
					 <li class="active"><i class="fa fa-money"></i> Pagamentos</li>
					 <li class="active"><i class="fa fa-calendar"></i> Agenda</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-calendar"></i> Agenda</h3>
					<span class="text-muted">Agenda de pagamentos aos fornecedores</span>
					<br/>
					<a style="cursor:pointer" ng-click="modalConfig()" class="btn btn-sm btn-success"><i class="fa fa-cog"></i> Configuração</a>
					<a style="cursor:pointer" ng-click="modalSimulador()" class="btn btn-sm btn-info"><i class="fa fa-check-circle-o"></i> Simulador</a>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md">
				<div class="alert alert-sistema" style="display:none"></div>

				<div class="panel panel-default" >
					<div class="panel-heading"><i class="fa fa-calendar"></i> Agenda</div>

					<div class="panel-body">
						
						<div id='calendar'></div>
						
					</div>
				</div><!-- /panel -->
			</div>
		</div><!-- /main-container -->

		<!-- /Modal de Configuração-->
		<div class="modal fade" id="modal-config" style="display:none">
  			<div class="modal-dialog error modal-md">
    			<div class="modal-content">
      				<div class="modal-header">
      					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
						<h4>Configuração de pagamentos a fornecedores</h4>
      				</div>

				    <div class="modal-body">
				    	<div class="row">
							<div class="col-sm-12">
				    			<div class="alert alert-config" style="display:none"></div>
				    		</div>
				    	</div>
				    	<div class="row" ng-if="configPagamentos != null && configPagamentos != false">
							<div class="col-sm-12 config-semana">
								<div class="form-group">
									<div class="form-group">
										<label  class="control-label">Defina os dias da semana :</label>
										<div class="col-lg-10">
											<label ng-repeat="item in configPagamentos.dias_semana " class="label-checkbox inline">
												<input type="checkbox" ng-disabled="configPagamentos.tipo_semanal == 1"  ng-click="clickDiaSemana(item)" ng-model="item.value"  ng-true-value="1" ng-false-value="0">
												<span class="custom-checkbox"></span>
												{{ item.dia }}
											</label>
										</div><!-- /.col -->
									</div><!-- /form-group -->
								</div>
							</div>
						</div>	
						<br/>
						<div class="row" ng-if="configPagamentos != null && configPagamentos != false">
							<div class="col-sm-12">
								<div class="form-group">
									<div class="form-group">
										<label  class="control-label">Permitir excedente ?</label>
										<div class="col-lg-10">
											<label class="label-radio inline">
												<input type="radio" name="radio-excedente"  ng-model="configPagamentos.excedente" value="1">
												<span class="custom-radio"></span>
												Sim
											</label>
											<label class="label-radio inline">
												<input type="radio" name="radio-excedente"  ng-model="configPagamentos.excedente" value="0">
												<span class="custom-radio"></span>
												Não
											</label>
										</div><!-- /.col -->
									</div><!-- /form-group -->
								</div>
							</div>
						</div>	
						<br/>
						<div class="row" ng-if="configPagamentos != null && configPagamentos != false">
							<div class="col-sm-12">
								<div  class="form-group">
									<label class="control-label">Valore máximos a serem pagos:</label>
									<div class="row">
										<div class="col-sm-12">
											<div class="form-group">
												<label class="col-lg-1 control-label">forma:</label>
												<div class="col-lg-5">
													<label class="label-radio inline">
														<input type="radio" name="radio-forma" ng-click="event = $event" ng-change="changeForma('porcentagem')" ng-model="configPagamentos.forma_porcentagem" value="1">
														<span class="custom-radio"></span>
														Porcentagem
													</label>
													<label class="label-radio inline">
														<input type="radio" name="radio-forma" ng-click="event = $event" ng-change="changeForma('valor')" ng-model="configPagamentos.forma_valor" value="1">
														<span class="custom-radio"></span>
														Valor
													</label>
												</div><!-- /.col -->
											</div><!-- /form-group -->
										</div>
									</div>

									<div class="row">
										<div class="col-sm-12">
											<label class="col-lg-1 control-label">Tipo:</label>
											<div class="col-lg-5">
												<label class="label-radio inline">
													<input type="radio" name="radio-tipo" ng-click="event = $event" ng-change="changeTipo('diario')" ng-model="configPagamentos.tipo_diario" value="1">
													<span class="custom-radio"></span>
													Diario
												</label>
												<label class="label-radio inline">
													<input type="radio" name="radio-tipo" ng-click="event = $event" ng-change="changeTipo('semanal')" ng-model="configPagamentos.tipo_semanal" value="1">
													<span class="custom-radio"></span>
													Semanal
												</label>
												<div ng-if="configPagamentos.tipo_diario == '1'">
													<div ng-repeat="item in configPagamentos.dias_semana" class="row row-dia-{{$index}}" >
														<div class="col-sm-12">
															<div class="form-group" style="margin-top: 10px;">
																<label class="col-lg-2 control-label" style="padding-left: 0;">{{item.dia}}:</label>
																<label class="col-lg-1 control-label" style="padding-left: 0;" ng-if="configPagamentos.forma_valor == '1'">R$</label>
																<div class="col-lg-9" style="padding-left:2px;padding-right: 5px">
																  <input  ng-if="configPagamentos.forma_valor == '1'" ng-disabled="item.value == 0" class="form-control input-xs" thousands-formatter ng-model="item.valor">
																  <input  ng-if="configPagamentos.forma_porcentagem == '1'" ng-disabled="item.value == 0" class="form-control input-xs" thousands-formatter ng-model="item.porcentagem">
																</div><!-- /.col -->
																<label class="col-lg-1 control-label" style="padding-left: 0;" ng-if="configPagamentos.forma_porcentagem == '1'">%</label>
															</div>
														</div>
													</div> 
												</div>
												<div ng-if="configPagamentos.tipo_semanal == '1'">
													<div class="row">
														<div class="col-sm-12 valor-semanal">
															<div class="form-group" style="margin-top: 10px;">
																<label class="col-lg-2 control-label" style="padding-left: 0;">valor:</label>
																<label class="col-lg-1 control-label" style="padding-left: 0;" ng-if="configPagamentos.forma_valor == '1'">R$</label>
																<div class="col-lg-9" style="padding-left:2px;padding-right: 5px">
																  <input  ng-if="configPagamentos.forma_valor == '1'" class="form-control input-xs" thousands-formatter ng-model="configPagamentos.valor_semanal">
																  <input  ng-if="configPagamentos.forma_porcentagem == '1'" class="form-control input-xs" thousands-formatter ng-model="configPagamentos.porcentagem_semanal">
																</div><!-- /.col -->
																<label class="col-lg-1 control-label" style="padding-left: 0;" ng-if="configPagamentos.forma_porcentagem == '1'">%</label>
															</div>
														</div>
													</div>
												</div>
											</div><!-- /.col -->
										</div>
									</div>
								</div>
							</div>
						</div>	
						<div class="row" ng-if="configPagamentos == null">
							<div class="col-sm-12">
								<i class="fa fa-refresh fa-spin"></i> Aguarde, carregando configurações ...
							</div>
						</div>	
						<div class="row" ng-if="configPagamentos == false">
							<div class="col-sm-12">
								Agenda sem configurações
							</div>
						</div>	
				    </div>
				      <div class="modal-footer">
				        <button type="button" ng-show="configPagamentos!=false" ng-disabled="configPagamentos == null" data-loading-text="Aguarde..." class="btn btn-block btn-md btn-warning"
				    		id="btn-salvar-config" ng-click="configPagamentos=false">
				    		<i class="fa fa-magic"></i> Limpar
				    	</button>
				    	 <button type="button" ng-show="configPagamentos==false" ng-disabled="configPagamentos == null" data-loading-text="Aguarde..." class="btn btn-block btn-md btn-warning"
				    		id="btn-salvar-config" ng-click="loafConfig()">
				    		<i class="fa fa-edit"></i> Voltar e Configurar
				    	</button>
				    	<button type="button" ng-disabled="configPagamentos == null" data-loading-text="Aguarde..." class="btn btn-block btn-md btn-success"
				    		id="btn-salvar-config" ng-click="SalvarConfigPagamento()">
				    		<i class="fa fa-save"></i> Salvar
				    	</button>
				    </div>
			  	</div>
			  	<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
			<!-- /.modal -->

		<!-- /Modal de Pagamentos-->
		<div class="modal fade" id="modal-simulador" style="display:none">
  			<div class="modal-dialog error modal-lg">
    			<div class="modal-content">
      				<div class="modal-header">
      					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
						<h4>Simular Pagamento</h4>
      				</div>
				    <div class="modal-body">
				    	<div class="row">
							<div class="col-sm-2">
								<div class="form-group" id="valor_pedido">
								    <label >Valor Pedido</label>
								    <input ng-model="simulador.valor_pedido" thousands-formatter type="text" class="form-control input-sm">
								</div><!-- /form-group -->
							</div>
							<div class="col-sm-2">
								<div class="form-group" id="qtd_parcelas">
								    <label >Qtd. Parcelas</label>
								    <input ng-model="simulador.qtd_parcelas" onkeypress="return SomenteNumero(event);" type="text" class="form-control input-sm">
								</div><!-- /form-group -->	
							</div>
							<div class="col-sm-2">
								<div class="form-group" id="intervalo">
								    <label >intervalo</label>
								    <input ng-model="simulador.intervalo" onkeypress="return SomenteNumero(event);" type="text" class="form-control input-sm">
								</div><!-- /form-group -->	
							</div>
							<div class="col-sm-2">
								<div class="form-group" id="dta_faturamento">
								    <label >Dta. Faturamento</label>
								    <input ng-model="simulador.dta_faturamento" ui-mask="99/99/9999" type="text" class="form-control input-sm">
								</div><!-- /form-group -->	
							</div>
							<div class="col-sm-2">
								<div class="form-group" id="dias_primeira_parcela">
								    <label >Dias p/ primeira par.</label>
								    <input ng-model="simulador.dias_primeira_parcela" onkeypress="return SomenteNumero(event);" type="text" class="form-control input-sm">
								</div><!-- /form-group -->	
							</div>
							<div class="col-sm-2">
								<div class="form-group" id="dias_ultima_parcela">
								    <label >Dias p/ ultima par.</label>
								    <input ng-model="simulador.dias_ultima_parcela" onkeypress="return SomenteNumero(event);" type="text" class="form-control input-sm">
								</div><!-- /form-group -->	
							</div>
				    	</div>
				    	<div class="row">
				    		<div class="col-sm-3">
								<label >&nbsp;</label>
								<div class="form-group">
								<button type="submit" ng-click="simularPagamento()" class="btn btn-sm btn-success">Simular</button>	
								</div>
							</div>
				    	</div>
				    	<div class="row" ng-if="simulador_msg != false">
				    		<div class="col-sm-12">
				    			<div class="alert alert-warning">{{ simulador_msg }}</div>
				    		</div>
				    	</div>
				       <div class="row" ng-if="simulador.pagamento_parcelado.length">
				       	<div class="col-sm-12">
					       	<table class="table table-bordered table-condensed">
								<thead >
									<tr>
										<th style="text-align: center;">VALOR PARCELA</th>
										<th style="text-align: center;">DATA PAGAMENTO</th>
										<th style="text-align: center;">INTERVALO</th>
									</tr>
								</thead>
								<tbody>
									<tr ng-repeat="item in simulador.pagamento_parcelado" ng-class="{'green-td':item.limite_data,'red-td':item.limite_data == false}" class="green-td">
										<td style="text-align: right;">R$ {{ item.vlr_parcela | numberFormat:2:',':'.' }}</td>
										<td style="text-align: center;">{{ item.dta_pagamento }}</td>
										<td style="text-align: center;">{{ item.intervalo }}</td>	
									</tr>
								</tbody>
							</table>
				       	</div>
				       </div>
				    </div>
				    <div class="modal-footer">
				    </div>
			  	</div>
			  	<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
			<!-- /.modal -->


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

	<!-- Agenda -->
	<script src='js/agenda/lib/moment.min.js'></script>
	<script src='js/agenda/lib/jquery.min.js'></script>
	<script src='js/agenda/fullcalendar.min.js'></script>
	<script src='js/agenda/lang-all.js'></script> 

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

	<!-- Extras -->
	<script src="js/extras.js"></script>


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
	<script src="js/angular-controller/agendaForncedores-controller.js"></script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

<?php
	/*include_once "util/login/restrito.php";
	restrito(array(1));*/
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
    <link href='bootstrap/css/bootstrap.min.css' rel='stylesheet prefetch'>

	<!-- Font Awesome -->
	<link href="css/font-awesome-4.1.0.min.css" rel="stylesheet">

	<!-- Pace -->
	<link href="css/pace.css" rel="stylesheet">

	<!-- Datepicker -->
	<link href="css/datepicker.css" rel="stylesheet"/>

	<!-- Datepicker -->
	<link href="css/datepicker.css" rel="stylesheet"/>

	<!-- Timepicker -->
	<link href="css/bootstrap-timepicker.css" rel="stylesheet"/>

	<!-- Chosen -->
	<link href="css/chosen/chosen.min.css" rel="stylesheet"/>

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">

	<style type="text/css">
		.panel.panel-default {
		    overflow: visible !important;
		}	
		/* Fix for Bootstrap 3 with Angular UI Bootstrap */

		.has-error-plano{
			border: 1px solid #b94a48;
			background: #E5CDCD;
		}

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

		.datepicker {
  		  z-index: 100000;
		}


	</style>
  </head>

  <body class="overflow-hidden" ng-controller="LancamentosController" ng-cloak>
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

				<?php /*include_once('menu-modulos.php')*/ ?>
				
			</div><!-- /sidebar-inner -->
		</aside>

		<div id="main-container">
			<div id="breadcrumb">
				<ul class="breadcrumb">
					 <li><i class="fa fa-home"></i> <a href="dashboard.php">Home</a></li>
					 <li class="active"><i class="fa fa-columns"></i> Ordem de Serviço</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-columns"></i> Ordem de Serviço</h3>
					<h6>&nbsp;</h6>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md" style="padding-top: 0px !important;">
				<div class="alert alert-sistema" style="display:none"></div>

				<div class="panel panel-default" id="box-novo" style="display:none">
					<div class="panel-heading">
						<i class="fa fa-plus-circle"></i> Nova Ordem de Serviço
						<div class="pull-right">
							<a class="btn btn-xs btn-{{(!editing) ? 'success' : 'danger'}} btn-novo" ng-click="showBoxNovo()">
								<i class="fa {{(!editing) ? 'fa-plus-circle' : 'fa-minus-circle'}}"></i> {{(!editing) ? 'Nova O.S.' : 'Cancelar'}}
							</a>
						</div>
					</div>

					<div class="panel-body">
						<div class="row">
							<div class="col-sm-2" id="id_cliente">
								<div class="form-group">
									<label class="control-label">N° Ordem de Serviço</label>
									<input type="text" class="form-control" readonly="readonly">
								</div>
							</div>

							<div class="col-sm-8" id="id_cliente">
								<div class="form-group">
									<label class="control-label">Cliente</label>
									<div class="input-group">
										<input ng-click="selCliente(0,10)"  type="text" class="form-control" ng-model="cliente.nome" readonly="readonly" style="cursor: pointer;" />
										<span class="input-group-btn">
											<button ng-click="selCliente(0,10)" type="button" class="btn btn-info"><i class="fa fa-users"></i></button>
										</span>
									</div>
								</div>
							</div>

							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label">Situação</label><br/>
									<label class="label-radio">
										<input name="cod_status_servico" ng-model="objectModel.cod_status_servico" value="2" type="radio">
										<span class="custom-radio text-success"></span>
										<span>Entregue/Concluído</span>
									</label>

									<label class="label-radio">
										<input name="cod_status_servico" ng-model="objectModel.cod_status_servico" value="1" type="radio">
										<span class="custom-radio text-warning"></span>
										<span>Em andamento</span>
									</label>

									<label class="label-radio">
										<input name="cod_status_servico" ng-model="objectModel.cod_status_servico" value="0" type="radio">
										<span class="custom-radio text-danger"></span>
										<span>Pendente</span>
									</label>
								</div>
							</div>
						</div>

						<div class="row" ng-if="flgTipoLancamento == 0 && cliente.vlr_saldo_devedor < 0">
							<div class="col-sm-12">
								<span style="font-weight: bold; color: #777">Saldo: </span>
								<span style="font-weight: bold; color: #E62C2C">{{cliente.vlr_saldo_devedor | numberFormat:2:',':'.'}}</span>
							</div>
						</div>

						<div class="row" ng-if="flgTipoLancamento == 0 && cliente.vlr_saldo_devedor >= 0">
							<div class="col-sm-12">
								<span style="font-weight: bold; color: #777">Saldo: </span>
								<span style="font-weight: bold; color: #1A7204">{{cliente.vlr_saldo_devedor | numberFormat:2:',':'.'}}</span>
							</div>
						</div>

						<div class="row">
							<div class="col-sm-6">
								<fieldset>
									<legend>Serviços</legend>
									<table class="table table-bordered table-condensed table-striped table-hover">
										<thead>
											<th class="text-center text-middle" width="70">Código</th>
											<th class="text-middle">Descrição</th>
											<th class="text-center text-middle" width="90">Valor</th>
											<th class="text-center text-middle" width="150">Status</th>
											<th class="text-center text-middle" width="40">
												<button class="btn btn-xs btn-block btn-primary"
													data-toggle="tooltip" title="Adicionar Serviço">
													<i class="fa fa-plus-square"></i>
												</button>
											</th>
										</thead>
										<tbody>
											<tr>
												<td class="text-center text-middle">1234</td>
												<td class="text-middle">Lorem ipsum dolor sit amet, consectetur adipisicing elit</td>
												<td class="text-right text-middle">R$ 130,00</td>
												<td class="text-middle">
													<select chosen option="status_servicos" ng-model="busca.id_plano_conta"
													    ng-options="status.cod_status_servico as status.dsc_status_servico for status in status_servicos">
													</select>
												</td>
												<td class="text-center text-middle">
													<button class="btn btn-xs btn-danger" 
														data-toggle="tooltip" title="Remover Serviço">
														<i class="fa fa-trash-o"></i>
													</button>
												</td>
											</tr>
										</tbody>
										<tfoot>
											<th class="text-right text-middle" colspan="2">Total Serviços</th>
											<th class="text-right text-middle">R$ 130,00</th>
											<th></th>
											<th></th>
										</tfoot>
									</table>
								</fieldset>
							</div>

							<div class="col-sm-6">
								<fieldset>
									<legend>Produtos</legend>
									<table class="table table-bordered table-condensed table-striped table-hover">
										<thead>
											<th class="text-center text-middle" width="70">Código</th>
											<th class="text-middle">Descrição</th>
											<th class="text-center text-middle" width="90">Valor</th>
											<th class="text-center text-middle" width="60">Qtd.</th>
											<th class="text-center text-middle" width="90">Subtotal</th>
											<th class="text-center text-middle" width="40">
												<button class="btn btn-xs btn-block btn-primary"
													data-toggle="tooltip" title="Adicionar Produto">
													<i class="fa fa-plus-square"></i>
												</button>
											</th>
										</thead>
										<tbody>
											<tr>
												<td class="text-center text-middle">1234</td>
												<td class="text-middle">Lorem ipsum dolor</td>
												<td class="text-right text-middle">R$ 59,90</td>
												<td class="text-center text-middle">
													<input type="text" class="form-control input-xs text-center" value="2">
												</td>
												<td class="text-right text-middle">R$ 119,80</td>
												<td class="text-center text-middle">
													<button class="btn btn-xs btn-danger" 
														data-toggle="tooltip" title="Remover Produto">
														<i class="fa fa-trash-o"></i>
													</button>
												</td>
											</tr>
										</tbody>
										<tfoot>
											<th class="text-right text-middle" colspan="3">Total Produtos</th>
											<th class="text-center text-middle">2</th>
											<th class="text-right text-middle">R$ 119,90</th>
											<th></th>
										</tfoot>
									</table>
								</fieldset>
							</div>
						</div>
					</div>

					<div class="panel-footer clearfix">
						<div class="pull-right">
							<button class="btn btn-sm btn-default" ng-click="showBoxNovo(true)">
								<i class="fa fa-times-circle"></i> Cancelar
							</button>
							<button class="btn btn-sm btn-success">
								<i class="fa fa-save"></i> Salvar Ordem de Serviço
							</button>
						</div>
					</div>
				</div>
				<!-- /panel -->

				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-filter"></i> Opções de Filtro
						<div class="pull-right">
							<a class="btn btn-xs btn-{{(!editing) ? 'success' : 'danger'}} btn-novo" ng-click="showBoxNovo()" ng-hide="editing">
								<i class="fa {{(!editing) ? 'fa-plus-circle' : 'fa-minus-circle'}}"></i> {{(!editing) ? 'Nova O.S.' : 'Cancelar'}}
							</a>
						</div>
					</div>

					<div class="panel-body">
						<div class="row">
							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label">De</label>
									<div class="input-group">
										<input readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="dtaInicial" class="datepicker form-control text-center">
										<span class="input-group-addon" id="cld_dtaInicial"><i class="fa fa-calendar"></i></span>
									</div>
								</div>
							</div>

							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label">Até</label>
									<div class="input-group">
										<input readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="dtaFinal" class="datepicker form-control text-center">
										<span class="input-group-addon" id="cld_dtaFinal"><i class="fa fa-calendar"></i></span>
									</div>
								</div>
							</div>

							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label">Cliente</label>
									<input ng-model="busca.nome_clienteORfornecedor" ng-enter="" type="text" class="form-control input-sm ng-pristine ng-valid ng-touched">
								</div>
							</div>

							<div class="col-sm-3">
								<div class="form-group" id="regimeTributario">
									<label class="ccontrol-label">Situação da O.S.</label> 
									<select chosen option="status_servicos" ng-model="busca.id_plano_conta"
									    ng-options="status.cod_status_servico as status.dsc_status_servico for status in status_servicos">
									</select>
								</div>
							</div>

							<div class="col-sm-1">
								<div class="form-group">
									<label class="control-label"><br></label>
									<button type="button" class="btn btn-sm btn-primary" ng-click="load(0,20)"><i class="fa fa-filter"></i> Filtrar</button>
								</div>
							</div>

							<div class="col-sm-1">
								<div class="form-group">
									<label class="control-label"><br></label>
									<button type="button" class="btn btn-sm btn-block btn-default" ng-click="limparBusca()">Limpar</button>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-12">
						<div class="alert alert-delete" style="display:none"></div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-12">
						<table class="table table-bordered table-condensed table-striped table-hover" style="background-color: #FFF;">
							<thead>
								<th class="text-center text-middle" width="170">Ações</th>
								<th class="text-center text-middle">Data da O.S.</th>
								<th class="text-middle">Cliente</th>
								<th class="text-center text-middle" width="130">Total Serviços</th>
								<th class="text-center text-middle" width="130">Total Produto</th>
								<th class="text-center text-middle" width="130">Total Pedido</th>
								<th class="text-center text-middle" width="25">Status</th>
							</thead>
							<tbody>
								<tr>
									<td class="text-middle">
										<button type="button" class="btn btn-xs btn-danger"
											data-toggle="tooltip" title="Excluir O.S.">
											<i class="fa fa-trash-o"></i>
										</button>
										<button type="button" class="btn btn-xs btn-warning"
											data-toggle="tooltip" title="Editar O.S.">
											<i class="fa fa-edit"></i>
										</button>
										<button type="button" class="btn btn-xs btn-default"
											data-toggle="tooltip" title="Visualizar Serviços da O.S.">
											<i class="fa fa-columns"></i>
										</button>
										<button type="button" class="btn btn-xs btn-default"
											data-toggle="tooltip" title="Visualizar Produtos da O.S.">
											<i class="fa fa-archive"></i>
										</button>
										<button type="button" class="btn btn-xs btn-primary"
											data-toggle="tooltip" title="Emitir NFS-e">
											<i class="fa fa-file-text-o"></i>
										</button>
										<button type="button" class="btn btn-xs btn-info"
											data-toggle="tooltip" title="Emitir NF-e">
											<i class="fa fa-file-text-o"></i>
										</button>
									</td>
									<td class="text-center text-middle">
										05/04/1991
									</td>
									<td class="text-middle">
										AES ELETROPAULO
									</td>
									<td class="text-right text-middle">
										R$ 999.999,99
									</td>
									<td class="text-right text-middle">
										R$ 999.999,99
									</td>
									<td class="text-right text-middle">
										R$ 999.999,99
									</td>
									<td class="text-center text-middle">
										<i class="fa fa-circle fa-lg text-warning" 
											data-toggle="tooltip" data-placement="left" title="Em andamento"></i>
									</td>
								</tr>
								<tr>
									<td class="text-middle">
										<button type="button" class="btn btn-xs btn-danger"
											data-toggle="tooltip" title="Excluir O.S.">
											<i class="fa fa-trash-o"></i>
										</button>
										<button type="button" class="btn btn-xs btn-warning"
											data-toggle="tooltip" title="Editar O.S.">
											<i class="fa fa-edit"></i>
										</button>
										<button type="button" class="btn btn-xs btn-default"
											data-toggle="tooltip" title="Visualizar Serviços da O.S.">
											<i class="fa fa-columns"></i>
										</button>
										<button type="button" class="btn btn-xs btn-primary"
											data-toggle="tooltip" title="Emitir NFS-e">
											<i class="fa fa-file-text-o"></i>
										</button>
									</td>
									<td class="text-center text-middle">
										05/04/1991
									</td>
									<td class="text-middle">
										AES ELETROPAULO
									</td>
									<td class="text-right text-middle">
										R$ 999.999,99
									</td>
									<td class="text-right text-middle">
										R$ 999.999,99
									</td>
									<td class="text-right text-middle">
										R$ 999.999,99
									</td>
									<td class="text-center text-middle">
										<i class="fa fa-circle fa-lg text-success" 
											data-toggle="tooltip" data-placement="left" title="Entregue/Concluído"></i>
									</td>
								</tr>
								<tr>
									<td class="text-middle">
										<button type="button" class="btn btn-xs btn-danger"
											data-toggle="tooltip" title="Excluir O.S.">
											<i class="fa fa-trash-o"></i>
										</button>
										<button type="button" class="btn btn-xs btn-warning"
											data-toggle="tooltip" title="Editar O.S.">
											<i class="fa fa-edit"></i>
										</button>
										<button type="button" class="btn btn-xs btn-default"
											data-toggle="tooltip" title="Visualizar Serviços da O.S.">
											<i class="fa fa-columns"></i>
										</button>
										<button type="button" class="btn btn-xs btn-default"
											data-toggle="tooltip" title="Visualizar Produtos da O.S.">
											<i class="fa fa-archive"></i>
										</button>
										<button type="button" class="btn btn-xs btn-primary"
											data-toggle="tooltip" title="Emitir NFS-e">
											<i class="fa fa-file-text-o"></i>
										</button>
										<button type="button" class="btn btn-xs btn-info"
											data-toggle="tooltip" title="Emitir NF-e">
											<i class="fa fa-file-text-o"></i>
										</button>
									</td>
									<td class="text-center text-middle">
										05/04/1991
									</td>
									<td class="text-middle">
										AES ELETROPAULO
									</td>
									<td class="text-right text-middle">
										R$ 999.999,99
									</td>
									<td class="text-right text-middle">
										R$ 999.999,99
									</td>
									<td class="text-right text-middle">
										R$ 999.999,99
									</td>
									<td class="text-center text-middle">
										<i class="fa fa-circle fa-lg text-danger" 
											data-toggle="tooltip" data-placement="left" title="Pendente"></i>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div><!-- /main-container -->

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

    <!-- Chosen -->
	<script src='js/chosen.jquery.min.js'></script>

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

	<!-- Datepicker -->
	<script src='js/bootstrap-datepicker.min.js'></script>

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

	<!-- fixedHeadTable -->
	<script type="text/javascript" src="js/fixedHeadTable/fixedHeadTable.js"></script>

	<!-- AngularJS -->
	<script type="text/javascript" src="bower_components/angular/angular.js"></script>
	<script type="text/javascript" src="bower_components/angular-ui-utils/mask.min.js"></script>
    <script src="js/angular-sanitize.min.js"></script>
    <script src="js/ui-bootstrap-tpls-0.6.0.js" type="text/javascript"></script>
    <script src="js/dialogs.v2.min.js" type="text/javascript"></script>
    <script src="js/auto-complete/ng-sanitize.js"></script>
    <script src="js/angular-chosen.js"></script>
    <script type="text/javascript">
    	var addParamModule = ['angular.chosen'] ;
    </script>
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/ordem_servico-controller.js?<?php echo filemtime('js/angular-controller/ordem_servico-controller.js')?>"></script>
	<script src="js/angular-strap.min.js"></script>
	<script src="js/angular-strap.tpl.min.js"></script>
	<script type="text/javascript"></script>

	<script type="text/javascript">
		$(document).ready(function() {
			$("#cld_pagameto").on("click", function(){ $("#pagamentoData").trigger("focus"); });
			$("#cld_dtaInicial").on("click", function(){ $("#dtaInicial").trigger("focus"); });
			$("#cld_dtaFinal").on("click", function(){ $("#dtaFinal").trigger("focus"); });
			$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
			$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});
		});
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

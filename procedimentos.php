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

	<!-- Chosen -->
	<link href="css/chosen/chosen.min.css" rel="stylesheet"/>

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">

	<link rel="stylesheet" type="text/css" href="css/bootstrap-timepicker.css">

	<!-- Datepicker -->
	<link href="css/datepicker.css" rel="stylesheet"/>

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

		.panel.panel-default {
		    overflow: visible !important;
		}

	</style>
  </head>

  <body class="overflow-hidden" ng-controller="ProcedimentosController" ng-cloak>
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
					 <li class="active"><i class="fa fa-tags"></i> Serviços</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-tags"></i> Serviços</h3>
					<br/>
					<a class="btn btn-info" id="btn-novo" ng-click="showBoxNovo()"><i class="fa fa-plus-circle"></i> Novo Serviço</a>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md">
				<div class="alert alert-sistema" style="display:none"></div>

				<div class="panel panel-default" id="box-novo" style="display:none">
					<div class="panel-heading"><i class="fa fa-plus-circle"></i>  Novo Serviço</div>

					<div class="panel-body">
						<div class="alert alert-success-baixa" style="display:none"></div>
						<div class="row">
							<div class="col-sm-2">
								<div class="form-group" id="cod_procedimento">
									<label class="control-label">Código</label>
									<input  ng-model="procedimento.cod_procedimento" type="text"  class="form-control input-sm" />
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group" id="dsc_procedimento">
									<label class="control-label">Descrição</label>
									<input  ng-model="procedimento.dsc_procedimento" type="text"  class="form-control input-sm" />
								</div>
							</div>
							
						</div>
						<div class="row">
							<div class="col-sm-4">
								<div class="form-group" id="id_especialidade">
									<label class="control-label">Categoria/Tipo</label> <i ng-click="modalNovaEspecilidade()" style="cursor:pointer;color: #9ad268;" class="fa fa-plus-circle fa-lg"></i>
									<select chosen 
								    option="especialidades"
								    ng-model="procedimento.id_especialidade"
								    allow-single-deselect="true"
								    ng-options="item.id as item.dsc_especialidade for item in especialidades">
									</select>
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group" id="vlr_procedimento">
									<label class="control-label">Valor</label>
									<input thousands-formatter ng-model="procedimento.vlr_procedimento" type="text"  class="form-control input-sm" />
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group" id="tma_procedimento">
									<label class="control-label">TMA</label>
									<input onkeypress="return SomenteNumero(event);" ng-model="procedimento.tma_procedimento" type="text"  class="form-control input-sm" />
								</div>
							</div>
						</div>
					</div>
					<div class="panel-footer clearfix">
						<div class="pull-right">
							<button ng-click="showBoxNovo(); reset();" type="submit" class="btn btn-danger btn-sm">
								<i class="fa fa-times-circle"></i> Cancelar
							</button>
							<button data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." id="salvar-procedimento" ng-click="salvarProcedimento()" type="submit" class="btn btn-success btn-sm">
								<i class="fa fa-save"></i> Salvar
							</button>
						</div>
					</div>
				</div><!-- /panel -->

				<div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-tasks"></i> Serviços cadastrados</div>

					<div class="panel-body">
						<div class="alert alert-procedimeto" style="display: none">
							
						</div>
						<table class="table table-bordered table-condensed table-striped table-hover">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="text-center">Código</th>
									<th class="text-center">Descrição</th>
									<th class="text-center">Categoria/Tipo</th>
									<th class="text-center">Valor</th>
									<th class="text-center">TMA</th>
									<th class="text-center" width="80">Opções</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-show="procedimentos == null">
                                        <th class="text-center" colspan="7" style="text-align:center"><i class='fa fa-refresh fa-spin'></i> Carregando ...</th>
                                    </tr>
                                    <tr ng-show="procedimentos.length == 0">
                                        <th colspan="7" class="text-center">Nenhum procedimento encontrado</th>
                                    </tr>
								<tr ng-repeat="item in procedimentos">
									<td width="80" class="text-center">{{ item.id }}</td>
									<td class="text-center">{{ item.cod_procedimento }}</td>
									<td>{{ item.dsc_procedimento }}</td>
									<td>{{ item.dsc_especialidade }}</td>
									<td class="text-right">{{ item.vlr_procedimento | numberFormat:2:',':'.' }}</td>
									<td class="text-right">{{ item.tma_procedimento }} <strong>min.</strong></td>
									<td align="center">
										<button type="button" ng-click="editar(item)" tooltip="Editar" class="btn btn-xs btn-warning" data-toggle="tooltip">
											<i class="fa fa-edit"></i>
										</button>
										<button type="button" data-loading-text="<i class='fa fa-refresh fa-spin'></i>" ng-click="delete(item.id)" tooltip="Excluir" class="btn btn-xs btn-danger delete" data-toggle="tooltip">
											<i class="fa fa-trash-o"></i>
										</button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="panel-footer clearfix">
						<div class="pull-right">
							<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.procedimentos.length > 1">
								<li ng-repeat="item in paginacao.procedimentos" ng-class="{'active': item.current}">
									<a href="" ng-click="loadProcedimentos(item.offset,item.limit)">{{ item.index }}</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /main-container -->
		<!-- /Modal nova especialidade-->
		<div class="modal fade" id="modal-nova-especialidade" style="display:none">
  			<div class="modal-dialog modal-md">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Nova Categoria/Tipo</span></h4>
      				</div>
				    <div class="modal-body">
				    	<div class="alert alert-nova-especialidade" style="display: none">
				    		
				    	</div>
						<div class="row">
							<div class="col-sm-12">
								<div class="row">
						    		<div class="col-sm-9" id="dsc_especialidade">
						    			<label class="control-label">Descrição:</label>
						    			<div class="form-group ">
						    					<input ng-model="nova_especialidade.dsc_especialidade" type="text"  class="form-control input-sm" >
						    			</div>
						    		</div>
						    		<div class="col-sm-3" id="hex_cor">
						    			<label class="control-label">Cor (Hexadecimal):</label>
						    			<div class="form-group ">
						    					<input ng-model="nova_especialidade.hex_cor" type="text"  class="form-control input-sm" >
						    			</div>
						    		</div>
						    	</div>
							</div>
						</div>
				    </div>
				    <div class="modal-footer clearfix">
				    	<div class="pull-right">
				    		<button type="button" data-loading-text=" Aguarde..."
					    		class="btn btn-sm btn-default" ng-click="cancelarModal('modal-nova-especialidade')">
					    		<i class="fa fa-times-circle"></i> Cancelar
					    	</button>
					    	<button type="button" data-loading-text=" Aguarde..." class="btn btn-sm btn-success"
					    		id="btn-salvar-especialidade" ng-click="salvarEspecialidade()">
					    		<i class="fa fa-save"></i> Salvar
					    	</button>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
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

	<!-- Bootstrap -->
    <script src="bootstrap/js/bootstrap.min.js"></script>

    <!-- Chosen -->
	<script src='js/chosen.jquery.min.js'></script>

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

	<!-- Datepicker -->
	<script src='js/bootstrap-datepicker.min.js'></script>

	<!-- Moment -->
	<script src="js/moment/moment.min.js"></script>

	<script src="js/jquery.noty.packaged.js"></script>

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
    <script src="js/angular-chosen.js"></script>
    <script type="text/javascript">
    	var addParamModule = ['angular.chosen'] ;
    </script>
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/procedimentos-controller.js"></script>
	<script type="text/javascript">
		$('.datepicker').datepicker();
		$("#cld_dtaInicial").on("click", function(){ $("#dtaInicial").trigger("focus"); });
		$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

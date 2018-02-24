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

		.panel.panel-default {
			overflow: visible !important;
		}
	</style>
  </head>

  <body class="overflow-hidden" ng-controller="FornecedoresController" ng-cloak>
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
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-truck"></i> Fornecedores</h3>
					<br/>
					<a class="btn btn-info" id="btn-novo" ng-disabled="editing" ng-click="showBoxNovo()"><i class="fa fa-plus-circle"></i> Novo Fornecedor</a>
					<a href="pedidos_fornecedores.php" class="btn btn-primary"><i class="fa fa-clipboard"></i> Pedidos</a>
					<a href="agenda.php" class="btn btn-success"><i class="fa fa-calendar"></i> Agenda</a>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md">
				<div class="alert alert-sistema" style="display:none"></div>

				<div class="panel panel-default" id="box-novo" style="display:none" id="id_panel_novo_fornecedor">
					<div class="panel-heading"><i class="fa fa-plus-circle"></i> Novo Fornecedor</div>

					<div class="panel-body">
						<form role="form">
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label for="" class="control-label">Tipo de Cadastro</label>
										<div class="form-group">
											<label class="label-radio inline">
												<input ng-model="fornecedor.tipo_cadastro" value="pf" type="radio" class="inline-radio"/>
												<span class="custom-radio"></span>
												<span>Pessoa Física</span>
											</label>

											<label class="label-radio inline">
												<input ng-model="fornecedor.tipo_cadastro" value="pj" type="radio" class="inline-radio"/>
												<span class="custom-radio"></span>
												<span>Pessoa Jurídica</span>
											</label>
										</div>
									</div>
								</div>
							</div>
							<div class="row" ng-if="fornecedor.tipo_cadastro == 'pf'">
								<div class="col-sm-4">
									<div class="form-group" id="nome_fornecedor">
										<label class="control-label">Nome</label>
										<input type="text" class="form-control input-sm"  ng-model="fornecedor.nome_fornecedor">
									</div>
								</div>

								<div class="col-sm-3">
									<div class="form-group" id="num_cpf">
										<label class="control-label">CPF</label>
										<input type="text" ui-mask="999.999.999-99" class="form-control input-sm"  ng-model="fornecedor.num_cpf">
									</div>
								</div>
							</div>
							<div class="row" ng-if="fornecedor.tipo_cadastro == 'pj'">
								<div class="col-sm-5">
									<div class="form-group" id="nome_fornecedor">
										<label class="control-label">Razão Social</label>
										<input  type="text" class="form-control input-sm"  ng-model="fornecedor.nome_fornecedor">
									</div>
								</div>

								<div class="col-sm-5">
									<div class="form-group">
										<label class="control-label">Nome Fantasia</label>
										<input id="nme_razao_social" type="text" class="form-control input-sm"  ng-model="fornecedor.nme_fantasia">
									</div>
								</div>
							</div>
							<div class="row" ng-if="fornecedor.tipo_cadastro == 'pj'">
								<div class="col-sm-2">
									<div class="form-group">
										<label class="control-label">CNPJ</label>
										<input ui-mask="99.999.999/9999-99" id="num_cnpj" type="text" class="form-control input-sm"  ng-model="fornecedor.num_cnpj">
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="control-label">I.E.</label>
										<input id="num_inscricao_estadual" type="text" class="form-control input-sm"  ng-model="fornecedor.num_inscricao_estadual">
									</div>
								</div>
							</div>						
							<div class="row">
								<div class="col-sm-2">
									<div class="form-group">
										<label class="control-label">CEP</label>
										<input type="text" class="form-control input-sm" ui-mask="99999-999" 
											ng-model="fornecedor.num_cep" 
											ng-keyUp="validCep(fornecedor.num_cep)" ng-blur="validCep(fornecedor.num_cep)">
									</div>
								</div>
								<div class="col-sm-5">
									<div class="form-group">
										<label class="control-label">Endereço</label>
										<input id="nme_endereco" type="text" class="form-control input-sm"  ng-model="fornecedor.nme_endereco">
									</div>
								</div>
								<div class="col-sm-1">
									<div class="form-group">
										<label class="control-label">Número</label>
										<input id="num_logradouro" type="text" class="form-control input-sm"  ng-model="fornecedor.num_logradouro">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label class="control-label">Bairro</label>
										<input id="nme_bairro" type="text" class="form-control input-sm"  ng-model="fornecedor.nme_bairro">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-2">
									<div class="form-group">
										<label class="control-label">Estado</label>
										<select id="cod_estado" chosen ng-change="loadCidadesByEstado()"
										    option="chosen_estado"
										    ng-model="fornecedor.cod_estado"
										    ng-options="estado.id as estado.nome for estado in chosen_estado">
										</select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label class="control-label">Cidade</label>
										<select id="cod_cidade" chosen
										    option="chosen_cidade"
										    ng-model="fornecedor.cod_cidade"
										    ng-options="a.id as a.nome for a in chosen_cidade">
										</select>
									</div>
								</div>
							
								<div class="col-sm-7">
									<div id="complemento" class="form-group">
										<label class="control-label">Complemento:</label>
										<input type="text" class="form-control input-sm" ng-model="fornecedor.end_complemento">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-2">
									<div class="form-group">
										<label class="control-label">Telefone 1</label>
										<input ui-mask="(99) 99999999?9" id="num_cnpj" type="text" class="form-control input-sm"  ng-model="fornecedor.telefones[0].num_telefone">
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="control-label">Telefone 2</label>
										<input ui-mask="(99) 99999999?9" id="num_cnpj" type="text" class="form-control input-sm"  ng-model="fornecedor.telefones[1].num_telefone">
									</div>
								</div>

								<div class="col-sm-6">
									<div id="id_plano_contas_padrao" class="form-group">
										<label class="control-label">Natureza Padrão (Contas a Pagar/Receber)</label>
										<select chosen class="form-control input-sm" 
											ng-model="fornecedor.id_plano_contas_padrao" 
											ng-options="a.id as a.dsc_completa for a in plano_contas">
										</select>
									</div>
								</div>
							</div>	
							<div style="padding: 10px 15px 10px 0px; margin-bottom:10px" class="panel-heading">
								<i class="fa fa-dollar"></i> Dados Bancário
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div id="id_banco" class="form-group">
										<label class="control-label">Banco</label>
										<select chosen class="form-control input-sm" option="bancos" ng-model="fornecedor.id_banco" ng-options="a.id as a.nome for a in bancos"></select>
									</div>
								</div>

								<div class="col-sm-2">
									<div id="agencia" class="form-group">
										<label class="control-label">Agência</label>
										<input type="text" class="form-control input-sm" ng-model="fornecedor.num_agencia">
									</div>
								</div>

								<div class="col-sm-2">
									<div id="conta" class="form-group">
										<label class="control-label">C/C</label>
										<input type="text" class="form-control input-sm" ng-model="fornecedor.num_conta">
									</div>
								</div>
							</div>	
						</form>
					</div>

					<div class="panel-footer clearfix">
						<div class="pull-right">
							<button ng-click="showBoxNovo(); reset();" type="submit" class="btn btn-danger btn-sm">
								<i class="fa fa-times-circle"></i> Cancelar
							</button>
							<button id="btn-salvar-fornecedor" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." ng-click="salvar()" type="submit" class="btn btn-success btn-sm">
								<i class="fa fa-save"></i> Salvar
							</button>
						</div>
					</div>
				</div><!-- /panel -->

				<div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-tasks"></i> Fornecedores Cadastrados</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-11">
								<div class="input-group">
						            <input  type="text" class="form-control input-sm"
						            	ng-model="busca.fornecedor"
						             	ng-enter="load(0,10)" 
						             	ng-keyup="load(0,10)"
						             	>
						            <div class="input-group-btn">
						            	<button ng-click="load(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div>
						        </div>
							</div>
							<div class="col-sm-1">
								<button type="button" class="btn btn-sm btn-default" ng-click="busca.fornecedor='';load(0,10)">Limpar</button>
							</div>
						</div>
						<br>
						<table class="table table-bordered table-condensed table-striped table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Nome/Razão Social</th>
									<th>Nome Fantasia</th>
									<th>CNPJ</th>
									<th>CPF</th>
									<th>IE</th>
									<th width="80" style="text-align: center;">Opções</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-if="fornecedores.fornecedores.length == 0">
									<td width="80" colspan="8" class="text-center"> Nenhum fornecedor encontrado </td>
								</tr>
								<tr ng-if="fornecedores.fornecedores == null" class="text-center">
									<td width="80" colspan="8"><i class='fa fa-refresh fa-spin'></i> Carregando... </td>
								</tr>
								<tr ng-repeat="item in fornecedores.fornecedores" bs-tooltip title="{{ configuracao.id_fornecedor_movimentacao_caixa == item.id && 'Este fornecedor não pode ser editado nem deletado, ele faz parte das configurações internas do sistema' || '' }} ">
									<td width="80" >{{ item.id }}</td>
									<td>{{ item.nome_fornecedor }}</td>
									<td>{{ item.nme_fantasia }}</td>
									<td>{{ item.num_cnpj | cnpjFormat }}</td>
									<td>{{ item.num_cpf | cpfFormat }}</td>
									<td>{{ item.num_inscricao_estadual }}</td>
									<td align="center">
										<button ng-show="item.id != fornecedor.id"  ng-disabled="configuracao.id_fornecedor_movimentacao_caixa == item.id"  type="button" ng-click="editar(item)" tooltip="Editar" title="Editar" class="btn btn-xs btn-warning" data-toggle="tooltip">
											<i class="fa fa-edit"></i>
										</button>
										<button   ng-show="item.id == fornecedor.id"  type="button" tooltip="Em edição" title="Em edição" class="btn btn-xs btn-success" data-toggle="tooltip">
											<i class="fa fa-edit"></i>
										</button>
										<button  ng-disabled=" configuracao.id_fornecedor_movimentacao_caixa == item.id" type="button" ng-click="delete(item)" tooltip="Excluir" title="Excluir" class="btn btn-xs btn-danger delete" data-toggle="tooltip">
											<i class="fa fa-trash-o"></i>
										</button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="panel-footer clearfix">
						<div class="pull-right">
							<ul class="pagination pagination-sm m-top-none" ng-show="fornecedores.paginacao.length > 1">
								<li ng-repeat="item in fornecedores.paginacao" ng-class="{'active': item.current}">
									<a href="" h ng-click="load(item.offset,item.limit)">{{ item.index }}</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /main-container -->

		<!-- /Modal load CEP-->
		<div class="modal fade" id="busca-cep" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
    				<div class="modal-header">
						<h4>Aguarde!</h4>
      				</div>

				    <div class="modal-body">
				   		<strong>Buscando CEP...</strong>
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

	<!-- Chosen -->
	<script src='js/chosen.jquery.min.js'></script>

	<!-- Moment -->
	<script src="js/moment/moment.min.js"></script>

	<script src="js/jquery.noty.packaged.js"></script>

	<!-- Bower Components -->	
	<script src="bower_components/noty/lib/noty.min.js" type="text/javascript"></script>
    <script src="bower_components/mojs/build/mo.min.js" type="text/javascript"></script>

	<!-- Extras -->
	<script src="js/extras.js?version=<?php echo date("dmY-His", filemtime("js/extras.js")) ?>"></script>

	<script src="js/jquery.noty.packaged.js"></script>

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
    <script src="js/app.js?version=<?php echo date("dmY-His", filemtime("js/app.js")) ?>"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js?version=<?php echo date("dmY-His", filemtime("js/angular-services/user-service.js")) ?>"></script>
	<script src="js/angular-controller/fornecedores-controller.js?version=<?php echo date("dmY-His", filemtime("js/angular-controller/fornecedores-controller.js")) ?>"></script>
	<?php include("google_analytics.php"); ?>

  </body>
</html>

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
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

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
	<link rel="stylesheet" type="text/css" href="css/custom.css">

	<style type="text/css">

		/* Fix for Bootstrap 3 with Angular UI Bootstrap */
		.has-error {
			color:#A94442;
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


	</style>
  </head>

  <body class="overflow-hidden" ng-controller="BaseTributariaController" ng-cloak>
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
					<li><i class="fa fa-home"></i><a href="dashboard.php">Home</a></li>
					<li><i class="fa fa-building-o"></i> Empreendimento</li>
					<li><i class="fa fa-cog"></i> <a href="empreendimento_config.php">Configurações</a></li>
					<li class="active"><i class="fa fa-tags"></i> Base Tributária</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-tags"></i> Base Tributária</h3>
					<br/>
					<a class="btn btn-info" id="btn-novo" ng-disabled="editing" ng-click="showBoxNovo()"><i class="fa fa-plus-circle"></i> Nova Base Tributária</a>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md">
				<div class="alert alert-sistema" style="display:none"></div>

				<div class="panel panel-default" id="box-novo" style="display:none">
					<div class="panel-heading"><i class="fa fa-plus-circle"></i> Nova Base Tributária</div>

					<div class="panel-body">
						<div  class="row">
							<div class="col-sm-12">
								<div id="dsc_base_tributaria" class="form-group">
									<label class="control-label">Descrição <span style="color:red;font-weight: bold;">*</span></label>
									<input type="text" class="form-control" ng-model="base_tributaria.dsc_base_tributaria">
								</div>
							</div>
						</div>


						<div class="panel panel-default">
							<div class="panel-heading">Item</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-sm-4">
										<div class="form-group" id="cod_produto">
											<label class="control-label">Produto</label>
											<div class="input-group"  >
												<input readonly="readonly" ng-click="showProdutos()"  ng-model="base_tributaria_item.nome_produto" type="text" class="form-control input-sm" ng-click="showProdutos()" >
												<span class="input-group-btn">
														<button ng-click="showProdutos()" ng-disabled="finalizarOrcamento" type="button" class="btn btn-primary btn-sm"><i class="fa fa fa-archive"></i></button>
												</span>
											</div>
										</div>
									</div>				
									<div class="col-sm-2">
										<div id="vlr_base_calculo_icms" class="form-group">
											<label class="control-label">Base Cálculo ICMS</label>
											<input type="text" class="form-control input-sm" mask-moeda ng-model="base_tributaria_item.vlr_base_calculo_icms">
										</div>
									</div>
									<div class="col-sm-2">
										<div id="vlr_base_calculo_icms_st" class="form-group">
											<label class="control-label">Base Cálculo ICMS ST</label>
											<input type="text" class="form-control input-sm" mask-moeda ng-model="base_tributaria_item.vlr_base_calculo_icms_st">
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group" id="cod_tipo_tributacao_ipi">
											<label class="ccontrol-label">Tributação IPI</label> 
											<select chosen ng-change="ClearChosenSelect('cod_tipo_tributacao_ipi')"
										    option="chosen_tributacao_ipi"
										    ng-model="base_tributaria_item.cod_tipo_tributacao_ipi"
										    ng-options="tipo_tributacao_ipi.cod_controle_item_nfe as tipo_tributacao_ipi.nme_item for tipo_tributacao_ipi in chosen_tributacao_ipi">
											</select>
										</div>
									</div>
									<div class="col-sm-2">
										<div id="vlr_base_calculo_ipi" class="form-group">
											<label class="control-label">Base Cálculo IPI</label>
											<input type="text" class="form-control input-sm" mask-moeda ng-model="base_tributaria_item.vlr_base_calculo_ipi">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-2">
										<div id="vlr_pis" class="form-group">
											<label class="control-label">Vlr. PIS</label>
											<input type="text" class="form-control input-sm" mask-moeda ng-model="base_tributaria_item.vlr_pis">
										</div>
									</div>
									<div class="col-sm-2">
										<div id="vlr_cofins" class="form-group">
											<label class="control-label">Vlr. COFINS</label>
											<input type="text" class="form-control input-sm" mask-moeda ng-model="base_tributaria_item.vlr_cofins">
										</div>
									</div>
									<div class="col-sm-2">
										<div id="vlr_pis_st" class="form-group">
											<label class="control-label">Vlr. PIS ST</label>
											<input type="text" class="form-control input-sm" mask-moeda ng-model="base_tributaria_item.vlr_pis_st">
										</div>
									</div>
									<div class="col-sm-2">
										<div id="vlr_cofins_st" class="form-group">
											<label class="control-label">Vlr. COFINS ST</label>
											<input type="text" class="form-control input-sm" mask-moeda ng-model="base_tributaria_item.vlr_cofins_st">
										</div>
									</div>

								</div>
								<br/>
								<div class="row">
									<div class="col-sm-12">
								    	<button type="button" data-loading-text=" Aguarde..." class="btn btn-sm btn-primary"
								    		id="btn-aplicar-sangria" ng-click="incluirBaseTributaria()">
								    		<i class="" ng-class="{'fa fa-edit':editingProdutoCliente,'fa fa-plus-circle':editingProdutoCliente==false}"></i> {{ editingBaseTributaria && 'Alterar' || 'Incluir' }}
								    	</button>
							    	</div>
						    	</div>
						    	<br/>
								<div class="row">
										<div class="col-sm-12">
											<div class="empreendimentos form-group" id="produto_cliente">
													<table class="table table-bordered table-condensed table-striped table-hover">
														<thead>
															<tr>
																<td>Base Cal. ICMS</td>
																<td>Base Cal. ICMS ST</td>
																<td>Base Cal. IPI</td>
																<td>Vlr.PIS</td>
																<td>Vlr.COFINS</td>
																<td>Vlr. PIS ST</td>
																<td>Vlr. COFINS ST</td>
																<td width="60" align="center">
																	
																</td>
															</tr>
														</thead>
														<tbody>
															<tr ng-show="(base_tributaria.base_tributaria_itens.length == 0 && base_tributaria.base_tributaria_itens != null)">
																<td colspan="8" align="center">Nenhum Relacionado Encontrado</td>
															</tr>
															<tr>
																<td colspan="8" class="text-center" ng-if="base_tributaria.base_tributaria_itens == null">
																	<i class='fa fa-refresh fa-spin'></i> Carregando
																</td>
															</tr>
															<tr ng-repeat="item in base_tributaria.base_tributaria_itens" bs-tooltip >
																<td>{{ item.vlr_base_calculo_icms | numberFormat:2:',':'.' }}</td>
																<td>{{ item.vlr_base_calculo_icms_st | numberFormat:2:',':'.' }}</td>
																<td>{{ item.vlr_base_calculo_ipi | numberFormat:2:',':'.' }}</td>
																<td>{{ item.vlr_pis | numberFormat:2:',':'.' }}</td>
																<td>{{ item.vlr_cofins | numberFormat:2:',':'.' }}</td>
																<td>{{ item.vlr_pis_st | numberFormat:2:',':'.' }}</td>
																<td>{{ item.vlr_cofins_st | numberFormat:2:',':'.' }}</td>
																<td align="center">
																	<button type="button" ng-click="editarBaseTributaria(item,$index)" tooltip="Editar" title="Editar" class="btn btn-xs btn-warning" data-toggle="tooltip">
																		<i class="fa fa-edit"></i>
																	</button>
																	<button class="btn btn-xs btn-danger" ng-disabled="itemEditing($index)" ng-click="delBaseTributaria($index)" tooltip="excluir" title="excluir" data-toggle="tooltip"><i class="fa fa-trash-o"></i></button>
																</td>
															</tr>
														</tbody>
													</table>
										
											</div>
										</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<div class="pull-right">
									<button ng-click="showBoxNovo(); reset();" type="submit" class="btn btn-danger btn-sm">
										<i class="fa fa-times-circle"></i> Cancelar
									</button>
									<button  ng-click="salvar()" id="salvar-base-tributaria" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." type="button" class="btn btn-success btn-sm">
										<i class="fa fa-save"></i> Salvar
									</button>
								</div>
							</div>
						</div>

					</div>
				</div><!-- /panel -->

				<div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-tasks"></i> Bases Tributária Cadastradas</div>

					<div class="panel-body">
						<div  class="alert alert-list" style="display:none"></div>
						<table class="table table-bordered table-condensed table-striped table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Descrição</th>
									<th width="80" style="text-align: center;">Opções</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="item in bases_tributaria" bs-tooltip>
									<td width="80">{{ item.cod_base_tributaria }}</td>
									<td>{{ item.dsc_base_tributaria }}</td>
									<td align="center">
										<button type="button" ng-click="editar(item)" tooltip="Editar" title="Editar" class="btn btn-xs btn-warning" data-toggle="tooltip">
											<i class="fa fa-edit"></i>
										</button>
										<button type="button" ng-click="delete(item)" tooltip="Excluir" title="Excluir" class="btn btn-xs btn-danger delete" data-toggle="tooltip">
											<i class="fa fa-trash-o"></i>
										</button>
									</td>
								</tr>
								<tr>
									<td colspan="3" class="text-center" ng-if="bases_tributaria.length == 0 && bases_tributaria != null">
										Nenhuma Base Trbibutária Especial Encontrada
									</td>
								</tr>
								<tr>
									<td colspan="3" class="text-center" ng-if="bases_tributaria == null">
										<i class='fa fa-refresh fa-spin'></i> Carregando
									</td>
								</tr>
							</tbody>
						</table>	
						<div class="pull-right">
							<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.bases_tributaria.length > 1">
								<li ng-repeat="item in paginacao.bases_tributaria" ng-class="{'active': item.current}">
									<a href="" h ng-click="load(item.offset,item.limit)">{{ item.index }}</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /main-container -->
		<!-- /Modal Produtos-->
		<div class="modal fade" id="list_produtos" style="display:none">
  			<div class="modal-dialog modal-lg">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Produtos</span></h4>	
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.produtos" ng-enter="loadProdutos(0,10)" type="text" class="form-control input-sm">

						            <div class="input-group-btn">
						            	<button tabindex="-1" class="btn btn-sm btn-primary" type="button"
						            		ng-click="loadProdutos(0,10)">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div> <!-- /input-group-btn -->
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>

						<br>

						<div class="row">
							<div class="col-md-12">
								<div class="alert alert-produtos" style="display:none"></div>
						   		<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(produtos.length != 0)">
										<tr>
											<th>#</th>
											<th>Nome</th>
											<th>Fabricante</th>
											<th>Tamanho</th>
											<th>Sabor/Cor</th>
											<th width="80"></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(produtos.produtos.length == null)">
											<td colspan="6" class="text-center"><i class='fa fa-refresh fa-spin'></i> Carregando...</td>
										</tr>
										<tr ng-show="(produtos.produtos.length == 0)">
											<td colspan="6">Não a resultados para a busca</td>
										</tr>
										<tr ng-repeat="item in produtos.produtos">
											<td>{{ item.id_produto }}</td>
											<td>{{ item.nome }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td>{{ item.peso }}</td>
											<td>{{ item.sabor }}</td>
											<td>
											<button ng-click="addProduto(item)" class="btn btn-success btn-xs" type="button">
												<i class="fa fa-check-square-o"></i> Selecionar
											</button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>

					    <div class="row">
					    	<div class="col-md-12">
								<div class="input-group pull-right">
						             <ul class="pagination pagination-xs m-top-none" ng-show="produtos.paginacao.length > 1">
										<li ng-repeat="item in produtos.paginacao" ng-class="{'active': item.current}">
											<a href="" ng-click="loadProdutos(item.offset,item.limit)">{{ item.index }}</a>
										</li>
									</ul>
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
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

	<!-- Bower Components -->	
	<script src="bower_components/noty/lib/noty.min.js" type="text/javascript"></script>
    <script src="bower_components/mojs/build/mo.min.js" type="text/javascript"></script>

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
	<script src="js/angular-controller/base_tributaria-controller.js"></script>
	<script type="text/javascript"></script>>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

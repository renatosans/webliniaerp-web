<?php
	include_once "util/login/restrito.php";
	//restrito();
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

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/custom.css">

	<style type="text/css">
		.panel.panel-default {
		    overflow: visible !important;
		}
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
	</style>
  </head>

  <body class="overflow-hidden" ng-controller="EmpreendimentoController" ng-cloak>
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
					 <li class="active"><i class="fa fa-building-o"></i> Empreendimentos</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-building-o"></i> Empreendimentos</h3>
					<br/>
					<a  class="btn btn-info" id="btn-novo" ng-disabled="editing" ng-click="showBoxNovo()"><i class="fa fa-plus-circle"></i> Novo Empreendimento</a>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md">
				<div class="alert alert-sistema" style="display:none"></div>

				<div class="panel panel-default" id="box-novo" style="display:none;overflow:visible;">
					<div  class="panel-heading"><i class="fa fa-plus-circle"></i> Novo Empreendimento</div>

					<div class="panel-body">
							<div class="row">
								<div class="col-sm-3">
									<div id="nome_empreendimento" class="form-group">
										<label class="control-label">Descrição</label>
										<input  class="form-control" type="text" id="descricao" ng-model="empreendimento.nome_empreendimento">
									</div>
								</div>	
								<div class="col-sm-3">
									<div id="cnpj" class="form-group">
										<label class="control-label">CNPJ </label> 
										<input class="form-control" ui-mask="99.999.999/9999-99" ng-model="empreendimento.num_cnpj">
									</div>
								</div>
								<div class="col-sm-3">
									<div id="num_inscricao_estadual" class="form-group">
										<label class="control-label">Inscrição Estadual</label>
										<input class="form-control" ng-model="empreendimento.num_inscricao_estadual">
									</div>
								</div>
								<div class="col-lg-3">
									<div id="nme_razao_social" class="form-group">
										<label class="control-label">Razão Social  </label>
										<input class="form-control" ng-model="empreendimento.nme_razao_social">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-3">
									<div id="nme_fantasia" class="form-group">
										<label class="control-label">Nome Fantasia  </label>
										<input class="form-control" ng-model="empreendimento.nme_fantasia">
									</div>
								</div>
								<div class="col-sm-2">
									<div id="cep" class="form-group">
										<label class="control-label">CEP  </label>
										<input type="text" class="form-control" ui-mask="99999-999" 
											ng-model="empreendimento.num_cep" ng-keyUp="validCep(empreendimento.num_cep)" ng-blur="validCep(empreendimento.num_cep)">
									</div>
								</div>

								<div class="col-sm-6">
									<div id="endereco" class="form-group">
										<label class="control-label">Endereço  </label>
										<input type="text" class="form-control" ng-model="empreendimento.nme_logradouro">
									</div>
								</div>

								<div class="col-sm-1">
									<div id="numero" class="form-group">
										<label class="control-label">N°. </label>
										<input id="num_logradouro" type="text" class="form-control" ng-model="empreendimento.num_logradouro">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-2">
									<div id="bairro" class="form-group">
										<label class="control-label">Bairro  </label>
										<input type="text" class="form-control" ng-model="empreendimento.nme_bairro_logradouro">
									</div>
								</div>
								<div class="col-sm-2">
									<div id="id_estado" class="form-group">
										<label class="control-label">Estado  </label>
										<select id="id_select_estado" class="form-control" ng-change="loadCidadesByEstado()"  ng-model="empreendimento.cod_estado" ng-options="item.id as item.nome for item in estados" ng-change="loadCidadesByEstado()"></select>
									</div>
								</div>

								<div class="col-sm-4">
									<div id="id_cidade" class="form-group">
										<label class="control-label">Cidade   <span ng-if="cidades.length == 0" style="margin-left: 195px;color:#428bca"><i class='fa fa-refresh fa-spin'></i> Carregando ...</span></label>
										<select class="form-control"  ng-model="empreendimento.cod_cidade" ng-options="a.id as a.nome for a in cidades"></select>
									</div>
								</div>
								<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Teste?</label>
											<div class="col-lg">
												<label class="label-radio inline">
													<input  type="radio" ng-model="empreendimento.flg_teste"  value="1" name="empreendimeto_teste">
													<span class="custom-radio"></span>
													Sim
												</label>
												<label class="label-radio inline">
													<input  type="radio" ng-model="empreendimento.flg_teste"  value="0" name="empreendimeto_teste">
													<span class="custom-radio"></span>
													Não
												</label>
											</div>
										</div>
								</div>
								<div class="row">
									<div class="col-sm-2">
									<div class="form-group" id="qtd_dias_teste">
										<label class="control-label">Qtd Dias</label>
											<input class="form-control text-center"  ng-show="empreendimento.flg_teste == 1" onKeyPress="return SomenteNumero(event);"  type="text" ng-model="empreendimento.qtd_dias_teste">
											<input class="form-control text-center" ng-if="empreendimento.flg_teste != 1" ng-disabled="true" >
									</div>	
									</div>			
								</div>	
							</div>
							<div style="padding: 10px 15px 10px 0px; margin-bottom:10px" class="panel-heading">
								<i class="fa fa-barcode"></i>&nbsp; Dados Fiscais
							</div>
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group" id="regimeTributario">
										<label class="ccontrol-label">Regime Tributario </label> 
										<select chosen ng-change="ClearChosenSelect('cod_regime_tributario')"
									    option="regimeTributario"
									    ng-model="empreendimento.cod_regime_tributario"
									    no-results-text="'Nenhum valor encontrado'"
									    ng-options="regimeTributario.cod_controle_item_nfe as regimeTributario.nme_item for regimeTributario in regimeTributario">
										</select>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group" id="regimePisCofins">
										<label class="ccontrol-label">Regime Pis Cofins  </label> 
										<select chosen ng-change="ClearChosenSelect('cod_regime_pis_cofins')"
									    option="regimePisCofins"
									    no-results-text="'Nenhum valor encontrado'"
									    ng-model="empreendimento.cod_regime_pis_cofins"
									    ng-options="regime.cod_controle_item_nfe as regime.nme_item for regime in regimePisCofins">
										</select>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group" id="tipoEmpresaeso">
										<label class="ccontrol-label">Tipo da Empresa</label> 
										<select chosen ng-change="ClearChosenSelect('cod_tipo_empresa')"
									    option="tipoEmpresa"
									    no-results-text="'Nenhum valor encontrado'"
									    ng-model="empreendimento.cod_tipo_empresa"
									    ng-options="regime.cod_controle_item_nfe as regime.nme_item for regime in tipoEmpresa">
										</select>
									</div>
								</div>

							</div>
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group" id="zoneamento">
										<label class="ccontrol-label">Zoneamento</label> 
										<select chosen ng-change="ClearChosenSelect('cod_zoneamento')"
									    option="zoneamentos"
									    no-results-text="'Nenhum valor encontrado'"
									    ng-model="empreendimento.cod_zoneamento"
									    ng-options="zoneamento.cod_zoneamento as zoneamento.dsc_zoneamento for zoneamento in zoneamentos">
										</select>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group" id="vlr_custo">
										<label class="control-label">Percentual Credito Simples</label>
										<input  ng-model="empreendimento.num_percentual_credito_simples" thousands-formatter class="form-control input-sm">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label for="" class="control-label">Contribuinte ICMS</label>
										<div class="form-group">
											<label class="label-radio inline">
												<input ng-model="empreendimento.flg_contribuinte_icms" value="0" type="radio" class="inline-radio">
												<span class="custom-radio"></span>
												<span>Não</span>
											</label>

											<label class="label-radio inline">
												<input ng-model="empreendimento.flg_contribuinte_icms" value="1" type="radio" class="inline-radio">
												<span class="custom-radio"></span>
												<span>Sim</span>
											</label>
										</div>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label for="" class="control-label">Contribuinte IPI</label>
										<div class="form-group">
											<label class="label-radio inline">
												<input ng-model="empreendimento.flg_contribuinte_ipi" value="0" type="radio" class="inline-radio">
												<span class="custom-radio"></span>
												<span>Não</span>
											</label>

											<label class="label-radio inline">
												<input ng-model="empreendimento.flg_contribuinte_ipi" value="1" type="radio" class="inline-radio">
												<span class="custom-radio"></span>
												<span>Sim</span>
											</label>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-3">
									<div id="num_inscricao_estadual_st" class="form-group">
										<label class="control-label">Inscrição Estadual ST </label>
										<input class="form-control" ng-model="empreendimento.num_inscricao_estadual_st">
									</div>
								</div>
							</div>
							<div class="row" ng-show="editing">
										<div class="col-sm-12">
											<div class="empreendimentos form-group" id="produto_cliente">
													<table class="table table-bordered table-condensed table-striped table-hover">
														<thead>
															<tr>
																<td colspan="3">
																		<strong>Regime Especial</strong> <i ng-click="showModalRegimeEspecial()" style="cursor:pointer;color: #9ad268;" class="fa fa-plus-circle fa-lg"></i>
																</td>
															</tr>
															<tr>
																<td>#</td>
																<td>Descrição</td>
																<td width="60" align="center">
																	
																</td>
															</tr>
														</thead>
														<tbody>
															<tr ng-show="(empreendimento.regime_especial.length == 0 && empreendimento.regime_especial != null)">
																<td colspan="3" align="center">Nenhum Regime Relacionado</td>
															</tr>
															<tr>
																<td colspan="3" class="text-center" ng-if="empreendimento.regime_especial == null">
																	<i class='fa fa-refresh fa-spin'></i> Carregando
																</td>
															</tr>
															<tr ng-repeat="item in empreendimento.regime_especial" bs-tooltip >
																<td>{{ item.cod_regime_especial }}</td>
																<td>{{ item.dsc_regime_especial }}</td>
																<td align="center">
																	<button class="btn btn-xs btn-danger" ng-disabled="itemEditing($index)" ng-click="delRegimeEspecial($index)" tooltip="excluir" title="excluir" data-toggle="tooltip"><i class="fa fa-trash-o"></i></button>
																</td>
															</tr>
														</tbody>
													</table>
										
											</div>
										</div>
							</div>					
							<div class="form-group" >
								<div class="col-sm-12">
									<div class="pull-right">
										<button ng-click="showBoxNovo(); reset();" type="submit" class="btn btn-danger btn-sm">
											<i class="fa fa-times-circle"></i> Cancelar
										</button>
										<button ng-click="salvar()" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." id="salvar-empreendimento" type="submit" class="btn btn-success btn-sm">
											<i class="fa fa-save"></i> Salvar
										</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div><!-- /panel -->

				<div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-tasks"></i> Empreendimentos Cadastrados</div>

					<div class="panel-body">
						<div class="row">
							<div class="col-sm-11">
								<div class="input-group">
						            <input ng-model="busca.text" type="text" class="form-control input-sm" ng-enter="load(0,10)">
						            <div class="input-group-btn">
						            	<button ng-click="load(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div>
						        </div>
							</div>
							<div class="col-sm-1">
								<button type="button" class="btn btn-sm btn-default" ng-click="resetFilter()">Limpar</button>
							</div>
						</div>

						<br>
						<table class="table table-bordered table-condensed table-striped table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Descrição</th>
									<th width="100" style="text-align: center;">Opções</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="item in empreendimentos">
									<td width="80">{{ item.id }}</td>
									<td>{{ item.nome_empreendimento }}</td>
									<td align="center">
										<button  type="button" ng-click="editar(item)" tooltip="Editar" class="btn btn-xs btn-warning" data-toggle="tooltip">
											<i class="fa fa-edit"></i>
										</button>
										<button  type="button" ng-click="delete(item)" tooltip="Excluir" class="btn btn-xs btn-danger delete" data-toggle="tooltip">
											<i class="fa fa-trash-o"></i>
										</button>
										<button ng-if="userLogged.id_empreendimento != 6" type="button" ng-click="editar(item)" tooltip="Detalhes" data-toggle="tooltip" class="btn btn-xs btn-primary ng-scope" data-original-title="" title="">
											<i class="fa fa-tasks"></i>
										</button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="panel-footer clearfix">
						<div class="pull-right">
							<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.itens.length > 1">
								<li ng-repeat="item in paginacao.itens" ng-class="{'active': item.current}">
									<a href="" h ng-click="load(item.offset,item.limit)">{{ item.index }}</a>
								</li>
							</ul>
						</div>
					</div>
				</div>

				<!-- <div class="row">
					<div class="col-sm-5">
						<div class="panel panel-default">
							<div class="panel-heading">
								Plano de Contas
								<ul class="tool-bar">
									<li>
										<a href="#" ng-click="loadPlanoContas();">
											<i class="fa fa-refresh"></i>
										</a>
									</li>
								</ul>
							</div>
							<div class="panel-body">
								<div
									data-angular-treeview="true"
									data-tree-model="roleList"
									data-node-id="id"
									data-node-label="nme_completo"
									data-node-children="children">
								</div>
							</div>
							<div class="panel-footer">
								<pre>{{ currentNode }}</pre>
							</div>
						</div>
					</div>
				</div> -->
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

		<!-- /Modal empreendimento-->
		<div class="modal fade" id="list-regime-especial" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Regime Especial</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.empreendimento" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadRegimeEspecial(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div> <!-- /input-group-btn -->
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>

						<br>

						<div class="row">
							<div class="col-sm-12">
								<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(regimes.length != 0)">
										<tr>
											<th colspan="2">#</th>
											<th colspan="2">Descrição</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(regimes.length == 0)">
											<td colspan="2">Nenhum Regime Encontrado</td>
										</tr>
										<tr ng-repeat="item in regimes">
											<td>{{ item.cod_regime_especial }}</td>
											<td>{{ item.dsc_regime_especial }}</td>
											<td width="50" align="center">
												<button type="button" class="btn btn-xs btn-success" ng-disabled="selectedRegimeEspecial(item)" ng-click="selRegimeEspecial(item)">
													<i class="fa fa-check-square-o"></i> Selecionar
												</button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>

						<div class="row">
				    		<div class="col-sm-12">
				    			<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao.regimes.length > 1">
									<li ng-repeat="item in paginacao.regimes" ng-class="{'active': item.current}">
										<a href="" ng-click="loadRegimeEspecial(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
				    		</div>
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
	<script src="js/angular-controller/empreendimentos-controller.js"></script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

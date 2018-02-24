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

	<!-- Datepicker -->
	<link href="css/datepicker.css" rel="stylesheet"/>

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


	</style>
  </head>

  <body class="overflow-hidden" ng-controller="ControleProteseController" ng-cloak>
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
					 <li class="active"><i class="fa fa-list-alt "></i> Controle de Prótese</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-list-alt"></i> Controle de Prótese</h3>
					<br/>
					<a class="btn btn-info" id="btn-novo" ng-disabled="editing" ng-click="showBoxNovo()"><i class="fa fa-plus-circle"></i> Novo Pedido de Prótese</a>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md">
				<div class="alert alert-sistema" style="display:none"></div>

				<div class="panel panel-default" id="box-novo" style="display:none">
					<div class="panel-heading"><i class="fa fa-plus-circle"></i> Novo Pedido de Prótese</div>

					<div class="panel-body">
						<div class="row">
							<div class="col-sm-12"><div style="display: none" class="alert alert-controle-form"></div></div>
						</div>
						<div class="row">
							<div class="col-sm-4" id="id_profissional_solicitante">
								<label class="control-label">Profissional</label>
								<div class="input-group">
						            <input ng-model="controle_protese.nome_profissinal " ng-disabled="true" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="selProfissionais()" tabindex="-2" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-user"></i>
						            	</button>
						            </div>
						        </div>
							</div>
							<div class="col-sm-4" id="id_paciente">
								<label class="control-label">Paciente</label>
								<div class="input-group">
						            <input ng-model="controle_protese.nome_paciente" ng-disabled="true" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="selCliente()" tabindex="-2" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-user-md"></i>
						            	</button>
						            </div>
						        </div>
							</div>
							<div class="col-sm-4" id="id_laboratorio">
								<label class="control-label">Laboratório</label>
								<div class="input-group">
						            <input ng-model="controle_protese.nome_laboratorio" ng-disabled="true" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="selFornecedor()" tabindex="-2" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-hospital-o "></i>
						            	</button>
						            </div>
						        </div>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-sm-3" id="dta_previsao_entrega">
                    			<div class="form-group" >
                    				<label class="control-label">Previsão de Entrega</label>
                    				<div class="controls">
                    					<div class="input-group">
                    						<input id="input-dta-previsao-entrega" ui-mask="99/99/9999" ng-model="dta_entrada" class="form-control input-sm datepicker"/>
                    						<span class="input-group-btn">
												<button  id="btnDtaCalendar" style="margin-top: 1px" class="btn btn-default btn-sm" type="button">
													<i class="fa fa-calendar"></i>
												</button>
											</span>
                    					</div>
                    				</div>
                    			</div>
                    		</div>
							<div class="col-sm-3" id="dta_envio">
                    			<div class="form-group">
                    				<label class="control-label">Data de Envio</label>
                    				<div class="controls">
                    					<div class="input-group">
                    						<input id="input-dta-envio" ui-mask="99/99/9999" ng-model="dta_envio" class="form-control input-sm datepicker"/>
                    						<span class="input-group-btn">
												<button  id="btnDtaCalendar" style="margin-top: 1px" class="btn btn-default btn-sm" type="button">
													<i class="fa fa-calendar"></i>
												</button>
											</span>
                    					</div>
                    				</div>
                    			</div>
                    		</div>
                    		<div class="col-sm-3" id="dta_entrega">
                    			<div class="form-group" >
                    				<label class="control-label">Data de Entrega</label>
                    				<div class="controls">
                    					<div class="input-group">
                    						<input id="input-dta-entrega" ui-mask="99/99/9999" ng-model="dta_entrega" class="form-control input-sm datepicker"/>
                    						<span class="input-group-btn">
												<button  id="btnDtaCalendar" style="margin-top: 1px" class="btn btn-default btn-sm" type="button">
													<i class="fa fa-calendar"></i>
												</button>
											</span>
                    					</div>
                    				</div>
                    			</div>
                    		</div>
						</div>
					</div>
					<div class="panel-footer pull-right">
						<button ng-click="cancelar()" class="btn btn-danger btn-sm"><i class="fa fa-times-circle"></i> Cancelar</button>
						<button ng-click="salvarControle()" class="btn btn-success btn-sm" id="salvar-controle" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde...">
							<i class="fa fa-save"></i> Salvar
						</button>
					</div>
				</div><!-- /panel -->

				<div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-list-alt "></i> Pedidos Realizados</div>

					<div class="panel-body">
						<div class="row">
							<div class="col-sm-12"><div style="display: none" class="alert alert-controle"></div></div>
						</div>
						<table class="table table-bordered table-condensed table-striped table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Usúario</th>
									<th>Profissional</th>
									<th>Paciente</th>
									<th>Laboratório</th>
									<th>Dta. Pre. Entrega</th>
									<th>Dta. Envio</th>
									<th>Dta. Entrega </th>
									<th>Status</th>
									<th width="80" style="text-align: center;">Opções</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-if="controles == null">
									<td colspan="10" class="text-center"><i class='fa fa-refresh fa-spin'></i> Aguarde, carregando registros...</td>
								</tr>
								<tr ng-if="controles.length == 0">
									<td colspan="10" class="text-center">Nenhum registro encontrado</td>
								</tr>
								<tr ng-repeat="item in controles" ng-if="controles.length > 0" bs-tooltip>
									<td>{{ item.id }}</td>
									<td>{{ item.nome_usuario }}</td>
									<td>{{ item.nome_profissional_solicitante }}</td>
									<td>{{ item.nome_paciente }}</td>
									<td>{{ item.nome_laboratorio }}</td>
									<td>{{ item.dta_previsao_entrega | dateFormat:'date' }}</td>
									<td>{{ item.dta_envio | dateFormat:'date' }}</td>
									<td>{{ item.dta_entrega | dateFormat:'date' }}</td>
									<td>{{ item.dsc_status_controle_protese }}</td>
									<td align="center">
										<button ng-if="item.id_status == 1" data-loading-text="<i class='fa fa-refresh fa-spin'></i>" ng-click="changeStatus(2,$index,$event)" type="button" title="Enviar para produção" class="btn btn-xs btn-warning" data-toggle="tooltip">
											<i class="fa fa-paper-plane"></i>
										</button>
										<button ng-if="item.id_status == 2" data-loading-text="<i class='fa fa-refresh fa-spin'></i>" ng-click="changeStatus(3,$index,$event)" type="button" title="Confirmar entrega" class="btn btn-xs btn-primary" data-toggle="tooltip">
											<i class="fa fa-check-circle-o "></i>
										</button>
										<button ng-if="item.id_status!= 4 && item.id_status!=3" data-loading-text="<i class='fa fa-refresh fa-spin'></i>" ng-click="changeStatus(4,$index,$event)" type="button" title="Cancelar pedido" class="btn btn-xs btn-danger" data-toggle="tooltip">
											<i class="fa fa-plus-circle "></i>
										</button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="panel-footer">
						<div class="row">
							<div class="col-sm-12">
								<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao.controles.length > 1">
									<li ng-repeat="item in paginacao.controles" ng-class="{'active': item.current}">
										<a href="" ng-click="loadControles(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /main-container -->

		<!-- Modais
		================================================== -->

		<!-- /Modal empreendimento-->
		<div class="modal fade" id="list_empreendimentos" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Empreendimentos</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.empreendimento" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadAllEmpreendimentos(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
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
									<thead ng-show="(empreendimento.length != 0)">
										<tr>
											<th colspan="2">Nome</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(empreendimento.length == 0)">
											<td colspan="2">Não há empreendimentos cadastrados</td>
										</tr>
										<tr ng-repeat="item in empreendimentos">
											<td>{{ item.nome_empreendimento }}</td>
											<td width="50" align="center">
												<button ng-show="transferencia.id_empreendimento != item.id" type="button" class="btn btn-xs btn-success" ng-click="addEmpreendimento(item)">
													<i class="fa fa-check-square-o"></i> Selecionar
												</button>
												<button ng-show="transferencia.id_empreendimento == item.id" ng-show="existsAcessorio(item)" ng-disabled="true" class="btn btn-primary btn-xs" type="button">
                                                	<i class="fa fa-check-circle-o"></i> Selecionado
                                            	</button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>

						<div class="row">
				    		<div class="col-sm-12">
				    			<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao.empreendimentos.length > 1">
									<li ng-repeat="item in paginacao.empreendimentos" ng-class="{'active': item.current}">
										<a href="" ng-click="loadAllEmpreendimentos(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
				    		</div>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal produtos-->
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
						            <input ng-model="busca.produto" ng-enter="loadProdutos(0,10)" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadProdutos(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div> <!-- /input-group-btn -->
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>

						<br/>

				   		<div class="row">
				   			<div class="col-sm-12">
				   				<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(produtos.length != 0)">
										<tr>
											<th >ID</th>
											<th >Nome</th>
											<th >Fabricante</th>
											<th >Tamanho</th>
											<th >Sabor/cor</th>
											<th >qtd</th>
											<th ></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="(produtos.length == 0)" class="text-center">
											<td colspan="7">Nenhum produto encontrado</td>
										</tr>
										<tr ng-show="produtos == null" class="text-center">
											<td colspan="7" ><i class='fa fa-refresh fa-spin'></i> Carregando...</td>
										</tr>
										<tr ng-repeat="item in produtos">
											<td>{{ item.id }}</td>
											<td>{{ item.nome }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td>{{ item.peso }}</td>
											<td>{{ item.sabor }}</td>
											<td  width="50"><input  ng-model="item.qtd_pedida" type="text" class="form-control input-xs" /></td>
											<td width="50" align="center">
												<button ng-show="!produtoSelected(item.id)" type="button" class="btn btn-xs btn-success" ng-click="addProduto(item)">
													<i class="fa fa-check-square-o"></i> Selecionar
												</button>
												<button ng-show="produtoSelected(item.id)" ng-show="existsAcessorio(item)" ng-disabled="true" class="btn btn-primary btn-xs" type="button">
                                                	<i class="fa fa-check-circle-o"></i> Selecionado
                                            	</button>
											</td>
										</tr>
									</tbody>
								</table>
				   			</div>
				   		</div>

				   		<div class="row">
					    	<div class="col-sm-12">
					    		<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao.produtos.length > 1">
									<li ng-repeat="item in paginacao.produtos" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadProdutos(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
					    	</div>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal Detalhes transferencia -->
		<div class="modal fade" id="modal-detalhes-transferencia" style="display:none">
  			<div class="modal-dialog modal-lg">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Detalhes da Transferência</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-sm-12">
								<b>ID: </b> {{view.transferencia.id}}<br/>
								<b>Dta. Pedido: </b> {{view.transferencia.dta_pedido | dateFormat : 'dateTime'}}<br/>
								<b>Usuario: </b> {{view.transferencia.nome_usuario_pedido}}<br/>
								<b>Empreendimento: </b> {{view.transferencia.nome_empreendimento_transferencia}}<br/>
								<b>Status: </b> {{view.transferencia.dsc_status_transferencia_estoque}}<br/><br/>
							</div>
						</div>
				   		<div class="row">
				   			<div class="col-sm-12">
				   				<table class="table table-bordered table-condensed table-striped table-hover">
									<thead ng-show="(view.transferencia.itens.length != 0)">
										<tr>
											<th >ID produto</th>
											<th >Produto</th>
											<th class="text-center" >Qtd. Pedida</th>
											<th ng-show="item.id_status_transferencia == 2" class="text-center">Qtd. Transferida</th>
											<th ng-show="item.id_status_transferencia == 3" class="text-center">Qtd. Entregue</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in view.transferencia.itens">
											<td>{{ item.id_produto }}</td>
											<td>{{ item.nome_produto }}</td>
											<td class="text-center">{{ item.qtd_pedida }}</td>
											<td ng-show="item.id_status_transferencia == 2" class="text-center">{{ item.qtd_transferida }}</td>
											<td ng-show="item.id_status_transferencia == 3" class="text-center">{{ item.qtd_entregeue }}</td>
										</tr>
									</tbody>
								</table>
				   			</div>
				   		</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal Clientes-->
        <div class="modal fade" id="list_clientes" style="display:none">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4>Pacientes</span></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input ng-model="busca.clientes"  ng-enter="loadCliente(0,10)" type="text" class="form-control input-sm">
                                    <div class="input-group-btn">
                                        <button ng-click="loadCliente(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
                                            <i class="fa fa-search"></i> Buscar
                                        </button>
                                    </div> <!-- /input-group-btn -->
                                </div> <!-- /input-group -->
                            </div><!-- /.col -->
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered table-condensed table-striped table-hover">
                                    <tr ng-show="clientes != false && (clientes.length <= 0 || clientes == null)">
                                        <th class="text-center" colspan="9" style="text-align:center"><strong>Carregando</strong><img src="assets/imagens/progresso_venda.gif"></th>
                                    </tr>
                                    <tr ng-show="clientes == false">
                                        <th colspan="4" class="text-center">Não a resultados para a busca</th>
                                    </tr>
                                    <thead ng-show="(clientes.length != 0)">
                                        <tr>
                                            <th >Nome</th>
                                            <th >Apelido</th>
                                            <th >Perfil</th>
                                            <th colspan="2">selecionar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="item in clientes">
                                            <td>{{ item.nome }}</td>
                                            <td>{{ item.apelido }}</td>
                                            <td>{{ item.nome_perfil }}</td>
                                            <td width="50" align="center">
                                                <button ng-show="controle_protese.id_paciente != item.id" type="button" class="btn btn-xs btn-success" ng-click="addCliente(item)">
                                                    <i class="fa fa-check-square-o"></i> Selecionar
                                                </button>
                                                 <button ng-show="controle_protese.id_paciente == item.id" ng-disabled="true" class="btn btn-primary btn-xs" type="button">
                                                	<i class="fa fa-check-circle-o"></i> Selecionado
                                            	</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao_clientes.length > 1">
                                    <li ng-repeat="item in paginacao_clientes" ng-class="{'active': item.current}">
                                        <a href="" h ng-click="loadCliente(item.offset,item.limit)">{{ item.index }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

        <!-- /Modal fornecedor-->
		<div class="modal fade" id="list_fornecedores" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Laboratórios</h4>
      				</div>
				    <div class="modal-body">
				    	<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.fornecedores" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadFornecedor(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div> <!-- /input-group-btn -->
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>
						<br/></br>
				   		<table class="table table-bordered table-condensed table-striped table-hover">
							<thead ng-show="(fornecedores.length != 0)">
								<tr>
									<th colspan="2">Nome</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-show="(fornecedores.length == 0)">
									<td colspan="2">Não a fornecedores relacionados para esté produto</td>
								</tr>
								<tr ng-repeat="item in fornecedores">
									<td>{{ item.nome_fornecedor }}</td>
									<td width="80">
										<button ng-show="controle_protese.id_laboratorio != item.id" ng-click="addFornecedor(item)" class="btn btn-success btn-xs" type="button">
												<i class="fa fa-check-square-o"></i> Selecionar
										</button>
										<button ng-show="controle_protese.id_laboratorio == item.id" ng-disabled="true" class="btn btn-primary btn-xs" type="button">
                                        	<i class="fa fa-check-circle-o"></i> Selecionado
                                    	</button>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="row">
				    		<div class="col-sm-12">
				    			<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao_fornecedores.length > 1">
									<li ng-repeat="item in paginacao_fornecedores" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadFornecedor(item.offset,item.limit)">{{ item.index }}</a>
									</li>
								</ul>
				    		</div>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

        <!-- /Modal Profissionais-->
        <div class="modal fade" id="list_profissionais" style="display:none">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4>Profissionais</span></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input ng-model="busca.clientes"  ng-enter="loadCliente(0,10)" type="text" class="form-control input-sm">
                                    <div class="input-group-btn">
                                        <button ng-click="loadCliente(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
                                            <i class="fa fa-search"></i> Buscar
                                        </button>
                                    </div> <!-- /input-group-btn -->
                                </div> <!-- /input-group -->
                            </div><!-- /.col -->
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered table-condensed table-striped table-hover">
                                    <tr ng-show="profissionais != false && (profissionais.length <= 0 || profissionais == null)">
                                        <th class="text-center" colspan="9" style="text-align:center"><strong>Carregando</strong><img src="assets/imagens/progresso_venda.gif"></th>
                                    </tr>
                                    <tr ng-show="profissionais == false">
                                        <th colspan="4" class="text-center">Não a resultados para a busca</th>
                                    </tr>
                                    <thead ng-show="(profissionais.length != 0)">
                                        <tr>
                                            <th >Nome</th>
                                            <th >Apelido</th>
                                            <th >Perfil</th>
                                            <th colspan="2">selecionar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="item in profissionais">
                                            <td>{{ item.nome }}</td>
                                            <td>{{ item.apelido }}</td>
                                            <td>{{ item.nome_perfil }}</td>
                                            <td width="50" align="center">
                                                <button ng-show="controle_protese.id_profissional_solicitante != item.id" type="button" class="btn btn-xs btn-success" ng-click="addProfissional(item)">
                                                    <i class="fa fa-check-square-o"></i> Selecionar
                                                </button>
                                                <button ng-show="controle_protese.id_profissional_solicitante == item.id" ng-disabled="true" class="btn btn-primary btn-xs" type="button">
                                                	<i class="fa fa-check-circle-o"></i> Selecionado
                                            	</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao_profissionais.length > 1">
                                    <li ng-repeat="item in paginacao_profissionais" ng-class="{'active': item.current}">
                                        <a href="" h ng-click="loadProfissionais(item.offset,item.limit)">{{ item.index }}</a>
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

	<!-- Datepicker -->
	<script src='js/bootstrap-datepicker.min.js'></script>

	<!-- Endless -->
	<script src="js/endless/endless.js"></script>

	<!-- Moment -->
	<script src="js/moment/moment.min.js"></script>

	<script src="js/jquery.noty.packaged.js"></script>

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
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/controle_protese-controller.js"></script>
	<script type="text/javascript">
		$('.datepicker').datepicker();
		$('.datepicker').parent('.input-group').find('button').on("click", function(){ $("#dtaInicial").trigger("focus"); });
		$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
		$('.datepicker').parent('.input-group').find('button').on("click", function(){ $(this).parents('.input-group').find('input').trigger("focus") });
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

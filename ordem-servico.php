<?php
	include_once "util/login/restrito.php";
	//restrito(array(1));
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

  <body class="overflow-hidden" ng-controller="OrdemServicoController" ng-cloak>
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

				<?php include_once('menu-modulos.php') ?>
				
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
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md" style="padding-top: 0px !important;">
				<div class="panel panel-primary m-top-md" id="box-novo" style="display:none">
					<div class="panel-heading">
						<i class="fa fa-plus-circle"></i> Nova Ordem de Serviço <i class="fa fa-circle {{ (caixa != null) ? 'text-success' : 'text-danger' }}"></i>
						<div class="pull-right">
							<a class="btn btn-xs btn-{{(!editing) ? 'success' : 'danger'}} btn-novo" ng-click="showBoxNovo()">
								<i class="fa {{(!editing) ? 'fa-plus-circle' : 'fa-minus-circle'}}"></i> {{(!editing) ? 'Nova O.S.' : 'Cancelar'}}
							</a>
						</div>
					</div>

					<div class="panel-body">
						<div class="row">
							<div class="col-sm-9">
								<div class="row">
									<div class="col-sm-3" id="id">
										<div class="form-group element-group">
											<label class="control-label">N° da OS</label>
											<input type="text" class="form-control" readonly="readonly" value="#{{ objectModel.id }}">
										</div>
									</div>

									<div class="col-sm-3">
										<div class="form-group element-group">
											<label class="control-label">Data da OS</label>
											<input type="text" class="form-control text-center" readonly="readonly" ng-model="objectModel.dta_ordem_servico">
										</div>
									</div>

									<div class="col-sm-6">
										<div class="form-group element-group">
											<label class="control-label">Criador da O.S.</label>
											<input type="text" class="form-control" readonly="readonly" ng-model="objectModel.criador.nme_usuario">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-10" id="id_cliente">
										<div class="form-group element-group">
											<label class="control-label">Cliente</label>
											<div class="input-group">
												<input type="text" class="form-control" readonly="readonly" style="cursor: pointer;"
													name="id_cliente"
													ng-model="objectModel.cliente.nome" ng-click="showModal('list_clientes', 'cliente')"/>
												<span class="input-group-btn">
													<button ng-click="showModal('list_clientes', 'cliente')" type="button" class="btn btn-info">
														<i class="fa fa-users"></i>
													</button>
												</span>
											</div>
										</div>
									</div>

									<div class="col-sm-2" ng-show="(objectModel.cliente != null)">
										<div class="form-group element-group">
											<label class="control-label">Saldo</label>
											<input type="text" class="form-control" readonly="readonly" 
												style="{{ (objectModel.cliente.vlr_saldo_devedor >= 0) ? 'color: #1A7204;' : 'color: #E62C2C;' }}" 
												value="R$ {{ objectModel.cliente.vlr_saldo_devedor | numberFormat : 2 : ',' : '.' }}">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-2">
										<div class="form-group element-group">
											<label class="control-label">Recorrente?</label>
											<div class="controls">
												<label class="label-radio inline">
													<input type="radio" value="0" ng-model="objectModel.flg_recorrente" ng-disabled="objectModel.id">
													<span class="custom-radio"></span>
													<span>Não</span>
												</label>
												<label class="label-radio inline">
													<input type="radio" value="1" ng-model="objectModel.flg_recorrente" ng-disabled="objectModel.id">
													<span class="custom-radio"></span>
													<span>Sim</span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-sm-3" ng-show="objectModel.flg_recorrente === '1'">
										<div class="form-group element-group">
											<label class="control-label">Periodicidade</label>
											<select class="form-control" ng-model="objectModel.qtd_meses_recorrencia" ng-disabled="objectModel.id">
												<option></option>
												<option value="1">Mensal</option>
												<option value="2">Bimestral</option>
												<option value="3">Trimestral</option>
												<option value="4">Quadrimestral</option>
												<option value="6">Semestral</option>
												<option value="12">Anual</option>
											</select>
										</div>
									</div>

									<div class="col-sm-1" ng-show="objectModel.id">
										<div class="form-group element-group">
											<label class="control-label text-center">Nº</label>
											<input type="text" class="form-control" ng-model="objectModel.num_controle" disabled="disabled" />
										</div>
									</div>

									<div class="col-sm-1" ng-show="objectModel.flg_recorrente === '1'">
										<div class="form-group element-group">
											<label class="control-label text-center">Qtd.</label>
											<input type="text" class="form-control" ng-model="objectModel.qtd_recorrencias" ng-disabled="objectModel.id" />
										</div>
									</div>
								</div>
							</div>

							<div class="col-sm-3">
								<div class="form-group element-group">
									<label class="control-label">Situação da OS</label><br/>
									<label class="label-radio" ng-repeat="item in status_ordem_servico">
										<input type="radio" value="{{ item.id }}" ng-model="objectModel.cod_status_servico">
										<span class="custom-radio {{ item.clr_class }}"></span>
										<span>{{ item.dsc_status }}</span>
									</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-sm-6">
								<fieldset>
									<legend>Serviços</legend>
									<table name="servicos" class="table table-bordered table-condensed table-striped table-hover">
										<thead>
											<th class="text-center text-middle" width="70">Código</th>
											<th class="text-middle">Descrição</th>
											<th class="text-center text-middle" width="60">Qtd.</th>
											<th class="text-center text-middle" width="90">Valor</th>
											<th class="text-center text-middle" width="150">Status</th>
											<th class="text-center text-middle" width="40">
												<button class="btn btn-xs btn-block btn-primary"
													data-toggle="tooltip" title="Adicionar Serviço"
													ng-click="showModal('list_servicos')">
													<i class="fa fa-plus-square"></i>
												</button>
											</th>
										</thead>
										<tbody>
											<tr ng-show="(objectModel.servicos == null || objectModel.servicos.length == 0)">
												<td class="text-center" colspan="6">Nenhum serviço foi adicionado!</td>
											</tr>
											<tr ng-repeat="item in objectModel.servicos">
												<td class="text-center text-middle">
													{{ item.cod_procedimento }}
												</td>
												<td class="text-middle">
													{{ item.dsc_procedimento }}
												</td>
												<td class="text-center text-middle">
													<input type="text" class="form-control input-xs text-center"
														ng-model="item.qtd_pedido" 
														ng-keyup="recalculaTotais()">
												</td>
												<td class="text-right text-middle">
													<input type="text" class="form-control input-xs text-center"
														ng-model="item.vlr_procedimento" mask-moeda
														ng-keyup="recalculaTotais()">
												</td>
												<td class="text-middle">
													<select chosen option="status_servico" ng-model="item.cod_status_servico"
													    ng-options="status.id as status.dsc_status for status in status_servico">
													</select>
												</td>
												<td class="text-center text-middle">
													<button class="btn btn-xs btn-danger" 
														data-toggle="tooltip" title="Remover Serviço"
														ng-click="removeItem(item, 'servicos')">
														<i class="fa fa-trash-o"></i>
													</button>
												</td>
											</tr>
										</tbody>
										<tfoot ng-show="(objectModel.servicos.length > 0)">
											<th class="text-right text-middle" colspan="2">
												Total Serviços
											</th>
											<th class="text-center text-middle">
												{{ objectModel.qtd_total_servicos }}
											</th>
											<th class="text-right text-middle">
												R$ {{ objectModel.vlr_total_servicos | numberFormat : 2 : ',' : '.' }}
											</th>
											<th></th>
											<th></th>
										</tfoot>
									</table>
								</fieldset>
							</div>

							<div class="col-sm-6">
								<fieldset>
									<legend>Produtos</legend>
									<table name="produtos" class="table table-bordered table-condensed table-striped table-hover">
										<thead>
											<th class="text-center text-middle" width="70">Código</th>
											<th class="text-middle">Descrição</th>
											<th class="text-center text-middle" width="90">Valor</th>
											<th class="text-center text-middle" width="60">Qtd.</th>
											<th class="text-center text-middle" width="90">Subtotal</th>
											<th class="text-center text-middle" width="40">
												<button class="btn btn-xs btn-block btn-primary"
													data-toggle="tooltip" title="Adicionar Produto"
													ng-click="showModal('list_produtos')">
													<i class="fa fa-plus-square"></i>
												</button>
											</th>
										</thead>
										<tbody>
											<tr ng-show="(objectModel.produtos == null || objectModel.produtos.length == 0)">
												<td class="text-center" colspan="6">Nenhum produto foi adicionado!</td>
											</tr>
											<tr ng-repeat="item in objectModel.produtos">
												<td class="text-center text-middle">
													{{ item.id_produto }}
												</td>
												<td class="text-middle">
													{{ item.nome_produto }}
												</td>
												<td class="text-right text-middle">
													<input type="text" class="form-control input-xs text-center"
														ng-model="item.vlr_venda_varejo" thousands-formatter precision='{{ configuracoes.qtd_casas_decimais }}'
														ng-keyup="recalculaTotais()">
												</td>
												<td class="text-center text-middle">
													<input type="text" class="form-control input-xs text-center"
														ng-model="item.qtd_pedido" 
														ng-keyup="recalculaTotais()">
												</td>
												<td class="text-right text-middle">
													R$ {{ (item.qtd_pedido * item.vlr_venda_varejo)  | numberFormat : 2 : ',' : '.' }}
												</td>
												<td class="text-center text-middle">
													<button class="btn btn-xs btn-danger" 
														data-toggle="tooltip" title="Remover Produto"
														ng-click="removeItem(item, 'produtos')">
														<i class="fa fa-trash-o"></i>
													</button>
												</td>
											</tr>
										</tbody>
										<tfoot ng-show="(objectModel.produtos.length > 0)">
											<th class="text-right text-middle" colspan="3">
												Total Produtos
											</th>
											<th class="text-center text-middle">
												{{ objectModel.qtd_total_produtos }}
											</th>
											<th class="text-right text-middle">
												R$ {{ objectModel.vlr_total_produtos | numberFormat : 2 : ',' : '.' }}
											</th>
											<th></th>
										</tfoot>
									</table>
								</fieldset>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-8">
								<div class="form-group element-group">
									<label class="control-label">Observações Gerais</label>
									<textarea class="form-control" rows="6"
										ng-model="objectModel.dsc_observacoes_gerais"></textarea>
								</div>
							</div>
							<div class="col-lg-4 text-right">
								<h1>
									<small>Valor Total da OS</small>
									<br/>
									R$ {{ objectModel.vlr_total_os | numberFormat : 2 : ',' : '.' }}
								</h1>
							</div>
						</div>
					</div>

					<div class="panel-footer clearfix">
						<div class="pull-left">
							<div class="alert alert-form alert-warning hide"></div>
							<div class="alert alert-form alert-danger hide"></div>
						</div>

						<div class="pull-right">
							<button id="btnCancelarOS" class="btn btn-sm btn-default" 
								ng-click="showBoxNovo(true)" data-loading-text="Aguarde...">
								<i class="fa fa-times-circle"></i> Cancelar
							</button>
							<button id="btnSalvarOS" class="btn btn-sm btn-success"
								ng-click="save()" data-loading-text="Aguarde...">
								<i class="fa fa-save"></i> Salvar Ordem de Serviço
							</button>
						</div>
					</div>
				</div>
				<!-- /panel -->

				<div class="panel panel-default m-top-md">
					<div class="panel-heading">
						<i class="fa fa-filter"></i> Opções de Filtro <i class="fa fa-circle {{ (caixa != null) ? 'text-success' : 'text-danger' }}"></i>
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

							<div class="col-sm-4">
								<div class="form-group">
									<label class="control-label">Cliente</label>
									<input ng-model="busca.nome" ng-keyup="loadOrdensServicos(0,10)" ng-enter="loadOrdensServicos(0,10)" type="text" class="form-control input-md ng-pristine ng-valid ng-touched">
								</div>
							</div>

							<div class="col-sm-3">
								<div class="form-group" id="regimeTributario">
									<label class="ccontrol-label">Situação da O.S.</label> 
									<select chosen option="status_ordem_servico" ng-model="busca.cod_status_servico"
									    ng-options="status.id as status.dsc_status for status in status_ordem_servico">
									</select>
								</div>
							</div>

							<div class="col-sm-1">
								<div class="form-group">
									<label class="control-label"><br></label>
									<button type="button" class="btn btn-sm btn-block btn-primary" ng-click="loadOrdensServicos(0,10)"><i class="fa fa-filter"></i> Filtrar</button>
								</div>
							</div>

							<div class="col-sm-1">
								<div class="form-group">
									<label class="control-label"><br></label>
									<button type="button" class="btn btn-sm btn-block btn-default" ng-click="resetFilter()">Limpar</button>
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
								<th class="text-center text-middle" width="10"></th>
								<th class="text-center text-middle" width="130">Aberturda da O.S.</th>
								<th class="text-middle" width="200">Cliente</th>
								<th class="text-center text-middle" width="130">Total Serviços</th>
								<th class="text-center text-middle" width="130">Total Produto</th>
								<th class="text-center text-middle" width="130">Total Pedidos</th>
								<th class="text-center text-middle" width="150">Status OS</th>
								<th class="text-center text-middle" width="150">Status NFS-e</th>
							</thead>
							<tbody>
								<tr ng-repeat="item in ordens_servico">
									<td class="text-middle">
										<div class="btn-group">
											<button type="button" class="btn btn-sm btn-default dropdown-toggle" 
												data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												Ações <span class="caret"></span>
											</button>
											<ul class="dropdown-menu">
												<li ng-click="editItem(item)">
													<a href=""><i class="fa fa-edit"></i> Editar Ordem de Serviço</a>
												</li>
												<li ng-show="item.vlr_produtos > 0">
													<a href="nota-fiscal.php?id_venda={{ item.id_venda }}">
														<i class="fa fa-file-text-o"></i> Emitir/Visualizar NF (Produtos)
													</a>
												</li>
												<li ng-show="item.vlr_servicos > 0">
													<a href="nota-fiscal-servico.php?id={{ item.cod_ordem_servico }}">
														<i class="fa fa-file-text-o"></i> Emitir/Visualizar NF (Serviços)
													</a>
												</li>
											</ul>
										</div>
									</td>
									<td class="text-center text-middle">
										{{ item.dta_ordem_servico | dateFormat : 'dateTime' }}
									</td>
									<td class="text-middle">
										{{ item.nme_cliente | uppercase }}
									</td>
									<td class="text-right text-middle">
										R$ {{ item.vlr_servicos | numberFormat : 2 : ',' : '.' }}
									</td>
									<td class="text-right text-middle">
										R$ {{ item.vlr_produtos | numberFormat : 2 : ',' : '.' }}
									</td>
									<td class="text-right text-middle">
										R$ {{ (item.vlr_servicos + item.vlr_produtos) | numberFormat : 2 : ',' : '.' }}
									</td>
									<td class="text-middle">
										<i class="fa fa-circle fa-lg text-{{ item.clr_class }}" 
											tooltip data-original-title="{{ item.dsc_status }}"></i>
										{{ item.dsc_status }}
									</td>
									<td class="text-middle text-center">
										{{ item.dsc_status_nfs }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<ul class="pagination pagination-sm m-top-none pull-right" ng-show="paginacao.ordens_servico.length > 1">
							<li ng-repeat="item in paginacao.ordens_servico" ng-class="{'active': item.current}">
								<a href="" ng-click="loadOrdensServicos(item.offset,item.limit)">{{ item.index }}</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div><!-- /main-container -->

		<!-- /Modal Clientes-->
        <div class="modal fade" id="list_clientes" style="display:none">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4>Clientes</span></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input ng-model="busca.clientes" ng-keyup="loadClientes(0,10)"  ng-enter="loadClientes(0,10)" type="text" class="form-control input-sm">
                                    <div class="input-group-btn">
                                        <button ng-click="loadClientes(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
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
                                    <thead ng-show="(clientes.length != 0)">
                                        <tr>
                                            <th>Nome</th>
                                            <th>Apelido</th>
                                            <th class="text-center">Perfil</th>
                                            <th class="text-center" colspan="2">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    	<tr ng-show="clientes != false && (clientes.length <= 0 || clientes == null)">
	                                        <td class="text-center text-bold" colspan="9">
	                                        	Aguarde, carregando <img src="assets/imagens/progresso_venda.gif">
                                    		</td>
	                                    </tr>
	                                    
	                                    <tr ng-show="clientes == false">
	                                        <td colspan="4" class="text-center">Não há resultados para a busca</td>
	                                    </tr>

                                        <tr ng-repeat="item in clientes">
                                            <td>{{ item.nome }}</td>
                                            <td>{{ item.apelido }}</td>
                                            <td class="text-center">{{ item.nome_perfil | uppercase }}</td>
                                            <td class="text-center" width="50">
                                                <button type="button" class="btn btn-xs {{ (item[modalSelectDestination+'_selected']) ? 'btn-primary' : 'btn-success' }}" 
                                                	ng-click="selectCliente(item)" ng-disabled="(item[modalSelectDestination+'_selected'])">
                                                    <i class="fa fa-check-square-o"></i>
                                                    {{ (item[modalSelectDestination+'_selected']) ? 'Selecionado' : 'Selecionar' }}
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao.clientes.length > 1">
                                    <li ng-repeat="item in paginacao.clientes" ng-class="{'active': item.current}">
                                        <a href="" h ng-click="loadClientes(item.offset,item.limit)">{{ item.index }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

        <!-- /Modal Servicos-->
        <div class="modal fade" id="list_servicos" style="display:none">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4>Serviços</span></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input ng-model="busca.servicos" ng-keyup="loadServicos(0,10)" ng-enter="loadServicos(0,10)" type="text" class="form-control input-sm">
                                    <div class="input-group-btn">
                                        <button ng-click="loadServicos(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
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
                                    <thead ng-show="(servicos.length != 0)">
                                        <tr>
                                            <th class="text-center">Código</th>
                                            <th>Descrição</th>
                                            <th class="text-center">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    	<tr ng-show="servicos != false && (servicos.length <= 0 || servicos == null)">
	                                        <td class="text-center text-bold" colspan="9">
	                                        	Aguarde, carregando <img src="assets/imagens/progresso_venda.gif">
	                                    	</td>
	                                    </tr>
	                                    <tr ng-show="servicos == false">
	                                        <td colspan="4" class="text-center">Não há resultados para a busca</td>
	                                    </tr>
                                        <tr ng-repeat="item in servicos">
                                            <td class="text-center">{{ item.cod_procedimento }}</td>
                                            <td>{{ item.dsc_procedimento }}</td>
                                            <td class="text-center" width="50">
                                                <button type="button" class="btn btn-xs {{ (item.selected) ? 'btn-primary' : 'btn-success' }}" 
                                                	ng-click="selectServico(item)" ng-disabled="(item.selected)">
                                                    <i class="fa fa-check-square-o"></i>
                                                    {{ (item.selected) ? 'Selecionado' : 'Selecionar' }}
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao.servicos.length > 1">
                                    <li ng-repeat="item in paginacao.servicos" ng-class="{'active': item.current}">
                                        <a href="" h ng-click="loadServicos(item.offset,item.limit)">{{ item.index }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

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
						            <input ng-model="busca.produtos" ng-keyup="loadProdutos(0,10)"  ng-enter="loadProdutos(0,10)" type="text" class="form-control input-sm">
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
											<th class="text-center">Código</th>
											<th>Nome</th>
											<th>Fabricante</th>
											<th class="text-center">Tamanho</th>
											<th class="text-center">Sabor/Cor</th>
											<th class="text-center">Quantidade</th>
											<th class="text-center">Ações</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-show="produtos != false && (produtos.length <= 0 || produtos == null)">
	                                        <td class="text-center text-bold" colspan="7">
	                                        	Aguarde, carregando <img src="assets/imagens/progresso_venda.gif">
	                                    	</td>
	                                    </tr>
	                                    <tr ng-show="produtos == false">
	                                        <td colspan="7" class="text-center">Não há resultados para a busca</td>
	                                    </tr>
										<tr ng-repeat="item in produtos">
											<td class="text-center">{{ item.id }}</td>
											<td>{{ item.nome_produto }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td class="text-center">{{ item.peso }}</td>
											<td class="text-center">{{ item.sabor }}</td>
											<td class="text-center" width="50">
												<input type="text" class="form-control input-xs"
													ng-model="item.qtd_pedido">
											</td>
											<td width="50" align="center">
												<button type="button" class="btn btn-xs {{ (item.selected) ? 'btn-primary' : 'btn-success' }}" 
                                                	ng-click="selectProduto(item)" ng-disabled="(item.selected)">
                                                    <i class="fa fa-check-square-o"></i>
                                                    {{ (item.selected) ? 'Selecionado' : 'Selecionar' }}
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

	<script src='js/agenda/lib/moment.min.js'></script>

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
	<script src="js/angular-controller/ordem_servico-controller.js?v=<?php echo filemtime('js/angular-controller/ordem_servico-controller.js') ?>"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/angular-strap/2.1.2/angular-strap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/angular-strap/2.1.2/angular-strap.tpl.min.js"></script>
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

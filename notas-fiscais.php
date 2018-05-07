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

		/*--------------------------------------*/
		.chosen-choices{
			border-bottom-left-radius: 4px;
			border-bottom-right-radius: 4px;
			border-top-left-radius: 4px;
			border-top-right-radius: 4px;
			font-size: 12px;
			border-color: #ccc;
		}
	</style>
  </head>

  <body class="overflow-hidden" ng-controller="NotasFiscaisController" ng-cloak>
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
					 <li><i class="fa fa-signal"></i> <a href="vendas.php">Vendas</a></li>
					 <li class="active"><i class="fa fa-barcode"></i> Notas Fiscais</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-barcode"></i> Notas Fiscais</h3>
				</div><!-- /page-title -->
			</div><!-- /main-header -->
			<div class="padding-md">
				<div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-filter"></i> Opções de Filtro</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label">Nº NF-e</label>
									<input ng-model="busca.numeroo" ng-enter="filtrar()" type="text" class="form-control input-sm ng-pristine ng-valid ng-touched">
								</div>
							</div>

							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label">Destinatário</label>
									<input ng-model="busca.text" type="text" class="form-control input-sm ng-pristine ng-valid ng-touched"
										 ng-enter="filtrar()" 
										 ng-keyup="filtrar()"
									>
								</div>
							</div>

							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label">Data de Emissão</label>
									<div class="input-group">
										<input id="inputDtaEmissao" readonly="readonly" style="background:#FFF;cursor:pointer" type="text" class="datepicker form-control input-sm">
										<span  id="btnDtaEmissao" class="input-group-addon"><i class="fa fa-calendar"></i></span>
									</div>
								</div>
							</div>

							<div class="col-sm-1">
								<div class="form-group">
									<div class="controls">
										<label class="control-label"><br></label>
									</div>
									<button type="button" class="btn btn-sm btn-primary" ng-click="filtrar()"><i class="fa fa-filter"></i> Filtrar</button>
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

				<div class="panel panel-default">
					<div class="panel-tab clearfix">
						<ul class="tab-bar">
							<li class="active"><a href="#list_nfe" data-toggle="tab"><i class="fa fa-file-text-o"></i> NF-e</a></li>
							<li><a href="#list_nfse" data-toggle="tab"><i class="fa fa-file-text-o"></i> NFS-e</a></li>
							<li><a href="#list_sat" data-toggle="tab"><i class="fa fa-file-text-o"></i> SAT</a></li>
							<li><a href="#list_nfce" data-toggle="tab"><i class="fa fa-file-text-o"></i> NFC-e</a></li>
						</ul>
					</div>
					<div class="panel-body">
						<div class="tab-content">
							<div class="tab-pane fade" id="list_nfe">
								<div class="panel-tab clearfix">
									<ul class="tab-bar">
										<li class="active">
											<a href="#emitidas_nfe" data-toggle="tab">
												<i class="fa fa-check-circle-o"></i> Emitidas
											</a>
										</li>
										<li>
											<a href="#canceladas_nfe" data-toggle="tab">
												<i class="fa fa-times-circle-o"></i> Canceladas
											</a>
										</li>
										<li>
											<a href="#inutilizadas_nfe" data-toggle="tab">
												<i class="fa fa-times-circle-o"></i> Inutilizadas
											</a>
										</li>
									</ul>
								</div>

								<div class="tab-content">
									<div class="tab-pane fade active in" id="emitidas_nfe">
										<br/>
										<div class="row">
											<div class="col-lg-12">
												<div class="table-responsive">
													<div class="alert alert-list-notas" style="display:none"></div>
													<table class="table table-bordered table-condensed table-striped table-hover">
														<thead>
															<th class="text-middle text-center" width="50"></th>
															<th class="text-middle text-center">Nº NF-e</th>
															<th class="text-middle">Destinatário</th>
															<th class="text-middle text-center">Data de Emissão</th>
															<th class="text-middle text-center">Valor da NF-e</th>
															<th class="text-middle text-center">Tipo NF-e</th>
															<th class="text-middle text-center">Operação</th>
															<th class="text-middle text-center">Status</th>
														</thead>
														<tbody>
															<tr ng-show="(!emitidas_nfe)">
																<td colspan="8" class="text-center text-middle">Nenhuma NF-e encontrada!</td>
															</tr>
															<tr ng-show="(emitidas_nfe.length == 0)">
																<td colspan="8" class="text-center text-middle"><i class="fa fa-refresh fa-spin"></i> Aguarde, carregando...</td>
															</tr>
															<tr bs-tooltip ng-repeat="nota in emitidas_nfe">
																<td class="text-middle">
																	<div class="btn-group">
																		<button type="button" class="btn btn-sm btn-default dropdown-toggle" 
																			data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																			Ações <span class="caret"></span>
																		</button>
																		<ul class="dropdown-menu">
																			<li ng-show="(nota.status == 'autorizado')|| (nota.status == 'processando_cancelamento')||(nota.status == 'erro_cancelamento')" 
																				ng-click="showDANFEModal(nota, 'PDF')">
																				<a href="">
																					<i class="fa fa-file-pdf-o"></i> Visualizar DANFE (PDF)
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'autorizado')|| (nota.status == 'processando_cancelamento')||(nota.status == 'erro_cancelamento')">
																				<a href="{{ nota.caminho_xml_nota_fiscal }}" target="_blank">
																					<i class="fa fa-file-code-o"></i> Visualizar DANFE (XML)
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'cancelado')|| (nota.status == 'processando_cancelamento')">
																				<a href="{{ nota.caminho_xml_cancelamento }}" target="_blank">
																					<i class="fa fa-file-code-o"></i> Visualizar XML de Cancelamento
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'processando_autorizacao') || (nota.status == 'processando_cancelamento')">
																				<a href="" target="_blank" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Atualizando" 
																					ng-click="atualzarStatus(nota.cod_nota_fiscal, $index, $event)">
																					<i class="fa fa-refresh"></i> Atualizar Status
																				</a>
																			</li>
																			<li role="separator" class="divider" 
																				ng-show="(nota.status == 'autorizado' || nota.status == 'processando_autorizacao')">
																			</li>
																			<li>
																				<a href="nota-fiscal.php?id_transferencia={{ nota.cod_transferencia }}" 
																					ng-show="nota.cod_transferencia">
																					<i class="fa fa-list-alt"></i> Visualizar Detalhes
																				</a>
																				<a href="nota-fiscal.php?id_venda={{ nota.cod_venda }}" 
																					ng-show="nota.cod_venda">
																					<i class="fa fa-list-alt"></i> Visualizar Detalhes
																				</a>
																				<a href="nota-fiscal-servico.php?id={{ nota.cod_ordem_servico }}" 
																					ng-show="nota.cod_ordem_servico">
																					<i class="fa fa-list-alt"></i> Visualizar Detalhes
																				</a>
																			</li>
																			<li ng-show="(nota.cod_venda && nota.status == 'autorizado')" 
																				ng-click="modalCorrecao(nota,$index)">
																				<a href="">
																					<i class="fa fa-edit"></i> Cartas de Correção
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'autorizado')" 
																				ng-click="modalCancelar(nota,$index)">
																				<a href="">
																					<i class="fa fa-times-circle"></i> Cancelar Nota
																				</a>
																			</li>
																		</ul>
																	</div>
																</td>
																<td class="text-center text-middle">{{ nota.numero }}</td>
																<td class="text-middle">{{ nota.nome_destinatario }}</td>
																<td class="text-center text-middle">{{ nota.data_emissao | date : 'dd/MM/yyyy' }}</td>
																<td class="text-right text-middle">
																	R$ {{ (nota.cod_ordem_servico != null) ? nota.valor_total_servicos : ((nota.cod_venda != null || nota.cod_transferencia != null) ? nota.valor_total : '') | numberFormat : 2 : ',' : '.' }}
																</td>
																<td class="text-center text-middle">
																	{{ (nota.cod_ordem_servico != null) ? 'Serviço' : ((nota.cod_venda != null || nota.cod_transferencia != null) ? 'Produtos' : '') }}
																</td>
																<td class="text-center text-middle">
																	<i class="fa fa-info-circle fa-2x" data-toggle="tooltip" title="{{ nota.natureza_operacao }}"
																		ng-show="(nota.cod_venda != null || nota.cod_transferencia != null)"></i>

																	<i class="fa fa-info-circle fa-2x" data-toggle="tooltip" title="{{ nota.issqn_descricao_servico_municipio }}"
																		ng-show="(nota.cod_ordem_servico != null)"></i>
																</td>
																<td class="text-middle text-center">
																	<span class="label label-success" ng-show="(nota.status == 'autorizado') || (nota.status == 'erro_cancelamento') || (nota.status == 'processando_cancelamento')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		NF-e Autorizada
																	</span>
																	<span class="label label-warning" ng-show="(nota.status == 'processando_autorizacao')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Processando Autorização
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'erro_autorizacao')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Erro na Autorização
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'erro_cancelamento')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz_cancelamento }}">
																		Erro no cancelamento
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'cancelado')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Cancelada
																	</span>
																	<span class="label label-warning" ng-show="(nota.status == 'processando_cancelamento')" 
																		data-toggle="tooltip" title="Atualize o status para ver o andamento">
																		Processando Cancelamento
																	</span>															
																</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="pull-right">
												<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.emitidas_nfe.length > 1">
													<li ng-repeat="item in paginacao.emitidas_nfe" ng-class="{'active': item.current}">
														<a href="" ng-click="loadNotas('NFE','emitidas_nfe','autorizado',item.offset,item.limit)">{{ item.index }}</a>
													</li>
												</ul>
											</div>
										</div>
									</div>
									<div class="tab-pane fade" id="canceladas_nfe">
										<br/>
										<div class="row">
											<div class="col-lg-12">
												<div class="table-responsive">
													<div class="alert alert-list-notas" style="display:none"></div>
													<table class="table table-bordered table-condensed table-striped table-hover">
														<thead>
															<th class="text-middle text-center" width="50"></th>
															<th class="text-middle text-center">Nº NF-e</th>
															<th class="text-middle">Destinatário</th>
															<th class="text-middle text-center">Data de Emissão</th>
															<th class="text-middle text-center">Valor da NF-e</th>
															<th class="text-middle text-center">Tipo NF-e</th>
															<th class="text-middle text-center">Operação</th>
															<th class="text-middle text-center">Status</th>
														</thead>
														<tbody>
															<tr ng-show="(!canceladas_nfe)">
																<td colspan="8" class="text-center text-middle">Nenhuma NF-e encontrada!</td>
															</tr>
															<tr ng-show="(canceladas_nfe.length == 0)">
																<td colspan="8" class="text-center text-middle"><i class="fa fa-refresh fa-spin"></i> Aguarde, carregando...</td>
															</tr>
															<tr bs-tooltip ng-repeat="nota in canceladas_nfe">
																<td class="text-middle">
																	<div class="btn-group">
																		<button type="button" class="btn btn-sm btn-default dropdown-toggle" 
																			data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																			Ações <span class="caret"></span>
																		</button>
																		<ul class="dropdown-menu">
																			<li ng-show="(nota.status == 'autorizado')|| (nota.status == 'processando_cancelamento')||(nota.status == 'erro_cancelamento')" 
																				ng-click="showDANFEModal(nota, 'PDF')">
																				<a href="">
																					<i class="fa fa-file-pdf-o"></i> Visualizar DANFE (PDF)
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'autorizado')|| (nota.status == 'processando_cancelamento')||(nota.status == 'erro_cancelamento')">
																				<a href="{{ nota.caminho_xml_nota_fiscal }}" target="_blank">
																					<i class="fa fa-file-code-o"></i> Visualizar DANFE (XML)
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'cancelado')|| (nota.status == 'processando_cancelamento')">
																				<a href="{{ nota.caminho_xml_cancelamento }}" target="_blank">
																					<i class="fa fa-file-code-o"></i> Visualizar XML de Cancelamento
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'processando_autorizacao') || (nota.status == 'processando_cancelamento')">
																				<a href="" target="_blank" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Atualizando" 
																					ng-click="atualzarStatus(nota.cod_nota_fiscal, $index, $event)">
																					<i class="fa fa-refresh"></i> Atualizar Status
																				</a>
																			</li>
																			<li role="separator" class="divider" 
																				ng-show="(nota.status == 'autorizado' || nota.status == 'processando_autorizacao')">
																			</li>
																			<li>
																				<a href="nota-fiscal.php?id_venda={{ nota.cod_venda }}" 
																					ng-show="nota.cod_venda">
																					<i class="fa fa-list-alt"></i> Visualizar Detalhes
																				</a>
																				<a href="nota-fiscal-servico.php?id={{ nota.cod_ordem_servico }}" 
																					ng-show="nota.cod_ordem_servico">
																					<i class="fa fa-list-alt"></i> Visualizar Detalhes
																				</a>
																			</li>
																			<li ng-show="(nota.cod_venda && nota.status == 'autorizado')" 
																				ng-click="modalCorrecao(nota,$index)">
																				<a href="">
																					<i class="fa fa-edit"></i> Cartas de Correção
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'autorizado')" 
																				ng-click="modalCancelar(nota,$index)">
																				<a href="">
																					<i class="fa fa-times-circle"></i> Cancelar Nota
																				</a>
																			</li>
																		</ul>
																	</div>
																</td>
																<td class="text-center text-middle">{{ nota.numero }}</td>
																<td class="text-middle">{{ nota.nome_destinatario }}</td>
																<td class="text-center text-middle">{{ nota.data_emissao | date : 'dd/MM/yyyy' }}</td>
																<td class="text-right text-middle">
																	R$ {{ (nota.cod_ordem_servico != null) ? nota.valor_total_servicos : ((nota.cod_venda != null || nota.cod_transferencia != null) ? nota.valor_total : '') | numberFormat : 2 : ',' : '.' }}
																</td>
																<td class="text-center text-middle">
																	{{ (nota.cod_ordem_servico != null) ? 'Serviço' : ((nota.cod_venda != null || nota.cod_transferencia != null) ? 'Produtos' : '') }}
																</td>
																<td class="text-center text-middle">
																	<i class="fa fa-info-circle fa-2x" data-toggle="tooltip" title="{{ nota.natureza_operacao }}"
																		ng-show="(nota.cod_venda != null || nota.cod_transferencia != null)"></i>

																	<i class="fa fa-info-circle fa-2x" data-toggle="tooltip" title="{{ nota.issqn_descricao_servico_municipio }}"
																		ng-show="(nota.cod_ordem_servico != null)"></i>
																</td>
																<td class="text-middle text-center">
																	<span class="label label-success" ng-show="(nota.status == 'autorizado') || (nota.status == 'erro_cancelamento') || (nota.status == 'processando_cancelamento')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		NF-e Autorizada
																	</span>
																	<span class="label label-warning" ng-show="(nota.status == 'processando_autorizacao')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Processando Autorização
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'erro_autorizacao')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Erro na Autorização
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'erro_cancelamento')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz_cancelamento }}">
																		Erro no cancelamento
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'cancelado')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Cancelada
																	</span>
																	<span class="label label-warning" ng-show="(nota.status == 'processando_cancelamento')" 
																		data-toggle="tooltip" title="Atualize o status para ver o andamento">
																		Processando Cancelamento
																	</span>															
																</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="pull-right">
												<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.canceladas_nfe.length > 1">
													<li ng-repeat="item in paginacao.canceladas_nfe" ng-class="{'active': item.current}">
														<a href="" ng-click="loadNotas('NFE','canceladas_nfe','cancelado',item.offset,item.limit)">{{ item.index }}</a>
													</li>
												</ul>
											</div>
										</div>
									</div>
									<div class="tab-pane fade" id="inutilizadas_nfe">
										<br/>
										<div class="row">
											<div class="col-sm-12">
												<button class="btn btn-info btn-sm" ng-click="modalInutilizacao()">
													<i class="fa fa-plus-circle"></i>
													 Nova Inutilização
												</button>
											</div>
										</div>
										<hr>
										<div class="row">
											<div class="col-lg-12">
												<div class="table-responsive">
													<div class="alert alert-list-notas" style="display:none"></div>
													<table class="table table-bordered table-condensed table-striped table-hover">
														<thead>
															<th class="text-middle text-center" width="50"></th>
															<th class="text-middle text-center">ID</th>
															<th class="text-middle text-center">Data</th>
															<th class="text-middle text-center">Número Inicial</th>
															<th class="text-middle text-center">Número Final</th>
															<th class="text-middle text-center">Status</th>
														</thead>
														<tbody>
															<tr ng-show="(!inutilizacoes)">
																<td colspan="8" class="text-center text-middle">Nenhuma Inutilização encontrada!</td>
															</tr>
															<tr ng-show="(inutilizacoes.length == 0)">
																<td colspan="8" class="text-center text-middle"><i class="fa fa-refresh fa-spin"></i> Aguarde, carregando...</td>
															</tr>
															<tr bs-tooltip ng-repeat="nota in inutilizacoes">
																<td class="text-middle">
																	<div class="btn-group">
																		<button type="button" class="btn btn-sm btn-default dropdown-toggle" 
																			data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																			Ações <span class="caret"></span>
																		</button>
																		<ul class="dropdown-menu">
																			<li>
																				<a href="{{ nota.caminho_xml }}" target="_blank">
																					<i class="fa fa-file-code-o"></i> Visualizar XML
																				</a>
																			</li>
																		</ul>
																	</div>
																</td>
																<td class="text-center text-middle">{{ nota.id }}</td>
																<td class="text-center text-middle">{{ nota.dta_registro }}</td>
																<td class="text-center text-middle">{{ nota.num_inicial }}</td>
																<td class="text-center text-middle">{{ nota.num_final }}</td>
																<td class="text-middle text-center">
																	<span class="label label-success" ng-show="(nota.status == 'autorizado') || (nota.status == 'erro_cancelamento') || (nota.status == 'processando_cancelamento')"
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Inutilização Autorizada
																	</span>
																</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="pull-right">
												<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.canceladas_nfe.length > 1">
													<li ng-repeat="item in paginacao.canceladas_nfe" ng-class="{'active': item.current}">
														<a href="" ng-click="loadNotas('NFE','canceladas_nfe','cancelado',item.offset,item.limit)">{{ item.index }}</a>
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="list_nfse">
								<div class="panel-tab clearfix">
									<ul class="tab-bar">
										<li class="active">
											<a href="#emitidas_nfse" data-toggle="tab">
												<i class="fa fa-check-circle-o"></i> Emitidas
											</a>
										</li>
										<li>
											<a href="#canceladas_nfse" data-toggle="tab">
												<i class="fa fa-times-circle-o"></i> Canceladas
											</a>
										</li>
									</ul>
								</div>
								<div class="tab-content">
									<div class="tab-pane fade active in" id="emitidas_nfse">
										<br/>
										<div class="row">
											<div class="col-lg-12">
												<div class="table-responsive">
													<div class="alert alert-list-notas" style="display:none"></div>
													<table class="table table-bordered table-condensed table-striped table-hover">
														<thead>
															<th class="text-middle text-center" width="50"></th>
															<th class="text-middle text-center">Nº NF-e</th>
															<th class="text-middle">Destinatário</th>
															<th class="text-middle text-center">Data de Emissão</th>
															<th class="text-middle text-center">Valor da NF-e</th>
															<th class="text-middle text-center">Tipo NF-e</th>
															<th class="text-middle text-center">Operação</th>
															<th class="text-middle text-center">Status</th>
														</thead>
														<tbody>
															<tr ng-show="(!emitidas_nfse)">
																<td colspan="8" class="text-center text-middle">Nenhuma NF-e encontrada!</td>
															</tr>
															<tr ng-show="(emitidas_nfse.length == 0)">
																<td colspan="8" class="text-center text-middle"><i class="fa fa-refresh fa-spin"></i> Aguarde, carregando...</td>
															</tr>
															<tr bs-tooltip ng-repeat="nota in emitidas_nfse">
																<td class="text-middle">
																	<div class="btn-group">
																		<button type="button" class="btn btn-sm btn-default dropdown-toggle" 
																			data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																			Ações <span class="caret"></span>
																		</button>
																		<ul class="dropdown-menu">
																			<li ng-show="(nota.status == 'autorizado')|| (nota.status == 'processando_cancelamento')||(nota.status == 'erro_cancelamento')" 
																				ng-click="showDANFEModal(nota, 'PDF')">
																				<a href="">
																					<i class="fa fa-file-pdf-o"></i> Visualizar DANFE (PDF)
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'autorizado')|| (nota.status == 'processando_cancelamento')||(nota.status == 'erro_cancelamento')">
																				<a href="{{ nota.caminho_xml_nota_fiscal }}" target="_blank">
																					<i class="fa fa-file-code-o"></i> Visualizar DANFE (XML)
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'cancelado')|| (nota.status == 'processando_cancelamento')">
																				<a href="{{ nota.caminho_xml_cancelamento }}" target="_blank">
																					<i class="fa fa-file-code-o"></i> Visualizar XML de Cancelamento
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'processando_autorizacao') || (nota.status == 'processando_cancelamento')">
																				<a href="" target="_blank" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Atualizando" 
																					ng-click="atualzarStatus(nota.cod_nota_fiscal, $index, $event)">
																					<i class="fa fa-refresh"></i> Atualizar Status
																				</a>
																			</li>
																			<li role="separator" class="divider" 
																				ng-show="(nota.status == 'autorizado' || nota.status == 'processando_autorizacao')">
																			</li>
																			<li>
																				<a href="nota-fiscal.php?id_venda={{ nota.cod_venda }}" 
																					ng-show="nota.cod_venda">
																					<i class="fa fa-list-alt"></i> Visualizar Detalhes
																				</a>
																				<a href="nota-fiscal-servico.php?id={{ nota.cod_ordem_servico }}" 
																					ng-show="nota.cod_ordem_servico">
																					<i class="fa fa-list-alt"></i> Visualizar Detalhes
																				</a>
																			</li>
																			<li ng-show="(nota.cod_venda && nota.status == 'autorizado')" 
																				ng-click="modalCorrecao(nota,$index)">
																				<a href="">
																					<i class="fa fa-edit"></i> Cartas de Correção
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'autorizado')" 
																				ng-click="modalCancelar(nota,$index)">
																				<a href="">
																					<i class="fa fa-times-circle"></i> Cancelar Nota
																				</a>
																			</li>
																		</ul>
																	</div>
																</td>
																<td class="text-center text-middle">{{ nota.numero }}</td>
																<td class="text-middle">{{ nota.nome_destinatario }}</td>
																<td class="text-center text-middle">{{ nota.data_emissao | date : 'dd/MM/yyyy' }}</td>
																<td class="text-right text-middle">
																	R$ {{ (nota.cod_ordem_servico != null) ? nota.valor_total_servicos : ((nota.cod_venda != null || nota.cod_transferencia != null) ? nota.valor_total : '') | numberFormat : 2 : ',' : '.' }}
																</td>
																<td class="text-center text-middle">
																	{{ (nota.cod_ordem_servico != null) ? 'Serviço' : ((nota.cod_venda != null || nota.cod_transferencia != null) ? 'Produtos' : '') }}
																</td>
																<td class="text-center text-middle">
																	<i class="fa fa-info-circle fa-2x" data-toggle="tooltip" title="{{ nota.natureza_operacao }}"
																		ng-show="(nota.cod_venda != null || nota.cod_transferencia != null)"></i>

																	<i class="fa fa-info-circle fa-2x" data-toggle="tooltip" title="{{ nota.issqn_descricao_servico_municipio }}"
																		ng-show="(nota.cod_ordem_servico != null)"></i>
																</td>
																<td class="text-middle text-center">
																	<span class="label label-success" ng-show="(nota.status == 'autorizado') || (nota.status == 'erro_cancelamento') || (nota.status == 'processando_cancelamento')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		NF-e Autorizada
																	</span>
																	<span class="label label-warning" ng-show="(nota.status == 'processando_autorizacao')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Processando Autorização
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'erro_autorizacao')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Erro na Autorização
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'erro_cancelamento')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz_cancelamento }}">
																		Erro no cancelamento
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'cancelado')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Cancelada
																	</span>
																	<span class="label label-warning" ng-show="(nota.status == 'processando_cancelamento')" 
																		data-toggle="tooltip" title="Atualize o status para ver o andamento">
																		Processando Cancelamento
																	</span>															
																</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="pull-right">
												<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.emitidas_nfse.length > 1">
													<li ng-repeat="item in paginacao.emitidas_nfse" ng-class="{'active': item.current}">
														<a href="" ng-click="loadNotas('NFSE','emitidas_nfse','autorizado',item.offset,item.limit)">{{ item.index }}</a>
													</li>
												</ul>
											</div>
										</div>
									</div>
									<div class="tab-pane fade" id="canceladas_nfse">
										<br/>
										<div class="row">
											<div class="col-lg-12">
												<div class="table-responsive">
													<div class="alert alert-list-notas" style="display:none"></div>
													<table class="table table-bordered table-condensed table-striped table-hover">
														<thead>
															<th class="text-middle text-center" width="50"></th>
															<th class="text-middle text-center">Nº NF-e</th>
															<th class="text-middle">Destinatário</th>
															<th class="text-middle text-center">Data de Emissão</th>
															<th class="text-middle text-center">Valor da NF-e</th>
															<th class="text-middle text-center">Tipo NF-e</th>
															<th class="text-middle text-center">Operação</th>
															<th class="text-middle text-center">Status</th>
														</thead>
														<tbody>
															<tr ng-show="(!canceladas_nfse)">
																<td colspan="8" class="text-center text-middle">Nenhuma NF-e encontrada!</td>
															</tr>
															<tr ng-show="(canceladas_nfse.length == 0)">
																<td colspan="8" class="text-center text-middle"><i class="fa fa-refresh fa-spin"></i> Aguarde, carregando...</td>
															</tr>
															<tr bs-tooltip ng-repeat="nota in canceladas_nfse">
																<td class="text-middle">
																	<div class="btn-group">
																		<button type="button" class="btn btn-sm btn-default dropdown-toggle" 
																			data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																			Ações <span class="caret"></span>
																		</button>
																		<ul class="dropdown-menu">
																			<li ng-show="(nota.status == 'autorizado')|| (nota.status == 'processando_cancelamento')||(nota.status == 'erro_cancelamento')" 
																				ng-click="showDANFEModal(nota, 'PDF')">
																				<a href="">
																					<i class="fa fa-file-pdf-o"></i> Visualizar DANFE (PDF)
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'autorizado')|| (nota.status == 'processando_cancelamento')||(nota.status == 'erro_cancelamento')">
																				<a href="{{ nota.caminho_xml_nota_fiscal }}" target="_blank">
																					<i class="fa fa-file-code-o"></i> Visualizar DANFE (XML)
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'cancelado')|| (nota.status == 'processando_cancelamento')">
																				<a href="{{ nota.caminho_xml_cancelamento }}" target="_blank">
																					<i class="fa fa-file-code-o"></i> Visualizar XML de Cancelamento
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'processando_autorizacao') || (nota.status == 'processando_cancelamento')">
																				<a href="" target="_blank" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Atualizando" 
																					ng-click="atualzarStatus(nota.cod_nota_fiscal, $index, $event)">
																					<i class="fa fa-refresh"></i> Atualizar Status
																				</a>
																			</li>
																			<li role="separator" class="divider" 
																				ng-show="(nota.status == 'autorizado' || nota.status == 'processando_autorizacao')">
																			</li>
																			<li>
																				<a href="nota-fiscal.php?id_venda={{ nota.cod_venda }}" 
																					ng-show="nota.cod_venda">
																					<i class="fa fa-list-alt"></i> Visualizar Detalhes
																				</a>
																				<a href="nota-fiscal-servico.php?id={{ nota.cod_ordem_servico }}" 
																					ng-show="nota.cod_ordem_servico">
																					<i class="fa fa-list-alt"></i> Visualizar Detalhes
																				</a>
																			</li>
																			<li ng-show="(nota.cod_venda && nota.status == 'autorizado')" 
																				ng-click="modalCorrecao(nota,$index)">
																				<a href="">
																					<i class="fa fa-edit"></i> Cartas de Correção
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'autorizado')" 
																				ng-click="modalCancelar(nota,$index)">
																				<a href="">
																					<i class="fa fa-times-circle"></i> Cancelar Nota
																				</a>
																			</li>
																		</ul>
																	</div>
																</td>
																<td class="text-center text-middle">{{ nota.numero }}</td>
																<td class="text-middle">{{ nota.nome_destinatario }}</td>
																<td class="text-center text-middle">{{ nota.data_emissao | date : 'dd/MM/yyyy' }}</td>
																<td class="text-right text-middle">
																	R$ {{ (nota.cod_ordem_servico != null) ? nota.valor_total_servicos : ((nota.cod_venda != null || nota.cod_transferencia != null) ? nota.valor_total : '') | numberFormat : 2 : ',' : '.' }}
																</td>
																<td class="text-center text-middle">
																	{{ (nota.cod_ordem_servico != null) ? 'Serviço' : ((nota.cod_venda != null || nota.cod_transferencia != null) ? 'Produtos' : '') }}
																</td>
																<td class="text-center text-middle">
																	<i class="fa fa-info-circle fa-2x" data-toggle="tooltip" title="{{ nota.natureza_operacao }}"
																		ng-show="(nota.cod_venda != null || nota.cod_transferencia != null)"></i>

																	<i class="fa fa-info-circle fa-2x" data-toggle="tooltip" title="{{ nota.issqn_descricao_servico_municipio }}"
																		ng-show="(nota.cod_ordem_servico != null)"></i>
																</td>
																<td class="text-middle text-center">
																	<span class="label label-success" ng-show="(nota.status == 'autorizado') || (nota.status == 'erro_cancelamento') || (nota.status == 'processando_cancelamento')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		NF-e Autorizada
																	</span>
																	<span class="label label-warning" ng-show="(nota.status == 'processando_autorizacao')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Processando Autorização
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'erro_autorizacao')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Erro na Autorização
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'erro_cancelamento')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz_cancelamento }}">
																		Erro no cancelamento
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'cancelado')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Cancelada
																	</span>
																	<span class="label label-warning" ng-show="(nota.status == 'processando_cancelamento')" 
																		data-toggle="tooltip" title="Atualize o status para ver o andamento">
																		Processando Cancelamento
																	</span>															
																</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="pull-right">
												<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.canceladas_nfse.length > 1">
													<li ng-repeat="item in paginacao.canceladas_nfse" ng-class="{'active': item.current}">
														<a href="" ng-click="loadNotas('NFSE','canceladas_nfse','cancelado',item.offset,item.limit)">{{ item.index }}</a>
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="list_sat">
								<div class="panel-tab clearfix">
									<ul class="tab-bar">
										<li class="active">
											<a href="#emitidas_sat" data-toggle="tab">
												<i class="fa fa-check-circle-o"></i> Emitidas
											</a>
										</li>
										<li>
											<a href="#canceladas_sat" data-toggle="tab">
												<i class="fa fa-times-circle-o"></i> Canceladas
											</a>
										</li>
									</ul>
								</div>
								<div class="tab-content">
									<div class="tab-pane fade active in" id="emitidas_sat">
										<br/>
										<div class="row">
											<div class="col-lg-12">
												<div class="table-responsive">
													<div class="alert alert-list-notas" style="display:none"></div>
													<table class="table table-bordered table-condensed table-striped table-hover">
														<thead>
															<th class="text-middle text-center" width="50"></th>
															<th class="text-middle text-center">Nº NF-e</th>
															<th class="text-middle">Destinatário</th>
															<th class="text-middle text-center">Data de Emissão</th>
															<th class="text-middle text-center">Valor da NF-e</th>
															<th class="text-middle text-center">Tipo NF-e</th>
															<th class="text-middle text-center">Operação</th>
															<th class="text-middle text-center">Status</th>
														</thead>
														<tbody>
															<tr ng-show="(!emitidas_sat)">
																<td colspan="8" class="text-center text-middle">Nenhuma NF-e encontrada!</td>
															</tr>
															<tr ng-show="(emitidas_sat.length == 0)">
																<td colspan="8" class="text-center text-middle"><i class="fa fa-refresh fa-spin"></i> Aguarde, carregando...</td>
															</tr>
															<tr bs-tooltip ng-repeat="nota in emitidas_sat">
																<td class="text-middle">
																	<div class="btn-group">
																		<button type="button" class="btn btn-sm btn-default dropdown-toggle" 
																			data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																			Ações <span class="caret"></span>
																		</button>
																		<ul class="dropdown-menu">
																			<li ng-show="(nota.status == 'autorizado')|| (nota.status == 'processando_cancelamento')||(nota.status == 'erro_cancelamento')" 
																				ng-click="showDANFEModal(nota, 'PDF')">
																				<a href="">
																					<i class="fa fa-file-pdf-o"></i> Visualizar DANFE (PDF)
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'autorizado')|| (nota.status == 'processando_cancelamento')||(nota.status == 'erro_cancelamento')">
																				<a href="{{ nota.caminho_xml_nota_fiscal }}" target="_blank">
																					<i class="fa fa-file-code-o"></i> Visualizar DANFE (XML)
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'cancelado')|| (nota.status == 'processando_cancelamento')">
																				<a href="{{ nota.caminho_xml_cancelamento }}" target="_blank">
																					<i class="fa fa-file-code-o"></i> Visualizar XML de Cancelamento
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'processando_autorizacao') || (nota.status == 'processando_cancelamento')">
																				<a href="" target="_blank" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Atualizando" 
																					ng-click="atualzarStatus(nota.cod_nota_fiscal, $index, $event)">
																					<i class="fa fa-refresh"></i> Atualizar Status
																				</a>
																			</li>
																			<li role="separator" class="divider" 
																				ng-show="(nota.status == 'autorizado' || nota.status == 'processando_autorizacao')">
																			</li>
																			<li>
																				<a href="nota-fiscal.php?id_venda={{ nota.cod_venda }}" 
																					ng-show="nota.cod_venda">
																					<i class="fa fa-list-alt"></i> Visualizar Detalhes
																				</a>
																				<a href="nota-fiscal-servico.php?id={{ nota.cod_ordem_servico }}" 
																					ng-show="nota.cod_ordem_servico">
																					<i class="fa fa-list-alt"></i> Visualizar Detalhes
																				</a>
																			</li>
																			<li ng-show="(nota.cod_venda && nota.status == 'autorizado')" 
																				ng-click="modalCorrecao(nota,$index)">
																				<a href="">
																					<i class="fa fa-edit"></i> Cartas de Correção
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'autorizado')" 
																				ng-click="modalCancelar(nota,$index)">
																				<a href="">
																					<i class="fa fa-times-circle"></i> Cancelar Nota
																				</a>
																			</li>
																		</ul>
																	</div>
																</td>
																<td class="text-center text-middle">{{ nota.numero }}</td>
																<td class="text-middle">{{ nota.nome_destinatario }}</td>
																<td class="text-center text-middle">{{ nota.data_emissao | date : 'dd/MM/yyyy' }}</td>
																<td class="text-right text-middle">
																	R$ {{ (nota.cod_ordem_servico != null) ? nota.valor_total_servicos : ((nota.cod_venda != null || nota.cod_transferencia != null) ? nota.valor_total : '') | numberFormat : 2 : ',' : '.' }}
																</td>
																<td class="text-center text-middle">
																	{{ (nota.cod_ordem_servico != null) ? 'Serviço' : ((nota.cod_venda != null || nota.cod_transferencia != null) ? 'Produtos' : '') }}
																</td>
																<td class="text-center text-middle">
																	<i class="fa fa-info-circle fa-2x" data-toggle="tooltip" title="{{ nota.natureza_operacao }}"
																		ng-show="(nota.cod_venda != null || nota.cod_transferencia != null)"></i>

																	<i class="fa fa-info-circle fa-2x" data-toggle="tooltip" title="{{ nota.issqn_descricao_servico_municipio }}"
																		ng-show="(nota.cod_ordem_servico != null)"></i>
																</td>
																<td class="text-middle text-center">
																	<span class="label label-success" ng-show="(nota.status == 'autorizado') || (nota.status == 'erro_cancelamento') || (nota.status == 'processando_cancelamento')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		NF-e Autorizada
																	</span>
																	<span class="label label-warning" ng-show="(nota.status == 'processando_autorizacao')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Processando Autorização
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'erro_autorizacao')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Erro na Autorização
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'erro_cancelamento')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz_cancelamento }}">
																		Erro no cancelamento
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'cancelado')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Cancelada
																	</span>
																	<span class="label label-warning" ng-show="(nota.status == 'processando_cancelamento')" 
																		data-toggle="tooltip" title="Atualize o status para ver o andamento">
																		Processando Cancelamento
																	</span>															
																</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="pull-right">
												<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.emitidas_sat.length > 1">
													<li ng-repeat="item in paginacao.emitidas_sat" ng-class="{'active': item.current}">
														<a href="" ng-click="loadNotas('SAT','emitidas_sat','autorizado',item.offset,item.limit)">{{ item.index }}</a>
													</li>
												</ul>
											</div>
										</div>
									</div>
									<div class="tab-pane fade" id="canceladas_sat">
										<br/>
										<div class="row">
											<div class="col-lg-12">
												<div class="table-responsive">
													<div class="alert alert-list-notas" style="display:none"></div>
													<table class="table table-bordered table-condensed table-striped table-hover">
														<thead>
															<th class="text-middle text-center" width="50"></th>
															<th class="text-middle text-center">Nº NF-e</th>
															<th class="text-middle">Destinatário</th>
															<th class="text-middle text-center">Data de Emissão</th>
															<th class="text-middle text-center">Valor da NF-e</th>
															<th class="text-middle text-center">Tipo NF-e</th>
															<th class="text-middle text-center">Operação</th>
															<th class="text-middle text-center">Status</th>
														</thead>
														<tbody>
															<tr ng-show="(!canceladas_sat)">
																<td colspan="8" class="text-center text-middle">Nenhuma NF-e encontrada!</td>
															</tr>
															<tr ng-show="(canceladas_sat.length == 0)">
																<td colspan="8" class="text-center text-middle"><i class="fa fa-refresh fa-spin"></i> Aguarde, carregando...</td>
															</tr>
															<tr bs-tooltip ng-repeat="nota in canceladas_sat">
																<td class="text-middle">
																	<div class="btn-group">
																		<button type="button" class="btn btn-sm btn-default dropdown-toggle" 
																			data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																			Ações <span class="caret"></span>
																		</button>
																		<ul class="dropdown-menu">
																			<li ng-show="(nota.status == 'autorizado')|| (nota.status == 'processando_cancelamento')||(nota.status == 'erro_cancelamento')" 
																				ng-click="showDANFEModal(nota, 'PDF')">
																				<a href="">
																					<i class="fa fa-file-pdf-o"></i> Visualizar DANFE (PDF)
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'autorizado')|| (nota.status == 'processando_cancelamento')||(nota.status == 'erro_cancelamento')">
																				<a href="{{ nota.caminho_xml_nota_fiscal }}" target="_blank">
																					<i class="fa fa-file-code-o"></i> Visualizar DANFE (XML)
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'cancelado')|| (nota.status == 'processando_cancelamento')">
																				<a href="{{ nota.caminho_xml_cancelamento }}" target="_blank">
																					<i class="fa fa-file-code-o"></i> Visualizar XML de Cancelamento
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'processando_autorizacao') || (nota.status == 'processando_cancelamento')">
																				<a href="" target="_blank" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Atualizando" 
																					ng-click="atualzarStatus(nota.cod_nota_fiscal, $index, $event)">
																					<i class="fa fa-refresh"></i> Atualizar Status
																				</a>
																			</li>
																			<li role="separator" class="divider" 
																				ng-show="(nota.status == 'autorizado' || nota.status == 'processando_autorizacao')">
																			</li>
																			<li>
																				<a href="nota-fiscal.php?id_venda={{ nota.cod_venda }}" 
																					ng-show="nota.cod_venda">
																					<i class="fa fa-list-alt"></i> Visualizar Detalhes
																				</a>
																				<a href="nota-fiscal-servico.php?id={{ nota.cod_ordem_servico }}" 
																					ng-show="nota.cod_ordem_servico">
																					<i class="fa fa-list-alt"></i> Visualizar Detalhes
																				</a>
																			</li>
																			<li ng-show="(nota.cod_venda && nota.status == 'autorizado')" 
																				ng-click="modalCorrecao(nota,$index)">
																				<a href="">
																					<i class="fa fa-edit"></i> Cartas de Correção
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'autorizado')" 
																				ng-click="modalCancelar(nota,$index)">
																				<a href="">
																					<i class="fa fa-times-circle"></i> Cancelar Nota
																				</a>
																			</li>
																		</ul>
																	</div>
																</td>
																<td class="text-center text-middle">{{ nota.numero }}</td>
																<td class="text-middle">{{ nota.nome_destinatario }}</td>
																<td class="text-center text-middle">{{ nota.data_emissao | date : 'dd/MM/yyyy' }}</td>
																<td class="text-right text-middle">
																	R$ {{ (nota.cod_ordem_servico != null) ? nota.valor_total_servicos : ((nota.cod_venda != null || nota.cod_transferencia != null) ? nota.valor_total : '') | numberFormat : 2 : ',' : '.' }}
																</td>
																<td class="text-center text-middle">
																	{{ (nota.cod_ordem_servico != null) ? 'Serviço' : ((nota.cod_venda != null || nota.cod_transferencia != null) ? 'Produtos' : '') }}
																</td>
																<td class="text-center text-middle">
																	<i class="fa fa-info-circle fa-2x" data-toggle="tooltip" title="{{ nota.natureza_operacao }}"
																		ng-show="(nota.cod_venda != null || nota.cod_transferencia != null)"></i>

																	<i class="fa fa-info-circle fa-2x" data-toggle="tooltip" title="{{ nota.issqn_descricao_servico_municipio }}"
																		ng-show="(nota.cod_ordem_servico != null)"></i>
																</td>
																<td class="text-middle text-center">
																	<span class="label label-success" ng-show="(nota.status == 'autorizado') || (nota.status == 'erro_cancelamento') || (nota.status == 'processando_cancelamento')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		NF-e Autorizada
																	</span>
																	<span class="label label-warning" ng-show="(nota.status == 'processando_autorizacao')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Processando Autorização
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'erro_autorizacao')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Erro na Autorização
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'erro_cancelamento')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz_cancelamento }}">
																		Erro no cancelamento
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'cancelado')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Cancelada
																	</span>
																	<span class="label label-warning" ng-show="(nota.status == 'processando_cancelamento')" 
																		data-toggle="tooltip" title="Atualize o status para ver o andamento">
																		Processando Cancelamento
																	</span>															
																</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="pull-right">
												<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.canceladas_sat.length > 1">
													<li ng-repeat="item in paginacao.canceladas_sat" ng-class="{'active': item.current}">
														<a href="" ng-click="loadNotas('SAT','canceladas_sat','cancelado',item.offset,item.limit)">{{ item.index }}</a>
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="list_nfce">
								<div class="panel-tab clearfix">
									<ul class="tab-bar">
										<li class="active">
											<a href="#emitidas_nfce" data-toggle="tab">
												<i class="fa fa-check-circle-o"></i> Emitidas
											</a>
										</li>
										<li>
											<a href="#canceladas_nfce" data-toggle="tab">
												<i class="fa fa-times-circle-o"></i> Canceladas
											</a>
										</li>
									</ul>
								</div>
								<div class="tab-content">
									<div class="tab-pane fade active in" id="emitidas_nfce">
										<br/>
										<div class="row">
											<div class="col-lg-12">
												<div class="table-responsive">
													<div class="alert alert-list-notas" style="display:none"></div>
													<table class="table table-bordered table-condensed table-striped table-hover">
														<thead>
															<th class="text-middle text-center" width="50"></th>
															<th class="text-middle text-center">Nº NF-e</th>
															<th class="text-middle">Destinatário</th>
															<th class="text-middle text-center">Data de Emissão</th>
															<th class="text-middle text-center">Valor da NF-e</th>
															<th class="text-middle text-center">Tipo NF-e</th>
															<th class="text-middle text-center">Operação</th>
															<th class="text-middle text-center">Status</th>
														</thead>
														<tbody>
															<tr ng-show="(!emitidas_nfce)">
																<td colspan="8" class="text-center text-middle">Nenhuma NF-e encontrada!</td>
															</tr>
															<tr ng-show="(emitidas_nfce.length == 0)">
																<td colspan="8" class="text-center text-middle"><i class="fa fa-refresh fa-spin"></i> Aguarde, carregando...</td>
															</tr>
															<tr bs-tooltip ng-repeat="nota in emitidas_nfce">
																<td class="text-middle">
																	<div class="btn-group">
																		<button type="button" class="btn btn-sm btn-default dropdown-toggle" 
																			data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																			Ações <span class="caret"></span>
																		</button>
																		<ul class="dropdown-menu">
																			<li ng-show="(nota.status == 'autorizado')|| (nota.status == 'processando_cancelamento')||(nota.status == 'erro_cancelamento')" 
																				ng-click="showDANFEModal(nota, 'PDF')">
																				<a href="">
																					<i class="fa fa-file-pdf-o"></i> Visualizar DANFE (PDF)
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'autorizado')|| (nota.status == 'processando_cancelamento')||(nota.status == 'erro_cancelamento')">
																				<a href="{{ nota.caminho_xml_nota_fiscal }}" target="_blank">
																					<i class="fa fa-file-code-o"></i> Visualizar DANFE (XML)
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'cancelado')|| (nota.status == 'processando_cancelamento')">
																				<a href="{{ nota.caminho_xml_cancelamento }}" target="_blank">
																					<i class="fa fa-file-code-o"></i> Visualizar XML de Cancelamento
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'processando_autorizacao') || (nota.status == 'processando_cancelamento')">
																				<a href="" target="_blank" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Atualizando" 
																					ng-click="atualzarStatus(nota.cod_nota_fiscal, $index, $event)">
																					<i class="fa fa-refresh"></i> Atualizar Status
																				</a>
																			</li>
																			<li role="separator" class="divider" 
																				ng-show="(nota.status == 'autorizado' || nota.status == 'processando_autorizacao')">
																			</li>
																			<li>
																				<a href="nota-fiscal.php?id_venda={{ nota.cod_venda }}" 
																					ng-show="nota.cod_venda">
																					<i class="fa fa-list-alt"></i> Visualizar Detalhes
																				</a>
																				<a href="nota-fiscal-servico.php?id={{ nota.cod_ordem_servico }}" 
																					ng-show="nota.cod_ordem_servico">
																					<i class="fa fa-list-alt"></i> Visualizar Detalhes
																				</a>
																			</li>
																			<li ng-show="(nota.cod_venda && nota.status == 'autorizado')" 
																				ng-click="modalCorrecao(nota,$index)">
																				<a href="">
																					<i class="fa fa-edit"></i> Cartas de Correção
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'autorizado')" 
																				ng-click="modalCancelar(nota,$index)">
																				<a href="">
																					<i class="fa fa-times-circle"></i> Cancelar Nota
																				</a>
																			</li>
																		</ul>
																	</div>
																</td>
																<td class="text-center text-middle">{{ nota.numero }}</td>
																<td class="text-middle">{{ nota.nome_destinatario }}</td>
																<td class="text-center text-middle">{{ nota.data_emissao | date : 'dd/MM/yyyy' }}</td>
																<td class="text-right text-middle">
																	R$ {{ (nota.cod_ordem_servico != null) ? nota.valor_total_servicos : ((nota.cod_venda != null || nota.cod_transferencia != null) ? nota.valor_total : '') | numberFormat : 2 : ',' : '.' }}
																</td>
																<td class="text-center text-middle">
																	{{ (nota.cod_ordem_servico != null) ? 'Serviço' : ((nota.cod_venda != null || nota.cod_transferencia != null) ? 'Produtos' : '') }}
																</td>
																<td class="text-center text-middle">
																	<i class="fa fa-info-circle fa-2x" data-toggle="tooltip" title="{{ nota.natureza_operacao }}"
																		ng-show="(nota.cod_venda != null || nota.cod_transferencia != null)"></i>

																	<i class="fa fa-info-circle fa-2x" data-toggle="tooltip" title="{{ nota.issqn_descricao_servico_municipio }}"
																		ng-show="(nota.cod_ordem_servico != null)"></i>
																</td>
																<td class="text-middle text-center">
																	<span class="label label-success" ng-show="(nota.status == 'autorizado') || (nota.status == 'erro_cancelamento') || (nota.status == 'processando_cancelamento')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		NF-e Autorizada
																	</span>
																	<span class="label label-warning" ng-show="(nota.status == 'processando_autorizacao')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Processando Autorização
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'erro_autorizacao')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Erro na Autorização
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'erro_cancelamento')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz_cancelamento }}">
																		Erro no cancelamento
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'cancelado')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Cancelada
																	</span>
																	<span class="label label-warning" ng-show="(nota.status == 'processando_cancelamento')" 
																		data-toggle="tooltip" title="Atualize o status para ver o andamento">
																		Processando Cancelamento
																	</span>															
																</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="pull-right">
												<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.emitidas_nfce.length > 1">
													<li ng-repeat="item in paginacao.emitidas_nfce" ng-class="{'active': item.current}">
														<a href="" ng-click="loadNotas('NFCE','emitidas_nfce','autorizado',item.offset,item.limit)">{{ item.index }}</a>
													</li>
												</ul>
											</div>
										</div>
									</div>
									<div class="tab-pane fade" id="canceladas_nfce">
										<br/>
										<div class="row">
											<div class="col-lg-12">
												<div class="table-responsive">
													<div class="alert alert-list-notas" style="display:none"></div>
													<table class="table table-bordered table-condensed table-striped table-hover">
														<thead>
															<th class="text-middle text-center" width="50"></th>
															<th class="text-middle text-center">Nº NF-e</th>
															<th class="text-middle">Destinatário</th>
															<th class="text-middle text-center">Data de Emissão</th>
															<th class="text-middle text-center">Valor da NF-e</th>
															<th class="text-middle text-center">Tipo NF-e</th>
															<th class="text-middle text-center">Operação</th>
															<th class="text-middle text-center">Status</th>
														</thead>
														<tbody>
															<tr ng-show="(!canceladas_nfce)">
																<td colspan="8" class="text-center text-middle">Nenhuma NF-e encontrada!</td>
															</tr>
															<tr ng-show="(canceladas_nfce.length == 0)">
																<td colspan="8" class="text-center text-middle"><i class="fa fa-refresh fa-spin"></i> Aguarde, carregando...</td>
															</tr>
															<tr bs-tooltip ng-repeat="nota in canceladas_nfce">
																<td class="text-middle">
																	<div class="btn-group">
																		<button type="button" class="btn btn-sm btn-default dropdown-toggle" 
																			data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																			Ações <span class="caret"></span>
																		</button>
																		<ul class="dropdown-menu">
																			<li ng-show="(nota.status == 'autorizado')|| (nota.status == 'processando_cancelamento')||(nota.status == 'erro_cancelamento')" 
																				ng-click="showDANFEModal(nota, 'PDF')">
																				<a href="">
																					<i class="fa fa-file-pdf-o"></i> Visualizar DANFE (PDF)
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'autorizado')|| (nota.status == 'processando_cancelamento')||(nota.status == 'erro_cancelamento')">
																				<a href="{{ nota.caminho_xml_nota_fiscal }}" target="_blank">
																					<i class="fa fa-file-code-o"></i> Visualizar DANFE (XML)
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'cancelado')|| (nota.status == 'processando_cancelamento')">
																				<a href="{{ nota.caminho_xml_cancelamento }}" target="_blank">
																					<i class="fa fa-file-code-o"></i> Visualizar XML de Cancelamento
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'processando_autorizacao') || (nota.status == 'processando_cancelamento')">
																				<a href="" target="_blank" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Atualizando" 
																					ng-click="atualzarStatus(nota.cod_nota_fiscal, $index, $event)">
																					<i class="fa fa-refresh"></i> Atualizar Status
																				</a>
																			</li>
																			<li role="separator" class="divider" 
																				ng-show="(nota.status == 'autorizado' || nota.status == 'processando_autorizacao')">
																			</li>
																			<li>
																				<a href="nota-fiscal.php?id_venda={{ nota.cod_venda }}" 
																					ng-show="nota.cod_venda">
																					<i class="fa fa-list-alt"></i> Visualizar Detalhes
																				</a>
																				<a href="nota-fiscal-servico.php?id={{ nota.cod_ordem_servico }}" 
																					ng-show="nota.cod_ordem_servico">
																					<i class="fa fa-list-alt"></i> Visualizar Detalhes
																				</a>
																			</li>
																			<li ng-show="(nota.cod_venda && nota.status == 'autorizado')" 
																				ng-click="modalCorrecao(nota,$index)">
																				<a href="">
																					<i class="fa fa-edit"></i> Cartas de Correção
																				</a>
																			</li>
																			<li ng-show="(nota.status == 'autorizado')" 
																				ng-click="modalCancelar(nota,$index)">
																				<a href="">
																					<i class="fa fa-times-circle"></i> Cancelar Nota
																				</a>
																			</li>
																		</ul>
																	</div>
																</td>
																<td class="text-center text-middle">{{ nota.numero }}</td>
																<td class="text-middle">{{ nota.nome_destinatario }}</td>
																<td class="text-center text-middle">{{ nota.data_emissao | date : 'dd/MM/yyyy' }}</td>
																<td class="text-right text-middle">
																	R$ {{ (nota.cod_ordem_servico != null) ? nota.valor_total_servicos : ((nota.cod_venda != null || nota.cod_transferencia != null) ? nota.valor_total : '') | numberFormat : 2 : ',' : '.' }}
																</td>
																<td class="text-center text-middle">
																	{{ (nota.cod_ordem_servico != null) ? 'Serviço' : ((nota.cod_venda != null || nota.cod_transferencia != null) ? 'Produtos' : '') }}
																</td>
																<td class="text-center text-middle">
																	<i class="fa fa-info-circle fa-2x" data-toggle="tooltip" title="{{ nota.natureza_operacao }}"
																		ng-show="(nota.cod_venda != null || nota.cod_transferencia != null)"></i>

																	<i class="fa fa-info-circle fa-2x" data-toggle="tooltip" title="{{ nota.issqn_descricao_servico_municipio }}"
																		ng-show="(nota.cod_ordem_servico != null)"></i>
																</td>
																<td class="text-middle text-center">
																	<span class="label label-success" ng-show="(nota.status == 'autorizado') || (nota.status == 'erro_cancelamento') || (nota.status == 'processando_cancelamento')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		NF-e Autorizada
																	</span>
																	<span class="label label-warning" ng-show="(nota.status == 'processando_autorizacao')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Processando Autorização
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'erro_autorizacao')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Erro na Autorização
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'erro_cancelamento')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz_cancelamento }}">
																		Erro no cancelamento
																	</span>
																	<span class="label label-danger" ng-show="(nota.status == 'cancelado')" 
																		data-toggle="tooltip" title="{{ nota.mensagem_sefaz }}">
																		Cancelada
																	</span>
																	<span class="label label-warning" ng-show="(nota.status == 'processando_cancelamento')" 
																		data-toggle="tooltip" title="Atualize o status para ver o andamento">
																		Processando Cancelamento
																	</span>															
																</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="pull-right">
												<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.canceladas_nfce.length > 1">
													<li ng-repeat="item in paginacao.canceladas_nfce" ng-class="{'active': item.current}">
														<a href="" ng-click="loadNotas('NFCE','canceladas_nfce','cancelado',item.offset,item.limit)">{{ item.index }}</a>
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /main-container -->

		<!-- /Modal novo tamanho-->
		<div class="modal fade" id="modal-cencelar-nota" style="display:none">
  			<div class="modal-dialog modal">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Cancelar Nota N°{{ notaCancelar.dados_emissao.num_documento_fiscal }}</span></h4>
      				</div>
				    <div class="modal-body">
				    	<fieldset>
							<legend class="clearfix"><span class="">Nota</span></legend>
							<div class="row">
					    		<div class="col-sm-3">
					    			<label class="control-label">Data de Emissão:</label>
					    			<div class="form-group ">
					    					<input ng-disabled="true" ng-model="notaCancelar.dados_emissao.data_emissao" type="text"  class="form-control input-sm" >
					    			</div>
					    		</div>
					    		<div class="col-sm-9">
					    			<label class="control-label">Chave NF-e:</label>
					    			<div class="form-group ">
					    					<input ng-disabled="true" ng-model="notaCancelar.dados_emissao.chave_nfe" type="text"  class="form-control input-sm" >
					    			</div>
					    		</div>
					    	</div>
					    	<div class="row">
					    		<div class="col-sm-3">
					    			<label class="control-label">Total:</label>
					    			<div class="form-group ">
					    					<input ng-disabled="true" ng-model="notaCancelar.dados_emissao.valor_total" thousands-formatter type="text"  class="form-control input-sm" >
					    			</div>
					    		</div>
					    	</div>
						<function>
				    	<fieldset>
							<legend class="clearfix"><span class="">Destinatário</span></legend>
							<div class="row">
								<div class="col-sm-12">
					    			<label class="control-label">{{ notaCancelar.destinatario.tipo_cadastro == 'pj' && 'Razão Social:' || 'Nome:' }}</label>
					    			<div class="form-group ">
					    					<input ng-disabled="true" ng-model="notaCancelar.destinatario.xNome" type="text"  class="form-control input-sm" >
					    			</div>
								</div>
							</div>
							<div class="row">
					    		<div class="col-sm-6" ng-if="notaCancelar.destinatario.tipo_cadastro == 'pf'">
					    			<label class="control-label">CPF:</label>
					    			<div class="form-group ">
					    					<input ng-disabled="true" ui-mask="999.999.999-99" ng-model="notaCancelar.destinatario.CPF" type="text"  class="form-control input-sm" >
					    			</div>
					    		</div>
					    		<div class="col-sm-6" ng-if="notaCancelar.destinatario.tipo_cadastro == 'pj'">
					    			<label class="control-label">CNPJ:</label>
					    			<div class="form-group ">
					    					<input ng-disabled="true" ui-mask="99.999.999/9999-99"  ng-model="notaCancelar.destinatario.CNPJ" type="text"  class="form-control input-sm" >
					    			</div>
					    		</div>
				    		</div>
						</fieldset>
						<fieldset>
							<legend class="clearfix"><span class="">Justificativa para o cancelamento</span></legend>
							<div class="row">
								<div class="col-sm-12">
					    			<div class="form-group ">
					    					<input ng-model="notaCancelar.justificativa" type="text"  class="form-control input-sm" >
					    			</div>
								</div>
							</div>
						</fieldset>
				    </div>
				    <div class="modal-footer">
				    	<button type="button" data-loading-text=" Aguarde..."
				    		class="btn btn-md btn-default" ng-click="cancelarModal('modal-novo-tamanho')" id="btn-aplicar-sangria">
				    		<i class="fa fa-times-circle"></i> Cancelar Operação
				    	</button>
				    	<button type="button" id="btn-cancelar-nota" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." class="btn btn-md btn-success" ng-click="cacelarNfe()">
				    		<i class="fa fa-ban"></i> Cancelar Nota
				    	</button>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<div class="modal modal-fade" id="modal-inutilizacao" style="display: none;">
  			<div class="modal-dialog modal">
    			<div class="modal-content">
      				<div class="modal-header">
      					<h4>Nova Inutilização</h4>
      				</div>
      				<div class="modal-body">
      					<div class="alert alert-danger" ng-if="(msg_error)">{{ msg_error }}</div>
      					<div class="row">
							<div class="form-group">
								<div class="col-sm-4">
									<label class="control-label">CNPJ do Emitente</label>
									<input type="text" name="cnpj" class="form-control" ng-model="inutilizacao.num_cnpj" ui-mask="99.999.999/9999-99" disabled>
								</div>
								<div class="col-sm-2">
									<label class="control-label">Serie</label>
									<input type="text" name="num_serie" class="form-control" ng-model="inutilizacao.serie" disabled>
								</div>
								<div class="col-sm-3">
									<label class="control-label">Número Inicial</label>
									<input type="text" name="num_inicial" class="form-control" ng-model="inutilizacao.num_inicial">
								</div>
								<div class="col-sm-3">
									<label class="control-label">Número Final</label>
									<input type="text" name="num_final" class="form-control" ng-model="inutilizacao.num_final">
								</div>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<label class="control-label">Justificativa</label>
									<textarea rows="5" class="form-control" ng-model="inutilizacao.dsc_justificativa"></textarea>
								</div>
							</div>	
						</div>
      				</div>
      				<div class="modal-footer">
      					<button class="btn btn-sm btn-default" data-dismiss="modal">
      						<i class="fa fa-times-circle"></i>
      						 Cancelar
      					</button>
      					<button class="btn btn-sm btn-success" ng-click="salvarInutilizacao($event)">
      						<i class="fa fa-save"></i>
      						 Salvar
      					</button>
      				</div>
      			</div>
      		</div>
		</div>


		<!-- /Modal novo tamanho-->
		<div class="modal fade" id="modal-corrigir-nota" style="display:none;">
  			<div class="modal-dialog modal modal-lg">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Corrigir Nota N°{{ notaCorrigir.dados_emissao.num_documento_fiscal }}</span></h4>		
      				</div>
				    <div class="modal-body">
				    	<fieldset>
							<legend class="clearfix">
								<a ng-show="!flg_nova_correcao" class="btn btn-info btn-xs" id="btn-novo" ng-click="showNovaCorrrecao(true)"><i class="fa fa-plus-circle"></i> Novo Correção</a>
								<a ng-show="flg_nova_correcao" class="btn btn-info btn-xs" id="btn-novo" ng-click="showNovaCorrrecao(false)"><i class="fa fa-minus-circle"></i> Novo Correção</a>
							</legend >
								<div class="row">
									<div class="col-sm-12">
										<div class="alert alert-correcao" style="display:none"></div>
									</div>
								</div>
								<div class="row" ng-show="flg_nova_correcao">
									<div class="col-sm-12">
										<div class="form-group" >
											<label class="control-label">Texto para correção</label>
											<textarea  ng-model="notaCorrigir.correcao" class="form-control" rows="5"></textarea>
										</div>
									</div>
								</div>
								<div class="row pull-right" ng-show="flg_nova_correcao">
									<div class="col-sm-12">
										<button type="button" data-loading-text=" Aguarde..."
								    		class="btn btn-md btn-default" ng-click="showNovaCorrrecao(false)" id="btn-aplicar-sangria">
								    		<i class="fa fa-times-circle"></i> Cancelar 
								    	</button>
								    	<button type="button" id="btn-corrigir-nota" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." class="btn btn-md btn-success" ng-click="corrgirNfe()">
								    		<i class="fa fa-save"></i> Salvar 
								    	</button>
									</div>
								</div>
						</fieldset>
				    	<fieldset>
							<legend class="clearfix"><span class="">Correções</span></legend>
							<div class="row">
								<div class="col-sm-12">
									<div class="alert alert-list-correcoes" style="display:none"></div>
					    			<table class="table table-bordered table-condensed table-striped table-hover">
										<thead>
											<th class="text-middle" width="50">Sequência</th>
											<th class="text-middle">Correção</th>
											<th class="text-middle text-center">Status</th>
											<th class="text-center" width="200">Ações</th>
										</thead>
										<tbody>
											<tr ng-if="nota_correcoes.length == 0">
												<td colspan="6" class="text-center">
													Não existe correções para esta nota
												</td>
											</tr>
											<tr ng-repeat="item in nota_correcoes">
												<td class="text-center">{{ item.numero_sequencial_evento }}</td>
												<td class="">{{ item.carta_correcao }}</td>
												<td class="text-center">
													<span class="label label-warning"
														ng-show="(item.status == 'processando_autorizacao')" 
														data-toggle="tooltip">
														Processando autorização
													</span>
													<span class="label label-danger"
														ng-show="(item.status == 'erro_autorizacao')" 
														data-toggle="tooltip">
														Erro na Autorização
													</span>
													<span class="label label-success"
														ng-show="(item.status == 'autorizado')" 
														data-toggle="tooltip">
														Autorizado
													</span>
												</td>
												<td class="text-center">
													<button type="button" id="btn-cancelar-nota" 
														data-loading-text="<i class='fa fa-refresh fa-spin'></i>" 
														class="btn btn-xs btn-success" 
														ng-click="atualizarCorrecao(item,$index)"
														ng-show="(item.status != 'autorizado')">
											    		<i class="fa fa-refresh"></i> Atualizar Status
											    	</button>

											    	<a href="{{ item.caminho_pdf }}" target="_blank" class="btn btn-xs btn-primary"
											    		ng-show="(item.status == 'autorizado')">
											    		<i class="fa fa-file-pdf-o"></i> Ver PDF
											    	</button>

											    	<a href="{{ item.caminho_pdf }}" target="_blank" class="btn btn-xs btn-primary"
											    		ng-show="(item.status == 'autorizado')">
											    		<i class="fa fa-file-code-o"></i> Ver XML
											    	</button>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</fieldset>
				    </div>
				    <!--<div class="modal-footer">
				    	<button type="button" data-loading-text=" Aguarde..."
				    		class="btn btn-md btn-default" ng-click="cancelarModal('modal-novo-tamanho')" id="btn-aplicar-sangria">
				    		<i class="fa fa-times-circle"></i> Cancelar Operação
				    	</button>
				    	<button type="button" id="btn-cancelar-nota" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." class="btn btn-md btn-success" ng-click="cacelarNfe()">
				    		<i class="fa fa-ban"></i> Cancelar Nota
				    	</button>
				    </div>-->
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

    <!-- Easy Modal -->
    <script src="js/eModal.js"></script>

	<!-- Modernizr -->
	<script src='js/modernizr.min.js'></script>

    <!-- Pace -->
	<script src='js/pace.min.js'></script>

	<!-- Popup Overlay -->
	<script src='js/jquery.popupoverlay.min.js'></script>

    <!-- Slimscroll -->
	<script src='js/jquery.slimscroll.min.js'></script>

	<!-- Datepicker -->
	<script src='js/bootstrap-datepicker.min.js'></script>

	<!-- Cookie -->
	<script src='js/jquery.cookie.min.js'></script>

	<!-- Endless -->
	<script src="js/endless/endless.js"></script>

	<!-- Chosen -->
	<script src='js/chosen.jquery.min.js'></script>

	<!-- Bower Components -->	
	<script src="bower_components/noty/lib/noty.min.js" type="text/javascript"></script>
    <script src="bower_components/mojs/build/mo.min.js" type="text/javascript"></script>

	<!-- Extras -->
	<script src="js/extras.js"></script>
	
	<!-- Moment -->
	<script src="js/moment/moment.min.js"></script>

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
    	$('.datepicker').datepicker();
    	$("#btnDtaEmissao").on("click", function(){ $("#inputDtaEmissao").trigger("focus"); });
		$("#btnDtaSaida").on("click", function(){ $("#inputDtaSaida").trigger("focus"); });

		$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
    	var addParamModule = ['angular.chosen'] ;
    </script>
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/notas_fiscais-controller.js"></script>
	<script type="text/javascript"></script>>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

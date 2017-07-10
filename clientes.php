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
	<link href="css/font-awesome-4.5.0/css/font-awesome.min.css" rel="stylesheet">

	 <!-- ui treeview -->
    <link rel="stylesheet" href="css/bootstrap-treeview.css"/>

	<!-- Pace -->
	<link href="css/pace.css" rel="stylesheet">

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

  <body class="overflow-hidden" ng-controller="ClientesController" ng-cloak>
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
					 <li class="active"><i class="fa fa-users"></i> Clientes/Usuários</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-users"></i> Clientes/Usuários</h3>
					<br/>
					<a class="btn btn-info" id="btn-novo"  ng-click="showBoxNovo()"><i class="fa fa-plus-circle"></i> Novo Cliente/Usuário</a>
					<a href="desejos_clientes.php" ng-if="userLogged.id_empreendimento != 75" class="btn btn-primary"><i class="fa fa-clipboard"></i> Desejos</a>
					<a href="extrato.php" class="btn btn-success"><i class="fa fa-clipboard"></i> Extrato</a>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md">
				<div class="alert alert-sistema" style="display:none"></div>
				<div class="panel panel-default" id="box-novo" style="display:none">
					<div class="panel-heading"><i class="fa fa-plus-circle"></i> Novo Cliente/Usuário</div>
					<div class="panel-tab clearfix" id="tab-cliente">
						<ul class="tab-bar">
							<li class="active"><a href="#informacoes_basicas" data-toggle="tab"><i class="fa fa-user"></i> Informações Básicas</a></li>
							<li><a href="#informacoes_complementares" data-toggle="tab"><i class="fa fa-user-plus"></i> Informações Complementares</a></li>
							<li><a href="#empreendimentos" data-toggle="tab"><i class="fa fa-building-o"></i> Empreendimentos</a></li>
							<li><a href="#dados_fiscais" data-toggle="tab"><i class="fa fa-file-text-o"></i> Dados Fiscais</a></li>
							<li><a href="#dados_acesso" data-toggle="tab"><i class="fa fa-user-secret"></i> Dados de Acesso</a></li>
							<li><a href="#atendimentos" ng-if="userLogged.id_empreendimento == 75" ng-click="getAtendimentos()" data-toggle="tab"><i class="fa fa-list-alt"></i> Atendimentos</a></li>
							<li><a href="#pagamentos" ng-if="userLogged.id_empreendimento == 75" ng-click="loadPagamentosPaciente()" data-toggle="tab"><i class="fa fa-list-alt"></i> Pagamentos</a></li>
						</ul>
					</div>
					<div class="panel-body" id="tab-cliente-body">
						<form role="form">
							<div class="tab-content">
								<div class="tab-pane fade in active" id="informacoes_basicas">
									<div class="row">
										<div class="col-sm-12">
											<div class="form-group">
												<label for="" class="control-label">Tipo de Cadastro</label>
												<div class="form-group">
													<label class="label-radio inline">
														<input ng-model="cliente.cliente_tipo_cadastro" ng-click="setTipoCadastro('cliente','pf')" value="cliente_pf" type="radio" class="inline-radio"/>
														<span class="custom-radio"></span>
														<span>Cliente - Pessoa Física</span>
													</label>

													<label class="label-radio inline">
														<input ng-model="cliente.cliente_tipo_cadastro" ng-click="setTipoCadastro('cliente','pj')" value="cliente_pj" type="radio" class="inline-radio"/>
														<span class="custom-radio"></span>
														<span>Cliente - Pessoa Jurídica</span>
													</label>
													<label class="label-radio inline">
														<input ng-model="cliente.cliente_tipo_cadastro" ng-click="setTipoCadastro('usuario','pf')" value="usuario_pf" type="radio" class="inline-radio"/>
														<span class="custom-radio"></span>
														<span>Usuário do Sistema</span>
													</label>
												</div>
											</div>
										</div>
									</div>
									<div class="row" ng-if="cliente.tipo_cadastro == 'pf'">
										<div class="col-sm-6">
											<div id="nome" class="form-group">
												<label for="nome" class="control-label">Nome <i style="font-size: 10px;color: #FF0000;" class="fa fa-asterisk"></i></label>
												<input type="text" class="form-control input-sm" id="nome" ng-model="cliente.nome">
											</div>
										</div>

										<div class="col-sm-2">
											<div id="dta_nacimento" class="form-group">
												<label class="control-label">Data de Nacimento</label>
												<input class="form-control input-sm" ui-mask="99/99/9999" id="dta_nacimento" ng-model="cliente.dta_nacimento">
											</div>
										</div>

										<div class="col-sm-4">
											<div id="apelido" class="form-group">
												<label for="apelido" class="control-label">Apelido</label>
												<input type="text" class="form-control input-sm" id="apelido" ng-model="cliente.apelido">
											</div>
										</div>
									</div>
									<div class="row" ng-if="cliente.tipo_cadastro == 'pf'">
										<div class="col-sm-2">
											<div id="rg" class="form-group">
												<label class="control-label">RG</label>
												<input class="form-control input-sm"  ng-model="cliente.rg" />
											</div>
										</div>

										<div class="col-sm-2">
											<div id="cpf" class="form-group">
												<label class="control-label">CPF</label>
												<input class="form-control input-sm" ui-mask="999.999.999-99" ng-model="cliente.cpf" />
											</div>
										</div>
									</div>
									<div class="row" ng-if="cliente.tipo_cadastro == 'pj'">
										<div class="col-lg-3">
											<div id="razao_social" class="form-group">
												<label class="control-label">Razão Social  <i style="font-size: 10px;color: #FF0000;" class="fa fa-asterisk"></i></label>
												<input placeholder="" class="form-control input-sm" ng-model="cliente.razao_social">
											</div>
										</div>

										<div class="col-sm-3">
											<div id="nome_fantasia" class="form-group">
												<label class="control-label">Nome Fantasia  <i style="font-size: 10px;color: #FF0000;" class="fa fa-asterisk"></i></label>
												<input class="form-control input-sm" ng-model="cliente.nome_fantasia">
											</div>
										</div>

										<div class="col-sm-2">
											<div id="cnpj" class="form-group">
												<label class="control-label">CNPJ  <i style="font-size: 10px;color: #FF0000;" class="fa fa-asterisk"></i></label>
												<input class="form-control input-sm" ui-mask="99.999.999/9999-99" ng-model="cliente.cnpj">
											</div>
										</div>

										<div class="col-sm-2">
											<div id="inscricao_estadual" class="form-group">
												<label class="control-label">I.E.</label>
												<input class="form-control input-sm" ng-model="cliente.inscricao_estadual">
											</div>
										</div>
										<div class="col-sm-2">
											<div id="inscricao_estadual" class="form-group">
												<label class="control-label">I.M.</label>
												<input class="form-control input-sm" ng-model="cliente.num_inscricao_municipal">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-2">
											<div id="tel_fixo" class="form-group">
												<label for="" class="control-label">Telefone Fixo</label>
												<input type="text" ui-mask="(99) 99999999" class="form-control input-sm" ng-model="cliente.tel_fixo">
											</div>
										</div>

										<div class="col-sm-2">
											<div id="celular" class="form-group">
												<label for="" class="control-label">Celular </label>
												<input type="text" ui-mask="(99) 99999999?9" class="form-control input-sm" ng-model="cliente.celular">
											</div>
										</div>

										<div class="col-sm-2">
											<div id="nextel" class="form-group">
												<label for="" class="control-label">ID Nextel</label>
												<input type="text" class="form-control input-sm" ng-model="cliente.nextel">
											</div>
										</div>

										<div class="col-sm-4">
											<div id="email" class="form-group">
												<label for="" class="control-label">End. Email </label>
												<input type="text" class="form-control input-sm" ng-model="cliente.email">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-2">
											<div id="cep" class="form-group">
												<label class="control-label">CEP</label>
												<input type="text" class="form-control input-sm" ui-mask="99999-999" ng-model="cliente.cep" ng-keyUp="validCep(cliente.cep)" ng-blur="validCep(cliente.cep)">
											</div>
										</div>

										<div class="col-sm-7">
											<div id="endereco" class="form-group">
												<label class="control-label">Endereço</label>
												<input type="text" class="form-control input-sm" ng-model="cliente.endereco">
											</div>
										</div>

										<div class="col-sm-1">
											<div id="numero" class="form-group">
												<label class="control-label">No.</label>
												<input id="num_logradouro" type="text" class="form-control input-sm" ng-model="cliente.numero" ng-blur="consultaLatLog()">
											</div>
										</div>

										<div class="col-sm-2">
											<div id="bairro" class="form-group">
												<label class="control-label">Bairro</label>
												<input type="text" class="form-control input-sm" ng-model="cliente.bairro">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-12">
											<div id="complemento" class="form-group">
												<label class="control-label">Complemento:</label>
												<input type="text" class="form-control input-sm" ng-model="cliente.end_complemento">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-4">
											<div id="ponto_referencia" class="form-group">
												<label class="control-label">Ponto Referência</label>
												<input type="text" class="form-control input-sm" ng-model="cliente.ponto_referencia">
											</div>
										</div>
										<div class="col-sm-2">
											<div id="regiao" class="form-group">
												<label class="control-label">Região</label>
												<input type="text" class="form-control input-sm" ng-model="cliente.regiao">
											</div>
										</div>

										<div class="col-sm-2">
											<div id="id_estado" class="form-group">
												<label class="control-label">Estado</label>
												<select chosen id="id_select_estado" class="form-control input-sm" readonly="readonly" ng-model="cliente.id_estado" ng-options="item.id as item.nome for item in estados" ng-change="cliente.id_cidade=null;loadCidadesByEstado()"></select>
											</div>
										</div>


										<div class="col-sm-4">
											<div id="id_cidade" class="form-group">
												<label class="control-label">Cidade</label>
												<select chosen class="form-control input-sm" readonly="readonly" ng-model="cliente.id_cidade" ng-options="a.id as a.nome for a in cidades"></select>
											</div>
										</div>
									</div>			
								</div>
								<div class="tab-pane fade" id="informacoes_complementares">
									<div class="row">
										<div class="col-sm-2">
											<div id="indicacao" class="form-group">
												<label class="control-label">Indicação?</label>
												<div class="form-group">
													<label class="label-radio inline">
														<input ng-model="cliente.indicacao" value="1" type="radio" class="inline-radio">
														<span class="custom-radio"></span>
														<span>Sim</span>
													</label>

													<label class="label-radio inline">
														<input ng-model="cliente.indicacao" value="0" type="radio" class="inline-radio">
														<span class="custom-radio"></span>
														<span>Não</span>
													</label>
												</div>
											</div>
										</div>
										<div class="col-sm-3" ng-if="cliente.indicacao == 1">
											<div id="indicado_por_quem" class="form-group">
												<label class="control-label">Indicado por Quem?</label>
												<input type="text" class="form-control input-sm" ng-model="cliente.indicado_por_quem">
											</div>
										</div>

										<div class="col-sm-2">
											<div id="id_como_encontrou" class="form-group">
												<label class="control-label">Como Encontrou?</label>
												<select class="form-control input-sm" ng-model="cliente.id_como_encontrou" ng-options="a.id as a.nome for a in comoencontrou"><option value=""></option></select>
											</div>
										</div>

										<div class="col-sm-4" ng-show="cliente.id_como_encontrou == 'outros' ">
											<div id="como_entrou_outros" class="form-group">
												<label class="control-label">Descreva</label>
												<input type="text" class="form-control input-sm" ng-model="cliente.como_entrou_outros">
											</div>
										</div>

										<div class="col-sm-4">
											<div id="id_finalidade" class="form-group">
												<label class="control-label">Finalidade</label>
												<input type="text" class="form-control input-sm" ng-model="cliente.dsc_finalidade">
												<!--<select class="form-control input-sm" ng-model="cliente.id_finalidade" ng-options="a.id as a.nome for a in finalidades"></select>-->
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-2">
											<div id="email_marketing" class="form-group">
												<label for="" class="control-label">Newsletter?</label>
												<div class="form-group">
													<label class="label-radio inline">
														<input ng-model="cliente.email_marketing" value="1" type="radio" class="inline-radio">
														<span class="custom-radio"></span>
														<span>Sim</span>
													</label>

													<label class="label-radio inline">
														<input ng-model="cliente.email_marketing" value="0" type="radio" class="inline-radio">
														<span class="custom-radio"></span>
														<span>Não</span>
													</label>
												</div>
											</div>
										</div>

										<div class="col-sm-4">
											<div id="id_finalidade" class="form-group">
												<label class="control-label">Grupo de Comissionamento</label>
												<select chosen class="form-control input-sm" ng-model="cliente.id_grupo_comissionamento" ng-options="a.id as a.nme_grupo_comissao for a in grupo_comissionamento">

												</select>
											</div>
										</div>

										<div class="col-sm-6">
											<div id="id_plano_contas_padrao" class="form-group">
												<label class="control-label">Natureza Padrão (Contas a Pagar/Receber)</label>
												<select chosen class="form-control input-sm" 
													ng-model="cliente.id_plano_contas_padrao" 
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
												<select class="form-control input-sm" ng-model="cliente.id_banco" ng-options="a.id as a.nome for a in bancos"></select>
											</div>
										</div>

										<div class="col-sm-2">
											<div id="agencia" class="form-group">
												<label class="control-label">Agência</label>
												<input type="text" class="form-control input-sm" ng-model="cliente.agencia">
											</div>
										</div>

										<div class="col-sm-2">
											<div id="conta" class="form-group">
												<label class="control-label">C/C</label>
												<input type="text" class="form-control input-sm" ng-model="cliente.conta">
											</div>
										</div>
										<div class="col-sm-2">
											<div id="cliente_desde" class="form-group">
												<label for="cliente_desde" class="control-label">Cliente Desde</label>
												<input type="text" class="form-control input-sm" ui-mask="99/9999" ng-model="cliente.cliente_desde">
											</div>
										</div>
									</div>	
								</div>
								<div class="tab-pane fade" id="empreendimentos">
									<div class="row">
										<div class="col-sm-12">
											<div class="form-group" id="empreendimentos">
												<table class="table table-bordered table-condensed table-striped table-hover">
													<thead>
														<tr>
															<td><i class="fa fa-building-o"></i> Empreendimentos</td>
															<td width="60" align="center">
																<img style="float: right;" ng-if="load_empreendimentos == true" src="img/loder_circular_15x15.gif">
																<button ng-if="(cliente.id == undefined || cliente.id == '') || (load_empreendimentos == false)" ng-click="loadEmpreendimentos()"  class="btn btn-xs btn-primary"><i class="fa fa-plus-circle"></i></button>
															</td>
														</tr>
													</thead>
													<tbody>
														<tr ng-repeat="item in cliente.empreendimentos" >
															<td>{{ item.nome_empreendimento }}</td>
															<td align="center">
																<button ng-show="item.id != userLogged.id_empreendimento" ng-click="delEmpreendimento($index)" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></button>
															</td>
														</tr>
														<tr ng-show="(cliente.empreendimentos == 0)">
															<td colspan="2" class="text-center">Nenhum empreendimento selecionado</td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="dados_fiscais">
									<div class="row">
										<div class="col-sm-4">
											<div class="form-group" id="regimeTributario">
												<label class="ccontrol-label">Regime Tributário </label> 
												<select chosen ng-change="ClearChosenSelect('cod_regime_tributario')"
											    option="regimeTributario"
											    no-results-text ="'Nenhum valor encontrado'"
											    allow-single-deselect="true"
											    ng-model="cliente.cod_regime_tributario"
											    ng-options="regime.cod_controle_item_nfe as regime.nme_item for regime in regimeTributario">
												</select>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group" id="regimePisCofins">
												<label class="ccontrol-label">Regime Pis Cofins  </label> 
												<select chosen ng-change="ClearChosenSelect('cod_regime_pis_cofins')"
											    option="regimePisCofins"
											    no-results-text ="'Nenhum valor encontrado'"
											    allow-single-deselect="true"
											    ng-model="cliente.cod_regime_pis_cofins"
											    ng-options="regime.cod_controle_item_nfe as regime.nme_item for regime in regimePisCofins">
												</select>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group" id="tipoEmpresaeso">
												<label class="ccontrol-label">Tipo da Empresa</label> 
												<select chosen ng-change="ClearChosenSelect('cod_tipo_empresa')"
											    option="tipoEmpresa"
											    no-results-text ="'Nenhum valor encontrado'"
											    allow-single-deselect="true"
											    ng-model="cliente.cod_tipo_empresa"
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
											    no-results-text ="'Nenhum valor encontrado'"
											    allow-single-deselect="true"
											    ng-model="cliente.cod_zoneamento"
											    ng-options="zoneamento.cod_zoneamento as zoneamento.dsc_zoneamento for zoneamento in zoneamentos">
												</select>
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-group">
												<label for="" class="control-label">Contribuinte ICMS</label>
												<div class="form-group">
													<label class="label-radio inline">
														<input ng-model="cliente.flg_contribuinte_icms" value="0" type="radio" class="inline-radio">
														<span class="custom-radio"></span>
														<span>Não</span>
													</label>

													<label class="label-radio inline">
														<input ng-model="cliente.flg_contribuinte_icms" value="1" type="radio" class="inline-radio">
														<span class="custom-radio"></span>
														<span>Sim</span>
													</label>
												</div>
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-group">
												<label for="" class="control-label">Isento da I.E.</label>
												<div class="form-group">
													<label class="label-radio inline">
														<input ng-model="cliente.flg_isento_inscricao_estadual" value="0" type="radio" class="inline-radio">
														<span class="custom-radio"></span>
														<span>Não</span>
													</label>

													<label class="label-radio inline">
														<input ng-model="cliente.flg_isento_inscricao_estadual" value="1" type="radio" class="inline-radio">
														<span class="custom-radio"></span>
														<span>Sim</span>
													</label>
												</div>
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-group">
												<label for="" class="control-label">Contribuinte IPI</label>
												<div class="form-group">
													<label class="label-radio inline">
														<input ng-model="cliente.flg_contribuinte_ipi" value="0" type="radio" class="inline-radio">
														<span class="custom-radio"></span>
														<span>Não</span>
													</label>

													<label class="label-radio inline">
														<input ng-model="cliente.flg_contribuinte_ipi" value="1" type="radio" class="inline-radio">
														<span class="custom-radio"></span>
														<span>Sim</span>
													</label>
												</div>
											</div>
										</div>
									</div>
									<div class="row" ng-show="cliente.tipo_cadastro == 'pj'">
										<div class="col-sm-2">
											<div class="form-group">
												<div id="inscricao_estadual_st" class="form-group">
													<label class="control-label">I.E. ST</label>
													<input class="form-control input-sm" ng-model="cliente.inscricao_estadual_st">
												</div>
											</div>
										</div>
									</div>
									<div class="row" ng-show="cliente.id != null">
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
																	<tr ng-show="(cliente.regime_especial.length == 0 && cliente.regime_especial != null)">
																		<td colspan="3" align="center">Nenhum Regime Relacionado</td>
																	</tr>
																	<tr>
																		<td colspan="3" class="text-center" ng-if="cliente.regime_especial == null">
																			<i class='fa fa-refresh fa-spin'></i> Carregando
																		</td>
																	</tr>
																	<tr ng-repeat="item in cliente.regime_especial" bs-tooltip >
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
								</div>
								<div class="tab-pane fade" id="dados_acesso">
									<div class="row">
										<div class="col-sm-3">
											<div id="id_perfil" class="form-group">
												<label class="control-label">Perfil  <i style="font-size: 10px;color: #FF0000;" class="fa fa-asterisk"></i></label>
												<select chosen class="form-control input-sm" ng-model="cliente.id_perfil" ng-options="a.id as a.nome for a in perfis"  ng-change="loadModulosByPerfil(cliente.id_perfil)" ></select>
											</div>
										</div>
										<div class="col-sm-2">
											<div id="login" class="form-group">
												<label class="control-label">Login</label>
												<input type="text" class="form-control input-sm" ng-model="cliente.login">
											</div>
										</div>
										<div class="col-sm-2">
											<div id="senha" class="form-group">
												<label class="control-label">Senha</label>
												<input class="form-control input-sm" type="password" id="input_senha" ng-model="cliente.senha">
												
											</div>
										</div>
										<div class="col-sm-2">
											<div  class="form-group">
												<label class="control-label"></label>
												<div class="checkbox" style="padding-left: 0px;">
												    <label class="label-checkbox">
														<input type="checkbox" onclick="if($(this).is(':checked')){$('#input_senha').attr('type','text')}else{$('#input_senha').attr('type','password')}">
														<span class="custom-checkbox"></span>
														 Mostrar senha<br>
													</label>
												</div>
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label"><span class="custom-checkbox"></span>
														 Bloquear Usuário?<br></label>
												<div class="form-group">
													<label class="label-radio inline">
														<input ng-model="cliente.flg_ativo" value="1" type="radio" class="inline-radio">
														<span class="custom-radio"></span>
														<span>Não</span>
													</label>

													<label class="label-radio inline">
														<input ng-model="cliente.flg_ativo" value="0" type="radio" class="inline-radio">
														<span class="custom-radio"></span>
														<span>Sim</span>
													</label>
												</div>
											</div>
										</div>
									</div>
									<div class="row" ng-show="isNumeric(cliente.id_perfil)">
										<div class="col-sm-12">
											<div class="padding-md" style="padding:0 !important">
												<div class="panel panel-default" id="modulos">
													<div class="panel-heading"><i class="fa fa-th fa-lg"></i>&nbsp;Módulos
															<span ng-show="cliente.modulos.length > 0" class="pull-right">Selecionados: <span style="background:#504f63" class="badge badge-primary">{{ cliente.modulos.length }}</span></span>
													</div>
													<div ng-show="loadingModulos" style="max-height:305px;min-height:305px;overflow:auto" class="panel-body">
														<div class="text-center" style="height: 100%px;line-height: 120px;vertical-align:middle;width: 100%;font-size: 15px;">
												    		<i class='fa fa-refresh fa-spin'></i> Aguarde, carregando Módulos...
												    	</div>
											        </div>
											        <div ng-show="!loadingModulos" style="max-height:305px;min-height:305px;overflow:auto" class="panel-body" id="treeview-modulos"></div>
											    </div>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="atendimentos">
									<div class="row">
										<div class="col-sm-12">
											<table class="table table-bordered table-hover table-striped table-condensed">
												<thead>
													<th class="text-center">Data</th>
													<th class="text-center">Procedimento</th>
													<th class="text-center">Dente/Região</th>
													<th class="text-center">Face</th>
													<th class="text-center">Status</th>
													<th class="text-center">Agendamento</th>
													<th class="text-center">Valor</th>
												</thead>
												<tbody>
													<tr ng-repeat="item in atendimentos" bs-tooltip>
														<td class="text-center">{{ item.dta_venda | dateFormat: 'dateTime' }}</td>
														<td>{{ item.dsc_procedimento }}</td>
														<td>{{ item.nme_dente }}</td>
														<td>{{ item.dsc_face }}</td>
														<td><i class="fa fa-circle" ng-class="{'text-danger':item.id_status_procedimento == 1,'text-warning':item.id_status_procedimento == 2,'text-success': item.id_status_procedimento == 3 }"></i> 
															{{item.dsc_status_procedimento}}
														</td>
														<td style="width: 154px">
															{{ item.dta_inicio_procedimento }}
														</td>
														<td class="text-right"><!--<i class="fa fa-circle" ng-class="{'text-danger':item.flg_item_pago == 0,'text-success':item.flg_item_pago == 1}"></i> -->R$ {{ item.valor_real_item | numberFormat:2:',':'.'}}</td>
													</tr>
													<tr ng-if="atendimentos.length == 0">
														<td colspan="8" class="text-center" text-center>
															Nenhum procedimento encontrado
														</td>
													</tr>
													<tr ng-if="atendimentos==null">
														<td colspan="8" class="text-center">
															<i class='fa fa-refresh fa-spin'></i> Carregando ...
														</td>
													</tr>
												</tbody>
												<tfoot ng-if="atendimentos.length > 0">
													<th class="text-right" colspan="6">Total</th>
													<th class="text-right">R$ {{ totalAtendimentos() | numberFormat:2:',':'.' }}</th>
												</tfoot>
											</table>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="pagamentos">
									<div class="row">
										<div class="col-sm-12">
											<table class="table table-bordered table-hover table-striped table-condensed">
												<thead>
													<th class="text-center">Dta lançamento</th>
													<th class="text-center">Forma Pagamento</th>
													<th class="text-center">Valor</th>
												</thead>
												<tbody>
													<tr ng-repeat="item in pagamentosCliente.pagamentos" bs-tooltip>
														<td class="text-center">{{ item.dta_entrada | dateFormat: 'dateTime' }}</td>

														<td ng-show="item.id_forma_pagamento != 6">{{ item.descricao_forma_pagamento }}</td>
														<td ng-show="item.id_forma_pagamento == 6">{{ item.descricao_forma_pagamento }} em {{ item.num_parcelas }}x de R$ {{ item.vlr_parcela |numberFormat:2:',':'.' }}</td>
														
														<td class="text-right">{{ item.valor_pagamento |numberFormat:2:',':'.' }}</td>
														
													</tr>
													<tr ng-if="pagamentosCliente.pagamentos.length == 0">
														<td colspan="8" class="text-center" text-center>
															Nenhum pagamento encontrado
														</td>
													</tr>
													<tr ng-if="pagamentosCliente.pagamentos==null">
														<td colspan="8" class="text-center">
															<i class='fa fa-refresh fa-spin'></i> Carregando ...
														</td>
													</tr>
												</tbody>
												<tfoot ng-if="pagamentosCliente.pagamentos.length > 0">
													<th class="text-right" colspan="2">Total</th>
													<th class="text-right">R$ {{ pagamentosCliente.total | numberFormat:2:',':'.' }}</th>
												</tfoot>
											</table>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="panel-footer">
						<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<div class="pull-right">
											<button ng-click="showBoxNovo(); reset();" type="submit" class="btn btn-danger btn-sm">
												<i class="fa fa-times-circle"></i> Cancelar
											</button>
											<button data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." id="btn_salvar" ng-click="salvar($event)" type="submit" class="btn btn-success btn-sm">
												<i class="fa fa-save"></i> Salvar
											</button>
										</div>
									</div>
								</div>
							</div>
						</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-tasks"></i> Clientes Cadastrados
						<a href="util/export.php?c=usuarios&id_empreendimento={{ userLogged.id_empreendimento }}" target="_blank" ng-if="funcioalidadeAuthorized('ver_botao_exportar')" class="btn btn-xs btn-success pull-right"  type="button">
							<i class="fa fa-download"></i>
							Exportar
						</a>
					</div>

					<div class="panel-body">
						<div class="row">
							<div class="col-lg-2">
								<div class="form-group" style="padding-top:7px; margin-bottom:0px;">
									<label class="label-radio inline">
										<input  ng-click="busca.tipo_cliente = 'pf'" name="changeDetalhesCC" ng-checked="(busca.tipo_cliente == 'pf')" type="radio" class="inline-radio">
										<span class="custom-radio"></span>
										<span>PF</span>
									</label>

									<label class="label-radio inline">
										<input  ng-click="busca.tipo_cliente = 'pj'" name="changeDetalhesCC"  ng-checked="(busca.tipo_cliente == 'pj')" type="radio" class="inline-radio">
										<span class="custom-radio"></span>
										<span>PJ</span>
									</label>

									<label class="label-radio inline">
										<input  ng-click="busca.tipo_cliente = 'ambos'" name="changeDetalhesCC"  ng-checked="(busca.tipo_cliente == 'ambos')" type="radio" class="inline-radio">
										<span class="custom-radio"></span>
										<span>Ambos</span>
									</label>
								</div>
							</div>
							<div class=" col-sm-9">
								<div class="input-group">
						            <input ng-model="busca.clientes"  type="text" class="form-control input-sm"
						            	ng-enter="loadClientes(0,10)" 
						            	ng-keyup="loadClientes(0,10)"
						            >
						            <div class="input-group-btn">
						            	<button ng-click="loadClientes(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div>
						        </div>
							</div>
							<div class="col-sm-1">
								<button type="button" class="btn btn-sm btn-default" ng-click="busca.clientes='';busca.tipo_cliente = 'ambos'; loadClientes(0,10); ">Limpar</button>
							</div>
						</div>

						<br>

						<div class="row">
							<div class="col-sm-12 table-responsive">
								<table class="table table-bordered table-condensed table-striped table-hover">
									<tr ng-if="clientes != false && (clientes.length <= 0 || clientes == null)">
										<th class="text-center" colspan="10" style="text-align:center">
											<i class="fa fa-refresh fa-spin"></i> Aguarde, carregando listagem de clientes ...
										</th>
									</tr>
									<tr ng-if="clientes == false">
										<th class="text-center" colspan="10" style="text-align:center">
											Não a resultados para a busca
										</th>
									</tr>
									<thead>
										<tr>
											<th class="text-center" width="50">#</th>
											<th class="text-center" width="80">Data Cadastro</th>
											<th class="text-center" width="100">Saldo</th>
											<th class="text-center" style="min-width: 150px;">Nome</th>
											<th class="text-center" width="130">Apelido</th>
											<th class="text-center">Perfil</th>
											<th class="text-center" style="min-width: 100px;">Telefone</th>
											<th class="text-center" style="min-width: 100px;">Celular</th>
											<th class="text-center" style="min-width: 100px;">Nextel</th>
											<th class="text-center" width="70">Status</th>
											<th width="80" style="text-align: center;">Opções</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in clientes">
											<td class="text-center">{{ item.id }}</td>
											<td class="text-center">{{ item.dta_cadastro | dateFormat:'date' }}</td>
											<td class="text-center" ng-if="item.vlr_saldo_devedor == undefined">
												<p class="label"><i class="fa fa-refresh fa-spin"></i> Carregando ...</p>
											</td>
											<td class="text-right" ng-if="item.vlr_saldo_devedor != undefined">
												<span class="label label-money-blue" ng-if="item.vlr_saldo_devedor == 0">R$ {{ item.vlr_saldo_devedor | numberFormat: 2 : ',' : '.' }}</span>
												<span class="label label-success" ng-if="item.vlr_saldo_devedor > 0">R$ {{ item.vlr_saldo_devedor | numberFormat: 2 : ',' : '.' }}</span>
												<span class="label label-danger" ng-if="item.vlr_saldo_devedor < 0">R$ {{ item.vlr_saldo_devedor | numberFormat: 2 : ',' : '.' }}</span>
											</td>
											<td ng-if="!(item.nome == null || item.nome=='')">{{ item.nome | uppercase }}</td>
											<td ng-if="(item.nome == null || item.nome=='')" ng-bind-html="item.cpf | cpfFormat:'<b>CPF:</b> '"></td>
											<td control-size-string content="{{ item.apelido | uppercase }}" size="14"></td>
											<td class="text-center">{{ item.nome_perfil | uppercase }}</td>
											<td class="text-center">{{ item.tel_fixo | phoneFormat }}</td>
											<td class="text-center">{{ item.celular | phoneFormat }}</td>
											<td class="text-center">{{ item.nextel }}</td>
											<td class="text-center">
												<span class="label label-success" ng-if="item.acesso_restrito == true">Liberado</span>
												<span class="label label-danger" ng-if="item.acesso_restrito == false">Bloqueado</span>
											</td>
											<td align="center">
												<button type="button" ng-click="editar(item)" tooltip="Editar" class="btn btn-xs btn-warning" data-toggle="tooltip">
													<i class="fa fa-edit"></i>
												</button>
												<button type="button" ng-click="delete(item)" tooltip="Excluir" class="btn btn-xs btn-danger delete" data-toggle="tooltip">
													<i class="fa fa-trash-o"></i>
												</button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="panel-footer clearfix">
						<div class="pull-right">
							<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.itens.length > 1">
								<li ng-repeat="item in paginacao.itens" ng-class="{'active': item.current}">
									<a href="" h ng-click="loadClientes(item.offset,item.limit)">{{ item.index }}</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /main-container -->

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

		<!-- /Modal fornecedor-->
		<div class="modal fade" id="list_emp" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Empreendimentos</h4>
      				</div>
				    <div class="modal-body">
				   		<table class="table table-bordered table-condensed table-striped table-hover">
							<thead>
								<tr>
									<th colspan="2">Nome</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="item in empreendimentos">
									<td>{{ item.nome_empreendimento }}</td>
									<td width="80">
										<button ng-click="addEmp(item)" ng-disabled="verificarEmpSelected(item)" class="btn btn-success btn-xs" type="button">
												<i class="fa fa-check-square-o"></i> Selecionar
										</button>
									</td>
								</tr>
							</tbody>
						</table>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal load CEP-->
		<div class="modal fade" id="busca-cep" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
    				<div class="modal-header">
						<h4>Aguarde</h4>
      				</div>

				    <div class="modal-body">
				   		<strong>buscando CEP ...</strong>
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

	<!-- Google Maps API -->
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>

	<script src="js/bootstrap-treeview.js"></script>
	<script src="js/jquery.noty.packaged.js"></script>

	<!-- AngularJS -->
	<script type="text/javascript" src="bower_components/angular/angular.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/angular-strap/2.1.2/angular-strap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/angular-strap/2.1.2/angular-strap.tpl.min.js"></script>
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
	<script src="js/angular-controller/clientes-controller.js"></script>
	<script type="text/javascript"></script>>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

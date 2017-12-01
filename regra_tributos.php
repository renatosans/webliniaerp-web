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
		.panel.panel-default {
		    overflow: visible !important;
		}
		.has-error {
			color:#A94442;
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

  <body class="overflow-hidden" ng-controller="RegraTributosController" ng-cloak>
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
					<li class="active"><i class="fa fa-tags"></i> Regra de Tributos</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-tags"></i> Regra de Tributos</h3>
					<br/>
					<a class="btn btn-info" id="btn-novo"  ng-click="showBoxNovo()"><i class="fa fa-plus-circle"></i> {{ editing_filtro && 'Novo Filtro de Tributos' || 'Nova Regra de Tributos' }}</a><br/><br/>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md" style="padding-top: 0 !important;">
				<div class="alert alert-sistema" style="display:none"></div>
				<div class="panel-tab" ng-if="editing">
					<ul class="wizard-steps wizard-demo" id="wizardDemo1"> 
						<li ng-class="{'active':editing_filtro == false}">
							<a href="" data-toggle="tab"  ng-click="viewFiltros(false)">Regra de Tributos </a>
						</li> 
						<li  ng-class="{'active':editing_filtro}"  ng-click="viewFiltros(true)">
							<a href="" data-toggle="tab">Filtro Tributos</a>
						</li> 
					</ul>
				</div>
				<div class="panel panel-default" id="box-novo" style="display:none">
					<div class="panel-heading">
					<i class="fa fa-plus-circle"></i> {{ editing_filtro == true && 'Adicionar Filtro a Regra' || 'Nova Regra de Tributos' }}
					<strong ng-if="editing_filtro"> {{ regra_tributos.dsc_regra_tributos }}</strong>
					</div>
					<div class="panel-body">
					<div  class="row" ng-if="editing_filtro == false">
						<div class="col-sm-12">
							<div id="dsc_regra_tributos" class="form-group">
								<label class="control-label">Descrição <span style="color:red;font-weight: bold;">*</span></label>
								<input type="text" class="form-control" ng-model="regra_tributos.dsc_regra_tributos">
							</div>
						</div>
					</div>
					<div ng-if="editing_filtro">
					<fieldset>
						<legend>Filtro</legend>
						<div class="row">
						    <div class="col-sm-2">
								<div class="form-group" id="cod_operacao">
									<label class="ccontrol-label">Operação</label> 
									<select chosen ng-change="ClearChosenSelect('cod_operacao')"
								    option="chosen_operacao"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.filtro_tributos.cod_operacao"
								    ng-options="operacao.cod_operacao as operacao.dsc_operacao for operacao in chosen_operacao">
									</select>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group" id="cod_estado_origem">
									<label class="ccontrol-label">Estado de Origem</label> 
									<select chosen ng-change="ClearChosenSelect('cod_estado_origem')"
									    option="chosen_estado"
									    allow-single-deselect="true"
									    ng-model="regra_tributos.filtro_tributos.cod_estado_origem"
									    ng-options="estado.id as estado.nome for estado in chosen_estado">
									</select>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group" id="cod_estado_destino">
									<label class="ccontrol-label">Estado Destino</label> 
									<select chosen ng-change="ClearChosenSelect('cod_estado_destino')"
								    option="chosen_estado"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.filtro_tributos.cod_estado_destino"
								    ng-options="estado.id as estado.nome for estado in chosen_estado">
									</select>
								</div>
							</div>
							<div class="col-sm-2">
								<div id="dta_inicio_vigencia" class="form-group">
									<label class="control-label">Dta Inicio Vigencia</label>
									<input type="text" class="form-control input-sm" ui-mask="99/99/9999" ng-model="regra_tributos.filtro_tributos.dta_inicio_vigencia">
								</div>
							</div>
							<div class="col-sm-2">
								<div id="dta_fim_vigencia" class="form-group">
									<label class="control-label">Dta Fim Vigencia </label>
									<input type="text" class="form-control input-sm" ui-mask="99/99/9999" ng-model="regra_tributos.filtro_tributos.dta_fim_vigencia">
								</div>
							</div>
							
						</div>
						<div class="row">
							<div class="col-sm-3">
								<div class="form-group" id="cod_zoneamento_emitente">
									<label class="ccontrol-label">Zoneamento Emitente</label> 
									<select chosen ng-change="ClearChosenSelect('cod_zoneamento_emitente')"
								    option="chosen_zoneamento"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.filtro_tributos.cod_zoneamento_emitente"
								    ng-options="zoneamento.cod_zoneamento as zoneamento.dsc_zoneamento for zoneamento in chosen_zoneamento">
									</select>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group" id="cod_regime_especial_emitente">
									<label class="ccontrol-label">Regime Especial Emitente</label> 
									<select chosen ng-change="ClearChosenSelect('cod_regime_especial_emitente')"
								    option="chosen_regime_especial_emitente"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.filtro_tributos.cod_regime_especial_emitente"
								    ng-options="regime_especial_emitente.cod_regime_especial as regime_especial_emitente.dsc_regime_especial for regime_especial_emitente in chosen_regime_especial_emitente">
									</select>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group" id="cod_tipo_empresa_emitente">
									<label class="ccontrol-label">Tipo Empresa Emitente</label> 
									<select chosen ng-change="ClearChosenSelect('cod_tipo_empresa_emitente')"
								    option="chosen_tipo_empresa"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.filtro_tributos.cod_tipo_empresa_emitente"
								    ng-options="tipo_empresa.cod_controle_item_nfe as tipo_empresa.nme_item for tipo_empresa in chosen_tipo_empresa">
									</select>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group" id="cod_regime_tributario_emitente">
									<label class="ccontrol-label">Regime Tributário Emitente</label> 
									<select chosen ng-change="ClearChosenSelect('cod_regime_tributario_emitente')"
								    option="chosen_regime_tributario_emitente"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.filtro_tributos.cod_regime_tributario_emitente"
								    ng-options="regime_tributario_emitente.cod_controle_item_nfe as regime_tributario_emitente.nme_item for regime_tributario_emitente in chosen_regime_tributario_emitente">
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-3">
								<div class="form-group" id="flg_cont_ipi_emitente">
									<label for="" class="control-label">Emitente Contribuinte IPI</label>
									<div class="form-group">
										<label class="label-radio inline">
											<input ng-model="regra_tributos.filtro_tributos.flg_cont_ipi_emitente" ng-true-value="null"  type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>vazio</span>
										</label>
										<label class="label-radio inline">
											<input ng-model="regra_tributos.filtro_tributos.flg_cont_ipi_emitente" value="0" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Não</span>
										</label>

										<label class="label-radio inline">
											<input ng-model="regra_tributos.filtro_tributos.flg_cont_ipi_emitente" value="1" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Sim</span>
										</label>
									</div>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group" id="flg_cont_icms_emitente">
									<label for="" class="control-label">Emitente Contribuinte ICMS</label>
									<div class="form-group">
										<label class="label-radio inline">
											<input ng-model="regra_tributos.filtro_tributos.flg_cont_icms_emitente" ng-true-value="null"  type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Vazio</span>
										</label>

										<label class="label-radio inline">
											<input ng-model="regra_tributos.filtro_tributos.flg_cont_icms_emitente" value="0" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Não</span>
										</label>

										<label class="label-radio inline">
											<input ng-model="regra_tributos.filtro_tributos.flg_cont_icms_emitente" value="1" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Sim</span>
										</label>
									</div>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group" id="cod_crt_emitente">
									<label class="ccontrol-label">CRT Emitente</label> 
									<select chosen ng-change="ClearChosenSelect('cod_crt_emitente')"
								    option="chosen_crt_emitente"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.filtro_tributos.cod_crt_emitente"
								    ng-options="crt_emitente.cod_controle_item_nfe as crt_emitente.nme_item for crt_emitente in chosen_crt_emitente">
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-3">
								<div class="form-group" id="cod_zoneamento_destinatario">
									<label class="ccontrol-label">Zoneamento Destinatário</label> 
									<select chosen ng-change="ClearChosenSelect('cod_zoneamento_destinatario')"
								    option="chosen_zoneamento"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.filtro_tributos.cod_zoneamento_destinatario"
								    ng-options="zoneamento.cod_zoneamento as zoneamento.dsc_zoneamento for zoneamento in chosen_zoneamento">
									</select>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group" id="cod_regime_especial_destinatario">
									<label class="ccontrol-label">Regime Especial Destinatário</label> 
									<select chosen ng-change="ClearChosenSelect('cod_regime_especial_destinatario')"
								    option="chosen_regime_especial_destinatario"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.filtro_tributos.cod_regime_especial_destinatario"
								    ng-options="regime_especial_destinatario.cod_regime_especial as regime_especial_destinatario.dsc_regime_especial for regime_especial_destinatario in chosen_regime_especial_destinatario">
									</select>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group" id="cod_tipo_empresa_destinatario">
									<label class="ccontrol-label">Tipo Empresa Destinatário</label> 
									<select chosen ng-change="ClearChosenSelect('cod_tipo_empresa_destinatario')"
								    option="chosen_tipo_empresa"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.filtro_tributos.cod_tipo_empresa_destinatario"
								    ng-options="tipo_empresa.cod_controle_item_nfe as tipo_empresa.nme_item for tipo_empresa in chosen_tipo_empresa">
									</select>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group" id="cod_regime_tributario_destinatario">
									<label class="ccontrol-label">Regime Tributário Destinatário</label> 
									<select chosen ng-change="ClearChosenSelect('cod_regime_tributario_destinatario')"
								    option="chosen_regime_tributario_destinatario"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.filtro_tributos.cod_regime_tributario_destinatario"
								    ng-options="regime_tributario_destinatario.cod_controle_item_nfe as regime_tributario_destinatario.nme_item for regime_tributario_destinatario in chosen_regime_tributario_destinatario">
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-3">
								<div class="form-group" id="flg_cont_ipi_destinatario">
									<label for="" class="control-label">Destinatário Contribuinte IPI</label>
									<div class="form-group">
										<label class="label-radio inline">
											<input ng-model="regra_tributos.filtro_tributos.flg_cont_ipi_destinatario" ng-true-value="null"  type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Vazio</span>
										</label>

										<label class="label-radio inline">
											<input ng-model="regra_tributos.filtro_tributos.flg_cont_ipi_destinatario" value="0" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Não</span>
										</label>

										<label class="label-radio inline">
											<input ng-model="regra_tributos.filtro_tributos.flg_cont_ipi_destinatario" value="1" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Sim</span>
										</label>
									</div>
								</div>
							</div>

							<div class="col-sm-3">
								<div class="form-group" id="flg_cont_icms_destinatario">
									<label for="" class="control-label">Destinatário Contribuinte ICMS</label>
									<div class="form-group">
										<label class="label-radio inline">
											<input ng-model="regra_tributos.filtro_tributos.flg_cont_icms_destinatario" ng-true-value="null"  type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Vazio</span>
										</label>
										<label class="label-radio inline">
											<input ng-model="regra_tributos.filtro_tributos.flg_cont_icms_destinatario" value="0" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Não</span>
										</label>

										<label class="label-radio inline">
											<input ng-model="regra_tributos.filtro_tributos.flg_cont_icms_destinatario" value="1" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Sim</span>
										</label>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-3">
								<div class="form-group" id="cod_destinacao">
									<label class="ccontrol-label">Destinação</label> 
									<select chosen ng-change="ClearChosenSelect('cod_destinacao')"
								    option="chosen_cod_destinacao"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.filtro_tributos.cod_destinacao"
								    ng-options="destinacao.cod_controle_item_nfe as destinacao.nme_item for destinacao in chosen_cod_destinacao">
									</select>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group" id="cod_origem_mercadoria">
									<label class="ccontrol-label">Origem Mercadoria</label> 
									<select chosen ng-change="ClearChosenSelect('cod_origem_mercadoria')"
								    option="chosen_origem_mercadoria"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.filtro_tributos.cod_origem_mercadoria"
								    ng-options="origem_mercadoria.cod_controle_item_nfe as origem_mercadoria.nme_item for origem_mercadoria in chosen_origem_mercadoria">
									</select>
								</div>
							</div>

							<div class="col-sm-3">
								<div class="form-group" id="cod_situacao">
									<label class="ccontrol-label">Situação Especial</label> 
									<select chosen ng-change="ClearChosenSelect('cod_situacao')"
								    option="chosen_situacao"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.filtro_tributos.cod_situacao"
								    ng-options="situacao.cod_situacao_especial as situacao.dsc_situacao_especial for situacao in chosen_situacao">
									</select>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group" id="cod_forma_aquisicao">
									<label class="ccontrol-label">Forma Aquisição</label> 
									<select chosen ng-change="ClearChosenSelect('cod_forma_aquisicao')"
								    option="chosen_forma_aquisicao"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.filtro_tributos.cod_forma_aquisicao"
								    ng-options="forma_aquisicao.cod_controle_item_nfe as forma_aquisicao.nme_item for forma_aquisicao in chosen_forma_aquisicao">
									</select>
								</div>
							</div>	
						</div>

						<div class="row">
							<div class="col-sm-4" id="cod_ncm">
								<label class="control-label">NCM</label>
								<div class="input-group">
									<input ng-click="selNcm()" type="text" class="form-control input-sm" ng-model="regra_tributos.filtro_tributos.ncm_view" readonly="readonly" style="cursor: pointer;" />
									<span class="input-group-btn">
										<button ng-click="selNcm()"  type="button" class="btn btn-sm"><i class="fa-search fa"></i></button>
									</span>
								</div>
							</div>
							<div class="col-sm-1" id="ex_tipi">
								<div class="form-group">
									<label class="ccontrol-label">EX TIPI</label> 
										<input  onKeyPress="return SomenteNumero(event);" ng-model="regra_tributos.filtro_tributos.ex_tipi" type="text" class="form-control input-sm">
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group" id="cod_especializacao_ncm">
									<label class="ccontrol-label">Especialização NCM</label> 
									<select chosen ng-change="ClearChosenSelect('cod_especializacao_ncm')"
								    option="chosen_especializacao_ncm"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.filtro_tributos.cod_especializacao_ncm"
								    ng-options="especializacao_ncm.cod_especializacao_ncm as especializacao_ncm.dsc_especializacao_ncm for especializacao_ncm in chosen_especializacao_ncm">
									</select>
								</div>
							</div>

							<div class="col-sm-2">
								<div class="form-group" id="codigo_barra">
									<label class="control-label">Núm. CEST</label>
									<input ng-model="regra_tributos.filtro_tributos.num_cest" type="text"  class="form-control input-sm" onKeyPress="return SomenteNumero(event);">
								</div>
							</div>
						</div>
					</fieldset>

					<fieldset>
						<legend>ICMS</legend>
						<div class="row" >
							<div class="col-sm-7">
								<div class="form-group" id="cod_cstcsosn">
									<label class="ccontrol-label">CSTCSOSN</label> 
									<select chosen ng-change="ClearChosenSelect('cod_cstcsosn')"
								    option="chosen_cstcsosn"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.configuracao_icms.cod_cstcsosn"
								    ng-options="cstcsosn.cod_cstcsosn as ('CST: '+cstcsosn.dsc_cst+' - CSOSN: '+cstcsosn.dsc_cson+' - '+cstcsosn.dsc_geral) for cstcsosn in chosen_cstcsosn">
									</select>
								</div>
							</div>

							<div class="col-sm-2">
								<div id="tag_icms" class="form-group">
									<label class="control-label">TAG ICMS</label>
									<input type="text" class="form-control input-sm" ng-model="regra_tributos.configuracao_icms.tag_icms">
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-sm-3">
								<div class="form-group" id="cod_modalidade_base_icms">
									<label class="ccontrol-label">Modalidade Base ICMS</label> 
									<select chosen ng-change="ClearChosenSelect('cod_modalidade_base_icms')"
								    option="chosen_base_icms"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.configuracao_icms.cod_modalidade_base_icms"
								    ng-options="base_icms.cod_controle_item_nfe as base_icms.nme_item for base_icms in chosen_base_icms">
									</select>
								</div>
							</div>

							<div class="col-sm-2">
								<div id="vlr_aliquota_icms" class="form-group">
									<label class="control-label">Alíquota ICMS</label>
									<input type="text" class="form-control input-sm" mask-moeda ng-model="regra_tributos.configuracao_icms.vlr_aliquota_icms">
								</div>
							</div>

							<div class="col-sm-2">
								<div id="num_percentual_reducao_icms" class="form-group">
									<label class="control-label">% Redução ICMS</label>
									<input type="text" class="form-control input-sm" mask-moeda ng-model="regra_tributos.configuracao_icms.num_percentual_reducao_icms">
								</div>
							</div>

							<div class="col-sm-2">
								<div id="num_percentual_mva_proprio" class="form-group">
									<label class="control-label">% MVA Proprio</label>
									<input type="text" class="form-control input-sm" mask-moeda ng-model="regra_tributos.configuracao_icms.num_percentual_mva_proprio">
								</div>
							</div>
						</div>

						<div class="row" >
							<div class="col-sm-3">
								<div class="form-group" id="cod_modalidade_base_icms_st">
									<label class="ccontrol-label">Modalidade Base ICMS ST</label> 
									<select chosen ng-change="ClearChosenSelect('cod_modalidade_base_icms_st')"
								    option="chosen_base_icms_st"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.configuracao_icms.cod_modalidade_base_icms_st"
								    ng-options="base_icms_st.cod_controle_item_nfe as base_icms_st.nme_item for base_icms_st in chosen_base_icms_st">
									</select>
								</div>
							</div>

							<div class="col-sm-2">
								<div id="vlr_aliquota_icms_st" class="form-group">
									<label class="control-label">Alíquota ICMS ST</label>
									<input type="text" class="form-control input-sm" mask-moeda ng-model="regra_tributos.configuracao_icms.vlr_aliquota_icms_st">
								</div>
							</div>

							<div class="col-sm-2">
								<div id="num_percentual_reducao_icms_st" class="form-group">
									<label class="control-label">% Redução ICMS ST</label>
									<input type="text" class="form-control input-sm" mask-moeda ng-model="regra_tributos.configuracao_icms.num_percentual_reducao_icms_st">
								</div>
							</div>

							<div class="col-sm-2">
								<div id="num_percentual_mva_ajustado_st" class="form-group">
									<label class="control-label">% MVA Ajustado ST</label>
									<input type="text" class="form-control input-sm" mask-moeda ng-model="regra_tributos.configuracao_icms.num_percentual_mva_ajustado_st">
								</div>
							</div>

							<div class="col-sm-2">
								<div id="vlr_aliquota_icms_proprio_st" class="form-group">
									<label class="control-label">Alíquota ICMS Proprio ST</label>
									<input type="text" class="form-control input-sm" mask-moeda ng-model="regra_tributos.configuracao_icms.vlr_aliquota_icms_proprio_st">
								</div>
							</div>
						</div>

						<div class="row" >
							<div class="col-sm-5">
								<div class="form-group" id="cod_motivo_des_icms">
									<label class="ccontrol-label">Motivo Desoneração ICMS</label> 
									<select chosen ng-change="ClearChosenSelect('cod_motivo_des_icms')"
								    option="chosen_motivo_des_icms"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.configuracao_icms.cod_motivo_des_icms"
								    ng-options="motivo_des_icms.cod_controle_item_nfe as motivo_des_icms.nme_item for motivo_des_icms in chosen_motivo_des_icms">
									</select>
								</div>
							</div>

							<div class="col-sm-2">
								<div id="num_percentual_base_icms_proprio" class="form-group">
									<label class="control-label">% Base ICMS Proprio</label>
									<input type="text" class="form-control input-sm" mask-moeda ng-model="regra_tributos.configuracao_icms.num_percentual_base_icms_proprio">
								</div>
							</div>

							<div class="col-sm-2">
								<div id="num_percentual_diferimento" class="form-group">
									<label class="control-label">% Diferimento</label>
									<input type="text" class="form-control input-sm" mask-moeda ng-model="regra_tributos.configuracao_icms.num_percentual_diferimento">
								</div>
							</div>

							<div class="col-sm-2">
								<div id="num_percentual_diferimento_icms" class="form-group">
									<label class="control-label">% Diferimento ICMS</label>
									<input type="text" class="form-control input-sm" mask-moeda ng-model="regra_tributos.configuracao_icms.num_percentual_diferimento_icms">
								</div>
							</div>
						</div>

						<div class="row" >
							<div class="col-sm-3">
								<div class="form-group" id="cod_convenio_st">
									<label class="ccontrol-label">Convênio ST</label> 
									<select chosen 
								    option="chosen_convenio_st"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.configuracao_icms.cod_convenio_st"
								    ng-options="convenio_st.cod_controle_item_nfe as convenio_st.nme_item for convenio_st in chosen_convenio_st">
									</select>
								</div>
							</div>

							<div class="col-sm-3">
								<div class="form-group" id="cod_base_tributaria">
									<label class="ccontrol-label">Base Tributária</label> 
									<select chosen ng-change="ClearChosenSelect('cod_base_tributaria')"
								    option="chosen_base_tributaria"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.configuracao_icms.cod_base_tributaria"
								    ng-options="base_tributaria.cod_base_tributaria as base_tributaria.dsc_base_tributaria for base_tributaria in chosen_base_tributaria">
									</select>
								</div>
							</div>

							<div class="col-sm-2" 
								ng-if="regra_tributos.filtro_tributos.cod_estado_origem != regra_tributos.filtro_tributos.cod_estado_destino">
								<div id="num_percentual_mva_proprio" class="form-group">
									<label class="control-label">Aliquota ICMS UF Destino</label>
									<input type="text" class="form-control input-sm" mask-moeda ng-model="regra_tributos.configuracao_icms.vlr_aliquota_icms_uf_destino">
								</div>
							</div>

							<div class="col-sm-2"
								ng-if="regra_tributos.filtro_tributos.cod_estado_origem != regra_tributos.filtro_tributos.cod_estado_destino">
								<div id="num_percentual_mva_proprio" class="form-group">
									<label class="control-label">% FCP</label>
									<input type="text" class="form-control input-sm" mask-moeda ng-model="regra_tributos.configuracao_icms.num_percentual_fcp">
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-sm-2">
								<div class="form-group" id="flg_destacar_icms">
									<label for="" class="control-label">Destacar ICMS</label>
									<div class="form-group">
										<label class="label-radio inline">
											<input ng-model="regra_tributos.configuracao_icms.flg_destacar_icms" value="0" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Não</span>
										</label>

										<label class="label-radio inline">
											<input ng-model="regra_tributos.configuracao_icms.flg_destacar_icms" value="1" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Sim</span>
										</label>
									</div>
								</div>
							</div>

							<div class="col-sm-2">
								<div class="form-group" id="flg_destacar_icms_st">
									<label for="" class="control-label">Destacar ICMS ST</label>
									<div class="form-group">
										<label class="label-radio inline">
											<input ng-model="regra_tributos.configuracao_icms.flg_destacar_icms_st" value="0" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Não</span>
										</label>

										<label class="label-radio inline">
											<input ng-model="regra_tributos.configuracao_icms.flg_destacar_icms_st" value="1" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Sim</span>
										</label>
									</div>
								</div>
							</div>

							<div class="col-sm-2">
								<div class="form-group" id="flg_destacar_icms_des">
									<label for="" class="control-label">Destacar ICMS Deson.</label>
									<div class="form-group">
										<label class="label-radio inline">
											<input ng-model="regra_tributos.configuracao_icms.flg_destacar_icms_des" value="0" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Não</span>
										</label>

										<label class="label-radio inline">
											<input ng-model="regra_tributos.configuracao_icms.flg_destacar_icms_des" value="1" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Sim</span>
										</label>
									</div>
								</div>
							</div>

							<div class="col-sm-2">
								<div class="form-group" id="flg_incluir_frete_base_icms">
									<label for="" class="control-label">Frete Base ICMS</label>
									<div class="form-group">
										<label class="label-radio inline">
											<input ng-model="regra_tributos.configuracao_icms.flg_incluir_frete_base_icms" value="0" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Não</span>
										</label>

										<label class="label-radio inline">
											<input ng-model="regra_tributos.configuracao_icms.flg_incluir_frete_base_icms" value="1" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Sim</span>
										</label>
									</div>
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group" id="flg_incluir_ipi_base_icms">
									<label for="" class="control-label">Incluir IPI Base ICMS</label>
									<div class="form-group">
										<label class="label-radio inline">
											<input ng-model="regra_tributos.configuracao_icms.flg_incluir_ipi_base_icms" value="0" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Não</span>
										</label>

										<label class="label-radio inline">
											<input ng-model="regra_tributos.configuracao_icms.flg_incluir_ipi_base_icms" value="1" type="radio" class="inline-radio">
											<span class="custom-radio"></span>
											<span>Sim</span>
										</label>
									</div>
								</div>
							</div>
						</div>
					</fieldset>
					
					<fieldset>
						<legend>IPI</legend>
						<div class="row">
								<div class="col-sm-2">
										<div class="form-group" id="cod_base_tributaria">
											<label class="ccontrol-label">CST IPI</label> 
											<select chosen ng-change="ClearChosenSelect('cod_base_tributaria')"
										    option="chosen_cst_ipi"
										    allow-single-deselect="true"
										    ng-model="regra_tributos.configuracao_ipi.cst_ipi"
										    ng-options="cst_ipi.num_item as ( cst_ipi.dsc_completa )  for cst_ipi in chosen_cst_ipi">
											</select>
										</div>
									</div>
								<div class="col-sm-2">
									<div id="vlr_aliquota" class="form-group">
										<label class="control-label">vlr Aliquota</label>
										<input type="text" class="form-control input-sm" mask-moeda ng-model="regra_tributos.configuracao_ipi.vlr_aliquota">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group" id="cod_base_tributaria">
										<label class="ccontrol-label">Base Tributaria</label> 
										<select chosen ng-change="ClearChosenSelect('cod_base_tributaria')"
									    option="chosen_base_tributaria"
									    allow-single-deselect="true"
									    ng-model="regra_tributos.configuracao_ipi.cod_base_tributaria"
									    ng-options="base_tributaria.cod_base_tributaria as base_tributaria.dsc_base_tributaria for base_tributaria in chosen_base_tributaria">
										</select>
									</div>
								</div>
								<div class="col-sm-5">
									<div class="form-group" id="cod_tipo_tributacao_ipi">
										<label class="ccontrol-label">Tipo Tributação IPI</label> 
										<select chosen ng-change="ClearChosenSelect('cod_tipo_tributacao_ipi')"
									    option="chosen_tributacao_ipi"
									    allow-single-deselect="true"
									    ng-model="regra_tributos.configuracao_ipi.cod_tipo_tributacao_ipi"
									    ng-options="tributacao_ipi.cod_controle_item_nfe as tributacao_ipi.nme_item for tributacao_ipi in chosen_tributacao_ipi">
										</select>
									</div>
								</div>
						</div>
					</fieldset>
					
					<fieldset>
						<legend>PIS COFINS</legend>
						<div class="row" >
							<div class="col-sm-2">
									<div class="form-group" id="cod_base_tributaria">
										<label class="ccontrol-label">CST PIS COFINS</label> 
										<select chosen ng-change="ClearChosenSelect('cod_base_tributaria')"
										   option="chosen_pis_cofins"
										   allow-single-deselect="true"
										   ng-model="regra_tributos.configuracao_pis_cofins.cst_pis_cofins"
										   ng-options="cst_ipi.num_item as (cst_ipi.dsc_completa) for cst_ipi in chosen_pis_cofins">
										</select>
									</div>
									<!--<div id="cst_pis_cofins" class="form-group">
										<label class="control-label">CST PIS COFINS</label>
										<input type="text" class="form-control input-sm" ng-model="regra_tributos.configuracao_pis_cofins.cst_pis_cofins">
									</div>-->
								</div>
							<div class="col-sm-2">
									<div id="vlr_aliquota_pis" class="form-group">
										<label class="control-label">Aliquota PIS</label>
										<input type="text" class="form-control input-sm" mask-moeda ng-model="regra_tributos.configuracao_pis_cofins.vlr_aliquota_pis">
									</div>
								</div>
							<div class="col-sm-2">
								<div id="vlr_aliquota_cofins" class="form-group">
									<label class="control-label">Aliquota COFINS</label>
									<input type="text" class="form-control input-sm" mask-moeda ng-model="regra_tributos.configuracao_pis_cofins.vlr_aliquota_cofins">
								</div>
							</div>
							<div class="col-sm-2">
								<div id="vlr_aliquota_pis_st" class="form-group">
									<label class="control-label">Aliquota PIS ST</label>
									<input type="text" class="form-control input-sm" mask-moeda ng-model="regra_tributos.configuracao_pis_cofins.vlr_aliquota_pis_st">
								</div>
							</div>
							<div class="col-sm-2">
								<div id="vlr_aliquota_cofins_st" class="form-group">
									<label class="control-label">Aliquota COFINS ST</label>
									<input type="text" class="form-control input-sm" mask-moeda ng-model="regra_tributos.configuracao_pis_cofins.vlr_aliquota_cofins_st">
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group" id="cod_base_tributaria">
									<label class="ccontrol-label">Base Tributaria</label> 
									<select chosen ng-change="ClearChosenSelect('cod_base_tributaria')"
								    option="chosen_base_tributaria"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.configuracao_pis_cofins.cod_base_tributaria"
								    ng-options="base_tributarua.cod_base_tributaria as base_tributarua.dsc_base_tributaria for base_tributarua in chosen_base_tributaria">
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-5">
								<div class="form-group" id="cod_tipo_tributacao_pis_cofins">
									<label class="ccontrol-label">Tipo Tributação PIS COFINS</label> 
									<select chosen ng-change="ClearChosenSelect('cod_tipo_tributacao_pis_cofins')"
								    option="chosen_tributacao_pis_cofins"
								    allow-single-deselect="true"
								    ng-model="regra_tributos.configuracao_pis_cofins.cod_tipo_tributacao_pis_cofins"
								    ng-options="tributacao_pis_cofins.cod_controle_item_nfe as tributacao_pis_cofins.nme_item for tributacao_pis_cofins in chosen_tributacao_pis_cofins">
									</select>
								</div>
							</div>
						</div>
					</fieldset>
					
					</div>
					<br/></br/>
					<div class="row" ng-if="editing_filtro == false">
						<div class="col-sm-12">
							<div class="pull-right">
								<button ng-click="showBoxNovo(); reset();" type="submit" class="btn btn-danger btn-sm">
									<i class="fa fa-times-circle"></i> Cancelar
								</button>
								<button  ng-click="salvar()" id="salvar-regra-tributos" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." type="button" class="btn btn-success btn-sm">
									<i class="fa fa-save"></i> Salvar
								</button>
							</div>
						</div>
					</div>
					<div class="row" ng-if="editing_filtro">
						<div class="col-sm-12">
							<div class="pull-right">
								<button ng-click="viewFiltros(false); reset();" type="submit" class="btn btn-warning btn-sm">
									<i class="fa fa-reply"></i> Voltar
								</button>
								<button ng-click="showBoxNovo(); resetFiltro();" type="submit" class="btn btn-danger btn-sm">
									<i class="fa fa-times-circle"></i> Cancelar
								</button>
								<button  ng-click="salvarFiltro()" id="salvar-filtros-tributos" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." type="button" class="btn btn-success btn-sm">
									<i class="fa fa-save"></i> Salvar
								</button>
							</div>
						</div>
					</div>

				</div>
				</div><!-- /panel -->

				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-tasks"></i> {{ editing_filtro  && 'Filtros Cadastrados Relazionados a Regra ' || 'Regras Cadastradas'  }} 
						<strong ng-if="editing_filtro">{{ regra_tributos.dsc_regra_tributos }}</strong>
					</div>

					<div class="panel-body" ng-if="editing_filtro == false">
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
								<tr ng-repeat="item in regras" bs-tooltip>
									<td width="80">{{ item.cod_regra_tributos }}</td>
									<td>{{ item.dsc_regra_tributos }}</td>
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
									<td colspan="3" class="text-center" ng-if="regras.length == 0 && regras != null">
										Nenhuma Situação Especial Encontrada
									</td>
								</tr>
								<tr>
									<td colspan="3" class="text-center" ng-if="regras == null">
										<i class='fa fa-refresh fa-spin'></i> Carregando
									</td>
								</tr>
							</tbody>
						</table>	
						<div class="pull-right">
							<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.regras.length > 1">
								<li ng-repeat="item in paginacao.regras" ng-class="{'active': item.current}">
									<a href="" h ng-click="load(item.offset,item.limit)">{{ item.index }}</a>
								</li>
							</ul>
						</div>
					</div>

					<div class="panel-body" ng-if="editing_filtro">
						<div  class="alert alert-list" style="display:none"></div>
						<table class="table table-bordered table-condensed table-striped table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Operação</th>
									<th>Estado Origem</th>
									<th>Estado Destino</th>
									<th>Dta. Inicio Vigencia</th>
									<th>Dta. Fim Vigencia</th>
									<th width="80" style="text-align: center;">Opções</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="item in filtros" bs-tooltip>
									<td>#{{ item.cod_filtro_tributos }}</td>
									<td>{{ item.dsc_operacao }}</td>
									<td>{{ item.nme_estado_origem }}</td>
									<td>{{ item.nme_estado_destino }}</td>
									<td>{{ item.dta_inicio_vigencia | dateFormat : 'date'}}</td>
									<td>{{ item.dta_fim_vigencia | dateFormat : 'date'}}</td>
									<td align="center">
										<button type="button" ng-click="editarFiltro(item)" tooltip="Editar" title="Editar" class="btn btn-xs btn-warning" data-toggle="tooltip">
											<i class="fa fa-edit"></i>
										</button>
										<button type="button" ng-click="deleteFiltro(item)" tooltip="Excluir" title="Excluir" class="btn btn-xs btn-danger delete" data-toggle="tooltip">
											<i class="fa fa-trash-o"></i>
										</button>
									</td>
								</tr>
								<tr>
									<td colspan="5" class="text-center" ng-if="filtros.length == 0 && filtros != null">
										Nenhuma Situação Especial Encontrada
									</td>
								</tr>
								<tr>
									<td colspan="5" class="text-center" ng-if="filtros == null">
										<i class='fa fa-refresh fa-spin'></i> Carregando
									</td>
								</tr>
							</tbody>
						</table>	
						<div class="pull-right">
							<ul class="pagination pagination-sm m-top-none" ng-show="paginacao.filtros.length > 1">
								<li ng-repeat="item in paginacao.filtros" ng-class="{'active': item.current}">
									<a href="" h ng-click="loadFiltros(item.offset,item.limit)">{{ item.index }}</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /main-container -->

		<!-- /Modal NCM -->
		<div class="modal fade" id="list-ncm" style="display:none">
  			<div class="modal-dialog modal-lg">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>NCM</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.ncm"  ng-enter="loadNcm(0,10)" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadNcm(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
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
									<tr ng-if="lista_ncm != false && (lista_ncm.length <= 0 || lista_ncm == null)">
										<th class="text-center" colspan="9" style="text-align:center"><strong>Carregando</strong><img src="assets/imagens/progresso_venda.gif"></th>
									</tr>
									<tr ng-if="lista_ncm == false">
										<th class="text-center" colspan="9" colspan="9" style="text-align:center">Não a resultados para a busca</th>
									</tr>
									<thead ng-show="(lista_ncm.length != 0)">
										<tr>
											<th >NCM</th>
											<th >Descrição</th>
											<th >% IPI</th>
											<th colspan="2">selecionar</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in lista_ncm">
											<td>{{ item.cod_ncm }}</td>
											<td>{{ item.dsc_ncm | limitTo : 95 : 0 }} <a href="" style="text-decoration: underline;color: #000;" ng-if="item.dsc_ncm.length > 95">...</a></td>
											<td ng-if="item.num_percentual_ipi !=null">{{ item.num_percentual_ipi | numberFormat:2:',':'.' }}%</td>
											<td ng-if="item.num_percentual_ipi ==null">NT</td>
											<td width="50" align="center">
												<button type="button" class="btn btn-xs btn-success" ng-click="changeNcm(item)">
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
				    			<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao.especializacao_ncm.length > 1">
									<li ng-repeat="item in paginacao.especializacao_ncm" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadNcm(item.offset,item.limit)">{{ item.index }}</a>
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
    <script src="js/angular-chosen.js"></script>
    <script type="text/javascript">
    	var addParamModule = ['angular.chosen'] ;
    </script>
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/regra_tributos-controller.js?version=<?php  echo date("dmY-His", filemtime("js/angular-controller/regra_tributos-controller.js")) ?>"></script>
	<script type="text/javascript"></script>>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

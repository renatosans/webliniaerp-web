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

  <body class="overflow-hidden" ng-controller="NotaFiscalServicoController" ng-cloak>
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
					 <li><i class="fa fa-signal"></i> <a href="vendas.php">Vendas</a></li>
					 <li><i class="fa fa-barcode"></i> <a href="notas-fiscais.php">Notas Fiscais</a></li>
					 <li class="active"><i class="fa fa-file-text-o"></i> Visualização de Nota Fiscal</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-file-text-o"></i> Visualização de Nota Fiscal</h3>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title">NFS-e Nº {{ nf.outros.num_documento_fiscal }}</h3>
					</div>

					<div class="panel-tab clearfix">
						<ul class="tab-bar">
							<li class="active"><a href="#geral" data-toggle="tab"><i class="fa fa-gear"></i> Dados de Emissão</a></li>
							<li><a href="#emitente" data-toggle="tab"><i class="fa fa-building-o"></i> Dados do Emitente</a></li>
							<li><a href="#destinatario" data-toggle="tab"><i class="fa fa-user"></i> Dados do Tomador</a></li>
							<li><a href="#servicos" data-toggle="tab"><i class="fa fa-list"></i> Serviços</a></li>
							<li><a href="#resumo" data-toggle="tab"><i class="fa fa-bars"></i> Resumo da NFS-e</a></li>
						</ul>
					</div>

					<div class="panel-body">
						<div class="tab-content">
							<div class="tab-pane fade in active" id="geral">
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group" id="cod_regra_tributacao">
											<label class="control-label">Regra de Tributação</label> 
											<div class="input-group">
												<input type="text" class="form-control" readonly="readonly" style="cursor: pointer;"
													value="{{ nf.regra_tributacao.nme_regra_servico }}" 
													ng-click="showModal('regrasTributacao')"
													ng-disabled="nf.outros.cod_nota_fiscal"/>
												<span class="input-group-btn">
													<button type="button" class="btn btn-default"
														ng-click="showModal('regrasTributacao')"
														ng-disabled="nf.outros.cod_nota_fiscal">
														<i class="fa fa-tags"></i>
													</button>
												</span>
											</div>
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Data de Emissão</label>
											<input type="text" class="form-control input-sm text-center" readonly="readonly"
												value="{{ nf.outros.data_emissao }}">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Código Serviço Municipal</label>
											<input type="text" class="form-control input-sm" readonly="readonly"
												value="{{ nf.regra_tributacao.cod_servico_municipio }}">
										</div>
									</div>

									<div class="col-sm-6">
										<div class="form-group">
											<label class="control-label">Descrição Serviço Municipal</label>
											<input type="text" class="form-control input-sm" readonly="readonly"
												value="{{ nf.regra_tributacao.dsc_servico_municipio }}">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">CNAE</label>
											<input type="text" class="form-control input-sm" ng-model="nf.outros.codigo_cnae" ng-disabled="nf.outros.cod_nota_fiscal">
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Código Trib. Municipio</label>
											<input type="text" class="form-control input-sm" ng-model="nf.outros.codigo_tributario_municipio" ng-disabled="nf.outros.cod_nota_fiscal">
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Código Obra</label>
											<input type="text" class="form-control input-sm" ng-model="nf.outros.codigo_obra" ng-disabled="nf.outros.cod_nota_fiscal">
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Código ART</label>
											<input type="text" class="form-control input-sm" ng-model="nf.outros.art" ng-disabled="nf.outros.cod_nota_fiscal">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label class="control-label">Discriminação</label>
											<textarea class="form-control" rows="5" 
												ng-model="nf.outros.discriminacao_servico"
												ng-disabled="nf.outros.cod_nota_fiscal"></textarea>
										</div>
									</div>
								</div>
							</div>

							<div class="tab-pane fade in" id="emitente">
								<div class="row">
									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">CNPJ</label>
											<input type="text" class="form-control input-sm" readonly="readonly" 
												value="{{ nf.emitente.num_cnpj }}" 
												ng-model="nf.emitente.num_cnpj"
												ui-mask="99.999.999/9999-99">
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Inscrição Municipal</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="{{ nf.emitente.num_inscricao_municipal }}">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-5">
										<div class="form-group">
											<label class="control-label">Razão Social</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="{{ nf.emitente.nme_razao_social }}">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label class="control-label">Nome Fantasia</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="{{ nf.emitente.nme_fantasia }}">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">CEP</label>
											<input type="text"  class="form-control input-sm" readonly="readonly" value="{{ nf.emitente.num_cep }}">
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label class="control-label">Endereço</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="{{ nf.emitente.nme_logradouro }}">
										</div>
									</div>
									<div class="col-sm-1">
										<div class="form-group">
											<label class="control-label">Número</label>
											<input type="text"  class="form-control input-sm" readonly="readonly" value="{{ nf.emitente.num_logradouro }}">
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label class="control-label">Bairro</label>
											<input type="text"  class="form-control input-sm" readonly="readonly" value="{{ nf.emitente.nme_bairro_logradouro }}">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Estado</label>
											<input type="text"  class="form-control input-sm" readonly="readonly" value="{{ nf.emitente.nme_estado }}">
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label class="control-label">Cidade</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="{{ nf.emitente.nme_municipio }}">
										</div>
									</div>
								</div>
							</div>

							<div class="tab-pane fade in" id="destinatario">
								<div class="row">
									<div class="col-sm-2">
										<div class="form-group" ng-show="nf.tomador.tipo_cadastro == 'pj'">
											<label class="control-label">CNPJ</label>
											<input type="text" class="form-control input-sm" readonly="readonly" 
												value="{{ nf.tomador.cnpj }}">
										</div>
										<div class="form-group" ng-show="nf.tomador.tipo_cadastro == 'pf'">
											<label class="control-label">CPF</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="{{ nf.tomador.cpf }}">
										</div>
									</div>

									<div class="col-sm-5">
										<div class="form-group" ng-show="nf.tomador.tipo_cadastro == 'pj'">
											<label class="control-label">Razão Social</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="{{ nf.tomador.razao_social }}">
										</div>

										<div class="form-group" ng-show="nf.tomador.tipo_cadastro == 'pf'">
											<label class="control-label">Nome</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="{{ nf.tomador.nome }}">
										</div>
									</div>
									<div class="col-sm-4" ng-show="nf.tomador.tipo_cadastro == 'pj'">
										<div class="form-group">
											<label class="control-label">Nome Fantasia</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="{{ nf.tomador.nome_fantasia }}">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">CEP</label>
											<input type="text"  class="form-control input-sm" readonly="readonly" value="{{ nf.tomador.cep }}">
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label class="control-label">Endereço</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="{{ nf.tomador.endereco }}">
										</div>
									</div>
									<div class="col-sm-1">
										<div class="form-group">
											<label class="control-label">Número</label>
											<input type="text"  class="form-control input-sm" readonly="readonly" value="{{ nf.tomador.numero }}">
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label class="control-label">Bairro</label>
											<input type="text"  class="form-control input-sm" readonly="readonly" value="{{ nf.tomador.bairro }}">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Telefone</label>
											<input type="text"  class="form-control input-sm" readonly="readonly" value="{{ nf.tomador.tel_fixo }}">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label class="control-label">E-mail</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="{{ nf.tomador.email }}">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Estado</label>
											<input type="text"  class="form-control input-sm" readonly="readonly" value="{{ nf.tomador.nme_estado }}">
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label class="control-label">Cidade</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="{{ nf.tomador.nme_municipio }}">
										</div>
									</div>
								</div>
							</div>

							<div class="tab-pane fade in" id="servicos">
								<table class="table table-bordered table-hover table-condensed table-striped">
									<thead>
										<th width="100" class="text-center">Código</th>
										<th>Descrição</th>
										<th width="120" class="text-center">Valor</th>
									</thead>
									<tbody>
										<tr ng-repeat="item in nf.ordem_servico.servicos">
											<td class="text-center text-middle">
												{{ item.cod_procedimento }}
											</td>
											<td class="text-middle">
												{{ item.dsc_procedimento }}
											</td>
											<td class="text-right text-middle">
												R$ {{ item.vlr_procedimento | numberFormat : 2 : ',' : '.' }}
											</td>
										</tr>
									</tbody>
								</table>
							</div>

							<div class="tab-pane fade in" id="resumo">
								<div class="row">
									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Alíquota do ISS</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="{{ nf.regra_tributacao.prc_retencao_iss | numberFormat : 2 : ',' : '.' }}%">
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group" ng-show="nf.regra_tributacao.flg_retem_iss_pf == 1">
											<label class="control-label">Valor do ISS PF</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="R$ {{ nf.total.vlr_iss_pf | numberFormat : 2 : ',' : '.' }}">
										</div>

										<div class="form-group" ng-show="nf.regra_tributacao.flg_retem_iss_pj == 1">
											<label class="control-label">Valor do ISS PJ</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="R$ {{ nf.total.vlr_iss_pj | numberFormat : 2 : ',' : '.' }}">
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">ISS Retido?</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="{{ (nf.total.flg_iss_retido) ? 'Sim' : 'Não' }}">
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Valor do ISS Retido</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="R$ {{ nf.total.vlr_iss_retido | numberFormat : 2 : ',' : '.' }}">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Valor do PIS</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="R$ {{ nf.total.vlr_pis | numberFormat : 2 : ',' : '.' }}">
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Valor do COFINS</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="R$ {{ nf.total.vlr_cofins | numberFormat : 2 : ',' : '.' }}">
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Valor do INSS</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="R$ {{ nf.total.vlr_inss | numberFormat : 2 : ',' : '.' }}">
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Valor do I.R.</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="R$ {{ nf.total.vlr_ir | numberFormat : 2 : ',' : '.' }}">
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Valor do CSLL</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="R$ {{ nf.total.vlr_csll | numberFormat : 2 : ',' : '.' }}">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Valor Total Serviços</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="R$ {{ nf.ordem_servico.vlr_servicos | numberFormat : 2 : ',' : '.' }}">
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Base de Cálculo</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="R$ {{ nf.ordem_servico.vlr_servicos | numberFormat : 2 : ',' : '.' }}">
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">% Tributos</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="{{ nf.regra_tributacao.prc_tributos | numberFormat : 2 : ',' : '.' }}%">
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Valor Aprox. Tributos</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="R$ {{ nf.total.vlr_tributos | numberFormat : 2 : ',' : '.' }}">
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Valor de Outras Retenções</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="R$ {{ nf.total.vlr_outras_retencoes | numberFormat : 2 : ',' : '.' }}">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">% de Deduções</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="{{ nf.emitente.prc_deducoes | numberFormat : 2 : ',' : '.' }}%">
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Valor de Deduções</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="R$ {{ nf.total.vlr_deducoes | numberFormat : 2 : ',' : '.' }}">
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Valor Desc. Incondicionado</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="R$ {{ nf.total.vlr_desconto_incondicionado | numberFormat : 2 : ',' : '.' }}">
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">Valor Desc. Condicionado</label>
											<input type="text" class="form-control input-sm" readonly="readonly" value="R$ {{ nf.total.vlr_desconto_condicionado | numberFormat : 2 : ',' : '.' }}">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="panel-footer clearfix">
						<div class="pull-left">
							<p id="msg-success" class="alert alert-success hide">
								Nota Fiscal de Serviço transmitida com sucesso!
								<br/>
								Utilize a tela de Notas Fiscais para consultar a situação de emissão.
							</p>
						</div>
						<div class="pull-right">
							<button type="button" id="btnCalcular" class="btn btn-sm btn-default"
								data-loading-text="Aguarde..."
								ng-click="calcularImpostos()" 
								ng-show="!nf.outros.cod_nota_fiscal">
								<i class="fa fa-refresh"></i> Calcular Impostos
							</button>
							<button type="button" id="btnTransmitir" class="btn btn-sm btn-success" 
								ng-show="!nf.outros.cod_nota_fiscal"
								ng-click="transmitirNFSe()">
								<i class="fa fa-send"></i> Transmitir NFS-e
							</button>
							<button type="button" class="btn btn-sm btn-danger" 
								ng-show="nf.outros.cod_nota_fiscal">
								<i class="fa fa-times-circle"></i> Cancelar NFS-e
							</button>
							<a href="{{ nf.outros.caminho_danfe }}" target="_blank"
								class="btn btn-sm btn-primary"
								ng-show="nf.outros.cod_nota_fiscal">
								<i class="fa fa-file-pdf-o"></i> Visualizar DANFE (PDF)
							</a>
							<a href="{{ nf.outros.caminho_xml_nota_fiscal }}" target="_blank"
								class="btn btn-sm btn-primary"
								ng-show="nf.outros.cod_nota_fiscal">
								<i class="fa fa-file-pdf-o"></i> Visualizar DANFE (XML)
							</a>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /main-container -->

		<!-- /Modal Clientes-->
		<div class="modal fade" id="list_regrasTributacao" style="display:none">
  			<div class="modal-dialog modal-lg" >
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Regras de Tributação</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.text" ng-enter="loadRegrasTributacao(0,10)" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadRegrasTributacao(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
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
									<thead>
										<tr>
											<th>Nome</th>
											<th>Cód. Serviço Municipal</th>
											<th>Desc. Serviço Municipal</th>
											<th>UF</th>
											<th>Município</th>
											<th>% Tributos</th>
											<th>Ações</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="item in regrasTributacao">
											<td>{{ item.nme_regra_servico }}</td>
											<td>{{ item.cod_servico_municipio }}</td>
											<td>{{ item.dsc_servico_municipio }}</td>
											<td>{{ item.uf }}</td>
											<td>{{ item.municipio }}</td>
											<td class="text-center">{{ item.prc_tributos }}%</td>
											<td width="50" align="center">
												<button type="button" class="btn btn-xs btn-success" ng-click="selectRegraTributacao(item)">
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
				    			<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao.regras_tributacao.length > 1">
									<li ng-repeat="item in paginacao.regras_tributacao" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadRegrasTributacao(item.offset, item.limit)">{{ item.index }}</a>
									</li>
								</ul>
				    		</div>
				    	</div>
				    </div>
			  	</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal Processando-->
		<div class="modal fade" id="modal-calculando" style="display:none">
  			<div class="modal-dialog error modal-sm">
    			<div class="modal-content">
				    <div class="modal-body">
				    	<div class="row">
				    		<div class="col-sm-12">
				    			<i class='fa fa-refresh fa-spin'></i> Aguarde, calculando os impostos...
							</div>
				    	</div>
				    </div>
			  	</div>
			  	<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- /Modal Processando-->
		<div class="modal fade" id="modal-transmissao" style="display:none">
  			<div class="modal-dialog error modal-sm">
    			<div class="modal-content">
				    <div class="modal-body">
				    	<div class="row">
				    		<div class="col-sm-12">
				    			<i class='fa fa-refresh fa-spin'></i> Aguarde, processando transmissão...
							</div>
				    	</div>
				    </div>
			  	</div>
			  	<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
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
    	var addParamModule = ['angular.chosen'] ;
    </script>
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/nota_fiscal_servico-controller.js"></script>
	<script type="text/javascript"></script>>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

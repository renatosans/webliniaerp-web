<?php
	include_once "util/login/restrito.php";
	restrito(array(1,8));
?>
<!DOCTYPE html>
<html lang="en" ng-app="HageERP" >
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

  <body class="overflow-hidden ng-cloak" ng-controller="relMovimentacaoCaixaController">

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
			<div class="padding-md">
				<div class="clearfix">
					<div class="pull-left">
						<span class="img-demo">
							<img src="assets/imagens/logos/{{ userLogged.nme_logo }}">
						</span>

						<div class="pull-left m-left-sm">
							<h3 class="m-bottom-xs m-top-xs">Relatório de Movimentação de Caixa</h3>
							<span class="text-muted">Detalhes das movimentações</span>
						</div>
					</div>

					<div class="pull-right text-right">
						<h5><strong>#<?php echo rand(); ?></strong></h5>
						<strong><?php echo date("d/m/Y H:i:s"); ?></strong>
					</div>
				</div>

				<hr>

				<div class="row">
					<div class="col-sm-12">
						<div class="pull-right">
							<button class="btn btn-sm btn-success hidden-print"  id="invoicePrint"><i class="fa fa-print"></i> Imprimir</button>
						</div>
					</div>
				</div>

				<p><b>Caixa   :</b> {{ movimentacao.dsc_conta_bancaria }} </p>
				<p><b>Operador   :</b> {{ movimentacao.operador }} </p>
				<p><b>Data de abertura   :</b> {{ movimentacao.dta_abertura | dateFormat:'dateTime' }} </p>
				<p><b>Data de fechamento :</b> {{ movimentacao.dta_fechamento | dateFormat:'dateTime' }} </p>
				<div class="alert alert-delete" style="display:none"></div>

				<table class="table table-bordered table-condensed table-striped table-hover">
					<thead ng-if="movimentacoes.length > 0">
						<tr colspan="5" ng-if="movimentacoes.length > 0">
							<td style="background: #D5D5D5;" colspan="9" class="text-uppercase text-center" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">
								<span style="font-size: 14px;">Total por Forma de Pagamento</span>
							</td>
							<td style="background: #D5D5D5;" colspan="6" class="text-uppercase text-center" ng-if="!funcioalidadeAuthorized('ver_taxa_maquineta')">
								<span style="font-size: 14px;">Total por Forma de Pagamento</span>
							</td>
						</tr>

						<tr ng-if="movimentacoes.length > 0">
							<td colspan="5" class="text-right text-success text-bold">{{ totais.formas_pagamento.dinheiro.dsc }} (Pagamentos de Venda)</td>
							<td class="text-right text-success text-bold">R$ {{ totais.formas_pagamento.dinheiro.valor - total_reforco_caixa | numberFormat : 2 : ',' : '.'}}</td>
							<td ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')"></td>
							<td class="text-right text-success text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ 0 | numberFormat : 2 : ',' : '.'}}</td>
							<td class="text-right text-success text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ totais.formas_pagamento.dinheiro.valor - total_reforco_caixa | numberFormat : 2 : ',' : '.'}}</td>
						</tr>
						
						<tr ng-if="movimentacoes.length > 0">
							<td colspan="5" class="text-right text-info text-bold">Reforços</td>
							<td class="text-right text-info text-bold">R$ {{ total_reforco_caixa  | numberFormat : 2 : ',' : '.'}}</td>
							<td ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')"></td>
							<td class="text-right text-info text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ 0 | numberFormat : 2 : ',' : '.'}}</td>
							<td class="text-right text-info text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ total_reforco_caixa  | numberFormat : 2 : ',' : '.'}}</td>
						</tr>
						
						<tr ng-if="movimentacoes.length > 0">
							<td colspan="5" class="text-right text-danger text-bold">Sangrias</td>
							<td class="text-right text-danger text-bold">R$ {{ totais.formas_pagamento.sangria.valor | numberFormat : 2 : ',' : '.'}}</td>
							<td ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')"></td>
							<td class="text-right text-danger text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ 0 | numberFormat : 2 : ',' : '.'}}</td>
							<td class="text-right text-danger text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ totais.formas_pagamento.sangria.valor | numberFormat : 2 : ',' : '.'}}</td>
						</tr>
						
						<tr ng-if="movimentacoes.length > 0">
							<td colspan="5" class="text-right text-bold">Saldo Dinheiro</td>
							<td class="text-right text-bold">
								<span ng-class="{ 'text-danger': (getSaldoDinheiro() < 0), 'text-success': (getSaldoDinheiro() > 0) }">R$ {{ getSaldoDinheiro() | numberFormat : 2 : ',' : '.'}}</span>
							</td>
							<td ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')"></td>
							<td class="text-right text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ 0 | numberFormat : 2 : ',' : '.'}}</td>
							<td class="text-right text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">
								<span ng-class="{ 'text-danger': (getSaldoDinheiro() < 0), 'text-success': (getSaldoDinheiro() > 0) }">R$ {{ getSaldoDinheiro() | numberFormat : 2 : ',' : '.'}}</span>
							</td>
						</tr>
						
						<tr ng-if="movimentacoes.length > 0">
							<td colspan="5" class="text-right text-bold">{{ totais.formas_pagamento.cartao_credito.dsc }}</td>
							<td class="text-right text-bold">R$ {{ totais.formas_pagamento.cartao_credito.valor | numberFormat : 2 : ',' : '.'}}</td>
							<td ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')"></td>
							<td ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')" class="text-right text-bold">R$ {{ total_desconto_taxa_maquineta_credito | numberFormat : 2 : ',' : '.'}}</td>
							<td class="text-right text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ totais.formas_pagamento.cartao_credito.valor - total_desconto_taxa_maquineta_credito | numberFormat : 2 : ',' : '.'}}</td>
						</tr>
						
						<tr ng-if="movimentacoes.length > 0">
							<td colspan="5" class="text-right text-bold">{{ totais.formas_pagamento.cartao_debito.dsc }}</td>
							<td class="text-right text-bold">R$ {{ totais.formas_pagamento.cartao_debito.valor | numberFormat : 2 : ',' : '.'}}</td>
							<td ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')"></td>
							<td ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')" class="text-right text-bold">R$ {{ total_desconto_taxa_maquineta_debito | numberFormat : 2 : ',' : '.'}}</td>
							<td class="text-right text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ totais.formas_pagamento.cartao_debito.valor - total_desconto_taxa_maquineta_debito | numberFormat : 2 : ',' : '.'}}</td>
						</tr>
						
						<tr ng-if="movimentacoes.length > 0">
							<td colspan="5" class="text-right text-bold">{{ totais.formas_pagamento.cheque.dsc }}</td>
							<td class="text-right text-bold">R$ {{ totais.formas_pagamento.cheque.valor | numberFormat : 2 : ',' : '.'}}</td>
							<td ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')"></td>
							<td class="text-right text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ 0 | numberFormat : 2 : ',' : '.'}}</td>
							<td class="text-right text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ totais.formas_pagamento.cheque.valor | numberFormat : 2 : ',' : '.'}}</td>
						</tr>
						
						<tr ng-if="movimentacoes.length > 0">
							<td colspan="5" class="text-right text-bold">{{ totais.formas_pagamento.a_receber.dsc }}</td>
							<td class="text-right text-bold">R$ {{ totais.formas_pagamento.a_receber.valor | numberFormat : 2 : ',' : '.'}}</td>
							<td ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')"></td>
							<td class="text-right text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ 0 | numberFormat : 2 : ',' : '.'}}</td>
							<td class="text-right text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ totais.formas_pagamento.a_receber.valor | numberFormat : 2 : ',' : '.'}}</td>
						</tr>
						
						<tr ng-if="movimentacoes.length > 0">
							<td colspan="5" class="text-right text-bold">{{ totais.formas_pagamento.vale_troca.dsc }}</td>
							<td class="text-right text-bold">R$ {{ totais.formas_pagamento.vale_troca.valor | numberFormat : 2 : ',' : '.'}}</td>
							<td ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')"></td>
							<td class="text-right text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ 0 | numberFormat : 2 : ',' : '.'}}</td>
							<td class="text-right text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ totais.formas_pagamento.vale_troca.valor | numberFormat : 2 : ',' : '.'}}</td>
						</tr>
						
						<tr ng-if="movimentacoes.length > 0">
							<td colspan="5" class="text-right text-bold">{{ totais.formas_pagamento.boleto_bancario.dsc }}</td>
							<td class="text-right text-bold">R$ {{ totais.formas_pagamento.boleto_bacario.valor | numberFormat : 2 : ',' : '.'}}</td>
							<td ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')"></td>
							<td class="text-right text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ 0 | numberFormat : 2 : ',' : '.'}}</td>
							<td class="text-right text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ totais.formas_pagamento.boleto_bacario.valor | numberFormat : 2 : ',' : '.'}}</td>
						</tr>
						
						<tr ng-if="movimentacoes.length > 0">
							<td colspan="5" class="text-right text-bold">{{ totais.formas_pagamento.transferencia_bancaria.dsc }}</td>
							<td class="text-right text-bold">R$ {{ totais.formas_pagamento.transferencia_bancaria.valor | numberFormat : 2 : ',' : '.'}}</td>
							<td ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')"></td>
							<td class="text-right text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ 0 | numberFormat : 2 : ',' : '.'}}</td>
							<td class="text-right text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ totais.formas_pagamento.transferencia_bancaria.valor | numberFormat : 2 : ',' : '.'}}</td>
						</tr>
						
						<tr ng-if="movimentacoes.length > 0">
							<td colspan="5" class="text-right text-bold">{{ totais.formas_pagamento.promessa_pagamento.dsc }}</td>
							<td class="text-right text-bold">R$ {{ totais.formas_pagamento.promessa_pagamento.valor | numberFormat : 2 : ',' : '.'}}</td>
							<td ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')"></td>
							<td class="text-right text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ 0 | numberFormat : 2 : ',' : '.'}}</td>
							<td class="text-right text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ totais.formas_pagamento.promessa_pagamento.valor | numberFormat : 2 : ',' : '.'}}</td>
						</tr>
						
						<tr ng-if="movimentacoes.length > 0">
							<td colspan="5" class="text-right text-bold">{{ totais.formas_pagamento.voucher.dsc }}</td>
							<td class="text-right text-bold">R$ {{ totais.formas_pagamento.voucher.valor | numberFormat : 2 : ',' : '.'}}</td>
							<td ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')"></td>
							<td class="text-right text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ 0 | numberFormat : 2 : ',' : '.'}}</td>
							<td class="text-right text-bold" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">R$ {{ totais.formas_pagamento.voucher.valor | numberFormat : 2 : ',' : '.'}}</td>
						</tr>

						<tr>
							<td style="background: #D5D5D5;" colspan="9" class="text-uppercase text-center" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">
								<span style="font-size: 14px;">Detalhes</span>
							</td>
							<td style="background: #D5D5D5;" colspan="6" class="text-uppercase text-center" ng-if="!funcioalidadeAuthorized('ver_taxa_maquineta')">
								<span style="font-size: 14px;">Detalhes</span>
							</td>
						</tr>

						<tr>
							<th rowspan="2" style="line-height: 46px;">Data</th>
							<th rowspan="2" class="text-center" style="line-height: 46px;">Cliente</th>
							<th rowspan="2" class="text-center" style="line-height: 46px;">Usuario</th>
							<th rowspan="2" class="text-center" style="line-height: 46px;">Tipo</th>
							<th rowspan="2" class="text-center" style="line-height: 46px;width: 300px;">Descrição</th>
							<th rowspan="2" class="text-center" style="line-height: 46px;">Valor</th>
							<th rowspan="1" class="text-center" colspan="3" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">Taxa Maquineta</th>
						</tr>
						<tr ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">
							<th class="text-center" rowspan="1">% Perc.</th>
							<th class="text-center" rowspan="1">R$ Desc.</th>
							<th class="text-center" rowspan="1">Valor c/ Desc.</th>
						</tr>
					</thead>
					<tbody>
						<tr ng-if="movimentacoes.length == 0 && movimentacoes != null">
							<td class="text-center" colspan="9">
								<i class="fa fa-refresh fa-spin"></i> Aguarde, carregando movimentações...
							</td>
						</tr>
						
						<tr ng-if="movimentacoes == null">
							<td colspan="9" class="text-center">
								Nenhuma movimentação encontrada para este caixa.
							</td>
						</tr>

						<tr ng-repeat="item in movimentacoes">
							<td>{{ item.dta_movimentacao | dateFormat:'dateTime' }}</td>
							<td>{{ item.nme_cliente | uppercase }}</td>
							<td>{{ item.nme_usuario | uppercase }}</td>
							<td class="text-center">{{ item.tipo_movimentacao }}</td>

							<td ng-if="item.id_forma_pagamento_entrada == 6 && item.id_venda != null">
								{{ item.dsc_movimentacao}} #{{ item.id_venda }} <b>( Pago em : C.C - {{ item.parcelas }} X R$ {{ item.vlr_parcela | numberFormat:2:',':'.' }} )</b>
							</td>

							<td ng-if="item.id_forma_pagamento_entrada == 6 && item.id_venda == null">
								{{ item.dsc_movimentacao}}  <b>( Pago em : C.C - {{ item.parcelas }} X R$ {{ item.vlr_parcela | numberFormat:2:',':'.' }} )</b>
							</td>

							<td ng-if="item.tipo_movimentacao == 'Sangria' || item.tipo_movimentacao == 'Reforco' ">
								<a ng-if="item.tipo_movimentacao == 'Sangria'" style="cursor:pointer" init-popover content="
								<div><b>Fornecedor:</b> {{ item.nome_fornecedor }} <br/> <b>Observação:</b> {{ item.obs_pagamento_saida }}<div/>
								" >{{ item.obs_pagamento_saida }} <small>({{ item.conta_saida }} >> {{ item.conta_entrada }})</small></a>

								<a ng-if="item.tipo_movimentacao == 'Reforco'" style="cursor:pointer" init-popover content="
								<div><b>Observação:</b> {{ item.obs_pagamento_entrada }}<div/>
								" >{{ item.obs_pagamento_entrada }} <small>({{ item.conta_saida }} >> {{ item.conta_entrada }})</small></a>
							</td>

							<td ng-if="item.id_tipo_movimentacao == 5">
								{{ item.dsc_movimentacao}} #{{ item.id_venda }}</b>
							</td>

							<td ng-if="item.id_forma_pagamento_entrada != 6 && item.tipo_movimentacao != 'Sangria' && item.tipo_movimentacao != 'Reforco' && item.id_tipo_movimentacao != 5 && item.id_venda != null ">
								{{ item.dsc_movimentacao}} #{{ item.id_venda }} <b>( Pago em : {{ item.forma_pagamento_entrada }} )</b>
							</td>

							<td ng-if="item.id_forma_pagamento_entrada != 6 && item.tipo_movimentacao != 'Sangria' && item.tipo_movimentacao != 'Reforco' && item.id_tipo_movimentacao != 5 && item.id_venda == null ">
								{{ item.dsc_movimentacao}} {{ item.id_venda }} <b>( Pago em : {{ item.forma_pagamento_entrada }} )</b>
							</td>

							<td ng-if="isEntrada(item) && item.id_tipo_movimentacao != 5" style="color: #118A2E;" class="text-right">
								<strong>R$ {{ item.valor_entrada | numberFormat:2:',':'.' }}</strong>
							</td>
							<td ng-if="isSaida(item)&& item.id_tipo_movimentacao != 5" style="color:red;" class="text-right">
								<strong>- R$ {{ item.valor_entrada | numberFormat:2:',':'.' }}</strong>
							</td>
							<td ng-if="item.id_tipo_movimentacao == 5" style="color:rgb(208, 216, 22);" class="text-right">
								<strong>R$ {{ item.para_receber | numberFormat:2:',':'.' }}</strong>
							</td>
							<td class="text-right" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')" >
								 {{ item.taxa_maquineta * 100 | numberFormat:2:',':'.' }}%
							</td>
							<td class="text-right" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">
								 R$ {{ item.vlr_taxa_maquineta | numberFormat:2:',':'.' }}
							</td>
							<td class="text-right" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">
								 R$ {{ item.valor_desconto_maquineta | numberFormat:2:',':'.' }}
							</td>
						</tr>

						<tr ng-if="movimentacoes.length > 0">
							<td colspan="5" class="text-right">Saldo Final</td>
							<td style="color:#000;" class="text-right">
								<strong>R$ {{ totais.total - totais.formas_pagamento.sangria.valor| numberFormat:2:',':'.'}}</strong>
							</td>
							<td ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">

							</td>
							<td  style="color:#000;" class="text-right" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">
								<strong>R$ {{ total_desconto_taxa_maquineta | numberFormat:2:',':'.'}}</strong>
							</td>
							<td colspan="2" style="color:#000;" class="text-right" ng-if="funcioalidadeAuthorized('ver_taxa_maquineta')">
								<strong>R$ {{ totais.total - total_desconto_taxa_maquineta | numberFormat:2:',':'.'}}</strong>
							</td>
						</tr>
						
						<tr ng-if="movimentacoes.length > 0">
							<td colspan="5" class="text-right">Total de Vendas</td>
							<td class="text-right" style="color:#000;"><strong>R$ {{ (total_vendas | numberFormat : 2 : ',' : '.') }}</strong> </td>
							<td ></td>
							<td ng-if="key != 'cartao_debito' && key != 'cartao_credito' && funcioalidadeAuthorized('ver_taxa_maquineta')" class="text-right" style="color:#000;">
								<strong>R$ {{ ((total_desconto_taxa_maquineta_debito+total_desconto_taxa_maquineta_credito) | numberFormat : 2 : ',' : '.') }}</strong>
							</td>
							<td ng-if="key != 'cartao_debito' && key != 'cartao_credito' && funcioalidadeAuthorized('ver_taxa_maquineta')" class="text-right" style="color:#000;">
								<strong>R$ {{ ((total_vendas-(total_desconto_taxa_maquineta_debito+total_desconto_taxa_maquineta_credito)) | numberFormat:2:',':'.') }}</strong>
							</td>
						</tr>
						
						
					</tbody>
				</table>

			</div>
		</div>
		<!-- /main-container -->

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
	<script src="js/angular-controller/rel_movimentacao_caixa-controller.js"></script>
	<script type="text/javascript"></script>

		<script id="printFunctions">
		function printDiv(id, pg) {
			var contentToPrint, printWindow;

			contentToPrint = window.document.getElementById(id).innerHTML;
			printWindow = window.open(pg);

		    printWindow.document.write("<link href='bootstrap/css/bootstrap.min.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/font-awesome.min.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/pace.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/endless.min.css' rel='stylesheet'>");
			printWindow.document.write("<link href='css/endless-skin.css' rel='stylesheet'>");

			printWindow.document.write("<style type='text/css' media='print'>@page { size: portrait; } th, td { font-size: 8pt; }</style><style type='text/css'>#invoicePrint{ display:none }</style>");

			printWindow.document.write(contentToPrint);

			printWindow.window.print();
			printWindow.document.close();
			printWindow.focus();
		}

		$(function()	{
			$('#invoicePrint').click(function()	{
				printDiv("main-container", "");
			});
		});

	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

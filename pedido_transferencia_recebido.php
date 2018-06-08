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
      <link rel='stylesheet prefetch' href='bootstrap/css/bootstrap.min.css?version=<?php echo date("dmY-His", filemtime("bootstrap/css/bootstrap.min.css")) ?>'>

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

		/*@media screen and (min-width: 768px) {

			#list_proodutos.modal-dialog  {width:900px;}

		}*/

		.tr-pintada td{
			background: #f5f5f5 !important ;
		}

		/*#list_produtos .modal-dialog  {width:70%;}

		#list_produtos .modal-content {min-height: 640px;;}*/

		.tr-out-estoque td {
			background: #FA8072 !important ;
			color: black;
		}

		.panel.panel-default {
		    overflow: visible !important;
		}


	</style>
  </head>

  <body class="overflow-hidden" ng-controller="PedidoTransferenciaRecebidoController" ng-cloak>
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
					 <li class="active"><i class="fa fa-sitemap"></i><a href="depositos.php"> Depósitos</a></li>
					 <li class="active"><i class="fa fa-list-ol"></i><a href="estoque.php"> Controle de Estoque</a></li>
					 <li class="active"><i class="fa fa-arrows-h"></i><a href="pedido_transferencia.php"> Transferências</a></li>
					 <li class="active"><i class="fa fa-arrows-h "></i> Pedidos de Transferências</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-arrows-h"></i> Pedidos de Transferências</h3>
					<br/>
					<a ng-if="!isNumeric(transferencia.id)" class="btn btn-info" id="btn-novo"  ng-click="openNovaTransferencia()"><i class="fa fa-plus-circle"></i> Enviar Mercadorias</a>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md">
				<div class="alert alert-sistema" style="display:none"></div>

				<div class="panel panel-default" id="box-novo" style="display:none">
					<div ng-if="isNumeric(transferencia.id)" class="panel-heading"><i class="fa fa-edit"></i> Atendendo Pedido de Transferência #{{ transferencia.id }}</div>
					<div ng-if="!enviarNovaTransferencia">
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-12"><div style="display: none" class="alert alert-transferencia-form"></div></div>
							</div>

							<div class="row" ng-if="configuracao.flg_controlar_validade_transferencia == 0">
								<div class="col-sm-12">
									<div class="form-group" id="produtos">
											<table class="table table-bordered table-condensed table-striped table-hover" id="produtos">
												<thead>
													<tr>
														<td colspan="11"><i class="fa fa-archive"></i> Produtos</td>
														<td width="80" align="center">
															<button class="btn btn-xs btn-primary" ng-disabled="!isNumeric(transferencia.id_empreendimento_transferencia)" ng-click="showProdutos()"><i class="fa fa-plus-circle"></i></button>
														</td>
													</tr>
												</thead>
												<tbody>
													<tr>
														<th>ID</th>
														<th>Produto</th>
														<th>Fabricante</th>
														<th>Peso</th>
														<th>Sabor</th>
														<th class="text-center" >Estoque</th>
														<th class="text-center" width="90" 
															ng-if="funcioalidadeAuthorized('ver_valor_custo_produto')">
															Vlr. Custo
															<div class="btn-group">
																<button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																		<span class="caret"></span>
																</button>
																<ul class="dropdown-menu">
																	<li>
																		<a href="" class="text-left" ng-click="setarVlrCusto(null,'vlr_custo_real')">
																			Custo
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_custo_real'" ></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(null,'vlr_venda_atacado')">
																			Atacado
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_atacado'"></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(null,'vlr_venda_intermediario')">
																			Intermediário
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_intermediario'"></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(null,'vlr_venda_varejo')">
																			Varejo
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_varejo'"></i>
																		</a>
																	</li>
																</ul>	
															</div>	
														</th>
														<th class="text-center" >Qtd.Pedida</th>
														<th class="text-center">Qtd. Multipla</th>
														<th>Qtd. Transferir</th>
														<th width="250">
															Selecione o depósito de saída
															<button style="float:right" class="btn btn-xs btn-info" ng-disabled="!isNumeric(transferencia.id_empreendimento_transferencia)" ng-click="selDeposito()" tooltip data-placement="top" title="Selecionar deposito para todos os itens"><i class="fa fa-sitemap"></i></button>
														</th>
														<th></th>
													</tr>
													<tr ng-repeat="(index, item) in transferencia.produtos" id="tr-prd-{{ item.id }}">
														<td>{{ item.id	 }}</td>
														<td>{{ item.nome }}</td>
														<td>{{ item.nome_fabricante }}</td>
														<td>{{ item.peso }}</td>
														<td>{{ item.sabor }}</td>
														<td class="text-center" ng-if="!item.load_estoque">{{ item.qtd_item }}</td>
														<td class="text-center" ng-if="item.load_estoque"><i class='fa fa-refresh fa-spin'></i></td>
														<td class="text-center" ng-if="funcioalidadeAuthorized('ver_valor_custo_produto')">
															{{ item.vlr_custo | numberFormat:2:',':'.' }}
															<div class="btn-group">
																<button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																		<span class="caret"></span>
																</button>
																<ul class="dropdown-menu">
																	<li>
																		<a href="" class="text-left" ng-click="setarVlrCusto(item,'vlr_custo_real')">
																			Custo: R$ {{ item.vlr_custo_real | numberFormat:2:',':'.' }}
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_custo_real'" ng-if="funcioalidadeAuthorized('ver_valor_custo_produto')"></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(item,'vlr_venda_atacado')">
																			Atacado: R$ {{ item.vlr_venda_atacado | numberFormat:2:',':'.' }}
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_atacado'"></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(item,'vlr_venda_intermediario')">
																			Intermediário: R$ {{ item.vlr_venda_intermediario | numberFormat:2:',':'.' }}
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_intermediario'"></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(item,'vlr_venda_varejo')">
																			Varejo: R$ {{ item.vlr_venda_varejo | numberFormat:2:',':'.' }}
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_varejo'"></i>
																		</a>
																	</li>
																</ul>	
															</div>													
														</td>
														<td width="80" class="text-center">{{ item.qtd_pedida }}</td>
														<td class="text-center">{{ item.qtd_multiplo_transferencia }}</td>
														<td  width="100" align="center" id="td-prd-{{ item.id }}" >
															<div class="form-group">
																<input type="text" 
																	class="form-control text-center input-xs input-group" 
																	onKeyPress="return SomenteNumero(event);" 
																	style="width: 75px" 
																	ng-model="item.qtd_transferida" 
																	ng-if="item.flg_unidade_fracao != 1" 
																	ng-blur="verificaQtdMultiplo('produtos',index,item)"
																	id="txt-qtd-multiplo-{{ index }}"/>
															</div>
															<div class="form-group">
																<input type="text" 
																	class="form-control text-center input-xs input-group" 
																	onKeyPress="return SomenteNumero(event);" 
																	style="width: 75px" 
																	ng-model="item.qtd_transferida" 
																	ng-if="item.flg_unidade_fracao == 1" 
																	ng-blur="verificaQtdMultiplo('produtos',index,item)"
																	id="txt-qtd-multiplo-{{ index }}" 
																	thousands-formatter precision="3"/>
															</div>
														</td>
														<td id="td-prd-deposito-saida-{{ item.id }}">
															<select chosen ng-change="loadestoque(item)" 
														    	option="depositos_chosen"
														    	ng-model="item.id_deposito_saida"
														    	ng-options="deposito.id as deposito.nme_deposito for deposito in depositos_chosen">
															</select>
														</td>
														<td align="center">
															<button ng-if="item.add == 1" class="btn btn-xs btn-danger" ng-click="excluirProdutoLista($index)"><i class="fa fa-trash-o"></i></button>
														</td>
													</tr>
												</tbody>
											</table>
									</div>
								</div>
							</div>
							
							<div class="row" ng-if="configuracao.flg_controlar_validade_transferencia == 1">
								<div class="col-sm-12">
									<div class="table-responsive">
										<div class="form-group" id="produtos">
											<table class="table table-bordered table-condensed table-striped table-hover">
												<thead>
													<tr>
														<td colspan="9"><i class="fa fa-archive"></i> Produtos</td>
														<td width="60" align="center">
														<button class="btn btn-xs btn-primary" ng-disabled="!isNumeric(transferencia.id_empreendimento_transferencia)" ng-click="showProdutosByValidade()"><i class="fa fa-plus-circle"></i></button>
														</td>
													</tr>
												</thead>
												<tbody>
													<tr>
														<th>ID</th>
														<th>Produto</th>
														<th>Fabricante</th>
														<th>Tamanho</th>
														<th>Sabor/Cor</th>
														<th class="text-center" >Estoque</th>
														<th class="text-center" width="90"
															ng-if="funcioalidadeAuthorized('ver_valor_custo_produto')">
															Vlr. Custo
															<div class="btn-group">
																<button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																		<span class="caret"></span>
																</button>
																<ul class="dropdown-menu">
																	<li>
																		<a href="" class="text-left" ng-click="setarVlrCusto(null,'vlr_custo_real')">
																			Custo
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_custo_real'" ></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(null,'vlr_venda_atacado')">
																			Atacado
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_atacado'"></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(null,'vlr_venda_intermediario')">
																			Intermediário
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_intermediario'"></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(null,'vlr_venda_varejo')">
																			Varejo
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_varejo'"></i>
																		</a>
																	</li>
																</ul>	
															</div>	
														</th>
														<th class="text-center" >Qtd.Pedida</th>
														<th class="text-center">Qtd. Multipla</th>
														<th>Qtd. transferir</th>
														<!--<th width="250">
															Depósito
															<button style="float:right" class="btn btn-xs btn-info" ng-disabled="!isNumeric(transferencia.id_empreendimento_transferencia)" ng-click="selDeposito()" tooltip data-placement="top" title="Selecionar deposito para todos os itens"><i class="fa fa-sitemap"></i></button>
														</th>-->
														<th></th>
													</tr>
													<tr ng-repeat="item in transferencia.produtos" id="tr-prd-{{ item.id }}">
														<td>{{ item.id	 }}</td>
														<td>{{ item.nome }}</td>
														<td>{{ item.nome_fabricante }}</td>
														<td>{{ item.peso }}</td>
														<td>{{ item.sabor }}</td>
														<td class="text-center" ng-if="!item.load_estoque">{{ item.qtd_item }}</td>
														<td class="text-center" ng-if="item.load_estoque"><i class='fa fa-refresh fa-spin'></i></td>
														<td class="text-center" ng-if="funcioalidadeAuthorized('ver_valor_custo_produto')">
															{{ item.vlr_custo | numberFormat:2:',':'.' }}
															<div class="btn-group">
																<button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																		<span class="caret"></span>
																</button>
																<ul class="dropdown-menu">
																	<li>
																		<a href="" class="text-left" ng-click="setarVlrCusto(item,'vlr_custo_real')">
																			Custo: R$ {{ item.vlr_custo_real | numberFormat:2:',':'.' }}
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_custo_real'" ></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(item,'vlr_venda_atacado')">
																			Atacado: R$ {{ item.vlr_venda_atacado | numberFormat:2:',':'.' }}
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_atacado'"></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(item,'vlr_venda_intermediario')">
																			Intermediário: R$ {{ item.vlr_venda_intermediario | numberFormat:2:',':'.' }}
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_intermediario'"></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(item,'vlr_venda_varejo')">
																			Varejo: R$ {{ item.vlr_venda_varejo | numberFormat:2:',':'.' }}
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_varejo'"></i>
																		</a>
																	</li>
																</ul>	
															</div>													
														</td>
														<td width="80" class="text-center">{{ item.qtd_pedida }}</td>
														<td class="text-center">{{ item.qtd_multiplo_transferencia }}</td>
														<td  width="100" align="center" id="td-prd-{{ item.id }}" >
															<div class="input-group" id="dtaInicialDiv">
															<input ng-disabled="true" onKeyPress="return SomenteNumero(event);" style="width: 75px" ng-value="somarQtd(item)"  type="text" class="form-control input-sm" />
															<span id="btnDtaInicial" class="input-group-addon"
															 href=""
															 popover2
															 model="item.validades"
															 title="Validades"
															 func="ctrl"
															 placement="left"
															 content='
																 <table class="table table-bordered table-condensed table-striped table-hover">
																 	<tr ng-repeat="item in model">
																 		<td ng-bind="item.nome_deposito"></td>
																 		<td class"text-center" ng-bind="item.dta_validade|date" ng-if="item.dta_validade != %272099-12-31%27"></td>
																 		<td class"text-center" ng-if="item.dta_validade == %272099-12-31%27"></td>
																 		<td class"text-center" ng-bind="item.qtd_item"></td>
																 		<td width="80" ng-class="{%27has-error%27: item.tooltip != undefined }" >
																 			<input controll-tooltip="item.tooltip" ng-blur="func.clearTooltip(item)" somente-numeros ng-keyUp="func.vericarQtdByValidade(item,%27body%27)"  ng-model="item.qtd_transferida"  type="text" class="form-control input-xs text-center">
						           										</td>
																 	</tr>
																 </table>
														 	'
															><i class="fa fa-calendar"></i></span>
															</div>
														</td>
														<!--<td id="td-prd-deposito-saida-{{ item.id }}">
															<select chosen ng-change="loadestoque(item)" 
														    option="depositos_chosen"
														    ng-model="item.id_deposito_saida"
														    ng-options="deposito.id as deposito.nme_deposito for deposito in depositos_chosen">
															</select>
														</td>-->
														<td align="center">
															<button ng-if="item.add == 1" class="btn btn-xs btn-danger" ng-click="excluirProdutoListaByValidade($index)"><i class="fa fa-trash-o"></i></button>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel-footer">
							<div class="pull-left">
								<label class="label-checkbox">
									<input type="checkbox" id="toggleLine"
										ng-model="transferencia.venda.flg_gerar_venda" 
										ng-true-value="true" ng-false-value="false">
									<span class="custom-checkbox"></span>
									Gerar venda a partir desse envio?
								</label>
							</div>

							<div class="pull-right">
								<button ng-click="cancelar()" class="btn btn-danger btn-sm">
									<i class="fa fa-times-circle"></i> Cancelar
								</button>
								<button id="salvar-transferencia" class="btn btn-success btn-sm" 
									ng-click="salvarTransferencia()"
									data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde...">
									<i class="fa fa-save"></i> Salvar
								</button>
							</div>
							<div style="clear: both;"></div>
						</div>
					</div>
					<div ng-if="enviarNovaTransferencia">
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-12"><div style="display: none" class="alert alert-transferencia-form"></div></div>
							</div>
							<div class="row">
								<div class="col-sm-6" id="id_empreendimento_transferencia">
									<label class="control-label">Selecione o empreendimento para o qual deseja enviar produtos:</label>
									<div class="input-group">
							            <input ng-model="transferencia.nome_empreendimento" ng-disabled="true" type="text" class="form-control input-sm">
							            <div class="input-group-btn">
							            	<button ng-click="showEmpreendimentos()" tabindex="-2" class="btn btn-sm btn-primary" type="button">
							            		<i class="fa fa-building-o"></i>
							            	</button>
							            </div>
							        </div>
								</div>
							</div>
							<br/>
							<div class="row" ng-if="configuracao.flg_controlar_validade_transferencia == 0">
								<div class="col-sm-12">
									<div class="table-responsive">
										<div class="form-group" id="produtos">
											<table class="table table-bordered table-condensed table-striped table-hover" id="produtos">
												<thead>
													<tr>
														<td colspan="11"><i class="fa fa-archive"></i> Produtos</td>
														<td width="80" align="center">
															<button class="btn btn-xs btn-primary" tooltip title="Selecionar produto(s)" ng-disabled="!isNumeric(transferencia.id_empreendimento_transferencia)" ng-click="showProdutos()"><i class="fa fa-plus-circle"></i></button>
															<button 
																ng-disabled="!isNumeric(transferencia.id_empreendimento_transferencia)"
																class="btn btn-xs" ng-click="addFocus()" 
																ng-class="{ 'btn-info' : (busca_cod_barra == false), 'btn-success' : (busca_cod_barra == true) }">
																<i class="fa fa-barcode"></i>
															</button>
														</td>
													</tr>
												</thead>
												<tbody>
													<tr>
														<th>ID</th>
														<th>Produto</th>
														<th>Fabricante</th>
														<th>Tamanho</th>
														<th>Sabor/Cor</th>
														<th class="text-center" >Estoque</th>
														<th class="text-center" width="90" ng-if="funcioalidadeAuthorized('ver_valor_custo_produto')">
															Vlr. Custo
															<div class="btn-group">
																<button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																		<span class="caret"></span>
																</button>
																<ul class="dropdown-menu">
																	<li>
																		<a href="" class="text-left" ng-click="setarVlrCusto(null,'vlr_custo_real')">
																			Custo
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_custo_real'" ></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(null,'vlr_venda_atacado')">
																			Atacado
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_atacado'"></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(null,'vlr_venda_intermediario')">
																			Intermediário
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_intermediario'"></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(null,'vlr_venda_varejo')">
																			Varejo
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_varejo'"></i>
																		</a>
																	</li>
																</ul>	
															</div>	
														</th>
														<th class="text-center" >Qtd.Pedida</th>
														<th class="text-center">Qtd. Multipla</th>
														<th>Qtd. Transferir</th>
														<th width="250">
															Selecione o depósito de saída
															<button style="float:right" class="btn btn-xs btn-info" ng-disabled="!isNumeric(transferencia.id_empreendimento_transferencia)" ng-click="selDeposito()" tooltip data-placement="top" title="Selecionar depósito para todos os produtos"><i class="fa fa-sitemap"></i></button>
														</th>
														<th></th>
													</tr>
													<tr ng-repeat="(index, item) in transferencia.produtos" id="tr-prd-{{ item.id }}">
														<td>{{ item.id	 }}</td>
														<td>{{ item.nome }}</td>
														<td>{{ item.nome_fabricante }}</td>
														<td>{{ item.peso }}</td>
														<td>{{ item.sabor }}</td>
														<td class="text-center" ng-if="!item.load_estoque">{{ item.qtd_item }}</td>
														<td class="text-center" ng-if="item.load_estoque"><i class='fa fa-refresh fa-spin'></i></td>
														<td class="text-center" ng-if="funcioalidadeAuthorized('ver_valor_custo_produto')">
															{{ item.vlr_custo | numberFormat:2:',':'.' }}
															<div class="btn-group">
																<button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																		<span class="caret"></span>
																</button>
																<ul class="dropdown-menu">
																	<li>
																		<a href="" class="text-left" ng-click="setarVlrCusto(item,'vlr_custo_real')">
																			Custo: R$ {{ item.vlr_custo_real | numberFormat:2:',':'.' }}
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_custo_real'" ></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(item,'vlr_venda_atacado')">
																			Atacado: R$ {{ item.vlr_venda_atacado | numberFormat:2:',':'.' }}
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_atacado'"></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(item,'vlr_venda_intermediario')">
																			Intermediário: R$ {{ item.vlr_venda_intermediario | numberFormat:2:',':'.' }}
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_intermediario'"></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(item,'vlr_venda_varejo')">
																			Varejo: R$ {{ item.vlr_venda_varejo | numberFormat:2:',':'.' }}
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_varejo'"></i>
																		</a>
																	</li>
																</ul>	
															</div>													
														</td>
														<td width="80" class="text-center">{{ item.qtd_pedida }}</td>
														<td class="text-center">{{ item.qtd_multiplo_transferencia }}</td>
														<td  width="100" align="center" id="td-prd-{{ item.id }}" >
															<div class="form-group">
																<input type="text" 
																	class="form-control text-center input-xs input-group" 
																	onKeyPress="return SomenteNumero(event);" 
																	style="width: 75px" 
																	ng-model="item.qtd_transferida" 
																	ng-if="item.flg_unidade_fracao != 1" 
																	ng-blur="verificaQtdMultiplo('produtos',index,item)"
																	id="txt-qtd-multiplo-{{ index }}"/>
															</div>
															<div class="form-group">
																<input type="text" 
																	class="form-control text-center input-xs input-group" 
																	onKeyPress="return SomenteNumero(event);" 
																	style="width: 75px" 
																	ng-model="item.qtd_transferida" 
																	ng-if="item.flg_unidade_fracao == 1" 
																	ng-blur="verificaQtdMultiplo('produtos',index,item)"
																	id="txt-qtd-multiplo-{{ index }}" 
																	thousands-formatter precision="3"/>
															</div>
														</td>
														<td id="td-prd-deposito-saida-{{ item.id }}">
															<select chosen ng-change="loadestoque(item)" 
														    option="depositos_chosen"
														    ng-model="item.id_deposito_saida"
														    ng-options="deposito.id as deposito.nme_deposito for deposito in depositos_chosen">
															</select>
														</td>
														<td align="center">
															<button ng-if="item.add == 1" class="btn btn-xs btn-danger" ng-click="excluirProdutoLista($index)"><i class="fa fa-trash-o"></i></button>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							<div class="row" ng-if="configuracao.flg_controlar_validade_transferencia == 1">
								<div class="col-sm-12">
									<div class="form-group" id="produtos">
										<div class="table-responsive">
											<table class="table table-bordered table-condensed table-striped table-hover">
												<thead>
													<tr>
														<td colspan="9"><i class="fa fa-archive"></i> Produtos</td>
														<td width="60" align="center">
														<button class="btn btn-xs btn-primary" ng-disabled="!isNumeric(transferencia.id_empreendimento_transferencia)" ng-click="showProdutosByValidade()"><i class="fa fa-plus-circle"></i></button>
														</td>
													</tr>
												</thead>
												<tbody>
													<tr>
														<th>ID</th>
														<th>Produto</th>
														<th>Fabricante</th>
														<th>Peso</th>
														<th>Sabor</th>
														<th class="text-center" >Estoque</th>
														<th class="text-center" width="90">
															Vlr. Custo
															<div class="btn-group">
																<button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																		<span class="caret"></span>
																</button>
																<ul class="dropdown-menu">
																	<li>
																		<a href="" class="text-left" ng-click="setarVlrCusto(null,'vlr_custo_real')">
																			Custo
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_custo_real'" ></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(null,'vlr_venda_atacado')">
																			Atacado
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_atacado'"></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(null,'vlr_venda_intermediario')">
																			Intermediário
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_intermediario'"></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(null,'vlr_venda_varejo')">
																			Varejo
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_varejo'"></i>
																		</a>
																	</li>
																</ul>	
															</div>	
														</th>
														<th class="text-center" >Qtd.Pedida</th>
														<th class="text-center">Qtd. Multipla</th>
														<th>Qtd. Transferir</th>
														<!--<th width="250">
															Depósito
															<button style="float:right" class="btn btn-xs btn-info" ng-disabled="!isNumeric(transferencia.id_empreendimento_transferencia)" ng-click="selDeposito()" tooltip data-placement="top" title="Selecionar deposito para todos os itens"><i class="fa fa-sitemap"></i></button>
														</th>-->
														<th></th>
													</tr>
													<tr ng-repeat="item in transferencia.produtos" id="tr-prd-{{ item.id }}">
														<td>{{ item.id	 }}</td>
														<td>{{ item.nome }}</td>
														<td>{{ item.nome_fabricante }}</td>
														<td>{{ item.peso }}</td>
														<td>{{ item.sabor }}</td>
														<td class="text-center" ng-if="!item.load_estoque">{{ item.qtd_item }}</td>
														<td class="text-center" ng-if="item.load_estoque"><i class='fa fa-refresh fa-spin'></i></td>
														<td class="text-center">
															{{ item.vlr_custo | numberFormat:2:',':'.' }}
															<div class="btn-group">
																<button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																		<span class="caret"></span>
																</button>
																<ul class="dropdown-menu">
																	<li>
																		<a href="" class="text-left" ng-click="setarVlrCusto(item,'vlr_custo_real')">
																			Custo: R$ {{ item.vlr_custo_real | numberFormat:2:',':'.' }}
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_custo_real'" ></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(item,'vlr_venda_atacado')">
																			Atacado: R$ {{ item.vlr_venda_atacado | numberFormat:2:',':'.' }}
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_atacado'"></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(item,'vlr_venda_intermediario')">
																			Intermediário: R$ {{ item.vlr_venda_intermediario | numberFormat:2:',':'.' }}
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_intermediario'"></i>
																		</a>
																		<a href="" class="text-left" ng-click="setarVlrCusto(item,'vlr_venda_varejo')">
																			Varejo: R$ {{ item.vlr_venda_varejo | numberFormat:2:',':'.' }}
																			<i class="fa fa-check-circle-o fa-1" aria-hidden="true" ng-if="item.tipo_vlr_custo == 'vlr_venda_varejo'"></i>
																		</a>
																	</li>
																</ul>	
															</div>													
														</td>
														<td width="80" class="text-center">{{ item.qtd_pedida }}</td>
														<td class="text-center">{{ item.qtd_multiplo_transferencia }}</td>
														<td  width="100" align="center" id="td-prd-{{ item.id }}" >
															<div class="input-group" id="dtaInicialDiv">
															<input ng-disabled="true" onKeyPress="return SomenteNumero(event);" style="width: 75px" ng-value="somarQtd(item)"  type="text" class="form-control input-sm" />
															<span id="btnDtaInicial" class="input-group-addon"
															 href=""
															 popover2
															 model="item.validades"
															 title="Validades"
															 func="ctrl"
															 placement="left"
															 content='
																 <table class="table table-bordered table-condensed table-striped table-hover">
																 	<tr ng-repeat="item in model">
																 		<td ng-bind="item.nome_deposito"></td>
																 		<td class"text-center" ng-bind="item.dta_validade|date" ng-if="item.dta_validade != %272099-12-31%27"></td>
																 		<td class"text-center" ng-if="item.dta_validade == %272099-12-31%27"></td>
																 		<td class"text-center" ng-bind="item.qtd_item"></td>
																 		<td width="80" ng-class="{%27has-error%27: item.tooltip != undefined }" >
																 			<input controll-tooltip="item.tooltip" ng-blur="func.clearTooltip(item)" somente-numeros ng-keyUp="func.vericarQtdByValidade(item,%27body%27)"  ng-model="item.qtd_transferida"  type="text" class="form-control input-xs text-center">
						           										</td>
																 	</tr>
																 </table>
														 	'
															><i class="fa fa-calendar"></i></span>
															</div>
														</td>
														<!--<td id="td-prd-deposito-saida-{{ item.id }}">
															<select chosen ng-change="loadestoque(item)" 
														    option="depositos_chosen"
														    ng-model="item.id_deposito_saida"
														    ng-options="deposito.id as deposito.nme_deposito for deposito in depositos_chosen">
															</select>
														</td>-->
														<td align="center">
															<button ng-if="item.add == 1" class="btn btn-xs btn-danger" ng-click="excluirProdutoListaByValidade($index)"><i class="fa fa-trash-o"></i></button>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel-footer">
							<div class="row">
								<span class="pull-right">
								<div class="col-sm-12 pull-right">
									<button ng-click="cancelar()" class="btn btn-danger btn-sm"><i class="fa fa-times-circle"></i> Cancelar</button>
									<button  ng-click="salvarNovaTransferencia(5,$event)" class="btn btn-success btn-sm" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde...">
										<i class="fa fa-save"></i> Salvar
									</button>
									<button  ng-click="salvarTransferencia()" class="btn btn-primary btn-sm" id="salvar-transferencia" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde...">
										<i class="fa fa-paper-plane-o"></i> Salvar e enviar
									</button>
								</div>
								</span>
							</div>
						</div>
					</div>




				</div><!-- /panel -->

				<div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-tasks"></i> Pedidos de Transferência Recebidos</div>

					<div class="panel-body">
						<div class="row">
							<div class="col-sm-12"><div style="display: none" class="alert alert-transferencia-lista"></div></div>
						</div>
						<div class="table-responsive">
							<table class="table table-bordered table-condensed table-striped table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th>Data Pedido</th>
										<th>Data Transferência</th>
										<th>Usuário</th>
										<th>Empreendimento</th>
										<th>Status</th>
										<th width="100" style="text-align: center;">Ações</th>
									</tr>
								</thead>
								<tbody>
									<tr ng-show="listaTransferencias.transferencias == null">
										<td colspan="7" class="text-center">
											<i class='fa fa-refresh fa-spin'></i> Carregando...
										</td>
									</tr>
									<tr ng-show="listaTransferencias.transferencias.length == 0">
										<td colspan="7" class="text-center">
											Nenhuma transferência encontrada
										</td>
									</tr>
									<tr ng-repeat="item in listaTransferencias.transferencias" bs-tooltip>
										<td width="80">{{ item.id }}</td>
										<td>{{ item.dta_pedido | dateFormat : 'dateTime' }}</td>
										<td>{{ item.dta_transferencia | dateFormat : 'dateTime' }}</td>
										<td>{{ item.nome_usuario_transferencia }}</td>
										<td>{{ item.nome_empreendimento_pedido }}</td>
										<td>{{ item.id_status_transferencia == 1 && 'Pedido recebido' || item.dsc_status_transferencia_estoque }}</td>
										<td align="center">
											<button type="button" ng-show="item.id != transferencia.id && item.id_status_transferencia == 1" data-loading-text="<i class='fa fa-refresh fa-spin'></i>" ng-click="editTransferencia($index,$event)" title="Realizar Transferência" class="btn btn-xs btn-info" data-toggle="tooltip">
												<i class="fa fa-arrows-h"></i>
											</button>
											<button type="button" ng-show="item.id == transferencia.id && item.id_status_transferencia != 5" data-loading-text="<i class='fa fa-refresh fa-spin'></i>"  title="Em edição" class="btn btn-xs btn-success" data-toggle="tooltip">
												<i class="fa fa-arrows-h"></i>
											</button>

											<button type="button" ng-show=" item.id != transferencia.id && item.id_status_transferencia == 5" data-loading-text="<i class='fa fa-refresh fa-spin'></i>" ng-click="editTransferencia($index,$event,4)" title="editar pedido" class="btn btn-xs btn-warning" data-toggle="tooltip">
												<i class="fa fa-edit"></i>
											</button>
											<button type="button" ng-show="item.id == transferencia.id && item.id_status_transferencia == 5" data-loading-text="<i class='fa fa-refresh fa-spin'></i>"  title="Em edição" class="btn btn-xs btn-success" data-toggle="tooltip">
												<i class="fa fa-edit"></i>
											</button>

											<button type="button"  ng-click="detalhesPedido(item)" title="Detalhes" class="btn btn-xs btn-primary" data-toggle="tooltip">
												<i class="fa fa-tasks"></i>
											</button>
											<a href="nota-fiscal.php?id_transferencia={{ item.id }}" 
												class="btn btn-xs btn-info" 
												title="Emitir NF-e" data-toggle="tooltip">
												<i class="fa fa-file-text-o"></i>
											</a>
											<button type="button"  ng-click="deletarTransferencia(item)" ng-if="!(item.dta_transferencia)" title="Excluir" class="btn btn-xs btn-danger" data-toggle="tooltip">
												<i class="fa fa-trash-o"></i>
											</button>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="panel-footer">
						<div class="row">
							<div class="col-sm-12">
								<ul class="pagination pagination-xs m-top-none pull-right" ng-show="listaTransferencias.paginacao.length > 1">
									<li ng-repeat="item in listaTransferencias.paginacao" ng-class="{'active': item.current}">
										<a href="" ng-click="loadtransferencias(item.offset,item.limit)">{{ item.index }}</a>
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
						            <input ng-model="busca.empreendimento" ng-keyup="loadAllEmpreendimentos(0,10)" type="text" class="form-control input-sm">
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
		<div ng-if="configuracao.flg_controlar_validade_transferencia==1" class="modal fade" id="list_produtos" style="display:none">
  			<div class="modal-dialog modal-xl">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Produtos</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.produto" ng-enter="loadProdutosByValidade(0,10)" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadProdutosByValidade(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
						            		<i class="fa fa-search"></i> Buscar
						            	</button>
						            </div> <!-- /input-group-btn -->
						        </div> <!-- /input-group -->
							</div><!-- /.col -->
						</div>

						<br/>

				   		<div class="row">
				   			<div class="col-sm-12">
				   				<div class="table-responsive">
					   				<table class="table table-bordered table-condensed" id="produtos">
										<thead ng-show="(produtos.length != 0)">
											<tr>
												<th rowspan="2" style="line-height: 46px;" >ID</th>
												<th rowspan="2" style="line-height: 46px;" >Nome</th>
												<th rowspan="2" style="line-height: 46px;" >Fabricante</th>
												<th rowspan="2" style="line-height: 46px;" >Tamanho</th>
												<th rowspan="2" style="line-height: 46px;" >Sabor/cor</th>
												<th colspan="3" class="text-center">Estoque</th>
												<th rowspan="2" style="line-height: 46px;" class="text-center" >Qtd.</th>
												<th rowspan="2" style="line-height: 46px;" ></th>
											</tr>
											<tr>
												<td class="text-center">Depósito</td>
												<td width="50">Validade</td>
												<td width="50">Qtd.</td>
											</tr>
										</thead>
										<tbody>
											<tr ng-show="(produtos.length == 0)" class="text-center">
												<td colspan="7">Nenhum produto encontrado</td>
											</tr>
											<tr ng-show="produtos == null" class="text-center">
												<td colspan="7" ><i class='fa fa-refresh fa-spin'></i> Carregando...</td>
											</tr>
											<tr ng-repeat-start="(key,item) in produtos" ng-class="{'tr-pintada': (key%2)!=0 }">
												<td rowspan="{{ item.group.length }}" >
													<span style="display:block; margin-top: {{ item.group.length > 1 && item.group.length*12.5 || 0  }}px ">{{ item.id_produto }}</span>
												</td>
												<td rowspan="{{ item.group.length }}" >
													<span style="display:block; margin-top: {{ item.group.length > 1 && item.group.length*12.5 || 0  }}px ">{{ item.nome_produto }}</span>
												</td>
												<td rowspan="{{ item.group.length }}" >
													<span style="display:block; margin-top: {{ item.group.length > 1 && item.group.length*12.5 || 0  }}px ">{{ item.nome_fabricante }}</span>
												</td>
												<td rowspan="{{ item.group.length }}" >
													<span style="display:block; margin-top: {{ item.group.length > 1 && item.group.length*12.5 || 0  }}px ">{{ item.peso }}</span>
												</td>
												<td rowspan="{{ item.group.length }}" >
													<span style="display:block; margin-top: {{ item.group.length > 1 && item.group.length*12.5 || 0  }}px ">{{ item.sabor }}</span>
												</td>
												<td class="text-center">
													{{ item.nome_deposito }}
												</td>
												<td>
													<span ng-if="item.dta_validade != '2099-12-31'">{{ item.dta_validade | date }}<span>
												</td>
												<td>
													{{ item.qtd_item }}
												</td>
												<td  width="50" ng-class="{'has-error': item.tooltip != undefined }">
													<input controll-tooltip="item.tooltip"  ng-blur="clearTooltip(item)"  container="#list_produtos" somente-numeros ng-keyUp="vericarQtdByValidade(item,'#list_produtos')" ng-model="item.qtd_transferida" type="text" class="form-control text-center input-xs" />
												</td>
												<td width="50" align="center">
													<button ng-show="!produtoSelectedByValidade(item.id)" ng-disabled="!isNumeric(item.qtd_transferida)" type="button" class="btn btn-xs btn-success" ng-click="addProdutoByValidade(item)">
														<i class="fa fa-check-square-o"></i> Selecionar
													</button>
													<button ng-show="produtoSelectedByValidade(item.id)" ng-show="existsAcessorio(item)" ng-disabled="true" class="btn btn-primary btn-xs" type="button">
	                                                	<i class="fa fa-check-circle-o"></i> Selecionado
	                                            	</button>
												</td>
											</tr>
											<tr ng-repeat-end  ng-repeat="item_validade in item.group" ng-if="(item.id != item_validade.id)" ng-class="{'tr-pintada': (key%2)!=0 }">
												<td class="text-center">{{ item_validade.nome_deposito }}</td>
												<td>
													<span ng-if="item_validade.dta_validade != '2099-12-31'">{{ item_validade.dta_validade | date }}<span>
												</td>
												<td>
													{{ item_validade.qtd_item }}
												</td>
												<td  width="50" ng-class="{'has-error': item_validade.tooltip != undefined }">
													<input controll-tooltip="item_validade.tooltip"  ng-blur="clearTooltip(item_validade)" somente-numeros  ng-model="item_validade.qtd_transferida" ng-keyUp="vericarQtdByValidade(item_validade,'#list_produtos')" type="text" class="form-control input-xs" />
												</td>
												<td width="50" align="center">
													<button ng-show="!produtoSelectedByValidade(item_validade.id)"  ng-disabled="!isNumeric(item_validade.qtd_transferida)" type="button" class="btn btn-xs btn-success" ng-click="addProdutoByValidade(item_validade)">
														<i class="fa fa-check-square-o"></i> Selecionar
													</button>
													<button ng-show="produtoSelectedByValidade(item_validade.id)"  ng-disabled="true" class="btn btn-primary btn-xs" type="button">
	                                                	<i class="fa fa-check-circle-o"></i> Selecionado
	                                            	</button>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
				   			</div>
				   		</div>

				   		<div class="row">
					    	<div class="col-sm-12">
					    		<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao.produtos.length > 1">
									<li ng-repeat="item in paginacao.produtos" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadProdutosByValidade(item.offset,item.limit)">{{ item.index }}</a>
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
		<div ng-if="configuracao.flg_controlar_validade_transferencia==0" class="modal fade" id="list_produtos" style="display:none">
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
				   				<div class="table-responsive">
					   				<table class="table table-bordered table-condensed table-striped table-hover" id="produtos">
										<thead ng-show="(produtos.length != 0)">
											<tr>
												<th >ID</th>
												<th >Nome</th>
												<th >Fabricante</th>
												<th >Tamanho</th>
												<th >Sabor/Cor</th>
												<th >Estoque</th>
												<th >Qtd.</th>
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
												<td>{{ item.qtd_item }}</td>
												<td  width="100">
													<div class="form-group">
														<input type="text" 
															class="form-control text-center input-xs input-group" 
															onKeyPress="return SomenteNumero(event);" 
															ng-model="item.qtd_pedida" 
															ng-if="item.flg_unidade_fracao != 1" 
															ng-blur="verificaQtdMultiplo('produtos',index,item)"
															id="txt-qtd-multiplo-{{ index }}"
															ng-enter="addProduto(item)"
															ng-disabled="produtoSelected(item.id)"/>
													</div>
													<div class="form-group">
														<input type="text" 
															class="form-control text-center input-xs input-group" 
															onKeyPress="return SomenteNumero(event);" 
															ng-model="item.qtd_pedida" 
															ng-if="item.flg_unidade_fracao == 1" 
															ng-blur="verificaQtdMultiplo('produtos',index,item)"
															id="txt-qtd-multiplo-{{ index }}" 
															thousands-formatter precision="3"
															ng-enter="addProduto(item)"
															ng-disabled="produtoSelected(item.id)"/>
													</div>
												</td>
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
								<b>Usuário: </b> {{view.transferencia.nome_usuario_pedido}}<br/>
								<b>Empreendimento: </b> {{view.transferencia.nome_empreendimento_pedido}}<br/>
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
											<th class="text-center" >Quantidade Pedida</th>
											<th ng-show="item.id_status_transferencia == 2" class="text-center">Quantidade Transferida</th>
											<th ng-show="item.id_status_transferencia == 3" class="text-center">Quantidade Entregue</th>
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

		<!-- /Modal depositos-->
		<div class="modal fade" id="list_depositos" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Depósitos</span></h4>
      				</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
						            <input ng-model="busca.depositos" ng-enter="loadDepositos(0,10)" type="text" class="form-control input-sm">
						            <div class="input-group-btn">
						            	<button ng-click="loadDepositos(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
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
									<thead ng-show="(depositos.length != 0)">
										<tr>
											<th colspan="2">Nome</th>
										</tr>
									</thead>
									<tbody>
									<tr ng-show="depositos == null">
                                        <th class="text-center" colspan="9" style="text-align:center"><i class='fa fa-refresh fa-spin'></i> Carregando ...</th>
                                    </tr>
                                    <tr ng-show="depositos.length == 0">
                                        <th colspan="4" class="text-center">Não a resultados para a busca</th>
                                    </tr>
										<tr ng-repeat="item in depositos">
											<td>{{ item.nme_deposito }}</td>
											<td width="50" align="center">
												<button type="button" class="btn btn-xs btn-success" ng-click="addDeposito(item)">
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
					    		<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao_depositos.length > 1">
									<li ng-repeat="item in paginacao_depositos" ng-class="{'active': item.current}">
										<a href="" h ng-click="loadDepositos(item.offset,item.limit)">{{ item.index }}</a>
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


	<input ng-model="cod_barra_busca" ng-blur="blurBuscaCodBarra(cod_barra_busca)"  class="form-control input-sm" style="position: absolute;top: -100px" id="focus" ng-enter="loadProdutosCodigoBarra()"/>


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

	<!-- Easy Modal -->
    <script src="js/eModal.js"></script>

    <!-- UnderscoreJS -->
	<script type="text/javascript" src="bower_components/underscore/underscore.js"></script>

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
    	var addParamModule = ['angular.chosen'] ;
    </script>
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/pedido_transferencia_recebido-controller.js"></script>
	<script type="text/javascript"></script>>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

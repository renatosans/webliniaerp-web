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

	<!-- Bower Components -->	
	<link href="bower_components/noty/lib/noty.css" rel="stylesheet">

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-skin.css" rel="stylesheet">

	<link rel="stylesheet" type="text/css" href="css/bootstrap-timepicker.css">

	<!-- Datepicker -->
	<link href="css/datepicker.css" rel="stylesheet"/>

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

		.has-error td {
			background: #FA8072 !important ;
		}

	</style>
  </head>

  <body class="overflow-hidden" ng-controller="BaixaEstoqueController" ng-cloak>
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
					 <li><i class="fa fa-sitemap"></i> <a href="depositos.php">Depósitos</a></li>
					 <li><i class="fa fa-list-ol"></i> <a href="estoque.php">Controle de Estoque</a></li>
					 <li class="active"><i class="fa fa-caret-square-o-down"></i> Baixa Manual de Estoque</li>
				</ul>
			</div><!-- breadcrumb -->

			<div class="main-header clearfix">
				<div class="page-title">
					<h3 class="no-margin"><i class="fa fa-caret-square-o-down"></i> Baixa Manual de Estoque</h3>
					<br/>
					<a class="btn btn-info" id="btn-novo" ng-disabled="editing" ng-click="showBoxNovo()"><i class="fa fa-plus-circle"></i> Nova Baixa</a>
				</div><!-- /page-title -->
			</div><!-- /main-header -->

			<div class="padding-md">
				<div class="alert alert-sistema" style="display:none"></div>

				<div class="panel panel-default" id="box-novo" style="display:none">
					<div class="panel-heading"><i class="fa fa-plus-circle"></i> Nova Baixa</div>

					<div class="panel-body">
							<div class="alert alert-success-baixa" style="display:none"></div>
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
										<label class="control-label">Profissional</label>
										<div class="input-group">
											<input ng-click="selProfissionais()" type="text" class="form-control" ng-model="estoqueSaida.nome_profissional" readonly="readonly" style="cursor: pointer;" />
											<span class="input-group-btn">
												<button ng-click="selProfissionais()" type="button" class="btn"><i class="fa fa-user-md"></i></button>
											</span>
										</div>
									</div>
								</div>
								<!--<div class="col-sm-4">
									<div class="form-group">
										<label class="control-label">Cliente</label>
										<div class="input-group">
											<input ng-click="selPaciente()" type="text" class="form-control" ng-model="estoqueSaida.nome_paciente" readonly="readonly" style="cursor: pointer;" />
											<span class="input-group-btn">
												<button ng-click="selPaciente()" type="button" class="btn"><i class="fa fa-user"></i></button>
											</span>
										</div>
									</div>
								</div>-->
								<div class="col-sm-4">
									<div class="form-group">
										<label class="control-label">Depósito</label>
										<div class="input-group">
											<input ng-click="selDeposito()" type="text" class="form-control" ng-model="estoqueSaida.nome_deposito" readonly="readonly" style="cursor: pointer;" />
											<span class="input-group-btn">
												<button ng-click="selDeposito()" type="button"  class="btn"><i class="fa fa-sitemap"></i></button>
											</span>
										</div>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label class="control-label">Motivo</label>
										<select allow-single-deselect="true"
											    ng-model="estoqueSaida.id_motivo"
											    ng-options="item.id as item.dsc_motivo for item in motivos"
											    class="form-control">
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<label class="control-label">Descrição</label>
									<div class="form-group">
										<textarea class="form-control" rows="3" ng-model="estoqueSaida.descricao"></textarea>
									</div>
								</div>
							</div>
							<div class="row" >
								<div class="col-sm-12">
									<div class="produtos form-group" id="produtos">
											<table class="table table-bordered table-condensed table-striped table-hover">
												<thead>
													<tr>
														<td colspan="6"><i class="fa fa fa-th fa-lg"></i> Produtos</td>
														<td width="60" align="center">
															<button class="btn btn-xs btn-primary" ng-disabled="!isNumeric(estoqueSaida.id_deposito)" ng-click="selProduto()"><i class="fa fa-plus-circle"></i></button>
														</td>
													</tr>
												</thead>
												<tbody>
													<tr ng-show="(estoqueSaida.produtos.length == 0)">
														<td colspan="7" align="center">Nenhum produto selecionado</td>
													</tr>
													<tr ng-show="(estoqueSaida.produtos.length > 0)">
														<td>#</td>
														<td class="text-center">Produto</td>
														<td class="text-center">Fabricante</td>
														<td class="text-center">Tamanho</td>
														<td class="text-center">Sabor/Cor</td>
														<td class="text-center" width="50">Qtd.</td>
														<td class="text-center" align="center"></td>
													</tr>
													<tr ng-repeat="item in estoqueSaida.produtos" id="tr-list-produtos-{{ item.id_produto  }}">
														<td>{{ item.id_produto }}</td>
														<td>{{ item.nome_produto }}</td>
														<td>{{ item.nome_fabricante }}</td>
														<td>{{ item.peso }}</td>
														<td>{{ item.sabor }}</td>
														<td  width="80">
															<input style="text-align: center" onKeyPress="return SomenteNumero(event);" ng-if="item.flg_unidade_fracao != 1" ng-model="item.qtd_saida" ng-keyup="" type="text" class="form-control input-xs" />
															<input style="text-align: center" onKeyPress="return SomenteNumero(event);" ng-if="item.flg_unidade_fracao == 1" thousands-formatter precision="3" ng-model="item.qtd_saida" ng-keyup="" type="text" class="form-control input-xs" />
														</td>
														<td align="center">
															<button class="btn btn-xs btn-danger" ng-click="delProduto($index)"><i class="fa fa-trash-o"></i></button>
														</td>
													</tr>
												</tbody>
											</table>
								
									</div>
								</div>
							</div>
							<!--<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label class="control-label">Data de saida </label>
										<div class="input-group">
											<input readonly="readonly" style="background:#FFF;cursor:pointer" type="text" id="dtaInicial" class="datepicker form-control text-center">
											<span class="input-group-addon" id="cld_dtaInicial"><i class="fa fa-calendar"></i></span>
										</div>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label class="control-label">&nbsp</label>
										<input type="time" class="form-control input" style="width: 50%;">
									</div>
								</div>
							</div>-->
							<br/>
							<div class="row">
								<div class="col-sm-12" class="pull-right">
									<div class="pull-right">
										<button ng-click="showBoxNovo(); reset();" type="submit" class="btn btn-default btn-sm">
											<i class="fa fa-times-circle"></i> Cancelar
										</button>
										<button data-loading-text="<i class='fa fa-refresh fa-spin'></i> Aguarde..." id="salvar-baixa-estoque" ng-click="salvar()" type="submit" class="btn btn-success btn-sm">
											<i class="fa fa-save"></i> Salvar
										</button>
									</div>
								</div>
							</div>
					</div>
				</div><!-- /panel -->

				<!--<div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-tasks"></i> Baixas realizadas</div>

					<div class="panel-body">
						<table class="table table-bordered table-condensed table-striped table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Descrição</th>
									<th width="80" style="text-align: center;">Opções</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="item in agendamentos">
									<td width="80">{{ item.id }}</td>
									<td>{{ item.descricao_categoria }}</td>
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
				</div>-->
			</div>
		</div><!-- /main-container -->

		<!-- /Modal Clientes-->
        <div class="modal fade" id="list_pacientes" style="display:none">
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
                                    <input ng-model="busca.pacientes"  ng-enter="loadPacientes(0,10)" type="text" class="form-control input-sm">
                                    <div class="input-group-btn">
                                        <button ng-click="loadPacientes(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
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
                                   <tr ng-show="pacientes == null">
                                        <th class="text-center" colspan="9" style="text-align:center"><i class='fa fa-refresh fa-spin'></i> Carregando ...</th>
                                    </tr>
                                    <tr ng-show="pacientes.length == 0">
                                        <th colspan="4" class="text-center">Não a resultados para a busca</th>
                                    </tr>
                                    <thead ng-show="(pacientes.length != 0)">
                                        <tr>
                                            <th >Nome</th>
                                            <th >Apelido</th>
                                            <th >Perfil</th>
                                            <th colspan="2">selecionar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="item in pacientes">
                                            <td>{{ item.nome }}</td>
                                            <td>{{ item.apelido }}</td>
                                            <td>{{ item.nome_perfil }}</td>
                                            <td width="50" align="center">
                                                <button  type="button" class="btn btn-xs btn-success" ng-click="addPaciente(item)">
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
                                <ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao_pacientes.length > 1">
                                    <li ng-repeat="item in paginacao_pacientes" ng-class="{'active': item.current}">
                                        <a href="" h ng-click="loadPacientes(item.offset,item.limit)">{{ item.index }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
        <!-- /Modal Profissinais-->
        <div class="modal fade" id="list_profissioanais" style="display:none">
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
                                    <input ng-model="busca.profissionais"  ng-enter="loadProfissionais(0,10)" ng-keyup="loadProfissionais(0,10)" type="text" class="form-control input-sm">
                                    <div class="input-group-btn">
                                        <button ng-click="loadProfissionais(0,10)" tabindex="-1" class="btn btn-sm btn-primary" type="button">
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
                                    <tr ng-show="profissionais == null">
                                        <th class="text-center" colspan="9" style="text-align:center"><i class='fa fa-refresh fa-spin'></i> Carregando ...</th>
                                    </tr>
                                    <tr ng-show="profissionais.length == 0">
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
                                                <button  type="button" class="btn btn-xs btn-success" ng-click="addProfissional(item)">
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
       <!-- /Modal Produtos-->
		<div class="modal fade" id="list_produtos" style="display:none">
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
						            <input ng-model="busca.produtos" ng-enter="loadProdutos(0,10)" type="text" class="form-control input-sm">
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
											<th >Qtd. estoque</th>
											<th >Qtd. saida</th>
											<th ></th>
										</tr>
									</thead>
									<tbody>
									<tr ng-show="produtos == null">
                                        <th class="text-center" colspan="9" style="text-align:center"><i class='fa fa-refresh fa-spin'></i> Carregando ...</th>
                                    </tr>
                                    <tr ng-show="produtos.length == 0">
                                        <th colspan="4" class="text-center">Não a resultados para a busca</th>
                                    </tr>
										<tr ng-repeat="item in produtos">
											<td>{{ item.id_produto }}</td>
											<td>{{ item.nome_produto }}</td>
											<td>{{ item.nome_fabricante }}</td>
											<td>{{ item.peso }}</td>
											<td>{{ item.sabor }}</td>
											<td>{{ item.qtd_real_estoque }}</td>
											<td  width="80">
												<input onKeyPress="return SomenteNumero(event);" ng-if="item.flg_unidade_fracao != 1" ng-model="item.qtd_saida" type="text" class="form-control input-xs" />
												<input onKeyPress="return SomenteNumero(event);" thousands-formatter precision="3" ng-if="item.flg_unidade_fracao == 1" ng-model="item.qtd_saida" type="text" class="form-control input-xs" />
											</td>
											<td width="50" align="center">
												<button  ng-show="!produtoSelected(item.id_produto)" type="button" class="btn btn-xs btn-success" ng-disabled="" ng-click="addProduto(item)">
													<i class="fa fa-check-square-o"></i> Selecionar
												</button>
												<button ng-show="produtoSelected(item.id_produto)" ng-disabled="true" class="btn btn-primary btn-xs" type="button">
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
					    		<ul class="pagination pagination-xs m-top-none pull-right" ng-show="paginacao_produtos.length > 1">
									<li ng-repeat="item in paginacao_produtos" ng-class="{'active': item.current}">
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

		<!-- /Modal depositos-->
		<div class="modal fade" id="list_depositos" style="display:none">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4>Depositos</span></h4>
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

	<!-- Datepicker -->
	<script src='js/bootstrap-datepicker.min.js'></script>

	<!-- Moment -->
	<script src="js/moment/moment.min.js"></script>

	<!-- Bower Components -->	
	<script src="bower_components/noty/lib/noty.min.js" type="text/javascript"></script>
    <script src="bower_components/mojs/build/mo.min.js" type="text/javascript"></script>

	<!-- Extras -->
	<script src="js/extras.js"></script>

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
    <script src="js/app.js"></script>
    <script src="js/auto-complete/AutoComplete.js"></script>
    <script src="js/angular-services/user-service.js"></script>
	<script src="js/angular-controller/baixa_estoque-controller.js"></script>
	<script type="text/javascript">
		$('.datepicker').datepicker();
		$("#cld_dtaInicial").on("click", function(){ $("#dtaInicial").trigger("focus"); });
		$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
	</script>
	<?php include("google_analytics.php"); ?>
  </body>
</html>

app.controller('OrdemServicoController', function($scope, $http, $window, $dialogs, UserService, ConfigService, AsyncAjaxSrvc){
	$scope.userLogged 			= UserService.getUserLogado();
	$scope.configuracoes 		= ConfigService.getConfig($scope.userLogged.id_empreendimento);
	$scope.status_ordem_servico = AsyncAjaxSrvc.getListOfItens(baseUrlApi()+'status/atendimento');;
	$scope.status_servico 		= AsyncAjaxSrvc.getListOfItens(baseUrlApi()+'status/procedimento');;
	$scope.busca 				= { clientes: "", 	servicos: "", 	produtos: "",  nome: "", cod_status_servico: null, show_cancelados: 0, limit: "10"};
	$scope.paginacao			= { clientes: null, servicos: null, produtos: null, ordens_servico: null };

	$scope.showBoxNovo = function(clearData){
    	$scope.editing = !$scope.editing;
		$('#box-novo').toggle(0,function(){$("select").trigger("chosen:updated");});
		if(clearData) {
			clearValidationFormStyle();
			clearObject();
		}
	}

	$scope.editItem = function(item) {
		$scope.objectModel = {
			id: item.cod_ordem_servico,
			id_venda: item.id_venda,
			criador: {
				id: item.cod_criador,
				nme_usuario: item.nme_criador
			},
			cliente: {
				id: item.cod_cliente,
				nome: item.nme_cliente
			},
			flg_recorrente: (!empty(item.flg_recorrente)) ? item.flg_recorrente.toString() : 0,
			qtd_meses_recorrencia: (!empty(item.qtd_meses_recorrencia)) ? item.qtd_meses_recorrencia.toString() : null,
			qtd_recorrencias: item.qtd_recorrencias,
			num_controle: item.num_controle,
			id_venda_principal: item.id_venda_principal,
			dsc_observacoes_gerais: item.dsc_observacoes_gerais,
			id_empreendimento: $scope.userLogged.id_empreendimento,
			cod_status_servico: item.cod_status_servico,
			servicos: [],
			produtos: [],
			vlr_total_servicos: item.vlr_servicos,
			vlr_total_produtos: item.vlr_produtos,
			vlr_total_os: (item.vlr_servicos + item.vlr_produtos),
			dta_ordem_servico: moment(item.dta_ordem_servico, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm:ss')
		};
		loadSaldoDevedorCliente();
		loadProdutosByIdOrdemServico();
		loadServicosByIdOrdemServico();
		$scope.showBoxNovo(false);
		 $(window).scrollTop(0);
		setTimeout(function() {
			$scope.$apply();
		}, 500);
	}

	$scope.showModal = function(modal, objectDestination) {
		$scope.modalSelectDestination = objectDestination;
		switch(modal) {
			case 'list_clientes':
				$scope.loadClientes(0, 10);
				break;
			case 'list_servicos':
				$scope.loadServicos(0, 10);
				break;
			case 'list_produtos':
				$scope.loadProdutos(0, 10);
				break;
		}
		
		$("#"+ modal).modal("show");
	}

	$scope.loadClientes = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;

		$scope.clientes = [];

		var query_string = "?(tue->id_empreendimento[exp]=="+ $scope.userLogged.id_empreendimento +"&usu->id[exp]= NOT IN("+ $scope.configuracoes.id_cliente_movimentacao_caixa +","+ $scope.configuracoes.id_usuario_venda_vitrine +"))";

		if($scope.busca.clientes != "")
			query_string += "&"+$.param({'(usu->nome':{exp:"like '%"+$scope.busca.clientes+"%' OR tpj.razao_social like '%"+$scope.busca.clientes+"%' OR tpj.nome_fantasia like '%"+$scope.busca.clientes+"%' OR usu.apelido LIKE '%"+$scope.busca.clientes+"%')"}});

		$http.get(baseUrlApi()+"usuarios/"+ offset +"/"+ limit +"/"+ query_string)
			.success(function(data, status, headers, config) {
				$.each(data.usuarios,function(i,item){
					if(!empty($scope.objectModel.cliente) && $scope.objectModel.cliente.id === item.id)
						item[$scope.modalSelectDestination+'_selected'] = true;
					else
						item[$scope.modalSelectDestination+'_selected'] = false;

					$scope.clientes.push(item);
				});

				$scope.paginacao.clientes = [];

				$.each(data.paginacao,function(i,item){
					$scope.paginacao.clientes.push(item);
				});
			})
			.error(function(data, status, headers, config) {
				$scope.clientes = false;
			});
	}

	$scope.selectCliente = function(item){
		$("#list_clientes").modal("hide");
		$scope.objectModel[$scope.modalSelectDestination] = item;
		if($scope.modalSelectDestination === 'cliente')
			loadSaldoDevedorCliente();
	}

	$scope.loadServicos = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;

		$scope.servicos = [];

		var query_string = "?id_empreendimento="+ $scope.userLogged.id_empreendimento;

		if($scope.busca.servicos != ""){
			query_string += "&"+$.param({'dsc_procedimento':{exp:"like'%"+$scope.busca.servicos+"%'"}});
		}

		$http.get(baseUrlApi()+"clinica/procedimentos/"+ offset +"/"+ limit +"/"+ query_string)
			.success(function(data, status, headers, config) {
				$.each(data.procedimentos,function(i,item){
					if(!empty(_.findWhere($scope.objectModel.servicos, { cod_procedimento: item.cod_procedimento})))
						item.selected = true;
					else
						item.selected = false;

					$scope.servicos.push(item);
				});
				
				$scope.paginacao.servicos = [];

				$.each(data.paginacao,function(i,item){
					$scope.paginacao.servicos.push(item);
				});
			})
			.error(function(data, status, headers, config) {
				$scope.servicos = false ;
			});
	}

	$scope.selectServico = function(item) {
		item.cod_status_servico = 1;
		item.qtd_pedido = 1;
		$scope.objectModel.servicos.push(item);
		$scope.recalculaTotais();
		$("#list_servicos").modal("hide");
	}

	$scope.loadProdutos = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

    	var query_string = "?tpe->id_empreendimento="+$scope.userLogged.id_empreendimento+"&tp->flg_excluido=0";

    	if($scope.busca.produtos != ""){
    		query_string += "&"+$.param({'(tp->nome':{exp:"like'%"+$scope.busca.produtos+"%' OR tf.nome_fabricante like'%"+$scope.busca.produtos+"%')"}});
    	}

		$scope.produtos = [];
		$http.get(baseUrlApi()+"estoque_produtos/null/"+offset+"/"+limit+"/"+query_string+"&cplSql= ORDER BY tp.nome ASC, tt.nome_tamanho ASC, tcp.nome_cor ASC")
			.success(function(data, status, headers, config) {
				$.each(data.produtos, function(i, item) {
					item.id_produto = parseInt(item.id_produto, 10);

					if(!empty(_.findWhere($scope.objectModel.produtos, { id_produto: item.id_produto})))
						item.selected = true;
					else
						item.selected = false;

					$scope.produtos.push(item);
				});
				$scope.paginacao.produtos 	= data.paginacao;
			})
			.error(function(data, status, headers, config) {
				$scope.produtos = [];
			});
	}

	$scope.selectProduto = function(item) {
		if(empty(item.qtd_pedido))
			item.qtd_pedido = 1;
		$scope.objectModel.produtos.push(item);
		$scope.recalculaTotais();
		$("#list_produtos").modal("hide");
	}

	$scope.removeItem = function(item, objectOwner) {
		$scope.objectModel[objectOwner] = _.without($scope.objectModel[objectOwner], item);
		$scope.recalculaTotais();
		updateView(100);
	}

	$scope.recalculaTotais = function() {
		$scope.objectModel.vlr_total_servicos = _.reduce($scope.objectModel.servicos, function(value, item){
			return value + (parseInt(item.qtd_pedido, 10) * item.vlr_procedimento);
		}, 0);

		$scope.objectModel.qtd_total_servicos = _.reduce($scope.objectModel.servicos, function(value, item){
			return value + parseInt(item.qtd_pedido, 10);
		}, 0);

		$scope.objectModel.vlr_total_produtos = _.reduce($scope.objectModel.produtos, function(value, item){
			return value + (parseInt(item.qtd_pedido, 10) * item.vlr_venda_varejo);
		}, 0);

		$scope.objectModel.qtd_total_produtos = _.reduce($scope.objectModel.produtos, function(value, item){
			return value + parseInt(item.qtd_pedido, 10);
		}, 0);

		$scope.objectModel.vlr_total_os = ($scope.objectModel.vlr_total_servicos + $scope.objectModel.vlr_total_produtos);
	}

	$scope.abrirCaixa = function() {
   		$http.get(baseUrlApi()+"pedido_venda/abrir_caixa/"+ $scope.configuracoes.id_caixa_padrao +"/"+ $scope.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				$scope.caixa = data;
			});
	}

	$scope.save = function() {
		clearValidationFormStyle();

		$('#btnCancelarOS').button('loading');
		$('#btnSalvarOS').button('loading');

		var postData = angular.copy($scope.objectModel);
			postData.id_abertura_caixa 	= $scope.caixa.id;
			postData.id_plano_conta 	= $scope.configuracoes.id_plano_caixa;
			postData.dta_ordem_servico 	= moment(postData.dta_ordem_servico +' '+ moment().format('HH:mm:ss'), 'DD/MM/YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss');

		delete postData.criador.modulosAssociatePage;
		delete postData.criador.empreendimento_usuario;

		$http.post(baseUrlApi()+"ordem-servico", postData)
			.success(function(data, status, headers, config) {
				$('#btnCancelarOS').button('reset');
				$('#btnSalvarOS').button('reset');
				
				$scope.showBoxNovo(true);
				$scope.loadOrdensServicos(0);
			})
			.error(function(errors, status, headers, config) {
				$('#btnCancelarOS').button('reset');
				$('#btnSalvarOS').button('reset');

				if(status === 406) {
					$('.alert-form.alert-warning').text("Atenção! Alguns campos obrigatórios não foram preenchidos.").removeClass('hide');
					applyFormErrors(errors, 'objectModel');
				} else {
					$('.alert-form.alert-danger').text(errors).removeClass('hide');
				}
			});
	}

	function loadSaldoDevedorCliente() {
		$http.get(baseUrlApi()+"usuarios/saldodevedor/"+ $scope.userLogged.id_empreendimento +"?usu->id="+ $scope.objectModel.cliente.id)
			.success(function(data, status, headers, config) {
				$scope.objectModel.cliente.vlr_saldo_devedor = data.vlr_saldo_devedor;
			})
			.error(function(data, status, headers, config) {
				
			});
	}

	$scope.reset = function(){
		$scope.OrdensServicos = {itens:[]};
	}

	$scope.resetFilter = function() {
		$("#dtaInicial").val("");
		$("#dtaFinal").val("");
		$scope.busca.nome = "" ;
		$scope.busca.limit = "10" ;
		$scope.busca.cod_status_servico = null ;
		$scope.reset();
		$scope.loadOrdensServicos(0);
	}

	$scope.loadOrdensServicos = function(offset) {
		$scope.ordens_servico = [];
		$scope.paginacao.ordens_servico = [];

		var query_string = "?atd->id_empreendimento="+ $scope.userLogged.id_empreendimento;

		if($scope.busca.nome != ""){
			query_string += "&("+$.param({'(cli->nome':{exp:"like'%"+$scope.busca.nome+"%') OR jud.razao_social LIKE '%"+$scope.busca.nome+"%' OR jud.nome_fantasia LIKE '%"+$scope.busca.nome+"%')"}});
		}

		if($scope.busca.cod_status_servico != null)
			query_string += "&atd->id_status="+ $scope.busca.cod_status_servico;
		else if(parseInt($scope.busca.show_cancelados, 10) === 0)
			query_string += "&("+$.param({'atd->id_status':{exp:"<> 5)"}});

		if(!empty($("#dtaInicial").val()) && !empty($("#dtaFinal").val())){
			var dta_inicio = moment($("#dtaInicial").val(), 'DD/MM/YYYY').format('YYYY-MM-DD');
			var dta_final = moment($("#dtaFinal").val(), 'DD/MM/YYYY').format('YYYY-MM-DD');
			query_string += "&("+$.param({'2':{exp:"=2 AND cast(ven.dta_venda as date) BETWEEN '"+ dta_inicio +"' AND '"+ dta_final +"' )"}});
		}

		$http.get(baseUrlApi()+"ordens-servico/"+ offset +"/"+ $scope.busca.limit + query_string)
			.success(function(data, status, headers, config) {
				$scope.ordens_servico = data.itens;
				$scope.paginacao.ordens_servico = data.paginacao;
			});
	}

	function loadProdutosByIdOrdemServico() {
		$http.get(baseUrlApi()+"ordem-servico/"+ $scope.objectModel.id +"/produtos")
			.success(function(data, status, headers, config) {
				$scope.objectModel.produtos = data;
			});
	}

	function loadServicosByIdOrdemServico() {
		$http.get(baseUrlApi()+"ordem-servico/"+ $scope.objectModel.id +"/servicos")
			.success(function(data, status, headers, config) {
				$scope.objectModel.servicos = data;
			});
	}

	function clearObject() {
		$scope.objectModel = {
			criador: $scope.userLogged,
			id_empreendimento: $scope.userLogged.id_empreendimento,
			cod_status_servico: 4,
			flg_recorrente: 0,
			servicos: [],
			produtos: [],
			vlr_total_servicos: 0,
			vlr_total_produtos: 0,
			vlr_total_os: 0,
			dta_ordem_servico: moment().format('DD/MM/YYYY HH:mm:ss')
		};
		$scope.loadOrdensServicos(0);
	}

	clearObject();
	$scope.abrirCaixa();

	$('#sizeToggle').trigger("click");
});

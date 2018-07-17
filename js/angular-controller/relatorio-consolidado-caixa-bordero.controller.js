app.controller('RelConsCxaBorderoController', function($scope, $http, $window, UserService, EmpreendimentoService) {
	$scope.userLogged = UserService.getUserLogado();

	$scope.lancamentos = [];
	$scope.showBoxEdit = false;
	$scope.edit = 0;
	$scope.formas_pagamento = [
		{nome:"Cheque",id:2},
		{nome:"Dinheiro",id:3},
		{nome:"Cartão de Débito",id:5},
		{nome:"Cartão de Crédito",id:6},
		{nome:"Voucher",id:10}
	];
	$scope.totaisResumo = {};


	$scope.canShowCardColumns = function(lancamento) {
		return (lancamento.id_forma_pagamento == 5 || lancamento.id_forma_pagamento == 6 || lancamento.id_forma_pagamento == 10);
	}

	$scope.calculateTotais = function() {
		$scope.totais = {
			vlr_lancamento_sistema: 0,
			vlr_lancamento_operador: 0,
			vlr_lancamento_conferencista: 0
		};

		angular.forEach($scope.totaisResumo, function(item) {
			$scope.totais.vlr_lancamento_sistema += item.vlr_lancamento_sistema;
			$scope.totais.vlr_lancamento_operador += item.vlr_lancamento_operador;
			$scope.totais.vlr_lancamento_conferencista += item.vlr_lancamento_conferencista;
		});
	}

	$scope.colspanFp = function(id_forma_pagamento){
		if (!(id_forma_pagamento == 5 || id_forma_pagamento == 6 || id_forma_pagamento == 10)) {
			return 3
		}
	}

	$scope.addParcial = function(){
		if (empty($scope.borderoSelected.conferencias)) {
			$scope.borderoSelected.conferencias = [];	
		}
		$scope.borderoSelected.conferencias.push({});
	}

	$scope.limpaResumo = function(){
		$scope.totaisResumo = {
			cheque: {
				vlr_lancamento_sistema: 0,
				vlr_lancamento_operador: 0,
				vlr_lancamento_conferencista: 0,
				nme_fp: 'Cheque'
			},
			dinheiro: {
				vlr_lancamento_sistema: 0,
				vlr_lancamento_operador: 0,
				vlr_lancamento_conferencista: 0,
				nme_fp: 'Dinheiro'	
			},
			cartao_debito: {
				vlr_lancamento_sistema: 0,
				vlr_lancamento_operador: 0,
				vlr_lancamento_conferencista: 0,
				nme_fp: 'Cartão de Débito'
			},
			cartao_credito: {
				vlr_lancamento_sistema: 0,
				vlr_lancamento_operador: 0,
				vlr_lancamento_conferencista: 0,
				nme_fp: 'Cartão de Crédito'
			},
			voucher: {
				vlr_lancamento_sistema: 0,
				vlr_lancamento_operador: 0,
				vlr_lancamento_conferencista: 0,
				nme_fp: 'Voucher'
			}
		}
	}

	$scope.resumoLancamento = function(){
		$scope.limpaResumo();
		$scope.resumoLancamentosFcAndOp = _.groupBy($scope.borderoSelected.lancamentos, 'id_forma_pagamento');
		angular.forEach($scope.resumoLancamentosFcAndOp, function(item ,index){
			angular.forEach(item, function(i ,x){
				switch (i.id_forma_pagamento) {
					case 2:
						$scope.totaisResumo.cheque.vlr_lancamento_sistema += i.vlr_lancamento_sistema;
						$scope.totaisResumo.cheque.vlr_lancamento_operador += i.vlr_lancamento_operador;
						break;
					case 3:
						$scope.totaisResumo.dinheiro.vlr_lancamento_sistema += i.vlr_lancamento_sistema;
						$scope.totaisResumo.dinheiro.vlr_lancamento_operador += i.vlr_lancamento_operador;
						break;
					case 5:
						$scope.totaisResumo.cartao_debito.vlr_lancamento_sistema += i.vlr_lancamento_sistema;
						$scope.totaisResumo.cartao_debito.vlr_lancamento_operador += i.vlr_lancamento_operador;
						break;
					case 6:
						$scope.totaisResumo.cartao_credito.vlr_lancamento_sistema += i.vlr_lancamento_sistema;
						$scope.totaisResumo.cartao_credito.vlr_lancamento_operador += i.vlr_lancamento_operador;
						break;
					case 10:
						$scope.totaisResumo.voucher.vlr_lancamento_sistema += i.vlr_lancamento_sistema;
						$scope.totaisResumo.voucher.vlr_lancamento_operador += i.vlr_lancamento_operador;
						break;
				}
			})
		});
		$scope.resumoLancamentosConferencista = _.groupBy($scope.borderoSelected.conferencias, 'id_forma_pagamento');
		angular.forEach($scope.resumoLancamentosConferencista, function(item ,index){
			angular.forEach(item, function(i ,x){
				switch (i.id_forma_pagamento){
				 	case 2:
						$scope.totaisResumo.cheque.vlr_lancamento_conferencista += i.vlr_lancamento_conferencista;
						break;
					case 3:
						$scope.totaisResumo.dinheiro.vlr_lancamento_conferencista += i.vlr_lancamento_conferencista;
						break;
					case 5:
						$scope.totaisResumo.cartao_debito.vlr_lancamento_conferencista += i.vlr_lancamento_conferencista;
						break;
					case 6:
						$scope.totaisResumo.cartao_credito.vlr_lancamento_conferencista += i.vlr_lancamento_conferencista;
						break;
					case 10:
						$scope.totaisResumo.voucher.vlr_lancamento_conferencista += i.vlr_lancamento_conferencista;
						break;
				}
			})
		});
		$scope.calculateTotais();
	}

	$scope.removeItemConferencia = function(index){
		$scope.borderoSelected.conferencias.splice(index, 1);
		$scope.resumoLancamento();
	}

	$scope.getAllBorderos = function(offset, limit){
		var queryString = "?bfc->id_empreendimento="+ $scope.userLogged.id_empreendimento;
		$http.get(baseUrlApi()+"borderos/"+ offset +"/"+ limit + queryString)
			.success(function(data, status, headers, config) {
				$scope.borderos = data.borderos;
				$scope.paginacao = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				
			});
	}

	$scope.editBordero = function(item){
		if (!empty(item.dta_fechamento))
			$scope.edit = 1;
		else 
			$scope.edit = 0;

		$scope.borderoSelected = {
			bordero: item,
			fechamento: {
				id_conta_bancaria_destino: (!empty(item.dta_fechamento)) ? item.id_conta_bancaria_destino : null,
				id_usuario: $scope.userLogged.id
			}
		};

		$http.get(baseUrlApi()+"bordero/"+ item.id +"/lancamentos")
			.success(function(data, status, headers, config) {
				$scope.borderoSelected.lancamentos = data;
				$scope.resumoLancamento();
				$scope.calculateTotais();
			})
			.error(function(data, status, headers, config) {
				
			});

		$http.get(baseUrlApi()+"bordero/"+ item.id +"/conferencias")
			.success(function(data, status, headers, config) {
				$scope.borderoSelected.conferencias = data;
				$scope.resumoLancamento();
				$scope.calculateTotais();
			})
			.error(function(data, status, headers, config) {
				
			});	

		$('text-muted').animate({scrollTop: 0 },'slow');
		$scope.showBoxEdit = true;
	}

	$scope.hideBoxEdit = function(){
		$scope.showBoxEdit = false;
		$scope.lancamentos = [];
		$scope.borderoSelected = {};
	}

	$scope.loadBandeiras = function() {
		$scope.bandeiras = [];

		$http.get(baseUrlApi()+"bandeiras")
			.success(function(data, status, headers, config) {
				$scope.bandeiras = data;
			})
			.error(function(data, status, headers, config) {

			});
	}

	$scope.loadMaquinetas = function() {
		$scope.maquinetas = [];

		$http.get(baseUrlApi()+"maquinetas/?maq->id_empreendimento="+$scope.userLogged.id_empreendimento+"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				angular.forEach(data.maquinetas, function(maquineta){
					maquineta.id_maquineta = parseInt(maquineta.id_maquineta, 10);
				});
				$scope.maquinetas = data.maquinetas;
				if($scope.maquinetas.length == 1) $scope.pagamento.id_maquineta = $scope.maquinetas[0].id_maquineta ;
				$scope.paginacao.maquinetas = [] ;
			})
			.error(function(data, status, headers, config) {
				$scope.paginacao.maquinetas = [] ;
			});
	}

	$scope.loadContas = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;
		$scope.contas_bancarias = [] ;
		$http.get(baseUrlApi()+"contas_bancarias/"+offset+"/"+limit+"?id_tipo_conta[exp]=IN (1,2)&id_empreendimento="+$scope.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				$scope.contas_bancarias = data.contas;
			})
			.error(function(data, status, headers, config) {
					
			});
	}

	$scope.fecharBordero = function(){
		var btn = $('#btn-salvar');
		btn.button('loading');

		var err = false;
		if (!empty($scope.borderoSelected.conferencias)) {
			angular.forEach($scope.borderoSelected.conferencias, function(item, index){

				if (empty(item.id_forma_pagamento)) {
					err = true;
					alert('Existem itens sem forma de pagamento');
				} else if(item.id_forma_pagamento == 5 || item.id_forma_pagamento == 6 || item.id_forma_pagamento == 10){
					if (empty(item.id_maquineta)) {
						err = true;
						alert('Existem itens sem maquineta selecionada');
					} else if (empty(item.id_bandeira)){
						err = true;
						alert('Existem itens sem bandeira selecionada');
					}
				} else if (empty(item.vlr_lancamento_conferencista)) {
					err = true;
					alert('Existem itens sem valor de conferência');
				}
			});

		} else {
			err = true;
			alert('Insira formas de pagamento e seus respectivos valores no bloco de conferência');
		}

		if (empty($scope.borderoSelected.fechamento.id_conta_bancaria_destino)) {
			err = true;
			alert('Por Favor selecione uma conta de destino');
		}

		if (err == true) {
			btn.button('reset');
			return false;
		}

		$http.post(baseUrlApi()+"bordero/"+ $scope.borderoSelected.bordero.id +"/fechar/caixa", {data: JSON.stringify($scope.borderoSelected)})
			.success(function(data, status, headers, config) {
				$scope.hideBoxEdit();
				$scope.getAllBorderos(0,10);
				btn.button('reset');
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
			});	
	}

	$scope.getAllBorderos(0,10);
	$scope.loadBandeiras();
	$scope.loadMaquinetas();
	$scope.loadContas(0,100000);
	$scope.limpaResumo();
});
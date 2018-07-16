app.controller('RelConsCxaBorderoController', function($scope, $http, $window, UserService, EmpreendimentoService) {
	$scope.userLogged = UserService.getUserLogado();

	$scope.lancamentos = [];
	$scope.showBoxEdit = false;
	$scope.edit = 0;

	$scope.canShowCardColumns = function(lancamento) {
		return (lancamento.id_forma_pagamento == 5 || lancamento.id_forma_pagamento == 6 || lancamento.id_forma_pagamento == 10);
	}

	$scope.calculateTotais = function() {
		$scope.totais = {
			vlr_lancamento_sistema: 0,
			vlr_lancamento_operador: 0,
			vlr_lancamento_conferencista: 0
		};

		angular.forEach($scope.borderoSelected.lancamentos, function(lancamento) {
			$scope.totais.vlr_lancamento_sistema += lancamento.vlr_lancamento_sistema;
			$scope.totais.vlr_lancamento_operador += lancamento.vlr_lancamento_operador;
			$scope.totais.vlr_lancamento_conferencista += lancamento.vlr_lancamento_conferencista;
		});
	}

	$scope.colspanFp = function(id_forma_pagamento){
		if (!(id_forma_pagamento == 5 || id_forma_pagamento == 6 || id_forma_pagamento == 10)) {
			return 3
		}
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

		$http.get(baseUrlApi()+"bordero/"+ item.id +"/lancamentos")
			.success(function(data, status, headers, config) {
				$scope.borderoSelected = {
					bordero: item,
					fechamento: {
						id_conta_bancaria_destino: (!empty(item.dta_fechamento)) ? item.id_conta_bancaria_destino : null,
						id_usuario: $scope.userLogged.id
					},
					lancamentos: data
				};

				$scope.showBoxEdit = true;
				$scope.calculateTotais();
				$('text-muted').animate({scrollTop: 0 },'slow');
			})
			.error(function(data, status, headers, config) {
				
			});	
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
		angular.forEach($scope.borderoSelected.lancamentos, function(item, index){
			if(item.id_forma_pagamento == 5 || item.id_forma_pagamento == 6 || item.id_forma_pagamento == 10){
				if (empty(item.id_maquineta || item.id_bandeira || item.vlr_lancamento_conferencista)) {
					err = true;
				}
			}
		});

		if (empty($scope.borderoSelected.fechamento.id_conta_bancaria_destino)) {
			err = true;
		}

		if (err == true) {
			alert('Preencha todos os campos');
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
});
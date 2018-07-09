app.controller('RelConsCxaBorderoController', function($scope, $http, $window, UserService, EmpreendimentoService) {
	$scope.userLogged = UserService.getUserLogado();

	$scope.lancamentos = [{
		id: 12,
		id_forma_pagamento: 3,
		nome_forma_pagamento: "Dinheiro",
		id_maquineta: null,
		num_serie_maquineta: null,
		id_bandeira: null,
		nome_bandeira: null,
		vlr_lancamento_sistema: 229.25,
		vlr_lancamento_operador: 248.87,
		vlr_lancamento_conferencista: 0
	}, {
		id: 13,
		id_forma_pagamento: 5,
		nome_forma_pagamento: "Cartão de Débito",
		id_maquineta: null,
		num_serie_maquineta: null,
		id_bandeira: 2,
		nome_bandeira: "MASTERCARD",
		vlr_lancamento_sistema: 368.87,
		vlr_lancamento_operador: 368.87,
		vlr_lancamento_conferencista: 0
	}, {
		id: 14,
		id_forma_pagamento: 6,
		nome_forma_pagamento: "Cartão de Crédito",
		id_maquineta: null,
		num_serie_maquineta: null,
		id_bandeira: 1,
		nome_bandeira: "VISA",
		vlr_lancamento_sistema: 2976.97,
		vlr_lancamento_operador: 2692.98,
		vlr_lancamento_conferencista: 0
	}];

	$scope.canShowCardColumns = function(lancamento) {
		return (lancamento.id_forma_pagamento == 5 || lancamento.id_forma_pagamento == 6 || lancamento.id_forma_pagamento == 10);
	}

	$scope.calculateTotais = function() {
		$scope.totais = {
			vlr_lancamento_sistema: 0,
			vlr_lancamento_operador: 0,
			vlr_lancamento_conferencista: 0
		};

		angular.forEach($scope.lancamentos, function(lancamento) {
			$scope.totais.vlr_lancamento_sistema += lancamento.vlr_lancamento_sistema;
			$scope.totais.vlr_lancamento_operador += lancamento.vlr_lancamento_operador;
			$scope.totais.vlr_lancamento_conferencista += lancamento.vlr_lancamento_conferencista;
		});
	}

	$scope.calculateTotais();
});
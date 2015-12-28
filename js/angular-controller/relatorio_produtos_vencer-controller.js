app.controller('RelatorioProdutosVencerController', function($scope, $http, $window, UserService) {
	var ng = $scope,
		aj = $http;

	ng.tempoAvalicao = 30;
	ng.itensPagina = 10;
	ng.userLogged = UserService.getUserLogado();
	ng.itens = [];
	ng.paginacao = {};

	ng.resetFilter = function() {
		ng.tempoAvalicao = 30;
		ng.itensPagina = 10;
		ng.itens = [];
	}

	ng.loadProdutosVencer = function() {
		aj.get(baseUrlApi()+"produtos/vencer/"+ ng.userLogged.id_empreendimento +"/"+ ng.tempoAvalicao + "/0/" + ng.itensPagina)
			.success(function(data, status, headers, config) {
				ng.itens = data.produtos;
			})
			.error(function(data, status, headers, config) {
				arr = null;
			});
	}

	ng.loadProdutosVencer();
});

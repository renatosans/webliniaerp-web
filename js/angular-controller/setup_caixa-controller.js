app.controller('SetupCaixa', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
	ng.planoConta 	= {};
    ng.planoContas	= [];
    ng.currentNode 	= null;
    ng.editing 		= false;

	ng.loadPlanoContas = function() {
		aj.get(baseUrlApi()+"planocontas")
			.success(function(data, status, headers, config) {
				ng.planoContas = data;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.planoContas = [];
			});
	}

	ng.salvar = function() {
		console.log(ng.currentNode.id);
	}


	ng.loadPlanoContas();
});

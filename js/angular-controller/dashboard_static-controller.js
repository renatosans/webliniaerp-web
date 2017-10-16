app.controller('DashboardController', function($scope, $http, $window, UserService) {

	var ng = $scope,
		aj = $http;
	ng.solitacao_enviada = false ;

	ng.userLogged = UserService.getUserLogado();

	ng.loadModal = function(){
		$('#modal-fim-teste').modal('show');
	}

	ng.loadModal();

	ng.enviarSolicitacao = function(){
		aj.get(baseUrlApi()+"solitacoes/reativar_conta")
			.success(function(data, status, headers, config) {
				ng.solitacao_enviada = true ;
			})
			.error(function(data, status, headers, config) {
				alert('Ocorreu um erro ao enviar sua solitação, tente novamente mais tarde.')
	 		});
	}

});

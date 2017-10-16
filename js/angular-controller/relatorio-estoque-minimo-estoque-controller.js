app.controller('RelatorioProdutosEstoqueMinimoController', function($scope, $http, $window, UserService, EmpreendimentoService) {
	var ng = $scope,
		aj = $http;

	ng.userLogged = UserService.getUserLogado();
	ng.dados_empreendimento = EmpreendimentoService.getDadosEmpreendimento(ng.userLogged.id_empreendimento);


	ng.itensEstoqueMinimo = null;

	ng.loadProdutosEstoqueMinimo = function() {
		ng.itensEstoqueMinimo = [];

		aj.get(baseUrlApi()+"produtos/estoque/minimo?id_empreendimento="+ ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.itensEstoqueMinimo = data;
			})
			.error(function(data, status, headers, config) {
				arr = null;
				ng.status = status;
				ng.itensEstoqueMinimo = null;
				ng.msg_error = data;
			});
	}
	
	ng.doExportExcel = function(id_table){
    	$('#'+ id_table).tableExport({
    		filename: id_table, 
    		type:'excel', 
    		escape:'false'
    	});
    }

	ng.loadProdutosEstoqueMinimo();
});

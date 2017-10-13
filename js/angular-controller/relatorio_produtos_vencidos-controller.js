app.controller('RelatorioProdutosVencidosController', function($scope, $http, $window, UserService, EmpreendimentoService) {
	var ng = $scope,
		aj = $http;
	ng.userLogged = UserService.getUserLogado();
	ng.dados_empreendimento = EmpreendimentoService.getDadosEmpreendimento(ng.userLogged.id_empreendimento);
	ng.itens = null;
	ng.paginacao = {};

	ng.loadProdutosVencidos = function() {
		ng.itens = [];
		aj.get(baseUrlApi()+"produtos/vencidos/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.itens = data.produtos;
			})
			.error(function(data, status, headers, config) {
				arr = null;
				ng.status = status;
				ng.itens = null;
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

	ng.loadProdutosVencidos();
});

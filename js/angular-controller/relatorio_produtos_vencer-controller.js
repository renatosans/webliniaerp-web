app.controller('RelatorioProdutosVencerController', function($scope, $http, $window, UserService) {
	var ng = $scope,
		aj = $http;

	ng.tempoAvalicao = "30";
	ng.itensPagina = "10";
	ng.userLogged = UserService.getUserLogado();
	ng.itens = null;
	ng.paginacao = [];
	ng.paginacao = {};

	ng.doExportExcel = function(id_table){
    	$('#'+ id_table).tableExport({
    		filename: id_table, 
    		type:'excel', 
    		escape:'false'
    	});
    }

	ng.resetFilter = function() {
		ng.tempoAvalicao = "30";
		ng.itensPagina = "10";
		ng.itens = [];
	}

	ng.loadProdutosVencer = function(offset) {
		offset = offset == null ? 0  : offset;

		aj.get(baseUrlApi()+"produtos/vencer/"+ ng.userLogged.id_empreendimento +"/"+ ng.tempoAvalicao +"/"+ offset +"/"+ ng.itensPagina)
			.success(function(data, status, headers, config) {
				ng.itens = data.produtos;
				ng.paginacao = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				arr = null;
			});
	}

	ng.loadProdutosVencer(0);
});

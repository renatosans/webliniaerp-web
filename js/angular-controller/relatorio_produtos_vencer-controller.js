app.controller('RelatorioProdutosVencerController', function($scope, $http, $window, UserService, EmpreendimentoService) {
	var ng = $scope,
		aj = $http;

	ng.tempoAvalicao = "30";
	ng.itensPagina = "10";
	ng.userLogged = UserService.getUserLogado();
	ng.dados_empreendimento = EmpreendimentoService.getDadosEmpreendimento(ng.userLogged.id_empreendimento);
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
		ng.itens = null;
	}

	ng.loadProdutosVencer = function(offset) {
		offset = offset == null ? 0  : offset;
		ng.itens = [];
		ng.msg_error = null;

		aj.get(baseUrlApi()+"produtos/vencer/"+ ng.userLogged.id_empreendimento +"/"+ ng.tempoAvalicao +"/"+ offset +"/"+ ng.itensPagina)
			.success(function(data, status, headers, config) {
				ng.itens = data.produtos;
				ng.paginacao = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				arr = null;
				ng.status = status;
				ng.itens = null;
				ng.msg_error = data;
			});
	}

	ng.loadProdutosVencer(0);
});

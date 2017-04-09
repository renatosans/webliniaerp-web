app.controller('RelatorioAnaliticoEstoqueController', function($scope, $http, $window, UserService,FuncionalidadeService,ConfigService, EmpreendimentoService) {
	var ng = $scope,
		aj = $http;
	ng.userLogged = UserService.getUserLogado();
	ng.config     = ConfigService.getConfig(ng.userLogged.id_empreendimento);
	ng.dados_empreendimento = EmpreendimentoService.getDadosEmpreendimento(ng.userLogged.id_empreendimento);
	ng.itensPorPagina = "10";
	ng.deposito = {};
	ng.depositos = [];
	ng.itens = [];
	ng.paginacao = {};

	ng.funcioalidadeAuthorized = function(cod_funcionalidade){
    	return FuncionalidadeService.Authorized(cod_funcionalidade,ng.userLogged.id_perfil,ng.userLogged.id_empreendimento);
    }

	ng.reset = function() {
		ng.itens = [];
		$(".has-error").removeClass("has-error");
	}

	ng.resetFilter = function() {
		ng.itensPorPagina = "10";
		ng.deposito = {};
		ng.reset();

		if(!ng.funcioalidadeAuthorized('buscar_por_deposito')){
	    	ng.deposito.id = !empty(ng.config.id_deposito_padrao) ? ""+ng.config.id_deposito_padrao : 0 ;
	    	ng.loadItens();
	    }
	}

	ng.aplicarFiltro = function() {
		ng.reset();

		$("#modal-loading").modal('show');

		ng.loadItens(0);
	}

	ng.loadItens = function(offset) {
		aj.get(baseUrlApi()+"relatorio/estoque/analitico/"+ offset +"/"+ng.itensPorPagina+"/?id_deposito="+ng.deposito.id+"&id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.reset();
				ng.itens = data.dados;
				ng.paginacao.itens = data.paginacao;
				$("#modal-loading").modal('hide');
			})
			.error(function(data, status, headers, config) {
				ng.reset();
				$("#modal-loading").modal('hide');
			});
	}

	ng.loadDepositos = function() {
		var id_deposito = "";

		aj.get(baseUrlApi()+"depositos?id_empreendimento="+ng.userLogged.id_empreendimento+id_deposito)
			.success(function(data, status, headers, config) {
				ng.depositos = data.depositos;
			})
			.error(function(data, status, headers, config) {
				console.log(data, status, headers, config);
			});
	}
	if(!ng.funcioalidadeAuthorized('buscar_por_deposito')){
    	ng.deposito.id = !empty(ng.config.id_deposito_padrao) ? ""+ng.config.id_deposito_padrao : 0 ;
    	ng.loadItens();
    }
	ng.reset();
	ng.loadDepositos();

	$('#sizeToggle').trigger("click");
});

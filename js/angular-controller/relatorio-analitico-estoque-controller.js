app.controller('RelatorioAnaliticoEstoqueController', function($scope, $http, $window, UserService,FuncionalidadeService,ConfigService, EmpreendimentoService, TabelaPrecoService) {
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

	ng.existeTabelaPreco = function(nome_tabela){
			return TabelaPrecoService.existeTabelaPreco(ng.userLogged.id_empreendimento, nome_tabela);
		};

		ng.resetFilter = function() {
			$("#dtaInicial").val("");
			$("#dtaFinal").val("");
			ng.reset();
		}

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

	ng.doExportExcel = function(){
		var query_String = "?est->id_deposito="+ng.deposito.id+"&tpp->id_empreendimento="+ng.userLogged.id_empreendimento;
    	window.location.href = baseUrlApi()+"relatorio/estoque/analitico/export"+ query_String;
    }

	ng.loadItens = function(offset) {
		aj.get(baseUrlApi()+"relatorio/estoque/analitico/"+ offset +"/"+ng.itensPorPagina+"/?id_deposito="+ng.deposito.id+"&id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.reset();
				ng.itens = data.dados;
				ng.paginacao.itens = data.paginacao;
				ng.qtd_estoque_total = 0;
				ng.vlr_custo_total = 0;
				ng.vlr_total_venda_atacado = 0;
				ng.vlr_total_venda_intermediario = 0;
				ng.vlr_total_venda_varejo = 0;
				$.each(ng.itens, function(index, item) {
					if(item.qtd_item > 0){
						ng.qtd_estoque_total += item.qtd_item;
						ng.vlr_custo_total += item.vlr_custo_total;
						ng.vlr_total_venda_atacado += item.vlr_total_venda_atacado;
						ng.vlr_total_venda_intermediario += item.vlr_total_venda_intermediario;
						ng.vlr_total_venda_varejo += item.vlr_total_venda_varejo;
					}
				});
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

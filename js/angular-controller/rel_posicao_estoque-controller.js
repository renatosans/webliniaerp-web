app.controller('RelatorioPosicaoEstoqueController', function($scope, $http, $window, UserService, ConfigService, FuncionalidadeService, EmpreendimentoService) {
	var ng 				= $scope,
		aj 				= $http;
	ng.userLogged 		= UserService.getUserLogado();
	ng.dados_empreendimento = EmpreendimentoService.getDadosEmpreendimento(ng.userLogged.id_empreendimento);
	ng.configuracoes 		= ConfigService.getConfig(ng.userLogged.id_empreendimento);
	ng.busca = {};
	ng.busca_deposito = {};
	ng.vlrTotalFaturamento = 0;
	ng.prc_quebra_total = 0;
	ng.med_prc_quebra_total = 0;

	ng.doExportExcel = function(id_table){
    	$('#'+ id_table).tableExport({
    		filename: id_table, 
    		type:'excel', 
    		escape:'false'
    	});
    }

	ng.funcioalidadeAuthorized = function(cod_funcionalidade){
		return FuncionalidadeService.Authorized(cod_funcionalidade,ng.userLogged.id_perfil,ng.userLogged.id_empreendimento);
	}

	ng.loadDepositos = function(offset, limit) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;
		ng.depositos = null ;
		var query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento ;
		if(!empty(ng.busca_deposito.depositos))
			query_string  += "&"+$.param({nme_deposito:{exp:"like '%"+ng.busca_deposito.depositos+"%'"}});

    	aj.get(baseUrlApi()+"depositos/"+offset+"/"+limit+query_string)
		.success(function(data, status, headers, config) {
			ng.depositos = data.depositos ;
			ng.paginacao_depositos = data.paginacao ;
			if(ng.depositos.length == 1){
				ng.busca.nome_deposito = ng.depositos[0].nme_deposito;
				ng.busca.id_deposito   = ng.depositos[0].id;
			}
			
		})
		.error(function(data, status, headers, config) {
			ng.depositos = [] ;	
		});
	}

	ng.loadPosicoes = function(){
		var dta_inicial = moment($("#dta_inicial").val(), 'DD/MM/YYYY').format('YYYY-MM-DD 00:00:00');
		var dta_final = moment($("#dta_final").val(), 'DD/MM/YYYY').format('YYYY-MM-DD 23:59:59');
		
		if (empty(dta_final)){
			alert("Preencha a Data Inicial");
			return false;
		} else if(empty(dta_final)){
			alert("Preencha a Data Final");
			return false;
		} else if(empty(ng.busca.id_deposito)){
			alert("Escolha um depósito");
			return false;
		}

		ng.posicoes = [];
		ng.loadTotalFaturamento();

		var queryString = "?tpe->id_empreendimento="+ ng.userLogged.id_empreendimento 
							+"&tme->dta_movimentacao[exp]=BETWEEN '"+dta_inicial+"' AND '"+dta_final+"'" 
							+"&tme->id_deposito="+ ng.busca.id_deposito;

		aj.get(baseUrlApi() +"relatorio/posicoes-estoque/" +dta_inicial+ "/" +dta_final+ queryString)
			.success(function(data, status, headers, config) {
				ng.posicoes = data;
				angular.forEach(ng.posicoes, function(item, index){
					item.qtd_estoque_inicial = parseFloat(item.qtd_estoque_inicial);
					item.qtd_estoque = parseFloat(item.qtd_estoque);

					item.qtd_saldo_estoque = ((item.qtd_estoque_inicial + item.qtd_compras) - (item.qtd_vendas + item.qtd_baixas_manuais));

					if (item.qtd_saldo_estoque < 0) {
						item.qtd_diferenca = (item.qtd_estoque - (item.qtd_saldo_estoque * -1));
					} else{
						item.qtd_diferenca = (item.qtd_estoque - item.qtd_saldo_estoque );
					}

					item.qtd_quebra = (item.qtd_baixas_manuais + (item.qtd_diferenca * -1));

					item.prc_quebra_faturamento = (((item.qtd_quebra * item.vlr_custo) / ng.vlrTotalFaturamento) * 100);

					ng.prc_quebra_total += item.prc_quebra_faturamento;

				})

				ng.med_prc_quebra_total = (ng.prc_quebra_total / ng.posicoes.length);
			})
			.error(function(data, status, headers, config) {
				ng.posicoes = null;
			});

	}

	ng.resetFilter = function(){
		ng.busca = {};
		ng.busca_deposito = {};
		ng.dta_inicial = "";
		ng.dta_final = "";
	}

	ng.loadTotalFaturamento = function() {
		var first_date = moment($("#dta_inicial").val(), 'DD/MM/YYYY').format('YYYY-MM-DD');
		var last_date = moment($("#dta_final").val(), 'DD/MM/YYYY').format('YYYY-MM-DD');
		aj.get(baseUrlApi()+"total_faturamento/dashboard/"+first_date+'/'+last_date+'?id_empreendimento='+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.vlrTotalFaturamento = data.total_faturamento;
			})
			.error(function(data, status, headers, config) {
				ng.vlrTotalFaturamento = 0 ;
			});
	}

	ng.selDepositoNewPosicao = function(){
		$('#list_depositos_new').modal('show');
		ng.loadDepositos(0,10);
	}

	ng.addDepositoNewPosicao = function(item){
		ng.busca.nome_deposito = item.nme_deposito;
		ng.busca.id_deposito   = item.id;
		$('#list_depositos_new').modal('hide');
	}
});

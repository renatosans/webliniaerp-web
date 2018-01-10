app.controller('RelatorioSangrias', function($scope, $http, $window, UserService, EmpreendimentoService) {
	var ng 				= $scope,
		aj			    = $http;
	ng.userLogged 		= UserService.getUserLogado();
	ng.dados_empreendimento = EmpreendimentoService.getDadosEmpreendimento(ng.userLogged.id_empreendimento);
	ng.itensPorPagina	= 10;
	ng.paginacao  		= { };
	ng.pagamentos = null;

	ng.doExportExcel = function(id_table){
    	$('#'+ id_table).tableExport({
    		filename: id_table, 
    		type:'excel', 
    		escape:'false'
    	});
    }

	ng.reset = function() {
		 $("#dtaInicial").val('');
		 $("#dtaFinal").val('');
		 ng.fornecedor = {} ;
		ng.pagamentos = null;
		ng.msg_error = null;
	}

	ng.resetFilter = function() {
		ng.reset();
	}

	ng.aplicarFiltro = function() {
		ng.msg_error = null;
		$("#modal-aguarde").modal('show');
		ng.loadPagamentos(0, ng.itensPorPagina);
	}

	ng.loadPagamentos = function(offset,limit) {
		var dtaInicial  = $("#dtaInicial").val();
		var dtaFinal    = $("#dtaFinal").val();
		var queryString = "?tac->id_empreendimento="+ ng.userLogged.id_empreendimento;

		if(dtaInicial != "" && dtaFinal != "") {
			dtaInicial = formatDate(dtaInicial);
			dtaFinal   = formatDate(dtaFinal);
			queryString += "&"+$.param({'mvc->dta_movimentacao':{exp:"BETWEEN '"+dtaInicial+" 00:00:00' AND '"+dtaFinal+" 23:59:59'"}});
		} else if(dtaInicial != "") {
			dtaInicial = formatDate(dtaInicial);
			queryString += "&"+$.param({'mvc->dta_movimentacao':{exp:">='"+dtaInicial+"'"}});
		} else if(dtaFinal != "") {
			dtaFinal = formatDate(dtaFinal);
			queryString += "&"+$.param({'mvc->dta_movimentacao':{exp:"<='"+dtaFinal+"'"}});
		}

		aj.get(baseUrlApi() + "relatorio/sangrias"+ queryString)
			.success(function(data, status, headers, config) {
				ng.vlr_total_sangrias = 0;
				$.each(data.vendas, function(i, item) {
					ng.vlr_total_sangrias += item.valor_pagamento;
				});

				ng.planos_contas = _.groupBy(ng.pagamentos, 'dsc_plano');
				$.each(ng.planos_contas, function(nme_plano_conta, itens){
				    ng.planos_contas[nme_plano_conta].itens = itens;
				    ng.planos_contas[nme_plano_conta].total = 0;
				    $.each(ng.planos_contas[nme_plano_conta].itens, function(i, item){
				        ng.planos_contas[nme_plano_conta].total += item.valor_pagamento;
				    });
				});
				

				ng.pagamentos 			 = data.vendas;
				ng.paginacao.pagamentos  = data.paginacao;
				$("#modal-aguarde").modal('hide');
			})
			.error(function(data, status, headers, config) {
				ng.pagamentos = null;
				ng.status = status;
				ng.msg_error = data;
				ng.paginacao.pagamentos = [];
				$("#modal-aguarde").modal('hide');
			});
	}

	ng.reset();
});

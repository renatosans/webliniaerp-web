app.controller('RelatorioSangrias', function($scope, $http, $window, UserService) {
	var ng 				= $scope,
		aj			    = $http;
	ng.userLogged 		= UserService.getUserLogado();
	ng.itensPorPagina	= 10;
	ng.paginacao  		= { };

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
	}

	ng.resetFilter = function() {
		ng.reset();
		ng.loadPagamentos(0, ng.itensPorPagina);
	}

	ng.aplicarFiltro = function() {
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

				ng.pagamentos 			 = data.vendas;
				ng.paginacao.pagamentos  = data.paginacao;
				$("#modal-aguarde").modal('hide');
			})
			.error(function(data, status, headers, config) {
				ng.pagamentos = [] ;
				ng.paginacao.pagamentos = [];
				$("#modal-aguarde").modal('hide');
			});
	}

	ng.reset();
});

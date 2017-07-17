app.controller('RelatorioConsolidadoCaixa', function($scope, $http, $window, UserService, EmpreendimentoService) {
	var ng = $scope,
		aj = $http;

	ng.userLogged 		= UserService.getUserLogado();
	ng.dados_empreendimento = EmpreendimentoService.getDadosEmpreendimento(ng.userLogged.id_empreendimento);
	ng.itens 			= null;
	ng.total 			= 0;
	ng.formas_pagamento = null;
	ng.paginacao 		= {};

	var params = getUrlVars();

	ng.doExportExcel = function(id_table){
    	$('#'+ id_table).tableExport({
    		filename: id_table, 
    		type:'excel', 
    		escape:'false'
    	});
    }

	ng.reset = function() {
		ng.itens = null;
		ng.formas_pagamento = null;
		ng.total = 0;
		$(".has-error").removeClass("has-error");
		$("#dtaMovimentacao").tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	ng.resetFilter = function() {
		$("#dtaMovimentacao").val("");
		ng.deposito = {};
		ng.reset();

		var dtaComparacao = new Date();

		var mes="";
		if((dtaComparacao.getMonth()+1) < 10 )
			mes = "0"+(dtaComparacao.getMonth()+1);
		else{
			mes = (dtaComparacao.getMonth()+1);
		}

		var dia ="" ;
		if(dtaComparacao.getDate() < 10 )
			dia = "0"+dtaComparacao.getDate();
		else{
			dia = dtaComparacao.getDate();
		}

		$("#dtaMovimentacao").val(dia + "/" + mes +"/"+ dtaComparacao.getFullYear());
		ng.loadItens(dtaComparacao.getFullYear() +"-"+ mes +"-"+ dia);
	}

	ng.aplicarFiltro = function() {
		ng.reset();

		var dtaMovimentacao 	= $("#dtaMovimentacao").val();

		if(dtaMovimentacao == "") {
			$("#dtaMovimentacaoDiv").addClass("has-error");

			$("#dtaMovimentacao")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", "Campo obrigatório!")
				.attr("data-original-title", "Campo obrigatório!");
			$("#dtaMovimentacao").tooltip();

			return;
		}

		var date_movimentacao = formatDate(dtaMovimentacao);

		ng.loadItens(date_movimentacao);

		$("#modal-aguarde").modal('show');
	}

	ng.loadItens = function(dtaComparacao) {
		ng.msg_error = null;
		ng.itens = [];

		var params = "?abt_caixa->id_empreendimento="+ ng.userLogged.id_empreendimento;
			params += "&date_format(dta_abertura,%27%Y-%m-%d%27)="+ dtaComparacao;
			params += "&date_format(dta_fechamento,%27%Y-%m-%d%27)="+ dtaComparacao;

		aj.get(baseUrlApi()+"relatorio/caixa/consolidado"+params)
			.success(function(data, status, headers, config) {
				ng.itens = data;

				var formas_pagamento = {
					a_receber: {
						dsc: "A Receber",
						valor: 0
					},
					cartao_credito: {
						dsc: "Cartão de Crédito",
						valor: 0
					},
					cartao_debito: {
						dsc: "Cartão de Débito",
						valor: 0
					},
					cheque: {
						dsc: "Cheque",
						valor: 0
					},
					dinheiro: {
						dsc: "Dinheiro",
						valor: 0
					},
					vale_troca: {
						dsc: "Vale Troca",
						valor: 0
					},
					boleto_bancario: {
						dsc: "Boleto Bancário",
						valor: 0
					},
					transferencia_bancaria: {
						dsc: "Transferência Bancário",
						valor: 0
					}
				};

				$.each(ng.itens, function(i, item) {
					$.each(item.totais.formas_pagamento, function(x, fp) {

						console.log(fp);
						ng.total += parseFloat(fp.valor);
						if(!empty(formas_pagamento[x]))
							formas_pagamento[x].valor += parseFloat(fp.valor);
						else {
							formas_pagamento[x] = fp;
						}
					});
				});

				ng.formas_pagamento = formas_pagamento;

				$("#modal-aguarde").modal('hide');
			})
			.error(function(data, status, headers, config) {
				$("#modal-aguarde").modal('hide');
				ng.itens = null;
				ng.status = status;
				ng.msg_error = data;
			});
	}

	ng.reset();

	var dtaComparacao = new Date();

	var mes="";
	if((dtaComparacao.getMonth()+1) < 10 )
		mes = "0"+(dtaComparacao.getMonth()+1);
	else{
		mes = (dtaComparacao.getMonth()+1);
	}

	var dia ="" ;
	if(dtaComparacao.getDate() < 10 )
		dia = "0"+dtaComparacao.getDate();
	else{
		dia = dtaComparacao.getDate();
	}

	$("#dtaMovimentacao").val(dia + "/" + mes +"/"+ dtaComparacao.getFullYear());
	ng.loadItens(dtaComparacao.getFullYear() +"-"+ mes +"-"+ dia);
});

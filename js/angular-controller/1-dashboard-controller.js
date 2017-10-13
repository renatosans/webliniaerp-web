app.controller('DashboardController', function($scope, $http, $window, UserService) {
	var ng = $scope,
		aj = $http;

	ng.userLogged = UserService.getUserLogado();
	ng.total = {
		vlrTotalFaturamento: 0,
		vlrSaldoDevedorFornecedores: 0,
		vlrSaldoDevedorClientes: 0,
		vlrCustoTotalEstoque: 0,
		vlrTotalPagamentosConfirmados: 0,
		vlrTotalPagamentosNaoConfirmados: {
			cheque: 0,
			boleto: 0,
			credito: 0
		}
	};
	ng.count = {
		produtos: 0,
		clientes: 0,
		vendas: 0,
		orcamentos: 0
	};

	ng.top_10_vendas_categoria = [];
	ng.top_10_vendas_fabricante = [];
	ng.top_10_vendas_produto = [];
	ng.top_10_vendas_cliente = [];

	ng.comparativo_vendas_xkey = 'mes_referencia';
	ng.comparativo_vendas_ykeys = ['total_orcamentos', 'total_vendas_confirmadas'];
	ng.comparativo_vendas_labels = ['Orçamentos', 'Vendas'];
	ng.comparativo_vendas_data = [];

	ng.filtro = {
		dta_inicio: "",
		dta_fim: "",
	};

	ng.aplicarFiltro = function() {
		ng.limparFiltros();
		if(ng.filtro.dta_inicio == "") {
			angular.element(".divDtaInicial").addClass("has-error");

			angular.element(".divDtaInicial")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", "Campo obrigatório!")
				.attr("data-original-title", "Campo obrigatório!");
			angular.element(".divDtaInicial").tooltip();

			return;
		}

		if(ng.filtro.dta_fim == "") {
			angular.element(".divDtaFinal").addClass("has-error");

			angular.element(".divDtaFinal")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", "Campo obrigatório!")
				.attr("data-original-title", "Campo obrigatório!");
			angular.element(".divDtaFinal").tooltip();

			return;
		}

		var date_first = formatDate(ng.filtro.dta_inicio);
		var date_last  = formatDate(ng.filtro.dta_fim);

		if(date_last < date_first){
			// toggle error

			return;
		}

		ng.loadTotalFaturamento(date_first, date_last);
		ng.loadTotalPagamentosConfirmados(date_first, date_last);
		ng.loadTotalPagamentosNaoConfirmados(date_first, date_last, 2); // Cheque
		ng.loadTotalPagamentosNaoConfirmados(date_first, date_last, 4); // Boleto Bancário
		ng.loadTotalPagamentosNaoConfirmados(date_first, date_last, 6); // Cartão de Crédito
		ng.loadSaldoDevedorFornecedor(date_first, date_last);
		ng.loadSaldoDevedorCliente(date_first, date_last);
		ng.loadVendasBycategoria(date_first, date_last);
		ng.loadCountOrcamentos(date_first, date_last);
		ng.loadCountVendas(date_first, date_last);
		ng.loadVendasTop10Clientes(date_first, date_last);
		ng.loadVendasTop10Fabricantes(date_first, date_last);
		ng.loadVendasTop10Produtos(date_first, date_last);
	}

	ng.limparFiltros = function() {
		angular.element(".divDtaInicial").tooltip('destroy');
		angular.element(".divDtaFinal").tooltip('destroy');
		angular.element(".has-error").removeClass("has-error");
	}

	ng.loadTotalFaturamento = function(first_date, last_date) {
		var reqUrl = 'total_faturamento/dashboard/';
			reqUrl += first_date + '/' + last_date;
			reqUrl += '?id_empreendimento='+ ng.userLogged.id_empreendimento;

		aj.get(baseUrlApi() + reqUrl)
			.success(function(d,s,h,c) {
				ng.total.vlrTotalFaturamento = numberFormat(d.total_faturamento, 2, ',', '.');
			})
			.error(function(d,s,h,c) {
				ng.total.vlrTotalFaturamento = 0;
			});
	};

	ng.loadTotalPagamentosConfirmados = function(first_date, last_date) {
		var reqUrl = 'dashboard/total/pagamentos/confirmados/sim/';
			reqUrl += first_date + '/' + last_date;
			reqUrl += '?id_empreendimento='+ ng.userLogged.id_empreendimento;

		aj.get(baseUrlApi() + reqUrl)
			.success(function(d,s,h,c) {
				ng.total.vlrTotalPagamentosConfirmados = numberFormat(d.total_pagamentos_confirmados, 2, ',', '.');
			})
			.error(function(d,s,h,c) {
				ng.total.vlrTotalPagamentosConfirmados = 0;
			});
	};

	ng.loadTotalPagamentosNaoConfirmados = function(first_date, last_date, id_forma_pagamento) {
		var reqUrl = 'dashboard/total/pagamentos/confirmados/nao/';
			reqUrl += first_date + '/' + last_date;
			reqUrl += '?id_empreendimento='+ ng.userLogged.id_empreendimento;
			reqUrl += '&id_forma_pagamento='+ id_forma_pagamento;

		aj.get(baseUrlApi() + reqUrl)
			.success(function(d,s,h,c) {
				switch(id_forma_pagamento) {
					case 2: { // cheque
						ng.total.vlrTotalPagamentosNaoConfirmados.cheque = numberFormat(parseFloat(d.total_pagamentos_nao_confirmados, 10), 2, ',', '.');
						break;
					}
					case 4: { // boleto bancário
						ng.total.vlrTotalPagamentosNaoConfirmados.boleto = numberFormat(parseFloat(d.total_pagamentos_nao_confirmados, 10), 2, ',', '.');
						break;
					}
					case 6: { // cartão de crédito
						ng.total.vlrTotalPagamentosNaoConfirmados.credito = numberFormat(parseFloat(d.total_pagamentos_nao_confirmados, 10), 2, ',', '.');
						break;
					}
				}
			})
			.error(function(d,s,h,c) {
				ng.total.vlrTotalPagamentosNaoConfirmados.cheque = 0;
				ng.total.vlrTotalPagamentosNaoConfirmados.boleto = 0;
				ng.total.vlrTotalPagamentosNaoConfirmados.credito = 0;
			});
	};

	ng.loadSaldoDevedorFornecedor = function(first_date, last_date) {
		var reqUrl = 'saldo_devedor_fornecedor/dashboard/';
			reqUrl += first_date + '/' + last_date;
			reqUrl += '?id_empreendimento='+ ng.userLogged.id_empreendimento;

		aj.get(baseUrlApi() + reqUrl)
			.success(function(d,s,h,c) {
				ng.total.vlrSaldoDevedorFornecedores = numberFormat(d.saldo_devedor_fornecedores, 2, ',', '.');
			})
			.error(function(d,s,h,c) {
				ng.total.vlrSaldoDevedorFornecedores = 0;
			});
	};

	ng.loadSaldoDevedorCliente = function(first_date, last_date) {
		var reqUrl = 'saldo_devedor_cliente/dashboard/';
			reqUrl += first_date + '/' + last_date;
			reqUrl += '?id_empreendimento='+ ng.userLogged.id_empreendimento;

		aj.get(baseUrlApi() + reqUrl)
			.success(function(d,s,h,c) {
				ng.total.vlrSaldoDevedorClientes = numberFormat(d.saldo_devedor_fornecedores, 2, ',', '.');
			})
			.error(function(d,s,h,c) {
				ng.total.vlrSaldoDevedorClientes = 0;
			});
	};

	ng.loadComparativoVendas = function() {
		ng.comparativo_vendas_data = [];

		var reqUrl = "comparativo_vendas/dashboard";
			reqUrl += "?id_empreendimento=" + ng.userLogged.id_empreendimento;

		aj.get(baseUrlApi() + reqUrl)
			.success(function(d,s,h,c) {
				angular.forEach(d, function(item, i) {
					ng.comparativo_vendas_data.push(item);
					ng.vlrTotalVendasPeriodoComparativo += item.total_vendas_confirmadas;
				});
				ng.vlrTotalVendasPeriodoComparativo = numberFormat(ng.vlrTotalVendasPeriodoComparativo, 2, ',', '.');
			})
			.error(function(data, status, headers, config) {
				ng.comparativo_vendas_data = [];
				ng.vlrTotalVendasPeriodoComparativo = 0;
			});
	};

	ng.loadVendasTop10Categoria = function(first_date, last_date) {
		ng.top_10_vendas_categoria = [];
		aj.get(baseUrlApi()+"dashboard/vendas/top10/categoria?fd="+first_date+'&ld='+last_date+'&id_empreendimento='+ng.userLogged.id_empreendimento)
			.success(function(d,s,h,c) {
				angular.forEach(d, function(item, i) {
					var insert = {
						label: item.nome_categoria,
						value: item.qtd_total_vendas
					};
					ng.top_10_vendas_categoria.push(insert);
				});
			})
			.error(function(data, status, headers, config) {
				ng.top_10_vendas_categoria = [];
			});
	};

	ng.loadVendasTop10Fabricante = function(first_date, last_date) {
		ng.top_10_vendas_fabricante = [];
		aj.get(baseUrlApi()+"dashboard/vendas/top10/fabricante?fd="+first_date+'&ld='+last_date+'&id_empreendimento='+ng.userLogged.id_empreendimento)
			.success(function(d,s,h,c) {
				angular.forEach(d, function(item, i) {
					var insert = {
						label: item.nome_fabricante,
						value: item.qtd_total_vendas
					};
					ng.top_10_vendas_fabricante.push(insert);
				});
			})
			.error(function(data, status, headers, config) {
				ng.top_10_vendas_fabricante = [];
			});
	};

	ng.loadVendasTop10Produto = function(first_date, last_date) {
		ng.top_10_vendas_produto = [];
		aj.get(baseUrlApi()+"dashboard/vendas/top10/produto?fd="+first_date+'&ld='+last_date+'&id_empreendimento='+ng.userLogged.id_empreendimento)
			.success(function(d,s,h,c) {
				angular.forEach(d, function(item, i) {
					var insert = {
						label: item.nome,
						value: item.qtd_total_vendas
					};
					ng.top_10_vendas_produto.push(insert);
				});
			})
			.error(function(data, status, headers, config) {
				ng.top_10_vendas_produto = [];
			});
	};

	ng.loadVendasTop10Cliente = function(first_date, last_date) {
		ng.top_10_vendas_cliente = [];
		aj.get(baseUrlApi()+"dashboard/vendas/top10/cliente?fd="+first_date+'&ld='+last_date+'&id_empreendimento='+ng.userLogged.id_empreendimento)
			.success(function(d,s,h,c) {
				angular.forEach(d, function(item, i) {
					var insert = {
						label: item.nome,
						value: item.qtd_total_vendas
					};
					ng.top_10_vendas_cliente.push(insert);
				});
			})
			.error(function(data, status, headers, config) {
				ng.top_10_vendas_cliente = [];
			});
	};

	ng.loadVendasTop10ProdutosEstoqueMinimo = function() {
		ng.vendasFabricantes = [];
		aj.get(baseUrlApi()+"dashboard/vendas/top10/produto/estoque_minimo?id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(d,s,h,c) {
				ng.produtosMinimoEstoque = data;
			})
			.error(function(data, status, headers, config) {
				ng.produtosMinimoEstoque = [];
			});
	};

	ng.loadCountProdutos = function() {
		var vlrTotalVendasPeriodoComparativo = 0 ;
		aj.get(baseUrlApi()+"count_produtos/dashboard?id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(d,s,h,c) {
				ng.count.produtos = d.total_produtos ;
			})
			.error(function(data, status, headers, config) {
				ng.count.produtos = 0;
			});
	};

	ng.loadCountClientes = function() {
		var vlrTotalVendasPeriodoComparativo = 0 ;
		aj.get(baseUrlApi()+"count_clientes/dashboard?id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(d,s,h,c) {
				ng.count.clientes = d.total_clientes ;
			})
			.error(function(data, status, headers, config) {
				ng.count.clientes = 0 ;
			});
	};

	ng.loadCountOrcamentos = function(first_date,last_date) {
		var vlrTotalVendasPeriodoComparativo = 0 ;
		aj.get(baseUrlApi()+"count_orcamentos/dashboard/"+first_date+"/"+last_date+"?id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(d,s,h,c) {
				ng.count.orcamentos = d.total_orcamentos  ;
			})
			.error(function(data, status, headers, config) {
				ng.count.orcamentos  = 0 ;
			});
	};

	ng.loadCountVendas = function(first_date,last_date) {
		var vlrTotalVendasPeriodoComparativo = 0 ;
		aj.get(baseUrlApi()+"count_vendas/dashboard/"+first_date+"/"+last_date+"?id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(d,s,h,c) {
				ng.count.vendas = d.total_vendas  ;
			})
			.error(function(data, status, headers, config) {
				ng.count.vendas  = 0 ;
			});
	};

	ng.loadConsolidadoEstoque = function() {
		aj.get(baseUrlApi()+"relatorio/estoque/consolidado?id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(d,s,h,c) {
				ng.estoqueDepositos = d;

				angular.forEach(d, function(item, i) {
					ng.total.vlrCustoTotalEstoque += item.vlr_custo_total;
				});

				ng.total.vlrCustoTotalEstoque = numberFormat(ng.total.vlrCustoTotalEstoque, 2, ',', '.');
			})
			.error(function(data, status, headers, config) {
				ng.estoqueDepositos = [];
			});
	};

	// TEST
	var first_date = formatDate("01/11/2014");
	var last_date = formatDate("30/11/2014");

	ng.loadTotalFaturamento(first_date, last_date);

	ng.loadTotalPagamentosConfirmados(first_date, last_date);
	ng.loadTotalPagamentosNaoConfirmados(first_date, last_date, 2); // Cheque
	ng.loadTotalPagamentosNaoConfirmados(first_date, last_date, 4); // Boleto Bancário
	ng.loadTotalPagamentosNaoConfirmados(first_date, last_date, 6); // Cartão de Crédito

	ng.loadSaldoDevedorFornecedor(first_date, last_date);
	ng.loadSaldoDevedorCliente(first_date, last_date);

	ng.loadComparativoVendas();

	ng.loadVendasTop10Categoria(first_date, last_date);
	ng.loadVendasTop10Fabricante(first_date, last_date);
	ng.loadVendasTop10Produto(first_date, last_date);
	ng.loadVendasTop10Cliente(first_date, last_date);

	ng.loadCountProdutos();
	ng.loadCountClientes();
	ng.loadCountOrcamentos(first_date, last_date);
	ng.loadCountVendas(first_date, last_date);

	ng.loadConsolidadoEstoque();
});

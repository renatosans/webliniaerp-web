app.controller('DashboardController', function($scope, $http, $window, UserService,ConfigService,FuncionalidadeService) {
	try {
		var ng = $scope,
			aj = $http;

		ng.userLogged = UserService.getUserLogado();
		ng.config     = ConfigService.getConfig(ng.userLogged.id_empreendimento);
		// Labels de Valores Totais
		ng.total = {
			vlrTotalFaturamento 				: 0,
			vlrSaldoDevedorFornecedores 		: 0,
			vlrSaldoDevedorClientes 			: 0,
			vlrCustoTotalEstoque 				: 0,
			vlrTotalPagamentosConfirmados 		: 0,
			vlrTotalPagamentosNaoConfirmados 	: {
				cheque 	: 0,
				boleto 	: 0,
				credito : 0
			}
		};

		ng.count = {
			produtos : null,
			clientes : null,
			vendas : null,
			orcamentos : null
		};

		ng.opcoes_filtro_pesquisa = [];
		ng.opcoes_filtro_pesquisa.push({value: 5, label: 'Últ. 5 dias'}); 		// 0
		ng.opcoes_filtro_pesquisa.push({value: 7, label: 'Últ. 7 dias'}); 		// 1
		ng.opcoes_filtro_pesquisa.push({value: 15, label: 'Últ. 15 dias'}); 	// 2
		ng.opcoes_filtro_pesquisa.push({value: 30, label: 'Últ. 30 dias'}); 	// 3
		ng.opcoes_filtro_pesquisa.push({value: 0, label: 'Mês atual'}); 		// 4

		ng.opcao_selecionada = ng.opcoes_filtro_pesquisa[4];

		ng.calcula_periodo_filtro = function(){
			if(ng.opcao_selecionada.value === 0) { // mês atual
				$("#dtaInicial").val(getFirstDateOfMonthString());
				$("#dtaFinal").val(getLastDateOfMonthString());

				ng.aplicarFiltro();
			}
			else {
				var hoje = moment();
				var fim = angular.copy(hoje);
				var inicio = hoje.subtract(ng.opcao_selecionada.value, 'day');

				$("#dtaInicial").val(inicio.format('DD/MM/YYYY'));
				$("#dtaFinal").val(fim.format('DD/MM/YYYY'));

				ng.aplicarFiltro();
			}
		}

		ng.funcioalidadeAuthorized = function(cod_funcionalidade){
    		return FuncionalidadeService.Authorized(cod_funcionalidade,ng.userLogged.id_perfil,ng.userLogged.id_empreendimento);
    	}

		// Gráfico de Acompanhamento de vendas Últimos 12 meses
		/*var init = {
			data: [
				[1, 5],
				[2, 8],
				[3, 5],
				[4, 8],
				[5, 7],
				[6, 9],
				[7, 8],
				[8, 8],
				[9, 10],
				[10, 12],
				[11, 10],
				[12, 7]
			],
			label: "Vendas"
		},
		options = {
			series: {
				lines: {
					show: true,
					fill: true,
					fillColor: 'rgba(55,180,148,0.2)'
				},
				points: {
					show: true,
					radius: '4.5'
				}
			},
			grid: {
				hoverable: true,
				clickable: true
			},
			colors: ["#9AD268"]
		},
		plot;

		plot = $.plot($('#placeholder'), [init], options);

		$("<div id='tooltip'></div>").css({
			position: "absolute",
			display: "none",
			border: "1px solid #222",
			padding: "4px",
			color: "#fff",
			"border-radius": "4px",
			"background-color": "rgb(0,0,0)",
			opacity: 0.90
		}).appendTo("body");

		$("#placeholder").bind("plothover", function (event, pos, item) {

			var str = "(" + pos.x.toFixed(2) + ", " + pos.y.toFixed(2) + ")";
			$("#hoverdata").text(str);

			if (item) {
				var x = item.datapoint[0],
					y = item.datapoint[1];

					$("#tooltip").html("Vendas : R$ " + y + " mil")
					.css({top: item.pageY+5, left: item.pageX+5})
					.fadeIn(200);
			} else {
				$("#tooltip").hide();
			}
		});

		$("#placeholder").bind("plotclick", function (event, pos, item) {
			if (item) {
				$("#clickdata").text(" - click point " + item.dataIndex + " in " + item.series.label);
				plot.highlight(item.series, item.datapoint);
			}
		});

		animate();

		function animate() {
		   $('#placeholder').animate( {tabIndex: 0}, {
			   duration: 3000,
			   step: function ( now, fx ) {

					 var r = $.map( init.data, function ( o ) {
						  return [[ o[0], o[1] * fx.pos ]];
					});

					 plot.setData( [{ data: r }] );
				 plot.draw();
				}
			});
		}*/

		ng.resetFilter = function() {
			$("#dtaInicial").val("");
			$("#dtaFinal").val("");
			ng.reset();
		}

		ng.aplicarFiltro = function() {
			ng.limparFiltros();
			var dtaInicial 	= $("#dtaInicial").val();
			var dtaFinal 	= $("#dtaFinal").val();

			if(dtaInicial == "") {
				$("#dtaInicialDiv").addClass("has-error");

				$("#dtaInicial")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", "Campo obrigatório!")
					.attr("data-original-title", "Campo obrigatório!");
				$("#dtaInicial").tooltip();

				return;
			}

			if(dtaFinal == "") {
				$("#dtaFinalDiv").addClass("has-error");

				$("#dtaFinalDiv")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", "Campo obrigatório!")
					.attr("data-original-title", "Campo obrigatório!");
				$("#dtaFinalDiv").tooltip();

				return;
			}

			var date_first = formatDate(dtaInicial);
			var date_last  = formatDate(dtaFinal);

			if(date_last<date_first){
				$("#dtaFinalDiv").addClass("has-error");

				$("#dtaFinalDiv")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", "Campo obrigatório!")
					.attr("data-original-title", "A segunda data deve ser maior que a primeira");
				$("#dtaFinalDiv").tooltip();

				return;
			}

			ng.loadTotalFaturamento(date_first, date_last);
			ng.totalCustoProdutosVendidos(date_first, date_last);
			ng.totalTaxaMaquinetas(date_first, date_last);
			ng.totalPagamentosFornecedores(date_first, date_last);

			ng.loadTotalPagamentosConfirmados(date_first, date_last);
			ng.loadTotalPagamentosNaoConfirmados(date_first, date_last, 2); // Cheque
			ng.loadTotalPagamentosNaoConfirmados(date_first, date_last, 4); // Boleto Bancário
			ng.loadTotalPagamentosNaoConfirmados(date_first, date_last, 6); // Cartão de Crédito

			ng.loadSaldoDevedorFornecedor(date_first, date_last);
			ng.loadVendasBycategoria(date_first, date_last);
			ng.loadCountOrcamentos();
			ng.loadCountVendas(date_first, date_last);
			ng.loadVendasTop10Clientes(date_first, date_last);
			ng.loadVendasTop10Fabricantes(date_first, date_last);
			ng.loadVendasTop10Produtos(date_first, date_last);
			ng.loadSaldoDevedorCliente();
		}

		ng.limparFiltros = function() {
			$("#dtaInicial").tooltip('destroy');
			$("#dtaFinal").tooltip('destroy');
			$(".has-error").removeClass("has-error");
		}

		//Carregando dados da API

		ng.loadTotalFaturamento = function(first_date, last_date) {
			ng.total.vlrTotalFaturamento = 'loading';
			aj.get(baseUrlApi()+"total_faturamento/dashboard/"+first_date+'/'+last_date+'?id_empreendimento='+ng.userLogged.id_empreendimento)
				.success(function(data, status, headers, config) {
					ng.total.vlrTotalFaturamento = data.total_faturamento;
					//$('#clientsSalesCount').text(ng.total.vlrTotalFaturamento);
				})
				.error(function(data, status, headers, config) {
					ng.total.vlrTotalFaturamento = 0 ;
				});
		}

		ng.totalCustoProdutosVendidos = function(first_date, last_date) {
			ng.total.vlr_custo_produto = 'loading';
			aj.get(baseUrlApi()+"total_custo_produtos_vendidos/dashboard/"+first_date+'/'+last_date+'?tv->id_empreendimento='+ng.userLogged.id_empreendimento)
				.success(function(data, status, headers, config) {
					ng.total.vlr_custo_produto = data.totalCustoProdutosVendidos;
					//$('#clientsSalesCount').text(ng.total.vlr_custo_produto);
				})
				.error(function(data, status, headers, config) {
					ng.total.vlr_custo_produto = 0 ;
				});
		}

		ng.totalTaxaMaquinetas = function(first_date, last_date) {
			ng.total.vlr_taxa_maquineta = 'loading';
			aj.get(baseUrlApi()+"total_taxa_maquinetas/dashboard/"+first_date+'/'+last_date+'?tpv->id_empreendimento='+ng.userLogged.id_empreendimento)
				.success(function(data, status, headers, config) {
					ng.total.vlr_taxa_maquineta = data.totalTaxaMaquinetas;
					//$('#clientsSalesCount').text(ng.total.vlr_taxa_maquineta);
				})
				.error(function(data, status, headers, config) {
					ng.total.vlr_taxa_maquineta = 0 ;
				});
		}

		ng.totalPagamentosFornecedores = function(first_date, last_date) {
			ng.total.vlr_pagamento_fornecedor = 'loading';
			aj.get(baseUrlApi()+"total_pagamentos_fornecedores/dashboard/"+first_date+'/'+last_date+'?tpf->id_empreendimento='+ng.userLogged.id_empreendimento)
				.success(function(data, status, headers, config) {
					ng.total.vlr_pagamento_fornecedor = data.totalPagamentosFornecedores;
					//$('#clientsSalesCount').text(ng.total.vlr_pagamento_fornecedor);
				})
				.error(function(data, status, headers, config) {
					ng.total.vlr_pagamento_fornecedor = 0 ;
				});
		}

		ng.loadTotalPagamentosConfirmados = function(first_date, last_date) {
			ng.total.vlrTotalPagamentosConfirmados = 'loading';
			aj.get(baseUrlApi()+"dashboard/total/pagamentos/confirmados/sim/"+first_date+'/'+last_date+'?tv->id_empreendimento='+ng.userLogged.id_empreendimento)
				.success(function(data, status, headers, config) {
					ng.total.vlrTotalPagamentosConfirmados = data.total_pagamentos_confirmados;
					//$('#clientsOkPaymentsCount').text(ng.total.vlrTotalPagamentosConfirmados);
				})
				.error(function(data, status, headers, config) {
					ng.total.vlrTotalPagamentosConfirmados = 0 ;
				});
		}

		ng.loadTotalPagamentosNaoConfirmados = function(first_date, last_date, id_forma_pagamento) {
			ng.total.vlrTotalPagamentosNaoConfirmados.cheque = 'loading';
			ng.total.vlrTotalPagamentosNaoConfirmados.boleto = 'loading';
			ng.total.vlrTotalPagamentosNaoConfirmados.credito = 'loading';
			aj.get(baseUrlApi()+"dashboard/total/pagamentos/confirmados/nao/"+first_date+'/'+last_date+'?tv->id_empreendimento='+ng.userLogged.id_empreendimento+'&id_forma_pagamento='+id_forma_pagamento)
				.success(function(data, status, headers, config) {
					switch(id_forma_pagamento) {
						case 2: { // cheque
							ng.total.vlrTotalPagamentosNaoConfirmados.cheque = parseFloat(data.total_pagamentos_nao_confirmados,10);
							//$('#clientsNonOkPaymentsChequeCount').text(numberFormat(ng.total.vlrTotalPagamentosNaoConfirmados.cheque, 2, ",", "."));
							break;
						}
						case 4: { // boleto bancário
							ng.total.vlrTotalPagamentosNaoConfirmados.boleto = parseFloat(data.total_pagamentos_nao_confirmados,10);
							//$('#clientsNonOkPaymentsBoletoCount').text(numberFormat(ng.total.vlrTotalPagamentosNaoConfirmados.boleto, 2, ",", "."));
							break;
						}
						case 6: { // cartão de crédito
							ng.total.vlrTotalPagamentosNaoConfirmados.credito = parseFloat(data.total_pagamentos_nao_confirmados,10);
							//$('#clientsNonOkPaymentsCreditoCount').text(numberFormat(ng.total.vlrTotalPagamentosNaoConfirmados.credito, 2, ",", "."));
							break;
						}
					}
				})
				.error(function(data, status, headers, config) {
					ng.total.vlrTotalPagamentosNaoConfirmados.cheque = 0;
					ng.total.vlrTotalPagamentosNaoConfirmados.boleto = 0;
					ng.total.vlrTotalPagamentosNaoConfirmados.credito = 0;
				});
		}

		ng.loadSaldoDevedorFornecedor = function(first_date, last_date) {
			aj.get(baseUrlApi()+"saldo_devedor_fornecedor/dashboard/"+first_date+'/'+last_date+'?id_empreendimento='+ng.userLogged.id_empreendimento)
				.success(function(data, status, headers, config) {
					ng.total.vlrSaldoDevedorFornecedores = numberFormat(data.saldo_devedor_fornecedores, 2, ",", ".");
					$('#suppliersSalesCount').text(ng.total.vlrSaldoDevedorFornecedores);
				})
				.error(function(data, status, headers, config) {
					ng.total.vlrSaldoDevedorFornecedores = 0;
				});
		}

		ng.loadSaldoDevedorCliente = function(){
			var queryString = "?(usu->id[exp]= NOT IN("+ng.config .id_cliente_movimentacao_caixa+","+ng.config.id_usuario_venda_vitrine+"))";
			queryString      += "&having=vlr_saldo_devedor<0";
			aj.get(baseUrlApi()+"usuarios/saldodevedor/"+ng.userLogged.id_empreendimento+"/null/true"+queryString)
				.success(function(data, status, headers, config) {
					data.vlr_saldo_devedor

					data.saldo_devedor_clientes = data.vlr_saldo_devedor  * (-1);
					ng.total.vlrSaldoDevedorClientes = numberFormat(data.vlr_saldo_devedor *(-1), 2, ",", ".");
					$('#negativeCount').text(ng.total.vlrSaldoDevedorClientes);

				})
				.error(function(data, status, headers, config) {
					ng.total.vlrSaldoDevedorClientes = 0;
				});
		}

		/*ng.loadSaldoDevedorCliente = function(first_date, last_date) {
			aj.get(baseUrlApi()+"saldo_devedor_cliente/dashboard/?id_empreendimento="+ng.userLogged.id_empreendimento+"&id_cliente[exp]=!="+ng.configuracao.id_cliente_movimentacao_caixa)
				.success(function(data, status, headers, config) {
					if(Number(data.saldo_devedor_clientes) < 0){
						data.saldo_devedor_clientes = data.saldo_devedor_clientes  * (-1);
						ng.total.vlrSaldoDevedorClientes = numberFormat(data.saldo_devedor_clientes, 2, ",", ".");
						$('#negativeCount').text(ng.total.vlrSaldoDevedorClientes);
					}else{
						$('#negativeCount').text('0,00');
					}
				})
				.error(function(data, status, headers, config) {
					ng.total.vlrSaldoDevedorClientes = 0;
				});
		}*/

		ng.loadComparativoVendas = function() {
			ng.vendasUltimos3Meses = [];
			var vlrTotalVendasPeriodoComparativo = 0 ;
			aj.get(baseUrlApi()+"comparativo_vendas/dashboard?id_empreendimento="+ng.userLogged.id_empreendimento)
				.success(function(data, status, headers, config) {
					$.each(data,function(i,item){
						ng.vendasUltimos3Meses.push(item);
						vlrTotalVendasPeriodoComparativo += item.total_vendas_confirmadas;
					});

					lineChart = Morris.Bar({
							element: 'lineChart',
							data: ng.vendasUltimos3Meses,
							xkey: 'mes_referencia',
							ykeys: ['total_orcamentos', 'total_vendas_confirmadas'],
							labels: ['Orçamentos', 'Vendas'],
							gridTextColor : '#fff',
							//dateFormat: formatDate,
							//xLabelFormat: formatDate
					});
					ng.vlrTotalVendasPeriodoComparativo = vlrTotalVendasPeriodoComparativo;

				})
				.error(function(data, status, headers, config) {

				});
		}

		ng.loadVendasBycategoria = function(first_date, last_date) {
			ng.vendasCategoria = [];
			aj.get(baseUrlApi()+"dashboard/vendas/top10/categoria?fd="+first_date+'&ld='+last_date+'&id_empreendimento='+ng.userLogged.id_empreendimento)
				.success(function(data, status, headers, config) {
					ng.vendasCategoriaTable = data;

					$.each(data,function(i,item){
						var insert = {
							id: item.id,
							label : item.nome_categoria,
							value : item.qtd_total_vendas
						};

						ng.vendasCategoria.push(insert);
					});
					var chart = Morris.Donut({
						element: "categoriasDonutChart", 
						data: ng.vendasCategoria,
						/*, colors:['#ffc545','#fe402b','#222222','#9AD268']*/
					});
					chart.on('click', function(i, row) {
						var dtaInicial 	= $("#dtaInicial").val();
						var dtaFinal 	= $("#dtaFinal").val();

						window.location.href = "rel-vendas-categoria.php?id_categoria="+ row.id +"&nme_categoria="+ row.label +"&dtaInicial="+ dtaInicial +"&dtaFinal="+ dtaFinal;
					});
				})
				.error(function(data, status, headers, config) {
					if(status == 404){
						// $.gritter.add({
						// 	title: '<i class="fa fa-exclamation-triangle"></i> Vendas por Categoria!',
						// 	text: data,
						// 	sticky: false,
						// 	time: 5000,
						// 	class_name: 'gritter-warning'
						// });
					}
				});
		}

		ng.loadVendasTop10Clientes = function(first_date, last_date) {
			ng.vendasClientes = [];
			aj.get(baseUrlApi()+"dashboard/vendas/top10/cliente?fd="+first_date+'&ld='+last_date+'&id_empreendimento='+ng.userLogged.id_empreendimento)
				.success(function(data, status, headers, config) {
					ng.vendasClientesTable = data;

					$.each(data,function(i,item){
						var insert = {
							label : item.nome,
							value : item.vlr_total_vendas
						};

						ng.vendasClientes.push(insert);
					});

					Morris.Donut({ element: "clientesDonutChart", data: ng.vendasClientes/*, colors:['#ffc545','#fe402b','#222222','#9AD268']*/ });
				})
				.error(function(data, status, headers, config) {
					if(status == 404){
						// $.gritter.add({
						// 	title: '<i class="fa fa-exclamation-triangle"></i> Top 10 Vendas por Cliente!',
						// 	text: data,
						// 	sticky: false,
						// 	time: 5000,
						// 	class_name: 'gritter-warning'
						// });
					}
				});
		}

		ng.loadVendasTop10Produtos = function(first_date, last_date) {
			ng.vendasProdutos = [];
			aj.get(baseUrlApi()+"dashboard/vendas/top10/produto?fd="+first_date+'&ld='+last_date+'&id_empreendimento='+ng.userLogged.id_empreendimento)
				.success(function(data, status, headers, config) {
					//console.log(data);

					ng.vendasProdutosTable = data;

					$.each(data,function(i,item){
						var insert = {
							label : item.nome,
							value : item.qtd_total_vendas
						};

						ng.vendasProdutos.push(insert);
					});

					Morris.Donut({ element: "produtosDonutChart", data: ng.vendasProdutos/*, colors:['#ffc545','#fe402b','#222222','#9AD268']*/ });
				})
				.error(function(data, status, headers, config) {
					if(status == 404){
						// $.gritter.add({
						// 	title: '<i class="fa fa-exclamation-triangle"></i> Top 10 Vendas por Cliente!',
						// 	text: data,
						// 	sticky: false,
						// 	time: 5000,
						// 	class_name: 'gritter-warning'
						// });
					}
				});
		}

		ng.loadVendasTop10Fabricantes = function(first_date, last_date) {
			ng.vendasFabricantes = [];
			aj.get(baseUrlApi()+"dashboard/vendas/top10/fabricante?fd="+first_date+'&ld='+last_date+'&id_empreendimento='+ng.userLogged.id_empreendimento)
				.success(function(data, status, headers, config) {
					ng.vendasFabricantesTable = data;

					$.each(data,function(i,item){
						var insert = {
							label : item.nome_fabricante,
							value : item.qtd_total_vendas
						};

						ng.vendasFabricantes.push(insert);
					});

					Morris.Donut({ element: "fabricantesDonutChart", data: ng.vendasFabricantes/*, colors:['#ffc545','#fe402b','#222222','#9AD268']*/ });
				})
				.error(function(data, status, headers, config) {
					if(status == 404){
						// $.gritter.add({
						// 	title: '<i class="fa fa-exclamation-triangle"></i> Top 10 Vendas por Fabricante!',
						// 	text: data,
						// 	sticky: false,
						// 	time: 5000,
						// 	class_name: 'gritter-warning'
						// });
					}
				});
		}

		ng.loadVendasTop10ProdutosEstoqueMinimo = function() {
			ng.vendasFabricantes = [];
			aj.get(baseUrlApi()+"dashboard/vendas/top10/produto/estoque_minimo?id_empreendimento="+ng.userLogged.id_empreendimento)
				.success(function(data, status, headers, config) {
					ng.produtosMinimoEstoque = data;
				})
				.error(function(data, status, headers, config) {
					if(status == 404){
						// $.gritter.add({
						// 	title: '<i class="fa fa-exclamation-triangle"></i> Top 10 Produtos Próximo Estoque Mínimo!',
						// 	text: data,
						// 	sticky: false,
						// 	time: 5000,
						// 	class_name: 'gritter-warning'
						// });
					}
				});
		}

		ng.loadCountProdutos = function() {
			var vlrTotalVendasPeriodoComparativo = 0 ;
			aj.get(baseUrlApi()+"count_produtos/dashboard?tpe->id_empreendimento="+ng.userLogged.id_empreendimento)
				.success(function(data, status, headers, config) {
					ng.count.produtos = data.total_produtos ;
				})
				.error(function(data, status, headers, config) {
					ng.count.produtos = 0;
				});
		}

		ng.loadCountClientes = function() {
			var vlrTotalVendasPeriodoComparativo = 0 ;
			aj.get(baseUrlApi()+"count_clientes/dashboard?tue->id_empreendimento="+ng.userLogged.id_empreendimento)
				.success(function(data, status, headers, config) {
					ng.count.clientes = data.total_clientes ;
				})
				.error(function(data, status, headers, config) {
					ng.count.clientes = 0 ;
				});
		}

		ng.loadCountOrcamentos = function() {
			var vlrTotalVendasPeriodoComparativo = 0 ;
			aj.get(baseUrlApi()+"count_orcamentos/dashboard?id_empreendimento="+ng.userLogged.id_empreendimento)
				.success(function(data, status, headers, config) {
					ng.count.orcamentos = data.total_orcamentos  ;
				})
				.error(function(data, status, headers, config) {
					ng.count.orcamentos  = 0 ;
				});
		}

		ng.loadCountVendas = function(first_date,last_date) {
			var vlrTotalVendasPeriodoComparativo = 0 ;
			aj.get(baseUrlApi()+"count_vendas/dashboard/"+first_date+"/"+last_date+"?id_empreendimento="+ng.userLogged.id_empreendimento)
				.success(function(data, status, headers, config) {
					ng.count.vendas = data.total_vendas  ;
				})
				.error(function(data, status, headers, config) {
					ng.count.vendas  = 0 ;
				});
		}

		ng.loadConsolidadoEstoque = function() {
			var queryString = "?id_empreendimento="+ng.userLogged.id_empreendimento;
			//queryString += !ng.funcioalidadeAuthorized('ver_estoque_todos_depositos') ? "&id_deposito="+ng.config.id_deposito_padrao  : "" ;
			aj.get(baseUrlApi()+"relatorio/estoque/consolidado"+queryString)
				.success(function(data, status, headers, config) {
					ng.estoqueDepositos = data;
				})
				.error(function(data, status, headers, config) {
					ng.estoqueDepositos = [];

					if(status == 404){
						// $.gritter.add({
						// 	title: '<i class="fa fa-exclamation-triangle"></i> Estoque Consolidado!',
						// 	text: data,
						// 	sticky: false,
						// 	time: 5000,
						// 	class_name: 'gritter-warning'
						// });
					}
				});
		}

		ng.loadCustoTotalEstoque = function() {
			var id_deposito = !ng.funcioalidadeAuthorized('ver_estoque_todos_depositos') ? '/'+ng.config.id_deposito_padrao : '' ;
			aj.get(baseUrlApi()+"custoTotalEstoque/dashboard/"+ng.userLogged.id_empreendimento+id_deposito)
				.success(function(data, status, headers, config) {
					ng.total.vlrCustoTotalEstoque = data.vlrCustoTotalEstoque;

				})
				.error(function(data, status, headers, config) {
					
				});
		}

		$("#dtaInicial").val(getFirstDateOfMonthString());
		$("#dtaFinal").val(getLastDateOfMonthString());

		ng.configuracao = null ;
		ng.loadConfig = function(){
			var error = 0 ;
			aj.get(baseUrlApi()+"configuracoes/"+ng.userLogged.id_empreendimento)
				.success(function(data, status, headers, config) {
					ng.configuracao = data ;
					ng.loadSaldoDevedorCliente();
				})
				.error(function(data, status, headers, config) {
					ng.loadSaldoDevedorCliente();
					if(status == 404){
						ng.configuracao = false ;
					}
				});
		}

		ng.aplicarFiltro();
		ng.loadComparativoVendas();
		ng.loadCountProdutos();
		ng.loadConfig();
		ng.loadConsolidadoEstoque();
		ng.loadVendasTop10ProdutosEstoqueMinimo();
		ng.loadCountClientes();
		ng.loadCustoTotalEstoque();

		var item = [{de: 0, ate: 2, qtd: 0}, {de: 3, ate: 5, qtd: 0}, {de: 6, ate: 10, qtd: 0}, {de: 11, ate: null, qtd: 0}];
		var num = 11 ;
		var aux = num ;
		var resto = 0 ;
		var ate_ant = 0 ;

		$.each(item,function(i,	v){
			resto = v.ate == null ? false : (v.ate - ate_ant) ;
			ate_ant = v.ate ;
			if(resto == false){
				item[i].qtd = num;
				num = 0 ;
			}
			else if(num >= resto ){
				item[i].qtd = resto;
				num = num -resto ;
			}else{
				item[i].qtd = num;
				num = 0 ;
			}

			if(num <= 0){
				return false ;
			}
		});


	}catch(err) {
    	alert(err.message);
	}
});

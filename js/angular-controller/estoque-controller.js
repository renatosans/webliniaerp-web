app.controller('EstoqueController', function($scope, $http, $window, $dialogs,$filter, UserService, ConfigService, PrestaShop){

	var ng = $scope
		aj = $http;

	ng.baseUrl 						= baseUrl();
	ng.userLogged 					= UserService.getUserLogado();
	ng.configuracao 				= ConfigService.getConfig(ng.userLogged.id_empreendimento);
	ng.nota 						= { vlr_total_imposto : '', xml_nfe: '', flg_cadastra_produto_nao_encontrado: 0, itens: [] };
	ng.ultimasEntradas 				= [];
	ng.editing 						= false;
	ng.paginacao 					= {};
	ng.busca 						= { fornecedores: "", pedidos: "" };
	ng.valor_total_entrada 			= 0;
	ng.qtd_total_entrada 			= 0;
	ng.produto 						= {};
	ng.vlr_frete 					= "";
	ng.pedidos 						= [];
    ng.novoPedido 					= [];
    ng.produtos 					= [];
    ng.paginacao_produtos 			= [];
    ng.paginacao_fornecedores 		= {};
    ng.paginacao_pedidos 			= {};
    ng.pesquisa 					= { produto: "", fornecedores: "" };
    ng.fornecedor 					= {};
    ng.nota.flg_alterar_valor_custo = 0;
    ng.busca_cod_barra 				= false;

	$("#arquivo-nota").change(function() {
		var filename = $(this).val().split('\\').pop();
		$(this).parent().find('span').attr('data-title',filename);
		$(this).parent().find('label').attr('data-title','Trocar XML');
		$(this).parent().find('label').addClass('selected');
	});

	ng.incluirDuplicata = function(item){
		ng.new_duplicata.xml = false;
		ng.new_duplicata.dta_vencimento = (!empty($("#vencimentoduplicatas").val())) ? formatDate($("#vencimentoduplicatas").val()) : null;
		if(empty(ng.nota.duplicatas))
			ng.nota.duplicatas = [];

		ng.nota.duplicatas.push(angular.copy(ng.new_duplicata));
		$("#list_duplicatas").modal("hide");
	}

	ng.deleteDuplicata = function(item) {
		ng.nota.duplicatas = _.without(ng.nota.duplicatas, item);
	}

	ng.resetModalDuplicada = function(){
		ng.new_duplicata.num_duplicata = "";
		ng.new_duplicata.dta_vencimento = "";
		ng.new_duplicata.vlr_duplicata = 0;
	}

	ng.reset = function() {
		ng.nota 				= {vlr_total_imposto : '', xml_nfe: '', flg_cadastra_produto_nao_encontrado: 0};
		ng.valor_total_entrada 	= 0 ;
		ng.qtd_total_entrada 	= 0 ;
		ng.produto 				= {};
		ng.vlr_frete 			= "";
	}

	ng.addItemNF = function(itemNF) {
		ng.nota.itens.push(angular.copy({
			id_pedido: 				(ng.nota.itens.length > 0) ? ng.nota.itens[0].id_pedido : 0,
			id_produto: 			itemNF.id_produto,
			margem_atacado: 		itemNF.margem_atacado,
			margem_intermediario: 	itemNF.margem_intermediario,
			margem_varejo: 			itemNF.margem_varejo,
			nome_fabricante: 		itemNF.nome_fabricante,
			nome_produto: 			itemNF.nome_produto,
			codigo_barra: 			itemNF.codigo_barra,
			peso: 					itemNF.peso,
			qtd: 					itemNF.qtd,
			validades: 				[{ qtd: itemNF.qtd }],
			custo: 					itemNF.custo,
			imposto: 				itemNF.imposto,
			flg_localizado: 		itemNF.flg_localizado
		}));
	}

	ng.loadDataFromXML = function() {
		ng.nota.itens = [] ; 
		$('#form-xml').ajaxForm({
		 	url: baseUrlApi()+"estoque/importar/nfe?id_empreendimento="+ng.userLogged.id_empreendimento,
		 	type: 'POST',
		 	beforeSend: function() {
		 		$("#loadXMLButton").button('loading');
		 		$("#xml_nfe").removeClass("has-error");
		 		$("#xml_nfe").tooltip('destroy');
		 	},
		 	success:function(data){
		 		$("#loadXMLButton").button('reset');

		 		ng.nota.num_nota_fiscal = data.nNF;

		 		if(data.fornecedor.flg_localizado) {
					ng.nota.id_fornecedor           	= data.fornecedor.dados.id;
					ng.nota.nme_fornecedor          	= data.fornecedor.dados.nome_fornecedor;
					ng.nota.flg_fornecedor_localizado 	= data.fornecedor.flg_localizado;
					ng.nota.id_pedido_fornecedor    	= "" ;
				}

				$.each(data.itensBD, function(i, itemNF){
					if(ng.nota.itens.length > 0 && itemNF.flg_localizado) {
						var newObj 				= _.findWhere(ng.nota.itens, {id_produto: itemNF.id_produto.toString()});
						
						if(newObj != undefined) {
							newObj.validades 		= [{ qtd: itemNF.qtd }];
							newObj.flg_localizado 	= true;
							newObj.qtd 				= itemNF.qtd;
							newObj.custo 			= itemNF.custo;
							newObj.imposto 			= itemNF.imposto;

							ng.nota.itens[0] = angular.copy(newObj);
						}
						else
							ng.addItemNF(itemNF);
					}
					else
						ng.addItemNF(itemNF);
				});

				ng.atualizaValores();
				ng.atualizaValorTotal();
				ng.atualizaQtdValidadeItens();

				angular.forEach(data.duplicatas, function(duplicata){
					duplicata.xml = true;
				});

				ng.nota.duplicatas = data.duplicatas;

				setTimeout(function() {
					$scope.$apply();
				}, 500);
		 	},
		 	error: function(data, status, headers, config){
		 		$("#loadXMLButton").button('reset');
		 		
		 		if(data.status == 406) {
		 			$("#xml_nfe").addClass("has-error");

					var formControl = $($("#xml_nfe"))
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", data.responseText)
						.attr("data-original-title", data.responseText);
					formControl.tooltip();
		 		}
		 		else {
		 			defaulErrorHandler(data, status, headers, config);
		 		}
		 	}
		}).submit();
		ng.loadPlanoContas();
	}

	ng.atualizaValoresDuplicatas = function(){
		ng.nota.vlr_total_duplicatas = 0;
		angular.forEach(ng.nota.duplicatas, function(duplicata, i){
			ng.nota.vlr_total_duplicatas += duplicata.vlr_duplicata;
		});
	}

	ng.salvar = function(){
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$($(".has-error").find("button")).tooltip('destroy');
		$(".has-error").removeClass("has-error");

		var btn = $('#btn-salvar-entrada');
			btn.button('loading');

		if(!ng.nota.itens.length > 0){
			$dialogs.notify('Atenção!', 'Nenhum item foi adicionado para dar entrada');
			return false;
		}

		$.each(ng.nota.itens, function(i, item) {
			if(!empty(ng.nota.xml_nfe) && !item.flg_localizado) {
				$dialogs.notify('Atenção!','Alguns produtos não foram localizados!<br/>Não será possível realizar a entrada da NF.');
				return false;
			}
		});

		if(ng.flg_alterar_valor_custo == 1 && !ng.validarCusto()){
			$dialogs.notify('Atenção!','Alguns produtos estão sem valor de custo!<br/>Não será possível realizar a entrada da NF.');
			return false;
		}

		var validar_validade = true ;
		var postPrestaShop = {produtos:[]};
		$.each(ng.nota.itens,function(i,item){
			if(item.validades == undefined){
				validar_validade = false;
				return;
			}
			postPrestaShop.produtos.push(item.id_produto);
		});

		if(!validar_validade){
			btn.button('reset');
			$dialogs.notify('Atenção!','A quantidade e data de validade de nenhum produto pode ser fazio');
			return;
		}

		var post_data = angular.copy(ng.nota);

		var itens = [];
		$.each(post_data.itens, function(i, item){
			$.each(item.validades, function(x, validade){
				var item_atual = {};
				
				$.each(item, function(a, val){
					if(a != '$$hashKey' && a != 'validades')
						item_atual[a] = val;
				});

				item_atual.desconto = (item_atual.desconto / 100);
				item_atual.imposto 	= (item_atual.imposto / 100);

				if(empty(validade.validade))
					validade.validade = "122099";

				var ano       = parseInt(validade.validade.substring(2,6));
				var mes       = parseInt(validade.validade.substring(0,2)) -1;
				var objDate   = new Date(ano, mes , 1);

				item_atual.dta_validade = ano +'-'+ (mes+1) +'-'+ ultimoDiaDoMes(objDate);
				item_atual.qtd = validade.qtd;
				itens.push(item_atual);
			});
		});

		post_data.id_empreendimento = ng.userLogged.id_empreendimento;
		post_data.itens             = itens;
		post_data.dta_entrada       = formatDate($("#pagamentoData").val());
		post_data.id_usuario		  = ng.userLogged.id;

		post_data.vlr_total_imposto 	= parseFloat((!empty(post_data.vlr_total_imposto)) ? post_data.vlr_total_imposto.replace(",", ".") : 0);
		post_data.vlr_frete 			= parseFloat((!empty(post_data.vlr_frete)) ? post_data.vlr_frete.replace(",", ".") : 0);

		$.each(post_data.itens,function(i,x){
			post_data.itens[i].imposto = (!empty(x.imposto) && $.isNumeric(x.imposto)) ? (Number(x.imposto) / 100) : 0;
		});	

		$http.post(baseUrlApi()+'estoque/entrada', {nota: JSON.stringify(post_data)})
			.success(function(data, status, headers, config) {
				btn.button('reset');
				ng.reset();
				ng.showBoxNovo();
				$('html,body').animate({scrollTop: 0},'slow');
				ng.mensagens('alert-success',
							'<strong>Entrada cadastrada com sucesso</strong>',
							'.alert-entrada-lista');
				ng.loadEntradas(0,20);
				PrestaShop.send('post',baseUrlApi()+"prestashop/estoque",postPrestaShop);
				// ng.showModalPrecos(); // Inativado temporariamente
			})
			.error(function(data, status) {
				btn.button('reset');
				if(status == 406) {
					$('html,body').animate({scrollTop: 0},'slow');
					$.each(data, function(i, item) {
						$("#"+i).addClass("has-error");

						var formControl = $($("#"+i))
							.attr("data-toggle", "tooltip")
							.attr("data-placement", "bottom")
							.attr("title", item)
							.attr("data-original-title", item);
						formControl.tooltip();
					});
				}else{
					alert('Desculpe, ocorreu um erro inesperado!');
				}
			});

			ng.precoProduto = [];
	}

	ng.showModalPrecos = function(){
		ng.precoProduto = [];

			$.each(ng.nota.itens, function(x, itemEntrada) {
				itemEntrada.margem_intermediario = parseFloat(itemEntrada.margem_intermediario) * 100;
				itemEntrada.margem_atacado       = parseFloat(itemEntrada.margem_atacado) * 100;
				itemEntrada.margem_varejo        = parseFloat(itemEntrada.margem_varejo) * 100;

				if(ng.precoProduto != null && ng.precoProduto.length > 0) {
					var b = true;

					$.each(ng.precoProduto, function(y, itemPreco){
						if(itemEntrada.id_produto === itemPreco.id_produto)
							b = false;
					});

					if(b)
						ng.precoProduto.push(itemEntrada);
				}
				else
					ng.precoProduto.push(itemEntrada);
			});

		$('#list_precos').modal('show');return;
	}

	ng.showDetalhes = function(item){
		ng.current_detalhes = item ;
		ng.loadItensEntrada(item.id,0,10);
		$('#list_detalhes').modal('show');
	}

	ng.loadItensEntrada = function(id_estoque_entrada,offset,limit,divLoadingAjax) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 20 : limit;

		if(divLoadingAjax == null) ng.detalhes = null;
		else
			$(divLoadingAjax).show();


		aj.get(baseUrlApi()+"estoque/entradas/itens/"+offset+"/"+limit+"?id_estoque_entrada="+id_estoque_entrada)
			.success(function(data, status, headers, config) {
				ng.detalhes    = data.itens ;
				$(divLoadingAjax).hide();
				$.each(data.paginacao,function(i,x){
					data.paginacao[i].id_estoque_entrada = id_estoque_entrada ;
				});
				ng.paginacao.detalhes            = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.detalhes = [];
			});
	 }

	ng.salvarPrecoProduto = function(item){
		var itens = [] ;
		$.each(ng.precoProduto, function(x, itemEntrada) {
			itens = [] ;
			itemEntrada = angular.copy(itemEntrada);
			itemEntrada.margem_intermediario        =  parseFloat(itemEntrada.margem_intermediario) / 100;
			itemEntrada.margem_atacado              =  parseFloat(itemEntrada.margem_atacado)       / 100;
			itemEntrada.margem_varejo               =  parseFloat(itemEntrada.margem_varejo)        / 100;
			itemEntrada.id_empreendimento           =  ng.userLogged.id_empreendimento ;

			itens.push(itemEntrada);
		});

		$($(".has-error")).tooltip('destroy');
		$(".has-error").removeClass("has-error");

		$http.post(baseUrlApi()+'produtos/preco',{precos:itens})
		.success(function(data, status, headers, config) {
			$('#list_precos').modal('hide');
			ng.mensagens('alert-success','<strong>Preços do produto atualizados com sucesso</strong>');
			ng.precoProduto = [] ;
		})
		.error(function(data, status) {
			if(status == 406) {

				$.each(data, function(i, item) {
					$("#"+i).addClass("has-error");
					var formControl = $($("#"+i))
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", item)
						.attr("data-original-title", item);
					formControl.tooltip();
				});
			}else{
				alert('Desculpe, ocorreu um erro inesperado!');
			}
		});
	}

	/* inicio - Ações de Fornecedores */

	ng.fornecedores = [] ;

	ng.showFornecedores = function(){
		$('#list_fornecedores').modal('show');
		ng.busca.fornecedores = "";
		ng.loadFornecedores(0,10);
	}

	ng.loadFornecedores = function(offset,limit) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 20 : limit;

		var query_string = "?frn->id_empreendimento="+ng.userLogged.id_empreendimento;
		if(ng.busca.fornecedores != ""){
			query_string += "&"+$.param({nome_fornecedor:{exp:"like'%"+ng.busca.fornecedores+"%'"}})+"";
		}

		ng.fornecedores = [];
		aj.get(baseUrlApi()+"fornecedores/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.fornecedores        = data.fornecedores ;
				ng.paginacao.fornecedores = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.fornecedores = [];
			});
	}

	ng.addFornecedor = function(item){
		ng.nota.nme_fornecedor          = item.nome_fornecedor;
		ng.nota.id_fornecedor           = item.id;
		ng.nota.id_pedido_fornecedor    = "" ;
		ng.valor_total_entrada          = 0 ;
		ng.qtd_total_entrada            = 0 ;
		$('#list_fornecedores').modal('hide');
	}

	ng.salvarFornecedor = function(){
		$("#list_fornecedores .has-error")
			.removeClass('has-error')
			.removeAttr("data-toggle")
			.removeAttr("data-placement")
			.removeAttr("title")
			.removeAttr("data-original-title");

		ng.new_fornecedor.empreendimentos = [{ id: ng.userLogged.id_empreendimento }];
		ng.new_fornecedor.id_empreendimento = ng.userLogged.id_empreendimento;
		var btn = $('#btn-salvar-fornecedor');
		btn.button('loading');
		
		var postData = angular.copy(ng.new_fornecedor);

		if(!empty(postData.dta_nacimento))
			postData.dta_nacimento = moment(postData.dta_nacimento, 'DD-MM-YYYY').format('YYYY-MM-DD');
		
		aj.post(baseUrlApi() + "fornecedor", postData)
			.success(function(data, status, headers, config) {
				ng.addFornecedor(data.dados);
				btn.button('reset');
				ng.new_fornecedor = {};
				ng.enableNewFormFornecedor = false;
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');

				if(status == 406) {
		 			var errors = data;
		 			var count = 0;

		 			$.each(errors, function(i, item) {
		 				$("#"+i).addClass("has-error");
		 				var formControl = $($("#"+i))
		 					.attr("data-toggle", "tooltip")
		 					.attr("data-placement", "bottom")
		 					.attr("title", item)
		 					.attr("data-original-title", item);
		 				formControl.tooltip('show');
		 				count ++ ;
		 			});

		 			if(count == 0)
		 				ng.mensagens('alert-warning','<strong>Informe ao menos o nome ou CPF do cliente</strong>','.alert-cadastro-rapido-error');
			 	} else
			 		ng.mensagens('alert-danger','<strong>Ocorreu um erro fatal</strong>','.alert-cadastro-rapido');
			});
	}

	/* end */

	/* inicio - Ações de pedidos */

	ng.pedidos = [] ;

	ng.showPedidos = function(){
		if(ng.nota.id_fornecedor == null){
			$dialogs.notify('Atenção!','Nenhum fornecedor foi adicionado');
			return ;
		}

		ng.busca.pedidos = "" ;
		$('#list_pedidos').modal('show');
		ng.loadPedidos(0,10);
	}

	ng.loadPedidos = function(offset,limit) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 20 : limit;
		var query_string = "?entrada->id_pedido_fornecedor[exp]=IS NULL&"+$.param({'frn->id':ng.nota.id_fornecedor});
		if(ng.busca.pedidos != ""){
			query_string += "&"+$.param({'pedido->id':ng.busca.pedidos});
		}

		ng.pedidos = [];
		aj.get(baseUrlApi()+"pedidos/"+offset+"/"+limit+"/"+query_string+"&tpf->id_empreendimento="+ng.userLogged.id_empreendimento+"&tpf->flg_pedido_real=1")
			.success(function(data, status, headers, config) {
				ng.pedidos        = data.pedidos ;
				ng.paginacao.pedidos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.pedidos          = [];
			})                                    ;
	}

	ng.addPedido = function(item){
		ng.nota.id_pedido_fornecedor = item.id;
		ng.loadItensPedido(item.id);
		$('#list_pedidos').modal('hide');
	}

	ng.validarDataValidade = function(mes_ano,index) {
		var mes = parseInt(mes_ano.substr(0,2),10);
		var ano = parseInt(mes_ano.substr(2,4),10);

		var valid = true;

		var dtaAtual = new Date();
		var mesAtual = dtaAtual.getMonth() + 1;
		var anoAtual = dtaAtual.getFullYear();

		if(mes > 12)
			valid = false;

		if(ano < anoAtual)
			valid = false;

		if(ano < 1900 || ano > 2100)
			valid = false;

		if(!valid) {
			ng.mensagens('alert-danger','<strong>A data informada é inválida</strong>','.alert-itens');
			ng.itemValidade.validade = "";
		}
	}

	ng.showValidades = function(item) {
		ng.produto = item;
		$("#list_validades").modal('show');
	}

	ng.addValidadeItem = function() {
		if(ng.produto.validades == null || typeof(ng.produto.validades) == "undefined")
			ng.produto.validades = [];
		
		ng.produto.validades.push(ng.itemValidade);
		ng.itemValidade = {};
		ng.atualizaQtdValidadeItens();
	}

	ng.atualizaQtdValidadeItens = function() {
		var qtdTotal = 0;

		if(ng.produto.validades == null || typeof(ng.produto.validades) == "undefined")
			ng.produto.validades = [];

		$.each(ng.produto.validades, function(i, item) {
			qtdTotal += parseFloat(item.qtd);
		});

		ng.produto.qtd = qtdTotal;
		ng.atualizaValores();
	}

	ng.deleteValidadeItem = function(index) {
		ng.produto.validades.splice(index,1);
		ng.atualizaQtdValidadeItens();
	}

	ng.loadItensPedido = function(id_pedido) {
		if(ng.nota.itens == null || ng.nota.itens == "undefined")
			ng.nota.itens = [];

		aj.get(baseUrlApi()+"pedido_itens/"+id_pedido)
			.success(function(data, status, headers, config) {
				$.each(data.itens,function(i,item){
					item.qtd 	  = 0;
					item.custo    = null;
					item.imposto  = null;
					item.desconto = null;
					item.total    = 0;

					if(ng.nota.itens.length > 0){
						var objNF = _.findWhere(ng.nota.itens, {id_produto: parseInt(item.id_produto, 10)});
						if(objNF == undefined)
							ng.nota.itens.push(item);
					}else
						ng.nota.itens.push(item);
				});

				ng.atualizaValores();
			})
			.error(function(data, status, headers, config) {
				ng.nota.itens = [];
			});
	}

	/* end */

	/* inicio - Ações de depositos */

	ng.depositos = [] ;

	ng.showDepositos = function(){
		ng.busca.depositos = "" ;
		ng.loadDepositos(0,10);
		$('#list_depositos').modal('show');
	}

	ng.loadDepositos = function(offset,limit) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 20 : limit;

		var query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento;

		if(ng.busca.depositos != ""){
			query_string += "&"+$.param({'nme_deposito':{exp:"like'%"+ng.busca.depositos+"%'"}});
		}

		ng.depositos = [];
		aj.get(baseUrlApi()+"depositos/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.depositos        = data.depositos ;
				ng.paginacao.depositos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.depositos = [];
			});
	}

	ng.addDeposito = function(item){
		ng.nota.id_deposito     = item.id;
		ng.nota.nme_deposito    = item.nme_deposito;
		$('#list_depositos').modal('hide');
	}

	/* end */

	/* inicio - Ações dos itens dos entrada */

	ng.deleteItem = function(item){
		if(item == null)
			ng.nota.itens = [];

		ng.nota.itens = _.without(ng.nota.itens, item);

		/*$.each(ng.nota.itens, function(x, currentItem){
			if(currentItem.id === item.id && currentItem.validade === item.validade)
				ng.nota.itens.splice(x,1);
		});*/

		if(!ng.nota.itens.length > 0)
			ng.nota.id_pedido_fornecedor= "";
		ng.atualizaValores();
	}

	ng.atualizaValorTotal = function() {
		var vlr_frete = (ng.nota.vlr_frete != null && ng.nota.vlr_frete != "") ? parseFloat(ng.nota.vlr_frete) : "";
		var vlr_frete = parseFloat(ng.nota.vlr_frete);

		if(isNaN(vlr_frete))
			vlr_frete = 0;

		ng.nota.vlr_total_nota_fiscal = vlr_frete + ng.valor_total_entrada;
	}

	ng.atualizaValores = function(){
		ng.nota.vlr_total_imposto = 0 ;
		ng.valor_total_entrada = 0 ;
		ng.qtd_total_entrada   = 0 ;

		$.each(ng.nota.itens, function(i,item){
			var qtd          = item.qtd      == null || item.qtd      == "" ? 0 : parseFloat(item.qtd);
			var custo        = item.custo    == null || item.custo    == "" ? 0 : parseFloat(item.custo);
			var por_desconto = item.desconto == null || item.desconto == "" ? 0 : parseFloat(item.desconto);
			var por_imposto  = item.imposto  == null || item.imposto  == "" ? 0 : parseFloat(item.imposto);

			
			var vl_imposto   = custo * (por_imposto  / 100) ;
			var vl_desconto  = (custo+vl_imposto) * (por_desconto / 100) ;

			var vl_total     = qtd * ((custo + vl_imposto) - vl_desconto);

			ng.nota.vlr_total_imposto += vl_imposto;
			ng.valor_total_entrada    += vl_total;
			ng.qtd_total_entrada      += qtd ;
			ng.nota.itens[i].total = vl_total ;

			ng.atualizaValorTotal();
		});

		ng.nota.vlr_total_imposto = numberFormat(ng.nota.vlr_total_imposto, 2, ',', '.');
	}

	ng.validarCusto = function(){
		var validade = true;
		$.each(ng.nota.itens,function(i,item){
			if(item.custo == null || item.custo == ""){
				validade = false;
			}
		});

		return validade
	}

	ng.busca = { nme_usuario: "", fornecedor: "", notafiscal: "",pedido: "" , dep_entrada: ""};
	ng.resetFilter = function() {
		$("#datarecebimento").val("");
		ng.busca.nme_usuario = "" ;
		ng.busca.fornecedor = "";
		ng.busca.notafiscal = "";
		ng.busca.pedido = "";
		ng.busca.dep_entrada = "";
		ng.reset();
		ng.loadEntradas(0,10);
	}

	ng.loadEntradas = function(offset,limit){
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 20 : limit;

		var query_string = "?1=1";

		if(ng.busca.nme_usuario != "")
			query_string += "&("+$.param({'tu->nome':{exp:"like '%"+ng.busca.nme_usuario+"%' "}})+")";

		if(ng.busca.fornecedor != "")
			query_string += "&("+$.param({'tf->nome_fornecedor':{exp:"like '%"+ng.busca.fornecedor+"%' "}})+")";

		if(ng.busca.notafiscal != "")
			query_string += "&("+$.param({num_nota_fiscal:{exp:"like '%"+ng.busca.notafiscal+"%' "}})+")";

		if(ng.busca.pedido != "")
			query_string += "&("+$.param({'ped_fornecedor->id':{exp:"like '%"+ng.busca.pedido+"%' "}})+")";

		if(ng.busca.dep_entrada != "")
			query_string += "&("+$.param({'dep->nme_deposito':{exp:"like '%"+ng.busca.dep_entrada+"%' "}})+")";

		if($("#datarecebimento").val() != ""){
			var data = moment($("#datarecebimento").val(), "DD/MM/YYYY").format("YYYY-MM-DD");
			query_string += "&("+$.param({'1':{exp:"= 1 AND cast(dta_entrada as date) = '"+ data +"' "}})+")";
		}

		ng.ultimasEntradas = [] ;

		aj.get(baseUrlApi()+"estoque/entradas/"+ng.userLogged.id_empreendimento+"/"+offset+"/"+limit+"/"+query_string)
		.success(function(data, status, headers, config) {
			ng.ultimasEntradas       = data.entradas ;
			ng.paginacao.entradas    = data.paginacao;
		})
		.error(function(data, status, headers, config) {
			ng.depositos = [];
		});
	}

	/* end */

	ng.clearChosenSelect = function(option, prefix){
		if(empty(ng.new_produto[ prefix +'_'+ option]))
			ng.new_produto[ prefix +'_'+ option] = null;
	}

	ng.cores 				= [{ id: '', nome_cor: '--- Selecione ---' }];
	ng.tamanhos 			= [{ id: '', nome_tamanho: '--- Selecione ---' }];
	ng.fabricantes 			= [{ id: "", nome_fabricante: "--- Selecione ---" }];
	ng.categorias 			= [{ id: "", descricao_categoria: "--- Selecione ---" }];
	ng.formas_aquisicao     = [{ cod_controle_item_nfe: null, nme_item : 'Selecione' }];
    ng.origens_mercadoria   = [{ cod_controle_item_nfe: null, nme_item : 'Selecione' }];
    ng.tipos_tributacao_ipi = [{ cod_controle_item_nfe: null, nme_item : 'Selecione' }];

	ng.loadCores = function() {
		ng.cores = [{ id: '', nome_cor: '--- Selecione ---' }];

		aj.get(baseUrlApi() +"cores_produto?tcpe->id_empreendimento="+ ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.cores = ng.cores.concat(data);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.cores = [];
			});
	}

	ng.loadTamanhos = function() {
		ng.tamanhos = [{ id: '', nome_tamanho: '--- Selecione ---' }];

		aj.get(baseUrlApi() +"tamanhos?tte->id_empreendimento="+ ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.tamanhos = ng.tamanhos.concat(data);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.tamanhos = [];
			});
	}

	ng.loadFabricantes = function() {
		ng.fabricantes = [{ id: "", nome_fabricante: "--- Selecione ---" }];

		aj.get(baseUrlApi()+"fabricantes?tfe->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.fabricantes = ng.fabricantes.concat(data.fabricantes);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.fabricantes = [];
			});
	}

	ng.loadCategorias = function(descricao_categoria) {
		ng.categorias = [{ id: "", descricao_categoria: "--- Selecione ---" }];

		aj.get(baseUrlApi()+"categorias?tce->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.categorias = ng.categorias.concat(data.categorias);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.categorias = [];
			});
	}

	ng.loadControleNfe = function(ctr,key) {
		aj.get(baseUrlApi()+"nfe/controles/null/"+ctr)
			.success(function(data, status, headers, config) {
				ng[key] = ng[key].concat(data);
			})
			.error(function(data, status, headers, config) {
				ng[key] = [];
			});
	}

	ng.salvarProduto = function(){
		$("#list_produtos .has-error")
			.removeClass('has-error')
			.removeAttr("data-toggle")
			.removeAttr("data-placement")
			.removeAttr("title")
			.removeAttr("data-original-title");

		ng.new_produto.empreendimentos = [{ id_empreendimento: ng.userLogged.id_empreendimento }];
		ng.new_produto.id_empreendimento = ng.userLogged.id_empreendimento;

		var btn = $('#btn-salvar-produto');
		btn.button('loading');
		
		var postData = angular.copy(ng.new_produto);
		
		aj.post(baseUrlApi() + "produto", postData)
			.success(function(data, status, headers, config) {
				if(!empty(ng.itemDePara)){
					ng.itemDePara.id_produto = data.id;
					ng.itemDePara.flg_localizado = true;
					ng.itemDePara.peso = (!empty(ng.new_produto.id_tamanho)) ? _.findWhere(ng.tamanhos, { id: ng.new_produto.id_tamanho }).nome_tamanho : null;
					ng.itemDePara.sabor = (!empty(ng.new_produto.id_cor)) ? _.findWhere(ng.cores, { id: ng.new_produto.id_cor }).nome_cor : null;
				}
				else {
					ng.addProduto({
						id_produto: 		data.id,
						nome_fabricante: 	(!empty(ng.new_produto.id_fabricante)) ? _.findWhere(ng.fabricantes, { id: ng.new_produto.id_fabricante }).nome_fabricante : null,
						nome_produto: 		ng.new_produto.nome,
						codigo_barra: 		ng.new_produto.codigo_barra,
						peso: 				(!empty(ng.new_produto.id_tamanho)) ? _.findWhere(ng.tamanhos, { id: ng.new_produto.id_tamanho }).nome_tamanho : null,
						sabor: 				(!empty(ng.new_produto.id_cor)) ? _.findWhere(ng.cores, { id: ng.new_produto.id_cor }).nome_cor : null,
						qtd: 				1,
						validades: 			[{ qtd: 1 }],
						custo: 				0,
						flg_localizado: 	true
					});
				}

				btn.button('reset');
				ng.new_produto = {};
				ng.enableNewFormProduto = false;
				$('#list_produtos').modal('hide');
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
				if(status == 406) {
		 			var errors = data;
		 			var count = 0;

		 			$.each(errors, function(i, item) {
		 				$("#"+i).addClass("has-error");
		 				var formControl = $($("#"+i))
		 					.attr("data-toggle", "tooltip")
		 					.attr("data-placement", "bottom")
		 					.attr("title", item)
		 					.attr("data-original-title", item);
		 				formControl.tooltip('show');
		 				count ++ ;
		 			});

		 			if(count == 0)
		 				ng.mensagens('alert-warning','<strong>Informe ao menos o nome ou CPF do cliente</strong>','.alert-cadastro-rapido-error');
			 	} else
			 		ng.mensagens('alert-danger','<strong>Ocorreu um erro fatal</strong>','.alert-cadastro-rapido');
			});
	}

	/* Modal Duplicatas*/

	ng.showModalDuplicatas = function(){
		$("#list_duplicatas").modal("show");
		ng.resetModalDuplicada();
	}

	/* Modal Produtos */

	// Funções para o modal de produtos
    var pesquisa_produto = ng.pesquisa.produto;

    ng.selProduto = function(item){
    	if(!empty(item)) {
    		ng.itemDePara = item;
    		ng.new_produto = {
    			codigo_barra: item.codigo_barra,
    			nome: item.nome_produto
    		};
    	}

    	ng.pesquisa.produto = "";
    	pesquisa_produto    = "";
    	ng.loadProdutos();
    	$("#list_produtos").modal("show");
    }

    ng.addProduto = function(item, autoAddQtd){
    	if( empty(ng.itemDePara) ) {
			autoAddQtd = (autoAddQtd == false) ? false : true;

			if(autoAddQtd && empty(item.qtd))
				item.qtd = 1;

			ng.produto = item;
			ng.nota.itens.push(item);

			if(autoAddQtd) {
				ng.itemValidade = { validade: '', qtd: item.qtd };
				ng.addValidadeItem();
				ng.atualizaTotal();
			}
		}
		else {
			ng.itemDePara.id_produto = angular.copy( item.id );
			ng.itemDePara.flg_localizado = true;
		}
    }

    ng.addFocus = function(){
   		ng.cod_barra_busca = '';
   		$('#focus').focus();
   		ng.busca_cod_barra = true ;
   	}

   	ng.blurBuscaCodBarra = function(){
   		ng.busca_cod_barra = false ;
   		$.noty.closeAll();
		var i = noty({
			timeout : 4000,
			layout: 'topRight',
			type: 'warning',
			theme: 'relax',
			text: 'Busca por codigo de barra desativada',
		});
   	}

   	ng.buscaCodBarra = function(){
   		if(empty(ng.cod_barra_busca)){
   			$.noty.closeAll();
			var i = noty({
				timeout : 4000,
				layout: 'topRight',
				type: 'warning',
				theme: 'relax',
				text: 'O codigo de barra não pode ser vazio',
			});
   			return ;
   		}
    	var query_string = "?tpe->id_empreendimento="+ng.userLogged.id_empreendimento;

    	if(ng.cod_barra_busca != "")
    		query_string += "&"+$.param({'codigo_barra':ng.cod_barra_busca});

		aj.get(baseUrlApi()+"produtos"+query_string)
			.success(function(data, status, headers, config) {
				if(data.produtos.length == 1){
					item = data.produtos[0];
					item.nome_produto = item.nome;
					ng.cod_barra_busca = '';
					ng.addProduto(item, false);
					ng.showValidades(item);
				}else{
					ng.pesquisa.produto = ng.cod_barra_busca ;
					ng.selProduto();
				}
			})
			.error(function(data, status, headers, config) {
				$.noty.closeAll();
				var i = noty({
					timeout : 4000,
					layout: 'topRight',
					type: 'error',
					theme: 'relax',
					text: 'Codigo de barra não corresponde a nenhum produto',
				});
			});	
   	}

	$scope.loadProdutos = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;

    	var query_string = "?tpe->id_empreendimento="+$scope.userLogged.id_empreendimento+"&pro->flg_excluido=0";

    	if($scope.pesquisa.produto != ""){
    		query_string += "&"+$.param({'(pro->nome':{exp:"like'%"+$scope.pesquisa.produto+"%' OR pro.codigo_barra like'%"+$scope.pesquisa.produto+"%' OR fab.nome_fabricante like'%"+$scope.pesquisa.produto+"%')"}});
    	}

		$scope.produtos = [];
		// $http.get(baseUrlApi()+"estoque_produtos/null/"+offset+"/"+limit+"/"+query_string+"&cplSql= ORDER BY tp.nome ASC")
		$http.get(baseUrlApi()+"produtos/"+ offset +"/"+ limit + query_string)
			.success(function(data, status, headers, config) {
				$.each(data.produtos, function(i, item) {
					item.nome_produto = item.nome;
					item.id_produto = parseInt(item.id_produto, 10);
					$scope.produtos.push(item);
				});
				$scope.paginacao.produtos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				$scope.produtos = [];
			});
	}

	ng.isProdutoSelected = function(produto) {
		return (!empty(_.findWhere(ng.nota.itens, {id_produto: produto.id_produto})));
	}

	ng.changeQtd = function(){
		ng.atualizaTotal();
	}

	ng.atualizaTotal = function(){
		var total =  0 ;
		$.each(ng.novoPedido,function(i,item){
			var qtd          = item.qtd == '' ? 1 : parseFloat(item.qtd);
			var custo_compra = parseFloat(item.custo_compra);
			
			total += qtd * custo_compra ;
		});
		ng.total = total;
	}

	/* Final Modal Produtos */

	/* inicio - funções gerais */

	ng.clearForm = function(){
		ng.nota.itens = [] ;
		$('#pagamentoData').val('');
		$.each(ng.nota,function(i,item){
				ng.nota[i] = "";
		});
	}

	ng.showBoxNovo = function(onlyShow){
		ng.editing = !ng.editing;

		if(onlyShow) {
			$('i','#btn-novo').removeClass("fa-plus-circle").addClass("fa-minus-circle");
			$('#box-novo').show();
		}
		else {
			$('#box-novo').toggle(400, function(){
				if($(this).is(':visible')){
					$('i','#btn-novo').removeClass("fa-plus-circle").addClass("fa-minus-circle");
				}else{
					ng.clearForm();
					$('i','#btn-novo').removeClass("fa-minus-circle").addClass("fa-plus-circle");
				}
			});
		}
	}

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	function formatDate(dta) {
		var arr_date = dta.split('/');
		var date= arr_date[2]+'-'+arr_date[1]+'-'+arr_date[0];

		return date;
	}

	ng.Keyup = function(event,valor){
		var val = String.fromCharCode(event.keyCode)

		if(event.keyCode >= 48 && event.keyCode <= 57 ){
			valor = valor.substring(0,valor.length-1) ;
		}
	}

	ng.loadPlanoContas = function() {
		ng.plano_contas = [{id:"",dsc_completa:"--- Selecione ---"}];
		aj.get(baseUrlApi()+"planocontas?tpc->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				$.each(data, function(i, item){
					data[i].cod_plano 			= (!empty(item.cod_plano)) ? parseInt(item.cod_plano, 10) : null;
					data[i].cod_plano_pai 		= (!empty(item.cod_plano_pai)) ? parseInt(item.cod_plano_pai, 10) : null;
					data[i].id 					= (!empty(item.id)) ? parseInt(item.id, 10) : null;
					data[i].id_empreendimento 	= (!empty(item.id_empreendimento)) ? parseInt(item.id_empreendimento, 10) : null;
					data[i].id_plano_pai 		= (!empty(item.id_plano_pai)) ? parseInt(item.id_plano_pai, 10) : null;
				});
				ng.roleList = data;
				ng.plano_contas = ng.plano_contas.concat(data);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.roleList = [];
			});
	}

	ng.loadContasBancarias = function() {
		ng.contas_bancarias = [];
		aj.get(baseUrlApi()+"contas_bancarias?id_empreendimento="+ng.userLogged.id_empreendimento+"&id_tipo_conta[exp]=<>5")
			.success(function(data, status, headers, config) {
				ng.contas_bancarias = data.contas;
			})
			.error(function(data, status, headers, config) {
				ng.contas_bancarias = null;
			});
	}

	ng.loadEntradas(0,10);
	ng.loadCores();
	ng.loadTamanhos();
	ng.loadFabricantes();
	ng.loadCategorias();
	ng.loadContasBancarias();
	ng.loadControleNfe('forma_aquisicao','formas_aquisicao');
	ng.loadControleNfe('origem_mercadoria','origens_mercadoria');
	ng.loadControleNfe('tipo_tributacao_ipi','tipos_tributacao_ipi');
});
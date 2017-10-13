app.directive('file', function(){
	return {
		scope: {
			file: '='
		},
		link: function(scope, el, attrs){
			el.bind('change', function(event){
				var files = event.target.files;
				var file = files[0];
				scope.file = file ? file.name : undefined;
				scope.$apply();
			});
		}
	};
});

app.controller('PedidoVendaController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
    ng.busca = {produtos:"",depositos:"",empreendimento:"",clientes:""};
    ng.editing = false;
    ng.paginacao = {};
    ng.cliente = {};
    ng.receber_pagamento = false;
    ng.modo_venda = 'pdv';
    //ng.escolher_tiras = false ;
    ng.tela = 'escolher_bases';
    ng.chinelosInfantis = {
							tamanhos:['23/24','31/32'],
							precos:{
								'1-100'  : 5.99,
								'101-149' : 4.99,
								'150'     : 4.00
							}

    					  };
    ng.chinelosAdultos  = {
    						tamanhos:['33/34'],
    						precos:{
								'1-49'  : 13.50,
								'50-100' : 6.99,
								'101-149': 6.50,
								'150'    : 5.99
							}
    					  };
    
    ng.showBoxNovo = function(onlyShow){
    	if(onlyShow) {
			$('i','#btn-novo').removeClass("fa-plus-circle").addClass("fa-minus-circle");
			$('#box-novo').show();
		}
		else {
			ng.reset();
			$('#box-novo').toggle(400, function(){
				if($(this).is(':visible')){
					$('i','#btn-novo').removeClass("fa-plus-circle").addClass("fa-minus-circle");
				}else{
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

	ng.reset = function() {
		ng.busca.produtos = '';
		ng.produto = {};
		ng.editing = false;
		ng.empreendimentosAssociados = [{ id_empreendimento : ng.userLogged.id_empreendimento, nome_empreendimento : ng.userLogged.nome_empreendimento }];
		valor_campo_extra = angular.copy(ng.valor_campo_extra);
		ng.produto.valor_campo_extra = valor_campo_extra ;
		ng.produto_normal = 1 ;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	ng.resetFilter = function() {
		ng.reset();
		ng.loadProdutos(0,10);
	}

	ng.editar = function(item) {
		ng.editing = true ;
		ng.showBoxNovo(true);
	}	

	ng.removeErrorEstoque = function(){
		$($(".painel-estoque").find('.has-error')).tooltip('destroy');
		$(".painel-estoque").find('.has-error').removeClass("has-error");
	}
	ng.addNovoInventario = function(){
		var error = 0 ;
		ng.removeErrorEstoque();
		if(empty(ng.inventario_novo.id_deposito)){
			error ++ ;
			$("#inventario_novo_deposito").addClass("has-error");
			var formControl = $('#inventario_novo_deposito')
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'Informe o deposito')
				.attr("data-original-title", 'Informe o deposito');
			formControl.tooltip();
		}else{
			var dta_validade = empty(ng.inventario_novo.dta_validade) ? '2099-12-31' : formatDate(uiDateFormat(ng.inventario_novo.dta_validade,'99/99/999')) ;
			if(ng.existsDateEstoque(dta_validade,ng.inventario_novo.id_deposito)){
				 error ++ ;
				$("#inventario_novo_validade").addClass("has-error");
				var formControl = $('#inventario_novo_validade')
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Já existe está data de validade para o deposito selecionado')
					.attr("data-original-title", 'Já existe está data de validade para o deposito selecionado');
				formControl.tooltip();
			}
		}
		if(empty(ng.inventario_novo.qtd_ivn)){
			error ++ ;
			if(!ng.existsDateEstoque(dta_validade,ng.inventario_novo.id_deposito)){
				$("#inventario_novo_qtd").addClass("has-error");
				var formControl = $('#inventario_novo_qtd')
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Informa quantidade desejada')
					.attr("data-original-title", 'Informa quantidade desejada');
				formControl.tooltip();
			}
		}

		if(error > 0)
			return false;

		var item = {
			id_deposito   : ng.inventario_novo.id_deposito,
			nme_deposito  : ng.inventario_novo.nome_deposito,
			nome_deposito : ng.inventario_novo.nome_deposito,
			qtd_item      : 0,
			dta_validade  : dta_validade,
			qtd_ivn       : ng.inventario_novo.qtd_ivn
		}

		ng.produto.estoque.push(item);
		ng.inventario_novo = [] ;
	}

	ng.base 			= null ;
	ng.tira 			= null ;
	ng.tira_piercing 	= null ;

	ng.loadCorpoProduto = function(){
		var error = 0 ;
		aj.get(baseUrlApi()+"pedido/produtos/?tpe->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.corpo_pedido  = data ;
				ng.base 		 = ng.corpo_pedido.base;
				ng.base.qtd_tamanho = Object.keys(ng.base.tamanhos).length ;
			})
			.error(function(data, status, headers, config) {
				if(status == 404){
					ng.corpo_pedido = false ;
				}
			});
	}

	ng.valorTotalBase = 0 ;
	ng.calcularValorBase = function(){
		var total = 0 ;
		$.each(ng.base.produtos,function(i,v){
			$.each(v.tamanhos,function(x,y){
				if(isNaN(Number(y.qtd)))
					var qtd = 0 ;
				else
					var qtd = Number(y.qtd) ;

				total += qtd*y.vlr_venda_varejo;
			});
		});
		ng.valorTotalBase = total ;

		//ng.vlrTotalCompra = ng.valorTotalBase + ng.valorTotalTiras ;

	}

	ng.valorTotalTiras = 0 ;
	ng.calcularValorTiras = function(){
		var vlr_tiras = 0 ;
		$.each(ng.base_selecionadas,function(i,v){
			$.each(v.tiras_acessorios,function(x,y){
				if(isNaN(Number(y.qtd)))
					var qtd = 0 ;
				else
					var qtd = Number(y.qtd) ;

				vlr_tiras += qtd*y.vlr_venda_varejo;
			});
		});
		ng.valorTotalTiras = vlr_tiras ;
		ng.vlrTotalCompra += ng.valorTotalTiras ;
		/*var total = 0 ;
		$.each(ng.tira.produtos,function(i,v){
			$.each(v.tamanhos,function(x,y){
				if(isNaN(Number(y.qtd)))
					var qtd = 0 ;
				else
					var qtd = Number(y.qtd) ;

				total += qtd*y.vlr_venda_varejo;
			});
		});
		ng.valorTotalTira = total ;
		ng.vlrTotalCompra = ng.valorTotalBase + ng.valorTotalTira + ng.valorTotalTiraPiercing ;*/
	}

	ng.selCliente = function(){
		var offset = 0  ;
    	var limit  =  10 ;;

			ng.loadCliente(offset,limit);
			$("#list_clientes").modal("show");
	}

	ng.addClienteAutoComplete = function(item){
		ng.addCliente(item);
		ng.clientes_auto_complete_visible = false ;
		ng.busca.cliente_outo_complete = "" ;
	}

	ng.addCliente = function(item){
		ng.cliente = item;
		$("#list_clientes").modal("hide");
		aj.get(baseUrlApi()+"usuarios/saldodevedor/"+ ng.userLogged.id_empreendimento+"?usu->id="+item.id)
			.success(function(data, status, headers, config) {
				ng.cliente.vlr_saldo_devedor = data.vlr_saldo_devedor;
			})
			.error(function(data, status, headers, config) {
				console.log('erro ao consultar saldo do cliente');
			});
	}
	ng.removeCliente = function(){
		ng.cliente = {id:''} ;
	}
	ng.loadCliente= function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.clientes = [];
		query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+"&usu->id[exp]= NOT IN("+ng.configuracoes.id_cliente_movimentacao_caixa+","+ng.configuracoes.id_usuario_venda_vitrine+"))";

		if(ng.busca.clientes != ""){
			query_string += "&"+$.param({'(usu->nome':{exp:"like'%"+ng.busca.clientes+"%' OR usu.apelido LIKE '%"+ng.busca.clientes+"%')"}});
		}

		aj.get(baseUrlApi()+"usuarios/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				$.each(data.usuarios,function(i,item){
					ng.clientes.push(item);
				});
				ng.paginacao_clientes = [];
				$.each(data.paginacao,function(i,item){
					ng.paginacao_clientes.push(item);
				});
			})
			.error(function(data, status, headers, config) {
				ng.clientes = false ;
			});
	}
	ng.configuracoes = {} ;
	ng.loadConfig = function(){
		var error = 0 ;
		aj.get(baseUrlApi()+"configuracoes/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.configuracoes = data ;
				ng.abrirCaixa();
			})
			.error(function(data, status, headers, config) {
				if(status == 404){
					ng.caixa_configurado = false ;
				}
			});
	}

	ng.validatePedido = function(){
		var error = 0 ;
		var empty_pedido = true ;
		$(".has-error").tooltip('destroy');
		$(".has-error").removeClass("has-error");

		if( typeof ng.cliente.id == "undefined" || empty(ng.cliente.id)){
			$("#id_cliente").addClass("has-error");
					var formControl = $("#id_cliente")
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "top")
						.attr("title", 'Informe o cliente')
						.attr("data-original-title", 'Informe o cliente');
					formControl.tooltip({ container: 'body'});
			return false ;
		}

		$.each(ng.base.produtos,function(id_cor,obj){
			$.each(obj.tamanhos,function(id_tamanho,prd){
				var  qtd_base = Number.isInteger(parseInt(prd.qtd)) ? Number(prd.qtd) : 0 ;
				if(qtd_base > 0 && empty_pedido)
					empty_pedido = false ;

				/*if( !(qtd_base % 2) == 0){
					$("#input-base-"+id_cor+"-"+id_tamanho).addClass("has-error");
					var formControl = $("#input-base-"+id_cor+"-"+id_tamanho)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "top")
						.attr("title", 'A quantidade deve ser multiplo de 2')
						.attr("data-original-title", 'A quantidade deve ser multiplo de 2');
					formControl.tooltip({ container: 'body'});
					error ++ ;
				}*/
			});
		});

		if(error > 0)
			return false ;

		var qtd_tamanho = [] ;
		$.each(ng.base.produtos,function(id_cor,obj){
			$.each(obj.tamanhos,function(id_tamanho,prd){
				/*var  qtd_base = Number.isInteger(parseInt(prd.qtd)) ? Number(prd.qtd) : 0 ;
					if(!(typeof ng.tira.produtos[id_cor] == "undefined" || typeof ng.tira.produtos[id_cor].tamanhos[id_tamanho] == "undefined")){
				 		var qtd_tira = ng.tira.produtos[id_cor].tamanhos[id_tamanho].qtd ;
				 		qtd_tira = Number.isInteger(parseInt(qtd_tira)) ? Number(qtd_tira) : 0 ;
				 	}else
				 		var qtd_tira = 0 ;
				 	if(!(typeof ng.tira_piercing.produtos[id_cor] == "undefined" || typeof ng.tira_piercing.produtos[id_cor].tamanhos[id_tamanho] == "undefined")){
						var qtd_tira_piercing = ng.tira_piercing.produtos[id_cor].tamanhos[id_tamanho].qtd ;
						qtd_tira_piercing = Number.isInteger(parseInt(qtd_tira_piercing)) ? Number(qtd_tira_piercing) : 0 ;
					}else
						var qtd_tira_piercing = 0;

					if(!(qtd_base == (qtd_tira+qtd_tira_piercing))){
						$("#input-base-"+id_cor+"-"+id_tamanho).addClass("has-error");
						var formControl = $("#input-base-"+id_cor+"-"+id_tamanho)
							.attr("data-toggle", "tooltip")
							.attr("data-placement", "top")
							.attr("title", 'A quantidade total de tiras deve corresponder ao quantidade de bases')
							.attr("data-original-title", 'A quantidade total de tiras deve corresponder ao quantidade de bases');
						formControl.tooltip({ container: 'body'});
						error ++ ;
					}*/
			});
		});

		if(error > 0)
			return false ;
		else{
			if(empty_pedido){
				$dialogs.notify('Atenção!','O pedido não pode ser vazio.');
				return false ;
			}else
				return true ;
		}
	}

    ng.getPrecoChinelo = function(tipo,qtd){
    	var qtd_i = 0 ;
    	var qtd_f = 0 ;
    	var qtd_arr = [] ;
    	var vlr_chinelo = false ;
    	if(tipo == 'infantil'){
    		$.each(ng.chinelosInfantis.precos,function(qtd_preco,vlr_preco){
    			qtd_arr = qtd_preco.split('-');
    			if(qtd_arr.length == 2){
    				qtd_i = Number(qtd_arr[0]);
    				qtd_f = Number(qtd_arr[1]);
    				if(qtd >= qtd_i && qtd <= qtd_f){
    					vlr_chinelo = Number(vlr_preco);
    					return;
    				}
    			}else{
    				qtd_i = Number(qtd_arr[0]);
    				if(qtd >= qtd_i){
    					vlr_chinelo = Number(vlr_preco);
    					return;
    				}
    			}  			
	    		if(vlr_chinelo != false)
	    			return ;
    		});	
    	}

    	if(tipo == 'adulto'){
    		$.each(ng.chinelosAdultos.precos,function(qtd_preco,vlr_preco){
    			qtd_arr = qtd_preco.split('-');
    			if(qtd_arr.length == 2){
    				qtd_i = Number(qtd_arr[0]);
    				qtd_f = Number(qtd_arr[1]);
    				if(qtd >= qtd_i && qtd <= qtd_f){
    					vlr_chinelo = Number(vlr_preco);
    					return;
    				}
    			}else{
    				qtd_i = Number(qtd_arr[0]);
    				if(qtd >= qtd_i){
    					vlr_chinelo = Number(vlr_preco);
    					return;
    				}
    			}  			
	    		if(vlr_chinelo != false)
	    			return ;
    		});	
    	}


    	return vlr_chinelo ;
    }

	ng.getTipoTamanhoChinelo = function(tamanho){
		var tamanhos_infantis = ng.chinelosInfantis.tamanhos ;
		var tamanhos_adultos  = ng.chinelosAdultos.tamanhos ;
		var ti = null ;
		var tf = null ;

		if(tamanhos_infantis.length == 2){
			ti = tamanhos_infantis[0];
			tf = tamanhos_infantis[1];
			if(tamanho >= ti && tamanho <= tf)
				return 'infantil';
		}
	

		if(tamanhos_adultos.length == 2){
			ti = tamanhos_adultos[0];
			tf = tamanhos_adultos[1];
			if(tamanho >= ti && tamanho <= tf)
				return 'adulto';
		}else{
			ti = tamanhos_adultos[0];
			if(tamanho >= ti)
				return 'adulto';
		}

	}
	ng.calcSubTotal = function(tipo){
		total = 0 ;
		$.each(ng.chinelos_gerados,function(i,chinelo){
			if(!empty(chinelo.valor_desconto_cal)){
				var desconto = Number(chinelo.valor_desconto_cal)/100;
				chinelo.valor_desconto = desconto ;
				chinelo.valor_real_item = roundNumber((chinelo.valor_init - (chinelo.valor_init * desconto)));
				chinelo.desconto_aplicado = 1 ;
			}else{
				chinelo.valor_real_item = chinelo.valor_init == undefined ? chinelo.valor_real_item : chinelo.valor_init ;
				chinelo.desconto_aplicado = 0 ;
				chinelo.valor_desconto    = 0 ;
			}
			
		});
	}
	ng.chinelos_gerados = [] ;
	ng.TelaDefinirPreco= function(){
		ng.tela = 'definir_valores';
		var chinelos_gerados = [];
		var total_chinelos   = {infantis:0,adultos:0,total:0};
		var total_custo      = 0 ;

		$.each(ng.base_selecionadas,function(i,base){
			total_custo += Number(base.qtd)*Number(base.vlr_custo_real);
			$.each(base.tiras_acessorios,function(x,item){
				total_custo += Number(item.qtd)*Number(item.vlr_custo_real);
				if(item.tipo_produto == 'tira'){
					var nome_chinelo = "Chinelo Personalizado Base "+base.nome_tamanho+" "+base.nome_cor+" Tira "+item.nome_cor; 
					var qtd_chinelo  = Number(item.qtd);
					var tipo = ng.getTipoTamanhoChinelo(base.nome_tamanho) ;
					var chinelo = {nome:nome_chinelo,qtd:qtd_chinelo,tipo:tipo} ;
					total_chinelos.total += qtd_chinelo ;
					if(tipo == 'adulto')
						total_chinelos.adultos += qtd_chinelo ;
					else
						total_chinelos.infantis += qtd_chinelo ;
					chinelos_gerados.push(chinelo);
				}
			});
		});

		var vlr_uni_infantil = ng.getPrecoChinelo('infantil',total_chinelos.infantis) ;
		var vlr_uni_adulto   = ng.getPrecoChinelo('adulto',total_chinelos.adultos) ;
		var vlr_custo_uni    = total_custo/total_chinelos.total ;

		$.each(chinelos_gerados,function(i,chinelo){
			chinelos_gerados[i].vlr_custo        =  vlr_custo_uni ;
			chinelos_gerados[i].valor_real_item  = chinelo.tipo == 'adulto' ? vlr_uni_adulto : vlr_uni_infantil ;
			chinelos_gerados[i].valor_init       = chinelos_gerados[i].valor_real_item ;
		});

		ng.chinelos_gerados = chinelos_gerados ;
		console.log(ng.chinelos_gerados);

	}

	ng.calTotalChinelos = function(){
		total = 0 ;
		$.each(ng.chinelos_gerados,function(i,chinelo){
			total += Number(chinelo.qtd) * Number(chinelo.valor_real_item) ;
		});
		ng.vlrTotalCompra = total ;
		return numberFormat(total,2,',','.');
	}

	ng.salvar = function(){
		var qtd_base  = 0 ;
		var qtd_tiras = 0 ;
		var error = 0 ;
		var produtos = [];
		var id_produto_base = null;
		$.each(ng.base_selecionadas,function(i,v){
			qtd_base = parseInt(v.qtd);
			qtd_tiras = 0 ;
			id_produto_base = v.id_produto;
			var item = {
				id_produto 				: v.id_produto,
				desconto_aplicado 		: 0,
				valor_desconto 			: 0,
				qtd 					: v.qtd,
				valor_real_item 		: v.vlr_venda_varejo,
				vlr_custo				: v.vlr_custo_real,
				perc_imposto_compra     : v.perc_imposto_compra,
				perc_desconto_compra    : v.perc_desconto_compra,
				perc_margem_aplicada    : v.perc_venda_varejo,
				tipo_produto            : 'base'
			}
			produtos.push(item);
			$.each(v.tiras_acessorios,function(x,y){
				qtd_tiras += parseInt(y.qtd);
				var item = {
					id_produto 				: y.id_produto,
					desconto_aplicado 		: 0,
					valor_desconto 			: 0,
					qtd 					: y.qtd,
					valor_real_item 		: y.vlr_venda_varejo,
					vlr_custo				: y.vlr_custo_real,
					perc_imposto_compra     : y.perc_imposto_compra,
					perc_desconto_compra    : y.perc_desconto_compra,
					perc_margem_aplicada    : y.perc_venda_varejo,
					tipo_produto            : y.tipo_produto,
					id_produto_base         : id_produto_base
				}
				produtos.push(item);
			});
			if(!(qtd_base == qtd_tiras ))
				error ++
		});

		if(error > 0){
			//$dialogs.notify('Atenção!','O pedido não pode ser vazio.');
			//return false ;
		}
		var btn = $('#btn-salvar');
		btn.button('loading');
		var venda = {
			id_usuario : ng.userLogged.id ,
			id_cliente : ng.cliente.id ,
			venda_confirmada : 0,
			id_empreendimento : ng.userLogged.id_empreendimento,
			id_status_pedido : 1,
			itens : [],
			chinelos_gerados : ng.chinelos_gerados 
		}
		var pagamentos   = [] ;
		/*$.each(ng.base.produtos,function(i,v){
			$.each(v.tamanhos,function(x,y){
				if(Number.isInteger(parseInt(y.qtd))){
					var item = {
						id_produto 				: y.id_produto,
						desconto_aplicado 		: 0,
						valor_desconto 			: 0,
						qtd 					: y.qtd,
						valor_real_item 		: y.vlr_venda_varejo,
						vlr_custo				: y.vlr_custo_real,
						perc_imposto_compra     : y.perc_imposto_compra,
						perc_desconto_compra    : y.perc_desconto_compra,
						perc_margem_aplicada    : y.perc_venda_varejo,
						tipo_produto            : 'base'
					}
					produtos.push(item);
				}
			});
		});*/

		/*$.each(ng.tira.produtos,function(i,v){
			$.each(v.tamanhos,function(x,y){
				if(Number.isInteger(parseInt(y.qtd))){
					var item = {
						id_produto 				: y.id_produto,
						desconto_aplicado 		: 0,
						valor_desconto 			: 0,
						qtd 					: y.qtd,
						valor_real_item 		: y.vlr_venda_varejo,
						vlr_custo				: y.vlr_custo_real,
						perc_imposto_compra     : y.perc_imposto_compra,
						perc_desconto_compra    : y.perc_desconto_compra,
						perc_margem_aplicada    : y.perc_venda_varejo,
						tipo_produto            : 'tira'
					}
					produtos.push(item);
				}
			});
		});*

		/*$.each(ng.tira_piercing.produtos,function(i,v){
			$.each(v.tamanhos,function(x,y){
				if(Number.isInteger(parseInt(y.qtd))){
					var item = {
						id_produto 				: y.id_produto,
						desconto_aplicado 		: 0,
						valor_desconto 			: 0,
						qtd 					: y.qtd,
						valor_real_item 		: y.vlr_venda_varejo,
						vlr_custo				: y.vlr_custo_real,
						perc_imposto_compra     : y.perc_imposto_compra,
						perc_desconto_compra    : y.perc_desconto_compra,
						perc_margem_aplicada    : y.perc_venda_varejo,
						tipo_produto            : 'tira_piercing'
					}
					produtos.push(item);
				}
			});
		});*/

		//pagamentos
		var Today        = new Date();
		var data_atual   = Today.getDate()+"/"+(Today.getMonth()+1)+"/"+Today.getFullYear();

		$.each(ng.recebidos, function(i,v){
			var parcelas = Number(v.parcelas);

			v.data_pagamento 			= formatDate(data_atual);
			v.id_abertura_caixa 		= ng.caixa_aberto.id ;
			v.id_plano_conta    		= ng.caixa.id_plano_caixa;
			v.id_tipo_movimentacao		= 3;
			v.id_cliente				= ng.cliente.id;
			v.id_forma_pagamento		= v.id_forma_pagamento;
			v.valor_pagamento			= v.valor;
			v.status_pagamento			= 1;
			v.id_empreendimento			= ng.userLogged.id_empreendimento;
			v.id_conta_bancaria       	= ng.caixa.id_caixa;
			v.id_cliente_lancamento		= ng.caixa.id_cliente_movimentacao_caixa;

			if(Number(v.id_forma_pagamento) == 6){

				var valor_parcelas 	 = v.valor/parcelas ;
				var next_date		 = somadias(data_atual,30);
				var itens_prc        = [] ;

				for(var count = 0 ; count < parcelas ; count ++){
					var item 			 = angular.copy(v);
					item.valor_pagamento = valor_parcelas ;
					item.data_pagamento  = formatDate(next_date) ;
					next_date			 = somadias(next_date,30);

					itens_prc.push(item);
				}

				pagamentos.push({id_forma_pagamento : v.id_forma_pagamento ,id_tipo_movimentacao: 3, parcelas:itens_prc});

			}else if(Number(v.id_forma_pagamento) == 2){
				$.each(ng.pg_cheques,function(i_cheque, v_cheque){
					v.id_banco 				= v_cheque.id_banco ;
					v.num_conta_corrente 	= v_cheque.num_conta_corrente ;
					v.num_cheque 			= v_cheque.num_cheque ;
					v.flg_cheque_predatado 	= v_cheque.flg_cheque_predatado ;
					v.data_pagamento 		= v_cheque.data_pagamento ;
					v.valor_pagamento 		= v_cheque.valor_pagamento ;
					v_push = angular.copy(v);
					pagamentos.push(v_push);
				});
			}else if(Number(v.id_forma_pagamento) == 4){
				$.each(ng.pg_boletos,function(i_boleto, v_boleto){
					v.id_banco 				= v_boleto.id_banco ;
					v.data_pagamento 		= v_boleto.data_pagamento ;
					v.valor_pagamento 		= v_boleto.valor_pagamento ;
					v.doc_boleto            = v_boleto.doc_boleto ;
					v.num_boleto            = v_boleto.num_boleto ;
					v.status_pagamento      = v_boleto.status_pagamento ;
					v_push = angular.copy(v);
					pagamentos.push(v_push);
				});
			}else{
				pagamentos.push(v);
			}
		});

		if(ng.troco > 0 && ng.modo_venda == 'pdv'){
			$.each(pagamentos,function(key,value){
				if(Number(value.id_forma_pagamento) == 3){
					pagamentos[key].valor           = pagamentos[key].valor_pagamento - ng.troco ;
					pagamentos[key].valor_pagamento = pagamentos[key].valor_pagamento - ng.troco ;
				}
			});
		}

		var vlr_restante = ng.vlrTotalCompra - ng.total_pg;

			if(vlr_restante > 0){
				item = {
				id_abertura_caixa		:ng.caixa_aberto.id,
				id_plano_conta   		:ng.caixa.id_plano_caixa,
				id_tipo_movimentacao 	: 5,
				valor 					:vlr_restante
				}

			pagamentos.push(item);

			}

		venda.itens = produtos;
		

		console.log({pedido_venda:venda,pagamentos:pagamentos});
		return ;
		
		aj.post(baseUrlApi()+"pedido_venda/gravar_pedido_venda",{pedido_venda:venda,pagamentos:pagamentos})
			.success(function(data, status, headers, config) {
				ng.tela = 'escolher_bases';
				//ng.escolher_tiras = false ;
				//ng.receber_pagamento = false ;
				ng.showBoxNovo();
				ng.loadVendas(0,10);
				ng.mensagens('alert-success','<b>Pedido realizado com sucesso</b>','.alert-listagem');
				btn.button('reset');
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
			});
	}

	ng.abrirCaixa = function(){
   		aj.get(baseUrlApi()+"pedido_venda/abrir_caixa/"+ng.configuracoes.id_caixa_pedidos_venda+"/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.caixa_aberto = data ;
				ng.caixa = data ;
			})
			.error(function(data, status, headers, config) {
				alert(data);
		});
	}

	ng.changeStatus = function(pedido,id_status,msg,event){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>'+msg+'</strong>');
		var button = $(event.target);
		if(!button.is(':button'))
			button = $(event.target).parent();
		
		dlg.result.then(function(btn){
			ng.pro_out_estoque = [];
			button.button('loading');
			aj.get(baseUrlApi()+"pedido_venda/change_status/"+pedido.id+"/"+id_status+( Number(id_status == 2) ? "/"+ng.caixa_aberto.id_deposito : '' ) )
				.success(function(data, status, headers, config) {
					button.button('reset');
					ng.mensagens('alert-success','<b>Pedido alterado com sucesso</b>','.alert-listagem');
					pedido.id_status_pedido = data.id ;
					pedido.dsc_status = data.dsc_status ;
				})
				.error(function(data, status, headers, config) {
					button.button('reset');
					 if(status == 406){
					 	ng.loadDetalhesPedido(pedido,null,null,true);
					 	ng.pro_out_estoque  = data.out_estoque ;
					 }else{
						button.button('reset');
					    ng.mensagens('alert-danger','<b>Ocorreu um erro ao alterar o pedido</b>','.alert-listagem');
					}				
			});
		}, undefined);
	}

	ng.finalizaPedido = function(index,event){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem Certeza que Deseja Finalizar este pedido?</strong>');

		dlg.result.then(function(btn){
			var btn = $(event.target);
			if(!btn.is(':button'))
				btn = $(event.target).parent();
			btn.button('loading');
			var caixa = { 
				id_abertura_caixa : ng.caixa_aberto.id,
				id_deposito       : ng.caixa_aberto.id_deposito,
				id_caixa          : ng.caixa_aberto.id_caixa
			}
			ng.pro_out_estoque = [];
			aj.post(baseUrlApi()+"pedido_venda/finalizar",{id_pedido_venda:ng.vendas[index].id,caixa:caixa})
				.success(function(data, status, headers, config) {
					ng.loadVendas(0,10);
					ng.mensagens('alert-success','<b>Pedido Finalizado com sucesso</b>','.alert-listagem');
					btn.button('reset');
				})
				.error(function(data, status, headers, config) {
					btn.button('reset');
					 if(status == 406){
					 	ng.loadDetalhesPedido(ng.vendas[index],null,null,true);
					 	ng.pro_out_estoque  = data.out_estoque ;
					 }else{
						alert(data);
						btn.button('reset');
					}
			});
		}, undefined);
	}

	ng.pro_out_estoque = [] ;
	ng.outEstoque = function(item){
		var exists = false ;
		$.each(ng.pro_out_estoque,function(i,x){
			if(Number(x[0]) == Number(item.id_produto)){
				exists = true;
				return;
			}
		});

		return exists ;
	}

	ng.loadVendas = function(offset,limit) {
		var query_string = "?tpv->id_empreendimento="+ ng.userLogged.id_empreendimento+"&tpv->flg_excluido=0&tpv->id_pedido_master[exp]=IS NULL";
		
		var date =  empty($("#dtaInicial").val()) ? null : formatDate($("#dtaInicial").val()) ;
		query_string += empty(date)                 ? "" : "&date_format(tpv->dta_venda,'%Y-%m-%d')="+date ;
		query_string += empty(ng.busca.ven_id_vendedor) ? "" : "&tpv->id_usuario="+ng.busca.ven_id_vendedor  ;
		query_string += empty(ng.busca.ven_id_cliente)     ? "" : "&tpv->id_cliente="+ng.busca.ven_id_cliente  ;



		aj.get(baseUrlApi()+"pedidos_venda/"+ offset +"/"+ limit + query_string)
			.success(function(data, status, headers, config) {
				ng.vendas 			= data.vendas;
				ng.paginacao.vendas = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				console.log(data);
				ng.vendas  = [] ;
				ng.paginacao.vendas = [] ;
			});
	}
	ng.empty = function(vlr){
		return empty(vlr);
	}
	ng.index_base = null ;
	ng.id_tamanho_c = null ;
	ng.addAcessorio = function(index,item){
		ng.index_base = index ;
		$('#list_acessorios').modal('show');
		ng.loadAcessorios(0,10);
	}

	ng.addTira = function(index,item){
		ng.index_base = index ;
		ng.id_tamanho_c = item.id_tamanho;
		$('#list_tiras').modal('show');
		ng.loadTiras(0,10);
	}
	ng.selTira = function(item){
		var tira = angular.copy(item);
		var total_tiras = 0 ;
		var total_base  = parseInt(ng.base_selecionadas[ng.index_base].qtd) ;
		var qtd_add     = isNaN(parseInt(tira.qtd)) ? 0 : parseInt(tira.qtd) ;
		var erro_exists = 0 ;
		$.each(ng.base_selecionadas[ng.index_base].tiras_acessorios,function(i,v){
			 var qtd_tira = isNaN(parseInt(v.qtd)) ? 0 : parseInt(v.qtd) ;
			 total_tiras += qtd_tira ;
			 if(Number(v.id_produto) == Number(tira.id_produto)){
			 	erro_exists ++;
			 	return false ;
			 }
		});

		if(erro_exists > 0){
			ng.mensagens('alert-warning','<strong>Este produto já foi selecionada</strong>','#alert-tiras');
		}else if((total_tiras+qtd_add)>total_base){
			ng.mensagens('alert-warning','<strong>A quantidade de tiras não pode ultrapassar a de bases</strong>','#alert-tiras');
		}/*else if( !((qtd_add % 2) == 0) ){
			ng.mensagens('alert-warning','<strong>A quantidade deve ser multiplo de 2 </strong>','#alert-tiras');
		}*/else{
			ng.base_selecionadas[ng.index_base].tiras_acessorios.push(tira);
			ng.calcularValorTiras();
		}
	}

	ng.DeltiraAcessorio = function(key,index){
		ng.base_selecionadas[key].tiras_acessorios.splice(index,1);
	}

	ng.selAcessorio = function(item){
		var tira = angular.copy(item);
		ng.base_selecionadas[ng.index_base].tiras_acessorios.push(tira);
		ng.calcularValorTiras();
	}

	ng.loadAcessorios = function(offset,limit) {
		ng.acessorios = [] ;
		offset = offset == null ? 0 : offset ; 
		limit  = limit  == null ? 10 : limit ; 

		var query_string = "?"+$.param({'(tcep->nome_campo':{exp:"IN ('flg_acessorio')  AND tvcep.valor_campo = '1'"}})+")";
		query_string += "&tpe->id_empreendimento="+ ng.userLogged.id_empreendimento;
		query_string += empty(ng.busca.acessorios)  ? "" : "&pro->nome[exp]= LIKE '%"+ng.busca.acessorios+"%'" ;

		aj.get(baseUrlApi()+"pedido/tiras/"+ offset +"/"+ limit + query_string)
			.success(function(data, status, headers, config) {
				ng.acessorios 			 = data.tiras ;
				ng.paginacao.acessorios  = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				console.log(data);
				ng.acessorios = null ;
			});
	}

	ng.loadTiras = function(offset,limit) {
		ng.tiras = [] ;
		offset = offset == null ? 0 : offset ; 
		limit  = limit  == null ? 10 : limit ; 
		//(tcep.nome_campo IN ('flg_tira','flg_acessorio')  AND tvcep.valor_campo = '1')

		var query_string = "?"+$.param({'(tcep->nome_campo':{exp:"IN ('flg_tira')  AND tvcep.valor_campo = '1'"}})+")";
		query_string += "&tpe->id_empreendimento="+ ng.userLogged.id_empreendimento+"&tt->id="+ng.id_tamanho_c;
		query_string += empty(ng.busca.tiras)  ? "" : "&pro->nome[exp]= LIKE '%"+ng.busca.tiras+"%'" ;

		aj.get(baseUrlApi()+"pedido/tiras/"+ offset +"/"+ limit + query_string)
			.success(function(data, status, headers, config) {
				ng.tiras 			= data.tiras;
				ng.paginacao.tiras  = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				console.log(data);
				ng.tiras = null ;
			});
	}
	ng.itens = [] ;
	ng.loadDetalhesPedido = function(item,offset,limit,pro_out_estoque){
		if(pro_out_estoque != true){
			ng.pro_out_estoque = [];
		}
		ng.pedido = item ;
		$('#list_detalhes').modal('show');
		aj.get(baseUrlApi()+"pedidos_venda/itens/"+item.id )
			.success(function(data, status, headers, config) {
				ng.detalhes 			= data;
				//ng.paginacao.detalhes   = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.detalhes 		  = [];
				//ng.paginacao.detalhes = [];
			});
	}
	ng.base_selecionadas = [] ;
	ng.telaTiras = function(vlr){
		$(".has-error").tooltip('destroy');
		$(".has-error").removeClass("has-error");
		if( typeof ng.cliente.id == "undefined" || empty(ng.cliente.id)){
			$("#id_cliente").addClass("has-error");
					var formControl = $("#id_cliente")
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "top")
						.attr("title", 'Informe o cliente')
						.attr("data-original-title", 'Informe o cliente');
					formControl.tooltip({ container: 'body'});
			$('input',$('.has-error').eq(0)).focus();
			return false ;
		}
		var empty_pedido = true ;
		$.each(ng.base.produtos,function(id_cor,obj){
			$.each(obj.tamanhos,function(id_tamanho,prd){
				var  qtd_base = Number.isInteger(parseInt(prd.qtd)) ? Number(prd.qtd) : 0 ;
				if(qtd_base > 0 && empty_pedido)
					empty_pedido = false ;
			});
		});

		if(empty_pedido){
			$dialogs.notify('Atenção!','Nenhuma base selecionada');
			return false ;
		}

		var error = 0 ;
		$.each(ng.base.produtos,function(id_cor,obj){
			$.each(obj.tamanhos,function(id_tamanho,prd){
				var  qtd_base = Number.isInteger(parseInt(prd.qtd)) ? Number(prd.qtd) : 0 ;
				if(qtd_base > 0 && empty_pedido)
					empty_pedido = false ;

				/*if( !(qtd_base % 2) == 0){
					$("#input-base-"+id_cor+"-"+id_tamanho).addClass("has-error");
					var formControl = $("#input-base-"+id_cor+"-"+id_tamanho)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "top")
						.attr("title", 'A quantidade deve ser multiplo de 2')
						.attr("data-original-title", 'A quantidade deve ser multiplo de 2');
					formControl.tooltip({ container: 'body'});
					error ++ ;
				}*/
			});
		});

		if(error > 0)
			return false;

		ng.escolher_tiras=vlr;
		ng.tela = vlr ? 'escolher_tiras' : 'escolher_bases' ;
		if(vlr){
			ng.base_selecionadas = [] ;
			$.each(ng.base.produtos,function(i,v){
				$.each(v.tamanhos,function(y,x){
					var qtd = isNaN(parseInt(x.qtd)) ? 0 : parseInt(x.qtd) ;
					if(qtd > 0){
						x.tiras = [] ;
						ng.base_selecionadas.push(x);
					}
				});
			});
			console.log(ng.base_selecionadas);
		}
	}
	// Funções de pagamento 
	ng.total_pg             = 0 ;
	ng.troco				= 0;
	ng.pagamentos          = [];
	ng.vlrTotalCompra = 0;
	ng.formas_pagamento = [
		{nome:"Dinheiro",id:3},
		{nome:"Cheque",id:2},
		{nome:"Boleto Bancário",id:4},
		{nome:"Cartão de Débito",id:5},
		{nome:"Cartão de Crédito",id:6},
		{nome:"Transferência",id:8}
	  ];
	ng.cheques					=[{id_banco:null,valor:0,num_conta_corrente:null,num_cheque:null,flg_cheque_predatado:0}];
	ng.boletos					= [{id_banco:null,num_conta_corrente:null,num_cheque:null,status_pagamento:0}];



	  ng.loadContas = function() {
		aj.get(baseUrlApi()+"contas_bancarias?cnt->id_tipo_conta[exp]=!=5&id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.contas = data.contas;
			})
			.error(function(data, status, headers, config) {
				ng.contas = [] ;
			});
	}

	ng.telaPagamento = function () {
			ng.tela = 'receber_pagamento';
			//ng.receber_pagamento = true ;
			$('html,body').animate({scrollTop: 0},100);
	}

	ng.receberPagamento = function(){
		var produtos = angular.copy(ng.carrinho);
		var venda    = {
							id_usuario:ng.userLogged.id,
							id_cliente:parseInt(ng.cliente.id),
							venda_confirmada:1,
							id_empreendimento:ng.userLogged.id_empreendimento,
							id_deposito : ng.caixa.id_deposito
						};

		venda.id_cliente = isNaN(venda.id_cliente) ? "" : venda.id_cliente;

		$.each(produtos,function(index,value){
			produtos[index].venda_confirmada 	= 1 ;
			produtos[index].valor_produto 		= value.vlr_unitario;
			produtos[index].qtd           		= value.qtd_total;

			if(value.flg_desconto != null && Number(value.valor_desconto) > 0 && !isNaN(Number(value.valor_desconto))){
				produtos[index].desconto_aplicado	= parseInt(value.flg_desconto) != 1 && isNaN(parseInt(value.flg_desconto)) ? 0 : 1 ;
				produtos[index].valor_desconto      = parseInt(value.flg_desconto) == 1 ? value.valor_desconto/100 : 0 ;
			} else {
				produtos[index].desconto_aplicado	= 0 ;
				produtos[index].valor_desconto      = 0 ;
			}
		});

		/*
		* agrupando os produtos de 10 em 10
		*/

		var index_current 	  = 0  ;
		var n_repeat 	  	  = 10 ;
		var repeat_count      = 0  ;
		var produtos_enviar   = [] ;


		$.each(produtos,function(index,obj){
			if(repeat_count >= n_repeat){
					index_current ++ ;
					repeat_count = 0 ;
			}

			if(!(produtos_enviar[index_current] instanceof Array)){
				produtos_enviar[index_current] = [];
			}

			produtos_enviar[index_current].push(obj);
			repeat_count ++ ;
		});
		ng.out_produtos = [] ;
		ng.out_descontos = [] ;
		ng.verificaEstoque(produtos_enviar,0,'receber');
	}

	ng.receber = function(){
		if(!ng.vlrTotalCompra > 0){
			$dialogs.notify('Atenção!','<strong>Não há nenhum valor à receber</strong>');
			return;
		}
		$('#modal-receber').modal('show');
	}

	ng.recebidos = [] ;

	ng.totalPagamento = function(){
		var total = 0 ;
		$.each(ng.recebidos,function(i,v){
			total += Number(v.valor);
		});
		ng.total_pg = Math.round( total * 100) /100 ;
	}

	ng.calculaTroco = function(){
		var troco = 0 ;
		troco = ng.total_pg - ng.vlrTotalCompra;
		if(troco > 0)
			ng.troco = troco;
		else
			ng.troco = 0 ;
	}

	ng.pagamento = {};
	ng.pg_cheques = [] ;
	ng.aplicarRecebimento = function(){
		var restante  = Math.round((ng.vlrTotalCompra - ng.total_pg) * 100) /100 ;
		if((ng.pagamento.valor > restante) && (ng.pagamento.id_forma_pagamento != 3) && (ng.modo_venda == 'pdv')){
			ng.mensagens('alert-warning','<strong>o valor do pagamento utrapassa o valor restante à receber</strong>','.alert-pagamento');
			return;
		}

		if(ng.pagamento.id_forma_pagamento == 7 && ng.pagamento.valor > restante && (ng.modo_venda == 'pdv')){ 
			ng.mensagens('alert-warning','<strong>o valor do pagamento utrapassa o valor restante à receber</strong>','.alert-pagamento');
			return;
		}

		var error = 0 ;
		$('.has-error').tooltip('destroy');
		$('.has-error').removeClass('has-error');
		if(ng.pagamento.id_forma_pagamento ==  undefined || ng.pagamento.id_forma_pagamento ==  ''){
			error ++ ;
			$("#pagamento_forma_pagamento").addClass("has-error");

			var formControl = $("#pagamento_forma_pagamento")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'A escolha da forma de pagamento é obrigatória')
				.attr("data-original-title", 'A escolha da forma de chequ é obrigatória');
			formControl.tooltip();
		}
		console.log(ng.pagamento);
		if(ng.pagamento.valor ==  undefined || ng.pagamento.valor ==  ''){
			error ++ ;
			$("#pagamento_valor").addClass("has-error");

			var formControl = $("#pagamento_valor")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'O valor é obrigatório')
				.attr("data-original-title", 'O valor é obrigatório');
			formControl.tooltip();
		}

		if((ng.pagamento.id_maquineta ==  undefined || ng.pagamento.id_maquineta ==  '') && (ng.pagamento.id_forma_pagamento == 5 || ng.pagamento.id_forma_pagamento == 6 ) ){
			error ++ ;
			$("#pagamento_maquineta").addClass("has-error");

			var formControl = $("#pagamento_maquineta")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'O escolha da maquineta é obrigatório')
				.attr("data-original-title", 'O escolha da maquineta é obrigatório');
			formControl.tooltip();
		}

		if(ng.pagamento.id_forma_pagamento == 2){
			$.each(ng.cheques, function(i,v){
				if($('.cheque_data input').eq(i).val() == "" || $('.cheque_data input').eq(i).val() == undefined ){
					$('.cheque_data').eq(i).addClass("has-error");

					var formControl = $('.cheque_data').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'A data do cheque é obrigatória')
						.attr("data-original-title", 'A data do cheque é obrigatória');
					formControl.tooltip();
					error ++ ;
				}

				if(v.valor_pagamento == "" || v.valor_pagamento == 0 || v.valor_pagamento == undefined ){
					$('.cheque_valor').eq(i).addClass("has-error");

					var formControl = $('.cheque_valor').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O valor do cheque é obrigatório')
						.attr("data-original-title", 'O valor do cheque é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.id_banco == "" || v.id_banco == 0 || v.id_banco == undefined ){
					$('.cheque_banco').eq(i).addClass("has-error");

					var formControl = $('.cheque_banco').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O banco é obrigatório')
						.attr("data-original-title", 'O banco é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.num_conta_corrente == "" || v.num_conta_corrente == 0 || v.num_conta_corrente == undefined ){
					$('.cheque_cc').eq(i).addClass("has-error");

					var formControl = $('.cheque_cc').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O número da C/C é obrigatório')
						.attr("data-original-title", 'O Num. C/C é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.num_cheque == "" || v.num_cheque == 0 || v.num_cheque == undefined ){
					$('.cheque_num').eq(i).addClass("has-error");

					var formControl = $('.cheque_num').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O Núm. Cheque é obrigatório')
						.attr("data-original-title", 'O Núm. Cheque é obrigatório');
					formControl.tooltip();
					error ++ ;
				}
			});

			//ng.calTotalCheque();
		}else if(ng.pagamento.id_forma_pagamento == 4){
			$.each(ng.boletos, function(i,v){
				if($('.boleto_data input').eq(i).val() == "" || $('.boleto_data input').eq(i).val() == undefined ){
					$('.boleto_data').eq(i).addClass("has-error");

					var formControl = $('.boleto_data').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'A data do boleto é obrigatória')
						.attr("data-original-title", 'A data do boleto é obrigatória');
					formControl.tooltip();
					error ++ ;
				}

				if(v.valor_pagamento == "" || v.valor_pagamento == 0 || v.valor_pagamento == undefined ){
					$('.boleto_valor').eq(i).addClass("has-error");

					var formControl = $('.boleto_valor').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O valor do boleto é obrigatório')
						.attr("data-original-title", 'O valor do boleto é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.id_banco == "" || v.id_banco == 0 || v.id_banco == undefined ){
					$('.boleto_banco').eq(i).addClass("has-error");

					var formControl = $('.boleto_banco').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O banco é obrigatório')
						.attr("data-original-title", 'O banco é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.doc_boleto == "" || v.doc_boleto == 0 || v.doc_boleto == undefined ){
					$('.boleto_doc').eq(i).addClass("has-error");

					var formControl = $('.boleto_doc').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O documento do boleto é obrigatório')
						.attr("data-original-title", 'O documento do boleto é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.num_boleto == "" || v.num_boleto == 0 || v.num_boleto == undefined ){
					$('.boleto_num').eq(i).addClass("has-error");

					var formControl = $('.boleto_num').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O Núm. Cheque é obrigatório')
						.attr("data-original-title", 'O Núm. Cheque é obrigatório');
					formControl.tooltip();
					error ++ ;
				}
			});

			//ng.calTotalCheque();
		}else if(ng.pagamento.id_forma_pagamento == 8){
			if(empty(ng.pagamento.id_banco)){
				$("#pagamento_id_banco").addClass("has-error");
				var formControl = $("#pagamento_id_banco")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Selecione o banco')
					.attr("data-original-title", 'Selecione o banco');
				formControl.tooltip();
			}
			if(empty(ng.pagamento.agencia_transferencia)){
				$("#pagamento_agencia_transferencia").addClass("has-error");
				var formControl = $("#pagamento_agencia_transferencia")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Informe o número da agência')
					.attr("data-original-title", 'Informe o número da agência');
				formControl.tooltip();
			}
			if(empty(ng.pagamento.conta_transferencia)){
				$("#pagamento_conta_transferencia").addClass("has-error");
				var formControl = $("#pagamento_conta_transferencia")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Informe o número da conta')
					.attr("data-original-title", 'Informe o número da conta');
				formControl.tooltip();
			}
			if(empty(ng.pagamento.proprietario_conta_transferencia)){
				$("#proprietario_conta_transferencia").addClass("has-error");
				var formControl = $("#proprietario_conta_transferencia")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Informe o Proprietário da conta')
					.attr("data-original-title", 'Informe o Proprietário da conta');
				formControl.tooltip();
			}
			if(empty(ng.pagamento.id_conta_bancaria)){
				$("#pagamento_id_conta_transferencia_destino").addClass("has-error");
				var formControl = $("#pagamento_id_conta_transferencia_destino")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Informe a conta de origem')
					.attr("data-original-title", 'Informe a conta de origem');
				formControl.tooltip();
			}
		}

		if(error > 0){
			return;
		}

		if((ng.pagamento.id_forma_pagamento == 6 || ng.pagamento.id_forma_pagamento == 2 || ng.pagamento.id_forma_pagamento == 4 ) && (ng.pagamento.parcelas ==  undefined || ng.pagamento.parcelas ==  '') ){
			ng.pagamento.parcelas = 1 ;
		}

		var push = true ;

		if(ng.pagamento.id_forma_pagamento == 2){


			$.each(ng.recebidos,function(a,b){
				if(Number(b.id_forma_pagamento) == 2){
					ng.recebidos.splice(a,1);
				}
			});


			var valor_parcelas = ng.pagamento.valor / ng.pagamento.parcelas;
			var count = 0 ;
			ng.pg_cheques = [];
			$.each(ng.cheques,function(index,value){
				value.id_forma_pagamento  	= ng.pagamento.id_forma_pagamento;
				value.valor 				= Math.round(valor_parcelas * 100) / 100;
				value.id_maquineta 			= ng.pagamento.id_maquineta;
				value.parcelas 				= 1 ;
				value.data_pagamento		= formatDate($('.chequeData').eq(count).val());
				//value.valor_pagamento		= valor_parcelas;
				ng.pg_cheques.push(value);
				count ++ ;
			});
		}else if(ng.pagamento.id_forma_pagamento == 4){


			$.each(ng.recebidos,function(a,b){
				if(Number(b.id_forma_pagamento) == 4){
					ng.recebidos.splice(a,1);
				}
			});


			var valor_parcelas = ng.pagamento.valor / ng.pagamento.parcelas;
			var count = 0 ;
			ng.pg_boletos = [];
			$.each(ng.boletos,function(index,value){
				value.id_forma_pagamento  	= ng.pagamento.id_forma_pagamento;
				//value.valor 				= Math.round(valor_parcelas * 100) / 100;
				value.id_maquineta 			= ng.pagamento.id_maquineta;
				value.parcelas 				= 1 ;
				value.data_pagamento		= formatDate($('.boletoData').eq(count).val());
			//value.valor_pagamento		= valor_parcelas;
				ng.pg_boletos.push(value);
				count ++ ;
			});
		}

		console.log(ng.pg_boletos);

		if(ng.pagamento.id_forma_pagamento == 3){
			$.each(ng.recebidos,function(x,y){
				if(Number(y.id_forma_pagamento) == 3){
					ng.recebidos[x].valor = ng.recebidos[x].valor + ng.pagamento.valor ;
					push = false ;
				}
			});
		}

		if(push){
			if(ng.pagamento.id_forma_pagamento == 8){
				var item = {
								id_forma_pagamento 				 : ng.pagamento.id_forma_pagamento,
								valor              				 : ng.pagamento.valor,
								id_maquineta	   				 : ng.pagamento.id_maquineta,
								parcelas           				 : ng.pagamento.parcelas,
								id_vale_troca     				 : ng.pagamento.id_vale_troca,
								agencia_transferencia            : ng.pagamento.agencia_transferencia,
								conta_transferencia              : ng.pagamento.conta_transferencia,
								proprietario_conta_transferencia : ng.pagamento.proprietario_conta_transferencia,
								id_conta_transferencia_destino   : ng.pagamento.id_conta_transferencia_destino,
								id_banco                         : ng.pagamento.id_banco
						   };
			}else{
				var item = {
								id_forma_pagamento 				 : ng.pagamento.id_forma_pagamento,
								valor              				 : ng.pagamento.valor,
								id_maquineta	   				 : ng.pagamento.id_maquineta,
								parcelas           				 : ng.pagamento.parcelas,
								id_vale_troca     				 : ng.pagamento.id_vale_troca
						   };
			}

			$.each(ng.formas_pagamento,function(i,v){
				if(v.id == ng.pagamento.id_forma_pagamento){
					item.forma_pagamento = v.nome ;
					return;
				}
			});
			ng.recebidos.push(item);
		}
		ng.totalPagamento();
		ng.calculaTroco();
		ng.pagamento = {} ;
		//console.log(ng.recebidos,ng.cheques);
	}

	ng.deleteRecebidos = function(index){
		ng.recebidos.splice(index,1);
		ng.totalPagamento();
		ng.calculaTroco();
	}

	ng.loadMaquinetas = function() {
		ng.maquinetas = [];

		aj.get(baseUrlApi()+"maquinetas/?maq->id_empreendimento="+ng.userLogged.id_empreendimento+"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				ng.maquinetas 			= data.maquinetas;
				ng.paginacao.maquinetas = [] ;
			})
			.error(function(data, status, headers, config) {
				ng.paginacao.maquinetas = [] ;
			});
	}


	ng.cancelarModal = function(id){
		$('#'+id).modal('hide');
	}

	ng.cancelarPagamento = function(){
		if(ng.pagamento_fulso == true){
			window.location = "pdv.php";
		}else{
			ng.tela = 'escolher_tiras';
			//ng.receber_pagamento = false;
			ng.recebidos = [];
			ng.totalPagamento();
			ng.calculaTroco();
		}
	}



	var nParcelasAntCheque = 1 ;
	var nParcelasAntBoleto = 1 ;
	ng.pagamento.parcelas  = 1 ;

	ng.pushCheques = function(){
		if(ng.pagamento.id_forma_pagamento == 2){
			ng.pagamento.parcelas = empty(ng.pagamento.parcelas) ? 1 : ng.pagamento.parcelas ;
			ng.pagamento.parcelas = ng.pagamento.parcelas == "" ?  1 : ng.pagamento.parcelas ;
			if(ng.pagamento.parcelas > nParcelasAntCheque){
				var repeat = parseInt(ng.pagamento.parcelas) - parseInt(nParcelasAntCheque) ;
				while(repeat > 0){
					var item = {id_banco:null,valor_pagamento:0,num_conta_corrente:null,num_cheque:null,flg_cheque_predatado:0};
					ng.cheques.push(item);
					repeat -- ;
				}
			}else if(ng.pagamento.parcelas < nParcelasAntCheque){
				var repeat = parseInt(nParcelasAntCheque) - parseInt(ng.pagamento.parcelas) ;
				while(repeat > 0){
					var index = ng.cheques.length - 1;
					ng.cheques.splice(index,1);
					repeat -- ;
				}
			}
			nParcelasAntCheque = ng.pagamento.parcelas;
			ng.calTotalCheque();
			setTimeout(function(){ ng.loadDatapicker();}, 1000);
		}else if(ng.pagamento.id_forma_pagamento == 4){
			ng.pagamento.parcelas = empty(ng.pagamento.parcelas) ? 1 : ng.pagamento.parcelas ;
			ng.pagamento.parcelas = ng.pagamento.parcelas == "" ?  1 : ng.pagamento.parcelas ;
			if(ng.pagamento.parcelas > nParcelasAntBoleto){
				var repeat = parseInt(ng.pagamento.parcelas) - parseInt(nParcelasAntBoleto) ;
				while(repeat > 0){
					var item = {id_banco:null,valor_pagamento:0,num_conta_corrente:null,num_cheque:null,status_pagamento:0};
					ng.boletos.push(item);
					repeat -- ;
				}
			}else if(ng.pagamento.parcelas < nParcelasAntBoleto){
				var repeat = parseInt(nParcelasAntBoleto) - parseInt(ng.pagamento.parcelas) ;
				while(repeat > 0){
					var index = ng.cheques.length - 1;
					ng.boletos.splice(index,1);
					repeat -- ;
				}
			}
			nParcelasAntBoleto = ng.pagamento.parcelas;
			ng.calTotalBoleto();
			setTimeout(function(){ ng.loadDatapicker();}, 1000);
		}
	}


	ng.loadDatapicker = function(){
		$(".chequeData").datepicker();
		$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
		$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});

		$(".boletoData").datepicker();
		$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
		$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});
	}

	ng.selectChange = function(){
		if(ng.pagamento.id_forma_pagamento == 2){
			ng.pagamento.valor = 0 ;
			ng.pagamento.parcelas = ng.cheques.length  > 0 ? ng.cheques.length : 1 ;
			if(ng.cheques.length > 0)
				ng.calTotalCheque();
		}else if(ng.pagamento.id_forma_pagamento == 6){
			ng.pagamento.parcelas = 1 ;
		}else if(ng.pagamento.id_forma_pagamento == 4){
			ng.pagamento.valor = 0 ;
			ng.pagamento.parcelas = ng.boletos.length  > 0 ? ng.boletos.length : 1 ;
			if(ng.boletos.length > 0)
				ng.calTotalBoleto();
		}	

		ng.loadDatapicker();
	}

	ng.delItemCheque = function($index){
		ng.cheques.splice($index,1);
		ng.pagamento.parcelas = ng.cheques.length ;
		nParcelasAnt  = ng.pagamento.parcelas
	}

	ng.focusData  = function($index){
		if(ng.pagamento.id_forma_pagamento == 2)
			$(".chequeData").eq($index).trigger("focus");
		if(ng.pagamento.id_forma_pagamento == 4)
			$(".boletoData").eq($index).trigger("focus");
	}

	ng.bancos = [] ;
	ng.loadBancos = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

		ng.bancos = [];

		aj.get(baseUrlApi()+"bancos")
			.success(function(data, status, headers, config) {
				ng.bancos = data.bancos;
			})
			.error(function(data, status, headers, config) {

			});
	}

	ng.calTotalCheque = function(){
		var valor = 0 ;
		$.each(ng.cheques,function(i,v){
			valor += Number(v.valor_pagamento);
		});

		ng.pagamento.valor = valor;

	}

	ng.calTotalBoleto = function(){
		var valor = 0 ;
		$.each(ng.boletos,function(i,v){
			valor += Number(v.valor_pagamento);
		});

		ng.pagamento.valor = valor;

	}

	ng.qtdCheque = function(){
		if(ng.pagamento.id_forma_pagamento == 2){
			ng.pagamento.parcelas = ng.cheques.length  > 0 ? ng.cheques.length : 1 ;
		}

	}

	ng.pagamentoFulso = function (){
		ng.receber_pagamento = true ;
		ng.venda_aberta 	 = true ;
		ng.pagamento_fulso   = true ;
	}

	ng.showVlrReal = function(){
		ng.show_vlr_real = !ng.show_vlr_real ;
	}
	ng.view = {desconto_all:0} ;
	ng.aplicarDescontoAll = function(){
		//console.log(ng.view.desconto_all);
		$(".has-error").tooltip('destroy');
		$(".has-error").removeClass('has-error');
		if(empty(ng.view.desconto_all)){
			$("#desconto-all").addClass("has-error");
			var formControl = $('#desconto-all')
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("data-original-title", 'Infome o Desconto Desejado');
			formControl.tooltip();
			return ;
		}

		$.each(ng.chinelos_gerados,function(i,chinelo){
				chinelo.valor_desconto_cal = ng.view.desconto_all ;		
		});
		ng.view.desconto_all = 0 ;
		ng.calcSubTotal('desconto');
	}

	ng.imprimirRomaneio = function(item){
		var caminho = baseUrlApi()+'relPDF?' + $.param({
			classe : 		'PedidoVendaDao',
			metodo : 		'getRelRomaneio',
			parametros : 	[ item.id ],
			template : 		'romaneio_pedido_personalizado'
		});
		
		eModal.setEModalOptions({ loadingHtml: '<div><div style="text-align: center;margin-top:5px;margin-bottom:3px"><span class="fa fa-circle-o-notch fa-spin fa-3x text-primary"></span></div><div style="text-align: center;"><span class="h4">Carregando, aguarde...</span></div></div>'});
		var title = 'Controle Interno de Produção';
        eModal
            .iframe({message:caminho, title:title,size:'lg'})
            .then(function () { t8.success('iFrame loaded!!!!', title) });
	}

	ng.imprimirRomaneioCliente = function(item){
		var caminho = baseUrlApi()+'relPDF?'+$.param({classe:'PedidoVendaDao',metodo:'getRelRomaneio',parametros:[item.id],template:'romaneio_pedido_personalizado_cliente'});
		eModal.setEModalOptions({ loadingHtml: '<div><div style="text-align: center;margin-top:5px;margin-bottom:3px"><span class="fa fa-circle-o-notch fa-spin fa-3x text-primary"></span></div><div style="text-align: center;"><span class="h4">Carregando, aguarde...</span></div></div>'});
		var title = 'Pedido de Venda (Via Cliente)';
        eModal
            .iframe({message:caminho, title:title,size:'lg'})
            .then(function () { t8.success('iFrame loaded!!!!', title) });
	}


	
	ng.loadConfig();
	ng.loadVendas(0,10);
	if(!($.cookie("alerta") == undefined)){
		var alerta = JSON.parse($.cookie("alerta"));
		$.removeCookie("alerta");
		ng.mensagens(alerta.class,'<b>'+alerta.msg+'</b>','.alert-listagem');

	}
});

app.directive('bsTooltip', function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attr) {
            $timeout(function () {
                	  element.find("[data-toggle=tooltip]").tooltip();
            });
        }
    }
});

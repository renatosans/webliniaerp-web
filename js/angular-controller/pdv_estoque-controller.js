app.controller('pdv_estoqueController', function($scope, $http, $window,$dialogs, UserService) {
	var ng = $scope,
		aj = $http;

	ng.userLogged 	 		= UserService.getUserLogado();
	ng.busca 		 		= {codigo: "", ok: false};
	ng.msg 		     		= "";
	ng.itensCarrinho 		= [];
	ng.nome_ultimo_produto 	= null ;
	ng.imgProduto			= 'img/imagem_padrao_produto.gif';
	ng.busca.clientes       = '';
	ng.paginacao            = {produtos:null};

	ng.carrinho = [];
	ng.vlrTotalCompra = 0;

	ng.calcTotalCompra = function() {
		var total = 0 ;
		$.each(ng.carrinho, function(i, item) {
			total += Number(item.sub_total);
		});
		ng.vlrTotalCompra = total ;
	}

	ng.calcSubTotal = function(item){
		var qtd_total = isNaN(Number(item.qtd_total)) || Number(item.qtd_total) == 0  ? 1 : Number(item.qtd_total) ;
		item.sub_total = qtd_total * Number(item.vlr_unitario);
		ng.calcTotalCompra();
	}

	ng.findProductByBarCode = function() {
		if(ng.cliente == null){
			$dialogs.notify('Atenção!','Antes de adicionar um produto, selecione um cliente');
			return;
		}
		if(ng.busca.codigo != "") {
			ng.msg = "";
			ng.busca.ok = !ng.busca.ok;
			$http.get(baseUrlApi()+'estoque/?prd->codigo_barra='+ng.busca.codigo+"&emp->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.busca.codigo = "" ;
				ng.incluirCarrinho(data.produtos[0]);
				ng.calcTotalCompra();
				//ng.verificarCarrinho(data.produtos);
	        }).error(function(data, status) {
	        	ng.busca.ok = false;
				ng.msg = "O código de barra não existe!";
	   	    });

		}
		else {
			ng.showProdutos();
			//ng.busca.ok = false;
			//ng.msg = "O código de barras é obrigatório!";
		}
	}

	ng.indexItensEstoque = null ;
	ng.showModal = function(produtos,nome_produto,index){
		if(index == null){
			ng.itensEstoque 		 = _.groupBy(produtos, "nome_deposito");
			ng.nome_produto_modal    = produtos[0].nome_produto ;
			ng.indexItensEstoque 	 = null;
		}else{
			ng.itensEstoque 		 = angular.copy(produtos) ;
			ng.indexItensEstoque	 = index ;
			ng.nome_produto_modal	 = nome_produto;
		}
		$("#list_validades").modal("show");
	}

	ng.verificarCarrinho = function(item){
		var id_produto = item[0].id_produto;
		var saida = true ;
		var estoques = item;
		var index    = null ;
		$.each(ng.carrinho,function(i,value){
			console.log(id_produto, value.id_produto);
			if(parseInt(id_produto) == parseInt(value.id_produto)){
				saida     = false ;
				estoques  = value.estoques;
				index     = i ;
				return;
			}
		});
		if(saida){
			//ng.showModal(estoques);
			ng.itensEstoque
		}else{
			ng.showModal(estoques, estoques.nome_produto, index);
		}
	}

	ng.incluirCarrinho = function(produto){
		if(ng.cliente.nome_perfil == "atacado"){
			produto.vlr_unitario    = produto.vlr_venda_atacado;
			produto.vlr_real        = produto.vlr_venda_atacado;
		}else if(ng.cliente.nome_perfil == "varejo"){
			produto.vlr_unitario	= produto.vlr_venda_varejo;
			produto.vlr_real        = produto.vlr_venda_varejo;
		}else if(ng.cliente.nome_perfil == "vendedor Externo"){
			produto.vlr_unitario	= produto.vlr_venda_intermediario;
			produto.vlr_real        = produto.vlr_venda_intermediario;
		}
		produto.valor_desconto = 0;
		produto.qtd_total = 1 ;
		produto.sub_total = produto.qtd_total * produto.vlr_unitario;

		ng.vezes_valor			    = produto.qtd_total+' x R$ '+numberFormat(produto.vlr_unitario,2,',','.');
		ng.nome_ultimo_produto      = produto.nome_produto ;

		if(produto.img != null)
			ng.imgProduto = produto.img ;
		else
			ng.imgProduto = 'img/imagem_padrao_produto.gif';

		ng.carrinho.push(produto) ;
		//var produto  = {nome_produto:null,qtd_total:0/*,estoques:ng.itensEstoque*/} ;
		/*var cont     = 0;
		$.each(ng.itensEstoque,function(index,value){
			$.each(value,function(i,item){
				if(cont == 0){
					produto.nome_produto            = item.nome_produto;
					produto.id_produto 				= item.id_produto;
					produto.img						= baseUrl() + item.img;
					produto.valor_desconto          = item.vlr_desconto_cliente * 100 ;
					produto.flg_desconto			= "0" ;

					if(ng.cliente.nome_perfil == "atacado"){
						produto.vlr_unitario = item.vlr_venda_atacado;
						produto.vlr_real     = item.vlr_venda_atacado;
					}else if(ng.cliente.nome_perfil == "varejo"){
						produto.vlr_unitario	= item.vlr_venda_varejo;
						produto.vlr_real     = item.vlr_venda_varejo;
					}else if(ng.cliente.nome_perfil == "vendedor Externo"){
						produto.vlr_unitario	= item.vlr_venda_intermediario;
						produto.vlr_real     = item.vlr_venda_intermediario;
					}
				}
				if(item.qtd_saida != null && !isNaN(parseInt(item.qtd_saida))){
					produto.qtd_total += parseInt(item.qtd_saida);
				}
				cont ++ ;
			});
		});

		if(produto.qtd_total == 0){
			ng.mensagens('alert-warning','Informe a quantidade desejada','.alert-validades');
			return;
		}
		console.log(ng.indexItensEstoque);
		produto.sub_total = produto.qtd_total * produto.vlr_unitario ;
		if(ng.indexItensEstoque != null){
			produto.valor_desconto = ng.carrinho[ng.indexItensEstoque].valor_desconto ;
			produto.flg_desconto   = ng.carrinho[ng.indexItensEstoque].flg_desconto ;
			produto.vlr_unitario   = ng.carrinho[ng.indexItensEstoque].vlr_unitario ;
			produto.sub_total      = produto.qtd_total * produto.vlr_unitario;
			ng.carrinho[ng.indexItensEstoque] = produto ;
		}else{
			ng.carrinho.push(produto);
			ng.vezes_valor			= produto.qtd_total+' x R$ '+numberFormat(produto.vlr_unitario,2,',','.');
			ng.nome_ultimo_produto  = produto.nome_produto ;

			if(produto.img != null)
				ng.imgProduto = produto.img ;
			else
				ng.imgProduto = 'img/imagem_padrao_produto.gif';
		}
		ng.indexItensEstoque = null ;
		ng.calcTotalCompra();
		$("#list_validades").modal("hide");
		*/
	}

	ng.virificarQuantidade = function(key,index , $event){
		var qtd_max = parseInt(ng.itensEstoque[key][index].qtd_item);
		var qtd     = ng.itensEstoque[key][index].qtd_saida == "" ? 0 : parseInt(ng.itensEstoque[key][index].qtd_saida) ;
		var input = $($event.target);
		if(qtd  > qtd_max){
			ng.itensEstoque[key][index].qtd_saida = "" ;
				input.attr("data-toggle", "tooltip")
				.attr("title", 'A quantidade desejada ('+qtd+') é maior que à em estoque ('+qtd_max+')')
				.attr("data-original-title", 'A quantidade desejada ('+qtd+') é maior que à em estoque');
				input.addClass("has-error")
			input.tooltip("show");
			setTimeout(function(){
				input.tooltip('destroy');
				input.removeClass("has-error");

			},5000);
		}else{
			input.tooltip('destroy');
			input.removeClass("has-error");
		}
	}

	ng.salvar = function(){
		var produtos = angular.copy(ng.carrinho);
		var venda    ={
						id_usuario:ng.userLogged.id,id_cliente:parseInt(ng.cliente.id),venda_confirmada:1,
						id_empreendimento:ng.userLogged.id_empreendimento
					  };

		$.each(produtos,function(index,value){
			produtos[index].venda_confirmada = 1 ;
			produtos[index].valor_produto = value.vlr_unitario;
			produtos[index].qtd           = value.qtd_total;
			if(value.flg_desconto != null && Number(value.valor_desconto) > 0 && !isNaN(Number(value.valor_desconto)) ){
				produtos[index].desconto_aplicado	= parseInt(value.flg_desconto) != 1 && isNaN(parseInt(value.flg_desconto)) ? 0 : 1 ;
				produtos[index].valor_desconto      = parseInt(value.flg_desconto) == 1 ? value.valor_desconto/100 : 0 ;
			}else{
				produtos[index].desconto_aplicado	= 0 ;
				produtos[index].valor_desconto      = 0 ;
			}
		});
	
		aj.post(baseUrlApi()+"venda/loja",{produtos:produtos,venda:venda})
		.success(function(data, status, headers, config) {	
			ng.cancelar();

			dlg = $dialogs.confirm('Venda Realizada!!!' ,'<strong>A venda foi realizada com sucesso. Deseja ser efetuar lançamentos de pagamento deste cliente?</strong>');
			dlg.result.then(function(btn){
				window.location.href = baseUrl()+"lancamentos.php";
			}, undefined);
		})
		.error(function(data, status, headers, config) {
			ng.out_produtos = data ;
			if(status == 406){
				$.each(data,function(i, value){
					$("#"+value+" td").css({background:"#FF9191"});
				});
			}
		});
		

		//console.log(produtos);
		/*
		if(ng.carrinho.length <= 0){
			$dialogs.notify('Atenção!','Nenhum produto foi selecionado');
			return;
		}

		var venda = {
				venda:{
					id_usuario:ng.userLogged.id,id_cliente:parseInt(ng.cliente.id),venda_confirmada:1,
					id_empreendimento:ng.userLogged.id_empreendimento
				},
				itens:[]
			};
		var push_saida = [] ;
		$.each(ng.carrinho,function(i,value){
			var valor_real_item  = value.vlr_unitario;
			var flg_desconto     = parseInt(value.flg_desconto);
			var valor_desconto   = value.valor_desconto / 100;
			var vlr_real         = value.vlr_real;
			var itens_saida      = [];
			var item_venda 		 = {
					id_produto 			:value.id_produto,
					desconto_aplicado	:flg_desconto,
					valor_desconto		:flg_desconto == 1 ? valor_desconto : 0,
					qtd 				:value.qtd_total,
					valor_real_item     :vlr_real
			}
			$.each(value.estoques,function(index,estoque){
				$.each(estoque,function(indexPro,produto){
					var qtd_saida = (produto.qtd_saida != "" && !isNaN(parseInt(produto.qtd_saida))) ? produto.qtd_saida : 0 ;
					if(qtd_saida> 0){
						var item_saida = {
							id_produto 			:parseInt(produto.id_produto),
							id_deposito			:parseInt(produto.id_deposito),
							dta_validade		:produto.dta_validade ,
							valor_desconto		:flg_desconto == 1 ? valor_desconto : 0,
							qtd 				:value.qtd_total,
							qtd_item			:parseInt(produto.qtd_saida),
							valor_real_item 	:valor_real_item
						}
						itens_saida.push(item_saida);
					}
				});
			});
		push_saida.push({itens_venda:item_venda,itens_saida:itens_saida});
		itens_saida = [];
		});
		venda.itens = push_saida;
		aj.post(baseUrlApi()+"venda", venda)
			.success(function(data, status, headers, config) {
				ng.cancelar();

				dlg = $dialogs.confirm('Venda Realizada!!!' ,'<strong>A venda foi realizada com sucesso. Deseja ser efetuar lançamentos de pagamento deste cliente?</strong>');
				dlg.result.then(function(btn){
					window.location.href = baseUrl()+"vendas.php";
				}, undefined);
			})
			.error(function(data, status, headers, config) {

			});
		console.log(venda);
		*/
	}


	ng.aplicarDesconto = function(index,$event,checkebox){
		checkebox = checkebox == null ? true : false ;
		if(checkebox)
		var element = $event.target ;
		else{
	    var element = $($event.target).parent().prev().children().children('input');
		}

		var valor_desconto = ng.carrinho[index].valor_desconto/100 ;
		var vlr_real     = ng.carrinho[index].vlr_real ;
		if($(element).is(':checked')){
			if(checkebox)
			ng.carrinho[index].vlr_unitario =  vlr_real - (vlr_real * valor_desconto) ;
			else{
	            ng.carrinho[index].vlr_unitario =  vlr_real - (vlr_real * valor_desconto) ;
			}
		}else{
			ng.carrinho[index].vlr_unitario =  vlr_real ;
		}
		ng.carrinho[index].sub_total = ng.carrinho[index].qtd_total * ng.carrinho[index].vlr_unitario ;
		ng.calcTotalCompra();
	}

	ng.selCliente = function(){
		var offset = 0  ;
    	var limit  =  10 ;;

			ng.loadCliente(offset,limit);
			$("#list_clientes").modal("show");
	}


	ng.addCliente = function(item){
    	ng.cliente = item;
    	$("#list_clientes").modal("hide");
	}

	ng.loadCliente= function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.clientes = [];
		query_string = "";
		if(ng.busca.clientes != ""){
			query_string += "?"+$.param({'usu->nome':{exp:"like'%"+ng.busca.clientes+"%'  AND (per.nome='atacado' OR per.nome='varejo' OR per.nome = 'vendedor Externo')"}});
		}else{
			query_string += "?"+$.param({'per->nome':{exp:"='atacado' OR per.nome='varejo' OR per.nome = 'vendedor Externo'"}});
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

			});
	}
	ng.out_produtos = [] ;
	ng.delItem = function(index){
		index = ng.out_produtos.indexOf(ng.carrinho[index].id_produto,1);
		if(index < 0)
			ng.out_produtos.splice(index,1);
		
		ng.carrinho.splice(index,1);
		ng.calcTotalCompra();
		if(ng.carrinho.length == 0){
			ng.imgProduto  = 'img/imagem_padrao_produto.gif';
			ng.vezes_valor = null ;
			ng.nome_ultimo_produto = null ;
		}

	}

	ng.cancelar = function(){
		ng.carrinho    			= [];
		ng.cliente     			= {};
		ng.vezes_valor 			= null;
		ng.nome_ultimo_produto 	= null;
		ng.imgProduto 			= 'img/imagem_padrao_produto.gif';
		//window.location.href = baseUrl()+"lancamentos.php";
	}

		ng.produtos = [] ;

   	ng.showProdutos = function(){
   		ng.busca.produtos = "" ;
   		ng.loadProdutos(0,10);
   		$('#list_produtos').modal('show');
   	}

   	ng.loadProdutos = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

    	var query_string = "?group=&emp->id_empreendimento="+ng.userLogged.id_empreendimento;

    	if(ng.busca.produtos != ""){
    		query_string += "&"+$.param({'prd->nome':{exp:"like'%"+ng.busca.produtos+"%' OR fab.nome_fabricante like'%"+ng.busca.produtos+"%'"}});
    	}

		ng.produtos = [];
		aj.get(baseUrlApi()+"estoque/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.produtos        = data.produtos ;
				ng.paginacao.produtos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.produtos = [];
			});
	}

	ng.addProduto = function(item){
		ng.incluirCarrinho(item);
		ng.calcTotalCompra();
	}

	/* end */


	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	ng.calcTotalCompra();
});

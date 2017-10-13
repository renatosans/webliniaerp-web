app.controller('SepararVendaController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
	var params      = getUrlVars();
	ng.id_venda     = params.id_venda ;

	ng.loadDetalhesVenda = function(id_venda) {
		aj.get(baseUrlApi()+"venda/itens/"+ id_venda)
			.success(function(data, status, headers, config) {
				ng.itens = data;
			})
			.error(function(data, status, headers, config) {
				ng.itens = [] ;
			});
	}
	var item_current = null ;
	ng.findProductByid = function(item,index) {
			item_current = index ;
			$http.get(baseUrlApi()+'estoque/?prd->id='+item.id_produto+"&emp->id_empreendimento="+ng.userLogged.id_empreendimento+"&etq->qtd_item[exp]=>0")
			.success(function(data, status, headers, config) {
				if(item.estoques == null){
					ng.itensEstoqueValidades  = _.groupBy(data.produtos, "nome_deposito");
					ng.itensEstoque  =  ng.agruparEstoqueValidades(ng.itensEstoqueValidades);
					item.estoques             = ng.itensEstoque;
				}else{
					ng.itensEstoque 		 = angular.copy(item.estoques);
				}
				ng.nome_produto_modal    = item.nome_produto;
				$("#list_validades").modal("show");
	        }).error(function(data, status) {
	        	alert('Ocorreu um erro inesperado !');
	   	    });
	}

	ng.agruparEstoqueValidades = function(itensEstoque){
		var estoque_atual = null ;
		var estoque       = [] ;
		$.each(itensEstoque,function(a,b){
			var count = 0;
			$.each(b,function(c,d){
				estoque_atual = angular.copy(d);
				count += parseInt(d.qtd_item);
			});
			estoque_atual.qtd_item = count;
			estoque.push(estoque_atual);
		});

		return _.groupBy(estoque, "nome_deposito");
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

	ng.verificarQtdMax = function(){
		var qtd_max = ng.itens[item_current].qtd;
		var qtd     = 0 ;
		$.each(ng.itensEstoque,function(index,value){
			$.each(value,function(i,v){
				qtd +=  v.qtd_saida == null ? 0 : parseInt(v.qtd_saida);
			});
		});
		if(qtd > qtd_max){
			return {qtd:qtd,qtd_max:qtd_max};
		}else{
			return false;
		}
	}

	ng.verificarQtdMax = function(){
		var qtd_max = ng.itens[item_current].qtd;
		var qtd     = 0 ;
		$.each(ng.itensEstoque,function(index,value){
			$.each(value,function(i,v){
				qtd +=  v.qtd_saida == null ? 0 : parseInt(v.qtd_saida);
			});
		});
		if(qtd > qtd_max){
			return {qtd:qtd,qtd_max:qtd_max};
		}else{
			return false;
		}
	}

	ng.qtdTotalEstoque = function(){
		var total = 0 ;
		$.each(ng.itensEstoque,function(index,value){
			$.each(value,function(i,v){
				if(v.qtd_saida != null && !isNaN(parseInt(v.qtd_saida) ) ) {
					total += parseInt(v.qtd_saida); 
				}
			});	
		});

		return total ;		
	}

	ng.validQtdTotalEstoque = function(){
		var total = 0 ;
		var saida = true;
		$.each(ng.itens,function(index,value){
			qtd 	  = parseInt(value.qtd);
			qtd_saida = parseInt(value.qtd_saida_total);
			if(qtd != qtd_saida){
				saida = false;
			}else if(value.estoques == null){
				saida = false;
			}
		});


		return saida ;		
	}


	ng.incluirCarrinho = function(){
		var invalido = ng.verificarQtdMax();
		if(invalido){
			ng.mensagens('alert-warning','A quantidade informada ('+invalido.qtd+') e maior que a quantidade da compra ('+invalido.qtd_max+')','.alert-validades');
			return ;
		}
		ng.itens[item_current].estoques = ng.itensEstoque;
		$("#list_validades").modal("hide");
		ng.itens[item_current].qtd_saida_total = ng.qtdTotalEstoque();
	}

	ng.calQtdAdd = function(estoques){
		estoques = typeof estoques == 'object' ? estoques : {} ;
		var total = 0 ;
		$.each(estoques,function(a,b){
			$.each(b,function(i,x){
				if($.isNumeric(x.qtd_saida))
					total += Number(x.qtd_saida) ;
			});
		});
		return total ;
	}

	ng.salvar = function(){
		var btn = $("#btn_separar_venda") ;
		btn.button('loading');
		if(!ng.validQtdTotalEstoque()){
			$dialogs.notify('Atenção!','Não foi informado a quantidade exata para cada produto');
			btn.button('reset');
			return;
		}
		var itens_saida = [];
		var itens_saida_aux = [] ;
		$.each(ng.itens,function(key,item){
			$.each(item.estoques,function(index,value){
				$.each(value,function(i,v){	
					if(!empty(v.qtd_saida))
						itens_saida.push({ 
											id_produto  : Number(v.id_produto),
											id_deposito : Number(v.id_deposito),
											qtd         : Number(v.qtd_saida)
										});
				});	
			});	
		});
		//console.log(itens_saida);return;

		
		$http.post(baseUrlApi()+'estoque/baixa',{
													itens_saida 		: itens_saida,
													id_venda 			: params.id_venda,
													id_empreendimento 	: ng.userLogged.id_empreendimento
												})
			.success(function(data, status, headers, config) {
				window.location.href = "vendas.php?alert=true";
				btn.button('reset');
	        }).error(function(data, status){
	        	alert('Ocorreu um erro inesperado !');
	        	btn.button('reset');
	   		});
	   	
	}


	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}


	ng.loadDetalhesVenda(params.id_venda);
});

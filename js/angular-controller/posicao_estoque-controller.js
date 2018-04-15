app.controller('PosicaoEstoqueController', function($scope, $http, $window, $dialogs, UserService,ConfigService){
	var ng = $scope
		aj = $http;

	ng.baseUrl 		 = baseUrl();
	ng.userLogged 	 = UserService.getUserLogado();
	ng.configuracoes = ConfigService.getConfig(ng.userLogged.id_empreendimento);
	ng.posicao_estoque   = {} ;
	ng.busca         = {};
	ng.new_posicao = {};
	ng.loadProdutoNewPosicao = {};

    ng.editing = false;

    ng.resetPosicoes = function () {
    	$("#busca_dta_inicial").val('');
    	$("#busca_dta_final").val('');
    	ng.busca.id_deposito = "";
    	ng.busca.nome_deposito = "";
    	ng.loadPosicoes(0,10);
    }

    ng.reset = function () {
    	$("#dtaInicial").val('');
    	$("#dtaFinal").val('');
    	ng.new_posicao.id_deposito = "";
    	ng.new_posicao.nome_deposito = "";
    }

    ng.selPosicao = function (item) {
    	$('#detalhes_posicao').modal('show');
    	ng.detalhes_posicao = item;
    	ng.loadPosicao(item);
    }

    ng.loadPosicao = function (item) {
    	ng.produtos_posicao = [];
		aj.get(baseUrlApi()+"posicao-estoque/"+item.id+"/itens")
			.success(function(data, status, headers, config) {
				ng.produtos_posicao = data;
			})
			.error(function(data, status, headers, config) {
			});    	
    }

    ng.loadPosicoes = function(offset, limit) {

    	var queryString = "?tpe->id_empreendimento="+ng.userLogged.id_empreendimento;

    	if (!empty(ng.busca.id_deposito))
    		queryString += "&dep->id="+ng.busca.id_deposito;

    	ng.busca.dta_inicial = (!empty($("#busca_dta_inicial").val())) ? formatDate($("#busca_dta_inicial").val()) : null;
    	ng.busca.dta_final = (!empty($("#busca_dta_final").val())) ? formatDate($("#busca_dta_final").val()) : null;

    	if (ng.busca.dta_inicial > ng.busca.dta_final) {
    		alert("A data final deve ser maior que a data inicial");
    		return false;
    	}

    	if (!empty(ng.busca.dta_inicial) && !empty(ng.busca.dta_final)) {
    		queryString+= "&"+$.param({'tpe->dta_posicao_estoque':{exp:"BETWEEN '"+ ng.busca.dta_inicial + "' AND '" + ng.busca.dta_final + "'"}});
    	}

    	aj.get(baseUrlApi()+"posicao-estoque/"+offset +"/"+ limit + queryString)
			.success(function(data, status, headers, config) {
				ng.posicoes_estoque = data;
			})
			.error(function(data, status, headers, config) {
			});
    }

    ng.isNumeric = function(vlr){
    	return $.isNumeric(vlr);
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

	ng.selProduto = function(){
		ng.loadProdutoNewPosicao.produtos = "";
   		ng.loadProdutos(0,10);
   		$('#list_produtos').modal('show');
   	}

   	ng.loadProdutos = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

		ng.produtos =  null;

		var query_string = "?tpe->id_empreendimento="+ng.userLogged.id_empreendimento;
			query_string += "&(tpc->id_combinacao[exp]=IS NULL OR (tpc.id_combinacao IS NOT NULL AND pro.id = tpc.id_produto ) )" ;

		if(ng.loadProdutoNewPosicao.produtos != ""){
			if(isNaN(Number(ng.loadProdutoNewPosicao.produtos)))
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.loadProdutoNewPosicao.produtos+"%' OR codigo_barra like '%"+ng.loadProdutoNewPosicao.produtos+"%' OR cat.descricao_categoria like '%"+ng.loadProdutoNewPosicao.produtos+"%' OR fab.nome_fabricante like '%"+ng.loadProdutoNewPosicao.produtos+"%'"}})+")";
			else
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.loadProdutoNewPosicao.produtos+"%' OR codigo_barra like '%"+ng.loadProdutoNewPosicao.produtos+"%' OR cat.descricao_categoria like '%"+ng.loadProdutoNewPosicao.produtos+"%' OR fab.nome_fabricante like '%"+ng.loadProdutoNewPosicao.produtos+"%' OR pro.id = "+ng.loadProdutoNewPosicao.produtos+""}})+")";
		}

		aj.get(baseUrlApi()+"produtos/"+ offset +"/"+ limit +"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.produtos           = data.produtos ;
				ng.paginacao_produtos = data.paginacao;
				
			})
			.error(function(data, status, headers, config) {
				ng.produtos = [];
			});
	}

	ng.loadEstoqueInicial = function(item) {
		item.loading.estoque_inicial = true;

		var dta_inicial = moment($("#dtaInicial").val(), 'DD/MM/YYYY').format('YYYY-MM-DD 00:00:00');

		var queryString = "?dta_movimentacao[exp]=<'"+dta_inicial+"'";

		aj.get(baseUrlApi() +"posicao-estoque/produto/"+ item.id_produto +"/inicial"+ queryString)
			.success(function(data, status, headers, config) {
				item.qtd_estoque_inicial = data.qtd_estoque_inicial;
				setTimeout(function(){
					$scope.$apply();
				},1);
				item.loading.estoque_inicial = false;
			})
			.error(function(data, status, headers, config) {
				item.qtd_estoque_inicial = 0;
				item.loading.estoque_inicial = false;
			});
	}

	ng.loadComprasProduto = function(item) {
		item.loading.compras = true;

		var dta_inicial = moment($("#dtaInicial").val(), 'DD/MM/YYYY').format('YYYY-MM-DD 00:00:00');
		var dta_final = moment($("#dtaFinal").val(), 'DD/MM/YYYY').format('YYYY-MM-DD 23:59:59');

		var queryString = "?dta_movimentacao[exp]=BETWEEN '"+dta_inicial+"' AND '"+dta_final+"'";

		aj.get(baseUrlApi() +"posicao-estoque/produto/"+ item.id_produto +"/compras"+ queryString)
			.success(function(data, status, headers, config) {
				item.qtd_compras_periodo = data.qtd_compras;
				setTimeout(function(){
					$scope.$apply();
				},1);
				item.loading.compras = false;
			})
			.error(function(data, status, headers, config) {
				item.qtd_compras_periodo = 0;
				item.loading.compras = false;
			});
	}

	ng.loadVendasProduto = function(item) {
		item.loading.vendas = true;

		var dta_inicial = moment($("#dtaInicial").val(), 'DD/MM/YYYY').format('YYYY-MM-DD 00:00:00');
		var dta_final = moment($("#dtaFinal").val(), 'DD/MM/YYYY').format('YYYY-MM-DD 23:59:59');

		var queryString = "?dta_movimentacao[exp]=BETWEEN '"+dta_inicial+"' AND '"+dta_final+"'";

		aj.get(baseUrlApi() +"posicao-estoque/produto/"+ item.id_produto +"/vendas"+ queryString)
			.success(function(data, status, headers, config) {
				item.qtd_vendas_periodo = data.qtd_vendas;
				setTimeout(function(){
					$scope.$apply();
				},1);
				item.loading.vendas = false;
			})
			.error(function(data, status, headers, config) {
				item.qtd_vendas_periodo = 0;
				item.loading.vendas = false;
			});
	}

	ng.loadBaixasProduto = function(item) {
		item.loading.baixas = true;

		var dta_inicial = moment($("#dtaInicial").val(), 'DD/MM/YYYY').format('YYYY-MM-DD 00:00:00');
		var dta_final = moment($("#dtaFinal").val(), 'DD/MM/YYYY').format('YYYY-MM-DD 23:59:59');

		var queryString = "?dta_movimentacao[exp]=BETWEEN '"+dta_inicial+"' AND '"+dta_final+"'";

		aj.get(baseUrlApi() +"posicao-estoque/produto/"+ item.id_produto +"/baixas"+ queryString)
			.success(function(data, status, headers, config) {
				item.qtd_desperdicios_periodo = data.qtd_baixas_manuais;
				setTimeout(function(){
					$scope.$apply();
				},1);
				item.loading.baixas = false;
			})
			.error(function(data, status, headers, config) {
				item.qtd_desperdicios_periodo = 0;
				item.loading.baixas = false;
			});
	}

	ng.getSaldoProduto = function(item) {
		return ((item.qtd_estoque_inicial + item.qtd_compras_periodo) - item.qtd_vendas_periodo - item.qtd_desperdicios_periodo);
	}

	ng.getDiferencaProduto = function(item) {
		return (item.qtd_sobra_periodo - ng.getSaldoProduto(item));
	}

	ng.new_posicao.produtos = [] ;
	ng.addProduto = function(item){
		var produto = angular.copy(item);
		
		produto.qtd_estoque_inicial = 0;
		produto.qtd_compras_periodo = 0;
		produto.qtd_vendas_periodo = 0;
		produto.qtd_desperdicios_periodo = 0;
		produto.qtd_sobra_periodo = 0;

		produto.loading = {
			estoque_inicial: false,
			compras: false,
			vendas: false,
			baixas: false
		};

		ng.new_posicao.produtos.push(produto);

		ng.loadEstoqueInicial(produto);
		ng.loadComprasProduto(produto);
		ng.loadVendasProduto(produto);
		ng.loadBaixasProduto(produto);
	}

	ng.produtoSelected = function(id){
		var r = false ;
		$.each(ng.new_posicao.produtos,function(i,x){
			if(Number(x.id_produto) == Number(id)){
				r = true ;
				return false ;
			}
		});
		return r ;
	}

	ng.selDepositoNewPosicao = function(){
		$('#list_depositos_new').modal('show');
		ng.loadDepositos(0,10);
	}

	ng.selDepositoBusca = function(){
		$('#list_depositos_busca').modal('show');
		ng.loadDepositos(0,10);
	}

	ng.addDepositoNewPosicao = function(item){
		ng.new_posicao.nome_deposito = item.nme_deposito;
		ng.new_posicao.id_deposito   = item.id;
		$('#list_depositos_new').modal('hide');
	}

	ng.addDepositoBusca = function(item){
		ng.busca.nome_deposito = item.nme_deposito;
		ng.busca.id_deposito   = item.id;
		$('#list_depositos_busca').modal('hide');
	}

	ng.loadDepositos = function(offset, limit) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;
		ng.depositos = null ;
		var query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento ;
		if(!empty(ng.busca.depositos))
			query_string  += "&"+$.param({nme_deposito:{exp:"like '%"+ng.busca.depositos+"%'"}});

    	aj.get(baseUrlApi()+"depositos/"+offset+"/"+limit+query_string)
		.success(function(data, status, headers, config) {
			ng.depositos = data.depositos ;
			ng.paginacao_depositos = data.paginacao ;
			if(ng.depositos.length == 1){
				ng.new_posicao.nome_deposito = ng.depositos[0].nme_deposito;
				ng.new_posicao.id_deposito   = ng.depositos[0].id;
			}
			
		})
		.error(function(data, status, headers, config) {
			ng.depositos = [] ;	
		});
	}

	ng.salvar = function(){
		var btn = $('#salvar-baixa-estoque');
		btn.button('loading');

		if (empty($("#dtaInicial").val())) {
			alert("Preencha a data inicial");
			btn.button('reset');
			return false;
		} else if (empty($("#dtaFinal").val())) {
			alert("Preencha a data final");
			btn.button('reset');
			return false;
		} else if (empty(ng.new_posicao.id_deposito)){
			alert("Escolha um depósito");
			btn.button('reset');
			return false;
		} else if (ng.new_posicao.produtos.length == 0) {
			alert("Selecione ao menos um produto");
			btn.button('reset');
			return false;
		}

		ng.new_posicao.id_empreendimento 		= ng.userLogged.id_empreendimento;
		ng.new_posicao.id_usuario_responsavel 	= ng.userLogged.id;
		ng.new_posicao.dta_inicial 				= (!empty($("#dtaInicial").val())) ? formatDate($("#dtaInicial").val()) : null;
		ng.new_posicao.dta_final 				= (!empty($("#dtaFinal").val())) ? formatDate($("#dtaFinal").val()) 	: null;

		if (ng.new_posicao.dta_inicial > ng.new_posicao.dta_final) {
    		alert("A data final deve ser maior que a data inicial");
    		btn.button('reset');
    		return false;
    	}

		aj.post(baseUrlApi()+"posicao-estoque/save",{ data: JSON.stringify( ng.new_posicao ) })
		.success(function(data, status, headers, config) {
			ng.mensagens('alert-success','Posição salva com sucesso','.alert-success-baixa');
			btn.button('reset');
			ng.new_posicao.produtos = [];
			$("#dtaInicial").val()
			$("#dtaFinal").val()
			ng.loadPosicoes(0,10);
			ng.reset();
		})
		.error(function(data, status, headers, config) {
			if(status == 406){
				$.each(data.out_estoque,function(i,x){	
					var msg = 'A quantidade solicitada ( '+x.qtd_saida+' ) é maior que a em estoque ( '+x.qtd_estoque+' )';			
					$('#tr-list-produtos-'+i).addClass('has-error');
					$('#tr-list-produtos-'+i).find('input').attr("data-placement", "top").attr("title", msg).attr("data-original-title", msg); 
					$('#tr-list-produtos-'+i).find('input').tooltip();
				});
			}
			btn.button('reset');
		});
	}
	ng.delProduto = function(index){
		ng.new_posicao.produtos.splice(index,1);
	}

	ng.loadDepositos();
	ng.loadPosicoes(0,10);

	$('#dtaInicial').on('change',function(event){
		ng.new_posicao.dta_inicial = $(this).val();
	});
	$('#dtaFinal').on('change',function(event){
		ng.new_posicao.dta_final = $(this).val();
	});
	$('#busca_dta_inicial').on('change',function(event){
		ng.busca.dta_inicial = $(this).val();
	});
	$('#busca_dta_final').on('change',function(event){
		ng.busca.dta_final = $(this).val();
	});
});


$("body").on("mouseenter","tr.has-error",function() {
  $(this).find('input').focus();
});

$("body").on("mouseleave","tr.has-error",function() {
  $(this).find('input').blur();
});
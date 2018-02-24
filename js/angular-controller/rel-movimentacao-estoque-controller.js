app.controller('RelatorioTotalProdutoEstoque', function($scope, $http, $window, UserService,$dialogs, EmpreendimentoService) {
	var ng 				= $scope,
		aj 				= $http;
	ng.userLogged 		= UserService.getUserLogado();
	ng.dados_empreendimento = EmpreendimentoService.getDadosEmpreendimento(ng.userLogged.id_empreendimento);
	ng.itensPorPagina 	= 10;
	ng.deposito 		= {};
	ng.depositos 		= [];
	ng.vendas 		   	= [];
	ng.paginacao 	   	= {};
	ng.busca			= {nome_produto:null,id_produto:null,qtd_produto:null,produto_modal:null,depositos:null,id_deposito:null,nome_deposito:null}
	ng.busca.clientes  	= '';
	ng.cliente          = {};
	ng.saldo_anterior   = false ;
	ng.movimentacoes    = false ;

	ng.doExportExcel = function(id_table){
    	$('#'+ id_table).tableExport({
    		filename: id_table, 
    		type:'excel', 
    		escape:'false'
    	});
    }

	ng.reset = function() {
			ng.busca			= {nome_produto:null,id_produto:null,qtd_produto:null,produto_modal:null,depositos:null,id_deposito:null,nome_deposito:null};
			ng.itensPorPagina   = 10 ;
	}

	ng.resetFilter = function() {
		ng.busca = {} ;
		ng.movimentacoes = false ;
	}

	ng.lengthObj = function(obj){
		if(typeof obj == 'object' && !empty(obj))
			return Object.keys(obj).length;
	}
	
	ng.aplicarFiltro = function() {
		if(!$.isNumeric(ng.busca.id_produto)){
			ng.movimentacoes    = false ;
			return;
		}
		$("#modal-aguarde").modal('show');
		ng.loadMovimentacao();
	}
	ng.grupo_busca     = '';
	ng.grupo_tabela    = 'produto';
	ng.busca_deposito  = false ; 
	ng. agrupar = false ;
	ng.loadProdutos = function(offset,limit) {
		offset = offset == null ? 0 : offset  ;
		limit  = limit == null ? 10 : limit  ;
		ng.produtos = [] ;
		var queryString = "";
		if(!empty(ng.busca.id_produto)){
			queryString += "?pro->id="+ng.busca.id_produto;
		}
		if(!empty(ng.busca.id_deposito)){
			queryString += empty(queryString) ? "?dep->id="+ng.busca.id_deposito : "&dep->id="+ng.busca.id_deposito;
		}if( (empty(ng.grupo_busca)) && (!empty(ng.busca.id_deposito)) ){
			var url = 'relatorio/produto/estoque/'+ng.userLogged.id_empreendimento+'/produto_deposito';
			ng. agrupar = false ;
		}else if(empty(ng.grupo_busca)){
			var url = 'relatorio/produto/estoque/'+ng.userLogged.id_empreendimento+'/produto';
			ng. agrupar = false ;
		}else if(!empty(ng.busca.id_deposito) && (ng.grupo_busca == 'produto') ){
			var url = 'relatorio/produto/estoque/'+ng.userLogged.id_empreendimento+'/produto_deposito';
			ng. agrupar = true ;
		}else if(!empty(ng.busca.id_deposito) && (ng.grupo_busca == 'deposito')){
			var url = 'relatorio/produto/estoque/'+ng.userLogged.id_empreendimento+'/produto_deposito';
			ng. agrupar = true ;
		}else{
			var url = 'relatorio/produto/estoque/'+ng.userLogged.id_empreendimento+'/deposito';
			ng. agrupar = ng.grupo_busca ;
		}


		url += queryString;


		aj.get(baseUrlApi()+url)
			.success(function(data, status, headers, config) {
				if(ng.agrupar){
					var aux = _.groupBy(data.produtos, ng.grupo_busca == 'produto' ?  "id_produto" : "nome_deposito");
					if(ng.grupo_busca == 'produto'){
						$.each(aux, function(i,v){
							$.each(data.produtos,function(x,y){
								if(Number(i) == y.id_produto ){
									aux[i].nome = y.nome;
									aux[i].nome_fabricante = y.nome_fabricante ;
									aux[i].peso  = y.peso ;
									return;
								}
							});
						});
					}
					ng.produtos = aux ;
					
				}else{
					ng.grupo_tabela = ng.grupo_busca ;
					ng.busca_deposito = empty(ng.busca.id_deposito) ? false : true ;
					ng.produtos = data.produtos;
					
				}
				ng.paginacao.produtos = data.paginacao ;
				$("#modal-aguarde").modal('hide');
			})
			.error(function(data, status, headers, config) {
				ng.produtos = [];
				ng.paginacao.produtos = [];
				$("#modal-aguarde").modal('hide');
			});
	}


	ng.modalProdutos = function(){
		ng.busca.produto_modal = '' ;
		$('#list_produtos').modal('show');
		ng.loadProdutosModal(0,10);
	}

	ng.loadProdutosModal = function(offset,limit) {
		ng.produtos_modal = null ;
		offset = offset == null ? 0 : offset  ;
		limit  = limit == null ? 10 : limit  ;
		var queryString = "?tpe->id_empreendimento="+ng.userLogged.id_empreendimento;

		if(ng.busca.produto_modal != ""){
			if(isNaN(Number(ng.busca.produto_modal)))
				queryString += "&("+$.param({nome:{exp:"like '%"+ng.busca.produto_modal+"%' OR codigo_barra like '%"+ng.busca.produto_modal+"%' OR fab.nome_fabricante like '%"+ng.busca.produto_modal+"%'"}})+")";
			else
				queryString += "&("+$.param({nome:{exp:"like '%"+ng.busca.produto_modal+"%' OR codigo_barra like '%"+ng.busca.produto_modal+"%' OR fab.nome_fabricante like '%"+ng.busca.produto_modal+"%' OR pro.id = "+ng.busca.produto_modal+""}})+")";
		}

		aj.get(baseUrlApi()+"produtos/"+offset+'/'+limit+"/"+queryString)
			.success(function(data, status, headers, config) {
				ng.produtos_modal = data.produtos;
				ng.paginacao.produtos_modal = data.paginacao ;

			})
			.error(function(data, status, headers, config) {
				ng.produtos_modal = [];
				ng.paginacao.produtos_modal = [];
			});
	}

	ng.addProduto = function(item){
		ng.busca.id_produto   = item.id;
		ng.busca.nome_produto = item.nome;
    	$('#list_produtos').modal('hide');
	}

	ng.modalDepositos = function(){
		$('#modal-depositos').modal('show');
		ng.loadDepositos(0,10);
	}
	
	ng.busca_vazia = {} ;
	ng.loadDepositos = function(offset, limit) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;
		ng.busca_vazia.depositos = false ;
		var query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento ;
		if(!empty(ng.busca.depositos))
			query_string  += "&"+$.param({nme_deposito:{exp:"like '%"+ng.busca.depositos+"%'"}});

    	aj.get(baseUrlApi()+"depositos/"+offset+"/"+limit+query_string)
		.success(function(data, status, headers, config) {
			ng.depositos = data.depositos ;	
			ng.paginacao.depositos = data.paginacao ;
		})
		.error(function(data, status, headers, config) {
			if(status != 404)
				alert("ocorreu um erro");
			else{
				ng.paginacao.depositos = [] ;
				ng.depositos = [] ;	
				ng.busca_vazia.depositos = true ;
			}
				
		});
	}

	ng.addDeposito = function(item){
		ng.busca.id_deposito   = item.id;
		ng.busca.nome_deposito = item.nme_deposito;
    	$('#modal-depositos').modal('hide');
	}

	ng.loadMovimentacao= function() {
		ng.saldo_anterior   = false ;
		//ng.movimentacoes = null ;
		var query_string = " tme.id_produto="+ng.busca.id_produto+"" ;
		var dta_saldo_anterior = "";

		if(!empty(ng.busca.deposito)){
			query_string += " AND tme.id_deposito="+ng.busca.deposito.id;
		}

		if(!empty(ng.busca.dta_inicial) && empty(ng.busca.dta_final)){
			dta_saldo_anterior = ng.busca.dta_inicial ;
			query_string = empty(query_string) ? 'WHERE '  : query_string ;
			query_string  += " date_format(dta_movimentacao,'%Y-%m-%d')>='"+ng.busca.dta_inicial+"'";
		}else if(!empty(ng.busca.dta_final) && empty(ng.busca.dta_inicial)){
			query_string = empty(query_string) ? 'WHERE '  : query_string ;
			query_string  += " date_format(dta_movimentacao,'%Y-%m-%d')<='"+ng.busca.dta_final+"'" ;
		}else if(!empty(ng.busca.dta_final) && !empty(ng.busca.dta_inicial)){
			dta_saldo_anterior = ng.busca.dta_inicial ;
			query_string = empty(query_string) ? 'WHERE '  : query_string ;
			query_string  += " dta_movimentacao BETWEEN '"+ng.busca.dta_inicial+" 00:00:00' AND '"+ng.busca.dta_final+" 23:59:59'" ;
		}
		query_string = "?cplSql= WHERE "+query_string+" ORDER BY dta_movimentacao ASC, tme.id ASC" ;
    	aj.get(baseUrlApi()+"movimentacao_estoque/"+query_string)
		.success(function(data, status, headers, config) {
			var saldo_anterior = 0 ;
			if(!empty(data.saldo_anterior) || (empty(data.saldo_anterior) && data.movimentacoes == 0)){
				ng.saldo_anterior   =  data.saldo_anterior ;
				saldo_anterior = data.saldo_anterior ;
			}
			var inventarios = [] ;
			$.each(data.movimentacoes,function(i,v){
				var dta = v.dta_movimentacao.split(' ');
				if(v.acao_movimentacao == 'INVENTÁRIO'){
					saldo_anterior += v.qtd_entrada;

					if(!empty(inventarios[v.id_deposito+"_"+v.dta_validade])){
						saldo_anterior -= inventarios[v.id_deposito+"_"+v.dta_validade];
						inventarios[v.id_deposito+"_"+v.dta_validade] = 0; 
					}
					if(empty(inventarios[v.id_deposito+"_"+v.dta_validade]))
						inventarios[v.id_deposito+"_"+v.dta_validade] = 0 ;

					inventarios[v.id_deposito+"_"+v.dta_validade] += Number(v.qtd_entrada);
				}else if(v.acao_movimentacao == 'ENTRADA'){
					saldo_anterior += v.qtd_entrada ;
					if(empty(inventarios[v.id_deposito+"_"+v.dta_validade]))
						inventarios[v.id_deposito+"_"+v.dta_validade] = 0 ;
					inventarios[v.id_deposito+"_"+v.dta_validade] += Number(v.qtd_entrada);
				}else if(v.acao_movimentacao == 'SAIDA'){
					saldo_anterior -= v.qtd_saida ;
					if(!empty(inventarios[v.id_deposito+"_"+v.dta_validade])){
						inventarios[v.id_deposito+"_"+v.dta_validade] -= Number(v.qtd_saida) ; 
					}
				}
				data.movimentacoes[i].dta = dta[0];
				data.movimentacoes[i].total = saldo_anterior;
				ng.saldo_produto = saldo_anterior;
			});	
			
			ng.movimentacoes =  _.groupBy(data.movimentacoes, "dia_movimentacao"); 
			
			$("#modal-aguarde").modal('hide');
		})
		.error(function(data, status, headers, config) {
			ng.movimentacoes = [] ;
			$("#modal-aguarde").modal('hide');
		});
	}

	ng.modalDepositos = function(){
		$('#modal-depositos').modal('show');
		ng.loadDepositos(0,10);
	}

	ng.depositos = {itens:null,paginacao:[]};
	ng.loadDepositos = function(offset, limit) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;
		ng.depositos = {itens:null,paginacao:[]};
		var query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento ;
		if(!empty(ng.busca.depositos))
			query_string  += "&"+$.param({nme_deposito:{exp:"like '%"+ng.busca.depositos+"%'"}});

    	aj.get(baseUrlApi()+"depositos/"+offset+"/"+limit+query_string)
		.success(function(data, status, headers, config) {
			ng.depositos = {itens:data.depositos,paginacao:data.paginacao};
		})
		.error(function(data, status, headers, config) {
			ng.depositos = {itens:[],paginacao:[]};
		});
	}

	ng.addDeposito = function(item){
		ng.busca.deposito = item ;
		$('#modal-depositos').modal('hide');
	}

	ng.configuracao = null ;


	ng.reset();
	//ng.aplicarFiltro();
});
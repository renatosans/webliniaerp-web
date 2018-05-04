app.controller('RelatorioVendasHoraAHoraController', function($scope, $http, $window, UserService,EmpreendimentoService) {
	var ng 				= $scope,
		aj 				= $http;
	ng.userLogged 		= UserService.getUserLogado();
	ng.dados_empreendimento = EmpreendimentoService.getDadosEmpreendimento(ng.userLogged.id_empreendimento);
	ng.itensPorPagina 	= 10;
	ng.deposito 		= {};
	ng.depositos 		= [];
	ng.vendas 		   	= [];
	ng.paginacao 	   	= {};
	ng.busca            ={
							fornecedores:'',
							id_forma_pagamento:'',
							agrupar:false,
							status_pagamento:''
						}
	ng.cliente          = {};
	var buscaExport     = {};

	 ng.formas_pagamento = [
		{nome:"Cheque",id:2},
		{nome:"Dinheiro",id:3},
		{nome:"Boleto Bancário",id:4},
		{nome:"Cartão de Débito",id:5},
		{nome:"Cartão de Crédito",id:6},
		{nome:"Transferência",id:8}
	  ]


	ng.reset = function() {
		 $("#dtaInicial").val('');
		 $("#dtaFinal").val('');
		 ng.vendas = [];
	}

	ng.resetFilter = function() {
		ng.reset();
	}

	ng.vendas = null ;
	ng.loadVendas = function() {
		var dtaInicial  = $("#dtaInicial").val();
		var dtaFinal    = $("#dtaFinal").val();
		if(empty(dtaInicial) && empty(dtaFinal)){
			$('.alert-periodo').show();
			return ;
		}

		$('.alert-periodo').hide();

		buscaExport = {};
		ng.vendas = [] ;
		$("#modal-aguarde").modal('show');
		var queryString = "?id_empreendimento="+ng.userLogged.id_empreendimento;

		if(dtaInicial != "" && dtaFinal != ""){
			dtaInicial = moment(dtaInicial,'MM/YYYY').format('YYYY-MM');
			dtaFinal   =  moment(dtaFinal,'MM/YYYY').format('YYYY-MM');
			//queryString += "&"+$.param({"date_format(data_pagamento,'%Y-%m')":{exp:"BETWEEN '"+dtaInicial+"' AND '"+dtaFinal+"'"}});

		}else if(dtaInicial != ""){
			dtaInicial = moment(dtaInicial,'MM/YYYY').format('YYYY-MM');
			dtaFinal = 'null' ;
			//queryString += "&"+$.param({data_pagamento:{exp:">='"+dtaInicial+"'"}});
		}else if(dtaFinal != ""){
			dtaInicial = 'null';
			dtaFinal =  moment(dtaFinal,'MM/YYYY').format('YYYY-MM');
			//queryString += "&"+$.param({data_pagamento:{exp:"<='"+dtaFinal+"'"}});
		}

		var url = baseUrlApi()+"relatorio/venda_hora_a_hora/"+ng.userLogged.id_empreendimento+"/"+dtaInicial+"/"+dtaFinal;

		ng.vendas = [] ;

		aj.get(url)
			.success(function(data, status, headers, config) {
				ng.vendas = data.horas;
				$("#modal-aguarde").modal('hide');

			})
			.error(function(data, status, headers, config) {
				ng.vendas = false;
				$("#modal-aguarde").modal('hide');
				if (status != 404) {
					alert("Ocorreu um erro ao carregar o relatorio");
				};
			});
	}

	ng.selFornecedor = function(){
		var offset = 0  ;
    	var limit  =  10;

			ng.loadFornecedor(offset,limit);
			$("#list_fornecedores").modal("show");
	}

	ng.fornecedor = {} ;
	ng.addFornecedor = function(item){
    	ng.fornecedor 				= item;
    	$("#list_fornecedores").modal("hide");
	}

	ng.loadFornecedor = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.fornecedores = null;
		var query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento ;
		if(ng.busca.fornecedores != ""){
			query_string += "&"+$.param({'frn->nome_fornecedor':{exp:"like'%"+ng.busca.fornecedores+"%'"}});
		}

		aj.get(baseUrlApi()+"fornecedores/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.fornecedores 		  = data.fornecedores;
				ng.paginacao_fornecedores = data.paginacao ;
			})
			.error(function(data, status, headers, config) {
				ng.fornecedores = [];
			});
	}

	ng.export = function(){
		var url = baseUrlApi()+"export/PagamentoFornecedorDao";
		if(ng.busca.agrupar){
			var head  = {id_fornecedor:{name:'ID fornecedor'}, nome_fornecedor:{name:'Nome Fornecedor'}, qtd_pagamento:{name:'Qtd. pagamentos'}, valor_pagamento:{name:'Valor dos Pagamentos','function':[['number_format',['${value}',2,',','.']]] }};
			params = {params:['null','null',buscaExport],head:head,exception:[{format:['descricao_forma_pagamento','valor'],values:['','','TOTAL A PAGAR NO PERÍODO',{value:'${valor}','function':{valor:[['number_format',['${value}',2,',','.']]]}}]}]};
			 url  += "/pagamentoFornecedorGroup/relatorio_pagamentos_fornecedor?"+$.param(params); 
		}else{
			var head  = { id_pagamento: {name:'ID Pagamento'},id_fornecedor:{name:'ID fornecedor'}, nome_fornecedor:{name:'Nome Fornecedor'},data_pagamento:{name:'Data do Pagamento','function':[['strtotime',['${value}']],['date',['d/m/Y','${value}']]]},descricao_forma_pagamento: {name:'Forma de Pagamento'},status_pagamento:{name:'Status','function':[['equal',['${value}',['Pendente','Pago']]]]},valor_pagamento:{name:'Valor do Pagamento','function':[['number_format',['${value}',2,',','.']]]}};
			params = {params:['null','null',buscaExport],head:head,exception:[ {format:['descricao_forma_pagamento','data','valor'],values:['','','','','',{value:'TOTAL A PAGAR PARA O DIA ${data}','function':{data:[['strtotime',['${value}']],['date',['d/m/Y','${value}']]]}},{value:'${valor}','function':{valor:[['number_format',['${value}',2,',','.']]]}}]},{format:['descricao_forma_pagamento','valor'],values:['','','','','','TOTAL A PAGAR NO PERÍODO',{value:'${valor}','function':{valor:[['number_format',['${value}',2,',','.']]]}}]}]};
			url  += "/pagamentoFornecedor/relatorio_pagamentos_fornecedor?"+$.param(params);
		}

		location.href=url;
	}

	ng.lengthObject = function(obj){
		return Object.keys(obj).length ;
	}

	ng.loadConfig = function(){
		var error = 0 ;
		aj.get(baseUrlApi()+"configuracoes/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.configuracoes = data ;
				//ng.loadDRE(true);
			})
			.error(function(data, status, headers, config) {

			});
	}
	ng.loadConfig();
});

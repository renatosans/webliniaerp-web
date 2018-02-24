app.controller('RelatorioContasPagar', function($scope, $http, $window, UserService, EmpreendimentoService, ConfigService) {
	var ng 				= $scope,
		aj			    = $http;
	ng.userLogged 		= UserService.getUserLogado();
	ng.dados_empreendimento = EmpreendimentoService.getDadosEmpreendimento(ng.userLogged.id_empreendimento);
	ng.configuracoes	 		= ConfigService.getConfig(ng.userLogged.id_empreendimento);
	ng.itensPorPagina	= 10;
	ng.deposito 		= {};
	ng.depositos	 	= [];
	ng.itens 			= [];
	ng.paginacao  		= {};
	ng.busca      		= {clientes:''};
	ng.cliente    		= {};
	ng.fornecedor 		= {};
	ng.pagamentos 		= null;

	var params = getUrlVars();

	ng.reset = function() {
		 $("#dtaInicial").val('');
		 $("#dtaFinal").val('');
		 ng.fornecedor = {} ;
		 ng.busca.fornecedores = '' ;
	}

	ng.resetFilter = function() {
		ng.reset();
		ng.msg_error = null;
	}

	ng.doExportExcel = function(id_table){
    	$('#'+ id_table).tableExport({
    		filename: id_table, 
    		type:'excel', 
    		escape:'false'
    	});
    }

	ng.aplicarFiltro = function() {
		$("#modal-aguarde").modal('show');
		ng.loadPagamentos(0,ng.itensPorPagina);
	}

	ng.loadPagamentos = function(offset,limit) {
		ng.msg_error = null;
		var dtaInicial  = $("#dtaInicial").val();
		var dtaFinal    = $("#dtaFinal").val();
		var queryString = "?pag->id_empreendimento="+ng.userLogged.id_empreendimento+"&pag->status_pagamento=0"+"&pag->flg_excluido=0";

		if(dtaInicial != "" && dtaFinal != ""){
			dtaInicial = formatDate(dtaInicial);
			dtaFinal   = formatDate(dtaFinal);
			queryString += "&"+$.param({data_pagamento:{exp:"BETWEEN '"+dtaInicial+" 00:00:00' AND '"+dtaFinal+" 23:59:59'"}});
		}else if(dtaInicial != ""){
			dtaInicial = formatDate(dtaInicial);
			queryString += "&"+$.param({data_pagamento:{exp:">='"+dtaInicial+"'"}});
		}else if(dtaFinal != ""){
			dtaFinal = formatDate(dtaFinal);
			queryString += "&"+$.param({data_pagamento:{exp:"<='"+dtaFinal+"'"}});
		}

		if(ng.fornecedor.id == null || ng.fornecedor.id == ""){
			queryString += "&pag->id_fornecedor[exp]=<>"+ng.configuracoes.id_fornecedor_movimentacao_caixa;
		}

		if(ng.fornecedor.id != null && ng.fornecedor.id != ""){
			queryString += "&frn->id="+ng.fornecedor.id;
		}

		aj.get(baseUrlApi()+"pagamentos/fornecedores/"+queryString)
			.success(function(data, status, headers, config) {
				ng.pagamentos 			 = data.pagamentos;
				ng.calTotal();
				ng.vlr_total = 0;
				$.each(ng.pagamentos,function(index,item){
					ng.vlr_total += item.valor_pagamento;
				});

				$("#modal-aguarde").modal('hide');
			})
			.error(function(data, status, headers, config) {
				ng.pagamentos = null;
				ng.status = status;
				ng.msg_error = data;
				ng.calTotal();
				$("#modal-aguarde").modal('hide');
			});
	}

	ng.calSubTotal = function(obj){
		var sub_total = 0 ;
		$.each(obj,function(key,value){
			$.each(value,function(i,item){
				sub_total += item.valor_pagamento;
			});
			obj[key].sub_total = sub_total ;
			sub_total = 0 ;
		});
		return obj;
	}

	ng.calTotal = function(){
		var total =  0 ;
		if(!empty(ng.pagamentos)) {
			$.each(ng.pagamentos,function(key,value){
				total += value.sub_total;
			});
		}
		ng.total = total ;
	}

	ng.addFornecedor = function(item){

    	ng.fornecedor = item;
    	$("#list_fornecedores").modal("hide");
	}

	ng.selFornecedor = function(){
			ng.loadFornecedores();
			$("#list_fornecedores").modal("show");
	}

	ng.loadFornecedores = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;
		ng.fornecedores = [];
		aj.get(baseUrlApi()+"fornecedores?id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.fornecedores = data.fornecedores;
			})
			.error(function(data, status, headers, config) {

			});
	}



	ng.reset();
	ng.loadPagamentos(0,10);

});

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

	if(!empty(params.dtaInicial)) ng.stardate_dtaInicial = moment(params.dtaInicial).format('DD/MM/YYYY') ;
	if(!empty(params.dtaFinal)) ng.stardate_dtaFinal = moment(params.dtaFinal).format('DD/MM/YYYY') ;

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

	ng.aplicarFiltro = function(dtaInicial,dtaFinal) {
		$("#modal-aguarde").modal('show');
		if(!empty(dtaInicial) || !empty(dtaFinal)){
			ng.loadPagamentos(0,ng.itensPorPagina, dtaInicial,dtaFinal);
			ng.busca.dtaInicial = dtaInicial;
			ng.busca.dtaFinal = dtaFinal;
		} else{
			dtaInicial  = formatDate($("#dtaInicial").val());
			dtaFinal    = formatDate($("#dtaFinal").val());
			ng.loadPagamentos(0,ng.itensPorPagina, dtaInicial,dtaFinal);
		}
	}

	ng.loadPagamentos = function(offset,limit,dtaInicial,dtaFinal) {
		ng.msg_error = null;

		dtaInicial = empty(dtaInicial) ? ng.busca.dtaInicial : dtaInicial;
		dtaFinal   = empty(dtaFinal)   ? ng.busca.dtaFinal   : dtaFinal;
		var queryString = "?pag->id_empreendimento="+ng.userLogged.id_empreendimento+"&pag->status_pagamento=0"+"&pag->flg_excluido=0";

		if(dtaInicial != "" && dtaFinal != ""){
			queryString += "&"+$.param({data_pagamento:{exp:"BETWEEN '"+dtaInicial+" 00:00:00' AND '"+dtaFinal+" 23:59:59'"}});
		}else if(dtaInicial != ""){
			queryString += "&"+$.param({data_pagamento:{exp:">='"+dtaInicial+"'"}});
		}else if(dtaFinal != ""){
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

	if(!empty(params.dtaInicial) ||  !empty(params.dtaFinal)){
		ng.aplicarFiltro(params.dtaInicial,params.dtaFinal) ;
	}

	ng.reset();

});

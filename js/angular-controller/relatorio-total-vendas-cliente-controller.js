app.controller('RelatorioTotalVendasClienteController', function($scope, $http, $window, UserService, EmpreendimentoService) {
	var ng 				= $scope,
		aj 				= $http;
	ng.userLogged 		= UserService.getUserLogado();
	ng.dados_empreendimento = EmpreendimentoService.getDadosEmpreendimento(ng.userLogged.id_empreendimento);
	ng.itensPorPagina 	= 10;
	ng.deposito 		= {};
	ng.depositos 		= [];
	ng.vendas 		   	= null;
	ng.paginacao 	   	= {};
	ng.busca			= {};
	ng.busca.clientes  	= '';
	ng.cliente          = {};

	ng.doExportExcel = function(id_table){
    	$('#'+ id_table).tableExport({
    		filename: id_table, 
    		type:'excel', 
    		escape:'false'
    	});
    }

	ng.reset = function() {
		 $("#dtaInicial").val('');
		 $("#dtaFinal").val('');
		 ng.cliente = {} ;
		 ng.busca.clientes = '' ;
   		ng.msg_error = null;

	}

	ng.resetFilter = function() {
		ng.reset();
	}

	ng.aplicarFiltro = function() {
		$("#modal-aguarde").modal('show');
		ng.loadVendas(0,ng.itensPorPagina);
	}

	ng.loadVendas = function(offset,limit) {
		var dtaInicial  = $("#dtaInicial").val();
		var dtaFinal    = $("#dtaFinal").val();
		var queryString = "?ven->id_empreendimento="+ng.userLogged.id_empreendimento;

		if(dtaInicial != "" && dtaFinal != ""){
			dtaInicial = formatDate(dtaInicial);
			dtaFinal   = formatDate(dtaFinal);
			queryString += "&"+$.param({dta_venda:{exp:"BETWEEN '"+dtaInicial+" 00:00:00' AND '"+dtaFinal+" 23:59:59'"}});
		}else if(dtaInicial != ""){
			dtaInicial = formatDate(dtaInicial);
			queryString += "&"+$.param({dta_venda:{exp:">='"+dtaInicial+"'"}});
		}else if(dtaFinal != ""){
			dtaFinal = formatDate(dtaFinal);
			queryString += "&"+$.param({dta_venda:{exp:"<='"+dtaFinal+"'"}});
		}

		if(ng.cliente.id != "" && ng.cliente.id != null){
			queryString += "&usu->id="+ng.cliente.id;
		}

		aj.get(baseUrlApi()+"relatorio/vendas/consolidado/cliente/"+offset+'/'+limit+"/"+queryString)
			.success(function(data, status, headers, config) {
				ng.vendas = data.vendas;
				ng.paginacao.vendas = data.paginacao ;
				$("#modal-aguarde").modal('hide');

			})
			.error(function(data, status, headers, config) {
				ng.vendas = null;
				ng.status = status;
				ng.msg_error = data;
				ng.paginacao.vendas = [];
				$("#modal-aguarde").modal('hide');
			});
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
		query_string = "?tue->id_empreendimento="+ng.userLogged.id_empreendimento;
		if(ng.busca.clientes != ""){
			query_string += "&"+$.param({'(usu->nome':{exp:"like'%"+ng.busca.clientes+"%')"}});
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


	ng.reset();
	ng.aplicarFiltro();
});

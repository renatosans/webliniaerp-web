app.controller('RelatorioContasReceber', function($scope, $http, $window, UserService) {
	var ng = $scope,
		aj = $http;
	ng.userLogged = UserService.getUserLogado();
	ng.itensPorPagina = 10;
	ng.deposito = {};
	ng.depositos = [];
	ng.itens = [];
	ng.paginacao = {};
	ng.busca     = {clientes:''};
	ng.cliente   = {};

	var params = getUrlVars();

	ng.reset = function() {
		 $("#dtaInicial").val('');
		 $("#dtaFinal").val('');
		 ng.cliente = {} ;
		 ng.busca.clientes = '' ;
	}

	ng.resetFilter = function() {
		ng.reset();
		ng.aplicarFiltro();
	}

	ng.aplicarFiltro = function() {
		$("#modal-aguarde").modal('show');
		ng.loadPagamentos(0,ng.itensPorPagina);
	}

	ng.loadPagamentos = function(offset,limit) {
		var dtaInicial  = $("#dtaInicial").val();
		var dtaFinal    = $("#dtaFinal").val();
		var queryString = "?pag->id_empreendimento="+ng.userLogged.id_empreendimento;
			queryString += "&pag->status_pagamento=0";
			queryString += "&cnt->id_tipo_conta[exp]=<> 5";

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

		if(ng.cliente.id != null && ng.cliente.id != ""){
			queryString += queryString == "" ? "?usu->id="+ng.cliente.id : "&usu->id="+ng.cliente.id;
		}

		aj.get(baseUrlApi()+"pagamentos/clientes/"+offset+'/'+limit+"/"+queryString)
			.success(function(data, status, headers, config) {
				ng.pagamentos 			 = data.pagamentos;
				ng.paginacao.pagamentos  = data.paginacao ;
				ng.calTotal();
				$("#modal-aguarde").modal('hide');
			})
			.error(function(data, status, headers, config) {
				ng.pagamentos = [] ;
				ng.paginacao.pagamentos = [];
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
		$.each(ng.pagamentos,function(key,value){
			total += value.sub_total;
		});
		ng.total = total ;
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
			query_string += "&"+$.param({'usu->nome':{exp:"like'%"+ng.busca.clientes+"%'"}});
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

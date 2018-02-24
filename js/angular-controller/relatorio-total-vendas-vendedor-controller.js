app.controller('RelatorioTotalVendasVendedorController', function($scope, $http, $window, UserService,FuncionalidadeService, EmpreendimentoService) {
	var ng 				= $scope,
		aj 				= $http;
	ng.userLogged 		= UserService.getUserLogado();
	ng.dados_empreendimento = EmpreendimentoService.getDadosEmpreendimento(ng.userLogged.id_empreendimento);
	ng.itensPorPagina 	= 10;
	ng.vendas 		   	= null;
	ng.paginacao 	   	= {};
	ng.busca			= {}
	ng.busca.vendedores  = '';
	ng.vendedor          = {};

	ng.doExportExcel = function(id_table){
    	$('#'+ id_table).tableExport({
    		filename: id_table, 
    		type:'excel', 
    		escape:'false'
    	});
    }

	ng.funcioalidadeAuthorized = function(cod_funcionalidade){
	return FuncionalidadeService.Authorized(cod_funcionalidade,ng.userLogged.id_perfil,ng.userLogged.id_empreendimento);
}

	ng.reset = function() {
		 $("#dtaInicial").val('');
		 $("#dtaFinal").val('');
		 ng.vendedor = {} ;
		 ng.busca.vendedores = '' ;
		 ng.msg_error = null;
	}

	ng.resetFilter = function() {
		ng.reset();
		ng.aplicarFiltro();
	}

	ng.aplicarFiltro = function() {
		if( !empty(getUrlVars()) && !empty(getUrlVars().dtaInicial) && !empty(getUrlVars().dtaFinal)) {
			$("#dtaInicial").val(getUrlVars().dtaInicial);
			$("#dtaFinal").val(getUrlVars().dtaFinal);
		}

		$("#modal-aguarde").modal('show');
		ng.loadVendas(0,ng.itensPorPagina);
	}

	ng.loadVendas = function(offset,limit) {
		ng.vendas = [];
		var dtaInicial  = $("#dtaInicial").val();
		var dtaFinal    = $("#dtaFinal").val();
		var queryString = "?ven->id_empreendimento="+ng.userLogged.id_empreendimento;

		queryString += !ng.funcioalidadeAuthorized('listar_todas_vendas_vendedores') ? '&ven->id_usuario='+ng.userLogged.id : '' ;

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

		if(ng.vendedor.id != "" && ng.vendedor.id != null){
			queryString += "&vdd->id="+ng.vendedor.id;
		}

		aj.get(baseUrlApi()+"relatorio/vendas/consolidado/vendedor/"+offset+'/'+limit+"/"+queryString)
			.success(function(data, status, headers, config) {
				ng.vendas = data.vendas;
				ng.paginacao.vendas = data.paginacao ;
				$("#modal-aguarde").modal('hide');

			})
			.error(function(data, status, headers, config) {
				$("#modal-aguarde").modal('hide');
				ng.vendas = null;
				ng.status = status;
				ng.msg_error = data;
				ng.paginacao.vendas = [];
				ng.paginacao.vendas = null;
			});
	}

	ng.selCliente = function(){
		var offset = 0  ;
    	var limit  =  10 ;;

		ng.loadCliente(offset,limit);
		$("#list_clientes").modal("show");
	}


	ng.addCliente = function(item){
    	ng.vendedor = item;
    	$("#list_clientes").modal("hide");
	}

	ng.loadCliente= function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.vendedores = [];
		query_string = "?tue->id_empreendimento="+ng.userLogged.id_empreendimento;

		query_string += "&usu->flg_tipo=usuario" ;

		if(ng.busca.vendedores != ""){
			query_string += "&" + $.param({'(usu->nome':{exp:"like'%"+ng.busca.vendedores+"%')"}});
		}
		aj.get(baseUrlApi()+"usuarios/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				$.each(data.usuarios,function(i,item){
					ng.vendedores.push(item);
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
	ng.aplicarFiltro(0,10);
});

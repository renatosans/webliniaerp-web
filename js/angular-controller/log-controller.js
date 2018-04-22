app.controller('LogController', function($scope, $http, $window, UserService,FuncionalidadeService, EmpreendimentoService) {
	var ng 				= $scope,
		aj 				= $http;
	ng.userLogged 		= UserService.getUserLogado();
	ng.dados_empreendimento = EmpreendimentoService.getDadosEmpreendimento(ng.userLogged.id_empreendimento);
	ng.itensPorPagina 	= 10;
	ng.vendas 		   	= null;
	ng.paginacao 	   	= {};
	ng.busca			= {};
	ng.busca_modal		= {};

    ng.tipos_log = [
    	{ value: null	, dsc:'Selecione'},
    	{ value:'1'		, dsc:'Cancelamento de Orçamento/Comanda'},
    	{ value:'2'		, dsc:'Cancelamento de Venda'},
    	{ value:'3'		, dsc:'Cancelamento de Pagamento'},
    	{ value:'4'		, dsc:'Exclusão de Item'}
	];

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
	
	ng.selCliente = function(){
		ng.loadCliente(0,10);
		$("#list_clientes").modal("show");
	}

	ng.addCliente = function(item){
		ng.usuario_responsavel = item.nome;
		ng.busca.id_usuario_responsavel = item.id;
    	$("#list_clientes").modal("hide");
	}

	ng.loadCliente = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		
		query_string = "?tue->id_empreendimento="+ng.userLogged.id_empreendimento;

		query_string += "&usu->flg_tipo=usuario" ;

		if(!empty(ng.busca_modal.vendedores)){
			query_string += "&" + $.param({'(usu->nome':{exp:"like'%"+ng.busca_modal.vendedores+"%')"}});
		}
		aj.get(baseUrlApi()+"usuarios/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.vendedores = [];
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

	ng.aplicarFiltro = function() {
		ng.loadLog();
		ng.msg_error = null;
	}

	ng.loadLog = function(){
		var dtaInicial  = $("#dtaInicial").val();
		var dtaFinal    = $("#dtaFinal").val();
		var queryString = "?log->id_empreendimento="+ ng.userLogged.id_empreendimento;

		if (dtaInicial == "" || dtaInicial == null) {
			alert('Preencha a data Inicial');
			return false;
		} else if (dtaFinal == "" || dtaFinal == null) {
			alert('Preencha a data Final');
			return false;
		} else if (ng.busca.id_usuario_responsavel == "" || ng.busca.id_usuario_responsavel == null) {
			alert('Escolha um usuário responsável');
			return false;
		} else if (ng.busca.tipo_log == "" || ng.busca.tipo_log == null) {
			alert('Escolha um tipo de log');
			return false;
		}

		if (ng.busca.id_usuario_responsavel != "") {
			queryString += "&log->id_usuario="+ng.busca.id_usuario_responsavel;
		}

		if (ng.busca.tipo_log != "") {
			queryString += "&log->id_tipo_log="+ng.busca.tipo_log;
		}

		if(dtaInicial != "" && dtaFinal != "") {
			dtaInicial = formatDate(dtaInicial);
			dtaFinal   = formatDate(dtaFinal);
			queryString += "&"+$.param({'log->datetime':{exp:"BETWEEN '"+dtaInicial+" 00:00:00' AND '"+dtaFinal+" 23:59:59'"}});
		} else if(dtaInicial != "") {
			dtaInicial = formatDate(dtaInicial);
			queryString += "&"+$.param({'log->datetime':{exp:">='"+dtaInicial+"'"}});
		} else if(dtaFinal != "") {
			dtaFinal = formatDate(dtaFinal);
			queryString += "&"+$.param({'log->datetime':{exp:"<='"+dtaFinal+"'"}});
		}

		aj.get(baseUrlApi()+"log"+ queryString)
		.success(function(data, status, headers, config) {
			ng.list_log = data;
		})
		.error(function(data, status, headers, config) {
			ng.list_log = null;
			ng.msg_error = data;
		});
	}

	ng.reset = function() {
		 $("#dtaInicial").val('');
		 $("#dtaFinal").val('');
		 ng.busca = {};
		 ng.usuario_responsavel = null;
		 ng.msg_error = null;
		 ng.list_log = null;
	}

	ng.resetFilter = function() {
		ng.reset();
	}

	ng.reset();
});

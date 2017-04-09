app.controller('RelatorioSaldoDevedorClienteController', function($scope, $http, $window, UserService, EmpreendimentoService) {
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

	ng.reset = function() {
		 $("#dtaInicial").val('');
		 $("#dtaFinal").val('');
		 ng.vendedor = {} ;
		 ng.busca.vendedores = '' ;
	}

	ng.resetFilter = function() {
		ng.reset();
		ng.aplicarFiltro();
	}

	ng.aplicarFiltro = function() {
		$("#modal-aguarde").modal('show');
		ng.loadVendas(0,ng.itensPorPagina);
	}

	ng.loadVendas = function(offset,limit) {
		ng.vendas = [];
		var dtaInicial  = $("#dtaInicial").val();
		var dtaFinal    = $("#dtaFinal").val();
		var queryString = "?(usu->id[exp]= NOT IN("+ng.configuracao.id_cliente_movimentacao_caixa+","+ng.configuracao.id_usuario_venda_vitrine+"))";
		queryString      += "&having=vlr_saldo_devedor<0";
		if(ng.vendedor.id != "" && ng.vendedor.id != null){
			queryString += "&usu->id="+ng.vendedor.id;
		}

		aj.get(baseUrlApi()+"usuarios/saldodevedor/"+ng.userLogged.id_empreendimento+"/"+queryString)
			.success(function(data, status, headers, config) {
				if(ng.vendedor.id != "" && ng.vendedor.id != null)
					ng.vendas.push(data);
				else
					ng.vendas = data;

				if(data == false)
					ng.vendas = null;
				
				$("#modal-aguarde").modal('hide');

			})
			.error(function(data, status, headers, config) {
				$("#modal-aguarde").modal('hide');
				ng.vendas = [];
			});
	}

	ng.loadConfig = function(){
		var error = 0 ;
		aj.get(baseUrlApi()+"configuracoes/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.configuracao = data ;
				ng.loadVendas();
			})
			.error(function(data, status, headers, config) {
				ng.loadVendas();
				if(status == 404){
					ng.configuracao = false ;
				}
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
	ng.loadConfig();
});

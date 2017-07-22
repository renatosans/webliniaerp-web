app.controller('RelatorioTotalVendasDepositoController', function($scope, $http, $window, UserService,FuncionalidadeService, EmpreendimentoService) {
	var ng 				= $scope,
		aj 				= $http;
	ng.userLogged 		= UserService.getUserLogado();
	ng.dados_empreendimento = EmpreendimentoService.getDadosEmpreendimento(ng.userLogged.id_empreendimento);
	ng.itensPorPagina 	= 10;
	ng.vendas 		   	= null;
	ng.paginacao 	   	= {};
	ng.busca			= {}
	ng.busca.vendedores  = '';

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

	ng.showProdutos = function(){
   		ng.busca.produtos = "" ;
   		ng.loadProdutos(0,10);
   		$('#list_produtos').modal('show');
   	}

	ng.loadProdutos = function(offset,limit) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 20 : limit;

		var query_string = "?group=&emp->id_empreendimento="+ng.userLogged.id_empreendimento;

		if(ng.busca.produtos != ""){
			query_string += "&("+$.param({'prd->nome':{exp:"like'%"+ng.busca.produtos+"%' OR fab.nome_fabricante like'%"+ng.busca.produtos+"%'"}})+")";
		}

		ng.produtos = [];
		aj.get(baseUrlApi()+"estoque/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.produtos        = data.produtos ;
				ng.paginacao.produtos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.produtos = [];
			});
	}

	ng.addProduto = function(item){
		ng.produto = item ;
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
		ng.deposito = item;
    	$('#modal-depositos').modal('hide');
	}

	ng.reset = function() {
		 $("#dtaInicial").val('');
		 $("#dtaFinal").val('');
		 ng.deposito = null;
		 ng.produto = null;
		 ng.vendas = null;
		 ng.paginacao.vendas = null;
		 ng.msg_error = null;
	}

	ng.resetFilter = function() {
		ng.reset();
	}

	ng.aplicarFiltro = function() {
		ng.loadVendas(0,ng.itensPorPagina);
		ng.msg_error = null;
	}

	ng.loadVendas = function(offset,limit) {
		
		var dtaInicial  = $("#dtaInicial").val();
		var dtaFinal    = $("#dtaFinal").val();

		if(empty(dtaInicial)){
			alert('Você deve preencher a data inicial');
			return;
		}

		if(empty(dtaFinal)){
			alert('Você deve preencher a data final');
			return;
		}

		var queryString = "?tde->id_empreendimento="+ng.userLogged.id_empreendimento;
			queryString += "&"+$.param({'ven->dta_venda':{exp:"BETWEEN '"+ moment(dtaInicial, 'DD/MM/YYYY').format('YYYY-MM-DD') +" 00:00:00' AND '"+ moment(dtaFinal, 'DD/MM/YYYY').format('YYYY-MM-DD') +" 23:59:59'"}});

		if(!empty(ng.deposito))
			queryString += "&dep->id=" + ng.deposito.id;

		if(!empty(ng.produto))
			queryString += "&itv->id_produto=" + ng.produto.id_produto;

		$("#modal-aguarde").modal('show');

		ng.vendas = [];

		aj.get(baseUrlApi()+"relatorio/vendas/deposito/"+offset+'/'+limit+queryString)
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
				ng.paginacao.vendas = null;
			});
	}

	ng.reset();
});

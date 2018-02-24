app.controller('RelatorioVendasFabricante', function($scope, $http, $window, UserService,ConfigService, FuncionalidadeService, EmpreendimentoService) {
	var ng 				= $scope,
		aj 				= $http;
	ng.userLogged 		= UserService.getUserLogado();
	ng.config     = ConfigService.getConfig(ng.userLogged.id_empreendimento);
	ng.dados_empreendimento = EmpreendimentoService.getDadosEmpreendimento(ng.userLogged.id_empreendimento);
	ng.itensPorPagina 	= 10;
	ng.vendas 		   	= null;
	ng.paginacao 	   	= {};
	ng.busca			= {}
	ng.busca.vendedores  = '';
	ng.asd = {};

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

	ng.reset = function() {
		 $("#dtaInicial").val('');
		 $("#dtaFinal").val('');
		 ng.vendedor = null;
		 ng.produto = null;
		 ng.vendas = null;
		 ng.paginacao.vendas = null;
		 ng.busca.id_fabricante   = null;
		 ng.busca.nome_fabricante = null;
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

		var queryString = "?ven->id_empreendimento="+ng.userLogged.id_empreendimento;
			queryString += "&"+$.param({'ven->dta_venda':{exp:"BETWEEN '"+ moment(dtaInicial, 'DD/MM/YYYY').format('YYYY-MM-DD') +" 00:00:00' AND '"+ moment(dtaFinal, 'DD/MM/YYYY').format('YYYY-MM-DD') +" 23:59:59'"}});

		if(!empty(ng.busca) && !empty(ng.busca.id_fabricante))
			queryString += "&fab->id=" + ng.busca.id_fabricante;

		$("#modal-aguarde").modal('show');

		ng.vendas = [];

		aj.get(baseUrlApi()+"relatorio/vendas/fabricante/"+offset+'/'+limit+queryString)
			.success(function(data, status, headers, config) {
				ng.vendas = _.groupBy(data.vendas, 'nme_fabricante');
				ng.paginacao.vendas = data.paginacao ;
				
				angular.forEach(ng.vendas, function(items, fornecedor) {
					ng.vendas[fornecedor] = {
						items: items,
						qtd_total_vendida: 0,
						vlr_total_custo_medio: 0,
						vlr_total_vendido: 0
					};

					angular.forEach(ng.vendas[fornecedor].items, function(item, index) {
						ng.vendas[fornecedor].qtd_total_vendida 	+= item.qtd_vendida;
						ng.vendas[fornecedor].vlr_total_custo_medio += item.med_custo;
						ng.vendas[fornecedor].vlr_total_vendido 	+= item.vlr_subtotal;
					});
				});

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

	ng.loadVendasByProduto = function(item, event){

		$(event.target).popover({
            title: 'Vendas',
            placement: 'top',
            content: '<strong>Aguarde, carregando...</strong>',
            html: true,
            container: $(event.target),
            trigger  :'hover',
        }).popover('show');

		var dtaInicial  = $("#dtaInicial").val();
		var dtaFinal    = $("#dtaFinal").val();

		var queryString = "?ven->id_empreendimento="+ng.userLogged.id_empreendimento;
			queryString += "&"+$.param({'ven->dta_venda':{exp:"BETWEEN '"+ moment(dtaInicial, 'DD/MM/YYYY').format('YYYY-MM-DD') +" 00:00:00' AND '"+ moment(dtaFinal, 'DD/MM/YYYY').format('YYYY-MM-DD') +" 23:59:59'"}});

		ng.id_fabricante = item.id_fabricante;
			queryString += "&prd->id_fabricante=" + ng.id_fabricante;

		ng.cod_produto = item.cod_produto;
			queryString += "&prd->id=" + ng.cod_produto;

		

		aj.get(baseUrlApi()+"relatorio/vendas/produto/fabricante/periodo/"+queryString)
			.success(function(data, status, headers, config) {

				ng.asd = data.teste;

				var tbl = '<table class="table table-bordered table-condensed table-striped table-hover">' ;
					tbl += '<tr>'+'<td>Venda</td>'+'<td class"text-center">Data</td>'+'<td class"text-center">Vendedor</td>'+'<td class"text-center">Cliente</td>'+'<td class"text-center">Qtde. Vendida</td>'+'</tr>';

				$.each(ng.asd,function(i,v){
					tbl += '<tr>'+'<td class"text-center">'+v.id_venda+'</td>'+'<td class"text-center">'+formatDateBR(v.dta_venda)+'</td>'+'<td class"text-center">'+v.nme_vendedor+'</td>'+'</td>'+'<td class"text-center">'+v.nme_cliente+'</td>'+'</td>'+'<td align="center">'+v.qtd_total+'</td>'+'</tr>';
				});
				
				tbl += '</table>';

				

				 $(event.target).popover('destroy').popover({
					title: 'Vendas',
					placement: 'top',
					content: tbl,
					html: true,
					container: $(event.target),
					trigger  :'hover',
				}).popover('show');

			})
			.error(function(data, status, headers, config) {
				$("#modal-aguarde").modal('hide');
				ng.vendas = null;
				ng.status = status;
				ng.msg_error = data;
				ng.paginacao.vendas = null;
			});
	}

	ng.modalFabricantes = function(){
		$('#modal-fabricantes').modal('show');
		ng.loadFabricantes(0,10);
	}
	ng.busca_vazia = {} ;
	ng.loadFabricantes = function(offset, limit) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;
		ng.busca_vazia.fabricantes = false ;
		var query_string = "?tfe->id_empreendimento="+ng.userLogged.id_empreendimento ;
		if(!empty(ng.busca.fabricantes))
			query_string  += "&"+$.param({nome_fabricante:{exp:"like '%"+ng.busca.fabricantes+"%'"}});

    	aj.get(baseUrlApi()+"fabricantes/"+offset+"/"+limit+query_string)
		.success(function(data, status, headers, config) {
			ng.fabricantes = data.fabricantes ;	
			ng.paginacao.fabricantes = data.paginacao ;
		})
		.error(function(data, status, headers, config) {
			if(status != 404)
				alert("ocorreu um erro");
			else{
				ng.paginacao.fabricantes = [] ;
				ng.fabricantes = [] ;	
				ng.busca_vazia.fabricantes = true ;
			}
				
		});
	}

	ng.addFabricante = function(item){
		ng.busca.id_fabricante   = item.id;
		ng.busca.nome_fabricante = item.nome_fabricante;
    	$('#modal-fabricantes').modal('hide');
	}

	ng.reset();
});

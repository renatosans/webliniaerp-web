app.controller('RelatorioTotalVendasCliente', function($scope, $http, $window, UserService, EmpreendimentoService) {
	var ng 				= $scope;
		aj 				= $http;
	ng.userLogged 		= UserService.getUserLogado();
	ng.dados_empreendimento = EmpreendimentoService.getDadosEmpreendimento(ng.userLogged.id_empreendimento);
	ng.itensPorPagina 	= 10;
	ng.deposito 		= {};
	ng.depositos 		= [];
	ng.itens 		   	= [];
	ng.paginacao 	   	= {};
	ng.busca			= {}
	ng.busca.clientes  	= '';
	ng.cliente          = {};
	ng.vendas 			= [];

	 $scope.all_countries = [{
                "id": 28,
                    "title": "Sweden"
            }, {
                "id": 56,
                    "title": "USA"
            }, {
                "id": 89,
                    "title": "England"
            }];

	$scope.popover = {content: ''};

	 $scope.all_countries = [{
                "id": 28,
                    "title": "Sweden"
            }, {
                "id": 56,
                    "title": "USA"
            }, {
                "id": 89,
                    "title": "England"
            }];
        
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
		 ng.produto = {} ;
		 ng.busca.clientes = '' ;
	}

	ng.resetFilter = function() {
		ng.reset();
		ng.loadVendas(0,ng.itensPorPagina);
	}

	ng.aplicarFiltro = function() {
		$("#modal-aguarde").modal('show');
		ng.loadVendas(0,ng.itensPorPagina);
	}

	ng.loadVendas = function() {
		dtaInicial = ng.busca.dtaInicial;
		dtaFinal   = ng.busca.dtaFinal;
		var queryString = "";

		if(!empty(dtaInicial) && !empty(dtaFinal)){
			if(dtaInicial > dtaFinal){
				$("#modal-aguarde").modal('hide');
				ng.mensagens('alert-danger','<strong>A data inicial deve ser menor que a final</strong>','.errorBusca');
				return;
			}
			queryString = "?"+$.param({'ven->dta_venda':{exp:"BETWEEN '"+dtaInicial+" 00:00:00' AND '"+dtaFinal+" 23:59:59'"}});
		}else if(!empty(dtaInicial)){
			queryString = "?"+$.param({'ven->dta_venda':{exp:">='"+dtaInicial+" 00:00:00'"}});
		}else if(!empty(dtaFinal)){
			queryString = "?"+$.param({'ven->dta_venda':{exp:"<='"+dtaFinal+" 23:59:59'"}});
		}

		if(!isNaN(ng.produto.id_produto)){
			queryString += queryString == "" ? "?pro->id="+ng.produto.id_produto : "&pro->id="+ng.produto.id_produto ;
		}

		ng.vendas = null ;

		/*if(ng.cliente.id != "" && ng.cliente.id != null){
			queryString = queryString == "" ? "?usu->id="+ng.cliente.id : "&usu->id="+ng.cliente.id ;
		}*/
		
		aj.get(baseUrlApi()+"produtos/by_venda/"+ng.userLogged.id_empreendimento+"/"+queryString)
			.success(function(data, status, headers, config) {
				ng.vendas = data;
				$("#modal-aguarde").modal('hide');

			})
			.error(function(data, status, headers, config) {
				ng.vendas = [];
				$("#modal-aguarde").modal('hide');
			});
	}

	ng.showProdutos = function(){
   		ng.busca.produtos = "" ;
   		ng.loadProdutos(0,10);
   		$('#list_produtos').modal('show');
   	}
   	ng.detail_custo_total = [];

   	ng.limparPopOver = function(){
   		ng.detail_custo_total = [];
   	}

   	ng.detalCustoProduto = function(item){
   		var dtaInicial  = ng.busca.dtaInicial;
		var dtaFinal    = ng.busca.dtaFinal;
		queryString = "" ;
		if(!empty(dtaInicial) && !empty(dtaFinal)){
			if(!dtaInicial > dtaFinal){
				queryString = "?"+$.param({'tv->dta_venda':{exp:"BETWEEN '"+dtaInicial+" 00:00:00' AND '"+dtaFinal+" 23:59:59'"}});
			}
		}else if(!empty(dtaInicial)){
			queryString = "?"+$.param({'tv->dta_venda':{exp:">='"+dtaInicial+" 00:00:00'"}});
		}else if(!empty(dtaFinal)){
			queryString = "?"+$.param({'tv->dta_venda':{exp:"<='"+dtaFinal+" 23:59:59'"}});
		}

		aj.get(baseUrlApi()+"produtos/detail_custo_total_produto/"+ng.userLogged.id_empreendimento+"/"+item.cod_produto+queryString)
			.success(function(data, status, headers, config) {
				var tr = "";
				$.each(data,function(i,item){
					tr += "<tr>"
						 	+"<td  class='text-right'>"+FormatMilhar(item.qtd_vendida)+"</td>"
						 	+"<td  class='text-right' >R$ "+numberFormat(item.vlr_item_custo,2,',','.')+"</td>"
						 	+"<td  class='text-right'>R$ "+numberFormat(item.vlr_custo_total,2,',','.')+"</td>"
						 +"<tr>";

				});
				var template = '<table id="data" class="table table-bordered table-hover table-striped table-condensed">'
						+'<thead>'
						+'<tr>'
						+'<th>Qtd</th>'
						+'<th>vlr. Custo Uni.</th>'
						+'<th>Vlr. Custo Total</th>'
						+'</tr>'
						+'</thead>'
						+'<tbody>'
						+tr
						+'</tbody>'
						+'</table>';
				$('.popover .popover-content').html(template)
		})
			.error(function(data, status, headers, config) {
				item.detail_custo_total = [];
		});
   	}

   	ng.produto_debito = {} ;
   	ng.showProdutoDebito = function(item){
   		ng.produto_debito.nome_produto = item.nme_produto ;
   		ng.produto_debito.id_produto   = item.cod_produto ;
   		ng.loadProdutoDebito(0,10);
   		$('#list_produtos_debito').modal('show');
   	}

   	ng.loadProdutoDebito = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit ;

    	var dtaInicial  = ng.busca.dtaInicial;
		var dtaFinal    = ng.busca.dtaFinal;
		queryString = "" ;
		if(dtaInicial != "" && dtaFinal != ""){
			if(!dtaInicial > dtaFinal){
				queryString = "?"+$.param({'tv->dta_venda':{exp:"BETWEEN '"+dtaInicial+" 00:00:00' AND '"+dtaFinal+" 23:59:59'"}});
			}
		}else if(dtaInicial != ""){
			queryString = "?"+$.param({'tv->dta_venda':{exp:">='"+dtaInicial+" 00:00:00'"}});
		}else if(dtaFinal != ""){
			queryString = "?"+$.param({'tv->dta_venda':{exp:"<='"+dtaFinal+" 23:59:59'"}});
		}

		ng.produto_debito.itens = [];
		aj.get(baseUrlApi()+"produtos/detail_produto_debito/"+ng.userLogged.id_empreendimento+"/"+ng.produto_debito.id_produto +"/"+offset+"/"+limit+queryString)
			.success(function(data, status, headers, config) {
				ng.produto_debito.itens     = data.produtos ;
				ng.produto_debito.paginacao = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.produto_debito.itens = [];
			});
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

	ng.mensagens = function(classe , msg,alertClass){
		alertClass = alertClass == null  ? '.alert-sistema' : alertClass ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$('.alert-sistema').fadeOut('slow');
		},5000);
	}

	ng.reset();
	ng.aplicarFiltro();
});

app.directive('bsPopover', function () {
    return function (scope, element, attrs) {
        element.find("a[rel=popover]").popover({
            placement: 'bottom',
            html: 'true'
        });
    };
});


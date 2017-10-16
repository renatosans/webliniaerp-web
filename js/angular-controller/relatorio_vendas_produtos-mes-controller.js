app.controller('RelatorioTotalVendasCliente', function($scope, $http, $window,$dialogs, UserService, EmpreendimentoService) {
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
	ng.vendas 			= null;

	$scope.popover = {content: ''};

	ng.doExportExcel = function(id_table){
    	$('#'+ id_table).tableExport({
    		filename: id_table, 
    		type:'excel', 
    		escape:'false'
    	});
    }

	ng.ancoraSaldo = function(key){
		$('html, body').stop().animate({
        	scrollTop: $( '#saldo_'+key ).offset().top - 160
    	}, 500);
	}
       
	ng.reset = function() {
		 $("#dtaInicial").val('');
		 ng.produto = {} ;
		 ng.busca.clientes = '' ;
	}

	ng.resetFilter = function() {
		ng.reset();
		$('.datepicker1').datepicker('clearDates');
		ng.msg_error = null;
		ng.vendas = null;
	}

	ng.aplicarFiltro = function() {
		ng.loadVendas();
	}

	ng.loadVendas = function() {
		var datas  = $("#dtaInicial").val();
		if(empty(datas)){
			alert('Atenção!\nEscolha as datas para montar o relatorio');
			return;
		}
		$("#modal-aguarde").modal('show');
		ng.vendas = [] ;
		var queryString = "";
		var dt  = new Date();
		var ano_atual = dt.getFullYear() ;
		dta_in = null;		

		if(!empty(datas)){
			datas  = datas.trim().replace(/\//g,"-").split(",");
			dta_in = "";
			$.each(datas,function(key,dta){
				dta_in += "'"+dta+"',";
			});
			dta_in = dta_in.substring(0,dta_in.length-1);
			console.log(dta_in);
		}

		if(!empty(dta_in)){
			queryString = empty(queryString) ? "?" : queryString+"&" ;
			queryString += $.param({sql:{literal_exp:"DATE_FORMAT(ven.dta_venda,'%m-%Y') IN("+dta_in+")"}});
		}

		if(!isNaN(ng.produto.id_produto)){
			queryString = queryString == "" ? "?pro->id="+ng.produto.id_produto : queryString+"&pro->id="+ng.produto.id_produto ;
		}

		queryString = queryString == "" ? "?" : queryString+"&" ;
		//queryString += $.param({'group_by':["DATE_FORMAT(ven.dta_venda,'%m-%Y')","itv.id_produto"]});
		queryString += "&"+$.param({'order_by':["mes_ano_venda DESC"]});

		ng.vendas 			= [];
		
		aj.get(baseUrlApi()+"v2/produtos/by_venda/"+ng.userLogged.id_empreendimento+"/"+queryString)
			.success(function(data, status, headers, config) {
				var vendas = _.groupBy(data, "mes_ano_venda");
				var aux    = {} ;
				$.each(vendas,function(perido,perido_produtos){
					aux[perido] = {itens:perido_produtos,saldo:0};
					var saldo_vlr_custo_total = 0 ;
					var saldo_vlr_lucro_bruto = 0 ;
					var saldo_vlr_vendido     = 0 ;
					$.each(perido_produtos,function(i,produto){
						saldo_vlr_custo_total += Number(produto.vlr_custo_total);
						saldo_vlr_vendido     += Number(produto.vlr_vendido);
						saldo_vlr_lucro_bruto += Number(produto.vlr_lucro_bruto);
					});
					aux[perido].saldo_vlr_custo_total = saldo_vlr_custo_total;
					aux[perido].saldo_vlr_vendido = saldo_vlr_vendido;
					aux[perido].saldo_vlr_lucro_bruto = saldo_vlr_lucro_bruto;
				});
				ng.vendas = aux ;
				console.log(ng.vendas);
				$("#modal-aguarde").modal('hide');

			})
			.error(function(data, status, headers, config) {
				ng.vendas = null;
				ng.status = status;
				ng.msg_error = data;
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
   		var dtaInicial  = $("#dtaInicial").val();
		var dtaFinal    = $("#dtaFinal").val();
		queryString = "" ;
		/*if(dtaInicial != "" && dtaFinal != ""){
			dtaInicial = formatDate(dtaInicial);
			dtaFinal   = formatDate(dtaFinal);
			if(!dtaInicial > dtaFinal){
				queryString = "?"+$.param({'tv->dta_venda':{exp:"BETWEEN '"+dtaInicial+" 00:00:00' AND '"+dtaFinal+" 23:59:59'"}});
			}
		}else if(dtaInicial != ""){
			dtaInicial = formatDate(dtaInicial);
			queryString = "?"+$.param({'tv->dta_venda':{exp:">='"+dtaInicial+" 00:00:00'"}});
		}else if(dtaFinal != ""){
			dtaFinal = formatDate(dtaFinal);
			queryString = "?"+$.param({'tv->dta_venda':{exp:"<='"+dtaFinal+" 23:59:59'"}});
		}*/

		queryString = queryString == "" ? "?" : "&" ;
		queryString += "DATE_FORMAT(tv->dta_venda,'%Y-%m')="+item.mes_ano_venda;
		
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
   		ng.produto_debito.nome_produto    = item.nme_produto ;
   		ng.produto_debito.id_produto  	  = item.cod_produto ;
   		ng.produto_debito.mes_ano_venda   = item.mes_ano_venda ;
   		ng.loadProdutoDebito(0,10);
   		$('#list_produtos_debito').modal('show');
   	}

   	ng.loadProdutoDebito = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit ;

    	var dtaInicial  = $("#dtaInicial").val();
		var dtaFinal    = $("#dtaFinal").val();
		queryString = "" ;
		/*if(dtaInicial != "" && dtaFinal != ""){
			dtaInicial = formatDate(dtaInicial);
			dtaFinal   = formatDate(dtaFinal);
			if(!dtaInicial > dtaFinal){
				queryString = "?"+$.param({'tv->dta_venda':{exp:"BETWEEN '"+dtaInicial+" 00:00:00' AND '"+dtaFinal+" 23:59:59'"}});
			}
		}else if(dtaInicial != ""){
			dtaInicial = formatDate(dtaInicial);
			queryString = "?"+$.param({'tv->dta_venda':{exp:">='"+dtaInicial+" 00:00:00'"}});
		}else if(dtaFinal != ""){
			dtaFinal = formatDate(dtaFinal);
			queryString = "?"+$.param({'tv->dta_venda':{exp:"<='"+dtaFinal+" 23:59:59'"}});
		}*/

		queryString = queryString == "" ? "?" : "&" ;
		queryString += "DATE_FORMAT(tv->dta_venda,'%Y-%m')="+ng.produto_debito.mes_ano_venda;

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

    	var query_string = "?tpe->id_empreendimento="+ng.userLogged.id_empreendimento;

    	if(ng.busca.produtos != ""){
    		query_string += "&("+$.param({'pro->nome':{exp:"like'%"+ng.busca.produtos+"%' OR fab.nome_fabricante like'%"+ng.busca.produtos+"%'"}})+")";
    	}

		ng.produtos = [];
		//aj.get(baseUrlApi()+"estoque/"+offset+"/"+limit+"/"+query_string)
		aj.get(baseUrlApi()+"produtos/"+ offset +"/"+ limit +"/"+query_string)
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

	ng.countQtdItens = function(itens){
		var total = 0 ;
		$.each(itens,function(i,v){
			total += Number(v.qtd_vendida);
		});
		return total ;
	}

	ng.reset();
	//ng.aplicarFiltro();
});

app.directive('bsPopover', function () {
    return function (scope, element, attrs) {
        element.find("a[rel=popover]").popover({
            placement: 'bottom',
            html: 'true'
        });
    };
});


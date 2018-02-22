app.controller('RelatorioTotalVendasVendedorDiarioController', function($scope, $http, $window, UserService,FuncionalidadeService, EmpreendimentoService) {
	var ng 				= $scope,
		aj 				= $http;
	ng.userLogged 		= UserService.getUserLogado();
	ng.dados_empreendimento = EmpreendimentoService.getDadosEmpreendimento(ng.userLogged.id_empreendimento);
	ng.itensPorPagina 	= 10;
	ng.vendas 		   	= null;
	ng.paginacao 	   	= {};
	ng.busca			= {};
	ng.busca_modal		= {};

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
   		ng.loadProdutos(0,10);
   		$('#list_produtos').modal('show');
   	}

	ng.loadProdutos = function(offset,limit) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 20 : limit;

		var query_string = "?group=&emp->id_empreendimento="+ng.userLogged.id_empreendimento;

		if(!empty(ng.busca_modal.produtos)){
			query_string += "&("+$.param({'prd->nome':{exp:"like'%"+ng.busca_modal.produtos+"%' OR fab.nome_fabricante like'%"+ng.busca_modal.produtos+"%'"}})+")";
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
		ng.busca.produto = item;
		$('#list_produtos').modal('hide');
	}

	ng.reset = function() {
		 $("#dtaInicial").val('');
		 $("#dtaFinal").val('');
		 ng.busca = {};
		 ng.vendedor = null;
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

	ng.loadVendas = function() {
		if(empty(ng.busca.dta)){
			alert('Você deve preencher o campo Data');
			return;
		}

		var queryString = "?a->id_empreendimento="+ng.userLogged.id_empreendimento;
			queryString += "&"+$.param({'a->dta_venda':{exp:"like "+"'%"+ moment(ng.busca.dta, 'DD/MM/YYYY').format('YYYY-MM-DD')+"%'"}});

		if(!empty(ng.busca.vendedor))
			queryString += "&a->id_usuario=" + ng.busca.vendedor.id;

		if(!empty(ng.busca.produto))
			queryString += "&a->id_produto=" + ng.busca.produto.id_produto;

		if(ng.busca.op_valor == "between"){
			if(empty(ng.busca.valor_fim)){
					$(".valor_fim").addClass("has-error");

					var formControl = $(".valor_fim")
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'Escolha o valor final')
						.attr("data-original-title", 'Escolha o valor final');
					formControl.tooltip();
					return;
			}else if(empty(ng.busca.valor_inicio)){
				ng.busca.valor_inicio = 0;
				queryString +="&" + $.param({num_percentual_desconto:{exp:"between "+ng.busca.valor_inicio+" AND "+ng.busca.valor_fim+""}}) ;
			}else
				queryString +="&" + $.param({num_percentual_desconto:{exp:"between "+ng.busca.valor_inicio+" AND "+ng.busca.valor_fim+""}}) ;
		}else if(ng.busca.op_valor == "=")
			queryString += "&" + $.param({num_percentual_desconto:{literal_exp:"CAST(num_percentual_desconto AS CHAR) ="+ng.busca.valor_fim+"" }}) ;
		else if(ng.busca.op_valor == "<")
			queryString += "&" + $.param({num_percentual_desconto:{literal_exp:"num_percentual_desconto  <'"+ng.busca.valor_fim+"'" }})  ;
		else if(ng.busca.op_valor == ">")
			queryString += "&" + $.param({num_percentual_desconto:{literal_exp:"num_percentual_desconto  >'"+ng.busca.valor_fim+"'" }})  ;
		else if(ng.busca.op_valor == "<=")
			queryString += "&" + $.param({num_percentual_desconto:{literal_exp:"(num_percentual_desconto  < '"+ng.busca.valor_fim+"' OR CAST(num_percentual_desconto AS CHAR) = '"+ng.busca.valor_fim+"')" }})  ;
		else if(ng.busca.op_valor == ">=")
			queryString += "&" + $.param({num_percentual_desconto:{literal_exp:"(num_percentual_desconto  >='"+ng.busca.valor_fim+"'  OR CAST(num_percentual_desconto AS CHAR) = '"+ng.busca.valor_fim+"')" }})  ;

		$("#modal-aguarde").modal('show');

		ng.vendas = [];

		aj.get(baseUrlApi()+"relatorio/vendas/diario/vendedor/"+queryString)
			.success(function(data, status, headers, config) {
				ng.vendas = data.vendas;
				$("#modal-aguarde").modal('hide');
				ng.vlr_custo_total = 0;
				ng.vlr_real_item_total = 0;
				ng.vlr_venda_item_total = 0;
				ng.vlr_subtotal_item_total = 0;
				$.each(ng.vendas, function(index, item) {
					if(item.qtd > 0){
						ng.vlr_custo_total += item.vlr_custo;
						ng.vlr_real_item_total += item.vlr_real_item;
						ng.vlr_venda_item_total += item.vlr_venda_item;
						ng.vlr_subtotal_item_total += item.vlr_subtotal_item;
					}
				});

			})
			.error(function(data, status, headers, config) {
				$("#modal-aguarde").modal('hide');
				ng.vendas = null;
				ng.status = status;
				ng.msg_error = data;
			});
	}

	ng.selCliente = function(){
		ng.loadCliente(0,10);
		$("#list_clientes").modal("show");
	}

	ng.addCliente = function(item){
		ng.busca.vendedor = item;
    	$("#list_clientes").modal("hide");
	}

	ng.loadCliente= function(offset,limit) {
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

	ng.reset();

	$('#dtaInicial').on('change',function(event){
		ng.busca.dta = $(this).val();
	});
	$('#dtaFinal').on('change',function(event){
		ng.busca.dta_final = $(this).val();
	});
});

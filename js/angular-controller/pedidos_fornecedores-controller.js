app.controller('PedidosFornecedoresController', function($scope, $http, $window,$dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 				= baseUrl();
	ng.userLogged 			= UserService.getUserLogado();
    ng.pedidos 				= [];
    ng.novoPedido 			= [];
    ng.produtos 			= [];
    ng.paginacao_produtos 	= [];
    ng.paginacao_fornecedores 	= {};
    ng.paginacao_pedidos 		= {};
    ng.pesquisa 			= {produto:"",fornecedores:""};
    ng.fornecedor           = {};
    ng.flg_pedido_real      = 1;
    ng.busca 			    = {fornecedores:""};

    ng.nome_produto_form = null;

    ng.vl_btn =  {salvar_pedido:"Salvar pedido"} ;

   	//funções para produtos ja cadastrados

   	ng.reset = function(){
		ng.PedidosFornecedores = {itens:[]};
	}

   	ng.busca = { solicitante: "", fornecedor: "", fornecedores:""};
		ng.resetFilter = function() {
			$("#datapedido").val("");
			ng.busca.solicitante = "" ;
			ng.busca.fornecedor = "";
			ng.reset();
			ng.loadPedidosFornecedores(0,10);
		}

	ng.loadPedidosFornecedores = function(offset, limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;

    	var query_string = "?tpf->id_empreendimento="+ng.userLogged.id_empreendimento;
			
			if(ng.busca.fornecedor != "")
				query_string += "&("+$.param({'frn->nome_fornecedor':{exp:"like '%"+ng.busca.fornecedor+"%' "}})+")";

			if(ng.busca.solicitante != "")
				query_string += "&("+$.param({'usu->nome':{exp:"like '%"+ng.busca.solicitante+"%' "}})+")";

			if($("#datapedido").val() != ""){
			var data = moment($("#datapedido").val(), "DD/MM/YYYY").format("YYYY-MM-DD");
			query_string += "&("+$.param({'1':{exp:"= 1 AND cast(dta_pedido as date) = '"+ data +"' "}})+")";
		}
		

		ng.pedidos = [];
		ng.paginacao_pedidos = {};

		var url = "pedidos/"+ offset +"/"+ limit + query_string;

		aj.get(baseUrlApi()+url)
			.success(function(data, status, headers, config) {
				ng.pedidos = data.pedidos;
				ng.paginacao_pedidos = data.paginacao;
			})
			.error(function(data, status, headers, config) {

			});
	}


	ng.viewDetalhes = function(item,offset,limit){
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;

		ng.itensPedido = [];
		aj.get(baseUrlApi()+"pedido_itens/"+item.id+"/"+offset+"/"+limit)
			.success(function(data, status, headers, config) {
				$.each(data.itens,function(i,item){
					ng.itensPedido.push(item);
					$('#view-itens-pedido').modal('show');
				});
			})
			.error(function(data, status, headers, config) {

		});
	}



	ng.delete = function(item){
		 dlg = $dialogs.confirm('Por favor confirme' ,'<strong>Tem certeza que deseja excluir esté pedido?</strong>');
		 dlg.result.then(function(btn){
	           $http.get(baseUrlApi()+"pedido/delete/"+item.id)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Pedido excluido com sucesso</strong>');
					ng.loadPedidosFornecedores();
				})
				.error(function(data, status, headers, config) {

				});
         },function(btn){

         });

	}

    //funções de novo pedido

    ng.showBoxNovoPedido = function(){
    	$('#box-novo-pedido').toggle(400,function(){
    		if($(this).is(':visible')){
    			$('i','#btn-novo-pedido').removeClass("fa-plus-circle").addClass("fa-minus-circle");
    		}else{
    			$('i','#btn-novo-pedido').removeClass("fa-minus-circle").addClass("fa-plus-circle");
    		}
    	});
    }

    ng.addItem = function(){
		var item = {
			nome_produto    : null,
			id_produto      : null,
			custo_compra    : 0,
			nome_fabricante : null,
			qtd				: 1
		}

		ng.novoPedido.push(item);
	}

	ng.deleteItem = function(index){
		ng.novoPedido.splice(index, 1);
		ng.atualizaTotal();
	}

	ng.salvarPedido = function(fornecedor) {
		  if(ng.novoPedido.length <= 0){
		  	$dialogs.notify('Atenção!','Nenhum item foi adicionado ao pedido');
		  	return;
		  }

		  ng.vl_btn.salvar_pedido = "Aguarde ...";
		  $http.post(baseUrlApi()+'pedido',{itens:ng.novoPedido,flg_pedido_real:ng.flg_pedido_real,id_usuario:ng.userLogged.id,id_fornecedor:ng.fornecedor.id_fornecedor,id_empreendimento:ng.userLogged.id_empreendimento}
	                ).success(function(data, status, headers, config) {
	                	ng.vl_btn.salvar_pedido = "Salvar pedido";
	                	ng.showBoxNovoPedido();
	                	ng.novoPedido = [];
	                	ng.loadPedidosFornecedores();
	                	ng.mensagens('alert-success','<strong>Pedido cadastrado com sucesso</strong>');
	                }).error(function(data, status) {
	                	alert('Desculpe, ocorreu um erro inesperado!');
	                });
		  return false;
	}

	ng.verificaNovosPedidos = function(){
		var retorno = true
		$.each(ng.novoPedido,function(i,item){
			if(item.id_fornecedor == null || item.id_produto == null){
				retorno = false ;
			}
		});
		return retorno;
	}


    //Funções para o modal de fornecedores

    ng.selFornecedor = function(){
		ng.loadFornecedores();
		$("#list_fornecedores").modal("show");

	}

	ng.addFornecedor = function(item){
    	ng.fornecedor.nome_fornecedor = item.nome_fornecedor;
    	ng.fornecedor.id_fornecedor = item.id;
    	$("#list_fornecedores").modal("hide");
	}


	ng.loadFornecedores= function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;
		ng.fornecedores = [];

		var query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento+"&id[exp]=!="+ng.configuracao.id_fornecedor_movimentacao_caixa ;

		if(ng.busca.fornecedores != ""){
			query_string += "&"+$.param({'frn->nome_fornecedor':{exp:"like'%"+ng.busca.fornecedores+"%'"}});
		}

		var url = "fornecedores/"+offset+"/"+limit+"/"+query_string;

		aj.get(baseUrlApi()+url)
			.success(function(data, status, headers, config) {
				ng.fornecedores = data.fornecedores;
				ng.paginacao_fornecedores = data.paginacao;
			})
			.error(function(data, status, headers, config) {

			});
	}

	//Funções para o modal de produtos
    var pesquisa_produto = ng.pesquisa.produto;

    ng.selProduto = function(){
    	ng.pesquisa.produto = "";
    	pesquisa_produto    = "";
    	ng.loadProdutos();
    	$("#list_produtos").modal("show");
    }

    ng.addProduto = function(item){
    	var itemList = {};
	    	itemList.nome_produto      = item.nome;
	    	itemList.id_produto        = item.id;
	    	itemList.custo_compra      = item.custo_compra;
	    	itemList.nome_fabricante   = item.nome_fabricante;
	    	itemList.peso   			= item.peso;
	    ng.novoPedido.push(itemList);
    	ng.atualizaTotal();
    	$("#list_produtos").modal("hide");
    }

    ng.loadProdutosBusca = function(){
    	pesquisa_produto = ng.pesquisa.produto;
    	ng.loadProdutos() ;
    }

    ng.loadProdutos = function(offset,limit) {
    	offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.produtos = [];
		ng.paginacao_produtos = [];

		var query_string = "?tpe->id_empreendimento="+ng.userLogged.id_empreendimento;

		if(pesquisa_produto != ""){
    		query_string += "&"+$.param({'produto->nome':{exp:"like'%"+pesquisa_produto+"%' OR fabricante.nome_fabricante like'%"+pesquisa_produto+"%'"}});
    	}

		aj.get(baseUrlApi()+"produtos_by_fornecedor/"+ng.fornecedor.id_fornecedor+"/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				$.each(data.paginacao,function(i,item){
					ng.paginacao_produtos.push(item);
				});
				$.each(data.produtos,function(i,item){
					ng.produtos.push(item);
				});
			})
			.error(function(data, status, headers, config) {

			});
	}

	ng.changeQtd = function(){
		ng.atualizaTotal();
	}

	ng.atualizaTotal = function(){
		var total =  0 ;
		$.each(ng.novoPedido,function(i,item){
			var qtd          = item.qtd == '' ? 1 : parseInt(item.qtd);
			var custo_compra = parseFloat(item.custo_compra);
			console.log(item);
			total += qtd * custo_compra ;
		});
		ng.total = total;
	}

	//
	ng.configuracao = null ;
	ng.loadConfig = function(){
		var error = 0 ;
		aj.get(baseUrlApi()+"configuracoes/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.configuracao = data ;
			})
			.error(function(data, status, headers, config) {
				if(status == 404){
					ng.configuracao = false ;
				}
			});
	}

	ng.mensagens = function(clase , msg){
		$('.alert-sistema').fadeIn().addClass(clase).html(msg)
		setTimeout(function(){$('.alert-sistema').fadeOut('slow');},5000);

	}


	ng.loadConfig();
	ng.loadPedidosFornecedores();

});

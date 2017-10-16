app.controller('GradeController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
	ng.produto 		= {};
    ng.produtos		= [];
    ng.fabricantes	= [];
    ng.importadores	= [];
    ng.categorias	= [];
    ng.valor_tabela = "";
    ng.produto.fornecedores = [{id_fornecedor:1,nome_fornecedor:"Hage - Matriz"}];
    ng.busca = {produtos:"",produtosModal:""};

    ng.editing = false;
    ng.paginacao = {};

    ng.showBoxNovo = function(onlyShow){
    	if(onlyShow) {
			$('i','#btn-novo').removeClass("fa-plus-circle").addClass("fa-minus-circle");
			$('#box-novo').show();
		}
		else {
			$('#box-novo').toggle(400, function(){
				if($(this).is(':visible')){
					$('i','#btn-novo').removeClass("fa-plus-circle").addClass("fa-minus-circle");
				}else{
					$('i','#btn-novo').removeClass("fa-minus-circle").addClass("fa-plus-circle");
				}
			});
		}
	}

	ng.mensagens = function(classe , msg){
		$('.alert-sistema').fadeIn().addClass(classe).html(msg);

		setTimeout(function(){
			$('.alert-sistema').fadeOut('slow');
		},5000);
	}

	ng.reset = function() {
		ng.produto = {};
		ng.editing = false;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	ng.loadProdutos = function(offset, limit) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;

		var query_string = "?gpe->id_empreendimento="+ng.userLogged.id_empreendimento;

		if(ng.busca.produtos != ""){
			query_string += "&"+$.param({nome:{exp:"like'%"+ng.busca.produtos+"%' OR fab.nome_fabricante like '%"+ng.busca.produtos+"%'"}})+"";
		}

		aj.get(baseUrlApi()+"grades/"+ offset +"/"+ limit +"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.produtos = data.produtos;
				ng.paginacao.itens = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.produtos = [];
			});
	}



	ng.delete = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja excluir este produto?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"grade/delete/"+item.id)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Produto excluido com sucesso</strong>');
					ng.loadProdutos();
				})
				.error(function(data, status, headers, config) {
					alert('erro inesperado');
				});
		}, undefined);
	}

	 /* inicio - Ações de produtos */

   	ng.produtos = [] ;

   	ng.showProdutos = function(){
   		ng.busca.produtos = "" ;
   		ng.loadProdutosModal(0,10);
   		$('#list_produtos').modal('show');
   	}

   	ng.loadProdutosModal = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

    	var query_string = "?tpe->id_empreendimento="+ng.userLogged.id_empreendimento;

    	if(ng.busca.produtosModal != ""){
    		query_string += "&"+$.param({'nome':{exp:"like'%"+ng.busca.produtosModal+"%' OR nome_fabricante like'%"+ng.busca.produtosModal+"%'"}});
    	}

    	console.log(query_string);

		ng.produtosModal = null;
		aj.get(baseUrlApi()+"produtos/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.produtosModal           = data.produtos ;
				ng.paginacao.produtosModal = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.produtosModal           = [];
				ng.paginacao.produtosModal = [];
			});
	}

	ng.addProduto = function(item){
	aj.post(baseUrlApi()+"grade",{id_produto:item.id, id_empreendimento:ng.userLogged.id_empreendimento})
		.success(function(data, status, headers, config) {
			ng.mensagens('alert-success','O produto foi adicionado com sucesso','.alert-produtos');
			ng.loadProdutos();
		})
		.error(function(data, status, headers, config) {
			alert('ocorreu um erro inesperado');
		});
	}

	/* end */

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	ng.loadProdutos(0,10);
});

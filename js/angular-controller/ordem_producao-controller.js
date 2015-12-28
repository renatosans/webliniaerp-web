app.controller('OrdemProducaoController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 		 = baseUrl();
	ng.userLogged 	 = UserService.getUserLogado();
	ng.ordemProducao = {itens:[]};
	ng.busca         = {produtos:"",depositos:""};
	ng.paginacao     = {produtos:[]} ;

    ng.editing = false;

    ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

    ng.showProdutos = function(busca_cdb){
   	 	ng.busca.produtos = "" ;
   		ng.loadProdutos(0,10);
   		$('#list_produtos').modal('show');
   	}

   ng.loadProdutos = function(offset, limit) {
		ng.produtos = [];

		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;

		var query_string = "?tpe->id_empreendimento="+ng.userLogged.id_empreendimento+"&pro->flg_produto_composto=1";

		if(ng.busca.produtos != ""){
			if(isNaN(Number(ng.busca.produtos)))
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.busca.produtos+"%' OR codigo_barra like '%"+ng.busca.produtos+"%' OR fab.nome_fabricante like '%"+ng.busca.produtos+"%'"}})+")";
			else
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.busca.produtos+"%' OR codigo_barra like '%"+ng.busca.produtos+"%' OR fab.nome_fabricante like '%"+ng.busca.produtos+"%' OR pro.id = "+ng.busca.produtos+""}})+")";
		}

		aj.get(baseUrlApi()+"produtos/"+ offset +"/"+ limit +"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.produtos = data.produtos;
				ng.paginacao.produtos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404) {
					ng.produtos = null;
					ng.paginacao.produtos = null;
				}
			});
	}

	ng.addProduto = function(item){
		item = angular.copy(item) ;
		if(empty(item.qtd)){
			item.qtd =  1 ;
		}
		ng.ordemProducao.itens.push(item);
	}

	ng.delProduto = function(index){
		ng.ordemProducao.itens.splice(index,1);
	}

	ng.verificaProduto = function(item){
		var exists = false ;
		$.each(ng.ordemProducao.itens,function(i,produto){
			if(Number(produto.id) == Number(item.id)){
				exists = true ;
				return ;
			}
		});
		return exists ;
	}

	ng.salvar = function(){
		var btn = $("#btn-salvar");
		btn.button('loading');
		var error = 0
		$(".has-error").find("input").tooltip('destroy');
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").tooltip('destroy');
		$(".has-error").removeClass("has-error");
		$.each(ng.ordemProducao.itens,function(i,produto){
			if(empty(produto.qtd)){
				error ++ ;
				$("#produto-qtd-"+i).parent().addClass("has-error");

				var formControl = $("#produto-qtd-"+i)
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Informe a Quatidade a ser Produzida')
					.attr("data-original-title", 'Informe a Quatidade a ser Produzida');
				formControl.tooltip();
			}
			
		});

		if(error > 0){
			btn.button('reset');
			return
		}



		var post = angular.copy(ng.ordemProducao);

		post.id_responsavel = ng.userLogged.id ;
		post.id_status     = 1 ;
		post.id_empreendimento = ng.userLogged.id_empreendimento ;

		aj.post(baseUrlApi()+"ordem_producao",post)
			.success(function(data, status, headers, config) {
				ng.showBoxNovo(); ng.reset();
				$('html,body').animate({scrollTop: 0},'slow');
				ng.mensagens('alert-success','Ordem de Produção Cadastrada com Sucesso','.alert-list-pedidos');
				ng.loadOrdemProducao();
				btn.button('reset');
			})
			.error(function(data, status, headers, config) {
				if(status == 406){
					$.each(data, function(i, item) {
						$("#"+i).addClass("has-error");
						var formControl = $("#"+i)
							.attr("data-toggle", "tooltip")
							.attr("data-placement", "bottom")
							.attr("title", item)
							.attr("data-original-title", item);
						formControl.tooltip();
					});
				}else{
					alert('error ao cadastrar');
				}
				btn.button('reset');
		});
	}

	 ng.loadOrdemProducao = function(offset, limit) {
		ng.ordem_producao = [];

		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;

		var query_string = "?top->id_empreendimento="+ng.userLogged.id_empreendimento+"&top->flg_excluido=0";

		

		aj.get(baseUrlApi()+"ordem_producao/"+ offset +"/"+ limit +"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.ordem_producao = data.ordem_producao;
				ng.paginacao.ordem_producao = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404) {
					ng.ordem_producao = [];
					ng.paginacao.ordem_producao = [];
				}
			});
	}


	ng.viewOrdemProducao = {} ;

	ng.showView = function(item,out_estoque){
		if(out_estoque != true)
			ng.pro_out_estoque = [] ;
		ng.viewOrdemProducao = item ;
		ng.loadItensOrdemProducao(0,10,item.id);
		$('#list_detalhes').modal('show');
	}

	ng.loadItensOrdemProducao = function(offset, limit,id) {
		ng.viewOrdemProducao.itens = [];
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10  : limit;
		var query_string = "?tiop->id_ordem_producao="+id;
		aj.get(baseUrlApi()+"itens_ordem_producao/"+offset+"/"+limit+query_string)
			.success(function(data, status, headers, config) {
				ng.viewOrdemProducao.itens = data.itens_ordem_producao;
				ng.paginacao.itens_ordem_producao = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404) {
					ng.viewOrdemProducao.itens = [];
					ng.paginacao.itens_ordem_producao = [];
				}
			});
	}
 	ng.pro_out_estoque = [] ;
	ng.outEstoque = function(item){
		var exists = false ;
		$.each(ng.pro_out_estoque,function(i,x){
			if(Number(x[0]) == Number(item.id)){
				exists = true;
				return;
			}
		});

		return exists ;
	}

	ng.depositos = [] ;

	ng.showDepositos = function(){
		ng.busca.depositos = "" ;
		ng.loadDepositos(0,10);
		$('#list_depositos').modal('show');
	}

	ng.loadDepositos = function(offset,limit) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 20 : limit;

		var query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento;

		if(ng.busca.depositos != ""){
			query_string += "&"+$.param({'nme_deposito':{exp:"like'%"+ng.busca.depositos+"%'"}});
		}



		ng.depositos = [];
		aj.get(baseUrlApi()+"depositos/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.depositos        = data.depositos ;
				ng.paginacao.depositos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.depositos = [];
			});
	}

	ng.addDeposito = function(item){
		ng.ordemProducao.id_deposito     = item.id;
		ng.ordemProducao.nme_deposito    = item.nme_deposito;
		$('#list_depositos').modal('hide');
	}

	ng.changeStatus = function(item,id_status,event){
		var but = $(event.target);
		if(!but.is(':button'))
			but = $(event.target).parent();
		
		
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem Certeza que Deseja Alterar o Status Desta Ordem de Produção?</strong>');

		dlg.result.then(function(btn){
			but.button('loading');
			aj.get(baseUrlApi()+"ordem_producao/chage_status/"+item.id+"/"+id_status+"/"+ng.userLogged.id_empreendimento+"")
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','Status Alterado com Sucesso','.alert-list-pedidos');
					item.id_status = data.status.id;
					item.nome_status = data.status.nome_status;
				})
				.error(function(data, status, headers, config) {
				 but.button('reset');
				 if(status == 406){
				 	$('#list_out_estoque').modal('show')
				 	ng.pro_out_estoque  = data.out_estoque ;
				 	ng.list_out_estoque = data.lista;
				 	//ng.showView(item,true);
				 }else{
				 alert('Erro ao Mudar Status')
				}
			});
		}, function(){ but.button('reset') } );
	}
		

	ng.reset = function(){
		ng.ordemProducao = {itens:[]};
	}


    ng.showBoxNovo = function(onlyShow){
    	ng.editing = !ng.editing;

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

	 ng.loadOrdemProducao();

	
});

app.directive('bsTooltip', function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attr) {
            $timeout(function () {
                	  element.find("[data-toggle=tooltip]").tooltip();
            });
        }
    }
});

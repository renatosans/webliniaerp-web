app.controller('SituacaoEspecialController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 			= baseUrl();
	ng.userLogged 		= UserService.getUserLogado();
	ng.situacaoEspecial = {
		dsc_situacao_especial	: null,
		cod_empreendimento		: ng.userLogged.id_empreendimento,
		produto_cliente 		: []
	};
	ng.produto_cliente = {
			cod_situacao_especial 	: null,
			cod_produto 			: null,
			cod_cliente 			: null,
			dsc_texto_legal 		: null
	}
	ng.busca = {clientes:'',produtos:''} ;
	ng.editingProdutoCliente = false ;
	ng.editing = false;
	
	ng.paginacao = {situacoes:null} ;
    ng.situacoes	= [];
  

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

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	ng.reset = function() {
		ng.situacaoEspecial = {
			dsc_situacao_especial	: null,
			cod_empreendimento		: ng.userLogged.id_empreendimento,
			produto_cliente 		: []
		};
		ng.editing = false;
		ng.editingProdutoCliente = false ;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	ng.load = function(offset,limit) {
		offset = offset == null ? 0 : offset ;
		limit  = limit  == null ? 10 : limit ;
		ng.situacoes = null ;
		aj.get(baseUrlApi()+"situacao_especial/get/"+offset+"/"+limit+"?cod_empreendimento="+ng.userLogged.id_empreendimento+"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				ng.situacoes = data.situacoes;
				ng.paginacao.situacoes = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404){
					ng.situacoes = [];
					ng.paginacao.situacoes = [];
				}
			});
	}

	ng.salvar = function() {
		$('.has-error').tooltip('destroy')
		$('.has-error').removeClass('has-error')

		var btn = $("#salvar-situacao-especial") ;
		btn.button('loading');
		var url = 'situacao_especial';
		var itemPost = {};
		var msg = "Situação salva com sucesso!";

		if(ng.situacaoEspecial.cod_situacao_especial != null && ng.situacaoEspecial.cod_situacao_especial > 0) {
			url += '/update';
			msg = 'Situação alterada com sucesso!'
		}

		itemPost = angular.copy(ng.situacaoEspecial);

		aj.post(baseUrlApi()+url, itemPost)
			.success(function(data, status, headers, config) {
				ng.mensagens('alert-success','<strong>'+msg+'</strong>','.alert-list');
				ng.showBoxNovo();
				ng.reset();
				ng.load();
				btn.button('reset');
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
				if(status == 406) {
					var errors = data;
					$.each(errors, function(i, item) {
						$("#"+i).addClass("has-error");
						var formControl = $("#"+i)
							.attr("data-toggle", "tooltip")
							.attr("data-placement", "bottom")
							.attr("title", item)
							.attr("data-original-title", item);
						formControl.tooltip();
					});
					$('html,body').animate({scrollTop: $('.has-error').eq(0).offset().top-50},'slow');
					$('.has-error').eq(0).tooltip('show');
				}else{
					ng.mensagens('alert-danger','<strong>Erro ao efetuar cadastro!</strong>');
					$('html,body').animate({scrollTop: 0},'slow');
				}
			});
	}

	ng.editar = function(item) {
		$('html,body').animate({scrollTop: 0},'slow');
		ng.situacaoEspecial = angular.copy(item);
		ng.situacaoEspecial.produto_cliente = null ;
		ng.showBoxNovo(true);
		ng.loadProdutoCliente(ng.situacaoEspecial.cod_situacao_especial);
	}

	ng.loadProdutoCliente = function(cod_situacao_especial) {
		aj.get(baseUrlApi()+"situacao_especial/produto_cliente/"+cod_situacao_especial)
			.success(function(data, status, headers, config) {
				ng.situacaoEspecial.produto_cliente = data ;
			})
			.error(function(data, status, headers, config) {
				if(status == 404) {
					ng.situacaoEspecial.produto_cliente = [] ;
				}
			});
	}

	ng.delete = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem Certeza que Deseja Excluir Esta Situação Especial ?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"situacao_especial/delete/"+item.cod_situacao_especial)
				.success(function(data, status, headers, config) {
					ng.load();
					ng.mensagens('alert-success','<strong>Regime excluido com sucesso</strong>','.alert-list');
					ng.reset();
				})
				.error(function(data, status, headers, config) {
					ng.mensagens('alert-danger','<strong>Erro ao excluir</strong>','.alert-list');
				});
		}, undefined);
	}

	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	ng.selProduto = function(busca_cdb){
   		ng.busca.produtos = "" ;
   		ng.loadProduto(0,10);
   		$('#list_produtos').modal('show');
   	}

   	ng.loadProduto = function(offset,limit) {
		ng.produtos = [];

		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;

		var query_string = "?tpe->id_empreendimento="+ng.userLogged.id_empreendimento;

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
					ng.produtos = [];
					ng.paginacao.produtos = [];
				}
			});
	}

	ng.addProduto = function(item){
		ng.produto_cliente.cod_produto = item.id ;
		ng.produto_cliente.nme_produto = item.nome ;
		$("#list_produtos").modal("hide");
	}

	ng.selCliente = function(){
		var offset = 0  ;
    	var limit  =  10 ;;

		ng.loadCliente(offset,limit);
		$("#list_clientes").modal("show");
	}


	ng.addCliente = function(item){
		ng.produto_cliente.cod_cliente = item.id ;
		ng.produto_cliente.nme_cliente = item.nome ;
		$("#list_clientes").modal("hide");
	}

	ng.loadCliente= function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.clientes = [];
		query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+"&usu->id[exp]= NOT IN("+ng.configuracoes.id_cliente_movimentacao_caixa+","+ng.configuracoes.id_usuario_venda_vitrine+"))";

		if(ng.busca.clientes != ""){
			query_string += "&"+$.param({'(usu->nome':{exp:"like'%"+ng.busca.clientes+"%' OR usu.apelido LIKE '%"+ng.busca.clientes+"%')"}});
		}

		aj.get(baseUrlApi()+"usuarios/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				$.each(data.usuarios,function(i,item){
					ng.clientes.push(item);
				});
				ng.paginacao_clientes = [];
				$.each(data.paginacao,function(i,item){
					ng.paginacao_clientes.push(item);
				});
			})
			.error(function(data, status, headers, config) {
				ng.clientes = false ;
			});
	}

	ng.produto_cliente = {
			cod_situacao_especial 	: null,
			cod_produto 			: null,
			cod_cliente 			: null,
			dsc_texto_legal 		: null
	}

	ng.incluirProdutoCliente = function(){
		$('.has-error').tooltip('destroy')
		$('.has-error').removeClass('has-error')
		var error = 0 ;
		/*if(empty(ng.produto_cliente.cod_cliente)){
			$("#produto-cliente-nme_cliente").addClass("has-error");
			var formControl = $("#produto-cliente-nme_cliente")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", "Selecione um cliente")
				.attr("data-original-title", "Selecione um cliente");
			formControl.tooltip();
			error ++ ;
		}	*/
		if(empty(ng.produto_cliente.cod_produto)){
			$("#produto-cliente-nme_produto").addClass("has-error");
			var formControl = $("#produto-cliente-nme_produto")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", "Selecione um produto")
				.attr("data-original-title", "Selecione um produto");
			formControl.tooltip();
			error ++ ;
		}

		if(error > 0)	
			return

		if(ng.editingProdutoCliente){
			ng.situacaoEspecial.produto_cliente[ng.produto_cliente.index] = {
				cod_situacao_especial_produto_cliente : ng.produto_cliente.cod_situacao_especial_produto_cliente,
				cod_situacao_especial 	: ng.produto_cliente.cod_situacao_especial,
				cod_produto 			: ng.produto_cliente.cod_produto,
				cod_cliente 			: ng.produto_cliente.cod_cliente,
				dsc_texto_legal 		: ng.produto_cliente.dsc_texto_legal,
				nme_cliente             : ng.produto_cliente.nme_cliente,
				nme_produto             : ng.produto_cliente.nme_produto
			};
		}else{
			ng.situacaoEspecial.produto_cliente.push({
				cod_situacao_especial 	: null,
				cod_produto 			: ng.produto_cliente.cod_produto,
				cod_cliente 			: ng.produto_cliente.cod_cliente,
				dsc_texto_legal 		: ng.produto_cliente.dsc_texto_legal,
				nme_cliente             : ng.produto_cliente.nme_cliente,
				nme_produto             : ng.produto_cliente.nme_produto
			});
		}

		ng.produto_cliente = {
			cod_situacao_especial 	: null,
			cod_produto 			: null,
			cod_cliente 			: null,
			dsc_texto_legal 		: null
		}

		ng.editingProdutoCliente = false ;
	}

	ng.itemEditing = function(index){
		return (Number(ng.produto_cliente.index) == Number(index) && ng.editingProdutoCliente) ;
	}

	ng.delProdutoCliente = function(index){
		ng.situacaoEspecial.produto_cliente.splice(index,1);
	}

	ng.editarProdutoCliente = function(item,index){
		ng.produto_cliente = {
			cod_situacao_especial_produto_cliente : item.cod_situacao_especial_produto_cliente,
			cod_situacao_especial 				  : item.cod_situacao_especial,
			cod_produto 						  : item.cod_produto,
			cod_cliente 						  : item.cod_cliente,
			dsc_texto_legal 					  : item.dsc_texto_legal,
			nme_produto             			  : item.nme_produto,
			nme_cliente 						  : item.nme_cliente,
			index                   			  : index
		}
		ng.editingProdutoCliente = true ;
	}

	ng.loadConfig = function(){
		aj.get(baseUrlApi()+"configuracoes/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.configuracoes = data ;
			})
			.error(function(data, status, headers, config) {
				if(status == 404){
					ng.caixa_configurado = false ;
				}
			});
	}

	ng.load(0,10);
	ng.loadConfig() ;

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

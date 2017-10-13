app.controller('DetalhesController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 		   = baseUrl();
	ng.userLogged 	   = UserService.getUserLogado();
	ng.produto         = {};
	ng.exists		   = null ; 
	ng.desejo 		   = {id_usuario:ng.userLogged.id,id_empreendimento:ng.userLogged.id_empreendimento} ;

	var params         = getUrlVars();

	ng.loadProduto = function() {
		var query_string = "?tpe->id_empreendimento="+$scope.userLogged.id_empreendimento+"&tpe->id_produto="+params.produto+"&tp->flg_excluido=0";

		aj.get(baseUrlApi()+"estoque_produtos/null/"+0+"/"+1+"/"+query_string+"&cplSql= ORDER BY tp.nome ASC, tt.nome_tamanho ASC, tcp.nome_cor ASC")
		//aj.get(baseUrlApi()+"grade/produto/" + params.produto + "?grd->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {		
				data = data.produtos[0];
				if(ng.userLogged.perc_venda == "perc_venda_atacado"){
					data.valor_produto = data.vlr_venda_atacado;
				}else if(ng.userLogged.perc_venda == "perc_venda_varejo"){
					data.valor_produto	= data.vlr_venda_varejo;
				}else if(ng.userLogged.perc_venda == "perc_venda_intermediario"){
					data.valor_produto	= data.vlr_venda_intermediario;
				}else{
					data.valor_produto = data.vlr_venda_atacado;
				}

				if(data.img == null){
					data.img = "assets/imagens/produtos/730x730.gif";
				}

				ng.produto = data;

			})
			.error(function(data, status, headers, config) {
				ng.produto = [];
			});
	}

	ng.addCarrinho = function() {
		var produto = {
			id_produto      : ng.produto.id_produto,
			nome 			: ng.produto.nome,
			img  			: ng.produto.img,
			nome_fabricante : ng.produto.nome_fabricante,
			peso 			: ng.produto.peso,
			valor_produto 	: ng.produto.valor_produto,
			qtd 			: 1

		}
		aj.post(baseUrl()+"util/loja/carrinho.php",{produto:produto,acao:'add'})
			.success(function(data, status, headers, config) {				
				ng.exists = true ;
			})
			.error(function(data, status, headers, config) {
				
			});
	}

	ng.delCarrinho = function(id_produto){
		aj.post(baseUrl()+"util/loja/carrinho.php",{id_produto:id_produto,acao:'del'})
			.success(function(data, status, headers, config) {		
				ng.exists = false ;
			})
			.error(function(data, status, headers, config) {
			});
	}

	ng.exists = function() {
		aj.post(baseUrl()+"util/loja/carrinho.php",{id_produto:params.produto,acao:'exists'})
			.success(function(data, status, headers, config) {				
				ng.exists = true ;
			})
			.error(function(data, status, headers, config) {
				ng.exists = false ;
			});
	}

	ng.loadEmpreendimento = function(id_empreendimento) {
		aj.get(baseUrlApi()+"empreendimento/"+id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.empreendimento = data;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.empreendimento = [];
			});
	}

	ng.resetDesejo = function (){
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$($(".has-error").find("button")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
		ng.desejo.sabor_desejado	= null ;
		ng.desejo.qtd   	   		= null ;
	}

	ng.semEstoque = function(item){
		ng.resetDesejo();
		if(item.qtd_real_estoque <= 0){
			ng.desejo.nome_produto 		= item.nome ;
			ng.desejo.id_produto   		= item.id_produto ;
			ng.desejo.sabor_desejado	= null ;
			ng.desejo.qtd   	   		= null ;
			$("#modal-desejo").modal('show');
		}else{
			window.location.href = baseUrl()+"hage/detalhes?produto=" + item.id_produto 
		}

		return false;
	}

	ng.salvarDesejo = function(){
		var btn = $('#btn-salvar-desejo');
   		btn.button('loading');
		aj.post(baseUrlApi()+"clientes/desejos/",ng.desejo)
		.success(function(data, status, headers, config) {
			btn.button('reset');
			ng.resetDesejo();
			ng.mensagens('alert-success','Seu pedido foi enviado com sucesso!','.alert-desejo');
		})
		.error(function(data, status, headers, config) {
			btn.button('reset');
			if(status == 406){
				var errors = data;

				$.each(errors, function(i, item) {
					$("#"+i).addClass("has-error");

					var formControl = $($("#"+i).find(".form-control")[0])
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", item)
						.attr("data-original-title", item);
					formControl.tooltip();
				});
			}else{
				alert('Ocorreu um erro inesperado');
			}
				
		});	
	}

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	ng.exists();
	ng.loadProduto();
	ng.loadEmpreendimento(ng.userLogged.id_empreendimento);
});

app.controller('CarrinhoController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 		   = baseUrl();
	ng.userLogged 	   = UserService.getUserLogado();
	ng.carrinho 	   = [] ;
	ng.out_produtos    = [] ;

	ng.qtd_total 	   = 0 ;
	ng.valor_total     = 0 ;

	ng.calculaTotais = function(){
		var qtd   = 0 ;
		var valor = 0;
		$.each(ng.carrinho,function(index, value){
			qtd   += value.qtd == '' ? 1 : Number(value.qtd);
			var qtd_atual = value.qtd == '' ? 1 : Number(value.qtd);
			valor += (qtd_atual * Number(value.valor_produto)); 
		});

		ng.qtd_total   = qtd ;
		ng.valor_total = valor;
	}

	ng.loadCarrinho = function(){
		ng.carrinho = [] ;
		aj.post(baseUrl()+"util/loja/carrinho.php",{acao:'get_carrinho'})
			.success(function(data, status, headers, config) {	
				$.each(data,function(i,value){
					ng.carrinho.push(value);
				});			
				ng.calculaTotais();
				console.log(ng.carrinho);
			})
			.error(function(data, status, headers, config) {
			});
	}

	ng.delCarrinho = function(id_produto){
		index = ng.out_produtos.indexOf(id_produto);
		if(index < 0)
			ng.out_produtos.splice(index,1);

		aj.post(baseUrl()+"util/loja/carrinho.php",{id_produto:id_produto,acao:'del'})
			.success(function(data, status, headers, config) {		
				ng	.loadCarrinho();	
				ng.mensagens("alert-success","Produto retirado com sucesso do carrinho");
			})
			.error(function(data, status, headers, config) {
			});
	}

	ng.limparCarrinho = function(){
		aj.post(baseUrl()+"util/loja/carrinho.php",{acao:'cancelar'})
			.success(function(data, status, headers, config) {		
				ng.loadCarrinho();	
			})
			.error(function(data, status, headers, config) {
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

	ng.chageQtd = function(item){
		item.qtd = item.qtd == '0' ? 1 : item.qtd ;
		produto  = angular.copy(item);
		produto.qtd = produto.qtd == "" ? 1 : produto.qtd;
		aj.post(baseUrl()+"util/loja/carrinho.php",{produto:produto,acao:'add'})
			.success(function(data, status, headers, config) {				
				  ng.calculaTotais();
			})
			.error(function(data, status, headers, config) {
				alert('ocorreu um erro inesperado !!')
			});
	}

	ng.confirmar = function(){
		$(".tr_produtos_carrinho td").css({background:"#f9f9f9"});
		var btn = $('#btn-confirmar-venda');
		btn.button('loading');
		ng.out_produtos = [] ;
		var venda = {
						id_empreendimento:ng.userLogged.id_empreendimento,
						id_cliente       :ng.userLogged.id,
						venda_confirmada :0
					}
		aj.post(baseUrl()+"util/loja/carrinho.php",{acao:'get_carrinho'})
			.success(function(produtos, status, headers, config) {	
				aj.post(baseUrlApi()+"venda/loja",{produtos:produtos,venda:venda})
				.success(function(data, status, headers, config) {	
					ng.mensagens("alert-success","Sua compra foi efetuada com sucesso");
					ng.limparCarrinho();
					btn.button('reset');
				})
				.error(function(data, status, headers, config) {
					ng.out_produtos = data ;
					btn.button('reset');
					if(status == 406){
						$.each(data,function(i, value){
							$("#"+value+" td").css({background:"#FF9191"});
						});
					}
				});
			})
			.error(function(data, status, headers, config) {
			});
	}

	ng.mensagens = function(classe, msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}
	
	ng.loadCarrinho();
	ng.loadEmpreendimento(ng.userLogged.id_empreendimento);
});

app.controller('VitrineController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 		   = baseUrl();
	ng.userLogged 	   = UserService.getUserLogado();
	ng.busca		   = { nome: "", id_categoria: null, id_fabricante: null };
	ng.paginacao       = { grade: null }; 
	ng.desejo 		   = { id_usuario: ng.userLogged.id, id_empreendimento: ng.userLogged.id_empreendimento };

	ng.configuracoes = {} ;
	ng.loadConfig = function(){
		aj.get(baseUrlApi() +"configuracoes/"+ ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.configuracoes = data;
				ng.loadGrade(0,12);
			});
	}

	ng.loadGrade = function(offset,limit) {
		offset   = offset == null ? 0  : offset;
    	limit    = limit  == null ? 10 : limit;
		
		ng.grade = [];
		ng.errorBusca = false ;
		
		var arr = [];
		
		var query_string = "?tpe->id_empreendimento="+$scope.userLogged.id_empreendimento+"&tp->flg_excluido=0";

		if(!empty(ng.configuracoes.flg_deposito_padrao_vitrine) && ng.configuracoes.flg_deposito_padrao_vitrine == 1) {
			if(!empty(ng.configuracoes.id_deposito_padrao)) {
				var sem_estoque_str = "";

				if(!empty(ng.configuracoes.flg_exibir_produtos_sem_estoque) && ng.configuracoes.flg_exibir_produtos_sem_estoque == 1)
					sem_estoque_str = " OR tde.id_deposito IS NULL";

				query_string += "&"+$.param({'(tde->id_deposito=':{exp: ng.configuracoes.id_deposito_padrao + sem_estoque_str +")"}});
			}
		}

    	if($scope.busca.nome != ""){
    		query_string += "&"+$.param({'(tp->nome':{exp:"like'%"+$scope.busca.nome+"%' OR tf.nome_fabricante like'%"+$scope.busca.nome+"%' OR tc.descricao_categoria like'%"+$scope.busca.nome+"%')"}});
    	}

		if(ng.busca.id_categoria != null && ng.busca.id_categoria != ""){
			query_string += "&tp->id_categoria="+ ng.busca.id_categoria;
		}

		if(ng.busca.id_fabricante != null && ng.busca.id_fabricante != ""){
			query_string += "&tp->id_fabricante="+ ng.busca.id_fabricante;
		}

		//aj.get(baseUrlApi()+"grade/"+offset+"/"+limit+"/?grd->id_empreendimento="+ng.userLogged.id_empreendimento+query_string)
		aj.get(baseUrlApi()+"estoque_produtos/null/"+offset+"/"+limit+"/"+query_string+"&cplSql= ORDER BY tp.nome ASC, tt.nome_tamanho ASC, tcp.nome_cor ASC")
			.success(function(data, status, headers, config) {	
				$.each(data.produtos,function(index,value){
					if(ng.userLogged.perc_venda == "perc_venda_atacado"){
						value.valor_produto = value.vlr_venda_atacado;
					}else if(ng.userLogged.perc_venda == "perc_venda_varejo"){
						value.valor_produto	= value.vlr_venda_varejo;
					}else if(ng.userLogged.perc_venda == "perc_venda_intermediario"){
						value.valor_produto	= value.vlr_venda_intermediario;
					}

					if(value.img == null){
						value.img = "assets/imagens/produtos/730x730.gif";
					}
					
					arr.push(value);
					if((index + 1) % 4 == 0){
						ng.grade.push(arr);
						arr = []
					}
				});

				if(data.produtos.length % 4 != 0){	
					ng.grade.push(arr);
				}

				ng.paginacao.grade = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.grade 			= [];
				ng.paginacao.grade  = [];
				ng.errorBusca = true ;
			});
	}

	ng.loadFabricantes = function() {

		aj.get(baseUrlApi()+"fabricantes?tfe->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {				
				ng.fabricantes = data.fabricantes ;
			})
			.error(function(data, status, headers, config) {
				ng.fabricantes = [];
			});
	}

	ng.loadCategorias = function() {

		aj.get(baseUrlApi()+"categorias?tce->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {				
				ng.categorias = data.categorias ;
			})
			.error(function(data, status, headers, config) {
				ng.categorias = [];
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
			
	ng.loadConfig();
	ng.loadFabricantes();
	ng.loadCategorias();
	ng.loadEmpreendimento(ng.userLogged.id_empreendimento);
});

app.controller('FaixaDescontoPermitidoController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
	ng.categoria 	= {};
    ng.categorias	= [];
    ng.faixa        = {perc_desconto_min:0,perc_desconto_max:0,usuarios:[]};
    ng.busca        = {usuarios:''};
    ng.emptyBusca   = {usuarios:false} 	
    ng.editing = false;

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


	ng.mensagens = function(classe , msg){
		$('.alert-sistema').fadeIn().addClass(classe).html(msg);

		setTimeout(function(){
			$('.alert-sistema').fadeOut('slow');
		},5000);
	}

	ng.reset = function() {
		ng.faixa   = {perc_desconto_min:0,perc_desconto_max:0,usuarios:[]};
		ng.removeError();
		ng.editing = false;
	}


	ng.paginacao_faixas = [] ;
	ng.loadFaixas = function(offset,limit) {
		offset = offset == null ? 0  : offset ;
		limit = limit   == null ? 10 : limit ;
		aj.get(baseUrlApi()+"faixasdescontopermitido/"+offset+"/"+limit+"?tfdp->id_empreendimento="+ng.userLogged.id_empreendimento+'&tfdp->flg_excluido=0')
			.success(function(data, status, headers, config) {
				ng.faixas = data.faixas;
				ng.paginacao_faixas = data.paginacao;
			})
			.error(function(data, status, headers, config) {
					ng.faixas = [];
					ng.paginacao_faixas = [];
			});
	}

	ng.salvar = function() {
		var url = 'faixadescontopermitido';
		var itemPost = angular.copy(ng.faixa);
		itemPost.id_empreendimento  = ng.userLogged.id_empreendimento;
		var msg      = 'Faixa salva com sucesso!';
		var btn  	 = $('#btn-salvar-faixa');
		btn.button('loading');

		ng.removeError();
		itemPost.perc_desconto_min = itemPost.perc_desconto_min/100;
		itemPost.perc_desconto_max = itemPost.perc_desconto_max/100;

		if(ng.faixa.id != null && ng.faixa.id > 0) {
			itemPost.id  = ng.faixa.id;
			url 		+= '/update';
			var msg      = 'Faixa atualizada com sucesso!';
			itemPost.delete_usuarios = ng.delete_usuarios;
			if(itemPost.usuarios.length > 0){
				var usuarios = [] ;
				$.each(itemPost.usuarios,function(i,v){
					if(empty(v.id_rel)){
						var item = {
							id_usuario : Number(v.id),
							id_responsavel_atv  : ng.userLogged.id ,
							flg_ativo : 1,
						}
						usuarios.push(item);
					}
				});

				itemPost.usuarios = usuarios ;
			}else
				delete itemPost.usuarios ;

		}else{
			if(itemPost.usuarios.length > 0){
				var usuarios = [] ;
				$.each(itemPost.usuarios,function(i,v){
					var item = {
						id_usuario : Number(v.id),
						id_responsavel_atv  : ng.userLogged.id ,
						flg_ativo : 1,
					}
					usuarios.push(item);
				});

				itemPost.usuarios = usuarios ;
			}else
				delete itemPost.usuarios ;
		}


		console.log(itemPost);

		aj.post(baseUrlApi()+url, itemPost)
			.success(function(data, status, headers, config) {
				btn.button('reset');
				ng.mensagens('alert-success','<strong>'+msg+'</strong>');
				ng.showBoxNovo();
				ng.reset();
				ng.loadFaixas();
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
				if(status == 406) {
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
				}
			});
	}

	ng.removeError = function(){
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").tooltip('destroy');
		$(".has-error").css({border:"none",background: 'none'}).addClass('has-error');
		$(".has-error").removeClass("has-error");
	}

	ng.editar = function(item) {
		var item = angular.copy(item);
		ng.faixa.usuarios = item.usuarios ;
		item.perc_desconto_min = item.perc_desconto_min * 100;
		item.perc_desconto_max = item.perc_desconto_max * 100;
		ng.faixa = angular.copy(item);
		ng.showBoxNovo(true);
	}

	ng.delete = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja excluir está faixa?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"faixadesconto/delete/"+item.id)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Faixa excluido com sucesso</strong>');
					ng.loadFaixas();
					ng.loadCores();
				})
				.error(defaulErrorHandler);
		}, undefined);
	}

	ng.loadPerfis = function() {
		aj.get(baseUrlApi()+"perfis/0/1000?tpue->id_empreendimento="+ ng.userLogged.id_empreendimento +"&cplSql= ORDER BY tp.nome ASC")
			.success(function(data, status, headers, config) {
				ng.perfis = data;
			})
			.error(function(data, status, headers, config) {
				ng.perfis = null;
			});
	}

	//funções do modal de usuarios

	ng.loadUsuarios= function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;

		ng.emptyBusca.usuarios = false ;
		ng.paginacao_usuarios  = [];
		ng.usuarios = [];

		var perfisToFilter = _.filter(ng.perfis.perfis, function(perfil){ if(perfil.nome == 'caixa' || perfil.nome == 'vendedor interno' || perfil.nome == 'vendedor externo') { return perfil; } });
		var perfisIndexes = '';
		$.each(perfisToFilter, function(i, item){
			perfisIndexes += item.id;
			if((i+1) < perfisToFilter.length)
				perfisIndexes += ',';
		});

		query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+")&usu->flg_tipo=usuario";

		if(ng.busca.usuarios != ""){
			query_string += "&"+$.param({'(usu->nome':{exp:"like'%"+ng.busca.usuarios+"%' OR usu.apelido LIKE '%"+ng.busca.usuarios+"%')"}});
		}

		aj.get(baseUrlApi()+"usuarios/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				$.each(data.usuarios,function(i,item){
					ng.usuarios.push(item);
				});
				ng.paginacao_usuarios = [];
				$.each(data.paginacao,function(i,item){
					ng.paginacao_usuarios.push(item);
				});
			})
			.error(function(data, status, headers, config) {
				if(status == 404){
					ng.usuarios = [] ;
					ng.emptyBusca.usuarios = true ;
				}else{
					alert('Ocorreu um erro ao carregar os usuários');
				}
			});
	}

	ng.selUsuario = function(){
		var offset = 0  ;
    	var limit  =  10 ;;

			ng.loadUsuarios(offset,limit);
			$("#list_usuarios").modal("show");
	}

	ng.addUsuario = function(item){
		ng.faixa.usuarios.push(item);
	}

	ng.usuarioSelecionado = function(id){
		var x = false ;
		$.each(ng.faixa.usuarios,function(i,v){
			if(Number(id) == Number(v.id)){
				x = true ;
				return ;
			}				
		});
		return x ;
	}


	ng.delete_usuarios = [] ;
	ng.delUsuario = function(index,item){
		ng.faixa.usuarios.splice(index,1);
		if(!empty(item.id_rel))
			ng.delete_usuarios.push(item);
	}

	//fim
	
	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	ng.loadFaixas();
	ng.loadPerfis();
});

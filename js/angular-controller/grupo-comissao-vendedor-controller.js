app.controller('GrupoComissaoVendedorController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 					= baseUrl();
	ng.userLogged 				= UserService.getUserLogado();
	ng.grupoComissaoVendedor 	= {};
    ng.grupoComissaoVendedores	= [];
    ng.paginacao    			= {grupoComissaoVendedores : [] } ;

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
		ng.grupoComissaoVendedor = {};
		ng.editing = false;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	ng.busca = { text: "" };
	ng.resetFilter = function() {
		ng.busca.text = "" ;
		ng.reset();
		ng.load(0,10);
	}

	ng.load = function(offset, limit) {
		offset = offset == null ? 0 : offset ;
		limit  = limit  == null ? 10 : limit ;

		var query_string = "?id_empreendimento="+ ng.userLogged.id_empreendimento;

		if(ng.busca.text != "")
			query_string += "&("+$.param({nme_grupo_comissao:{exp:"like '%"+ng.busca.text+"%' OR id = '"+ng.busca.text+"'"}})+")";

		aj.get(baseUrlApi()+"grupo/comissao/vendedores/" + offset + "/" + limit + query_string)
			.success(function(data, status, headers, config) {
				ng.grupoComissaoVendedores = data.grupoComissaoVendedores;
				ng.paginacao.grupoComissaoVendedores = data.paginacao ;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.grupoComissaoVendedores = null;
			});
	}

	ng.salvar = function() {
		var url = 'grupo/comissao/vendedor';
		var itemPost = {};
		var btn      = $("#btn-salvar");
		btn.button('loading');

		if(ng.grupoComissaoVendedor.id != null && ng.grupoComissaoVendedor.id > 0) {
			itemPost.id = ng.grupoComissaoVendedor.id;
			url += '/update';
		}

		itemPost.id_empreendimento 	= ng.userLogged.id_empreendimento;
		itemPost.nme_grupo_comissao = ng.grupoComissaoVendedor.nme_grupo_comissao;
		itemPost.perc_comissao 		= ng.grupoComissaoVendedor.perc_comissao / 100;

		aj.post(baseUrlApi()+url, itemPost)
			.success(function(data, status, headers, config) {
				ng.mensagens('alert-success','<strong>Grupo salvo com sucesso!</strong>');
				ng.showBoxNovo();
				ng.reset();
				ng.load();
				btn.button('reset');
			})
			.error(function(data, status, headers, config) {
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
				btn.button('reset');
			});
	}

	ng.editar = function(item) {
		ng.grupoComissaoVendedor = angular.copy(item);
		ng.grupoComissaoVendedor.perc_comissao = item.perc_comissao * 100;
		ng.showBoxNovo(true);
	}

	ng.delete = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja excluir este grupo?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"grupo/comissao/vendedor/delete/"+item.id)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Grupo excluido com sucesso</strong>');
					ng.reset();
					ng.load();
				})
				.error(defaulErrorHandler);
		}, undefined);
	}

	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	ng.load();
});

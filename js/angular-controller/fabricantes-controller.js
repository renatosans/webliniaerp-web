app.controller('FabricantesController', function($scope, $http, $window, $dialogs, UserService, PrestaShop){
	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
	ng.fabricante 	= {};
    ng.fabricantes	= [];
    ng.paginacao    = {fabricantes : [] } ;
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
		ng.fabricante = {};
		ng.empreendimentosAssociados = [{ id : ng.userLogged.id_empreendimento,nome_empreendimento:ng.userLogged.nome_empreendimento }];
		ng.editing = false;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}
	
	ng.busca = { text: "", empreendimento: "" };
	ng.empreendimentosAssociados = [{ id : ng.userLogged.id_empreendimento,nome_empreendimento:ng.userLogged.nome_empreendimento,flg_visivel:1 }];
	ng.showEmpreendimentos = function() {
		$('#list_empreendimentos').modal('show');
		ng.loadAllEmpreendimentos(0,10);
	}

	ng.loadAllEmpreendimentos = function(offset, limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

    	var query_string = "?id_usuario="+ng.userLogged.id;
    	if(ng.busca.empreendimento != ""){
    		query_string += "&" +$.param({nome_empreendimento:{exp:"like'%"+ng.busca.empreendimento+"%'"}});
    	}

    	ng.empreendimentos = [];
		aj.get(baseUrlApi()+"empreendimentos/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.empreendimentos = data.empreendimentos;
				ng.paginacao.empreendimentos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.empreendimentos = [];
			});
	}

	ng.loadEmpreendimentosByFabricante = function() {
		aj.get(baseUrlApi()+"empreendimentos/ref/fabricante/"+ng.fabricante.id)
			.success(function(data, status, headers, config) {
				ng.empreendimentosAssociados = [];
				ng.empreendimentosAssociados = data;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.empreendimentos = [];
			});
	}

	ng.empreendimentoIsSelected = function(item){
		var r = false ;
		$.each(ng.empreendimentosAssociados,function(i,v){
			if(Number(item.id)==Number(v.id)){
				r = true ;
				return;
			}
		});
		return r ;
	}

	ng.delEmpreendimento = function(item) {
		ng.empreendimentosAssociados.pop(item);
	}

	ng.addEmpreendimento = function(item) {
		if(ng.empreendimentosAssociados == null)
			ng.empreendimentosAssociados = [];

		var s = true;

		$.each(ng.empreendimentosAssociados, function(i, emp) {
			if(emp.id == item.id)
				s = false;
		});

		if(s) {
			ng.empreendimentosAssociados.push(item);
		}
		else {
			$('#list_empreendimentos').modal('hide');
			ng.mensagens('alert-danger','<strong>Este empreendimento já foi adicionado a listagem</strong>');
		}
	}

	ng.resetFilter = function() {
		ng.busca.text = "" ;
		ng.reset();
		ng.load(0,10);
	}

	ng.load = function(offset, limit) {
		offset = offset == null ? 0 : offset ;
		limit  = limit  == null ? 10 : limit ;

		var query_string = "?tfe->id_empreendimento="+ ng.userLogged.id_empreendimento;

		if(ng.busca.text != ""){
			query_string += "&("+$.param({nome_fabricante:{exp:"like'%"+ng.busca.text+"%')"}});
		}	

		aj.get(baseUrlApi()+"fabricantes/"+ offset +"/"+ limit + query_string)
			.success(function(data, status, headers, config) {
				ng.fabricantes = data.fabricantes;
				ng.paginacao.fabricantes = data.paginacao ;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.fabricantes = [];
			});
	}

	ng.salvar = function() {
		var url = 'fabricante';
		var itemPost = {};

		if(ng.empreendimentosAssociados == null || ng.empreendimentosAssociados.length == 0) {
			ng.mensagens('alert-danger','<strong>Você deve selecionar ao menos um empreendimento</strong>');
			btn.button('reset');
			return false;
		}

		if(ng.fabricante.id != null && ng.fabricante.id > 0) {
			itemPost.id = ng.fabricante.id;
			url += '/update';
		}

		itemPost.id_empreendimento 	= ng.userLogged.id_empreendimento;
		itemPost.nome_fabricante 	= ng.fabricante.nome_fabricante;
		itemPost.empreendimentos = ng.empreendimentosAssociados;

		/*aj.post(baseUrlApi()+url, itemPost)
			.success(function(data, status, headers, config) {
				if(!empty(data.fabricante) && !empty(data.fabricante.id))
					itemPost.id = data.fabricante.id ;
				PrestaShop.send('post',baseUrlApi()+"prestashop/fabricante",itemPost);
				ng.mensagens('alert-success','<strong>Fabricante salvo com sucesso!</strong>');
				ng.showBoxNovo();
				ng.reset();
				ng.load();
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
			});*/


		$('#form-fabricante').ajaxForm({
		 	url: baseUrlApi()+url,
		 	type: 'post',
		 	data:itemPost,
		 	success:function(data){
		 		if(!empty(data.fabricante) && !empty(data.fabricante.id))
					itemPost.id = data.fabricante.id ;
				PrestaShop.send('post',baseUrlApi()+"prestashop/fabricante",itemPost);
				ng.mensagens('alert-success','<strong>Fabricante salvo com sucesso!</strong>');
				ng.showBoxNovo();
				ng.reset();
				ng.load();
		 	},
		 	error:function(data){
		 		if(data.status == 406) {
					var errors = data.responseJSON;

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
		 	}
		}).submit();


	}

	ng.editar = function(item) {
		ng.fabricante = angular.copy(item);
		ng.showBoxNovo(true);
		ng.loadEmpreendimentosByFabricante();
	}

	ng.delete = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja excluir este fabricante?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"fabricante/delete/"+item.id)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Fabricante excluido com sucesso</strong>');
					ng.reset();
					ng.load();
					PrestaShop.send('delete',baseUrlApi()+"prestashop/fabricante/"+item.id+"/"+ng.userLogged.id_empreendimento);
				})
				.error(defaulErrorHandler);
		}, undefined);
	}

	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	ng.load();
});

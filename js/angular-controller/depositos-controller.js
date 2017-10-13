app.controller('DepositosController', function($scope, $http, $window, $dialogs, UserService,FuncionalidadeService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 						= baseUrl();
	ng.userLogged 					= UserService.getUserLogado();
	ng.deposito 					= {};
    ng.depositos					= [];
    ng.empreendimentos 				= [];
    ng.paginacao           			= {} ;
    ng.busca               			= {empreendimento:"", text: ""} ;
    ng.empreendimentosAssociados = [{ id : ng.userLogged.id_empreendimento,nome_empreendimento:ng.userLogged.nome_empreendimento,flg_visivel:1 }];
    ng.editing = false;

    ng.funcioalidadeAuthorized = function(cod_funcionalidade){
		return FuncionalidadeService.Authorized(cod_funcionalidade,ng.userLogged.id_perfil,ng.userLogged.id_empreendimento);
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

	ng.showEmpreendimentos = function() {
		$('#list_empreendimentos').modal('show');
		ng.loadAllEmpreendimentos(0,10);
	}

	ng.mensagens = function(classe , msg){
		$('.alert-sistema').removeClass("alert-danger");
		$('.alert-sistema').removeClass("alert-success");
		$('.alert-sistema').removeClass("alert-warning");
		$('.alert-sistema').removeClass("alert-info");
		$('.alert-sistema')
			.fadeIn()
			.addClass(classe)
			.html(msg);

		setTimeout(function(){
			$('.alert-sistema').fadeOut('slow');
		},5000);
	}

	ng.reset = function() {
		ng.deposito = {};
		ng.empreendimentosAssociados = [{ id : ng.userLogged.id_empreendimento,nome_empreendimento:ng.userLogged.nome_empreendimento }];
		ng.editing = false;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
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

	ng.loadEmpreendimentosByDeposito = function() {
		aj.get(baseUrlApi()+"deposito/empreendimentos/"+ng.deposito.id)
			.success(function(data, status, headers, config) {
				ng.empreendimentosAssociados = [];
				ng.empreendimentosAssociados = data;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.empreendimentos = [];
			});
	}

	ng.resetFilter = function() {
		ng.busca.text = "" ;
		ng.reset();
		ng.loadDepositos(0,10);
	}

	ng.loadDepositos = function(offset,limit) {
		offset = offset == null ? 0 : offset ;
		limit  = limit == null ? 10 : limit ;

		var query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento;

		if(ng.busca.text != "")
			query_string += "&("+$.param({nme_deposito:{exp:"like '%"+ng.busca.text+"%' OR dep.id = '"+ng.busca.text+"'"}})+")";

		console.log(ng.userLogged.id_empreendimento);
		aj.get(baseUrlApi()+"depositos/"+offset+"/"+limit+ query_string)
			.success(function(data, status, headers, config) {
				ng.depositos = data.depositos;
				ng.paginacao.depositos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.depositos = [];
				ng.paginacao.depositos = [];
			});
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

	ng.salvar = function() {
		var btn = $('#btn-salvar-deposito');
		btn.button('loading');
		if(ng.empreendimentosAssociados == null || ng.empreendimentosAssociados.length == 0) {
			ng.mensagens('alert-danger','<strong>Você deve selecionar ao menos um empreendimento</strong>');
			btn.button('reset');
			return false;
		}

		var url = 'deposito?update=';
		var itemPost = {};

		if(ng.deposito.id != null && ng.deposito.id > 0) {
			itemPost.id = ng.deposito.id;
			url += 'true';
		}
		else
			url += 'false';

		itemPost.nme_deposito 		= ng.deposito.nme_deposito;
		itemPost.empreendimentos = ng.empreendimentosAssociados;

		aj.post(baseUrlApi()+url, itemPost)
			.success(function(data, status, headers, config) {
				ng.mensagens('alert-success','<strong>Deposito salvo com sucesso!</strong>');
				ng.showBoxNovo();
				ng.reset();
				ng.loadDepositos();
				btn.button('reset');
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

	ng.editar = function(item) {
		ng.deposito = angular.copy(item);
		ng.showBoxNovo(true);
		ng.loadEmpreendimentosByDeposito();
	}

	ng.delete = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja excluir este deposito?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"deposito/delete/"+item.id)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Deposito excluido com sucesso</strong>');
					ng.reset();
					ng.loadDepositos();
				})
				.error(defaulErrorHandler);
		}, undefined);
	}

	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	ng.loadAllEmpreendimentos();
	ng.loadDepositos();
});

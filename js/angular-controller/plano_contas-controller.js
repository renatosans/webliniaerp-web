app.controller('PlanoContasController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
	ng.planoConta 	= {};
    ng.planoContas	= [];
    ng.currentNode 	= null;
    ng.editing 		= false;


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
		$("#blockTree").css("display", "none");
		ng.editing = false;
		ng.planoConta = {};
		ng.currentNode = null;
		delete ng.id_delete ;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
		ng.loadPlanoContas();
	}

	ng.loadPlanoContas = function() {
		aj.get(baseUrlApi()+"plano_contas_treeview?tpc->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.planoContas = data;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.planoContas = [];
			});
	}

	ng.salvar = function() {
		var url  = ng.editing ? 'planocontas/update' : 'planocontas';
		var msg  = ng.editing ? 'Plano de contas atualizado com sucesso!' : 'Plano de contas salvo com sucesso!';

		if(ng.currentNode != null){
			ng.planoConta.id_plano_pai = ng.currentNode.id;
		}

		$($(".has-error").find(".form-control")).tooltip('destroy');
		$($(".has-error").find("button")).tooltip('destroy');
		$(".has-error").removeClass("has-error");

		ng.planoConta.id_empreendimento = ng.userLogged.id_empreendimento;

		aj.post(baseUrlApi()+url, ng.planoConta)
			.success(function(data, status, headers, config) {
				ng.mensagens('alert-success','<strong>'+msg+'</strong>');
				ng.reset();
				ng.loadPlanoContas();
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
			});
	}

	ng.editar = function() {
		ng.editing = true;
		ng.planoConta = angular.copy(ng.currentNode);
		ng.id_delete  = ng.currentNode.id;
		if( !(ng.planoConta.id_plano_pai == null || ng.planoConta.id_plano_pai == "") ){
			//ng.currentNode.nme_completo   = ng.planoConta.nme_completo_pai;
			ng.currentNode.id   = ng.currentNode.id_plano_pai;
		}else
			ng.currentNode = null;
		$("#blockTree").css("display", "block");
	}

	ng.delete = function(id_delete){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja excluir este Plano de Conta?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"planocontas/delete/"+id_delete)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Plano de Conta excluido com sucesso</strong>');
					ng.reset();
				})
				.error(defaulErrorHandler);
		}, undefined);
	}

	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	ng.loadPlanoContas();
});

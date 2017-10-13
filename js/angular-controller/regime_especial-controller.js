app.controller('RegimeEspecialController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 			= baseUrl();
	ng.userLogged 		= UserService.getUserLogado();
	ng.regimeEspecial 	= {
							dsc_regime_especial:null,
							dsc_texto_legal:null,
							cod_empreendimento:ng.userLogged.id_empreendimento
						 };
	ng.paginacao = {regimes:null} ;
    ng.regimes	= [];
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

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	ng.reset = function() {
		ng.regimeEspecial = {
							dsc_regime_especial:null,
							dsc_texto_legal:null,
							cod_empreendimento:ng.userLogged.id_empreendimento
						 };
		ng.editing = false;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	ng.load = function(offset,limit) {
		offset = offset == null ? 0 : offset ;
		limit  = limit  == null ? 10 : limit ;
		ng.regimes = null ;
		aj.get(baseUrlApi()+"regime_especial/get/"+offset+"/"+limit+"?cod_empreendimento="+ng.userLogged.id_empreendimento+"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				ng.regimes = data.regimes;
				ng.paginacao.regimes = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404){
					ng.regimes = [];
					ng.paginacao.regimes = [];
				}
			});
	}

	ng.salvar = function() {
		$('.has-error').tooltip('destroy')
		$('.has-error').removeClass('has-error')

		var btn = $("#salvar-regime-especial") ;
		btn.button('loading');
		var url = 'regime_especial';
		var itemPost = {};
		var msg = "Regime salvo com sucesso!";

		if(ng.regimeEspecial.cod_regime_especial != null && ng.regimeEspecial.cod_regime_especial > 0) {
			url += '/update';
			msg = 'Regime alterado com sucesso!'
		}

		itemPost = angular.copy(ng.regimeEspecial);

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
				}else
					ng.mensagens('alert-danger','<strong>Erro ao efetuar cadastro!</strong>');
			});
	}

	ng.editar = function(item) {
		ng.regimeEspecial = angular.copy(item);
		ng.showBoxNovo(true);
	}

	ng.delete = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem Certeza que Deseja Excluir Este Regime Especial ?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"regime_especial/delete/"+item.cod_regime_especial)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Regime excluido com sucesso</strong>','.alert-list');
					ng.reset();
					ng.load();
				})
				.error(function(data, status, headers, config) {
					ng.mensagens('alert-danger','<strong>Erro ao excluir</strong>','.alert-list');
				});
		}, undefined);
	}

	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	ng.load(0,10);

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

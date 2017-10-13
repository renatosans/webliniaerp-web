app.controller('ZoneamentoController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 			= baseUrl();
	ng.userLogged 		= UserService.getUserLogado();
	ng.zoneamento    	= {
							cod_zoneamento : null,
							dsc_zoneamento :null
						 };
	ng.paginacao = {zoneamentos:null} ;
    ng.zoneamentos	= [];
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
		ng.zoneamento = {
							cod_zoneamento: null,
							dsc_zoneamento: null
						 };
		ng.editing = false;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	ng.load = function(offset,limit) {
		offset = offset == null ? 0 : offset ;
		limit  = limit  == null ? 10 : limit ;
		ng.zoneamentos = null ;
		aj.get(baseUrlApi()+"zoneamento/get/"+offset+"/"+limit+"?cod_empreendimento="+ng.userLogged.id_empreendimento+"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				ng.zoneamentos = data.zoneamentos;
				ng.paginacao.zoneamentos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404){
					ng.zoneamentos = [];
					ng.paginacao.zoneamentos = [];
				}
			});
	}

	ng.salvar = function() {
		$('.has-error').tooltip('destroy')
		$('.has-error').removeClass('has-error')

		var btn = $("#salvar-zoneamento") ;
		btn.button('loading');
		var url = 'zoneamento';
		var itemPost = {};
		var msg = "Zoneamento salvo com sucesso!";

		if(ng.zoneamento.cod_zoneamento != null && ng.zoneamento.cod_zoneamento > 0) {
			url += '/update';
			msg = 'Zoneamento alterado com sucesso!';
		}

		itemPost = angular.copy(ng.zoneamento);
		itemPost.cod_empreendimento = ng.userLogged.id_empreendimento ;

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
		ng.zoneamento = angular.copy(item);
		ng.showBoxNovo(true);
	}

	ng.delete = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem Certeza que Deseja Excluir Este Zoneamento ?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"zoneamento/delete/"+item.cod_zoneamento)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Zoneamento excluido com sucesso</strong>','.alert-list');
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

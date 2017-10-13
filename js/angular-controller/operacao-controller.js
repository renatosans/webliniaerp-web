app.controller('OperacaoController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 			= baseUrl();
	ng.userLogged 		= UserService.getUserLogado();
	var operacao        = {
							cod_operacao 			: null ,
							id_empreendimento 		: null ,
							dsc_operacao 			: null ,
							num_cfop_produto 		: null ,
							num_cfop_produto_st 	: null ,
							num_cfop_mercadoria 	: null ,
							num_cfop_mercadoria_st 	: null ,
							num_cfop_mercadoria_st_subst 	: null ,
							cod_operacao_estorno 	: null ,
							cod_operacao_devolucao 	: null 
						 };
	ng.operacao    = angular.copy(operacao) ;
	ng.paginacao   = {operacao:null} ;
    ng.operacoes   = [];
    ng.editing     = false;

    ng.showBoxNovo = function(onlyShow){
    	ng.editing = !ng.editing;
    	
    	if(onlyShow) {
			$('i','#btn-novo').removeClass("fa-plus-circle").addClass("fa-minus-circle");
			$('#box-novo').show(400,function(){$("select").trigger("chosen:updated");});
			setTimeout(function(){$("select").trigger("chosen:updated") }, 600);
		}
		else {
			ng.loadOperacaoCombo(null);
			$('#box-novo').toggle(400, function(){
				if($(this).is(':visible')){
					$('i','#btn-novo').removeClass("fa-plus-circle").addClass("fa-minus-circle");
				}else{
					$('i','#btn-novo').removeClass("fa-minus-circle").addClass("fa-plus-circle");
				}
				$("select").trigger("chosen:updated");
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
		console.log('sdsad');
		ng.operacao = operacao ;
		ng.editing 	= false;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	ng.load = function(offset,limit) {
		offset = offset == null ? 0 : offset ;
		limit  = limit  == null ? 10 : limit ;
		ng.operacoes = null ;
		aj.get(baseUrlApi()+"operacao/get/"+offset+"/"+limit+"?cod_empreendimento="+ng.userLogged.id_empreendimento+"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				ng.operacoes = data.operacao;
				ng.paginacao.operacao = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404){
					ng.operacoes = [];
					ng.paginacao.operacao = [];
				}
			});
	}
	ng.chosen_operacao  = [{cod_operacao:null,dsc_operacao:'Selecione'}] ;
	ng.loadOperacaoCombo = function(id_exceto) {
		ng.chosen_operacao  = [{cod_operacao:null,dsc_operacao:'Selecione'}] ;
		var queryString = id_exceto == null ? "" : "&cod_operacao[exp]=!="+id_exceto;
		aj.get(baseUrlApi()+"operacao/get/?cod_empreendimento="+ng.userLogged.id_empreendimento+"&flg_excluido=0"+queryString)
			.success(function(data, status, headers, config) {
				ng.chosen_operacao = ng.chosen_operacao.concat(data.operacao);
				setTimeout(function(){
					$("select").trigger("chosen:updated");
				},300);
			})
			.error(function(data, status, headers, config) {
					
			});
	}

	//ng.loadOperacaoCombo(null);

	ng.salvar = function() {
		$('.has-error').tooltip('destroy')
		$('.has-error').removeClass('has-error')

		var btn = $("#salvar-operacao") ;
		btn.button('loading');
		var url = 'operacao';
		var itemPost = {};
		var msg = "Operação salva com sucesso!";

		if(ng.operacao.cod_operacao != null && ng.operacao.cod_operacao > 0) {
			url += '/update';
			msg = 'Operação alterada com sucesso!';
		}

		itemPost = angular.copy(ng.operacao);
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
					var count  = 0 ;
					$.each(errors, function(i, item) {
						$("#"+i).addClass("has-error");
						var formControl = $("#"+i)
							.attr("data-toggle", "tooltip")
							.attr("data-placement", "bottom")
							.attr("title", item)
							.attr("data-original-title", item);
						if(count == 0)
							formControl.tooltip('show').focus();
						else
							formControl.tooltip();
						count ++ ;
					});
				}else
					ng.mensagens('alert-danger','<strong>Erro ao efetuar cadastro!</strong>');
			});
	}

	ng.editar = function(item) {
		ng.operacao = angular.copy(item);
		ng.loadOperacaoCombo(item.cod_operacao);
		ng.showBoxNovo(true);
		$('html,body').animate({scrollTop: 0},'slow');
	}

	ng.delete = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem Certeza que Deseja Excluir Está Operação ?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"operacao/delete/"+item.cod_operacao)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Operação excluida com sucesso</strong>','.alert-list');
					ng.reset();
					ng.load();
				})
				.error(function(data, status, headers, config) {
					ng.mensagens('alert-danger','<strong>Erro ao excluir</strong>','.alert-list');
				});
		}, undefined);
	}

	ng.loadControleNfe = function(ctr,key) {
		aj.get(baseUrlApi()+"nfe/controles/null/"+ctr)
			.success(function(data, status, headers, config) {
				ng[key] = [{num_item:null,nme_item:'Selecione',dsc_completa:'Selecione'}];
				ng[key] = ng[key].concat(data) ;
				setTimeout(function(){
					$("select").trigger("chosen:updated");
				},300);
			})
			.error(function(data, status, headers, config) {
				
		});
	}
	//ng.lista_cfop  = [{cod_controle_item_nfe:null}] ;
    ng.loadControleNfe('cfop','lista_cfop');
    ng.loadControleNfe('tipo_documento','lista_tipo_documento');
	ng.loadControleNfe('presenca_comprador','lista_presenca_comprador');
	ng.loadControleNfe('local_destino','lista_local_destino');
	ng.loadControleNfe('finalidade_emissao','lista_finalidade_emissao');
	ng.loadControleNfe('consumidor_final','lista_consumidor_final');


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
